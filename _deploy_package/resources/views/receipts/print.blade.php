<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $contribution->receipt_number ?? 'Pending' }}</title>
    <style>
        @page { margin: 20mm; }
        body { font-family: Arial, sans-serif; color: #111827; background: #f9fafb; }
        .receipt { max-width: 720px; margin: 0 auto; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; }
        .header { display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e5e7eb; padding-bottom: 16px; margin-bottom: 16px; }
        .brand { display: flex; align-items: center; gap: 12px; }
        .brand img { width: 48px; height: 48px; object-fit: contain; }
        .title { font-size: 20px; font-weight: 700; }
        .section { margin-bottom: 16px; }
        .label { color: #6b7280; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; }
        .value { font-size: 14px; margin-top: 4px; }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .total { font-size: 18px; font-weight: 700; color: #047857; }
        .footer { border-top: 1px dashed #e5e7eb; padding-top: 16px; margin-top: 16px; font-size: 12px; color: #6b7280; }
        .actions { text-align: right; margin-bottom: 16px; }
        .btn { background: #16a34a; color: white; border: none; padding: 10px 16px; border-radius: 6px; cursor: pointer; font-size: 14px; }
        @media print {
            body { background: white; }
            .actions { display: none; }
            .receipt { border: none; box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button class="btn" onclick="window.print()">Print Receipt</button>
    </div>

    <div class="receipt">
        <div class="header">
            <div class="brand">
                <img src="{{ asset('images/logos/jces-logo.png') }}" alt="JCES Logo">
                <div>
                    <div class="title">JCES-PTA Receipt</div>
                    <div style="font-size: 12px; color: #6b7280;">J. Cruz Sr. Elementary School</div>
                </div>
            </div>
            <div style="text-align: right;">
                <div class="label">Receipt No.</div>
                <div class="value" style="font-weight: 600;">{{ $contribution->receipt_number ?? 'Pending' }}</div>
            </div>
        </div>

        <div class="grid section">
            <div>
                <div class="label">Parent</div>
                <div class="value">{{ $contribution->parent ? $contribution->parent->first_name . ' ' . $contribution->parent->last_name : 'N/A' }}</div>
            </div>
            <div>
                <div class="label">Date</div>
                <div class="value">{{ optional($contribution->contribution_date)->format('F d, Y') }}</div>
            </div>
            <div>
                <div class="label">Project</div>
                <div class="value">{{ $contribution->project?->project_name ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="label">Payment Method</div>
                <div class="value">{{ ucfirst(str_replace('_', ' ', $contribution->payment_method)) }}</div>
            </div>
        </div>

        <div class="section">
            <div class="label">Amount</div>
            <div class="value total">₱{{ number_format($contribution->contribution_amount, 2) }}</div>
        </div>

        <div class="section">
            <div class="label">Status</div>
            <div class="value">{{ ucfirst($contribution->payment_status) }}</div>
        </div>

        @if(!empty($contribution->notes))
            <div class="section">
                <div class="label">Notes</div>
                <div class="value">{{ $contribution->notes }}</div>
            </div>
        @endif

        <div class="footer">
            <div>Processed by: {{ $contribution->processedBy?->name ?? 'Pending Verification' }}</div>
            <div>Thank you for supporting our PTA projects.</div>
        </div>
    </div>
</body>
</html>
