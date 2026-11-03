<?php

use Livewire\Volt\Component;
use App\Models\Item;
use App\Models\Comment;
use App\Models\Like;

new class extends Component {
    public Item $item;
    public string $newComment = '';

    public function mount(Item $item): void
    {
        $this->item = $item;
        $this->reloadItem();
    }

    public function reloadItem(): void
    {
        $this->item->load(['likes', 'comments.user']);
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

        $this->reloadItem();
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

        $this->reset('newComment');
        $this->reloadItem();
    }
}; ?>

<div>
    <div class="bg-zinc-900/50 border border-zinc-800/80 rounded-xl p-6 space-y-6 text-stone-200">

        <!-- Reacción de Corazón y Contador -->
        <div class="flex items-center justify-between border-b border-zinc-800/80 pb-4">
            <div class="flex items-center space-x-6">
                <button type="button" 
                        wire:click="toggleLike" 
                        class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider transition-colors cursor-pointer {{ $item->isLikedBy(auth()->user()) ? 'text-rose-500 hover:text-rose-400' : 'text-stone-400 hover:text-stone-200' }}">
                    <svg class="w-5 h-5 {{ $item->isLikedBy(auth()->user()) ? 'fill-current' : 'fill-none stroke-current' }}" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                    </svg>
                    <span>{{ $item->likes->count() }} {{ Str::plural('Reacción', $item->likes->count()) }}</span>
                </button>

                <div class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-stone-400">
                    <svg class="w-5 h-5 fill-none stroke-current" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 01-.923 1.785A5.969 5.969 0 006 21c1.282 0 2.47-.402 3.445-1.087.51-.358 1.14-.492 1.75-.413.882.115 1.8.18 2.805.18z" />
                    </svg>
                    <span>{{ $item->comments->count() }} {{ Str::plural('Comentario', $item->comments->count()) }}</span>
                </div>
            </div>
        </div>

        <!-- Módulo de Comentarios -->
        <div class="space-y-4">
            <h3 class="text-sm font-serif font-bold text-stone-200 tracking-wider uppercase">Comentarios</h3>

            @auth
                <form wire:submit="postComment" class="flex gap-3">
                    <input type="text" 
                           wire:model="newComment" 
                           placeholder="Escribe un comentario o información sobre el objeto..." 
                           class="flex-1 bg-zinc-800/80 border border-zinc-700/80 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-stone-400">
                    
                    <button type="submit" 
                            class="px-4 py-2 bg-stone-100 text-stone-900 font-semibold text-xs rounded-lg uppercase tracking-wider hover:bg-stone-300 transition-colors cursor-pointer">
                        Publicar
                    </button>
                </form>
                @error('newComment') <span class="text-red-400 text-[11px] block">{{ $message }}</span> @enderror
            @else
                <div class="p-3 bg-zinc-800/40 rounded-lg border border-zinc-800 text-center">
                    <p class="text-xs text-stone-400">Inicia sesión para dar me gusta y dejar un comentario.</p>
                </div>
            @endauth

            <!-- Lista de Comentarios -->
            <div class="space-y-3 pt-2">
                @forelse($item->comments->sortByDesc('created_at') as $comment)
                    <div class="flex items-start space-x-3 bg-zinc-800/40 p-3 rounded-lg border border-zinc-800/60">
                        <div class="w-8 h-8 rounded-full bg-stone-800 border border-stone-700 flex items-center justify-center text-xs font-bold uppercase text-stone-300 shrink-0 overflow-hidden">
                            @if($comment->user?->avatar_path)
                                <img src="{{ asset('storage/' . $comment->user->avatar_path) }}" alt="{{ $comment->user->name }}" class="w-full h-full object-cover">
                            @else
                                {{ substr($comment->user?->name ?? 'U', 0, 1) }}
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-stone-200 truncate">{{ $comment->user?->name ?? 'Usuario' }}</span>
                                <span class="text-[10px] text-stone-500">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-stone-300 mt-1 break-words">{{ $comment->body }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-stone-500 text-center py-4">No hay comentarios aún.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>