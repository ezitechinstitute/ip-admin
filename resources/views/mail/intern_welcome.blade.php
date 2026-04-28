<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Welcome to Ezitech Internship Program</title>
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
            background: linear-gradient(90deg, #10b981 0%, #059669 100%); 
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
        .welcome-badge {
            display: inline-block;
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            margin: 15px 0;
        }
        .info-box {
            background: #ecfdf5;
            border-left: 5px solid #10b981;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-box strong {
            color: #059669;
        }
        .credentials-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
        }
        .credential-item {
            margin: 15px 0;
            padding: 10px;
            background: white;
            border-radius: 6px;
        }
        .credential-label {
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .credential-value {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            word-break: break-all;
        }
        .cta-button {
            display: inline-block;
            margin: 25px 0;
            padding: 12px 30px;
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: opacity 0.3s;
        }
        .cta-button:hover {
            opacity: 0.9;
        }
        .next-steps {
            background: #eff6ff;
            border-left: 5px solid #3b82f6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .next-steps h3 {
            color: #1e40af;
            margin-top: 0;
        }
        .next-steps ol {
            color: #334155;
            padding-left: 20px;
        }
        .next-steps li {
            margin: 8px 0;
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
                <div class="wrapper" role="article" aria-label="Welcome to Ezitech Internship Program">
                    <div class="top-accent"></div>
                    
                    <div class="header"></div>

                    <div class="hero">
                        <h1 style="margin:0; font-size:32px; line-height:1.2; color: #10b981; font-weight: 700; letter-spacing: -1px;">
                            Ezitech Learning Institute
                        </h1>
                        <div class="welcome-badge">Welcome Aboard! 🎉</div>
                    </div>

                    <div class="content">
                        <p>Dear <strong>{{ $name }}</strong>,</p>
                        
                        <p>Congratulations! Your registration with Ezitech Internship Program has been received successfully. We are excited to have you join our community of talented interns.</p>

                        <div class="info-box">
                            <p><strong>✓ Registration Status:</strong> Successfully Received</p>
                            <p style="margin-bottom: 0;">Our team will review your application and contact you soon to schedule your interview.</p>
                        </div>

                        <h2 style="color: #059669; margin-top: 30px;">Your Portal Credentials</h2>
                        
                        <div class="credentials-box">
                            <div class="credential-item">
                                <div class="credential-label">Your ETI ID</div>
                                <div class="credential-value">{{ $eti_id ?? 'ETI-' . $intern_id }}</div>
                            </div>
                            <div class="credential-item">
                                <div class="credential-label">Email Address</div>
                                <div class="credential-value" style="font-size: 14px;">{{ $email }}</div>
                            </div>
                            <div class="credential-item">
                                <div class="credential-label">Portal Password</div>
                                <div class="credential-value" style="font-size: 14px;">{{ $password ?? 'default_password_' . $intern_id }}</div>
                            </div>
                        </div>

                        <p style="color: #dc2626; font-weight: 600;">
                            ⚠️ Keep your credentials safe. You'll need them to log in to your portal.
                        </p>

                        <div class="next-steps">
                            <h3>What Happens Next?</h3>
                            <ol>
                                <li><strong>Review Stage:</strong> Our admissions team will review your application and qualifications.</li>
                                <li><strong>Interview Scheduling:</strong> You'll receive an email with your interview date and time.</li>
                                <li><strong>Interview:</strong> Attend your scheduled interview (online or in-person).</li>
                                <li><strong>Results:</strong> You'll be notified about the outcome within 48 hours.</li>
                            </ol>
                        </div>

                        <p>You can now log in to your portal and explore the resources available to you.</p>

                        <div style="text-align: center;">
                            <a href="{{ url('/intern/login') }}" class="cta-button">
                                Access Your Portal
                            </a>
                        </div>

                        <p style="color: #64748b; font-size: 14px; margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                            <strong>Need Help?</strong><br>
                            If you have any questions or need assistance, please don't hesitate to contact our support team at <a href="mailto:info@ezitech.org" style="color: #10b981;">info@ezitech.org</a>
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
