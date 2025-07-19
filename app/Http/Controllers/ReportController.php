<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use App\Models\OtherSourceProfile;
use App\Models\Transaction;
use App\Models\Letters;
use App\Models\StoredDevice;
use App\Models\DeploymentDevice;
use App\Models\Device;

use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Session;
class ReportController extends Controller
{
    public function index()
    {
        return view('page.report');
    }

    public function generateReport(Request $request)
    {
        $reportTypes = $request->input('report_types', []);
        $filters = $request->input('filters', []);

        $startDate = isset($filters['startDate']) && $filters['startDate'] ? Carbon::parse($filters['startDate'])->startOfDay() : null;
        $endDate = isset($filters['endDate']) && $filters['endDate'] ? Carbon::parse($filters['endDate'])->endOfDay() : null;

        $reportsData = [];

        foreach ($reportTypes as $type) {
            switch ($type) {
                case 'inventory':
                    $query = StoredDevice::with('device');
                    if ($startDate && $endDate) $query->whereBetween('created_at', [$startDate, $endDate]);
                    if (!empty($filters['inventoryCondition'])) $query->where('condition', $filters['inventoryCondition']);
                    if (!empty($filters['inventoryDeviceType'])) {
                        $query->whereHas('device', function($q) use ($filters) { $q->where('type', $filters['inventoryDeviceType']); });
                    }
                    $reportsData['inventory'] = ['title' => 'Daftar Inventaris', 'data' => $query->get()->toArray()];
                    break;

                case 'instansi':
                    $query = Profile::with('user')->whereHas('user', function($q) { $q->where('role', 'user'); });
                    if ($startDate && $endDate) $query->whereBetween('created_at', [$startDate, $endDate]);
                    if (!empty($filters['instansiType'])) $query->where('institution_type', $filters['instansiType']);
                    $reportsData['instansi'] = ['title' => 'Daftar Instansi (Klien)', 'data' => $query->get()->toArray()];
                    break;

                case 'other_profile':
                    $query = OtherSourceProfile::query();
                    if ($startDate && $endDate) $query->whereBetween('created_at', [$startDate, $endDate]);
                    $reportsData['other_profile'] = ['title' => 'Daftar Profil Sumber Lain', 'data' => $query->get()->toArray()];
                    break;

                case 'flow_transaction':
                    // Memuat semua relasi yang diperlukan
                    $query = Transaction::with(['client.profile', 'otherSourceProfile', 'details.storedDevice.device']);
                    if ($startDate && $endDate) $query->whereBetween('created_at', [$startDate, $endDate]);
                    if (!empty($filters['transactionType'])) $query->where('transaction_type', $filters['transactionType']);
                    if (!empty($filters['transactionStatus'])) $query->where('instalation_status', $filters['transactionStatus']);
                    $reportsData['flow_transaction'] = ['title' => 'Alur Transaksi Perangkat', 'data' => $query->get()->toArray()];
                    break;

                case 'letter':
                    $query = Letters::with('client.profile');
                    if ($startDate && $endDate) $query->whereBetween('created_at', [$startDate, $endDate]);
                    $reportsData['letter'] = ['title' => 'Daftar Surat', 'data' => $query->get()->toArray()];
                    break;

                case 'deployed_device':
                    $query = DeploymentDevice::with(['client.profile', 'details.storedDevice.device']);
                    if ($startDate && $endDate) $query->whereBetween('created_at', [$startDate, $endDate]);
                    if (!empty($filters['inventoryCondition'])) {
                        $query->whereHas('details.storedDevice', function($q) use ($filters) { $q->where('condition', $filters['inventoryCondition']); });
                    }
                    if (!empty($filters['inventoryDeviceType'])) {
                        $query->whereHas('details.storedDevice.device', function($q) use ($filters) { $q->where('type', $filters['inventoryDeviceType']); });
                    }
                    $reportsData['deployed_device'] = ['title' => 'Perangkat Dideploy', 'data' => $query->get()->toArray()];
                    break;
            }
        }
        return response()->json($reportsData);
    }

    public function downloadPdf(Request $request)
    {
        $reportTypeToExport = $request->query('report_type');
        if (!$reportTypeToExport) return back()->with('error', 'Pilih jenis laporan untuk diunduh sebagai PDF.');

        $rawFilters = $request->query();
        $mappedFilters = [
            'startDate' => $rawFilters['start_date'] ?? null, 'endDate' => $rawFilters['end_date'] ?? null,
            'transactionType' => $rawFilters['transaction_type'] ?? null, 'transactionStatus' => $rawFilters['transaction_status'] ?? null,
            'instansiType' => $rawFilters['instansi_type'] ?? null, 'inventoryCondition' => $rawFilters['inventory_condition'] ?? null,
            'inventoryDeviceType' => $rawFilters['inventory_device_type'] ?? null, 'documentSize' => $rawFilters['document_size'] ?? 'a4',
        ];
        $data = $this->generateReport(new Request(['report_types' => [$reportTypeToExport], 'filters' => $mappedFilters]))->getData(true);
        $reportData = $data[$reportTypeToExport] ?? null;
        if (!$reportData) return back()->with('error', 'Data laporan tidak ditemukan.');

        $documentSize = $mappedFilters['documentSize'];
        $pdf = Pdf::loadView('component.pdf_template', [
            'reportData' => $reportData, 'rawFilters' => $rawFilters, 'documentSize' => $documentSize,
            'reportTypeToExport' => $reportTypeToExport
        ]);
        if ($documentSize === 'f4') $pdf->setPaper([0, 0, 595.28, 935.43], 'portrait');
        return $pdf->download('laporan_' . $reportTypeToExport . '_' . Carbon::now()->format('Ymd_His') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $reportTypeToExport = $request->query('report_type');
        if (!$reportTypeToExport) return back()->with('error', 'Pilih jenis laporan untuk diekspor.');

        $rawFilters = $request->query();
        $mappedFilters = [
            'startDate' => $rawFilters['start_date'] ?? null, 'endDate' => $rawFilters['end_date'] ?? null,
            'transactionType' => $rawFilters['transaction_type'] ?? null, 'transactionStatus' => $rawFilters['transaction_status'] ?? null,
            'instansiType' => $rawFilters['instansi_type'] ?? null, 'inventoryCondition' => $rawFilters['inventory_condition'] ?? null,
            'inventoryDeviceType' => $rawFilters['inventory_device_type'] ?? null,
        ];
        $data = $this->generateReport(new Request(['report_types' => [$reportTypeToExport], 'filters' => $mappedFilters]))->getData(true);
        $reportData = $data[$reportTypeToExport] ?? [];
        return Excel::download(new ReportExport($reportData, $reportTypeToExport, $rawFilters), 'laporan_' . $reportTypeToExport . '_' . Carbon::now()->format('Ymd_His') . '.xlsx');
    }

    public function printPdf(Request $request)
    {
        try {
            // Validasi input tetap sama
            $request->validate(['report_type' => 'required|string']);

            // Ambil semua data yang diperlukan untuk membuat laporan
            $reportParams = [
                'report_type' => $request->input('report_type'),
                'filters' => $request->input('filters', [])
            ];
            
            // SIMPAN "RESEP" LAPORAN KE SESSION
            Session::put('printable_report_params', $reportParams);

            // Beri tahu JavaScript bahwa persiapan berhasil
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            // Jika terjadi error, kembalikan pesan error
            return response()->json(['success' => false, 'message' => 'Gagal menyiapkan data cetak: ' . $e->getMessage()], 500);
        }
    }

    /**
     * BARU: Fungsi yang dipanggil oleh route untuk menyajikan PDF.
     */
    public function viewPrintablePdf()
    {
        // 1. Ambil "resep" dari Session
        $params = Session::get('printable_report_params');

        // Jika tidak ada resep (misal, user mengakses URL langsung), gagalkan.
        if (!$params) {
            abort(404, 'Sesi laporan tidak ditemukan atau sudah kedaluwarsa.');
        }

        // 2. Hapus resep dari session agar tidak bisa di-refresh
        Session::forget('printable_report_params');
        
        // 3. Generate data laporan menggunakan resep (menggunakan logika dari fungsi lama Anda)
        $requestForGeneration = new Request([
            'report_types' => [$params['report_type']],
            'filters' => $params['filters']
        ]);
        $data = $this->generateReport($requestForGeneration)->getData(true);
        $reportData = $data[$params['report_type']] ?? null;

        if (!$reportData) {
            abort(404, 'Gagal menghasilkan data untuk laporan.');
        }

        // 4. Buat PDF secara on-the-fly
        $pdf = Pdf::loadView('component.pdf_template', [
            'reportData' => $reportData,
            'rawFiltersForTemplate' => ['start_date' => $params['filters']['startDate'] ?? null, 'end_date' => $params['filters']['endDate'] ?? null],
            'documentSize' => $params['filters']['documentSize'] ?? 'a4',
            'reportTypeToPrint' => $params['report_type']
        ]);
        
        // Sesuaikan ukuran kertas jika perlu
        if (($params['filters']['documentSize'] ?? 'a4') === 'f4') {
            $pdf->setPaper([0, 0, 595.28, 935.43], 'portrait');
        }

        // 5. Sajikan PDF langsung ke browser (inline)
        return $pdf->stream('laporan.pdf'); // .stream() adalah alias untuk respons dengan Content-Type: application/pdf
    }
}