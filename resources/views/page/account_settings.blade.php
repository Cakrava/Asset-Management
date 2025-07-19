@extends('layout.sidebar')

@section('content')
    @include('component.loader')
    {{-- SweetAlert CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Optimasi: Pastikan script dijalankan setelah DOM siap
        document.addEventListener('DOMContentLoaded', function () {
            function tampilkanDivSesuaiUkuran() {
                var isMobile = window.innerWidth <= 768;
                var accountdivmobile = document.getElementById('accountdivmobile');
                var accountdivdesktop = document.getElementById('accountdivdesktop');
                if (accountdivmobile && accountdivdesktop) {
                    accountdivmobile.style.display = isMobile ? 'block' : 'none';
                    accountdivdesktop.style.display = isMobile ? 'none' : 'block';
                }
            }

            // Jalankan saat halaman dimuat
            tampilkanDivSesuaiUkuran();

            // Jalankan juga saat ukuran layar berubah (responsive)
            window.addEventListener('resize', tampilkanDivSesuaiUkuran);
        });
    </script>

    <div id="accountdivmobile" style="display: none;">
        @include('layout.bottom-navigation')
        <div class="mobile-app-container">

            <br>
            <br>
            <br>

            <div class="app-content">
                <!-- [ Card Utama ] start -->
                <div class="mobile-card">
                    {{-- Notifikasi --}}
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
                        <div class="alert alert-success alert-dismissible fade show mobile-alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show mobile-alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Navigasi Tab Gaya Mobile --}}
                    <ul class="nav nav-pills mobile-tabs nav-fill mb-3" id="accountTab" role="tablist">
                        @php $isFirstTab = true; @endphp

                        @if(session('istemporary') !== false)
                            <li class="nav-item" role="presentation">
                                <a class="nav-link @if($isFirstTab) active @php $isFirstTab = false; @endphp @endif"
                                    id="account-settings-tab" data-bs-toggle="pill" href="#account-settings-content" role="tab"
                                    aria-controls="account-settings-content" aria-selected="true">
                                    <i class="ti ti-settings me-1"></i> Akun
                                    @if (session('istemporary') == true)
                                        <span class="badge bg-danger ms-1 p-1 rounded-circle"
                                            style="font-size: 0.5em; vertical-align: super;">
                                            <i class="ti ti-alert-circle" style="font-size: 0.8em;"></i>
                                        </span>
                                    @endif
                                </a>
                            </li>
                        @endif

                        @if(session('role') == 'master' || session('role') == 'admin')
                            <li class="nav-item" role="presentation">
                                <a class="nav-link @if($isFirstTab) active @php $isFirstTab = false; @endphp @endif"
                                    id="manage-account-tab" data-bs-toggle="pill" href="#manage-account-content" role="tab"
                                    aria-controls="manage-account-content" aria-selected="false">
                                    <i class="ti ti-users-group me-1"></i> Kelola
                                </a>
                            </li>
                        @endif

                        <li class="nav-item" role="presentation">
                            <a class="nav-link @if($isFirstTab) active @php $isFirstTab = false; @endphp @endif"
                                id="delete-account-tab" data-bs-toggle="pill" href="#delete-account-content" role="tab"
                                aria-controls="delete-account-content" aria-selected="false">
                                <i class="ti ti-trash me-1"></i> Hapus
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" id="accountTabContent">
                        @php $isFirstPane = true; @endphp

                        {{-- KONTEN TAB PENGATURAN AKUN --}}
                        @if(session('istemporary') !== false)
                            <div class="tab-pane fade @if($isFirstPane) show active @php $isFirstPane = false; @endphp @endif"
                                id="account-settings-content" role="tabpanel" aria-labelledby="account-settings-tab">
                                <div class="mobile-card-body">
                                    <h5 class="mb-3"><i class="ti ti-settings me-2"></i>Pengaturan Akun</h5>
                                    <p class="text-warning small mb-3">
                                        <i class="ti ti-alert-triangle me-1"></i> Ganti password dan email Anda untuk
                                        mempertahankan
                                        akun ini.
                                    </p>

                                    <form action="{{ route('panel.account.update') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="emailMobile" class="form-label">Email</label>
                                            <input type="email" class="form-control form-control-lg" name="email"
                                                id="emailMobile" value="{{ old('email', $users->email) }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="passwordMobile" class="form-label">Password Baru</label>
                                            <div class="password-input-group-mobile">
                                                <input type="password"
                                                    class="form-control form-control-lg password-field-mobile"
                                                    id="passwordMobile" name="password">
                                                <span class="password-toggle-icon-mobile password-toggle-mobile"><i
                                                        class="ti ti-eye-off"></i></span>
                                            </div>
                                            <small class="text-muted">Biarkan kosong untuk mempertahankan password lama.</small>
                                        </div>
                                        <div class="mb-4">
                                            <label for="password_confirmationMobile" class="form-label">Konfirmasi Password
                                                Baru</label>
                                            <div class="password-input-group-mobile">
                                                <input type="password"
                                                    class="form-control form-control-lg password-field-mobile"
                                                    id="password_confirmationMobile" name="password_confirmation">
                                                <span class="password-toggle-icon-mobile password-toggle-mobile"><i
                                                        class="ti ti-eye-off"></i></span>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                                            <i class="ti ti-device-floppy me-1"></i> Simpan Perubahan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif

                        {{-- KONTEN TAB KELOLA AKUN --}}
                        @if(session('role') == 'master' || session('role') == 'admin')
                            <div class="tab-pane fade @if($isFirstPane) show active @php $isFirstPane = false; @endphp @endif"
                                id="manage-account-content" role="tabpanel" aria-labelledby="manage-account-tab">
                                <div class="mobile-card-body">
                                    <h5 class="mb-3"><i class="ti ti-users-cog me-2"></i>Kelola Akun Pengguna</h5>

                                    <div class="d-grid gap-2 mb-3">
                                        {{-- Tombol Buat Akun Baru (User) dengan SweetAlert --}}
                                        <button type="button" class="btn btn-outline-primary btn-lg" id="generateUserAccountBtnMobile">
                                            <i class="ti ti-refresh me-1"></i> Buat Akun Baru
                                        </button>
                                        {{-- Form tersembunyi untuk membuat akun baru --}}
                                        <form action="{{ route('panel.account.generate') }}" method="POST" id="generateAccountFormMobile" class="d-none">
                                            @csrf
                                            <input type="hidden" name="account_type" value="instantiation">
                                        </form>

                                        @if(session('role') == 'master')
                                            <button type="button" class="btn btn-outline-warning btn-lg" id="newAdminButtonMobile">
                                                <i class="ti ti-user-shield me-1"></i> Buat Administrator Baru
                                            </button>
                                            {{-- Hidden form for new admin --}}
                                            <form action="{{ route('panel.account.generateAdministrator') }}" method="POST"
                                                class="d-none" id="generateAdminFormMobile">
                                                @csrf
                                            </form>
                                        @endif
                                    </div>

                                    <div id="alert-container-mobile" class="mb-3">
                                        <!-- Alerts will be dynamically added here -->
                                    </div>

                                    <div class="table-responsive">
                                        <table id="accountTableMobile" class="table table-hover mobile-table">
                                            <thead>
                                                <tr>
                                                    <th>Email</th>
                                                    <th>Role</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($allUsers as $user)
                                                    <tr>
                                                        <td>{{ $user->email }}<br><small class="text-muted">Pass:
                                                                defaultnewaccount</small></td>
                                                        <td>
                                                            <span class="badge {{ $user->role == 'admin' ? 'bg-primary' : 'bg-warning text-dark' }}">
                                                                {{ $user->role == 'admin' ? 'Admin' : 'User' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-sm btn-light btn-copy-credential-mobile"
                                                                data-user-email="{{ $user->email }}">
                                                                <i class="ti ti-clipboard"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- KONTEN TAB HAPUS AKUN --}}
                        <div class="tab-pane fade @if($isFirstPane) show active @php $isFirstPane = false; @endphp @endif"
                            id="delete-account-content" role="tabpanel" aria-labelledby="delete-account-tab">
                            <div class="mobile-card-body text-center">
                                <h5 class="mb-3"><i class="ti ti-trash-x me-2"></i>Hapus Akun Saya</h5>
                                <p class="text-muted mb-3">
                                    Menghapus akun akan menghilangkan semua data Anda secara permanen. Pastikan Anda telah
                                    menyimpan informasi penting.
                                </p>
                                <button type="button" class="btn btn-danger w-100 btn-lg" data-bs-toggle="modal"
                                    data-bs-target="#deleteConfirmationModalMobile">
                                    <i class="ti ti-alert-triangle me-1"></i> Hapus Akun Saya
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- [ Card Utama ] end -->

                <!-- [ MODALS (YANG TERSISA) ] -->

                <!-- Modal Konfirmasi New Administrator -->
                <div class="modal fade" id="adminConfirmationModalMobile" tabindex="-1"
                    aria-labelledby="adminConfirmationModalLabelMobile" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="adminConfirmationModalLabelMobile">Konfirmasi Administrator Baru
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-danger"><strong>PERHATIAN:</strong> Anda akan membuat akun
                                    <strong>Administrator
                                        Baru</strong> dengan hak akses penuh.
                                </p>
                                <p>Ketik teks berikut untuk konfirmasi: <strong class="text-danger"
                                        id="conAdministratorMobileDisplay">new-administrator</strong></p>
                                <input type="text" class="form-control form-control-lg" id="confirmationInputMobile"
                                    placeholder="Ketik teks di atas">
                                <div id="confirmationErrorMobile" class="text-danger mt-2" style="display: none;">Teks
                                    konfirmasi tidak sesuai.</div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="button" class="btn btn-warning" id="confirmAdminButtonMobile">Ya, Buat
                                    Administrator</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Konfirmasi Delete Account -->
                <div class="modal fade" id="deleteConfirmationModalMobile" tabindex="-1"
                    aria-labelledby="deleteConfirmationModalLabelMobile" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form action="{{ route('panel.account.destroyMyAccount') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteConfirmationModalLabelMobile">Konfirmasi Hapus Akun
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Apakah Anda yakin ingin menghapus akun Anda? Tindakan ini tidak dapat dibatalkan.</p>
                                    <div class="mb-3">
                                        <label for="deletePasswordMobile" class="form-label">Password Anda</label>
                                        <div class="password-input-group-mobile">
                                            <input type="password"
                                                class="form-control form-control-lg password-field-mobile"
                                                id="deletePasswordMobile" name="password" required>
                                            <span class="password-toggle-icon-mobile password-toggle-mobile"><i
                                                    class="ti ti-eye-off"></i></span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirmationTextMobile" class="form-label">Ketik untuk konfirmasi:
                                            <span class="fw-bold text-danger" id="confirmation-text-display-mobile"></span>
                                        </label>
                                        <input type="text" class="form-control form-control-lg" id="confirmationTextMobile"
                                            name="confirmation" required>
                                        <input type="hidden" name="confirmation_text"
                                            value="delete-account-{{ Auth::user()->email }}">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger">Ya, Hapus Akun Saya</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- [ S T Y L I N G ] --}}
        <style>
            /* === COPY PASTE CSS DARI KODE ASLI ANDA === */
            /* Basic Reset & App Container */
            body,
            html {
                margin: 0;
                padding: 0;
                height: 100%;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                background-color: #f0f2f5;
            }

            .mobile-app-container {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
            }

            .app-header {
                background-color: #0EA2BC;
                color: white;
                padding: 12px 15px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                position: sticky;
                top: 0;
                z-index: 1030;
                height: 56px;
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

            .app-content {
                flex-grow: 1;
                padding: 15px;
                overflow-y: auto;
            }

            .mobile-card {
                background-color: #ffffff;
                border-radius: 12px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
                margin-bottom: 15px;
                overflow: hidden;
            }

            .mobile-card-body {
                padding: 20px;
            }

            .mobile-tabs {
                background-color: #f8f9fa;
                border-bottom: 1px solid #dee2e6;
                padding-top: 5px;
            }



            .mobile-tabs .nav-link.active {
                background-color: #0EA2BC !important;
                /* Jadikan background warna primer */
                color: #FFFFFF !important;
                /* Jadikan teks warna putih */
                border-bottom-color: #0EA2BC;
                /* Border bisa sama dengan background atau dihilangkan/transparent */
                border-radius: 6px 6px 0 0;
                /* Opsional: Memberi sudut melengkung di atas agar lebih mirip tab */
            }

            .mobile-tabs .nav-link.active i {
                color: #FFFFFF !important;
                /* Ikon juga menjadi putih */
            }

            /* Pastikan style untuk tab yang TIDAK aktif tetap jelas */
            .mobile-tabs .nav-link {
                color: #6c757d;
                border: none;
                border-bottom: 3px solid transparent;
                padding: 10px 5px;
                font-size: 0.9rem;
                font-weight: 500;
                transition: color 0.2s ease, border-color 0.2s ease, background-color 0.2s ease;
                border-radius: 6px 6px 0 0;
            }

            .mobile-tabs .nav-link:not(.active):hover {
                background-color: #e9ecef;
                color: #0EA2BC;
            }

            .mobile-tabs .nav-link i {
                font-size: 1.1rem;
                margin-bottom: 0px;
                vertical-align: middle;
            }

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

            .password-input-group-mobile {
                position: relative;
            }

            .password-field-mobile {
                padding-right: 40px !important;
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

            .mobile-alert {
                margin: 0 0 15px 0;
                border-radius: 8px;
            }

            .mobile-alert:first-child {
                margin-top: 0;
            }
            .mobile-card .mobile-alert {
                margin-left: 20px;
                margin-right: 20px;
            }
            .mobile-card .mobile-alert:first-child {
                margin-top: 20px;
            }

            .mobile-table th,
            .mobile-table td {
                padding: 0.6rem 0.5rem;
                font-size: 0.9rem;
                vertical-align: middle;
            }

            .mobile-table .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.8rem;
            }

            .mobile-table .badge {
                font-size: 0.75rem;
                padding: 0.3em 0.5em;
            }

            div.dataTables_wrapper div.dataTables_length select,
            div.dataTables_wrapper div.dataTables_filter input {
                width: auto !important;
                display: inline-block !important;
                padding: 0.3rem 0.5rem;
                font-size: 0.9rem;
            }

            div.dataTables_wrapper div.dataTables_paginate ul.pagination {
                justify-content: center !important;
            }

            .modal-dialog-centered .modal-content {
                border-radius: 12px;
            }

            .modal-header {
                border-bottom: 1px solid #eee;
                padding: 1rem 1.5rem;
            }

            .modal-title {
                font-size: 1.1rem;
                font-weight: 500;
            }

            .modal-body {
                padding: 1.5rem;
            }

            .modal-footer {
                border-top: 1px solid #eee;
                padding: 1rem 1.5rem;
                justify-content: space-between;
            }

            .modal-footer .btn {
                flex-grow: 1;
                margin: 0 5px;
            }

            .modal-footer .btn:first-child {
                margin-left: 0;
            }

            .modal-footer .btn:last-child {
                margin-right: 0;
            }
        </style>


        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Password Toggle (Mobile version)
                const passwordInputGroupsMobile = document.querySelectorAll('.password-input-group-mobile');
                passwordInputGroupsMobile.forEach(inputGroup => {
                    const passwordInput = inputGroup.querySelector('.password-field-mobile');
                    const passwordToggle = inputGroup.querySelector('.password-toggle-mobile');
                    if (passwordInput && passwordToggle) {
                        const eyeIcon = passwordToggle.querySelector('i');
                        passwordToggle.addEventListener('click', function () {
                            if (passwordInput.type === 'password') {
                                passwordInput.type = 'text';
                                eyeIcon.classList.remove('ti-eye-off'); eyeIcon.classList.add('ti-eye');
                            } else {
                                passwordInput.type = 'password';
                                eyeIcon.classList.remove('ti-eye'); eyeIcon.classList.add('ti-eye-off');
                            }
                        });
                    }
                });

                // Delete Account Modal - Show confirmation text
                const deleteConfirmationModalMobile = document.getElementById('deleteConfirmationModalMobile');
                if (deleteConfirmationModalMobile) {
                    deleteConfirmationModalMobile.addEventListener('show.bs.modal', function (event) {
                        const confirmationTextDisplay = document.getElementById('confirmation-text-display-mobile');
                        if (confirmationTextDisplay) {
                            confirmationTextDisplay.textContent = 'delete-account-{{ Auth::user()->email }}';
                        }
                        document.getElementById('confirmationTextMobile').value = ''; // Clear previous input
                    });
                }

                // Handle tab activation from URL hash or session for initial load
                var hash = window.location.hash;
                var activeTabId = null;

                @if(session('istemporary') !== false)
                    activeTabId = activeTabId || 'account-settings-tab';
                @endif
                @if(session('role') == 'master' || session('role') == 'admin')
                    activeTabId = activeTabId || 'manage-account-tab';
                @endif
                activeTabId = activeTabId || 'delete-account-tab'; // Default to delete if others not applicable

                if (hash) {
                    var triggerEl = document.querySelector('.mobile-tabs a[href="' + hash + '-content"]');
                    if (triggerEl) activeTabId = triggerEl.id;
                }

                if (activeTabId) {
                    var tabToActivate = document.getElementById(activeTabId);
                    if (tabToActivate) {
                        var tab = new bootstrap.Tab(tabToActivate);
                        tab.show();
                    }
                }


                // Update hash on tab change
                var tabElements = document.querySelectorAll('.mobile-tabs a[data-bs-toggle="pill"]');
                tabElements.forEach(function (tabEl) {
                    tabEl.addEventListener('shown.bs.tab', function (event) {
                        var newHash = event.target.getAttribute('href').replace('-content', '');
                        if (history.pushState) {
                            history.pushState(null, null, newHash);
                        } else {
                            window.location.hash = newHash;
                        }
                    });
                });

                // *** SWEETALERT FOR GENERATE NEW USER (MOBILE) ***
                const generateBtnMobile = document.getElementById('generateUserAccountBtnMobile');
                if(generateBtnMobile) {
                    generateBtnMobile.addEventListener('click', function(event) {
                        event.preventDefault();
                        Swal.fire({
                            title: 'Buat Akun User Baru?',
                            text: "Akun baru dengan role 'User' akan dibuat.",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, buat akun!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById('generateAccountFormMobile').submit();
                            }
                        })
                    });
                }

                // jQuery dependent scripts
                $(document).ready(function () {
                    // DataTable Initialization
                    if ($('#accountTableMobile').length) { // Cek jika tabel ada
                        $('#accountTableMobile').DataTable({
                            "responsive": true,
                            "autoWidth": false,
                            "pageLength": 5,
                            "lengthChange": false,
                            "language": {
                                "search": "",
                                "searchPlaceholder": "Cari akun...",
                                "paginate": {
                                    "previous": "<i class='ti ti-chevron-left'></i>",
                                    "next": "<i class='ti ti-chevron-right'></i>"
                                },
                                "info": "Menampilkan _START_-_END_ dari _TOTAL_ akun",
                                "infoEmpty": "Tidak ada akun",
                                "infoFiltered": "(difilter dari _MAX_ total akun)",
                                "zeroRecords": "Tidak ada akun ditemukan"
                            },
                            "dom": '<"row"<"col-sm-12 mb-2"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
                        });
                    }

                    // Copy Credentials
                    $(document).on('click', '.btn-copy-credential-mobile', function (e) {
                        e.preventDefault();
                        var userEmail = $(this).data('user-email');
                        var userPassword = 'defaultnewaccount';

                        var copyText = `Kredensial login:\nEmail: ${userEmail}\nPassword: ${userPassword}\nHarap segera ganti email dan password.`;
                        navigator.clipboard.writeText(copyText).then(() => {
                            showAlertMobile('success', 'Kredensial disalin!');
                        }).catch(err => {
                            showAlertMobile('danger', 'Gagal menyalin.');
                        });
                    });

                    function showAlertMobile(type, message) {
                        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                        var alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                                                                ${message}
                                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                            </div>`;
                        $('#alert-container-mobile').html(alertHtml);
                        setTimeout(() => { $('#alert-container-mobile .alert').alert('close'); }, 3000);
                    }

                    // New Administrator Confirmation
                    $('#newAdminButtonMobile').on('click', function (e) {
                        e.preventDefault();
                        $('#confirmationInputMobile').val('');
                        $('#confirmationErrorMobile').hide();
                        $('#conAdministratorMobileDisplay').text("new-administrator"); // Set static text
                        var adminModal = new bootstrap.Modal(document.getElementById('adminConfirmationModalMobile'));
                        adminModal.show();
                    });

                    $('#confirmAdminButtonMobile').on('click', function () {
                        const inputText = $('#confirmationInputMobile').val();
                        const expectedText = $('#conAdministratorMobileDisplay').text();
                        if (inputText === expectedText) {
                            $('#generateAdminFormMobile').submit();
                        } else {
                            $('#confirmationErrorMobile').show();
                        }
                    });
                });
            });
        </script>
    </div>

    <div id="accountdivdesktop" style="display: block;">

        <div class="pc-container">
            <div class="pc-content">
                <!-- [ breadcrumb ] start -->
                <div class="page-header">
                    <div class="page-block">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('panel.dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item" aria-current="page">Account</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ breadcrumb ] end -->

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
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-4">
                                <ul class="list-group list-group-flush profile-menu">

                                    @if(session('istemporary') !== false)
                                        <li class="list-group-item profile-menu-item">
                                            <a href="#account-settings" class="nav-link active" data-bs-toggle="tab">
                                                <i class="ti ti-user me-2"></i> Account Settings
                                                @if (session('istemporary') == true)
                                                    <span class="badge bg-danger ms-2"
                                                        style="width: 10px;height:10px;border-radius :100%; margin-right: 5px;">
                                                    </span>
                                                @endif
                                            </a>

                                        </li>
                                    @endif
                                    @if(session('role') == 'master' || session('role') == 'admin')
                                        <li class="list-group-item profile-menu-item">
                                            <a href="#manage-account"
                                                class="nav-link {{ session('istemporary') === false ? 'active' : '' }}"
                                                data-bs-toggle="tab">
                                                <i class="ti ti-users me-2"></i> Manage Account
                                            </a>
                                        </li>
                                    @endif
                                    <li class="list-group-item profile-menu-item">
                                        <a href="#delete-akun" class="nav-link" data-bs-toggle="tab">
                                            <i class="ti ti-lock me-2"></i> Delete My Account
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-8">
                                <div class="tab-content profile-content">
                                    @if(session('istemporary') !== false)
                                        <div class="tab-pane fade show active" id="account-settings">
                                            <h5 class="mb-4 font-weight-bold "><i class="fas fa-cogs me-2"></i>
                                                Account Settings</h5>
                                            <hr class="border-primary mb-4">
                                            <p style="color: orange;">Ganti password dan email Anda untuk mempertahankan akun
                                                Anda.
                                            </p>

                                            <form action="{{ route('panel.account.update') }}" method="POST">
                                                @csrf
                                                <div class="row mb-4 gy-3">
                                                    <div class="col-md-12">
                                                        <div class="row gy-3">
                                                            <div class="col-md-12">
                                                                <label for="email"
                                                                    class="form-label small text-muted">Email</label>
                                                                <input type="email" class="form-control" name="email" id="email"
                                                                    value="{{ $users->email }}">
                                                            </div>
                                                            <div class="col-md-12">
                                                                <label for="password" class="form-label small text-muted">New
                                                                    Password (Biarkan kosong untuk mempertahankan password
                                                                    lama)</label>
                                                                <div class="password-input-group">
                                                                    <input type="password"
                                                                        class="form-control form-control-sm password-field"
                                                                        id="password" name="password">
                                                                    <span class="password-toggle-icon password-toggle">
                                                                        <i class="ti ti-eye-off"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <label for="password_confirmation"
                                                                    class="form-label small text-muted">Confirm New
                                                                    Password</label>
                                                                <div class="password-input-group">
                                                                    <input type="password"
                                                                        class="form-control form-control-sm password-field"
                                                                        id="password_confirmation" name="password_confirmation">
                                                                    <span class="password-toggle-icon password-toggle">
                                                                        <i class="ti ti-eye-off"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-end mt-3">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-check-circle me-1"></i> Save
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif

                                    <div class="tab-pane fade " id="delete-akun">
                                        <h5 class="mb-4 font-weight-bold "><i class="fas fa-lock me-2"></i>
                                            Delete Account</h5>
                                        <hr class="border-primary mb-4">
                                        <p class="text-muted">Menghapus akun akan menghilangkan semua data Anda secara
                                            permanen
                                            dari sistem kami. Pastikan Anda telah menyimpan semua informasi yang diperlukan
                                            sebelum melanjutkan.</p>

                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteConfirmationModal">
                                            <i class="fas fa-trash-alt me-1"></i> Delete Account
                                        </button>

                                        <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog"
                                            aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteConfirmationModalLabel">
                                                            Confirm
                                                            Delete Account</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('panel.account.destroyMyAccount') }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <div class="modal-body">
                                                            <p>Apakah Anda yakin ingin menghapus akun Anda? Tindakan ini
                                                                tidak dapat dibatalkan.</p>
                                                            <div class="mb-3">
                                                                <label for="deletePassword"
                                                                    class="form-label">Password</label>
                                                                <div class="password-input-group">
                                                                    <input type="password"
                                                                        class="form-control form-control-sm password-field"
                                                                        id="deletePassword" name="password" required>
                                                                    <span class="password-toggle-icon password-toggle">
                                                                        <i class="ti ti-eye-off"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <div
                                                                    style="background-color: #dedede; padding: 10px; margin-bottom: 10px; text-align : center;border-radius : 5px; color: red;">
                                                                    <span class="fw-bold"
                                                                        id="confirmation-text-display"></span>
                                                                </div>
                                                                <input type="text" class="form-control"
                                                                    id="confirmationText" name="confirmation" required>
                                                                <input type="hidden" name="confirmation_text"
                                                                    value="delete-account-{{ Auth::user()->email }}">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Delete
                                                                Account</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    @if(session('role') == 'master' || session('role') == 'admin')
                                        <div class="tab-pane fade {{ session('istemporary') ? '' : 'show active' }}"
                                            id="manage-account">
                                            <h5 class="mb-4 font-weight-bold "><i class="fas fa-users-cog me-2"></i>
                                                Manage Account</h5>
                                            <hr class="border-primary mb-4">

                                            <link rel="stylesheet"
                                                href="{{ asset('assets/css/plugins/dataTables.bootstrap5.min.css') }}">
                                            <link
                                                href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"
                                                rel="stylesheet" />

                                            <div class="col-sm-12">
                                                <div class="card">
                                                    <div class="card-header" style="margin-bottom : -15px">
                                                        <div class="mt-3">
                                                            <div class="d-flex justify-content-start" style="gap : 10px">
                                                                
                                                                {{-- Tombol Buat Akun User Baru dengan SweetAlert --}}
                                                                <button type="button" class="btn btn-outline-primary" id="generateUserAccountBtn">
                                                                    <i class="ti ti-refresh me-1"></i> Generate Account
                                                                </button>
                                                                
                                                                {{-- Form tersembunyi untuk generate akun --}}
                                                                <form action="{{ route('panel.account.generate') }}" method="POST" id="generateAccountForm" class="d-none">
                                                                    @csrf
                                                                    <input type="hidden" name="account_type" value="instantiation">
                                                                </form>

                                                                <form
                                                                    action="{{ route('panel.account.generateAdministrator') }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    @if(session('role') == 'master')
                                                                        <button type="submit" class="btn btn-outline-warning">
                                                                            <i class="ti ti-refresh me-1"></i> New Administrator
                                                                        </button>
                                                                    @endif
                                                                </form>
                                                            </div>

                                                            <div id="alert-container"
                                                                style="margin-top: 20px; margin-bottom : -20px">
                                                            </div>
                                                            <div style="clear: both;"></div>
                                                        </div>
                                                    </div>

                                                    <div class="card-body">
                                                        <div class="dt-responsive">
                                                            <div class="row justify-content-end ">
                                                                <div class="col-md-6 col-lg-4 col-xl-3">
                                                                    <div id="dom-jqry_filter"
                                                                        class="dataTables_filter text-end">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <table id="dom-jqry"
                                                                class="table table-striped table-bordered nowrap">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Email</th>
                                                                        <th>Password</th>
                                                                        <th>Role</th>
                                                                        <th>Actions</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="storedDeviceTableBody">
                                                                    @foreach($allUsers as $user)
                                                                        <tr>
                                                                            <td>{{ $user->email }}</td>
                                                                            <td>defaultnewaccount</td>
                                                                            <td style="color: {{ $user->role == 'admin' ? 'blue' : 'orange' }}">
                                                                                {{ $user->role == 'admin' ? 'Administrator' : 'User' }}
                                                                            </td>
                                                                            <td>
                                                                                <div class="d-flex gap-3">
                                                                                    <button type="button"
                                                                                        class="btn btn-sm btn-primary btn-copy-credential"
                                                                                        style="border-radius: 5px;min-width : 100px"
                                                                                        data-user-email="{{ $user->email }}">
                                                                                        <i class="ti ti-clipboard me-1"></i> Copy
                                                                                    </button>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Konfirmasi New Administrator -->
                    <div class="modal fade" id="adminConfirmationModal" tabindex="-1" role="dialog"
                        aria-labelledby="adminConfirmationModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="adminConfirmationModalLabel">Konfirmasi Administrator Baru
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>PERHATIAN:</strong> Anda akan membuat akun <strong>Administrator
                                            Baru</strong>
                                        yang memiliki
                                        hak akses penuh ke aplikasi ini. Pastikan Anda memahami risiko keamanan yang terkait
                                        dengan tindakan
                                        ini.</p>
                                    <p>Untuk melanjutkan, harap ketikkan teks berikut pada kolom di bawah ini untuk
                                        konfirmasi:
                                    </p>
                                    <div style="background-color: #dedede; border-radius: 5px; text-align: center;">
                                        <p><span id="conAdministrator"
                                                class="font-weight-bold text-danger">new-administrator</span></p>

                                    </div>
                                    <input type="text" class="form-control" id="confirmationInput">
                                    <div id="confirmationError" class="text-danger mt-2" style="display: none;">Teks
                                        konfirmasi
                                        tidak
                                        sesuai. Silakan coba lagi.</div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="button" class="btn btn-warning" id="confirmAdminButton">Ya, Buat
                                        Administrator</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- [ Profile Card ] end -->
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>

    {{-- [ S T Y L I N G ] --}}
    <style>
        .card-body {
            padding-left: 5px;
            padding-right: 15px;
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
        }

        .profile-content {
            padding-left: 10px;
        }

        .profile-content h5 {
            color: #495057;
        }
        .tab-pane.fade .password-input-group {
            position: relative;
        }

        .tab-pane.fade .password-input-group .form-control.password-field {
            padding-right: 30px;
        }

        .tab-pane.fade .password-toggle-icon.password-toggle {
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
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .tab-pane.fade #edit-profile .col-md-8 {
            padding-right: 20px;
            padding-left: 0;
            text-align: right;
        }

        .profile-image {
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        .profile-image:hover {
            opacity: 0.8;
            border-color: #0EA2BC;
            transform: scale(1.05);
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .text-primary {
            color: #0EA2BC;
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
            border-color: #0EA2BC;
        }

        .btn-outline-primary {
            color: #0EA2BC;
            border-color: #0EA2BC;
        }

        .btn-outline-primary:hover {
            background-color: #e0f2ff;
        }
        .tab-pane.fade {
            padding: 20px;
        }

        #delete-my-account {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 30px;
            padding-bottom: 30px;
        }
    </style>

    <!-- [Page Specific JS] start -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ asset('asset/dist/assets/js/plugins/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('asset/dist/assets/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            // Initialize DataTable
            var table = $('#dom-jqry').DataTable({
                "dom": '<"row justify-content-between"<"col-md-6"l><"col-md-6 text-end"f>>rt<"row"<"col-md-6"i><"col-md-6 text-end"p>>',
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
                    "className": 'dt-body-center'
                }]
            });

            // *** SWEETALERT FOR GENERATE NEW USER (DESKTOP) ***
            $('#generateUserAccountBtn').on('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Buat Akun User Baru?',
                    text: "Akun baru dengan role 'User' akan dibuat.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, buat akun!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#generateAccountForm').submit();
                    }
                })
            });


            $(document).on('click', '.btn-copy-credential', function (e) {
                e.preventDefault();
                var userEmail = $(this).data('user-email');
                // Passwordnya sekarang selalu sama
                var userPassword = 'defaultnewaccount'; 

                var copyText = `Akun anda telah diproses, harap segera mengganti email dan password agar akun tidak terhapus secara otomatis. Kredensial login anda adalah :\n\nemail : ${userEmail}\n Password : ${userPassword}\n\nterima kasih`;

                navigator.clipboard.writeText(copyText).then(() => {
                    showAlert('success', 'Credentials copied to clipboard!');
                })
                .catch(err => {
                    console.error('Failed to copy text: ', err);
                    showAlert('danger', 'Failed to copy credentials to clipboard.');
                });
            });


            function showAlert(type, message) {
                var alertClass = type === 'success' ? 'alert-success' : (type === 'info' ? 'alert-info' : 'alert-danger');
                var alertHtml = `
                    <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`;
                $('#alert-container').html(alertHtml);
            }

            var adminForm = $('form[action="{{ route('panel.account.generateAdministrator') }}"]');
            $('button:contains("New Administrator")').on('click', function (e) {
                e.preventDefault(); 
                $('#confirmationInput').val(''); 
                $('#confirmationError').hide(); 
                $('#adminConfirmationModal').modal('show');
            });
            $('#confirmAdminButton').on('click', function () {
                const inputText = $('#confirmationInput').val();
                const expectedText = "new-administrator";
                if (inputText === expectedText) {
                    adminForm.submit(); 
                } else {
                    $('#confirmationError').show();
                }
            });

        });
    </script>
    <style>
        .highlight-row {
            background-color: #00a2ff36;
        }

        .alert-warning {
            position: relative;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInputGroups = document.querySelectorAll('.password-input-group');

            passwordInputGroups.forEach(inputGroup => {
                const passwordInput = inputGroup.querySelector('.password-field');
                const passwordToggle = inputGroup.querySelector('.password-toggle');
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

            const deleteConfirmationModal = document.getElementById('deleteConfirmationModal');
            if (deleteConfirmationModal) {
                deleteConfirmationModal.addEventListener('show.bs.modal', function (event) {
                    const confirmationTextDisplay = document.getElementById('confirmation-text-display');
                    if (confirmationTextDisplay) {
                        confirmationTextDisplay.textContent = 'delete-account-{{ Auth::user()->email }}';
                    }
                });
            }

        });

    </script>

    </div>


@endsection