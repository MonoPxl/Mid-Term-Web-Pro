@php
    $post = $post ?? null;
    $selectedType = old('type', $post?->type->value ?? \App\Enums\PostType::Announcement->value);
    $selectedCategory = old('category_id', $post?->category_id);
    $hasCover = filled($post?->cover_image_path);
@endphp

<div>
    <label for="title" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
        {{ __('Title') }}
    </label>

    <input
        id="title"
        name="title"
        type="text"
        value="{{ old('title', $post?->title) }}"
        required
        maxlength="180"
        class="mt-1 block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"
    >

    @error('title')
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>

<div class="grid gap-6 sm:grid-cols-2">
    <div>
        <label for="category_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
            {{ __('Category') }}
        </label>

        <select
            id="category_id"
            name="category_id"
            required
            class="mt-1 block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"
        >
            <option value="">{{ __('Choose a category') }}</option>

            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) $selectedCategory === (string) $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        @error('category_id')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="type" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
            {{ __('Post type') }}
        </label>

        <select
            id="type"
            name="type"
            required
            class="mt-1 block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"
        >
            @foreach (\App\Enums\PostType::cases() as $postType)
                <option value="{{ $postType->value }}" @selected($selectedType === $postType->value)>
                    {{ $postType->label() }}
                </option>
            @endforeach
        </select>

        @error('type')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>

<div>
    <label for="body" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
        {{ __('Content') }}
    </label>

    <textarea
        id="body"
        name="body"
        rows="12"
        required
        maxlength="20000"
        class="mt-1 block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm leading-6 text-zinc-900 shadow-sm focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"
        placeholder="{{ __('Write the announcement or event details here...') }}"
    >{{ old('body', $post?->body) }}</textarea>

    @error('body')
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>

<div
    x-data="{
        preview: null,
        remove: @js((bool) old('remove_cover_image')),
        pick(event) {
            const file = event.target.files?.[0];

            if (!file) {
                this.preview = null;

                return;
            }

            if (this.preview) URL.revokeObjectURL(this.preview);

            this.preview = URL.createObjectURL(file);
            this.remove = false;
        },
        clearSelection() {
            if (this.preview) URL.revokeObjectURL(this.preview);

            this.preview = null;
            this.$refs.cover.value = '';
        },
        removeExisting() {
            this.clearSelection();
            this.remove = true;
        },
    }"
    class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-800"
>
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ __('Cover image') }}</p>
            <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                {{ __('Optional. Shown at the top of your broadcast and as its preview in the feed.') }}
            </p>
        </div>

        @if ($hasCover)
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    x-show="! preview && ! remove"
                    @click="removeExisting()"
                    class="text-xs font-semibold text-red-600 transition hover:text-red-700"
                >
                    {{ __('Remove image') }}
                </button>

                <button
                    type="button"
                    x-show="remove"
                    @click="remove = false"
                    class="text-xs font-semibold text-brand transition hover:text-brand-dark"
                >
                    {{ __('Keep current image') }}
                </button>
            </div>
        @endif
    </div>

    <div class="mt-4 grid gap-4 sm:grid-cols-[minmax(0,16rem)_minmax(0,1fr)] sm:items-center">
        <div class="relative aspect-[2.15/1] overflow-hidden rounded-xl bg-brand-soft ring-1 ring-slate-900/5">
            <img
                x-show="preview"
                :src="preview"
                alt="{{ __('Selected cover preview') }}"
                class="absolute inset-0 h-full w-full object-cover"
                x-cloak
            >

            @if ($hasCover)
                <img
                    x-show="! preview && ! remove"
                    src="{{ $post->coverImageUrl() }}"
                    alt="{{ __('Current cover image') }}"
                    class="absolute inset-0 h-full w-full object-cover"
                >
            @endif

            <div
                x-show="! preview && ({{ $hasCover ? 'false' : 'true' }} || remove)"
                class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-brand/70"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="size-7" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 19.5h16.5A1.5 1.5 0 0 0 21.75 18V6A1.5 1.5 0 0 0 20.25 4.5H3.75A1.5 1.5 0 0 0 2.25 6v12A1.5 1.5 0 0 0 3.75 19.5Zm10.5-11.25h.008v.008h-.008V8.25Z" />
                </svg>
                <span class="text-xs font-semibold">{{ __('No cover image') }}</span>
            </div>
        </div>

        <div>
            <label for="cover_image" class="sr-only">{{ __('Cover image') }}</label>

            <input
                id="cover_image"
                name="cover_image"
                type="file"
                accept="image/jpeg,image/png,image/webp"
                x-ref="cover"
                @change="pick($event)"
                class="block w-full text-sm text-zinc-500 file:mr-3 file:rounded-full file:border-0 file:bg-brand-soft file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand hover:file:bg-brand/10 dark:text-zinc-400"
            >

            <div class="mt-2 flex flex-wrap items-center gap-3">
                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('JPG, PNG or WebP. Max 2 MB. A 1200 × 560 image works best.') }}</p>

                <button
                    type="button"
                    x-show="preview"
                    @click="clearSelection()"
                    class="text-xs font-semibold text-red-600 transition hover:text-red-700"
                >
                    {{ __('Clear selection') }}
                </button>
            </div>

            @if ($hasCover)
                <input type="hidden" name="remove_cover_image" value="{{ old('remove_cover_image') ? 1 : 0 }}" :value="remove ? 1 : 0">
            @endif

            @error('cover_image')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<fieldset class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-800">
    <legend class="px-1 text-sm font-semibold text-zinc-900 dark:text-white">
        {{ __('Event details') }}
    </legend>

    <p class="mb-4 text-sm text-zinc-500 dark:text-zinc-400">
        {{ __('These fields are required when the post type is Event. They are ignored for announcements.') }}
    </p>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="event_starts_at" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                {{ __('Starts at') }}
            </label>

            <input
                id="event_starts_at"
                name="event_starts_at"
                type="datetime-local"
                value="{{ old('event_starts_at', $post?->event_starts_at?->format('Y-m-d\TH:i')) }}"
                class="mt-1 block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"
            >

            @error('event_starts_at')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="event_ends_at" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                {{ __('Ends at') }}
            </label>

            <input
                id="event_ends_at"
                name="event_ends_at"
                type="datetime-local"
                value="{{ old('event_ends_at', $post?->event_ends_at?->format('Y-m-d\TH:i')) }}"
                class="mt-1 block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"
            >

            @error('event_ends_at')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="mt-4">
        <label for="event_location" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
            {{ __('Location') }}
        </label>

        <input
            id="event_location"
            name="event_location"
            type="text"
            value="{{ old('event_location', $post?->event_location) }}"
            maxlength="255"
            class="mt-1 block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-brand focus:ring-2 focus:ring-brand/20 focus:outline-none dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"
            placeholder="{{ __('e.g. Auditorium Research Center ITS') }}"
        >

        @error('event_location')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
</fieldset>

@error('status')
    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
@enderror

<div class="flex flex-wrap items-center gap-3 border-t border-zinc-200 pt-6 dark:border-zinc-800">
    <button
        type="submit"
        name="status"
        value="{{ \App\Enums\PostStatus::Draft->value }}"
        class="inline-flex items-center justify-center rounded-full border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-brand-soft hover:text-brand"
    >
        {{ __('Save draft') }}
    </button>

    <button
        type="submit"
        name="status"
        value="{{ \App\Enums\PostStatus::Published->value }}"
        class="inline-flex items-center justify-center rounded-full bg-brand px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-dark"
    >
        {{ $post?->isPublished() ? __('Save changes') : __('Publish post') }}
    </button>

    <a
        href="{{ $post ? route('posts.show', $post) : route('dashboard') }}"
        class="text-sm font-medium text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
    >
        {{ __('Cancel') }}
    </a>
</div>
