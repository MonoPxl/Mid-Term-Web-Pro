@props(['user', 'size' => 'md'])

@php
    $sizeClasses = match ($size) {
        'xs' => 'size-6 text-[0.6rem]',
        'sm' => 'size-8 text-xs',
        'lg' => 'size-12 text-base',
        'xl' => 'size-24 text-3xl',
        default => 'size-10 text-sm',
    };

    $avatarUrl = $user?->avatarUrl();
@endphp

@if ($avatarUrl)
    <img
        src="{{ $avatarUrl }}"
        alt="{{ $user->name }}"
        {{ $attributes->merge(['class' => $sizeClasses.' shrink-0 rounded-full object-cover ring-1 ring-black/5']) }}
    >
@else
    <span
        {{ $attributes->merge(['class' => $sizeClasses.' inline-flex shrink-0 items-center justify-center rounded-full bg-brand-soft font-bold text-brand ring-1 ring-inset ring-brand/10']) }}
        role="img"
        aria-label="{{ $user?->name }}"
    >
        {{ $user?->initials() }}
    </span>
@endif
