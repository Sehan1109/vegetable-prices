<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Lanka Produce Prices') }} — Admin Login</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Lucide Icons -->
        <script src="https://unpkg.com/lucide@latest"></script>

        <style>
            body { font-family: 'Inter', sans-serif; }
            .font-mono { font-family: 'JetBrains Mono', monospace; }

            .glow-emerald {
                box-shadow: 0 0 60px -15px rgba(16, 185, 129, 0.35);
            }

            @keyframes float {
                0%, 100% { transform: translateY(0px) rotate(0deg); }
                50% { transform: translateY(-12px) rotate(3deg); }
            }
            @keyframes float-slow {
                0%, 100% { transform: translateY(0px) rotate(0deg); }
                50% { transform: translateY(-8px) rotate(-2deg); }
            }
            .animate-float { animation: float 5s ease-in-out infinite; }
            .animate-float-slow { animation: float-slow 7s ease-in-out infinite; }

            @keyframes pulse-glow {
                0%, 100% { opacity: 0.4; }
                50% { opacity: 0.8; }
            }
            .animate-pulse-glow { animation: pulse-glow 3s ease-in-out infinite; }

            /* Dark mode init without flash */
            html.dark { color-scheme: dark; }
        </style>

        <script>
            // Apply dark mode from storage before page renders (prevents flash)
            (function() {
                try {
                    const stored = localStorage.getItem('darkMode');
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    if (stored === 'true' || (stored === null && prefersDark)) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                } catch(e) {}
            })();
        </script>
    </head>

    <body class="min-h-screen bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 antialiased transition-colors duration-500"
          x-data="{ darkMode: document.documentElement.classList.contains('dark') }"
          x-init="$watch('darkMode', v => { document.documentElement.classList.toggle('dark', v); try { localStorage.setItem('darkMode', v); } catch(e){} })">

        <!-- Background Decorative Elements -->
        <div class="fixed inset-0 -z-10 overflow-hidden">
            <!-- Base gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-slate-50 via-white to-emerald-50/30 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 transition-colors duration-700"></div>

            <!-- Emerald glow top-right -->
            <div class="absolute -top-32 -right-32 w-[600px] h-[600px] bg-emerald-400/10 dark:bg-emerald-500/10 rounded-full blur-[100px] animate-pulse-glow"></div>

            <!-- Teal glow bottom-left -->
            <div class="absolute -bottom-40 -left-40 w-[500px] h-[500px] bg-teal-400/10 dark:bg-teal-500/10 rounded-full blur-[100px] animate-pulse-glow" style="animation-delay: 1.5s"></div>

            <!-- Subtle dot grid -->
            <div class="absolute inset-0 opacity-[0.025] dark:opacity-[0.04]"
                 style="background-image: radial-gradient(circle, #10b981 1px, transparent 1px); background-size: 32px 32px;"></div>
        </div>

        <!-- Top Nav Bar -->
        <nav class="fixed top-0 left-0 right-0 z-50 flex items-center justify-between px-6 py-4">
            <!-- Logo -->
            <a href="/" class="flex items-center gap-3 group">
                <div class="w-9 h-9 bg-emerald-500 rounded-xl flex items-center justify-center shadow-md shadow-emerald-500/30 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5 text-slate-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="flex flex-col leading-none">
                    <span class="text-sm font-black tracking-tight text-slate-900 dark:text-white">Lanka Produce Prices</span>
                    <span class="text-[9px] font-mono font-bold uppercase tracking-widest text-emerald-500">Ceylon Markets</span>
                </div>
            </a>

            <!-- Dark mode toggle -->
            <button @click="darkMode = !darkMode"
                    class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-emerald-500 dark:hover:text-emerald-400 transition-all hover:scale-105">
                <!-- Sun icon (shown in dark) -->
                <svg x-show="darkMode" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="5"/>
                    <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                </svg>
                <!-- Moon icon (shown in light) -->
                <svg x-show="!darkMode" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 3a9 9 0 1 0 9 9A7 7 0 0 1 12 3z"/>
                </svg>
            </button>
        </nav>

        <!-- Page Content -->
        <div class="min-h-screen flex items-center justify-center px-4 pt-20 pb-8">
            {{ $slot }}
        </div>

        <!-- Footer -->
        <div class="fixed bottom-0 left-0 right-0 flex items-center justify-center pb-4">
            <p class="text-[10px] font-mono text-slate-400 dark:text-slate-600 tracking-widest uppercase">
                © {{ date('Y') }} Lanka Produce Prices · HARTI Data Initiative
            </p>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (window.lucide) lucide.createIcons();
            });
        </script>
    </body>
</html>
