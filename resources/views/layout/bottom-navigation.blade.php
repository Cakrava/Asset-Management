<div class="bottom-navigation d-md-none">
    <ul class="nav justify-content-around">
        <li class="nav-item">
            <a class="nav-link {{ Route::is('panel.dashboard') ? 'active' : '' }}" href="{{ route('panel.dashboard') }}"
                onclick="localStorage.setItem('lastVisited', '{{ route('panel.dashboard') }}')">
                <i class="ti ti-home"></i>
                <span>Dashboard</span>
            </a>
        </li>

        @if(auth()->user()->role == 'user')
            <li class="nav-item">
                <a class="nav-link {{ Route::is('panel.ticket.user-ticket') ? 'active' : '' }}"
                    href="{{ route('panel.ticket.user-ticket') }}"
                    onclick="localStorage.setItem('lastVisited', '{{ route('panel.ticket.user-ticket') }}')">
                    <i class="ti ti-ticket"></i>
                    <span>Ticket</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::is('panel.message.user-message') ? 'active' : '' }}"
                    href="{{ route('panel.message.user-message') }}">
                    <i class="ti ti-mail"></i>
                    <span>Message</span>
                </a>
            </li>
        @endif
        <li class="nav-item">
            <a class="nav-link {{ Route::is('histories.show') ? 'active' : '' }}" href="{{ route('histories.show') }}"
                onclick="localStorage.setItem('lastVisited', '{{ route('histories.show') }}')">
                <i class="ti ti-list"></i>
                <span>History</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::is(patterns: 'panel.profile') ? 'active' : '' }}"
                href="{{ route('panel.profile') }}"
                onclick="localStorage.setItem('lastVisited', '{{ route('panel.profile') }}')">
                <i class="ti ti-user"></i>
                <span>Akun</span>
            </a>
        </li>
    </ul>
</div>
<script>
    // Optional: redirect ke halaman terakhir jika ingin, misal saat load halaman utama
    // if (localStorage.getItem('lastVisited')) {
    //     window.location.href = localStorage.getItem('lastVisited');
    // }
</script>


<style>
    body {
        /* PENTING: Sesuaikan padding-bottom dengan tinggi + margin bottom-navigation */
        /* Jika bottom-nav tingginya ~60px dan margin-bottom 15px, total ~75px */
        padding-bottom: 15px;
        /* Beri sedikit ruang ekstra */
    }

    .bottom-navigation {
        position: fixed;
        bottom: 0;
        /* Mepet ke bawah layar */
        left: 0;
        right: 0;
        /* Mepet ke kanan dan kiri layar */
        width: 100%;
        /* Lebar penuh */
        max-width: 100%;
        /* Batas lebar maksimum agar tidak terlalu lebar di layar besar (tapi tetap mobile-first) */
        background-color: #ffffff;
        border-radius: 20px 20px 0 0;
        /* Sudut atas melengkung 20px */
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        /* Shadow yang lebih menyebar dan lembut */
        z-index: 10;
        padding: 6px 8px;
        /* Padding internal untuk bar */
        overflow: hidden;
        /* Untuk memastikan anak elemen tidak merusak border-radius */
        transition: all 0.3s ease-out;
        /* Transisi untuk semua perubahan */
    }

    .bottom-navigation .nav {
        flex-wrap: nowrap;
    }

    .bottom-navigation .nav-item {
        flex-grow: 1;
        text-align: center;
        position: relative;
        /* Untuk potensi elemen ::after atau ::before */
    }

    .bottom-navigation .nav-link {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 8px 4px;
        /* Padding dalam link */
        color: #868e96;
        /* Warna ikon dan teks sedikit lebih terang/soft */
        text-decoration: none;
        font-size: 0.7rem;
        /* Ukuran font teks sedikit lebih kecil */
        position: relative;
        /* Untuk z-index jika diperlukan */
        transition: color 0.3s ease, transform 0.2s ease-out;
    }

    .bottom-navigation .nav-link i {
        font-size: 1.5rem;
        /* Ikon sedikit lebih besar */
        margin-bottom: 1px;
        /* Jarak ikon dan teks sedikit dikurangi */
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        /* Transisi dengan efek "spring" */
    }

    .bottom-navigation .nav-link span {
        transition: font-weight 0.3s ease, color 0.3s ease;
    }

    /* Style untuk link yang aktif */
    .bottom-navigation .nav-link.active {
        color: #0EA2BC;
        /* Warna utama Anda */
        /* transform: translateY(-2px); /* Efek "pop up" kecil untuk item aktif */
    }

    .bottom-navigation .nav-link.active i {
        transform: scale(1.15) translateY(-3px);
        /* Ikon membesar dan sedikit naik */
    }

    .bottom-navigation .nav-link.active span {
        font-weight: 600;
        /* Teks menjadi tebal */
        color: #0EA2BC;
    }

    .bottom-navigation .nav-link:not(.active)::after {
        content: '';
        /* Perlu ada agar transisi opacity berfungsi */
        position: absolute;
        bottom: 2px;
        left: 50%;
        transform: translateX(-50%);
        width: 6px;
        height: 6px;
        background-color: #0EA2BC;
        border-radius: 50%;
        opacity: 0;
        /* Sembunyikan titik untuk item tidak aktif */
    }


    /* Efek hover untuk item yang tidak aktif */
    .bottom-navigation .nav-link:not(.active):hover {
        color: #495057;
        /* Warna lebih gelap saat hover */
    }

    .bottom-navigation .nav-link:not(.active):hover i {
        transform: scale(1.1);
        /* Ikon sedikit membesar saat hover */
    }
</style>