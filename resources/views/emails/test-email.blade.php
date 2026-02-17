<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InfraHub - Test Email</title>
</head>

<body
    style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
        style="background-color: #f1f5f9;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600"
                    style="max-width: 600px; width: 100%;">

                    <!-- Logo Header -->
                    <tr>
                        <td align="center" style="padding-bottom: 24px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td
                                        style="background: linear-gradient(135deg, #f97316, #ea580c); border-radius: 12px; padding: 12px 24px;">
                                        <span
                                            style="color: #ffffff; font-size: 24px; font-weight: 800; letter-spacing: -0.5px;">🏗️
                                            INFRA<span style="color: #fed7aa;">HUB</span></span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Main Card -->
                    <tr>
                        <td
                            style="background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); overflow: hidden;">

                            <!-- Hero Banner -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td
                                        style="background: linear-gradient(135deg, #f97316 0%, #ea580c 50%, #c2410c 100%); padding: 40px 40px 30px; text-align: center;">
                                        <div style="font-size: 48px; margin-bottom: 12px;">✅</div>
                                        <h1
                                            style="margin: 0; color: #ffffff; font-size: 26px; font-weight: 700; letter-spacing: -0.3px;">
                                            Email Configuration Successful!
                                        </h1>
                                        <p style="margin: 8px 0 0; color: rgba(255,255,255,0.85); font-size: 15px;">
                                            Your SMTP settings are working perfectly.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Body Content -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="padding: 32px 40px;">
                                        <p style="margin: 0 0 20px; color: #334155; font-size: 15px; line-height: 1.7;">
                                            Hello <strong>{{ $recipientName }}</strong>,
                                        </p>
                                        <p style="margin: 0 0 24px; color: #475569; font-size: 14px; line-height: 1.7;">
                                            This is a test email from your <strong>InfraHub</strong> platform.
                                            If you're reading this message, it confirms that your email system is
                                            properly configured and ready to send notifications, alerts, and reports.
                                        </p>

                                        <!-- Config Summary Box -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                                            width="100%"
                                            style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
                                            <tr>
                                                <td style="padding: 20px 24px 12px;">
                                                    <p
                                                        style="margin: 0; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8;">
                                                        📋 Configuration Details
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 0 24px 20px;">
                                                    <table role="presentation" cellspacing="0" cellpadding="0"
                                                        border="0" width="100%">
                                                        <tr>
                                                            <td
                                                                style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                                                <span style="color: #64748b; font-size: 13px;">SMTP
                                                                    Host</span>
                                                            </td>
                                                            <td
                                                                style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; text-align: right;">
                                                                <code
                                                                    style="background: #e2e8f0; padding: 2px 8px; border-radius: 4px; font-size: 13px; color: #334155;">{{ $smtpHost }}</code>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td
                                                                style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                                                <span
                                                                    style="color: #64748b; font-size: 13px;">Port</span>
                                                            </td>
                                                            <td
                                                                style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; text-align: right;">
                                                                <code
                                                                    style="background: #e2e8f0; padding: 2px 8px; border-radius: 4px; font-size: 13px; color: #334155;">{{ $smtpPort }}</code>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td
                                                                style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                                                <span
                                                                    style="color: #64748b; font-size: 13px;">From</span>
                                                            </td>
                                                            <td
                                                                style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; text-align: right;">
                                                                <span
                                                                    style="color: #334155; font-size: 13px; font-weight: 600;">{{ $fromAddress }}</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 8px 0;">
                                                                <span style="color: #64748b; font-size: 13px;">Sent
                                                                    at</span>
                                                            </td>
                                                            <td style="padding: 8px 0; text-align: right;">
                                                                <span
                                                                    style="color: #334155; font-size: 13px;">{{ $sentAt }}</span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Status Badge -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                                            width="100%" style="margin-top: 24px;">
                                            <tr>
                                                <td align="center">
                                                    <table role="presentation" cellspacing="0" cellpadding="0"
                                                        border="0">
                                                        <tr>
                                                            <td
                                                                style="background: linear-gradient(135deg, #059669, #10b981); border-radius: 8px; padding: 12px 32px;">
                                                                <span
                                                                    style="color: #ffffff; font-size: 14px; font-weight: 700;">✓
                                                                    All Systems Operational</span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

                                        <p
                                            style="margin: 24px 0 0; color: #94a3b8; font-size: 13px; line-height: 1.6; text-align: center;">
                                            You can manage your email settings from the
                                            <a href="{{ $settingsUrl }}"
                                                style="color: #f97316; text-decoration: none; font-weight: 600;">Admin
                                                Panel → Settings → Email Settings</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 24px 40px; text-align: center;">
                            <p style="margin: 0 0 4px; color: #94a3b8; font-size: 12px;">
                                © {{ date('Y') }} InfraHub • Construction & Infrastructure Management
                            </p>
                            <p style="margin: 0; color: #cbd5e1; font-size: 11px;">
                                This is an automated test email. No action is required.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>