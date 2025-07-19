<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Layanan TI - DISKOMINFO Kota Pariaman</title>
    <meta name="description"
        content="Layanan Teknologi Informasi untuk instansi di Kota Pariaman. Ajukan kebutuhan pemasangan, perbaikan, atau konsultasi jaringan langsung dari dashboard Anda.">
    <meta name="keywords" content="DISKOMINFO, Pariaman, Layanan TI, Jaringan, Pemasangan, Perbaikan, Konsultasi">



    @include('layout.icon_tittle')
    <!-- Ganti dengan path apple touch icon Anda -->

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Source+Sans+Pro:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700;1,900&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ flexible_asset('front/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ flexible_asset('asset/dist/assets/js/plugins/feather.min.js') }}" rel="stylesheet">
    <link href="{{ flexible_asset('front/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ flexible_asset('front/assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ flexible_asset('front/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ flexible_asset('front/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="{{ flexible_asset('front/assets/css/main.css') }}" rel="stylesheet">

    <!-- =======================================================
  * Template Name: HeroBiz
  * Diadaptasi untuk: DISKOMINFO Kota Pariaman
  * Original Template URL: https://bootstrapmade.com/herobiz-bootstrap-business-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
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

            <a class="btn-getstarted" href="{{ route('auth.login') }}">
                {{ Auth::check() ? 'Dashboard' : 'Login' }}
            </a>
            
            <!-- Arahkan ke halaman login Anda (biasanya menggunakan route Laravel) -->

        </div>
    </header>

    <main class="main">

        <!-- Hero Section -->
        <section id="hero" class="hero section">
            <div class="container d-flex flex-column justify-content-center align-items-center text-center position-relative"
                data-aos="zoom-out">
                <img src="{{ flexible_asset('asset/image/hero.png') }}" class="img-fluid animated"
                    alt="Ilustrasi Layanan TI" style="width: 350px; height: auto;">
                <!-- Ganti dengan gambar yang relevan -->
                <h1>Layanan Teknologi Informasi untuk <span>Instansi Anda</span></h1>
                <p>Ajukan kebutuhan pemasangan, perbaikan, atau konsultasi jaringan langsung dari dashboard Anda. Semua
                    aduan Anda ditindaklanjuti oleh DISKOMINFO Kota Pariaman.</p>
                <div class="d-flex">
                    <a href="{{ route('auth.login') }}" class="btn-get-started">Masuk Dashboard</a>
                    <!-- Arahkan ke halaman login Anda (biasanya menggunakan route Laravel) -->
                    <a href="#tentang" class="glightbox btn-watch-video d-flex align-items-center scrollto"><i
                            class="bi bi-info-circle"></i><span>Pelajari Lebih Lanjut</span></a>
                </div>
            </div>
        </section><!-- /Hero Section -->

        <!-- Layanan Kami Section (Sebelumnya Featured Services) -->
        <section id="layanan-kami" class="featured-services section">
            <div class="container section-title" data-aos="fade-up">
                <h2>Layanan Kami</h2>
                <p>Kami menyediakan berbagai layanan teknologi informasi untuk mendukung kebutuhan instansi Anda.</p>
            </div>
            <div class="container">
                <div class="row gy-4">

                    <div class="col-xl-4 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="100">
                        <div class="service-item position-relative">
                            <div class="icon"><i class="bi bi-hdd-network icon"></i></div>
                            <h4><a href="#" class="stretched-link">Deployment</a></h4>
                            <p>Pemasangan perangkat jaringan baru untuk kebutuhan instansi.</p>
                        </div>
                    </div><!-- End Service Item -->

                    <div class="col-xl-4 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="200">
                        <div class="service-item position-relative">
                            <div class="icon"><i class="bi bi-tools icon"></i></div>
                            <h4><a href="#" class="stretched-link">Repairing</a></h4>
                            <p>Layanan perbaikan perangkat jaringan yang bermasalah.</p>
                        </div>
                    </div><!-- End Service Item -->

                    <div class="col-xl-4 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="300">
                        <div class="service-item position-relative">
                            <div class="icon"><i class="bi bi-person-lines-fill icon"></i></div>
                            <h4><a href="#" class="stretched-link">Consultant</a></h4>
                            <p>Konsultasi terkait kebutuhan dan solusi infrastruktur jaringan instansi Anda.</p>
                        </div>
                    </div><!-- End Service Item -->

                </div>
            </div>
        </section><!-- /Layanan Kami Section -->

        <!-- Tentang Section -->
        <section id="tentang" class="about section">
            <div class="container section-title" data-aos="fade-up">
                <h2>Tentang DISKOMINFO Kota Pariaman</h2>
            </div>
            <div class="container" data-aos="fade-up">
                <div class="row g-4 g-lg-5" data-aos="fade-up" data-aos-delay="200">
                    <div class="col-lg-5">
                        <div class="about-img">
                            <img src="{{ flexible_asset('asset/image/kantor_kominfo.png') }}" class="img-fluid"
                                alt="Kantor DISKOMINFO Kota Pariaman"> <!-- Ganti dengan foto kantor atau logo -->
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <h3 class="pt-0 pt-lg-5">Mitra Teknologi Informasi Terpercaya Anda</h3>
                        <p class="fst-italic">
                            Dinas Komunikasi dan Informatika Kota Pariaman bertanggung jawab dalam penyediaan dan
                            pengelolaan infrastruktur teknologi informasi untuk mendukung operasional pemerintahan dan
                            pelayanan publik.
                        </p>
                        <p>
                            Sistem ini dirancang untuk memudahkan instansi-instansi (baik pemerintah maupun
                            non-pemerintah) dalam mengajukan kebutuhan teknis seperti pemasangan, perbaikan, maupun
                            konsultasi jaringan secara terstruktur dan terdokumentasi. Kami berkomitmen untuk memberikan
                            layanan yang responsif dan berkualitas.
                        </p>
                    </div>
                </div>
            </div>
        </section><!-- /Tentang Section -->
        <section id="video" class="video section">
            <div class="container" data-aos="fade-up">
                <div class="section-title">
                    <h2>Kunjungi Youtube Kami</h2>
                    <p>Lihat lebih dekat tentang DISKOMINFO Kota Pariaman.</p>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="video-wrap">
                            <iframe width="100%" height="500" src="https://www.youtube.com/embed/p9CcJhx5qfA"
                                title="YouTube video player" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Prosedur Pengajuan Layanan Section (Sebelumnya Onfocus) -->
        <section id="prosedur-pengajuan" class="onfocus section dark-background">
            <div class="container section-title" data-aos="fade-up">
                <h2 style="color: #fff;">Prosedur Pengajuan Layanan</h2>
                <p style="color: #eee;">Ikuti langkah-langkah mudah berikut untuk mengajukan layanan.</p>
            </div>
            <div class="container-fluid p-0" data-aos="fade-up">
                <div class="row g-0">
                    <!-- Hapus bagian video-play -->
                    <div class="col-lg-12"> <!-- Ubah col-lg-6 menjadi col-lg-12 agar full width -->
                        <div class="content d-flex flex-column justify-content-center h-100 p-5">
                            <!-- Mengganti konten dengan alur visual sederhana -->
                            <div class="row gy-4 text-center">
                                <div class="col-md-3">
                                    <div class="prosedur-item">
                                        <div class="prosedur-icon"><i class="bi bi-box-arrow-in-right"
                                                style="font-size: 3rem;"></i></div>
                                                @auth
                                                <h4>Dashboard</h4>
                                            @else
                                                <h4>Login</h4>
                                            @endauth
                                            
                                        <p>Masuk ke dashboard sistem menggunakan akun instansi Anda.</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="prosedur-item">
                                        <div class="prosedur-icon"><i class="bi bi-ticket-detailed"
                                                style="font-size: 3rem;"></i></div>
                                        <h4>Buat Tiket Aduan</h4>
                                        <p>Isi formulir pengajuan layanan dengan detail kebutuhan Anda.</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="prosedur-item">
                                        <div class="prosedur-icon"><i class="bi bi-ui-checks-grid"
                                                style="font-size: 3rem;"></i></div>
                                        <h4>Pilih Jenis Layanan</h4>
                                        <p>Pilih jenis layanan yang dibutuhkan (Pemasangan / Perbaikan / Konsultasi).
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="prosedur-item">
                                        <div class="prosedur-icon"><i class="bi bi-eye" style="font-size: 3rem;"></i>
                                        </div>
                                        <h4>Lihat Progres Tiket</h4>
                                        <p>Pantau status dan progres penanganan tiket Anda melalui dashboard.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section><!-- /Prosedur Pengajuan Layanan Section -->


        <!-- Testimonials Section -->
        <section id="testimonials" class="testimonials section">
            <!-- Menghapus class dark-background jika tidak diinginkan -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Apa Kata Mereka?</h2>
                <p>Pendapat dari instansi yang telah menggunakan layanan kami.</p>
            </div>
            <!-- <img src="{{ flexible_asset('front/assets/img/testimonials-bg.jpg') }}" class="testimonials-bg" alt="">  opsional, bisa dihapus jika tidak cocok -->
            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="swiper init-swiper">
                    <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "slidesPerView": "auto",
              "pagination": {
                "el": ".swiper-pagination",
                "type": "bullets",
                "clickable": true
              },
              "breakpoints": {
                "320": { "slidesPerView": 1, "spaceBetween": 40 },
                "1200": { "slidesPerView": 3, "spaceBetween": 10 }
              }
            }
          </script>
                    <div class="swiper-wrapper">

                        <div class="swiper-slide">
                            <div class="testimonial-item">
                                <img src="{{ flexible_asset('asset/image/testimoni/instansi-1.png') }}"
                                    class="testimonial-img" alt="Logo Instansi A">
                                <!-- Ganti dengan logo instansi atau ikon generik -->
                                <h3>Instansi ABC</h3>
                                <h4>Dinas Pendidikan</h4> <!-- Contoh jenis instansi -->
                                <!-- <div class="stars"> <i class="bi bi-star-fill"></i> ... </div> -->
                                <!-- Bintang dihapus -->
                                <p>
                                    <i class="bi bi-quote quote-icon-left"></i>
                                    <span>Proses pemasangan cepat dan tepat. Sangat membantu operasional kami.</span>
                                    <i class="bi bi-quote quote-icon-right"></i>
                                </p>
                            </div>
                        </div><!-- End testimonial item -->

                        <div class="swiper-slide">
                            <div class="testimonial-item">
                                <img src="{{ flexible_asset('asset/image/testimoni/instansi-2.png') }}"
                                    class="testimonial-img" alt="Logo Instansi B">
                                <h3>Instansi XYZ</h3>
                                <h4>Kantor Kecamatan</h4>
                                <p>
                                    <i class="bi bi-quote quote-icon-left"></i>
                                    <span>Tim DISKOMINFO sangat responsif terhadap tiket perbaikan yang kami ajukan.
                                        Masalah jaringan cepat teratasi.</span>
                                    <i class="bi bi-quote quote-icon-right"></i>
                                </p>
                            </div>
                        </div><!-- End testimonial item -->

                        <div class="swiper-slide">
                            <div class="testimonial-item">
                                <img src="{{ flexible_asset('asset/image/testimoni/instansi-3.png') }}"
                                    class="testimonial-img" alt="Logo Instansi C">
                                <h3>Sekolah Dasar Negeri 01</h3>
                                <h4>Sekolah</h4>
                                <p>
                                    <i class="bi bi-quote quote-icon-left"></i>
                                    <span>Sangat membantu saat kami butuh penambahan jaringan internal untuk
                                        laboratorium komputer. Konsultasi juga sangat jelas.</span>
                                    <i class="bi bi-quote quote-icon-right"></i>
                                </p>
                            </div>
                        </div><!-- End testimonial item -->

                        <div class="swiper-slide">
                            <div class="testimonial-item">
                                <img src="{{ flexible_asset('asset/image/testimoni/instansi-4.png') }}"
                                    class="testimonial-img" alt="Logo Instansi D">
                                <h3>Puskesmas Sehat</h3>
                                <h4>Layanan Kesehatan</h4>
                                <p>
                                    <i class="bi bi-quote quote-icon-left"></i>
                                    <span>Layanan perbaikan berjalan dengan baik, komunikasi dari tim teknis juga
                                        informatif.</span>
                                    <i class="bi bi-quote quote-icon-right"></i>
                                </p>
                            </div>
                        </div><!-- End testimonial item -->

                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </section><!-- /Testimonials Section -->

        <!-- FAQ Section -->
        <section id="faq-layanan" class="faq section"> <!-- Mengganti ID agar lebih spesifik -->
            <div class="container-fluid">
                <div class="row gy-4">
                    <div class="col-lg-7 d-flex flex-column justify-content-center order-2 order-lg-1">
                        <div class="content px-xl-5" data-aos="fade-up" data-aos-delay="100">
                            <h3><span>Pertanyaan Umum</span><strong> Seputar Layanan Jaringan</strong></h3>
                            <p>
                                Temukan jawaban atas pertanyaan yang sering diajukan terkait layanan kami.
                            </p>
                        </div>
                        <div class="faq-container px-xl-5" data-aos="fade-up" data-aos-delay="200">

                            <div class="faq-item faq-active">
                                <i class="faq-icon bi bi-question-circle"></i>
                                <h3>Bagaimana cara mengajukan permintaan pemasangan jaringan?</h3>
                                <div class="faq-content">
                                    <p>Anda dapat mengajukan permintaan melalui dashboard sistem setelah melakukan
                                        login. Pilih menu "Buat Tiket Aduan" dan pilih jenis layanan "Pemasangan". Isi
                                        formulir yang tersedia dengan lengkap.</p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                            <div class="faq-item">
                                <i class="faq-icon bi bi-question-circle"></i>
                                <h3>Berapa lama waktu penanganan aduan?</h3>
                                <div class="faq-content">
                                    <p>Waktu penanganan aduan bervariasi tergantung tingkat kompleksitas dan
                                        ketersediaan sumber daya. Kami berusaha menangani setiap aduan secepat mungkin.
                                        Anda dapat memantau progresnya melalui dashboard.</p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                            <div class="faq-item">
                                <i class="faq-icon bi bi-question-circle"></i>
                                <h3>Apakah sistem ini bisa digunakan oleh instansi non-pemerintah?</h3>
                                <div class="faq-content">
                                    <p>Ya, sistem ini dirancang untuk dapat digunakan oleh instansi pemerintah maupun
                                        non-pemerintah yang berada di wilayah Kota Pariaman dan membutuhkan layanan
                                        teknis dari DISKOMINFO.</p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                        </div>
                    </div>
                    <div class="col-lg-5 order-1 order-lg-2">
                        <img src="{{ flexible_asset('asset/image/faq.png') }}" class="img-fluid" alt="Ilustrasi FAQ"
                            data-aos="zoom-in" data-aos-delay="100">
                        <!-- Ganti dengan gambar yang relevan -->
                    </div>
                </div>
            </div>
        </section><!-- /Faq Section -->

        <!-- Galeri Section (Sebelumnya Portfolio) -->
        <section id="galeri" class="portfolio section">
            <div class="container section-title" data-aos="fade-up">
                <h2>Galeri Dokumentasi</h2>
                <p>Beberapa dokumentasi kegiatan pemasangan, perbaikan, dan kunjungan teknis.</p>
            </div>
            <div class="container-fluid">
                <div class="isotope-layout" data-layout="masonry" data-sort="original-order">
                    <!-- Menghapus filter portfolio-filters -->
                    <div class="row g-0 isotope-container" data-aos="fade-up" data-aos-delay="200">

                        @for ($i = 1; $i <= 9; $i++)
                            <div class="col-xl-3 col-lg-4 col-md-6 portfolio-item isotope-item">
                                <div class="portfolio-content h-100">
                                    <img src="{{ flexible_asset('asset/image/galeri/' . $i . '.png') }}" class="img-fluid"
                                        alt="Dokumentasi {{ $i }}">
                                    <div class="portfolio-info">
                                        <h4>Dokumentasi {{ $i }}</h4>
                                        <p>Keterangan Dokumentasi {{ $i }}</p>
                                        <a href="{{ flexible_asset('asset/image/galeri/' . $i . '.png') }}"
                                            title="Dokumentasi {{ $i }}" data-gallery="portfolio-gallery-all"
                                            class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                                    </div>
                                </div>
                            </div><!-- End Portfolio Item -->
                        @endfor

                    </div><!-- End Portfolio Container -->


                </div>
            </div>
        </section><!-- /Galeri Section -->

        <!-- Contact Section -->
        <section id="kontak" class="contact section">
            <div class="container section-title" data-aos="fade-up">
                <h2>Kontak Kami</h2>
                <p>Hubungi kami jika Anda memiliki pertanyaan atau membutuhkan bantuan lebih lanjut.</p>
            </div>

            <div class="mb-5">
                <!-- Ganti dengan koordinat Google Maps DISKOMINFO Kota Pariaman -->
                <iframe style="border:0; width: 100%; height: 400px;"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.490650889967!2d100.1200583153099!3d-0.6207739996257816!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2fd51c3c023d1a7f%3A0x296f1c62bd351205!2sKantor%20Walikota%20Pariaman!5e0!3m2!1sid!2sid!4v1678888888888"
                    frameborder="0" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
                <!-- Contoh URL di atas adalah Kantor Walikota Pariaman, silakan disesuaikan -->
            </div>

            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="row justify-content-center">
                    <!-- Menambahkan justify-content-center untuk memusatkan kolom jika tidak full width -->
                    <div class="col-lg-7">
                        <!-- Mengubah dari col-lg-4 menjadi col-lg-7 atau sesuai kebutuhan agar tidak terlalu sempit -->
                        <div class="info-wrap"
                            style="background: #f8f9fa; padding: 30px; border-radius: 8px; box-shadow: 0 0 20px rgba(0,0,0,0.1);">
                            <h3 class="text-center mb-4">Detail Kontak</h3>
                            <p class="text-center mb-4">Kami siap membantu kebutuhan teknologi informasi instansi Anda.
                            </p>

                            <div class="info-item d-flex align-items-start mb-4">
                                <i class="bi bi-geo-alt flex-shrink-0"
                                    style="font-size: 1.5rem; color: var(--accent-color, #0EA2BC); margin-right: 15px; margin-top: 3px;"></i>
                                <div>
                                    <h4>Alamat:</h4>
                                    <p>Jl. Imam Bonjol No. 44, Pariaman Tengah, <br>Kota Pariaman, Sumatera Barat</p>
                                    <!-- Ganti dengan alamat DISKOMINFO -->
                                </div>
                            </div><!-- End Info Item -->

                            <hr class="my-3"> <!-- Garis pemisah opsional -->

                            <div class="info-item d-flex align-items-start mb-4">
                                <i class="bi bi-envelope flex-shrink-0"
                                    style="font-size: 1.5rem; color: var(--accent-color, #0EA2BC); margin-right: 15px; margin-top: 3px;"></i>
                                <div>
                                    <h4>Email:</h4>
                                    <p><a href="mailto:diskominfo@pariamankota.go.id">diskominfo@pariamankota.go.id</a>
                                    </p>
                                    <!-- Ganti dengan email DISKOMINFO -->
                                </div>
                            </div><!-- End Info Item -->

                            <hr class="my-3"> <!-- Garis pemisah opsional -->

                            <div class="info-item d-flex align-items-start">
                                <!-- mb-4 dihilangkan dari item terakhir -->
                                <i class="bi bi-phone flex-shrink-0"
                                    style="font-size: 1.5rem; color: var(--accent-color, #0EA2BC); margin-right: 15px; margin-top: 3px;"></i>
                                <div>
                                    <h4>Telepon:</h4>
                                    <p><a href="tel:075193435">(0751) 93435</a></p>
                                    <!-- Ganti dengan telepon DISKOMINFO -->
                                </div>
                            </div><!-- End Info Item -->
                        </div>
                    </div>
                    <!-- Kolom untuk form (col-lg-8) telah dihapus -->
                </div>
            </div>
        </section><!-- /Contact Section -->
    </main>

    <footer id="footer" class="footer dark-background">
        <div class="footer-top">
            <div class="container">
                <div class="row gy-4">
                    <div class="col-lg-5 col-md-12 footer-about">
                        <a href="index.html" class="logo d-flex align-items-center">
                            <!-- Sesuaikan href ini dengan route Laravel jika perlu -->
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
                            <li><a href="#hero">Beranda</a></li>
                            <li><a href="#layanan-kami">Layanan</a></li>
                            <li><a href="#tentang">Tentang Kami</a></li>
                            <li><a href="#prosedur-pengajuan">Prosedur</a></li>
                        </ul>
                    </div>

                    <div class="col-lg-3 col-md-6 footer-links">
                        <h4>Informasi Lain</h4>
                        <ul>
                            <li><a href="#testimonials">Testimoni</a></li>
                            <li><a href="#faq-layanan">FAQ</a></li>
                            <li><a href="#galeri">Galeri</a></li>
                            <li>
                                <a href="{{ route('auth.login') }}">
                                    {{ Auth::check() ? 'Dashboard' : 'Login' }}
                                </a>
                            </li>
                            
                            <!-- Arahkan ke login (biasanya menggunakan route Laravel) -->
                        </ul>
                    </div>
                    <!-- Hapus kolom footer-links yang tidak terpakai -->
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
                    <!-- Contoh tambahan -->
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
    <script src="{{ flexible_asset('front/assets/vendor/php-email-form/validate.js') }}"></script>
    <!-- File ini mungkin perlu penyesuaian jika formnya dihandle Laravel -->
    <script src="{{ flexible_asset('front/assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ flexible_asset('front/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ flexible_asset('front/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ flexible_asset('front/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ flexible_asset('front/assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>

    <!-- Main JS File -->
    <script src="{{ flexible_asset('front/assets/js/main.js') }}"></script>

</body>

</html>