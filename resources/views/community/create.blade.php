<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Order | Masaganang Pagpapala</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&display=swap" rel="stylesheet">
    
    <!-- Standard CSS Links -->
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/orders-create.css') }}">
</head>
<body class="orders-page">

    <!-- 1. Hidden Checkbox for Side Menu Toggle State -->
    <input type="checkbox" id="menu-toggle" style="display: none;">

    <!-- 2. Side Menu Reveal Container -->
    @include('partials.side-menu')

    <!-- 3. Main Shifting Page Wrapper -->
    <div class="page-wrapper" id="page-wrapper">
        
        @include('partials.header')

        <div class="dashboard-container">
            <div class="create-wrapper">
                
                <div class="page-header">
                    <a href="{{ route('orders.index') }}" class="back-btn">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                    <div>
                        <h1>Design Your Custom Cake</h1>
                        <p>Upload a reference, configure your specs, and we'll bake it to perfection.</p>
                    </div>
                </div>

                <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data" class="form-grid">
                    @csrf

                    <!-- LEFT COLUMN: Image Drag & Drop -->
                    <div class="upload-column">
                        <label id="dropZone" class="upload-zone">
                            <input type="file" name="reference_image" id="referenceImageInput" accept="image/*" style="display:none;">
                            
                            <div id="uploadPlaceholder" class="upload-placeholder">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                <strong>Drag & drop image here</strong>
                                <span>or click to browse</span>
                            </div>
                            
                            <img id="imagePreview" class="preview-img" src="" alt="Preview">
                            
                            <button type="button" id="clearImageBtn" onclick="clearImage(event)" class="btn-clear">
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                            
                            <!-- AI Loading Overlay -->
                            <div id="aiLoadingOverlay" class="loading-overlay">
                                <svg class="spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle opacity="0.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path opacity="0.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span style="font-size:0.875rem; font-weight:800; color:#8A38F5; text-transform:uppercase;">AI Analyzing...</span>
                            </div>
                        </label>
                    </div>

                    <!-- RIGHT COLUMN: Fields -->
                    <div class="fields-column">
                        
                        <div class="duo-card">

                            <!-- AI Theme Block -->
                            <div id="aiThemeCard" class="ai-info-card" style="display:none;">
                                <div class="ai-card-badge">✨ Identified Cake Theme</div>
                                <div id="aiThemeTitle" class="ai-card-title"></div>
                                <input type="hidden" name="ai_theme" id="hiddenAiTheme">
                            </div>

                            <!-- AI Description Block -->
                            <div id="aiDescCard" class="ai-info-card" style="display:none;">
                                <div class="ai-card-badge">📝 Cake Description</div>
                                <div id="aiThemeDescription" class="ai-card-text"></div>
                                <input type="hidden" name="ai_description" id="hiddenAiDescription">
                            </div>

                            <!-- Colors -->
                            <div class="input-group color-container">
                                <label class="input-label" style="justify-content:center; margin-bottom:16px;">Icing Colors</label>
                                <div class="color-grid">
                                    <div class="color-item">
                                        <div class="color-picker-wrapper"><input type="color" name="color_base" id="colorBase" value="#ffffff" class="color-picker-input"></div>
                                        <span class="color-label">Base</span>
                                    </div>
                                    <div class="color-item">
                                        <div class="color-picker-wrapper"><input type="color" name="color_top" id="colorTop" value="#ffffff" class="color-picker-input"></div>
                                        <span class="color-label">Top</span>
                                    </div>
                                    <div class="color-item">
                                        <div class="color-picker-wrapper"><input type="color" name="color_border" id="colorBorder" value="#ffffff" class="color-picker-input"></div>
                                        <span class="color-label">Border</span>
                                    </div>
                                    <div class="color-item">
                                        <div class="color-picker-wrapper"><input type="color" name="color_sprinkles" id="colorSprinkles" value="#ffffff" class="color-picker-input"></div>
                                        <span class="color-label">Details</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Flavor -->
                            <div class="input-group">
                                <label class="input-label">Flavor</label>
                                <div class="pill-grid">
                                    <label class="pill-radio flex-1"><input type="radio" name="flavor" value="Chocolate" checked><div class="pill-label">Chocolate</div></label>
                                    <label class="pill-radio flex-1"><input type="radio" name="flavor" value="Vanilla"><div class="pill-label">Vanilla</div></label>
                                    <label class="pill-radio flex-1"><input type="radio" name="flavor" value="Ube"><div class="pill-label">Ube</div></label>
                                    <label class="pill-radio flex-1"><input type="radio" name="flavor" value="Mocha Chiffon"><div class="pill-label">Mocha Chiffon</div></label>
                                    <label class="pill-radio flex-1"><input type="radio" name="flavor" value="Regular Chiffon"><div class="pill-label">Reg. Chiffon</div></label>
                                </div>
                            </div>

                            <div class="split-grid input-group">
                                <!-- Shape -->
                                <div>
                                    <label class="input-label">Shape <span id="shapeLockNotice" class="ai-notice">Locked by AI</span></label>
                                    <div class="pill-grid flex-1">
                                        <label class="pill-radio"><input type="radio" name="shape" value="Round" id="shapeRound" checked><div class="pill-label">Round</div></label>
                                        <label class="pill-radio"><input type="radio" name="shape" value="Square" id="shapeSquare"><div class="pill-label">Square</div></label>
                                        <label class="pill-radio"><input type="radio" name="shape" value="Rectangle" id="shapeRect"><div class="pill-label">Rect</div></label>
                                    </div>
                                </div>
                                <!-- Height -->
                                <div>
                                    <label class="input-label">Height <span id="heightLockNotice" class="ai-notice">Suggested by AI</span></label>
                                    <select name="height" id="heightSelect" class="clean-input" style="padding:12px;">
                                        <option value="4 inches">4 inches (Standard)</option>
                                        <option value="5 inches">5 inches (Tall)</option>
                                        <option value="6 inches">6 inches (Extra Tall)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Cake Size -->
                            <div class="input-group">
                                <label class="input-label">Cake Size (Diameter)</label>
                                <div class="size-container">
                                    <label class="size-radio">
                                        <input type="radio" name="size" value="6" checked>
                                        <div class="size-circle size-6">6"</div>
                                    </label>
                                    <label class="size-radio">
                                        <input type="radio" name="size" value="8">
                                        <div class="size-circle size-8">8"</div>
                                    </label>
                                    <label class="size-radio">
                                        <input type="radio" name="size" value="10">
                                        <div class="size-circle size-10">10"</div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Messages -->
                        <div class="duo-card">
                            <label class="input-label">Cake Message & Toppers</label>
                            <div id="messagesContainer">
                                <div class="message-row">
                                    <input type="text" name="messages[]" class="clean-input msg-input" placeholder="e.g. Happy Birthday">
                                    <select name="message_placement[]" class="clean-input msg-select">
                                        <option value="Top">Top</option>
                                        <option value="Front">Front</option>
                                        <option value="Board">Board</option>
                                    </select>
                                    <div class="color-picker-wrapper msg-color"><input type="color" name="message_color[]" value="#374151" class="color-picker-input"></div>
                                    <button type="button" onclick="this.parentElement.remove()" class="btn-delete">
                                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                            <button type="button" onclick="addMessageRow()" class="btn-add"><span>+</span> Add message</button>
                        </div>

                        <!-- Submit Details -->
                        <div class="duo-card">
                            <div class="input-group">
                                <label class="input-label">Pickup Schedule</label>
                                <div class="date-wrapper">
                                    <svg class="date-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <input type="datetime-local" id="combinedDateTime" class="clean-input" required>
                                </div>
                                <span class="sub-label">Mon-Sat: 8 AM - 5 PM. Sundays: 7 AM - 9 AM & 11 PM only.</span>
                                <input type="hidden" name="pickup_date" id="hiddenDate">
                                <input type="hidden" name="pickup_time" id="hiddenTime">
                            </div>

                            <div class="input-group">
                                <label class="input-label">Additional Instructions</label>
                                <textarea name="additional_notes" id="additionalNotes" rows="2" class="clean-input" placeholder="e.g. Make the colors exactly as in the photo..."></textarea>
                            </div>

                            <input type="hidden" name="customer_name" value="{{ Auth::check() ? Auth::user()->name : 'Guest' }}">
                            <input type="hidden" name="location_address" value="TBD">
                            <input type="hidden" name="notification_preference" value="Email">

                            <button type="submit" class="btn-submit">Submit Order</button>
                        </div>
                    </div>
                </form>
            </div>
            
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('referenceImageInput');
        const dropZone = document.getElementById('dropZone');
        const preview = document.getElementById('imagePreview');
        const clearBtn = document.getElementById('clearImageBtn');
        const placeholder = document.getElementById('uploadPlaceholder');
        const overlay = document.getElementById('aiLoadingOverlay');
    
        dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('dragover'); });
        dropZone.addEventListener('dragleave', () => { dropZone.classList.remove('dragover'); });
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault(); dropZone.classList.remove('dragover');
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                processImage(fileInput.files[0]);
            }
        });
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) processImage(e.target.files[0]);
        });
    
        function clearImage(e) {
            if(e) e.preventDefault();
            fileInput.value = ''; preview.src = ''; preview.style.display = 'none';
            clearBtn.style.display = 'none'; placeholder.style.display = 'block'; dropZone.classList.remove('has-image');
            
            document.getElementById('aiThemeCard').style.display = 'none';
            document.getElementById('aiDescCard').style.display = 'none';
            document.getElementById('shapeRound').disabled = false;
            document.getElementById('shapeSquare').disabled = false;
            document.getElementById('shapeRect').disabled = false;
            document.getElementById('shapeLockNotice').classList.remove('visible');
            document.getElementById('heightLockNotice').classList.remove('visible');
        }
    
        async function processImage(file) {
            preview.src = URL.createObjectURL(file); 
            preview.style.display = 'block';
            placeholder.style.display = 'none'; 
            dropZone.classList.add('has-image');
            clearBtn.style.display = 'block'; 
            overlay.classList.add('visible');
    
            let formData = new FormData();
            formData.append('image', file);
            formData.append('_token', '{{ csrf_token() }}');
    
            try {
                const response = await fetch('/api/vision/extract-colors', { 
                    method: 'POST', 
                    body: formData, 
                    headers: { 'Accept': 'application/json' } 
                });
                const data = await response.json();
                overlay.classList.remove('visible');
    
                if (response.ok && data.success) {
                    if (data.processed_image_url) {
                        preview.src = data.processed_image_url;
                    }

                    // Display AI Theme Card
                    if (data.theme) {
                        document.getElementById('aiThemeCard').style.display = 'block';
                        document.getElementById('aiThemeTitle').innerText = data.theme;
                        document.getElementById('hiddenAiTheme').value = data.theme;
                    }

                    // Display AI Description Card
                    if (data.description) {
                        document.getElementById('aiDescCard').style.display = 'block';
                        document.getElementById('aiThemeDescription').innerText = data.description;
                        document.getElementById('hiddenAiDescription').value = data.description;
                    }

                    document.getElementById('colorBase').value = data.colors.base;
                    document.getElementById('colorTop').value = data.colors.top;
                    document.getElementById('colorBorder').value = data.colors.border;
                    document.getElementById('colorSprinkles').value = data.colors.sprinkles;
    
                    if (data.shape) {
                        const shapeMap = { 'Round': 'shapeRound', 'Square': 'shapeSquare', 'Rectangle': 'shapeRect' };
                        const targetId = shapeMap[data.shape];
                        if (targetId) {
                            ['shapeRound', 'shapeSquare', 'shapeRect'].forEach(id => document.getElementById(id).disabled = true);
                            document.getElementById(targetId).disabled = false; 
                            document.getElementById(targetId).checked = true;
                            document.getElementById('shapeLockNotice').classList.add('visible');
                        }
                    }
    
                    if (data.height) {
                        const hSelect = document.getElementById('heightSelect');
                        for(let i = 0; i < hSelect.options.length; i++) {
                            if(hSelect.options[i].value.toLowerCase().includes(data.height.toLowerCase())) {
                                hSelect.selectedIndex = i;
                                document.getElementById('heightLockNotice').classList.add('visible');
                                break;
                            }
                        }
                    }
    
                    if (data.messages && Array.isArray(data.messages) && data.messages.length > 0) {
                        document.getElementById('messagesContainer').innerHTML = '';
                        data.messages.forEach(msg => {
                            const color = /^#[0-9A-F]{6}$/i.test(msg.color) ? msg.color : '#374151';
                            addMessageRow(msg.text, color);
                        });
                    }
                } else {
                    if (data.error === 'invalid_content') {
                        alert("Oops! That doesn't look like a cake. Please upload a valid cake or pastry reference image.");
                    } else {
                        alert("AI Error: " + (data.error || "Could not process image."));
                    }
                    clearImage();
                }
            } catch (error) {
                overlay.classList.remove('visible');
                console.error("Network Error:", error);
                alert("Network connection failed. Please try uploading again.");
                clearImage();
            }
        }
    
        function addMessageRow(text = '', color = '#374151') {
            const container = document.getElementById('messagesContainer');
            const row = document.createElement('div');
            row.className = 'message-row';
            row.innerHTML = `
                <input type="text" name="messages[]" class="clean-input msg-input" placeholder="e.g. Happy Birthday" value="${text}">
                <select name="message_placement[]" class="clean-input msg-select">
                    <option value="Top">Top</option><option value="Front">Front</option><option value="Board">Board</option>
                </select>
                <div class="color-picker-wrapper msg-color"><input type="color" name="message_color[]" value="${color}" class="color-picker-input"></div>
                <button type="button" onclick="this.parentElement.remove()" class="btn-delete"><svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
            `;
            container.appendChild(row);
        }
    
        const dtInput = document.getElementById('combinedDateTime');
        const hiddenDate = document.getElementById('hiddenDate');
        const hiddenTime = document.getElementById('hiddenTime');
    
        let tomorrow = new Date(); 
        tomorrow.setDate(tomorrow.getDate() + 1); 
        tomorrow.setHours(7, 0, 0, 0); 
        const tzOffset = (new Date()).getTimezoneOffset() * 60000; 
        dtInput.min = (new Date(tomorrow - tzOffset)).toISOString().slice(0, 16);
    
        dtInput.addEventListener('change', function() {
            if(!this.value) return;
            const selected = new Date(this.value); 
            const day = selected.getDay(); 
            const hour = selected.getHours(); 
            const minutes = selected.getMinutes();
            let isValid = false; 
            let errorMessage = "";
    
            if (day === 0) {
                if ((hour >= 7 && hour < 9) || (hour === 9 && minutes === 0) || (hour === 23)) { isValid = true; } 
                else { errorMessage = "On Sundays, pickup is only available between 7:00 AM - 9:00 AM, or at 11:00 PM."; }
            } else {
                if (hour >= 8 && hour < 17 || (hour === 17 && minutes === 0)) { isValid = true; } 
                else { errorMessage = "Monday-Saturday pickup is only available between 8:00 AM and 5:00 PM."; }
            }
            if (!isValid) { 
                alert(errorMessage); 
                this.value = ''; 
                hiddenDate.value = ''; 
                hiddenTime.value = ''; 
            } else { 
                const parts = this.value.split('T'); 
                hiddenDate.value = parts[0]; 
                hiddenTime.value = parts[1]; 
            }
        });
        
        // Script to close the menu when clicking the shrunken page
        document.getElementById('page-wrapper').addEventListener('click', function(e) {
            const toggle = document.getElementById('menu-toggle');
            if (toggle.checked && e.target.closest('#menu-btn') === null) {
                toggle.checked = false; 
            }
        });
    </script>
</body>
</html>