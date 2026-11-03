<?php

use Livewire\Volt\Component;
use App\Models\Item;
use App\Models\Conversation;
use App\Models\Comment;
use App\Models\Like;
use App\Models\ActivityLog;

new class extends Component {
    public Item $item;
    public string $newComment = '';

    public function mount(int $id): void
    {
        $this->loadItem($id);
    }

    public function loadItem(int $id): void
    {
        $this->item = Item::with(['category', 'user', 'comments.user', 'likes'])->findOrFail($id);
    }

    public function getIsLikedProperty(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return $this->item->likes->contains('user_id', auth()->id());
    }

    public function toggleLike(): void
    {
        if (!auth()->check()) {
            return;
        }

        $userId = auth()->id();

        $like = Like::where('user_id', $userId)
                    ->where('item_id', $this->item->id)
                    ->first();

        if ($like) {
            $like->delete();
        } else {
            Like::create([
                'user_id' => $userId,
                'item_id' => $this->item->id,
            ]);
        }

        $this->loadItem($this->item->id);
    }

    public function postComment(): void
    {
        if (!auth()->check()) {
            return;
        }

        $this->validate([
            'newComment' => 'required|string|max:500',
        ]);

        Comment::create([
            'user_id' => auth()->id(),
            'item_id' => $this->item->id,
            'body' => trim($this->newComment),
        ]);

        ActivityLog::create([
        'user_id'     => auth()->id(),
        'action'      => 'comment.created',
        'description' => 'Comentó en el objeto #' . $this->item->id,
        'ip_address'  => request()->ip(),
        ]);

        $this->reset('newComment');
        $this->loadItem($this->item->id);
    }

    public function startChat()
    {
        if (auth()->id() === $this->item->user_id) {
            return;
        }

        $conversation = Conversation::firstOrCreate([
            'item_id' => $this->item->id,
            'sender_id' => auth()->id(),
            'receiver_id' => $this->item->user_id,
        ]);

        return $this->redirect(route('chat.show', $conversation->id), navigate: true);
    }

    public function deleteItem(): void
    {
        if (auth()->id() !== $this->item->user_id) {
            return;
        }

        ActivityLog::create([
        'user_id'     => auth()->id(),
        'action'      => 'item.deleted',
        'description' => 'Eliminó el objeto: ' . $this->item->title,
        'ip_address'  => request()->ip(),
        ]);

        $this->item->delete();
        $this->dispatch('swal:deleted');
    }
}; ?>

<div class="max-w-4xl mx-auto px-4 py-12 text-white font-sans space-y-8">
    
    <!-- Estilos para la animación de latido -->
    <style>
        @keyframes pulse-heart {
            0%, 100% { transform: scale(1); }
            15% { transform: scale(1.25); }
            30% { transform: scale(1); }
            45% { transform: scale(1.15); }
        }
        .animate-heartbeat {
            animation: pulse-heart 1.2s infinite ease-in-out;
        }
    </style>

    <div class="mb-6">
        <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center space-x-2 text-xs uppercase tracking-widest text-stone-400 hover:text-white transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="w-4 h-4" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
            </svg>
            <span>Volver al inicio</span>
        </a>
    </div>

    <!-- Detalle Principal del Objeto -->
    <div class="bg-zinc-900/40 rounded-xl border border-zinc-800/60 overflow-hidden grid grid-cols-1 md:grid-cols-2 gap-8 p-6 md:p-8">
        
        <!-- Imagen -->
        <div class="h-64 md:h-96 bg-zinc-800/20 rounded-lg overflow-hidden flex items-center justify-center relative border border-zinc-800/40">
            @if($item->image_path)
                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
            @else
                <span class="text-stone-600 text-xs uppercase tracking-widest">Sin foto disponible</span>
            @endif

            <span class="absolute top-4 left-4 text-[10px] uppercase tracking-widest font-semibold px-2.5 py-1 rounded-full shadow-md 
                {{ $item->status === 'perdido' ? 'bg-stone-800 text-red-400 border border-red-900/30' : 'bg-white text-black' }}">
                {{ $item->status }}
            </span>
        </div>

        <!-- Información -->
        <div class="flex flex-col justify-between space-y-6">
            <div class="space-y-4">
                <span class="text-xs uppercase tracking-wider text-stone-500 font-medium">
                    #{{ $item->category->category_name ?? 'General' }}
                </span>
                
                <h2 class="text-3xl md:text-4xl font-serif text-stone-100 leading-tight">
                    {{ $item->title }}
                </h2>

                <div class="w-12 h-[1px] bg-stone-700 my-2"></div>

                @if($item->description)
                    <div class="space-y-1">
                        <h4 class="text-[10px] uppercase tracking-widest text-stone-500 font-semibold">Descripción</h4>
                        <p class="text-sm text-stone-300 font-sans leading-relaxed">{{ $item->description }}</p>
                    </div>
                @endif

                <div class="space-y-1">
                    <h4 class="text-[10px] uppercase tracking-widest text-stone-500 font-semibold">Ubicación</h4>
                    <p class="text-sm text-stone-300 font-sans">{{ $item->location }}</p>
                </div>

                <div class="space-y-1">
                    <h4 class="text-[10px] uppercase tracking-widest text-stone-500 font-semibold">Fecha de reporte</h4>
                    <p class="text-sm text-stone-300 font-sans">{{ $item->created_at->diffForHumans() }}</p>
                </div>

                <!-- Autor -->
                <div class="flex items-center space-x-3 pt-4 border-t border-zinc-800/60">
                    @if ($item->user->avatar_path ?? false)
                        <img src="{{ asset('storage/' . $item->user->avatar_path) }}" 
                             alt="{{ $item->user->name }}" 
                             class="w-10 h-10 rounded-full object-cover border border-stone-700/50">
                    @else
                        <div class="w-10 h-10 rounded-full bg-stone-800 flex items-center justify-center text-sm font-bold uppercase text-stone-300 border border-stone-700/50">
                            {{ substr($item->user->name ?? 'U', 0, 1) }}
                        </div>
                    @endif

                    <div>
                        <span class="text-[10px] uppercase tracking-wider text-stone-500 block font-medium">Reportado por</span>
                        <a href="{{ route('users.show', $item->user->id) }}" 
                           wire:navigate 
                           class="text-xs font-semibold text-stone-200 hover:text-amber-400 transition-colors underline decoration-stone-600 underline-offset-4">
                            {{ $item->user->name }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="pt-4 border-t border-zinc-800/40">
                @if(auth()->id() !== $item->user_id)
                    <button wire:click="startChat" 
                            class="w-full text-center block bg-white text-black hover:bg-stone-200 font-medium py-2.5 px-6 uppercase tracking-widest text-xs transition-all duration-200 rounded cursor-pointer">
                        Iniciar Chat Privado
                    </button>
                @else
                    <div class="flex gap-3">
                        <a href="{{ route('items.edit', $item->id) }}" wire:navigate 
                           class="flex-1 text-center block bg-zinc-800/60 hover:bg-zinc-800 text-stone-300 hover:text-white border border-zinc-700/60 font-medium py-2.5 px-4 uppercase tracking-widest text-xs transition-all duration-200 rounded">
                            Editar
                        </a>

                        <button type="button" 
                                onclick="confirmarEliminacion()"
                                class="px-4 bg-red-950/40 hover:bg-red-900/60 text-red-400 border border-red-900/40 font-medium py-2.5 uppercase tracking-widest text-xs transition-all duration-200 rounded cursor-pointer">
                            Eliminar
                        </button>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- Reacciones y Comentarios -->
    <div class="bg-zinc-900/40 rounded-xl border border-zinc-800/60 p-6 md:p-8 space-y-6">

        <!-- Botón de Me Gusta con LATIDO 💓 -->
        <button type="button" 
                wire:click.prevent="toggleLike" 
                class="group flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider transition-all duration-300 cursor-pointer select-none">
            
            <div class="relative flex items-center justify-center p-1 rounded-full transition-transform duration-300 group-hover:scale-125 active:scale-90 {{ $this->isLiked ? 'animate-heartbeat' : '' }}">
                
                @if($this->isLiked)
                    <span class="absolute inset-0 rounded-full bg-rose-500/30 blur-md animate-pulse"></span>
                @endif

                <svg class="w-5 h-5 relative z-10 transition-all duration-300 transform {{ $this->isLiked ? 'text-rose-500 fill-current drop-shadow-[0_0_10px_rgba(244,63,94,0.8)]' : 'text-stone-400 fill-none stroke-current group-hover:text-rose-400 group-hover:scale-110' }}" 
                     stroke-width="2" 
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                </svg>
            </div>

            <span class="transition-colors duration-200 {{ $this->isLiked ? 'text-rose-400 font-bold' : 'text-stone-400 group-hover:text-stone-200' }}">
                {{ $item->likes->count() }} {{ Str::plural('Reacciones', $item->likes->count()) }}
            </span>
        </button>

        <!-- Formulario e Historial de Comentarios -->
        <div class="space-y-4">
            <h3 class="text-xs font-semibold text-stone-400 tracking-widest uppercase">Comentarios</h3>

            @auth
                <form wire:submit.prevent="postComment" class="flex gap-3">
                    <input type="text" 
                           wire:model="newComment" 
                           placeholder="Escribe un comentario..." 
                           class="flex-1 bg-zinc-800/80 border border-zinc-700/80 rounded-lg px-4 py-2 text-xs text-white placeholder-stone-500 focus:outline-none focus:border-stone-400 transition-colors">
                    
                    <button type="submit" 
                            class="px-5 py-2 bg-stone-100 text-stone-900 font-semibold text-xs rounded-lg uppercase tracking-wider hover:bg-stone-300 transition-colors cursor-pointer">
                        Publicar
                    </button>
                </form>
                @error('newComment') <span class="text-red-400 text-[11px] block">{{ $message }}</span> @enderror
            @else
                <div class="p-3 bg-zinc-800/40 rounded-lg border border-zinc-800/60 text-center">
                    <p class="text-xs text-stone-400">Inicia sesión para reaccionar y comentar.</p>
                </div>
            @endauth

            <div class="space-y-3 pt-2">
                @forelse($item->comments as $comment)
                    <div class="flex items-start space-x-3 bg-zinc-800/30 p-3 rounded-lg border border-zinc-800/50">
                        <div class="w-8 h-8 rounded-full bg-stone-800 border border-stone-700 flex items-center justify-center text-xs font-bold uppercase text-stone-300 shrink-0 overflow-hidden">
                            @if($comment->user->avatar_path ?? false)
                                <img src="{{ asset('storage/' . $comment->user->avatar_path) }}" alt="{{ $comment->user->name }}" class="w-full h-full object-cover">
                            @else
                                {{ substr($comment->user->name ?? 'U', 0, 1) }}
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-stone-200 truncate">{{ $comment->user->name ?? 'Usuario' }}</span>
                                <span class="text-[10px] text-stone-500">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-stone-300 mt-1 break-words leading-relaxed">{{ $comment->body }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-stone-500 text-center py-4">No hay comentarios aún. ¡Sé el primero en comentar!</p>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Script de SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmarEliminacion() {
            Swal.fire({
                title: '¿Eliminar publicación?',
                text: "Esta acción no se puede deshacer.",
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
                    @this.call('deleteItem');
                }
            });
        }

        window.addEventListener('swal:deleted', () => {
            Swal.fire({
                title: '¡Eliminado!',
                text: 'La publicación ha sido eliminada correctamente.',
                icon: 'success',
                background: '#18181b',
                color: '#f5f5f4',
                iconColor: '#22c55e',
                showConfirmButton: false,
                timer: 1800,
                customClass: {
                    popup: 'rounded-xl border border-zinc-800 shadow-2xl'
                }
            }).then(() => {
                Livewire.navigate("{{ route('dashboard') }}");
            });
        });
    </script>
</div>