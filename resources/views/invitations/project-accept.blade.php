<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Project Invitation — InfraHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #020617;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f1f5f9;
        }

        .card {
            max-width: 480px;
            width: 90%;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid #1e293b;
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            backdrop-filter: blur(20px);
        }

        .icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #e8a229, #d4911e);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: #94a3b8;
            font-size: 0.9rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .details {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 2rem;
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            font-size: 0.85rem;
        }

        .detail-row:not(:last-child) {
            border-bottom: 1px solid #1e293b;
        }

        .detail-label {
            color: #64748b;
        }

        .detail-value {
            font-weight: 600;
            color: #f1f5f9;
        }

        .btn {
            display: inline-block;
            width: 100%;
            padding: 0.85rem 2rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
        }

        .btn-primary {
            background: linear-gradient(135deg, #e8a229, #d4911e);
            color: #152d4a;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(232, 162, 41, 0.3);
        }

        .expires {
            margin-top: 1rem;
            font-size: 0.75rem;
            color: #64748b;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="icon">📁</div>
        <h1>You're invited!</h1>
        <p class="subtitle">
            You've been invited to join a project on InfraHub. Accept below to get started.
        </p>

        <div class="details">
            <div class="detail-row">
                <span class="detail-label">Project</span>
                <span class="detail-value">{{ $project->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Company</span>
                <span class="detail-value">{{ $company->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Your Role</span>
                <span
                    class="detail-value">{{ \App\Models\ProjectInvitation::$roles[$invitation->role] ?? ucfirst($invitation->role) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Invited By</span>
                <span class="detail-value">{{ $invitation->inviter?->name ?? 'Team Admin' }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('project-invitation.confirm', $invitation->token) }}">
            @csrf
            <button type="submit" class="btn btn-primary">Accept & Join Project</button>
        </form>

        @if($invitation->expires_at)
            <p class="expires">This invitation expires on {{ $invitation->expires_at->format('M d, Y') }}</p>
        @endif
    </div>
</body>

</html>