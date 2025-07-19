@extends('layout.sidebar') {{-- Sesuaikan dengan nama layout utama Anda --}}

@section('content')
    {{-- 1. Tambahkan SweetAlert2 CDN di bagian atas --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @include('component.loader')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('panel.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page">Master Ticket View</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Ticket Requested View</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <!-- User List (Kolom Kiri) -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Users with Tickets</h5>
                        </div>
                        <div class="card-body" style="max-height: 75vh; overflow-y: auto;">
                            @if($usersForList->isEmpty())
                                <p class="text-muted">No users found with active tickets.</p>
                            @else
                                <div class="list-group" id="user-list-container">
                                    @foreach ($usersForList as $u)
                                        <a href="#"
                                            class="list-group-item list-group-item-action d-flex align-items-center user-list-item"
                                            data-user-id="{{ $u->id }}" data-user-name="{{ $u->profile->name ?? $u->name }}">
                                            <img src="{{ $u->profile && $u->profile->image ? asset('storage/' . $u->profile->image) : asset('assets/images/user/avatar-default.png') }}"
                                                alt="{{ $u->profile->name ?? $u->name }}" class="rounded-circle me-3" width="45"
                                                height="45" style="object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0">{{ $u->profile->name ?? $u->name }}</h6>
                                                <small class="text-muted">{{ $u->email }}</small>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Ticket Details (Kolom Kanan) -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 id="ticket-list-header">Tickets</h5>
                        </div>
                        <div class="card-body" id="ticket-details-container" style="max-height: 75vh; overflow-y: auto;">
                            <p class="text-muted" id="select-user-message">Select a user from the list to view their
                                tickets.</p>
                            <div id="ticket-loader" style="display: none; text-align: center; padding: 20px;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p>Loading tickets...</p>
                            </div>
                            <!-- Tiket akan dimuat di sini oleh JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>

    <!-- Template untuk item tiket (digunakan oleh JavaScript) -->
    <template id="ticket-item-template">
        <div class="card mb-3 shadow-sm ticket-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="card-title mb-0 ticket-subject"></h5>
                    <span class="badge ticket-status"></span>
                </div>
                <p class="mb-1"><strong>Type:</strong> <span class="ticket-type"></span></p>
                <p class="card-text ticket-notes" style="white-space: pre-wrap; max-height: 150px; overflow-y: auto;"></p>
                <p class="card-text mb-0"><small class="text-muted">Created: <span class="ticket-created-at"></span></small></p>
                <div class="ticket-cancel-info-placeholder"></div>
                @if(auth()->user()->role == 'master')
                    <hr class="my-2">
                    <div class="ticket-actions">
                        <div class="d-flex justify-content-end mt-2">
                            <button class="btn btn-sm me-2 accept-ticket-btn" style="background-color: #0EA2BC; color:white;" data-ticket-id="">Accept</button>
                            <button class="btn btn-danger btn-sm reject-ticket-btn" data-ticket-id="">Reject</button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </template>

    {{-- 2. Hapus HTML Modal Bootstrap yang lama. Sudah tidak diperlukan lagi. --}}
    {{-- <div class="modal fade" id="confirmationModal" ...> ... </div> --}}


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Elemen UI Utama
            const userListContainer = document.getElementById('user-list-container');
            const ticketDetailsContainer = document.getElementById('ticket-details-container');
            const ticketItemTemplate = document.getElementById('ticket-item-template');
            const ticketListHeader = document.getElementById('ticket-list-header');
            const selectUserMessage = document.getElementById('select-user-message');
            const ticketLoader = document.getElementById('ticket-loader');

            // 3. Hapus referensi ke modal Bootstrap yang lama
            // let bsConfirmationModal; -> Dihapus

            // Fungsi untuk melakukan aksi (accept/reject)
            async function performTicketAction(ticketId, actionType) {
                const url = `/tickets/${ticketId}/${actionType}`;
                const selectedUserId = localStorage.getItem('lastUserId');

                // Tampilkan loader SweetAlert
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we update the ticket.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const result = await response.json();

                    if (response.ok) {
                        Swal.fire({
                            title: 'Success!',
                            text: result.message || 'Ticket status has been updated.',
                            icon: 'success'
                        }).then(() => {
                            // Refresh daftar tiket untuk pengguna yang sedang dipilih
                            if (selectedUserId) {
                                fetchAndRenderTickets(selectedUserId);
                            } else {
                                location.reload(); // Fallback
                            }
                        });
                    } else {
                        // Tampilkan error menggunakan SweetAlert
                        Swal.fire({
                            title: 'Error!',
                            text: result.message || 'Failed to update ticket status.',
                            icon: 'error'
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Oops...',
                        text: 'Something went wrong. Please check the console.',
                        icon: 'error'
                    });
                }
            }


            // Event listener untuk tombol accept dan reject
            ticketDetailsContainer.addEventListener('click', function (event) {
                const targetButton = event.target.closest('.accept-ticket-btn, .reject-ticket-btn');
                if (!targetButton) return;

                const ticketId = targetButton.dataset.ticketId;
                const actionType = targetButton.classList.contains('accept-ticket-btn') ? 'accept' : 'reject';

                // 4. Ganti pemanggilan modal dengan SweetAlert2
                const swalOptions = {
                    title: `Konfirmasi ${actionType === 'accept' ? 'Penerimaan' : 'Penolakan'}`,
                    text: `Apakah Anda yakin ingin ${actionType === 'accept' ? 'menerima' : 'menolak'} tiket ini?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: actionType === 'accept' ? '#0EA2BC' : '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Lanjutkan!',
                    cancelButtonText: 'Batal'
                };

                Swal.fire(swalOptions).then((result) => {
                    if (result.isConfirmed) {
                        // Panggil fungsi untuk memproses aksi jika dikonfirmasi
                        performTicketAction(ticketId, actionType);
                    }
                });
            });


            // --- SISA KODE (TIDAK ADA PERUBAHAN) ---

            const lastUserId = localStorage.getItem('lastUserId');
            if (lastUserId) {
                const lastUserItem = userListContainer.querySelector(`[data-user-id="${lastUserId}"]`);
                if (lastUserItem) {
                    setTimeout(() => lastUserItem.click(), 100);
                }
            } else if (userListContainer) {
                const firstUserItem = userListContainer.querySelector('.user-list-item');
                if (firstUserItem) {
                    setTimeout(() => firstUserItem.click(), 100);
                } else if (selectUserMessage) {
                    selectUserMessage.style.display = 'block';
                }
            }


            if (!userListContainer || !ticketDetailsContainer || !ticketItemTemplate || !ticketListHeader || !selectUserMessage || !ticketLoader) {
                console.error('One or more essential UI elements are missing.');
                if(selectUserMessage) selectUserMessage.textContent = 'Error: UI components missing.';
                return;
            }

            const statusClasses = {
                'pending': 'bg-warning text-dark', 'process': 'bg-primary text-white', 'inprogress': 'bg-primary text-white',
                'completed': 'bg-success text-white', 'rejected': 'bg-danger text-white', 'canceled': 'bg-secondary text-white', 'default': 'bg-light text-dark'
            };
            const statusLabels = {
                'pending': 'Pending', 'process': 'In Process', 'inprogress': 'In Progress',
                'completed': 'Completed', 'rejected': 'Rejected', 'canceled': 'Canceled', 'default': 'Unknown'
            };

            userListContainer.addEventListener('click', function (event) {
                let target = event.target.closest('.user-list-item');
                if (target) {
                    event.preventDefault();
                    const userId = target.dataset.userId;
                    const userName = target.dataset.userName || 'Selected User';
                    if (!userId) return;
                    localStorage.setItem('lastUserId', userId);
                    userListContainer.querySelectorAll('.user-list-item.active').forEach(item => item.classList.remove('active', 'bg-light'));
                    target.classList.add('active', 'bg-light');
                    if (ticketListHeader) ticketListHeader.textContent = `Tickets for ${userName}`;
                    fetchAndRenderTickets(userId);
                }
            });

            async function fetchAndRenderTickets(userId) {
                if (ticketDetailsContainer) ticketDetailsContainer.innerHTML = '';
                if (selectUserMessage) selectUserMessage.style.display = 'none';
                if (ticketLoader) ticketLoader.style.display = 'block';

                try {
                    const url = `{{ url('/admin/users') }}/${userId}/tickets`;
                    const response = await fetch(url);
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    const tickets = await response.json();

                    if (ticketLoader) ticketLoader.style.display = 'none';
                    if (tickets.length === 0) {
                        if (ticketDetailsContainer) ticketDetailsContainer.innerHTML = '<p class="text-muted">This user has no tickets.</p>';
                        return;
                    }

                    tickets.forEach(ticket => {
                        const ticketNode = ticketItemTemplate.content.cloneNode(true);
                        const ticketCard = ticketNode.querySelector('.ticket-card');
                        if (!ticketCard) return;

                        const acceptBtn = ticketCard.querySelector('.accept-ticket-btn');
                        const rejectBtn = ticketCard.querySelector('.reject-ticket-btn');
                        if (acceptBtn) acceptBtn.dataset.ticketId = ticket.id;
                        if (rejectBtn) rejectBtn.dataset.ticketId = ticket.id;

                        const subjectEl = ticketCard.querySelector('.ticket-subject');
                        if (subjectEl) subjectEl.textContent = ticket.subject || 'No Subject';

                        const typeEl = ticketCard.querySelector('.ticket-type');
                        if (typeEl) typeEl.textContent = ticket.ticket_type ? (ticket.ticket_type.charAt(0).toUpperCase() + ticket.ticket_type.slice(1)) : 'N/A';

                        const notesEl = ticketCard.querySelector('.ticket-notes');
                        if (notesEl) notesEl.textContent = ticket.notes || ticket.description || 'No additional notes.';

                        const statusBadge = ticketCard.querySelector('.ticket-status');
                        const ticketStatus = ticket.status ? ticket.status.toLowerCase() : 'default';
                        if (statusBadge) {
                            if (ticketStatus === 'pending') {
                                statusBadge.textContent = 'Waiting from master';
                            } else {
                                statusBadge.textContent = statusLabels[ticketStatus] || statusLabels['default'];
                            }
                            statusBadge.className = 'badge ticket-status ' + (statusClasses[ticketStatus] || statusClasses['default']);
                        }

                        const createdAtEl = ticketCard.querySelector('.ticket-created-at');
                        if (createdAtEl && ticket.created_at) {
                            try {
                                createdAtEl.textContent = new Date(ticket.created_at).toLocaleDateString('id-ID', {day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'});
                            } catch (e) { createdAtEl.textContent = "Invalid date"; }
                        } else if (createdAtEl) {
                            createdAtEl.textContent = "N/A";
                        }

                        const cancelInfoPlaceholder = ticketCard.querySelector('.ticket-cancel-info-placeholder');
                        if (cancelInfoPlaceholder && ticket.request_to_cancel == 1) {
                            const cancelInfo = document.createElement('p');
                            cancelInfo.className = 'text-danger small mt-2 mb-0';
                            cancelInfo.innerHTML = '<em>User has requested cancellation for this ticket.</em>';
                            cancelInfoPlaceholder.innerHTML = '';
                            cancelInfoPlaceholder.appendChild(cancelInfo);
                        } else if (cancelInfoPlaceholder) {
                            cancelInfoPlaceholder.innerHTML = '';
                        }

                        if (ticketDetailsContainer) ticketDetailsContainer.appendChild(ticketNode);
                    });

                } catch (error) {
                    console.error('Error fetching or rendering tickets:', error);
                    if (ticketLoader) ticketLoader.style.display = 'none';
                    if (ticketDetailsContainer) ticketDetailsContainer.innerHTML = `<p class="text-danger">Could not load tickets. ${error.message}</p>`;
                }
            }
        });
    </script>
@endsection