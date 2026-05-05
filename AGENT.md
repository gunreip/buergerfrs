# AGENT.md

## Projektüberblick

Dies ist ein Laravel 13.x Projekt mit Livewire, Fortify, Horizon, Telescope und diversen Spatie-Paketen. Es nutzt moderne PHP-Versionen (>=8.3) und ist für größere Anwendungen mit Rollen-/Rechtemanagement, API-Authentifizierung, Aktivitätslogs, Backups und Response-Caching ausgelegt.

---

## Build- und Test-Kommandos

- **Lokale Entwicklung:**
  - `composer setup` – Initiales Setup (Abhängigkeiten, .env, Key, Migrationen, npm build)
  - `composer dev` – Startet Server, Queue, Log-Viewer und Vite parallel
- **Tests:**
  - `composer test` – Führt Linting und alle Tests (Pest, PHPUnit) aus
  - `./vendor/bin/pest` – Nur Tests
- **Linter:**
  - `composer lint` – Code-Style mit Laravel Pint
- **Icons deployen:**
  - `php artisan flux:icon <name>` – Flux-Icon in Projekt kopieren (muss vor Nutzung in `config/buergerfrs-icons.php` eingetragen werden)

---

## Wichtige Verzeichnisse & Dateien

- `app/` – Hauptapplikation (Controller, Models, Livewire, Actions, Support, Settings)
- `app/Livewire/Admin/` – Admin-Livewire-Komponenten: `UserList`, `RoleList`, `AppSettings`
- `UserEdit` ist aktuell geparkt und nicht aktiv geroutet; Rollenänderungen erfolgen über Modals in `UserList`.
- `app/Support/Icons/IconRegistry.php` – Zentrales Icon-Registry für Flux-Icons (Kategorie-basiert, mit Fallback)
- `app/Support/Settings/RoleBadgeResolver.php` – Löst Rollen-Badge-Einstellungen (Farbe, Variante, Icon) auf
- `app/Support/Avatar/AvatarPath.php` – Erzeugt geshardete Avatar-Pfade unter `storage/app/public/avatars/...`
- `app/Support/Avatar/UserAvatarStorage.php` – Speichert, löscht und resolved User-Avatar-Dateien über den Public-Disk
- `app/Settings/AppDisplaySettings.php` – Spatie-Settings: `app_display`-Gruppe (z.B. `roleBadges`)
- `users.settings` – JSONB-Spalte für userbezogene UI-/Profil-Einstellungen; Zugriff über `User::setting()` und `User::setSetting()`
- `config/buergerfrs-icons.php` – Erlaubte Flux-Icons pro Kategorie (z.B. `role_user_management`), inkl. Fallback-Definition
- `routes/web.php` – Web-Routen
- `routes/admin.php` – Admin-Routen (geschützt durch `auth`, `verified`, `role:Admin|Super-Admin`)
- `routes/settings.php` – Einstellungen
- `resources/views/` – Blade-Templates
- `resources/views/components/ui/user-avatar.blade.php` – Zentrale User-Avatar-Anzeige mit Bild-Fallback auf Initials
- `tests/` – Feature- und Unit-Tests (Pest)
- `.env.example` – Beispiel-Umgebung

---

## Konventionen & Hinweise

- **User-Authentifizierung:** Laravel Fortify (siehe `app/Actions/Fortify/`)
- **Rollen & Rechte:** Spatie Permission (siehe `config/permission.php`)
- **API-Auth:** Laravel Sanctum
- **Aktivitätslogs:** Spatie Activitylog (`config/activitylog.php`)
- **Backups:** Spatie Backup (`config/backup.php`)
- **Response-Cache:** Spatie ResponseCache (`config/responsecache.php`)
- **Queue:** Horizon
- **Debugging:** Debugbar, Telescope, Ray
- **Migrations:** Alle wichtigen Pakete bringen eigene Migrationen mit (bereits veröffentlicht)
- **Settings:** Spatie Laravel Settings; Einstellungsklassen liegen in `app/Settings/`. Gruppen-Name entspricht dem Klassennamen in snake_case (z.B. `AppDisplaySettings` → `app_display`). Nach neuen Settings-Klassen oder Settings-Migrations: php artisan migrate ausführen, danach php artisan settings:discover und php artisan settings:clear-cache.
- **User Settings:** User-spezifische, nicht sicherheitskritische UI-/Profil-Einstellungen werden in `users.settings` als JSONB gespeichert. Zugriff erfolgt über `User::setting(string $key, mixed $default = null)` und `User::setSetting(string $key, mixed $value)`. Das Model wird nach `setSetting()` nicht automatisch gespeichert; `save()` muss explizit aufgerufen werden. Dot-Notation verwenden, z.B. `ui.per_page.admin_users`, `profile.nickname`, `profile.avatar_path`.
- **Icons:** Ausschließlich Flux-Icons verwenden. Erlaubte Icons, Badge-Farben und Badge-Varianten werden zentral in `config/buergerfrs-icons.php` pro Kategorie definiert. Neue Icons sollten vor der Registrierung mit `php artisan flux:icon <name>` deployed werden. Wenn ein registriertes Icon nicht verfügbar ist, rendert `x-ui.safe-flux-icon` sichtbar das rote `file-x`-Fallback. Zugriff immer über `App\Support\Icons\IconRegistry` – kein direkter Zugriff auf die Config.
- **Admin-Bereich:** Routen in `routes/admin.php`, geschützt durch Middleware `role:Admin|Super-Admin` (Spatie Permission). Livewire-Komponenten in `app/Livewire/Admin/`.
- **User Avatars:** User-Avatare werden nicht user-id-basiert abgelegt, sondern über geshardete UUID-Pfade unter `storage/app/public/avatars/...`. Der gespeicherte relative Pfad liegt in `users.settings.profile.avatar_path`. Upload/Delete/URL-Resolution läuft über `App\Support\Avatar\UserAvatarStorage`; Pfaderzeugung über `App\Support\Avatar\AvatarPath`. Anzeige immer über `x-ui.user-avatar`, damit Bildanzeige und Fallback auf Initials zentral bleiben.
- **Buttons:** Standard-Aktionsbuttons werden über `x-ui.button.*` gerendert, nicht direkt als rohe `flux:button`, sofern es sich um wiederkehrende Aktionen wie `save`, `cancel`, `create`, `delete`, `edit`, `reset` oder `remove` handelt. Die Button-Komponenten besitzen ein Standard-Icon; `icon="..."` überschreibt es, `:icon="false"` rendert ohne Icon. Button-Layout, Farbe, Variant und Icon/Text-Trennung werden zentral in `resources/views/components/ui/button/base.blade.php` gepflegt.

---

## CI/CD & Linting

- Siehe `.github/workflows/tests.yml` und `lint.yml` für automatisierte Tests und Code-Qualität.
- Node.js v22 und PHP 8.3–8.5 werden in CI getestet.

---

## Development

- **Livewire/Flux**: Es wird ausschließlich Livewire/Flux verwendet, kein selbstgestrickter Code für Aufgaben die mit Livewire/Flux gelöst werden können.

---

## Weitere Hinweise

- Für neue Pakete: Nach `composer require` ggf. `php artisan vendor:publish` und `php artisan migrate` ausführen.
- Für Umgebungsvariablen: `.env.example` als Vorlage nutzen.
- **Versions- und Paketübersicht:** Siehe `VERSIONS.md` für eine aktuelle Übersicht aller wichtigen Composer- und npm-Abhängigkeiten inkl. Beschreibung. Die Datei wird regelmäßig gepflegt und enthält auch Hinweise zu verwendeten Systemversionen.
- Für alle Änderungen im Projekt gilt: **Regelmäßig git commits durchführen!** So bleiben Änderungen nachvollziehbar, können bei Bedarf rückgängig gemacht werden und die Zusammenarbeit im Team wird erleichtert.
- Aktuell persistierte User-Settings: `ui.per_page.admin_users` für die persönliche Per-Page-Auswahl auf `/admin/users`, `locale.app` als gespeicherte Locale-Präferenz, `profile.nickname` und `profile.avatar_path` für Profilanzeige.

---

**Letzte Aktualisierung:** 2026-05-03

---

> Diese Datei hilft AI Coding Agents, Build-/Test-Kommandos, Konventionen und Besonderheiten dieses Projekts sofort zu verstehen. Bei Änderungen an der Projektstruktur bitte diese Datei anpassen.
