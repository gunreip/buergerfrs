<?php

// app/Console/Commands/BackfillUserPersons.php

// php artisan app:backfill-user-persons --dry-run
// php artisan app:backfill-user-persons

namespace App\Console\Commands;

use App\Models\Person;
use App\Models\User;
use App\Support\ActivityLog\ConsoleActivityContext;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

#[Signature('app:backfill-user-persons {--dry-run : Show what would be created without writing changes}')]
#[Description('Create and link Person records for users without a person_id.')]
/**
 * Backfill helper command to ensure users have linked person records.
 */
class BackfillUserPersons extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $users = User::query()
            ->whereNull('person_id')
            ->orderBy('id')
            ->get();

        if ($users->isEmpty()) {
            $this->info('No users without person_id found.');

            $this->logRunActivity('app.backfill_user_persons.no_changes', 'No users without person_id found for backfill.', [
                'dry_run' => $dryRun,
            ]);

            return self::SUCCESS;
        }

        $created = 0;
        $linked = 0;
        $skipped = 0;

        $this->info(($dryRun ? '[DRY RUN] ' : '')."Found {$users->count()} user(s) without person_id.");

        foreach ($users as $user) {
            $nameParts = $this->splitUserName((string) $user->name);

            $this->line(sprintf(
                '%s user #%d <%s> → Person: first_name="%s", last_name="%s"',
                $dryRun ? '[DRY RUN]' : '[WRITE]',
                $user->id,
                $user->email,
                $nameParts['first_name'],
                $nameParts['last_name'],
            ));

            if ($dryRun) {
                $skipped++;

                continue;
            }

            try {
                DB::transaction(function () use ($user, $nameParts, &$created, &$linked): void {
                    $person = Person::query()->create([
                        'first_name' => $nameParts['first_name'],
                        'last_name' => $nameParts['last_name'],
                    ]);

                    $user->person()->associate($person);
                    $user->save();

                    $created++;
                    $linked++;
                });
            } catch (Throwable $exception) {
                report($exception);

                $this->error(sprintf(
                    'Failed for user #%d <%s>: %s',
                    $user->id,
                    $user->email,
                    $exception->getMessage(),
                ));

                $skipped++;
            }
        }

        $this->newLine();

        $this->info("Created persons: {$created}");
        $this->info("Linked users: {$linked}");
        $this->info("Skipped users: {$skipped}");

        $this->logRunActivity('app.backfill_user_persons.completed', 'Backfill user persons command completed.', [
            'dry_run' => $dryRun,
            'users_without_person' => $users->count(),
            'created_persons' => $created,
            'linked_users' => $linked,
            'skipped_users' => $skipped,
        ]);

        return self::SUCCESS;
    }

    /**
     * Split the current user name into a first_name and last_name fallback.
     *
     * @return array{first_name: string, last_name: string}
     */
    private function splitUserName(string $name): array
    {
        $name = trim($name);

        if ($name === '') {
            return [
                'first_name' => 'Unknown',
                'last_name' => 'Account',
            ];
        }

        $parts = preg_split('/\s+/', $name) ?: [];

        if (count($parts) === 1) {
            return [
                'first_name' => $parts[0],
                'last_name' => 'Account',
            ];
        }

        $firstName = array_shift($parts);

        return [
            'first_name' => $firstName,
            'last_name' => implode(' ', $parts),
        ];
    }

    private function logRunActivity(string $event, string $description, array $properties = []): void
    {
        try {
            $activity = activity('project')
                ->event($event);

            $activity
                ->withProperties(ConsoleActivityContext::merge($this, $properties))
                ->log($description);
        } catch (Throwable $exception) {
            $this->warn('Activity log write failed: '.$exception->getMessage());
        }
    }
}
