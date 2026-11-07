@extends('layouts.portal')

@section('title', 'Walk-in OCR Intake')

@section('content')
<div style="max-width: 800px; margin: 30px auto; padding: 0 16px;">
    <div style="background: #ffffff; border-radius: 16px; padding: 28px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
        
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
            <!-- Scan Document SVG Icon -->
            <svg viewBox="0 0 24 24" fill="none" stroke="#7e22ce" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 28px; height: 28px;">
                <path d="M3 7V5a2 2 0 0 1 2-2h2"></path>
                <path d="M17 3h2a2 2 0 0 1 2 2v2"></path>
                <path d="M21 17v2a2 2 0 0 1-2 2h-2"></path>
                <path d="M7 21H5a2 2 0 0 1-2-2v-2"></path>
                <rect x="7" y="7" width="10" height="10" rx="1"></rect>
            </svg>
            <h2 style="font-family: 'Manrope', sans-serif; font-weight: 800; color: #1e293b; margin: 0;">Walk-in Order OCR Intake</h2>
        </div>
        <p style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">Upload a physical order form or cake reference receipt to parse details automatically.</p>

        @if(session('success'))
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('staff.walkin.parse') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- File Upload Box -->
            <div style="border: 2px dashed #cbd5e1; border-radius: 12px; padding: 32px 20px; text-align: center; background: #f8fafc; margin-bottom: 20px; cursor: pointer;" onclick="document.getElementById('ocr-file-input').click();">
                
                <!-- Upload Cloud SVG -->
                <svg viewBox="0 0 24 24" fill="none" stroke="#7e22ce" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 44px; height: 44px; margin-bottom: 10px;">
                    <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                    <path d="M12 12v9"></path>
                    <path d="m16 16-4-4-4 4"></path>
                </svg>

                <p style="margin: 0; color: #334155; font-weight: 700; font-size: 0.95rem;">Click or drag order image here to upload</p>
                <span style="color: #94a3b8; font-size: 0.8rem; display: block; margin-top: 4px;">Supports PNG, JPG, JPEG up to 10MB</span>
                
                <input type="file" id="ocr-file-input" name="order_image" accept="image/*" required style="display: none;" onchange="previewOcrImage(event)">
            </div>

            <!-- Dynamic Image Preview Container -->
            <div id="ocr-preview-container" style="display: none; margin-bottom: 20px; text-align: center;">
                <img id="ocr-preview-img" src="#" alt="Order Preview" style="max-height: 240px; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.06);" />
            </div>

            <button type="submit" style="background: var(--purple-main, #7e22ce); color: #ffffff; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 800; cursor: pointer; width: 100%; font-size: 1rem; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                </svg>
                <span>Process & Extract OCR Data</span>
            </button>
        </form>
    </div>
</div>

<script>
    function previewOcrImage(event) {
        const input = event.target;
        const container = document.getElementById('ocr-preview-container');
        const img = document.getElementById('ocr-preview-img');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                container.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection