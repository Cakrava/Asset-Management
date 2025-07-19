
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Serah Terima - {{ $letter->letter_number }}</title>
    <style>
        @page {
            size: F4;
            margin: 1cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid black;
            padding-bottom: 10px;
            margin-bottom: 30px;
            position: relative;
        }

        .title {
            text-align: center;
            text-decoration: underline;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .subtitle {
            text-align: center;
            margin-top: 0;
            margin-bottom: 20px;
        }

        .content p {
            text-align: justify;
            margin: 10px 0;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 10pt;
        }

        .details-table th, .details-table td {
            border: 1px solid #000;
            padding: 4px 6px;
        }

        .details-table th {
            background-color: #eee;
        }

        .signatures {
            margin-top: 20px;
            width: 100%;
        }

        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-space {
            height: 50px;
        }

        .logo {
            position: absolute;
            left: 0;
            top: 0;
            height: 80px;
        }

        .header-text {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path('asset/image/icon_title.png') }}" alt="Logo" class="logo" >
            <div class="header-text" style="margin-left: 20px">
                <h3 style="margin: 0;">DINAS KOMUNIKASI DAN INFORMASI KOTA PARIAMAN</h3>
                <p style="margin: 0; font-size: small;">
                    Jl. Jend. Sudirman 25-31, Pd. II, Kec. Pariaman Tengah,<br>
                    Kota Pariaman, Sumatera Barat 25513
                </p>
            </div>
        </div>

        <div class="title">BERITA ACARA SERAH TERIMA BARANG</div>
        <div class="subtitle">Nomor: {{ $letter->letter_number }}</div>

        <div class="content">
            <p>Pada hari <strong>{{ $letter->created_at->translatedFormat('l') }}</strong>, tanggal <strong>{{ $letter->created_at->translatedFormat('d') }}</strong> bulan <strong>{{ $letter->created_at->translatedFormat('F') }}</strong> tahun <strong>{{ $letter->created_at->translatedFormat('Y') }}</strong>, kami yang bertanda tangan di bawah ini:</p>

            <p><strong>PIHAK PERTAMA</strong></p>
            <table style="margin-left: 20px; margin-bottom: 15px;">
                <tr>
                    <td width="140px">Nama</td>
                    <td>: ____________________________</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>: Petugas Instalasi / Teknisi</td>
                </tr>
                <tr>
                    <td>Instansi</td>
                    <td>: Dinas Komunikasi dan Informatika Kota Pariaman</td>
                </tr>
            </table>

            <p><strong>PIHAK KEDUA</strong></p>
            <table style="margin-left: 20px; margin-bottom: 15px;">
                <tr>
                    <td width="140px">Nama</td>
                    <td>: <strong>{{ $letter->client?->profile?->name ?? 'N/A' }}</strong></td>
                </tr>
                <tr>
                    <td>Instansi</td>
                    <td>: {{ $letter->client?->profile?->institution ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>: {{ $letter->client?->profile?->address ?? 'N/A' }}</td>
                </tr>
            </table>

            <p>Dengan ini menyatakan bahwa telah dilakukan serah terima barang sebagai bagian dari pelaksanaan kegiatan instalasi/perbaikan jaringan, dengan rincian sebagai berikut:</p>

            @if($letter->details && $letter->details->count() > 0)
                <table class="details-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Perangkat</th>
                            <th>Kondisi</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($letter->details as $index => $detail)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $detail->storedDevice?->device?->brand ?? 'N/A' }} {{ $detail->storedDevice?->device?->model ?? '' }}</td>
                                <td>{{ $detail->storedDevice?->condition ?? 'N/A' }}</td>
                                <td style="text-align: center;">{{ $detail->quantity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <p>Barang-barang tersebut telah diterima dalam kondisi baik dan lengkap, serta akan digunakan sesuai dengan kebutuhan dan ketentuan yang berlaku di lingkungan instansi penerima.</p>

            <p>Demikian berita acara ini dibuat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>

        <table class="signatures">
            <tr>
                <td>
                    PIHAK PERTAMA<br><br>
                    <div class="signature-space"></div>
                    (________________________)<br>
        
                </td>
                <td>
                    PIHAK KEDUA<br><br>
                    <div class="signature-space"></div>
                    (<strong>{{ $letter->client?->profile?->name ?? '____________________' }}</strong>)<br>
                    
                </td>
            </tr>
        </table>
    </div>

    @if(isset($auto_print) && $auto_print === true)
        <script> window.onload = () => window.print(); </script>
    @endif
</body>
</html>
