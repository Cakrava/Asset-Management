@extends('layout.sidebar')
@section('content')
    @include('component.loader')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function tampilkanDivSesuaiUkuran() {
                var isMobile = window.innerWidth <= 768;
                var divMessageMobile = document.getElementById('divMessageMobile');
                var divMessageDesktop = document.getElementById('divMessageDesktop');
                if (divMessageMobile && divMessageDesktop) {
                    divMessageMobile.style.display = isMobile ? 'flex' : 'none';
                    divMessageDesktop.style.display = isMobile ? 'none' : 'block';
                }
            }
            tampilkanDivSesuaiUkuran();
            window.addEventListener('resize', tampilkanDivSesuaiUkuran);
        });
    </script>

    {{-- ======================= TAMPILAN MOBILE ======================= --}}
    <div id="divMessageMobile" style="display: none;">
        <style>
            /* ... (CSS Mobile yang sudah ada) ... */
            #divMessageMobile html,
            #divMessageMobile body {
                height: 100%;
                margin: 0;
                overflow: hidden;
                background-color: #f5f5f5;
                -webkit-tap-highlight-color: transparent;
            }

            #divMessageMobile .mobile-chat-container {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                display: flex;
                flex-direction: column;
                background-color: #fff;
                z-index: 1000;
            }

            #divMessageMobile .chat-header {
                background-color: #0EA2BC;
                color: white;
                padding: 15px;
                display: flex;
                align-items: center;
                position: sticky;
                top: 0;
                z-index: 10;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }

            #divMessageMobile .chat-header h5 {
                margin: 0;
                font-weight: 600;
                font-size: 1.2rem;
            }

            #divMessageMobile .back-button {
                color: white;
                font-size: 1.5rem;
                margin-right: 15px;
                text-decoration: none;
                display: flex;
                align-items: center;
            }

            #divMessageMobile .chat-box-mobile-content {
                flex: 1;
                overflow-y: auto;
                padding: 15px;

                background-color: #f5f5f5;
                -webkit-overflow-scrolling: touch;
            }

            #divMessageMobile .message-bubble {
                max-width: 80%;
                padding: 12px 16px;
                margin-bottom: 8px;
                border-radius: 18px;
                position: relative;
                word-wrap: break-word;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            }

            #divMessageMobile .message-out {
                background-color: #0EA2BC;
                color: white;
                align-self: flex-end;
                border-bottom-right-radius: 4px;
            }

            #divMessageMobile .message-in {
                background-color: #ffffff;
                color: #333;
                align-self: flex-start;
                border-bottom-left-radius: 4px;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            }

            #divMessageMobile .ticket-info-block {
                border-radius: 12px;
                font-size: 0.85em;
                margin: 8px 0;
                padding: 10px;
                background-color: rgba(255, 255, 255, 0.2);
            }

            #divMessageMobile .message-in .ticket-info-block {
                background-color: rgba(0, 0, 0, 0.05);
            }

            #divMessageMobile .message-meta {
                font-size: 0.7rem;
                opacity: 0.8;
                margin-top: 4px;
            }

            #divMessageMobile .input-area-wrapper {
                padding: 0 15px 10px 15px;
                background-color: #fff;
                border-top: 1px solid #e0e0e0;
                position: sticky;
                bottom: 0;
                padding-top: 10px;
                z-index: 10;
            }

            #divMessageMobile .input-area {
                display: flex;
                align-items: center;
            }

            #divMessageMobile #attached-ticket-info-mobile {
                font-size: 0.8rem;
                color: #555;
                background-color: #e9ecef;
                padding: 5px 10px;
                border-radius: 15px;
                margin-bottom: 8px;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            #divMessageMobile #attached-ticket-info-mobile .text-content {
                margin-right: 8px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: calc(100% - 30px);
            }

            #divMessageMobile #remove-attached-ticket-btn-mobile {
                color: #dc3545;
                padding: 0;
                line-height: 1;
                background: none;
                border: none;
                cursor: pointer;
            }

            #divMessageMobile #remove-attached-ticket-btn-mobile i {
                font-size: 0.9rem;
            }

            #divMessageMobile #action-button-mobile {
                background-color: transparent;
                border: none;
                color: #0EA2BC;
                padding: 8px 10px 8px 0;
                font-size: 1.3rem;
                cursor: pointer;
                margin-right: 8px;
            }

            #divMessageMobile #action-button-mobile:hover {
                color: #0a889c;
            }

            #divMessageMobile .message-input {
                flex: 1;
                border: 1px solid #e0e0e0;
                border-radius: 20px;
                padding: 10px 15px;
                outline: none;
                font-size: 1rem;
                margin-right: 10px;
            }

            #divMessageMobile .send-button {
                background-color: #0EA2BC;
                color: white;
                border: none;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                min-width: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
            }

            #divMessageMobile .no-conversation-message {
                text-align: center;
                color: #999;
                padding: 40px 20px;
            }

            #divMessageMobile .status-bar {
                height: env(safe-area-inset-top);
                background-color: #0EA2BC;
            }

            #divMessageMobile .bottom-safe-area {
                height: env(safe-area-inset-bottom);
                background-color: #fff;
            }

            #divMessageMobile .ticket-item-in-modal-mobile {
                cursor: pointer;
                padding: 10px 15px;
                border-bottom: 1px solid #eee;
            }

            #divMessageMobile .ticket-item-in-modal-mobile:last-child {
                border-bottom: none;
            }

            #divMessageMobile .ticket-item-in-modal-mobile:hover {
                background-color: #f8f9fa;
            }

            #divMessageMobile .ticket-item-in-modal-mobile .ticket-subject {
                font-weight: 500;
                margin-bottom: 0.25rem;
                color: #333;
            }

            #divMessageMobile .ticket-item-in-modal-mobile .ticket-details {
                font-size: 0.85rem;
                color: #6c757d;
            }

            #actionModalMobile .modal-body .list-group-item-action {
                color: #495057;
            }

            #actionModalMobile .modal-body .list-group-item-action:hover {
                background-color: #f8f9fa;
            }

            #actionModalMobile .action-option,
            #actionModalDesktop .action-option {
                display: flex;
                align-items: center;
                padding: 12px 15px;
                cursor: pointer;
                border-bottom: 1px solid #eee;
            }

            #actionModalMobile .action-option:last-child,
            #actionModalDesktop .action-option:last-child {
                border-bottom: none;
            }

            #actionModalMobile .action-option:hover,
            #actionModalDesktop .action-option:hover {
                background-color: #f8f9fa;
            }

            #actionModalMobile .action-option i,
            #actionModalDesktop .action-option i {
                font-size: 1.2rem;
                margin-right: 15px;
                color: #0EA2BC;
            }

            #actionModalMobile .action-option.danger i,
            #actionModalDesktop .action-option.danger i {
                color: #dc3545;
            }

            #actionModalMobile .action-option span,
            #actionModalDesktop .action-option span {
                font-size: 1rem;
                color: #333;
            }
        </style>

        <div class="mobile-chat-container">
            <div class="status-bar"></div>
            <div class="chat-header">
                <a href="{{ url()->previous() }}" class="back-button">
                    <i class="ti ti-arrow-left"></i>
                </a>
                <h5>Chat dengan Admin</h5>
            </div>

            <div class="chat-box-mobile-content" id="chat-box-mobile">
                @php $messageExistsMobile = false; @endphp
                @forelse ($messages as $message)
                    @php
                        $messageExistsMobile = true;
                        $isOut = $message->sender_id === $userId;
                        $bubbleStylingClass = $isOut ? 'message-out' : 'message-in';
                        $senderName = $isOut ? 'Anda' : ($message->sender->name ?? 'Admin');
                    @endphp
                    <div class="d-flex flex-column {{ $isOut ? 'align-items-end' : 'align-items-start' }}">
                        <div class="message-bubble {{ $bubbleStylingClass }}">
                            @if ($message->ticket)
                                <div class="ticket-info-block">
                                    <p class="fw-bold mb-1">Tiket Terlampir:</p>
                                    <p><strong>ID:</strong> #{{ $message->ticket->id }}</p>
                                    <p><strong>Subjek:</strong> {{ $message->ticket->subject ?? 'N/A' }}</p>
                                    <p class="mb-0">
                                        <strong>Status:</strong>
                                        @php
                                            $status = strtolower($message->ticket->status ?? 'unknown');
                                            $statusClass = match ($status) {
                                                'open', 'baru' => 'badge bg-success',
                                                'pending', 'menunggu balasan', 'diproses' => 'badge bg-warning text-dark',
                                                'closed', 'selesai' => 'badge bg-secondary',
                                                default => 'badge bg-info',
                                            };
                                        @endphp
                                        <span class="{{ $statusClass }}">{{ $message->ticket->status ?? 'N/A' }}</span>
                                    </p>
                                </div>
                            @endif
                            <span style="white-space: pre-wrap;">{{ $message->message }}</span>
                        </div>
                        <div class="message-meta text-muted">
                            {{ $senderName }} ・ {{ $message->created_at->format('h:i A') }}
                        </div>
                    </div>
                @empty
                    <p class="no-conversation-message" id="initial-no-message-mobile">Belum ada percakapan. Mulai ketik pesan di
                        bawah!</p>
                @endforelse
            </div>

            <div class="input-area-wrapper">
                <div id="attached-ticket-info-mobile" style="display: none;">
                    <span class="text-content"></span>
                    <button type="button" id="remove-attached-ticket-btn-mobile" title="Hapus Lampiran">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>
                <div class="input-area">
                    <button type="button" id="action-button-mobile" title="Aksi Lainnya" data-bs-toggle="modal"
                        data-bs-target="#actionModalMobile">
                        <i class="fas fa-plus-circle"></i>
                    </button>
                    <input type="hidden" name="ticket_id_mobile" id="selected_ticket_id_mobile" value="">
                    <input type="text" name="message_mobile" id="message-input-mobile" class="message-input"
                        placeholder="Ketik pesan..." autocomplete="off">
                    <button id="send-button-mobile" class="send-button">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
            <div class="bottom-safe-area"></div>
        </div>

        <div class="modal fade" id="actionModalMobile" tabindex="-1" aria-labelledby="actionModalLabelMobile"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="actionModalTitleMobile">Pilih Aksi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0" id="actionModalBodyMobile">
                        {{-- Konten diisi oleh JS --}}
                    </div>
                    <div class="modal-footer" id="actionModalFooterMobile" style="display: flex;"> {{-- Pastikan footer
                        terlihat --}}
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                            id="actionModalCloseButtonMobile">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const chatBoxMobile = document.getElementById('chat-box-mobile');
                const messageInputMobile = document.getElementById('message-input-mobile');
                const sendButtonMobile = document.getElementById('send-button-mobile');
                const actionButtonMobile = document.getElementById('action-button-mobile');
                const actionModalMobileElement = document.getElementById('actionModalMobile');
                const actionModalTitleMobile = document.getElementById('actionModalTitleMobile');
                const actionModalBodyMobile = document.getElementById('actionModalBodyMobile');
                const actionModalFooterMobile = document.getElementById('actionModalFooterMobile');
                const actionModalCloseButtonMobile = document.getElementById('actionModalCloseButtonMobile');

                const selectedTicketIdInputMobile = document.getElementById('selected_ticket_id_mobile');
                const attachedTicketInfoDivMobile = document.getElementById('attached-ticket-info-mobile');
                const attachedTicketTextSpanMobile = attachedTicketInfoDivMobile ? attachedTicketInfoDivMobile.querySelector('.text-content') : null;
                const removeAttachedTicketButtonMobile = document.getElementById('remove-attached-ticket-btn-mobile');

                let actionModalInstanceMobile = null;
                if (actionModalMobileElement) {
                    actionModalInstanceMobile = new bootstrap.Modal(actionModalMobileElement);
                }

                const loggedInUserIdMobile = {{ Auth::id() ?? 'null' }};
                const initialNoMessageMobile = document.getElementById('initial-no-message-mobile');
                const csrfTokenMobile = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                function scrollToBottomMobile() { setTimeout(() => { if (chatBoxMobile) chatBoxMobile.scrollTop = chatBoxMobile.scrollHeight; }, 100); }
                scrollToBottomMobile();

                if (removeAttachedTicketButtonMobile && selectedTicketIdInputMobile && attachedTicketInfoDivMobile && attachedTicketTextSpanMobile) {
                    removeAttachedTicketButtonMobile.addEventListener('click', function () {
                        selectedTicketIdInputMobile.value = '';
                        attachedTicketTextSpanMobile.textContent = '';
                        attachedTicketInfoDivMobile.style.display = 'none';
                        if (messageInputMobile) messageInputMobile.focus();
                    });
                }

                if (sendButtonMobile) sendButtonMobile.addEventListener('click', sendMessageMobile);
                if (messageInputMobile) messageInputMobile.addEventListener('keypress', function (e) { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessageMobile(); } });

                function sendMessageMobile() {
                    const messageText = messageInputMobile.value.trim();
                    const attachedTicketId = selectedTicketIdInputMobile ? selectedTicketIdInputMobile.value : '';

                    if (messageText === '' && attachedTicketId === '') { if (messageInputMobile) messageInputMobile.focus(); return; }
                    if (!csrfTokenMobile) { console.error('CSRF token not found!'); alert('Gagal mengirim pesan: Sesi tidak valid.'); return; }

                    const formData = new FormData();
                    formData.append('message', messageText);
                    formData.append('_token', csrfTokenMobile);
                    if (attachedTicketId) formData.append('ticket_id', attachedTicketId);

                    if (messageInputMobile) messageInputMobile.disabled = true;
                    let originalIconHTML = ''; // Simpan ikon asli
                    if (sendButtonMobile) {
                        sendButtonMobile.disabled = true;
                        originalIconHTML = sendButtonMobile.innerHTML;
                        sendButtonMobile.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    }

                    fetch('{{ route('panel.chat.send') }}', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, body: formData })
                        .then(response => {
                            if (!response.ok) { return response.json().then(errData => { throw new Error(errData.message || `HTTP error ${response.status}`); }).catch(() => { throw new Error(`HTTP error ${response.status}`); }); }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success && data.message) {
                                addMessageToBoxMobile(data.message); // Panggil ini untuk menampilkan pesan
                                if (messageInputMobile) messageInputMobile.value = '';
                                if (selectedTicketIdInputMobile) selectedTicketIdInputMobile.value = '';
                                if (attachedTicketInfoDivMobile) attachedTicketInfoDivMobile.style.display = 'none';
                                if (attachedTicketTextSpanMobile) attachedTicketTextSpanMobile.textContent = '';
                                scrollToBottomMobile(); // Pastikan scroll setelah pesan ditambahkan
                            } else { alert(data.message || 'Gagal mengirim pesan. Respons tidak sukses.'); }
                        })
                        .catch(error => { console.error('Error sending message (Mobile):', error); alert('Terjadi kesalahan saat mengirim pesan: ' + error.message); })
                        .finally(() => {
                            if (messageInputMobile) messageInputMobile.disabled = false;
                            if (sendButtonMobile) { sendButtonMobile.disabled = false; sendButtonMobile.innerHTML = originalIconHTML; }
                            if (messageInputMobile) messageInputMobile.focus();
                        });
                }

                function addMessageToBoxMobile(msgData, senderProfileData = null) {
                    if (!chatBoxMobile || !msgData || typeof msgData.sender_id === 'undefined') { console.error("Invalid data for addMessageToBoxMobile:", msgData); return; }
                    const noConversationMsg = chatBoxMobile.querySelector('.no-conversation-message');
                    if (noConversationMsg) noConversationMsg.remove();
                    const initialMsg = document.getElementById('initial-no-message-mobile');
                    if (initialMsg && chatBoxMobile.contains(initialMsg)) initialMsg.remove();

                    const isOut = msgData.sender_id === loggedInUserIdMobile;
                    let senderName = isOut ? 'Anda' : (senderProfileData?.profile?.name || msgData.sender?.profile?.name || msgData.sender?.name || 'Admin');
                    const formattedTime = msgData.formatted_time || new Date(msgData.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });

                    const wrapper = document.createElement('div');
                    wrapper.className = `d-flex flex-column ${isOut ? 'align-items-end' : 'align-items-start'}`;
                    const bubble = document.createElement('div');
                    bubble.className = `message-bubble ${isOut ? 'message-out' : 'message-in'}`;

                    const ticketData = msgData.ticket || msgData.ticket_details;
                    if (ticketData && ticketData.id) {
                        const ticketInfoBlock = document.createElement('div');
                        ticketInfoBlock.className = `ticket-info-block`;
                        ticketInfoBlock.innerHTML = `<p class="fw-bold mb-1">Tiket Terlampir:</p><p><strong>ID:</strong> #${ticketData.id}</p><p><strong>Subjek:</strong> ${ticketData.subject || 'N/A'}</p><p class="mb-0"><strong>Status:</strong> <span class="${getStatusBadgeClassMobile(ticketData.status)}">${ticketData.status || 'N/A'}</span></p>`;
                        bubble.appendChild(ticketInfoBlock);
                    }
                    const messageTextSpan = document.createElement('span');
                    messageTextSpan.style.whiteSpace = 'pre-wrap';
                    messageTextSpan.textContent = msgData.message;
                    bubble.appendChild(messageTextSpan);
                    wrapper.appendChild(bubble);
                    const meta = document.createElement('div');
                    meta.className = 'message-meta text-muted';
                    meta.textContent = `${senderName} ・ ${formattedTime}`;
                    wrapper.appendChild(meta);
                    chatBoxMobile.appendChild(wrapper); // Pastikan ini dijalankan
                }

                function getStatusBadgeClassMobile(status) { if (!status) return 'badge bg-info'; const ls = status.toLowerCase(); switch (ls) { case 'open': case 'baru': return 'bg-success'; case 'pending': case 'menunggu balasan': case 'diproses': return 'bg-warning text-dark'; case 'closed': case 'selesai': return 'bg-secondary'; default: return 'bg-info'; } }

                if (typeof Pusher !== 'undefined' && loggedInUserIdMobile && csrfTokenMobile && '{{ env('PUSHER_APP_KEY') }}') {
                    Pusher.logToConsole = false;
                    const pusherMobile = new Pusher('{{ env('PUSHER_APP_KEY') }}', { cluster: '{{ env('PUSHER_APP_CLUSTER') }}', forceTLS: (('{{ env('PUSHER_SCHEME') }}' || 'https') === 'https'), authEndpoint: '/broadcasting/auth', auth: { headers: { 'X-CSRF-TOKEN': csrfTokenMobile } } });
                    const channelNameMobile = `private-conversation.${loggedInUserIdMobile}`;
                    const channelMobile = pusherMobile.subscribe(channelNameMobile);
                    channelMobile.bind('new-message', function (eventData) { if (eventData.message && eventData.message.sender_id !== loggedInUserIdMobile) { addMessageToBoxMobile(eventData.message, eventData.sender_data); scrollToBottomMobile(); } });
                }

                function showInitialActionOptionsMobile() {
                    if (!actionModalBodyMobile || !actionModalTitleMobile) return;
                    actionModalTitleMobile.textContent = 'Pilih Aksi';
                    actionModalBodyMobile.innerHTML = `
                                                <div class="action-option" data-action="attach-ticket-mobile">
                                                    <i class="fas fa-paperclip"></i> <span>Lampirkan Tiket</span>
                                                </div>
                                                <div class="action-option danger" data-action="clear-chat-confirm-mobile">
                                                    <i class="fas fa-trash-alt"></i> <span>Bersihkan Chat</span>
                                                </div>
                                            `;
                    if (actionModalFooterMobile) actionModalFooterMobile.style.display = 'flex';
                    if (actionModalCloseButtonMobile) actionModalCloseButtonMobile.textContent = "Tutup";
                }

                function showAttachTicketOptionsMobile() {
                    if (!actionModalBodyMobile || !actionModalTitleMobile) return;
                    actionModalTitleMobile.textContent = 'Pilih Tiket untuk Dilampirkan';
                    let ticketOptionsHTML = '<div class="list-group list-group-flush">';
                    @if(!empty($userTickets) && count($userTickets) > 0)
                        @foreach($userTickets as $ticket)
                            ticketOptionsHTML += `<a href="#" class="list-group-item list-group-item-action ticket-item-in-modal-mobile" data-ticket-id="{{ $ticket->id }}" data-ticket-subject="{{ Str::limit($ticket->subject ?? 'Tanpa Subjek', 35) }}"><div class="d-flex w-100 justify-content-between"><h6 class="mb-1 ticket-subject">#{{ $ticket->id }} - {{ Str::limit($ticket->subject ?? 'Tanpa Subjek', 35) }}</h6><small class="text-muted">{{ $ticket->created_at->diffForHumans() }}</small></div><p class="mb-0 ticket-details">Status: <span class="badge {{ match (strtolower($ticket->status ?? 'unknown')) { 'open' => 'bg-success', 'baru' => 'bg-success', 'pending' => 'bg-warning text-dark', 'menunggu balasan' => 'bg-warning text-dark', 'diproses' => 'bg-warning text-dark', 'closed' => 'bg-secondary', 'selesai' => 'bg-secondary', default => 'bg-info'} }}">{{ $ticket->status ?? 'N/A' }}</span></p></a>`;
                        @endforeach
                    @else
                        ticketOptionsHTML += '<p class="text-center text-muted p-3">Tidak ada tiket yang tersedia.</p>';
                    @endif
                    ticketOptionsHTML += '</div>';
                    actionModalBodyMobile.innerHTML = ticketOptionsHTML;
                    if (actionModalFooterMobile) { actionModalFooterMobile.style.display = 'flex'; if (actionModalCloseButtonMobile) actionModalCloseButtonMobile.textContent = "Batal"; }
                }

                function showClearChatConfirmationMobile() {
                    if (!actionModalBodyMobile || !actionModalTitleMobile) return;
                    actionModalTitleMobile.textContent = 'Konfirmasi Hapus Chat';
                    actionModalBodyMobile.innerHTML = `<p class="p-3 text-center">Anda yakin ingin membersihkan semua percakapan ini? Aksi ini tidak dapat diurungkan.</p><div class="d-grid gap-2 p-3"><button type="button" class="btn btn-danger" id="confirm-delete-chat-btn-mobile">Tetap Hapus</button><button type="button" class="btn btn-outline-secondary" data-action="back-to-options-mobile">Batal</button></div>`;
                    if (actionModalFooterMobile) actionModalFooterMobile.style.display = 'none';
                }

                if (actionModalMobileElement) {
                    actionModalMobileElement.addEventListener('show.bs.modal', function () { showInitialActionOptionsMobile(); });
                    actionModalBodyMobile.addEventListener('click', function (event) {
                        const target = event.target.closest('[data-action], .ticket-item-in-modal-mobile');
                        if (!target) return;
                        const action = target.dataset.action;

                        if (action === 'attach-ticket-mobile') showAttachTicketOptionsMobile();
                        else if (action === 'clear-chat-confirm-mobile') showClearChatConfirmationMobile();
                        else if (action === 'back-to-options-mobile') showInitialActionOptionsMobile();
                        else if (target.classList.contains('ticket-item-in-modal-mobile')) {
                            const ticketId = target.dataset.ticketId; const ticketSubject = target.dataset.ticketSubject;
                            if (selectedTicketIdInputMobile && attachedTicketInfoDivMobile && attachedTicketTextSpanMobile) {
                                selectedTicketIdInputMobile.value = ticketId; attachedTicketTextSpanMobile.textContent = `Tiket #${ticketId}: ${ticketSubject}`; attachedTicketInfoDivMobile.style.display = 'flex';
                            }
                            if (actionModalInstanceMobile) actionModalInstanceMobile.hide();
                            if (messageInputMobile) messageInputMobile.focus();
                        }
                    });
                    actionModalBodyMobile.addEventListener('click', function (event) {
                        if (event.target.id === 'confirm-delete-chat-btn-mobile') {
                            if (!csrfTokenMobile) { alert('Sesi tidak valid.'); if (actionModalInstanceMobile) actionModalInstanceMobile.hide(); return; }
                            event.target.disabled = true; event.target.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';
                            fetch("{{ route('delete.chat') }}", { method: 'GET', headers: { 'X-CSRF-TOKEN': csrfTokenMobile, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, })
                                .then(response => response.json()).then(data => {
                                    if (data.success) { alert('Percakapan berhasil dibersihkan.'); if (chatBoxMobile) chatBoxMobile.innerHTML = '<p class="no-conversation-message" id="initial-no-message-mobile">Percakapan telah dibersihkan.</p>'; }
                                    else { alert(data.message || 'Gagal membersihkan chat.'); }
                                }).catch(error => { console.error('Error clearing chat (Mobile):', error); alert('Terjadi kesalahan: ' + error.message); })
                                .finally(() => { if (actionModalInstanceMobile) actionModalInstanceMobile.hide(); });
                        }
                    });
                }
            });
        </script>
    </div>

    {{-- ======================= TAMPILAN DESKTOP ======================= --}}
    <div id="divMessageDesktop" style="display: none;">
        <div class="pc-container">
            <div class="pc-content">
                <style>
                    /* ... (CSS Desktop yang sudah ada) ... */
                    #divMessageDesktop {
                        height: calc(100vh - 70px);
                        display: flex;
                        flex-direction: column;
                        background-color: #f8f9fa;
                        padding: 20px;
                        border-radius: 8px;
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
                        margin: 20px;
                        position: relative;
                    }

                    #divMessageDesktop .desktop-chat-header {
                        padding-bottom: 15px;
                        margin-bottom: 15px;
                        border-bottom: 1px solid #e9ecef;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                    }

                    #divMessageDesktop .desktop-chat-header h5 {
                        font-size: 1.5rem;
                        color: #343a40;
                        margin-bottom: 0;
                    }

                    #divMessageDesktop .desktop-chat-header .back-button-desktop {
                        font-size: 1.2rem;
                        color: #0EA2BC;
                        text-decoration: none;
                    }

                    #divMessageDesktop .desktop-chat-header .back-button-desktop:hover {
                        color: #0a889c;
                    }

                    #divMessageDesktop .chat-box-desktop-content {
                        flex-grow: 1;
                        overflow-y: auto;
                        padding: 15px;
                        background-color: #ffffff;
                        border: 1px solid #dee2e6;
                        border-radius: 6px;
                        margin-bottom: 15px;
                    }

                    #divMessageDesktop .chat-box-desktop-content::-webkit-scrollbar {
                        width: 8px;
                    }

                    #divMessageDesktop .chat-box-desktop-content::-webkit-scrollbar-track {
                        background: #f1f1f1;
                        border-radius: 10px;
                    }

                    #divMessageDesktop .chat-box-desktop-content::-webkit-scrollbar-thumb {
                        background: #ccc;
                        border-radius: 10px;
                    }

                    #divMessageDesktop .chat-box-desktop-content::-webkit-scrollbar-thumb:hover {
                        background: #aaa;
                    }

                    #divMessageDesktop .message-bubble {
                        max-width: 70%;
                        padding: 10px 15px;
                        margin-bottom: 10px;
                        border-radius: 12px;
                        word-wrap: break-word;
                        font-size: 0.95rem;
                    }

                    #divMessageDesktop .message-out {
                        background-color: #0EA2BC;
                        color: white;
                        align-self: flex-end;
                        border-bottom-right-radius: 3px;
                        box-shadow: 0 2px 4px rgba(14, 162, 188, 0.2);
                    }

                    #divMessageDesktop .message-in {
                        background-color: #e9ecef;
                        color: #212529;
                        align-self: flex-start;
                        border-bottom-left-radius: 3px;
                        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
                    }

                    #divMessageDesktop .ticket-info-block {
                        border-radius: 8px;
                        font-size: 0.8em;
                        margin: 8px 0;
                        padding: 8px 12px;
                        border-left: 3px solid;
                    }

                    #divMessageDesktop .message-out .ticket-info-block {
                        background-color: rgba(255, 255, 255, 0.15);
                        border-left-color: rgba(255, 255, 255, 0.5);
                        color: #f0f0f0;
                    }

                    #divMessageDesktop .message-in .ticket-info-block {
                        background-color: #f8f9fa;
                        border-left-color: #0EA2BC;
                        color: #343a40;
                    }

                    #divMessageDesktop .message-meta {
                        font-size: 0.75rem;
                        color: #6c757d;
                        margin-top: 5px;
                    }

                    #divMessageDesktop .input-area-wrapper-desktop {
                        background-color: #ffffff;
                        border-top: 1px solid #dee2e6;
                        padding: 15px;
                        border-radius: 0 0 6px 6px;
                    }

                    #divMessageDesktop .input-area-desktop {
                        display: flex;
                        align-items: center;
                    }

                    #divMessageDesktop #attached-ticket-info-desktop {
                        font-size: 0.85rem;
                        color: #495057;
                        background-color: #e2e3e5;
                        padding: 6px 12px;
                        border-radius: 15px;
                        margin-bottom: 10px;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                    }

                    #divMessageDesktop #attached-ticket-info-desktop .text-content {
                        margin-right: 10px;
                    }

                    #divMessageDesktop #remove-attached-ticket-btn-desktop {
                        color: #c82333;
                        background: none;
                        border: none;
                        cursor: pointer;
                    }

                    #divMessageDesktop #remove-attached-ticket-btn-desktop i {
                        font-size: 1rem;
                    }

                    #divMessageDesktop #action-button-desktop {
                        background-color: #6c757d;
                        border: 1px solid #6c757d;
                        color: white;
                        padding: 9px 15px;
                        font-size: 0.9rem;
                        font-weight: 500;
                        cursor: pointer;
                        margin-right: 10px;
                        border-radius: 0.25rem;
                        transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
                    }

                    #divMessageDesktop #action-button-desktop:hover {
                        background-color: #5a6268;
                        border-color: #545b62;
                    }

                    #divMessageDesktop #action-button-desktop i {
                        margin-right: 5px;
                    }

                    #divMessageDesktop .message-input-desktop {
                        flex-grow: 1;
                        border: 1px solid #ced4da;
                        border-radius: 0.25rem;
                        padding: 10px 15px;
                        font-size: 0.95rem;
                        margin-right: 10px;
                        box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
                    }

                    #divMessageDesktop .message-input-desktop:focus {
                        border-color: #86b7fe;
                        outline: 0;
                        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, .25);
                    }

                    #divMessageDesktop .send-button-desktop {
                        background-color: #0EA2BC;
                        color: white;
                        border: none;
                        border-radius: 0.25rem;
                        padding: 10px 20px;
                        font-size: 0.95rem;
                        cursor: pointer;
                        transition: background-color 0.15s ease-in-out;
                    }

                    #divMessageDesktop .send-button-desktop:hover {
                        background-color: #0c8a9e;
                    }

                    #divMessageDesktop .send-button-desktop i {
                        margin-right: 5px;
                    }

                    #divMessageDesktop .no-conversation-message {
                        text-align: center;
                        color: #6c757d;
                        padding: 50px 20px;
                        font-size: 1.1rem;
                    }

                    #actionModalDesktop .list-group-item-action {
                        border-radius: 0;
                    }

                    #actionModalDesktop .ticket-subject {
                        color: #007bff;
                    }

                    #actionModalDesktop .ticket-details .badge {
                        font-weight: 500;
                    }
                </style>

                <div class="desktop-chat-container">
                    <div class="desktop-chat-header">
                        <a href="{{ url()->previous() }}" class="back-button-desktop">
                            <i class="ti ti-arrow-left"></i> Kembali
                        </a>
                        <h5>Chat dengan Admin</h5>
                    </div>

                    <div class="chat-box-desktop-content" id="chat-box-desktop">
                        @php $messageExistsDesktop = false; @endphp
                        @forelse ($messages as $message)
                            @php
                                $messageExistsDesktop = true;
                                $isOut = $message->sender_id === $userId;
                                $bubbleStylingClass = $isOut ? 'message-out' : 'message-in';
                                $senderName = $isOut ? 'Anda' : ($message->sender->name ?? 'Admin');
                            @endphp
                            <div class="d-flex flex-column {{ $isOut ? 'align-items-end' : 'align-items-start' }}">
                                <div class="message-bubble {{ $bubbleStylingClass }}">
                                    @if ($message->ticket)
                                        <div class="ticket-info-block">
                                            <p class="fw-bold mb-1">Tiket Terlampir:</p>
                                            <p><strong>ID:</strong> #{{ $message->ticket->id }}</p>
                                            <p><strong>Subjek:</strong> {{ $message->ticket->subject ?? 'N/A' }}</p>
                                            <p class="mb-0">
                                                <strong>Status:</strong>
                                                @php
                                                    $status = strtolower($message->ticket->status ?? 'unknown');
                                                    $statusClass = match ($status) {
                                                        'open', 'baru' => 'badge bg-success',
                                                        'pending', 'menunggu balasan', 'diproses' => 'badge bg-warning text-dark',
                                                        'closed', 'selesai' => 'badge bg-secondary',
                                                        default => 'badge bg-info',
                                                    };
                                                @endphp
                                                <span class="{{ $statusClass }}">{{ $message->ticket->status ?? 'N/A' }}</span>
                                            </p>
                                        </div>
                                    @endif
                                    <span style="white-space: pre-wrap;">{{ $message->message }}</span>
                                </div>
                                <div class="message-meta text-muted">
                                    {{ $senderName }} ・ {{ $message->created_at->format('d M Y, H:i A') }}
                                </div>
                            </div>
                        @empty
                            <p class="no-conversation-message" id="initial-no-message-desktop">Belum ada percakapan. Mulai ketik
                                pesan di bawah!</p>
                        @endforelse
                    </div>

                    <div class="input-area-wrapper-desktop">
                        <div id="attached-ticket-info-desktop" style="display: none;">
                            <span class="text-content"></span>
                            <button type="button" id="remove-attached-ticket-btn-desktop" title="Hapus Lampiran">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </div>
                        <div class="input-area-desktop">
                            <button type="button" id="action-button-desktop" title="Aksi Lainnya" data-bs-toggle="modal"
                                data-bs-target="#actionModalDesktop">
                                <i class="fas fa-cogs"></i> Action
                            </button>
                            <input type="hidden" name="ticket_id_desktop" id="selected_ticket_id_desktop" value="">
                            <input type="text" name="message_desktop" id="message-input-desktop"
                                class="message-input-desktop" placeholder="Ketik pesan Anda di sini..." autocomplete="off">
                            <button id="send-button-desktop" class="send-button-desktop">
                                <i class="fas fa-paper-plane"></i> Kirim
                            </button>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="actionModalDesktop" tabindex="-1" aria-labelledby="actionModalLabelDesktop"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="actionModalTitleDesktop">Pilih Aksi</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-0" id="actionModalBodyDesktop">
                                {{-- Konten diisi oleh JS --}}
                            </div>
                            <div class="modal-footer" id="actionModalFooterDesktop" style="display: flex;">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                                    id="actionModalCloseButtonDesktop">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const chatBoxDesktop = document.getElementById('chat-box-desktop');
                        const messageInputDesktop = document.getElementById('message-input-desktop');
                        const sendButtonDesktop = document.getElementById('send-button-desktop');
                        const actionButtonDesktop = document.getElementById('action-button-desktop');
                        const actionModalDesktopElement = document.getElementById('actionModalDesktop');
                        const actionModalTitleDesktop = document.getElementById('actionModalTitleDesktop');
                        const actionModalBodyDesktop = document.getElementById('actionModalBodyDesktop');
                        const actionModalFooterDesktop = document.getElementById('actionModalFooterDesktop');
                        const actionModalCloseButtonDesktop = document.getElementById('actionModalCloseButtonDesktop');

                        const selectedTicketIdInputDesktop = document.getElementById('selected_ticket_id_desktop');
                        const attachedTicketInfoDivDesktop = document.getElementById('attached-ticket-info-desktop');
                        const attachedTicketTextSpanDesktop = attachedTicketInfoDivDesktop ? attachedTicketInfoDivDesktop.querySelector('.text-content') : null;
                        const removeAttachedTicketButtonDesktop = document.getElementById('remove-attached-ticket-btn-desktop');

                        let actionModalInstanceDesktop = null;
                        if (actionModalDesktopElement) {
                            actionModalInstanceDesktop = new bootstrap.Modal(actionModalDesktopElement);
                        }

                        const loggedInUserIdDesktop = {{ Auth::id() ?? 'null' }};
                        const initialNoMessageDesktop = document.getElementById('initial-no-message-desktop');
                        const csrfTokenDesktop = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                        function scrollToBottomDesktop() { setTimeout(() => { if (chatBoxDesktop) chatBoxDesktop.scrollTop = chatBoxDesktop.scrollHeight; }, 100); }
                        function scrollToBottomDesktopAfterLoad() { if (chatBoxDesktop) chatBoxDesktop.scrollTop = chatBoxDesktop.scrollHeight; }
                        // Pindahkan ini ke luar event DOMContentLoaded utama jika ada duplikasi, atau pastikan hanya satu yang berjalan.
                        // Untuk sekarang, kita biarkan agar lebih mudah dibaca per blok.
                        setTimeout(function () { scrollToBottomDesktopAfterLoad(); }, 200);


                        if (removeAttachedTicketButtonDesktop && selectedTicketIdInputDesktop && attachedTicketInfoDivDesktop && attachedTicketTextSpanDesktop) {
                            removeAttachedTicketButtonDesktop.addEventListener('click', function () {
                                selectedTicketIdInputDesktop.value = '';
                                attachedTicketTextSpanDesktop.textContent = '';
                                attachedTicketInfoDivDesktop.style.display = 'none';
                                if (messageInputDesktop) messageInputDesktop.focus();
                            });
                        }

                        if (sendButtonDesktop) sendButtonDesktop.addEventListener('click', sendMessageDesktop);
                        if (messageInputDesktop) messageInputDesktop.addEventListener('keypress', function (e) { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessageDesktop(); } });

                        function sendMessageDesktop() {
                            const messageText = messageInputDesktop.value.trim();
                            const attachedTicketId = selectedTicketIdInputDesktop ? selectedTicketIdInputDesktop.value : '';

                            if (messageText === '' && attachedTicketId === '') { if (messageInputDesktop) messageInputDesktop.focus(); return; }
                            if (!csrfTokenDesktop) { console.error('CSRF token not found!'); alert('Gagal mengirim pesan: Sesi tidak valid.'); return; }

                            const formData = new FormData();
                            formData.append('message', messageText);
                            formData.append('_token', csrfTokenDesktop);
                            if (attachedTicketId) formData.append('ticket_id', attachedTicketId);

                            if (messageInputDesktop) messageInputDesktop.disabled = true;
                            let originalButtonText = ''; // Simpan ikon asli
                            if (sendButtonDesktop) {
                                sendButtonDesktop.disabled = true;
                                originalButtonText = sendButtonDesktop.innerHTML;
                                sendButtonDesktop.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
                            }

                            fetch('{{ route('panel.chat.send') }}', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, body: formData })
                                .then(response => {
                                    if (!response.ok) { return response.json().then(errData => { throw new Error(errData.message || `HTTP error ${response.status}`); }).catch(() => { throw new Error(`HTTP error ${response.status}`); }); }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.success && data.message) {
                                        addMessageToBoxDesktop(data.message); // Panggil ini untuk menampilkan pesan
                                        if (messageInputDesktop) messageInputDesktop.value = '';
                                        if (selectedTicketIdInputDesktop) selectedTicketIdInputDesktop.value = '';
                                        if (attachedTicketInfoDivDesktop) attachedTicketInfoDivDesktop.style.display = 'none';
                                        if (attachedTicketTextSpanDesktop) attachedTicketTextSpanDesktop.textContent = '';
                                        scrollToBottomDesktop(); // Pastikan scroll setelah pesan ditambahkan
                                    } else { alert(data.message || 'Gagal mengirim pesan. Respons tidak sukses.'); }
                                })
                                .catch(error => { console.error('Error sending message (Desktop):', error); alert('Terjadi kesalahan saat mengirim pesan: ' + error.message); })
                                .finally(() => {
                                    if (messageInputDesktop) messageInputDesktop.disabled = false;
                                    if (sendButtonDesktop) { sendButtonDesktop.disabled = false; sendButtonDesktop.innerHTML = originalButtonText; }
                                    if (messageInputDesktop) messageInputDesktop.focus();
                                });
                        }

                        function addMessageToBoxDesktop(msgData, senderProfileData = null) {
                            if (!chatBoxDesktop || !msgData || typeof msgData.sender_id === 'undefined') { console.error("Invalid data for addMessageToBoxDesktop:", msgData); return; }
                            const noConversationMsg = chatBoxDesktop.querySelector('.no-conversation-message');
                            if (noConversationMsg) noConversationMsg.remove();
                            const initialMsg = document.getElementById('initial-no-message-desktop');
                            if (initialMsg && chatBoxDesktop.contains(initialMsg)) initialMsg.remove();

                            const isOut = msgData.sender_id === loggedInUserIdDesktop;
                            let senderName = isOut ? 'Anda' : (senderProfileData?.profile?.name || msgData.sender?.profile?.name || msgData.sender?.name || 'Admin');
                            const formattedTime = msgData.formatted_time || new Date(msgData.created_at).toLocaleTimeString([], { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true });

                            const wrapper = document.createElement('div');
                            wrapper.className = `d-flex flex-column ${isOut ? 'align-items-end' : 'align-items-start'}`;
                            const bubble = document.createElement('div');
                            bubble.className = `message-bubble ${isOut ? 'message-out' : 'message-in'}`;

                            const ticketData = msgData.ticket || msgData.ticket_details;
                            if (ticketData && ticketData.id) {
                                const ticketInfoBlock = document.createElement('div');
                                ticketInfoBlock.className = `ticket-info-block`;
                                ticketInfoBlock.innerHTML = `<p class="fw-bold mb-1">Tiket Terlampir:</p><p><strong>ID:</strong> #${ticketData.id}</p><p><strong>Subjek:</strong> ${ticketData.subject || 'N/A'}</p><p class="mb-0"><strong>Status:</strong> <span class="${getStatusBadgeClassDesktop(ticketData.status)}">${ticketData.status || 'N/A'}</span></p>`;
                                bubble.appendChild(ticketInfoBlock);
                            }
                            const messageTextSpan = document.createElement('span');
                            messageTextSpan.style.whiteSpace = 'pre-wrap';
                            messageTextSpan.textContent = msgData.message;
                            bubble.appendChild(messageTextSpan);
                            wrapper.appendChild(bubble);
                            const meta = document.createElement('div');
                            meta.className = 'message-meta text-muted';
                            meta.textContent = `${senderName} ・ ${formattedTime}`;
                            wrapper.appendChild(meta);
                            chatBoxDesktop.appendChild(wrapper); // Pastikan ini dijalankan
                        }

                        function getStatusBadgeClassDesktop(status) { if (!status) return 'badge bg-info'; const ls = status.toLowerCase(); switch (ls) { case 'open': case 'baru': return 'bg-success'; case 'pending': case 'menunggu balasan': case 'diproses': return 'bg-warning text-dark'; case 'closed': case 'selesai': return 'bg-secondary'; default: return 'bg-info'; } }

                        if (typeof Pusher !== 'undefined' && loggedInUserIdDesktop && csrfTokenDesktop && '{{ env('PUSHER_APP_KEY') }}') {
                            Pusher.logToConsole = false;
                            const pusherDesktop = new Pusher('{{ env('PUSHER_APP_KEY') }}', { cluster: '{{ env('PUSHER_APP_CLUSTER') }}', forceTLS: (('{{ env('PUSHER_SCHEME') }}' || 'https') === 'https'), authEndpoint: '/broadcasting/auth', auth: { headers: { 'X-CSRF-TOKEN': csrfTokenDesktop } } });
                            const channelNameDesktop = `private-conversation.${loggedInUserIdDesktop}`;
                            const channelDesktop = pusherDesktop.subscribe(channelNameDesktop);
                            channelDesktop.bind('new-message', function (eventData) { if (eventData.message && eventData.message.sender_id !== loggedInUserIdDesktop) { addMessageToBoxDesktop(eventData.message, eventData.sender_data); scrollToBottomDesktop(); } });
                        }


                        function showInitialActionOptionsDesktop() {
                            if (!actionModalBodyDesktop || !actionModalTitleDesktop) return;
                            actionModalTitleDesktop.textContent = 'Pilih Aksi';
                            actionModalBodyDesktop.innerHTML = `
                                                        <div class="action-option" data-action="attach-ticket-desktop">
                                                            <i class="fas fa-paperclip"></i> <span>Lampirkan Tiket</span>
                                                        </div>
                                                        <div class="action-option danger" data-action="clear-chat-confirm-desktop">
                                                            <i class="fas fa-trash-alt"></i> <span>Bersihkan Chat</span>
                                                        </div>
                                                    `;
                            if (actionModalFooterDesktop) actionModalFooterDesktop.style.display = 'flex';
                            if (actionModalCloseButtonDesktop) actionModalCloseButtonDesktop.textContent = "Tutup";
                        }

                        function showAttachTicketOptionsDesktop() {
                            if (!actionModalBodyDesktop || !actionModalTitleDesktop) return;
                            actionModalTitleDesktop.textContent = 'Pilih Tiket untuk Dilampirkan';
                            let ticketOptionsHTML = '<div class="list-group list-group-flush">';
                            @if(!empty($userTickets) && count($userTickets) > 0)
                                @foreach($userTickets as $ticket)
                                    ticketOptionsHTML += `<a href="#" class="list-group-item list-group-item-action ticket-item-in-modal-desktop" data-ticket-id="{{ $ticket->id }}" data-ticket-subject="{{ Str::limit($ticket->subject ?? 'Tanpa Subjek', 50) }}"><div class="d-flex w-100 justify-content-between"><h6 class="mb-1 ticket-subject">#{{ $ticket->id }} - {{ Str::limit($ticket->subject ?? 'Tanpa Subjek', 50) }}</h6><small class="text-muted">{{ $ticket->created_at->format('d M Y H:i') }}</small></div><p class="mb-0 ticket-details">Status: <span class="badge {{ match (strtolower($ticket->status ?? 'unknown')) { 'open' => 'bg-success', 'baru' => 'bg-success', 'pending' => 'bg-warning text-dark', 'menunggu balasan' => 'bg-warning text-dark', 'diproses' => 'bg-warning text-dark', 'closed' => 'bg-secondary', 'selesai' => 'bg-secondary', default => 'bg-info'} }}">{{ $ticket->status ?? 'N/A' }}</span></p></a>`;
                                @endforeach
                            @else
                                ticketOptionsHTML += '<p class="text-center text-muted p-3">Tidak ada tiket yang tersedia.</p>';
                            @endif
                            ticketOptionsHTML += '</div>';
                            actionModalBodyDesktop.innerHTML = ticketOptionsHTML;
                            if (actionModalFooterDesktop) { actionModalFooterDesktop.style.display = 'flex'; if (actionModalCloseButtonDesktop) actionModalCloseButtonDesktop.textContent = "Batal"; }
                        }

                        function showClearChatConfirmationDesktop() {
                            if (!actionModalBodyDesktop || !actionModalTitleDesktop) return;
                            actionModalTitleDesktop.textContent = 'Konfirmasi Hapus Chat';
                            actionModalBodyDesktop.innerHTML = `<p class="p-3 text-center">Anda yakin ingin membersihkan semua percakapan ini? Aksi ini tidak dapat diurungkan.</p><div class="d-flex justify-content-center gap-2 p-3"><button type="button" class="btn btn-outline-secondary" data-action="back-to-options-desktop">Batal</button><button type="button" class="btn btn-danger" id="confirm-delete-chat-btn-desktop">Tetap Hapus</button></div>`;
                            if (actionModalFooterDesktop) actionModalFooterDesktop.style.display = 'none';
                        }

                        if (actionModalDesktopElement) {
                            actionModalDesktopElement.addEventListener('show.bs.modal', function () { showInitialActionOptionsDesktop(); });
                            actionModalBodyDesktop.addEventListener('click', function (event) {
                                const target = event.target.closest('[data-action], .ticket-item-in-modal-desktop');
                                if (!target) return;
                                const action = target.dataset.action;

                                if (action === 'attach-ticket-desktop') showAttachTicketOptionsDesktop();
                                else if (action === 'clear-chat-confirm-desktop') showClearChatConfirmationDesktop();
                                else if (action === 'back-to-options-desktop') showInitialActionOptionsDesktop();
                                else if (target.classList.contains('ticket-item-in-modal-desktop')) {
                                    const ticketId = target.dataset.ticketId; const ticketSubject = target.dataset.ticketSubject;
                                    if (selectedTicketIdInputDesktop && attachedTicketInfoDivDesktop && attachedTicketTextSpanDesktop) {
                                        selectedTicketIdInputDesktop.value = ticketId; attachedTicketTextSpanDesktop.textContent = `Tiket Terlampir: #${ticketId} (${ticketSubject})`; attachedTicketInfoDivDesktop.style.display = 'flex';
                                    }
                                    if (actionModalInstanceDesktop) actionModalInstanceDesktop.hide();
                                    if (messageInputDesktop) messageInputDesktop.focus();
                                }
                            });
                            actionModalBodyDesktop.addEventListener('click', function (event) {
                                if (event.target.id === 'confirm-delete-chat-btn-desktop') {
                                    if (!csrfTokenDesktop) { alert('Sesi tidak valid.'); if (actionModalInstanceDesktop) actionModalInstanceDesktop.hide(); return; }
                                    event.target.disabled = true; event.target.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';
                                    fetch("{{ route('delete.chat') }}", { method: 'GET', headers: { 'X-CSRF-TOKEN': csrfTokenDesktop, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, })
                                        .then(response => response.json()).then(data => {
                                            if (data.success) { alert('Percakapan berhasil dibersihkan.'); if (chatBoxDesktop) chatBoxDesktop.innerHTML = '<p class="no-conversation-message" id="initial-no-message-desktop">Percakapan telah dibersihkan.</p>'; }
                                            else { alert(data.message || 'Gagal membersihkan chat.'); }
                                        }).catch(error => { console.error('Error clearing chat (Desktop):', error); alert('Terjadi kesalahan: ' + error.message); })
                                        .finally(() => { if (actionModalInstanceDesktop) actionModalInstanceDesktop.hide(); });
                                }
                            });
                        }
                    });
                </script>
            </div>
        </div>
    </div>
@endsection