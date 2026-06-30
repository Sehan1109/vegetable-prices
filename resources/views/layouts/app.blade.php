<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sri Lanka Daily Vegetable Prices')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body x-data="PriceApp()" class="bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen font-sans transition-colors duration-500">

    <div class="fixed inset-0 -z-10 transition-colors duration-700" 
         :style="$store.theme.darkMode 
            ? 'background: radial-gradient(circle at center, #111827 0%, #000 100%)' 
            : 'background: radial-gradient(circle at center, #e2e8f0 0%, #f1f5f9 100%)'"></div>

    <div id="app-container" class="relative z-10">
        @yield('content')
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            // Safe storage helper to prevent crashes when storage is blocked
            const safeStorage = {
                get(key) {
                    try {
                        return localStorage.getItem(key);
                    } catch (e) {
                        console.warn('Storage access blocked:', e);
                        return null;
                    }
                },
                set(key, value) {
                    try {
                        localStorage.setItem(key, value);
                    } catch (e) {}
                }
            };

            // Register Global Theme Store FIRST
            Alpine.store('theme', {
                darkMode: safeStorage.get('darkMode') === 'true' || 
                          (!(safeStorage.get('darkMode')) && window.matchMedia('(prefers-color-scheme: dark)').matches),
                
                init() {
                    // Sync the html class on page load
                    document.documentElement.classList.toggle('dark', this.darkMode);
                },

                toggle() {
                    console.log('Theme toggle clicked. Current darkMode:', this.darkMode);
                    this.darkMode = !this.darkMode;
                    safeStorage.set('darkMode', this.darkMode);
                    document.documentElement.classList.toggle('dark', this.darkMode);
                    window.dispatchEvent(new CustomEvent('theme-changed', { detail: this.darkMode }));
                }
            });

            // Main Application Data
            Alpine.data('PriceApp', () => ({
                lang: safeStorage.get('lang') || '{{ request()->query('lang', 'en') }}',
                
                translations: {
                    en: { 
                        title: "National Produce Pulse", 
                        subtitle: "Real-time AI analysis of Sri Lankan wholesale and retail markets.",
                        explore: "Explore Center", prices: "Today's Prices", analytics: "Market Analytics", admin: "HARTI Admin", info: "Information"
                    },
                    si: { 
                        title: "ජාතික කෘෂි වෙළඳ නාඩි", 
                        subtitle: "ශ්‍රී ලංකාවේ තොග සහ සිල්ලර වෙළඳපොල පිළිබඳ සජීවී AI විශ්ලේෂණය.",
                        explore: "ගවේෂණ මධ්‍යස්ථානය", prices: "අද මිල ගණන්", analytics: "වෙළඳපොල විශ්ලේෂණය", admin: "පාලක පුවරුව", info: "තොරතුරු"
                    },
                    ta: { 
                        title: "தேசிய விவசாய சந்தை நிலவரம்", 
                        subtitle: "இலங்கை மொத்த மற்றும் சில்லறை சந்தைகளின் நேரடி AI பகுப்பாய்வு.",
                        explore: "ஆராய்ச்சி மையம்", prices: "இன்றைய விலைகள்", analytics: "சந்தை பகுப்பாய்வு", admin: "நிர்வாகம்", info: "தகவல்கள்"
                    }
                },

                get text() {
                    return this.translations[this.lang];
                },

                init() {
                    if (this.lang !== 'en') {
                        this.translateFullPage(this.lang);
                    }
                },

                setLang(newLang) {
                    this.lang = newLang;
                    safeStorage.set('lang', newLang);
                    let url = new URL(window.location.href);
                    url.searchParams.set('lang', newLang);
                    window.history.pushState({}, '', url);
                    // Dispatch event to let child components know language changed
                    this.$dispatch('lang-changed', newLang);

                    this.translateFullPage(newLang);
                },

                translateFullPage(targetLang) {
                    if (targetLang === 'en') {
                        document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                        document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" + location.hostname;
                        window.location.reload();
                        return;
                    }

                    const triggerGoogleTranslate = () => {
                        const select = document.querySelector('.goog-te-combo');
                        if (select) {
                            select.value = targetLang;
                            select.dispatchEvent(new Event('change'));
                        } else {
                            setTimeout(triggerGoogleTranslate, 300);
                        }
                    };
                    
                    triggerGoogleTranslate();
                },

                get darkMode() { return Alpine.store('theme').darkMode; }
            }));
        });
        
        document.addEventListener('DOMContentLoaded', () => {
            if(window.lucide) { lucide.createIcons(); }
        });
    </script>
    
    <!-- Google Translate Widget (Hidden) -->
    <div id="google_translate_element" style="display:none;"></div>
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en', 
                includedLanguages: 'en,si,ta',
                autoDisplay: false
            }, 'google_translate_element');
        }
    </script>
    <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</body>
</html>