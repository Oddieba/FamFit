{{-- Requiere: $campo, $titulo, $colores, $seleccionado --}}
<fieldset class="editor-grupo caja">
    <legend class="editor-grupo-titulo">{{ $titulo }}</legend>

    <div class="paleta">
        @foreach ($colores as $clave => $color)
            <label class="muestra">
                <input type="radio"
                       name="{{ $campo }}"
                       value="{{ $clave }}"
                       {{ $clave === $seleccionado ? 'checked' : '' }}>
                <span class="muestra-circulo" style="--muestra: {{ $color['hex'] }}"></span>
                <span class="muestra-nombre">{{ $color['nombre'] }}</span>
            </label>
        @endforeach
    </div>
</fieldset>
