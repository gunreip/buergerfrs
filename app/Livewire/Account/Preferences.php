<?php

// app/Livewire/Account/Preferences.php

namespace App\Livewire\Account;

use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

/**
 * Account preferences component for locale and user-specific list defaults.
 */
class Preferences extends Component
{
    public string $locale = 'de';

    public int $adminUsersPerPage = 50;

    /**
     * Load persisted preferences for the authenticated user.
     */
    public function mount(): void
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return;
        }

        $this->locale = (string) $user->setting('locale.app', app()->getLocale());
        $this->adminUsersPerPage = $this->normalizePerPage(
            $user->setting('ui.per_page.admin_users', 50)
        );
    }

    /**
     * Validate and persist account preferences.
     */
    public function save(): void
    {
        $this->validate([
            'locale' => ['required', 'string', Rule::in(['de', 'en'])],
            'adminUsersPerPage' => ['required', 'integer', Rule::in([10, 25, 50, 100])],
        ]);

        $user = Auth::user();

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

    /**
     * Normalize selectable pagination size.
     */
    private function normalizePerPage(mixed $value): int
    {
        $value = (int) $value;

        if (! in_array($value, [10, 25, 50, 100], true)) {
            return 50;
        }

        return $value;
    }

    /**
     * Render preferences form.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('components.account.⚡preferences');
    }
}
