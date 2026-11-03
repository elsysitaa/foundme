<?php

use Livewire\Volt\Component;
use App\Models\User;

new class extends Component {
    public function deleteUser(int $id): void
    {
        // Evitar que el admin se elimine a sí mismo
        if (auth()->id() === $id) {
            return;
        }

        User::destroy($id);
        $this->dispatch('swal:deleted');
    }

    public function with(): array
    {
        return [
            'users' => User::latest()->paginate(10),
        ];
    }
}; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="max-w-6xl mx-auto px-4 py-8 font-sans">
    <div class="mb-8 border-b border-zinc-800 pb-5 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-serif text-white font-bold">Gestión de Usuarios</h1>
            <p class="text-xs text-stone-400 mt-1">Administración y control de acceso de usuarios registrados.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="text-xs text-stone-400 hover:text-white uppercase tracking-widest">
            &larr; Volver al Panel
        </a>
    </div>

    <div class="bg-zinc-900/60 border border-zinc-800 rounded-2xl p-6 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-zinc-800 text-stone-500 uppercase tracking-wider">
                        <th class="pb-3">Usuario</th>
                        <th class="pb-3">Email</th>
                        <th class="pb-3">Rol</th>
                        <th class="pb-3">Registro</th>
                        <th class="pb-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/50 text-stone-300">
                    @foreach($users as $user)
                        <tr>
                            <td class="py-3 font-medium text-stone-100 flex items-center space-x-3">
                                @if ($user->avatar_path)
                                    <img src="{{ asset('storage/' . $user->avatar_path) }}" class="w-7 h-7 rounded-full object-cover">
                                @else
                                    <div class="w-7 h-7 rounded-full bg-zinc-800 flex items-center justify-center text-[10px] font-bold text-stone-300 border border-zinc-700">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                @endif
                                <span>{{ $user->name }}</span>
                            </td>
                            <td class="py-3">{{ $user->email }}</td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider {{ $user->is_admin ? 'bg-amber-950 text-amber-400 border border-amber-800/60' : 'bg-zinc-800 text-stone-400' }}">
                                    {{ $user->is_admin ? 'Admin' : 'Usuario' }}
                                </span>
                            </td>
                            <td class="py-3 text-stone-500">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="py-3 text-right">
                                @if(auth()->id() !== $user->id)
                                    <button type="button" 
                                            onclick="confirmarEliminacionUsuario({{ $user->id }})" 
                                            class="text-red-400 hover:text-red-300 transition-colors cursor-pointer">
                                        Eliminar
                                    </button>
                                @else
                                    <span class="text-stone-600 italic">Tú</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>

<script>
    function confirmarEliminacionUsuario(id) {
        Swal.fire({
            title: '¿Eliminar usuario?',
            text: "Se borrarán sus publicaciones y datos asociados.",
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
                @this.call('deleteUser', id);
            }
        });
    }

    window.addEventListener('swal:deleted', () => {
        Swal.fire({
            title: '¡Usuario Eliminado!',
            text: 'El usuario ha sido removido del sistema.',
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