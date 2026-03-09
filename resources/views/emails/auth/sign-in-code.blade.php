@extends('emails.layouts.brand')

@section('title', 'Your Sign-In Code')

@section('header_action')
    <span
        style="display:inline-block; padding:8px 16px; font-size:12px; font-weight:600; color:#6b7280; border:1px solid #e5e7eb; border-radius:8px; font-family:'Inter',sans-serif;">
        🔐 Security Code
    </span>
@endsection

@section('content')
    <div style="text-align:center;">
        {{-- Greeting --}}
        <h2 style="font-family:'Inter',sans-serif; font-size:22px; font-weight:700; color:#1f2937; margin:0 0 8px;">
            Your Sign-In Code
        </h2>
        <p style="font-size:14px; color:#6b7280; margin:0 0 28px; line-height:1.5;">
            Hi <strong>{{ $userName }}</strong>, use this code to complete your sign-in:
        </p>

        {{-- OTP Code Block — large, monospace, tap-to-select --}}
        <div
            style="background:linear-gradient(135deg, #eef2ff, #e0e7ff); border:2px solid #c7d2fe; border-radius:16px; padding:28px 24px 20px; margin:0 auto 24px; max-width:320px; cursor:pointer;">
            <div
                style="font-family:'Courier New', Courier, monospace; font-size:42px; font-weight:800; letter-spacing:12px; color:#4338ca; line-height:1; user-select:all; -webkit-user-select:all; -moz-user-select:all; padding:8px; background:#ffffff; border-radius:10px; border:1px solid #e0e7ff;">
                {{ $code }}
            </div>
            <p style="font-size:12px; color:#6366f1; margin:14px 0 0; font-weight:600;">
                👆 Tap the code to select it, then copy
            </p>
        </div>

        {{-- Expiry Warning --}}
        <div
            style="background:#fef3c7; border:1px solid #fde68a; border-radius:10px; padding:12px 20px; margin:0 auto 24px; max-width:380px;">
            <p style="color:#92400e; margin:0; font-size:13px; line-height:1.5;">
                ⏰ This code expires in <strong>{{ $expiryMinutes }} minutes</strong>.
                Do not share it with anyone.
            </p>
        </div>

        {{-- Security Note --}}
        <div style="border-top:1px solid #f3f4f6; padding-top:20px; margin-top:8px;">
            <p style="font-size:12px; color:#9ca3af; line-height:1.6; max-width:380px; margin:0 auto;">
                🛡️ If you didn't try to sign in, someone may be attempting to access your account.
                Please change your password immediately.
            </p>
        </div>
    </div>
@endsection