<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Ezitech Internship Portal</title>
</head>

<body style="margin:0; padding:0; background:#f4f6f8; font-family: Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:20px;">
<tr>
<td align="center">

<table width="500" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:10px; overflow:hidden;">

<!-- HEADER -->
<tr>
<td style="background:#3275db; color:#fff; text-align:center; padding:20px;">
<h2 style="margin:0;">Ezitech Institute</h2>
<p style="margin:5px 0;">Welcome Aboard 🎉</p>
</td>
</tr>

<!-- BODY -->
<tr>
<td style="padding:20px; color:#333;">

<p>Dear <strong>{{ $name }}</strong>,</p>

<p>Your registration has been <strong style="color:#042d89;">successfully received</strong>.</p>

<!-- CREDENTIALS -->
<div style="background:#f1f5ff; padding:15px; border-left:4px solid #3275db; border-radius:6px; margin:15px 0;">
<p><strong>ID:</strong> {{ $eti_id }}</p>
<p><strong>Email:</strong> {{ $email }}</p>
<p><strong>Password:</strong> {{ $password }}</p>
</div>

<p style="color:red; font-size:13px;">
⚠️ Keep your credentials safe. You'll need them to log in.
</p>

<!-- WHAT HAPPENS NEXT -->
<div style="background:#eef3ff; padding:15px; border-left:4px solid #3275db; border-radius:6px; margin:20px 0;">
<h3 style="margin-top:0; color:#042d89;">What Happens Next?</h3>

<p><strong>1. Review Stage:</strong> Our team will review your application.</p>
<p><strong>2. Interview Scheduling:</strong> You'll receive an email with details.</p>
<p><strong>3. Interview:</strong> Attend online or in-person interview.</p>
<p><strong>4. Results:</strong> You'll be notified within 48 hours.</p>
</div>

<p>You can now log in to your portal and explore resources.</p>

<!-- BUTTON -->
<div style="text-align:center; margin:20px 0;">
<a href="{{ url('/intern/login') }}" style="background:#042d89; color:#fff; padding:12px 20px; text-decoration:none; border-radius:6px;">
Access Portal
</a>
</div>

</td>
</tr>

<!-- FOOTER -->
<tr>
<td style="background:#042d89; color:#fff; text-align:center; padding:15px;">
<p style="margin:5px;">help@ezitech.org</p>
<p style="margin:5px;">+92 337 7777860 </p>
<p style="margin:5px; font-size:12px;">©️ 2026 Ezitech</p>
</td>
</tr>

</table>

</td>
</tr>
</table>

</body>
</html>
