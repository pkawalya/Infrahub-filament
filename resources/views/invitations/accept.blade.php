<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accept Invitation — {{ config('app.name') }}</title>
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
            max-width: 520px;
            width: 100%;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #3b82f6, #4f46e5);
            padding: 32px;
            text-align: center;
        }

        .card-header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .card-header p {
            color: rgba(255, 255, 255, 0.85);
            font-size: 14px;
        }

        .card-body {
            padding: 32px;
        }

        .welcome-text {
            color: #1f2937;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .welcome-text strong {
            color: #4f46e5;
        }

        .info-box {
            background: #f0f9ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }

        .info-box .label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }

        .info-box .value {
            font-size: 15px;
            font-weight: 600;
            color: #1e40af;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0f2fe;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            font-size: 15px;
            font-family: inherit;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            color: #1f2937;
        }

        .form-group input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-group .helper-text {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }

        .error-text {
            font-size: 13px;
            color: #dc2626;
            margin-top: 4px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 16px;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .section-label {
            font-size: 14px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
        }

        .btn-accept {
            display: block;
            width: 100%;
            padding: 14px 24px;
            background: linear-gradient(135deg, #3b82f6, #4f46e5);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .btn-accept:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
        }

        .card-footer {
            padding: 16px 32px;
            background: #f9fafb;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .card-footer p {
            color: #9ca3af;
            font-size: 12px;
        }

        .emoji {
            font-size: 48px;
            margin-bottom: 12px;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="card-header">
            <div class="emoji">✉️</div>
            <h1>You're Invited!</h1>
            <p>Join {{ $company?->name ?? config('app.name') }} on {{ config('app.name') }}</p>
        </div>
        <div class="card-body">
            <p class="welcome-text">
                Hello <strong>{{ $user->name }}</strong>,<br><br>
                You've been invited to join <strong>{{ $company?->name ?? config('app.name') }}</strong>.
                Set your password below to activate your account.
            </p>

            @if($errors->any())
                <div class="alert alert-error">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="info-box">
                <div class="info-row">
                    <div>
                        <div class="label">Email</div>
                        <div class="value">{{ $user->email }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div>
                        <div class="label">Role</div>
                        <div class="value">{{ \App\Models\User::$userTypes[$user->user_type] ?? $user->user_type }}
                        </div>
                    </div>
                </div>
                @if($company)
                    <div class="info-row">
                        <div>
                            <div class="label">Organization</div>
                            <div class="value">{{ $company->name }}</div>
                        </div>
                    </div>
                @endif
                <div class="info-row">
                    <div>
                        <div class="label">Invitation Expires</div>
                        <div class="value">{{ $invitation->expires_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ url('/invitation/accept/' . $invitation->token) }}">
                @csrf

                <p class="section-label">🔐 Set Your Password</p>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Choose a strong password"
                        required>
                    <p class="helper-text">Min 8 characters, with uppercase, lowercase, and a number.</p>
                    @error('password')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        placeholder="Re-enter your password" required>
                </div>

                <button type="submit" class="btn-accept">
                    ✅ Set Password & Activate Account
                </button>
            </form>
        </div>
        <div class="card-footer">
            <p>This invitation was sent by {{ $invitation->inviter?->name ?? 'an administrator' }}.<br>
                If you didn't expect this, you can safely ignore it.</p>
        </div>
    </div>
</body>

</html>