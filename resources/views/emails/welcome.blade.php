@extends('emails.layouts.brand')

@section('title', 'Welcome to InfraHub')

@section('content')
    <!-- Welcome Icon -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td align="center" style="padding-bottom:20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td style="background:linear-gradient(135deg, #38bdf8, #4f46e5); width:56px; height:56px; border-radius:50%; text-align:center; line-height:56px; font-size:28px; color:white;">
                            🎉
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Heading -->
    <h1 style="margin:0 0 8px; font-family:'Inter',sans-serif; font-size:24px; font-weight:800; color:#0f172a; text-align:center; letter-spacing:-0.3px;">
        Welcome to InfraHub!
    </h1>
    <p style="margin:0 0 28px; font-family:'Inter',sans-serif; font-size:14px; color:#6b7280; text-align:center; line-height:1.6;">
        Your account has been created. Let's get you started.
    </p>

    <!-- Greeting -->
    <p style="margin:0 0 16px; font-family:'Inter',sans-serif; font-size:15px; color:#334155; line-height:1.7;">
        Hello <strong>{{ $user->name }}</strong>,
    </p>
    <p style="margin:0 0 24px; font-family:'Inter',sans-serif; font-size:14px; color:#475569; line-height:1.7;">
        Welcome to <strong>InfraHub</strong> — the all-in-one construction and infrastructure management platform.
        @if($user->company)
            You've been added to <strong>{{ $user->company->name }}</strong>.
        @endif
        Below are a few things to help you get started.
    </p>

    <!-- Quick Start Steps -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
        style="margin-bottom:28px;">
        @foreach([
            ['icon' => '👤', 'title' => 'Complete Your Profile', 'desc' => 'Add your photo, phone number, and timezone.'],
            ['icon' => '📁', 'title' => 'Explore Projects', 'desc' => 'Navigate to your assigned projects and familiarise yourself.'],
            ['icon' => '🔔', 'title' => 'Set Up Notifications', 'desc' => 'Configure how you want to receive alerts and updates.'],
        ] as $step)
            <tr>
                <td style="padding:10px 0;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                        style="background:#f8fafc; border:1px solid #f0f0f5; border-radius:10px;">
                        <tr>
                            <td style="padding:16px; width:48px; vertical-align:top; text-align:center;">
                                <span style="font-size:22px;">{{ $step['icon'] }}</span>
                            </td>
                            <td style="padding:16px 16px 16px 0;">
                                <p style="margin:0 0 2px; font-family:'Inter',sans-serif; font-size:14px; font-weight:700; color:#1e293b;">{{ $step['title'] }}</p>
                                <p style="margin:0; font-family:'Inter',sans-serif; font-size:13px; color:#64748b; line-height:1.5;">{{ $step['desc'] }}</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        @endforeach
    </table>

    <!-- CTA -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td align="center">
                <a href="{{ config('app.url') }}/app"
                    style="display:inline-block; padding:14px 36px; background:linear-gradient(135deg, #4f46e5, #6366f1); color:#ffffff; font-family:'Inter',sans-serif; font-size:15px; font-weight:700; border-radius:10px; text-decoration:none; box-shadow:0 4px 14px rgba(79,70,229,0.3);"
                    target="_blank">
                    Go to Dashboard
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:24px 0 0; font-family:'Inter',sans-serif; font-size:13px; color:#94a3b8; text-align:center; line-height:1.6;">
        Need help? Reach out to your admin or visit our help center.
    </p>
@endsection
