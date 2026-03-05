@extends('emails.layouts.brand')

@section('title', 'Email Configuration Successful — InfraHub')

@section('content')
    <!-- Success Icon -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td align="center" style="padding-bottom:20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td
                            style="background:linear-gradient(135deg, #059669, #10b981); width:56px; height:56px; border-radius:50%; text-align:center; line-height:56px; font-size:28px;">
                            ✓
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Heading -->
    <h1
        style="margin:0 0 8px; font-family:'Inter',sans-serif; font-size:24px; font-weight:800; color:#0f172a; text-align:center; letter-spacing:-0.3px;">
        Email Configuration Successful!
    </h1>
    <p
        style="margin:0 0 28px; font-family:'Inter',sans-serif; font-size:14px; color:#6b7280; text-align:center; line-height:1.6;">
        Your SMTP settings are working perfectly.
    </p>

    <!-- Greeting -->
    <p style="margin:0 0 16px; font-family:'Inter',sans-serif; font-size:15px; color:#334155; line-height:1.7;">
        Hello <strong>{{ $recipientName }}</strong>,
    </p>
    <p style="margin:0 0 24px; font-family:'Inter',sans-serif; font-size:14px; color:#475569; line-height:1.7;">
        This is a test email from your <strong>InfraHub</strong> platform. If you're reading this message, it confirms that
        your email system is properly configured and ready to send notifications, alerts, and reports.
    </p>

    <!-- Config details -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
        style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; margin-bottom:24px;">
        <tr>
            <td style="padding:16px 20px 12px;">
                <span
                    style="font-family:'Inter',sans-serif; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#94a3b8;">📋
                    Configuration Details</span>
            </td>
        </tr>
        <tr>
            <td style="padding:0 20px 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="padding:10px 0; border-bottom:1px solid #e2e8f0;">
                            <span style="font-family:'Inter',sans-serif; font-size:13px; color:#64748b;">SMTP Host</span>
                        </td>
                        <td style="padding:10px 0; border-bottom:1px solid #e2e8f0; text-align:right;">
                            <code
                                style="background:#e2e8f0; padding:2px 8px; border-radius:4px; font-size:13px; color:#334155;">{{ $smtpHost }}</code>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0; border-bottom:1px solid #e2e8f0;">
                            <span style="font-family:'Inter',sans-serif; font-size:13px; color:#64748b;">Port</span>
                        </td>
                        <td style="padding:10px 0; border-bottom:1px solid #e2e8f0; text-align:right;">
                            <code
                                style="background:#e2e8f0; padding:2px 8px; border-radius:4px; font-size:13px; color:#334155;">{{ $smtpPort }}</code>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0; border-bottom:1px solid #e2e8f0;">
                            <span style="font-family:'Inter',sans-serif; font-size:13px; color:#64748b;">From</span>
                        </td>
                        <td style="padding:10px 0; border-bottom:1px solid #e2e8f0; text-align:right;">
                            <span
                                style="font-family:'Inter',sans-serif; font-size:13px; font-weight:600; color:#334155;">{{ $fromAddress }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;">
                            <span style="font-family:'Inter',sans-serif; font-size:13px; color:#64748b;">Sent at</span>
                        </td>
                        <td style="padding:10px 0; text-align:right;">
                            <span
                                style="font-family:'Inter',sans-serif; font-size:13px; color:#334155;">{{ $sentAt }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Status Badge -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td align="center">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td
                            style="background:linear-gradient(135deg, #059669, #10b981); border-radius:8px; padding:12px 32px;">
                            <span style="font-family:'Inter',sans-serif; color:#ffffff; font-size:14px; font-weight:700;">✓
                                All Systems Operational</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Settings link -->
    <p
        style="margin:24px 0 0; font-family:'Inter',sans-serif; font-size:13px; color:#94a3b8; text-align:center; line-height:1.6;">
        You can manage your email settings from the
        <a href="{{ $settingsUrl }}" style="color:#4f46e5; text-decoration:none; font-weight:600;">Admin Panel → Settings →
            Email</a>
    </p>
@endsection