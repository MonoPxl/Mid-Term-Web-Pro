@props(['post', 'comment', 'isReply' => false])

@php($replyError = (int) old('parent_id') === $comment->id)

<div @class([
    'flex gap-3',
    'rounded-2xl bg-slate-50/90 p-4 ring-1 ring-slate-100' => $isReply,
])>
    <x-user-avatar :user="$comment->user" :size="$isReply ? 'sm' : 'md'" />

    <div class="min-w-0 flex-1">
        <div class="flex flex-wrap items-start justify-between gap-2">
            <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                <span class="text-sm font-semibold text-slate-900">{{ $comment->user->name }}</span>

                @if ($comment->user_id === $post->user_id)
                    <span data-testid="author-badge" class="rounded-full bg-brand-soft px-2 py-0.5 text-[0.6rem] font-bold tracking-[0.12em] text-brand uppercase ring-1 ring-inset ring-brand/10">
                        {{ __('Author') }}
                    </span>
                @endif

                <span class="text-xs text-slate-400">{{ $comment->created_at?->diffForHumans() }}</span>
            </div>

            <div class="flex items-center gap-3">
                @auth
                    <button
                        type="button"
                        @click="replying = (replying === {{ $comment->id }} ? null : {{ $comment->id }})"
                        class="text-xs font-semibold text-slate-500 transition hover:text-brand"
                        :aria-expanded="replying === {{ $comment->id }} ? 'true' : 'false'"
                        aria-controls="reply-form-{{ $comment->id }}"
                    >
                        {{ __('Reply') }}
                    </button>
                @endauth

                @can('delete', $comment)
                    <form
                        method="POST"
                        action="{{ route('comments.destroy', $comment) }}"
                        onsubmit="return confirm('{{ __('Delete this comment?') }}')"
                    >
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="text-xs font-semibold text-red-500 transition hover:text-red-600">
                            {{ __('Delete') }}
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        <p class="mt-1.5 text-sm leading-6 whitespace-pre-line text-slate-600">{{ trim($comment->body) }}</p>

        @auth
            <div id="reply-form-{{ $comment->id }}" x-show="replying === {{ $comment->id }}" x-cloak class="mt-3">
                <form method="POST" action="{{ route('comments.store', $post) }}" class="rounded-2xl bg-white p-3 ring-1 ring-slate-200">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                    <label for="reply-body-{{ $comment->id }}" class="block text-xs font-semibold text-slate-700">
                        {{ __('Reply to :name', ['name' => $comment->user->name]) }}
                    </label>

                    <textarea
                        id="reply-body-{{ $comment->id }}"
                        name="body"
                        rows="2"
                        required
                        maxlength="2000"
                        class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:border-brand focus:bg-white focus:ring-2 focus:ring-brand/20 focus:outline-none"
                        placeholder="{{ __('Write your reply...') }}"
                    >{{ $replyError ? old('body') : '' }}</textarea>

                    @if ($replyError)
                        @error('body')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    @endif

                    <div class="mt-2 flex items-center gap-2">
                        <button type="submit" class="rounded-full bg-brand px-4 py-1.5 text-xs font-semibold text-white transition hover:bg-brand-dark">
                            {{ __('Post reply') }}
                        </button>

                        <button type="button" @click="replying = null" class="rounded-full px-3 py-1.5 text-xs font-semibold text-slate-500 transition hover:bg-slate-100">
                            {{ __('Cancel') }}
                        </button>
                    </div>
                </form>
            </div>
        @endauth
    </div>
</div>
