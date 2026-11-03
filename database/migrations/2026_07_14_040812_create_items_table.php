<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    // 1️⃣ Creamos primero la tabla de categorías de forma segura
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('category_name');
        $table->integer('status')->default(1);
        $table->timestamps();
    });

    // 2️⃣ Inmediatamente después creamos la tabla de items que la referencia
    Schema::create('items', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->enum('status', ['perdido', 'encontrado']);
        $table->string('location');
        $table->string('image_path')->nullable();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    Schema::dropIfExists('items');
    Schema::dropIfExists('categories');
    }
};
