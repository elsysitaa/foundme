<?php

use Livewire\Volt\Component;
use App\Models\Category;

new class extends Component {
    public string $category_name = '';

    public function createCategory(): void
    {
        // Notifica que la función no está disponible
        $this->dispatch('swal:unavailable');
        $this->reset('category_name');
    }

    public function toggleStatus(int $id): void
    {
        $category = Category::findOrFail($id);
        $category->status = !$category->status;
        $category->save();
    }

    public function deleteCategory(int $id): void
    {
        Category::destroy($id);
        $this->dispatch('swal:deleted');
    }

    public function with(): array
    {
        return [
            'categories' => Category::latest()->get(),
        ];
    }
}; ?>

<div class="max-w-5xl mx-auto px-4 py-8 font-sans">
    
    <div class="mb-8 border-b border-zinc-800 pb-5 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-serif text-white font-bold">Gestión de Categorías</h1>
            <p class="text-xs text-stone-400 mt-1">Añade o deshabilita categorías para el formulario de publicación.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="text-xs text-stone-400 hover:text-white uppercase tracking-widest">
            &larr; Volver al Panel
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Formulario para Nueva Categoría -->
        <div class="bg-zinc-900/60 border border-zinc-800 p-6 rounded-2xl h-fit space-y-4">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-stone-200">Nueva Categoría</h2>
            
            <form wire:submit.prevent="createCategory" class="space-y-4">
                <div>
                    <label class="block text-xs text-stone-400 mb-1">Nombre</label>
                    <input type="text" 
                           wire:model="category_name" 
                           placeholder="Ej: Electrónica, Mascotas..." 
                           class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-stone-400">
                </div>

                <button type="submit" class="w-full py-2.5 bg-white text-black font-semibold text-xs rounded-lg hover:bg-stone-200 transition-colors cursor-pointer">
                    Guardar Categoría
                </button>
            </form>
        </div>

        <!-- Tabla de Categorías Registradas -->
        <div class="md:col-span-2 bg-zinc-900/60 border border-zinc-800 rounded-2xl p-6">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-stone-200 mb-4">Categorías Existentes</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-zinc-800 text-stone-500 uppercase tracking-wider">
                            <th class="pb-3">Nombre</th>
                            <th class="pb-3">Estado</th>
                            <th class="pb-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/50">
                        @forelse($categories as $cat)
                            <tr>
                                <td class="py-3 text-stone-200 font-medium">
                                    {{ $cat->category_name }}
                                </td>
                                <td class="py-3">
                                    <button wire:click="toggleStatus({{ $cat->id }})" 
                                            class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider transition-colors cursor-pointer {{ $cat->status ? 'bg-emerald-950 text-emerald-400 border border-emerald-800' : 'bg-stone-800 text-stone-500' }}">
                                        {{ $cat->status ? 'Activa' : 'Inactiva' }}
                                    </button>
                                </td>
                                <td class="py-3 text-right space-x-2">
                                    <button type="button" 
                                            onclick="confirmarEliminacionCategoria({{ $cat->id }})" 
                                            class="text-red-400 hover:text-red-300 transition-colors cursor-pointer">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-stone-500">No hay categorías registradas aún.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Scripts dentro del contenedor principal -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmarEliminacionCategoria(id) {
            Swal.fire({
                title: '¿Eliminar categoría?',
                text: "Los elementos asociados a esta categoría podrían quedar sin clasificación.",
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
                    @this.call('deleteCategory', id);
                }
            });
        }

        window.addEventListener('swal:unavailable', () => {
            Swal.fire({
                title: 'Función no disponible',
                text: 'La creación de categorías aún no está habilitada.',
                icon: 'info',
                background: '#18181b',
                color: '#f5f5f4',
                iconColor: '#3b82f6',
                confirmButtonColor: '#27272a',
                confirmButtonText: 'Entendido',
                customClass: {
                    popup: 'rounded-xl border border-zinc-800 shadow-2xl'
                }
            });
        });

        window.addEventListener('swal:deleted', () => {
            Swal.fire({
                title: '¡Eliminada!',
                text: 'La categoría fue eliminada correctamente.',
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
</div>