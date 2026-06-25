<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Admin') }} - Control Matrix</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="font-sans antialiased bg-[#05080f] text-gray-300 selection:bg-emerald-500 selection:text-white" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0a101d] border-r border-gray-800 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col shadow-2xl">
            <div class="flex items-center justify-center h-16 bg-[#0a101d] border-b border-gray-800">
                <span class="text-white font-extrabold text-lg tracking-wider flex items-center space-x-2">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <span>ADMIN PANEL</span>
                </span>
            </div>

            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto" style="scrollbar-width: thin; scrollbar-color: #1f2937 transparent;">

                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 mt-4 px-3">Overview</div>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-[#111827] text-emerald-400 border border-gray-800' : 'text-gray-400 hover:bg-[#111827] hover:text-white' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3"></i>
                    Dashboard
                </a>
                <a href="{{ route('admin.analytics') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.analytics') ? 'bg-[#111827] text-emerald-400 border border-gray-800' : 'text-gray-400 hover:bg-[#111827] hover:text-white' }}">
                    <i data-lucide="bar-chart-2" class="w-5 h-5 mr-3"></i>
                    Analytics
                </a>

                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 mt-6 px-3">Data Operations</div>
                <a href="{{ route('admin.scraper') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.scraper') ? 'bg-[#111827] text-emerald-400 border border-gray-800' : 'text-gray-400 hover:bg-[#111827] hover:text-white' }}">
                    <i data-lucide="cpu" class="w-5 h-5 mr-3"></i>
                    HARTI Scraper
                </a>
                <a href="{{ route('admin.prices.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.prices.*') ? 'bg-[#111827] text-emerald-400 border border-gray-800' : 'text-gray-400 hover:bg-[#111827] hover:text-white' }}">
                    <i data-lucide="line-chart" class="w-5 h-5 mr-3"></i>
                    Price Records
                </a>

                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 mt-6 px-3">Catalog Management</div>
                <a href="{{ route('admin.markets.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.markets.*') ? 'bg-[#111827] text-emerald-400 border border-gray-800' : 'text-gray-400 hover:bg-[#111827] hover:text-white' }}">
                    <i data-lucide="map-pin" class="w-5 h-5 mr-3"></i>
                    Markets
                </a>
                <a href="{{ route('admin.vegetables.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.vegetables.*') ? 'bg-[#111827] text-emerald-400 border border-gray-800' : 'text-gray-400 hover:bg-[#111827] hover:text-white' }}">
                    <i data-lucide="leaf" class="w-5 h-5 mr-3"></i>
                    Vegetables
                </a>

                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 mt-6 px-3">Growth & SEO</div>
                <a href="{{ route('admin.seo') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.seo') ? 'bg-[#111827] text-emerald-400 border border-gray-800' : 'text-gray-400 hover:bg-[#111827] hover:text-white' }}">
                    <i data-lucide="search" class="w-5 h-5 mr-3"></i>
                    SEO Pages
                </a>
                <a href="{{ route('admin.sitemap') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.sitemap') ? 'bg-[#111827] text-emerald-400 border border-gray-800' : 'text-gray-400 hover:bg-[#111827] hover:text-white' }}">
                    <i data-lucide="map" class="w-5 h-5 mr-3"></i>
                    Sitemaps
                </a>

                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 mt-6 px-3">System Health</div>
                <a href="{{ route('admin.queue') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.queue') ? 'bg-[#111827] text-emerald-400 border border-gray-800' : 'text-gray-400 hover:bg-[#111827] hover:text-white' }}">
                    <i data-lucide="layers" class="w-5 h-5 mr-3"></i>
                    Queue Manager
                </a>
                <a href="{{ route('admin.scheduler') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.scheduler') ? 'bg-[#111827] text-emerald-400 border border-gray-800' : 'text-gray-400 hover:bg-[#111827] hover:text-white' }}">
                    <i data-lucide="clock" class="w-5 h-5 mr-3"></i>
                    Scheduler
                </a>
                <a href="{{ route('admin.cache') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.cache') ? 'bg-[#111827] text-emerald-400 border border-gray-800' : 'text-gray-400 hover:bg-[#111827] hover:text-white' }}">
                    <i data-lucide="zap" class="w-5 h-5 mr-3"></i>
                    Cache Manager
                </a>
                <a href="{{ route('admin.logs') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.logs') ? 'bg-[#111827] text-emerald-400 border border-gray-800' : 'text-gray-400 hover:bg-[#111827] hover:text-white' }}">
                    <i data-lucide="terminal" class="w-5 h-5 mr-3"></i>
                    System Logs
                </a>
                <a href="{{ route('admin.database') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.database') ? 'bg-[#111827] text-emerald-400 border border-gray-800' : 'text-gray-400 hover:bg-[#111827] hover:text-white' }}">
                    <i data-lucide="database" class="w-5 h-5 mr-3"></i>
                    Database State
                </a>

                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 mt-6 px-3">Administration</div>
                <a href="{{ route('admin.users') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.users') ? 'bg-[#111827] text-emerald-400 border border-gray-800' : 'text-gray-400 hover:bg-[#111827] hover:text-white' }}">
                    <i data-lucide="users" class="w-5 h-5 mr-3"></i>
                    User Management
                </a>
                <a href="{{ route('admin.settings') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.settings') ? 'bg-[#111827] text-emerald-400 border border-gray-800' : 'text-gray-400 hover:bg-[#111827] hover:text-white' }}">
                    <i data-lucide="settings" class="w-5 h-5 mr-3"></i>
                    Settings
                </a>

            </nav>

            <div class="flex-shrink-0 p-4 border-t border-gray-800">
                <a href="{{ route('home') }}" class="flex items-center w-full px-3 py-2 text-sm font-medium text-gray-400 rounded-md hover:bg-[#111827] hover:text-white">
                    <i data-lucide="external-link" class="w-5 h-5 mr-3"></i>
                    View Live Site
                </a>
            </div>
        </aside>

        <!-- Overlay for mobile -->
        <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="sidebarOpen = false" x-transition.opacity></div>

        <!-- Main Content -->
        <div class="flex flex-col flex-1 w-0 overflow-hidden">
            <!-- Header -->
            <header class="flex items-center justify-between flex-shrink-0 h-16 px-4 bg-[#0a101d] border-b border-gray-800 shadow">
                <button @click="sidebarOpen = true" class="p-2 text-gray-400 lg:hidden hover:text-white focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                </button>

                <div class="flex-1 flex justify-between items-center ml-4 lg:ml-0">
                    <h1 class="text-xl font-bold text-white tracking-tight">
                        @yield('header', 'System Control')
                    </h1>

                    <div class="flex items-center space-x-4">
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center focus:outline-none">
                                <span class="mr-2 text-sm font-medium text-gray-300">{{ Auth::user()->name ?? 'Admin' }}</span>
                                <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-white font-bold text-sm">
                                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                                </div>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 w-48 mt-2 py-1 bg-[#111827] border border-gray-700 rounded-md shadow-2xl z-50">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-[#1f2937] hover:text-white">Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-[#1f2937] hover:text-red-300">Log Out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main area -->
            <main class="flex-1 relative overflow-y-auto focus:outline-none" style="scrollbar-width: thin; scrollbar-color: #374151 transparent;">
                <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <!-- Toast Notifications Placeholder -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col space-y-3 pointer-events-none">
        @if(session('success'))
            <div class="pointer-events-auto bg-[#052e16] border border-emerald-500/50 text-emerald-400 px-4 py-3 rounded-md flex items-center shadow-[0_0_15px_rgba(16,185,129,0.1)] min-w-[300px]" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
                <i data-lucide="check-circle" class="w-5 h-5 mr-3"></i>
                <span class="text-sm font-medium">{{ session('success') }}</span>
                <button @click="show = false" class="ml-auto text-emerald-500 hover:text-emerald-300"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
        @endif
        @if(session('error'))
            <div class="pointer-events-auto bg-[#450a0a] border border-red-500/50 text-red-400 px-4 py-3 rounded-md flex items-center shadow-[0_0_15px_rgba(239,68,68,0.1)] min-w-[300px]" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
                <i data-lucide="alert-circle" class="w-5 h-5 mr-3"></i>
                <span class="text-sm font-medium">{{ session('error') }}</span>
                <button @click="show = false" class="ml-auto text-red-500 hover:text-red-300"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if(window.lucide) { lucide.createIcons(); }
        });
    </script>
</body>
</html>