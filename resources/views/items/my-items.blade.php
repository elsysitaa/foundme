<?php

use Livewire\Volt\Component;
use App\Models\Item;

new class extends Component {
    public function with(): array
    {
        return [
            'myItems' => Item::with('category')
                ->where('user_id', auth()->id())
                ->latest()
                ->get(),
        ];
    }
}; ?>

<div class="max-w-6xl mx-auto px-4 py-8 font-sans text-white">
    
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-serif font-bold text-stone-100">Mis Reportes</h1>
            <p class="text-xs text-stone-400 mt-1">Gestiona los objetos que has publicado en la plataforma.</p>
        </div>

        <a href="{{ route('items.create') }}" wire:navigate class="bg-white text-black hover:bg-stone-200 text-xs font-semibold uppercase tracking-wider px-4 py-2 rounded transition-colors">
            + Nuevo Reporte
        </a>
    </div>

    @if($myItems->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($myItems as $item)
                <div class="bg-zinc-900/40 border border-zinc-800/60 rounded-xl overflow-hidden hover:border-zinc-700 transition-all flex flex-col justify-between">
                    <div>
                        <div class="h-40 bg-zinc-800/40 relative overflow-hidden flex items-center justify-center">
                            @if($item->image_path)
                                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-stone-600 text-[10px] uppercase tracking-widest">Sin foto</span>
                            @endif

                            <span class="absolute top-3 left-3 text-[10px] uppercase tracking-widest font-semibold px-2.5 py-1 rounded-full shadow 
                                {{ $item->status === 'perdido' ? 'bg-stone-900 text-red-400 border border-red-900/40' : 'bg-white text-black' }}">
                                {{ $item->status }}
                            </span>
                        </div>

                        <div class="p-4 space-y-2">
                            <h3 class="text-base font-serif text-stone-100 font-semibold line-clamp-1">
                                {{ $item->title }}
                            </h3>
                            <p class="text-xs text-stone-400 line-clamp-1">📍 {{ $item->location }}</p>
                        </div>
                    </div>

                    <div class="p-4 pt-0 flex items-center justify-between border-t border-zinc-800/30 mt-3 text-xs">
                        <a href="{{ route('items.show', $item->id) }}" wire:navigate class="text-stone-400 hover:text-white text-[11px]">
                            Ver detalle
                        </a>

                        <a href="{{ route('items.edit', $item->id) }}" wire:navigate class="bg-zinc-800 hover:bg-zinc-700 text-stone-200 border border-zinc-700/60 px-3 py-1 rounded text-[10px] uppercase tracking-wider transition-colors">
                            Editar
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16 bg-zinc-900/30 rounded-xl border border-zinc-800/40 space-y-3">
            <p class="text-stone-400 text-sm">Aún no has registrado ninguna publicación.</p>
            <a href="{{ route('items.create') }}" wire:navigate class="text-xs text-stone-300 hover:underline uppercase tracking-wider">
                Publicar un objeto ahora
            </a>
        </div>
    @endif
</div>