<?php

use App\Concerns\PasswordValidationRules;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Security settings')] class extends Component {
    use PasswordValidationRules;

    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => $this->currentPasswordRules(),
                'password' => $this->passwordRules(),
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        Flux::toast(variant: 'success', text: __('Password updated.'));
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Security settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Password')" :subheading="__('Use a strong, unique password to keep your account secure.')">
        <x-slot:aside>
            <div class="rounded-2xl bg-brand-soft p-5 ring-1 ring-inset ring-brand/10">
                <span class="flex size-9 items-center justify-center rounded-xl bg-white text-brand shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd" />
                    </svg>
                </span>

                <p class="mt-3 text-sm font-bold text-brand-deep">{{ __('Account protection') }}</p>

                <ul class="mt-2 space-y-2.5 text-xs leading-5 text-slate-600">
                    <li>{{ __('Use at least 12 characters and mix letters, numbers, and symbols.') }}</li>
                    <li>{{ __('Avoid reusing a password from another account.') }}</li>
                    <li>{{ __('Update your password immediately if you suspect it was exposed.') }}</li>
                </ul>

                <p class="mt-4 border-t border-brand/10 pt-3 text-[0.7rem] leading-4 text-slate-500">
                    {{ __('Password confirmation is required before changes are saved.') }}
                </p>
            </div>
        </x-slot:aside>

        <form method="POST" wire:submit="updatePassword" class="space-y-6">
            <flux:input
                wire:model="current_password"
                :label="__('Current password')"
                type="password"
                required
                autocomplete="current-password"
                viewable
            />

            <flux:input
                wire:model="password"
                :label="__('New password')"
                type="password"
                required
                autocomplete="new-password"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <flux:input
                wire:model="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit" data-test="update-password-button">
                    {{ __('Update password') }}
                </flux:button>
            </div>
        </form>
    </x-pages::settings.layout>
</section>
