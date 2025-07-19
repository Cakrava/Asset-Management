<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Layanan TI - DISKOMINFO Kota Pariaman</title>
    <meta name="description"
        content="Layanan Teknologi Informasi untuk instansi di Kota Pariaman. Ajukan kebutuhan pemasangan, perbaikan, atau konsultasi jaringan langsung dari dashboard Anda.">
    <meta name="keywords" content="DISKOMINFO, Pariaman, Layanan TI, Jaringan, Pemasangan, Perbaikan, Konsultasi">
   <!-- [Favicon] icon -->
   @include('layout.icon_tittle')
    <!-- Ganti dengan path favicon Anda -->
    <link href="path/to/favicon.png" rel="icon">
    <link href="path/to/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Source+Sans+Pro:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700;1,900&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ flexible_asset('front/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ flexible_asset('front/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ flexible_asset('front/assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ flexible_asset('front/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ flexible_asset('front/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="{{ flexible_asset('front/assets/css/main.css') }}" rel="stylesheet">

    <style>
        /* CSS Tambahan untuk Form yang Lebih Menarik */
        .form-section {
            padding: 60px 0;
            min-height: 70vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-container {
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }

        .form-container h2 {
            color: var(--accent-color);
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }

        .form-control {
            border-radius: 5px;
            padding: 12px;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 8px rgba(14, 162, 188, 0.2);
        }

        .btn-submit {
            background-color: var(--accent-color);
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-submit:hover {
            background-color: #0d8a9e;
            transform: translateY(-2px);
        }
    </style>
</head>

<body class="index-page">

    <header id="header" class="header d-flex align-items-center sticky-top" style="padding: 5px;">
        <div class="container-fluid position-relative d-flex align-items-center justify-content-between">
            <img src="{{ flexible_asset('asset/image/top_logo.png') }}" alt="Logo DISKOMINFO Pariaman"
                style="width: 200px; height: auto;">


            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="#hero" class="active">Beranda</a></li>
                    <li><a href="#layanan-kami">Layanan</a></li>
                    <li><a href="#tentang">Tentang</a></li>
                    <li><a href="#prosedur-pengajuan">Prosedur</a></li>
                    <li><a href="#testimonials">Testimoni</a></li>
                    <li><a href="#faq-layanan">FAQ</a></li>
                    <li><a href="#galeri">Galeri</a></li>
                    <li><a href="#kontak">Kontak</a></li> <!-- Menambahkan Kontak ke Navigasi Utama -->
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

          
        </div>
    </header>


    <main class="main">

        <!-- Section Form Pengajuan -->
        <section id="form-pengajuan" class="form-section section">
            <div class="container" data-aos="fade-up">

              {{-- ... (kode sebelum form) ... --}}

<div class="form-container">
    
    <!-- Bagian untuk menampilkan notifikasi sukses atau error -->
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0" style="padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <!-- Akhir bagian notifikasi -->

    <div class="text-center" style=" margin-bottom: 40px;">
        <img src="{{ flexible_asset('asset/image/ic_confirmation.png') }}" alt="Logo Konfirmasi" style="width: 300px; height: auto;">
    </div>

    {{-- Arahkan action form ke route yang sudah dibuat --}}
    <form action="{{ route('letter.process.completion') }}" method="post" enctype="multipart/form-data">
        @csrf
        <!-- Token CSRF wajib untuk keamanan -->

        <div class="form-group">
            <label for="nomor_surat">Nomor Transaksi</label>
            {{-- Helper old() akan menjaga nilai input jika validasi gagal --}}
            <input type="text" class="form-control" id="nomor_surat" name="nomor_surat"
                value="{{ old('nomor_surat') }}" placeholder="Masukkan nomor surat resmi Anda" required>
        </div>

        <div class="form-group">
            <label for="lampiran_surat">Lampirkan Surat Tertanda (PDF, PNG, JPG)</label>
            <input type="file" class="form-control" id="lampiran_surat" name="lampiran_surat"
                accept=".pdf,.jpg,.jpeg,.png" required>
        </div>

        <input type="hidden" name="transaction_id" value="{{ $transaction_id }}">
        <div class="text-center">
            <button type="submit" class="btn-submit">Kirim Konfirmasi</button>
        </div>

    </form>
</div>

{{-- ... (kode setelah form) ... --}}

            </div>
        </section>
        <!-- /Section Form Pengajuan -->

    </main>

    <footer id="footer" class="footer dark-background">
        <div class="footer-top">
            <div class="container">
                <div class="row gy-4">
                    <div class="col-lg-5 col-md-12 footer-about">
                        <a href="index.html" class="logo d-flex align-items-center">
                            <span class="sitename">DISKOMINFO Kota Pariaman</span>
                        </a>
                        <p>Menyediakan layanan teknologi informasi yang handal dan terintegrasi untuk mendukung kemajuan
                            Kota Pariaman.</p>
                        <div class="footer-contact pt-3">
                            <p>Jl. Imam Bonjol No. 44</p>
                            <p>Pariaman Tengah, Kota Pariaman</p>
                            <p class="mt-3"><strong>Telepon:</strong> <span>(0751) 93435</span></p>
                            <p><strong>Email:</strong> <span>diskominfo@pariamankota.go.id</span></p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 footer-links">
                        <h4>Tautan Navigasi</h4>
                        <ul>
                            <li><a href="#">Beranda</a></li>
                            <li><a href="#">Layanan</a></li>
                            <li><a href="#">Tentang Kami</a></li>
                            <li><a href="#">Prosedur</a></li>
                        </ul>
                    </div>

                    <div class="col-lg-3 col-md-6 footer-links">
                        <h4>Informasi Lain</h4>
                        <ul>
                            <li><a href="#">FAQ</a></li>
                            <li><a href="#">Galeri</a></li>
                            <li><a href="#">Kontak</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="copyright text-center">
            <div
                class="container d-flex flex-column flex-lg-row justify-content-center justify-content-lg-between align-items-center">
                <div class="d-flex flex-column align-items-center align-items-lg-start">
                    <div>
                        © Hak Cipta <strong><span>DISKOMINFO Kota Pariaman</span></strong>. Seluruh Hak Cipta
                        Dilindungi.
                    </div>
                    <div class="credits">
                        Desain Asli oleh <a href="https://bootstrapmade.com/">BootstrapMade</a>
                    </div>
                </div>
                <div class="social-links order-first order-lg-last mb-3 mb-lg-0">
                    <a href="https://www.facebook.com/profile.php?id=100087503138330"><i class="bi bi-facebook"></i></a>
                    <a href="https://www.instagram.com/diskominfo_pariaman"><i class="bi bi-instagram"></i></a>
                    <a href="https://www.youtube.com/@kominfopariaman"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="{{ flexible_asset('front/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ flexible_asset('front/assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ flexible_asset('front/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ flexible_asset('front/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ flexible_asset('front/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ flexible_asset('front/assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>

    <!-- Main JS File -->
    <script src="{{ flexible_asset('front/assets/js/main.js') }}"></script>

</body>

</html>