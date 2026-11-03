<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-stone-100 leading-tight">
            {{ __('Mi Perfil') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <livewire:profile.custom-profile />
    </div>
</x-app-layout>