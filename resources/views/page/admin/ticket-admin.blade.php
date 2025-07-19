@extends('layout.sidebar') {{-- Sesuaikan dengan nama layout utama Anda --}}

@section('content')
    {{-- Pastikan CSRF token ada di layout utama Anda atau tambahkan di sini --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('panel.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page">Admin Ticket View</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Admin Ticket Overview</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
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
            <!-- [ Main Content ] start -->
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
                                            data-user-id="{{ $u->id }}" data-user-name="{{ $u->profile->name ?? $u->name }}"
                                            data-user-institution="{{ $u->profile->institution ?? 'N/A' }}"
                                            data-user-address="{{ $u->profile->address ?? 'N/A' }}">
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
                    <h5 class="card-title mb-0 ticket-subject"></h5> <!-- Ticket Subject -->
                    <span class="badge ticket-status"></span> <!-- Ticket Status -->
                </div>
                <p class="mb-1"><strong>Type:</strong> <span class="ticket-type-display"></span></p>
                <p class="card-text ticket-notes" style="white-space: pre-wrap; max-height: 150px; overflow-y: auto;"></p>
                <p class="card-text mb-0">
                    <small class="text-muted">Created: <span class="ticket-created-at"></span></small>
                </p>
                <div class="ticket-cancel-info-placeholder"></div>
                @if(auth()->user()->role == 'admin')
                    <hr class="my-2">
                    <div class="ticket-actions">
                        <div class="d-flex justify-content-end mt-2">
                            <button class="btn btn-sm me-2 process-ticket-btn" style="background-color: #0EA2BC ;color:white"
                                data-ticket-id="" data-ticket-subject="" data-ticket-type="">
                                <i class="fas fa-newspaper"></i> Process
                            </button>
                            <form method="POST" action="{{ route('panel.ticket.cancel') }}" class="d-inline admin-cancel-form">
                                @csrf
                                <input type="hidden" name="ticket_id" value="" class="admin-cancel-ticket-id-input">
                                <button type="submit" class="btn btn-sm admin-process-cancel-btn">
                                    <i class="fas fa-eraser"></i> Cancel
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </template>

    <!-- ======================================================================= -->
    <!-- ============== MODAL B (requiredEquipmentModal) - DIROMBAK ============ -->
    <!-- ======================================================================= -->
    <div class="modal fade" id="requiredEquipmentModal" tabindex="-1" aria-labelledby="requiredEquipmentModalLabel" aria-hidden="true">
        {{-- PERUBAHAN: Menggunakan modal-lg agar konsisten --}}
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="requiredEquipmentModalLabel">Process Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- PERUBAHAN: Menambahkan struktur row dengan 2 kolom --}}
                    <div class="row">
                        <!-- KOLOM UTAMA (AREA AKSI) -->
                        <div class="col-md-8">
                            {{-- LANGKAH 2: PEMILIHAN PERANGKAT --}}
                            <div id="equipmentSelectionView">
                                <h6>Langkah 1: Pilih Perangkat</h6>
                                <p>Pilih perangkat yang dibutuhkan untuk tiket: <strong id="modalTicketSubject"></strong></p>
                                <input type="text" id="equipmentSearchInput" class="form-control mb-3" placeholder="Cari berdasarkan Merek, Model, Tipe...">
                                <div id="inventoryListContainer" style="max-height: 45vh; overflow-y: auto; border: 1px solid #eee; padding: 10px;">
                                     <p class="text-muted" id="inventoryListPlaceholder">Ketik untuk mencari inventaris...</p>
                                </div>
                            </div>
                            
                            {{-- LANGKAH 3: PRATINJAU DOKUMEN SST --}}
                            <div id="sstDocumentView" style="display: none; font-family: 'Times New Roman', Times, serif; font-size: 12pt; padding: 20px; border: 1px solid #ccc; background-color: #f9f9f9; height: 100%;">
                                <p class="text-center">Memuat dokumen...</p>
                            </div>
                        </div>
    
                        <!-- KOLOM KANAN (PANEL RINGKASAN) -->
                        <div class="col-md-4 border-start">
                            <div class="p-2">
                                 <h5>Ringkasan</h5>
                                 <hr>
                                 <div>
                                     <strong>Klien:</strong>
                                     {{-- PENAMBAHAN: Placeholder untuk nama klien yang sudah diketahui --}}
                                     <p id="requiredEquipmentClientName" class="text-primary fw-bold">N/A</p>
                                 </div>
                                 <div class="mt-3">
                                    <strong>Perangkat Dipilih:</strong>
                                    {{-- PERUBAHAN: Memindahkan display equipment ke sini --}}
                                    <div id="selectedEquipmentDisplay" class="mt-2" style="max-height: 40vh; overflow-y: auto;">
                                        <ul id="selectedEquipmentList" class="list-group">
                                            <li class="list-group-item text-muted" id="noEquipmentSelected">Belum ada perangkat yang dipilih.</li>
                                        </ul>
                                    </div>
                                 </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- Footer ini sudah terstruktur dengan baik, tidak perlu banyak perubahan --}}
                    <div id="equipmentSelectionFooter">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="confirmAndShowSstBtn">Lanjutkan ke Pratinjau SST</button>
                    </div>
                    <div id="sstDocumentFooter" style="display: none;">
                        <button type="button" class="btn btn-secondary me-2" id="backToEquipmentSelectionBtn">Kembali</button>
                        <button type="button" class="btn btn-success" id="processNowBtn">Proses & Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

{{-- ====================================================================== --}}
{{-- =================== BLOK SCRIPT UTUH DAN FINAL ======================= --}}
{{-- ====================================================================== --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Variabel Global
        const allInventories = @json($inventories ?? []);
        let currentTicketIdForModal = null;
        let currentTicketDataForSST = {};
        let currentUserProfileData = {};
        let selectedEquipments = [];

        // Selektor Elemen DOM
        const userListContainer = document.getElementById('user-list-container');
        const ticketDetailsContainer = document.getElementById('ticket-details-container');
        const ticketItemTemplate = document.getElementById('ticket-item-template');
        const ticketListHeader = document.getElementById('ticket-list-header');
        const selectUserMessage = document.getElementById('select-user-message');
        const ticketLoader = document.getElementById('ticket-loader');

        // Selektor DOM untuk Modal B (Required Equipment Modal)
        const requiredEquipmentModalEl = document.getElementById('requiredEquipmentModal');
        const requiredEquipmentModal = new bootstrap.Modal(requiredEquipmentModalEl);
        const modalTicketSubjectEl = document.getElementById('modalTicketSubject');
        const requiredEquipmentClientNameEl = document.getElementById('requiredEquipmentClientName');
        const equipmentSearchInput = document.getElementById('equipmentSearchInput');
        const inventoryListContainer = document.getElementById('inventoryListContainer');
        const selectedEquipmentList = document.getElementById('selectedEquipmentList');
        const equipmentSelectionView = requiredEquipmentModalEl.querySelector('#equipmentSelectionView');
        const sstDocumentView = requiredEquipmentModalEl.querySelector('#sstDocumentView');
        const equipmentSelectionFooter = requiredEquipmentModalEl.querySelector('#equipmentSelectionFooter');
        const sstDocumentFooter = requiredEquipmentModalEl.querySelector('#sstDocumentFooter');
        const confirmAndShowSstBtn = requiredEquipmentModalEl.querySelector('#confirmAndShowSstBtn');
        const backToEquipmentSelectionBtn = requiredEquipmentModalEl.querySelector('#backToEquipmentSelectionBtn');
        const processNowBtn = requiredEquipmentModalEl.querySelector('#processNowBtn');

        // Konfigurasi Status
        const statusClasses = { 'pending': 'bg-warning text-dark', 'process': 'bg-info text-white', 'inprogress': 'bg-primary text-white', 'completed': 'bg-success text-white', 'canceled': 'bg-secondary text-white', 'default': 'bg-light text-dark' };
        const statusLabels = { 'pending': 'Pending', 'process': 'Ready to Process', 'inprogress': 'In Progress', 'completed': 'Completed', 'canceled': 'Canceled', 'default': 'Unknown' };
        
        // Event saat tombol 'Process' pada tiket di-klik
        ticketDetailsContainer.addEventListener('click', function (event) {
            const button = event.target.closest('.process-ticket-btn');
            if (button) {
                currentTicketIdForModal = button.dataset.ticketId;
                currentTicketDataForSST = { subject: button.dataset.ticketSubject, type: button.dataset.ticketType };
                
                modalTicketSubjectEl.textContent = currentTicketDataForSST.subject;
                requiredEquipmentClientNameEl.textContent = currentUserProfileData.name;

                requiredEquipmentModal.show();
            }
        });

        // =========================================================================
        // == PERBAIKAN UTAMA: TAMPILKAN ITEM SAAT MODAL DIBUKA ==
        // =========================================================================
        // Event listener ini akan berjalan SETIAP KALI modal akan ditampilkan.
        requiredEquipmentModalEl.addEventListener('show.bs.modal', function () {
            // Panggil fungsi renderInventoryList dengan string pencarian kosong ''
            // untuk menampilkan SEMUA item inventaris secara default.
            // Ini akan menggantikan placeholder "Ketik untuk mencari inventaris...".
            renderInventoryList('');
        });

        // Logika untuk mereset Modal B setiap kali ditutup (Kode Anda sudah benar)
        requiredEquipmentModalEl.addEventListener('hidden.bs.modal', function() {
            selectedEquipments = [];
            currentTicketIdForModal = null;
            equipmentSearchInput.value = '';
            renderSelectedEquipments(); 
            renderInventoryList('');    
            showEquipmentSelectionView(); 
            requiredEquipmentClientNameEl.textContent = 'N/A'; 
            processNowBtn.disabled = false;
            processNowBtn.innerHTML = 'Proses & Simpan';
        });


        // =========================================================================
        // == LOGIKA MODAL B (PEMILIHAN PERANGKAT, SST, DLL) ==
        // =========================================================================
        
        // Listener untuk input pencarian (Kode Anda sudah benar)
        equipmentSearchInput.addEventListener('input', function() { renderInventoryList(this.value); });
        
        // Fungsi lainnya (tidak ada perubahan, gunakan kode asli Anda)
        // ... (renderSelectedEquipments, showEquipmentSelectionView, generateSstDocument, dll.) ...


        // NOTE: Semua fungsi di bawah ini dibiarkan seperti aslinya karena sudah berfungsi dengan baik.
        // Perubahan di atas adalah satu-satunya yang diperlukan untuk mencapai tujuan Anda.
        
        userListContainer.addEventListener('click', function (event) {
            let target = event.target.closest('.user-list-item');
            if (target) {
                event.preventDefault();
                currentUserProfileData = { name: target.dataset.userName, institution: target.dataset.userInstitution, address: target.dataset.userAddress };
                userListContainer.querySelectorAll('.user-list-item.active').forEach(item => item.classList.remove('active', 'bg-light'));
                target.classList.add('active', 'bg-light');
                ticketListHeader.textContent = `Tickets for ${currentUserProfileData.name}`;
                fetchAndRenderTickets(target.dataset.userId);
            }
        });

        async function fetchAndRenderTickets(userId) {
            ticketDetailsContainer.innerHTML = '';
            selectUserMessage.style.display = 'none';
            ticketLoader.style.display = 'block';
            try {
                const response = await fetch(`{{ url('/admin/users') }}/${userId}/tickets`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const tickets = await response.json();
                ticketLoader.style.display = 'none';
                if (tickets.length === 0) {
                    ticketDetailsContainer.innerHTML = '<p class="text-muted">This user has no tickets.</p>';
                    return;
                }
                tickets.forEach(ticket => {
                    const ticketNode = ticketItemTemplate.content.cloneNode(true);
                    ticketNode.querySelector('.ticket-subject').textContent = ticket.subject || 'No Subject';
                    ticketNode.querySelector('.ticket-type-display').textContent = ticket.ticket_type ? (ticket.ticket_type.charAt(0).toUpperCase() + ticket.ticket_type.slice(1)) : 'N/A';
                    ticketNode.querySelector('.ticket-notes').textContent = ticket.notes || ticket.description || 'No additional notes.';
                    const statusBadge = ticketNode.querySelector('.ticket-status');
                    const ticketStatus = ticket.status ? ticket.status.toLowerCase() : 'default';
                    statusBadge.textContent = statusLabels[ticketStatus] || statusLabels['default'];
                    statusBadge.className = 'badge ticket-status ' + (statusClasses[ticketStatus] || statusClasses['default']);
                    const createdAtEl = ticketNode.querySelector('.ticket-created-at');
                    if (createdAtEl && ticket.created_at) createdAtEl.textContent = new Date(ticket.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                    const processBtn = ticketNode.querySelector('.process-ticket-btn');
                    if (processBtn) {
                        processBtn.dataset.ticketId = ticket.id;
                        processBtn.dataset.ticketSubject = ticket.subject || 'Untitled Ticket';
                        processBtn.dataset.ticketType = ticket.ticket_type || 'N/A';
                        processBtn.disabled = ticket.status !== 'process';
                        if (processBtn.disabled) {
                            processBtn.title = 'Ticket cannot be processed at this status.';
                            processBtn.style.backgroundColor = '#6c757d';
                        }
                    }
                    const adminCancelButton = ticketNode.querySelector('.admin-process-cancel-btn');
                    if (adminCancelButton) {
                        ticketNode.querySelector('.admin-cancel-ticket-id-input').value = ticket.id;
                        adminCancelButton.disabled = ticket.request_to_cancel != 1;
                        if (!adminCancelButton.disabled) {
                            adminCancelButton.classList.add('btn-danger');
                            adminCancelButton.innerHTML = '<i class="fas fa-user-check"></i> Confirm Client Cancel';
                        } else {
                            adminCancelButton.classList.add('btn-secondary', 'disabled');
                            adminCancelButton.innerHTML = '<i class="fas fa-ban"></i> Cancel';
                        }
                    }
                    ticketDetailsContainer.appendChild(ticketNode);
                });
            } catch (error) {
                console.error('Error fetching tickets:', error);
                ticketLoader.style.display = 'none';
                ticketDetailsContainer.innerHTML = `<p class="text-danger">Could not load tickets. ${error.message}</p>`;
            }
        }
        function renderInventoryList(searchTerm) {
            inventoryListContainer.innerHTML = '';
            const lowerSearchTerm = searchTerm.toLowerCase();
            const filteredInventories = allInventories.filter(inv => !inv.device ? false : [inv.device.brand, inv.device.model, inv.device.type, inv.device_id].some(val => (val || '').toString().toLowerCase().includes(lowerSearchTerm)));
            if (filteredInventories.length === 0) {
                inventoryListContainer.innerHTML = `<p class="text-muted">${searchTerm ? 'No inventory found.' : 'Type to search inventory...'}</p>`;
                return;
            }
            const ul = document.createElement('ul');
            ul.className = 'list-group';
            filteredInventories.forEach(inv => {
                const li = document.createElement('li');
                li.className = 'list-group-item';
                li.dataset.inventoryId = inv.id;
                const selectedItem = selectedEquipments.find(eq => eq.inventory.id === inv.id);
                li.innerHTML = `<div class="d-flex justify-content-between align-items-center"><div><strong>${inv.device.brand || 'N/A'} ${inv.device.model || 'N/A'}</strong> (${inv.device.type || 'N/A'})<br><small class="text-muted">Stock: ${inv.stock} | Condition: ${inv.condition}</small></div><div class="item-action-container"><div class="select-button-container"><button class="btn btn-sm ${selectedItem ? 'btn-success' : 'btn-outline-primary'} select-item-btn">${selectedItem ? `✓ Selected (${selectedItem.quantity})` : 'Select'}</button></div><div class="quantity-form-container" style="display: none;"><div class="input-group input-group-sm"><input type="number" class="form-control quantity-input" value="${selectedItem ? selectedItem.quantity : 1}" min="1" max="${inv.stock}" style="width: 70px;"><button class="btn btn-primary confirm-quantity-btn">OK</button><button class="btn btn-outline-secondary cancel-quantity-btn ms-1">×</button></div><p class="quantity-error-msg text-danger small mt-1" style="display: none;">Quantity exceeds available stock.</p></div></div></div>`;
                ul.appendChild(li);
            });
            inventoryListContainer.appendChild(ul);
        }
        inventoryListContainer.addEventListener('click', function(event) {
            const selectBtn = event.target.closest('.select-item-btn');
            const confirmBtn = event.target.closest('.confirm-quantity-btn');
            const cancelBtn = event.target.closest('.cancel-quantity-btn');
            if (selectBtn) {
                const li = selectBtn.closest('li');
                resetAllInventoryItemsUI(); 
                li.querySelector('.select-button-container').style.display = 'none';
                const formContainer = li.querySelector('.quantity-form-container');
                formContainer.style.display = 'block';
                formContainer.querySelector('.quantity-input').focus();
            }
            if (confirmBtn) {
                const li = confirmBtn.closest('li');
                const inventoryId = parseInt(li.dataset.inventoryId, 10);
                const quantityInput = li.querySelector('.quantity-input');
                const quantity = parseInt(quantityInput.value, 10);
                const inventoryData = allInventories.find(inv => inv.id === inventoryId);
                if (!confirmBtn.disabled && quantity > 0 && quantity <= inventoryData.stock) {
                    const existingItemIndex = selectedEquipments.findIndex(eq => eq.inventory.id === inventoryId);
                    if (existingItemIndex > -1) {
                        selectedEquipments[existingItemIndex].quantity = quantity;
                    } else {
                        selectedEquipments.push({ inventory: inventoryData, quantity: quantity });
                    }
                    renderSelectedEquipments();
                    renderInventoryList(equipmentSearchInput.value);
                }
            }
            if (cancelBtn) { resetAllInventoryItemsUI(); }
        });
        inventoryListContainer.addEventListener('input', function(event) {
            const quantityInput = event.target;
            if (quantityInput.classList.contains('quantity-input')) {
                const li = quantityInput.closest('li');
                const inventoryId = parseInt(li.dataset.inventoryId, 10);
                const inventoryData = allInventories.find(inv => inv.id === inventoryId);
                const stock = inventoryData.stock;
                const currentValue = parseInt(quantityInput.value, 10);
                const confirmBtn = li.querySelector('.confirm-quantity-btn');
                const errorMsg = li.querySelector('.quantity-error-msg');
                if (isNaN(currentValue) || currentValue < 1 || currentValue > stock) {
                    confirmBtn.disabled = true;
                    confirmBtn.classList.add('btn-secondary');
                    errorMsg.style.display = 'block';
                    errorMsg.textContent = currentValue > stock ? `Quantity exceeds available stock (${stock}).` : 'Quantity must be at least 1.';
                } else {
                    confirmBtn.disabled = false;
                    confirmBtn.classList.remove('btn-secondary');
                    errorMsg.style.display = 'none';
                }
            }
        });
        function resetAllInventoryItemsUI() {
            inventoryListContainer.querySelectorAll('li').forEach(item => {
                item.querySelector('.select-button-container').style.display = 'block';
                item.querySelector('.quantity-form-container').style.display = 'none';
            });
        }
        function renderSelectedEquipments() {
            selectedEquipmentList.innerHTML = '';
            if (selectedEquipments.length === 0) {
                selectedEquipmentList.innerHTML = '<li class="list-group-item text-muted" id="noEquipmentSelected">No equipment selected yet.</li>';
                return;
            }
            selectedEquipments.forEach((item, index) => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `<span>${item.inventory.device.brand} ${item.inventory.device.model}<small class="text-muted d-block">Qty: ${item.quantity}</small></span><button class="btn btn-sm btn-outline-danger remove-selected-equipment-btn" data-index="${index}">×</button>`;
                selectedEquipmentList.appendChild(li);
            });
        }
        selectedEquipmentList.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-selected-equipment-btn')) {
                const indexToRemove = parseInt(event.target.dataset.index, 10);
                selectedEquipments.splice(indexToRemove, 1);
                renderSelectedEquipments();
                renderInventoryList(equipmentSearchInput.value);
            }
        });
        function showEquipmentSelectionView() {
            equipmentSelectionView.style.display = 'block';
            sstDocumentView.style.display = 'none';
            equipmentSelectionFooter.style.display = 'flex';
            sstDocumentFooter.style.display = 'none';
            requiredEquipmentModalEl.querySelector('.modal-title').textContent = `Process Ticket`;
        }
        function showSstDocumentView() {
            if (selectedEquipments.length === 0) {
                alert('Pilih setidaknya satu perangkat untuk melanjutkan.');
                return;
            }
            equipmentSelectionView.style.display = 'none';
            sstDocumentView.style.display = 'block';
            equipmentSelectionFooter.style.display = 'none';
            sstDocumentFooter.style.display = 'flex';
            requiredEquipmentModalEl.querySelector('.modal-title').textContent = 'Pratinjau Surat Serah Terima';
            generateSstDocument();
        }
        confirmAndShowSstBtn.addEventListener('click', showSstDocumentView);
        backToEquipmentSelectionBtn.addEventListener('click', showEquipmentSelectionView);
         processNowBtn.addEventListener('click', async function () {
            if (!currentTicketIdForModal || selectedEquipments.length === 0) { alert('Error: Ticket ID or selected equipment is missing.'); return; }
            const payload = { ticket_id: parseInt(currentTicketIdForModal), equipments: selectedEquipments.map(item => ({ id: item.inventory.id, quantity: item.quantity })) };
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
            try {
                const response = await fetch("{{ route('admin.letters.generateSst') }}", { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, body: JSON.stringify(payload) });
                const result = await response.json();
                if (response.ok) {
                    requiredEquipmentModal.hide();
                    window.location.href = "{{ route('admin.letter.view') }}";
                  
                } else {
                    let errorMessage = result.message || 'Failed to process the request.';
                    if (result.errors) errorMessage += '\n\nDetails:\n' + Object.values(result.errors).map(e => `- ${e[0]}`).join('\n');
                    alert(errorMessage);
                }
            } catch (error) {
                console.error('Error processing SST:', error);
                alert('An unexpected network or client-side error occurred.');
            } finally {
                this.disabled = false;
                this.innerHTML = 'Process Now';
            }
        });
       
        function generateSstDocument() {
            const today = new Date();
            const localDate = today.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            const bulanRomawi = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            const nomorSurat = `.../SST/DISKOMINFO/${bulanRomawi[today.getMonth()]}/${today.getFullYear()}`;
            let perangkatListHtml = selectedEquipments.map(item => `<li style="margin-bottom: 5px;">${item.inventory.device.brand} ${item.inventory.device.model} (${item.inventory.condition}) - Jumlah: ${item.quantity} unit</li>`).join('');
            sstDocumentView.innerHTML = `<div style="text-align: center; margin-bottom: 20px;"><h4 style="text-decoration: underline; margin-bottom: 5px; font-weight: bold;">SURAT SERAH TERIMA PEMASANGAN JARINGAN</h4><p style="margin-top: 0;">Nomor: ${nomorSurat}</p></div><p>Pada hari ini, ${localDate}, telah dilakukan serah terima pekerjaan Pemasangan Jaringan Internet antara:</p><table style="width: 100%; margin-bottom: 15px;"><tr><td style="width: 150px;"><strong>Pihak Pertama:</strong></td><td>(Perwakilan DISKOMINFO Pariaman)</td></tr><tr><td style="width: 150px;"><strong>Pihak Kedua:</strong></td><td>(Perwakilan Klien)</td></tr><tr><td>Nama</td><td>: ${currentUserProfileData.name || 'N/A'}</td></tr><tr><td>Instansi</td><td>: ${currentUserProfileData.institution || 'N/A'}</td></tr><tr><td>Alamat</td><td>: ${currentUserProfileData.address || 'N/A'}</td></tr></table><p><strong>Rincian Pekerjaan:</strong></p><table style="width: 100%; margin-bottom: 15px;"><tr><td style="width: 180px;">Jenis Layanan</td><td>: ${currentTicketDataForSST.type ? (currentTicketDataForSST.type.charAt(0).toUpperCase() + currentTicketDataForSST.type.slice(1)) : 'N/A'}</td></tr><tr><td>Tanggal Pemasangan</td><td>: ${today.toLocaleDateString('id-ID')}</td></tr></table><p><strong>Rincian Perangkat yang digunakan:</strong></p><ol style="padding-left: 20px; margin-bottom: 15px;">${perangkatListHtml}</ol><p>Dengan ini, kedua belah pihak menyatakan bahwa pekerjaan pemasangan jaringan telah dilakukan sesuai dengan permintaan dan perangkat telah diterima dalam keadaan baik.</p><table style="width: 100%; margin-top: 40px; text-align: center;"><tr><td>Pihak Pertama,</td><td>Pihak Kedua,</td></tr><tr><td style="padding-top: 60px; padding-bottom: 10px;">(__________________)</td><td style="padding-top: 60px; padding-bottom: 10px;">(${currentUserProfileData.name || '__________________'})</td></tr></table>`;
        }
        if (userListContainer && userListContainer.querySelector('.user-list-item')) {
            userListContainer.querySelector('.user-list-item').click();
        } else {
            if(selectUserMessage) selectUserMessage.style.display = 'block';
        }
    });
</script>
@endsection