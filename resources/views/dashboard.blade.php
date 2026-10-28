<x-layouts::bulletin :title="__('Dashboard')" :compact="true">
    <div class="mx-auto w-full max-w-6xl space-y-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <x-user-avatar :user="auth()->user()" size="lg" class="ring-4 ring-white shadow-sm" />

                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
                        {{ __('Your dashboard') }}
                    </h1>

                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('Manage your posts and revisit the announcements you saved.') }}
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a
                    href="{{ route('posts.create') }}"
                    class="inline-flex items-center justify-center rounded-full bg-brand px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-dark"
                >
                    {{ __('Create a post') }}
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-brand-soft hover:text-brand"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-4" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                        {{ __('Log out') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $statCards = [
                    ['label' => __('My posts'), 'value' => $stats['posts']],
                    ['label' => __('Published'), 'value' => $stats['published']],
                    ['label' => __('Drafts'), 'value' => $stats['drafts']],
                    ['label' => __('Bookmarks'), 'value' => $stats['bookmarks']],
                ];
            @endphp

            @foreach ($statCards as $card)
                <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-900/5 its-card-shadow">
                    <p class="text-sm font-medium text-slate-500">{{ $card['label'] }}</p>
                    <p class="mt-1 text-3xl font-bold text-brand">{{ $card['value'] }}</p>
                </div>
            @endforeach
        </div>

        <section class="rounded-3xl bg-white ring-1 ring-slate-900/5 its-card-shadow">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-100 px-6 py-4 dark:border-zinc-800">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">
                    {{ __('My posts') }}
                </h2>

                <span class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ trans_choice(':count post|:count posts', $posts->count(), ['count' => $posts->count()]) }}
                </span>
            </div>

            @if ($posts->isEmpty())
                <div class="px-6 py-10 text-center">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('You have not created any posts yet.') }}
                    </p>

                    <a
                        href="{{ route('posts.create') }}"
                        class="mt-4 inline-flex items-center justify-center rounded-full bg-brand px-5 py-2 text-sm font-semibold text-white transition hover:bg-brand-dark"
                    >
                        {{ __('Write your first post') }}
                    </a>
                </div>
            @else
                <ul class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach ($posts as $post)
                        @php
                            $statusClasses = match ($post->status) {
                                \App\Enums\PostStatus::Published => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-400/15 dark:text-emerald-300',
                                \App\Enums\PostStatus::Draft => 'bg-amber-100 text-amber-800 dark:bg-amber-400/15 dark:text-amber-300',
                                default => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300',
                            };
                        @endphp

                        <li class="flex flex-col gap-4 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold tracking-wide uppercase {{ $statusClasses }}">
                                        {{ $post->status->label() }}
                                    </span>

                                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                        {{ $post->type->label() }} &middot; {{ $post->category->name }}
                                    </span>
                                </div>

                                <h3 class="mt-2 truncate text-base font-semibold text-zinc-900 dark:text-white">
                                    <a href="{{ route('posts.show', $post) }}" class="hover:text-brand">
                                        {{ $post->title }}
                                    </a>
                                </h3>

                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $post->isPublished()
                                        ? __('Published :date', ['date' => $post->published_at->format('d M Y')])
                                        : __('Last updated :date', ['date' => $post->updated_at->format('d M Y')]) }}
                                </p>
                            </div>

                            <div class="flex shrink-0 flex-wrap items-center gap-2">
                                <a
                                    href="{{ route('posts.show', $post) }}"
                                    class="rounded-full border border-slate-200 px-3.5 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-brand-soft hover:text-brand"
                                >
                                    {{ __('View') }}
                                </a>

                                <a
                                    href="{{ route('posts.edit', $post) }}"
                                    class="rounded-full border border-slate-200 px-3.5 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-brand-soft hover:text-brand"
                                >
                                    {{ __('Edit') }}
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('posts.destroy', $post) }}"
                                    onsubmit="return confirm('{{ __('Delete this post? This cannot be undone.') }}')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="rounded-full border border-red-200 px-3.5 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50"
                                    >
                                        {{ __('Delete') }}
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="rounded-3xl bg-white ring-1 ring-slate-900/5 its-card-shadow">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-zinc-100 px-6 py-4 dark:border-zinc-800">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">
                    {{ __('Bookmarked posts') }}
                </h2>

                <span class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ trans_choice(':count saved post|:count saved posts', $bookmarks->count(), ['count' => $bookmarks->count()]) }}
                </span>
            </div>

            @if ($bookmarks->isEmpty())
                <div class="px-6 py-10 text-center">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('You have not bookmarked any posts yet.') }}
                    </p>

                    <a
                        href="{{ route('home') }}"
                        class="mt-4 inline-flex items-center justify-center rounded-full border border-slate-200 px-5 py-2 text-sm font-semibold text-slate-600 transition hover:bg-brand-soft hover:text-brand"
                    >
                        {{ __('Browse the feed') }}
                    </a>
                </div>
            @else
                <ul class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach ($bookmarks as $bookmark)
                        <li class="flex flex-col gap-2 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
                                    {{ $bookmark->post->type->label() }} &middot; {{ $bookmark->post->category->name }}
                                </p>

                                <h3 class="mt-1 truncate text-base font-semibold text-zinc-900 dark:text-white">
                                    <a href="{{ route('posts.show', $bookmark->post) }}" class="hover:text-brand">
                                        {{ $bookmark->post->title }}
                                    </a>
                                </h3>

                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ __('By :name', ['name' => $bookmark->post->author->name]) }}
                                    &middot;
                                    {{ __('Published :date', ['date' => $bookmark->post->published_at->format('d M Y')]) }}
                                </p>
                            </div>

                            <a
                                href="{{ route('posts.show', $bookmark->post) }}"
                                class="shrink-0 rounded-lg border border-zinc-300 px-3 py-1.5 text-xs font-medium text-zinc-700 transition hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800"
                            >
                                {{ __('Open post') }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
</x-layouts::bulletin>
