<?php

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\Item;

new class extends Component {
    public User $user;

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    public function with(): array
    {
        return [
            // Carga todas las publicaciones creadas por este usuario
            'userItems' => Item::where('user_id', $this->user->id)->latest()->get(),
        ];
    }
}; ?>

<div class="max-w-4xl mx-auto px-6 py-12 text-white font-sans">
    
    <!-- Botón Volver -->
    <div class="mb-6">
        <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center space-x-2 text-xs uppercase tracking-widest text-stone-400 hover:text-white transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
            </svg>
            <span>Volver al inicio</span>
        </a>
    </div>

    <!-- Encabezado del Perfil con Foto Dinámica -->
    <div class="bg-zinc-900/40 p-8 rounded-2xl border border-zinc-800/60 mb-8 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center space-x-5">
            
            <!-- Foto de Perfil o Inicial -->
            @if ($user->avatar_path)
                <img src="{{ asset('storage/' . $user->avatar_path) }}" 
                     alt="{{ $user->name }}" 
                     class="w-16 h-16 rounded-full object-cover border border-stone-700 shadow-md">
            @else
                <div class="w-16 h-16 rounded-full bg-stone-800 border border-stone-700 flex items-center justify-center text-xl font-bold uppercase text-stone-200">
                    {{ substr($user->name, 0, 1) }}
                </div>
            @endif

            <div>
                <h1 class="text-2xl font-serif text-white font-bold">{{ $user->name }}</h1>
                <p class="text-xs text-stone-400 mt-0.5">Miembro desde {{ $user->created_at->format('M Y') }}</p>
            </div>
        </div>
        
        <div class="text-right">
            <span class="text-xs uppercase tracking-widest text-stone-400 block">Reportes Publicados</span>
            <span class="text-2xl font-serif text-white font-bold">{{ $userItems->count() }}</span>
        </div>
    </div>

    <!-- Lista de Publicaciones del Autor -->
    <h2 class="text-lg font-serif text-stone-200 uppercase tracking-wider mb-4 border-b border-zinc-800 pb-2">
        Publicaciones de {{ $user->name }}
    </h2>

    @if($userItems->isEmpty())
        <div class="p-8 bg-zinc-900/20 border border-zinc-800 rounded-xl text-center text-stone-400 text-xs">
            Este usuario aún no ha realizado publicaciones.
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach($userItems as $item)
                <a href="{{ route('items.show', $item->id) }}" wire:navigate class="group bg-zinc-900/60 border border-zinc-800 hover:border-zinc-700 rounded-xl overflow-hidden flex flex-col justify-between transition-all duration-200">
                    <div>
                        @if($item->image_path)
                            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" class="w-full h-36 object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-36 bg-stone-800/40 flex items-center justify-center text-stone-600 text-xs">
                                Sin imagen
                            </div>
                        @endif
                        
                        <div class="p-4 space-y-2">
                            <span class="text-[10px] uppercase tracking-wider px-2 py-0.5 rounded font-semibold {{ $item->status === 'perdido' ? 'bg-red-950 text-red-400 border border-red-800' : 'bg-emerald-950 text-emerald-400 border border-emerald-800' }}">
                                {{ $item->status }}
                            </span>
                            <h3 class="text-sm font-semibold text-white group-hover:text-amber-400 transition-colors truncate">{{ $item->title }}</h3>
                            <p class="text-xs text-stone-400 truncate">&lfloor; {{ $item->location }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>