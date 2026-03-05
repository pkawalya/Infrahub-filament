@extends('emails.layouts.brand')

@section('title', 'Your Verification Code — InfraHub')

@section('content')
    <!-- Heading -->
    <h1
        style="margin:0 0 8px; font-family:'Inter',sans-serif; font-size:24px; font-weight:800; color:#0f172a; text-align:center; letter-spacing:-0.3px;">
        Here is your verification code:
    </h1>

    <!-- Code Box -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin:28px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                    style="border:2px solid #e5e7eb; border-radius:12px; width:100%; max-width:360px;">
                    <tr>
                        <td align="center" style="padding:28px 24px;">
                            <span
                                style="font-family:'Inter',monospace; font-size:40px; font-weight:800; letter-spacing:10px; color:#4f46e5;">
                                {{ $code }}
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Security note -->
    <p
        style="margin:0 0 20px; font-family:'Inter',sans-serif; font-size:14px; color:#6b7280; text-align:center; line-height:1.7;">
        Please make sure you never share this code with anyone.
    </p>

    <!-- Expiration -->
    <p style="margin:0; font-family:'Inter',sans-serif; font-size:14px; color:#374151; text-align:center; line-height:1.7;">
        <strong>Note:</strong> The code will expire in {{ $expiresIn ?? '15' }} minutes.
    </p>
@endsection