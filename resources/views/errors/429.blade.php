<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Too Many Requests — InfraHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
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
            color: #e2e8f0;
            padding: 2rem 1rem;
        }

        .container {
            text-align: center;
            width: 100%;
            max-width: 540px;
            margin: auto;
        }

        .error-card {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 1rem;
            padding: 3rem 2rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 10px 10px -5px rgba(0, 0, 0, 0.5);
        }

        .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: rgba(244, 63, 94, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px rgba(244, 63, 94, 0.1);
        }

        .icon svg {
            width: 40px;
            height: 40px;
            color: #f43f5e;
        }

        .code {
            font-size: 4.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #f43f5e, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 0.5rem;
            letter-spacing: -0.05em;
        }

        h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 1rem;
        }

        p {
            color: #94a3b8;
            line-height: 1.7;
            margin-bottom: 2.5rem;
            font-size: 1.025rem;
            max-width: 440px;
            margin-left: auto;
            margin-right: auto;
        }

        .actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        a, button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1d4ed8, #2563eb);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(29, 78, 216, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(29, 78, 216, 0.5);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: #cbd5e1;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
        }

        .contact {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 0.85rem;
            color: #64748b;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="error-card">
            <div class="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                </svg>
            </div>
            <div class="code">429</div>
            <h1>Too Many Requests</h1>
            <p>You have sent too many requests in a short amount of time. Please slow down and wait a minute before making further requests.</p>
            
            <div class="actions">
                <button onclick="window.location.reload();" class="btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
                        stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Try Again
                </button>
                <a href="/" class="btn-secondary">Back to Home</a>
            </div>

            <div class="contact">
                Rate limiting protects the system and resources from automated abuse.
            </div>
        </div>
    </div>
</body>

</html>
