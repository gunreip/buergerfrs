## Translation-Workflow Guardrails

- Die Grundmenge der Translation-List basiert auf `translation_keys` als Work-Items, nicht auf vorhandenen `translation_values`.
- `Target Language` ist primär Fokus-/Bewertungskontext. Fehlende Übersetzungen (`missing`) für die gewählte Sprache müssen sichtbar und bearbeitbar bleiben; Sprachwechsel darf offene Fälle nicht ausblenden.
- Counter und Tabelle müssen immer aus derselben Filter- und Row-Menge berechnet werden.
- `Suggested Key` ist der einzige sichtbare Empfehlungskandidat in der Standard-UI. `Expected Key` gehört nicht in die Standard-UI.
- Key-Pflege (`native`, `dynamic`, no-key) und Übersetzungsarbeit (gekeyte Einträge pro Sprache) nicht stillschweigend vermischen. Wenn unterschiedliche Arbeitsmodi nötig sind, diese explizit benennen statt implizit über Filterlogik zu vermengen.
- `obsolete` ist Nebenworkflow für Review/Cleanup und darf den normalen Übersetzungsworkflow nicht dominieren.
- Vor Änderungen an Query-Semantik, Counterlogik, Suggested-Key-Regeln oder Translation-Commands zuerst `docs/key-rules-pattern.md` und die abgestimmten Workflow-Regeln prüfen; bei Abweichungen erneut fachlich abstimmen statt stillschweigend umbauen.

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
    - `php artisan test --filter=HighlightTest` – Testet die globale Search-Highlight-Komponente.
  - `php artisan test --filter=ImportLocaleReferenceDataCommandTest` – Smoke-/Regressionstest für den Locale-Reference-Import-Command und seine Optionen.
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
- `app/Livewire/Admin/CountryReferenceList.php` – Read-only Admin-Audit für importierte Länder-Referenzdaten, Address-Formats und Subdivisions.
- `app/Console/Commands/ImportLocaleReferenceData.php` – Artisan-Command `reference:import-locale-data` für Country-/Language-/Locale-Referenzdaten, REST-Countries-Metadaten, Addressing-Daten und Subdivisions.
- `app/Support/Locale/LocaleReferenceImporter.php` – Importer für `countries`, `country_names`, `country_subdivisions`, `address_formats`, `languages`, `language_names` und `locales`.
- `app/View/Components/Ui/Text/Highlight.php` – Globale Text-Highlight-Komponente für Suchtreffer in Admin-/Management-Listen.
- `app/Support/Avatar/AvatarPath.php` – Erzeugt geshardete Avatar-Pfade unter `storage/app/public/avatars/...`
- `app/Support/Avatar/UserAvatarStorage.php` – Speichert, löscht und resolved User-Avatar-Dateien über den Public-Disk
- `app/Settings/AppDisplaySettings.php` – Spatie-Settings: `app_display`-Gruppe (z.B. `roleBadges`)
- `users.settings` – JSONB-Spalte für userbezogene UI-/Profil-Einstellungen; Zugriff über `User::setting()` und `User::setSetting()`
- `config/buergerfrs-icons.php` – Erlaubte Flux-Icons pro Kategorie (z.B. `role_user_management`), inkl. Fallback-Definition
- `app/Models/Person.php` – Natürliche Person / Mensch als fachlicher Stammdatensatz; 1:1 mit `User` verknüpfbar
- `app/Models/Client.php` – Organisation / Mandant / Institution; kein Login-Konto
- `app/Models/ClientPerson.php` – Pivot-Model für `client_person` mit Beziehungstyp, Status und Verifizierungsdaten
- `app/Models/Country.php`, `CountryName.php`, `CountrySubdivision.php`, `AddressFormat.php`, `Language.php`, `LanguageName.php`, `Locale.php` – Referenzdaten-Models für Länder, lokalisierte Ländernamen, Subdivisions, postalische Address-Formats, Sprachen, lokalisierte Sprachnamen und Locales.
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
- **Abstimmung vor Umsetzung:** Bei komplexeren Änderungen, Workflow-Anpassungen, UI-/UX-Entscheidungen oder bereits mehrfach fehlgeschlagenen Themen nicht direkt implementieren. Zuerst fachlich abstimmen: Zielbild, gewünschtes Verhalten, Varianten, Vor-/Nachteile, Risiken, Randbedingungen und was ausdrücklich noch nicht umgesetzt werden soll. Hinweise wie „nur zur Info“ oder Vorab-Kontext sind als Orientierung zu behandeln, nicht als unmittelbarer Implementierungsauftrag.
- **Migrations:** Alle wichtigen Pakete bringen eigene Migrationen mit (bereits veröffentlicht)
- **Settings:** Spatie Laravel Settings; Einstellungsklassen liegen in `app/Settings/`. Gruppen-Name entspricht dem Klassennamen in snake_case (z.B. `AppDisplaySettings` → `app_display`). Nach neuen Settings-Klassen oder Settings-Migrations: php artisan migrate ausführen, danach php artisan settings:discover und php artisan settings:clear-cache.
- **Permission Metadata:** Die Tabelle `permissions` ist um `category`, `sort_order`, `description`, `is_system` erweitert. Diese Felder dienen nur der Admin-UI-Strukturierung und ändern nicht die technische Permission-Identität.
- **User Settings:** User-spezifische, nicht sicherheitskritische UI-/Profil-Einstellungen werden in `users.settings` als JSONB gespeichert. Zugriff erfolgt über `User::setting(string $key, mixed $default = null)` und `User::setSetting(string $key, mixed $value)`. Das Model wird nach `setSetting()` nicht automatisch gespeichert; `save()` muss explizit aufgerufen werden. Dot-Notation verwenden, z.B. `ui.per_page.admin_users`, `profile.nickname`, `profile.avatar_path`.
- **Icons:** Ausschließlich Flux-Icons verwenden. Erlaubte Icons, Badge-Farben und Badge-Varianten werden zentral in `config/buergerfrs-icons.php` pro Kategorie definiert. Neue Icons sollten vor der Registrierung mit `php artisan flux:icon <name>` deployed werden. Wenn ein registriertes Icon nicht verfügbar ist, rendert `x-ui.safe-flux-icon` sichtbar das rote `file-x`-Fallback. Zugriff immer über `App\Support\Icons\IconRegistry` – kein direkter Zugriff auf die Config.
- **Flux Icons:** Für allgemeines sicheres Flux-Icon-Rendering wird `x-ui.flux-icon` verwendet. Die Komponente prüft technisch, ob `flux.icon.{name}` existiert; fehlt es, wird `file-x` gerendert und ein `FallbackReport` geschrieben. `x-ui.safe-flux-icon` ist veraltet und soll nicht mehr verwendet werden.
- **IconRegistry:** `App\Support\Icons\IconRegistry` bleibt für fachlich konfigurierbare Icon-Metadaten wie Role-Badges/App-Settings zuständig. Sie ist nicht die allgemeine Voraussetzung für normale UI-Icons.
- **Admin-Bereich:** Routen in `routes/admin.php`, geschützt durch Middleware `role:Admin|Super-Admin` (Spatie Permission). Livewire-Komponenten in `app/Livewire/Admin/`.
- **Admin-Listen:** Admin-Listen verwenden ein einheitliches Muster mit Livewire, Flux, `x-ui.table.per-page-selector`, `x-ui.table.pagination`, `sortBy()`-Whitelist, `resetPage()` bei Filter-/Sortieränderungen und `wire:key` pro Tabellenzeile. Suchabfragen auf PostgreSQL sollen für Admin-/Audit-Listen standardmäßig case-insensitive über `ilike` laufen. Suchtreffer werden in Tabellen über `x-ui.text.highlight` markiert, nicht über lokale Closure-Funktionen in einzelnen Views. Ein optionaler Case-Sensitive-Toggle ist als TODO vorgesehen; wenn er umgesetzt wird, muss er Query-Operator (`ilike`/`like`) und Highlight-Komponente gemeinsam steuern.
- **Permissions:** Permission-Verwaltung läuft über `/admin/permissions` und `App\Livewire\Admin\PermissionList`. Permission-Identität (`name`, `guard_name`) wird nicht über die UI geändert. Editierbar sind nur Metadaten wie `category`, `sort_order`, `description`, `is_system`. Das Edit-Permission-Modal besitzt einen Dirty-State; Speichern ist nur bei echten Änderungen möglich und serverseitig gegen No-op-Saves abgesichert. Role↔Permission-Zuweisungen erfolgen rollen-zentriert über das Modal „Manage Role Permissions“ mit Dirty-State; gespeichert wird über Spatie `syncPermissions()`.
- **Reference Data:** Länder-, Sprach- und Locale-Stammdaten werden über `php artisan reference:import-locale-data` importiert. Der Command unterstützt `--dry-run`, `--locales=de,en`, `--with-country-meta`, `--with-addressing` und `--with-subdivisions`. REST-Countries-Daten liegen lokal unter `database/reference/restcountries.v3.1.json` und `database/reference/restcountries.v3.1.extra.json`. `symfony/intl` liefert Grunddaten für Countries/Languages/Locales; `commerceguys/addressing` liefert Address-Formats, postalische Regex/Required-Felder und Subdivisions. `mledoze/countries` wird wegen Symfony-Console-Abhängigkeitskonflikten nicht verwendet.
- **Reference Countries Audit:** `/admin/country-references` ist eine read-only Admin-Audit-Seite für Länder-Referenzdaten. `countries` bleibt die führende Stammdatentabelle; `country_names`, `country_subdivisions` und `address_formats` ergänzen lokalisierte Namen, Subdivisions und postalische Format-/Validierungsdaten. `countries` wird dadurch nicht obsolete.
- **User Avatars:** User-Avatare werden nicht user-id-basiert abgelegt, sondern über geshardete UUID-Pfade unter `storage/app/public/avatars/...`. Der gespeicherte relative Pfad liegt in `users.settings.profile.avatar_path`. Upload/Delete/URL-Resolution läuft über `App\Support\Avatar\UserAvatarStorage`; Pfaderzeugung über `App\Support\Avatar\AvatarPath`. Anzeige immer über `x-ui.user-avatar`, damit Bildanzeige und Fallback auf Initials zentral bleiben.
- **Buttons:** Standard-Aktionsbuttons werden über `x-ui.button.*` gerendert, nicht direkt als rohe `flux:button`, sofern es sich um wiederkehrende Aktionen wie `save`, `cancel`, `create`, `delete`, `edit`, `reset` oder `remove` handelt. Die Button-Komponenten besitzen ein Standard-Icon; `icon="..."` überschreibt es, `:icon="false"` rendert ohne Icon. Button-Layout, Farbe, Variant und Icon/Text-Trennung werden zentral in `resources/views/components/ui/button/base.blade.php` gepflegt.
- **Blade Component Props:** Dynamische, übersetzte oder typisierte Werte werden bei Blade-Komponenten bevorzugt als gebundene Props übergeben, z.B. `:title="__('...')"`, `:description="__('...')"`, `:label="__('...')"`, `:icon="false"`. Feste Literalwerte wie `color="green"`, `variant="subtle"` oder `type="button"` können normale Attribute bleiben. Das verhindert doppeltes Escaping wie sichtbares `&#039;`.
- **Search Highlighting:** Suchtreffer in Listen werden über `x-ui.text.highlight` gerendert. Die Komponente escaped den Wert zuerst und markiert Treffer anschließend mit `<mark class="highlight">...</mark>`. Standard ist case-insensitive Highlighting. `case-sensitive` bzw. `:case-sensitive="true"` ist technisch möglich, soll aber nur verwendet werden, wenn auch die zugehörige Query case-sensitive filtert. Andernfalls entstehen Treffer ohne sichtbares Highlight.
- **Tooltips:** Für fachliche Hilfetexte an Labels wird `x-ui.tooltip.trigger` verwendet, nicht `flux:tooltip`. Die Komponente rendert einen globalen Tooltip über `resources/js/components/ui/global-tooltip.js` und das globale Template in der Sidebar. Tooltip-Titel und Tooltip-Text werden als Props übergeben, bevorzugt gebunden, z.B. `:title="__('Salutation')"` und `:text="__('The salutation is used to address the person in a formal way.')"`; dadurch werden Escaping-Probleme bei Apostrophen, Quotes und Entities vermieden. Fehlende oder leere Werte werden absichtlich sichtbar als `No tooltip-title` bzw. `No tooltip-text` ausgegeben, damit unvollständige Implementierungen direkt auffallen. `required` am Trigger steuert ausschließlich den Required-Badge im Tooltip-Header; ohne `required` darf kein Required-Badge im Tooltip erscheinen. Die Tooltip-Position startet horizontal an der Cursor-Position, nicht an der Mitte des Trigger-Elements; vertikal wird weiterhin ober-/unterhalb des Trigger-Elements platziert und an den Viewport begrenzt.
- **Required Badge für Tooltips/Labels:** Für sichtbare Required-Badges in Labels wird `x-ui.tooltip.badge-required` verwendet, nicht ein roher `flux:badge`-Block. Beispiel: `<flux:label>{{ __('Birth place') }} <x-ui.tooltip.badge-required /></flux:label>`. Der Badge im Label und der Badge im Tooltip-Header sind getrennte Darstellungen: `x-ui.tooltip.badge-required` markiert das Label sichtbar, während `required` auf `x-ui.tooltip.trigger` den Tooltip-Header markiert.
          Beispiel:
  <x-ui.tooltip.trigger
      :title="__('Birth place')"
      :text="__('The birth place is required for identification and official records.')"
      required
  >
      <flux:label for="create-person-birth-place">
          {{ __('Birth place') }}
          <x-ui.tooltip.badge-required />
      </flux:label>
  </x-ui.tooltip.trigger>
- **Validation UX / Notices:** Browser-native HTML5-Constraint-Validation soll in komplexeren Livewire-/Flux-Formularen nicht die primäre UX sein. Feldfehler laufen serverseitig über Livewire/Laravel. Feldnahe `flux:error`-Texte können formularbezogen per CSS ausgeblendet werden, z.B. über einen Formular-Scope wie `#create-person-form [data-flux-error]`, damit das Layout stabil bleibt. Invalid-Markierung am Input bleibt erhalten. Für `/people/create` werden Validation-Fehler über das Browser-Event `buergerfrs:validation-errors` an `resources/js/notices/validation-notices.js` übergeben und dort als eigene Notices gerendert. Bei wenigen Fehlern erscheint eine Notice pro Feld, bei vielen Fehlern eine Sammelnotice. Notice-Actions nutzen `buergerfrs:focus-field`, um zum betroffenen Input zu scrollen/fokussieren.
- **Admin Activity Logging:** Admin-relevante Änderungen an Users/Roles/Permissions werden über `App\Support\Audit\AdminActivity` in `activity_log` mit `log_name=admin` dokumentiert. Geloggt werden u.a. User-Rollenänderungen, Role Create/Update, Permission-Metadatenänderungen und Role↔Permission-Zuweisungen. Events verwenden `admin.*`-Namen und enthalten `before`/`after`-Properties sowie Subject/Causer.
- **Management Activity Logging:** Fachliche Management-Aktionen werden über `App\Support\Audit\ManagementActivity` in `activity_log` mit `log_name=management` dokumentiert. Für `/people/create` wird `management.person.created` geschrieben, Subject ist `App\Models\Person`, Properties enthalten Person-/User-Daten sowie `generated_password_logged`.
- **User | Person | Client:** `User` ist ausschließlich das Login-/Auth-Konto. `Person` ist die natürliche Person / der Mensch als fachlicher Stammdatensatz. Jede Person soll fachlich ein User-Konto besitzen; technisch ist `users.person_id` aktuell nullable, damit bestehende/Seed-User migriert werden können. `Client` ist eine Organisation / Mandant / Institution und kann sich nicht selbst anmelden. Eine Person handelt privat oder im Kontext eines Clients.
- **User↔Person:** Die Beziehung ist als 1:1 über `users.person_id` modelliert. `User::person()` ist `belongsTo(Person::class)`, `Person::user()` ist `hasOne(User::class)`.
- **Client↔Person:** Die Beziehung läuft über `client_person` mit dem Pivot-Model `App\Models\ClientPerson`. Dort liegen `relationship_type`, `status`, `is_primary`, `starts_at`, `ends_at`, `verified_at`, `verified_by_user_id`, `created_by_user_id` und `notes`. `ClientPerson` erweitert `Illuminate\Database\Eloquent\Relations\Pivot`, nicht `Model`, weil die Relation über `belongsToMany()->using(ClientPerson::class)` läuft.
- **People Create v1:** `/people/create` ist eine Management-Seite, keine Admin-Modal-Aktion. Sie erstellt aktuell eine `Person`, erzeugt automatisch das zugehörige `User`-Login-Konto, weist initial die Rolle `User` zu, generiert ein temporäres Passwort und zeigt dieses einmalig im UI an. `person_number` wird beim Erstellen automatisch als nicht sprechende, zufallsbasierte Nummer mit Prüfziffer erzeugt und ist für sehr große Datenmengen ausgelegt. Die Validation-UX nutzt eigene browserseitige Notices statt nativer Browser-Constraint-Popups.
- **Generated Password Dev Log:** Temporär generierte Passwörter werden ausschließlich in `local` über `App\Support\Auth\GeneratedPasswordLogger` nach `storage/app/private/dev/generated-user-passwords.jsonl` geschrieben. Dieser Pfad ist ein lokales Dev-Artefakt und darf nicht versioniert werden.
- **Sidebar:** Die Hauptnavigation nutzt `flux:sidebar` mit `flux:sidebar.group` und `flux:sidebar.item`. Sidebar-Gruppen sollen bei Desktop-Collapse `icon="..."` und `expandable` besitzen, damit sie im collapsed Zustand als Icon sichtbar bleiben und ihre Items als Flyout öffnen. Gruppen werden über `:expanded="request()->routeIs(...)"` nur dann initial geöffnet, wenn eine passende Route aktiv ist; dadurch schließen inaktive Gruppen sauber. Kein Umbau auf `flux:navlist`, solange `flux:sidebar.group expandable` das gewünschte Verhalten abdeckt.

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
- Aktuell persistierte User-Settings: `ui.per_page.admin_users` für die persönliche Per-Page-Auswahl auf `/admin/users`, `locale.app` als gespeicherte Locale-Präferenz, `profile.nickname` und `profile.avatar_path` für Profilanzeige. Weitere Admin-Listen nutzen aktuell komponentenlokales `perPage`; Persistierung kann bei Bedarf analog ergänzt werden.
- Aktueller Fachmodell-Stand: `people`, `clients` und `client_person` sind als Grundmodell vorhanden. `people` wurde per Backfill 1:1 mit bestehenden `users` verknüpft. `client_person` ist technisch per Tinker verifiziert.

---

**Letzte Aktualisierung:** 2026-05-14

---

> Diese Datei hilft AI Coding Agents, Build-/Test-Kommandos, Konventionen und Besonderheiten dieses Projekts sofort zu verstehen. Bei Änderungen an der Projektstruktur bitte diese Datei anpassen.

## Ergänzung: Artisan Activity Logging

Für projekt-spezifische Artisan-Commands gilt die einheitliche Activity-Log-Konvention aus [docs/artisan-commands.md](docs/artisan-commands.md) (Event-Namensschema, log_name-Domänen, Mindest-Properties). Bei neuen Commands diese Konvention verpflichtend anwenden.
