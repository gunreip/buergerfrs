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
- `app/Livewire/Admin/` – Admin-Livewire-Komponenten: `UserList`, `RoleList`, `PermissionList`, `AppSettings`
- `UserEdit` ist aktuell geparkt und nicht aktiv geroutet; Rollenänderungen erfolgen über Modals in `UserList`.
- `app/Support/Icons/IconRegistry.php` – Zentrales Icon-Registry für Flux-Icons (Kategorie-basiert, mit Fallback)
- `app/Livewire/Admin/PermissionList.php` – Admin-UI für Permission-Übersicht, Permission-Metadaten und Role↔Permission-Zuweisungen
- `app/Support/Avatar/AvatarPath.php` – Erzeugt geshardete Avatar-Pfade unter `storage/app/public/avatars/...`
- `app/Support/Avatar/UserAvatarStorage.php` – Speichert, löscht und resolved User-Avatar-Dateien über den Public-Disk
- `app/Settings/AppDisplaySettings.php` – Spatie-Settings: `app_display`-Gruppe (z.B. `roleBadges`)
- `users.settings` – JSONB-Spalte für userbezogene UI-/Profil-Einstellungen; Zugriff über `User::setting()` und `User::setSetting()`
- `config/buergerfrs-icons.php` – Erlaubte Flux-Icons pro Kategorie (z.B. `role_user_management`), inkl. Fallback-Definition
- `app/Models/Person.php` – Natürliche Person / Mensch als fachlicher Stammdatensatz; 1:1 mit `User` verknüpfbar
- `app/Models/Client.php` – Organisation / Mandant / Institution; kein Login-Konto
- `app/Models/ClientPerson.php` – Pivot-Model für `client_person` mit Beziehungstyp, Status und Verifizierungsdaten
- `routes/web.php` – Web-Routen
- `routes/admin.php` – Admin-Routen wie `/admin/users`, `/admin/roles`, `/admin/permissions`, `/admin/app-settings` geschützt durch `auth`, `verified`, `role:Admin|Super-Admin`
- `routes/settings.php` – Einstellungen
- `resources/views/` – Blade-Templates
- `resources/views/components/ui/user-avatar.blade.php` – Zentrale User-Avatar-Anzeige mit Bild-Fallback auf Initials
- `resources/js/app.js` – Zentraler JavaScript-Einstiegspunkt; importiert formular- und noticebezogene Module.
- `resources/js/forms/focus-field.js` – Browser-Event `buergerfrs:focus-field`; scrollt ein Feld in den sichtbaren Bereich und fokussiert es.
- `resources/js/notices/notice-core.js` – Generische browserseitige Notice-UI mit Severity-Styles (`error`, `warning`, `info`, `success`), Actions, Close-Button und Timeout-Progress-Bar.
- `resources/js/notices/validation-notices.js` – Wandelt Livewire/Laravel-Validation-Fehler aus `buergerfrs:validation-errors` in eigene Validation-Notices um.
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
- **Permission Metadata:** Die Tabelle `permissions` ist um `category`, `sort_order`, `description`, `is_system` erweitert. Diese Felder dienen nur der Admin-UI-Strukturierung und ändern nicht die technische Permission-Identität.
- **User Settings:** User-spezifische, nicht sicherheitskritische UI-/Profil-Einstellungen werden in `users.settings` als JSONB gespeichert. Zugriff erfolgt über `User::setting(string $key, mixed $default = null)` und `User::setSetting(string $key, mixed $value)`. Das Model wird nach `setSetting()` nicht automatisch gespeichert; `save()` muss explizit aufgerufen werden. Dot-Notation verwenden, z.B. `ui.per_page.admin_users`, `profile.nickname`, `profile.avatar_path`.
- **Icons:** Ausschließlich Flux-Icons verwenden. Erlaubte Icons, Badge-Farben und Badge-Varianten werden zentral in `config/buergerfrs-icons.php` pro Kategorie definiert. Neue Icons sollten vor der Registrierung mit `php artisan flux:icon <name>` deployed werden. Wenn ein registriertes Icon nicht verfügbar ist, rendert `x-ui.safe-flux-icon` sichtbar das rote `file-x`-Fallback. Zugriff immer über `App\Support\Icons\IconRegistry` – kein direkter Zugriff auf die Config.
- **Admin-Bereich:** Routen in `routes/admin.php`, geschützt durch Middleware `role:Admin|Super-Admin` (Spatie Permission). Livewire-Komponenten in `app/Livewire/Admin/`.
- **Permissions:** Permission-Verwaltung läuft über `/admin/permissions` und `App\Livewire\Admin\PermissionList`. Permission-Identität (`name`, `guard_name`) wird nicht über die UI geändert. Editierbar sind nur Metadaten wie `category`, `sort_order`, `description`, `is_system`. Role↔Permission-Zuweisungen erfolgen rollen-zentriert über das Modal „Manage Role Permissions“ mit Dirty-State; gespeichert wird über Spatie `syncPermissions()`.
- **User Avatars:** User-Avatare werden nicht user-id-basiert abgelegt, sondern über geshardete UUID-Pfade unter `storage/app/public/avatars/...`. Der gespeicherte relative Pfad liegt in `users.settings.profile.avatar_path`. Upload/Delete/URL-Resolution läuft über `App\Support\Avatar\UserAvatarStorage`; Pfaderzeugung über `App\Support\Avatar\AvatarPath`. Anzeige immer über `x-ui.user-avatar`, damit Bildanzeige und Fallback auf Initials zentral bleiben.
- **Buttons:** Standard-Aktionsbuttons werden über `x-ui.button.*` gerendert, nicht direkt als rohe `flux:button`, sofern es sich um wiederkehrende Aktionen wie `save`, `cancel`, `create`, `delete`, `edit`, `reset` oder `remove` handelt. Die Button-Komponenten besitzen ein Standard-Icon; `icon="..."` überschreibt es, `:icon="false"` rendert ohne Icon. Button-Layout, Farbe, Variant und Icon/Text-Trennung werden zentral in `resources/views/components/ui/button/base.blade.php` gepflegt.
- **Blade Component Props:** Dynamische, übersetzte oder typisierte Werte werden bei Blade-Komponenten bevorzugt als gebundene Props übergeben, z.B. `:title="__('...')"`, `:description="__('...')"`, `:label="__('...')"`, `:icon="false"`. Feste Literalwerte wie `color="green"`, `variant="subtle"` oder `type="button"` können normale Attribute bleiben. Das verhindert doppeltes Escaping wie sichtbares `&#039;`.
- **Validation UX / Notices:** Browser-native HTML5-Constraint-Validation soll in komplexeren Livewire-/Flux-Formularen nicht die primäre UX sein. Feldfehler laufen serverseitig über Livewire/Laravel. Feldnahe `flux:error`-Texte können formularbezogen per CSS ausgeblendet werden, z.B. über einen Formular-Scope wie `#create-person-form [data-flux-error]`, damit das Layout stabil bleibt. Invalid-Markierung am Input bleibt erhalten. Für `/people/create` werden Validation-Fehler über das Browser-Event `buergerfrs:validation-errors` an `resources/js/notices/validation-notices.js` übergeben und dort als eigene Notices gerendert. Bei wenigen Fehlern erscheint eine Notice pro Feld, bei vielen Fehlern eine Sammelnotice. Notice-Actions nutzen `buergerfrs:focus-field`, um zum betroffenen Input zu scrollen/fokussieren.
- **Admin Activity Logging:** Admin-relevante Änderungen an Users/Roles/Permissions werden über `App\Support\Audit\AdminActivity` in `activity_log` mit `log_name=admin` dokumentiert. Geloggt werden u.a. User-Rollenänderungen, Role Create/Update, Permission-Metadatenänderungen und Role↔Permission-Zuweisungen. Events verwenden `admin.*`-Namen und enthalten `before`/`after`-Properties sowie Subject/Causer.
- **Management Activity Logging:** Fachliche Management-Aktionen werden über `App\Support\Audit\ManagementActivity` in `activity_log` mit `log_name=management` dokumentiert. Für `/people/create` wird `management.person.created` geschrieben, Subject ist `App\Models\Person`, Properties enthalten Person-/User-Daten sowie `generated_password_logged`.
- **User | Person | Client:** `User` ist ausschließlich das Login-/Auth-Konto. `Person` ist die natürliche Person / der Mensch als fachlicher Stammdatensatz. Jede Person soll fachlich ein User-Konto besitzen; technisch ist `users.person_id` aktuell nullable, damit bestehende/Seed-User migriert werden können. `Client` ist eine Organisation / Mandant / Institution und kann sich nicht selbst anmelden. Eine Person handelt privat oder im Kontext eines Clients.
- **User↔Person:** Die Beziehung ist als 1:1 über `users.person_id` modelliert. `User::person()` ist `belongsTo(Person::class)`, `Person::user()` ist `hasOne(User::class)`.
- **Client↔Person:** Die Beziehung läuft über `client_person` mit dem Pivot-Model `App\Models\ClientPerson`. Dort liegen `relationship_type`, `status`, `is_primary`, `starts_at`, `ends_at`, `verified_at`, `verified_by_user_id`, `created_by_user_id` und `notes`. `ClientPerson` erweitert `Illuminate\Database\Eloquent\Relations\Pivot`, nicht `Model`, weil die Relation über `belongsToMany()->using(ClientPerson::class)` läuft.
- **People Create v1:** `/people/create` ist eine Management-Seite, keine Admin-Modal-Aktion. Sie erstellt aktuell eine `Person`, erzeugt automatisch das zugehörige `User`-Login-Konto, weist initial die Rolle `User` zu, generiert ein temporäres Passwort und zeigt dieses einmalig im UI an. `person_number` wird beim Erstellen automatisch als nicht sprechende, zufallsbasierte Nummer mit Prüfziffer erzeugt und ist für sehr große Datenmengen ausgelegt. Die Validation-UX nutzt eigene browserseitige Notices statt nativer Browser-Constraint-Popups.
- **Generated Password Dev Log:** Temporär generierte Passwörter werden ausschließlich in `local` über `App\Support\Auth\GeneratedPasswordLogger` nach `storage/app/private/dev/generated-user-passwords.jsonl` geschrieben. Dieser Pfad ist ein lokales Dev-Artefakt und darf nicht versioniert werden.
- **Sidebar:** Die Hauptnavigation nutzt `flux:sidebar` mit `flux:sidebar.group` und `flux:sidebar.item`. Sidebar-Gruppen sollen bei Desktop-Collapse `icon="..."` und `expandable` besitzen, damit sie im collapsed Zustand als Icon sichtbar bleiben und ihre Items als Flyout öffnen. Kein Umbau auf `flux:navlist`, solange `flux:sidebar.group expandable` das gewünschte Verhalten abdeckt.

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
- Aktueller Fachmodell-Stand: `people`, `clients` und `client_person` sind als Grundmodell vorhanden. `people` wurde per Backfill 1:1 mit bestehenden `users` verknüpft. `client_person` ist technisch per Tinker verifiziert.

---

**Letzte Aktualisierung:** 2026-05-03

---

> Diese Datei hilft AI Coding Agents, Build-/Test-Kommandos, Konventionen und Besonderheiten dieses Projekts sofort zu verstehen. Bei Änderungen an der Projektstruktur bitte diese Datei anpassen.
