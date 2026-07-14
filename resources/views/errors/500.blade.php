<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internal Server Error — InfraHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
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
            max-width: 680px;
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
            background: rgba(239, 68, 68, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.1);
        }

        .icon svg {
            width: 40px;
            height: 40px;
            color: #ef4444;
        }

        .code {
            font-size: 4.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ef4444, #f97316);
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
            font-size: 1rem;
            max-width: 480px;
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

        /* Debug details style */
        .debug-container {
            margin-top: 2.5rem;
            text-align: left;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 0.75rem;
            padding: 1.5rem;
            overflow: hidden;
        }

        .debug-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #f87171;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .debug-info-row {
            margin-bottom: 0.75rem;
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .debug-info-row strong {
            color: #f1f5f9;
        }

        .debug-code {
            font-family: 'Fira Code', monospace;
            background: rgba(255, 255, 255, 0.05);
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
            font-size: 0.8rem;
            color: #f43f5e;
            word-break: break-all;
        }

        details {
            margin-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 1rem;
        }

        summary {
            font-size: 0.875rem;
            font-weight: 600;
            color: #94a3b8;
            cursor: pointer;
            outline: none;
            user-select: none;
            transition: color 0.2s;
        }

        summary:hover {
            color: #cbd5e1;
        }

        pre {
            font-family: 'Fira Code', monospace;
            font-size: 0.775rem;
            background: rgba(0, 0, 0, 0.4);
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-x: auto;
            margin-top: 0.75rem;
            color: #f1f5f9;
            border: 1px solid rgba(255, 255, 255, 0.05);
            line-height: 1.6;
            max-height: 250px;
            overflow-y: auto;
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
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>
            <div class="code">500</div>
            <h1>Internal Server Error</h1>
            <p>Something went wrong on our servers. We have logged the error and our technical team is investigating the issue.</p>
            
            <div class="actions">
                <button onclick="window.location.reload();" class="btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
                        stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Reload Page
                </button>
                <a href="/" class="btn-secondary">← Back to Home</a>
            </div>

            @if(config('app.debug') && isset($exception))
                <div class="debug-container">
                    <div class="debug-title">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" width="16" height="16" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5ZM12 2a9.75 9.75 0 0 0-9.75 9.75 9.75 9.75 0 0 0 9.75 9.75 9.75 9.75 0 0 0 9.75-9.75A9.75 9.75 0 0 0 12 2Z" />
                        </svg>
                        Technical Details (Debug Mode)
                    </div>
                    <div class="debug-info-row">
                        <strong>Exception class:</strong>
                        <span class="debug-code">{{ get_class($exception) }}</span>
                    </div>
                    <div class="debug-info-row">
                        <strong>Error Message:</strong>
                        <span class="debug-code">{{ $exception->getMessage() ?: 'No message provided' }}</span>
                    </div>
                    <div class="debug-info-row">
                        <strong>Location:</strong>
                        <span class="debug-code">{{ $exception->getFile() }}:{{ $exception->getLine() }}</span>
                    </div>

                    <details>
                        <summary>Show Stack Trace</summary>
                        <pre><code>{{ $exception->getTraceAsString() }}</code></pre>
                    </details>
                </div>
            @endif

            <div class="contact">
                Need immediate assistance? Contact system support.
            </div>
        </div>
    </div>
</body>

</html>
