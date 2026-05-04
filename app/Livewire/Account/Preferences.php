<?php

// app/Livewire/Account/Preferences.php

namespace App\Livewire\Account;

use App\Models\User;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Preferences extends Component
{
    public string $locale = 'de';
    public int $adminUsersPerPage = 50;

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return;
        }

        $this->locale = (string) $user->setting('locale.app', app()->getLocale());
        $this->adminUsersPerPage = $this->normalizePerPage(
            $user->setting('ui.per_page.admin_users', 50)
        );
    }

    public function save(): void
    {
        $this->validate([
            'locale' => ['required', 'string', Rule::in(['de', 'en'])],
            'adminUsersPerPage' => ['required', 'integer', Rule::in([10, 25, 50, 100])],
        ]);

        $user = auth()->user();

        if (! $user instanceof User) {
            return;
        }

        $user->setSetting('locale.app', $this->locale);
        $user->setSetting('ui.per_page.admin_users', $this->normalizePerPage($this->adminUsersPerPage));
        $user->save();

        Flux::toast(
            heading: __('Preferences saved'),
            text: __('Your personal application preferences have been updated.'),
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
        return view('components.account.⚡preferences');
    }
}
