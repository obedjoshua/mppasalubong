@extends('layouts.staff')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/staff/scanner.css') }}">
    <style>
        /* Camera Viewport Container */
        .scanner-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            background: #111;
            min-height: 280px;
        }

        /* Subtle Scanning Laser Line */
        .laser-line {
            position: absolute;
            top: 0;
            left: 5%;
            width: 90%;
            height: 3px;
            background: linear-gradient(90deg, transparent, #0d6efd, #0dcaf0, #0d6efd, transparent);
            box-shadow: 0 0 10px #0dcaf0;
            border-radius: 50%;
            z-index: 10;
            animation: scanLaser 2.2s ease-in-out infinite alternate;
            pointer-events: none;
        }

        @keyframes scanLaser {
            0% { top: 12%; opacity: 0.3; }
            50% { opacity: 0.9; }
            100% { top: 88%; opacity: 0.3; }
        }

        /* Prevent Modal Backdrop Click Lock & Layout Shifting */
        body.modal-open {
            padding-right: 0 !important;
            overflow: auto !important;
        }
        
        .modal-backdrop {
            z-index: 1040 !important;
        }

        #ticketModal {
            z-index: 1055 !important;
        }

        #ticketModal .modal-content {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
        }

        #ticketModal .modal-body {
            color: #212529;
        }
    </style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card scanner-card p-4 border-0 shadow-sm">
            <h2 class="h4 fw-bold mb-1">Counter Camera Scanner</h2>
            <p class="text-muted small mb-4">Scan customer ticket QR code or manually input the token.</p>

            <!-- Video Stream Viewport -->
            <div class="scanner-wrapper mb-4">
                <div class="laser-line" id="laserLine"></div>
                <div id="reader"></div>
            </div>

            <!-- Manual Input Group -->
            <div class="input-group mb-3">
                <input type="text" id="manual_ticket_id" class="form-control" placeholder="Enter Ticket Token (e.g. cpym25fmjJ4)...">
                <button onclick="verifyTicket(document.getElementById('manual_ticket_id').value)" class="btn btn-primary fw-bold" type="button">
                    Verify
                </button>
            </div>

            <!-- Output Verification Alert Fallback -->
            <div id="scan_result" class="d-none alert"></div>
        </div>
    </div>
</div>

<!-- Ticket Handoff & Payment Action Modal -->
<div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="ticketModalLabel">Ticket Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="resumeScanner()"></button>
            </div>
            <div class="modal-body p-4" id="ticketModalBody">
                <!-- Dynamically Populated by JS -->
            </div>
            <div class="modal-footer bg-light flex-wrap gap-2 justify-content-between" id="ticketModalFooter">
                <!-- Action Buttons Rendered Here -->
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrCode = null;
    let isProcessing = false;
    let ticketModalInstance = null;

    function initScanner() {
        const readerElement = document.getElementById("reader");
        if (!readerElement) return;

        // Move modal to <body> root to eliminate click lock & layout jump
        const modalElement = document.getElementById('ticketModal');
        if (modalElement && modalElement.parentNode !== document.body) {
            document.body.appendChild(modalElement);
        }

        ticketModalInstance = new bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: false
        });

        html5QrCode = new Html5Qrcode("reader", {
            formatsToSupport: [ Html5QrcodeSupportedFormats.QR_CODE ],
            verbose: false
        });

        const config = {
            fps: 25,
            qrbox: (viewfinderWidth, viewfinderHeight) => {
                const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                return { width: Math.floor(minEdge * 0.75), height: Math.floor(minEdge * 0.75) };
            },
            aspectRatio: 1.333333
        };

        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length) {
                const cameraId = devices[0].id;
                html5QrCode.start(cameraId, config, onScanSuccess, () => {});
            }
        }).catch(err => {
            html5QrCode.start({ facingMode: "user" }, config, onScanSuccess, () => {});
        });
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initScanner);
    } else {
        initScanner();
    }

    function onScanSuccess(decodedText) {
        if (isProcessing) return;

        isProcessing = true;
        if (html5QrCode) html5QrCode.pause();
        document.getElementById('laserLine').style.display = 'none';
        
        verifyTicket(decodedText);
    }

    function verifyTicket(ticketId) {
        if (!ticketId) {
            isProcessing = false;
            document.getElementById('laserLine').style.display = 'block';
            return alert('Please enter or scan a valid Ticket ID.');
        }

        fetch("{{ route('staff.qr.verify') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ ticket_id: ticketId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showTicketModal(data.ticket);
            } else {
                let container = document.getElementById('scan_result');
                container.classList.remove('d-none', 'alert-success');
                container.classList.add('alert-danger');
                container.innerHTML = `
                    <h5 class="fw-bold">Verification Failed</h5>
                    <p class="small mb-2">${data.message}</p>
                    <button onclick="resumeScanner()" class="btn btn-sm btn-secondary">Try Again</button>
                `;
            }
        })
        .catch(err => {
            console.error(err);
            resumeScanner();
        });
    }

    function showTicketModal(ticket) {
        const body = document.getElementById('ticketModalBody');
        const footer = document.getElementById('ticketModalFooter');

        const itemsList = ticket.items && ticket.items.length 
            ? ticket.items.map(i => `<li class="small fw-semibold">${i.name || i.title || 'Item'} x${i.quantity || 1} - ₱${parseFloat(i.price || 0).toFixed(2)}</li>`).join('')
            : '<li class="small text-muted">No line items specified.</li>';

        // Check if ticket status is approved/in-production to show deposit
        const isApproved = ['Approved', 'In Production', 'Ready for Pickup', 'Completed'].includes(ticket.status);
        const depositVal = parseFloat(ticket.deposit_required || 0);

        // Conditional rendering for Deposit Required
        const depositBlock = (isApproved && depositVal > 0) ? `
            <div>
                <div class="small text-muted fw-bold">Deposit Required</div>
                <div class="fw-bold text-primary fs-5">₱${depositVal.toFixed(2)}</div>
            </div>
        ` : '';

        // Formatted Date
        let scheduleText = 'N/A';
        if (ticket.pickup_date) {
            try {
                const dateObj = new Date(ticket.pickup_date);
                scheduleText = dateObj.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) + 
                    (ticket.pickup_time ? ` at ${ticket.pickup_time}` : '');
            } catch (e) {
                scheduleText = `${ticket.pickup_date} ${ticket.pickup_time || ''}`;
            }
        }

        body.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold text-dark mb-0">Ticket #${ticket.id}</h4>
                <span class="badge bg-${ticket.status === 'Completed' ? 'secondary' : 'success'} fs-6">${ticket.status}</span>
            </div>
            
            <div class="bg-light p-3 rounded-3 mb-3 border">
                <div class="row g-2">
                    <div class="col-6"><strong>Customer Name:</strong> ${ticket.customer_name || 'N/A'}</div>
                    <div class="col-6"><strong>Phone:</strong> ${ticket.customer_phone || 'N/A'}</div>
                    <div class="col-6"><strong>Pickup Branch:</strong> ${ticket.pickup_branch || 'N/A'}</div>
                    <div class="col-6"><strong>Schedule:</strong> ${scheduleText}</div>
                </div>
            </div>

            <div class="mb-3">
                <h6 class="fw-bold text-dark mb-2">Order Items:</h6>
                <ul class="mb-0 ps-3">
                    ${itemsList}
                </ul>
            </div>

            <hr>

            <div class="d-flex justify-content-between align-items-center">
                ${depositBlock}
                <div class="text-end ms-auto">
                    <div class="small text-muted fw-bold">Total Quote</div>
                    <div class="fw-bold h3 mb-0 text-success">₱${parseFloat(ticket.total_price || 0).toFixed(2)}</div>
                </div>
            </div>
        `;

        // Direct URL pointing to /staff/tickets/{id}
        const ticketDetailUrl = `/staff/tickets/${ticket.id}`;

        footer.innerHTML = `
            <div class="d-flex gap-2 w-100 flex-wrap">
                ${(isApproved && depositVal > 0) ? `
                    <button onclick="updatePaymentStatus('${ticket.id}', 'DP Paid')" class="btn btn-outline-warning flex-fill fw-bold">
                        Mark DP Paid
                    </button>
                ` : ''}
                <button onclick="updatePaymentStatus('${ticket.id}', 'Paid Full')" class="btn btn-outline-success flex-fill fw-bold">
                    Mark Full Paid
                </button>
            </div>
            
            <div class="d-flex gap-2 w-100 mt-2">
                <a href="${ticketDetailUrl}" target="_blank" class="btn btn-outline-primary fw-bold flex-fill">
                    View Full Ticket
                </a>
                <button onclick="resumeScanner()" class="btn btn-secondary fw-bold flex-fill">
                    Scan Next Ticket
                </button>
                <button onclick="completeHandoff('${ticket.id}')" class="btn btn-success fw-bold flex-fill">
                    Complete Handoff
                </button>
            </div>
        `;

        ticketModalInstance.show();
    }

    function updatePaymentStatus(ticketId, statusType) {
        fetch("{{ route('staff.qr.complete') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ ticket_id: ticketId, payment_status: statusType })
        })
        .then(res => res.json())
        .then(data => {
            alert(`Payment updated: Marked as ${statusType}`);
        });
    }

    function resumeScanner() {
        document.getElementById('scan_result').classList.add('d-none');
        document.getElementById('laserLine').style.display = 'block';
        isProcessing = false;
        if (ticketModalInstance) ticketModalInstance.hide();
        if (html5QrCode) html5QrCode.resume();
    }

    function completeHandoff(ticketId) {
        fetch("{{ route('staff.qr.complete') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ ticket_id: ticketId })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            location.reload();
        });
    }
</script>
@endsection