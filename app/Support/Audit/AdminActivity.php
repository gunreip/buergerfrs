<?php

// app/Support/Audit/AdminActivity.php

namespace App\Support\Audit;

use App\Models\FallbackReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminActivity
{
    private const LOG_NAME = 'admin';

    /**
     * Record an administrative action that does not need a specialized helper yet.
     *
     * @param  array<string, mixed>  $properties
     */
    public function record(string $event, string $description, ?Model $subject = null, array $properties = []): void
    {
        $this->log($event, $description, $subject, $properties);
    }

    public function userRoleChanged(User $targetUser, array $beforeRoles, array $afterRoles): void
    {
        $this->log(
            event: 'admin.user.role_changed',
            description: __('User role changed'),
            subject: $targetUser,
            properties: [
                'target_user' => [
                    'id' => $targetUser->id,
                    'name' => $targetUser->name,
                    'email' => $targetUser->email,
                ],
                'before' => [
                    'roles' => $beforeRoles,
                ],
                'after' => [
                    'roles' => $afterRoles,
                ],
            ],
        );
    }

    public function roleCreated(Role $role, array $metadata, array $badge): void
    {
        $this->log(
            event: 'admin.role.created',
            description: __('admin.roles.messages.role_created.heading'),
            subject: $role,
            properties: [
                'role' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'guard_name' => $role->guard_name,
                ],
                'metadata' => $metadata,
                'badge' => $badge,
            ],
        );
    }

    public function roleUpdated(Role $role, array $before, array $after): void
    {
        $this->log(
            event: 'admin.role.updated',
            description: __('Role metadata updated'),
            subject: $role,
            properties: [
                'role' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'guard_name' => $role->guard_name,
                ],
                'before' => $before,
                'after' => $after,
            ],
        );
    }

    public function permissionMetadataUpdated(Permission $permission, array $before, array $after): void
    {
        $this->log(
            event: 'admin.permission.metadata_updated',
            description: __('Permission metadata updated'),
            subject: $permission,
            properties: [
                'permission' => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'guard_name' => $permission->guard_name,
                ],
                'before' => $before,
                'after' => $after,
            ],
        );
    }

    public function rolePermissionsUpdated(Role $role, array $beforePermissions, array $afterPermissions): void
    {
        $this->log(
            event: 'admin.role.permissions_updated',
            description: __('Role permissions updated'),
            subject: $role,
            properties: [
                'role' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'guard_name' => $role->guard_name,
                ],
                'before' => [
                    'permissions' => $beforePermissions,
                ],
                'after' => [
                    'permissions' => $afterPermissions,
                ],
            ],
        );
    }

    public function fallbackReviewChanged(FallbackReport $report, bool $beforeReviewed, bool $afterReviewed): void
    {
        $this->log(
            event: $afterReviewed ? 'admin.fallback_report.reviewed' : 'admin.fallback_report.reopened',
            description: $afterReviewed ? __('Fallback report reviewed') : __('Fallback report reopened'),
            subject: $report,
            properties: [
                'fallback_report' => [
                    'id' => $report->id,
                    'type' => $report->type,
                    'key' => $report->key,
                    'fingerprint' => $report->fingerprint,
                ],
                'before' => ['reviewed' => $beforeReviewed],
                'after' => ['reviewed' => $afterReviewed],
            ],
        );
    }

    public function flagReferenceCommentChanged(User $user, string $code, ?string $before, ?string $after): void
    {
        $this->log(
            event: 'admin.flag_reference.comment_changed',
            description: __('Flag reference comment changed'),
            subject: $user,
            properties: [
                'flag_code' => $code,
                'before' => ['comment' => $before],
                'after' => ['comment' => $after],
            ],
        );
    }

    private function log(string $event, string $description, ?Model $subject, array $properties): void
    {
        $logger = activity(self::LOG_NAME)
            ->event($event)
            ->withProperties(array_merge($properties, [
                'source' => [
                    'route' => request()?->route()?->getName(),
                    'url' => request()?->fullUrl(),
                    'component' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['class'] ?? null,
                ],
            ]));

        if (auth()->check()) {
            $logger->causedBy(auth()->user());
        }

        if ($subject !== null) {
            $logger->performedOn($subject);
        }

        $logger->log($description);
    }
}
