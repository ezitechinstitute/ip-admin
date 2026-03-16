<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $invoice->inv_id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #2563eb;
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header .rating {
            color: #fbbf24;
            font-size: 14px;
            margin-top: 5px;
        }
        .header .rating span {
            color: #666;
            margin-left: 5px;
        }
        .company-info {
            margin-bottom: 30px;
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #2563eb;
        }
        .company-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .company-address {
            line-height: 1.6;
        }
        .company-address i {
            color: #2563eb;
            margin-right: 8px;
            font-style: normal;
        }
        .company-meta {
            text-align: right;
        }
        .company-meta .badge {
            background: #2563eb;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
            margin-bottom: 10px;
        }
        .invoice-info {
            margin-bottom: 30px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .invoice-info table {
            width: 100%;
        }
        .invoice-info td {
            padding: 8px;
        }
        .invoice-info .label {
            font-weight: bold;
            width: 150px;
            color: #4a5568;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }
        table.items th {
            background: #2563eb;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        table.items td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        table.items tr:last-child td {
            border-bottom: none;
        }
        table.items tr:nth-child(even) {
            background: #f8fafc;
        }
        .summary {
            width: 100%;
            margin-bottom: 30px;
            background: #f8fafc;
            border-radius: 8px;
            padding: 20px;
        }
        .summary td {
            padding: 10px;
        }
        .summary .total-row {
            font-weight: bold;
            font-size: 16px;
            background: #e2e8f0;
            border-radius: 4px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            margin-top: 30px;
        }
        .status {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status.paid { background: #10b981; color: white; }
        .status.pending { background: #f59e0b; color: white; }
        .status.overdue { background: #ef4444; color: white; }
        .status.partial { background: #f59e0b; color: white; }
        
        .map-link {
            color: #2563eb;
            text-decoration: none;
            font-size: 11px;
            margin-left: 5px;
        }
        .contact-info {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #cbd5e1;
        }
        .contact-info span {
            margin-right: 15px;
        }
        .location-badge {
            background: #e2e8f0;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            color: #4a5568;
            display: inline-block;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>EZITECH SOFTWARE HOUSE</h1>
        <div class="rating">
            ⭐⭐⭐⭐⭒ <span>4.7 (205 reviews)</span>
        </div>
    </div>

    <div class="company-info">
        <div class="company-details">
            <div class="company-address">
                <i>📍</i> <strong>Office #304-B, Amna Plaza, Peshawar Rd,</strong><br>
                <i>🏢</i> Rawalpindi, 46000, Pakistan<br>
                <i>🕒</i> <strong>Closed</strong> · Opens 9:30 AM Mon<br>
                <div class="contact-info">
                    <span><i>📞</i> +92 337 7777860</span>
                    <span><i>🌐</i> ezitech.org</span>
                </div>
                <div class="location-badge">
                    <i>📍</i> J23G+CH Rawalpindi, Pakistan
                </div>
            </div>
            <div class="company-meta">
                <span class="badge">Software Company</span><br>
                <small style="color: #666;">
                    <i>📋</i> GST Registered<br>
                    <i>📱</i> Follow us @ezitech
                </small>
            </div>
        </div>
    </div>

    <table style="width: 100%; margin-bottom: 30px;">
        <tr>
            <td style="width: 50%;">
                <strong>INVOICE DETAILS</strong><br>
                <small style="color: #666;">Tax Invoice</small>
            </td>
            <td style="width: 50%; text-align: right;">
                <strong>INVOICE</strong><br>
                <h2 style="color: #2563eb; margin: 5px 0; font-size: 24px;">{{ $invoice->inv_id }}</h2>
                <strong>Date:</strong> {{ $invoice->created_at->format('d M Y') }}<br>
                <strong>Due Date:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}
            </td>
        </tr>
    </table>

    <div class="invoice-info">
        <table>
            <tr>
                <td class="label">Bill To:</td>
                <td><strong>{{ $invoice->name }}</strong></td>
            </tr>
            <tr>
                <td class="label">Email:</td>
                <td>{{ $invoice->intern_email }}</td>
            </tr>
            <tr>
                <td class="label">Contact:</td>
                <td>{{ $invoice->contact }}</td>
            </tr>
            <tr>
                <td class="label">Technology:</td>
                <td>{{ $invoice->technology ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Invoice Type:</td>
                <td>{{ $invoice->invoice_type }}</td>
            </tr>
        </table>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align: right;">Amount (PKR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $invoice->invoice_type }} Program</strong><br>
                    <small>{{ $invoice->technology ? 'Technology: ' . $invoice->technology : '' }}</small>
                </td>
                <td style="text-align: right;">{{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="summary">
        <tr>
            <td style="width: 70%;"><strong>Subtotal:</strong></td>
            <td style="width: 30%; text-align: right;">PKR {{ number_format($invoice->total_amount, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Paid Amount:</strong></td>
            <td style="text-align: right;">PKR {{ number_format($invoice->received_amount, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td><strong>Balance Due:</strong></td>
            <td style="text-align: right; color: {{ $invoice->remaining_amount > 0 ? '#ef4444' : '#10b981' }};">
                PKR {{ number_format($invoice->remaining_amount, 2) }}
            </td>
        </tr>
    </table>

    <div style="margin-top: 30px; padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
        <strong>Payment Status:</strong><br>
        @php
            $status = $invoice->remaining_amount <= 0 ? 'Paid' : 
                     ($invoice->due_date < now() ? 'Overdue' : 'Pending');
            $statusClass = strtolower($status);
        @endphp
        <span class="status {{ $statusClass }}">{{ $status }}</span>
        
        @if($invoice->remaining_amount > 0)
            <p style="margin-top: 15px; color: #4a5568;">
                <strong>⚠️ Please pay the remaining balance of PKR {{ number_format($invoice->remaining_amount, 2) }} before {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</strong>
            </p>
        @else
            <p style="margin-top: 15px; color: #10b981;">
                <strong>✓ Invoice fully paid. Thank you for choosing Ezitech!</strong>
            </p>
        @endif
    </div>

    @if($invoice->notes)
    <div style="margin-top: 20px; padding: 15px; background: #fff; border-radius: 8px; border: 1px solid #e2e8f0;">
        <strong>Notes:</strong>
        <p style="margin-top: 5px; color: #4a5568;">{{ $invoice->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>This is a computer generated invoice. No signature required.</p>
        <p>
            <span>🏢 Office #304-B, Amna Plaza, Peshawar Rd, Rawalpindi</span> | 
            <span>📞 +92 337 7777860</span> | 
            <span>🌐 www.ezitech.org</span>
        </p>
        <p>📱 Follow us on social media @ezitech | 🕒 Mon-Fri: 9:30 AM - 6:30 PM</p>
    </div>

</body>
</html>