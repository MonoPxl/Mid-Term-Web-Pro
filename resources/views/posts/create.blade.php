<x-layouts::bulletin :title="__('Create post')" :compact="true">
    <div class="mx-auto w-full max-w-3xl">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
                {{ __('Create a post') }}
            </h1>

            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('Share an announcement or event with the ITS community. Save it as a draft first if you are not ready to publish.') }}
            </p>
        </div>

        <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" class="mt-6 space-y-6 rounded-3xl bg-white p-6 ring-1 ring-slate-900/5 its-card-shadow sm:p-8">
            @csrf

            @include('posts.partials.form', ['post' => null])
        </form>
    </div>
</x-layouts::bulletin>
