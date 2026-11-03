<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-[#1b1514] border-b border-zinc-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center space-x-3 group">
                    <img src="{{ asset('images/florFound.png') }}" alt="Logo Flor" class="h-8 w-auto object-contain select-none opacity-90">
                    <span class="text-2xl font-serif tracking-widest uppercase font-bold text-white">
                        FIND ME
                    </span>
                </a>
            </div>

            <div class="hidden sm:flex sm:items-center sm:space-x-6">
                
                @if(isset($categories))
                    <a href="{{ route('dashboard') }}" class="text-sm font-sans tracking-wider uppercase text-gray-300 hover:text-white transition-colors" wire:navigate>
                        Inicio
                    </a>
                @endif

                @auth
                    <!-- ✉️ ENLACE RÁPIDO A MENSAJES (Junto al menú) -->
                    <a href="{{ route('chat.index') }}" wire:navigate class="text-sm font-sans tracking-wider uppercase text-gray-300 hover:text-white transition-colors flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-dots" viewBox="0 0 16 16">
                            <path d="M5 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0m4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0m4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                            <path d="m2.165 15.803.02-.004c1.83-.363 2.948-.842 3.468-1.105A9 9 0 0 0 8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6a10.4 10.4 0 0 1-.52 1.209l-.034.077a11 11 0 0 1-.257.514C1.002 14.803 1 14.803 1 14.803a.5.5 0 0 0 .584.584c.12-.02.261-.055.42-.1zM8 2c3.866 0 7 2.686 7 6s-3.134 6-7 6a7 7 0 0 1-2.072-.312l-.335-.11-.3-.122a1 1 0 0 0-.828.082 7 7 0 0 1-1.282.527 8 8 0 0 0 .848-1.583 1 1 0 0 0-.173-.913C3.12 10.442 2.5 9.28 2.5 8c0-3.314 3.134-6 7-6"/>
                        </svg>
                        <span>Mensajes</span>
                    </a>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border-none text-sm font-medium rounded-md text-gray-300 hover:text-white bg-transparent focus:outline-none transition duration-150 ease-in-out">
                                <div x-data="{ name: '{{ auth()->user()->name ?? '' }}' }" x-text="name" x-on:profile-updated.window="name = $event.detail.name" class="font-sans tracking-wide"></div>
                                <div class="ms-2">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1.1 0 011.414 0L10 10.586l3.293-3.293a1.1 0 111.414 1.414l-4 4a1.1 0 01-1.414 0l-4-4a1.1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile')" wire:navigate>
                                Mi Perfil
                            </x-dropdown-link>

                            <!-- ✉️ OPCIÓN DENTRO DEL MENÚ DESPLEGABLE -->
                            <x-dropdown-link :href="route('chat.index')" wire:navigate>
                                Mis Mensajes
                            </x-dropdown-link>

                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link>
                                    Cerrar Sesión
                                </x-dropdown-link>
                            </button>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="space-x-4">
                        <a href="{{ route('login') }}" class="text-sm font-sans tracking-wider uppercase text-gray-400 hover:text-white transition-colors" wire:navigate>
                            Iniciar Sesión
                        </a>
                        <a href="{{ route('register') }}" class="text-sm font-sans tracking-wider uppercase bg-white text-black px-4 py-2 hover:bg-gray-200 transition-colors font-semibold" wire:navigate>
                            Registrarse
                        </a>
                    </div>
                @endauth
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-zinc-900 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>