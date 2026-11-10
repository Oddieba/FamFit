{{-- Requiere: $campo, $tipo ('radio' | 'checkbox'), $titulo, $opciones, $seleccionados (array)
     Opcional: $clase (por ejemplo 'opciones-compactas' para los accesorios) --}}
<fieldset class="editor-grupo">
    <legend class="editor-grupo-titulo">{{ $titulo }}</legend>

    <div class="opciones {{ $clase ?? '' }}">
        @foreach ($opciones as $clave => $opcion)
            <label class="opcion">
                <input type="{{ $tipo }}"
                       name="{{ $campo }}{{ $tipo === 'checkbox' ? '[]' : '' }}"
                       value="{{ $clave }}"
                       {{ in_array($clave, $seleccionados, true) ? 'checked' : '' }}>
                <span class="opcion-tarjeta">
                    <span class="opcion-imagen">
                        @if (! empty($opcion['imagen']))
                            <img src="{{ asset($opcion['imagen']) }}" alt="">
                        @else
                            <span aria-hidden="true">{{ $opcion['emoji'] }}</span>
                        @endif
                    </span>
                    <span class="opcion-nombre">{{ $opcion['nombre'] }}</span>
                </span>
            </label>
        @endforeach
    </div>
</fieldset>
