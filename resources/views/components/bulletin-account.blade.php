@auth
    @php($user = auth()->user())

    <!--
    <form method="POST" action="{{ route('logout') }}" class="flex">
        @csrf

        <button
            type="submit"
            class="inline-flex size-9 items-center justify-center rounded-full text-white/80 transition hover:bg-white/10 hover:text-white sm:size-10"
            aria-label="{{ __('Log out') }}"
            title="{{ __('Log out') }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="size-4 sm:size-[1.15rem]" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
            </svg>
        </button>
    </form>
    -->

    <a
        href="{{ route('posts.create') }}"
        @class([
            'inline-flex size-9 items-center justify-center rounded-full text-lg font-semibold transition sm:size-10',
            'bg-white text-brand shadow-sm' => request()->routeIs('posts.create'),
            'bg-white/10 text-white ring-1 ring-white/25 hover:bg-white/20' => ! request()->routeIs('posts.create'),
        ])
        aria-label="{{ __('Create a broadcast') }}"
        title="{{ __('Create a broadcast') }}"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4 sm:size-[1.15rem]" aria-hidden="true">
            <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
        </svg>
    </a>

    <a
        href="{{ route('profile.edit') }}"
        @class([
            'inline-flex items-center gap-2 rounded-full py-1 pr-1 pl-1 text-sm font-semibold text-white ring-1 transition sm:pr-3',
            'bg-white/20 ring-white/40' => request()->routeIs('profile.edit', 'security.edit'),
            'bg-white/10 ring-white/20 hover:bg-white/15' => ! request()->routeIs('profile.edit', 'security.edit'),
        ])
        @if (request()->routeIs('profile.edit', 'security.edit')) aria-current="page" @endif
    >
        <x-user-avatar :user="$user" size="sm" class="ring-2 ring-white/30" />
        <span class="hidden sm:inline">{{ __('My Profile') }}</span>
        <span class="sr-only sm:hidden">{{ __('My Profile') }}</span>
    </a>
@else
    <a
        href="{{ route('login') }}"
        class="inline-flex items-center justify-center rounded-full px-4 py-2 text-sm font-semibold text-white/85 transition hover:bg-white/10 hover:text-white"
    >
        {{ __('Log in') }}
    </a>

    <a
        href="{{ route('register') }}"
        class="inline-flex items-center justify-center rounded-full bg-white px-4 py-2 text-sm font-semibold text-brand shadow-sm transition hover:bg-blue-50"
    >
        {{ __('Register') }}
    </a>
@endauth
