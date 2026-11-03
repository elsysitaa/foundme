<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public bool $terms = false;

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'min:8'],
            'terms' => ['accepted'],
        ]);

        unset($validated['terms']);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div x-data="{ openPrivacyModal: false }" class="min-h-screen w-full flex items-center justify-center bg-[#1b1514] text-white px-6 md:px-16 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 max-w-6xl w-full items-center">
        
        <div class="flex flex-col items-center md:items-start space-y-6 text-center md:text-left select-none md:pl-12">
            <h1 class="text-6xl md:text-7xl font-serif tracking-widest uppercase text-white font-normal">
                FIND ME
            </h1>
            
            <div class="w-full max-w-md pt-2 md:pl-4">
                <img src="{{ asset('images/florFound.png') }}" 
                     alt="Flor Found Me" 
                     class="w-full h-auto object-contain max-h-[350px]">
            </div>
        </div>

        <div class="flex flex-col justify-center max-w-md mx-auto md:mx-0 w-full">
            
            <div class="mb-8 text-left">
                <h2 class="text-3xl md:text-4xl font-sans font-normal tracking-wide text-white mb-2">
                    Crear una cuenta nueva
                </h2>
                <a href="{{ route('login') }}" class="text-base text-stone-400 hover:text-white transition-colors duration-200 font-sans tracking-wide" wire:navigate>
                    ¿Ya estás registrado? <span class="underline">Accede</span>
                </a>
            </div>

            <form wire:submit="register" class="space-y-5">
                
                <div class="space-y-1.5">
                    <label class="block text-xs uppercase tracking-widest text-stone-300 font-medium">NOMBRE</label>
                    <input wire:model="name" type="text" required autofocus autocomplete="name" 
                           class="w-full bg-white text-black px-4 py-2.5 border-none outline-none focus:ring-2 focus:ring-stone-500 font-sans text-base transition-shadow">
                    <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-red-400" />
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs uppercase tracking-widest text-stone-300 font-medium">CORREO ELECTRÓNICO</label>
                    <input wire:model="email" type="email" required autocomplete="username" 
                           class="w-full bg-white text-black px-4 py-2.5 border-none outline-none focus:ring-2 focus:ring-stone-500 font-sans text-base transition-shadow">
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-400" />
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs uppercase tracking-widest text-stone-300 font-medium">CONTRASEÑA</label>
                    <input wire:model="password" type="password" required autocomplete="new-password" 
                           class="w-full bg-white text-black px-4 py-2.5 border-none outline-none focus:ring-2 focus:ring-stone-500 font-sans text-base transition-shadow">
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-400" />
                </div>

                <!-- CHECKBOX AVISO DE PRIVACIDAD -->
                <div class="space-y-1 pt-1">
                    <div class="flex items-start gap-2.5">
                        <input wire:model="terms" type="checkbox" id="terms" required 
                               class="mt-0.5 rounded bg-white border-none text-stone-800 focus:ring-2 focus:ring-stone-500 cursor-pointer">
                        
                        <label for="terms" class="text-base text-stone-300 leading-tight select-none">
                            Acepto el 
                            <button type="button" @click="openPrivacyModal = true" class="text-white underline hover:text-stone-300 font-medium cursor-pointer">
                                Aviso de Privacidad
                            </button> 
                            y los términos del servicio.
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('terms')" class="mt-1 text-xs text-red-400" />
                </div>

                <!-- BOTÓN DE REGISTRO -->
                <div class="pt-4 flex justify-center">
                    <button type="submit" 
                            :disabled="!$wire.terms"
                            :class="$wire.terms ? 'bg-black hover:bg-zinc-900 text-white cursor-pointer shadow-md active:scale-98' : 'bg-stone-800 text-stone-500 cursor-not-allowed border border-stone-700/50'"
                            class="w-full max-w-[240px] font-medium py-2.5 px-6 uppercase tracking-widest text-xs transition-all duration-200">
                        Ingresar
                    </button>
                </div>

            </form>
        </div>

    </div>

    <!-- MODAL AVISO DE PRIVACIDAD CON TU TEXTO REAL -->
    <div x-show="openPrivacyModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="openPrivacyModal = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" 
         style="display: none;">
        
        <div class="fixed inset-0" @click="openPrivacyModal = false"></div>

        <div class="relative max-w-2xl w-full bg-[#241c1b] border border-stone-800 rounded-xl p-6 sm:p-8 shadow-2xl z-10 max-h-[85vh] flex flex-col">
            
            <div class="flex items-center justify-between border-b border-stone-800 pb-3 mb-4">
                <div>
                    <h3 class="text-lg font-serif tracking-wider uppercase text-white">Aviso de Privacidad</h3>
                    <p class="text-[10px] text-stone-400 uppercase tracking-wider">Última actualización: {{ date('d/m/Y') }}</p>
                </div>
                <button type="button" @click="openPrivacyModal = false" class="text-stone-400 hover:text-white bg-stone-800/60 rounded-full p-1.5 transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="overflow-y-auto text-xs text-stone-300 space-y-5 pr-2 leading-relaxed font-sans">
                <section>
                    <h4 class="text-xs font-semibold text-stone-100 uppercase tracking-wider mb-1">1. Identidad y Domicilio del Responsable</h4>
                    <p>
                        <strong>FIND ME</strong> es responsable del tratamiento y protección de sus datos personales. Este Aviso de Privacidad regula la forma en que recolectamos, utilizamos, almacenamos y protegemos la información proporcionada por los usuarios en nuestra plataforma de objetos perdidos y encontrados.
                    </p>
                </section>

                <section>
                    <h4 class="text-xs font-semibold text-stone-100 uppercase tracking-wider mb-1">2. Datos Personales Recabados</h4>
                    <p class="mb-1">Para el correcto funcionamiento del servicio, recopilamos los siguientes datos:</p>
                    <ul class="list-disc list-inside space-y-1 text-stone-400 pl-1">
                        <li>Nombre de usuario y dirección de correo electrónico al registrarse.</li>
                        <li>Fotografías, ubicaciones e información general de los objetos reportados (perdidos o encontrados).</li>
                        <li>Mensajes e interacciones enviados a través del chat privado dentro de la plataforma.</li>
                    </ul>
                </section>

                <section>
                    <h4 class="text-xs font-semibold text-stone-100 uppercase tracking-wider mb-1">3. Finalidad del Tratamiento de Datos</h4>
                    <p class="mb-1">Los datos personales recabados serán utilizados para las siguientes finalidades principales:</p>
                    <ul class="list-disc list-inside space-y-1 text-stone-400 pl-1">
                        <li>Permitir la publicación y búsqueda de objetos perdidos y encontrados.</li>
                        <li>Facilitar la comunicación directa y segura entre usuarios a través de la mensajería interna.</li>
                        <li>Garantizar la seguridad de los datos de acceso mediante autenticación y encriptación.</li>
                        <li>Prevenir usos indebidos, fraudes o comportamientos inapropiados en la plataforma.</li>
                    </ul>
                </section>

                <section>
                    <h4 class="text-xs font-semibold text-stone-100 uppercase tracking-wider mb-1">4. Protección y Seguridad de los Datos</h4>
                    <p>
                        Implementamos medidas de seguridad técnicas, administrativas y físicas para proteger sus datos personales contra daño, pérdida, alteración, destrucción o el uso, acceso o tratamiento no autorizado. Las contraseñas de las cuentas están encriptadas mediante algoritmos de seguridad estándar.
                    </p>
                </section>

                <section>
                    <h4 class="text-xs font-semibold text-stone-100 uppercase tracking-wider mb-1">5. Derechos ARCO (Acceso, Rectificación, Cancelación y Oposición)</h4>
                    <p>
                        Como usuario, usted tiene derecho a acceder a sus datos personales en nuestro poder, rectificarlos en caso de ser inexactos o incompletos, cancelarlos cuando considere que no se requieren para las finalidades señaladas, u oponerse al tratamiento de los mismos. Puede editar o eliminar sus publicaciones en cualquier momento desde su panel de control.
                    </p>
                </section>

                <section>
                    <h4 class="text-xs font-semibold text-stone-100 uppercase tracking-wider mb-1">6. Transferencia de Datos</h4>
                    <p>
                        <strong>FIND ME</strong> no vende, alquila ni comparte sus datos personales con terceros no autorizados bajo ningún concepto, salvo por requerimientos judiciales o legales pertinentes.
                    </p>
                </section>

                <section>
                    <h4 class="text-xs font-semibold text-stone-100 uppercase tracking-wider mb-1">7. Modificaciones al Aviso de Privacidad</h4>
                    <p>
                        Nos reservamos el derecho de efectuar en cualquier momento modificaciones o actualizaciones al presente Aviso de Privacidad. Cualquier cambio será publicado dentro de esta misma sección.
                    </p>
                </section>
            </div>

            <div class="border-t border-stone-800 pt-4 mt-4 flex items-center justify-between">
                <span class="text-[11px] text-stone-500">FIND ME &copy; {{ date('Y') }}</span>
                <button type="button" 
                        @click="$wire.terms = true; openPrivacyModal = false" 
                        class="px-5 py-2 bg-white text-black rounded text-xs font-medium uppercase tracking-widest hover:bg-stone-200 transition-colors cursor-pointer">
                    Aceptar y cerrar
                </button>
            </div>
        </div>
    </div>
</div>