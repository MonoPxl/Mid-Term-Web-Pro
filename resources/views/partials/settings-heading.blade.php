<div class="relative overflow-hidden rounded-3xl bg-brand-deep p-6 text-white ring-1 ring-white/10 sm:p-8">
    <div class="its-blueprint pointer-events-none absolute inset-0 opacity-40"></div>
    <x-its-mark class="pointer-events-none absolute -right-10 -bottom-20 w-56 opacity-[0.12] sm:w-64" />

    <div class="relative flex flex-wrap items-center justify-between gap-5">
        <div class="flex items-center gap-4">
            <x-user-avatar :user="auth()->user()" size="lg" class="ring-2 ring-white/30" />

            <div>
                <p class="text-xs font-bold tracking-[0.22em] text-amber-300 uppercase">{{ __('Account') }}</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">{{ __('Settings') }}</h1>
                <p class="mt-1 text-sm text-white/70">{{ __('Manage your profile and account security.') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="shrink-0">
            @csrf

            <button
                type="submit"
                class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/20 transition hover:bg-white/15"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-4" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
                {{ __('Log out') }}
            </button>
        </form>
    </div>
</div>
