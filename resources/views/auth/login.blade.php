<x-guest-layout>

    <div class="w-full max-w-md mx-auto">

        {{-- Session Status --}}
        @if (session('status'))
            <div class="mb-6 px-4 py-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-sm font-mono font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
                {{ session('status') }}
            </div>
        @endif

        {{-- Card --}}
        <div class="relative bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/60 dark:border-slate-800/60 rounded-[2rem] shadow-2xl shadow-slate-900/10 dark:shadow-slate-950/50 overflow-hidden">

            {{-- Subtle emerald top border accent --}}
            <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-emerald-500/60 to-transparent"></div>

            {{-- Inner glow on hover (decorative) --}}
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-emerald-500/5 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 p-8 sm:p-10">

                {{-- Header --}}
                <div class="mb-8">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/5 border border-emerald-500/15 mb-5">
                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Admin Portal · Secure Access</span>
                    </div>

                    <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                        Welcome back
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 font-medium">
                        Sign in to access the HARTI data management panel.
                    </p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-mono font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="admin@example.com"
                                class="w-full pl-11 pr-4 py-3 rounded-2xl
                                       bg-slate-50 dark:bg-slate-800/60
                                       border border-slate-200 dark:border-slate-700/60
                                       text-slate-900 dark:text-white text-sm font-medium
                                       placeholder-slate-400 dark:placeholder-slate-500
                                       focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/60
                                       transition-all duration-200"
                            >
                        </div>
                        @error('email')
                            <p class="text-xs text-rose-500 font-mono flex items-center gap-1.5 mt-1">
                                <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path stroke-linecap="round" d="M12 8v4M12 16h.01"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="space-y-1.5">
                        <label for="password" class="block text-xs font-mono font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">
                            Password
                        </label>
                        <div class="relative" x-data="{ show: false }">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input
                                id="password"
                                :type="show ? 'text' : 'password'"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••••••"
                                class="w-full pl-11 pr-12 py-3 rounded-2xl
                                       bg-slate-50 dark:bg-slate-800/60
                                       border border-slate-200 dark:border-slate-700/60
                                       text-slate-900 dark:text-white text-sm font-medium
                                       placeholder-slate-400 dark:placeholder-slate-500
                                       focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/60
                                       transition-all duration-200"
                            >
                            <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors">
                                <svg x-show="!show" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="show" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-xs text-rose-500 font-mono flex items-center gap-1.5 mt-1">
                                <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path stroke-linecap="round" d="M12 8v4M12 16h.01"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Remember Me + Forgot Password --}}
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="flex items-center gap-2.5 cursor-pointer group">
                        <input id="remember_me" type="checkbox" name="remember"
                               class="w-4 h-4 rounded border-slate-300 dark:border-slate-600
                                      text-emerald-500 bg-white dark:bg-slate-800
                                      focus:ring-emerald-500/40 focus:ring-2
                                      cursor-pointer transition-all">
                        <span class="text-xs font-medium text-slate-600 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">Remember me</span>
                    </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-xs font-mono font-semibold text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 dark:hover:text-emerald-300 transition-colors hover:underline underline-offset-2">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    {{-- Submit Button --}}
                    <div class="pt-2">
                        <button type="submit"
                                class="w-full relative group flex items-center justify-center gap-2 px-6 py-3.5
                                       bg-gradient-to-r from-emerald-500 to-teal-500
                                       hover:from-emerald-400 hover:to-teal-400
                                       text-white font-bold text-sm rounded-2xl
                                       shadow-lg shadow-emerald-500/25
                                       hover:shadow-xl hover:shadow-emerald-500/35
                                       hover:scale-[1.02] active:scale-[0.98]
                                       transition-all duration-200">
                            <span>Sign In to Admin Panel</span>
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </button>
                    </div>

                </form>

                {{-- Divider --}}
                <div class="mt-8 pt-6 border-t border-slate-200/60 dark:border-slate-800/60">
                    <a href="/"
                       class="flex items-center justify-center gap-2 text-xs font-mono text-slate-500 dark:text-slate-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors group">
                        <svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                        </svg>
                        Back to public price dashboard
                    </a>
                </div>

            </div>
        </div>

        {{-- Security note --}}
        <div class="mt-5 flex items-center justify-center gap-2 text-[10px] font-mono text-slate-400 dark:text-slate-600 uppercase tracking-widest">
            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Secured · HARTI Admin Portal
        </div>

    </div>

</x-guest-layout>
