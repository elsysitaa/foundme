<x-app-layout>
    <div class="min-h-screen bg-[#1b1514] text-white px-6 md:px-12 py-8 font-sans">
        
        <!-- Encabezado -->
        <div class="max-w-7xl mx-auto mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-stone-800 pb-5 gap-4">
            <div>
                <span class="text-xs uppercase tracking-widest text-stone-400 font-semibold">Panel de Control</span>
                <h1 class="text-3xl font-serif text-white tracking-wide mt-1">Administración</h1>
            </div>
            
            <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center space-x-2 text-xs uppercase tracking-widest text-stone-400 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                </svg>
                <span>Volver al sitio</span>
            </a>
        </div>

        <!-- Tarjetas de Estadísticas Rápidas -->
        <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            
            <!-- Usuarios -->
            <div class="bg-zinc-900/60 border border-zinc-800/80 rounded-2xl p-6 shadow-xl flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-widest text-stone-400 font-medium">Usuarios Registrados</p>
                    <p class="text-3xl font-serif text-white font-bold mt-2">{{ \App\Models\User::count() }}</p>
                </div>
                <div class="p-3 bg-stone-800/50 rounded-xl border border-stone-700/40">
                    <svg class="w-6 h-6 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>

            <!-- Reportes -->
            <div class="bg-zinc-900/60 border border-zinc-800/80 rounded-2xl p-6 shadow-xl flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-widest text-stone-400 font-medium">Objetos Reportados</p>
                    <p class="text-3xl font-serif text-white font-bold mt-2">
                        {{ class_exists('App\Models\Item') ? \App\Models\Item::count() : 0 }}
                    </p>
                </div>
                <div class="p-3 bg-stone-800/50 rounded-xl border border-stone-700/40">
                    <svg class="w-6 h-6 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Categorías -->
            <div class="bg-zinc-900/60 border border-zinc-800/80 rounded-2xl p-6 shadow-xl flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-widest text-stone-400 font-medium">Categorías Activas</p>
                    <p class="text-3xl font-serif text-white font-bold mt-2">
                        {{ class_exists('App\Models\Category') ? \App\Models\Category::where('status', 1)->count() : 0 }}
                    </p>
                </div>
                <div class="p-3 bg-stone-800/50 rounded-xl border border-stone-700/40">
                    <svg class="w-6 h-6 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
            </div>

            <!-- Administradores -->
            <div class="bg-zinc-900/60 border border-zinc-800/80 rounded-2xl p-6 shadow-xl flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-widest text-stone-400 font-medium">Administradores</p>
                    <p class="text-3xl font-serif text-white font-bold mt-2">{{ \App\Models\User::where('is_admin', true)->count() }}</p>
                </div>
                <div class="p-3 bg-stone-800/50 rounded-xl border border-stone-700/40">
                    <svg class="w-6 h-6 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>

        </div>

        <!-- Módulos Principales -->
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <!-- Gestión de Categorías -->
            <div class="bg-zinc-900/60 border border-zinc-800/80 rounded-2xl p-6 shadow-xl space-y-4 flex flex-col justify-between">
                <div class="space-y-3">
                    <h2 class="text-lg font-serif text-stone-100 uppercase tracking-wider border-b border-zinc-800 pb-3">Categorías</h2>
                    <p class="text-xs text-stone-400 leading-relaxed">
                        Crea, activa o quita categorías del catálogo para que los usuarios clasifiquen mejor sus objetos reportados.
                    </p>
                </div>
                <div class="pt-4 border-t border-zinc-800/50">
                    <a href="{{ route('admin.categories.index') }}" wire:navigate 
                       class="inline-block px-4 py-2 bg-stone-800 hover:bg-stone-700 text-stone-200 rounded-lg text-xs font-semibold uppercase tracking-wider transition-colors">
                        Gestionar Categorías &rarr;
                    </a>
                </div>
            </div>

            <!-- Gestión de Usuarios -->
            <div class="bg-zinc-900/60 border border-zinc-800/80 rounded-2xl p-6 shadow-xl space-y-4 flex flex-col justify-between">
                <div class="space-y-3">
                    <h2 class="text-lg font-serif text-stone-100 uppercase tracking-wider border-b border-zinc-800 pb-3">Gestión de Usuarios</h2>
                    <p class="text-xs text-stone-400 leading-relaxed">
                        Visualiza la lista de usuarios registrados, gestiona sus permisos o elimina cuentas que infrinjan los términos del servicio.
                    </p>
                </div>
                <div class="pt-4 border-t border-zinc-800/50">
                    <a href="{{ route('admin.users.index') }}" wire:navigate 
                       class="inline-block px-4 py-2 bg-stone-800 hover:bg-stone-700 text-stone-200 rounded-lg text-xs font-semibold uppercase tracking-wider transition-colors">
                        Gestionar Usuarios &rarr;
                    </a>
                </div>
            </div>

            <!-- Moderación de Reportes -->
            <div class="bg-zinc-900/60 border border-zinc-800/80 rounded-2xl p-6 shadow-xl space-y-4 flex flex-col justify-between">
                <div class="space-y-3">
                    <h2 class="text-lg font-serif text-stone-100 uppercase tracking-wider border-b border-zinc-800 pb-3">Moderación de Reportes</h2>
                    <p class="text-xs text-stone-400 leading-relaxed">
                        Revisa todos los objetos perdidos y encontrados en la plataforma para ocultar o borrar publicaciones inapropiadas o duplicadas.
                    </p>
                </div>
                <div class="pt-4 border-t border-zinc-800/50">
                    <a href="{{ route('admin.items.index') }}" wire:navigate 
                       class="inline-block px-4 py-2 bg-stone-800 hover:bg-stone-700 text-stone-200 rounded-lg text-xs font-semibold uppercase tracking-wider transition-colors">
                        Moderación &rarr;
                    </a>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>