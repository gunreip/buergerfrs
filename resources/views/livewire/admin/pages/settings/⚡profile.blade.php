<?php

use App\Concerns\ProfileValidationRules;
use App\Support\Avatar\UserAvatarStorage;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Profile settings')] class extends Component {
    use ProfileValidationRules;
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $nickname = '';

    public $avatarUpload = null;
    public ?string $avatarPath = null;
    public bool $removeAvatar = false;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->nickname = (string) Auth::user()->setting('profile.nickname', '');
        $this->avatarPath = (string) Auth::user()->setting('profile.avatar_path', '');
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

        $this->validate([
            'nickname' => ['nullable', 'string', 'max:80'],
            'avatarUpload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $avatarStorage = app(UserAvatarStorage::class);

        $user->fill($validated);
        $user->setSetting('profile.nickname', trim($this->nickname));

        if ($this->removeAvatar) {
            $avatarStorage->deleteAvatarPath($this->avatarPath);

            $this->avatarPath = '';
            $user->setSetting('profile.avatar_path', null);
        }

        if ($this->avatarUpload !== null) {
            try {
                $this->avatarPath = $avatarStorage->storeUploadedAvatar($user, $this->avatarUpload);
            } catch (\Throwable $exception) {
                report($exception);

                Flux::toast(heading: __('Avatar could not be saved'), text: __('pages.settings.profile.please_check_the_storage_permissions_for_storage_app_public_avatars'), variant: 'danger', duration: 6000);
                // Flux::toast(heading: __('Avatar could not be saved'), text: __('pages.settings.profile.please_check_the_storage_permissions_for_storage_app_public_avatars'), variant: 'danger', duration: 6000);

                return;
            }

            $user->setSetting('profile.avatar_path', $this->avatarPath);

            $this->avatarUpload = null;
            $this->removeAvatar = false;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        Flux::toast(variant: 'success', text: __('Profile updated.'));
    }

    public function updatedAvatarUpload(): void
    {
        $this->validate([
            'avatarUpload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $this->removeAvatar = false;
    }

    public function markAvatarForRemoval(): void
    {
        $this->avatarUpload = null;
        $this->removeAvatar = true;
    }

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

        Flux::toast(text: __('A new verification link has been sent to your email address.'));
    }

    #[Computed]
    public function avatarUrl(): ?string
    {
        return app(UserAvatarStorage::class)->url($this->avatarPath);
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && !Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return !Auth::user() instanceof MustVerifyEmail || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-pages::settings.layout
        :heading="__('Profile')"
        :subheading="__('Update your name and email address')"
    >
        <form
            class="my-6 w-full space-y-6"
            wire:submit="updateProfileInformation"
        >

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(28rem,36rem)_minmax(34rem,38rem)]">
                {{-- Profile fields --}}
                <div class="space-y-6">
                    <div>
                        <flux:label for="name">
                            {{ __('ui.labels.name') }}
                        </flux:label>

                        <flux:input.group>
                            <flux:input.group.prefix>
                                <flux:icon.user class="h-5 w-5 text-gray-400" />
                            </flux:input.group.prefix>

                            <flux:input
                                id="name"
                                name="name"
                                type="text"
                                wire:model="name"
                                required
                                clearable
                                autofocus
                                autocomplete="name"
                            />
                        </flux:input.group>
                    </div>

                    <div>
                        <flux:label for="nickname">
                            {{ __('ui.names.nickname') }}
                        </flux:label>

                        <flux:input.group>
                            <flux:input.group.prefix>
                                <flux:icon.party-popper class="h-5 w-5 text-gray-400" />
                            </flux:input.group.prefix>

                            <flux:input
                                id="nickname"
                                name="nickname"
                                type="text"
                                wire:model="nickname"
                                clearable
                                autocomplete="nickname"
                                placeholder="{{ __('Optional display name') }}"
                            />
                        </flux:input.group>
                    </div>

                    <div>
                        <flux:label for="email">
                            {{ __('Email') }}
                        </flux:label>

                        <flux:input.group>
                            <flux:input.group.prefix>
                                <flux:icon.envelope class="h-5 w-5 text-gray-400" />
                            </flux:input.group.prefix>

                            <flux:input
                                id="email"
                                name="email"
                                type="email"
                                wire:model="email"
                                required
                                clearable
                                autocomplete="email"
                            />
                        </flux:input.group>

                        @if ($this->hasUnverifiedEmail)
                            <div>
                                <flux:text class="mt-4">
                                    {{ __('Your email address is unverified.') }}

                                    <flux:link
                                        class="cursor-pointer text-sm"
                                        wire:click.prevent="resendVerificationNotification"
                                    >
                                        {{ __('Click here to re-send the verification email.') }}
                                    </flux:link>
                                </flux:text>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-4">
                        <x-ui.button.save
                            data-test="update-profile-button"
                            type="submit"
                            label="{{ __('ui.save') }}"
                            {{-- icon="save" --}}
                            {{-- color="green" --}}
                        />
                        {{-- <flux:button
                            data-test="update-profile-button"
                            type="submit"
                            variant="primary"
                            color="green"
                            icon="save"
                        >
                            {{ __('ui.save') }}
                        </flux:button> --}}
                    </div>
                </div>

                {{-- Avatar --}}
                <flux:card class="space-y-4">
                    <div>
                        <flux:heading size="md">
                            {{ __('Avatar') }}
                        </flux:heading>

                        <flux:text class="mt-1 text-sm">
                            {{ __('Upload a personal profile avatar. JPG, PNG or WEBP up to 2 MB.') }}
                        </flux:text>
                    </div>

                    <div class="flex items-center gap-4">
                        @if ($avatarUpload)
                            <img
                                class="h-24 w-24 rounded-2xl object-cover ring-1 ring-zinc-700"
                                src="{{ $avatarUpload->temporaryUrl() }}"
                                alt="{{ __('Avatar preview') }}"
                            >
                        @elseif ($this->avatarUrl)
                            <img
                                class="h-24 w-24 rounded-2xl object-cover ring-1 ring-zinc-700"
                                src="{{ $this->avatarUrl }}"
                                alt="{{ __('pages.settings.profile.current_avatar') }}"
                            >
                        @else
                            <flux:avatar
                                class="size-24"
                                :name="Auth::user()->name"
                                :initials="Auth::user()->initials()"
                            />
                        @endif

                        <div class="min-w-0">
                            <flux:heading
                                class="truncate"
                                size="sm"
                            >
                                {{ Auth::user()->name }}
                            </flux:heading>

                            <flux:text class="truncate text-sm">
                                {{ Auth::user()->email }}
                            </flux:text>

                            @if ($avatarPath)
                                <flux:text class="mt-1 font-mono text-xs text-zinc-500">
                                    {{ $avatarPath }}
                                </flux:text>
                            @endif
                        </div>
                    </div>

                    <flux:input
                        type="file"
                        wire:model="avatarUpload"
                        accept="image/jpeg,image/png,image/webp"
                        label="{{ __('pages.settings.profile.avatar_image') }}"
                    />

                    @error('avatarUpload')
                        <flux:text class="text-sm text-red-400">
                            {{ $message }}
                        </flux:text>
                    @enderror

                    <div class="flex items-center gap-3">
                        <flux:button
                            type="button"
                            variant="ghost"
                            icon="trash"
                            wire:click="markAvatarForRemoval"
                            :disabled="$avatarPath === null || $avatarPath === ''"
                        >
                            {{ __('Remove avatar') }}
                        </flux:button>

                        @if ($removeAvatar)
                            <flux:badge
                                color="orange"
                                variant="subtle"
                            >
                                {{ __('Will be removed on save') }}
                            </flux:badge>
                        @endif
                    </div>
                </flux:card>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:pages::settings.delete-user-form />
        @endif
    </x-pages::settings.layout>
</section>
