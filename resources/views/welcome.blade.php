<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FIND ME</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- 🔔 CARGAMOS TAILWIND VÍA CDN SOLO PARA BIENVENIDA (Así no rompe tu Dashboard) 🔔 -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased bg-[#1b1514] text-white min-h-screen flex items-center justify-center selection:bg-stone-700">

    <div class="max-w-4xl w-full px-6 py-12 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        
        <!-- ================= COLUMNA IZQUIERDA: Textos y Botones Acceso ================= -->
        <div class="flex flex-col justify-center text-center md:text-left space-y-6 order-2 md:order-1 max-w-md mx-auto md:mx-0">
            <div class="space-y-2">
                <h1 class="text-5xl md:text-6xl font-serif tracking-widest uppercase text-white font-normal" style="font-family: Georgia, serif;">
                    FIND ME
                </h1>
                <p class="text-xs md:text-sm tracking-widest uppercase text-stone-400 font-medium">
                    Reporta objetos perdidos o comparte lo que has hallado
                </p>
            </div>
            
            <div class="w-16 h-[1px] bg-stone-600 mx-auto md:mx-0"></div>

            <!-- Botones Claros y Visibles -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4 justify-center md:justify-start">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="bg-white text-black hover:bg-stone-200 font-semibold px-6 py-3 uppercase tracking-widest text-xs transition-all duration-200 shadow-md text-center">
                            Ir al Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="bg-white text-black hover:bg-stone-200 font-semibold px-6 py-3 uppercase tracking-widest text-xs transition-all duration-200 shadow-md text-center">
                            Iniciar Sesión
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="border border-stone-600 hover:border-white text-white font-semibold px-6 py-3 uppercase tracking-widest text-xs transition-all duration-200 text-center">
                                Registrarse
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>

        <!-- ================= COLUMNA DERECHA: Tu Flor Icónica ================= -->
        <div class="flex flex-col items-center justify-center order-1 md:order-2 select-none">
            <div class="w-full max-w-[280px] md:max-w-sm transform -rotate-12 hover:rotate-[-15deg] transition-transform duration-300">
                <img src="{{ asset('images/florFound.png') }}" 
                     alt="Flor Found Me" 
                     class="w-full h-auto object-contain max-h-[320px] mx-auto">
            </div>
        </div>

    </div>

</body>
</html>