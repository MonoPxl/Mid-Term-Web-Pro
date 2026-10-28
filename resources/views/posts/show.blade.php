<x-layouts::bulletin :title="$post->title" :compact="true">
    <div class="mx-auto w-full max-w-6xl">
        <a
            href="{{ route('home') }}"
            class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-600 ring-1 ring-slate-200 transition hover:bg-brand-soft hover:text-brand"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4" aria-hidden="true">
                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd" />
            </svg>
            {{ __('Back to the feed') }}
        </a>

        @unless ($post->isPublished())
            <div class="mt-4 rounded-2xl border border-amber-300 bg-amber-50 px-5 py-4 text-sm font-medium text-amber-900">
                {{ __('This post is a draft. Only you can see this preview.') }}
            </div>
        @endunless

        <div class="mt-6 grid items-start gap-7 lg:grid-cols-[minmax(0,1fr)_320px] lg:gap-8">
            <article class="overflow-hidden rounded-3xl bg-white ring-1 ring-slate-900/5 its-card-shadow">
                <header @class([
                    'relative overflow-hidden bg-brand-deep px-6 py-9 text-white sm:px-10 sm:py-12',
                    'its-cover' => ! $post->cover_image_path,
                ])>
                    @if ($post->cover_image_path)
                        <img
                            src="{{ $post->coverImageUrl() }}"
                            alt=""
                            class="absolute inset-0 h-full w-full object-cover"
                        >
                        <div class="absolute inset-0 bg-linear-to-t from-brand-ink/90 via-brand-deep/60 to-brand-deep/20"></div>
                    @else
                        <div class="its-cover-grid pointer-events-none absolute inset-0"></div>
                        <x-its-mark class="pointer-events-none absolute -right-16 -bottom-20 w-72 opacity-[0.14] sm:w-80" />
                        <div class="absolute inset-0 bg-linear-to-t from-brand-deep/40 via-transparent to-white/5"></div>
                    @endif

                    <div class="relative">
                        <div class="flex flex-wrap items-center gap-2 text-xs font-bold tracking-[0.12em] uppercase">
                            <span class="rounded-full bg-white px-3.5 py-1.5 text-brand">
                                {{ $post->type->label() }}
                            </span>

                            <span class="rounded-full bg-white/15 px-3.5 py-1.5 text-white ring-1 ring-white/25">
                                {{ $post->category->name }}
                            </span>

                            @unless ($post->isPublished())
                                <span class="rounded-full bg-amber-300 px-3.5 py-1.5 text-amber-950">
                                    {{ $post->status->label() }}
                                </span>
                            @endunless
                        </div>

                        <h1 class="mt-5 max-w-3xl text-3xl leading-[1.05] font-bold tracking-[-0.045em] sm:text-5xl lg:text-[3.4rem]">
                            {{ $post->title }}
                        </h1>

                        <div class="mt-5 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm font-medium text-white/75">
                            <span class="inline-flex items-center gap-2">
                                <x-user-avatar :user="$post->author" size="sm" class="ring-2 ring-white/25" />
                                {{ $post->author->name }}
                            </span>

                            <span class="text-white/35" aria-hidden="true">&bull;</span>

                            <span>
                                {{ $post->isPublished()
                                    ? __('Published :date', ['date' => $post->published_at->format('d F Y')])
                                    : __('Last updated :date', ['date' => $post->updated_at->format('d F Y')]) }}
                            </span>
                        </div>
                    </div>
                </header>

                <div class="px-6 py-8 sm:px-10 sm:py-10">
                    <div class="text-base leading-8 whitespace-pre-line text-slate-700 sm:text-[1.05rem] sm:leading-9">{{ trim($post->body) }}</div>
                </div>
            </article>

            <aside class="space-y-5">
                @if ($post->isEvent())
                    <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-900/5 its-card-shadow sm:p-6">
                        <div class="flex items-center gap-3">
                            <span class="flex size-10 items-center justify-center rounded-xl bg-brand-soft text-brand">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M4.5 5.25h15A.75.75 0 0 1 20.25 6v13.5a.75.75 0 0 1-.75.75h-15a.75.75 0 0 1-.75-.75V6a.75.75 0 0 1 .75-.75Z" />
                                </svg>
                            </span>

                            <h2 class="text-sm font-bold tracking-[0.12em] text-slate-500 uppercase">
                                {{ __('Event details') }}
                            </h2>
                        </div>

                        <dl class="mt-5 space-y-4 text-sm">
                            @if ($post->event_starts_at)
                                <div>
                                    <dt class="text-xs font-semibold tracking-wide text-slate-400 uppercase">{{ __('Starts') }}</dt>
                                    <dd class="mt-1 font-semibold text-slate-900">
                                        {{ $post->event_starts_at->format('l, d F Y, H:i') }}
                                    </dd>
                                </div>
                            @endif

                            @if ($post->event_ends_at)
                                <div>
                                    <dt class="text-xs font-semibold tracking-wide text-slate-400 uppercase">{{ __('Ends') }}</dt>
                                    <dd class="mt-1 font-semibold text-slate-900">
                                        {{ $post->event_ends_at->format('l, d F Y, H:i') }}
                                    </dd>
                                </div>
                            @endif

                            @if ($post->event_location)
                                <div>
                                    <dt class="text-xs font-semibold tracking-wide text-slate-400 uppercase">{{ __('Location') }}</dt>
                                    <dd class="mt-1 font-semibold text-slate-900">
                                        {{ $post->event_location }}
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                @endif

                <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-900/5 its-card-shadow sm:p-6">
                    <h2 class="text-sm font-bold tracking-[0.12em] text-slate-500 uppercase">
                        {{ __('Author') }}
                    </h2>

                    <div class="mt-4 flex items-center gap-3">
                        <x-user-avatar :user="$post->author" size="lg" class="ring-brand/10" />

                        <div class="min-w-0">
                            <p class="truncate font-semibold text-slate-900">
                                {{ $post->author->name }}
                            </p>
                            <p class="truncate text-sm text-slate-500">
                                {{ $post->author->email }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="mt-5 grid grid-cols-2 gap-3 border-t border-slate-100 pt-4 text-sm"
                        x-data="{ bookmarks: {{ $post->bookmarks_count }} }"
                        @bookmark-toggled.window="if ($event.detail.postId == {{ $post->id }}) bookmarks = $event.detail.count"
                    >
                        <div>
                            <p class="text-xs font-semibold tracking-wide text-slate-400 uppercase">{{ __('Comments') }}</p>
                            <p class="mt-1 text-xl font-bold text-slate-900">
                                {{ $post->comments_count }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold tracking-wide text-slate-400 uppercase">{{ __('Bookmarks') }}</p>
                            <p class="mt-1 text-xl font-bold text-slate-900" x-text="bookmarks">{{ $post->bookmarks_count }}</p>
                        </div>
                    </div>

                    @if ($post->isPublished())
                        <div class="mt-5 border-t border-slate-100 pt-4">
                            @auth
                                <livewire:bookmark-button
                                    :post="$post"
                                    :bookmarked="$isBookmarked"
                                    variant="full"
                                    :count="$post->bookmarks_count"
                                />
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-semibold text-brand hover:text-brand-dark">
                                    {{ __('Log in to bookmark') }}
                                </a>
                            @endauth
                        </div>
                    @endif

                    @can('update', $post)
                        <div class="mt-4 flex gap-2 border-t border-slate-100 pt-4">
                            <a
                                href="{{ route('posts.edit', $post) }}"
                                class="flex-1 rounded-full border border-slate-200 px-3 py-2 text-center text-sm font-semibold text-slate-600 transition hover:bg-brand-soft hover:text-brand"
                            >
                                {{ __('Edit') }}
                            </a>

                            <form
                                method="POST"
                                action="{{ route('posts.destroy', $post) }}"
                                class="flex-1"
                                onsubmit="return confirm('{{ __('Delete this post? This cannot be undone.') }}')"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="w-full rounded-full border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50"
                                >
                                    {{ __('Delete') }}
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>
            </aside>
        </div>

        @if ($post->isPublished())
            <section id="comments" class="mt-8 scroll-mt-28 rounded-3xl bg-white p-6 ring-1 ring-slate-900/5 its-card-shadow sm:p-9" aria-labelledby="comments-heading">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 id="comments-heading" class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">
                            {{ trans_choice(':count comment|:count comments', $post->comments_count, ['count' => $post->comments_count]) }}
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">{{ __('Join the conversation or reply to a thread.') }}</p>
                    </div>

                    <span class="rounded-full bg-brand-soft px-3.5 py-1.5 text-xs font-bold tracking-wide text-brand uppercase">
                        {{ __('Discussion') }}
                    </span>
                </div>

                <div x-data="{ replying: {{ old('parent_id') ? (int) old('parent_id') : 'null' }} }">
                    @if ($errors->has('body') && ! old('parent_id'))
                        <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ $errors->first('body') }}
                        </div>
                    @endif

                    @if ($errors->has('parent_id'))
                        <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ $errors->first('parent_id') }}
                        </div>
                    @endif

                    @if ($comments->isEmpty())
                        <p class="mt-4 rounded-2xl bg-slate-50 px-4 py-5 text-sm text-slate-500">
                            {{ __('No comments yet. Start the conversation.') }}
                        </p>
                    @else
                        <ul class="mt-5">
                            @foreach ($comments as $comment)
                                <li class="border-b border-slate-100 py-5 first:pt-0">
                                    <x-comment :post="$post" :comment="$comment" />

                                    @if ($comment->replies->isNotEmpty())
                                        <ul class="mt-3 space-y-3 border-l-2 border-brand-soft pl-4 sm:ml-5 sm:pl-6">
                                            @foreach ($comment->replies as $reply)
                                                <li>
                                                    <x-comment :post="$post" :comment="$reply" :is-reply="true" />
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @auth
                        <form method="POST" action="{{ route('comments.store', $post) }}" class="mt-7 border-t border-slate-100 pt-6">
                            @csrf

                            <label for="comment-body" class="block text-sm font-semibold text-slate-700">
                                {{ __('Add a comment') }}
                            </label>

                            <textarea
                                id="comment-body"
                                name="body"
                                rows="3"
                                required
                                maxlength="2000"
                                class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand/20 focus:outline-none"
                                placeholder="{{ __('Share your thoughts or ask a question') }}"
                            >{{ old('parent_id') ? '' : old('body') }}</textarea>

                            @if (! old('parent_id'))
                                @error('body')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            @endif

                            <button
                                type="submit"
                                class="mt-3 inline-flex items-center justify-center rounded-full bg-brand px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-dark"
                            >
                                {{ __('Post comment') }}
                            </button>
                        </form>
                    @else
                        <p class="mt-5 border-t border-slate-100 pt-5 text-sm text-slate-500">
                            <a href="{{ route('login') }}" class="font-semibold text-brand hover:text-brand-dark">{{ __('Log in') }}</a>
                            {{ __('to join the conversation.') }}
                        </p>
                    @endauth
                </div>
            </section>
        @else
            <section class="mt-8 rounded-3xl bg-white p-6 text-sm text-slate-500 ring-1 ring-slate-900/5 its-card-shadow sm:p-8">
                {{ __('Comments are available once this post is published.') }}
            </section>
        @endif
    </div>
</x-layouts::bulletin>
