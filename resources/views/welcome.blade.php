<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Samanga Projects - Modern Project Management</title>
    <meta name="description"
        content="Samanga Projects - A powerful project management platform for modern teams. Organize tasks, collaborate, and deliver projects on time.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        accent: {
                            50: '#fdf4ff',
                            100: '#fae8ff',
                            200: '#f5d0fe',
                            300: '#f0abfc',
                            400: '#e879f9',
                            500: '#d946ef',
                            600: '#c026d3',
                            700: '#a21caf',
                        }
                    },
                }
            }
        }
    </script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #0ea5e9 0%, #8b5cf6 50%, #d946ef 100%);
        }

        .gradient-text {
            background: linear-gradient(135deg, #0ea5e9 0%, #8b5cf6 50%, #d946ef 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .hero-pattern {
            background-color: #f8fafc;
            background-image:
                radial-gradient(at 100% 0%, rgba(14, 165, 233, 0.1) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(139, 92, 246, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(217, 70, 239, 0.05) 0px, transparent 50%);
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #0ea5e9 0%, #8b5cf6 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(14, 165, 233, 0.3);
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>

<body class="antialiased font-sans hero-pattern min-h-screen">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="w-full py-4 px-6 sm:px-10 lg:px-16">
            <nav class="max-w-7xl mx-auto flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900">Samanga<span
                            class="gradient-text">Projects</span></span>
                </div>

                @if (Route::has('login'))
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ url('/admin') }}"
                                class="text-gray-600 hover:text-primary-600 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Dashboard</a>
                        @else
                            <a href="{{ url('/admin/login') }}"
                                class="text-gray-600 hover:text-primary-600 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Log
                                in</a>
                            <a href="{{ url('/admin/login') }}"
                                class="btn-primary text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow-md">Get
                                Started</a>
                        @endauth
                    </div>
                @endif
            </nav>
        </header>

        <!-- Hero Section -->
        <main class="flex-1 flex flex-col items-center justify-center w-full px-6 sm:px-10 py-12 lg:py-20">
            <div class="max-w-6xl w-full">
                <!-- Hero Content -->
                <div class="text-center mb-16">
                    <div
                        class="inline-flex items-center gap-2 bg-primary-50 text-primary-700 px-4 py-2 rounded-full text-sm font-medium mb-6">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        Trusted by teams worldwide
                    </div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 mb-6 leading-tight">
                        Manage Projects with
                        <span class="gradient-text">Clarity & Precision</span>
                    </h1>
                    <p class="text-lg sm:text-xl text-gray-600 max-w-3xl mx-auto mb-10 leading-relaxed">
                        Samanga Projects empowers your team to plan, track, and deliver exceptional work.
                        From tasks to documents — everything in one beautiful workspace.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="/admin"
                            class="btn-primary text-white font-bold py-4 px-8 rounded-xl text-lg shadow-lg w-full sm:w-auto">
                            Launch Dashboard
                            <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                        <a href="#features"
                            class="text-gray-700 font-semibold py-4 px-8 rounded-xl border-2 border-gray-200 hover:border-primary-300 hover:bg-primary-50 transition-all w-full sm:w-auto text-center">
                            Explore Features
                        </a>
                    </div>
                </div>

                <!-- Features Grid -->
                <div id="features" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-16">
                    <!-- Feature 1 -->
                    <div class="feature-card glass-card p-8 rounded-2xl transition-all duration-300">
                        <div
                            class="w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-400 rounded-2xl flex items-center justify-center mb-6 shadow-lg float-animation">
                            <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path
                                    d="M9 5H7C5.89543 5 5 5.89543 5 7V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V7C19 5.89543 18.1046 5 17 5H15M9 5C9 6.10457 9.89543 7 11 7H13C14.1046 7 15 6.10457 15 5M9 5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5M12 12H15M12 16H15M9 12H9.01M9 16H9.01" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Task Management</h3>
                        <p class="text-gray-600 leading-relaxed">Create, assign, and track tasks with powerful Kanban
                            boards. Set priorities, deadlines, and watch your team excel.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="feature-card glass-card p-8 rounded-2xl transition-all duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-violet-500 to-purple-400 rounded-2xl flex items-center justify-center mb-6 shadow-lg float-animation"
                            style="animation-delay: 0.5s">
                            <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path
                                    d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Team Collaboration</h3>
                        <p class="text-gray-600 leading-relaxed">Keep everyone aligned with real-time updates, comments,
                            and notifications. Collaborate seamlessly across your organization.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="feature-card glass-card p-8 rounded-2xl transition-all duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-pink-500 to-rose-400 rounded-2xl flex items-center justify-center mb-6 shadow-lg float-animation"
                            style="animation-delay: 1s">
                            <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14 2 14 8 20 8" />
                                <line x1="16" y1="13" x2="8" y2="13" />
                                <line x1="16" y1="17" x2="8" y2="17" />
                                <polyline points="10 9 9 9 8 9" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Document Management</h3>
                        <p class="text-gray-600 leading-relaxed">Organize files in folders, track versions, and never
                            lose important documents. Full audit trail included.</p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="feature-card glass-card p-8 rounded-2xl transition-all duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-400 rounded-2xl flex items-center justify-center mb-6 shadow-lg float-animation"
                            style="animation-delay: 0.3s">
                            <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Timeline & Planning</h3>
                        <p class="text-gray-600 leading-relaxed">Visualize project timelines, track milestones, and plan
                            sprints with interactive Gantt-style views.</p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="feature-card glass-card p-8 rounded-2xl transition-all duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-400 rounded-2xl flex items-center justify-center mb-6 shadow-lg float-animation"
                            style="animation-delay: 0.7s">
                            <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M21.21 15.89A10 10 0 1 1 8 2.83" />
                                <path d="M22 12A10 10 0 0 0 12 2v10z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Analytics & Reports</h3>
                        <p class="text-gray-600 leading-relaxed">Gain insights with progress tracking, team performance
                            metrics, and customizable project reports.</p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="feature-card glass-card p-8 rounded-2xl transition-all duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-blue-400 rounded-2xl flex items-center justify-center mb-6 shadow-lg float-animation"
                            style="animation-delay: 1.2s">
                            <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Secure & Reliable</h3>
                        <p class="text-gray-600 leading-relaxed">Enterprise-grade security with role-based access
                            control. Your data is safe and always available.</p>
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="text-center glass-card rounded-3xl p-10 md:p-14">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Ready to transform your workflow?</h2>
                    <p class="text-gray-600 mb-8 max-w-2xl mx-auto">Join teams who trust Samanga Projects to deliver
                        their best work, every time.</p>
                    <a href="/admin"
                        class="btn-primary inline-flex items-center text-white font-bold py-4 px-10 rounded-xl text-lg shadow-lg">
                        Get Started Free
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="w-full py-8 px-6 sm:px-10 border-t border-gray-200">
            <div class="max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 gradient-bg rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z" />
                        </svg>
                    </div>
                    <span class="font-semibold text-gray-700">Samanga Projects</span>
                </div>
                <p class="text-gray-500 text-sm">© {{ date('Y') }} Samanga Projects. Built with Laravel & Filament.</p>
            </div>
        </footer>
    </div>
</body>

</html>