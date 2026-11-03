<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definimos la lista de categorías
        $categories = [
            ['category_name' => 'Llaves', 'status' => 1],
            ['category_name' => 'Mascotas', 'status' => 1],
            ['category_name' => 'Electrónicos', 'status' => 1],
            ['category_name' => 'Documentos', 'status' => 1],
            ['category_name' => 'Accesorios', 'status' => 1],
        ];

        // Recorremos el arreglo e insertamos/actualizamos cada una en la BD
        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['category_name' => $category['category_name']], // Si ya existe por nombre, no la dupliques
                ['status' => $category['status']]
            );
        }
    }
}