<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
    <title>DISKOMDIGI kota Pariaman | Network Device Management</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Komdigi is designed for network device management.">
    <meta name="keywords" content="Komdigi, Network Device, Management, Admin Dashboard">
    <meta name="author" content="Your Name/Organization">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <!-- [Favicon] icon -->
    @include('layout.icon_tittle')
    <!-- [Google Font] Family -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" id="main-font-link">
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="{{ flexible_asset('asset/dist/assets/fonts/tabler-icons.min.css') }}">
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="{{ flexible_asset('asset/dist/assets/fonts/feather.css') }}">
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="{{ flexible_asset('asset/dist/assets/fonts/fontawesome.css') }}">
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="{{ flexible_asset('asset/dist/assets/fonts/material.css') }}">

    <!-- [MODIFIKASI] Menambahkan CSS SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="{{ flexible_asset('asset/dist/assets/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ flexible_asset('asset/dist/assets/css/style-preset.css') }}">

</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <!-- [ Sidebar Menu ] start -->

    @yield('menu')
    <!-- [ Sidebar Menu ] end -->
    <!-- [ Header Topbar ] start -->
    <header class="pc-header">
        <div class="header-wrapper">
            <style>
                .icon-loading-custom { animation: spin 1s linear infinite; }
                @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
            </style>
            <div class="me-auto pc-mob-drp">
                <ul class="list-unstyled">
                    <!-- ======= Menu collapse Icon ===== -->
                    <li class="pc-h-item pc-sidebar-collapse">
                        <a href="#" class="pc-head-link ms-0" id="sidebar-hide"><i class="ti ti-menu-2"></i></a>
                    </li>
                    @php
                        $userId = Auth::id();
                        $profile = \App\Models\Profile::where('user_id', $userId)->first();
                        $namaUser = $profile ? $profile->name : '';
                        $isMobile = request()->header('user-agent') && preg_match('/Mobile|Android|iPhone|iPad|iPod/i', request()->header('user-agent'));
                        $isMessageRoute = Route::currentRouteName() === 'panel.message.user-message';
                    @endphp
                    <div class="dropdown pc-h-item d-inline-flex d-md-none align-items-center">
                        @if ($isMobile && $isMessageRoute)
                            <a href="javascript:void(0);" onclick="var lastVisited = localStorage.getItem('lastVisited'); if (lastVisited) { window.location.href = lastVisited; } else { window.history.back(); }" class="back-button" style="color: #222; font-size: 1.5rem; margin-right: 10px; text-decoration: none; display: flex; align-items: center;">
                                <i class="ti ti-arrow-left"></i>
                            </a>
                            <span class="ms-2" style="font-size: 1em; font-weight: 600;">Chat Admin</span>
                        @else
                            @if(Route::currentRouteName() === 'histories.show')
                                <span class="ms-2" style="font-size: 0.9em;">Riwayat Saya</span>
                            @elseif(Route::currentRouteName() === 'panel.ticket.user-ticket')
                                <span class="ms-2" style="font-size: 0.9em;">Daftar Tiket</span>
                            @elseif(Route::currentRouteName() === 'panel.profile')
                                <span class="ms-2" style="font-size: 0.9em;">Profil Saya</span>
                            @elseif(Route::currentRouteName() === 'panel.account.acountSettings')
                                <span class="ms-2" style="font-size: 0.9em;">Pengaturan Akun</span>
                            @else
                                <span class="ms-2" style="font-size: 0.9em;">Selamat datang, {{ $namaUser }}</span>
                            @endif
                        @endif
                    </div>
                </ul>
            </div>
            <!-- [Mobile Media Block end] -->
            <div class="ms-auto">
                <ul class="list-unstyled">
                    @php
                        use App\Models\Profile;
                        $userId = Auth::id();
                        $profile = Profile::where('user_id', $userId)->first();
                        $image = $profile ? $profile->image : '';
                        $role = Session::get('role');
                    @endphp
                    <li class="dropdown pc-h-item header-user-profile">
                        <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
                             <img src="{{ $image ? asset('storage/' . $image) : asset('asset/image/profile.png') }}" alt="user-image" style=" min-width :30px; min-height: 30px;max-width: 30px; margin-right : 20px;max-height: 30px; border-radius: 100%;">
                                <span>{{ Session::get('name') }}
                                    @if (session('profile_incomplete_badge') == 'yes' || session('istemporary') == true)
                                        <span class="badge bg-danger ms-2" style="width: 10px; height:10px; border-radius: 100%; margin-right: 5px;"></span>
                                    @endif
                                </span>
                        </a>
                        <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                            <div class="dropdown-header">
                                <div class="d-flex mb-1">
                                    <div class="flex-shrink-0">
                                        <img src="{{ $image ? asset('storage/' . $image) : asset('asset/image/profile.png') }}" alt="user-image" style=" min-width :40px; min-height: 40px;max-width: 40px; margin-right : 20px;max-height: 40px; border-radius: 100%;">
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">{{ Session::get('name') }}</h6>
                                        @if(Session::get('role') == 'admin')
                                            <span>Administrator</span>
                                        @elseif(Session::get('role') == 'master')
                                            <span>Master</span>
                                        @else
                                            <span>User</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <ul class="nav drp-tabs nav-fill nav-tabs" id="mydrpTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="drp-t1" data-bs-toggle="tab" data-bs-target="#drp-tab-1" type="button" role="tab" aria-controls="drp-tab-1" aria-selected="true"><i class="ti ti-user"></i> Profile</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="drp-t2" data-bs-toggle="tab" data-bs-target="#drp-tab-2" type="button" role="tab" aria-controls="drp-tab-2" aria-selected="false"><i class="ti ti-settings"></i> Setting
                                        @if (session('istemporary') == true)
                                            <span class="badge bg-danger ms-2" style="width: 10px;height:10px;border-radius :100%; margin-right: 5px;"></span>
                                        @endif
                                    </button>
                                </li>
                            </ul>
                            <div class="tab-content" id="mysrpTabContent">
                                <div class="tab-pane fade show active" id="drp-tab-1" role="tabpanel" aria-labelledby="drp-t1" tabindex="0">
                                    <a href="{{ route('panel.profile') }}" class="dropdown-item">
                                        <i class="ti ti-user"></i>
                                        <span>View Profile</span>
                                        @if (session('profile_incomplete_badge') == 'yes')
                                            <span class="badge bg-danger ms-2" style="width: 10px;height:10px;border-radius :100%; margin-right: 5px;"></span>
                                        @endif
                                    </a>
                                    <!-- [MODIFIKASI] Menghapus atribut data-bs-toggle dan data-bs-target -->
                                    <a href="#" class="dropdown-item" id="logout-button-dropdown">
                                        <i class="ti ti-power"></i>
                                        <span>Logout</span>
                                    </a>
                                </div>
                                <div class="tab-pane fade" id="drp-tab-2" role="tabpanel" aria-labelledby="drp-t2" tabindex="0">
                                    <a href="{{ route('panel.account.acountSettings') }}" class="dropdown-item">
                                        <i class="ti ti-user"></i>
                                        <span>Account Settings</span>
                                        @if (session('istemporary') == true)
                                            <span class="badge bg-danger ms-2" style="width: 10px;height:10px;border-radius :100%; margin-right: 5px;"></span>
                                        @endif
                                    </a>
                                    <a href="{{ route('histories.show') }}" class="dropdown-item">
                                        <i class="ti ti-list"></i>
                                        <span>History</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <!-- [ Header ] end -->

    <!-- [MODIFIKASI] Menghapus HTML untuk Logout Modal -->

    <!-- [ Main Content ] start -->
    @yield('content')
    <!-- [ Main Content ] end -->

    @php
        $isUser = auth()->user()->role == 'user';
    @endphp
    <footer class="pc-footer {{ $isUser ? 'footer-user-mobile-hide' : '' }}">
        <div class="footer-wrapper container-fluid">
            <div class="row">
                <div class="col-sm my-1">
                    <p class="m-0">Komdigi ♥ Dikelola oleh Tim IT Anda.</p>
                </div>
                <div class="col-auto my-1">
                    <ul class="list-inline footer-link mb-0">
                        <li class="list-inline-item"><a href="../index.html">Beranda</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    @if($isUser)
        <style>
            @media (max-width: 767.98px) { .footer-user-mobile-hide { display: none !important; } }
        </style>
    @endif

    <!-- [Page Specific JS] start -->
    <script src="{{ flexible_asset('asset/dist/assets/js/plugins/apexcharts.min.js') }}"></script>
    <script src="{{ flexible_asset('asset/dist/assets/js/pages/dashboard-default.js') }}"></script>
    <!-- [Page Specific JS] end -->
    <!-- Required Js -->
    <script src="{{ flexible_asset('asset/dist/assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ flexible_asset('asset/dist/assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ flexible_asset('asset/dist/assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ flexible_asset('asset/dist/assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ flexible_asset('asset/dist/assets/js/pcoded.js') }}"></script>
    <script src="{{ flexible_asset('asset/dist/assets/js/plugins/feather.min.js') }}"></script>

    <!-- [MODIFIKASI] Menambahkan script SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        layout_change('light');
        change_box_container('false');
        layout_rtl_change('false');
        preset_change("preset-1");
        font_change("Public-Sans");
    </script>
    <script>
        window.onload = function () {
            const userRole = "{{ auth()->user()->role }}";
            const screenWidth = window.innerWidth;

            if (screenWidth <= 768 && userRole === 'admin') {
                console.log('Admin terdeteksi di perangkat mobile. Melakukan logout otomatis.');
                fetch('/logout', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (response.ok) {
                        window.location.href = "{{ route('auth.login') }}?message=" + encodeURIComponent("Admin tidak boleh login di perangkat mobile");
                    } else {
                        console.error('Gagal logout admin di mobile.');
                    }
                })
                .catch(error => {
                    console.error('Terjadi kesalahan saat logout:', error);
                });
            }

            // [MODIFIKASI] Mengubah event listener untuk menggunakan SweetAlert2
            const logoutButton = document.getElementById('logout-button-dropdown');
            if (logoutButton) {
                logoutButton.addEventListener('click', function (e) {
                    e.preventDefault(); // Mencegah navigasi default dari tag <a>

                    Swal.fire({
                        title: 'Konfirmasi Logout',
                        text: "Apakah Anda yakin ingin keluar dari aplikasi?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Logout!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Jika pengguna mengonfirmasi, arahkan ke URL logout
                            window.location.href = "{{ route('auth.logout') }}";
                        }
                    });
                });
            }
        };
    </script>

    @if(Session::get('role') == 'user')
    <script>
        const apiKey = "{{ env('API_GEOCODE') }}";
        const locationButton = document.getElementById('locationButton');
        const locationIcon = document.getElementById('locationIcon');
        const locationText = document.getElementById('locationText');
        const referenceInput = document.getElementById('reference');

        function toggleLoader(isLoading) {
            if (isLoading) {
                locationIcon.classList.add('ti-spin');
                locationText.textContent = "Getting address...";
                if (locationButton) locationButton.disabled = true;
            } else {
                locationIcon.classList.remove('ti-spin');
                if (locationButton) locationButton.disabled = false;
            }
        }

        function getMyLocation() {
            if (!navigator.geolocation) {
                locationText.textContent = "Geolocation not supported.";
                return;
            }
            toggleLoader(true);
            navigator.geolocation.getCurrentPosition(function (position) {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                const coordinateString = `${lat}, ${lon}`;
                referenceInput.value = coordinateString;
                getAddress(lat, lon);
            }, function (error) {
                locationText.textContent = "Error: " + error.message;
                console.error("Error getting location: ", error);
                toggleLoader(false);
            });
        }

        async function getAddress(lat, lon) {
            const url = `https://geocode.maps.co/reverse?lat=${lat}&lon=${lon}&api_key=${apiKey}`;
            try {
                const response = await fetch(url);
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(`HTTP error! status: ${response.status}, message: ${errorData.error || 'Failed to fetch address'}`);
                }
                const data = await response.json();
                if (data.display_name) {
                    locationText.textContent = data.display_name;
                } else {
                    locationText.textContent = "Address not found.";
                }
            } catch (error) {
                locationText.textContent = "Error fetching address.";
                console.error("Error fetching address:", error);
            } finally {
                toggleLoader(false);
            }
        }

        if (locationButton) {
            getMyLocation();
            locationButton.addEventListener('click', function () {
                getMyLocation();
            });
        }
    </script>
    @endif

</body>
</html>