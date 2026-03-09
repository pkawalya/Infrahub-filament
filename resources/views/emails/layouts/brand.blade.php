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
                                        <span
                                            style="font-family:'Inter',sans-serif; font-size:16px; font-weight:700; color:#1f2937; letter-spacing:-0.3px;">
                                            {{ config('app.name', 'App') }}
                                        </span>
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
                                    <td class="email-footer-inner" align="center" style="padding: 28px 40px 24px;">
                                        <!-- Footer text -->
                                        <p
                                            style="margin:0 0 14px; font-size:12px; color:#9ca3af; line-height:1.6; max-width:420px;">
                                            You received this email because you have an account with
                                            {{ config('app.name', 'us') }}. This is an automated message.
                                        </p>

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
                                            &copy; {{ date('Y') }} {{ config('app.name', 'App') }}
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