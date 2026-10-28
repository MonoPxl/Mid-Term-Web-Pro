@props(['post'])

@php($isBookmarked = (bool) ($post->is_bookmarked ?? false))

<article class="group flex h-full flex-col overflow-hidden rounded-2xl bg-white ring-1 ring-slate-900/5 its-card-shadow transition duration-300 hover:-translate-y-1 hover:shadow-[0_20px_50px_rgba(15,23,42,0.14)]">
    <div class="its-cover relative aspect-[2.15/1] overflow-hidden">
        <a
            href="{{ route('posts.show', $post) }}"
            class="absolute inset-0 z-0 rounded-2xl focus-visible:outline-2 focus-visible:outline-offset-[-3px] focus-visible:outline-white"
            aria-label="{{ __('Read :title', ['title' => $post->title]) }}"
        ></a>

        @if ($post->cover_image_path)
            <img
                src="{{ $post->coverImageUrl() }}"
                alt=""
                loading="lazy"
                class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]"
            >
            <div class="pointer-events-none absolute inset-0 bg-linear-to-b from-brand-ink/45 via-brand-ink/5 to-brand-deep/55"></div>
        @else
            <div class="its-cover-grid pointer-events-none absolute inset-0"></div>
            <x-its-mark class="pointer-events-none absolute -right-10 -bottom-14 w-52 opacity-[0.16]" />
            <div class="pointer-events-none absolute inset-0 bg-linear-to-t from-brand-deep/45 via-transparent to-white/5"></div>
        @endif

        @auth
            <div class="absolute top-4 left-4 z-10 sm:top-5 sm:left-5">
                <livewire:bookmark-button :post="$post" :bookmarked="$isBookmarked" variant="icon" />
            </div>
        @else
            <a
                href="{{ route('login') }}"
                class="absolute top-4 left-4 z-10 inline-flex size-11 items-center justify-center rounded-2xl bg-white/15 text-white ring-1 ring-white/25 backdrop-blur-sm transition hover:bg-white/25 sm:top-5 sm:left-5 sm:size-12"
                aria-label="{{ __('Log in to bookmark this post') }}"
                title="{{ __('Log in to bookmark this post') }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="size-5 sm:size-[1.35rem]" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322A1 1 0 0 1 17.75 3.77v16.48a.75.75 0 0 1-1.166.624L10 16.65l-6.584 4.224A.75.75 0 0 1 2.25 20.25V3.77a1 1 0 0 1 .157-.448C2.828 2.75 3.5 2.25 4.5 2.25h11c1 0 1.672.5 2.093 1.072Z" />
                </svg>
            </a>
        @endauth

        <span class="pointer-events-none absolute top-4 right-4 z-10 rounded-full bg-white/10 px-3 py-1 text-[0.65rem] font-bold tracking-[0.14em] text-white uppercase ring-1 ring-white/20 backdrop-blur-sm sm:top-5 sm:right-5 sm:text-xs">
            {{ $post->type->label() }}
        </span>

        @if ($post->isEvent() && $post->event_starts_at)
            <span class="pointer-events-none absolute right-4 bottom-4 left-4 z-10 inline-flex items-center gap-2 rounded-full bg-white/95 px-3.5 py-2 text-xs font-semibold text-brand-deep shadow-sm backdrop-blur-sm sm:right-5 sm:bottom-5 sm:left-5 sm:text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z" clip-rule="evenodd" />
                </svg>
                <span class="truncate">
                    {{ $post->event_starts_at->format('d M Y, H:i') }}
                    @if ($post->event_location)
                        <span class="font-normal text-slate-500">&middot; {{ $post->event_location }}</span>
                    @endif
                </span>
            </span>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-6 sm:p-7">
        <h3 class="line-clamp-3 text-[1.55rem] leading-[1.15] font-semibold tracking-[-0.035em] text-slate-900 sm:line-clamp-2 sm:text-[1.8rem]">
            <a href="{{ route('posts.show', $post) }}" class="transition hover:text-brand">
                {{ $post->title }}
            </a>
        </h3>

        <div class="mt-4 flex flex-wrap gap-2">
            <span class="rounded-full bg-brand px-4 py-1.5 text-xs font-semibold text-white sm:text-sm">
                {{ $post->type->label() }}
            </span>

            <span class="rounded-full bg-brand-soft px-4 py-1.5 text-xs font-semibold text-brand ring-1 ring-inset ring-brand/10 sm:text-sm">
                {{ $post->category->name }}
            </span>
        </div>

        <div class="mt-auto flex flex-wrap items-center gap-x-2.5 gap-y-1 border-t border-slate-100 pt-4 text-xs text-slate-500 sm:text-sm">
            <x-user-avatar :user="$post->author" size="xs" />
            <span class="font-medium text-slate-600">{{ $post->author->name }}</span>
            <span class="text-slate-300" aria-hidden="true">&bull;</span>
            <time datetime="{{ $post->published_at->toIso8601String() }}">
                {{ $post->published_at->format('d M Y') }}
            </time>

            <span class="ms-auto inline-flex items-center gap-1.5 text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 2c-4.418 0-8 2.91-8 6.5 0 1.79.892 3.41 2.348 4.61-.154.708-.516 1.52-1.126 2.29a.75.75 0 0 0 .827 1.145c1.497-.35 2.645-1.04 3.43-1.68A9.75 9.75 0 0 0 10 15c4.418 0 8-2.91 8-6.5S14.418 2 10 2Z" clip-rule="evenodd" />
                </svg>
                {{ $post->comments_count }}
            </span>
        </div>
    </div>
</article>
