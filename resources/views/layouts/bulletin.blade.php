@php($compact = $compact ?? false)

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')

        <script>
            document.documentElement.classList.remove('dark');
            document.documentElement.style.colorScheme = 'light';
        </script>
    </head>
    <body id="top" class="flex min-h-screen flex-col bg-[#f5f7fc] text-slate-900 antialiased">
        @if ($compact)
            <header class="its-hero sticky top-0 z-50 overflow-hidden border-b border-white/10 shadow-[0_18px_45px_-28px_rgba(2,6,23,0.9)]">
                <div class="its-blueprint pointer-events-none absolute inset-0 opacity-60"></div>

                <x-its-mark class="pointer-events-none absolute -right-12 -bottom-20 w-56 opacity-[0.09] sm:-right-8 sm:w-72" />

                <x-bulletin-navbar />
            </header>
        @else
            <header class="its-hero relative overflow-hidden pb-28 pt-6 sm:pb-32 sm:pt-8">
                <div class="its-blueprint pointer-events-none absolute inset-0 opacity-70"></div>

                <x-its-mark class="pointer-events-none absolute -right-28 -bottom-28 w-[26rem] opacity-[0.07] sm:-right-12 sm:-bottom-40 sm:w-[42rem]" />

                <div class="relative mx-auto max-w-[1320px] px-4 sm:px-6 lg:px-8">
                    <x-bulletin-navbar :brand="false" :padded="false" />

                    <div class="mt-10 text-center sm:mt-12 lg:mt-14">
                        <a href="{{ route('home') }}" class="inline-block text-white">
                            <span class="block text-[2.45rem] leading-[0.95] font-extrabold tracking-[-0.065em] sm:text-7xl lg:text-[6.75rem]">
                                myITS <span class="font-bold">Broadcast</span>
                            </span>
                        </a>

                        <p class="mt-2 text-lg font-medium tracking-tight text-white/95 italic sm:mt-3 sm:text-2xl">
                            {{ __('Stay Connected, Stay Ahead') }}
                        </p>
                    </div>
                </div>
            </header>

            <div
                x-data="{ scrolled: false }"
                x-init="scrolled = window.scrollY > 180"
                @scroll.window.passive="scrolled = window.scrollY > 180"
                x-show="scrolled"
                x-transition:enter="transition duration-300 ease-out"
                x-transition:enter-start="-translate-y-full opacity-0"
                x-transition:enter-end="translate-y-0 opacity-100"
                x-transition:leave="transition duration-200 ease-in"
                x-transition:leave-start="translate-y-0 opacity-100"
                x-transition:leave-end="-translate-y-full opacity-0"
                class="fixed inset-x-0 top-0 z-50"
                style="display: none"
            >
                <header class="its-hero relative overflow-hidden border-b border-white/10 shadow-[0_18px_45px_-20px_rgba(2,6,23,0.75)]">
                    <div class="its-blueprint pointer-events-none absolute inset-0 opacity-55"></div>

                    <x-its-mark class="pointer-events-none absolute -right-10 -bottom-16 w-52 opacity-[0.09]" />

                    <x-bulletin-navbar />
                </header>
            </div>
        @endif

        <main @class([
            'mx-auto w-full max-w-[1320px] flex-1 px-4 sm:px-6 lg:px-8',
            'py-8 lg:py-10' => $compact,
            'pb-16' => ! $compact,
        ])>
            <x-flash-message />

            {{ $slot }}
        </main>

        <footer class="its-hero relative overflow-hidden">
            <div class="its-blueprint pointer-events-none absolute inset-0 opacity-40"></div>
            <x-its-mark class="pointer-events-none absolute -right-16 -bottom-24 w-72 opacity-[0.12] sm:w-80" />

            <div class="relative mx-auto max-w-[1320px] px-4 py-9 text-center text-white sm:py-10">
                <p class="text-2xl font-extrabold tracking-[-0.045em] sm:text-3xl">
                    myITS <span class="font-semibold">Broadcast</span>
                </p>
                <p class="mt-1 text-xs font-medium tracking-wide text-white/65 sm:text-sm">
                    {{ __('Campus bulletin for ITS') }}
                </p>

                <nav class="mt-5 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-xs font-semibold text-white/70" aria-label="{{ __('Site and account links') }}">
                    <a href="{{ route('home') }}" class="transition hover:text-white">{{ __('Feed') }}</a>

                    @auth
                        <a href="{{ route('dashboard') }}" class="transition hover:text-white">{{ __('My Broadcast') }}</a>
                        <a href="{{ route('profile.edit') }}" class="transition hover:text-white">{{ __('My Profile') }}</a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button type="submit" class="transition hover:text-white">{{ __('Log out') }}</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="transition hover:text-white">{{ __('Log in') }}</a>
                        <a href="{{ route('register') }}" class="transition hover:text-white">{{ __('Register') }}</a>
                    @endauth
                </nav>
            </div>
        </footer>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
