<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::view('/', 'welcome');

// Ruta pública o accesible para usuarios autenticados
Route::view('privacidad', 'privacy-policy')->name('privacy.policy');

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->middleware(['verified'])
        ->name('dashboard');

    Route::view('dashboard/reportar', 'items.create')
        ->name('items.create');

    Route::view('profile', 'profile')
        ->name('profile');

    // Ruta para ver los detalles de un objeto específico
    Route::get('dashboard/objeto/{id}', function ($id) {
        return view('items.show', ['id' => $id]);
    })->name('items.show');
    Volt::route('/usuario/{user}', 'users.show')->name('users.show');

    // Edición/Eliminación de la publicación con Volt
    Volt::route('items/{item}/edit', 'items.edit')->name('items.edit');

    // Chat y Mensajes (Corregido: sin el prefijo 'livewire.')
    Volt::route('/chat/{conversation}', 'chat.show')->name('chat.show');
    Volt::route('/mensajes', 'chat.index')->name('chat.index');

    // Mis publicaciones
    Volt::route('/mis-reportes', 'items.my-items')->name('items.my-items');
});

/*
|--------------------------------------------------------------------------
| Rutas Exclusivas de Administrador
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Panel principal de administración
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');

    // Gestión de usuarios
    Volt::route('/usuarios', 'admin.users.index')->name('users.index');

    // Gestión global de objetos/reportes
    Volt::route('/reportes', 'admin.items.index')->name('items.index');

    Volt::route('/categorias', 'admin.categories.index')->name('categories.index');


});

require __DIR__.'/auth.php';