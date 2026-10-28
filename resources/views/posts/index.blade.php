<x-layouts::bulletin :title="__('Campus Bulletin')">
    <section class="relative z-10 -mt-14 sm:-mt-16" aria-label="{{ __('Search and filter broadcasts') }}">
        <form method="GET" action="{{ route('home') }}" class="mx-auto max-w-6xl">
            <div class="flex items-center gap-3 rounded-full bg-white py-2.5 pr-4 pl-5 ring-1 ring-slate-900/5 its-shadow sm:gap-4 sm:py-3 sm:pr-6 sm:pl-7">
                <button
                    type="submit"
                    class="flex size-11 shrink-0 items-center justify-center rounded-full text-slate-500 transition hover:bg-brand-soft hover:text-brand sm:size-14"
                    aria-label="{{ __('Search') }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-7 sm:size-9" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 0 0-15 7.5 7.5 0 0 0 0 15Z" />
                    </svg>
                </button>

                <label for="q" class="sr-only">{{ __('Search broadcasts') }}</label>

                <input
                    id="q"
                    name="q"
                    type="search"
                    value="{{ $filters['q'] ?? '' }}"
                    maxlength="100"
                    placeholder="{{ __('Looking for anything specific?') }}"
                    class="min-w-0 flex-1 border-0 bg-transparent p-0 text-lg font-medium text-slate-900 placeholder-slate-500 focus:ring-0 focus:outline-none sm:text-2xl"
                >

                @if ($hasFilters)
                    <a
                        href="{{ route('home') }}"
                        class="hidden shrink-0 text-sm font-semibold text-slate-400 transition hover:text-brand sm:block"
                    >
                        {{ __('Clear') }}
                    </a>
                @endif
            </div>

            <div class="mt-4 grid grid-cols-2 gap-2 sm:flex sm:flex-wrap sm:items-center sm:justify-center">
                <label for="category" class="sr-only">{{ __('Category') }}</label>

                <select
                    id="category"
                    name="category"
                    class="w-full rounded-full border-0 bg-white py-2.5 pr-9 pl-4 text-sm font-medium text-slate-600 ring-1 ring-slate-200 transition focus:ring-2 focus:ring-brand focus:outline-none sm:w-auto"
                >
                    <option value="">{{ __('All categories') }}</option>

                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected(($filters['category'] ?? null) === $category->slug)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <label for="type" class="sr-only">{{ __('Post type') }}</label>

                <select
                    id="type"
                    name="type"
                    class="w-full rounded-full border-0 bg-white py-2.5 pr-9 pl-4 text-sm font-medium text-slate-600 ring-1 ring-slate-200 transition focus:ring-2 focus:ring-brand focus:outline-none sm:w-auto"
                >
                    <option value="">{{ __('All types') }}</option>

                    @foreach (\App\Enums\PostType::cases() as $postType)
                        <option value="{{ $postType->value }}" @selected(($filters['type'] ?? null) === $postType->value)>
                            {{ $postType->label() }}
                        </option>
                    @endforeach
                </select>

                <button
                    type="submit"
                    class="col-span-2 w-full rounded-full bg-brand px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-dark sm:col-span-1 sm:w-auto"
                >
                    {{ __('Filter') }}
                </button>
            </div>
        </form>
    </section>

    <section class="mt-12" aria-labelledby="latest-broadcasts">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 id="latest-broadcasts" class="text-lg font-bold tracking-tight text-slate-900 sm:text-xl">
                    {{ $hasFilters ? __('Search results') : __('Latest broadcasts') }}
                </h2>

                <p class="mt-0.5 text-sm text-slate-500">
                    {{ trans_choice(':count published broadcast|:count published broadcasts', $posts->total(), ['count' => $posts->total()]) }}
                </p>
            </div>

        </div>

        @if ($hasFilters)
            <p class="mb-6 rounded-2xl bg-brand-soft px-4 py-3 text-sm text-brand-deep">
                {{ __('Showing published broadcasts that match your search and filters.') }}
            </p>
        @endif

        @if ($posts->isEmpty())
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center its-card-shadow">
                <span class="mx-auto flex size-14 items-center justify-center rounded-full bg-brand-soft text-brand">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-7" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 0 0-15 7.5 7.5 0 0 0 0 15Z" />
                    </svg>
                </span>

                <p class="mt-4 text-lg font-semibold text-slate-900">
                    {{ $hasFilters ? __('No broadcasts found') : __('No broadcasts yet') }}
                </p>

                <p class="mx-auto mt-1 max-w-md text-sm leading-6 text-slate-500">
                    {{ $hasFilters
                        ? __('No published posts match your search. Try a different keyword or clear the filters.')
                        : __('No published posts yet. Check back soon.') }}
                </p>

                @if ($hasFilters)
                    <a
                        href="{{ route('home') }}"
                        class="mt-5 inline-flex items-center justify-center rounded-full bg-brand px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-dark"
                    >
                        {{ __('Clear filters') }}
                    </a>
                @endif
            </div>
        @else
            <div class="grid gap-7 md:grid-cols-2 lg:gap-8">
                @foreach ($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>

            <x-pagination :paginator="$posts" />
        @endif
    </section>
</x-layouts::bulletin>
