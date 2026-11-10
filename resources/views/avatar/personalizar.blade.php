<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, interactive-widget=resizes-content">
    <meta name="theme-color" content="#F5F7FB">

    <title>Personalizar avatar | FamFit Quest</title>
    <script src="{{ asset('js/theme.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('estilos/general.css') }}">
    <link rel="stylesheet" href="{{ asset('estilos/estiloavatar.css') }}">
    <link rel="stylesheet" href="{{ asset('estilos/tema-oscuro.css') }}">

</head>
<body class="avatar-pagina">

    @php
        // Menú lateral. Ajusta los nombres de ruta a los de tu routes/web.php.
        // Si una ruta aún no existe, el enlace apunta a "#" en vez de dar error.
        $menu = [
            ['texto' => 'Inicio',        'ruta' => 'menu',           'patrones' => ['menu'],           'icono' => 'inicio',        'color' => '#3EE0C1'],
            ['texto' => 'Entrenamiento', 'ruta' => 'entrenamientos', 'patrones' => ['entrenamientos'], 'icono' => 'entrenamiento', 'color' => '#3EE0C1'],
            ['texto' => 'Nutrición',     'ruta' => 'bienestar',      'patrones' => ['bienestar'],      'icono' => 'nutricion',     'color' => '#A7E66D'],
            ['texto' => 'Perfil',        'ruta' => 'avatar.editar',  'patrones' => ['avatar.*'],       'icono' => 'perfil',        'color' => '#FFFFFF'],
            ['texto' => 'Ajustes',       'ruta' => 'ajustes',        'patrones' => ['ajustes*'],       'icono' => 'ajustes',       'color' => '#7FA8FF'],
        ];
    @endphp

    <div class="avatar-app">

        <!-- MENÚ LATERAL -->
        <aside class="barra-lateral">
            <a href="{{ Route::has('menu') ? route('menu') : url('/') }}" class="logo-lateral" aria-label="FamFit, ir al inicio">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="#A7E66D" d="M2 5c6.5 0 10 3.2 10 8.5C5.5 13.5 2 10.3 2 5z"/>
                    <path fill="#4ADE80" d="M22 3.5c0 7.5-4.5 12-10 12 0-6.5 3.5-12 10-12z"/>
                    <path fill="none" stroke="#A7E66D" stroke-width="1.8" stroke-linecap="round" d="M12 21v-7.5"/>
                </svg>
                <span>famfit</span>
            </a>

            @foreach ($menu as $item)
                @php $activo = request()->routeIs(...$item['patrones']); @endphp
                <a href="{{ Route::has($item['ruta']) ? route($item['ruta']) : '#' }}"
                   class="nav-item {{ $activo ? 'activo' : '' }}"
                   style="--icono: {{ $item['color'] }}"
                   @if ($activo) aria-current="page" @endif>
                    @include('avatar.partials.icono', ['nombre' => $item['icono']])
                    <span>{{ $item['texto'] }}</span>
                </a>
            @endforeach

            <a href="{{ Route::has('ayuda') ? route('ayuda') : '#' }}"
               class="nav-item nav-ayuda"
               style="--icono: #8FA3C7">
                @include('avatar.partials.icono', ['nombre' => 'ayuda'])
                <span>Ayuda</span>
            </a>
        </aside>

        <!-- ESCENARIO: aquí va el avatar -->
        <section class="escenario" aria-label="Vista previa del avatar">
            {{-- AQUÍ VA EL AVATAR: reemplaza el contenido de este div por tu <img>, <canvas> o visor 3D.
                 Cada vez que el usuario cambia algo, el JS actualiza data-config y dispara
                 el evento "avatar:cambio" en document (ver public/js/avatar.js). --}}
            <div class="avatar-lienzo" id="avatar-visor" role="img" aria-label="Espacio para el avatar" data-config="">
                <span>Aquí irá tu avatar</span>
            </div>
        </section>

        <!-- PANEL DE PERSONALIZACIÓN -->
        <form id="avatar-form" class="editor" method="POST" action="{{ route('avatar.guardar') }}">
            @csrf

            <h1 class="editor-titulo">Personalizar Avatar</h1>

            @if (session('estado'))
                <div class="aviso aviso-exito" role="status">{{ session('estado') }}</div>
            @endif

            @if ($errors->any())
                <div class="aviso aviso-error" role="alert">
                    <strong>Revisa los datos:</strong>
                    <ul class="lista-errores">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Pestañas (sin JavaScript: radios + labels) -->
            <input type="radio" name="pestana-avatar" id="tab-cabello" class="pestana-radio" checked>
            <input type="radio" name="pestana-avatar" id="tab-ropa" class="pestana-radio">
            <input type="radio" name="pestana-avatar" id="tab-accesorios" class="pestana-radio">
            <input type="radio" name="pestana-avatar" id="tab-colores" class="pestana-radio">

            <div class="editor-pestanas">
                <label for="tab-cabello">Cabello</label>
                <label for="tab-ropa">Ropa</label>
                <label for="tab-accesorios">Accesorios</label>
                <label for="tab-colores">Colores</label>
            </div>

            <!-- CABELLO -->
            <section class="editor-panel editor-panel-cabello" aria-label="Cabello">
                @include('avatar.partials.opciones', [
                    'campo'         => 'cabello',
                    'tipo'          => 'radio',
                    'titulo'        => 'Estilo de cabello',
                    'opciones'      => $cabellos,
                    'seleccionados' => [old('cabello', $config['cabello'])],
                ])

                <fieldset class="editor-grupo caja">
                    <legend class="editor-grupo-titulo">Ajustes del cabello</legend>

                    <div class="ajuste">
                        <label class="etiqueta" for="largura_cabello">Largura del cabello</label>
                        <input type="range" class="deslizador" id="largura_cabello" name="largura_cabello"
                               min="0" max="100" step="5"
                               value="{{ old('largura_cabello', $config['largura_cabello']) }}"
                               style="--v: {{ old('largura_cabello', $config['largura_cabello']) }}%">
                        <output for="largura_cabello">{{ old('largura_cabello', $config['largura_cabello']) }}%</output>
                    </div>

                    <div class="ajuste">
                        <label class="etiqueta" for="volumen_cabello">Volumen</label>
                        <input type="range" class="deslizador" id="volumen_cabello" name="volumen_cabello"
                               min="0" max="100" step="5"
                               value="{{ old('volumen_cabello', $config['volumen_cabello']) }}"
                               style="--v: {{ old('volumen_cabello', $config['volumen_cabello']) }}%">
                        <output for="volumen_cabello">{{ old('volumen_cabello', $config['volumen_cabello']) }}%</output>
                    </div>
                </fieldset>
            </section>

            <!-- ROPA -->
            <section class="editor-panel editor-panel-ropa" aria-label="Ropa">

                <!-- Subpestañas: elegir qué prenda editar sin hacer scroll -->
                <input type="radio" name="subpestana-ropa" id="sub-superior" class="subpestana-radio" checked>
                <input type="radio" name="subpestana-ropa" id="sub-inferior" class="subpestana-radio">
                <input type="radio" name="subpestana-ropa" id="sub-calzado" class="subpestana-radio">

                <div class="subpestanas">
                    <label for="sub-superior">Superior</label>
                    <label for="sub-inferior">Inferior</label>
                    <label for="sub-calzado">Calzado</label>
                </div>

                <div class="subpanel subpanel-superior">
                    @include('avatar.partials.opciones', [
                        'campo'         => 'ropa_superior',
                        'tipo'          => 'radio',
                        'titulo'        => 'Parte superior',
                        'opciones'      => $ropa_superior,
                        'seleccionados' => [old('ropa_superior', $config['ropa_superior'])],
                    ])
                </div>

                <div class="subpanel subpanel-inferior">
                    @include('avatar.partials.opciones', [
                        'campo'         => 'ropa_inferior',
                        'tipo'          => 'radio',
                        'titulo'        => 'Parte inferior',
                        'opciones'      => $ropa_inferior,
                        'seleccionados' => [old('ropa_inferior', $config['ropa_inferior'])],
                    ])
                </div>

                <div class="subpanel subpanel-calzado">
                    @include('avatar.partials.opciones', [
                        'campo'         => 'calzado',
                        'tipo'          => 'radio',
                        'titulo'        => 'Calzado',
                        'opciones'      => $calzado,
                        'seleccionados' => [old('calzado', $config['calzado'])],
                    ])
                </div>
            </section>

            <!-- ACCESORIOS -->
            <section class="editor-panel editor-panel-accesorios" aria-label="Accesorios">
                @include('avatar.partials.opciones', [
                    'campo'         => 'accesorios',
                    'tipo'          => 'checkbox',
                    'titulo'        => 'Accesorios disponibles',
                    'opciones'      => $accesorios,
                    'seleccionados' => old('accesorios', $config['accesorios']),
                    'clase'         => 'opciones-compactas',
                ])
            </section>

            <!-- COLORES -->
            <section class="editor-panel editor-panel-colores" aria-label="Colores">
                @include('avatar.partials.paleta', [
                    'campo'        => 'color_cabello',
                    'titulo'       => 'Color del cabello',
                    'colores'      => $colores,
                    'seleccionado' => old('color_cabello', $config['color_cabello']),
                ])

                @include('avatar.partials.paleta', [
                    'campo'        => 'color_ropa',
                    'titulo'       => 'Color de la ropa',
                    'colores'      => $colores,
                    'seleccionado' => old('color_ropa', $config['color_ropa']),
                ])
            </section>

            <div class="editor-acciones">
                <button type="submit" class="btn-guardar" id="btn-guardar" data-estado="">
                    <span class="btn-capa btn-normal">Guardar cambios</span>
                    <span class="btn-capa btn-ok" aria-hidden="true">✓ Guardado</span>
                </button>
            </div>
        </form>

    </div>

    <script src="{{ asset('js/avatar.js') }}"></script>
</body>
</html>
