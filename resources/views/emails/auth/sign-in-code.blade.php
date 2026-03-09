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

        {{-- OTP Code Block — large, monospace, with copy button --}}
        <div id="codeBlock"
            style="background:linear-gradient(135deg, #eef2ff, #e0e7ff); border:2px solid #c7d2fe; border-radius:16px; padding:28px 24px; margin:0 auto 24px; max-width:320px;">
            <div id="otpCode"
                style="font-family:'Courier New', Courier, monospace; font-size:42px; font-weight:800; letter-spacing:12px; color:#4338ca; line-height:1; user-select:all; -webkit-user-select:all; -moz-user-select:all;">
                {{ $code }}
            </div>

            {{-- Copy button --}}
            <div style="margin-top:16px;">
                <a href="#" id="copyBtn" onclick="
                            var code = '{{ $code }}';
                            if (navigator.clipboard && navigator.clipboard.writeText) {
                                navigator.clipboard.writeText(code);
                            } else {
                                var ta = document.createElement('textarea');
                                ta.value = code;
                                ta.style.position = 'fixed';
                                ta.style.opacity = '0';
                                document.body.appendChild(ta);
                                ta.select();
                                document.execCommand('copy');
                                document.body.removeChild(ta);
                            }
                            var btn = document.getElementById('copyBtn');
                            btn.innerText = '✓ Copied!';
                            btn.style.background = '#10b981';
                            btn.style.borderColor = '#059669';
                            setTimeout(function(){ btn.innerText = '📋 Copy Code'; btn.style.background = '#4f46e5'; btn.style.borderColor = '#4338ca'; }, 2000);
                            return false;
                        "
                    style="display:inline-block; padding:10px 28px; font-size:14px; font-weight:600; color:#ffffff; background:#4f46e5; border:2px solid #4338ca; border-radius:10px; text-decoration:none; font-family:'Inter',sans-serif; cursor:pointer; transition: background 0.2s;">
                    📋 Copy Code
                </a>
            </div>
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