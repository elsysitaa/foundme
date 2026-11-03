<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Rules\ProfanityFilter;

new class extends Component {
    use WithFileUploads;

    // Propiedades del formulario
    public string $title = '';
    public string $status = 'perdido';
    public string $location = '';
    public string $category_name = ''; // Reemplaza a category_id para permitir entrada libre
    public $image;
    

    public array $categoriesList = [];

    public function mount(): void
    {
        if (Schema::hasTable('categories')) {
            $this->categoriesList = Category::where('status', 1)
                ->pluck('category_name')
                ->toArray();
        }
    }

    public function save(): void
    {
        // 1. Validamos los campos requeridos
        $this->validate([
            'title'         => ['required', 'min:3', 'max:255', new ProfanityFilter()],
            'status'        => 'required|in:perdido,encontrado',
            'location'      => ['required', 'min:3', new ProfanityFilter()],
            'category_name' => 'nullable|string|max:100',
            'image'         => 'nullable|image|max:2048', // Máximo 2MB
        ]);

        // 2. Procesamos la categoría (Se busca o se crea automáticamente)
        $categoryId = null;
        if (!empty(trim($this->category_name))) {
            $formattedName = Str::title(trim($this->category_name));
            
            $category = Category::firstOrCreate(
                ['category_name' => $formattedName],
                ['status' => 1]
            );

            $categoryId = $category->id;
        }

        // 3. Procesamos la imagen si el usuario subió una
        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('items', 'public');
        }

        // 4. Guardar en Base de Datos
        if (class_exists(Item::class)) {
            Item::create([
                'title'       => $this->title,
                'status'      => $this->status,
                'location'    => $this->location,
                'category_id' => $categoryId,
                'image_path'  => $imagePath,
                'user_id'     => auth()->id(),
            ]);
        }

        session()->flash('message', '¡Reporte creado con éxito!');

        // 5. Redirigimos al Dashboard usando wire:navigate
        $this->redirect(route('dashboard'), navigate: true);
    }
}; ?>

<div class="max-w-2xl mx-auto px-6 py-12 text-stone-100 font-sans">
    <div class="mb-8">
        <h2 class="text-2xl sm:text-3xl font-serif font-bold tracking-wide text-stone-100">Reportar Objeto</h2>
        <p class="text-sm text-stone-300 mt-1">Llena la información detallada del artículo a continuación</p>
    </div>

    <form wire:submit="save" class="space-y-6 bg-zinc-900/60 p-6 sm:p-8 rounded-xl border border-zinc-800">
        
        <!-- Nombre del objeto -->
        <div class="space-y-2">
            <label for="title_input" class="block text-sm font-semibold tracking-wide text-stone-200">
                ¿Qué objeto es?
            </label>
            <input wire:model="title" 
                   type="text" 
                   id="title_input"
                   placeholder="Ej. Llaves con llavero de cuero" 
                   required
                   class="w-full bg-[#1b1514] text-stone-100 px-4 py-2.5 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-stone-400 focus:outline-none font-sans text-base transition-all">
            @error('title') <span class="text-xs text-red-400 font-medium block">{{ $message }}</span> @enderror
        </div>

        <!-- Estado -->
        <div class="space-y-2">
            <label for="status_select" class="block text-sm font-semibold tracking-wide text-stone-200">
                Estado
            </label>
            <select wire:model="status" 
                    id="status_select"
                    required
                    class="w-full bg-[#1b1514] text-stone-100 px-4 py-2.5 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-stone-400 focus:outline-none font-sans text-base transition-all">
                <option value="perdido">Perdido</option>
                <option value="encontrado">Encontrado</option>
            </select>
            @error('status') <span class="text-xs text-red-400 font-medium block">{{ $message }}</span> @enderror
        </div>

        <!-- Ubicación -->
        <div class="space-y-2">
            <label for="location_input" class="block text-sm font-semibold tracking-wide text-stone-200">
                ¿Dónde se vio por última vez?
            </label>
            <input wire:model="location" 
                   type="text" 
                   id="location_input"
                   placeholder="Ej. Parque Central, cerca de las bancas" 
                   required
                   class="w-full bg-[#1b1514] text-stone-100 px-4 py-2.5 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-stone-400 focus:outline-none font-sans text-base transition-all">
            @error('location') <span class="text-xs text-red-400 font-medium block">{{ $message }}</span> @enderror
        </div>

        <!-- Campo de Categoría Dinámico con Datalist -->
        <div class="space-y-2">
            <label for="category_input" class="block text-sm font-semibold tracking-wide text-stone-200">Categoría</label>
            <input type="text" 
                   id="category_input"
                   list="categories_list" 
                   wire:model="category_name" 
                   placeholder="Escribe o selecciona una categoría..." 
                   autocomplete="off"
                   aria-describedby="category_help"
                   class="w-full bg-[#1b1514] text-stone-100 px-4 py-2.5 border border-zinc-700 rounded-lg focus:ring-2 focus:ring-stone-400 focus:outline-none font-sans text-base transition-all">
            
            <datalist id="categories_list">
                @foreach ($categoriesList as $cat)
                    <option value="{{ $cat }}"></option>
                @endforeach
            </datalist>

            <p id="category_help" class="text-xs text-stone-400">Selecciona de la lista o escribe una nueva para registrarla de inmediato.</p>
            @error('category_name') <span class="text-xs text-red-400 font-medium block">{{ $message }}</span> @enderror
        </div>

        <!-- Subida de Imagen -->
        <div class="space-y-2">
            <label for="image_input" class="block text-sm font-semibold tracking-wide text-stone-200">Foto del Objeto</label>
            <input wire:model="image" 
                   type="file" 
                   id="image_input"
                   accept="image/*"
                   aria-describedby="image_help"
                   class="w-full text-stone-300 font-sans text-sm file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-white file:text-black hover:file:bg-stone-200 file:cursor-pointer transition-colors">
            <p id="image_help" class="text-xs text-stone-400">Tamaño máximo: 2MB. Formatos permitidos: JPG, PNG.</p>
            @error('image') <span class="text-xs text-red-400 font-medium block">{{ $message }}</span> @enderror
        </div>

        <!-- Botones de Acción -->
        <div class="pt-4 flex items-center justify-end gap-4">
            <a href="{{ route('dashboard') }}" 
               wire:navigate 
               class="text-sm uppercase tracking-wider font-semibold text-stone-300 hover:text-white transition-colors focus:outline-none focus:underline">
                Cancelar
            </a>
            <button type="submit" 
                    class="bg-white text-black hover:bg-stone-200 font-semibold py-2.5 px-6 uppercase tracking-wider text-sm transition-all duration-200 rounded-lg active:scale-98 focus:ring-2 focus:ring-stone-400 focus:outline-none">
                Publicar Reporte
            </button>
        </div>
    </form>
</div>