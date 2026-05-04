<?php

// app/Livewire/Account/Settings.php

namespace App\Livewire\Account;

use App\Models\User;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Settings extends Component
{
    public string $nickname = '';
    public string $locale = 'de';
    public string $appearance = 'system';
    public int $adminUsersPerPage = 50;

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return;
        }

        $this->nickname = (string) $user->setting('profile.nickname', '');
        $this->locale = (string) $user->setting('locale.app', app()->getLocale());
        $this->appearance = (string) $user->setting('ui.appearance', 'system');
        $this->adminUsersPerPage = $this->normalizePerPage(
            $user->setting('ui.per_page.admin_users', 50)
        );
    }

    public function save(): void
    {
        $this->validate([
            'nickname' => ['nullable', 'string', 'max:80'],
            'locale' => ['required', 'string', Rule::in(['de', 'en'])],
            'appearance' => ['required', 'string', Rule::in(['system', 'light', 'dark'])],
            'adminUsersPerPage' => ['required', 'integer', Rule::in([10, 25, 50, 100])],
        ]);

        $user = auth()->user();

        if (! $user instanceof User) {
            return;
        }

        $user->setSetting('profile.nickname', trim($this->nickname));
        $user->setSetting('locale.app', $this->locale);
        $user->setSetting('ui.appearance', $this->appearance);
        $user->setSetting('ui.per_page.admin_users', $this->normalizePerPage($this->adminUsersPerPage));
        $user->save();

        Flux::toast(
            heading: __('Account settings saved'),
            text: __('Your personal settings have been updated.'),
            variant: 'success',
            duration: 3000,
        );
    }

    private function normalizePerPage(mixed $value): int
    {
        $value = (int) $value;

        if (! in_array($value, [10, 25, 50, 100], true)) {
            return 50;
        }

        return $value;
    }

    public function render()
    {
        return view('components.account.⚡settings');
    }
}
