<?php

use Livewire\Volt\Component;
use App\Models\Conversation;

new class extends Component {
    public function with(): array
    {
        $userId = auth()->id();

        // Buscamos todas las conversaciones donde participa el usuario autenticado
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

<div class="max-w-4xl mx-auto px-4 py-12 text-white font-sans" wire:poll.5s>
    <div class="mb-8 flex items-center justify-between border-b border-zinc-800/80 pb-4">
        <div>
            <h1 class="text-2xl font-serif text-stone-100">Mis Mensajes</h1>
            <p class="text-xs text-stone-400 mt-1">Conversaciones sobre reportes de objetos</p>
        </div>
        <a href="{{ route('dashboard') }}" wire:navigate class="text-xs uppercase tracking-widest text-stone-400 hover:text-white transition-colors">
            ← Volver al Home
        </a>
    </div>

    @if($conversations->isEmpty())
        <div class="bg-zinc-900/30 border border-zinc-800/60 rounded-xl p-12 text-center">
            <p class="text-stone-500 text-sm">No tienes mensajes ni conversaciones activas todavía.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($conversations as $conversation)
                @php 
                    $otherUser = auth()->id() === $conversation->sender_id ? $conversation->receiver : $conversation->sender;
                    $lastMessage = $conversation->messages->last();
                @endphp

                <a href="{{ route('chat.show', $conversation->id) }}" wire:navigate 
                   class="block bg-zinc-900/40 hover:bg-zinc-800/50 border border-zinc-800/60 hover:border-zinc-700/80 rounded-xl p-4 transition-all group">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <!-- Foto del Objeto -->
                            <div class="w-12 h-12 bg-zinc-800 rounded-lg overflow-hidden flex-shrink-0 border border-zinc-700/40">
                                @if($conversation->item->image_path)
                                    <img src="{{ asset('storage/' . $conversation->item->image_path) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-[9px] text-stone-500 uppercase font-semibold">Sin foto</div>
                                @endif
                            </div>

                            <!-- Información Principal -->
                            <div class="space-y-0.5">
                                <h3 class="text-sm font-semibold text-stone-200 group-hover:text-white transition-colors">
                                    {{ $conversation->item->title }}
                                </h3>
                                <p class="text-xs text-stone-400">
                                    <span class="text-stone-500 font-medium">Chat con:</span> {{ $otherUser->name }}
                                </p>
                                @if($lastMessage)
                                    <p class="text-xs text-stone-500 line-clamp-1 italic">
                                        "{{ $lastMessage->body }}"
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Fecha / Hora -->
                        <div class="text-right flex-shrink-0">
                            <span class="text-[10px] uppercase tracking-widest text-stone-500">
                                {{ $conversation->updated_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>