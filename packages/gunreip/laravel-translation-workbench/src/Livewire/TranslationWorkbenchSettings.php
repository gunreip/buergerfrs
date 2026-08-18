<?php

namespace Gunreip\TranslationWorkbench\Livewire;

use App\Livewire\Concerns\InteractsWithUserSettings;
use App\Models\User;
use Gunreip\TranslationWorkbench\Support\TranslationWorkbenchVersion;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Throwable;

class TranslationWorkbenchSettings extends Component
{
    use InteractsWithUserSettings;

    public function resetEntriesUiState(): void
    {
        $this->resetStateGroup('ui_state');

        $this->dispatch('toast', type: 'success', message: __('Translation Workbench entry settings reset.'));
    }

    public function resetRawDataUiState(): void
    {
        $this->resetStateGroup('raw_data_ui_state');

        $this->dispatch('toast', type: 'success', message: __('Translation Workbench raw-data settings reset.'));
    }

    public function resetAllTranslationWorkbenchSettings(): void
    {
        $this->resetStateGroup('ui_state');
        $this->resetStateGroup('raw_data_ui_state');

        $this->dispatch('toast', type: 'success', message: __('Translation Workbench settings reset.'));
    }

    public function render(): View
    {
        return view('translation-workbench::livewire.settings', [
            'workbenchVersion' => app(TranslationWorkbenchVersion::class)->toArray(),
            'settingsGroups' => [
                $this->settingsGroup(
                    'Entries',
                    'Settings for the main Translation Workbench page: overview visibility, findings filters, sorting, pagination and modal behavior.',
                    'ui_state',
                    'table-cells',
                    'resetEntriesUiState',
                ),
                $this->settingsGroup(
                    'Raw data',
                    'Settings for the Translation Workbench raw-data page: selected table, table filters, sorting and pagination.',
                    'raw_data_ui_state',
                    'database',
                    'resetRawDataUiState',
                ),
            ],
            'configSummary' => $this->configSummary(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function settingsGroup(string $title, string $description, string $configKey, string $icon, string $resetAction): array
    {
        $settingKey = $this->settingKey($configKey);
        $defaults = $this->defaults($configKey);
        $userState = $this->userSetting($settingKey, []);
        $export = $this->exportState($configKey);

        return [
            'title' => $title,
            'description' => $description,
            'icon' => $icon,
            'reset_action' => $resetAction,
            'setting_key' => $settingKey,
            'defaults_file' => (string) config("translation-workbench.{$configKey}.defaults_file", ''),
            'export_file' => $this->exportPath($configKey),
            'default_count' => count($defaults),
            'user_count' => is_array($userState) ? count($userState) : 0,
            'export_count' => count((array) ($export['state'] ?? [])),
            'defaults' => $defaults,
            'user_state' => is_array($userState) ? $userState : [],
            'export' => $export,
            'has_user_state' => is_array($userState) && $userState !== [],
            'has_export_file' => File::exists($this->exportPath($configKey)),
        ];
    }

    private function resetStateGroup(string $configKey): void
    {
        $defaults = $this->defaults($configKey);
        $settingKey = $this->settingKey($configKey);

        $this->setUserSetting($settingKey, $defaults);
        $this->persistExportState($configKey, $defaults);
    }

    private function settingKey(string $configKey): string
    {
        return (string) config("translation-workbench.{$configKey}.setting_key", "ui.pages.translation_workbench.{$configKey}");
    }

    /**
     * @return array<string, mixed>
     */
    private function defaults(string $configKey): array
    {
        $fileDefaults = $this->defaultsFileState($configKey);
        $configDefaults = config("translation-workbench.{$configKey}.defaults", []);

        return [
            ...$fileDefaults,
            ...(is_array($configDefaults) ? $configDefaults : []),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function defaultsFileState(string $configKey): array
    {
        $path = base_path((string) config("translation-workbench.{$configKey}.defaults_file", ''));

        if (! File::exists($path)) {
            return [];
        }

        $decoded = json_decode((string) File::get($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @return array<string, mixed>
     */
    private function exportState(string $configKey): array
    {
        $path = $this->exportPath($configKey);

        if (! File::exists($path)) {
            return [];
        }

        $decoded = json_decode((string) File::get($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    private function exportPath(string $configKey): string
    {
        return storage_path((string) config("translation-workbench.{$configKey}.export_file", "translation-workbench/ui-state/{$configKey}.json"));
    }

    /**
     * @param  array<string, mixed>  $state
     */
    private function persistExportState(string $configKey, array $state): void
    {
        $path = $this->exportPath($configKey);

        try {
            File::ensureDirectoryExists(dirname($path));
            File::put($path, json_encode([
                'page' => $configKey === 'ui_state'
                    ? 'translation-workbench.entries'
                    : 'translation-workbench.raw-data',
                'updated_at' => now()->toISOString(),
                'state' => $state,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL);
        } catch (Throwable) {
            // The file snapshot is diagnostic only; the user-specific DB setting is authoritative.
        }
    }

    /**
     * @return array<int, array{label: string, value: string, color: string}>
     */
    private function configSummary(): array
    {
        return [
            [
                'label' => 'Source locale',
                'value' => (string) config('translation-workbench.source_locale', 'en'),
                'color' => 'sky',
            ],
            [
                'label' => 'Scan paths',
                'value' => number_format(count((array) config('translation-workbench.paths', []))),
                'color' => 'zinc',
            ],
            [
                'label' => 'Exclude paths',
                'value' => number_format(count((array) config('translation-workbench.exclude_paths', []))),
                'color' => 'zinc',
            ],
            [
                'label' => 'User',
                'value' => Auth::user() instanceof User ? (string) Auth::user()->email : 'guest',
                'color' => Auth::user() instanceof User ? 'green' : 'amber',
            ],
        ];
    }
}
