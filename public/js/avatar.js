/* =========================================
   FAMFIT QUEST - EDITOR DE AVATAR (lógica)
   - Muestra el valor de los deslizadores y pinta su relleno.
   - Guarda la configuración en #avatar-visor (data-config)
     y dispara el evento "avatar:cambio" para que tu avatar reaccione.
   ========================================= */
(function () {
    'use strict';

    const formulario = document.getElementById('avatar-form');
    const visor = document.getElementById('avatar-visor');
    if (!formulario || !visor) return;

    function leerConfiguracion() {
        const datos = new FormData(formulario);
        return {
            cabello: datos.get('cabello'),
            ropa_superior: datos.get('ropa_superior'),
            ropa_inferior: datos.get('ropa_inferior'),
            calzado: datos.get('calzado'),
            accesorios: datos.getAll('accesorios[]'),
            color_cabello: datos.get('color_cabello'),
            color_ropa: datos.get('color_ropa'),
            largura_cabello: Number(datos.get('largura_cabello')),
            volumen_cabello: Number(datos.get('volumen_cabello'))
        };
    }

    function actualizarValores() {
        formulario.querySelectorAll('input[type="range"]').forEach((deslizador) => {
            const salida = formulario.querySelector('output[for="' + deslizador.id + '"]');
            if (salida) salida.textContent = deslizador.value + '%';
            deslizador.style.setProperty('--v', deslizador.value + '%');
        });
    }

    function publicarCambio() {
        const configuracion = leerConfiguracion();
        visor.dataset.config = JSON.stringify(configuracion);
        document.dispatchEvent(new CustomEvent('avatar:cambio', { detail: configuracion }));
    }

    formulario.addEventListener('input', () => {
        actualizarValores();
        publicarCambio();
    });

    actualizarValores();
    publicarCambio();

    /* Ejemplo para cuando tengas el avatar:
       document.addEventListener('avatar:cambio', (e) => {
           miAvatar.actualizar(e.detail);
       });
    */
})();
