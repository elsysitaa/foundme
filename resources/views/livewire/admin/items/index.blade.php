<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Item;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $filterType = 'all';
    public string $filterStatus = 'all';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function toggleStatus(int $id): void
    {
        $item = Item::findOrFail($id);

        if (isset($item->is_active)) {
            $item->is_active = !$item->is_active;
        } elseif (isset($item->status)) {
            if (is_numeric($item->status)) {
                $item->status = (int)$item->status === 1 ? 0 : 1;
            } else {
                $item->status = strtolower((string)$item->status) === 'active' ? 'resolved' : 'active';
            }
        } elseif (isset($item->is_resolved)) {
            $item->is_resolved = !$item->is_resolved;
        }

        $item->save();
    }

    public function deleteItem(int $id): void
    {
        Item::destroy($id);
        $this->dispatch('swal:item-deleted');
    }

    public function with(): array
    {
        $query = Item::with(['user', 'category'])->latest();

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('location', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterType !== 'all') {
            $query->where('type', $this->filterType);
        }

        if ($this->filterStatus !== 'all') {
            if ($this->filterStatus === 'active') {
                $query->where(function($q) {
                    $q->where('is_active', 1)
                      ->orWhere('status', 1)
                      ->orWhere('status', 'active')
                      ->orWhere('is_resolved', 0);
                });
            } else {
                $query->where(function($q) {
                    $q->where('is_active', 0)
                      ->orWhere('status', 0)
                      ->orWhere('status', 'resolved')
                      ->orWhere('is_resolved', 1);
                });
            }
        }

        return [
            'items' => $query->paginate(10),
        ];
    }
}; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="max-w-7xl mx-auto px-4 py-8 font-sans">
    
    <!-- Encabezado -->
    <div class="mb-8 border-b border-zinc-800 pb-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-serif text-white font-bold">Gestión de Reportes</h1>
            <p class="text-xs text-stone-400 mt-1">Supervisa, filtra y administra las publicaciones de objetos perdidos y encontrados.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="text-xs text-stone-400 hover:text-white uppercase tracking-widest self-start md:self-auto">
            &larr; Volver al Panel
        </a>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="md:col-span-2">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Buscar por título, descripción o ubicación..." 
                   class="w-full bg-zinc-900/80 border border-zinc-800 rounded-xl px-4 py-2.5 text-xs text-white placeholder-stone-500 focus:outline-none focus:border-stone-500">
        </div>

        <div>
            <select wire:model.live="filterType" class="w-full bg-zinc-900/80 border border-zinc-800 rounded-xl px-3 py-2.5 text-xs text-stone-300 focus:outline-none focus:border-stone-500">
                <option value="all">Todos los tipos</option>
                <option value="lost">Solo Perdidos</option>
                <option value="found">Solo Encontrados</option>
            </select>
        </div>

        <div>
            <select wire:model.live="filterStatus" class="w-full bg-zinc-900/80 border border-zinc-800 rounded-xl px-3 py-2.5 text-xs text-stone-300 focus:outline-none focus:border-stone-500">
                <option value="all">Todos los estados</option>
                <option value="active">Activos</option>
                <option value="resolved">Resueltos / Cerrados</option>
            </select>
        </div>
    </div>

    <!-- Tabla de Reportes -->
    <div class="bg-zinc-900/60 border border-zinc-800 rounded-2xl p-6 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-zinc-800 text-stone-500 uppercase tracking-wider text-[11px]">
                        <th class="pb-3 px-2">Objeto</th>
                        <th class="pb-3 px-2">Tipo</th>
                        <th class="pb-3 px-2">Categoría</th>
                        <th class="pb-3 px-2">Reportado por</th>
                        <th class="pb-3 px-2">Fecha</th>
                        <th class="pb-3 px-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50 text-stone-300 align-middle">
                    @forelse($items as $item)
                        @php
                            // Normalizar Tipo
                            $typeVal = strtolower((string)($item->type ?? $item->type_item ?? 'lost'));
                            $isLost = in_array($typeVal, ['lost', 'perdido', '0']);

                            // Normalizar Estado
                            $rawStatus = $item->is_active ?? $item->status ?? $item->is_resolved ?? $item->activo ?? true;

                            if (is_bool($rawStatus)) {
                                $isActive = $rawStatus;
                            } elseif (is_numeric($rawStatus)) {
                                $isActive = (int)$rawStatus === 1;
                            } else {
                                $isActive = in_array(strtolower((string)$rawStatus), ['active', 'activo', 'publicado', 'open', '1']);
                            }

                            // Si la columna es is_resolved (1 = resuelto = inactivo)
                            if (isset($item->is_resolved) && !isset($item->is_active) && !isset($item->status)) {
                                $isActive = !(bool)$item->is_resolved;
                            }
                        @endphp
                        <tr>
                            <!-- Objeto (Imagen + Título) -->
                            <td class="py-4 px-2 font-medium text-stone-100 min-w-[220px]">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 flex-shrink-0">
                                        @if($item->image_path)
                                            <img src="{{ asset('storage/' . $item->image_path) }}" 
                                                 class="w-12 h-12 rounded-lg object-cover border border-zinc-800 shadow-sm">
                                        @elseif($item->image)
                                            <img src="{{ asset('storage/' . $item->image) }}" 
                                                 class="w-12 h-12 rounded-lg object-cover border border-zinc-800 shadow-sm">
                                        @else
                                            <div class="w-12 h-12 rounded-lg bg-zinc-800 flex items-center justify-center text-stone-500 text-[10px] border border-zinc-700/50">
                                                Sin foto
                                            </div>
                                        @endif
                                    </div>
                                    <div class="truncate">
                                        <a href="{{ route('items.show', $item->id) }}" wire:navigate class="hover:underline font-semibold text-white block truncate">
                                            {{ Str::limit($item->title ?? $item->name, 25) }}
                                        </a>
                                        <p class="text-[10px] text-stone-500 truncate mt-0.5">{{ Str::limit($item->location ?? 'Sin ubicación', 25) }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Tipo -->
                            <td class="py-4 px-2 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-semibold uppercase tracking-wider {{ $isLost ? 'bg-rose-950 text-rose-400 border border-rose-900/50' : 'bg-sky-950 text-sky-400 border border-sky-900/50' }}">
                                    {{ $isLost ? 'Perdido' : 'Encontrado' }}
                                </span>
                            </td>

                            <!-- Categoría -->
                            <td class="py-4 px-2 text-stone-400 whitespace-nowrap">
                                {{ $item->category->category_name ?? $item->category->name ?? 'Sin categoría' }}
                            </td>

                            <!-- Usuario -->
                            <td class="py-4 px-2 min-w-[150px]">
                                <span class="text-stone-300 font-medium block truncate">{{ $item->user->name ?? 'Usuario borrado' }}</span>
                                <p class="text-[10px] text-stone-500 truncate">{{ $item->user->email ?? '' }}</p>
                            </td>

                            <!-- Fecha -->
                            <td class="py-4 px-2 text-stone-500 whitespace-nowrap">
                                {{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}
                            </td>

                            <!-- Acciones -->
                            <td class="py-4 px-2 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end space-x-3">
                                    <button type="button" 
                                            onclick="mostrarProximamente()" 
                                            class="text-stone-400 hover:text-white transition-colors cursor-pointer">
                                        Editar
                                    </button>
                                    <button type="button" 
                                            onclick="confirmarEliminacionItem({{ $item->id }})" 
                                            class="text-red-400 hover:text-red-300 transition-colors cursor-pointer">
                                        Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-stone-500">
                                No se encontraron reportes con los criterios de búsqueda aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $items->links() }}
        </div>
    </div>
</div>

<script>
    function mostrarProximamente() {
        Swal.fire({
            title: '¡Próximamente disponible!',
            text: 'La función de edición desde el panel de administración se habilitará en una futura actualización.',
            icon: 'info',
            background: '#18181b',
            color: '#f5f5f4',
            iconColor: '#38bdf8',
            confirmButtonColor: '#3f3f46',
            confirmButtonText: 'Entendido',
            customClass: {
                popup: 'rounded-xl border border-zinc-800 shadow-2xl'
            }
        });
    }

    function confirmarEliminacionItem(id) {
        Swal.fire({
            title: '¿Eliminar reporte?',
            text: "Esta acción no se puede deshacer y se borrarán las imágenes asociadas.",
            icon: 'warning',
            showCancelButton: true,
            background: '#18181b',
            color: '#f5f5f4',
            iconColor: '#ef4444',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#27272a',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'rounded-xl border border-zinc-800 shadow-2xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('deleteItem', id);
            }
        });
    }

    window.addEventListener('swal:item-deleted', () => {
        Swal.fire({
            title: '¡Reporte Eliminado!',
            text: 'El reporte de objeto fue eliminado correctamente.',
            icon: 'success',
            background: '#18181b',
            color: '#f5f5f4',
            iconColor: '#22c55e',
            showConfirmButton: false,
            timer: 1500,
            customClass: {
                popup: 'rounded-xl border border-zinc-800 shadow-2xl'
            }
        });
    });
</script>