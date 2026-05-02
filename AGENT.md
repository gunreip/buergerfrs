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

---

## Wichtige Verzeichnisse & Dateien

- `app/` – Hauptapplikation (Controller, Models, Livewire, Actions)
- `routes/web.php` – Web-Routen
- `routes/settings.php` – Einstellungen
- `resources/views/` – Blade-Templates
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

---

**Letzte Aktualisierung:** 2026-04-30

---

> Diese Datei hilft AI Coding Agents, Build-/Test-Kommandos, Konventionen und Besonderheiten dieses Projekts sofort zu verstehen. Bei Änderungen an der Projektstruktur bitte diese Datei anpassen.