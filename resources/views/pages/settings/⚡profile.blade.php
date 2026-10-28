<?php

use App\Concerns\ProfileValidationRules;
/* @chisel-email-verification */
use Illuminate\Contracts\Auth\MustVerifyEmail;
/* @end-chisel-email-verification */
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new #[Title('Profile settings')] class extends Component {
    use ProfileValidationRules, WithFileUploads;

    public string $name = '';
    public string $email = '';
    public ?TemporaryUploadedFile $photo = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            ...$this->profileRules($user->id),
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        unset($validated['photo']);

        $user->fill($validated);

        if ($this->photo !== null) {
            if (filled($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $user->avatar_path = $this->photo->store('avatars', 'public');
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->reset('photo');

        Flux::toast(variant: 'success', text: __('Profile updated.'));
    }

    /**
     * Remove the user's uploaded profile photo.
     */
    public function removeAvatar(): void
    {
        $user = Auth::user();

        if (filled($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->update(['avatar_path' => null]);

        Flux::toast(variant: 'success', text: __('Profile photo removed.'));
    }

    /* @chisel-email-verification */
    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
    /* @end-chisel-email-verification */
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your name, email address, and profile photo.')">
        <x-slot:aside>
            <div class="rounded-2xl bg-brand-soft p-5 ring-1 ring-inset ring-brand/10">
                <span class="flex size-9 items-center justify-center rounded-xl bg-white text-brand shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 2a4 4 0 1 0 0 8 4 4 0 0 0 0-8ZM4 14.5A4.5 4.5 0 0 1 8.5 10h3a4.5 4.5 0 0 1 4.5 4.5v.5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-.5Z" clip-rule="evenodd" />
                    </svg>
                </span>

                <p class="mt-3 text-sm font-bold text-brand-deep">{{ __('Profile tips') }}</p>

                <ul class="mt-2 space-y-2.5 text-xs leading-5 text-slate-600">
                    <li>{{ __('A clear, square photo helps classmates recognize you across the feed and comments.') }}</li>
                    <li>{{ __('Use the name your classmates know you by.') }}</li>
                    <li>{{ __('Your email is used to sign in and receive updates.') }}</li>
                </ul>
            </div>
        </x-slot:aside>

        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <div class="flex flex-wrap items-center gap-5">
                <div class="relative">
                    @if ($photo)
                        <img src="{{ $photo->temporaryUrl() }}" alt="{{ __('New profile photo preview') }}" class="size-24 rounded-full object-cover ring-4 ring-brand-soft">
                    @else
                        <x-user-avatar :user="auth()->user()" size="xl" class="ring-4 ring-brand-soft" />
                    @endif
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-slate-700">{{ __('Profile photo') }}</p>

                    <input
                        id="avatar"
                        type="file"
                        wire:model="photo"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        class="peer sr-only"
                    >

                    <div class="mt-2 flex flex-wrap items-center gap-3">
                        <label
                            for="avatar"
                            class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-brand-soft px-4 py-2 text-sm font-semibold text-brand ring-1 ring-inset ring-brand/10 transition hover:bg-brand/10 peer-focus-visible:ring-2 peer-focus-visible:ring-brand/40"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4" aria-hidden="true">
                                <path d="M9.25 13.25a.75.75 0 0 0 1.5 0V4.636l2.955 3.129a.75.75 0 0 0 1.09-1.03l-4.25-4.5a.75.75 0 0 0-1.09 0l-4.25 4.5a.75.75 0 1 0 1.09 1.03L9.25 4.636v8.614Z" />
                                <path d="M3.5 12.75a.75.75 0 0 0-1.5 0v2.5A2.75 2.75 0 0 0 4.75 18h10.5A2.75 2.75 0 0 0 18 15.25v-2.5a.75.75 0 0 0-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5Z" />
                            </svg>
                            {{ $photo ? __('Choose a different photo') : __('Upload new photo') }}
                        </label>

                        @if (auth()->user()->avatar_path)
                            <button
                                type="button"
                                wire:click="removeAvatar"
                                wire:confirm="{{ __('Remove your profile photo?') }}"
                                class="text-xs font-semibold text-red-600 transition hover:text-red-700"
                            >
                                {{ __('Remove photo') }}
                            </button>
                        @endif
                    </div>

                    <p class="mt-2 text-xs text-slate-500">{{ __('A square JPG, PNG or WebP image works best. Maximum 2 MB.') }}</p>

                    <div wire:loading wire:target="photo" class="mt-1 text-xs font-semibold text-brand">
                        {{ __('Uploading photo...') }}
                    </div>

                    @error('photo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                {{-- @chisel-email-verification --}}
                @if ($this->hasUnverifiedEmail)
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="cursor-pointer text-sm" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
                {{-- @end-chisel-email-verification --}}
            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit" data-test="update-profile-button">
                    {{ __('Save profile') }}
                </flux:button>
            </div>
        </form>

        {{-- @chisel-email-verification --}}
        @if ($this->showDeleteUser)
        {{-- @end-chisel-email-verification --}}
            <livewire:pages::settings.delete-user-form />
        {{-- @chisel-email-verification --}}
        @endif
        {{-- @end-chisel-email-verification --}}
    </x-pages::settings.layout>
</section>
