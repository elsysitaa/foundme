<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public Item $item;
    public string $title = '';
    public string $location = '';
    public string $status = 'perdido';
    public string $category_id = '';
    public $newImage;

    // Recibimos directamente el modelo $item ya resuelto por Laravel
    public function mount(Item $item): void
    {
        // Seguridad: solo el dueño de la publicación puede editarla
        if (auth()->id() !== $item->user_id) {
            abort(403);
        }

        $this->item = $item;
        $this->title = $item->title;
        $this->location = $item->location;
        $this->status = $item->status;
        $this->category_id = (string) $item->category_id;
    }

    public function updateItem()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'status' => 'required|in:perdido,encontrado',
            'category_id' => 'required|exists:categories,id',
            'newImage' => 'nullable|image|max:2048',
        ]);

        $imagePath = $this->item->image_path;

        if ($this->newImage) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $this->newImage->store('items', 'public');
        }

        $this->item->update([
            'title' => $this->title,
            'location' => $this->location,
            'status' => $this->status,
            'category_id' => $this->category_id,
            'image_path' => $imagePath,
        ]);

        return $this->redirect(route('items.show', $this->item->id), navigate: true);
    }

    public function deleteItem()
    {
        if (auth()->id() !== $this->item->user_id) {
            abort(403);
        }

        if ($this->item->image_path && Storage::disk('public')->exists($this->item->image_path)) {
            Storage::disk('public')->delete($this->item->image_path);
        }

        $this->item->delete();

        return $this->redirect(route('dashboard'), navigate: true);
    }

    public function with(): array
    {
        return [
            'categories' => Category::where('status', 1)->get(),
        ];
    }
}; ?>

<div class="max-w-2xl mx-auto px-4 py-12 font-sans text-white">
    
    <div class="mb-6">
        <a href="{{ route('items.show', $item->id) }}" wire:navigate class="inline-flex items-center space-x-2 text-xs uppercase tracking-widest text-stone-400 hover:text-white transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
            </svg>
            <span>Volver a la publicación</span>
        </a>
    </div>

    <div class="bg-zinc-900/40 rounded-xl border border-zinc-800/60 p-6 md:p-8 space-y-6">
        
        <div>
            <h1 class="text-2xl font-serif font-bold text-stone-100">Editar Reporte</h1>
            <p class="text-xs text-stone-400 mt-1">Modifica los detalles del objeto o elimina la publicación.</p>
        </div>

        <form wire:submit="updateItem" class="space-y-5">
            
            <!-- Título -->
            <div class="space-y-1">
                <label class="text-[10px] uppercase tracking-widest text-stone-400 font-semibold">Título del objeto</label>
                <input type="text" wire:model="title" 
                       class="w-full bg-zinc-800/80 border border-zinc-700/80 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-stone-400">
                @error('title') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Categoría y Estado -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] uppercase tracking-widest text-stone-400 font-semibold">Categoría</label>
                    <select wire:model="category_id" 
                            class="w-full bg-zinc-800/80 border border-zinc-700/80 rounded-lg px-3 py-2 text-xs text-stone-300 focus:outline-none focus:border-stone-400">
                        <option value="">Selecciona categoría</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] uppercase tracking-widest text-stone-400 font-semibold">Estado</label>
                    <select wire:model="status" 
                            class="w-full bg-zinc-800/80 border border-zinc-700/80 rounded-lg px-3 py-2 text-xs text-stone-300 focus:outline-none focus:border-stone-400">
                        <option value="perdido">Perdido</option>
                        <option value="encontrado">Encontrado</option>
                    </select>
                    @error('status') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Ubicación -->
            <div class="space-y-1">
                <label class="text-[10px] uppercase tracking-widest text-stone-400 font-semibold">Ubicación</label>
                <input type="text" wire:model="location" 
                       class="w-full bg-zinc-800/80 border border-zinc-700/80 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-stone-400">
                @error('location') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Previsualización / Imagen -->
            <div class="space-y-2">
                <label class="text-[10px] uppercase tracking-widest text-stone-400 font-semibold">Imagen del objeto</label>
                
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 bg-zinc-800 rounded-lg overflow-hidden border border-zinc-700 shrink-0 flex items-center justify-center">
                        @if($newImage)
                            <img src="{{ $newImage->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif($item->image_path)
                            <img src="{{ asset('storage/' . $item->image_path) }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-stone-600 text-[9px] uppercase">Sin foto</span>
                        @endif
                    </div>

                    <input type="file" wire:model="newImage" accept="image/*" class="text-xs text-stone-400 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:bg-zinc-800 file:text-stone-300 hover:file:bg-zinc-700">
                </div>
                @error('newImage') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Botones de Guardar y Eliminar -->
            <div class="pt-4 border-t border-zinc-800/60 flex flex-col sm:flex-row items-center justify-between gap-3">
                <button type="submit" 
                        class="w-full sm:w-auto bg-white text-black hover:bg-stone-200 font-medium py-2.5 px-6 uppercase tracking-widest text-xs transition-colors rounded cursor-pointer">
                    Guardar Cambios
                </button>

                <button type="button" 
                        wire:click="deleteItem" 
                        wire:confirm="¿Estás seguro de que deseas eliminar esta publicación de forma permanente?"
                        class="w-full sm:w-auto text-red-400 hover:text-red-300 hover:bg-red-950/30 border border-red-900/40 py-2.5 px-4 rounded text-xs uppercase tracking-widest transition-colors cursor-pointer">
                    Eliminar Publicación
                </button>
            </div>

        </form>

    </div>
</div>