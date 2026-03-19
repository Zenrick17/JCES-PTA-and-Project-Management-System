<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Approved</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.5; color: #111827;">
    <p style="margin: 0 0 16px;">
        Hi {{ trim(($contribution->parent?->first_name ?? '') . ' ' . ($contribution->parent?->last_name ?? '')) ?: 'Parent' }},
    </p>

    <p style="margin: 0 0 16px;">
        Your payment has been approved. Here are the details:
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 560px; border-collapse: collapse;">
        <tr>
            <td style="padding: 6px 0; font-weight: bold;">Project</td>
            <td style="padding: 6px 0;">{{ $contribution->project?->project_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; font-weight: bold;">Amount</td>
            <td style="padding: 6px 0;">₱{{ number_format((float) $contribution->contribution_amount, 2) }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; font-weight: bold;">Payment Method</td>
            <td style="padding: 6px 0;">{{ strtoupper($contribution->payment_method ?? 'N/A') }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; font-weight: bold;">Receipt #</td>
            <td style="padding: 6px 0;">{{ $contribution->receipt_number ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; font-weight: bold;">Date</td>
            <td style="padding: 6px 0;">{{ optional($contribution->contribution_date)->format('M d, Y') ?? 'N/A' }}</td>
        </tr>
    </table>

    <p style="margin: 16px 0 0;">
        Thank you for your support.
    </p>
</body>
</html>
