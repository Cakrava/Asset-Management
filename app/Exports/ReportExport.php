<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class ReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $reportData;
    protected $reportType;
    protected $filters;

    public function __construct(array $reportData, string $reportType, array $filters)
    {
        $this->reportData = $reportData;
        $this->reportType = $reportType;
        $this->filters = $filters;
    }

    public function collection()
    {
        return collect($this->reportData['data'] ?? [])->map(function ($item) {
            $item = (array)$item; // Konversi ke array untuk konsistensi
            switch ($this->reportType) {
                case 'inventory':
                    return [
                        'id' => $item['id'] ?? '-',
                        'perangkat' => ($item['device']['brand'] ?? '-') . ' (' . ucfirst(str_replace('_', ' ', $item['device']['type'] ?? '-')) . ')',
                        'model' => $item['device']['model'] ?? '-',
                        'stok' => $item['stock'] ?? 0,
                        'kondisi' => $item['condition'] ?? '-',
                        'tanggal_dibuat' => Carbon::parse($item['created_at'])->format('Y-m-d H:i:s'),
                    ];
                case 'instansi':
                    return [
                        'id' => $item['id'] ?? '-',
                        'nama_instansi' => $item['institution'] ?? '-',
                        'tipe_instansi' => $item['institution_type'] ?? '-',
                        'kontak' => $item['phone'] ?? '-',
                        'alamat' => $item['address'] ?? '-',
                        'tanggal_dibuat' => Carbon::parse($item['created_at'])->format('Y-m-d H:i:s'),
                    ];
                case 'other_profile':
                     return [
                        'id' => $item['id'] ?? '-',
                        'nama' => $item['name'] ?? '-',
                        'kontak' => $item['phone'] ?? '-',
                        'instansi' => $item['institution'] ?? '-',
                        'tipe_instansi' => $item['institution_type'] ?? '-',
                        'tanggal_dibuat' => Carbon::parse($item['created_at'])->format('Y-m-d H:i:s'),
                    ];
                case 'flow_transaction':
                case 'deployed_device': // Menggabungkan logika karena sangat mirip
                    $clientName = '-';
                    if (!empty($item['client_id']) && !empty($item['client']['profile'])) {
                        $clientName = $item['client']['profile']['name'];
                    } elseif (!empty($item['other_source_profile'])) {
                        $clientName = $item['other_source_profile']['name'];
                    } elseif ($this->reportType === 'deployed_device' && !empty($item['client']['profile'])) {
                        $clientName = $item['client']['profile']['name'];
                    }

                    $devicesList = collect($item['details'])->map(function($detail) {
                        $storedDevice = $detail['stored_device'] ?? null;
                        $device = $storedDevice['device'] ?? null;
                        if ($device) {
                            $deviceTypeFormatted = ucfirst(str_replace('_', ' ', $device['type'] ?? ''));
                            $serialNumber = isset($storedDevice['serial_number']) ? " [S/N: {$storedDevice['serial_number']}]" : '';
                            return "{$device['brand']} {$device['model']} ({$deviceTypeFormatted}){$serialNumber}";
                        }
                        return 'Perangkat tidak ditemukan';
                    })->implode("\n"); // Pemisah baris baru untuk Excel

                    if ($this->reportType === 'flow_transaction') {
                        return [
                            'id_transaksi' => $item['unique_id'] ?? '-',
                            'tipe_transaksi' => ($item['transaction_type'] ?? '-') === 'in' ? 'Masuk' : 'Keluar',
                            'klien_sumber' => $clientName,
                            'daftar_perangkat' => $devicesList,
                            'status_instalasi' => $item['instalation_status'] ?? '-',
                            'tanggal_transaksi' => Carbon::parse($item['created_at'])->format('Y-m-d H:i:s'),
                        ];
                    } else { // deployed_device
                        return [
                            'id_deploy' => $item['id'] ?? '-',
                            'penerima' => $clientName,
                            'daftar_perangkat' => $devicesList,
                            'tanggal_deploy' => Carbon::parse($item['created_at'])->format('Y-m-d H:i:s'),
                            'status' => $item['status'] ?? '-',
                        ];
                    }
                case 'letter':
                    return [
                        'id_surat' => $item['id'] ?? '-',
                        'nomor_surat' => $item['letter_number'] ?? '-',
                        'perihal' => $item['subject'] ?? '-',
                        'klien' => $item['client']['profile']['name'] ?? '-',
                        'tanggal_surat' => Carbon::parse($item['created_at'])->format('Y-m-d H:i:s'),
                    ];
                default:
                    return $item;
            }
        });
    }

    public function headings(): array
    {
        switch ($this->reportType) {
            case 'inventory': return ['ID Inventaris', 'Perangkat', 'Model', 'Stok', 'Kondisi', 'Tanggal Dibuat'];
            case 'instansi': return ['ID Instansi', 'Nama Instansi', 'Tipe Instansi', 'Kontak', 'Alamat', 'Tanggal Dibuat'];
            case 'other_profile': return ['ID Profil Lain', 'Nama', 'Kontak', 'Instansi', 'Tipe Instansi', 'Tanggal Dibuat'];
            case 'flow_transaction': return ['ID Transaksi', 'Tipe Transaksi', 'Klien/Sumber', 'Daftar Perangkat', 'Status Instalasi', 'Tanggal Transaksi'];
            case 'letter': return ['ID Surat', 'Nomor Surat', 'Perihal', 'Klien', 'Tanggal Surat'];
            case 'deployed_device': return ['ID Deploy', 'Penerima', 'Daftar Perangkat', 'Tanggal Deploy', 'Status'];
            default: return ['ID', 'Data'];
        }
    }
}