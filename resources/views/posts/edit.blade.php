<x-layouts::bulletin :title="__('Edit post')" :compact="true">
    <div class="mx-auto w-full max-w-3xl">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
                    {{ __('Edit post') }}
                </h1>

                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Update your post, save it as a draft, or publish the latest version.') }}
                </p>
            </div>

            <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-semibold tracking-wide text-zinc-600 uppercase dark:bg-zinc-800 dark:text-zinc-300">
                {{ $post->status->label() }}
            </span>
        </div>

        <form method="POST" action="{{ route('posts.update', $post) }}" enctype="multipart/form-data" class="mt-6 space-y-6 rounded-3xl bg-white p-6 ring-1 ring-slate-900/5 its-card-shadow sm:p-8">
            @csrf
            @method('PUT')

            @include('posts.partials.form', ['post' => $post])
        </form>

        <div class="mt-6 rounded-3xl border border-red-200 bg-white p-6 ring-1 ring-red-100 sm:p-8">
            <h2 class="text-lg font-semibold text-slate-900">
                {{ __('Delete post') }}
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                {{ __('Deleting a post also removes its comments and bookmarks. This action cannot be undone.') }}
            </p>

            <form
                method="POST"
                action="{{ route('posts.destroy', $post) }}"
                class="mt-4"
                onsubmit="return confirm('{{ __('Delete this post? This cannot be undone.') }}')"
            >
                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="rounded-full border border-red-200 px-5 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-50"
                >
                    {{ __('Delete this post') }}
                </button>
            </form>
        </div>
    </div>
</x-layouts::bulletin>
