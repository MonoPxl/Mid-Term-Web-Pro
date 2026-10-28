@props(['paginator'])

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination navigation') }}" class="mt-12 flex justify-center">
        <div class="inline-flex max-w-full flex-wrap items-center justify-center gap-1 rounded-full bg-white px-2.5 py-2 ring-1 ring-slate-900/5 its-card-shadow">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center gap-1 rounded-full px-3 py-2 text-sm font-medium text-slate-300 sm:px-4" aria-disabled="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('Previous') }}</span>
                </span>
            @else
                <a
                    href="{{ $paginator->previousPageUrl() }}"
                    rel="prev"
                    class="inline-flex items-center gap-1 rounded-full px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-brand-soft hover:text-brand sm:px-4"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('Previous') }}</span>
                </a>
            @endif

            @foreach ($paginator->linkCollection()->slice(1, -1) as $link)
                @if (is_null($link['url']))
                    <span class="inline-flex size-9 items-center justify-center text-sm text-slate-400" aria-hidden="true">
                        {{ $link['label'] }}
                    </span>
                @elseif ($link['active'])
                    <span
                        class="inline-flex size-9 items-center justify-center rounded-full bg-brand text-sm font-semibold text-white shadow-sm"
                        aria-current="page"
                    >
                        {{ $link['label'] }}
                    </span>
                @else
                    <a
                        href="{{ $link['url'] }}"
                        class="inline-flex size-9 items-center justify-center rounded-full text-sm font-medium text-slate-600 transition hover:bg-brand-soft hover:text-brand"
                        aria-label="{{ __('Go to page :page', ['page' => $link['page']]) }}"
                    >
                        {{ $link['label'] }}
                    </a>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a
                    href="{{ $paginator->nextPageUrl() }}"
                    rel="next"
                    class="inline-flex items-center gap-1 rounded-full px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-brand-soft hover:text-brand sm:px-4"
                >
                    <span class="hidden sm:inline">{{ __('Next') }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
                    </svg>
                </a>
            @else
                <span class="inline-flex items-center gap-1 rounded-full px-3 py-2 text-sm font-medium text-slate-300 sm:px-4" aria-disabled="true">
                    <span class="hidden sm:inline">{{ __('Next') }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
