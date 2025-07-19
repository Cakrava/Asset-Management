@extends('layout.sidebar')

@section('content')
    @include('component.loader')
    <script>
        // Optimasi: Pastikan script dijalankan setelah DOM siap
        document.addEventListener('DOMContentLoaded', function () {
            function tampilkanDivSesuaiUkuran() {
                var isMobile = window.innerWidth <= 768;
                var divProfileMobile = document.getElementById('divProfileMobile');
                var divProfileDesktop = document.getElementById('divProfileDesktop');
                if (divProfileMobile && divProfileDesktop) {
                    divProfileMobile.style.display = isMobile ? 'block' : 'none';
                    divProfileDesktop.style.display = isMobile ? 'none' : 'block';
                }
            }

            // Jalankan saat halaman dimuat
            tampilkanDivSesuaiUkuran();

            // Jalankan juga saat ukuran layar berubah (responsive)
            window.addEventListener('resize', tampilkanDivSesuaiUkuran);
        });
    </script>
    <div id="divProfileMobile" style="display: none;">
        <br>
        <br>
        <br>
        <div class="mobile-app-container">
            @include('layout.bottom-navigation')
            <!-- Top App Bar -->


            <div class="app-content">
                @if(auth()->user()->role == 'user')
                    {{-- Pastikan bottom-navigation ini di-style agar fixed di bawah --}}
                    @include('layout.bottom-navigation')
                @endif

                <!-- [ Profile Card ] start -->
                <div class="mobile-card"> {{-- Mengganti card-profile dengan mobile-card untuk styling baru --}}
                    {{-- Notifikasi dipindahkan ke atas, di luar card atau di dalam app-content --}}
                    @if ($errors->any())
                        <div class="alert alert-danger mobile-alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session()->has('success'))
                        <div class="alert alert-success mobile-alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Navigasi Tab Gaya Mobile --}}
                    <ul class="nav nav-pills mobile-tabs nav-fill mb-3" id="profileTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="profile-info-tab" data-bs-toggle="pill"
                                href="#profile-info-content" role="tab" aria-controls="profile-info-content"
                                aria-selected="true">
                                <i class="ti ti-user me-1"></i> Info
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="edit-profile-tab" data-bs-toggle="pill" href="#edit-profile-content"
                                role="tab" aria-controls="edit-profile-content" aria-selected="false">
                                <i class="ti ti-edit me-1"></i> Edit
                                @if (session('profile_incomplete_badge') == 'yes')
                                    <span class="badge bg-danger ms-1 p-1 rounded-circle"
                                        style="font-size: 0.5em; vertical-align: super;">
                                        <i class="ti ti-alert-circle" style="font-size: 0.8em;"></i>
                                    </span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="change-password-tab" data-bs-toggle="pill"
                                href="#change-password-content" role="tab" aria-controls="change-password-content"
                                aria-selected="false">
                                <i class="ti ti-lock me-1"></i> Password
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" id="profileTabContent">
                        {{-- KONTEN TAB INFO PROFIL --}}
                        <div class="tab-pane fade show active" id="profile-info-content" role="tabpanel"
                            aria-labelledby="profile-info-tab">
                            <div class="mobile-card-body">
                                <div class="text-center mb-4">
                                    @if($profile->image)
                                        <img src="{{ asset('storage/' . $profile->image) }}"
                                            class="img-fluid rounded-circle shadow"
                                            style="width: 120px; height: 120px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('asset/image/profile.png') }}"
                                            class="img-fluid rounded-circle shadow"
                                            style="width: 120px; height: 120px; object-fit: cover;">
                                    @endif
                                </div>

                                <div class="accordion" id="profileAccordionMobile">
                                    <div class="accordion-item mobile-accordion-item">
                                        <h2 class="accordion-header" id="headingBasicMobile">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseBasicMobile" aria-expanded="true"
                                                aria-controls="collapseBasicMobile">
                                                <i class="ti ti-user me-2"></i> Informasi Dasar
                                            </button>
                                        </h2>
                                        <div id="collapseBasicMobile" class="accordion-collapse collapse show"
                                            aria-labelledby="headingBasicMobile" data-bs-parent="#profileAccordionMobile">
                                            <div class="accordion-body">
                                                <p class="mb-2"><strong>Nama:</strong> {{ $profile->name }}</p>
                                                <p class="mb-2"><strong>Email:</strong> {{ $email }}</p>
                                                <p class="mb-2"><strong>Telepon:</strong> {{ $profile->phone ?: '-' }}</p>
                                                <p class="mb-0"><strong>Alamat:</strong> {{ $profile->address ?: '-' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item mobile-accordion-item">
                                        <h2 class="accordion-header" id="headingInstitutionMobile">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseInstitutionMobile"
                                                aria-expanded="false" aria-controls="collapseInstitutionMobile">
                                                <i class="ti ti-building me-2"></i> Informasi Instansi
                                            </button>
                                        </h2>
                                        <div id="collapseInstitutionMobile" class="accordion-collapse collapse"
                                            aria-labelledby="headingInstitutionMobile"
                                            data-bs-parent="#profileAccordionMobile">
                                            <div class="accordion-body">
                                                <p class="mb-2"><strong>Instansi:</strong>
                                                    {{ $profile->institution ?: '-' }}
                                                </p>
                                                <p class="mb-2"><strong>Tipe Instansi:</strong>
                                                    {{ $profile->institution_type ?: '-' }}</p>
                                                <p class="mb-0"><strong>Bergabung Sejak:</strong>
                                                    {{ \Carbon\Carbon::parse($profile->created_at)->format('F Y') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item mobile-accordion-item">
                                        <h2 class="accordion-header" id="headingAdditionalMobile">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseAdditionalMobile"
                                                aria-expanded="false" aria-controls="collapseAdditionalMobile">
                                                <i class="ti ti-link me-2"></i> Informasi Tambahan
                                            </button>
                                        </h2>
                                        <div id="collapseAdditionalMobile" class="accordion-collapse collapse"
                                            aria-labelledby="headingAdditionalMobile"
                                            data-bs-parent="#profileAccordionMobile">
                                            <div class="accordion-body">
                                                <p class="mb-0"><strong>Referensi:</strong> {{ $profile->reference ?: '-' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- KONTEN TAB EDIT PROFIL --}}
                        <div class="tab-pane fade" id="edit-profile-content" role="tabpanel"
                            aria-labelledby="edit-profile-tab">
                            <div class="mobile-card-body">
                                <form action="{{ route('panel.profile.update') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="text-center mb-4">
                                        <div class="position-relative d-inline-block profile-image-container-mobile">
                                            <img id="imagePreviewMobile"
                                                class="rounded-circle shadow-sm cursor-pointer profile-image-mobile"
                                                src="{{ $profile->image ? asset('storage/' . $profile->image) : asset('asset/image/profile.png') }}"
                                                alt="Foto Profile"
                                                onclick="document.getElementById('fileInputMobile').click();" />
                                            <div class="profile-overlay-mobile"
                                                onclick="document.getElementById('fileInputMobile').click();">
                                                <i class="ti ti-camera-plus" style="font-size: 1.5rem;"></i>
                                            </div>
                                        </div>
                                        <input id="fileInputMobile" type="file" accept="image/*" name="profile_image"
                                            onchange="previewImageMobile(this)" style="display: none;" />
                                        <small class="d-block text-muted mt-2">Ketuk gambar untuk mengganti</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="nameMobile" class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control form-control-lg" id="nameMobile" name="name"
                                            value="{{ old('name', $profile->name) }}" placeholder="Nama Lengkap">
                                    </div>
                                    <div class="mb-3">
                                        <label for="emailMobile" class="form-label">Email</label>
                                        <input type="email" class="form-control form-control-lg" id="emailMobile"
                                            value="{{ $email }}" disabled>
                                        <small class="text-muted">Email tidak dapat diubah.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="phoneMobile" class="form-label">Nomor Telepon</label>
                                        <input type="tel" class="form-control form-control-lg" id="phoneMobile" name="phone"
                                            value="{{ old('phone', $profile->phone) }}" placeholder="Nomor Telepon">
                                    </div>
                                    <div class="mb-3">
                                        <label for="institutionMobile" class="form-label">Instansi</label>
                                        <input type="text" class="form-control form-control-lg" id="institutionMobile"
                                            name="institution" value="{{ old('institution', $profile->institution) }}"
                                            placeholder="Nama Instansi">
                                    </div>
                                    <div class="mb-3">
                                        <label for="addressMobile" class="form-label">Alamat Lengkap</label>
                                        <textarea class="form-control form-control-lg" id="addressMobile" name="address"
                                            placeholder="Alamat Lengkap" readonly
                                            rows="3">{{ old('address', $profile->address) }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="referenceMobile" class="form-label">Referensi Lokasi (Lat, Long)</label>
                                        <input type="text" class="form-control form-control-lg" id="referenceMobile"
                                            name="reference" placeholder="Latitude, Longitude" readonly
                                            value="{{ old('reference', $profile->reference) }}">
                                    </div>
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-outline-primary w-100 btn-lg mb-2"
                                            id="getLocationBtnMobile">
                                            <i class="ti ti-map-pin me-1"></i> Dapatkan Lokasi Saat Ini
                                        </button>
                                    </div>
                                    <div class="mb-3">
                                        <label for="institution_typeMobile" class="form-label">Tipe Instansi</label>
                                        <select class="form-select form-select-lg" id="institution_typeMobile"
                                            name="institution_type">
                                            <option value="Pemerintah" @selected(old('institution_type', $profile->institution_type) == 'Pemerintah')>Pemerintah</option>
                                            <option value="Swasta" @selected(old('institution_type', $profile->institution_type) == 'Swasta')>Swasta</option>
                                            <option value="Nirlaba" @selected(old('institution_type', $profile->institution_type) == 'Nirlaba')>Nirlaba</option>
                                            <option value="Pendidikan" @selected(old('institution_type', $profile->institution_type) == 'Pendidikan')>Pendidikan</option>
                                            <option value="Kesehatan" @selected(old('institution_type', $profile->institution_type) == 'Kesehatan')>Kesehatan</option>
                                            <option value="Keuangan" @selected(old('institution_type', $profile->institution_type) == 'Keuangan')>Keuangan</option>
                                            <option value="Teknologi" @selected(old('institution_type', $profile->institution_type) == 'Teknologi')>Teknologi</option>
                                            <option value="Lainnya" @selected(old('institution_type', $profile->institution_type) == 'Lainnya')>Lainnya</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                                        <i class="ti ti-device-floppy me-1"></i> Simpan Perubahan
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- KONTEN TAB UBAH PASSWORD --}}
                        <div class="tab-pane fade" id="change-password-content" role="tabpanel"
                            aria-labelledby="change-password-tab">
                            <div class="mobile-card-body">
                                <form action="{{ route('panel.profile.changePassword') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="current_password_mobile" class="form-label">Password Saat Ini</label>
                                        <div class="password-input-group-mobile">
                                            <input type="password"
                                                class="form-control form-control-lg password-field-mobile"
                                                id="current_password_mobile" name="current_password">
                                            <span class="password-toggle-icon-mobile password-toggle-mobile">
                                                <i class="ti ti-eye-off"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password_mobile" class="form-label">Password Baru</label>
                                        <div class="password-input-group-mobile">
                                            <input type="password"
                                                class="form-control form-control-lg password-field-mobile"
                                                id="new_password_mobile" name="new_password">
                                            <span class="password-toggle-icon-mobile password-toggle-mobile">
                                                <i class="ti ti-eye-off"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="confirm_password_mobile" class="form-label">Konfirmasi Password
                                            Baru</label>
                                        <div class="password-input-group-mobile">
                                            <input type="password"
                                                class="form-control form-control-lg password-field-mobile"
                                                id="confirm_password_mobile" name="confirm_password">
                                            <span class="password-toggle-icon-mobile password-toggle-mobile">
                                                <i class="ti ti-eye-off"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                                        <i class="ti ti-key me-1"></i> Ubah Password
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ Profile Card ] end -->
            </div>
        </div>

        <style>
            /* Basic Reset & App Container */
            body,
            html {
                margin: 0;
                padding: 0;
                height: 100%;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                background-color: #f0f2f5;
                /* Common app background color */
            }

            .mobile-app-container {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
                /* Full viewport height */
            }

            /* App Header (Top Bar) */
            .app-header {
                background-color: #0EA2BC;
                /* Primary color */
                color: white;
                padding: 12px 15px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                position: sticky;
                /* Atau fixed jika tidak ada konten di atasnya */
                top: 0;
                z-index: 1030;
                /* Di atas konten lain */
                height: 56px;
                /* Standard app bar height */
                display: flex;
                align-items: center;
            }

            .app-header-content {
                display: flex;
                align-items: center;
                width: 100%;
            }

            .app-header-back {
                color: white;
                font-size: 1.5rem;
                margin-right: 15px;
                text-decoration: none;
            }

            .app-header-title {
                font-size: 1.2rem;
                font-weight: 500;
                margin: 0;
                flex-grow: 1;
            }

            .app-header-menu-btn {
                background: none;
                border: none;
                color: white;
                font-size: 1.5rem;
                padding: 0;
            }


            /* App Content Area */
            .app-content {
                flex-grow: 1;
                padding: 15px;
                overflow-y: auto;
                /* Agar konten bisa di-scroll jika melebihi layar */
                padding-bottom: 70px;
                /* Ruang untuk bottom navigation jika ada */
            }

            /* Mobile Card Style */
            .mobile-card {
                background-color: #ffffff;
                border-radius: 12px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
                margin-bottom: 15px;
                overflow: hidden;
                /* Untuk menjaga border-radius */
            }

            .mobile-card-body {
                padding: 20px;
            }

            /* Mobile Tabs */
            .mobile-tabs {
                background-color: #f8f9fa;
                /* Slightly different from card body */
                border-bottom: 1px solid #dee2e6;
                padding-top: 5px;
                /* Small padding */
            }

            .mobile-tabs .nav-link {
                color: #6c757d;
                /* Abu-abu untuk tab tidak aktif */
                border: none;
                border-bottom: 3px solid transparent;
                padding: 10px 5px;
                /* Padding lebih touch-friendly */
                font-size: 0.9rem;
                font-weight: 500;
                transition: color 0.2s ease, border-color 0.2s ease;
            }

            .mobile-tabs .nav-link.active {
                color: #0EA2BC;
                /* Warna primary untuk tab aktif */
                border-bottom-color: #0EA2BC;
                background-color: transparent !important;
            }

            .mobile-tabs .nav-link i {
                display: block;
                /* Ikon di atas teks */
                font-size: 1.2rem;
                /* Ukuran ikon sedikit lebih besar */
                margin-bottom: 2px;
            }

            /* Accordion for Mobile */
            .mobile-accordion-item {
                border: none;
                /* Hapus border bawaan accordion item */
                border-bottom: 1px solid #eee;
                /* Garis pemisah tipis */
            }

            .mobile-accordion-item:last-child {
                border-bottom: none;
            }

            .accordion-button {
                font-size: 1rem;
                padding: 15px 20px;
                background-color: #fff;
                color: #333;
            }

            .accordion-button:not(.collapsed) {
                color: #0EA2BC;
                background-color: #f8f9fa;
                box-shadow: none;
            }

            .accordion-button:focus {
                box-shadow: none;
                border-color: transparent;
            }

            .accordion-body {
                padding: 15px 20px;
                font-size: 0.95rem;
            }

            .accordion-body p {
                color: #555;
            }

            .accordion-body strong {
                color: #333;
            }


            /* Form Elements */
            .form-label {
                font-weight: 500;
                color: #495057;
                margin-bottom: 0.5rem;
                font-size: 0.9rem;
            }

            .form-control-lg,
            .form-select-lg {
                padding: 0.8rem 1rem;
                font-size: 1rem;
                border-radius: 8px;
                border: 1px solid #ced4da;
            }

            .form-control-lg:focus,
            .form-select-lg:focus {
                border-color: #0EA2BC;
                box-shadow: 0 0 0 0.2rem rgba(14, 162, 188, 0.25);
            }

            .btn-lg {
                padding: 0.8rem 1.5rem;
                font-size: 1rem;
                border-radius: 8px;
            }

            .btn-primary {
                /* Pastikan warna primary konsisten */
                background-color: #0EA2BC;
                border-color: #0EA2BC;
            }

            .btn-primary:hover {
                background-color: #0c8a9e;
                border-color: #0c8a9e;
            }

            .btn-outline-primary {
                color: #0EA2BC;
                border-color: #0EA2BC;
            }

            .btn-outline-primary:hover {
                background-color: rgba(14, 162, 188, 0.1);
                color: #0EA2BC;
            }


            /* Profile Image Editing */
            .profile-image-container-mobile {
                position: relative;
                display: inline-block;
            }

            .profile-image-mobile {
                width: 120px;
                height: 120px;
                object-fit: cover;
                object-position: center;
                border: 3px solid #eee;
                cursor: pointer;
                transition: border-color 0.3s ease;
            }

            .profile-image-mobile:hover {
                border-color: #0EA2BC;
            }

            .profile-overlay-mobile {
                position: absolute;
                bottom: 5px;
                right: 5px;
                width: 30px;
                height: 30px;
                border-radius: 50%;
                background-color: rgba(14, 162, 188, 0.8);
                /* Primary color with transparency */
                color: white;
                display: flex;
                justify-content: center;
                align-items: center;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            .profile-overlay-mobile:hover {
                background-color: #0EA2BC;
            }


            /* Password Toggle Mobile */
            .password-input-group-mobile {
                position: relative;
            }

            .password-field-mobile {
                padding-right: 40px !important;
                /* Space for icon */
            }

            .password-toggle-icon-mobile {
                position: absolute;
                top: 50%;
                right: 12px;
                transform: translateY(-50%);
                cursor: pointer;
                color: #6c757d;
                font-size: 1.2rem;
            }

            /* Alert styling for mobile */
            .mobile-alert {
                margin: 0 20px 15px 20px;
                /* Sesuai padding mobile-card-body */
                border-radius: 8px;
            }

            .mobile-alert:first-child {
                /* Jika alert adalah elemen pertama di card */
                margin-top: 20px;
            }

            /* Placeholder for bottom navigation styling (jika ada) */
            .bottom-nav-bar {
                /* Asumsikan ini class utama di bottom-navigation.blade.php */
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                height: 60px;
                background-color: #fff;
                border-top: 1px solid #e0e0e0;
                display: flex;
                justify-content: space-around;
                align-items: center;
                z-index: 1000;
                box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.05);
            }

            .bottom-nav-bar a {
                color: #757575;
                text-decoration: none;
                text-align: center;
                padding: 5px;
                font-size: 0.75rem;
            }

            .bottom-nav-bar a.active {
                color: #0EA2BC;
                /* Primary color */
            }

            .bottom-nav-bar a i {
                display: block;
                font-size: 1.5rem;
                margin-bottom: 2px;
            }
        </style>

        <script>
            // Image Preview (Mobile version)
            function previewImageMobile(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        document.getElementById('imagePreviewMobile').src = e.target.result;
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            // Password Toggle (Mobile version)
            document.addEventListener('DOMContentLoaded', function () {
                const passwordInputGroupsMobile = document.querySelectorAll('.password-input-group-mobile');

                passwordInputGroupsMobile.forEach(inputGroup => {
                    const passwordInput = inputGroup.querySelector('.password-field-mobile');
                    const passwordToggle = inputGroup.querySelector('.password-toggle-mobile');
                    if (passwordInput && passwordToggle) { // Pastikan elemen ada
                        const eyeIcon = passwordToggle.querySelector('i');

                        passwordToggle.addEventListener('click', function () {
                            if (passwordInput.type === 'password') {
                                passwordInput.type = 'text';
                                eyeIcon.classList.remove('ti-eye-off');
                                eyeIcon.classList.add('ti-eye');
                            } else {
                                passwordInput.type = 'password';
                                eyeIcon.classList.remove('ti-eye');
                                eyeIcon.classList.add('ti-eye-off');
                            }
                        });
                    }
                });

                // Geolocation (Mobile version)
                const getLocationBtnMobile = document.getElementById('getLocationBtnMobile');
                if (getLocationBtnMobile) {
                    const apiKeyMobile = "{{ env('API_GEOCODE') }}";
                    getLocationBtnMobile.addEventListener('click', function () {
                        getMyLocationMobile();
                    });

                    function getMyLocationMobile() {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function (position) {
                                const lat = position.coords.latitude;
                                const lon = position.coords.longitude;
                                const coordinateString = `${lat}, ${lon}`;
                                const referenceInput = document.querySelector('input[name="reference"]#referenceMobile');
                                if (referenceInput) referenceInput.value = coordinateString;
                                getAddressMobile(lat, lon);
                            }, function (error) {
                                alert("Error mendapatkan lokasi: " + error.message);
                            });
                        } else {
                            alert("Geolocation tidak didukung oleh browser ini.");
                        }
                    }

                    async function getAddressMobile(lat, lon) {
                        const url = `https://geocode.maps.co/reverse?lat=${lat}&lon=${lon}&api_key=${apiKeyMobile}`;
                        const addressInput = document.querySelector('textarea[name="address"]#addressMobile');

                        try {
                            const response = await fetch(url);
                            const data = await response.json();
                            if (data.display_name && addressInput) {
                                addressInput.value = data.display_name;
                            } else if (addressInput) {
                                addressInput.value = "Alamat tidak ditemukan.";
                            }
                        } catch (error) {
                            if (addressInput) addressInput.value = "Error mengambil data alamat.";
                            console.error("Error fetching address:", error);
                        }
                    }
                }

                // Handle tab activation from URL hash if needed
                var hash = window.location.hash;
                if (hash) {
                    var triggerEl = document.querySelector('.mobile-tabs a[href="' + hash + '-content"]');
                    if (triggerEl) {
                        var tab = new bootstrap.Tab(triggerEl);
                        tab.show();
                    }
                    // Fallback for initial load of edit/password if they were active before
                    // e.g. if error redirects back to #edit-profile
                    if (hash === '#edit-profile') {
                        var editTabTrigger = document.querySelector('a#edit-profile-tab');
                        if (editTabTrigger) new bootstrap.Tab(editTabTrigger).show();
                    } else if (hash === '#change-password') {
                        var passTabTrigger = document.querySelector('a#change-password-tab');
                        if (passTabTrigger) new bootstrap.Tab(passTabTrigger).show();
                    }
                }

                // Update hash on tab change
                var tabElements = document.querySelectorAll('.mobile-tabs a[data-bs-toggle="pill"]');
                tabElements.forEach(function (tabEl) {
                    tabEl.addEventListener('shown.bs.tab', function (event) {
                        // Get the href attribute, remove "-content" to match hash
                        var newHash = event.target.getAttribute('href').replace('-content', '');
                        if (history.pushState) {
                            history.pushState(null, null, newHash);
                        } else {
                            window.location.hash = newHash;
                        }
                    });
                });

            });
        </script>
    </div>

    <div id="divProfileDesktop" style="display: none;">



        <div class="pc-container">
            <div class="pc-content">
                <!-- [ breadcrumb ] start -->
                @if(auth()->user()->role != 'user')
                    <div class="page-header">
                        <div class="page-block">
                            <div class="row align-items-center">
                                <div class="col-md-12">
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('panel.dashboard') }}">Dashboard</a></li>
                                        <li class="breadcrumb-item" aria-current="page"><a
                                                href="{{ route('panel.profile') }}">Profile</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- [ Profile Card ] start -->
                <div class="card card-profile rounded-lg shadow-md">
                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (session()->has('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-4">
                                <ul class="list-group list-group-flush profile-menu">
                                    <li class="list-group-item profile-menu-item">
                                        <a href="#profile-info" class="nav-link active" data-bs-toggle="tab">
                                            <i class="ti ti-user me-2"></i> Profile
                                        </a>
                                    </li>
                                    <li class="list-group-item profile-menu-item">
                                        <a href="#edit-profile" class="nav-link" data-bs-toggle="tab">
                                            <i class="ti ti-edit me-2"></i> Edit Profile
                                            @if (session('profile_incomplete_badge') == 'yes')
                                                <span class="badge bg-danger ms-2"
                                                    style="width: 10px;height:10px;border-radius :100%; position: absolute;">
                                                </span>
                                            @endif
                                        </a>
                                    </li>
                                    <li class="list-group-item profile-menu-item">
                                        <a href="#change-password" class="nav-link" data-bs-toggle="tab">
                                            <i class="ti ti-lock me-2"></i> Change Password
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-8">
                                <div class="tab-content profile-content">

                                    <div class="tab-pane fade show active" id="profile-info">
                                        <h5 class="mb-4 font-weight-bold "><i class="fas fa-user me-2"></i>
                                            Profile Information</h5>
                                        <hr>
                                        <div class="accordion" id="profileAccordion">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingBasic">
                                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapseBasic" aria-expanded="true"
                                                        aria-controls="collapseBasic">
                                                        <i class="ti ti-user me-2"></i> Basic Information
                                                    </button>
                                                </h2>
                                                <div id="collapseBasic" class="accordion-collapse collapse show"
                                                    aria-labelledby="headingBasic" data-bs-parent="#profileAccordion">
                                                    <div class="accordion-body">
                                                        <p class="mb-2"><i
                                                                class="ti ti-user me-2 text-muted"></i><strong>Name:</strong>
                                                            {{ $profile->name }}</p>
                                                        <p class="mb-2"><i
                                                                class="ti ti-mail me-2 text-muted"></i><strong>Email:</strong>
                                                            {{ $email }}</p>
                                                        <p class="mb-2"><i
                                                                class="ti ti-phone me-2 text-muted"></i><strong>Phone:</strong>
                                                            {{ $profile->phone }}</p>
                                                        <p class="mb-0"><i
                                                                class="ti ti-map-pin me-2 text-muted"></i><strong>Address:</strong>
                                                            {{ $profile->address }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingInstitution">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseInstitution"
                                                        aria-expanded="false" aria-controls="collapseInstitution">
                                                        <i class="ti ti-building me-2"></i> Institution Information
                                                    </button>
                                                </h2>
                                                <div id="collapseInstitution" class="accordion-collapse collapse"
                                                    aria-labelledby="headingInstitution" data-bs-parent="#profileAccordion">
                                                    <div class="accordion-body">
                                                        <p class="mb-2"><i
                                                                class="ti ti-building me-2 text-muted"></i><strong>Institution:</strong>
                                                            {{ $profile->institution }}</p>
                                                        <p class="mb-2"><i
                                                                class="ti ti-briefcase me-2 text-muted"></i><strong>Institution
                                                                Type:</strong> {{ $profile->institution_type }}</p>
                                                        <p class="mb-0"><i
                                                                class="ti ti-calendar-event me-2 text-muted"></i><strong>Joined
                                                                Since:</strong>
                                                            {{ \Carbon\Carbon::parse($profile->created_at)->format('F Y') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingAdditional">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseAdditional"
                                                        aria-expanded="false" aria-controls="collapseAdditional">
                                                        <i class="ti ti-link me-2"></i> Additional Information
                                                    </button>
                                                </h2>
                                                <div id="collapseAdditional" class="accordion-collapse collapse"
                                                    aria-labelledby="headingAdditional" data-bs-parent="#profileAccordion">
                                                    <div class="accordion-body">
                                                        <p class="mb-0"><i
                                                                class="ti ti-map me-2 text-muted"></i><strong>Reference:</strong>
                                                            {{ $profile->reference }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item"> <!-- Item Gambar Profil -->
                                                <h2 class="accordion-header" id="headingImage">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapseImage"
                                                        aria-expanded="false" aria-controls="collapseImage">
                                                        <i class="ti ti-camera me-2"></i> Profile Image
                                                    </button>
                                                </h2>
                                                <div id="collapseImage" class="accordion-collapse collapse"
                                                    aria-labelledby="headingImage" data-bs-parent="#profileAccordion">
                                                    <div class="accordion-body text-center">
                                                        @if($profile->image)
                                                            <img src="{{ asset('storage/' . $profile->image) }}"
                                                                class="img-fluid rounded"
                                                                style="max-width: 150px; max-height: 150px; min-width: 150px; min-height: 150px;">
                                                        @else
                                                            <img src="{{ asset('asset/image/profile.png') }}"
                                                                class="img-fluid rounded"
                                                                style="max-width: 150px; max-height: 150px; min-width: 150px; min-height: 150px;">
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>






                                    <div class="tab-pane fade" id="edit-profile">
                                        <h5 class="mb-4 font-weight-bold "><i class="fas fa-cogs me-2"></i>
                                            Profile Settings</h5>
                                        <hr class="border-primary mb-4">

                                        <form action="{{ route('panel.profile.update') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="row mb-4 gy-3">
                                                <div class="col-md-3 text-center">
                                                    <div class="position-relative d-inline-block profile-image-container">
                                                        <img id="imagePreview"
                                                            class="rounded-circle shadow-sm cursor-pointer profile-image"
                                                            style="width: 140px; height: 140px; object-fit: cover; object-position: center; border: 2px solid #80deea;"
                                                            src="{{ $profile->image ? asset('storage/' . $profile->image) : asset('asset/image/profile.png') }}"
                                                            alt="Foto Profile" />
                                                        <div class="profile-overlay"
                                                            onclick="event.stopPropagation(); document.getElementById('fileInput').click();">
                                                            <p class="profile-overlay-text">
                                                                <i class="fas fa-image me-1"></i> Change Photo
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <input id="fileInput" type="file" accept="image/*" name="profile_image"
                                                        onchange="previewImage(this)" style="display: none;" />
                                                </div>
                                                <div class="col-md-9">
                                                    <div class="row gy-3">
                                                        <div class="col-md-6">
                                                            <label for="name" class="form-label small text-muted">Full
                                                                Name</label>
                                                            <input type="text" class="form-control" id="name" name="name"
                                                                value="{{ old('name', $profile->name) }}"
                                                                placeholder="Full Name">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="email"
                                                                class="form-label small text-muted">Email</label>
                                                            <input type="email" class="form-control" id="email"
                                                                value="{{ $email }}" disabled>
                                                            <small class="text-muted">Email cannot be changed.</small>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="phone" class="form-label small text-muted">Phone
                                                                Number</label>
                                                            <input type="tel" class="form-control" id="phone" name="phone"
                                                                value="{{ old('phone', $profile->phone) }}"
                                                                placeholder="Phone Number">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="institution"
                                                                class="form-label small text-muted">Institution</label>
                                                            <input type="text" class="form-control" id="institution"
                                                                name="institution"
                                                                value="{{ old('institution', $profile->institution) }}"
                                                                placeholder="Institution Name">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="address"
                                                                class="form-label small text-muted">Address</label>
                                                            <input type="text" class="form-control" id="address"
                                                                name="address" placeholder="Full Address" readonly
                                                                value="{{ old('address', $profile->address) }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="reference"
                                                                class="form-label small text-muted">Location
                                                                Reference</label>
                                                            <input type="text" class="form-control" id="reference"
                                                                name="reference" placeholder="Latitude, Longitude" readonly
                                                                value="{{ old('reference', $profile->reference) }}">
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label for="institution_type" class="form-label small text-muted">Institution Type</label>
                                                            <select class="form-select" id="institution_type" name="institution_type">
                                                                {{-- Menambahkan opsi default untuk kasus field belum diisi --}}
                                                                <option value="" disabled @selected(is_null($profile->institution_type))>Pilih Tipe Institusi</option>
                                                                
                                                                <option value="government" @selected(old('institution_type', $profile->institution_type) == 'government')>Pemerintahan</option>
                                                                <option value="private" @selected(old('institution_type', $profile->institution_type) == 'private')>Swasta</option>
                                                                <option value="non_profit" @selected(old('institution_type', $profile->institution_type) == 'non_profit')>Nirlaba</option>
                                                                <option value="education" @selected(old('institution_type', $profile->institution_type) == 'education')>Pendidikan</option>
                                                                <option value="health" @selected(old('institution_type', $profile->institution_type) == 'health')>Kesehatan</option>
                                                                <option value="finance" @selected(old('institution_type', 'user.profile.institution_type') == 'finance')>Keuangan</option>
                                                                <option value="technology" @selected(old('institution_type', $profile->institution_type) == 'technology')>Teknologi</option>
                                                                <option value="other" @selected(old('institution_type', $profile->institution_type) == 'other')>Lainnya</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end mt-3">
                                                <button type="button" class="btn btn-outline-primary me-2"
                                                    id="getLocationBtn">
                                                    <i class="fas fa-location-crosshairs"></i> Get Location
                                                </button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-check-circle me-1"></i> Save
                                                </button>
                                            </div>
                                        </form>
                                    </div>


                                    <style>
                                        .text-primary {
                                            color: #0EA2BC;
                                            /* Warna biru Bootstrap primary */
                                        }

                                        .border-primary {
                                            border-color: #0EA2BC;
                                        }

                                        .bg-primary {
                                            background-color: #0EA2BC;
                                        }

                                        .btn-primary {
                                            background-color: #0EA2BC;
                                            color: white;
                                            border-color: #0EA2BC;
                                        }

                                        .btn-primary:hover {
                                            background-color: #0EA2BC;
                                            /* Warna biru lebih gelap saat hover */
                                            border-color: #0EA2BC;
                                        }

                                        .btn-outline-primary {
                                            color: #0EA2BC;
                                            border-color: #0EA2BC;
                                        }

                                        .btn-outline-primary:hover {
                                            background-color: #e0f2ff;
                                            /* Warna latar belakang lebih terang saat hover */
                                        }

                                        /* Style tambahan untuk overlay */
                                        .profile-image-container {
                                            position: relative;
                                            display: inline-block;
                                            /* Penting agar overlay bekerja dengan benar */
                                        }

                                        .profile-overlay {
                                            position: absolute;
                                            top: 0;
                                            left: 0;
                                            width: 100%;
                                            height: 100%;
                                            border-radius: 50%;
                                            /* Agar overlay berbentuk lingkaran */
                                            background-color: rgba(0, 0, 0, 0.5);
                                            /* Warna overlay hitam semi-transparan */
                                            display: flex;
                                            justify-content: center;
                                            align-items: center;
                                            opacity: 0;
                                            /* Awalnya tidak terlihat */
                                            transition: opacity 0.3s ease;
                                            /* Animasi transisi opacity */
                                            cursor: pointer;
                                            /* Tambahkan cursor pointer agar jelas bisa diklik */
                                        }

                                        .profile-image-container:hover .profile-overlay {
                                            opacity: 1;
                                            /* Tampilkan overlay saat hover */
                                        }

                                        .profile-overlay-text {
                                            color: white;
                                            font-weight: bold;
                                            font-size: 0.9em;
                                            /* Ukuran teks dikecilkan menjadi 0.9em (dari 1.2em) */
                                            text-align: center;
                                            display: flex;
                                            /* Agar icon dan text sejajar horizontal */
                                            align-items: center;
                                            /* Agar icon dan text sejajar vertical ditengah */
                                        }
                                    </style>

                                    <div class="tab-pane fade" id="change-password">
                                        <h5 class="mb-4 font-weight-bold "><i class="fas fa-lock me-2"></i>
                                            Change Password
                                        </h5>
                                        <hr>

                                        <form action="{{ route('panel.profile.changePassword') }}" method="POST">
                                            @csrf
                                            <div class="mb-2"> {{-- Margin bawah diperkecil --}}
                                                <label for="current_password" class="form-label small text-muted">Current
                                                    Password</label> {{-- Label dikecilkan dan warna diubah --}}
                                                <div class="password-input-group">
                                                    <input type="password"
                                                        class="form-control form-control-sm password-field" {{--
                                                        form-control-sm ditambahkan --}} id="current_password"
                                                        name="current_password">
                                                    <span class="password-toggle-icon password-toggle">
                                                        <i class="ti ti-eye-off"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="mb-2"> {{-- Margin bawah diperkecil --}}
                                                <label for="new_password" class="form-label small text-muted">New
                                                    Password</label> {{-- Label dikecilkan dan warna diubah --}}
                                                <div class="password-input-group">
                                                    <input type="password"
                                                        class="form-control form-control-sm password-field" {{--
                                                        form-control-sm ditambahkan --}} id="new_password"
                                                        name="new_password">
                                                    <span class="password-toggle-icon password-toggle">
                                                        <i class="ti ti-eye-off"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="confirm_password" class="form-label small text-muted">Confirm
                                                    New
                                                    Password</label> {{-- Label dikecilkan dan warna diubah --}}
                                                <div class="password-input-group">
                                                    <input type="password"
                                                        class="form-control form-control-sm password-field" {{--
                                                        form-control-sm ditambahkan --}} id="confirm_password"
                                                        name="confirm_password">
                                                    <span class="password-toggle-icon password-toggle">
                                                        <i class="ti ti-eye-off"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-outline-primary btn-sm">Change
                                                Password</button>
                                            {{-- btn-outline-primary dan btn-sm digunakan --}}
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ Profile Card ] end -->
            </div>
        </div>

        <style>
            .card-body {
                padding-left: 5px;
                /* Sesuaikan padding kiri card-body agar konten di dalamnya tetap punya jarak */
                padding-right: 15px;
                /* Sesuaikan padding kanan card-body agar konten di dalamnya tetap punya jarak */
            }

            .card-profile {
                border-radius: 15px;
                overflow: hidden;
            }

            .profile-menu .list-group-item.profile-menu-item {
                border: none;
                background-color: transparent;
            }

            .profile-menu .nav-link {
                color: #333;
                font-size: 0.9rem;
                padding: 0.5rem 1.25rem;
                margin-top: -10px;
                margin-bottom: -10px;
                border-radius: 0.25rem;
                transition: background-color 0.3s ease;
            }

            .profile-menu .nav-link:hover,
            .profile-menu .nav-link.active {
                background-color: #e9ecef;
                color: #0EA2BC;
                /* Warna biru untuk menu aktif/hover */
            }

            .profile-content {
                padding-left: 10px;
            }

            .profile-content h5 {
                color: #495057;
            }

            /* CSS for Password Toggle in Tab Content (General) */
            .tab-pane.fade .password-input-group {
                position: relative;
            }

            .tab-pane.fade .password-input-group .form-control.password-field {
                /* Target input dengan class password-field */
                padding-right: 30px;
            }

            .tab-pane.fade .password-toggle-icon.password-toggle {
                /* Target icon dengan class password-toggle */
                position: absolute;
                top: 50%;
                right: 5px;
                transform: translateY(-50%);
                cursor: pointer;
                opacity: 0.7;
            }

            .tab-pane.fade .password-toggle-icon.password-toggle:hover {
                opacity: 1;
            }

            .tab-pane.fade #edit-profile .row.mb-4 {
                display: flex;
                align-items: center;
            }

            .tab-pane.fade #edit-profile .col-md-4 {
                /* Tetap style kolom gambar */
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .tab-pane.fade #edit-profile .col-md-8 {
                /* Style kolom info (nama, email) - Padding kanan diubah */
                padding-right: 20px;
                /* Ganti padding-left jadi padding-right */
                padding-left: 0;
                /* Hilangkan padding kiri jika ada */
                text-align: right;
                /* Optional: Jika ingin teks rata kanan di kolom info */
            }

            .profile-image {
                opacity: 1;
                /* Opacity awal (penuh) */
                transition: opacity 0.3s ease;
                /* Transisi untuk efek smooth */
            }

            .profile-image:hover {
                opacity: 0.8;
                border-color: #0EA2BC;
                transform: scale(1.05);
                transition: transform 0.3s ease, opacity 0.3s ease;
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const passwordInputGroups = document.querySelectorAll('.password-input-group'); // Select all input groups

                passwordInputGroups.forEach(inputGroup => { // Loop through each input group
                    const passwordInput = inputGroup.querySelector('.password-field'); // Cari input di dalam group
                    const passwordToggle = inputGroup.querySelector('.password-toggle'); // Cari toggle di dalam group
                    const eyeIcon = passwordToggle.querySelector('i');

                    passwordToggle.addEventListener('click', function () {
                        if (passwordInput.type === 'password') {
                            passwordInput.type = 'text';
                            eyeIcon.classList.remove('ti-eye-off');
                            eyeIcon.classList.add('ti-eye');
                        } else {
                            passwordInput.type = 'password';
                            eyeIcon.classList.remove('ti-eye');
                            eyeIcon.classList.add('ti-eye-off');
                        }
                    });
                });
            });
        </script>
        <script>
            const apiKey = "{{ env('API_GEOCODE') }}";

            document.getElementById('getLocationBtn').addEventListener('click', function () {
                getMyLocation();
            });

            function getMyLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;

                        // Isi input reference dengan koordinat
                        const coordinateString = `${lat}, ${lon}`;
                        const referenceInput = document.getElementById('reference');
    if (referenceInput) referenceInput.value = coordinateString;

    getAddress(lat, lon);
                    }, function (error) {
                        alert("Error getting location: " + error.message);
                    });
                } else {
                    alert("Geolocation is not supported by this browser.");
                }
            }

            async function getAddress(lat, lon) {
                const url = `https://geocode.maps.co/reverse?lat=${lat}&lon=${lon}&api_key=${apiKey}`;

                try {
                    const response = await fetch(url);
                    const data = await response.json();

                    if (data.display_name) {
                        document.querySelector('input[name="address"]').value = data.display_name;
                    } else {
                        document.querySelector('input[name="address"]').value = "Address not found.";
                    }
                } catch (error) {
                    document.querySelector('input[name="address"]').value = "Error fetching address data.";
                    console.error("Error fetching address:", error);
                }
            }

        </script>
        <script>
            function previewImage(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        document.getElementById('imagePreview').src = e.target.result;
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>

    </div>
@endsection