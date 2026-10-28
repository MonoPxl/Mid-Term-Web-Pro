@php
    $baseClasses = 'inline-flex items-center justify-center gap-2 rounded-full px-3.5 py-2 text-sm font-semibold transition sm:px-5 sm:text-[0.95rem]';
    $activeClasses = 'bg-white text-brand shadow-sm';
    $idleClasses = 'text-white/80 hover:bg-white/10 hover:text-white';
@endphp

<nav {{ $attributes->merge(['class' => 'flex flex-wrap items-center justify-center gap-1.5 sm:gap-2']) }} aria-label="{{ __('Primary navigation') }}">
    <a
        href="{{ route('home') }}"
        @class([$baseClasses, request()->routeIs('home', 'posts.show') ? $activeClasses : $idleClasses])
        @if (request()->routeIs('home', 'posts.show')) aria-current="page" @endif
    >
        {{ __('Feed') }}
    </a>

    @auth
        <a
            href="{{ route('dashboard') }}"
            @class([$baseClasses, request()->routeIs('dashboard', 'posts.create', 'posts.edit') ? $activeClasses : $idleClasses])
            @if (request()->routeIs('dashboard', 'posts.create', 'posts.edit')) aria-current="page" @endif
        >
            {{ __('My Broadcast') }}
        </a>
    @endauth
</nav>
