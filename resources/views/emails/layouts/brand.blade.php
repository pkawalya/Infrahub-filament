<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>@yield('title', config('app.name', 'InfraHub'))</title>
    <!--[if mso]>
    <style>
        table {border-collapse: collapse; border-spacing: 0; border: none; margin: 0;}
        div, td {padding: 0;}
        div {margin: 0 !important;}
    </style>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        * {
            margin: 0;
            padding: 0;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            border: 0;
            display: block;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        a {
            color: #4f46e5;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        @media only screen and (max-width: 620px) {
            .email-wrapper {
                width: 100% !important;
                padding: 16px !important;
            }

            .email-card {
                border-radius: 12px !important;
            }

            .email-body {
                padding: 28px 20px !important;
            }

            .email-header {
                padding: 20px !important;
            }

            .email-footer-inner {
                padding: 24px 20px !important;
            }

            .responsive-full {
                width: 100% !important;
                display: block !important;
            }
        }
    </style>
</head>

<body style="margin:0; padding:0; background-color:#f4f6f9;">

    <!-- Full-width background wrapper -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
        style="background-color:#f4f6f9;">
        <tr>
            <td align="center" style="padding: 40px 20px;" class="email-wrapper">

                <!-- Card Container -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600"
                    style="max-width:600px; width:100%; background-color:#ffffff; border-radius:16px; box-shadow:0 2px 12px rgba(0,0,0,0.06); overflow:hidden;"
                    class="email-card">

                    {{-- ═══════════════ HEADER ═══════════════ --}}
                    <tr>
                        <td class="email-header" style="padding: 28px 40px; border-bottom: 1px solid #f0f0f5;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="left" valign="middle" style="width:180px;">
                                        <!-- Logo as text (maximum email client compatibility) -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td
                                                    style="background: linear-gradient(135deg, #38bdf8, #4f46e5); border-radius:8px; padding:8px 14px;">
                                                    <span
                                                        style="font-family:'Inter',sans-serif; font-size:11px; font-weight:800; color:#ffffff; letter-spacing:0.5px;">🏗️
                                                        INFRA<span style="color:#c7d2fe;">HUB</span></span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td align="right" valign="middle">
                                        @hasSection('header_action')
                                            @yield('header_action')
                                        @else
                                            <a href="{{ config('app.url') }}/app"
                                                style="display:inline-block; padding:8px 20px; font-size:13px; font-weight:600; color:#374151; border:1px solid #d1d5db; border-radius:8px; text-decoration:none; font-family:'Inter',sans-serif;">
                                                Log in
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ═══════════════ BODY ═══════════════ --}}
                    <tr>
                        <td class="email-body" style="padding: 40px 40px 36px;">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- ═══════════════ FOOTER ═══════════════ --}}
                    <tr>
                        <td style="border-top: 1px solid #f0f0f5;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td class="email-footer-inner" align="center" style="padding: 32px 40px 28px;">
                                        <!-- Footer Logo -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td align="center" style="padding-bottom:16px;">
                                                    <table role="presentation" cellspacing="0" cellpadding="0"
                                                        border="0">
                                                        <tr>
                                                            <td
                                                                style="background: linear-gradient(135deg, #38bdf8, #4f46e5); border-radius:6px; padding:6px 12px;">
                                                                <span
                                                                    style="font-family:'Inter',sans-serif; font-size:10px; font-weight:800; color:#ffffff; letter-spacing:0.5px;">🏗️
                                                                    INFRA<span style="color:#c7d2fe;">HUB</span></span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Footer text -->
                                        <p
                                            style="margin:0 0 16px; font-size:12px; color:#9ca3af; line-height:1.6; max-width:420px;">
                                            You have received this email because you are registered at InfraHub. This is
                                            an automated message from the Construction & Infrastructure Management
                                            platform.
                                        </p>

                                        <!-- Social icons row -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                                            style="margin-bottom:16px;">
                                            <tr>
                                                <td style="padding:0 6px;">
                                                    <a href="#"
                                                        style="display:inline-block; width:32px; height:32px; border-radius:50%; background:#e5e7eb; text-align:center; line-height:32px; font-size:14px; text-decoration:none; color:#6b7280;"
                                                        title="LinkedIn">in</a>
                                                </td>
                                                <td style="padding:0 6px;">
                                                    <a href="#"
                                                        style="display:inline-block; width:32px; height:32px; border-radius:50%; background:#e5e7eb; text-align:center; line-height:32px; font-size:14px; text-decoration:none; color:#6b7280;"
                                                        title="Facebook">f</a>
                                                </td>
                                                <td style="padding:0 6px;">
                                                    <a href="#"
                                                        style="display:inline-block; width:32px; height:32px; border-radius:50%; background:#e5e7eb; text-align:center; line-height:32px; font-size:14px; text-decoration:none; color:#6b7280;"
                                                        title="Twitter / X">𝕏</a>
                                                </td>
                                                <td style="padding:0 6px;">
                                                    <a href="mailto:support@infrahub.co"
                                                        style="display:inline-block; width:32px; height:32px; border-radius:50%; background:#e5e7eb; text-align:center; line-height:32px; font-size:14px; text-decoration:none; color:#6b7280;"
                                                        title="Email">✉</a>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Links row -->
                                        <p style="margin:0 0 12px; font-size:12px;">
                                            <a href="{{ config('app.url') }}"
                                                style="color:#4f46e5; text-decoration:none; font-weight:500;">Dashboard</a>
                                            <span style="color:#d1d5db; padding:0 8px;">|</span>
                                            <a href="{{ config('app.url') }}/privacy"
                                                style="color:#4f46e5; text-decoration:none; font-weight:500;">Privacy
                                                policy</a>
                                            <span style="color:#d1d5db; padding:0 8px;">|</span>
                                            <a href="{{ config('app.url') }}/support"
                                                style="color:#4f46e5; text-decoration:none; font-weight:500;">Help
                                                center</a>
                                        </p>

                                        <!-- Copyright -->
                                        <p style="margin:0; font-size:11px; color:#b0b8c9;">
                                            © {{ date('Y') }} InfraHub · Construction & Infrastructure Management
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
                <!-- /Card Container -->

            </td>
        </tr>
    </table>

</body>

</html>