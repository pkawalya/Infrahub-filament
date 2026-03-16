@extends('emails.layouts.brand')

@section('title', 'Sign-In Verification')

@section('header_action')
    <span
        style="display:inline-block; padding:8px 16px; font-size:12px; font-weight:600; color:#e8a229; border:1px solid #fde68a; background:#fffbeb; border-radius:8px;">
        Security Code
    </span>
@endsection

@section('content')
    <div style="text-align:left;">
        {{-- Heading --}}
        <h1 style="font-size:24px; font-weight:800; color:#0f172a; margin:0 0 20px; line-height:1.3;">
            Login attempted from a new device or location
        </h1>

        {{-- Greeting --}}
        <p style="font-size:15px; color:#334155; margin:0 0 8px; line-height:1.6;">
            Hi <strong>{{ $userName }}</strong>,
        </p>

        <p style="font-size:15px; color:#334155; margin:0 0 8px; line-height:1.6;">
            Please confirm the login request is from you to protect your account against unauthorized access.
        </p>

        <p style="font-size:15px; color:#334155; margin:0 0 24px; line-height:1.6;">
            Simply copy and paste the temporary authentication code into the verification form.
        </p>

        {{-- OTP Code --}}
        <div
            style="border:2px solid #fde68a; background:#fffbeb; border-radius:10px; padding:20px 24px; margin:0 0 12px; display:inline-block;">
            <span
                style="font-family:'Courier New', monospace; font-size:32px; font-weight:700; letter-spacing:6px; color:#e8a229; user-select:all;">{{ $code }}</span>
        </div>

        <p style="font-size:13px; color:#64748b; margin:0 0 32px; line-height:1.5;">
            The code will expire in <strong>{{ $expiryMinutes }} minutes</strong> after the request was made.
        </p>

        {{-- Divider --}}
        <hr style="border:none; border-top:1px solid #e2e8f0; margin:0 0 24px;">

        {{-- Login context --}}
        <h3 style="font-size:15px; font-weight:700; color:#0f172a; margin:0 0 16px;">
            Account login request
        </h3>

        <table role="presentation" cellspacing="0" cellpadding="0" border="0"
            style="font-size:14px; color:#334155; line-height:1.8;">
            <tr>
                <td style="padding:4px 0; font-weight:600; color:#64748b; width:120px; vertical-align:top;">Username:</td>
                <td style="padding:4px 0;">{{ $userName }}</td>
            </tr>
            <tr>
                <td style="padding:4px 0; font-weight:600; color:#64748b; vertical-align:top;">When:</td>
                <td style="padding:4px 0;">{{ $loginTime ?? now()->format('F j, Y, H:i') }} EAT</td>
            </tr>
            @if(!empty($ipAddress))
                <tr>
                    <td style="padding:4px 0; font-weight:600; color:#64748b; vertical-align:top;">IP Address:</td>
                    <td style="padding:4px 0;">{{ $ipAddress }}</td>
                </tr>
            @endif
            @if(!empty($userAgent))
                <tr>
                    <td style="padding:4px 0; font-weight:600; color:#64748b; vertical-align:top;">Device:</td>
                    <td style="padding:4px 0;">{{ $userAgent }}</td>
                </tr>
            @endif
        </table>

        {{-- Security Note --}}
        <div style="margin-top:28px; padding-top:20px; border-top:1px solid #e2e8f0;">
            <p style="font-size:13px; color:#94a3b8; line-height:1.6; margin:0;">
                If you didn't try to sign in, someone may be attempting to access your account.
                Please change your password immediately.
            </p>
        </div>
    </div>
@endsection