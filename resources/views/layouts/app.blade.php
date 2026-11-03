<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-[#1b1514] text-white flex flex-col min-h-screen">
        <div class="flex-grow">
            @if(view()->exists('livewire.layout.navigation'))
                <livewire:layout.navigation />
            @endif

            <main>
                {{ $slot }}
            </main>
        </div>

        <footer class="border-t border-zinc-800/80 bg-[#14100f] py-6 text-center text-xs text-stone-400 mt-12">
            <div class="max-w-7xl mx-auto px-4 flex flex-col items-center justify-center gap-4">
                <div class="flex items-center justify-center space-x-6">
                    <a href="{{ route('privacy.policy') }}" wire:navigate class="hover:text-white transition-colors">
                        Aviso de Privacidad
                    </a>
                    <a href="{{ route('dashboard') }}" wire:navigate class="hover:text-white transition-colors">
                        Inicio
                    </a>
                </div>
            </div>
        </footer>

        @livewireScripts
    </body>
</html>