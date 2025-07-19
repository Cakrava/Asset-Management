<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan {{ $reportData['title'] ?? 'Umum' }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.4; color: #333; }
        .header { text-align: center; border-bottom: 1px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header img { position: absolute; left: 0; top: 0; height: 75px; }
        .header .header-text h3 { margin: 0; font-size: 14pt; }
        .header .header-text p { margin: 5px 0 0 0; font-size: 10pt; }
        .report-main-title { text-align: center; font-size: 14pt; font-weight: bold; text-decoration: underline; margin-bottom: 5px; }
        .report-sub-title { text-align: center; margin-top: 0; margin-bottom: 20px; font-size: 12pt; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 9pt; }
        table th, table td { border: 1px solid #333; padding: 5px 7px; vertical-align: top; }
        table th { background-color: transparent; font-weight: bold; text-align: left; }
        table td ul { margin: 0; padding-left: 16px; }
        table td ul li { padding: 1px 0; }
        .meta-info { font-size: 10pt; margin-bottom: 10px; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    @php
        // Menentukan variabel utama dari controller
        $currentReportType = $reportTypeToPrint ?? $reportTypeToExport ?? 'unknown';
        $currentFilters = $rawFiltersForTemplate ?? $rawFilters ?? [];
    @endphp

    <div class="header">
        {{-- Menggunakan base_path() untuk path absolut di server, lebih andal untuk PDF --}}
        <img src="{{ base_path('public/asset/image/icon_title.png') }}" alt="Logo">
        <div class="header-text">
            <h3>DINAS KOMUNIKASI DAN INFORMASI KOTA PARIAMAN</h3>
            <p>
                Jl. Jend. Sudirman 25-31, Pd. II, Kec. Pariaman Tengah,<br>
                Kota Pariaman, Sumatera Barat 25513
            </p>
        </div>
    </div>

    <div class="content">
        @if ($currentReportType === 'letter' && isset($reportData['data'][0]))
            <div class="report-main-title">BERITA ACARA SERAH TERIMA BARANG</div>
            <div class="report-sub-title">Nomor: {{ $reportData['data'][0]['letter_number'] ?? '____________________' }}</div>
        @else
            <div class="report-main-title">LAPORAN {{ strtoupper($reportData['title'] ?? 'UMUM') }}</div>
            <div class="report-sub-title">Tanggal Cetak: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY') }}</div>
        @endif

        <div class="meta-info">
            <strong>Periode:</strong> {{ $currentFilters['start_date'] ?? 'Awal' }} s/d {{ $currentFilters['end_date'] ?? 'Akhir' }}
        </div>

        @if (isset($reportData['data']) && count($reportData['data']) > 0)
            <table>
                {{-- HEADERS --}}
                <thead>
                    @if ($currentReportType === 'inventory') <tr><th>No</th><th>Perangkat</th><th>Model</th><th>Stok</th><th>Kondisi</th></tr>
                    @elseif ($currentReportType === 'instansi') <tr><th>No</th><th>Nama Instansi</th><th>Tipe</th><th>Kontak</th><th>Alamat</th></tr>
                    @elseif ($currentReportType === 'other_profile') <tr><th>No</th><th>Nama</th><th>Instansi</th><th>Tipe</th><th>Kontak</th></tr>
                    @elseif ($currentReportType === 'flow_transaction') <tr><th>No</th><th>ID Transaksi</th><th>Tipe</th><th>Klien/Sumber</th><th>Daftar Perangkat</th><th>Status</th><th>Tanggal</th></tr>
                    @elseif ($currentReportType === 'letter') <tr><th>No</th><th>No. Surat</th><th>Perihal</th><th>Klien</th><th>Tanggal</th></tr>
                    @elseif ($currentReportType === 'deployed_device') <tr><th>No</th><th>Penerima</th><th>Daftar Perangkat</th><th>Tanggal Deploy</th><th>Status</th></tr>
                    @endif
                </thead>
                {{-- BODY --}}
                <tbody>
                    @foreach ($reportData['data'] as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            @if ($currentReportType === 'inventory')
                                <td>{{ $item['device']['brand'] ?? '-' }} ({{ ucfirst(str_replace('_', ' ', $item['device']['type'] ?? '-')) }})</td>
                                <td>{{ $item['device']['model'] ?? '-' }}</td>
                                <td>{{ $item['stock'] }}</td>
                                <td>{{ $item['condition'] }}</td>
                            @elseif ($currentReportType === 'instansi')
                                <td>{{ $item['institution'] }}</td>
                                <td>{{ $item['institution_type'] }}</td>
                                <td>{{ $item['phone'] ?? '-' }}</td>
                                <td>{{ $item['address'] ?? '-' }}</td>
                            @elseif ($currentReportType === 'other_profile')
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['institution'] ?? '-' }}</td>
                                <td>{{ $item['institution_type'] ?? '-' }}</td>
                                <td>{{ $item['phone'] ?? '-' }}</td>
                            @elseif ($currentReportType === 'flow_transaction' || $currentReportType === 'deployed_device')
                                @if ($currentReportType === 'flow_transaction')
                                    <td>{{ $item['transaction_id'] }}</td>
                                    <td>{{ ($item['transaction_type'] ?? '-') === 'in' ? 'Masuk' : 'Keluar' }}</td>
                                @endif
                                <td>
                                    @if (!empty($item['client_id']) && isset($item['client']['profile'])) {{ $item['client']['profile']['name'] }}
                                    @elseif (isset($item['other_source_profile'])) {{ $item['other_source_profile']['name'] }}
                                    @else - @endif
                                </td>
                                <td>
                                    @if (isset($item['details']) && count($item['details']) > 0)
                                        <ul>
                                        @foreach ($item['details'] as $detail)
                                            @php
                                                $storedDevice = $detail['stored_device'] ?? null;
                                                $device = $storedDevice['device'] ?? null;
                                            @endphp
                                            @if ($device)
                                                <li>{{ $device['brand'] ?? '-' }} {{ $device['model'] ?? '-' }} ({{ ucfirst(str_replace('_', ' ', $device['type'])) }})
                                                @if(isset($storedDevice['serial_number']) && $storedDevice['serial_number']) [S/N: {{ $storedDevice['serial_number'] }}] @endif
                                                </li>
                                            @else <li>Perangkat tidak ditemukan</li> @endif
                                        @endforeach
                                        </ul>
                                    @else Tidak ada perangkat @endif
                                </td>
                                <td>{{ $item['instalation_status'] ?? $item['status'] ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($item['created_at'])->locale('id')->isoFormat('D MMMM YYYY') }}</td>
                            @elseif ($currentReportType === 'letter')
                                <td>{{ $item['letter_number'] ?? '-' }}</td>
                                <td>{{ $item['subject'] ?? '-' }}</td>
                                <td>{{ $item['client']['profile']['name'] ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($item['created_at'])->locale('id')->isoFormat('D MMMM YYYY') }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-center">Tidak ada data ditemukan untuk periode dan filter yang dipilih.</p>
        @endif
    </div>
</body>
</html>