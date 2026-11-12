<!-- Floating Action Button -->
<div id="chat-widget-btn" onclick="toggleChatPopup()" style="position: fixed; bottom: 24px; right: 24px; z-index: 1040; cursor: pointer;">
    <div class="rounded-circle shadow-lg overflow-hidden border border-2 border-white" style="width: 60px; height: 60px; background: #111;">
        <img src="{{ asset('images/ask_jive_avatar.png') }}" alt="Ask Jive" class="w-100 h-100 object-fit-cover" style="transform: scaleX(-1);">
    </div>
</div>

<!-- Floating Chat Drawer Popup -->
<div id="chat-popup" class="card shadow-lg border-0 d-none" style="position: fixed; bottom: 95px; right: 24px; width: 380px; max-width: 90vw; height: 530px; z-index: 1045; border-radius: 16px; overflow: hidden;">
    <!-- Popup Header -->
    <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle overflow-hidden bg-primary" style="width: 36px; height: 36px;">
                <img src="{{ asset('images/ask_jive_avatar.png') }}" alt="Jive Avatar" class="w-100 h-100 object-fit-cover" style="transform: scaleX(-1);">
            </div>
            <div>
                <h6 class="mb-0 fw-bold" style="font-family: 'Recoleta-Bold', serif;">Ask Jive</h6>
                <small class="text-white-50" style="font-size: 11px;">Powered by Nemotron AI</small>
            </div>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <!-- Expand Button to Dedicated Full Page -->
            <a href="{{ route('chat.page') }}" class="btn btn-sm btn-outline-light border-0 p-1" title="Expand to Full Page">
                <i class="bi bi-arrows-angle-expand"></i>
            </a>
            <button type="button" class="btn-close btn-close-white" onclick="toggleChatPopup()"></button>
        </div>
    </div>

    <!-- Popup Messages Body -->
    <div class="card-body p-3 overflow-y-auto" id="popup-chat-body" style="background: #f8f9fa;">
        <div class="d-flex mb-3 gap-2">
            <div class="rounded-circle overflow-hidden flex-shrink-0" style="width: 32px; height: 32px;">
                <img src="{{ asset('images/ask_jive_avatar.png') }}" alt="Jive Avatar" class="w-100 h-100 object-fit-cover" style="transform: scaleX(-1);">
            </div>
            <div class="bg-white p-3 rounded-3 shadow-sm border" style="max-width: 82%;">
                <p class="mb-0 small text-dark">Hi! I'm Jive. How can I help you with your pasalubong orders today?</p>
            </div>
        </div>
    </div>

    <!-- Popup Input Footer -->
    <div class="card-footer bg-white p-2 border-top">
        <form id="popup-chat-form" onsubmit="handlePopupSend(event)">
            <div class="input-group">
                <input type="text" id="popup-chat-input" class="form-control border-0 bg-light" placeholder="Type your question..." autocomplete="off">
                <button class="btn btn-primary fw-bold" type="submit">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let popupHistory = [];
    const avatarUrl = "{{ asset('images/ask_jive_avatar.png') }}";

    function toggleChatPopup() {
        const popup = document.getElementById('chat-popup');
        popup.classList.toggle('d-none');
    }

    function handlePopupSend(e) {
        e.preventDefault();
        const input = document.getElementById('popup-chat-input');
        const message = input.value.trim();
        if (!message) return;

        appendPopupMessage('user', message);
        input.value = '';

        popupHistory.push({ role: 'user', content: message });

        fetch("{{ route('chat.send') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ messages: popupHistory })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                appendPopupMessage('assistant', data.reply);
                popupHistory.push({ role: 'assistant', content: data.reply });
            }
        });
    }

    function appendPopupMessage(role, text) {
        const body = document.getElementById('popup-chat-body');
        const isUser = role === 'user';
        
        const avatarHtml = `
            <div class="rounded-circle overflow-hidden flex-shrink-0" style="width: 32px; height: 32px;">
                <img src="${avatarUrl}" alt="Jive Avatar" class="w-100 h-100 object-fit-cover" style="transform: scaleX(-1);">
            </div>
        `;

        const msgHtml = `
            <div class="d-flex mb-3 gap-2 ${isUser ? 'justify-content-end' : ''}">
                ${!isUser ? avatarHtml : ''}
                <div class="${isUser ? 'bg-primary text-white' : 'bg-white text-dark'} p-3 rounded-3 shadow-sm border" style="max-width: 82%;">
                    <p class="mb-0 small">${text}</p>
                </div>
            </div>
        `;
        body.insertAdjacentHTML('beforeend', msgHtml);
        body.scrollTop = body.scrollHeight;
    }
</script>