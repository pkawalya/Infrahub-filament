@extends('emails.layouts.brand')

@section('title', 'Welcome to InfraHub — Your Login Details')

@section('content')
    <!-- Welcome Icon -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td align="center" style="padding-bottom:20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td
                            style="background:linear-gradient(135deg, #e8a229, #d4911e); width:56px; height:56px; border-radius:50%; text-align:center; line-height:56px; font-size:28px; color:white;">
                            🏗️
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Heading -->
    <h1 style="margin:0 0 8px; font-size:24px; font-weight:800; color:#0f172a; text-align:center; letter-spacing:-0.3px;">
        Welcome to InfraHub!
    </h1>
    <p style="margin:0 0 28px; font-size:14px; color:#64748b; text-align:center; line-height:1.6;">
        Your company has been registered. Here are your login details.
    </p>

    <!-- Greeting -->
    <p style="margin:0 0 16px; font-size:15px; color:#334155; line-height:1.7;">
        Hello <strong>{{ $user->name }}</strong>,
    </p>
    <p style="margin:0 0 24px; font-size:14px; color:#475569; line-height:1.7;">
        Thank you for signing up for <strong>InfraHub</strong>. Your company
        <strong>{{ $company->name }}</strong> has been registered on the
        <strong>{{ $plan->name }}</strong> plan with a <strong>14-day free trial</strong>.
    </p>

    <!-- Login Details Card -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
        style="background:#fffbeb; border:1px solid #fde68a; border-radius:12px; overflow:hidden; margin-bottom:24px;">
        <tr>
            <td
                style="height:4px; background:linear-gradient(90deg, #e8a229, #f5c563, #e8a229); line-height:4px; font-size:0;">
                &nbsp;</td>
        </tr>
        <tr>
            <td style="padding:20px 24px;">
                <p
                    style="margin:0 0 12px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1.5px; color:#92400e;">
                    Your Login Details
                </p>
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="padding:6px 0; font-size:13px; color:#92400e; font-weight:500; width:80px;">Email:</td>
                        <td style="padding:6px 0; font-size:14px; color:#152d4a; font-weight:700;">{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0; font-size:13px; color:#92400e; font-weight:500;">Password:</td>
                        <td style="padding:6px 0; font-size:14px; color:#152d4a; font-weight:600;">The password you created
                            during signup</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0; font-size:13px; color:#92400e; font-weight:500;">Company:</td>
                        <td style="padding:6px 0; font-size:14px; color:#152d4a; font-weight:600;">{{ $company->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0; font-size:13px; color:#92400e; font-weight:500;">Plan:</td>
                        <td style="padding:6px 0; font-size:14px; color:#152d4a; font-weight:600;">{{ $plan->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0; font-size:13px; color:#92400e; font-weight:500;">Role:</td>
                        <td style="padding:6px 0; font-size:14px; color:#152d4a; font-weight:600;">Company Admin</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Status Note -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
        style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; margin-bottom:24px;">
        <tr>
            <td style="padding:14px 20px;">
                <p style="margin:0; font-size:13px; color:#166534; line-height:1.6;">
                    <strong>⏳ Account Status: Pending Approval</strong><br>
                    Our team is reviewing your registration. You'll receive a confirmation email once your account is
                    activated — usually within 24 hours.
                </p>
            </td>
        </tr>
    </table>

    <!-- CTA -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td align="center">
                <a href="{{ $loginUrl }}"
                    style="display:inline-block; padding:14px 36px; background:linear-gradient(135deg, #e8a229, #d4911e); color:#152d4a; font-size:15px; font-weight:700; border-radius:10px; text-decoration:none; box-shadow:0 4px 14px rgba(232,162,41,0.3);"
                    target="_blank">
                    Go to Login Page
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:24px 0 0; font-size:13px; color:#94a3b8; text-align:center; line-height:1.6;">
        Need help? Contact us at <a href="mailto:info@infrahub.click" style="color:#e8a229;">info@infrahub.click</a>
    </p>
@endsection