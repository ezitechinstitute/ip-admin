<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Ezitech - Manager Account Created</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        /* Base styling */
        body { margin:0; padding:0; background:#f4f7f9; font-family: 'Segoe UI', Arial, sans-serif; -webkit-text-size-adjust:100%; }
        .wrapper { max-width:600px; margin:30px auto; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 12px 30px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; }
        
        .top-accent { height: 6px; background: linear-gradient(90deg, #1a5a9e 0%, #2b78c5 100%); }
        .header { padding:40px 20px 10px; text-align:center; background: #ffffff; }
        .hero { padding:10px 25px 25px; text-align:center; }
        .content { padding:0 40px 40px; color:#334155; font-size:16px; line-height:1.8; }
        
        /* Box for Credentials */
        .info-box { 
            margin:25px 0; background:#f8fafc; 
            border:1px solid #e2e8f0; border-left: 5px solid #1a5a9e;
            border-radius:12px; padding:20px; 
        }
        
        .credentials-text {
            font-size: 16px;
            color: #334155;
            margin: 0;
            line-height: 1.6;
        }

        /* Button Styling */
        .button-container { text-align: center; margin: 30px 0; }
        .btn {
            background-color: #2b78c5;
            color: #ffffff;
            padding: 15px 25px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 16px;
            display: inline-block;
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
                <div class="wrapper" role="article" aria-label="Manager Account Created">
                    <div class="top-accent"></div>
                    
                    <div class="header">
                        </div>

                    <div class="hero">
                        <h1 style="margin:0; font-size:28px; line-height:1.2; color: #2b78c5; font-weight: 700; letter-spacing: -1px;">
                            EziLine Software House
                        </h1>
                    </div>

                    <div class="content">
                        <p>Hi <strong>{{ $name }}</strong>,</p>
                        <p>We are pleased to inform you that your manager account has been successfully created on the ETI Platform.</p>

                        <div class="info-box">
                            <p class="credentials-text">
                                <strong>Account Email:</strong> {{ $email }}
                            </p>
                        </div>
                        
                        <p>Please click the button below to set your password and access your dashboard:</p>

                        <div class="button-container text-white">
                           <a style="color: white" href="{{ route('auth.set.new.password', ['email' => $email]) }}" target="_blank" class="btn">
    Set Your Password
</a>
                        </div>
                        
                        <p>If you did not request this account, please contact support immediately.</p>
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