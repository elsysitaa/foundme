<?php

use Livewire\Volt\Component;
use App\Models\Conversation;
use App\Models\Message;

new class extends Component {
    public int $conversationId;
    public string $newMessage = '';

    public function sendMessage(): void
    {
        $this->validate([
            'newMessage' => 'required|string|max:1000',
        ]);

        Message::create([
            'conversation_id' => $this->conversationId,
            'user_id' => auth()->id(),
            'body' => trim($this->newMessage),
        ]);

        $this->newMessage = '';
    }

    public function with(): array
    {
        $conversation = Conversation::with(['item', 'sender', 'receiver', 'messages.user'])
            ->findOrFail($this->conversationId);

        // Seguridad: Verificar que el usuario pertenece a esta conversación
        if (auth()->id() !== $conversation->sender_id && auth()->id() !== $conversation->receiver_id) {
            abort(403);
        }

        return [
            'conversation' => $conversation,
        ];
    }
}; ?>

<div class="max-w-3xl mx-auto px-4 py-8 text-white font-sans" wire:poll.3s>
    <!-- Encabezado del Chat -->
    <div class="bg-zinc-900/60 p-4 rounded-t-xl border border-zinc-800 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('dashboard') }}" wire:navigate class="text-stone-400 hover:text-white text-xs uppercase tracking-widest mr-2">
                ← Volver
            </a>
            <div>
                <h3 class="text-sm font-semibold text-stone-200">
                    {{ $conversation->item->title }}
                </h3>
                <p class="text-[10px] text-stone-400">
                    Chat con: {{ auth()->id() === $conversation->sender_id ? $conversation->receiver->name : $conversation->sender->name }}
                </p>
            </div>
        </div>
    </div>

    <!-- Caja de Mensajes -->
    <div class="bg-zinc-950/40 border-x border-zinc-800/80 p-4 h-96 overflow-y-auto space-y-4 flex flex-col-reverse">
        @foreach($conversation->messages->reverse() as $message)
            @php $isMe = $message->user_id === auth()->id(); @endphp
            <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                <div class="max-w-xs md:max-w-md px-4 py-2.5 rounded-2xl text-xs {{ $isMe ? 'bg-white text-black font-medium rounded-br-none' : 'bg-zinc-800 text-stone-200 rounded-bl-none border border-zinc-700/50' }}">
                    {{ $message->body }}
                </div>
                <span class="text-[9px] text-stone-500 mt-1 px-1">
                    {{ $message->created_at->format('H:i') }}
                </span>
            </div>
        @endforeach
    </div>

    <!-- Formulario de Envío -->
    <form wire:submit="sendMessage" class="bg-zinc-900/60 p-3 rounded-b-xl border border-zinc-800 flex items-center gap-2">
        <input wire:model="newMessage" type="text" placeholder="Escribe un mensaje..." required
               class="flex-1 bg-[#1b1514] text-white text-xs px-4 py-2.5 rounded border border-zinc-800 outline-none focus:ring-1 focus:ring-stone-500">
        <button type="submit" class="bg-white text-black text-xs uppercase tracking-widest font-semibold px-5 py-2.5 rounded hover:bg-stone-200 transition-all">
            Enviar
        </button>
    </form>
</div>