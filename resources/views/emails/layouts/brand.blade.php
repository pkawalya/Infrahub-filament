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
            background-color: #f1f5f9;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        table {
            border-collapse: collapse;
        }

        img {
            border: 0;
            display: block;
            outline: none;
        }

        a {
            color: #e8a229;
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
        }
    </style>
</head>

<body style="margin:0; padding:0; background-color:#f1f5f9;">

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
        style="background-color:#f1f5f9;">
        <tr>
            <td align="center" style="padding: 40px 20px;" class="email-wrapper">

                <!-- Logo -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="560"
                    style="max-width:560px; width:100%;">
                    <tr>
                        <td align="center" style="padding-bottom:24px;">
                            <img src="{{ config('app.url') }}/logo/infrahub-logo-new.png"
                                alt="{{ config('app.name', 'InfraHub') }}" height="44"
                                style="height:44px; border-radius:12px;">
                        </td>
                    </tr>
                </table>

                <!-- Card -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="560"
                    style="max-width:560px; width:100%; background-color:#ffffff; border-radius:16px; box-shadow:0 2px 12px rgba(0,0,0,0.06); overflow:hidden;"
                    class="email-card">

                    {{-- Amber accent bar --}}
                    <tr>
                        <td
                            style="height:4px; background:linear-gradient(90deg, #e8a229, #f5c563, #e8a229); line-height:4px; font-size:0;">
                            &nbsp;</td>
                    </tr>

                    {{-- Header --}}
                    <tr>
                        <td class="email-header" style="padding: 24px 40px 20px; border-bottom: 1px solid #f0f0f5;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="left" valign="middle">
                                        <span
                                            style="font-size:15px; font-weight:700; color:#0f172a; letter-spacing:-0.3px;">
                                            {{ config('app.name', 'InfraHub') }}
                                        </span>
                                    </td>
                                    <td align="right" valign="middle">
                                        @hasSection('header_action')
                                            @yield('header_action')
                                        @else
                                            <a href="{{ config('app.url') }}/app"
                                                style="display:inline-block; padding:8px 20px; font-size:13px; font-weight:600; color:#152d4a; background:linear-gradient(135deg, #e8a229, #d4911e); border-radius:8px; text-decoration:none;">
                                                Log in
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td class="email-body" style="padding: 36px 40px 32px;">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="border-top: 1px solid #f0f0f5;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td class="email-footer-inner" align="center" style="padding: 24px 40px 20px;">
                                        <p
                                            style="margin:0 0 12px; font-size:12px; color:#94a3b8; line-height:1.6; max-width:420px;">
                                            You received this email because you have an account with
                                            {{ config('app.name', 'InfraHub') }}. This is an automated message.
                                        </p>
                                        <p style="margin:0 0 12px; font-size:12px;">
                                            <a href="{{ config('app.url') }}/app"
                                                style="color:#e8a229; text-decoration:none; font-weight:500;">Dashboard</a>
                                            <span style="color:#d1d5db; padding:0 8px;">|</span>
                                            <a href="{{ config('app.url') }}/docs"
                                                style="color:#e8a229; text-decoration:none; font-weight:500;">Documentation</a>
                                            <span style="color:#d1d5db; padding:0 8px;">|</span>
                                            <a href="mailto:info@infrahub.click"
                                                style="color:#e8a229; text-decoration:none; font-weight:500;">Support</a>
                                        </p>
                                        <p style="margin:0; font-size:11px; color:#cbd5e1;">
                                            &copy; {{ date('Y') }} {{ config('app.name', 'InfraHub') }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>