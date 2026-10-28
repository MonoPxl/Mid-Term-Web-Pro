@props(['heading' => null, 'subheading' => null])

<div class="mt-6 flex items-start gap-6 max-md:flex-col">
    <aside class="w-full md:w-52 md:shrink-0">
        <nav class="rounded-2xl bg-white p-2 ring-1 ring-slate-900/5 its-card-shadow" aria-label="{{ __('Settings') }}">
            <a
                href="{{ route('profile.edit') }}"
                @class([
                    'flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold transition',
                    'bg-brand text-white shadow-sm' => request()->routeIs('profile.edit'),
                    'text-slate-600 hover:bg-brand-soft hover:text-brand' => ! request()->routeIs('profile.edit'),
                ])
                @if (request()->routeIs('profile.edit')) aria-current="page" @endif
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 2a4 4 0 1 0 0 8 4 4 0 0 0 0-8ZM4 14.5A4.5 4.5 0 0 1 8.5 10h3a4.5 4.5 0 0 1 4.5 4.5v.5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-.5Z" clip-rule="evenodd" />
                </svg>
                {{ __('Profile') }}
            </a>

            <a
                href="{{ route('security.edit') }}"
                @class([
                    'mt-1 flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold transition',
                    'bg-brand text-white shadow-sm' => request()->routeIs('security.edit'),
                    'text-slate-600 hover:bg-brand-soft hover:text-brand' => ! request()->routeIs('security.edit'),
                ])
                @if (request()->routeIs('security.edit')) aria-current="page" @endif
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd" />
                </svg>
                {{ __('Security') }}
            </a>
        </nav>

        <div class="mt-4 rounded-2xl bg-brand-soft p-4 ring-1 ring-inset ring-brand/10">
            <p class="text-[0.65rem] font-bold tracking-[0.18em] text-brand uppercase">{{ __('Signed in as') }}</p>
            <p class="mt-1 truncate text-xs text-slate-600">{{ auth()->user()->email }}</p>
        </div>
    </aside>

    <div class="min-w-0 flex-1">
        <div class="rounded-3xl bg-white p-6 ring-1 ring-slate-900/5 its-card-shadow sm:p-8">
            <p class="text-xs font-bold tracking-[0.18em] text-brand uppercase">{{ __('Account settings') }}</p>
            <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">{{ $heading }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $subheading }}</p>

            <div @class([
                'mt-6 grid gap-8',
                'lg:grid-cols-[minmax(0,1fr)_240px]' => isset($aside),
            ])>
                <div class="w-full max-w-xl">
                    {{ $slot }}
                </div>

                @isset($aside)
                    <aside class="space-y-4">
                        {{ $aside }}
                    </aside>
                @endisset
            </div>
        </div>
    </div>
</div>
