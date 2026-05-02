<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->inv_id }} - EZITECH</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0f766e;
            --primary-light: #14b8a6;
            --primary-dark: #115e59;
            --primary-bg: #f0fdfa;
            --text: #1e293b;
            --text-light: #64748b;
            --border: #e2e8f0;
            --border-light: #f1f5f9;
            --danger: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
            --purple: #8b5cf6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f8fafc;
            color: var(--text);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ============ WATERMARK ============ */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 120px;
            font-weight: 900;
            color: rgba(15, 118, 110, 0.03);
            pointer-events: none;
            z-index: 0;
            white-space: nowrap;
            letter-spacing: 10px;
        }

        /* ============ CONTAINER ============ */
        .invoice-wrapper {
            max-width: 800px;
            margin: 30px auto;
            padding: 0 15px;
            position: relative;
            z-index: 1;
        }

        .invoice-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 8px 24px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        /* ============ TOP BAR ============ */
        .invoice-top-bar {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 50%, var(--primary-light) 100%);
            padding: 12px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        .invoice-top-bar .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            animation: pulse 2s infinite;
        }

        .dot-active { background: #34d399; }
        .dot-overdue { background: #fbbf24; animation: pulse 1s infinite; }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        /* ============ HEADER ============ */
        .invoice-header {
            padding: 35px 40px 25px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px solid var(--border-light);
        }

        .brand-section .logo-text {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -0.5px;
            line-height: 1;
        }

        .brand-section .tagline {
            font-size: 11px;
            color: var(--text-light);
            margin-top: 4px;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: 500;
        }

        .invoice-badge {
            text-align: right;
        }

        .invoice-badge .badge-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 4px;
        }

        .invoice-badge .badge-number {
            font-size: 22px;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -0.3px;
        }

        .invoice-badge .badge-date {
            font-size: 12px;
            color: var(--text-light);
            margin-top: 2px;
        }

        /* ============ STATUS PILL ============ */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-top: 8px;
        }

        .status-pill .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .status-paid { background: #ecfdf5; color: #059669; }
        .status-paid .status-dot { background: #10b981; }

        .status-pending { background: #fffbeb; color: #d97706; }
        .status-pending .status-dot { background: #f59e0b; }

        .status-overdue { background: #fef2f2; color: #dc2626; }
        .status-overdue .status-dot { background: #ef4444; }

        .status-partial { background: #f5f3ff; color: #7c3aed; }
        .status-partial .status-dot { background: #8b5cf6; }

        /* ============ INFO GRID ============ */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            padding: 30px 40px;
            border-bottom: 1px solid var(--border-light);
        }

        .info-section .section-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 2px solid var(--primary-bg);
        }

        .info-section .section-content p {
            font-size: 13px;
            margin-bottom: 3px;
            color: var(--text);
        }

        .info-section .section-content .name {
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
        }

        .info-section .section-content .email {
            color: var(--text-light);
            font-size: 12px;
        }

        /* ============ TABLE ============ */
        .table-section {
            padding: 25px 40px;
        }

        .table-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 15px;
        }

        .invoice-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 13px;
        }

        .invoice-table thead th {
            background: var(--primary-bg);
            color: var(--primary-dark);
            padding: 10px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #ccfbf1;
        }

        .invoice-table thead th:last-child,
        .invoice-table tbody td:last-child {
            text-align: right;
        }

        .invoice-table tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-light);
            vertical-align: top;
        }

        .invoice-table tbody tr:last-child td {
            border-bottom: none;
        }

        .item-description {
            font-weight: 500;
            color: var(--text);
        }

        .item-meta {
            font-size: 11px;
            color: var(--text-light);
            margin-top: 2px;
        }

        .amount-cell {
            font-weight: 600;
            font-variant-numeric: tabular-nums;
            font-family: 'Inter', monospace;
        }

        /* ============ SUMMARY ============ */
        .summary-section {
            margin-top: 5px;
            padding: 20px 40px;
            border-top: 1px solid var(--border);
            background: #fafafa;
            display: flex;
            justify-content: flex-end;
        }

        .summary-box {
            width: 280px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            font-size: 13px;
        }

        .summary-row.total {
            font-size: 17px;
            font-weight: 700;
            color: var(--text);
            padding-top: 10px;
            margin-top: 8px;
            border-top: 2px solid var(--border);
        }

        .summary-row.paid {
            color: #059669;
        }

        .summary-row.balance {
            color: var(--danger);
            font-weight: 600;
        }

        .summary-label {
            color: var(--text-light);
        }

        .summary-amount {
            font-weight: 600;
            font-variant-numeric: tabular-nums;
        }

        /* ============ AMOUNT IN WORDS ============ */
        .words-section {
            padding: 0 40px 20px;
            background: #fafafa;
        }

        .words-box {
            background: white;
            border: 1px dashed var(--border);
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 12px;
            color: var(--text-light);
            font-style: italic;
        }

        .words-box strong {
            color: var(--primary-dark);
            font-style: normal;
        }

        /* ============ FOOTER ============ */
        .invoice-footer {
            padding: 25px 40px;
            border-top: 1px solid var(--border-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            color: var(--text-light);
        }

        .footer-left {
            line-height: 1.8;
        }

        .footer-left strong {
            color: var(--primary);
        }

        .footer-right {
            text-align: right;
            line-height: 1.8;
        }

        .footer-right .certified {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--primary-bg);
            color: var(--primary-dark);
            padding: 6px 14px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 10px;
            letter-spacing: 0.5px;
            margin-top: 8px;
        }

        /* ============ ACTION BAR ============ */
        .action-bar {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 11px 28px;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 2px 8px rgba(15, 118, 110, 0.3);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(15, 118, 110, 0.4);
        }

        .btn-outline {
            background: white;
            color: var(--primary);
            border: 1.5px solid var(--border);
        }

        .btn-outline:hover {
            border-color: var(--primary);
            background: var(--primary-bg);
        }

        .btn-icon {
            font-size: 16px;
        }

        /* ============ PRINT STYLES ============ */
        @media print {
            body {
                background: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .invoice-wrapper {
                margin: 0;
                padding: 0;
                max-width: 100%;
            }

            .invoice-card {
                box-shadow: none;
                border-radius: 0;
            }

            .action-bar,
            .no-print {
                display: none !important;
            }

            .invoice-top-bar {
                background: var(--primary-dark) !important;
            }

            .summary-section,
            .words-section {
                background: #fafafa !important;
            }

            @page {
                size: A4;
                margin: 12mm;
            }
        }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 600px) {
            .invoice-header {
                flex-direction: column;
                gap: 20px;
            }

            .invoice-badge {
                text-align: left;
            }

            .info-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .summary-box {
                width: 100%;
            }

            .invoice-footer {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .footer-right {
                text-align: center;
            }
        }
    </style>
</head>
<body>

    <!-- Watermark -->
    <div class="watermark">EZITECH</div>

    <!-- Invoice Container -->
    <div class="invoice-wrapper">

        <!-- Invoice Card -->
        <div class="invoice-card">

            <!-- Top Status Bar -->
            <div class="invoice-top-bar no-print">
                @php
                    $isOverdue = $invoice->due_date && strtotime($invoice->due_date) < time() && $invoice->remaining_amount > 0;
                    $isPaid = $invoice->remaining_amount <= 0;
                @endphp
                <span>
                    <span class="dot {{ $isOverdue ? 'dot-overdue' : 'dot-active' }}"></span>
                    {{ $isPaid ? 'Invoice Paid' : ($isOverdue ? 'Payment Overdue' : 'Invoice Active') }}
                </span>
                <span>Generated: {{ date('d M Y, h:i A') }}</span>
            </div>

            <!-- Header -->
            <div class="invoice-header">
                <div class="brand-section">
                    <div class="logo-text">EZITECH</div>
                    <div class="tagline">Learning Institute</div>
                </div>
                <div class="invoice-badge">
                    <div class="badge-title">Invoice</div>
                    <div class="badge-number">{{ $invoice->inv_id }}</div>
                    <div class="badge-date">{{ date('d M Y', strtotime($invoice->created_at)) }}</div>
                    @php
                        $statusClass = 'status-pending';
                        $statusText = 'Pending';
                        if ($invoice->remaining_amount <= 0) {
                            $statusClass = 'status-paid';
                            $statusText = 'Paid';
                        } elseif ($invoice->received_amount > 0) {
                            $statusClass = 'status-partial';
                            $statusText = 'Partial';
                        } elseif ($isOverdue) {
                            $statusClass = 'status-overdue';
                            $statusText = 'Overdue';
                        }
                    @endphp
                    <div class="status-pill {{ $statusClass }}">
                        <span class="status-dot"></span> {{ $statusText }}
                    </div>
                </div>
            </div>

            <!-- Info Grid -->
            <div class="info-grid">
                <div class="info-section">
                    <div class="section-label">Bill To</div>
                    <div class="section-content">
                        <p class="name">{{ $invoice->name }}</p>
                        <p class="email">{{ $invoice->intern_email }}</p>
                        @if($invoice->contact)
                            <p style="font-size:12px;">📱 {{ $invoice->contact }}</p>
                        @endif
                    </div>
                </div>
                <div class="info-section">
                    <div class="section-label">Invoice Details</div>
                    <div class="section-content">
                        <p><strong>Invoice #:</strong> {{ $invoice->inv_id }}</p>
                        <p><strong>Issue Date:</strong> {{ date('d M Y', strtotime($invoice->created_at)) }}</p>
                        <p><strong>Due Date:</strong> {{ $invoice->due_date ? date('d M Y', strtotime($invoice->due_date)) : 'N/A' }}</p>
                        @if($invoice->received_by)
                            <p><strong>Received By:</strong> {{ $invoice->received_by }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-section">
                <div class="table-title">Invoice Items</div>
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="item-description">{{ ucfirst($invoice->invoice_type ?? 'Internship') }} Fee</div>
                                <div class="item-meta">One-time payment</div>
                            </td>
                            <td class="amount-cell">PKR {{ number_format($invoice->total_amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Summary -->
            <div class="summary-section">
                <div class="summary-box">
                    <div class="summary-row">
                        <span class="summary-label">Subtotal</span>
                        <span class="summary-amount">PKR {{ number_format($invoice->total_amount, 2) }}</span>
                    </div>
                    @if($invoice->received_amount > 0)
                    <div class="summary-row paid">
                        <span>Paid</span>
                        <span>- PKR {{ number_format($invoice->received_amount, 2) }}</span>
                    </div>
                    @endif
                    @if($invoice->remaining_amount > 0)
                    <div class="summary-row balance">
                        <span>Balance Due</span>
                        <span>PKR {{ number_format($invoice->remaining_amount, 2) }}</span>
                    </div>
                    @endif
                    <div class="summary-row total">
                        <span>{{ $invoice->remaining_amount > 0 ? 'Total Due' : 'Total Paid' }}</span>
                        <span>PKR {{ number_format($invoice->remaining_amount > 0 ? $invoice->remaining_amount : $invoice->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Amount in Words -->
<div class="words-section">
    <div class="words-box">
        💬 <strong>Amount in Words:</strong> 
        {{ \App\Helpers\NumberToWords::convert($invoice->total_amount) }} Rupees Only
        @if($invoice->remaining_amount > 0)
            <br>💬 <strong>Balance in Words:</strong> 
            {{ \App\Helpers\NumberToWords::convert($invoice->remaining_amount) }} Rupees Only
        @endif
    </div>
</div>

            <!-- Footer -->
            <div class="invoice-footer">
                <div class="footer-left">
                    <strong>EZITECH Learning Institute</strong><br>
                    Empowering Through Technology & Education<br>
                    info@ezitech.org
                </div>
                <div class="footer-right">
                    <span class="certified">
                        ✅ Computer Generated Invoice
                    </span><br>
                    <span style="font-size:10px;">No physical signature required</span>
                </div>
            </div>

        </div>

        <!-- Action Buttons -->
        <div class="action-bar no-print">
         <a href="{{ route('invoice-page') }}" class="btn btn-outline">
    <span class="btn-icon">←</span> Back to Invoices
</a>
            <button class="btn btn-outline" onclick="window.location.reload()">
                <span class="btn-icon">🔄</span> Refresh
            </button>
            <button class="btn btn-primary" onclick="window.print()">
                <span class="btn-icon">🖨️</span> Print Invoice
            </button>
        </div>

    </div>

    <!-- Auto-print trigger -->
    <script>
        (function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('autoPrint') === '1') {
                window.addEventListener('load', function() {
                    setTimeout(function() {
                        window.print();
                    }, 800);
                });
            }
        })();
    </script>

</body>
</html>