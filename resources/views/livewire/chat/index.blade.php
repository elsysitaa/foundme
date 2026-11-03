<?php

use Livewire\Volt\Component;
use App\Models\Conversation;

new class extends Component {
    public function with(): array
    {
        $userId = auth()->id();

        // Obtener todas las conversaciones donde el usuario actual sea emisor o receptor
        $conversations = Conversation::with(['item', 'sender', 'receiver', 'messages'])
            ->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->latest('updated_at')
            ->get();

        return [
            'conversations' => $conversations,
        ];
    }
}; ?>

<div class="max-w-4xl mx-auto px-4 py-8 font-sans text-white">
    
    <div class="mb-6">
        <h1 class="text-2xl font-serif font-bold text-stone-100">Mis Mensajes</h1>
        <p class="text-xs text-stone-400 mt-1">Historial de chats activos sobre objetos perdidos o encontrados.</p>
    </div>

    <div class="bg-zinc-900/40 rounded-xl border border-zinc-800/60 overflow-hidden">
        @forelse($conversations as $conv)
            @php
                $otherUser = auth()->id() === $conv->sender_id ? $conv->receiver : $conv->sender;
                $lastMessage = $conv->messages->last();
            @endphp

            <a href="{{ route('chat.show', $conv->id) }}" wire:navigate 
               class="block p-4 border-b border-zinc-800/50 hover:bg-zinc-800/40 transition-colors flex items-center justify-between gap-4">
                
                <div class="flex items-center space-x-3 min-w-0">
                    <div class="w-10 h-10 rounded-full bg-stone-700 flex items-center justify-center font-bold text-sm uppercase text-stone-200 shrink-0">
                        {{ substr($otherUser->name ?? 'U', 0, 1) }}
                    </div>

                    <div class="min-w-0 space-y-1">
                        <div class="flex items-center space-x-2">
                            <h3 class="text-sm font-semibold text-stone-200 truncate">{{ $otherUser->name ?? 'Usuario' }}</h3>
                            <span class="text-[10px] uppercase text-stone-500 font-mono">
                                #{{ $conv->item->title ?? 'Objeto' }}
                            </span>
                        </div>
                        <p class="text-xs text-stone-400 truncate">
                            {{ $lastMessage->body ?? 'Sin mensajes aún' }}
                        </p>
                    </div>
                </div>

                <div class="text-right shrink-0">
                    <span class="text-[10px] text-stone-500 block">
                        {{ $lastMessage ? $lastMessage->created_at->diffForHumans() : '' }}
                    </span>
                    <span class="text-xs text-stone-300 hover:text-white uppercase tracking-wider font-medium text-[10px]">
                        Ver chat &rarr;
                    </span>
                </div>
            </a>
        @empty
            <div class="text-center py-16 text-stone-500 space-y-2">
                <p class="text-sm">No tienes ninguna conversación activa por el momento.</p>
                <a href="{{ route('dashboard') }}" wire:navigate class="text-xs text-stone-300 hover:underline uppercase tracking-wider">
                    Explorar publicaciones
                </a>
            </div>
        @endforelse
    </div>
</div>