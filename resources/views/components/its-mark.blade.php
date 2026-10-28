@props(['class' => ''])

<img
    src="{{ asset('images/its-mark-white.png') }}"
    {{ $attributes->merge(['class' => $class]) }}
    alt=""
    aria-hidden="true"
    draggable="false"
>
