@extends('emails.layouts.brand')

@section('title', 'Password Reset — InfraHub')

@section('content')
    <!-- Heading -->
    <h1
        style="margin:0 0 8px; font-family:'Inter',sans-serif; font-size:24px; font-weight:800; color:#0f172a; text-align:center; letter-spacing:-0.3px;">
        Reset your password
    </h1>
    <p
        style="margin:0 0 28px; font-family:'Inter',sans-serif; font-size:14px; color:#6b7280; text-align:center; line-height:1.6;">
        We received a request to reset your InfraHub password.
    </p>

    <!-- Greeting -->
    <p style="margin:0 0 16px; font-family:'Inter',sans-serif; font-size:15px; color:#334155; line-height:1.7;">
        Hello <strong>{{ $name ?? 'there' }}</strong>,
    </p>
    <p style="margin:0 0 28px; font-family:'Inter',sans-serif; font-size:14px; color:#475569; line-height:1.7;">
        Click the button below to set a new password. If you didn't request this, you can safely ignore this email.
    </p>

    <!-- CTA -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td align="center">
                <a href="{{ $resetUrl }}"
                    style="display:inline-block; padding:14px 36px; background:linear-gradient(135deg, #4f46e5, #6366f1); color:#ffffff; font-family:'Inter',sans-serif; font-size:15px; font-weight:700; border-radius:10px; text-decoration:none; box-shadow:0 4px 14px rgba(79,70,229,0.3);"
                    target="_blank">
                    🔒&nbsp; Reset Password
                </a>
            </td>
        </tr>
    </table>

    <!-- Expiry note -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
        style="margin-top:28px; background:#fef3c7; border:1px solid #fde68a; border-radius:10px;">
        <tr>
            <td style="padding:14px 20px;">
                <p style="margin:0; font-family:'Inter',sans-serif; font-size:13px; color:#92400e; line-height:1.6;">
                    <strong>⏰ This link expires in {{ $expiresIn ?? '60' }} minutes.</strong>
                    If the button doesn't work, copy and paste this URL into your browser:
                </p>
                <p
                    style="margin:8px 0 0; font-family:monospace; font-size:12px; color:#78716c; word-break:break-all; line-height:1.5;">
                    {{ $resetUrl }}
                </p>
            </td>
        </tr>
    </table>

    <p
        style="margin:24px 0 0; font-family:'Inter',sans-serif; font-size:13px; color:#94a3b8; text-align:center; line-height:1.6;">
        If you didn't request a password reset, no action is needed.
    </p>
@endsection