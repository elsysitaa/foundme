<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProfanityFilter implements ValidationRule
{
    protected array $badWords = [];

    public function __construct()
    {
        $filePath = storage_path('app/badwords.json');

        if (file_exists($filePath)) {
            // Lee el archivo generado en storage/app/badwords.json
            $content = file_get_contents($filePath);
            $this->badWords = array_filter(array_map('trim', explode("\n", $content)));
        }
    }

    /**
     * Ejecuta la regla de validación.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // 1. Convertimos el texto a minúsculas
        $text = mb_strtolower($value, 'UTF-8');

        // 2. Normalizamos acentos (ej: "pútos" -> "putos")
        $cleanText = strtr(
            utf8_decode($text),
            utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
            'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'
        );

        // 3. Verificamos contra la lista del archivo
        foreach ($this->badWords as $badWord) {
            $badWordClean = mb_strtolower(trim($badWord));

            // Ignoramos entradas vacías o demasiado cortas (menos de 3 caracteres)
            if (empty($badWordClean) || strlen($badWordClean) < 3) {
                continue;
            }

            if (str_contains($cleanText, $badWordClean)) {
                $fail('El campo contiene lenguaje inapropiado o palabras no permitidas.');
                return;
            }
        }
    }
}