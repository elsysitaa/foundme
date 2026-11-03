<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Item;
use App\Models\Category;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $selectedCategory = '';
    public string $statusFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    // Método para limpiar todos los filtros a su estado inicial
    public function clearFilters(): void
    {
        $this->search = '';
        $this->selectedCategory = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function with(): array
    {
        $query = Item::with(['category', 'user']);

        if (!empty(trim($this->search))) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('location', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->selectedCategory)) {
            $query->where('category_id', $this->selectedCategory);
        }

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        return [
            'items' => $query->latest()->paginate(9),
            'categories' => Category::where('status', 1)->get(),
        ];
    }
}; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 font-sans relative">
    
    <!-- Encabezado con Botón Admin si aplica -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-800/80 pb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-stone-100 tracking-wide">
                Objetos Reportados
            </h1>
            <p class="text-base text-stone-400 mt-1">
                Encuentra o reporta pertenencias dentro de la comunidad.
            </p>
        </div>

        @if(auth()->check() && auth()->user()->is_admin)
            <div>
                <a href="{{ route('admin.dashboard') }}" 
                   wire:navigate 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-stone-800 hover:bg-stone-700 text-stone-200 border border-stone-700/80 rounded-lg text-base font-semibold uppercase tracking-wider transition-colors">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Panel Admin</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Barra de Búsqueda y Filtros -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 bg-zinc-900/60 p-4 rounded-xl border border-zinc-800/80 mb-8">
        
        <!-- Campo de Búsqueda por Texto -->
        <div class="md:col-span-2 relative">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Buscar por título o ubicación..." 
                   class="w-full bg-zinc-800/80 border border-zinc-700/80 rounded-lg pl-10 pr-8 py-2.5 text-base text-white placeholder-stone-500 focus:outline-none focus:border-stone-400 transition-all">
            
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute left-3 top-3 text-stone-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>

            @if($search !== '')
                <button wire:click="$set('search', '')" class="absolute right-3 top-2.5 text-stone-500 hover:text-white text-base">
                    ✕
                </button>
            @endif
        </div>

        <!-- Filtro por Categoría -->
        <div>
            <select wire:model.live="selectedCategory" 
                    class="w-full bg-zinc-800/80 border border-zinc-700/80 rounded-lg px-3 py-2.5 text-base text-stone-300 focus:outline-none focus:border-stone-400">
                <option value="">Todas las categorías</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filtro por Estado -->
        <div>
            <select wire:model.live="statusFilter" 
                    class="w-full bg-zinc-800/80 border border-zinc-700/80 rounded-lg px-3 py-2.5 text-base text-stone-300 focus:outline-none focus:border-stone-400">
                <option value="">Todos los estados</option>
                <option value="perdido">Perdidos</option>
                <option value="encontrado">Encontrados</option>
            </select>
        </div>
    </div>

    <!-- Indicador de Carga -->
    <div wire:loading.flex class="justify-center py-6">
        <span class="text-base text-stone-400 uppercase tracking-widest animate-pulse">
            Buscando objetos...
        </span>
    </div>

    <!-- Lista / Grilla de Objetos -->
    <div wire:loading.remove>
        @if($items->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($items as $item)
                    <div class="bg-zinc-900/40 border border-zinc-800/60 rounded-xl overflow-hidden hover:border-zinc-700 transition-all flex flex-col justify-between">
                        <div>
                            <div class="h-48 bg-zinc-800/40 relative overflow-hidden flex items-center justify-center">
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

                            <div class="p-5 space-y-3">
                                <span class="text-[10px] uppercase tracking-widest text-stone-500 font-medium">
                                    #{{ $item->category->category_name ?? 'General' }}
                                </span>
                                
                                <h3 class="text-lg font-serif text-stone-100 font-semibold line-clamp-1">
                                    {{ $item->title }}
                                </h3>

                                <p class="text-base text-stone-400 line-clamp-1 flex items-center gap-1">
                                    <span>📍</span> {{ $item->location }}
                                </p>
                            </div>
                        </div>

                        <div class="p-5 pt-0 flex items-center justify-between border-t border-zinc-800/30 mt-3 text-base">
                            <span class="text-stone-500 text-[11px]">{{ $item->created_at->diffForHumans() }}</span>
                            
                            <a href="{{ route('items.show', $item->id) }}" wire:navigate 
                               class="text-stone-300 hover:text-white font-medium uppercase tracking-wider text-[10px] transition-colors">
                                Ver detalles &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="mt-8">
                {{ $items->links() }}
            </div>
        @else
            <!-- Estado vacío con botón de Limpiar Filtros -->
            <div class="text-center py-16 bg-zinc-900/30 rounded-xl border border-zinc-800/40 space-y-4">
                <p class="text-stone-400 text-sm">No se encontraron objetos que coincidan con tu búsqueda.</p>
                
                @if($search !== '' || $selectedCategory !== '' || $statusFilter !== '')
                    <button wire:click="clearFilters" 
                            class="inline-block px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-stone-200 border border-zinc-700 rounded-lg text-base font-semibold uppercase tracking-wider transition-colors cursor-pointer">
                         Limpiar Filtros
                    </button>
                @endif
            </div>
        @endif
    </div>

    <!-- Botón Flotante Grande (+) -->
    <a href="{{ route('items.create') }}" wire:navigate 
       title="Reportar nuevo objeto"
       class="fixed bottom-8 right-8 w-14 h-14 rounded-full bg-white text-black hover:bg-stone-200 flex items-center justify-center font-bold text-3xl shadow-2xl transition-transform duration-300 hover:rotate-90 active:scale-95 z-50 border-2 border-stone-300">
        +
    </a>
</div>