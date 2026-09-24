<?php

namespace App\Support;

use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;
use RuntimeException;

final class VersionManifest
{
    public const COMPONENTS = ['buergerfrs', 'translation-workbench', 'tw-graph'];

    private readonly string $file;

    public function __construct(?string $file = null)
    {
        $this->file = $file ?? base_path('versions.json');
    }

    public function all(): array
    {
        if (! is_readable($this->file)) {
            throw new RuntimeException('Cannot read versions.json.');
        }

        $versions = json_decode(file_get_contents($this->file), true, flags: JSON_THROW_ON_ERROR);
        if (! is_array($versions) || array_diff(array_keys($versions), self::COMPONENTS) !== []) {
            throw new RuntimeException('Invalid component keys in versions.json.');
        }

        foreach (self::COMPONENTS as $component) {
            $this->validate($versions[$component] ?? null);
        }

        return $versions;
    }

    public function update(string $component, ?string $bump, ?string $set): string
    {
        if (! in_array($component, self::COMPONENTS, true)) {
            throw new InvalidArgumentException('Unknown component: '.$component);
        }
        if (($bump === null) === ($set === null)) {
            throw new InvalidArgumentException('Specify exactly one of --bump or --set.');
        }

        $versions = $this->all();
        if ($set !== null) {
            $this->validate($set);
            $next = $set;
        } else {
            [$major, $minor, $patch] = array_map('intval', explode('.', $versions[$component]));
            $next = match ($bump) {
                'patch' => "$major.$minor.".($patch + 1),
                'minor' => "$major.".($minor + 1).'.0',
                'major' => ($major + 1).'.0.0',
                default => throw new InvalidArgumentException('Use major, minor or patch for --bump.'),
            };
        }

        $versions[$component] = $next;
        (new Filesystem)->replace($this->file, json_encode($versions, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL);

        return $next;
    }

    private function validate(mixed $version): void
    {
        if (! is_string($version) || ! preg_match('/^(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)$/D', $version)) {
            throw new InvalidArgumentException('Versions must use Major.Minor.Patch without leading zeroes.');
        }
    }
}
