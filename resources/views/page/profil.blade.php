@extends('layout.sidebar')

@section('content')
    {{-- Dependensi untuk Cropper.js --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    {{-- [MODIFIKASI] CSS untuk intl-tel-input --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

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
                                {{-- [MODIFIKASI] Menambahkan ID pada form --}}
                                <form id="profileFormMobile" action="{{ route('panel.profile.update') }}" method="POST"
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
                                        <input id="fileInputMobile" type="file" accept="image/*" class="profile-image-input" data-preview-element="#imagePreviewMobile" data-hidden-input="#croppedImageInputMobile" style="display: none;" />
                                        <input type="hidden" name="profile_image" id="croppedImageInputMobile">
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
                                        {{-- [MODIFIKASI] Penyesuaian input telepon --}}
                                        <input type="tel" class="form-control form-control-lg w-100" id="phoneMobile" name="phone"
                                            value="{{ old('phone', $profile->phone) }}" placeholder="Nomor Telepon">
                                        
                                        {{-- [MODIFIKASI] Div tersembunyi untuk pesan validasi telepon --}}
                                        <div class="alert alert-danger p-2 mt-2 d-none" id="phone-validation-alert-invalid-mobile" role="alert" style="font-size: 0.8em;">
                                            Nomor telepon yang Anda masukkan tidak valid.
                                            <button type="button" class="btn-close btn-sm" onclick="$(this).parent().addClass('d-none')" aria-label="Close" style="float: right;"></button>
                                        </div>
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
            /* All Mobile Styles... */
            body, html { margin: 0; padding: 0; height: 100%; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f0f2f5; }
            .mobile-app-container { display: flex; flex-direction: column; min-height: 100vh; }
            .app-header { background-color: #0EA2BC; color: white; padding: 12px 15px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); position: sticky; top: 0; z-index: 1030; height: 56px; display: flex; align-items: center; }
            .app-header-content { display: flex; align-items: center; width: 100%; }
            .app-header-back { color: white; font-size: 1.5rem; margin-right: 15px; text-decoration: none; }
            .app-header-title { font-size: 1.2rem; font-weight: 500; margin: 0; flex-grow: 1; }
            .app-header-menu-btn { background: none; border: none; color: white; font-size: 1.5rem; padding: 0; }
            .app-content { flex-grow: 1; padding: 15px; overflow-y: auto; padding-bottom: 70px; }
            .mobile-card { background-color: #ffffff; border-radius: 12px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08); margin-bottom: 15px; overflow: hidden; }
            .mobile-card-body { padding: 20px; }
            .mobile-tabs { background-color: #f8f9fa; border-bottom: 1px solid #dee2e6; padding-top: 5px; }
            .mobile-tabs .nav-link { color: #6c757d; border: none; border-bottom: 3px solid transparent; padding: 10px 5px; font-size: 0.9rem; font-weight: 500; transition: color 0.2s ease, border-color 0.2s ease; }
            .mobile-tabs .nav-link.active { color: #0EA2BC; border-bottom-color: #0EA2BC; background-color: transparent !important; }
            .mobile-tabs .nav-link i { display: block; font-size: 1.2rem; margin-bottom: 2px; }
            .mobile-accordion-item { border: none; border-bottom: 1px solid #eee; }
            .mobile-accordion-item:last-child { border-bottom: none; }
            .accordion-button { font-size: 1rem; padding: 15px 20px; background-color: #fff; color: #333; }
            .accordion-button:not(.collapsed) { color: #0EA2BC; background-color: #f8f9fa; box-shadow: none; }
            .accordion-button:focus { box-shadow: none; border-color: transparent; }
            .accordion-body { padding: 15px 20px; font-size: 0.95rem; }
            .accordion-body p { color: #555; }
            .accordion-body strong { color: #333; }
            .form-label { font-weight: 500; color: #495057; margin-bottom: 0.5rem; font-size: 0.9rem; }
            .form-control-lg, .form-select-lg { padding: 0.8rem 1rem; font-size: 1rem; border-radius: 8px; border: 1px solid #ced4da; }
            .form-control-lg:focus, .form-select-lg:focus { border-color: #0EA2BC; box-shadow: 0 0 0 0.2rem rgba(14, 162, 188, 0.25); }
            .btn-lg { padding: 0.8rem 1.5rem; font-size: 1rem; border-radius: 8px; }
            .btn-primary { background-color: #0EA2BC; border-color: #0EA2BC; }
            .btn-primary:hover { background-color: #0c8a9e; border-color: #0c8a9e; }
            .btn-outline-primary { color: #0EA2BC; border-color: #0EA2BC; }
            .btn-outline-primary:hover { background-color: rgba(14, 162, 188, 0.1); color: #0EA2BC; }
            .profile-image-container-mobile { position: relative; display: inline-block; }
            .profile-image-mobile { width: 120px; height: 120px; object-fit: cover; object-position: center; border: 3px solid #eee; cursor: pointer; transition: border-color 0.3s ease; }
            .profile-image-mobile:hover { border-color: #0EA2BC; }
            .profile-overlay-mobile { position: absolute; bottom: 5px; right: 5px; width: 30px; height: 30px; border-radius: 50%; background-color: rgba(14, 162, 188, 0.8); color: white; display: flex; justify-content: center; align-items: center; cursor: pointer; transition: background-color 0.3s ease; }
            .profile-overlay-mobile:hover { background-color: #0EA2BC; }
            .password-input-group-mobile { position: relative; }
            .password-field-mobile { padding-right: 40px !important; }
            .password-toggle-icon-mobile { position: absolute; top: 50%; right: 12px; transform: translateY(-50%); cursor: pointer; color: #6c757d; font-size: 1.2rem; }
            .mobile-alert { margin: 0 20px 15px 20px; border-radius: 8px; }
            .mobile-alert:first-child { margin-top: 20px; }
            .bottom-nav-bar { position: fixed; bottom: 0; left: 0; right: 0; height: 60px; background-color: #fff; border-top: 1px solid #e0e0e0; display: flex; justify-content: space-around; align-items: center; z-index: 1000; box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.05); }
            .bottom-nav-bar a { color: #757575; text-decoration: none; text-align: center; padding: 5px; font-size: 0.75rem; }
            .bottom-nav-bar a.active { color: #0EA2BC; }
            .bottom-nav-bar a i { display: block; font-size: 1.5rem; margin-bottom: 2px; }
            /* [MODIFIKASI] Style untuk intl-tel-input di mobile */
            .iti { width: 100%; }
        </style>
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

                                        {{-- [MODIFIKASI] Menambahkan ID pada form --}}
                                        <form id="profileFormDesktop" action="{{ route('panel.profile.update') }}" method="POST"
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
                                                    <input id="fileInput" type="file" accept="image/*" class="profile-image-input" data-preview-element="#imagePreview" data-hidden-input="#croppedImageInput" style="display: none;" />
                                                    <input type="hidden" name="profile_image" id="croppedImageInput">
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
                                                            {{-- [MODIFIKASI] Penyesuaian input telepon --}}
                                                            <input type="tel" class="form-control w-100" id="phone" name="phone"
                                                                value="{{ old('phone', $profile->phone) }}"
                                                                placeholder="Phone Number">
                                                            
                                                            {{-- [MODIFIKASI] Div tersembunyi untuk pesan validasi telepon --}}
                                                            <div class="alert alert-danger p-1 mt-1 d-none" id="phone-validation-alert-invalid-desktop" role="alert" style="font-size: 0.8em;">
                                                                Nomor telepon tidak valid.
                                                            </div>
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
                                                    id="getLocationBtnDesktop">
                                                    <i class="fas fa-location-crosshairs"></i> Get Location
                                                </button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-check-circle me-1"></i> Save
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    <style>
                                        /* All Desktop Styles... */
                                        .text-primary { color: #0EA2BC; } .border-primary { border-color: #0EA2BC; } .bg-primary { background-color: #0EA2BC; } .btn-primary { background-color: #0EA2BC; color: white; border-color: #0EA2BC; } .btn-primary:hover { background-color: #0EA2BC; border-color: #0EA2BC; } .btn-outline-primary { color: #0EA2BC; border-color: #0EA2BC; } .btn-outline-primary:hover { background-color: #e0f2ff; } .profile-image-container { position: relative; display: inline-block; } .profile-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border-radius: 50%; background-color: rgba(0, 0, 0, 0.5); display: flex; justify-content: center; align-items: center; opacity: 0; transition: opacity 0.3s ease; cursor: pointer; } .profile-image-container:hover .profile-overlay { opacity: 1; } .profile-overlay-text { color: white; font-weight: bold; font-size: 0.9em; text-align: center; display: flex; align-items: center; }
                                        /* [MODIFIKASI] Style untuk intl-tel-input di desktop */
                                        .iti { width: 100%; }
                                        .iti__country-list { z-index: 1056; }
                                    </style>

                                    <div class="tab-pane fade" id="change-password">
                                        <h5 class="mb-4 font-weight-bold "><i class="fas fa-lock me-2"></i>
                                            Change Password
                                        </h5>
                                        <hr>

                                        <form action="{{ route('panel.profile.changePassword') }}" method="POST">
                                            @csrf
                                            <div class="mb-2">
                                                <label for="current_password" class="form-label small text-muted">Current Password</label>
                                                <div class="password-input-group">
                                                    <input type="password" class="form-control form-control-sm password-field" id="current_password" name="current_password">
                                                    <span class="password-toggle-icon password-toggle"><i class="ti ti-eye-off"></i></span>
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <label for="new_password" class="form-label small text-muted">New Password</label>
                                                <div class="password-input-group">
                                                    <input type="password" class="form-control form-control-sm password-field" id="new_password" name="new_password">
                                                    <span class="password-toggle-icon password-toggle"><i class="ti ti-eye-off"></i></span>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="confirm_password" class="form-label small text-muted">Confirm New Password</label>
                                                <div class="password-input-group">
                                                    <input type="password" class="form-control form-control-sm password-field" id="confirm_password" name="confirm_password">
                                                    <span class="password-toggle-icon password-toggle"><i class="ti ti-eye-off"></i></span>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-outline-primary btn-sm">Change Password</button>
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
            .card-body { padding-left: 5px; padding-right: 15px; } .card-profile { border-radius: 15px; overflow: hidden; } .profile-menu .list-group-item.profile-menu-item { border: none; background-color: transparent; } .profile-menu .nav-link { color: #333; font-size: 0.9rem; padding: 0.5rem 1.25rem; margin-top: -10px; margin-bottom: -10px; border-radius: 0.25rem; transition: background-color 0.3s ease; } .profile-menu .nav-link:hover, .profile-menu .nav-link.active { background-color: #e9ecef; color: #0EA2BC; } .profile-content { padding-left: 10px; } .profile-content h5 { color: #495057; } .tab-pane.fade .password-input-group { position: relative; } .tab-pane.fade .password-input-group .form-control.password-field { padding-right: 30px; } .tab-pane.fade .password-toggle-icon.password-toggle { position: absolute; top: 50%; right: 5px; transform: translateY(-50%); cursor: pointer; opacity: 0.7; } .tab-pane.fade .password-toggle-icon.password-toggle:hover { opacity: 1; } .tab-pane.fade #edit-profile .row.mb-4 { display: flex; align-items: center; } .tab-pane.fade #edit-profile .col-md-4 { display: flex; flex-direction: column; align-items: center; text-align: center; } .tab-pane.fade #edit-profile .col-md-8 { padding-right: 20px; padding-left: 0; text-align: right; } .profile-image { opacity: 1; transition: opacity 0.3s ease; } .profile-image:hover { opacity: 0.8; border-color: #0EA2BC; transform: scale(1.05); transition: transform 0.3s ease, opacity 0.3s ease; }
        </style>
    </div>

  <!-- Modal Cropping Gambar -->
    <div class="modal fade" id="cropImageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <div class="btn-toolbar w-100" role="toolbar">
                        <div class="btn-group btn-group-sm me-2"><button class="btn btn-light" id="rotateLeft" title="Putar Kiri"><i class="ti ti-rotate-2"></i></button><button class="btn btn-light" id="rotateRight" title="Putar Kanan"><i class="ti ti-rotate-clockwise-2"></i></button></div>
                        <div class="btn-group btn-group-sm me-2"><button class="btn btn-light" id="flipHorizontal" title="Balik Horizontal"><i class="ti ti-arrows-horizontal"></i></button><button class="btn btn-light" id="flipVertical" title="Balik Vertikal"><i class="ti ti-arrows-vertical"></i></button></div>
                        <div class="btn-group btn-group-sm"><button class="btn btn-light" id="zoomIn" title="Perbesar"><i class="ti ti-zoom-in"></i></button><button class="btn btn-light" id="zoomOut" title="Perkecil"><i class="ti ti-zoom-out"></i></button></div>
                        <div class="ms-auto"><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="img-container mb-3" style="height: 400px;"><img id="imageToCrop" style="max-height: 100%;"></div>
                    <div class="row gx-3">
                        <div class="col"><label class="small">Brightness</label><input type="range" class="form-range" id="brightness" value="100" min="0" max="200" data-filter="brightness"></div>
                        <div class="col"><label class="small">Contrast</label><input type="range" class="form-range" id="contrast" value="100" min="0" max="200" data-filter="contrast"></div>
                        <div class="col"><label class="small">Saturate</label><input type="range" class="form-range" id="saturate" value="100" min="0" max="200" data-filter="saturate"></div>
                        <div class="col"><label class="small">Grayscale</label><input type="range" class="form-range" id="grayscale" value="0" min="0" max="100" data-filter="grayscale"></div>
                        <div class="col"><label class="small">Hue</label><input type="range" class="form-range" id="hue-rotate" value="0" min="0" max="360" data-filter="hue-rotate"></div>
                    </div>
                </div>
                <div class="modal-footer"><button class="btn btn-light" id="resetFilters">Reset</button><div class="flex-grow-1"></div><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" id="cropAndSaveButton">Simpan</button></div>
            </div>
        </div>
    </div>
    
    {{-- [MODIFIKASI] Menambahkan JS untuk intl-tel-input --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"></script>

    {{-- Kumpulan Semua Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ==========================================================
            // [MODIFIKASI] SCRIPT UNTUK INPUT TELEPON INTERNASIONAL
            // ==========================================================
            const phoneInputDesktop = document.querySelector("#phone");
            const phoneInputMobile = document.querySelector("#phoneMobile");

            const itiDesktop = window.intlTelInput(phoneInputDesktop, {
                initialCountry: "id",
                separateDialCode: true,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js",
            });

            const itiMobile = window.intlTelInput(phoneInputMobile, {
                initialCountry: "id",
                separateDialCode: true,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js",
            });

            // Handler untuk form desktop
            const formDesktop = document.getElementById('profileFormDesktop');
            if(formDesktop) {
                formDesktop.addEventListener('submit', function(e) {
                    // Sembunyikan peringatan sebelumnya
                    document.getElementById('phone-validation-alert-invalid-desktop').classList.add('d-none');

                    // Cek jika input diisi tapi tidak valid
                    if (phoneInputDesktop.value.trim() && !itiDesktop.isValidNumber()) {
                        e.preventDefault(); // Hentikan submit
                        document.getElementById('phone-validation-alert-invalid-desktop').classList.remove('d-none'); // Tampilkan error
                    } else {
                        // Jika valid atau kosong, set ke format internasional
                        phoneInputDesktop.value = itiDesktop.getNumber();
                    }
                });
            }

            // Handler untuk form mobile
            const formMobile = document.getElementById('profileFormMobile');
            if(formMobile) {
                formMobile.addEventListener('submit', function(e) {
                    // Sembunyikan peringatan sebelumnya
                    document.getElementById('phone-validation-alert-invalid-mobile').classList.add('d-none');

                    // Cek jika input diisi tapi tidak valid
                    if (phoneInputMobile.value.trim() && !itiMobile.isValidNumber()) {
                        e.preventDefault(); // Hentikan submit
                        document.getElementById('phone-validation-alert-invalid-mobile').classList.remove('d-none'); // Tampilkan error
                    } else {
                        // Jika valid atau kosong, set ke format internasional
                        phoneInputMobile.value = itiMobile.getNumber();
                    }
                });
            }

            // ==========================================================
            // SCRIPT LAMA (CROPPING, GEOLOCATION, DLL.)
            // ==========================================================
            const cropImageModalElement = document.getElementById('cropImageModal');
            if (cropImageModalElement) {
                const cropImageModal = new bootstrap.Modal(cropImageModalElement);
                const imageToCrop = document.getElementById('imageToCrop');
                const cropAndSaveButton = document.getElementById('cropAndSaveButton');
                let cropper;
                let currentPreviewElement, currentHiddenInput;

                const rotateLeftBtn = document.getElementById('rotateLeft');
                const rotateRightBtn = document.getElementById('rotateRight');
                const flipHorizontalBtn = document.getElementById('flipHorizontal');
                const flipVerticalBtn = document.getElementById('flipVertical');
                const zoomInBtn = document.getElementById('zoomIn');
                const zoomOutBtn = document.getElementById('zoomOut');
                const filterSliders = document.querySelectorAll('.form-range');
                const resetBtn = document.getElementById('resetFilters');

                let currentFilters = { brightness: 100, contrast: 100, grayscale: 0, saturate: 100, 'hue-rotate': 0 };

                function handleFile(file) {
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            imageToCrop.src = e.target.result;
                            cropImageModal.show();
                        };
                        reader.readAsDataURL(file);
                    }
                }

                document.querySelectorAll('.profile-image-input').forEach(input => {
                    input.addEventListener('change', (event) => {
                        currentPreviewElement = document.querySelector(event.target.dataset.previewElement);
                        currentHiddenInput = document.querySelector(event.target.dataset.hiddenInput);
                        if (event.target.files.length > 0) { handleFile(event.target.files[0]); }
                        event.target.value = '';
                    });
                });

                cropImageModalElement.addEventListener('shown.bs.modal', () => {
                    cropper = new Cropper(imageToCrop, { aspectRatio: 1, viewMode: 1, autoCropArea: 0.9, responsive: true, background: false, guides: false, center: false });
                });

                cropImageModalElement.addEventListener('hidden.bs.modal', () => {
                    if (cropper) { cropper.destroy(); cropper = null; }
                    const cropperImage = cropImageModalElement.querySelector('.cropper-view-box img');
                    if(cropperImage) cropperImage.style.filter = '';
                    if(resetBtn) resetBtn.click();
                });

                if(rotateLeftBtn) rotateLeftBtn.addEventListener('click', () => cropper && cropper.rotate(-90));
                if(rotateRightBtn) rotateRightBtn.addEventListener('click', () => cropper && cropper.rotate(90));
                if(flipHorizontalBtn) flipHorizontalBtn.addEventListener('click', () => cropper && cropper.scaleX(-cropper.getData().scaleX || -1));
                if(flipVerticalBtn) flipVerticalBtn.addEventListener('click', () => cropper && cropper.scaleY(-cropper.getData().scaleY || -1));
                if(zoomInBtn) zoomInBtn.addEventListener('click', () => cropper && cropper.zoom(0.1));
                if(zoomOutBtn) zoomOutBtn.addEventListener('click', () => cropper && cropper.zoom(-0.1));
                
                function applyCssFilters() {
                    const cropperImage = cropImageModalElement.querySelector('.cropper-view-box img');
                    if(cropperImage) {
                        const filterString = Object.entries(currentFilters).map(([key, value]) => {
                            const unit = key === 'hue-rotate' ? 'deg' : '%';
                            return `${key}(${value}${unit})`;
                        }).join(' ');
                        cropperImage.style.filter = filterString;
                    }
                }
                
                filterSliders.forEach(slider => {
                    slider.addEventListener('input', (e) => {
                        currentFilters[e.target.dataset.filter] = e.target.value;
                        applyCssFilters();
                    });
                });

                if(resetBtn) {
                    resetBtn.addEventListener('click', () => {
                        if(cropper) cropper.reset();
                        currentFilters = { brightness: 100, contrast: 100, grayscale: 0, saturate: 100, 'hue-rotate': 0 };
                        applyCssFilters();
                        filterSliders.forEach(slider => { slider.value = currentFilters[slider.dataset.filter]; });
                    });
                }
                
                if(cropAndSaveButton) {
                    cropAndSaveButton.addEventListener('click', () => {
                        if (!cropper) return;
                        
                        const finalCanvas = document.createElement('canvas');
                        const ctx = finalCanvas.getContext('2d');
                        const croppedCanvas = cropper.getCroppedCanvas({ width: 1024, height: 1024, imageSmoothingQuality: 'high' });

                        finalCanvas.width = croppedCanvas.width;
                        finalCanvas.height = croppedCanvas.height;
                        
                        const filterString = Object.entries(currentFilters).map(([key, value]) => {
                            const unit = key === 'hue-rotate' ? 'deg' : '%';
                            return `${key}(${value}${unit})`;
                        }).join(' ');
                        
                        ctx.filter = filterString;
                        ctx.drawImage(croppedCanvas, 0, 0, finalCanvas.width, finalCanvas.height);
                        
                        const finalDataUrl = finalCanvas.toDataURL('image/jpeg', 0.9);
                        
                        currentPreviewElement.src = finalDataUrl;
                        currentHiddenInput.value = finalDataUrl;
                        
                        cropImageModal.hide();
                    });
                }
            }

            const passwordInputGroups = document.querySelectorAll('.password-input-group, .password-input-group-mobile');
            passwordInputGroups.forEach(inputGroup => {
                const passwordInput = inputGroup.querySelector('.password-field, .password-field-mobile');
                const passwordToggle = inputGroup.querySelector('.password-toggle, .password-toggle-mobile');
                if (passwordInput && passwordToggle) {
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

            const getLocationBtnDesktop = document.getElementById('getLocationBtnDesktop');
            if (getLocationBtnDesktop) {
                const apiKey = "{{ env('API_GEOCODE') }}";
                getLocationBtnDesktop.addEventListener('click', function () {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function (position) {
                            const lat = position.coords.latitude;
                            const lon = position.coords.longitude;
                            document.getElementById('reference').value = `${lat}, ${lon}`;
                            fetch(`https://geocode.maps.co/reverse?lat=${lat}&lon=${lon}&api_key=${apiKey}`)
                                .then(response => response.json())
                                .then(data => { if(data.display_name) { document.querySelector('input[name="address"]').value = data.display_name; }})
                                .catch(error => console.error("Error fetching address:", error));
                        }, (error) => alert("Error mendapatkan lokasi: " + error.message));
                    } else { alert("Geolocation tidak didukung oleh browser ini."); }
                });
            }

            const getLocationBtnMobile = document.getElementById('getLocationBtnMobile');
            if (getLocationBtnMobile) {
                const apiKeyMobile = "{{ env('API_GEOCODE') }}";
                getLocationBtnMobile.addEventListener('click', function () {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function (position) {
                            const lat = position.coords.latitude;
                            const lon = position.coords.longitude;
                            document.getElementById('referenceMobile').value = `${lat}, ${lon}`;
                            fetch(`https://geocode.maps.co/reverse?lat=${lat}&lon=${lon}&api_key=${apiKeyMobile}`)
                                .then(response => response.json())
                                .then(data => { if(data.display_name) { document.getElementById('addressMobile').value = data.display_name; }})
                                .catch(error => console.error("Error fetching address:", error));
                        }, (error) => alert("Error mendapatkan lokasi: " + error.message));
                    } else { alert("Geolocation tidak didukung oleh browser ini."); }
                });
            }

            var hash = window.location.hash;
            if (hash) {
                var triggerEl = document.querySelector('.nav-link[href="' + hash + '"], .mobile-tabs a[href="' + hash + '-content"]');
                if (triggerEl) { var tab = new bootstrap.Tab(triggerEl); tab.show(); }
            }
            var tabElements = document.querySelectorAll('a[data-bs-toggle="tab"], a[data-bs-toggle="pill"]');
            tabElements.forEach(function (tabEl) {
                tabEl.addEventListener('shown.bs.tab', function (event) {
                    var newHash = event.target.getAttribute('href').replace('-content', '');
                    if (history.pushState) { history.pushState(null, null, newHash); } 
                    else { window.location.hash = newHash; }
                });
            });

        });
    </script>
@endsection