<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>{{ $title ?? 'Ezitech Notification' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        body { 
            margin: 0; 
            padding: 0; 
            background: #f4f7f9; 
            font-family: 'Segoe UI', Arial, sans-serif; 
            -webkit-text-size-adjust: 100%; 
        }
        .wrapper { 
            max-width: 600px; 
            margin: 30px auto; 
            background: #ffffff; 
            border-radius: 16px; 
            overflow: hidden; 
            box-shadow: 0 12px 30px rgba(0,0,0,0.1); 
            border: 1px solid #e2e8f0; 
        }
        .top-accent { 
            height: 6px; 
            background: linear-gradient(90deg, #1a5a9e 0%, #2b78c5 100%); 
        }
        .header { 
            padding: 40px 20px 10px; 
            text-align: center; 
            background: #ffffff; 
        }
        .hero { 
            padding: 10px 25px 25px; 
            text-align: center; 
        }
        .content { 
            padding: 0 40px 40px; 
            color: #334155; 
            font-size: 16px; 
            line-height: 1.8; 
        }
        .content h2 {
            color: #1a5a9e;
            margin: 20px 0 15px 0;
            font-size: 22px;
        }
        .content p {
            margin: 10px 0;
        }
        .message-box {
            background: #f8fafc;
            border-left: 5px solid #1a5a9e;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            color: #1e293b;
        }
        .cta-button {
            display: inline-block;
            margin: 25px 0;
            padding: 12px 30px;
            background: linear-gradient(90deg, #1a5a9e 0%, #2b78c5 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: opacity 0.3s;
        }
        .cta-button:hover {
            opacity: 0.9;
        }
        .footer { 
            background: #1e293b; 
            padding: 40px 25px; 
            text-align: center; 
            color: #ffffff; 
            font-size: 14px; 
        }
        .footer a { 
            color: #38bdf8; 
            text-decoration: none; 
            font-weight: 600; 
        }
        .social-links { 
            margin-top: 20px; 
            font-size: 12px; 
            color: #94a3b8; 
            border-top: 1px solid rgba(255,255,255,0.1); 
            padding-top: 20px; 
        }
    </style>
</head>
<body>
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7f9;">
        <tr>
            <td>
                <div class="wrapper" role="article" aria-label="Notification from Ezitech">
                    <div class="top-accent"></div>
                    
                    <div class="header"></div>

                    <div class="hero">
                        <h1 style="margin:0; font-size:32px; line-height:1.2; color: #2b78c5; font-weight: 700; letter-spacing: -1px;">
                            Ezitech Learning Institute
                        </h1>
                    </div>

                    <div class="content">
                        <p>Hello {{ $name }},</p>
                        
                        <div class="message-box">
                            {!! nl2br(e($messageBody)) !!}
                        </div>

                        @if(isset($action_url) && !empty($action_url))
                        <div style="text-align: center;">
                            <a href="{{ $action_url }}" class="cta-button">
                                Go to Portal
                            </a>
                        </div>
                        @endif

                        <p style="color: #64748b; font-size: 14px; margin-top: 30px;">
                            If you have any questions, feel free to contact our support team.
                        </p>
                    </div>

                    <div class="footer">
                        <p style="font-size: 18px; font-weight: 700; margin-bottom: 10px; letter-spacing: 0.5px;">
                            Ezitech Learning Institute
                        </p>
                        <p style="margin: 5px 0; opacity: 0.9;">
                            Email: <a href="mailto:info@ezitech.org">info@ezitech.org</a>
                        </p>
                        <p style="margin: 5px 0; opacity: 0.9;">
                            Support: <a href="tel:+923212225212">+92 321 2225212</a>
                        </p>
                        <div class="social-links">
                            <a href="https://www.ezitech.org/">Website</a> | <a href="https://www.linkedin.com/company/ezitechpk/">LinkedIn</a>
                        </div>
                        <p style="margin-top: 20px; font-size: 11px; opacity: 0.5;">&copy; 2026 Ezitech. All Rights Reserved.</p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
