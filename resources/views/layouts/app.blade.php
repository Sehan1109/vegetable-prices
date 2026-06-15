<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sri Lanka Daily Vegetable Prices</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-black text-slate-100 min-h-screen font-sans">
    
    <div class="fixed inset-0 bg-black -z-10" style="background-image: radial-gradient(circle at center, #111827 0%, #000 100%);"></div>

    <div id="app-container" x-data="PriceApp()" class="relative z-10">
        @yield('content')
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('PriceApp', () => ({
                lang: localStorage.getItem('lang') || '{{ request()->query('lang', 'en') }}',
                
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

                setLang(newLang) {
                    this.lang = newLang;
                    localStorage.setItem('lang', newLang);
                    let url = new URL(window.location.href);
                    url.searchParams.set('lang', newLang);
                    window.history.pushState({}, '', url);
                    // Dispatch event to let child components know language changed
                    this.$dispatch('lang-changed', newLang);
                }
            }));
        });
        
        document.addEventListener('DOMContentLoaded', () => {
            if(window.lucide) { lucide.createIcons(); }
        });
    </script>
</body>
</html>