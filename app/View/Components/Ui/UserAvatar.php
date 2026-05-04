<?php

// app/View/Components/Ui/UserAvatar.php

namespace App\View\Components\Ui;

use App\Models\User;
use App\Support\Avatar\UserAvatarStorage;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UserAvatar extends Component
{
    public ?User $user;

    public string $size;

    public string $class;

    public ?string $avatarUrl;

    /**
     * Create a new component instance.
     */
    public function __construct(
        ?User $user = null,
        string $size = 'md',
        string $class = '',
    ) {
        $this->user = $user;
        $this->size = $size;
        $this->class = $class;
        $this->avatarUrl = $this->resolveAvatarUrl($user);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.user-avatar');
    }

    private function resolveAvatarUrl(?User $user): ?string
    {
        if (! $user instanceof User) {
            return null;
        }

        $avatarPath = (string) $user->setting('profile.avatar_path', '');

        if ($avatarPath === '') {
            return null;
        }

        return app(UserAvatarStorage::class)->url($avatarPath);
    }
}
