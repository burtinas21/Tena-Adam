<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Staff Invitation – Smart Care</title>
  <style>
    body { margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
    .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.07); }
    .header  { background: #004795; padding: 32px 40px; text-align: center; }
    .header h1 { margin: 0; color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: 0.5px; }
    .header p  { margin: 4px 0 0; color: rgba(255,255,255,0.7); font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; }
    .body    { padding: 36px 40px; }
    .body h2 { margin: 0 0 8px; font-size: 18px; color: #1e293b; font-weight: 700; }
    .body p  { margin: 0 0 16px; font-size: 14px; color: #475569; line-height: 1.6; }
    .info-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 20px; margin-bottom: 24px; }
    .info-box p { margin: 0; font-size: 13px; color: #64748b; }
    .info-box span { font-weight: 600; color: #1e293b; }
    .btn-wrap { text-align: center; margin: 28px 0; }
    .btn { display: inline-block; background: #004795; color: #ffffff !important; text-decoration: none; font-size: 14px; font-weight: 700; padding: 14px 36px; border-radius: 10px; letter-spacing: 0.3px; }
    .note  { font-size: 12px; color: #94a3b8; text-align: center; margin-top: 8px; }
    .divider { border: none; border-top: 1px solid #e2e8f0; margin: 28px 0; }
    .fallback { font-size: 12px; color: #94a3b8; word-break: break-all; }
    .footer { background: #f8fafc; padding: 20px 40px; text-align: center; font-size: 11px; color: #94a3b8; }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="header">
      <h1>Smart Care</h1>
      <p>Tena-Adam Healthcare Platform</p>
    </div>

    <div class="body">
      <h2>Welcome, {{ $user->first_name }}!</h2>
      <p>
        You have been invited to join <strong>Smart Care</strong> as a Hospital Administrator.
        To get started, click the button below to activate your account and set your password.
      </p>

      <div class="info-box">
        <p>Your login email: <span>{{ $user->email }}</span></p>
      </div>

      <div class="btn-wrap">
        <a href="{{ $activationUrl }}" class="btn">Activate My Account</a>
      </div>
      <p class="note">This invitation link expires in <strong>24 hours</strong>.</p>

      <hr class="divider" />

      <p>If the button above does not work, copy and paste the link below into your browser:</p>
      <p class="fallback">{{ $activationUrl }}</p>

      <hr class="divider" />

      <p style="margin:0; font-size:13px; color:#94a3b8;">
        If you did not expect this invitation, you can safely ignore this email.
      </p>
    </div>

    <div class="footer">
      &copy; {{ date('Y') }} Smart Care – Tena-Adam &nbsp;|&nbsp; This is an automated message, please do not reply.
    </div>
  </div>
</body>
</html>
