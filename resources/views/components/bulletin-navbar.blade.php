@props(['brand' => true, 'padded' => true])

@php
    $containerClasses = 'relative mx-auto w-full max-w-[1320px]'.($padded ? ' px-4 sm:px-6 lg:px-8' : '');
@endphp

<div {{ $attributes->merge(['class' => $containerClasses]) }}>
    <div class="flex items-center justify-between gap-3 py-3 sm:py-4 md:grid md:grid-cols-[minmax(0,1fr)_auto_minmax(0,1fr)]">
        <div class="flex min-w-0 items-center">
            @if ($brand)
                <a href="{{ route('home') }}" class="inline-flex shrink-0 items-baseline gap-1.5 text-white">
                    <span class="text-lg font-extrabold tracking-[-0.045em] sm:text-xl">myITS</span>
                    <span class="text-lg font-semibold tracking-[-0.035em] sm:text-xl">Broadcast</span>
                </a>
            @endif
        </div>

        <div class="hidden justify-center md:flex">
            <x-bulletin-nav />
        </div>

        <div class="flex items-center justify-end gap-2">
            <x-bulletin-account />
        </div>
    </div>

    <div class="flex justify-center pb-3 md:hidden">
        <x-bulletin-nav />
    </div>
</div>
