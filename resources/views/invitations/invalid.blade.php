<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation Invalid — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            padding: 20px;
        }

        .card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 480px;
            width: 100%;
            text-align: center;
            padding: 48px 32px;
        }

        .emoji {
            font-size: 56px;
            margin-bottom: 20px;
        }

        h1 {
            color: #1f2937;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        p {
            color: #6b7280;
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 24px;
        }

        .btn-login {
            display: inline-block;
            padding: 12px 32px;
            background: linear-gradient(135deg, #3b82f6, #4f46e5);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
        }

        .help-text {
            margin-top: 24px;
            font-size: 13px;
            color: #9ca3af;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="emoji">⚠️</div>
        <h1>Invalid or Expired Invitation</h1>
        <p>
            This invitation link is no longer valid. It may have already been accepted, expired, or been revoked.
        </p>
        <p>
            If you need a new invitation, please contact your administrator.
        </p>
        <a href="{{ url('/app/login') }}" class="btn-login">
            Go to Login →
        </a>
        <p class="help-text">
            Already have an account? You can log in directly.
        </p>
    </div>
</body>

</html>