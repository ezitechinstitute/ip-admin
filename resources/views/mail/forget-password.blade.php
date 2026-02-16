<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Ezitech - OTP Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        /* English comments: Base styling matching your original broadcast template */
        body { margin:0; padding:0; background:#f4f7f9; font-family: 'Segoe UI', Arial, sans-serif; -webkit-text-size-adjust:100%; }
        .wrapper { max-width:600px; margin:30px auto; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 12px 30px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; }
        
        .top-accent { height: 6px; background: linear-gradient(90deg, #1a5a9e 0%, #2b78c5 100%); }
        .header { padding:40px 20px 10px; text-align:center; background: #ffffff; }
        .hero { padding:10px 25px 25px; text-align:center; }
        .content { padding:0 40px 40px; color:#334155; font-size:16px; line-height:1.8; }
        
        /* English comments: Highlighted box specifically for the OTP code */
        .otp-box { 
            margin:25px 0; background:#f8fafc; 
            border:1px solid #e2e8f0; border-left: 5px solid #1a5a9e;
            border-radius:12px; padding:20px; 
            text-align: center;
        }
        
        .otp-code {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 8px;
            color: black;
            margin: 0;
        }

        .footer { 
            background: #1e293b; 
            padding:40px 25px; text-align:center; color:#ffffff; font-size:14px; 
        }
        .footer a { color:#38bdf8; text-decoration:none; font-weight: 600; }
        
        .social-links { margin-top: 20px; font-size: 12px; color: #94a3b8; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; }
    </style>
</head>
<body>
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7f9;">
        <tr>
            <td>
                <div class="wrapper" role="article" aria-label="OTP Verification from Eziline">
                    <div class="top-accent"></div>
                    
                    <div class="header"></div>

                    <div class="hero">
                        <h1 style="margin:0; font-size:32px; line-height:1.2; color: #2b78c5; font-weight: 700; letter-spacing: -1px;">
                            Ezitech Learning Institute
                        </h1>
                    </div>

                    <div class="content">
                        <p>Hi,</p>
                        <p>You are receiving this email because a password reset request was made for your account. Please use the following <strong>One-Time Password (OTP)</strong> to complete the process:</p>

                        <div class="otp-box">
                            <p class="otp-code">{{ $otp }}</p>
                        </div>

                        <p style="color: #ef4444; font-size: 14px; font-weight: 600;">Note: This code will expire in 10 minutes.</p>
                        
                        <p>If you did not request a password reset, please ignore this email or contact support if you have concerns.</p>
                    </div>

                    <div class="footer">
                        <p style="font-size: 18px; font-weight: 700; margin-bottom: 10px; letter-spacing: 0.5px;">Eziline Software House</p>
                        <p style="margin: 5px 0; opacity: 0.9;">
                            Email: <a href="mailto:info@eziline.com">info@eziline.com</a>
                        </p>
                        <p style="margin: 5px 0; opacity: 0.9;">
                            Support: <a href="tel:+923212225212">+92 321 2225212</a>
                        </p>
                        <div class="social-links">
                            <a href="https://www.eziline.com/">Website</a> | <a href="https://www.linkedin.com/company/ezitechpk/">LinkedIn</a>
                        </div>
                        <p style="margin-top: 20px; font-size: 11px; opacity: 0.5;">&copy; 2026 Eziline. All Rights Reserved.</p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>