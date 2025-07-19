@extends('layout.sidebar') {{-- GANTI INI dengan layout admin Anda --}}

@section('content')
    @include('component.loader')
    {{-- CSS untuk fungsionalitas chat admin --}}
    <style>
        /* Styling Dasar Kolom */
        .chat-container {
            display: flex;
            height: calc(80vh - 50px);
            min-height: 500px;
        }
        .user-list-column {
            width: 30%;
            border-right: 1px solid #dee2e6;
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
        }
        .chat-area-column {
            width: 70%;
            display: flex;
            flex-direction: column;
        }
        .user-list-header, .chat-area-header {
            padding: 0.75rem 1.25rem;
            background-color: #e9ecef;
            border-bottom: 1px solid #dee2e6;
            font-weight: bold;
        }
        .user-list {
            overflow-y: auto;
            flex-grow: 1;
        }
        .user-list .list-group-item {
            cursor: pointer;
            border-radius: 0;
            border-left: 0;
            border-right: 0;
            border-top: 0;
        }
        .user-list .list-group-item:last-child { border-bottom: 0; }
        .user-list .list-group-item.active {
            background-color: #0EA2BC;
            color: white;
            border-color: #0EA2BC;
        }
        .user-list .list-group-item:hover:not(.active) { background-color: #e2e6ea; }
        #chat-box-admin {
            flex-grow: 1;
            overflow-y: auto;
            padding: 15px;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
        }
        .chat-card-footer-admin {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 0.75rem 1rem;
        }
        .no-conversation-selected {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            color: #6c757d;
            text-align: center;
            flex-direction: column;
            font-size: 1.1em;
        }
        .no-conversation-selected i {
            font-size: 3em;
            margin-bottom: 15px;
            color: #adb5bd;
        }
        /* Style Bubble Chat */
        .message-bubble {
            border-radius: 15px;
            margin-bottom: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            word-wrap: break-word;
            max-width: 85%;
            display: inline-block;
            vertical-align: top;
        }
        .message-out {
            background-color: #0EA2BC;
            color: white;
            border-bottom-right-radius: 5px;
        }
        .message-in {
            background-color: #e9ecef;
            color: #212529;
            border-bottom-left-radius: 5px;
        }
        .ticket-info-block {
            border-radius: 8px;
            font-size: 0.85em;
            margin-top: 5px;
            border-left: 4px solid rgba(0,0,0,0.2);
            padding: 8px 12px;
            text-align: left;
        }
        .message-in .ticket-info-block {
            background-color: rgba(0,0,0,0.05);
            border-left-color: rgba(0,0,0,0.3);
            color: #333;
        }
        .message-out .ticket-info-block {
            background-color: rgba(255,255,255,0.15);
            border-left-color: rgba(255,255,255,0.5);
            color: #f0f0f0;
        }
        .message-out .ticket-info-block a { color: #fff; text-decoration: underline; }
        .message-in .ticket-info-block a { color: #0EA2BC; text-decoration: underline; }
        .ticket-info-block p { margin-bottom: 0.25rem; }
        .ticket-info-block .badge { font-size: 0.9em; }
        .message-meta {
            font-size: 0.7rem !important;
            opacity: 0.8;
            margin-top: 3px;
            padding: 0 5px;
            display: block;
            width: 100%;
        }
        .align-items-end .message-meta { text-align: right; }
        .align-items-start .message-meta { text-align: left; }
        .loading-indicator {
            display: none;
            text-align: center;
            padding: 20px;
            color: #6c757d;
        }
        .loading-indicator.active { display: block; }

        /* Style untuk Pratinjau Lampiran & Modal */
        #attached-ticket-preview {
            display: none;
            padding: 8px 12px;
            background-color: #e9ecef;
            border-radius: 8px;
            margin-bottom: 10px;
            font-size: 0.85em;
            position: relative;
        }
        #attached-ticket-preview .attachment-text { color: #333; }
        #remove-attachment-btn {
            position: absolute;
            top: 5px; right: 10px;
            background: none; border: none;
            font-size: 1.2em; cursor: pointer;
            color: #555; padding: 0 5px;
        }
        #remove-attachment-btn:hover { color: #000; }
        .ticket-modal-item {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .ticket-modal-item:last-child { border-bottom: none; }
        .ticket-modal-item:hover { background-color: #f1f1f1; }
        .ticket-modal-item .ticket-subject { font-weight: 500; }
        .ticket-modal-item .ticket-id { color: #6c757d; font-size: 0.9em; }
    </style>

    <div class="pc-container">
        <div class="pc-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-body p-0">
                            <div class="chat-container">

                                {{-- KOLOM KIRI: DAFTAR USER --}}
                                <div class="user-list-column">
                                    <div class="user-list-header">Pengguna</div>
                                    <div class="list-group list-group-flush user-list" id="user-list">
                                        @php
                                            $jsUsersData = $users->mapWithKeys(function ($user) {
                                                return [
                                                    $user->id => [
                                                        'name' => $user->name,
                                                        'image' => $user->image ? asset('storage/' . $user->image) : asset('asset/image/profile.png'),
                                                        'initial_unread_count' => $user->unread_messages_to_admin_count ?? 0
                                                    ]
                                                ];
                                            });
                                        @endphp
                                         @forelse ($users as $user)
                                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                                data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" id="user-list-item-{{ $user->id }}">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $user->image ? asset('storage/' . $user->image) : asset('asset/image/profile.png') }}"
                                                        alt="{{ $user->name }}" class="rounded-circle me-2" style="width: 35px; height: 35px; object-fit: cover;"
                                                        onerror="this.onerror=null; this.src='{{ asset('asset/image/profile.png') }}';" id="user-list-image-{{ $user->id }}">
                                                    <span id="user-list-name-{{ $user->id }}">{{ $user->name }}</span>
                                                </div>
                                                <span class="badge bg-danger rounded-pill ms-auto unread-badge" id="unread-badge-{{ $user->id }}"
                                                    style="display: {{ ($user->unread_messages_to_admin_count ?? 0) > 0 ? 'inline-block' : 'none' }};">
                                                    {{ $user->unread_messages_to_admin_count ?? 0 }}
                                                </span>
                                            </a>
                                        @empty
                                            <div class="text-center p-3 text-muted" id="no-users-message">Belum ada pengguna memulai chat.</div>
                                        @endforelse
                                    </div>
                                </div>
                                {{-- AKHIR KOLOM KIRI --}}

                                {{-- KOLOM KANAN: AREA CHAT --}}
                                <div class="chat-area-column">
                                    <div class="chat-area-header" id="chat-area-header">Percakapan</div>
                                    <div id="chat-box-admin" data-current-user-id="">
                                        <div class="no-conversation-selected" id="no-conversation-selected">
                                            <i class="fas fa-comments"></i>
                                            <p>Pilih pengguna dari daftar di sebelah kiri untuk melihat percakapan.</p>
                                        </div>
                                        <div class="loading-indicator" id="loading-indicator"><i class="fas fa-spinner fa-spin"></i> Memuat percakapan...</div>
                                        <p class="no-conversation-message" id="empty-conversation-message" style="display: none;">Belum ada pesan dalam percakapan ini.</p>
                                    </div>

                                    <div class="card-footer chat-card-footer-admin" id="chat-input-area" style="display: none;">
                                        <div id="attached-ticket-preview">
                                            <span class="attachment-text"></span>
                                            <button id="remove-attachment-btn" title="Batal Lampirkan">×</button>
                                        </div>
                                        <form id="message-form-admin" action="{{ route('panel.admin.chat.send') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="receiver_id" id="admin-chat-receiver-id" value="">
                                            <input type="hidden" name="ticket_id" id="attached-ticket-id" value="">
                                            <div class="input-group">
                                                <button class="btn btn-outline-secondary" type="button" id="attach-ticket-btn" title="Lampirkan Tiket" disabled>
                                                    <i class="fas fa-paperclip"></i>
                                                </button>
                                                <input type="text" name="message" id="admin-message-input" class="form-control" placeholder="Ketik balasan untuk pengguna..." required autocomplete="off">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-paper-plane"></i> Kirim
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                {{-- AKHIR KOLOM KANAN --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Lampiran Tiket -->
    <div class="modal fade" id="ticket-attachment-modal" tabindex="-1" aria-labelledby="ticketAttachmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketAttachmentModalLabel">Pilih Tiket untuk Dilampirkan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" id="ticket-modal-body">
                    <div class="text-center p-4 text-muted">Memuat daftar tiket pengguna...</div>
                </div>
                 <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Referensi Elemen DOM ---
        const userList = document.getElementById('user-list');
        const chatBox = document.getElementById('chat-box-admin');
        const messageForm = document.getElementById('message-form-admin');
        const messageInput = document.getElementById('admin-message-input');
        const receiverIdInput = document.getElementById('admin-chat-receiver-id');
        const chatInputArea = document.getElementById('chat-input-area');
        const chatAreaHeader = document.getElementById('chat-area-header');
        const noConversationPlaceholder = document.getElementById('no-conversation-selected');
        const loadingIndicator = document.getElementById('loading-indicator');
        const emptyConversationMessage = document.getElementById('empty-conversation-message');
        const noUsersMessage = document.getElementById('no-users-message');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const defaultImageUrl = '{{ asset('asset/image/profile.png') }}';
        const storageBaseUrl = '{{ asset('storage') }}';

        // --- Elemen Baru untuk Lampiran Modal ---
        const attachTicketBtn = document.getElementById('attach-ticket-btn');
        const ticketModalElement = document.getElementById('ticket-attachment-modal');
        const ticketModal = new bootstrap.Modal(ticketModalElement);
        const ticketModalBody = document.getElementById('ticket-modal-body');
        const attachedTicketPreview = document.getElementById('attached-ticket-preview');
        const attachedTicketIdInput = document.getElementById('attached-ticket-id');
        const removeAttachmentBtn = document.getElementById('remove-attachment-btn');

        // --- Variabel State ---
        const loggedInAdminId = {{ $adminUserId ?? 'null' }};
        let selectedUserId = null;
        let pusher = null;
        let userChatChannel = null;
        let adminNotificationChannel = null;

        // --- Fungsi Bantuan ---
        function scrollToBottom() { setTimeout(() => { chatBox.scrollTop = chatBox.scrollHeight; }, 100); }
        function getStatusBadgeClass(status) {
            if (!status) return 'badge bg-info';
            const ls = status.toLowerCase();
            switch (ls) {
                case 'open': case 'baru': return 'badge bg-success';
                case 'pending': case 'menunggu balasan': case 'diproses': return 'badge bg-warning text-dark';
                case 'closed': case 'selesai': return 'badge bg-secondary';
                default: return 'badge bg-info';
            }
        }
        function createBadgeElement(userId, count) {
            const badge = document.createElement('span');
            badge.className = 'badge bg-danger rounded-pill ms-auto unread-badge';
            badge.id = `unread-badge-${userId}`;
            badge.textContent = count;
            badge.style.display = (count > 0) ? 'inline-block' : 'none';
            return badge;
        }

        // --- Fungsi Logika Chat ---
        function displayMessages(messages, currentConvUserId) {
            chatBox.innerHTML = '';
            loadingIndicator.classList.remove('active');
            noConversationPlaceholder.style.display = 'none';

            if (!messages || messages.length === 0) {
                emptyConversationMessage.style.display = 'block';
                return;
            }
            emptyConversationMessage.style.display = 'none';

            messages.forEach(msgData => {
                if (!msgData || typeof msgData.sender_id === 'undefined') return;
                const isOut = msgData.sender_id === loggedInAdminId;
                const alignWrapperClass = isOut ? 'align-items-end' : 'align-items-start';
                const bubbleStylingClass = isOut ? 'message-out' : 'message-in';
                const senderName = isOut ? 'Anda (Admin)' : (msgData.sender?.profile?.name ?? `User ${msgData.sender_id}`);
                const formattedTime = msgData.formatted_time || new Date(msgData.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                const wrapper = document.createElement('div'); wrapper.className = `d-flex flex-column mb-3 ${alignWrapperClass}`;
                const bubble = document.createElement('div'); bubble.className = `message-bubble p-3 ${bubbleStylingClass}`;
                const ticketData = msgData.ticket;
                if (ticketData && ticketData.id) {
                    const tBlock = document.createElement('div'); tBlock.className = 'ticket-info-block'; tBlock.innerHTML = `<p class="fw-bold mb-1">Tiket Terlampir:</p><p><strong>ID:</strong> #${ticketData.id}</p><p><strong>Subjek:</strong> ${ticketData.subject || 'N/A'}</p><p class="mb-0"><strong>Status:</strong> <span class="${getStatusBadgeClass(ticketData.status)}">${ticketData.status || 'N/A'}</span></p>`; bubble.appendChild(tBlock);
                }
                const msgSpan = document.createElement('span'); msgSpan.style.whiteSpace = 'pre-wrap'; msgSpan.textContent = msgData.message; bubble.appendChild(msgSpan);
                wrapper.appendChild(bubble);
                const meta = document.createElement('div'); meta.className = 'message-meta text-muted small'; meta.textContent = `${senderName} ・ ${formattedTime}`; wrapper.appendChild(meta);
                chatBox.appendChild(wrapper);
            });
            scrollToBottom();
        }

        function addIncomingMessageToBox(msgData) {
            if (!msgData || typeof msgData.sender_id === 'undefined' || msgData.sender_id !== selectedUserId) return;
            emptyConversationMessage.style.display = 'none';
            noConversationPlaceholder.style.display = 'none';

            const senderName = msgData.sender?.profile?.name ?? `User ${msgData.sender_id}`;
            const formattedTime = msgData.formatted_time || new Date(msgData.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            const wrapper = document.createElement('div'); wrapper.className = 'd-flex flex-column mb-3 align-items-start';
            const bubble = document.createElement('div'); bubble.className = 'message-bubble p-3 message-in';
            const ticketData = msgData.ticket;
            if (ticketData && ticketData.id) {
                const tBlock = document.createElement('div'); tBlock.className = 'ticket-info-block'; tBlock.innerHTML = `<p class="fw-bold mb-1">Tiket Terlampir:</p><p><strong>ID:</strong> #${ticketData.id}</p><p><strong>Subjek:</strong> ${ticketData.subject || 'N/A'}</p><p class="mb-0"><strong>Status:</strong> <span class="${getStatusBadgeClass(ticketData.status)}">${ticketData.status || 'N/A'}</span></p>`; bubble.appendChild(tBlock);
            }
            const msgSpan = document.createElement('span'); msgSpan.style.whiteSpace = 'pre-wrap'; msgSpan.textContent = msgData.message; bubble.appendChild(msgSpan);
            wrapper.appendChild(bubble);
            const meta = document.createElement('div'); meta.className = 'message-meta text-muted small'; meta.textContent = `${senderName} ・ ${formattedTime}`; wrapper.appendChild(meta);
            chatBox.appendChild(wrapper);
            scrollToBottom();
        }

        function addSentMessageToBox(msgData) {
            emptyConversationMessage.style.display = 'none';
            noConversationPlaceholder.style.display = 'none';

            const formattedTime = msgData.formatted_time || new Date(msgData.created_at || Date.now()).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            const wrapper = document.createElement('div'); wrapper.className = 'd-flex flex-column mb-3 align-items-end';
            const bubble = document.createElement('div'); bubble.className = 'message-bubble p-3 message-out';
            const ticketData = msgData.ticket;
            if (ticketData && ticketData.id) {
                const tBlock = document.createElement('div'); tBlock.className = 'ticket-info-block'; tBlock.innerHTML = `<p class="fw-bold mb-1">Tiket Terlampir:</p><p><strong>ID:</strong> #${ticketData.id}</p><p><strong>Subjek:</strong> ${ticketData.subject || 'N/A'}</p><p class="mb-0"><strong>Status:</strong> <span class="${getStatusBadgeClass(ticketData.status)}">${ticketData.status || 'N/A'}</span></p>`; bubble.appendChild(tBlock);
            }
            const msgSpan = document.createElement('span'); msgSpan.style.whiteSpace = 'pre-wrap'; msgSpan.textContent = msgData.message; bubble.appendChild(msgSpan);
            wrapper.appendChild(bubble);
            const meta = document.createElement('div'); meta.className = 'message-meta text-muted small'; meta.textContent = `Anda (Admin) ・ ${formattedTime}`; wrapper.appendChild(meta);
            chatBox.appendChild(wrapper);
            scrollToBottom();
        }

        function populateTicketModal(tickets) {
            ticketModalBody.innerHTML = '';
            if (tickets && tickets.length > 0) {
                attachTicketBtn.disabled = false;
                tickets.forEach(ticket => {
                    const item = document.createElement('div');
                    item.className = 'ticket-modal-item';
                    item.dataset.ticketId = ticket.id;
                    item.dataset.ticketSubject = ticket.subject || 'Tanpa Subjek';
                    item.innerHTML = `<div><div class="ticket-subject">${ticket.subject || 'Tanpa Subjek'}</div><div class="ticket-id">#${ticket.id}</div></div><span class="${getStatusBadgeClass(ticket.status)}">${ticket.status || 'N/A'}</span>`;
                    ticketModalBody.appendChild(item);
                });
            } else {
                attachTicketBtn.disabled = true;
                ticketModalBody.innerHTML = '<div class="text-center p-4 text-muted">Pengguna ini tidak memiliki tiket.</div>';
            }
        }

        function clearTicketAttachment() {
            attachedTicketIdInput.value = '';
            attachedTicketPreview.style.display = 'none';
            attachedTicketPreview.querySelector('.attachment-text').textContent = '';
        }

        function updateUserListRealtime(eventData) {
            const { message: messagePayload, sender_data: senderData } = eventData;
            if (!messagePayload || !senderData || !senderData.id) return;
            if (senderData.id === loggedInAdminId) return;

            const { id: senderId, profile } = senderData;
            const senderName = profile.name;
            const imageUrl = profile.image ? `${storageBaseUrl}/${profile.image}` : defaultImageUrl;
            let userListItem = document.getElementById(`user-list-item-${senderId}`);

            if (userListItem) {
                let badge = document.getElementById(`unread-badge-${senderId}`);
                if (!badge) {
                    badge = createBadgeElement(senderId, 0);
                    userListItem.appendChild(badge);
                }
                if (selectedUserId !== senderId) {
                    badge.textContent = (parseInt(badge.textContent) || 0) + 1;
                    badge.style.display = 'inline-block';
                }
                userList.prepend(userListItem);
            } else {
                noUsersMessage?.remove();
                userListItem = document.createElement('a');
                userListItem.href = '#';
                userListItem.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                userListItem.id = `user-list-item-${senderId}`;
                userListItem.dataset.userId = senderId;
                userListItem.dataset.userName = senderName;
                userListItem.innerHTML = `<div class="d-flex align-items-center"><img src="${imageUrl}" alt="${senderName}" class="rounded-circle me-2" style="width: 35px; height: 35px; object-fit: cover;" onerror="this.onerror=null; this.src='${defaultImageUrl}';"><span id="user-list-name-${senderId}">${senderName}</span></div>`;
                const badge = createBadgeElement(senderId, 1);
                userListItem.appendChild(badge);
                userList.prepend(userListItem);
            }
        }

        // --- Event Listener ---
        userList.addEventListener('click', function (e) {
            const selectedItem = e.target.closest('.list-group-item');
            if (!selectedItem) return;
            e.preventDefault();

            const userId = parseInt(selectedItem.dataset.userId);
            if (selectedUserId === userId) return;

            userList.querySelector('.active')?.classList.remove('active');
            selectedItem.classList.add('active');
            selectedUserId = userId;
            clearTicketAttachment();

            if (window.subscribeToUserChatChannel) {
                window.subscribeToUserChatChannel(selectedUserId);
            }

            chatAreaHeader.textContent = `Percakapan dengan ${selectedItem.dataset.userName}`;
            receiverIdInput.value = selectedUserId;
            chatBox.innerHTML = '';
            loadingIndicator.classList.add('active');
            noConversationPlaceholder.style.display = 'none';
            emptyConversationMessage.style.display = 'none';
            chatInputArea.style.display = 'none';

            fetch(`/panel/admin/chat/conversation/${selectedUserId}`, {
                method: 'GET',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }
            })
            .then(response => {
                loadingIndicator.classList.remove('active');
                if (!response.ok) return response.json().then(err => Promise.reject(err));
                return response.json();
            })
            .then(data => {
                displayMessages(data.messages, data.selectedUserId);
                populateTicketModal(data.userTickets);
                chatInputArea.style.display = 'block';
                messageInput.focus();
                const badgeToClear = document.getElementById(`unread-badge-${selectedUserId}`);
                if (badgeToClear) {
                    badgeToClear.textContent = '0';
                    badgeToClear.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Gagal mengambil percakapan:', error);
                chatBox.innerHTML = `<div class="alert alert-danger m-3">Gagal memuat percakapan.</div>`;
            });
        });

        attachTicketBtn.addEventListener('click', () => ticketModal.show());
        ticketModalBody.addEventListener('click', function(e) {
            const selectedTicket = e.target.closest('.ticket-modal-item');
            if (selectedTicket) {
                attachedTicketIdInput.value = selectedTicket.dataset.ticketId;
                attachedTicketPreview.querySelector('.attachment-text').textContent = `Terlampir: Tiket #${selectedTicket.dataset.ticketId} - ${selectedTicket.dataset.ticketSubject}`;
                attachedTicketPreview.style.display = 'block';
                ticketModal.hide();
                messageInput.focus();
            }
        });
        removeAttachmentBtn.addEventListener('click', () => clearTicketAttachment());

        messageForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const messageText = messageInput.value.trim();
            if (messageText === '' || !receiverIdInput.value) return;

            const formData = new FormData(messageForm);
            const submitButton = messageForm.querySelector('button[type="submit"]');
            submitButton.disabled = true;

            fetch(messageForm.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData
            })
            .then(response => {
                if (!response.ok) return response.json().then(err => Promise.reject(err));
                return response.json();
            })
            .then(data => {
                if (data.success && data.message) {
                    addSentMessageToBox(data.message);
                    messageInput.value = '';
                    clearTicketAttachment();
                } else {
                    throw new Error(data.error || 'Terjadi kesalahan.');
                }
            })
            .catch(error => {
                console.error('Gagal mengirim pesan:', error);
                alert(`Gagal mengirim pesan: ${error.message || 'Silakan coba lagi.'}`);
            })
            .finally(() => {
                submitButton.disabled = false;
                messageInput.focus();
            });
        });

        // --- Inisialisasi dan Konfigurasi Pusher (Tidak Diubah) ---
        if (typeof Pusher !== 'undefined' && loggedInAdminId && csrfToken) {
            Pusher.logToConsole = true; // Aktifkan untuk debugging

            try {
                pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                    cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                    forceTLS: (('{{ env('PUSHER_SCHEME') }}' || 'https') === 'https'),
                    authEndpoint: '/broadcasting/auth',
                    auth: { headers: { 'X-CSRF-TOKEN': csrfToken } }
                });

                // 1. Channel Notifikasi Admin (untuk pembaruan daftar pengguna)
                const adminChannelName = 'private-admin-channel';
                adminNotificationChannel = pusher.subscribe(adminChannelName);
                adminNotificationChannel.bind('pusher:subscription_error', status => console.error(`Pusher: Gagal subscribe ke ${adminChannelName}:`, status));
                adminNotificationChannel.bind('pusher:subscription_succeeded', () => {
                    console.log(`Pusher: Berhasil subscribe ke ${adminChannelName}.`);
                    adminNotificationChannel.bind('new-message', data => {
                        console.log(`Pusher: Menerima event 'new-message' di channel admin:`, data);
                        if (data.message && data.message.receiver_id === null && data.message.sender_id !== loggedInAdminId) {
                            updateUserListRealtime(data);
                        }
                    });
                });

            } catch (e) {
                console.error("Pusher: Gagal inisialisasi:", e);
            }

            // 2. Fungsi untuk Subscribe Channel Chat Pengguna (saat percakapan dibuka)
            window.subscribeToUserChatChannel = function (userId) {
                if (!pusher || !userId) return;
                const newChannelName = `private-conversation.${userId}`;
                if (userChatChannel && userChatChannel.name === newChannelName) return;

                if (userChatChannel) {
                    pusher.unsubscribe(userChatChannel.name);
                }

                userChatChannel = pusher.subscribe(newChannelName);
                userChatChannel.bind('pusher:subscription_error', status => console.error(`Pusher: Gagal subscribe ke ${newChannelName}:`, status));
                userChatChannel.bind('pusher:subscription_succeeded', () => {
                    console.log(`Pusher: Berhasil subscribe ke ${newChannelName}`);
                    userChatChannel.bind('new-message', data => {
                        console.log(`Pusher: Menerima event di channel user aktif ${newChannelName}:`, data);
                        if (data.message && selectedUserId === data.message.sender_id) {
                            addIncomingMessageToBox(data.message);
                            fetch('{{ route('panel.admin.chat.markAsRead') }}', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                                body: JSON.stringify({ user_id: selectedUserId })
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.success) console.log(`Pusher: Pesan dari user ${selectedUserId} ditandai dibaca.`);
                                else console.warn(`Pusher: Gagal menandai pesan dibaca (server).`);
                            })
                            .catch(error => console.error('Pusher: Error saat request mark-as-read:', error));
                        }
                    });
                });
            }

        } else {
            console.error("Pusher tidak dapat diinisialisasi. Periksa library, Admin ID, atau CSRF token.");
        }

    });
    </script>
@endsection