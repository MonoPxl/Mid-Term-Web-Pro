<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')

        <script>
            document.documentElement.classList.remove('dark');
            document.documentElement.style.colorScheme = 'light';
        </script>
    </head>
    <body class="min-h-screen bg-[#f5f7fc] text-slate-900 antialiased">
        <div class="relative flex min-h-svh flex-col items-center justify-center overflow-hidden px-6 py-10">
            <div class="its-hero absolute inset-x-0 top-0 h-[360px]">
                <div class="its-blueprint pointer-events-none absolute inset-0 opacity-70"></div>
                <x-its-mark class="pointer-events-none absolute -right-20 -bottom-24 w-[24rem] opacity-[0.12] sm:w-[30rem]" />
            </div>

            <div class="relative z-10 flex w-full max-w-md flex-col gap-6">
                <a href="{{ route('home') }}" class="flex flex-col items-center text-white" wire:navigate>
                    <span class="text-3xl font-extrabold tracking-[-0.045em] sm:text-4xl">
                        myITS <span class="font-bold">Broadcast</span>
                    </span>
                    <span class="mt-1 text-sm font-medium text-white/80 italic">
                        {{ __('Stay Connected, Stay Ahead') }}
                    </span>
                </a>

                <div class="rounded-3xl bg-white p-6 shadow-[0_20px_60px_rgba(15,23,42,0.14)] ring-1 ring-slate-900/5 sm:p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
