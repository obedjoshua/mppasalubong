@extends('layouts.portal')

@section('title', 'Send Promotional Broadcast')

@section('content')
<div style="max-width: 800px; margin: 30px auto; padding: 0 16px;">
    <div style="background: #ffffff; border-radius: 16px; padding: 28px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
        
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="#7e22ce" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 28px; height: 28px;">
                <path d="m3 11 18-5v12L3 13v-2z"></path>
                <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"></path>
            </svg>
            <h2 style="font-family: 'Recoleta-Bold', serif; color: #1e293b; margin: 0;">Send Promotional Broadcast</h2>
        </div>
        <p style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">Compose a promotional message. If no custom banner is uploaded, a default header image will be chosen automatically.</p>

        @if(session('success'))
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.promotions.send') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom: 18px;">
                <label style="display: block; font-weight: bold; margin-bottom: 6px; color: #334155;">Broadcast Title</label>
                <input type="text" name="title" required placeholder="Special Weekend Promo: 15% Off All Custom Cakes!" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #cbd5e1;">
            </div>

            <div style="margin-bottom: 18px;">
                <label style="display: block; font-weight: bold; margin-bottom: 6px; color: #334155;">Promo Banner Image (Optional)</label>
                <input type="file" name="banner" accept="image/*" style="width: 100%; padding: 8px; border-radius: 8px; border: 1px solid #cbd5e1;">
                <small style="color: #64748b; font-size: 0.75rem;">Leave empty to automatically assign default header image (community_header_01 or notif_header_01).</small>
            </div>

            <div style="margin-bottom: 18px;">
                <label style="display: block; font-weight: bold; margin-bottom: 6px; color: #334155;">Action Button URL (Optional)</label>
                <input type="url" name="action_url" placeholder="https://yourdomain.com/shop" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #cbd5e1;">
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: bold; margin-bottom: 6px; color: #334155;">Promotional Message Content</label>
                <textarea name="message" rows="6" required placeholder="Write your full promotional details..." style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #cbd5e1;"></textarea>
            </div>

            <button type="submit" onclick="return confirm('Dispatch promotional broadcast?');" style="background: #7e22ce; color: #ffffff; border: none; padding: 12px 24px; border-radius: 8px; font-weight: bold; cursor: pointer; width: 100%; font-size: 1rem; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                </svg>
                <span>Dispatch Broadcast to All Users</span>
            </button>
        </form>
    </div>
</div>
@endsection