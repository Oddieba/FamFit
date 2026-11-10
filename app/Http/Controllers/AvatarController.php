<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AvatarController extends Controller
{
    /** Configuración con la que empieza el editor. */
    private const CONFIG_INICIAL = [
        'cabello'         => 'coceta_alta',
        'ropa_superior'   => 'top_deportivo',
        'ropa_inferior'   => 'leggins',
        'calzado'         => 'tenis_running',
        'accesorios'      => [],
        'color_cabello'   => 'azul_electrico',
        'color_ropa'      => 'azul_marino',
        'largura_cabello' => 70,
        'volumen_cabello' => 40,
    ];

    /**
     * Opciones disponibles en el editor.
     * Cuando tengas las imágenes, agrega 'imagen' => 'img/avatar/coceta-alta.png'
     * (ruta dentro de public/) y reemplazará al emoji.
     */
    private function catalogo(): array
    {
        return [
            'cabellos' => [
                'coceta_alta'       => ['nombre' => 'Coceta alta', 'emoji' => '💁‍♀️'],
                'trenza_francesa'   => ['nombre' => 'Trenza francesa', 'emoji' => '🪢'],
                'corto_pixie'       => ['nombre' => 'Corto pixie', 'emoji' => '✂️'],
                'mono_deportivo'    => ['nombre' => 'Moño deportivo', 'emoji' => '🎀'],
                'ondulado_suave'    => ['nombre' => 'Ondulado suave', 'emoji' => '🌊'],
                'flequillo_lateral' => ['nombre' => 'Flequillo lateral', 'emoji' => '💇'],
                'media_coleta'      => ['nombre' => 'Media coleta', 'emoji' => '🎗️'],
                'trenzas_laterales' => ['nombre' => 'Trenzas laterales', 'emoji' => '🧶'],
                'rizos_naturales'   => ['nombre' => 'Rizos naturales', 'emoji' => '🌀'],
                'liso_largo'        => ['nombre' => 'Liso largo', 'emoji' => '〰️'],
                'bob_corto'         => ['nombre' => 'Bob corto', 'emoji' => '💈'],
                'dos_colitas'       => ['nombre' => 'Dos colitas', 'emoji' => '🎀'],
                'rapado_lateral'    => ['nombre' => 'Rapado lateral', 'emoji' => '⚡'],
                'afro_voluminoso'   => ['nombre' => 'Afro voluminoso', 'emoji' => '☁️'],
            ],
            'ropa_superior' => [
                'top_deportivo'      => ['nombre' => 'Top deportivo', 'emoji' => '🎽'],
                'camiseta'           => ['nombre' => 'Camiseta', 'emoji' => '👕'],
                'sudadera'           => ['nombre' => 'Sudadera', 'emoji' => '🧥'],
                'musculosa'          => ['nombre' => 'Musculosa', 'emoji' => '🏃'],
                'manga_larga'        => ['nombre' => 'Manga larga', 'emoji' => '👚'],
                'chaqueta_deportiva' => ['nombre' => 'Chaqueta deportiva', 'emoji' => '🧥'],
                'croptop'            => ['nombre' => 'Crop top', 'emoji' => '🎽'],
                'polo'               => ['nombre' => 'Polo', 'emoji' => '👕'],
                'chaleco'            => ['nombre' => 'Chaleco', 'emoji' => '🦺'],
                'compresion'         => ['nombre' => 'Camiseta de compresión', 'emoji' => '🥋'],
            ],
            'ropa_inferior' => [
                'leggins'           => ['nombre' => 'Leggins', 'emoji' => '🩱'],
                'short'             => ['nombre' => 'Short deportivo', 'emoji' => '🩳'],
                'jogger'            => ['nombre' => 'Jogger', 'emoji' => '👖'],
                'bicicletero'       => ['nombre' => 'Bicicletero', 'emoji' => '🚴'],
                'falda_deportiva'   => ['nombre' => 'Falda deportiva', 'emoji' => '👗'],
                'pantalon_ancho'    => ['nombre' => 'Pantalón ancho', 'emoji' => '👖'],
                'capri'             => ['nombre' => 'Capri', 'emoji' => '🩱'],
                'pantaloneta_larga' => ['nombre' => 'Pantaloneta larga', 'emoji' => '🩳'],
            ],
            'calzado' => [
                'tenis_running'            => ['nombre' => 'Tenis running', 'emoji' => '👟'],
                'tenis_casuales'           => ['nombre' => 'Tenis casuales', 'emoji' => '👟'],
                'zapatillas_entrenamiento' => ['nombre' => 'Zapatillas de entrenamiento', 'emoji' => '👟'],
                'botas_trekking'           => ['nombre' => 'Botas de trekking', 'emoji' => '🥾'],
                'sandalias_deportivas'     => ['nombre' => 'Sandalias deportivas', 'emoji' => '🩴'],
            ],
            'accesorios' => [
                'botella_termica'  => ['nombre' => 'Botella térmica', 'emoji' => '🧴'],
                'mancuernas_5kg'   => ['nombre' => 'Mancuernas 5 kg', 'emoji' => '🏋️'],
                'cinta_absorbente' => ['nombre' => 'Cinta absorbente', 'emoji' => '🎗️'],
                'gorra'            => ['nombre' => 'Gorra', 'emoji' => '🧢'],
                'gafas_deportivas' => ['nombre' => 'Gafas deportivas', 'emoji' => '🕶️'],
                'munequeras'       => ['nombre' => 'Muñequeras', 'emoji' => '🧤'],
                'audifonos'        => ['nombre' => 'Audífonos', 'emoji' => '🎧'],
                'reloj_deportivo'  => ['nombre' => 'Reloj deportivo', 'emoji' => '⌚'],
                'mochila'          => ['nombre' => 'Mochila', 'emoji' => '🎒'],
                'banda_elastica'   => ['nombre' => 'Banda elástica', 'emoji' => '➰'],
                'guantes_gimnasio' => ['nombre' => 'Guantes de gimnasio', 'emoji' => '🥊'],
                'cuerda_saltar'    => ['nombre' => 'Cuerda de saltar', 'emoji' => '🪢'],
            ],
            'colores' => [
                'cian'           => ['nombre' => 'Cian', 'hex' => '#0EA5A5'],
                'verde_lima'     => ['nombre' => 'Verde lima', 'hex' => '#A7E66D'],
                'azul_electrico' => ['nombre' => 'Azul eléctrico', 'hex' => '#2864F0'],
                'azul_marino'    => ['nombre' => 'Azul marino', 'hex' => '#0B1F5C'],
                'negro'          => ['nombre' => 'Negro', 'hex' => '#111827'],
                'blanco'         => ['nombre' => 'Blanco', 'hex' => '#F8FAFC'],
                'gris'           => ['nombre' => 'Gris', 'hex' => '#94A3B8'],
                'rojo'           => ['nombre' => 'Rojo', 'hex' => '#DC2626'],
                'naranja'        => ['nombre' => 'Naranja', 'hex' => '#F97316'],
                'amarillo'       => ['nombre' => 'Amarillo', 'hex' => '#FACC15'],
                'verde'          => ['nombre' => 'Verde', 'hex' => '#16A34A'],
                'morado'         => ['nombre' => 'Morado', 'hex' => '#7C3AED'],
                'rosado'         => ['nombre' => 'Rosado', 'hex' => '#EC4899'],
                'cafe'           => ['nombre' => 'Café', 'hex' => '#78350F'],
            ],
        ];
    }

    /**
     * Muestra el editor.
     */
    public function editar(Request $request): View
    {
        // TODO: cuando guardes el avatar en la BD, carga aquí la configuración
        // del usuario (por ejemplo $request->user()->avatar_config) en vez de la inicial.
        $config = self::CONFIG_INICIAL;

        return view('avatar.personalizar', $this->catalogo() + compact('config'));
    }

    /**
     * Valida y guarda los cambios.
     */
    public function guardar(Request $request): RedirectResponse
    {
        $catalogo = $this->catalogo();

        $datos = $request->validate([
            'cabello'         => ['required', Rule::in(array_keys($catalogo['cabellos']))],
            'ropa_superior'   => ['required', Rule::in(array_keys($catalogo['ropa_superior']))],
            'ropa_inferior'   => ['required', Rule::in(array_keys($catalogo['ropa_inferior']))],
            'calzado'         => ['required', Rule::in(array_keys($catalogo['calzado']))],
            'accesorios'      => ['nullable', 'array'],
            'accesorios.*'    => [Rule::in(array_keys($catalogo['accesorios']))],
            'color_cabello'   => ['required', Rule::in(array_keys($catalogo['colores']))],
            'color_ropa'      => ['required', Rule::in(array_keys($catalogo['colores']))],
            'largura_cabello' => ['required', 'integer', 'between:0,100'],
            'volumen_cabello' => ['required', 'integer', 'between:0,100'],
        ]);

        $datos['accesorios'] = $datos['accesorios'] ?? [];

        // TODO: guardar $datos en la BD (por ejemplo, una tabla "avatares"
        // o una columna JSON en users).

        // withInput() mantiene lo elegido en pantalla mientras no exista la BD.
        return redirect()
            ->route('avatar.editar')
            ->withInput($datos)
            ->with('estado', '¡Cambios guardados!');
    }
}
