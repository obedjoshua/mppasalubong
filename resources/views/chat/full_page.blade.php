@extends('layouts.app')

@section('content')
<style>
    /* Custom Fonts */
    @font-face {
        font-family: 'Recoleta-Bold';
        src: url('/fonts/Recoleta-Bold.woff2') format('woff2');
        font-weight: bold;
    }
    @font-face {
        font-family: 'modern-bold';
        src: url('/fonts/modern-bold.woff2') format('woff2');
        font-weight: bold;
    }

    .ask-jive-page {
        font-family: 'modern-bold', -apple-system, BlinkMacSystemFont, sans-serif;
        background-color: #f6f5f8;
        min-height: 100vh;
        padding-bottom: 270px;
        box-sizing: border-box;
    }
    .ask-jive-page *, .ask-jive-page *::before, .ask-jive-page *::after,
    #ask-bottom-dock-layer *, #ask-bottom-dock-layer *::before, #ask-bottom-dock-layer *::after {
        box-sizing: border-box;
    }

    /* Dark Hero Section */
    .ask-hero {
        background: #111827;
        color: #ffffff;
        padding: 40px 24px;
    }
    .ask-hero-container {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    }
    .ask-hero-title {
        font-family: 'Recoleta-Bold', serif;
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        letter-spacing: -0.5px;
        margin: 0 0 8px 0;
        color: #ffffff;
    }
    .ask-hero-sub {
        color: rgba(255, 255, 255, 0.72);
        font-size: 16px;
        line-height: 1.5;
        margin: 0 0 18px 0;
    }
    .btn-reset-chat {
        background: transparent;
        color: #ffffff;
        border: 1.5px solid rgba(255, 255, 255, 0.65);
        border-radius: 999px;
        padding: 9px 20px;
        font-family: 'modern-bold', sans-serif;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-reset-chat:hover {
        background: #ffffff;
        color: #111827;
    }

    /* Hero Banner Avatar */
    .hero-avatar-wrap {
        width: 140px;
        height: 140px;
        min-width: 140px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid #ffffff;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.35);
    }
    .hero-avatar-wrap img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover;
        transform: scaleX(-1);
        display: block;
    }
    @media (max-width: 640px) {
        .hero-avatar-wrap { display: none; }
    }

    /* Smooth Appearing Message Animation */
    @keyframes msgSlideFadeIn {
        0% {
            opacity: 0;
            transform: translateY(18px) scale(0.975);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Skeleton Shimmer Animation */
    @keyframes jiveSkeletonShimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }

    .jive-skeleton-wrap {
        display: flex;
        flex-direction: column;
        gap: 10px;
        min-width: 240px;
        max-width: 420px;
        padding: 4px 0;
    }
    .jive-skeleton-label {
        font-size: 12px;
        color: #9ca3af;
        letter-spacing: 0.2px;
        margin-bottom: 2px;
    }
    .jive-skeleton-line {
        height: 12px;
        border-radius: 999px;
        background: linear-gradient(90deg, rgba(255,255,255,0.08) 25%, rgba(255,255,255,0.22) 50%, rgba(255,255,255,0.08) 75%);
        background-size: 200% 100%;
        animation: jiveSkeletonShimmer 1.6s infinite linear;
    }
    .jive-skeleton-line.w-90 { width: 92%; }
    .jive-skeleton-line.w-75 { width: 76%; }
    .jive-skeleton-line.w-55 { width: 54%; }

    /* Main Conversation Feed (Wide layout matching reference style) */
    .ask-chat-wrapper {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
        padding: 32px 24px;
    }
    #full-chat-container {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }
    .msg-row {
        display: flex;
        gap: 16px;
        align-items: flex-start;
        animation: msgSlideFadeIn 0.45s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        will-change: transform, opacity;
    }
    .msg-row.user {
        justify-content: flex-end;
    }
    .msg-avatar {
        width: 48px;
        height: 48px;
        min-width: 48px;
        max-width: 48px;
        border-radius: 50%;
        overflow: hidden;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.12);
        flex-shrink: 0;
    }
    .msg-avatar img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover;
        transform: scaleX(-1);
        display: block;
    }
    .chat-bubble-assistant {
        background: #1f2937;
        color: #f9fafb;
        border-radius: 18px 18px 18px 4px;
        padding: 20px 24px;
        flex: 1;
        max-width: 75%;
        font-size: 15.5px;
        line-height: 1.6;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        transform-origin: bottom left;
    }
    .chat-bubble-user {
        background: #ffffff;
        color: #111827;
        border: 1px solid #e5e7eb;
        border-radius: 18px 18px 4px 18px;
        padding: 18px 24px;
        max-width: 65%;
        font-size: 15.5px;
        line-height: 1.55;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
        transform-origin: bottom right;
    }

    /* Interactive Cards inside Dark Assistant Bubble */
    .jive-ui-card {
        background: #ffffff;
        color: #111827;
        border-radius: 14px;
        padding: 18px;
        margin-top: 14px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        border: 1.5px solid #e5e7eb;
        animation: msgSlideFadeIn 0.45s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }
    .jive-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 6px;
    }
    .jive-card-title {
        font-family: 'Recoleta-Bold', serif;
        font-size: 17px;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }
    .jive-badge-price {
        background: #dcfce7;
        color: #15803d;
        font-size: 13px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 999px;
        white-space: nowrap;
    }
    .jive-badge-status {
        background: #fef3c7;
        color: #b45309;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 999px;
        white-space: nowrap;
    }
    .jive-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        margin-top: 12px;
        padding-top: 10px;
        border-top: 1px solid #f1f5f9;
    }
    .btn-card-dark {
        background: #111827;
        color: #ffffff;
        text-decoration: none;
        padding: 7px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        display: inline-block;
    }
    .btn-card-dark:hover {
        background: #7C3AED;
    }
    .btn-card-outline {
        background: #f3e8ff;
        color: #7C3AED;
        border: 1px solid #7C3AED;
        padding: 7px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    /* =====================================================================
       TOP-LEVEL BODY-MOUNTED FIXED BOTTOM DOCK
       ===================================================================== */
    #ask-bottom-dock-layer {
        position: fixed !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        width: 100vw !important;
        z-index: 2147483647 !important;
        background: transparent !important;
        box-shadow: none !important;
        pointer-events: none;
    }

    /* Top Row: 4 Suggested Prompt Cards */
    .prompt-chips-section {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
        padding: 10px 24px 14px;
        background: transparent !important;
        pointer-events: auto;
        opacity: 1;
        transform: translateY(0);
        transition: opacity 0.28s cubic-bezier(0.22, 1, 0.36, 1), transform 0.28s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .prompt-chips-section.is-hidden {
        opacity: 0 !important;
        transform: translateY(18px) !important;
        pointer-events: none !important;
    }

    .prompt-chips-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }
    @media (max-width: 900px) {
        .prompt-chips-grid {
            display: flex;
            overflow-x: auto;
            scrollbar-width: none;
        }
        .prompt-chips-grid::-webkit-scrollbar {
            display: none;
        }
        .chip-btn {
            min-width: 220px;
            flex-shrink: 0;
        }
    }

    .chip-btn {
        background: #e6e4e0;
        border: none;
        border-radius: 12px;
        padding: 16px 20px;
        color: #18181b;
        text-align: left;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 4px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
        transition: background-color 0.15s ease, transform 0.15s ease;
    }
    .chip-btn:hover {
        background: #dcd9d3;
        transform: translateY(-2px);
    }
    .chip-btn-title {
        font-family: 'modern-bold', sans-serif;
        font-size: 14.5px;
        font-weight: 800;
        color: #111827;
        line-height: 1.3;
    }
    .chip-btn-sub {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 13px;
        font-weight: 400;
        color: #27272a;
        line-height: 1.35;
    }

    /* Full-Width Divider & Input Bar Dock */
    .ask-input-dock {
        border-top: 1px solid #d4d4d8;
        background: #f6f5f8;
        padding: 14px 24px 18px;
        pointer-events: auto;
    }
    .ask-input-container {
        width: 100%;
        max-width: 960px;
        margin: 0 auto;
    }
    .ask-input-pill {
        display: flex;
        align-items: center;
        background: #ffffff;
        border: 1px solid #d4d4d8;
        border-radius: 999px;
        padding: 6px 8px 6px 22px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
    }
    .ask-input-pill input {
        flex: 1;
        border: none;
        outline: none;
        background: transparent;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 15px;
        color: #111827;
    }
    .ask-input-pill input::placeholder {
        color: #71717a;
    }
    .ask-submit-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: none;
        background: #00d2e6;
        color: #111827;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
        transition: transform 0.15s ease, background-color 0.15s ease;
    }
    .ask-submit-circle:hover {
        background: #00bdd0;
        transform: scale(1.04);
    }
    .ask-submit-circle:disabled {
        opacity: 0.55;
        cursor: not-allowed;
        transform: none;
    }
    .ask-disclaimer {
        text-align: center;
        margin-top: 10px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 11.5px;
        color: #3f3f46;
    }
    .ask-disclaimer span {
        text-decoration: underline;
    }
</style>

<!-- Main Page Wrapper -->
<div class="ask-jive-page">
    <!-- Hero Header -->
    <div class="ask-hero">
        <div class="ask-hero-container">
            <div>
                <h1 class="ask-hero-title">ASK JIVE ✨</h1>
                <p class="ask-hero-sub">Not sure what you want? From store products to pickup tickets, ask virtual Jive anything!</p>
                <button type="button" onclick="clearFullChat()" class="btn-reset-chat">Start a new conversation</button>
            </div>
            <div class="hero-avatar-wrap">
                <img src="{{ asset('images/ask_jive_avatar.png') }}" alt="Jive Avatar">
            </div>
        </div>
    </div>

    <!-- Main Conversation Area -->
    <div class="ask-chat-wrapper">
        <div id="full-chat-container">
            <div class="msg-row">
                <div class="msg-avatar">
                    <img src="{{ asset('images/ask_jive_avatar.png') }}" alt="Jive Avatar">
                </div>
                <div class="chat-bubble-assistant">
                    <p style="margin: 0;">Hi, I'm Jive. And I'm here to help you with your pasalubong orders and tickets. How can I help?</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Separate Top-Level Fixed Bottom Dock Layer -->
<div id="ask-bottom-dock-layer">
    <!-- 4 Two-Line Prompt Cards (Hidden until chat is finished AND scrolled to bottom) -->
    <div class="prompt-chips-section is-hidden" id="prompt-chips-container">
        <div class="prompt-chips-grid">
            <button type="button" onclick="sendSuggestedPrompt('What are your store branches and opening hours?')" class="chip-btn">
                <span class="chip-btn-title">Branches &amp; store hours</span>
                <span class="chip-btn-sub">where to pick up your order?</span>
            </button>
            <button type="button" onclick="sendSuggestedPrompt('Can I place an advanced order on Sunday?')" class="chip-btn">
                <span class="chip-btn-title">Sunday &amp; advance orders,</span>
                <span class="chip-btn-sub">what do I need to know?</span>
            </button>
            <button type="button" onclick="sendSuggestedPrompt('Check my order tickets')" class="chip-btn">
                <span class="chip-btn-title">Check my order tickets</span>
                <span class="chip-btn-sub">view status, quote, or QR</span>
            </button>
            <button type="button" onclick="sendSuggestedPrompt('Show available pasalubong items in the shop')" class="chip-btn">
                <span class="chip-btn-title">Show shop products</span>
                <span class="chip-btn-sub">freshly baked pasalubong</span>
            </button>
        </div>
    </div>

    <!-- Full-Width Bordered Input Section -->
    <div class="ask-input-dock">
        <div class="ask-input-container">
            <form id="full-chat-form" onsubmit="handleFullSend(event)" style="margin: 0;">
                <div class="ask-input-pill">
                    <input type="text" id="full-chat-input" placeholder="Type your question here" autocomplete="off">
                    <button id="full-chat-submit" class="ask-submit-circle" type="submit" aria-label="Send">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                </div>
            </form>
            <div class="ask-disclaimer">
                Info may be inaccurate, do not rely on it as advice. <span>T&amp;Cs apply</span>
            </div>
        </div>
    </div>
</div>

<script>
    let fullHistory = [];
    let isChatGenerating = false;
    const MIN_SKELETON_MS = 1250;
    const avatarUrl = "{{ asset('images/ask_jive_avatar.png') }}";
    const chatSendEndpoint = "{{ \Illuminate\Support\Facades\Route::has('chat.send') ? route('chat.send') : route('chat.send_message') }}";

    function checkBottomPosition() {
        const chipsContainer = document.getElementById('prompt-chips-container');
        if (!chipsContainer) return;

        const scrollPosition = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
        const windowHeight = window.innerHeight || document.documentElement.clientHeight;
        const totalHeight = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);

        const isAtBottom = (totalHeight - (scrollPosition + windowHeight) <= 35);

        if (!isChatGenerating && isAtBottom) {
            chipsContainer.classList.remove('is-hidden');
        } else {
            chipsContainer.classList.add('is-hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const dockLayer = document.getElementById('ask-bottom-dock-layer');
        if (dockLayer && dockLayer.parentElement !== document.body) {
            document.body.appendChild(dockLayer);
        }

        window.addEventListener('scroll', checkBottomPosition, { passive: true });
        window.addEventListener('resize', checkBottomPosition, { passive: true });
        checkBottomPosition();
    });

    function scrollToVeryBottomSmooth() {
        requestAnimationFrame(() => {
            window.scrollTo({
                top: Math.max(document.body.scrollHeight, document.documentElement.scrollHeight),
                behavior: 'smooth'
            });
            setTimeout(checkBottomPosition, 450);
            setTimeout(checkBottomPosition, 850);
        });
    }

    function sendSuggestedPrompt(text) {
        if (isChatGenerating) return;
        const input = document.getElementById('full-chat-input');
        if (input) {
            input.value = text;
            document.getElementById('full-chat-form').dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
        }
    }

    function showSkeletonLoading() {
        isChatGenerating = true;
        checkBottomPosition();

        const submitBtn = document.getElementById('full-chat-submit');
        if (submitBtn) submitBtn.disabled = true;

        const container = document.getElementById('full-chat-container');
        container.insertAdjacentHTML('beforeend', `
            <div id="full-skeleton-row" class="msg-row">
                <div class="msg-avatar"><img src="${avatarUrl}" alt="Jive Avatar"></div>
                <div class="chat-bubble-assistant">
                    <div class="jive-skeleton-wrap">
                        <div class="jive-skeleton-label">Jive is checking...</div>
                        <div class="jive-skeleton-line w-90"></div>
                        <div class="jive-skeleton-line w-75"></div>
                        <div class="jive-skeleton-line w-55"></div>
                    </div>
                </div>
            </div>
        `);
        scrollToVeryBottomSmooth();
    }

    function removeSkeletonLoading() {
        const el = document.getElementById('full-skeleton-row');
        if (el) el.remove();

        isChatGenerating = false;
        const submitBtn = document.getElementById('full-chat-submit');
        if (submitBtn) submitBtn.disabled = false;
    }

    function handleFullSend(e) {
        e.preventDefault();
        if (isChatGenerating) return;

        const input = document.getElementById('full-chat-input');
        const text = input.value.trim();
        if (!text) return;

        appendFullMessage('user', text, false);
        input.value = '';

        fullHistory.push({ role: 'user', content: text });
        showSkeletonLoading();

        const startedAt = Date.now();

        fetch(chatSendEndpoint, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ messages: fullHistory })
        })
        .then(res => {
            if (!res.ok) throw new Error("HTTP error " + res.status);
            return res.json();
        })
        .then(data => {
            const elapsed = Date.now() - startedAt;
            const remainingDelay = Math.max(0, MIN_SKELETON_MS - elapsed);

            setTimeout(() => {
                removeSkeletonLoading();
                if (data.success && data.reply) {
                    appendFullMessage('assistant', data.reply, true);
                    fullHistory.push({ role: 'assistant', content: data.reply });
                }
                checkBottomPosition();
            }, remainingDelay);
        })
        .catch(err => {
            const elapsed = Date.now() - startedAt;
            const remainingDelay = Math.max(0, MIN_SKELETON_MS - elapsed);

            setTimeout(() => {
                removeSkeletonLoading();
                console.error("Ask Jive Full Chat Error:", err);
                appendFullMessage('assistant', "Sorry, I encountered a connection error. Please try again.", true);
                checkBottomPosition();
            }, remainingDelay);
        });
    }

    function parseAllCards(text) {
        let formattedText = text;

        // 1. Parse [PRODUCT_CARD]
        const productRegex = /\[PRODUCT_CARD\]([\s\S]*?)\[END_PRODUCT_CARD\]/g;
        let prodMatch;
        while ((prodMatch = productRegex.exec(text)) !== null) {
            const block = prodMatch[1];
            const name = (block.match(/Name:\s*(.*)/i) || [])[1]?.trim() || 'Shop Item';
            const price = (block.match(/Price:\s*(.*)/i) || [])[1]?.trim() || '';
            const branch = (block.match(/Branch:\s*(.*)/i) || [])[1]?.trim() || 'Salacot & San Jose';
            const desc = (block.match(/Description:\s*(.*)/i) || [])[1]?.trim() || '';
            const link = (block.match(/Link:\s*(.*)/i) || [])[1]?.trim() || '/shop';

            const cardHtml = `
                <div class="jive-ui-card">
                    <div class="jive-card-header">
                        <h5 class="jive-card-title">${name}</h5>
                        ${price ? `<span class="jive-badge-price">${price}</span>` : ''}
                    </div>
                    ${desc ? `<p style="font-size: 13px; color: #4b5563; margin: 4px 0 0;">${desc}</p>` : ''}
                    <div class="jive-card-footer">
                        <span style="font-size: 12px; color: #374151;">📍 ${branch}</span>
                        <a href="${link}" class="btn-card-dark">View in Shop Page &rarr;</a>
                    </div>
                </div>
            `;
            formattedText = formattedText.replace(prodMatch[0], cardHtml);
        }

        // 2. Parse [TICKET_CARD]
        const ticketRegex = /\[TICKET_CARD\]([\s\S]*?)\[END_TICKET_CARD\]/g;
        let tickMatch;
        while ((tickMatch = ticketRegex.exec(text)) !== null) {
            const block = tickMatch[1];
            const id = (block.match(/ID:\s*(.*)/i) || [])[1]?.trim() || '#0000';
            const subject = (block.match(/Subject:\s*(.*)/i) || [])[1]?.trim() || 'Order Ticket';
            const status = (block.match(/Status:\s*(.*)/i) || [])[1]?.trim() || 'PENDING';
            const date = (block.match(/Date:\s*(.*)/i) || [])[1]?.trim() || '';
            const link = (block.match(/Link:\s*(.*)/i) || [])[1]?.trim() || '/orders';

            const ticketHtml = `
                <div class="jive-ui-card">
                    <div class="jive-card-header">
                        <span style="color: #7C3AED; font-size: 15px; font-weight: 800;">${id}</span>
                        <span class="jive-badge-status">${status}</span>
                    </div>
                    <div style="font-size: 15px; font-weight: 700; color: #111827;">${subject}</div>
                    ${date ? `<div style="font-size: 12px; color: #6b7280; margin-top: 4px;">Scheduled: ${date}</div>` : ''}
                    <div class="jive-card-footer" style="justify-content: flex-start; gap: 10px;">
                        <a href="${link}" class="btn-card-dark">View Details ↗</a>
                        <button type="button" onclick="sendSuggestedPrompt('What is the status of ticket ${id}?')" class="btn-card-outline">Ask Jive 💬</button>
                    </div>
                </div>
            `;
            formattedText = formattedText.replace(tickMatch[0], ticketHtml);
        }

        return `<div>${formattedText}</div>`;
    }

    function appendFullMessage(role, text, shouldScroll = true) {
        const container = document.getElementById('full-chat-container');
        const isUser = role === 'user';
        const contentHtml = isUser ? `<p style="margin: 0;">${text}</p>` : parseAllCards(text);

        const html = isUser ? `
            <div class="msg-row user">
                <div class="chat-bubble-user">${contentHtml}</div>
            </div>
        ` : `
            <div class="msg-row">
                <div class="msg-avatar">
                    <img src="${avatarUrl}" alt="Jive Avatar">
                </div>
                <div class="chat-bubble-assistant">${contentHtml}</div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', html);

        if (shouldScroll) {
            scrollToVeryBottomSmooth();
        }
    }

    function clearFullChat() {
        fullHistory = [];
        isChatGenerating = false;
        const submitBtn = document.getElementById('full-chat-submit');
        if (submitBtn) submitBtn.disabled = false;

        document.getElementById('full-chat-container').innerHTML = `
            <div class="msg-row">
                <div class="msg-avatar">
                    <img src="${avatarUrl}" alt="Jive Avatar">
                </div>
                <div class="chat-bubble-assistant">
                    <p style="margin: 0;">Hi, I'm Jive. And I'm here to help you with your pasalubong orders and tickets. How can I help?</p>
                </div>
            </div>
        `;
        window.scrollTo({ top: 0, behavior: 'smooth' });
        setTimeout(checkBottomPosition, 450);
    }
</script>
@endsection