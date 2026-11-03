<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public string $activeTab = 'reports';

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public $avatar;

    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
        $this->phone = $user->phone ?? '';
    }

    // Subida e imagen instantánea al seleccionar archivo directo desde el avatar
    public function updatedAvatar(): void
    {
        $this->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $user = auth()->user();

        // Borra la foto previa para evitar basura en el servidor
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $path = $this->avatar->store('avatars', 'public');
        $user->avatar_path = $path;
        $user->save();

        session()->flash('status', 'Foto de perfil actualizada.');
    }

    public function updateProfile(): void
    {
        $user = auth()->user();

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->name = $this->name;
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->save();

        session()->flash('status', 'Perfil actualizado con éxito.');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);
        session()->flash('password_status', 'Contraseña actualizada correctamente.');
    }

    public function with(): array
    {
        $user = auth()->user();

        return [
            'user' => $user,
            'myItems' => $user ? $user->items()->latest()->get() : collect(),
            'stats' => [
                'total' => $user ? $user->items()->count() : 0,
                'lost' => $user ? $user->items()->where('status', 'perdido')->count() : 0,
                'found' => $user ? $user->items()->where('status', 'encontrado')->count() : 0,
                'resolved' => $user ? $user->items()->where('status', 'entregado')->count() : 0,
            ]
        ];
    }
}; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 font-sans">
    
    <!-- Header / Tarjeta de Presentación con Subida Directa y Modal Alpine.js -->
    <div x-data="{ openAvatarModal: false }" class="bg-zinc-900/60 border border-zinc-800/80 rounded-2xl p-6 sm:p-8">
        <div class="flex flex-col md:flex-row items-center md:items-start justify-between gap-6">
            
            <!-- Info del Usuario -->
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 text-center sm:text-left">
                <div class="relative">
                    
                    <!-- Input nativo oculto conectado a Livewire -->
                    <input type="file" id="quick-avatar-input" wire:model="avatar" accept="image/*" class="hidden">

                    <!-- Avatar contenedor -->
                    <div class="relative w-24 h-24 rounded-full overflow-hidden bg-zinc-800 border-2 border-stone-700 flex items-center justify-center text-stone-400 font-bold text-2xl uppercase group shadow-lg">
                        
                        @if($user->avatar_path)
                            <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            {{ substr($user->name ?? 'U', 0, 2) }}
                        @endif

                        <!-- Capa Interactiva Overlay al pasar el cursor -->
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                            
                            <!-- Botón Subir Foto (Abre el selector de archivos) -->
                            <label for="quick-avatar-input" 
                                   title="Cambiar foto de perfil" 
                                   class="p-2 bg-stone-800/80 hover:bg-stone-700 text-white rounded-full transition-colors cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </label>

                            <!-- Botón Lupa (Abre la foto en grande) -->
                            <button type="button" 
                                    @click="openAvatarModal = true" 
                                    title="Ver foto en grande" 
                                    class="p-2 bg-stone-800/80 hover:bg-stone-700 text-white rounded-full transition-colors cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                </svg>
                            </button>
                        </div>

                        <!-- Spinner de carga mientras la foto sube al servidor -->
                        <div wire:loading wire:target="avatar" class="absolute inset-0 bg-black/80 flex items-center justify-center">
                            <svg class="animate-spin h-6 w-6 text-stone-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>

                    </div>
                </div>

                <div class="space-y-1">
                    <h1 class="text-2xl sm:text-3xl font-serif font-bold text-stone-100 tracking-wide">
                        {{ $user->name }}
                    </h1>
                    <p class="text-xs text-stone-400 flex items-center justify-center sm:justify-start gap-2">
                        <span> {{ $user->email }}</span>
                        @if($user->phone)
                            <span>•  {{ $user->phone }}</span>
                        @endif
                    </p>
                    <p class="text-[11px] text-stone-500 pt-1">
                        Miembro desde {{ $user->created_at ? $user->created_at->format('M Y') : 'Recientemente' }}
                    </p>
                </div>
            </div>

            <!-- Estadísticas Rápidas -->
            <div class="grid grid-cols-4 gap-3 bg-zinc-800/50 p-3 rounded-xl border border-zinc-700/50 text-center w-full md:w-auto">
                <div class="px-2">
                    <span class="block text-lg font-bold text-stone-100">{{ $stats['total'] }}</span>
                    <span class="text-[10px] uppercase tracking-wider text-stone-400">Total</span>
                </div>
                <div class="px-2 border-l border-zinc-700/60">
                    <span class="block text-lg font-bold text-red-400">{{ $stats['lost'] }}</span>
                    <span class="text-[10px] uppercase tracking-wider text-stone-400">Perdidos</span>
                </div>
                <div class="px-2 border-l border-zinc-700/60">
                    <span class="block text-lg font-bold text-emerald-400">{{ $stats['found'] }}</span>
                    <span class="text-[10px] uppercase tracking-wider text-stone-400">Hallados</span>
                </div>
                <div class="px-2 border-l border-zinc-700/60">
                    <span class="block text-lg font-bold text-stone-300">{{ $stats['resolved'] }}</span>
                    <span class="text-[10px] uppercase tracking-wider text-stone-400">Resueltos</span>
                </div>
            </div>

        </div>

        <!-- Modal Lightbox (Ver foto de perfil en grande) -->
        <div x-show="openAvatarModal" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="openAvatarModal = false"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
             style="display: none;">
            
            <div class="fixed inset-0" @click="openAvatarModal = false"></div>

            <div class="relative max-w-xl w-full bg-zinc-900 border border-zinc-800 rounded-2xl p-3 shadow-2xl z-10 overflow-hidden flex flex-col items-center">
                <!-- Botón de Cerrar -->
                <button type="button" 
                        @click="openAvatarModal = false"
                        class="absolute top-4 right-4 text-stone-400 hover:text-white bg-black/60 hover:bg-black/90 rounded-full p-2 transition-all z-20 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Vista Ampliada -->
                @if($user->avatar_path)
                    <img src="{{ asset('storage/' . $user->avatar_path) }}" 
                         alt="{{ $user->name }}" 
                         class="w-full max-h-[75vh] object-contain rounded-xl">
                @else
                    <div class="w-56 h-56 rounded-full bg-zinc-800 border-2 border-stone-700 flex items-center justify-center text-stone-400 font-bold text-5xl uppercase my-6">
                        {{ substr($user->name ?? 'U', 0, 2) }}
                    </div>
                @endif

                <p class="text-xs text-stone-400 py-2.5 font-medium">
                    {{ $user->name }}
                </p>
            </div>
        </div>
    </div>

    <!-- Navegación por Pestañas -->
    <div class="flex border-b border-zinc-800 gap-6">
        <button type="button" 
                wire:click="$set('activeTab', 'reports')"
                class="pb-3 text-xs uppercase tracking-widest font-semibold transition-all cursor-pointer border-b-2 
                {{ $activeTab === 'reports' ? 'border-stone-100 text-stone-100' : 'border-transparent text-stone-500 hover:text-stone-300' }}">
             Mis Reportes ({{ $myItems->count() }})
        </button>
        
        <button type="button" 
                wire:click="$set('activeTab', 'settings')"
                class="pb-3 text-xs uppercase tracking-widest font-semibold transition-all cursor-pointer border-b-2 
                {{ $activeTab === 'settings' ? 'border-stone-100 text-stone-100' : 'border-transparent text-stone-500 hover:text-stone-300' }}">
             Ajustes de Cuenta
        </button>
    </div>

    <!-- Pestaña 1: Mis Reportes -->
    @if($activeTab === 'reports')
        <div>
            @if($myItems->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($myItems as $item)
                        <div class="bg-zinc-900/40 border border-zinc-800/60 rounded-xl overflow-hidden flex flex-col justify-between hover:border-zinc-700 transition-all">
                            <div>
                                <div class="h-40 bg-zinc-800/40 relative flex items-center justify-center">
                                    @if($item->image_path)
                                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-stone-600 text-[10px] uppercase tracking-widest">Sin foto</span>
                                    @endif

                                    <span class="absolute top-3 left-3 text-[10px] uppercase tracking-widest font-semibold px-2.5 py-1 rounded-full shadow 
                                        {{ $item->status === 'perdido' ? 'bg-stone-900 text-red-400 border border-red-900/40' : ($item->status === 'encontrado' ? 'bg-white text-black' : 'bg-emerald-900 text-emerald-300') }}">
                                        {{ $item->status }}
                                    </span>
                                </div>

                                <div class="p-4 space-y-2">
                                    <span class="text-[10px] uppercase tracking-widest text-stone-500 font-medium">
                                        #{{ $item->category->category_name ?? 'General' }}
                                    </span>
                                    <h3 class="text-base font-serif text-stone-100 font-semibold line-clamp-1">
                                        {{ $item->title }}
                                    </h3>
                                    <p class="text-xs text-stone-400 line-clamp-1">
                                        📍 {{ $item->location }}
                                    </p>
                                </div>
                            </div>

                            <div class="p-4 pt-0 flex items-center justify-between border-t border-zinc-800/40 mt-3 text-xs">
                                <span class="text-stone-500 text-[11px]">{{ $item->created_at->diffForHumans() }}</span>
                                <a href="{{ route('items.show', [$item->id, 'from' => 'profile']) }}" wire:navigate class="text-stone-300 hover:text-white text-[10px] uppercase tracking-wider font-medium">Ver</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-zinc-900/30 rounded-xl border border-zinc-800/40 space-y-3">
                    <p class="text-stone-400 text-sm">Aún no has creado ningún reporte de objetos.</p>
                    <a href="{{ route('items.create') }}" wire:navigate class="inline-block px-4 py-2 bg-white text-black rounded-lg text-xs font-semibold uppercase tracking-wider hover:bg-stone-200 transition-all">
                        Publicar Reporte
                    </a>
                </div>
            @endif
        </div>
    @endif

    <!-- Pestaña 2: Ajustes de Cuenta -->
    @if($activeTab === 'settings')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <form wire:submit="updateProfile" class="bg-zinc-900/40 border border-zinc-800/60 p-6 rounded-xl space-y-4">
                <h2 class="text-lg font-serif font-semibold text-stone-100 border-b border-zinc-800 pb-2">
                    Información de Perfil
                </h2>

                @if (session('status'))
                    <div class="p-3 bg-emerald-950/60 border border-emerald-800/50 rounded-lg text-emerald-300 text-xs">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="space-y-1">
                    <label class="text-[10px] uppercase tracking-widest text-stone-400 font-semibold block">Nombre Completo</label>
                    <input type="text" wire:model="name" class="w-full bg-zinc-800/80 border border-zinc-700/80 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-stone-400">
                    @error('name') <span class="text-red-400 text-[11px]">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] uppercase tracking-widest text-stone-400 font-semibold block">Correo Electrónico</label>
                    <input type="email" wire:model="email" class="w-full bg-zinc-800/80 border border-zinc-700/80 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-stone-400">
                    @error('email') <span class="text-red-400 text-[11px]">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] uppercase tracking-widest text-stone-400 font-semibold block">Teléfono de Contacto</label>
                    <input type="text" wire:model="phone" placeholder="+52 123 456 7890" class="w-full bg-zinc-800/80 border border-zinc-700/80 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-stone-400">
                    @error('phone') <span class="text-red-400 text-[11px]">{{ $message }}</span> @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-4 py-2 bg-white text-black rounded-lg text-xs font-semibold uppercase tracking-wider hover:bg-stone-200 transition-all cursor-pointer">
                        Guardar Cambios
                    </button>
                </div>
            </form>

            <form wire:submit="updatePassword" class="bg-zinc-900/40 border border-zinc-800/60 p-6 rounded-xl space-y-4">
                <h2 class="text-lg font-serif font-semibold text-stone-100 border-b border-zinc-800 pb-2">
                    Seguridad
                </h2>

                @if (session('password_status'))
                    <div class="p-3 bg-emerald-950/60 border border-emerald-800/50 rounded-lg text-emerald-300 text-xs">
                        {{ session('password_status') }}
                    </div>
                @endif

                <div class="space-y-1">
                    <label class="text-[10px] uppercase tracking-widest text-stone-400 font-semibold block">Contraseña Actual</label>
                    <input type="password" wire:model="current_password" class="w-full bg-zinc-800/80 border border-zinc-700/80 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-stone-400">
                    @error('current_password') <span class="text-red-400 text-[11px]">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] uppercase tracking-widest text-stone-400 font-semibold block">Nueva Contraseña</label>
                    <input type="password" wire:model="password" class="w-full bg-zinc-800/80 border border-zinc-700/80 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-stone-400">
                    @error('password') <span class="text-red-400 text-[11px]">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] uppercase tracking-widest text-stone-400 font-semibold block">Confirmar Nueva Contraseña</label>
                    <input type="password" wire:model="password_confirmation" class="w-full bg-zinc-800/80 border border-zinc-700/80 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-stone-400">
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-4 py-2 bg-zinc-800 text-stone-200 border border-zinc-700 rounded-lg text-xs font-semibold uppercase tracking-wider hover:bg-zinc-700 transition-all cursor-pointer">
                        Actualizar Contraseña
                    </button>
                </div>
            </form>

        </div>
    @endif

</div>