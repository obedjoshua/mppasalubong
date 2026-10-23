@extends('layouts.staff')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/staff/scanner.css') }}">
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card scanner-card p-4 border-0 shadow-sm">
            <h2 class="h4 fw-bold mb-1">Counter Camera Scanner</h2>
            <p class="text-muted small mb-4">Scan customer ticket QR code or manually input the token.</p>

            <!-- Video Stream Viewport -->
            <div class="camera-viewport mb-4">
                <div id="reader"></div>
            </div>

            <!-- Manual Input Group -->
            <div class="input-group mb-3">
                <input type="text" id="manual_ticket_id" class="form-control" placeholder="Enter Ticket Token (e.g. cpym25fmjJ4)...">
                <button onclick="verifyTicket(document.getElementById('manual_ticket_id').value)" class="btn btn-primary fw-bold" type="button">
                    Verify
                </button>
            </div>

            <!-- Output Verification Container -->
            <div id="scan_result" class="d-none alert"></div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrCode = new Html5Qrcode("reader");

    function onScanSuccess(decodedText) {
        let ticketId = decodedText.includes('ticket=') 
            ? new URL(decodedText).searchParams.get('ticket') 
            : decodedText;

        html5QrCode.pause();
        verifyTicket(ticketId);
    }

    function verifyTicket(ticketId) {
        if (!ticketId) return alert('Please enter or scan a valid Ticket ID.');

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
            let container = document.getElementById('scan_result');
            container.classList.remove('d-none', 'alert-success', 'alert-danger');

            if (data.success) {
                container.classList.add('alert-success');
                container.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="fw-bold mb-0">Valid Ticket: #${data.ticket.id}</h5>
                        <span class="badge bg-success">${data.ticket.status}</span>
                    </div>
                    <p class="mb-1 small"><strong>Customer:</strong> ${data.ticket.customer_name}</p>
                    <p class="mb-1 small"><strong>Branch:</strong> ${data.ticket.pickup_branch}</p>
                    <p class="fw-bold h5 mt-2">Total Quote: ₱${parseFloat(data.ticket.total_price).toFixed(2)}</p>
                    <button onclick="completeHandoff('${data.ticket.id}')" class="btn btn-success w-100 mt-3 fw-bold">
                        Complete Pickup Handoff
                    </button>
                `;
            } else {
                container.classList.add('alert-danger');
                container.innerHTML = `
                    <h5 class="fw-bold">Verification Failed</h5>
                    <p class="small mb-2">${data.message}</p>
                    <button onclick="resumeScanner()" class="btn btn-sm btn-secondary">Try Again</button>
                `;
            }
        });
    }

    function resumeScanner() {
        document.getElementById('scan_result').classList.add('d-none');
        html5QrCode.resume();
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

    html5QrCode.start({ facingMode: "environment" }, { fps: 10, qrbox: { width: 220, height: 220 } }, onScanSuccess);
</script>
@endsection