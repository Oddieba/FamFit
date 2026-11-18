<nav class="fq-social-actions" aria-label="Navegación social">
    <a class="fq-button fq-button-light" href="{{ route('social') }}" @if(request()->routeIs('social')) aria-current="page" @endif>Inicio</a>
    <a class="fq-button fq-button-light" href="{{ route('social.discover') }}" @if(request()->routeIs('social.discover')) aria-current="page" @endif>Descubrir</a>
    <a class="fq-button fq-button-light" href="{{ route('social.explore') }}" @if(request()->routeIs('social.explore')) aria-current="page" @endif>Explorar</a>
    <a class="fq-button fq-button-light" href="{{ route('social.friends') }}" @if(request()->routeIs('social.friends')) aria-current="page" @endif>Amigos</a>
    <a class="fq-button fq-button-light" href="{{ route('social.requests') }}" @if(request()->routeIs('social.requests')) aria-current="page" @endif>Solicitudes</a>
    <a class="fq-button fq-button-light" href="{{ route('social.profile') }}" @if(request()->routeIs('social.profile')) aria-current="page" @endif>Mi perfil social</a>
</nav>
