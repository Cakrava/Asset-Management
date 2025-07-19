<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Letters;             // Pastikan nama model Anda 'Letters' (plural seperti yang digunakan di kode)
use App\Models\StoredDevice;
use App\Models\TransactionDetail;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class LettersController extends Controller
{

    public function index()
    {
        // 1. Ambil data surat yang sudah ada (kode Anda yang sekarang)
        $letters = Letters::with('client.profile')->latest()->get();

        // 2. Ambil semua user yang akan menjadi pilihan klien
        // Anda bisa filter berdasarkan role jika perlu, misal ->where('role', 'client')
        $clients = User::with('profile')
            ->where('role', 'user')
            ->where('status', 'active')
            ->whereColumn('created_at', '!=', 'updated_at') // artinya: data sudah pernah di-update
            ->orderBy('id')
            ->get();


        // 3. Ambil semua data inventaris untuk modal pemilihan perangkat
        $inventories = StoredDevice::with('device')
            ->where('stock', '>', 0) // Hanya tampilkan yang ada stok
            ->get();

        // 4. Kirim semua data ke view
        return view('page.admin.letter', [ // Pastikan nama view benar, misal 'panel.letter.index'
            'letters' => $letters,
            'clients' => $clients,
            'inventories' => $inventories,
        ]);
    }

    // Fungsi untuk menampilkan PDF di tab baru (untuk tombol Print/Lihat)
    public function viewArchivedPdf(Letters $letter)
    {
        // Validasi untuk memastikan file ada
        if (!$letter->pdf_path || !Storage::disk('public')->exists($letter->pdf_path)) {
            abort(404, 'File arsip surat tidak ditemukan.');
        }

        // 1. Ambil konten mentah (raw content) dari file PDF di storage
        $fileContents = Storage::disk('public')->get($letter->pdf_path);

        // 2. Buat respons HTTP secara manual.
        // Kita HANYA mengatur header 'Content-Type' dan TIDAK menyertakan 'Content-Disposition'.
        return Response::make($fileContents, 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
    public function viewSignedArchive(Letters $letter)
    {
        // Validasi untuk memastikan file ada
        if (!$letter->sign_pdf_path || !Storage::disk('public')->exists($letter->sign_pdf_path)) {
            abort(404, 'File arsip surat tidak ditemukan.');
        }

        // 1. Ambil konten mentah (raw content) dari file PDF di storage
        $fileContents = Storage::disk('public')->get($letter->sign_pdf_path);

        // 2. Buat respons HTTP secara manual.
        // Kita HANYA mengatur header 'Content-Type' dan TIDAK menyertakan 'Content-Disposition'.
        return Response::make($fileContents, 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
  

    // Fungsi untuk mengunduh file PDF
    public function downloadArchivedPdf(Letters $letter)
    {
        if (!$letter->pdf_path || !Storage::disk('public')->exists($letter->pdf_path)) {
            abort(404, 'File arsip surat tidak ditemukan.');
        }
        return Storage::disk('public')->download($letter->pdf_path);
    }

    public function storeWithDevices(Request $request)
    {
        // 1. Validasi Input
        $validatedData = $request->validate([
            'client_id' => 'required|integer|exists:users,id',
            'equipments' => 'required|array|min:1',
            'equipments.*.id' => 'required|integer|exists:stored_devices,id',
            'equipments.*.quantity' => 'required|integer|min:1',
        ]);

        // ===================================================================
        // NEW: 1. Cek jika sudah ada surat 'open' untuk klien yang sama
        // ===================================================================
        $existingOpenLetter = Letters::where('client_id', $validatedData['client_id'])
        ->whereIn('status', ['Open', 'Needed']) //
            ->first();

        if ($existingOpenLetter) {
            // Mengembalikan dengan response JSON untuk ditangani di frontend
            // Frontend bisa menampilkan alert/flash message dari response ini
            return response()->json([
                'message' => 'Klien ini sudah memiliki surat berstatus "Open/Needed". Mohon selesaikan surat sebelumnya atau buat yang baru setelah statusnya "Closed/Deleted".',
                'type' => 'warning' // Tambahkan tipe untuk penanganan di frontend
            ], 409); // Menggunakan status HTTP 409 Conflict
        }

        try {
            // ===================================================================
            // BAGIAN 1: TRANSAKSI DATABASE UNTUK MENYIMPAN DATA SURAT
            // ===================================================================
            Log::info('Memulai proses pembuatan surat baru (storeWithDevices).');

            // DB::transaction akan memastikan semua query berhasil atau tidak sama sekali.
            $letter = DB::transaction(function () use ($validatedData) {

                // Generate Nomor Surat dan ID Unik
                $now = Carbon::now();
                $year = $now->year;
                $month = $now->month;
                $romanMonth = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
                // Hitung surat yang sudah ada di tahun ini untuk nomor urut
              // Ambil nomor urut terakhir dari surat tahun ini (berdasarkan angka di depan / )
$lastLetter = Letters::whereYear('created_at', $year)
->orderByDesc('created_at')
->pluck('letter_number')
->first();

$lastNumber = 0;

if ($lastLetter) {
// Ambil angka paling depan dari format {nomor}/SST/...
preg_match('/^(\d+)/', $lastLetter, $matches);
if (isset($matches[1])) {
    $lastNumber = (int) $matches[1];
}
}

$nextLetterNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
$letterNumber = sprintf('%s/SST/DISKOMINFO/%s/%s', $nextLetterNumber, $romanMonth[$month - 1], $year);

                $letterNumber = sprintf('%s/SST/DISKOMINFO/%s/%s', $nextLetterNumber, $romanMonth[$month - 1], $year);
                $uniqueId = (string) Str::uuid();

                // Simpan data ke tabel 'letters'
                $newLetter = Letters::create([
                    'unique_id' => $uniqueId,
                    'letter_number' => $letterNumber,
                    'status' => 'Needed',
                    'client_id' => $validatedData['client_id'],
                    'ticket_id' => null,
                ]);
                Log::info("Data surat berhasil dibuat dengan ID: {$newLetter->id} dan Nomor: {$letterNumber}");

                // Loop dan simpan setiap item ke 'transaction_detail'
                foreach ($validatedData['equipments'] as $item) {
                    TransactionDetail::create([
                        'unique_id' => $uniqueId,
                        'stored_device_id' => $item['id'],
                        'quantity' => $item['quantity'],
                    ]);
                }
                Log::info("Detail transaksi untuk surat ID: {$newLetter->id} berhasil disimpan.");

                // Kembalikan model surat yang baru dibuat agar bisa digunakan di luar transaksi
                return $newLetter;
            });

            // ===================================================================
            // BAGIAN 2: PEMBUATAN DAN PENGARSIPAN FILE PDF
            // Bagian ini hanya berjalan jika transaksi database di atas berhasil.
            // ===================================================================

            Log::info("Memulai pembuatan arsip PDF untuk surat: {$letter->letter_number}");

            // Muat relasi yang dibutuhkan untuk ditampilkan di PDF (mencegah N+1 problem)
            // Pastikan relasi 'details' ada di model Letters Anda untuk TransactionDetail
            $letter->load('client.profile', 'details.storedDevice.device');

            // Tentukan nama dan path file PDF untuk disimpan
            $pdfFileName = 'arsip-surat/' . str_replace('/', '-', $letter->letter_number) . '.pdf';

            // Generate PDF dari view.
            // PENTING: Pastikan di view 'letter-print-preview', path ke logo menggunakan `public_path()`
            // Contoh: <img src="{{ public_path('asset/image/icon_title.png') }}">
            $pdf = PDF::loadView('page.admin.letter-print-preview', ['letter' => $letter]);

            // Simpan file PDF yang sudah digenerate ke dalam storage (di folder storage/app/public/arsip-surat)
            Storage::disk('public')->put($pdfFileName, $pdf->output());
            Log::info("Arsip PDF berhasil disimpan di: {$pdfFileName}");

            // ===================================================================
            // BAGIAN 3: SIMPAN PATH PDF KE DATABASE DAN BERI RESPON
            // ===================================================================

            // Simpan path file PDF ke record surat yang bersangkutan
            $letter->pdf_path = $pdfFileName;
            $letter->save();

            // Flash session untuk notifikasi di frontend
            session()->flash('success', 'Surat dengan nomor ' . $letter->letter_number . ' berhasil dibuat dan diarsipkan.');

            return response()->json(['message' => 'Surat berhasil dibuat dan diarsipkan.', 'type' => 'success'], 200);

        } catch (\Exception $e) {
            // Jika terjadi error di manapun (baik di dalam transaksi atau saat buat PDF), log errornya.
            Log::error('!!! GAGAL MEMBUAT SURAT BARU (storeWithDevices) !!!');
            Log::error('Error Message: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ' pada baris ' . $e->getLine());

            return response()->json([
                'message' => 'Terjadi kesalahan pada server saat membuat surat.',
                'error' => $e->getMessage(),
                'type' => 'error'
            ], 500);
        }
    }

    public function generateSst(Request $request)
    {
        // Log saat request pertama kali masuk
        Log::info('--- Proses Generate SST Dimulai (generateSst) ---');
        Log::info('Data mentah dari request:', $request->all());

        // 1. Validasi data yang masuk dari frontend
        try {
            $validatedData = $request->validate([
                'ticket_id' => 'required|integer|exists:tickets,id',
                'equipments' => 'required|array|min:1',
                // PERIKSA NAMA TABEL INI: pastikan 'stored_devices' sudah benar
                'equipments.*.id' => 'required|integer|exists:stored_devices,id',
                'equipments.*.quantity' => 'required|integer|min:1',
            ]);
            Log::info('Validasi berhasil.', $validatedData);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Jika validasi gagal, log errornya dan hentikan proses
            Log::error('Validasi Gagal:', $e->errors());
            // Laravel akan otomatis mengirim response 422, jadi tidak perlu return manual
            throw $e;
        }

        try {
            // 2. Gunakan DB Transaction untuk keamanan data
            Log::info('Memulai DB Transaction (generateSst).');
            // Mengembalikan instance model Letters yang baru dibuat dari transaksi
            $letter = DB::transaction(function () use ($validatedData) {

                Log::info('Mencari tiket dengan ID: ' . $validatedData['ticket_id']);
                $ticket = Ticket::findOrFail($validatedData['ticket_id']);
                Log::info('Tiket ditemukan. User ID: ' . $ticket->user_id);

                // 3. Generate ID unik
                $uniqueId = (string) Str::uuid();
                Log::info('Unique ID berhasil dibuat: ' . $uniqueId);

                // 4. Generate Nomor Surat Dinamis
                Log::info('Memulai pembuatan nomor surat.');
                $now = Carbon::now();
                $year = $now->year;
                $month = $now->month;
                $romanMonth = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

              // Ambil nomor urut terakhir dari surat tahun ini (berdasarkan angka di depan / )
$lastLetter = Letters::whereYear('created_at', $year)
->orderByDesc('created_at')
->pluck('letter_number')
->first();

$lastNumber = 0;

if ($lastLetter) {
// Ambil angka paling depan dari format {nomor}/SST/...
preg_match('/^(\d+)/', $lastLetter, $matches);
if (isset($matches[1])) {
    $lastNumber = (int) $matches[1];
}
}

$nextLetterNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
$letterNumber = sprintf('%s/SST/DISKOMINFO/%s/%s', $nextLetterNumber, $romanMonth[$month - 1], $year);

                $letterNumber = sprintf(
                    '%s/SST/DISKOMINFO/%s/%s',
                    $nextLetterNumber,
                    $romanMonth[$month - 1],
                    $year
                );
                Log::info('Nomor surat berhasil dibuat: ' . $letterNumber);

                // 5. Simpan data ke tabel 'letters'
                $letterData = [
                    'unique_id' => $uniqueId,
                    'letter_number' => $letterNumber,
                    'status' => 'Needed', // Status awal surat, bisa disesuaikan
                    'client_id' => $ticket->user_id,
                    'ticket_id' => $ticket->id,
                ];
                Log::info('Menyiapkan data untuk disimpan ke tabel letters:', $letterData);
                $newLetter = Letters::create($letterData); // Simpan dan tangkap instance Letters
                Log::info('Data berhasil disimpan ke tabel letters.');

                // 6. Loop dan simpan setiap item ke 'transaction_detail'
                Log::info('Memulai loop untuk menyimpan transaction_detail.');
                foreach ($validatedData['equipments'] as $index => $item) {
                    $detailData = [
                        'unique_id' => $uniqueId,
                        'stored_device_id' => $item['id'],
                        'quantity' => $item['quantity'],
                    ];
                    Log::info("Loop " . ($index + 1) . ": Menyimpan data detail:", $detailData);
                    TransactionDetail::create($detailData);
                    Log::info("Loop " . ($index + 1) . ": Data detail berhasil disimpan.");
                }
                Log::info('Loop transaction_detail selesai.');

                // 7. Update status tiket
                Log::info('Mengupdate status tiket ID ' . $ticket->id . ' menjadi "completed".');
                $ticket->update(['status' => 'completed']);
                Log::info('Status tiket berhasil diupdate.');

                Log::info('DB Transaction akan di-commit.');
                return $newLetter; // Mengembalikan instance surat yang baru dibuat
            });

            // ===================================================================
            // NEW: 8. PEMBUATAN DAN PENGARSIPAN FILE PDF (MIRIP storeWithDevices)
            // ===================================================================
            Log::info("Memulai pembuatan arsip PDF untuk surat dari generateSst: {$letter->letter_number}");

            // Muat relasi yang dibutuhkan untuk ditampilkan di PDF
            // Pastikan relasi 'details' ada di model Letters Anda
            $letter->load('client.profile', 'details.storedDevice.device');

            // Tentukan nama dan path file PDF untuk disimpan
            $pdfFileName = 'arsip-surat/' . str_replace('/', '-', $letter->letter_number) . '.pdf';

            // Generate PDF dari view
            $pdf = PDF::loadView('page.admin.letter-print-preview', ['letter' => $letter]);

            // Simpan file PDF yang sudah digenerate ke dalam storage
            Storage::disk('public')->put($pdfFileName, $pdf->output());
            Log::info("Arsip PDF berhasil disimpan di: {$pdfFileName}");

            // ===================================================================
            // NEW: 9. SIMPAN PATH PDF KE DATABASE
            // ===================================================================
            $letter->pdf_path = $pdfFileName;
            $letter->save();
            Log::info("PDF path berhasil disimpan untuk surat ID: {$letter->id}");

            // Flash session untuk notifikasi di frontend
            session()->flash('success', 'Surat Serah Terima berhasil dibuat dengan nomor ' . $letter->letter_number . ' dan tiket telah selesai.');

            // 10. Kirim response sukses jika transaksi berhasil
            Log::info('--- Proses Generate SST Selesai Sukses ---');
            return response()->json(['message' => 'Surat berhasil dibuat.', 'type' => 'success'], 200);

        } catch (\Exception $e) {
            // Tangkap SEMUA jenis error yang mungkin terjadi
            Log::error('!!! TERJADI ERROR KRITIS SAAT PROSES GENERATE SST (generateSst) !!!');
            Log::error('Error Message: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ' pada baris ' . $e->getLine());
            Log::error('Stack Trace: ' . $e->getTraceAsString()); // Penting untuk debugging

            // Kirim response error yang jelas ke frontend
            return response()->json([
                'message' => 'Terjadi kesalahan internal pada server. Silakan periksa log.',
                'error' => $e->getMessage(),
                'type' => 'error'
            ], 500);
        }
    }
    public function softDelete(Letters $letter)
    {
        // Pastikan hanya surat dengan status tertentu yang bisa dihapus
        if (!in_array($letter->status, ['Open', 'Needed'])) {
            return response()->json([
                'message' => 'Hanya surat dengan status "Open" atau "Needed" yang bisa dihapus.',
                'type'    => 'warning'
            ], 403);
        }
    
        DB::beginTransaction();
    
        try {
            // 1. Ubah status surat
            $letter->status = 'Deleted';
            $letter->save();
    
            // 2. Ambil semua transaksi terkait & ubah statusnya
            Transaction::where('letter_id', $letter->id)
                ->update(['instalation_status' => 'Revoked']);
    
            // 3. Ambil salah satu transaksi terkait untuk proses penghapusan link
            $transaction = Transaction::where('letter_id', $letter->id)->first();
    
            if ($transaction) {
                $this->removeLink($transaction->id);
            }
    
            DB::commit();
    
            return response()->json([
                'message' => 'Surat berhasil dihapus dan transaksi terkait telah dibatalkan (Revoked).',
                'type'    => 'success'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
    
            // Bisa ditambahkan: Log::error($e);
    
            return response()->json([
                'message' => 'Terjadi kesalahan saat mencoba menghapus surat.',
                'type'    => 'error'
            ], 500);
        }
    }
    
    private function removeLink($transactionId)
    {
        $jsonFilePath = storage_path('app/temporary_url.json');
    
        if (!File::exists($jsonFilePath)) {
            return;
        }
    
        $data = json_decode(File::get($jsonFilePath), true);
    
        if (is_array($data) && isset($data[$transactionId])) {
            unset($data[$transactionId]);
            File::put($jsonFilePath, json_encode($data, JSON_PRETTY_PRINT));
        }
    }
    

}