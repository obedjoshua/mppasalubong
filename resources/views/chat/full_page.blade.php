@extends('layouts.app')

@section('content')
<style>
    /* Custom Fonts */
    @font-face {
        font-family: 'Recoleta-Bold';
        src: url('/fonts/Recoleta Bold.otf') format('otf');
        font-weight: bold;
    }
    @font-face {
        font-family: 'modern-bold';
        src: url('/fonts/modern-bold.ttf') format('ttf');
        font-weight: bold;
    }

    .ask-jive-page {
        font-family: 'modern-bold', -apple-system, BlinkMacSystemFont, sans-serif;
        background-color: #f6f5f8;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        box-sizing: border-box;
    }
    .ask-jive-page *, .ask-jive-page *::before, .ask-jive-page *::after {
        box-sizing: border-box;
    }

    /* Dark Grainy Purple Gradient Hero Section */
    .ask-hero {
        position: relative;
        margin: 5px 5px 76px 5px;
        border-radius: 7px;
        background:
            radial-gradient(circle at 85% 25%, rgba(139, 92, 246, 0.34) 0%, transparent 50%),
            radial-gradient(circle at 15% 85%, rgba(88, 28, 135, 0.45) 0%, transparent 55%),
            linear-gradient(135deg, #12081f 0%, #1e1035 48%, #0f071a 100%);
        color: #ffffff;
        padding: 44px 36px;
        overflow: visible;
        box-shadow: none;
    }

    /* Subtle Film Grain Overlay */
    .ask-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 7px;
        opacity: 0.24;
        pointer-events: none;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.82' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        mix-blend-mode: overlay;
    }

    .ask-hero-container {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 1280px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    }

    .ask-hero-title {
        font-family: 'Recoleta-Bold', serif;
        font-size: clamp(2.45rem, 5vw, 3.85rem);
        font-weight: 800;
        letter-spacing: -0.02em;
        line-height: 1.05;
        margin: 0 0 14px 0;
        color: #a5f3fc;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .ask-hero-title .sparkle-icon {
        color: #fde68a;
        font-size: 0.85em;
    }

    .ask-hero-sub {
        font-family: 'modern-bold', -apple-system, BlinkMacSystemFont, sans-serif;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.92);
        font-size: 16px;
        line-height: 1.55;
        margin: 0 0 22px 0;
        max-width: 540px;
    }

    .btn-reset-chat {
        background: transparent;
        color: #ffffff;
        border: 1.5px solid rgba(255, 255, 255, 0.75);
        border-radius: 6px;
        padding: 10px 20px;
        font-family: 'modern-bold', sans-serif;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-reset-chat:hover {
        background: #ffffff;
        color: #12081f;
    }

    /* Hero Banner Avatar Shifted Downward */
    .hero-avatar-wrap {
        width: 240px;
        height: 240px;
        min-width: 240px;
        border-radius: 50%;
        overflow: hidden;
        background: linear-gradient(180deg, #fce7f3 0%, #a5f3fc 100%);
        box-shadow: none;
        position: relative;
        top: 84px;
        margin-top: -28px;
        margin-right: 12px;
        z-index: 5;
        flex-shrink: 0;
    }
    .hero-avatar-wrap img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover;
        transform: scaleX(-1);
        display: block;
    }
    @media (max-width: 768px) {
        .ask-hero {
            padding: 32px 20px;
            margin-bottom: 16px;
        }
        .hero-avatar-wrap { display: none; }
    }

    /* Smooth Appearing Message Animation */
    @keyframes msgSlideFadeIn {
        0% {
            opacity: 0;
            transform: translateY(10px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ChatGPT-Style Word-by-Word Fade-In Stagger Animation */
    @keyframes wordStaggerReveal {
        0% {
            opacity: 0;
            transform: translateY(3px);
            filter: blur(1.5px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
            filter: blur(0);
        }
    }

    .stagger-word {
        display: inline;
        animation: wordStaggerReveal 0.34s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }

    /* Skeleton Shimmer Animation */
    @keyframes jiveSkeletonShimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }

    .jive-skeleton-wrap {
        display: flex;
        flex-direction: column;
        gap: 9px;
        min-width: 240px;
        max-width: 420px;
        padding: 2px 0;
    }
    .jive-skeleton-label {
        font-size: 12px;
        color: #9ca3af;
        letter-spacing: 0.2px;
    }
    .jive-skeleton-line {
        height: 11px;
        border-radius: 999px;
        background: linear-gradient(90deg, rgba(255,255,255,0.08) 25%, rgba(255,255,255,0.22) 50%, rgba(255,255,255,0.08) 75%);
        background-size: 200% 100%;
        animation: jiveSkeletonShimmer 1.6s infinite linear;
    }
    .jive-skeleton-line.w-90 { width: 92%; }
    .jive-skeleton-line.w-75 { width: 76%; }
    .jive-skeleton-line.w-55 { width: 54%; }

    /* Main Conversation Area */
    .ask-chat-main {
        flex: 1;
        width: 100%;
        max-width: 1280px;
        margin: 0 auto;
        padding: 16px 24px 36px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    #full-chat-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .msg-row {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        animation: msgSlideFadeIn 0.3s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }
    .msg-row.user {
        justify-content: flex-end;
    }

    .msg-avatar {
        width: 46px;
        height: 46px;
        min-width: 46px;
        max-width: 46px;
        border-radius: 50%;
        overflow: hidden;
        box-shadow: none;
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
        background: #141416;
        color: #f9fafb;
        border-radius: 14px;
        padding: 18px 22px;
        max-width: 72%;
        min-width: 140px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 15px;
        line-height: 1.65;
        box-shadow: none;
    }
    .chat-bubble-assistant p:last-child {
        margin-bottom: 0;
    }
    .chat-bubble-assistant .intro-highlight {
        color: #a5f3fc;
        font-weight: 600;
    }

    .chat-bubble-user {
        background: #ffffff;
        color: #111827;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 16px 22px;
        max-width: 65%;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 15px;
        font-weight: 500;
        line-height: 1.5;
        box-shadow: none;
    }
    @media (max-width: 768px) {
        .chat-bubble-assistant { max-width: 86%; }
        .chat-bubble-user { max-width: 82%; }
    }

    /* Compact & Organized Ticket / Product Cards */
    .jive-cards-stack {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 12px;
    }
    .jive-ui-card {
        background: #2b2b2e;
        color: #f4f4f5;
        border-radius: 10px;
        padding: 13px 16px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: none;
        animation: msgSlideFadeIn 0.35s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }
    .jive-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 4px;
    }
    .jive-card-title-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .jive-card-index {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #7C3AED;
        color: #ffffff;
        font-family: 'modern-bold', sans-serif;
        font-size: 12px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .jive-card-title {
        font-family: 'modern-bold', sans-serif;
        font-size: 14.5px;
        font-weight: 800;
        color: #ffffff;
        margin: 0;
    }
    .jive-badge-price {
        background: rgba(16, 185, 129, 0.18);
        color: #6ee7b7;
        font-size: 12px;
        font-weight: 700;
        padding: 2px 9px;
        border-radius: 999px;
        white-space: nowrap;
    }
    .jive-badge-status {
        background: rgba(245, 158, 11, 0.2);
        color: #fcd34d;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 9px;
        border-radius: 999px;
        white-space: nowrap;
    }
    .jive-card-meta {
        font-size: 13px;
        color: #d4d4d8;
        margin: 2px 0 0 30px;
        line-height: 1.45;
    }
    .jive-card-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 10px 0 0 30px;
    }
    .btn-card-primary {
        background: #7C3AED;
        color: #ffffff;
        text-decoration: none;
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        display: inline-block;
        transition: background-color 0.15s ease;
    }
    .btn-card-primary:hover {
        background: #6D28D9;
    }
    .btn-card-secondary {
        background: rgba(255, 255, 255, 0.1);
        color: #f4f4f5;
        border: 1px solid rgba(255, 255, 255, 0.18);
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    .btn-card-secondary:hover {
        background: rgba(255, 255, 255, 0.18);
    }

    /* Prompt Chips Positioned at Very Bottom of Chat Feed */
    .prompt-chips-section {
        width: 100%;
        margin-top: 36px;
        padding-top: 12px;
        opacity: 1;
        transform: translateY(0);
        max-height: 400px;
        overflow: hidden;
        transition: opacity 0.32s cubic-bezier(0.22, 1, 0.36, 1),
                    transform 0.32s cubic-bezier(0.22, 1, 0.36, 1),
                    max-height 0.35s ease,
                    margin-top 0.3s ease;
    }
    .prompt-chips-section.is-hidden {
        opacity: 0;
        transform: translateY(14px);
        max-height: 0;
        margin-top: 0;
        padding-top: 0;
        pointer-events: none;
    }

    .prompt-chips-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }
    @media (max-width: 900px) {
        .prompt-chips-grid {
            display: flex;
            overflow-x: auto;
            padding-bottom: 4px;
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
        border-radius: 10px;
        padding: 15px 18px;
        color: #18181b;
        text-align: left;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 3px;
        box-shadow: none;
        transition: background-color 0.15s ease, transform 0.15s ease;
    }
    .chip-btn:hover {
        background: #dcd9d3;
        transform: translateY(-2px);
    }
    .chip-btn-title {
        font-family: 'modern-bold', sans-serif;
        font-size: 14px;
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

    /* Sticky Bottom Input Message Bar */
    .ask-input-dock {
        position: sticky;
        bottom: 0;
        left: 0;
        width: 100%;
        z-index: 40;
        border-top: 1px solid #d4d4d8;
        background: #f6f5f8;
        padding: 14px 20px 16px;
    }
    .ask-input-container {
        width: 100%;
        max-width: 880px;
        margin: 0 auto;
    }
    .ask-input-pill {
        display: flex;
        align-items: center;
        background: #ffffff;
        border: 1px solid #d4d4d8;
        border-radius: 999px;
        padding: 6px 8px 6px 22px;
        box-shadow: none;
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

    /* Electric Purple Send Button */
    .ask-submit-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: none;
        background: #7C3AED;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
        transition: transform 0.15s ease, background-color 0.15s ease;
    }
    .ask-submit-circle:hover {
        background: #6D28D9;
        transform: scale(1.04);
    }
    .ask-submit-circle:disabled {
        opacity: 0.55;
        cursor: not-allowed;
        transform: none;
    }

    .ask-disclaimer {
        text-align: center;
        margin-top: 8px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 11.5px;
        color: #3f3f46;
    }
    .ask-disclaimer span {
        text-decoration: underline;
    }

    /* Scroll Sentinel at the very bottom of the chat flow */
    #chat-scroll-sentinel {
        height: 24px;
        width: 100%;
        scroll-margin-bottom: 110px;
    }
</style>

<!-- Main Page Wrapper -->
<div class="ask-jive-page" id="ask-jive-root">
    <div>
        <!-- Hero Header -->
        <div class="ask-hero">
            <div class="ask-hero-container">
                <div>
                    <h1 class="ask-hero-title">ASK JIVE <span class="sparkle-icon">✨</span></h1>
                    <p class="ask-hero-sub">Not sure what you want?<br>From store products to pickup tickets, ask virtual Jive anything!</p>
                    <button type="button" onclick="clearFullChat()" class="btn-reset-chat">Start a new conversation</button>
                </div>
                <div class="hero-avatar-wrap">
                    <img src="{{ asset('images/ask_jive_avatar.png') }}" alt="Jive Avatar">
                </div>
            </div>
        </div>

        <!-- Main Conversation Area -->
        <div class="ask-chat-main">
            <div id="full-chat-container">
                <div class="msg-row">
                    <div class="msg-avatar">
                        <img src="{{ asset('images/ask_jive_avatar.png') }}" alt="Jive Avatar">
                    </div>
                    <div class="chat-bubble-assistant">
                        <p style="margin: 0;"><span class="intro-highlight">Hi, I'm Jive. And I'm here to help you</span> with your pasalubong orders and tickets. How can I help?</p>
                    </div>
                </div>
            </div>

            <!-- 4 Suggested Prompt Cards Positioned at the Very Bottom of the Chat -->
            <div class="prompt-chips-section" id="prompt-chips-container">
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

            <!-- Bottom Sentinel Anchor for Scroll Tracking -->
            <div id="chat-scroll-sentinel"></div>
        </div>
    </div>

    <!-- Sticky Bottom Input Message Bar -->
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
    const MIN_SKELETON_MS = 850;
    const avatarUrl = "{{ asset('images/ask_jive_avatar.png') }}";
    const chatSendEndpoint = "{{ \Illuminate\Support\Facades\Route::has('chat.send') ? route('chat.send') : route('chat.send_message') }}";

    /**
     * Scrolls ALL possible scroll containers (window, documentElement, body, or layout wrapper divs)
     * plus brings the bottom sentinel into view so the page always tracks the growing response.
     */
    function followBottomImmediate() {
        const sentinel = document.getElementById('chat-scroll-sentinel');

        let parent = sentinel ? sentinel.parentElement : document.getElementById('ask-jive-root');
        while (parent && parent !== document.body && parent !== document.documentElement) {
            const style = window.getComputedStyle(parent);
            const overflowY = style.overflowY;
            if ((overflowY === 'auto' || overflowY === 'scroll') && parent.scrollHeight > parent.clientHeight) {
                parent.scrollTop = parent.scrollHeight;
            }
            parent = parent.parentElement;
        }

        const maxScroll = Math.max(
            document.body.scrollHeight,
            document.documentElement.scrollHeight
        );
        document.documentElement.scrollTop = maxScroll;
        document.body.scrollTop = maxScroll;
        window.scrollTo(0, maxScroll);

        if (sentinel && typeof sentinel.scrollIntoView === 'function') {
            sentinel.scrollIntoView({ behavior: 'auto', block: 'end' });
        }
    }

    function setChipsLoadingState(loading) {
        isChatGenerating = loading;
        const chipsContainer = document.getElementById('prompt-chips-container');
        const submitBtn = document.getElementById('full-chat-submit');

        if (submitBtn) submitBtn.disabled = loading;

        if (chipsContainer) {
            if (loading) {
                chipsContainer.classList.add('is-hidden');
            } else {
                chipsContainer.classList.remove('is-hidden');
                requestAnimationFrame(() => followBottomImmediate());
                setTimeout(() => followBottomImmediate(), 120);
            }
        }
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
        setChipsLoadingState(true);

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
        requestAnimationFrame(() => followBottomImmediate());
    }

    function removeSkeletonLoading() {
        const el = document.getElementById('full-skeleton-row');
        if (el) el.remove();
    }

    function handleFullSend(e) {
        e.preventDefault();
        if (isChatGenerating) return;

        const input = document.getElementById('full-chat-input');
        const text = input.value.trim();
        if (!text) return;

        appendUserMessage(text);
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
                    const plainSummary = data.reply
                        .replace(/<[^>]*>/g, ' ')
                        .replace(/\s+/g, ' ')
                        .trim()
                        .slice(0, 800);
                    fullHistory.push({ role: 'assistant', content: plainSummary || 'Response sent.' });
                    appendAssistantMessageProgressive(data.reply);
                } else {
                    fullHistory.pop();
                    setChipsLoadingState(false);
                }
            }, remainingDelay);
        })
        .catch(err => {
            const elapsed = Date.now() - startedAt;
            const remainingDelay = Math.max(0, MIN_SKELETON_MS - elapsed);

            setTimeout(() => {
                removeSkeletonLoading();
                fullHistory.pop();
                console.error("Ask Jive Full Chat Error:", err);
                appendAssistantMessageProgressive("Sorry, I encountered a connection error. Please try again.");
            }, remainingDelay);
        });
    }

    function extractTextAndCards(rawText) {
        let plainText = rawText;
        let cardItems = [];
        let cardCounter = 1;

        // 1. Extract [PRODUCT_CARD]
        const productRegex = /\[PRODUCT_CARD\]([\s\S]*?)\[END_PRODUCT_CARD\]/g;
        plainText = plainText.replace(productRegex, (match, block) => {
            const name = (block.match(/Name:\s*(.*)/i) || [])[1]?.trim() || 'Shop Item';
            const price = (block.match(/Price:\s*(.*)/i) || [])[1]?.trim() || '';
            const branch = (block.match(/Branch:\s*(.*)/i) || [])[1]?.trim() || 'Salacot & San Jose';
            const desc = (block.match(/Description:\s*(.*)/i) || [])[1]?.trim() || '';
            const link = (block.match(/Link:\s*(.*)/i) || [])[1]?.trim() || '/shop';

            cardItems.push(`
                <div class="jive-ui-card">
                    <div class="jive-card-top">
                        <div class="jive-card-title-row">
                            <span class="jive-card-index">${cardCounter++}</span>
                            <h5 class="jive-card-title">${name}</h5>
                        </div>
                        ${price ? `<span class="jive-badge-price">${price}</span>` : ''}
                    </div>
                    <div class="jive-card-meta">
                        ${desc ? `<div>${desc}</div>` : ''}
                        <div style="color: #a1a1aa; font-size: 12px; margin-top: 2px;">📍 ${branch}</div>
                    </div>
                    <div class="jive-card-actions">
                        <a href="${link}" class="btn-card-primary">View in Shop &rarr;</a>
                    </div>
                </div>
            `);
            return '';
        });

        // 2. Extract [TICKET_CARD]
        const ticketRegex = /\[TICKET_CARD\]([\s\S]*?)\[END_TICKET_CARD\]/g;
        plainText = plainText.replace(ticketRegex, (match, block) => {
            const id = (block.match(/ID:\s*(.*)/i) || [])[1]?.trim() || '#0000';
            const subject = (block.match(/Subject:\s*(.*)/i) || [])[1]?.trim() || 'Order Ticket';
            const status = (block.match(/Status:\s*(.*)/i) || [])[1]?.trim() || 'PENDING';
            const date = (block.match(/Date:\s*(.*)/i) || [])[1]?.trim() || '';
            const link = (block.match(/Link:\s*(.*)/i) || [])[1]?.trim() || '/orders';

            cardItems.push(`
                <div class="jive-ui-card">
                    <div class="jive-card-top">
                        <div class="jive-card-title-row">
                            <span class="jive-card-index">${cardCounter++}</span>
                            <h5 class="jive-card-title">${id} — ${subject}</h5>
                        </div>
                        <span class="jive-badge-status">${status}</span>
                    </div>
                    ${date ? `<div class="jive-card-meta">Scheduled: ${date}</div>` : ''}
                    <div class="jive-card-actions">
                        <a href="${link}" class="btn-card-primary">View Details ↗</a>
                        <button type="button" onclick="sendSuggestedPrompt('What is the status of ticket ${id}?')" class="btn-card-secondary">Ask Jive</button>
                    </div>
                </div>
            `);
            return '';
        });

        const cleanedText = plainText
            .replace(/(<br\s*\/?>\s*){2,}/gi, '<br>')
            .replace(/^(<br\s*\/?>|\s)+|(<br\s*\/?>|\s)+$/gi, '')
            .trim();

        return {
            cleanText: cleanedText,
            cardItems: cardItems
        };
    }

    function appendUserMessage(text) {
        const container = document.getElementById('full-chat-container');
        const escaped = text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
        container.insertAdjacentHTML('beforeend', `
            <div class="msg-row user">
                <div class="chat-bubble-user"><p style="margin: 0;">${escaped}</p></div>
            </div>
        `);
        requestAnimationFrame(() => followBottomImmediate());
    }

    /**
     * Progressive DOM Builder:
     * Appends HTML elements and words one-by-one into the live chat bubble so the container
     * grows naturally line-by-line (never pre-sized) and scrolls down with every new line.
     */
    function appendAssistantMessageProgressive(rawText) {
        const container = document.getElementById('full-chat-container');
        const { cleanText, cardItems } = extractTextAndCards(rawText);

        const rowId = 'msg-row-' + Date.now();
        const textId = 'msg-text-' + Date.now();
        const cardsId = 'msg-cards-' + Date.now();

        container.insertAdjacentHTML('beforeend', `
            <div class="msg-row" id="${rowId}">
                <div class="msg-avatar">
                    <img src="${avatarUrl}" alt="Jive Avatar">
                </div>
                <div class="chat-bubble-assistant">
                    <div id="${textId}"></div>
                    <div id="${cardsId}"></div>
                </div>
            </div>
        `);

        const textEl = document.getElementById(textId);
        const cardsEl = document.getElementById(cardsId);

        followBottomImmediate();

        const streamQueue = [];

        if (cleanText) {
            const tempSource = document.createElement('div');
            tempSource.innerHTML = cleanText;

            function buildStreamSteps(sourceNode, targetParent) {
                Array.from(sourceNode.childNodes).forEach(child => {
                    if (child.nodeType === Node.TEXT_NODE) {
                        const tokens = child.nodeValue.split(/(\s+)/);
                        tokens.forEach(token => {
                            if (!token) return;
                            if (/^\s+$/.test(token)) {
                                streamQueue.push({
                                    type: 'space',
                                    parent: targetParent,
                                    text: token
                                });
                            } else {
                                streamQueue.push({
                                    type: 'word',
                                    parent: targetParent,
                                    text: token
                                });
                            }
                        });
                    } else if (child.nodeType === Node.ELEMENT_NODE) {
                        const clonedEl = child.cloneNode(false);
                        streamQueue.push({
                            type: 'element',
                            parent: targetParent,
                            element: clonedEl
                        });
                        buildStreamSteps(child, clonedEl);
                    }
                });
            }

            buildStreamSteps(tempSource, textEl);
        }

        let stepIdx = 0;
        let lastBubbleHeight = textEl.offsetHeight;
        const wordDelayMs = 68;

        function revealCardsSequentially(cardIdx = 0) {
            if (cardIdx >= cardItems.length) {
                setTimeout(() => {
                    setChipsLoadingState(false);
                }, 180);
                return;
            }

            let stack = cardsEl.querySelector('.jive-cards-stack');
            if (!stack) {
                stack = document.createElement('div');
                stack.className = 'jive-cards-stack';
                cardsEl.appendChild(stack);
            }

            stack.insertAdjacentHTML('beforeend', cardItems[cardIdx]);
            followBottomImmediate();

            setTimeout(() => revealCardsSequentially(cardIdx + 1), 160);
        }

        function processNextStep() {
            if (stepIdx >= streamQueue.length) {
                followBottomImmediate();
                if (cardItems.length > 0) {
                    setTimeout(() => revealCardsSequentially(0), 120);
                } else {
                    setTimeout(() => {
                        setChipsLoadingState(false);
                    }, 180);
                }
                return;
            }

            const step = streamQueue[stepIdx++];

            if (step.type === 'element') {
                step.parent.appendChild(step.element);
                const currentHeight = textEl.offsetHeight;
                if (currentHeight > lastBubbleHeight) {
                    lastBubbleHeight = currentHeight;
                    followBottomImmediate();
                }
                processNextStep();
                return;
            }

            if (step.type === 'space') {
                step.parent.appendChild(document.createTextNode(step.text));
                processNextStep();
                return;
            }

            const span = document.createElement('span');
            span.className = 'stagger-word';
            span.textContent = step.text;
            step.parent.appendChild(span);

            const currentHeight = textEl.offsetHeight;
            if (currentHeight > lastBubbleHeight) {
                lastBubbleHeight = currentHeight;
                followBottomImmediate();
            }

            setTimeout(processNextStep, wordDelayMs);
        }

        processNextStep();
    }

    function clearFullChat() {
        fullHistory = [];
        setChipsLoadingState(false);

        document.getElementById('full-chat-container').innerHTML = `
            <div class="msg-row">
                <div class="msg-avatar">
                    <img src="{{ asset('images/ask_jive_avatar.png') }}" alt="Jive Avatar">
                </div>
                <div class="chat-bubble-assistant">
                    <p style="margin: 0;"><span class="intro-highlight">Hi, I'm Jive. And I'm here to help you</span> with your pasalubong orders and tickets. How can I help?</p>
                </div>
            </div>
        `;

        const root = document.getElementById('ask-jive-root');
        let parent = root ? root.parentElement : null;
        while (parent && parent !== document.body && parent !== document.documentElement) {
            parent.scrollTop = 0;
            parent = parent.parentElement;
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>
@endsection