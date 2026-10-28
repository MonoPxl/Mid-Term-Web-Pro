@php
    $label = $bookmarked ? __('Remove bookmark') : __('Bookmark this post');
    $iconLabel = $bookmarked ? __('Remove bookmark') : __('Save for later');
@endphp

<button
    type="button"
    wire:click="toggle"
    wire:loading.attr="disabled"
    wire:target="toggle"
    @class([
        'inline-flex items-center justify-center transition disabled:cursor-wait disabled:opacity-60',
        'size-11 rounded-2xl ring-1 backdrop-blur-sm sm:size-12' => $variant === 'icon',
        'bg-white text-brand shadow-sm ring-white/50' => $variant === 'icon' && $bookmarked,
        'bg-white/15 text-white ring-white/25 hover:bg-white/25' => $variant === 'icon' && ! $bookmarked,
        'w-full gap-2 rounded-full px-4 py-2.5 text-sm font-semibold' => $variant === 'full',
        'border border-slate-200 text-slate-600 hover:bg-brand-soft hover:text-brand' => $variant === 'full' && $bookmarked,
        'bg-brand text-white shadow-sm hover:bg-brand-dark' => $variant === 'full' && ! $bookmarked,
    ])
    aria-label="{{ $variant === 'full' ? $label : $iconLabel }}"
    aria-pressed="{{ $bookmarked ? 'true' : 'false' }}"
    title="{{ $variant === 'full' ? $label : $iconLabel }}"
>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        fill="{{ $bookmarked ? 'currentColor' : 'none' }}"
        stroke="currentColor"
        stroke-width="1.7"
        @class([
            'size-5 sm:size-[1.35rem]' => $variant === 'icon',
            'size-4' => $variant === 'full',
        ])
        aria-hidden="true"
    >
        <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322A1 1 0 0 1 17.75 3.77v16.48a.75.75 0 0 1-1.166.624L10 16.65l-6.584 4.224A.75.75 0 0 1 2.25 20.25V3.77a1 1 0 0 1 .157-.448C2.828 2.75 3.5 2.25 4.5 2.25h11c1 0 1.672.5 2.093 1.072Z" />
    </svg>

    @if ($variant === 'full')
        <span>{{ $label }}</span>
    @endif
</button>
