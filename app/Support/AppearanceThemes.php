<?php

namespace App\Support;

final class AppearanceThemes
{
    public const DEFAULT = 'famfit-original';

    public const THEMES = [
        'classic-white' => ['name' => 'Blanco clásico', 'background' => '#FFFFFF', 'surface' => '#F5F5F5', 'accent' => '#2864F0', 'text' => '#202020', 'muted' => '#656565', 'border' => '#E5E5E5'],
        'light-gray' => ['name' => 'Gris claro', 'background' => '#F0F0F0', 'surface' => '#FFFFFF', 'accent' => '#2864F0', 'text' => '#292929', 'muted' => '#666666', 'border' => '#DADADA'],
        'pearl-gray' => ['name' => 'Gris perla', 'background' => '#E8EAED', 'surface' => '#F8F9FA', 'accent' => '#50657A', 'text' => '#30343B', 'muted' => '#606872', 'border' => '#D5D9DE'],
        'sky-blue' => ['name' => 'Azul cielo', 'background' => '#EAF4FF', 'surface' => '#FFFFFF', 'accent' => '#2879C7', 'text' => '#19324D', 'muted' => '#536C84', 'border' => '#C9DFF5'],
        'ice-blue' => ['name' => 'Azul hielo', 'background' => '#E8F1F8', 'surface' => '#F8FCFF', 'accent' => '#34769E', 'text' => '#263D52', 'muted' => '#5D7182', 'border' => '#CDDFEC'],
        'mint-green' => ['name' => 'Verde menta', 'background' => '#E8F5EC', 'surface' => '#FFFFFF', 'accent' => '#3A875E', 'text' => '#244A35', 'muted' => '#52745F', 'border' => '#CDE5D4'],
        'lavender' => ['name' => 'Lavanda', 'background' => '#F0ECFA', 'surface' => '#FFFFFF', 'accent' => '#7356A5', 'text' => '#40365C', 'muted' => '#6D6285', 'border' => '#DDD4F0'],
        'cream' => ['name' => 'Crema', 'background' => '#F7F1E7', 'surface' => '#FFFCF6', 'accent' => '#91672F', 'text' => '#453D32', 'muted' => '#756A5B', 'border' => '#E9DFD0'],
        'soft-gray' => ['name' => 'Gris suave', 'background' => '#343434', 'surface' => '#414141', 'accent' => '#91B7FF', 'text' => '#F3F4F6', 'muted' => '#B8BBC2', 'border' => '#5A5A5A'],
        'deep-gray' => ['name' => 'Gris profundo', 'background' => '#242424', 'surface' => '#303030', 'accent' => '#8FB4FF', 'text' => '#F4F4F4', 'muted' => '#B1B4BB', 'border' => '#484848'],
        'classic-black' => ['name' => 'Negro clásico', 'background' => '#121212', 'surface' => '#1E1E1E', 'accent' => '#82AAFF', 'text' => '#F4F4F4', 'muted' => '#AAAAAA', 'border' => '#383838'],
        'amoled' => ['name' => 'AMOLED puro', 'background' => '#000000', 'surface' => '#0A0A0A', 'accent' => '#8BB5FF', 'text' => '#F5F5F5', 'muted' => '#A9A9A9', 'border' => '#272727'],
        'night-blue' => ['name' => 'Azul nocturno', 'background' => '#17212B', 'surface' => '#202E3B', 'accent' => '#61C9C4', 'text' => '#E9F0F5', 'muted' => '#A9BAC8', 'border' => '#354653'],
        'deep-blue' => ['name' => 'Azul profundo', 'background' => '#101B2D', 'surface' => '#192840', 'accent' => '#79A9FF', 'text' => '#EAF1FA', 'muted' => '#A7B7CE', 'border' => '#30425C'],
        'dark-purple' => ['name' => 'Morado oscuro', 'background' => '#1C1825', 'surface' => '#292333', 'accent' => '#C4A2FF', 'text' => '#F0EBF7', 'muted' => '#B8ADC8', 'border' => '#433A50'],
        'forest-green' => ['name' => 'Verde bosque', 'background' => '#17231D', 'surface' => '#223229', 'accent' => '#91CF8D', 'text' => '#EAF2EB', 'muted' => '#AABCAF', 'border' => '#394B3F'],
        'dark-red' => ['name' => 'Rojo oscuro', 'background' => '#251B1B', 'surface' => '#332525', 'accent' => '#F08D86', 'text' => '#F5EBE9', 'muted' => '#C3AEAB', 'border' => '#4A3837'],
        'dark-coffee' => ['name' => 'Café oscuro', 'background' => '#24201A', 'surface' => '#322C23', 'accent' => '#E5B77E', 'text' => '#F1EAE0', 'muted' => '#BDB19F', 'border' => '#494135'],
        'graphite' => ['name' => 'Grafito', 'background' => '#20252A', 'surface' => '#2B3238', 'accent' => '#79C6CF', 'text' => '#E8EDF0', 'muted' => '#ACB8BF', 'border' => '#3E484F'],
        'slate' => ['name' => 'Pizarra', 'background' => '#171B20', 'surface' => '#232A32', 'accent' => '#9AB2D0', 'text' => '#E8EDF2', 'muted' => '#AAB5C1', 'border' => '#39434D'],
        'famfit-original' => ['name' => 'FamFit Original', 'background' => '#F5F7FB', 'surface' => '#FFFFFF', 'accent' => '#2864F0', 'text' => '#172033', 'muted' => '#748096', 'border' => '#E7EBF3'],
    ];

    public const CATEGORIES = [
        'light' => [
            'name' => 'Temas claros',
            'description' => 'Paletas luminosas con textos oscuros de alto contraste.',
            'themes' => ['classic-white', 'light-gray', 'pearl-gray', 'sky-blue', 'ice-blue', 'mint-green', 'lavender', 'cream'],
        ],
        'dark' => [
            'name' => 'Temas oscuros',
            'description' => 'Fondos oscuros con superficies y acentos adaptados.',
            'themes' => ['soft-gray', 'deep-gray', 'classic-black', 'amoled', 'night-blue', 'deep-blue', 'dark-purple', 'forest-green', 'dark-red', 'dark-coffee', 'graphite', 'slate'],
        ],
        'original' => [
            'name' => 'Tema original FamFit',
            'description' => 'La paleta original de FamFit Quest.',
            'themes' => ['famfit-original'],
        ],
    ];

    public static function resolve(?string $theme): string
    {
        return isset(self::THEMES[$theme]) ? $theme : self::DEFAULT;
    }

    public static function keys(): array
    {
        return array_keys(self::THEMES);
    }

    public static function isDark(?string $theme): bool
    {
        return in_array($theme, self::CATEGORIES['dark']['themes'], true);
    }
}
