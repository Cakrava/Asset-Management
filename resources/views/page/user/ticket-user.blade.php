@extends('layout.sidebar')
@section('content')
@include('component.loader')
    <script>
        // Optimasi: Pastikan script dijalankan setelah DOM siap
        document.addEventListener('DOMContentLoaded', function () {
            function tampilkanDivSesuaiUkuran() {
                var isMobile = window.innerWidth <= 768;
                var divTicketMobile = document.getElementById('divTicketMobile');
                var divTicketDesktop = document.getElementById('divTicketDesktop');
                if (divTicketMobile && divTicketDesktop) {
                    divTicketMobile.style.display = isMobile ? 'block' : 'none';
                    divTicketDesktop.style.display = isMobile ? 'none' : 'block';
                }
            }

            // Jalankan saat halaman dimuat
            tampilkanDivSesuaiUkuran();

            // Jalankan juga saat ukuran layar berubah (responsive)
            window.addEventListener('resize', tampilkanDivSesuaiUkuran);
        });
    </script>


    <div id="divTicketMobile" style="display: none;">
        @include('layout.bottom-navigation')
        @php
            // Dengan "Make Ticket" sebagai modal, tab utama yang aktif defaultnya adalah "My Tickets"
            $pageTitle = 'Daftar Tiket'; // Judul default sekarang
            // $activeMainTab tidak lagi relevan untuk mengontrol 'make-ticket' vs 'my-tickets' via PHP,
            // karena 'my-tickets' akan selalu menjadi pane yang aktif di awal.
        @endphp


        <style>
            body,
            html {
                margin: 0;
                padding: 0;
                height: 100%;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                background-color: #f4f6f8;
            }

            .mobile-app-container {
                display: flex;
                flex-direction: column;
                height: 100vh;
                overflow: hidden;
                background-color: #fff;
            }

            .app-header {
                background-color: #007bff;
                color: white;
                padding: 15px 20px;
                text-align: center;
                font-size: 1.2rem;
                font-weight: 500;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                position: sticky;
                top: 0;
                z-index: 1000;
            }

            .app-content {
                flex-grow: 1;
                overflow-y: auto;
                padding: 15px;
                background-color: #f4f6f8;
            }

            .bottom-nav {
                display: flex;
                background-color: #ffffff;
                border-top: 1px solid #e0e0e0;
                position: sticky;
                bottom: 0;
                z-index: 999;
                /* Di bawah FAB dan Modal */
                box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.05);
            }

            .bottom-nav-item {
                flex-grow: 1;
                text-align: center;
                padding: 10px 5px;
                cursor: pointer;
                color: #757575;
                text-decoration: none;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                font-size: 0.75rem;
            }

            .bottom-nav-item .nav-icon {
                font-size: 1.5rem;
                margin-bottom: 2px;
            }

            .bottom-nav-item.active {
                color: #007bff;
            }

            .bottom-nav-item.active .nav-icon {
                color: #007bff;
            }

            /* Floating Action Button (FAB) */
            .fab {
                position: fixed;
                bottom: 75px;
                /* Sesuaikan jarak dari bottom nav */
                right: 20px;
                width: auto;
                /* Lebar otomatis berdasarkan konten */
                padding: 12px 18px;
                /* Padding untuk tombol FAB */
                border-radius: 28px;
                /* Membuatnya lebih bulat */
                background-color: #007bff;
                color: white;
                border: none;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
                font-size: 0.9rem;
                /* Ukuran font teks di FAB */
                font-weight: 500;
                z-index: 1050;
                /* Di atas konten lain, di bawah modal backdrop jika perlu */
                display: flex;
                align-items: center;
                justify-content: center;
                transition: transform 0.2s ease-in-out;
            }

            .fab:hover {
                transform: scale(1.05);
                background-color: #0056b3;
            }

            .fab .nav-icon {
                /* Jika menggunakan ikon di FAB */
                font-size: 1.3rem;
                margin-right: 8px;
            }


            /* Styling untuk tab di dalam "My Tickets" */
            .sub-nav-tabs {
                display: flex;
                overflow-x: auto;
                background-color: #fff;
                border-bottom: 1px solid #dee2e6;
                margin-bottom: 1rem;
                padding: 0.5rem 0;
            }

            .sub-nav-tabs .nav-item {
                flex-shrink: 0;
            }

            .sub-nav-tabs .nav-link {
                color: #495057;
                border: none;
                border-bottom: 3px solid transparent;
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
                white-space: nowrap;
            }

            .sub-nav-tabs .nav-link.active {
                color: #007bff;
                border-bottom-color: #007bff;
                font-weight: 500;
            }

            .sub-nav-tabs .badge {
                font-size: 0.7rem;
                padding: 0.3em 0.5em;
                margin-left: 5px;
            }

            /* Card styling */
            .app-card {
                background-color: #ffffff;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
                margin-bottom: 15px;
            }

            .app-card .card-header {
                background-color: transparent;
                border-bottom: 1px solid #f0f0f0;
                padding: 15px;
                font-size: 1.1rem;
                font-weight: 500;
            }

            .app-card .card-body {
                padding: 15px;
            }

            .app-card .card-title {
                font-size: 1rem;
                font-weight: 600;
                margin-bottom: 0.5rem;
            }

            .app-card .card-text {
                font-size: 0.9rem;
                color: #555;
                line-height: 1.5;
            }

            .app-card .card-text small.text-muted {
                color: #777 !important;
                font-size: 0.8rem;
            }

            .app-card .badge {
                font-size: 0.75rem;
                padding: 0.4em 0.6em;
            }

            .app-card .btn {
                font-size: 0.9rem;
                padding: 0.5rem 1rem;
            }

            /* Form styling for mobile (global, termasuk di modal) */
            .form-label {
                font-weight: 500;
                margin-bottom: 0.5rem;
                font-size: 0.9rem;
            }

            .form-control,
            .form-select {
                padding: 0.75rem 1rem;
                font-size: 1rem;
                border-radius: 6px;
                border: 1px solid #ced4da;
            }

            .form-control:focus,
            .form-select:focus {
                border-color: #80bdff;
                box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
            }

            .btn-primary {
                background-color: #007bff;
                border-color: #007bff;
            }

            .btn-primary.w-100 {
                /* Tombol submit di modal */
                padding: 0.75rem;
                font-size: 1rem;
                margin-top: 10px;
            }

            .alert {
                border-radius: 6px;
                padding: 1rem;
                font-size: 0.9rem;
            }

            #warning-box-modal .card {
                /* Warning box di dalam modal */
                border-left: 4px solid #ffc107;
                background-color: #fffcf1;
                margin-bottom: 1rem;
                /* Tambah margin bawah jika di modal */
            }

            #warning-box-modal .card-title {
                color: #c69500;
            }

            .ticket-card {
                border-left: 4px solid transparent;
            }

            .ticket-card.status-pending {
                border-left-color: #ffc107;
            }

            .ticket-card.status-process {
                border-left-color: #007bff;
            }

            .ticket-card.status-completed {
                border-left-color: #28a745;
            }

            .ticket-card.status-canceled,
            .ticket-card.status-rejected {
                border-left-color: #6c757d;
            }

            .popover {
                max-width: 280px;
                box-shadow: 0 3px 15px rgba(0, 0, 0, 0.15);
                border-radius: 8px;
            }

            .popover-header {
                font-size: 0.9rem;
                font-weight: 500;
                background-color: #f8f9fa;
                border-bottom: 1px solid #e9ecef;
            }

            .popover-body {
                font-size: 0.85rem;
                padding: 10px 12px;
            }

            /* Modal styling */
            .modal-dialog-scrollable .modal-content {
                /* Untuk modal yang kontennya panjang */
                max-height: calc(100vh - 40px);
                /* Sisakan margin atas bawah */
            }

            .modal-content {
                border-radius: 10px;
            }

            .modal-header {
                border-bottom: 1px solid #e9ecef;
                padding: 1rem;
            }

            /* Kembalikan border tipis */
            .modal-header .btn-close {
                padding: 0.5rem;
            }

            .modal-title {
                font-size: 1.1rem;
                font-weight: 500;
            }

            .modal-body {
                padding: 1rem;
                font-size: 0.95rem;
            }

            .modal-footer {
                border-top: 1px solid #e9ecef;
                padding: 0.75rem 1rem;
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

        <div class="mobile-app-container">
            <header class="app-header">
                <span id="app-header-title">{{ $pageTitle }}</span>
            </header>

            <main class="app-content">
                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Tab Content sekarang hanya untuk "My Tickets" atau tab lain jika ada --}}
                <div class="tab-content" id="mobileAppTabContent">
                    {{-- My Tickets (akan selalu menjadi tab yang terlihat atau default) --}}
                    <div class="tab-pane fade show active" id="pane-my-tickets" role="tabpanel"
                        aria-labelledby="nav-my-tickets-tab">
                        <div class="app-card">
                            <div class="card-header ">
                                
                            </div>
                            <div class="p-0">
                                <ul class="nav sub-nav-tabs" id="ticketStatusTabsMobile" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="pending-tab-mobile" data-bs-toggle="tab"
                                            data-bs-target="#tab-pending-mobile" type="button" role="tab"
                                            aria-controls="tab-pending-mobile" aria-selected="false">Pending
                                            @if($countPending > 0) <span
                                                class="badge bg-warning text-dark">{{ $countPending }}</span>
                                            @endif</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="process-tab-mobile" data-bs-toggle="tab"
                                            data-bs-target="#tab-process-mobile" type="button" role="tab"
                                            aria-controls="tab-process-mobile" aria-selected="false">
                                            Proses @if($countProcess > 0) <span
                                            class="badge bg-primary text-light">{{ $countProcess }}</span> @endif
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="completed-tab-mobile" data-bs-toggle="tab"
                                            data-bs-target="#tab-completed-mobile" type="button" role="tab"
                                            aria-controls="tab-completed-mobile" aria-selected="false">Selesai</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="closed-tab-mobile" data-bs-toggle="tab"
                                            data-bs-target="#tab-closed-mobile" type="button" role="tab"
                                            aria-controls="tab-closed-mobile" aria-selected="false">Ditutup</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body pt-2">
                                <div class="tab-content" id="ticketStatusTabsContentMobile">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-warning text-dark',
                                            'process' => 'bg-primary',
                                            'completed' => 'bg-success',
                                            'canceled' => 'bg-secondary',
                                            'rejected' => 'bg-danger',
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Pending',
                                            'process' => 'Dalam Proses',
                                            'completed' => 'Selesai',
                                            'canceled' => 'Dibatalkan',
                                            'rejected' => 'Ditolak',
                                        ];
                                        $statusesToLoopMobile = ['pending', 'process', 'completed', 'closed'];
                                    @endphp

                                    @foreach($statusesToLoopMobile as $currentStatusFilterMobile)
                                        @php
                                            $tabIdMobile = "tab-" . $currentStatusFilterMobile . "-mobile";
                                            $ariaLabelledByMobile = $currentStatusFilterMobile . "-tab-mobile";
                                            $filteredTicketsMobile = collect($tickets)->filter(function ($ticket) use ($currentStatusFilterMobile) {
                                                if ($currentStatusFilterMobile === 'closed') {
                                                    return in_array($ticket->status, ['canceled', 'rejected']);
                                                }
                                                return $ticket->status == $currentStatusFilterMobile;
                                            });
                                            $hasTicketsInThisStatusMobile = $filteredTicketsMobile->isNotEmpty();
                                        @endphp

                                        <div class="tab-pane fade" id="{{ $tabIdMobile }}" role="tabpanel"
                                            aria-labelledby="{{ $ariaLabelledByMobile }}" tabindex="0">
                                            @if($hasTicketsInThisStatusMobile)
                                                @foreach($filteredTicketsMobile as $ticket)
                                                    @php
                                                        $classMobile = $statusClasses[$ticket->status] ?? 'bg-light text-dark';
                                                        $labelMobile = $statusLabels[$ticket->status] ?? ucfirst($ticket->status);
                                                        $fullNotesMobile = $ticket->description ?? $ticket->notes ?? 'Tidak ada catatan.';
                                                    @endphp
                                                    <div class="app-card ticket-card mb-3 status-{{$ticket->status}}">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                <h6 class="card-title mb-0" style="flex-basis: 70%;">
                                                                    {{ $ticket->subject }}
                                                                </h6>
                                                                <span class="badge {{ $classMobile }} ms-2">{{ $labelMobile }}</span>
                                                            </div>
                                                            <p class="card-text mb-1"><small class="text-muted">Tipe:</small>
                                                                {{ ucfirst($ticket->ticket_type) }}</p>
                                                            <p class="card-text mb-1">
                                                                <small class="text-muted">Catatan:</small>
                                                                {{ Str::limit($fullNotesMobile, 80) }}
                                                                @if(strlen($fullNotesMobile) > 80)
                                                                    <a href="#" class="text-primary small" data-bs-toggle="popover"
                                                                        data-bs-trigger="click" data-bs-placement="top"
                                                                        title="Detail Catatan"
                                                                        data-bs-content="{{ e($fullNotesMobile) }}">(selengkapnya)</a>
                                                                @endif
                                                            </p>
                                                            <p class="card-text mb-2"><small class="text-muted">Dibuat:</small>
                                                                {{ \Carbon\Carbon::parse($ticket->created_at)->translatedFormat('d M Y, H:i') }}
                                                            </p>

                                                            @if($ticket->request_to_cancel == 1 && ($ticket->status == 'pending' || $ticket->status == 'process'))
                                                                <p class="text-danger small mt-1 mb-2"><em><i
                                                                            class="ti ti-info-circle me-1"></i>Menunggu persetujuan
                                                                        pembatalan</em></p>
                                                            @endif

                                                            @if($ticket->status == 'pending' || $ticket->status == 'process')
                                                                <button type="button"
                                                                    class="btn {{ $ticket->request_to_cancel == 1 ? 'btn-outline-secondary' : 'btn-outline-danger' }} btn-sm w-100 mt-2"
                                                                    data-bs-toggle="modal" data-bs-target="#cancelTicketModalMobile"
                                                                    data-ticket-id="{{ $ticket->id }}"
                                                                    data-ticket-subject="{{ $ticket->subject }}"
                                                                    @disabled($ticket->request_to_cancel == 1)>
                                                                    <i
                                                                        class="ti {{ $ticket->request_to_cancel == 1 ? 'ti-clock' : 'ti-ban' }} me-1"></i>
                                                                    {{ $ticket->request_to_cancel == 1 ? 'Pembatalan Diajukan' : 'Ajukan Pembatalan' }}
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="text-center py-4 text-muted">
                                                    <i class="ti ti-ticket-off" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                                                    <p class="mb-0">
                                                        Tidak ada tiket
                                                        @if($currentStatusFilterMobile == 'pending') pending.
                                                        @elseif($currentStatusFilterMobile == 'process') yang sedang diproses.
                                                        @elseif($currentStatusFilterMobile == 'completed') yang telah selesai.
                                                        @elseif($currentStatusFilterMobile == 'closed') yang ditutup.
                                                        @endif
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Floating Action Button -->
                <button type="button" class="fab btn" style="bottom: 90px; right: 20px;" data-bs-toggle="modal"
                    data-bs-target="#makeTicketModal">
                    <i class="nav-icon ti ti-plus"></i>
                    <span class="ms-2">Buat Tiket</span>
                </button>

                <!-- Modal: Make Ticket -->
                <div class="modal fade" id="makeTicketModal" tabindex="-1" aria-labelledby="makeTicketModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="makeTicketModalLabel">Buat Tiket Baru</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('panel.ticket.store') }}" method="POST" id="makeTicketFormModal">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="subject-modal" class="form-label">Subjek</label>
                                        <input type="text" class="form-control" id="subject-modal" name="subject"
                                            placeholder="Masukkan subjek tiket" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="ticket-type-modal" class="form-label">Tipe Tiket</label>
                                        <select class="form-select" id="ticket-type-modal" name="ticket_type" required>
                                            <option value="">-- Pilih Tipe Tiket --</option>
                                            <option value="consultation">Konsultasi</option>
                                            <option value="deployment">Deployment</option>
                                            <option value="repair">Perbaikan</option>
                                            <option value="remove">Pencabutan</option>
                                        </select>
                                    </div>

                                    <div id="warning-box-modal" style="display: none;">
                                        <div class="card">
                                            <div class="card-body py-2 px-3">
                                                <h6 class="card-title text-warning mb-1">
                                                    <i class="ti ti-alert-triangle me-1"></i> Informasi Tambahan
                                                </h6>
                                                <p class="card-text text-secondary small mb-0">
                                                    Untuk tipe tiket <strong>Konsultasi</strong>,
                                                    <strong>Perbaikan</strong>, atau <strong>Pencabutan</strong>,
                                                    mohon jelaskan detail perangkat jaringan yang ada (merek, tipe) di
                                                    Catatan.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description-modal" class="form-label">Catatan</label>
                                        <textarea class="form-control" id="description-modal" name="description" rows="4"
                                            placeholder="Jelaskan masalah Anda atau detail perangkat..."
                                            required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Kirim Tiket</button>
                                </form>
                            </div>
                            {{-- <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary"
                                    data-bs-dismiss="modal">Tutup</button>
                                <button type="button" class="btn btn-primary"
                                    onclick="document.getElementById('makeTicketFormModal').submit();">Kirim Tiket</button>
                            </div> --}}
                        </div>
                    </div>
                </div>


                <!-- Modal Konfirmasi Cancel (Mobile Styled) -->
                <div class="modal fade" id="cancelTicketModalMobile" tabindex="-1"
                    aria-labelledby="cancelTicketModalLabelMobile" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="cancelTicketModalLabelMobile">Konfirmasi Pembatalan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Anda yakin ingin mengajukan pembatalan untuk tiket: <br>"<strong
                                    id="modalTicketSubjectMobile"></strong>"?
                                <p class="text-muted small mt-2" id="cancelModalInfoMobile" style="display: none;">Admin
                                    akan
                                    meninjau permintaan pembatalan Anda.</p>
                            </div>
                            <div class="modal-footer d-flex flex-row gap-2">
                                <button type="button" class="btn btn-outline-secondary flex-fill"
                                    data-bs-dismiss="modal">Tidak</button>
                                <form action="{{ route('panel.ticket.cancel') }}" method="POST" class="m-0 flex-fill">
                                    @csrf
                                    <input type="hidden" name="ticket_id" id="cancelTicketIdInputMobile" value="">
                                    <button type="submit" class="btn btn-danger w-100" id="confirmCancelButtonMobile">Ya,
                                        Ajukan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </main>


        </div>


        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const defaultTitle = '{{ $pageTitle }}';
                const appHeaderTitle = document.getElementById('app-header-title');

                function updateAppTitle(title) {
                    if (appHeaderTitle) {
                        appHeaderTitle.textContent = title;
                    }
                }
                // Set initial title (karena sekarang hanya ada satu main view, tidak perlu update dinamis dari tab)
                updateAppTitle(defaultTitle);


                // --- Inisialisasi Popovers ---
                const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
                const popoverList = [...popoverTriggerList].map(popoverTriggerEl => {
                    return new bootstrap.Popover(popoverTriggerEl, {
                        html: popoverTriggerEl.hasAttribute('data-bs-html') || popoverTriggerEl.getAttribute('data-bs-trigger') === 'click',
                        sanitize: !(popoverTriggerEl.hasAttribute('data-bs-html') || popoverTriggerEl.getAttribute('data-bs-trigger') === 'click')
                    });
                });

                // --- Handle Warning Box for Make Ticket Modal ---
                const ticketTypeSelectModal = document.getElementById('ticket-type-modal');
                const warningBoxModal = document.getElementById('warning-box-modal');
                const showWarningForModal = ['consultation', 'repair', 'remove'];

                function checkTicketTypeModal() {
                    if (ticketTypeSelectModal && warningBoxModal) {
                        const selectedValue = ticketTypeSelectModal.value;
                        warningBoxModal.style.display = showWarningForModal.includes(selectedValue) ? 'block' : 'none';
                    }
                }
                if (ticketTypeSelectModal) {
                    checkTicketTypeModal(); // Initial check
                    ticketTypeSelectModal.addEventListener('change', checkTicketTypeModal);
                }

                // Reset form dan warning box ketika modal ditutup
                const makeTicketModalEl = document.getElementById('makeTicketModal');
                if (makeTicketModalEl) {
                    makeTicketModalEl.addEventListener('hidden.bs.modal', function () {
                        const form = document.getElementById('makeTicketFormModal');
                        if (form) form.reset();
                        if (warningBoxModal) warningBoxModal.style.display = 'none';
                        // Reset pilihan select ke default
                        if (ticketTypeSelectModal) ticketTypeSelectModal.value = "";
                    });
                }


                // --- Handle Modal Cancel for Mobile ---
                const cancelTicketModalMobile = document.getElementById('cancelTicketModalMobile');
                if (cancelTicketModalMobile) {
                    cancelTicketModalMobile.addEventListener('show.bs.modal', event => {
                        const button = event.relatedTarget;
                        const ticketId = button.getAttribute('data-ticket-id');
                        const ticketSubject = button.getAttribute('data-ticket-subject');
                        const isDisabled = button.hasAttribute('disabled');

                        const modalSubjectSpan = cancelTicketModalMobile.querySelector('#modalTicketSubjectMobile');
                        const modalTicketIdInput = cancelTicketModalMobile.querySelector('#cancelTicketIdInputMobile');
                        const modalInfo = cancelTicketModalMobile.querySelector('#cancelModalInfoMobile');
                        const confirmButton = cancelTicketModalMobile.querySelector('#confirmCancelButtonMobile');

                        if (modalSubjectSpan) modalSubjectSpan.textContent = ticketSubject ? ticketSubject : 'Tiket ini';
                        if (modalTicketIdInput) modalTicketIdInput.value = ticketId;
                        if (modalInfo) modalInfo.style.display = isDisabled ? 'none' : 'block';
                        if (confirmButton) confirmButton.disabled = isDisabled;
                    });
                }

                // --- Handle Persistent Sub-Tabs for "My Tickets" ---
                const subTabContainerMobile = document.getElementById('ticketStatusTabsMobile');
                const subTabKeyMobile = 'activeSubMobileTicketTab'; // Key untuk sub-tab di "My Tickets"

                function activateBSTab(tabTriggerEl) {
                    if (tabTriggerEl) {
                        try {
                            const tab = new bootstrap.Tab(tabTriggerEl);
                            tab.show();
                        } catch (e) {
                            console.error("Error activating BS tab:", e, "Element:", tabTriggerEl);
                        }
                    }
                }

                // Restore active sub-tab for "My Tickets"
                if (subTabContainerMobile) {
                    const lastActiveSubTabTargetMobile = sessionStorage.getItem(subTabKeyMobile);
                    let subTabButtonToActivateMobile = subTabContainerMobile.querySelector('#pending-tab-mobile'); // Default

                    if (lastActiveSubTabTargetMobile) {
                        const potentialSubTabButton = subTabContainerMobile.querySelector(`button[data-bs-target="${lastActiveSubTabTargetMobile}"]`);
                        if (potentialSubTabButton) {
                            subTabButtonToActivateMobile = potentialSubTabButton;
                        }
                    }
                    if (subTabButtonToActivateMobile && !subTabButtonToActivateMobile.classList.contains('active')) {
                        activateBSTab(subTabButtonToActivateMobile);
                    } else if (!subTabButtonToActivateMobile && subTabContainerMobile.querySelector('.nav-link')) {
                        // Jika tidak ada sub-tab spesifik yang aktif atau tersimpan, aktifkan yang pertama jika ada
                        activateBSTab(subTabContainerMobile.querySelector('.nav-link'));
                    }

                    // Event listener for sub-tabs (My Tickets status)
                    subTabContainerMobile.addEventListener('shown.bs.tab', function (event) {
                        const activeSubTabTarget = event.target.getAttribute('data-bs-target');
                        if (activeSubTabTarget) {
                            sessionStorage.setItem(subTabKeyMobile, activeSubTabTarget);
                        }
                    });
                }

                // Tidak ada lagi persistent main tab karena bottom nav hanya punya satu item utama (My Tickets)
                // Jika ada item lain di bottom nav, logika persistent main tab perlu diaktifkan kembali.
                // const mainTabContainerMobile = document.getElementById('mobileAppBottomNav');
                // const mainTabKeyMobile = 'activeMainMobileTab';
                // if (mainTabContainerMobile) {
                //     mainTabContainerMobile.addEventListener('shown.bs.tab', function (event) {
                //         const activeMainTabTarget = event.target.getAttribute('href');
                //         if (activeMainTabTarget) {
                //             sessionStorage.setItem(mainTabKeyMobile, activeMainTabTarget);
                //             // updateAppTitle() akan dipanggil dari onclick di bottom-nav-item jika ada > 1 item
                //         }
                //     });
                //     // Logika untuk restore main tab dari sessionStorage jika ada > 1 item
                // }

                window.updateAppTitle = updateAppTitle; // Expose jika dibutuhkan
            });
        </script>
    </div>

    <div id="divTicketDesktop" style="display: none;">
       

    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('panel.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page">Ticket</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-md-12">

                    {{-- Navigation Tabs --}}
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-make-ticket-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-make-ticket" type="button" role="tab"
                                aria-controls="pills-make-ticket" aria-selected="false">
                                <i class="ti ti-plus me-2"></i> Make Ticket
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-my-ticket-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-my-ticket" type="button" role="tab" aria-controls="pills-my-ticket"
                                aria-selected="false">
                                <i class="ti ti-ticket me-2"></i> My Tickets
                                @if($countPending || $countProcess)
                                    <span class="badge bg-danger ms-2">{{ $countPending + $countProcess }}</span>
                                @endif
                            </button>
                        </li>
                    </ul>

                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Tab Content --}}
                    <div class="tab-content" id="pills-tabContent">

                        {{-- Make Ticket Form --}}
                        <div class="tab-pane fade" id="pills-make-ticket" role="tabpanel"
                            aria-labelledby="pills-make-ticket-tab" tabindex="0">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Create a New Ticket</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('panel.ticket.store') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="subject" class="form-label">Subject</label>
                                            <input type="text" class="form-control" id="subject" name="subject"
                                                placeholder="Enter ticket subject" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="ticket-type" class="form-label">Ticket Type</label>
                                            <select class="form-control" id="ticket-type" name="ticket_type" required>
                                                <option value="">-- Pilih Tipe Ticket --</option>
                                                <option value="consultation">Consultation</option>
                                                <option value="deployment">Deployment</option>
                                                <option value="repair">Repair</option>
                                                <option value="remove">Remove</option>
                                            </select>
                                        </div>

                                        <div class="col-xl-12" id="warning-box" style="display: none;">
                                            <div class="card border-warning border-opacity-50 shadow-sm">
                                                <div class="card-body">
                                                    <h4 class="card-title text-warning mb-2">
                                                        Informasi Tambahan Diperlukan
                                                    </h4>
                                                    <p class="card-text text-secondary small">
                                                        Anda memilih jenis tiket <strong>Consultation</strong>,
                                                        <strong>Repair</strong>, atau <strong>Remove</strong>.
                                                        Mohon jelaskan secara detail perangkat jaringan yang sudah ada di bagian Notes,
                                                        termasuk <u>merek</u> dan <u>tipe</u> perangkat (misalnya router,
                                                        switch, access point).
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Notes</label>
                                            <div class="form-group">
                                                <textarea class="form-control" id="description" name="description" rows="3"
                                                    placeholder="Describe your issue or existing network devices" required></textarea>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit Ticket</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- My Tickets --}}
                        <div class="tab-pane fade" id="pills-my-ticket" role="tabpanel"
                            aria-labelledby="pills-my-ticket-tab" tabindex="0">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Tiket Anda</h5>
                                    <br>
                                    <ul class="nav nav-tabs card-header-tabs" id="ticketStatusTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="pending-tab" data-bs-toggle="tab"
                                                data-bs-target="#tab-pending" type="button" role="tab"
                                                aria-controls="tab-pending" aria-selected="false">Pending
                                                @if($countPending > 0) <span
                                                    class="badge bg-warning text-dark">{{ $countPending }}</span> {{-- text-dark for better contrast on warning --}}
                                                @endif</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="process-tab" data-bs-toggle="tab"
                                                data-bs-target="#tab-process" type="button" role="tab"
                                                aria-controls="tab-process" aria-selected="false">
                                                In Progress @if($countProcess > 0) <span
                                                class="badge bg-primary text-light">{{ $countProcess }}</span> @endif
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="completed-tab" data-bs-toggle="tab"
                                                data-bs-target="#tab-completed" type="button" role="tab"
                                                aria-controls="tab-completed" aria-selected="false">Completed</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="canceled-tab" data-bs-toggle="tab"
                                                data-bs-target="#tab-canceled" type="button" role="tab"
                                                aria-controls="tab-canceled" aria-selected="false">Close</button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="ticketStatusTabsContent">

                                        @php
                                            $statusClasses = [
                                                'pending' => 'bg-warning text-dark', // Ensure good contrast
                                                'process' => 'bg-primary',
                                                'completed' => 'bg-success',
                                                'canceled' => 'bg-secondary',
                                                'rejected' => 'bg-danger',
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Pending',
                                                'process' => 'In Progress',
                                                'completed' => 'Completed',
                                                'canceled' => 'Canceled',
                                                'rejected' => 'Rejected',
                                            ];
                                            $statusesToLoop = ['pending', 'process', 'completed', 'canceled']; // 'canceled' tab now includes 'rejected'
                                        @endphp

                                        @foreach($statusesToLoop as $currentStatusFilter)
                                            @php
                                                $tabId = "tab-" . $currentStatusFilter;
                                                $ariaLabelledBy = $currentStatusFilter . "-tab";
                                                $hasTicketsInThisStatus = false;

                                                // Determine which tickets to show based on $currentStatusFilter
                                                $filteredTickets = collect($tickets)->filter(function($ticket) use ($currentStatusFilter) {
                                                    if ($currentStatusFilter === 'canceled') { // 'Close' tab shows 'canceled' and 'rejected'
                                                        return in_array($ticket->status, ['canceled', 'rejected']);
                                                    }
                                                    return $ticket->status == $currentStatusFilter;
                                                });
                                                if ($filteredTickets->isNotEmpty()) {
                                                    $hasTicketsInThisStatus = true;
                                                }
                                            @endphp

                                            <div class="tab-pane fade" id="{{ $tabId }}" role="tabpanel" aria-labelledby="{{ $ariaLabelledBy }}" tabindex="0">
                                                {{-- Desktop Table View --}}
                                                <div class="table-responsive d-none d-md-block">
                                                    <table class="table table-striped table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>Tipe Tiket</th>
                                                                <th>Subjek</th>
                                                                <th>Catatan</th>
                                                                <th>Status</th>
                                                                <th>Tanggal Dibuat</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if($hasTicketsInThisStatus)
                                                                @foreach($filteredTickets as $ticket)
                                                                    @php
                                                                        $class = $statusClasses[$ticket->status] ?? 'bg-light text-dark';
                                                                        $label = $statusLabels[$ticket->status] ?? ucfirst($ticket->status);
                                                                        $fullNotes = $ticket->description ?? $ticket->notes ?? 'Tidak ada catatan.';
                                                                        $shortNotesTable = Str::limit($fullNotes, 70, '...');
                                                                    @endphp
                                                                    <tr @if($ticket->status == 'process' || $ticket->status == 'canceled' || $ticket->status == 'rejected')
                                                                            data-bs-toggle="popover"
                                                                            data-bs-trigger="hover"
                                                                            data-bs-placement="top"
                                                                            data-bs-title="Detail Catatan"
                                                                            data-bs-content="{{ e($fullNotes) }}"
                                                                            style="cursor: help;"
                                                                        @endif
                                                                    >
                                                                        <td>
                                                                            {{ $ticket->ticket_type }}
                                                                            @if($ticket->request_to_cancel == 1 && ($ticket->status == 'pending' || $ticket->status == 'process'))
                                                                                <p class="text-danger small mt-1 mb-0"><em>Menunggu persetujuan pembatalan</em></p>
                                                                            @endif
                                                                        </td>
                                                                        <td>{{ $ticket->subject }}</td>
                                                                        <td>{{ $shortNotesTable }}</td>
                                                                        <td><span class="badge {{ $class }}">{{ $label }}</span></td>
                                                                        <td>{{ \Carbon\Carbon::parse($ticket->created_at)->translatedFormat('d F Y') }}</td>
                                                                        <td>
                                                                            @if($ticket->status == 'pending' || $ticket->status == 'process')
                                                                                <button type="button"
                                                                                        class="btn {{ $ticket->request_to_cancel == 1 ? 'btn-secondary' : 'btn-danger' }} btn-sm"
                                                                                        data-bs-toggle="modal" data-bs-target="#cancelTicketModal"
                                                                                        data-ticket-id="{{ $ticket->id }}"
                                                                                        data-ticket-subject="{{ $ticket->subject }}"
                                                                                        @disabled($ticket->request_to_cancel == 1)>
                                                                                    {{ $ticket->request_to_cancel == 1 ? 'Requested' : 'Cancel' }}
                                                                                </button>
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td colspan="6" class="text-center">
                                                                        Tidak ada tiket
                                                                        @if($currentStatusFilter == 'pending') pending.
                                                                        @elseif($currentStatusFilter == 'process') yang sedang diproses.
                                                                        @elseif($currentStatusFilter == 'completed') completed.
                                                                        @elseif($currentStatusFilter == 'canceled') yang ditutup atau ditolak.
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>

                                                {{-- Mobile Flatlist View --}}
                                                <div class="d-block d-md-none">
                                                    @if($hasTicketsInThisStatus)
                                                        @foreach($filteredTickets as $ticket)
                                                            @php
                                                                $class = $statusClasses[$ticket->status] ?? 'bg-light text-dark';
                                                                $label = $statusLabels[$ticket->status] ?? ucfirst($ticket->status);
                                                                $fullNotes = $ticket->description ?? $ticket->notes ?? 'Tidak ada catatan.';
                                                                $shortNotesMobile = Str::limit($fullNotes, 100, '...'); // Slightly more for mobile cards
                                                            @endphp
                                                            <div class="card mb-3 shadow-sm">
                                                                <div class="card-body">
                                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                                        <h6 class="card-title mb-0">{{ $ticket->subject }}</h6>
                                                                        <span class="badge {{ $class }}">{{ $label }}</span>
                                                                    </div>
                                                                    <p class="card-text mb-1"><small class="text-muted">Tipe:</small> {{ $ticket->ticket_type }}</p>
                                                                    <p class="card-text mb-1">
                                                                        <small class="text-muted">Catatan:</small> {{ $shortNotesMobile }}
                                                                        @if(strlen($fullNotes) > strlen($shortNotesMobile))
                                                                        <a href="#" class="text-primary small" data-bs-toggle="popover" data-bs-trigger="click" data-bs-placement="bottom" title="Detail Catatan" data-bs-content="{{ e($fullNotes) }}">(lihat semua)</a>
                                                                        @endif
                                                                    </p>
                                                                    <p class="card-text mb-2"><small class="text-muted">Dibuat:</small> {{ \Carbon\Carbon::parse($ticket->created_at)->translatedFormat('d F Y, H:i') }}</p>

                                                                    @if($ticket->request_to_cancel == 1 && ($ticket->status == 'pending' || $ticket->status == 'process'))
                                                                        <p class="text-danger small mt-1 mb-2"><em>Menunggu persetujuan pembatalan</em></p>
                                                                    @endif

                                                                    @if($ticket->status == 'pending' || $ticket->status == 'process')
                                                                        <button type="button"
                                                                                class="btn {{ $ticket->request_to_cancel == 1 ? 'btn-outline-secondary' : 'btn-outline-danger' }} btn-sm w-100"
                                                                                data-bs-toggle="modal" data-bs-target="#cancelTicketModal"
                                                                                data-ticket-id="{{ $ticket->id }}"
                                                                                data-ticket-subject="{{ $ticket->subject }}"
                                                                                @disabled($ticket->request_to_cancel == 1)>
                                                                            {{ $ticket->request_to_cancel == 1 ? 'Cancel Requested' : 'Request Cancel' }}
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="text-center py-3">
                                                            Tidak ada tiket
                                                            @if($currentStatusFilter == 'pending') pending.
                                                            @elseif($currentStatusFilter == 'process') yang sedang diproses.
                                                            @elseif($currentStatusFilter == 'completed') completed.
                                                            @elseif($currentStatusFilter == 'canceled') yang ditutup atau ditolak.
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach

                                    </div><!-- /.tab-content -->
                                </div> <!-- /.card-body -->
                            </div> <!-- /.card -->
                        </div><!-- /.tab-pane My Tickets -->

                    </div> {{-- End pills-tabContent --}}

                    <!-- Modal Konfirmasi Cancel -->
                    <div class="modal fade" id="cancelTicketModal" tabindex="-1"
                        aria-labelledby="cancelTicketModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="cancelTicketModalLabel">Konfirmasi Pembatalan Tiket</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Apakah Anda benar-benar ingin meminta pembatalan untuk tiket "<strong id="modalTicketSubject"></strong>"?
                                    <p class="text-muted small mt-2" id="cancelModalInfo" style="display: none;">Admin akan meninjau permintaan pembatalan Anda.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Tidak</button>
                                    <form action="{{ route('panel.ticket.cancel') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="ticket_id" id="cancelTicketIdInput" value="">
                                        <button type="submit" class="btn btn-danger" id="confirmCancelButton">Ya, Batalkan</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- End Modal --}}

                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Inisialisasi Popovers ---
        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
        const popoverList = [...popoverTriggerList].map(popoverTriggerEl => {
            return new bootstrap.Popover(popoverTriggerEl, {
                html: popoverTriggerEl.hasAttribute('data-bs-html') || popoverTriggerEl.getAttribute('data-bs-trigger') === 'click', // Allow HTML for click triggers too
                sanitize: ! (popoverTriggerEl.hasAttribute('data-bs-html') || popoverTriggerEl.getAttribute('data-bs-trigger') === 'click')
            });
        });


        // --- Handle Warning Box ---
        const ticketTypeSelect = document.getElementById('ticket-type');
        const warningBox = document.getElementById('warning-box');
        const showWarningFor = ['consultation', 'repair', 'remove'];

        function checkTicketType() {
            if (ticketTypeSelect && warningBox) {
                const selectedValue = ticketTypeSelect.value;
                if (showWarningFor.includes(selectedValue)) {
                    warningBox.style.display = 'block';
                } else {
                    warningBox.style.display = 'none';
                }
            }
        }
        if (ticketTypeSelect) {
            checkTicketType();
            ticketTypeSelect.addEventListener('change', checkTicketType);
        }


        // --- Handle Modal Cancel ---
        const cancelTicketModal = document.getElementById('cancelTicketModal');
        if (cancelTicketModal) {
            cancelTicketModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                const ticketId = button.getAttribute('data-ticket-id');
                const ticketSubject = button.getAttribute('data-ticket-subject');
                const isDisabled = button.hasAttribute('disabled');

                const modalSubjectSpan = cancelTicketModal.querySelector('#modalTicketSubject');
                const modalTicketIdInput = cancelTicketModal.querySelector('#cancelTicketIdInput');
                const modalInfo = cancelTicketModal.querySelector('#cancelModalInfo');

                if (modalSubjectSpan) modalSubjectSpan.textContent = ticketSubject ? ticketSubject : 'Tiket ini';
                if (modalTicketIdInput) modalTicketIdInput.value = ticketId;
                if (modalInfo) modalInfo.style.display = isDisabled ? 'none' : 'block'; // Tetap 'block' jika tidak disabled
            });
        }

        // --- Handle Persistent Tabs ---
        const mainTabContainer = document.getElementById('pills-tab');
        const subTabContainer = document.getElementById('ticketStatusTabs'); // My Tickets sub-tabs

        const mainTabKey = 'activeMainTicketTab';
        const subTabKey = 'activeSubTicketTab';

        function activateTab(tabTriggerEl) {
            if (tabTriggerEl) {
                try {
                    const tab = new bootstrap.Tab(tabTriggerEl);
                    tab.show();
                } catch (e) {
                    console.error("Error activating tab:", e, "Element:", tabTriggerEl);
                }
            }
        }

        const lastActiveMainTabTarget = sessionStorage.getItem(mainTabKey);
        const lastActiveSubTabTarget = sessionStorage.getItem(subTabKey);

        let mainTabButtonToActivate = document.querySelector('#pills-make-ticket-tab'); // Default main tab

        if (lastActiveMainTabTarget) {
            const potentialMainTabButton = document.querySelector(`#pills-tab button[data-bs-target="${lastActiveMainTabTarget}"]`);
            if (potentialMainTabButton) {
                mainTabButtonToActivate = potentialMainTabButton;
            }
        }
        activateTab(mainTabButtonToActivate);

        if (mainTabButtonToActivate && mainTabButtonToActivate.getAttribute('data-bs-target') === '#pills-my-ticket') {
            let subTabButtonToActivate = document.querySelector('#ticketStatusTabs #pending-tab'); // Default sub tab

            if (lastActiveSubTabTarget) {
                const potentialSubTabButton = document.querySelector(`#ticketStatusTabs button[data-bs-target="${lastActiveSubTabTarget}"]`);
                if (potentialSubTabButton) {
                    subTabButtonToActivate = potentialSubTabButton;
                }
            }
            if (subTabButtonToActivate) { // Pastikan subTabButtonToActivate tidak null
                activateTab(subTabButtonToActivate);
            } else {
                console.warn("Default sub-tab (#pending-tab) or stored sub-tab button not found for #pills-my-ticket, or subTabContainer is missing.");
            }
        }

        if (mainTabContainer) {
            mainTabContainer.addEventListener('shown.bs.tab', function (event) {
                const activeMainTabTarget = event.target.getAttribute('data-bs-target');
                if (activeMainTabTarget) {
                    sessionStorage.setItem(mainTabKey, activeMainTabTarget);

                    if (activeMainTabTarget === '#pills-my-ticket') {
                        const currentActiveSubTabButton = document.querySelector('#ticketStatusTabs .nav-link.active');
                        let subTabToMakeActive = document.querySelector('#ticketStatusTabs #pending-tab'); // Default
                        const storedSubTabTarget = sessionStorage.getItem(subTabKey);
                        if (storedSubTabTarget) {
                            const potentialSubTab = document.querySelector(`#ticketStatusTabs button[data-bs-target="${storedSubTabTarget}"]`);
                            if (potentialSubTab) {
                                subTabToMakeActive = potentialSubTab;
                            }
                        }
                        if (subTabToMakeActive && (!currentActiveSubTabButton || currentActiveSubTabButton.getAttribute('data-bs-target') !== subTabToMakeActive.getAttribute('data-bs-target'))) {
                           activateTab(subTabToMakeActive);
                        }
                    }
                }
            });
        }

        if (subTabContainer) {
            subTabContainer.addEventListener('shown.bs.tab', function (event) {
                const activeSubTabTarget = event.target.getAttribute('data-bs-target');
                if (activeSubTabTarget) {
                    sessionStorage.setItem(subTabKey, activeSubTabTarget);
                }
            });
        }
    });
    </script>

    </div>
@endsection