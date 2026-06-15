<!-- Vegetable Illustration Component in Laravel Blade -->
@props(['id' => 'carrot', 'size' => 64, 'class' => ''])

<div class="relative inline-flex items-center justify-center select-none {{ $class }}" style="width: {{ $size }}px; height: {{ $size }}px;" id="veg-illus-{{ $id }}">
    @switch($id)
        @case('carrot')
            <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                <!-- Background Circle -->
                <circle cx="50" cy="50" r="42" fill="url(#carrotGradBg)" opacity="0.15" />
                <!-- Leaf/Stem -->
                <path d="M62 25C65 18 58 12 58 12C58 12 50 16 53 23" stroke="#22C55E" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M54 28C55 20 48 15 48 15C48 15 42 20 46 26" stroke="#16A34A" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M68 32C73 28 68 20 68 20C68 20 59 21 61 28" stroke="#15803D" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                <!-- Carrot Body -->
                <path d="M58 28C54 29 41 42 25 72C23 76 21 80 20 81C20 81 24 79 28 77C58 61 71 48 72 44C74 38 64 26 58 28Z" fill="url(#carrotGrad)" />
                <!-- Texture Ridges -->
                <path d="M48 45C45 44 42 46 41 48" stroke="#EA580C" stroke-width="2" stroke-linecap="round" />
                <path d="M38 56C36 55 33 57 32 59" stroke="#EA580C" stroke-width="2" stroke-linecap="round" />
                <path d="M56 36C54 35 52 37 50 38" stroke="#EA580C" stroke-width="2" stroke-linecap="round" />
                <defs>
                    <linearGradient id="carrotGrad" x1="68" y1="30" x2="22" y2="78" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#FB923C" />
                        <stop offset="60%" stop-color="#F97316" />
                        <stop offset="100%" stop-color="#EA580C" />
                    </linearGradient>
                    <linearGradient id="carrotGradBg" x1="0" y1="0" x2="100" y2="100" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#F97316" />
                        <stop offset="100%" stop-color="#F43F5E" />
                    </linearGradient>
                </defs>
            </svg>
            @break
            
        @case('tomato')
           <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
            <circle cx="50" cy="50" r="42" fill="url(#tomatoGradBg)" opacity="0.15" />
            <circle cx="50" cy="54" r="28" fill="url(#tomatoGrad)" />
            <path d="M26 50C22 56 26 62 30 68" fill="url(#tomatoGrad)" opacity="0.2" />
            <path d="M50 26V21C50 21 47 23 45 22" stroke="#16A34A" stroke-width="3" stroke-linecap="round" />
            <path d="M50 25C47 20 40 21 40 21C40 21 45 24 47 25.5" fill="#15803D" />
            <path d="M50 25C53 20 60 21 60 21C60 21 55 24 53 25.5" fill="#16A34A" />
            <path d="M50 25C44 26 36 29 36 29C36 29 44 29 47 27" fill="#15803D" />
            <path d="M50 25C56 26 64 29 64 29C64 29 56 29 53 27" fill="#16A34A" />
            <ellipse cx="40" cy="42" rx="6" ry="3" transform="rotate(-30 40 42)" fill="#FFFFFF" opacity="0.6" />
            <defs>
              <linearGradient id="tomatoGrad" x1="35" y1="34" x2="68" y2="76" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#F87171" />
                <stop offset="35%" stop-color="#EF4444" />
                <stop offset="100%" stop-color="#991B1B" />
              </linearGradient>
              <linearGradient id="tomatoGradBg" x1="0" y1="0" x2="100" y2="100" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#FECACA" />
                <stop offset="100%" stop-color="#991B1B" />
              </linearGradient>
            </defs>
          </svg>
           @break

        @case('brinjal')
           <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
            <circle cx="50" cy="50" r="42" fill="url(#brinjalGradBg)" opacity="0.15" />
            <path d="M48 24C48 16 41 12 41 12C41 12 46 16 46 22" stroke="#15803D" stroke-width="3" stroke-linecap="round" />
            <path d="M46 25C48 24 53 24 56 26C65 29 74 48 72 65C70 78 58 84 46 84C34 84 24 76 26 61C28 46 38 29 46 25Z" fill="url(#brinjalGrad)" />
            <path d="M46 25C44 28 35 32 35 32C35 32 44 32 47 28" fill="#16A34A" />
            <path d="M54 25C56 28 65 32 65 32C65 32 56 32 53 28" fill="#15803D" />
            <path d="M50 25C50 31 52 35 52 35C52 35 48 35 48 28.5" fill="#14532D" />
            <path d="M34 52C32 58 35 68 40 72" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" opacity="0.4" />
            <defs>
              <linearGradient id="brinjalGrad" x1="42" y1="26" x2="58" y2="82" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#A855F7" />
                <stop offset="45%" stop-color="#7E22CE" />
                <stop offset="100%" stop-color="#3B0764" />
              </linearGradient>
              <linearGradient id="brinjalGradBg" x1="0" y1="0" x2="100" y2="100" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#D8B4FE" />
                <stop offset="100%" stop-color="#4A044E" />
              </linearGradient>
            </defs>
          </svg>
           @break
           
        @case('leeks')
           <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
            <circle cx="50" cy="50" r="42" fill="url(#leekGradBg)" opacity="0.15" />
            <path d="M72 73C74 76 72 79 73 82" stroke="#E2E8F0" stroke-width="2" stroke-linecap="round" />
            <path d="M75 70C78 72 77 75 79 77" stroke="#CBD5E1" stroke-width="2" stroke-linecap="round" />
            <path d="M69 75C69 79 67 81 67 84" stroke="#E2E8F0" stroke-width="2" stroke-linecap="round" />
            <path d="M30 18C34 26 50 50 56 62C58 66 69 72 72 68C75 64 74 54 68 50C53 38 34 24 24 16" fill="url(#leekWhite)" />
            <path d="M53 44C47 38 31 18 20 12C18 10 14 12 16 16C24 30 40 48 46 52" fill="#22C55E" />
            <path d="M58 48C56 40 44 22 36 12C34 10 32 12 33 15C39 28 50 44 54 48" fill="#15803D" />
            <path d="M22 22C24 18 28 14 30 16C32 18 28 26 26 28" fill="#4ADE80" />
            <defs>
              <linearGradient id="leekWhite" x1="28" y1="18" x2="72" y2="68" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#86EFAC" />
                <stop offset="40%" stop-color="#F0FDF4" />
                <stop offset="85%" stop-color="#FFFFFF" />
                <stop offset="100%" stop-color="#E2E8F0" />
              </linearGradient>
              <linearGradient id="leekGradBg" x1="0" y1="0" x2="100" y2="100" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#4ADE80" />
                <stop offset="100%" stop-color="#065F46" />
              </linearGradient>
            </defs>
          </svg>
           @break
        
        {{-- Generic Fallback --}}
        @default
            <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                <circle cx="50" cy="50" r="42" fill="url(#defGrad)" />
                <path d="M50 25C50 25 60 30 65 45C70 60 60 75 50 75C40 75 30 60 35 45C40 30 50 25 50 25Z" fill="#16A34A" />
                <defs>
                    <linearGradient id="defGrad" x1="0" y1="0" x2="100" y2="100" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#F1F5F9" />
                        <stop offset="100%" stop-color="#94A3B8" />
                    </linearGradient>
                </defs>
            </svg>
    @endswitch
</div>
