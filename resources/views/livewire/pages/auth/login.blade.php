<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Volt\Component;

new class extends Component
{
    // Instanciamos el formulario de login nativo de Laravel Breeze
    public LoginForm $form;

    /**
     * Procesa la solicitud de inicio de sesión.
     */
   public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        // 🎯 Obtenemos la URL propuesta o el dashboard por defecto usando la Fachada nativa
        $url = \Illuminate\Support\Facades\Redirect::getIntendedUrl() 
               ?? route('dashboard', absolute: false);

        // 🚀 Redirigimos de forma segura usando Livewire Navigate
        $this->redirect($url, navigate: true);
    }
}; ?>

<div class="min-h-screen w-full flex items-center justify-center bg-[#1b1514] text-white px-6 md:px-16 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 max-w-6xl w-full items-center">
        
        <div class="flex flex-col justify-center max-w-md mx-auto md:mx-0 w-full order-2 md:order-1">
            
            <div class="mb-8 text-left">
                <h2 class="text-3xl md:text-4xl font-sans font-normal tracking-wide text-white mb-2">
                    Iniciar Sesión
                </h2>
                <a href="{{ route('register') }}" class="text-base text-stone-400 hover:text-white transition-colors duration-200 font-sans tracking-wide" wire:navigate>
                    ¿No tienes una cuenta? <span class="underline">Regístrate aquí</span>
                </a>
            </div>

            <form wire:submit="login" class="space-y-5">
                
                <div class="space-y-1.5">
                    <label class="block text-xs uppercase tracking-widest text-stone-300 font-medium">CORREO ELECTRÓNICO</label>
                    <input wire:model="form.email" type="email" required autofocus autocomplete="username" 
                           class="w-full bg-white text-black px-4 py-2.5 border-none outline-none focus:ring-2 focus:ring-stone-500 font-sans text-base transition-shadow">
                    <x-input-error :messages="$errors->get('form.email')" class="mt-1 text-xs text-red-400" />
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs uppercase tracking-widest text-stone-300 font-medium">CLAVE</label>
                    <input wire:model="form.password" type="password" required autocomplete="current-password" 
                           class="w-full bg-white text-black px-4 py-2.5 border-none outline-none focus:ring-2 focus:ring-stone-500 font-sans text-base transition-shadow">
                    <x-input-error :messages="$errors->get('form.password')" class="mt-1 text-xs text-red-400" />
                </div>

                <div class="pt-4 flex justify-center">
                    <button type="submit" class="w-full max-w-[240px] bg-black hover:bg-zinc-900 text-white font-medium py-2.5 px-6 uppercase tracking-widest text-xs transition-all duration-200 shadow-md active:scale-98">
                        Ingresar
                    </button>
                </div>

            </form>
        </div>

        <div class="flex flex-col items-center md:items-end space-y-6 text-center md:text-right select-none order-1 md:order-2 md:pr-12">
            <h1 class="text-6xl md:text-7xl font-serif tracking-widest uppercase text-white font-normal">
                FIND ME
            </h1>
            
            <div class="w-full max-w-md pt-2 md:pr-4 transform -rotate-12 hover:rotate-[-15deg] transition-transform duration-300">
                <img src="{{ asset('images/florFound.png') }}" 
                     alt="Flor Find Me" 
                     class="w-full h-auto object-contain max-h-[350px] mx-auto md:mr-0">
            </div>
        </div>

    </div>
</div>