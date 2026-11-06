{{-- Requiere: $nombre (inicio, entrenamiento, nutricion, perfil, ajustes, ayuda) --}}
<svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true" focusable="false">
    @switch($nombre)
        @case('inicio')
            <path fill="currentColor" d="M12 3 2 11.5h3V20h5.5v-5.5h3V20H19v-8.5h3L12 3z"/>
            @break
        @case('entrenamiento')
            <path fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" d="M6 7v10M3.5 9.5v5M18 7v10M20.5 9.5v5M6 12h12"/>
            @break
        @case('nutricion')
            <path fill="currentColor" d="M12 7.5c1.2-1 2.6-1.6 4.3-1.2 2.7.6 4.2 3.4 3.5 6.6-.7 3.3-2.8 6.6-5 6.6-.9 0-1.4-.5-2.8-.5s-1.9.5-2.8.5c-2.2 0-4.3-3.3-5-6.6-.7-3.2.8-6 3.5-6.6 1.7-.4 3.1.2 4.3 1.2z"/>
            <path fill="currentColor" d="M12 6c0-1.6.9-3 2.5-3.5 0 1.6-.9 3-2.5 3.5z"/>
            @break
        @case('perfil')
            <circle cx="12" cy="8" r="4" fill="currentColor"/>
            <path fill="currentColor" d="M4 20c0-4 3.6-6 8-6s8 2 8 6v1H4v-1z"/>
            @break
        @case('ajustes')
            <path fill="currentColor" fill-rule="evenodd" d="M10.3 2h3.4l.5 2.4c.6.2 1.1.5 1.6.9l2.3-.8 1.7 2.9-1.8 1.6c.1.6.1 1.2 0 1.8l1.8 1.6-1.7 2.9-2.3-.8c-.5.4-1 .7-1.6.9l-.5 2.4h-3.4l-.5-2.4c-.6-.2-1.1-.5-1.6-.9l-2.3.8-1.7-2.9 1.8-1.6a6 6 0 0 1 0-1.8L4.2 8.4l1.7-2.9 2.3.8c.5-.4 1-.7 1.6-.9L10.3 2zM12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/>
            @break
        @case('ayuda')
            <circle cx="12" cy="12" r="9.5" fill="currentColor"/>
            <path d="M9.5 9.5a2.5 2.5 0 1 1 3.6 2.2c-.7.4-1.1.9-1.1 1.8" fill="none" stroke="#050D1F" stroke-width="2" stroke-linecap="round"/>
            <circle cx="12" cy="17" r="1.1" fill="#050D1F"/>
            @break
    @endswitch
</svg>
