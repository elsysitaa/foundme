<?php

use Livewire\Volt\Component;
use App\Models\Conversation;
use App\Models\Message;

new class extends Component {
    public Conversation $conversation;
    public string $newMessage = '';

    public function mount(Conversation $conversation): void
    {
        // Seguridad: Solo los participantes pueden ver esta conversación
        if (auth()->id() !== $conversation->sender_id && auth()->id() !== $conversation->receiver_id) {
            abort(403);
        }

        $this->conversation = $conversation;
    }

    public function sendMessage(): void
    {
        $this->validate([
            'newMessage' => 'required|string|max:1000',
        ]);

        Message::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => auth()->id(),
            'body' => trim($this->newMessage),
        ]);

        $this->newMessage = '';
    }

    public function with(): array
    {
        // Obtener la otra persona con la que se está chateando
        $otherUser = auth()->id() === $this->conversation->sender_id 
            ? $this->conversation->receiver 
            : $this->conversation->sender;

        return [
            'messages' => $this->conversation->messages()->with('user')->oldest()->get(),
            'otherUser' => $otherUser,
        ];
    }
}; ?>

<div class="max-w-4xl mx-auto px-4 py-8 font-sans text-white">
    
    <!-- Botón de regresar -->
    <div class="mb-4">
        <a href="{{ route('items.show', $conversation->item_id) }}" wire:navigate class="inline-flex items-center space-x-2 text-xs uppercase tracking-widest text-stone-400 hover:text-white transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
            </svg>
            <span>Volver a la publicación</span>
        </a>
    </div>

    <!-- Contenedor Principal del Chat -->
    <div class="bg-zinc-900/50 rounded-xl border border-zinc-800/80 overflow-hidden flex flex-col h-[600px]">
        
        <!-- Header del Chat -->
        <div class="p-4 bg-zinc-900 border-b border-zinc-800 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-stone-700 flex items-center justify-center font-bold text-xs uppercase text-stone-200">
                    {{ substr($otherUser->name, 0, 1) }}
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-stone-100">{{ $otherUser->name }}</h3>
                    <p class="text-[10px] text-stone-400">Objeto: {{ $conversation->item->title ?? 'Reporte' }}</p>
                </div>
            </div>

            <span class="text-[10px] uppercase tracking-widest px-2.5 py-1 rounded-full bg-zinc-800 text-stone-300 border border-zinc-700">
                Chat Privado
            </span>
        </div>

        <!-- Área de Mensajes (Auto-refresh cada 3 segundos) -->
        <div wire:poll.3s class="flex-1 p-4 overflow-y-auto space-y-4 bg-zinc-950/30 flex flex-col-reverse">
            <div class="space-y-4 flex flex-col justify-end min-h-full">
                @forelse($messages as $msg)
                    @php
                        $isMe = $msg->user_id === auth()->id();
                    @endphp

                    <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                        <div class="max-w-[80%] sm:max-w-[70%] rounded-2xl px-4 py-2.5 text-xs leading-relaxed
                            {{ $isMe ? 'bg-white text-black rounded-br-none' : 'bg-zinc-800 text-stone-200 border border-zinc-700/60 rounded-bl-none' }}">
                            {{ $msg->body }}
                        </div>
                        <span class="text-[9px] text-stone-500 mt-1 px-1">
                            {{ $msg->created_at->format('H:i') }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-12 text-stone-500 text-xs">
                        Inicia la conversación enviando un mensaje.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Formulario para Enviar Mensaje -->
        <div class="p-4 bg-zinc-900 border-t border-zinc-800">
            <form wire:submit="sendMessage" class="flex items-center gap-2">
                <input type="text" 
                       wire:model="newMessage" 
                       placeholder="Escribe tu mensaje..." 
                       class="flex-1 bg-zinc-800/80 border border-zinc-700 rounded-lg px-4 py-2.5 text-xs text-white placeholder-stone-500 focus:outline-none focus:border-stone-400 transition-all">
                
                <button type="submit" 
                        class="bg-white text-black hover:bg-stone-200 font-semibold px-5 py-2.5 rounded-lg text-xs uppercase tracking-wider transition-colors cursor-pointer shrink-0">
                    Enviar
                </button>
            </form>
            @error('newMessage') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
        </div>

    </div>
</div>