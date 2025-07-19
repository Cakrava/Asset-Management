<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan - Tata Letak Ilustrasi Tanpa Kartu</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    @include('layout.icon_tittle')
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: sans-serif;
            background-color: white;
            /* Latar Belakang Abu-abu Sangat Terang */
            color: #333;
            /* Warna Teks Utama */
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 100vh;
            /* Minimum Tinggi Viewport */
        }

        .container-404 {
            display: flex;
            width: 100%;
            padding: 20px;
            margin: 20px;
            flex-direction: row;
            /* Default: Teks dan Gambar Berdampingan */
            box-sizing: border-box;
            /* Pastikan padding dihitung dalam lebar */
        }

        .text-content {
            flex: 1;
            /* Mengisi Ruang yang Tersedia di Kiri */
            text-align: left;
            /* Teks Rata Kiri */
            padding-right: 30px;
            /* Spasi ke Gambar di Kanan */
            box-sizing: border-box;
            /* Pastikan padding dihitung dalam lebar */
        }

        .error-label {
            font-size: 16px;
            color: #777;
            /* Warna Label Error */
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .error-title {
            font-size: 2.5em;
            /* Ukuran Judul Lebih Besar */
            margin-bottom: 15px;
            color: #222;
            /* Warna Judul */
            font-weight: bold;
        }

        .error-description {
            font-size: 1em;
            color: #555;
            /* Warna Deskripsi */
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .go-back-link {
            display: inline-flex;
            /* Flex untuk Align Ikon */
            align-items: center;
            color: #3498db;
            /* Warna Link */
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .go-back-link:hover {
            color: #2980b9;
            /* Warna Link Hover */
        }

        .go-back-icon {
            margin-left: 5px;
            /* Spasi Ikon dari Teks */
            font-size: 0.9em;
        }


        .image-404 {
            flex: 1;
            /* Mengisi Ruang yang Tersedia di Kanan */
            text-align: right;
            /* Pusatkan Gambar ke Kanan */
            box-sizing: border-box;
            /* Pastikan padding dihitung dalam lebar */
            padding-left: 30px;
            /* Spasi ke Teks di Kiri */
            display: flex;
            /* Gunakan flexbox untuk memusatkan gambar */
            justify-content: center;
            /* Pusatkan horizontal */
            align-items: center;
            /* Pusatkan vertikal (jika perlu) */
        }

        .image-404 img {
            max-width: 100%;
            /* Gambar Responsif */
            height: auto;
            display: block;
            /* Hilangkan Spasi Bawah Gambar Inline */
        }

        /* Media Query untuk Responsif */
        @media (max-width: 768px) {
            body {
                padding: 20px;
                /* Tambahkan padding di body untuk layar kecil */
            }

            .container-404 {
                flex-direction: column;
                /* Tumpuk Teks dan Gambar Secara Vertikal */
                text-align: center;
                /* Pusatkan teks pada layar kecil */
                padding: 15px;
                /* Kurangi padding container pada layar kecil */
            }

            .text-content {
                text-align: center;
                /* Pusatkan teks */
                padding-right: 0;
                /* Hilangkan padding kanan pada layar kecil */
                margin-bottom: 20px;
                /* Tambahkan margin bawah agar ada jarak dengan gambar */
                padding-left: 0;
                /* Hilangkan padding kiri pada layar kecil */
            }

            .image-404 {
                text-align: center;
                /* Pusatkan gambar */
                padding-left: 0;
                /* Hilangkan padding kiri pada layar kecil */
                padding-top: 0;
                /* Hilangkan padding atas pada layar kecil */
                display: block;
                /* Kembalikan display ke block agar tidak flex lagi (opsional, tergantung efek yang diinginkan) */
            }

            .image-404 img {
                max-width: 90%;
                /* Perkecil ukuran gambar sedikit lagi pada layar kecil */
                max-height: 300px;
                /* Batasi tinggi gambar agar tidak terlalu besar secara vertikal */
                height: auto;
                /* Pastikan rasio aspek terjaga */
            }

            .error-title {
                font-size: 2em;
                /* Perkecil ukuran judul pada layar kecil */
            }

            .error-description {
                font-size: 0.9em;
                /* Perkecil ukuran deskripsi pada layar kecil */
            }
        }
    </style>
</head>

<body>
    <div class="container-404">
        <div class="text-content">
            <p class="error-label">Error 404</p>
            <h1 class="error-title">Halaman tidak ditemukan</h1>
            <p class="error-description">
                Maaf, halaman yang Anda cari tidak dapat ditemukan atau telah dihapus.
            </p>
            <a href="{{ url()->previous() }}" class="go-back-link">Kembali <i
                    class="fas fa-arrow-left go-back-icon"></i></a>
        </div>
        <div class="image-404">
            <img src="{{ asset('asset/image/404.png') }}" alt="Ilustrasi 404">
            <!-- Placeholder Gambar -->
        </div>
    </div>
</body>

</html>