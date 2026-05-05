<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Offer Letter - Eziline Software House</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            line-height: 1.8;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #0d9394;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header h2 {
            color: #0d9394;
            margin: 0;
            font-size: 22px;
        }
        .header p {
            margin: 3px 0;
            font-size: 13px;
            color: #666;
        }
        .title {
            text-align: center;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 20px 0;
            font-size: 18px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table td {
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }
        table td:first-child {
            font-weight: bold;
            color: #555;
            width: 35%;
        }
        .signature {
            margin-top: 60px;
            border-top: 2px solid #0d9394;
            padding-top: 20px;
        }
        .signature .left {
            float: left;
            width: 50%;
        }
        .signature .right {
            float: right;
            width: 50%;
            text-align: right;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 11px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>EZILINE SOFTWARE HOUSE</h2>
        <p>Amna Plaza, Near Radio Pakistan, Rawalpindi</p>
        <p>www.eziline.com | hr@eziline.com</p>
    </div>

    <div class="title">Internship Offer Letter</div>

    <p>Dear <strong>{{ $offerLetter->username ?? 'Intern' }}</strong>,</p>
    <p>Congratulations! We are pleased to offer you an internship position at <strong>Eziline Software House</strong>.</p>
    <p>This is to confirm your selection for the internship program. We believe your skills and enthusiasm will be a valuable addition to our team.</p>

    <table>
        <tr>
            <td>Offer Letter ID</td>
            <td>{{ $offerLetter->offer_letter_id }}</td>
        </tr>
        <tr>
            <td>Name</td>
            <td>{{ $offerLetter->username ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Email</td>
            <td>{{ $offerLetter->email ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Technology</td>
            <td>{{ $offerLetter->tech ?? 'Not Assigned' }}</td>
        </tr>
        <tr>
            <td>Duration</td>
            <td>3 Months</td>
        </tr>
        <tr>
            <td>Start Date</td>
            <td>{{ isset($offerLetter->created_at) ? date('F d, Y', strtotime($offerLetter->created_at)) : date('F d, Y') }}</td>
        </tr>
    </table>

    <p>We look forward to a productive and mutually beneficial association. Should you have any questions, please don't hesitate to contact our HR department.</p>

    <div class="signature clearfix">
        <div class="left">
            <p>Offer ID: {{ $offerLetter->offer_letter_id }}</p>
            <p>Date: {{ date('F d, Y') }}</p>
        </div>
        <div class="right">
            <p>____________________</p>
            <p><strong>Authorized Signature</strong></p>
            <p>HR Department</p>
        </div>
    </div>

    <div class="footer">
        This is a system-generated document from Eziline I-Portal | {{ date('Y-m-d H:i:s') }}
    </div>

</body>
</html>