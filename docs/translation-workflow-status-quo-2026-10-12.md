# Translation Workflow — Status quo und Überschneidungen

Stand: 12.10.2026, aktueller Arbeitsbaum (einschließlich uncommitteter Änderungen).

## Umfang / Aussagegrenze

Statische Prüfung der Routen, UI-Aktionen, Command-Registrierung, Pipeline-Aufrufe,
Modelle und zentralen Services. Artisan list wurde lesend ausgeführt. Keine
Scan-, Import-, Export-, Cleanup- oder Schreibpipeline auf echten Daten gestartet.
Keine neue Datenbankzählung; frühere Bestandszahlen stehen im separaten Bericht
translation-workbench-inventory-2026-10-12.txt und sind Momentaufnahmen.
Externe Cronjobs, Shell-Historie und tatsächlich laufende Hintergrundprozesse sind
hier nicht inventarisiert. Kein vollständiger Mutationstest sämtlicher Optionen.
Die folgenden Quellenpfade sind relativ zur Projektwurzel.

## 1. ERGEBNIS

Es bestehen drei Datenmodelle, nicht lediglich zwei Menüs:

- A) Legacy: translation_keys / translation_values / translation_usages,
  translation_audit_events und eigene Entscheidungsdaten.

- B) Älteres Package-Modell: translation_workbench_entries / values / occurrences /
  events sowie dynamische Werte und Discovery-Daten.

- C) Aktuelle Foundation: translation_workbench_findings / keys / key_findings /
  lang_values / reviews / source_files / dynamic_sources samt Timeline-Daten.

Old Workbench und altes Raw Data sind inzwischen über Route und Menü stillgelegt.
Das Modell B bleibt aber durch scan und import-existing aktiv beschrieben.
WICHTIG: sync-foundation liest nicht einfach B als Migration ein. Es ruft selbst
TranslationWorkbenchScanner::scan() auf und synchronisiert dessen Ergebnisse über
TranslationWorkbenchFoundationSyncer::sync(). Beide Scan-Ziele laufen parallel.

Die neue gemeinsame Menügruppe ändert keine Datenhoheit und synchronisiert keine
Werte. Insbesondere bleiben Management, Sub-Languages, Usage und Lang Ballast
an das Legacy-System angebunden. Statistics zählt weiterhin Legacy-Daten.

## 2. OBERFLÄCHEN: WER MACHT WAS UND WANN?

### Workbench

**Route:** `/admin/translation-workbench`

**Klasse:** `packages/gunreip/laravel-translation-workbench/src/Livewire/TranslationWorkbenchEntries.php`

Aktive Bearbeitung von Findings, Keys, Sprachwerten, Reviews, dynamischen
Zuordnungen, Bulk-Equalize, Konflikten und Cleanup-Entscheidungen.
saveTranslationValue / saveTranslationValueForLocale schreiben Foundation-Werte.
Zahlreiche fachliche Entscheidungen liegen direkt in dieser großen Livewire-
Klasse; eine universelle gemeinsame Schreib-Service-Schicht mit Legacy besteht
nicht. Speichern eines Werts ist nicht gleich Veröffentlichung in lang/.
UI-Pipeline: erstellt PipelineRun mit Benutzer-ID, startet per nohup/exec
translation:workbench --complete --pipeline-run-id=N als Hintergrundprozess.
Log: storage/logs/translation-workbench-pipeline-N.log. Kein Queue-Job an dieser
Stelle. Weitere UI-Helfer rufen plan-code-updates, apply-code-updates (ohne
--write), detect-shared-key-candidates und detect-suspicious-keyed-additions auf.

### Management (Legacy)

**Route:** `/admin/translations`

**Klasse:** `app/Livewire/Admin/TranslationList.php`

saveTranslationEdit(): eigenständiger Schreibweg für Legacy-Key-/Value-Daten
und Review-/Audit-Zustände; keine direkte Veröffentlichung der Sprachdateien.
Datenhoheit kollidiert fachlich mit Workbench, nicht durch dieselbe DB-Zeile,
sondern durch identische logische Übersetzungskeys in getrennten Modellen.

### Sub-Languages

**Route:** `/admin/translation-sub-languages`

**Klasse:** `app/Livewire/Admin/TranslationSubLanguages.php`

NICHT nur Spracheinstellungen: saveTranslationEntryEdit() schreibt
TranslationValue::updateOrCreate(), source=manual, status=missing/ok,
reviewed_at und reviewed_by_user_id. Zusätzlicher aktiver Legacy-Editor!
Varianten-/Fallback-/Base-Duplicate-Funktionen vor Ablösung ausdrücklich sichern.

### Usage

**Route:** `/admin/translations/usage`

**Klasse:** `app/Livewire/Admin/TranslationUsageAudit.php`

Liest Audit-Auswertungen und Legacy-Keys. createTranslationKeyFromUsageAudit()
legt Legacy-Keys an; saveUsageAuditDecision() speichert eigene Entscheidungen
und zugehörige Verwendungen. Quellcode-Apply erfolgt über separaten Command.
Überschneidung mit Workbench Shared candidates / Bulk-Equalize / Key-Review /
Code-Update. Fachliche Gleichwertigkeit ist noch nicht vollständig belegt.

### Lang Ballast

**Route:** `/admin/translations/lang-ballast`

**Klasse:** `app/Livewire/Admin/TranslationLangBallast.php`

Liest Audit-Dateien; setDecisionStatus() speichert TranslationLangBallastDecision.
translations:lang-ballast:apply --write setzt Entscheidungen an lang/ um.
Überschneidung mit Foundation-Cleanup/Export-Pruning; anderer Entscheidungsbestand.

### Statistics

**Route:** `/admin/translation-statistics`

**Klasse:** `app/Livewire/Admin/TranslationStatistics.php`

render() liest translation_keys / translation_values. Statistik ist somit keine
Foundation-Gesamtauswertung, auch wenn sie jetzt unter Workbench steht.

### Raw Data

**Route:** `/admin/translation-workbench/raw-data-new`

Klassen: TranslationWorkbenchRawDataNew, Basis TranslationWorkbenchRawData,
TranslationWorkbenchRawDataChainPreview, TwGraphDatasetDetails.
Diagnose / Auswahl / Timeline / Details; UI-Zustand wird gespeichert.
Keine fachliche Übersetzungsbearbeitung im hier geprüften Seitenfluss.
Die RawData-Basisklasse ist weiterhin Voraussetzung für New und Lazy-Preview.
TW-Graph/DataDriven bereitet Darstellung/Geometrie auf; keine Exportinstanz.

### Settings

**Route:** `/admin/translation-workbench/settings`

**Klasse:** `TranslationWorkbenchSettings.`

Verwaltet Workbench-UI-/Raw-Data-Zustand; ist nicht automatisch eine vollständige
zentrale Übersetzungs-/Sprachkonfiguration. AppGeneralSettings, Locale und
Sprachdaten sowie Package-Konfiguration werden daneben verwendet.

## 3. PIPELINES UND SCHREIBGRENZEN

### A) app/Console/Commands/ProjectTranslations.php — project:translations

Standardfolge:
ensure-lang-directories -> audit-code -> sync-audits -> generate-literal-diffs
-> audit-sub-language-redundancy -> export-lang-files -> abschließende Audits
und Decision-/Ballast-Previews.
--apply-diffs: führt erzeugten Patch aus, danach erneutes Audit/Sync.
--apply-usage-decisions: schreibt geprüfte Source-Ersetzungen, danach neue Audits.
--skip-export / --skip-audits: überspringen Teilgruppen, kein globaler Dry-Run.
STANDARD IST NICHT READ-ONLY: DB-Sync, Verzeichnisse, Reports und Sprachdateien.
Fehler im Unterprozess bricht Pipeline ab. Voriger Export-Schutz ist vorhanden.

### B) Package Console/RunTranslationWorkbench.php — translation:workbench

Standard: scan + sync-foundation, Sprach-/Source-Import, Discovery,
Klassifikation, Kandidaten, Inventar und Export-/Code-Update-Berichte.
Standardlauf schreibt DB-Daten. --dry-run verhindert fachliche DB-Schreibwege;
Berichte sind trotzdem Ausgabe-Artefakte, kein Versprechen völliger Dateifreiheit.
--write-lang-files aktiviert Sprachdatei-Schreiben.
--complete aktiviert zusätzlich mark-obsolete und tatsächliche Code-Updates,
mit nachfolgenden Wiederholungen zum Aktualisieren der Berichte.
Im Complete-Fluss wird der Lang-Import hinter den Export gelegt. Die genaue
Schrittfolge hängt von skip-Optionen ab; Refresh-Schritte in derselben Klasse
sind beabsichtigte Wiederholungen, nicht automatisch redundante Fehler.
--truncate / --truncate-foundation sind explizite separate Lösch-/Neuaufbauwege.

### C) Dateien als gemeinsamer Übergabepunkt

Legacy Exporter: app/Console/Commands/TranslationsExportLangFiles.php.
Baut Ziel-Payloads aus Legacy-DB. Aktueller Schutz verweigert Entfernen/Ändern
vorhandener aktiver Workbench-PHP-Werte; schützt nicht pauschal alle Writer.
Foundation Exporter: Foundation/TranslationWorkbenchLangFileExporter.php.
Plant Änderungen auf Basis bestehender Datei plus exportierbarer / zu entfernender
Foundation-Werte. Command schreibt erst mit --write.
Beide schreiben lang/{locale}/...; gleiche Datei bedeutet keine gemeinsame DB.
Foundation/LangValueImporter kann fehlende zuvor importierte Werte obsolete
markieren. Export -> Import beeinflusst somit auch DB-Status.
Laravel übersetzt im normalen UI aus lang/, nicht direkt aus diesen Editortabellen.

### D) Zusätzliche Auslöser

Repository-Scheduler: routes/console.php und app/Console/Kernel.php enthalten
DB-Health/Backup, keine Translation-Pipeline. Aktiv verdrahtet ist console.php
über bootstrap/app.php. Externe Automatisierung wurde nicht geprüft.
RuntimeDynamicTranslationCollector::options() wird unter anderem von UserList,
RoleList, CreatePerson und EditPerson aufgerufen. Er schreibt dynamische
Quellen/Werte/Kandidaten und Timeline-Information bei normalen UI-Abläufen,
wenn runtime_collector.enabled aktiv ist (Konfigurationsdefault=true).
Das ist Datenerfassung, keine automatische Übersetzungsfreigabe.

## 4. KONKRETE DOPPELGLEISIGKEITEN UND ENTSCHEIDUNGSBEDARF

### Priorität 1 — Werte und Veröffentlichung

Management + Sub-Languages + Workbench können gleiche logische Werte editieren.
Zwei Exporter veröffentlichen verschiedene Bestände in dieselben Dateien.
Ziel: Foundation als verbindlicher Bestand; zuerst Abgleich nach Key/Locale,
Wert, Status, Herkunft und Review. Konflikte sichtbar entscheiden. Keine
Übernahme allein nach Tabellen-ID oder pauschal letztem Zeitstempel.

### Priorität 1 — Quellcode schreiben

Legacy: generate-literal-diffs + ProjectTranslations::applyLatestDiffPatch,
translations:rewrite-literals (ohne --dry-run direkt schreibend),
translations:usage-decisions:apply --write.
Workbench: TranslationWorkbenchCodeUpdatePlanner / CodeUpdateApplier und
apply-suspicious-key-restores --write.
Ziel: gemeinsamer freigegebener Änderungsplan mit einheitlicher Vorprüfung;
zunächst Schreibpfade inventarisiert belassen, nicht unbesehen deaktivieren.

### Priorität 1 — Cleanup

Legacy Lang-Ballast, Stale-Dynamic-Cleanup und Statusnormalisierung versus
Workbench Obsolete-Erkennung, Cleanup-Reviews, excluded-paths und Export-Pruning.
Verschiedene Gültigkeitsregeln können dasselbe logische Ergebnis widersprüchlich
behandeln. Export-Schutz allein deckt Cleanup-Writer nicht vollständig ab.

### Priorität 2 — Scans/Importe/Discovery

Legacy audit-code + sync-audits und sync-dynamic-keys versus Workbench scan +
sync-foundation + Discovery/Runtime-Collector. Teilweise gemeinsame Scanner-
Infrastruktur, aber verschiedene Persistenz und Klassifikation.
Workbench scan und sync-foundation scannen sogar separat für Modell B und C.
Das ist ein möglicher Konsolidierungs-/Performancepunkt, kein Beleg, dass einer
der Aufrufe heute gefahrlos entfallen kann.

### Priorität 2 — Berichte/Reviews/History

Statistics und Legacy-Audits bilden nicht den neuen Gesamtbestand ab.
Legacy TranslationActivity / translation_audit_events, alte Package events und
Foundation TranslationWorkbenchTimelineRecorder / timeline_events bestehen
nebeneinander. Historie bewahren; Überführung braucht Herkunftskennzeichnung.
collect-timeline-chains und TimelineChainCollector liefern Diagnosekontext;
TW-Graph ist die Darstellungsschicht, kein fachlicher Writer.

## 5. EMPFOHLENE NÄCHSTE ARBEITSSCHRITTE (NOCH NICHT UMGESETZT)

1. Read-only Abgleich Legacy/Workbench nach Key + Locale, inklusive Sub-Languages:
   nur Legacy / nur Foundation / identisch / abweichend / Statuskonflikt.

2. Funktionen aus Management, Sub-Languages, Usage, Ballast einzeln zuordnen:
   in Workbench vorhanden / dort erweitern / als gemeinsame Verwaltung behalten.

3. Einen Schreibvertrag für Werte, Review, Historie, Export, Code-Apply bestimmen.
   Erst dann Legacy-Editoren schreibgeschützt setzen oder delegieren.

4. Einheitlichen Orchestrator festlegen; bisherige Commands nicht allein wegen
   ähnlicher Namen entfernen. Dry-Run/Write-Optionen müssen konsistent werden.

5. Isolierte Workflowtests für UI und CLI gegen denselben Vertrag; anschließend
   Legacy-Zugriffe auf Sprachdateien und redundante Persistenz schrittweise abbauen.

## 6. COMMAND-KATALOG (REGISTRIERTE BEFEHLE, QUELLEN UND OPTIONEN)

Die Beschreibungen sind die aktuellen Artisan-Beschreibungen; Optionen sind
wortgetreu aus der laufenden Registrierung übernommen. Sie sind kein Ersatz für
obige Einordnung der Schreibwirkung. Unterbefehle sind auch einzeln aufrufbar.

### `project:translations`

**Quelle:** [app/Console/Commands/ProjectTranslations.php](../app/Console/Commands/ProjectTranslations.php)

**Aufgabe:** Run translation workflow commands in sequence (audit, sync, diff, optional apply, export, final language and usage audits).

- `--paths`: Comma-separated scan paths for translations:generate-literal-diffs
- `--locales`: Comma-separated locale list for translations:export-lang-files
- `--apply-diffs`: Apply generated latest.patch automatically
- `--apply-usage-decisions`: Apply ready translation usage audit decisions to source files
- `--skip-export`: Skip translations:export-lang-files
- `--skip-audits`: Skip final translations:audit-lang and translations:audit-compare

### `translation:workbench`

**Quelle:** [packages/gunreip/laravel-translation-workbench/src/Console/RunTranslationWorkbench.php](../packages/gunreip/laravel-translation-workbench/src/Console/RunTranslationWorkbench.php)

**Aufgabe:** Run the full Translation Workbench scan/import/discovery/diagnostics pipeline.

- `--paths`: Comma-separated relative paths passed to scan and dynamic option discovery.
- `--source-locale`: Source locale used for importing existing translations and dynamic option values.
- `--dry-run`: Report only; do not write database rows.
- `--truncate`: Truncate all Translation Workbench tables before rebuilding.
- `--force-truncate`: Skip the interactive safety confirmation for --truncate.
- `--truncate-foundation`: Truncate only the new foundation tables before syncing foundation rows.
- `--mark-obsolete`: Mark previously seen but now missing entries and occurrences as obsolete/stale.
- `--skip-foundation`: Skip syncing scanner findings into the new foundation tables.
- `--skip-lang-values`: Skip importing existing lang file values into the separate lang values table.
- `--skip-import`: Skip importing existing source language values.
- `--skip-dynamic-options`: Skip discovering dynamic option values.
- `--skip-duplicates`: Skip duplicate candidate detection.
- `--skip-shared-key-candidates`: Skip detecting new findings that may reuse reviewed shared translation keys.
- `--skip-suspicious-keyed-additions`: Skip detecting code-level translation keys without Workbench/code-update provenance.
- `--skip-suspicious-key-restores`: Skip applying reviewed suspicious key restore decisions.
- `--skip-dynamic-classification`: Skip classifying dynamic value sources.
- `--skip-dynamic-resolution`: Skip resolving unknown dynamic sources from option discoveries.
- `--skip-dynamic-source-candidates`: Skip discovering reviewable dynamic source candidates.
- `--skip-lang-file-export`: Skip the final lang file export dry-run/report step.
- `--skip-lang-node-classification`: Skip classifying keys as lang-file leaf/container/conflict nodes.
- `--skip-key-inventory`: Skip established translation-key inventory aggregation.
- `--skip-code-update-plan`: Skip the final code update planning report step.
- `--skip-code-update-apply`: Skip the final code update apply dry-run and patch report step.
- `--write-lang-files`: Write exported lang files as the final pipeline step. Without this option the export step is a dry-run report only.
- `--pipeline-run-id`: Existing Translation Workbench pipeline run ID used for UI progress tracking.
- `--complete`: Run the full safe write workflow: mark obsolete, write lang files, apply safe code updates, then refresh reports.

### `translation-workbench:apply-code-updates`

**Quelle:** [packages/gunreip/laravel-translation-workbench/src/Console/ApplyTranslationWorkbenchCodeUpdates.php](../packages/gunreip/laravel-translation-workbench/src/Console/ApplyTranslationWorkbenchCodeUpdates.php)

**Aufgabe:** Apply safe reviewed Translation Workbench code replacements, dry-run by default.

- `--paths`: Comma-separated relative paths to limit the code update apply plan.
- `--limit`: Maximum number of reviewed findings to inspect before applying safe updates.
- `--write`: Actually update source files. Without this option the command only reports what would be changed.
- `--suppress-dry-run-warning`: Suppress the dry-run warning when the command is used as an orchestrated report refresh step.

### `translation-workbench:apply-suspicious-key-restores`

**Quelle:** [packages/gunreip/laravel-translation-workbench/src/Console/ApplySuspiciousKeyRestores.php](../packages/gunreip/laravel-translation-workbench/src/Console/ApplySuspiciousKeyRestores.php)

**Aufgabe:** Restore reviewed suspicious direct translation-key calls back to source literals, dry-run by default.

- `--paths`: Comma-separated relative paths to limit suspicious key restores.
- `--source-locale`: Source locale used to restore literal text from lang/* values.
- `--limit`: Maximum number of reviewed restore decisions to inspect.
- `--write`: Actually update source files. Without this option the command only reports what would be changed.
- `--only-apply`: Mark this run as an early pipeline apply step for already reviewed restore decisions.
- `--suppress-dry-run-warning`: Suppress the dry-run warning when the command is used as an orchestrated report refresh step.

### `translation-workbench:classify-dynamic-values`

**Quelle:** [packages/gunreip/laravel-translation-workbench/src/Console/ClassifyDynamicValues.php](../packages/gunreip/laravel-translation-workbench/src/Console/ClassifyDynamicValues.php)

**Aufgabe:** Classify dynamic translation findings into hardcoded/db and single/multi value sources.

- `--dry-run`: Report classifications without writing database rows.

### `translation-workbench:classify-lang-node-types`

**Quelle:** [packages/gunreip/laravel-translation-workbench/src/Console/ClassifyLangNodeTypes.php](../packages/gunreip/laravel-translation-workbench/src/Console/ClassifyLangNodeTypes.php)

**Aufgabe:** Classify Translation Workbench keys as lang-file leaf, container, conflict or unknown nodes.

- `--dry-run`: Report lang-node type changes without writing database rows.

### `translation-workbench:classify-timeline-event-noise`

**Quelle:** [packages/gunreip/laravel-translation-workbench/src/Console/ClassifyTimelineEventNoise.php](../packages/gunreip/laravel-translation-workbench/src/Console/ClassifyTimelineEventNoise.php)

**Aufgabe:** Detect and optionally classify historical high-volume timeline event noise without changing event_type.

- `--write`: Mark candidate events as dead_dev_event. Without this option the command only reports candidates.
- `--threshold`: Minimum dynamic_source_classification_changed count before a chain is considered noisy.
- `--chain-id`: Limit the analysis to one or more timeline-chain IDs.

### `translation-workbench:cleanup-excluded-paths`

**Quelle:** [packages/gunreip/laravel-translation-workbench/src/Console/CleanupExcludedTranslationWorkbenchPaths.php](../packages/gunreip/laravel-translation-workbench/src/Console/CleanupExcludedTranslationWorkbenchPaths.php)

**Aufgabe:** Report and optionally obsolete Translation Workbench rows that match configured exclude_paths.

- `--apply`: Mark currently active rows below configured exclude_paths as obsolete/stale.
- `--force`: Skip the interactive confirmation when --apply is used.
- `--samples`: Number of sample paths to print per affected table.

### `translation-workbench:collect-timeline-chains`

**Quelle:** [packages/gunreip/laravel-translation-workbench/src/Console/CollectTimelineChains.php](../packages/gunreip/laravel-translation-workbench/src/Console/CollectTimelineChains.php)

**Aufgabe:** Collect translation-chain context for a future extended timeline without changing existing timeline events.

- `--sync`: Write the current timeline-chain snapshot into the database.
- `--dry-run`: Report only; do not write timeline-chain database rows.

### `translation-workbench:detect-duplicates`

**Quelle:** [packages/gunreip/laravel-translation-workbench/src/Console/DetectDuplicateCandidates.php](../packages/gunreip/laravel-translation-workbench/src/Console/DetectDuplicateCandidates.php)

**Aufgabe:** Detect possible duplicate translation workbench entries from narrow diagnostic fingerprints.

- `--dry-run`: Report duplicate groups without writing candidate rows.

### `translation-workbench:detect-shared-key-candidates`

**Quelle:** [packages/gunreip/laravel-translation-workbench/src/Console/DetectSharedKeyCandidates.php](../packages/gunreip/laravel-translation-workbench/src/Console/DetectSharedKeyCandidates.php)

**Aufgabe:** Detect new findings whose literal matches an already bulk-reviewed shared translation key.

- `--dry-run`: Report possible shared-key candidates without writing database rows.

### `translation-workbench:detect-suspicious-keyed-additions`

**Quelle:** [packages/gunreip/laravel-translation-workbench/src/Console/DetectSuspiciousKeyedAdditions.php](../packages/gunreip/laravel-translation-workbench/src/Console/DetectSuspiciousKeyedAdditions.php)

**Aufgabe:** Detect code-level translation keys that appear without a reviewed Workbench or code-update provenance.

- `--paths`: Comma-separated relative paths to scan. Defaults to translation-workbench.paths.
- `--source-locale`: Source locale used to check whether the keyed call already exists in lang/* files.
- `--since-hours`: Only report newly seen keyed additions from the last N hours unless --all is used.
- `--all`: Report all suspicious keyed additions, including historical rows.
- `--skip-raw-data`: Write only suspicious-key results and summary, without the centralized raw scanner data.
- `--fail-on-suspicious`: Return a failing exit code when suspicious keyed additions are found.

### `translation-workbench:discover-dynamic-options`

**Quelle:** [packages/gunreip/laravel-translation-workbench/src/Console/DiscoverDynamicOptions.php](../packages/gunreip/laravel-translation-workbench/src/Console/DiscoverDynamicOptions.php)

**Aufgabe:** Discover possible dynamic multi option values from Blade foreach option lists and simple hard-coded component arrays.

- `--paths`: Comma-separated relative view paths to scan. Defaults to translation-workbench paths.
- `--source-locale`: Source locale used when writing discovered option labels as dynamic source values.
- `--sync`: Write discovered options into translation_workbench_values and translation_workbench_dynamic_values.
- `--dry-run`: Report only; do not write database rows.

### `translation-workbench:discover-dynamic-source-candidates`

**Quelle:** [packages/gunreip/laravel-translation-workbench/src/Console/DiscoverDynamicSourceCandidates.php](../packages/gunreip/laravel-translation-workbench/src/Console/DiscoverDynamicSourceCandidates.php)

**Aufgabe:** Discover reviewable source candidates for unresolved dynamic translation sources.

- `--dry-run`: Report dynamic source candidates without writing database rows.

### `translation-workbench:export-lang-files`

**Quelle:** [packages/gunreip/laravel-translation-workbench/src/Console/ExportTranslationWorkbenchLangFiles.php](../packages/gunreip/laravel-translation-workbench/src/Console/ExportTranslationWorkbenchLangFiles.php)

**Aufgabe:** Export reviewed Translation Workbench lang values into Laravel lang files.

- `--locale`: Locale(s) to export. Defaults to all locales with active Workbench lang values.
- `--namespace`: Namespace file(s) to export, for example ui or admin. Defaults to all namespaces.
- `--write`: Write lang files. Without this option the command only reports the export plan.
- `--suppress-dry-run-warning`: Suppress the dry-run warning when the command is used as an orchestrated report refresh step.

### `translation-workbench:import-existing`

**Quelle:** [packages/gunreip/laravel-translation-workbench/src/Console/ImportExistingTranslations.php](../packages/gunreip/laravel-translation-workbench/src/Console/ImportExistingTranslations.php)

**Aufgabe:** Import already existing source lang file values for keyed translation workbench entries.

- `--source-locale`: Source language locale directory to import as the translation basis.
- `--dry-run`: Report only; do not write database rows.

### `translation-workbench:import-lang-values`

**Quelle:** [packages/gunreip/laravel-translation-workbench/src/Console/ImportTranslationWorkbenchLangValues.php](../packages/gunreip/laravel-translation-workbench/src/Console/ImportTranslationWorkbenchLangValues.php)

**Aufgabe:** Import existing lang file values into the separate Translation Workbench lang values table.

- `--source-locale`: Source locale directory below lang/ to import.
- `--all-locales`: Import all locale directories below lang/, excluding lang/vendor.
- `--dry-run`: Read and report only; do not write lang value rows.
- `--truncate-lang-values`: Truncate imported lang value rows before importing.
- `--force-truncate`: Skip the interactive safety confirmation for --truncate-lang-values.

### `translation-workbench:inventory-keys`

**Quelle:** [packages/gunreip/laravel-translation-workbench/src/Console/InventoryTranslationWorkbenchKeys.php](../packages/gunreip/laravel-translation-workbench/src/Console/InventoryTranslationWorkbenchKeys.php)

**Aufgabe:** Build an inventory of established Translation Workbench keys and their code/value usage counts.

- `--sync`: Write the current established-key inventory into the database.
- `--dry-run`: Report only; do not write inventory database rows.
- `--source-locale`: Source locale used to split source and target lang values.

### `translation-workbench:plan-code-updates`

**Quelle:** [packages/gunreip/laravel-translation-workbench/src/Console/PlanTranslationWorkbenchCodeUpdates.php](../packages/gunreip/laravel-translation-workbench/src/Console/PlanTranslationWorkbenchCodeUpdates.php)

**Aufgabe:** Plan safe code replacements for reviewed Translation Workbench keys without changing source files.

- `--paths`: Comma-separated relative paths to limit the code update plan.
- `--limit`: Maximum number of reviewed findings to inspect.

### `translation-workbench:profile-findings`

**Quelle:** [packages/gunreip/laravel-translation-workbench/src/Console/ProfileTranslationWorkbenchFindings.php](../packages/gunreip/laravel-translation-workbench/src/Console/ProfileTranslationWorkbenchFindings.php)

**Aufgabe:** Profile common Translation Workbench findings table filter states.

- `--per-page`: Number of rows requested by each profiled paginator call.

### `translation-workbench:resolve-unknown-dynamic-sources`

**Quelle:** [packages/gunreip/laravel-translation-workbench/src/Console/ResolveUnknownDynamicSources.php](../packages/gunreip/laravel-translation-workbench/src/Console/ResolveUnknownDynamicSources.php)

**Aufgabe:** Resolve unknown dynamic sources by matching option discoveries and writing concrete dynamic source values.

- `--dry-run`: Report resolvable unknown dynamic sources without writing database rows.

### `translation-workbench:scan`

**Quelle:** [packages/gunreip/laravel-translation-workbench/src/Console/ScanTranslationWorkbench.php](../packages/gunreip/laravel-translation-workbench/src/Console/ScanTranslationWorkbench.php)

**Aufgabe:** Scan code for translation-capable literals and collect them in the translation workbench tables.

- `--paths`: Comma-separated relative paths to scan. Defaults to config translation-workbench.paths.
- `--dry-run`: Scan and report only; do not write database rows.
- `--truncate`: Truncate translation workbench tables before writing scanned entries.
- `--force-truncate`: Skip the interactive safety confirmation for --truncate.
- `--mark-obsolete`: Mark previously seen but now missing entries as obsolete.

### `translation-workbench:sync-foundation`

**Quelle:** [packages/gunreip/laravel-translation-workbench/src/Console/SyncTranslationWorkbenchFoundation.php](../packages/gunreip/laravel-translation-workbench/src/Console/SyncTranslationWorkbenchFoundation.php)

**Aufgabe:** Sync scanner findings into the new Translation Workbench foundation tables.

- `--paths`: Comma-separated relative paths to scan. Defaults to config translation-workbench.paths.
- `--dry-run`: Scan and report only; do not write foundation database rows.
- `--truncate-foundation`: Truncate only the new foundation tables before writing.
- `--force-truncate`: Skip the interactive safety confirmation for --truncate-foundation.
- `--mark-obsolete`: Mark active foundation rows as obsolete when they are no longer seen in the latest scan.

### `translations:audit-code`

**Quelle:** [app/Console/Commands/TranslationsAuditCode.php](../app/Console/Commands/TranslationsAuditCode.php)

**Aufgabe:** Audit translation calls in application code and write machine-readable reports.

### `translations:audit-compare`

**Quelle:** [app/Console/Commands/TranslationsAuditCompare.php](../app/Console/Commands/TranslationsAuditCompare.php)

**Aufgabe:** Compare translation code audit and language-file audit results.

### `translations:audit-duplicate-usage-literals`

**Quelle:** [app/Console/Commands/TranslationsAuditDuplicateUsageLiterals.php](../app/Console/Commands/TranslationsAuditDuplicateUsageLiterals.php)

**Aufgabe:** Audit duplicate source-language translation values used by multiple translation keys and list candidates for central ui.* translations.

- `--source-locale`: Source locale used for duplicate literal detection
- `--locales`: Comma-separated target locale list to include in the report, e.g. de,en
- `--min-keys`: Minimum number of different translation keys for the same source literal
- `--min-length`: Minimum trimmed source literal length
- `--exclude-ui`: Exclude values that already contain ui.* keys

### `translations:audit-frequent-usage-literals`

**Quelle:** [app/Console/Commands/TranslationsAuditFrequentUsageLiterals.php](../app/Console/Commands/TranslationsAuditFrequentUsageLiterals.php)

**Aufgabe:** Audit frequently used source-language translation values and show whether they are already centralized.

- `--source-locale`: Source locale used for usage literal reporting
- `--locales`: Comma-separated target locale list to include in the report, e.g. de,en
- `--min-usages`: Minimum usage count for a source literal
- `--min-length`: Minimum trimmed source literal length
- `--include-zero-usage`: Include source values without usage records

### `translations:audit-lang`

**Quelle:** [app/Console/Commands/TranslationsAuditLang.php](../app/Console/Commands/TranslationsAuditLang.php)

**Aufgabe:** Audit application language files and write machine-readable reports.

### `translations:audit-lang-ballast`

**Quelle:** [app/Console/Commands/TranslationsAuditLangBallast.php](../app/Console/Commands/TranslationsAuditLangBallast.php)

**Aufgabe:** Audit lang/* translation entries that no longer match the current translation database state.

### `translations:audit-sub-language-redundancy`

**Quelle:** [app/Console/Commands/TranslationsAuditSubLanguageRedundancy.php](../app/Console/Commands/TranslationsAuditSubLanguageRedundancy.php)

**Aufgabe:** Mark sub-language values as base-duplicates when identical to their base locale values.

- `--locales`: Comma-separated sub-language locales override (e.g. de-at,de-ch)
- `--dry-run`: Show what would be changed without persisting

### `translations:backfill-audit-discovered-events`

**Quelle:** [app/Console/Commands/BackfillTranslationAuditDiscoveredEvents.php](../app/Console/Commands/BackfillTranslationAuditDiscoveredEvents.php)

**Aufgabe:** Create missing discovered baseline events for existing translation keys.

- `--dry-run`: Report missing baselines without writing them

### `translations:backfill-native-text-from-values`

**Quelle:** [app/Console/Commands/TranslationsBackfillNativeTextFromValues.php](../app/Console/Commands/TranslationsBackfillNativeTextFromValues.php)

**Aufgabe:** Fill missing translation_keys.native_text from available translation_values entries.

- `--prefer-locales`: Comma-separated locale priority list for selecting source values
- `--limit`: Maximum number of keys to backfill (0 = no limit)
- `--dry-run`: Preview updates without writing to the database

### `translations:cleanup-stale-dynamic-keys`

**Quelle:** [app/Console/Commands/CleanupStaleDynamicTranslationKeys.php](../app/Console/Commands/CleanupStaleDynamicTranslationKeys.php)

**Aufgabe:** Review legacy stale dynamic translation keys and either reactivate or archive them with audit events.

- `--apply`: Persist the cleanup decisions. Without this option the command only reports what would happen.

### `translations:ensure-lang-directories`

**Quelle:** [app/Console/Commands/TranslationsEnsureLangDirectories.php](../app/Console/Commands/TranslationsEnsureLangDirectories.php)

**Aufgabe:** Ensure lang/{locale} directories exist for translation-enabled languages.

- `--locales`: Comma-separated locale list override (e.g. de,en,fr)
- `--dry-run`: Show what would be created without writing to disk

### `translations:export-lang-files`

**Quelle:** [app/Console/Commands/TranslationsExportLangFiles.php](../app/Console/Commands/TranslationsExportLangFiles.php)

**Aufgabe:** Export translation values from database to lang/{locale}/*.php and lang/{locale}.json files.

- `--locales`: Comma-separated locale list override (e.g. de,en,fr)
- `--dry-run`: Show what would be written without writing files

### `translations:generate-literal-diffs`

**Quelle:** [app/Console/Commands/TranslationsGenerateLiteralDiffs.php](../app/Console/Commands/TranslationsGenerateLiteralDiffs.php)

**Aufgabe:** Generate runnable patch diffs for replacing literal translation calls with translation keys.

- `--paths`: Comma-separated relative paths to scan (default: translation-workbench.paths)
- `--output-dir`: Relative output directory for generated patch files
- `--allow-suggested`: Also use suggested_key when key is empty
- `--require-complete-values`: Require non-empty translation values for all enabled locales (default: true)
- `--no-require-complete-values`: Allow mappings even if some locale values are missing
- `--write-per-file-patches`: Also write individual per-file patch files

### `translations:lang-ballast:apply`

**Quelle:** [app/Console/Commands/TranslationsApplyLangBallastDecisions.php](../app/Console/Commands/TranslationsApplyLangBallastDecisions.php)

**Aufgabe:** Preview or apply approved lang ballast decisions to lang/* files.

- `--write`: Actually modify lang files. Without --write only preview files are generated.

### `translations:normalize-keyed-native-statuses`

**Quelle:** [app/Console/Commands/TranslationsNormalizeKeyedNativeStatuses.php](../app/Console/Commands/TranslationsNormalizeKeyedNativeStatuses.php)

**Aufgabe:** Normalize legacy translation_keys rows with status native and a concrete key to status missing + classification key.

- `--limit`: Maximum number of rows to normalize (0 = no limit)
- `--dry-run`: Preview affected rows without writing changes

### `translations:rewrite-literals`

**Quelle:** [app/Console/Commands/TranslationsRewriteLiterals.php](../app/Console/Commands/TranslationsRewriteLiterals.php)

**Aufgabe:** Rewrite translation literal calls to translation keys using translation_keys.native_text mappings.

- `--dry-run`: Show replacements without writing files
- `--paths`: Comma-separated relative paths to scan (default: app,routes,resources/views)

### `translations:sync-audits`

**Quelle:** [app/Console/Commands/SyncTranslationAudits.php](../app/Console/Commands/SyncTranslationAudits.php)

**Aufgabe:** Sync translation audit JSON files into translation management tables.

### `translations:sync-dynamic-keys`

**Quelle:** [app/Console/Commands/SyncDynamicTranslationKeys.php](../app/Console/Commands/SyncDynamicTranslationKeys.php)

**Aufgabe:** Scan dynamic translation candidates and dynamic_label usages, then sync them into translation review tables.

- `--paths`: Comma-separated relative paths to scan (default: translation-workbench.paths)
- `--output-dir`: Relative output directory for generated audit files
- `--dry-run`: Write audit files only; do not sync database rows

### `translations:usage-decisions:apply`

**Quelle:** [app/Console/Commands/ApplyTranslationUsageAuditDecisions.php](../app/Console/Commands/ApplyTranslationUsageAuditDecisions.php)

**Aufgabe:** Apply ready translation usage audit decisions to source files.

- `--write`: Write changes to files. Defaults to dry-run.
- `--sample`: Maximum number of items written to sample report files.

### `translations:usage-decisions:preview`

**Quelle:** [app/Console/Commands/PreviewTranslationUsageAuditDecisions.php](../app/Console/Commands/PreviewTranslationUsageAuditDecisions.php)

**Aufgabe:** Create preview files for ready translation usage audit decisions.

Erfasst: 43 registrierte Translation-Commands.

## Nachprüfung nach Writer-Auslagerung — 13.10.2026

Statische Nachprüfung des aktuellen Arbeitsbaums, ergänzt durch isolierte Tests.
Keine erneute Datenbestandszählung und keine echte Scan-/Import-/Exportpipeline ausgeführt.
Die Zahlen im separaten Vergleichsbericht vom 13.10. sind weiterhin eine Momentaufnahme.

### Schreibwege und Verantwortung

| Einstieg | Aktueller Schreibweg | Noch außerhalb des Writers / Voraussetzung für Umstellung |
|---|---|---|
| Workbench: skalare und dynamische Werte | `TranslationWorkbenchTranslationWriter` → Foundation-Sprachwerte bzw. dynamische Schlüsselwerte + Timeline | Livewire löst Finding/Key, zulässige Sprachvarianten und dynamische Optionen auf. Keine zentrale Benutzerautorisierung oder Revisionsprüfung im Writer. |
| Workbench: einzelne Schlüsselreviews | `TranslationWorkbenchKeyReviewWriter` → Keys, Reviews, ggf. Rekey der Sprachwerte, Timeline | Aktionsberechtigung im fachlichen Sinn, Containerprüfung und Eingabevalidierung teilweise in Livewire. Das ist keine Benutzer-/Rollenprüfung. |
| Workbench: Bulk Equalize | `TranslationWorkbenchBulkKeyWriter` → Keys/Relationen, Reviews, Timeline | Gemeinsame Auswahl- und Zielregeln vorhanden; kein vollständiges Paralleländerungs-/Replay-Protokoll. |
| Workbench: Scalar → Array-Pfad | `TranslationWorkbenchScalarTransformWriter` → Key-Pfade, Sprachwerte, Reviews, Timeline | Geeignete Kandidaten werden in Livewire bestimmt; Writer prüft Zielkonflikte erneut. Keine Konvertierung des Textinhalts in ein Array. |
| Workbench: Konflikt / Provenienz / Cleanup / Quellenlink | Jeweiliger Conflict-, Provenance-, Cleanup- bzw. DynamicSourceLinkWriter | Livewire bestimmt Kontext und erneuert Berichte. Das Speichern einer Entscheidung führt nicht automatisch deren vorgeschlagene Quellcodekorrektur aus. |
| Workbench: Pipeline starten | `startCompletePipelineRun` → RunTracker, Hintergrundkommando `translation:workbench --complete`; Startfehler setzt Runstatus | Weiterhin Orchestrierung in Livewire; keine redaktionelle Werteänderung im Modal. Dateischreibprüfungen sind keine Benutzerautorisierung. |
| Management (`TranslationList`) | Weiterhin Legacy `TranslationKey`/`TranslationValue`, Legacy-Audit und Activity | Kein neuer Foundation-Writer eingebunden; Lesen und Schreiben müssen zusammen migriert werden. |
| Sub-Languages (`TranslationSubLanguages`) | Weiterhin Legacy-Werte und `is_base_duplicate`, Activity | Duplicate-/Override- und Fallback-Semantik muss in Foundation ausdrücklich abgebildet und getestet werden. |
| Artisan Legacy | `project:translations` orchestriert Legacy-Scanner/Audits/Export; Export ohne `--skip-export` weiterhin möglich | Eigener Schreibweg bleibt aktiv. Export-/Importverantwortung vor einem Cutover festlegen. |
| Artisan Workbench | `translation:workbench` orchestriert Foundation und Teile des älteren Package-Modells; `--write-lang-files`/`--complete` erlauben Dateischreiben | Auslagerung der UI-Writer hat Commands nicht auf eine neue zentrale Autorisierung oder alleinige Datenhoheit umgestellt. |

In `TranslationWorkbenchEntries` sind die geprüften redaktionellen Speicheraktionen jetzt Delegationen.
Die direkte Speicherung des Pipeline-Fehlerstatus bleibt dort bewusst bestehen.
Scanner, Importer, Exporter und andere UI-Klassen sind damit nicht automatisch vereinheitlicht.

### Berechtigungsbefund und gezielte Korrektur

- Die Routen `admin.translation-workbench`, `admin.translations` und
  `admin.translation-sub-languages` liegen hinter `auth`, `verified` und `role:Admin|Super-Admin`.
- In den geprüften redaktionellen Actions von Workbench, Management und Sub-Languages
  findet sich keine eigene `authorize`-/Gate-/Rollenprüfung. Die Writer selbst prüfen
  ebenfalls keine Benutzerrechte; `Auth::id()` in der Historie ist keine Autorisierung.
- Die installierte Livewire-Version übernimmt nur registrierte persistente Middleware.
  Spaties `RoleMiddleware` war dort nicht registriert; die Tests reproduzierten die fehlende
  Rollenprüfung in der für Folgeanfragen ausgewählten Middleware.
- `AppServiceProvider` registriert nun `RoleMiddleware` als persistent. Das erzwingt die
  **bereits vorhandenen** Rollenanforderungen der ursprünglichen Route auch bei Updates;
  es führt keine neue Rolle ein. Dies gilt für alle entsprechend geschützten Livewire-Routen.
- Tests prüfen die Middleware-Auswahl für die drei Routen und die Ausführung der ausgewählten
  Rollen-Middleware mit Admin/Super-Admin sowie Rollenentzug. Kein vollständiger Browser-/HTTP-
  Snapshot-Test und keine Behauptung einer bereits zentralen Service-Autorisierung.
- `verified` ist ebenfalls nicht in Livewires Standardliste. Das aktuelle `App\Models\User`
  implementiert jedoch nicht `MustVerifyEmail`; daraus wird hier keine neue Verifizierungspflicht abgeleitet.

### Verhaltensunterschiede vor Management-/Sub-Languages-Cutover

| Aspekt | Management | Sub-Languages | Workbench/Foundation |
|---|---|---|---|
| Textnormalisierung | Originalstring bleibt gespeichert; Trim nur für Leer-/Statusprüfung | Trim beim Speichern | Trim, leer wird `null` |
| Status bei Inhalt / leer | `ok` / `missing` | `ok` / `missing` | `active` / `missing` |
| Unverändertes Speichern | `updateOrCreate` erneuert Review-Metadaten auch bei unverändertem Wert | Unveränderte Werte werden übersprungen | Writer vergleicht Werte/Attribute; unveränderte Operation ohne neues Werteereignis |
| Variantenentscheidung | Legacy-Datenmodell | `markSubLanguageAsDuplicate` / `keepSubLanguageAsOverride` setzen `is_base_duplicate` | Kein entsprechender redaktioneller Duplicate-/Override-Writer; keine automatische Gleichsetzung mit Legacy |
| Historie | Werte-Audit je Änderung plus Activity | Activity mit Status/has_value, kein äquivalentes Werte-Audit in `saveTranslationEntryEdit` | Foundation-Timeline mit Werteänderungen |
| Atomarität der geprüften Speicheraktion | Schleife/Audits ohne umfassende gemeinsame DB-Transaktion | Werte/Activity ohne gemeinsame DB-Transaktion | Ausgelagerte Writer speichern ihre Änderungen und Historie gemeinsam |
| Eingabeprüfung | `nullable|string|max:10000` für aufgelöste Editor-Sprachen | Gleiche Textregeln für `editingTranslationLocales` | Auswahl-/Kontextregeln überwiegend in Livewire; kein identischer zentraler Text-/Sprachenvertrag |

### Nächste verbindliche Reihenfolge

1. Zentralen Aktions-/Autorisierungsvertrag für UI und vertrauenswürdige CLI-Aufrufe festlegen;
   keine implizite Freigabe nur wegen `runningInConsole()` und keine neuen Rollen ohne Abstimmung.
2. Die bestehenden Editorregeln (Sprachen, Werte, Key/Finding-Bezug) in die gemeinsame
   Anwendungsschicht überführen und dort auch direkte Aufrufe testen.
3. Variantenentscheidung/Fallback, Leerwerte und Historie fachlich abgleichen; Unterschiede
   ausdrücklich entscheiden, nicht beim Austauschen eines Writers nebenbei verändern.
4. Datenkonflikte anhand des Vergleichsberichts auflösen; keine automatische Gewinnerwahl.
5. Erst dann Management und Sub-Languages jeweils lesend **und** schreibend auf Foundation
   umstellen. CLI-Importe/Exporte für denselben Umfang mitziehen oder sperren, sodass kein
   dauerhafter Parallelbetrieb zweier maßgeblicher Bestände entsteht.

## Konkretisierter Aktions- und Berechtigungsvertrag — 13.10.2026

**SOLL-Vertrag für die weitere Umsetzung.** Die unten beschriebenen Aktionskontexte,
Prüfungen und Systemfreigaben sind noch nicht vollständig implementiert. Die bereits
aktive Rollenprüfung der Routen und Livewire-Folgeanfragen bleibt davon getrennt.
Keine neuen Rollen, Commands oder Command-Optionen werden mit dieser Festlegung eingeführt.

### Drei Aufrufer, eine Prüfung pro Aktion

1. **Interaktiver Benutzer:** aktuelle authentifizierte Benutzer-ID. Für die Verwaltung
   weiterhin `Admin|Super-Admin`; die Anwendungsschicht prüft diese Rolle vor jeder
   Mutation erneut. Eine im Browser übergebene Benutzer-ID wird nicht als Identität übernommen.
2. **Benutzerinitiierter Hintergrundlauf:** Initiator wird beim Anlegen des Runs serverseitig
   gespeichert. Der Worker lädt Benutzer und aktuelle Rolle vor jedem schreibenden Schritt
   erneut. Fehlender/gelöschter Benutzer oder entzogene Rolle sperrt den Schritt; kein
   automatischer Wechsel auf einen Systemakteur. Der Run-Identifier allein ist keine Freigabe.
3. **Expliziter Systemlauf:** serverseitig freigegebener Command bzw. Job mit benannten
   Operationen und begrenztem Umfang. Kein pauschales `runningInConsole()`-Bypass und kein
   aus Request-Parametern erzeugbarer Systemkontext. Automatische Beobachtungen/Importe
   dürfen keine menschliche Review-Entscheidung vortäuschen.

Ein direkt aus der Konsole gestarteter neuer redaktioneller Aufruf braucht eine
explizite Benutzerzuordnung und dieselbe Rollenprüfung. Die Shell ist dabei eine
vertrauenswürdige Betriebsgrenze, keine Web-Authentifizierung. Es gibt derzeit keinen
neuen redaktionellen CLI-Command; seine konkrete Oberfläche wird bei dessen Einführung festgelegt.

### Aktionsmatrix

| Aktion | Benutzer / initiierter Run | Expliziter Systemlauf | Zusätzliche fachliche und Schreibvoraussetzung |
|---|---|---|---|
| W01 Beobachtungen erfassen | Admin/Super-Admin, passender Scanumfang | Nur dafür freigegebener Scanner/Collector | Beobachtungen ändern; keine redaktionellen Werte/Reviews überschreiben. Laufender Runtime-Collector erhält nur Beobachtungsrechte, nicht allgemeine Workbench-Schreibrechte. |
| W02 Importieren / abgleichen | Admin/Super-Admin, begrenzte Quelle und Locales | Nur freigegebener Importer | Geänderte redaktionelle Werte als Konflikt behandeln; fehlende Dateieinträge sind keine Löschfreigabe. |
| W03 Werte / Zuordnung bearbeiten | Admin/Super-Admin | Keine automatische redaktionelle Entscheidung | Key/Finding-Bezug, erlaubte Locales/Optionen, Text-/Zielregeln und erwartete Revision prüfen. Alle betroffenen Werte/Reviews/Timeline-Ereignisse gemeinsam speichern. |
| W04 Review entscheiden | Admin/Super-Admin | Keine automatische menschliche Freigabe | Entscheidung muss für Kontext zulässig sein; Bezug auf die konkret geprüfte Revision/den Plan. Herkunftsreview führt nicht automatisch eine Literal-Restaurierung aus. |
| W05 Sprachdateien veröffentlichen | Admin/Super-Admin und ausdrücklicher Schreibauftrag | Nur für Veröffentlichung freigegebener Lauf mit gültigem Plan | Planumfang, Freigaben, Eingabehashes, Zielpfade und tatsächlicher Dateidiff prüfen. Export-Preview veröffentlicht nichts. |
| W06 Quellcode ändern | Admin/Super-Admin und ausdrücklicher Schreibauftrag | Nur dafür freigegebener Lauf mit gültigem Plan | Konkrete Dateien/Ersetzungen und deren Hashes prüfen; Syntaxprüfung; keine Ausweitung auf ungeprüfte Dateien. |
| W07a Cleanup entscheiden / obsolete markieren | Admin/Super-Admin | Nur Beobachtungsstatus im W01-Vertrag, keine ersetzte Benutzerentscheidung | Ausgewählte Werte und aktuelle Nutzungen/Abhängigkeiten prüfen. `obsolete` ist nicht gleich physisch gelöscht. |
| W07b Physisch entfernen / aus Dateien entfernen | Admin/Super-Admin und ausdrücklicher Cleanup-Auftrag | Nur ausdrücklich freigegebener Cleanup-Lauf | Aktuelle Nutzungen, Variantenabhängigkeiten und Cleanup-Freigabe erneut prüfen. Historie erhalten; ein Teilscan reicht nicht als Löschgrundlage. |

Die Benutzerrollen gelten damit zunächst für alle redaktionellen Aktionen gleichermaßen.
Eine spätere feinere Verteilung braucht eine eigene Entscheidung, keine still eingeführten
`editor`-/`reviewer`-Rollen. Ein Systemlauf mit Publish-Berechtigung darf deshalb noch immer
keine neue W04-Entscheidung erzeugen.

### Mindestinhalt des serverseitigen Aktionskontexts

- **Akteur:** Benutzer-ID oder explizite Systemidentität; bei Runs zusätzlich Initiator,
  Run-ID und auslösender Command/Job. Systemhistorie behält `reviewed_by_user_id = null`;
  keine erfundene Benutzer-ID.
- **Operation:** eine konkrete Operation aus der Matrix. Eine allgemeine Freigabe
  „Translations schreiben“ reicht für Dateiänderungen/Cleanup nicht aus.
- **Umfang:** serverseitig aufgelöste Key-/Finding-IDs, Locales, dynamische Optionen bzw.
  Plan- und Dateiumfang. Client-Arrays werden validiert, nicht als Berechtigung übernommen.
- **Revision:** erwarteter Datenstand für redaktionelle Änderungen; für Veröffentlichung
  und Cleanup zusätzlich Planrevision und relevante Dateihashes.
- **Absicht:** Beobachtung, Preview, DB-Änderung oder Dateiveröffentlichung. Ein Run kann
  unterschiedliche Schritte enthalten; „dry-run“ eines Exporters macht vorherige
  Scanner-/Importer-DB-Schritte nicht automatisch schreibfrei.
- **Nachverfolgbarkeit:** Operations-ID für Historie, Wiederholung und Fehlerzuordnung.
  Wiederholung derselben Operations-ID mit anderem Inhalt wird abgewiesen; keine neue
  Entscheidung durch bloße Wiederholung eines Transportaufrufs.

### Reihenfolge an der gemeinsamen Anwendungsgrenze

1. Akteur aus vertrauenswürdigem Kontext ermitteln und aktuelle Rollen/Systemfreigabe prüfen.
2. Operation und Umfang auflösen; Zugehörigkeit von Key, Finding, Locale und Option prüfen.
3. Fachliche Regeln, erwartete Revision und gegebenenfalls Plan-/Dateihashes prüfen.
4. Ausdrückliche Schreibabsicht für den konkreten Effekt prüfen. Dateirechte sind eine
   zusätzliche technische Voraussetzung, keine Benutzerberechtigung.
5. Innerhalb der Transaktion bzw. des kontrollierten Publish-Ablaufs aktuelle Zustände
   erneut prüfen; Mutation und Historie konsistent speichern.
6. Ergebnis zurückgeben; UI-Toast/Modal und CLI-Ausgabe bleiben beim jeweiligen Aufrufer.
   Berichtserneuerung nach einem Commit ist ein separater Effekt: schlägt sie fehl,
   wird „gespeichert, Bericht nicht erneuert“ gemeldet, nicht „Speichern fehlgeschlagen“.

Die neue Anwendungsgrenze wird vor die bestehenden Writer gesetzt. Sämtliche produktiven
UI-/CLI-Aufrufer müssen durch sie laufen, bevor der Schutz als umgesetzt gelten kann.
Die bisherigen Writer allein bilden noch keine Benutzerautorisierungsgrenze.

### Ablehnung und überprüfbare Abnahmekriterien

| Fall | Erwartetes Ergebnis |
|---|---|
| Gast, falsche/entzogene Rolle, gelöschter Initiator | Keine Mutation; UI verweigert Zugriff, CLI beendet sich erfolglos. Kein System-Fallback. |
| Systemlauf verlangt nicht freigegebene Operation | Ablehnung vor Mutation; Herkunft und angeforderte Operation im Fehler nachvollziehbar. |
| Unzulässige Locale, fremder Finding-/Key-Bezug, unbekannte Option | Validierungsfehler; keine Teiländerung. |
| Datensatz/Plan seit Öffnen des Editors geändert | Konflikt mit Hinweis zum Neuladen; keine Überschreibung mit veraltetem Stand. |
| Publish ohne Schreibabsicht oder mit geändertem Dateihash | Keine Dateiänderung; Preview darf weiterhin einen Bericht liefern. |
| Neuer aktiver Gebrauch nach Cleanup-Review | Entfernung wird blockiert; vorhandene Inhalte und Historie bleiben erhalten. |
| Historienfehler bei DB-Aktion | Werte/Zuordnungen und Review werden gemeinsam zurückgerollt. |
| Dateifehler nach teilweise abgeschlossener Veröffentlichung | Tatsächlich abgeschlossene Schritte/Restzustand melden; keine falsche Erfolgsmeldung oder behauptete globale Transaktion. |
| Derselbe Aktionskontext über UI und CLI | Gleiche Autorisierung, Validierung und fachliche Wirkung. |

Nächster Umsetzungsschritt: W03 für skalare Werte als begrenzten Einstieg durch diese
Anwendungsgrenze führen, zuerst Workbench; Gast/falsche Rolle, gültige Admin-Rolle,
zulässige Locales und Key/Finding-Zuordnung direkt testen. Management, Sub-Languages
und bestehende Commands bleiben bis zu ihrem eigenen geprüften Cutover auf dem aktuellen
Pfad. Während der schrittweisen Einführung wird kein vollständiger Schutz aller Writer behauptet.

### Umgesetzt: erste W03-Anwendungsgrenze für skalare Workbench-Werte

`TranslationWorkbenchScalarEditor` autorisiert anhand des aktuell authentifizierten
Benutzers (`Admin|Super-Admin`, kein Console-Bypass). Eine gegebenenfalls bereits geladene
Rollenrelation wird vor der Prüfung verworfen. Der Dienst löst Key und Finding erneut auf,
verlangt deren aktive Relation und einen vorhandenen Übersetzungsschlüssel. Er validiert
String-/Nullwerte und die erlaubten Locales, bevor der bestehende Writer speichert.
`TranslationWorkbenchEditorLocales` enthält die bisherige Sprachermittlung, gemeinsam
verwendet von UI und Anwendungsgrenze.

Die Workbench-Aktion `saveTranslationValue` nutzt diese Grenze, einschließlich des
bestehenden delegierenden `saveDynamicTranslationValue`-Einstiegs. Der separate
Multi-Options-Editor ist damit noch nicht autorisiert. Angeforderte unzulässige Varianten
werden jetzt mit Validierungsfehler abgewiesen statt still übersprungen; keine Teilwerte
werden gespeichert. Textlängenlimits aus Legacy wurden nicht nebenbei übernommen.

Tests verwenden eine isolierte Datenbank und einen expliziten Test-Admin (Rollenprüfung
mit Testdouble). Sie prüfen Guest/Non-Admin, Rollenentzug, inaktive/fremde Zuordnungen,
Variantendeaktivierung, ungültige Werttypen sowie die bisherigen Speicher-/Rollbackfälle.
Dies ist kein vollständiger Authentifizierungs- oder Browsertest.

Offen bleiben erwartete Revisionen, autorisierte Hintergrund-/Systemkontexte und die
anderen Actions. `TranslationWorkbenchTranslationWriter` bleibt ein interner
Persistenzdienst und ist allein keine Autorisierungsgrenze. Vor vollständiger Umstellung
müssen sämtliche produktiven Aufrufer der jeweiligen Operation über die Anwendungsgrenze
laufen. Nächster begrenzter Schritt: dieselbe Prüfung für dynamische Multi-Options-Werte
und individuelle Quellwertkorrekturen, ergänzt um zulässige Option-Keys.

### Umgesetzt: W03-Anwendungsgrenze für dynamische Optionen

`TranslationWorkbenchDynamicEditor` schützt Multi-Options-Speichern und einzelne
Quellwertkorrekturen. Die Rollen- und aktive Zuordnungsprüfung liegt gemeinsam für
ScalarEditor und DynamicEditor in `TranslationWorkbenchEditorAccess`.
`TranslationWorkbenchDynamicOptions` übernimmt unverändert die bisherige Ermittlung
für Vorschau und Speicherung: gespeicherte Werte in Quell-/Zielsprache sowie aktive
Quelloptionen einschließlich bestätigter Runtime-Verknüpfungen. Bereits gespeicherte
Optionen bleiben auch bei wegfallender Quelle sichtbar/bearbeitbar.

Der Dienst bestimmt Sprachen, Option-Key, native Label und Herkunft serverseitig.
Der Client liefert Feldwerte; `dynamicMultiValueKeyMap` ist keine Schreibautorität.
Unbekannte Feld-Keys oder falsche Werttypen lehnen jetzt die komplette Aktion ab,
statt solche Felder still zu ignorieren. Rücknahme bei Historienfehlern sowie bisheriges
Verhalten für Leerwerte und unveränderte Eingaben bleiben erhalten.

Tests decken UI und direkte Dienstaufrufe für Gast/falsche Rolle, inaktive Zuordnung,
unbekannte Felder, manipulierte Feldzuordnungen, ungültige Werte und gelöste Runtime-Links
ab. Die vorhandenen Werte-/Rollbacktests laufen gegen die geschützten UI-Aktionen weiter.
Keine Revisionsprüfung, Systemakteur-Freigabe oder Autorisierung aller Review-/Cleanup-
Writer wird damit behauptet. Auch dieser Schritt stellt Legacy noch nicht um.

### Umgesetzt: Rollen- und Zuordnungsprüfung für einzelne Schlüsselreviews

`TranslationWorkbenchKeyReviewEditor` ist nun die Anwendungsgrenze für manuelles
Umbenennen, Vorschlagsannahme, UI-/Dynamic-Bestätigung/Ablehnung, Kandidatenklassifikation
und Single-/Multi-Entscheidungen. Alle zehn öffentlichen Livewire-Aktionen prüfen die
Rolle vor Verarbeitung; der Dienst prüft sie zusätzlich für direkte Aufrufer. Die aktive
Key/Finding-Relation und Datensätze werden vor Schreiben erneut aufgelöst.

Manuelle Zielschlüssel werden auch im Dienst mit dem bisherigen Format und der bestehenden
Container-Sperre geprüft. Strukturattribute werden daraus serverseitig neu aufgebaut.
Vorschlagsannahme verwendet den aktuellen Key-/Finding-Vorschlag; Review-Metadaten kommen
vom aktuellen Finding. Die bisherige unterschiedliche Behandlung von manuellem Rekey und
Vorschlagsannahme bleibt erhalten. Review-Typ und Entscheidung müssen zusammenpassen.

Bewusste Grenze dieses Schritts: Klassifikationsattribute werden weiterhin durch die
bestehenden vertrauenswürdigen Livewire-Aktionen aufgebaut, nicht als freie Browserdaten
übergeben. Die Detailregeln für Klassifikation sind noch nicht vollständig in der
Anwendungsschicht zentralisiert. Revisionsschutz bleibt offen. Bulk, Konflikt-/Provenienz-,
Cleanup- und Quellenlink-Reviews sind noch nicht durch diese neue Anwendungsgrenze geschützt;
sie brauchen wegen anderer bzw. optionaler Datensatzbezüge getrennte Prüfungen.

Tests decken alle zehn UI-Einstiege bei falscher Rolle, direkte Dienstaufrufe als Gast,
inaktive Zuordnungen, unbekannte Entscheidungen, ungültige/Container-Ziele und die aktuelle
Vorschlagsauflösung ab. Vorhandene Klassifikations-, Rekey- und Rollbacktests bleiben aktiv.

## Umgesetzt: Rollenprüfung für Bulk Equalize und Scalar → Array

Die beiden Bestätigungsaktionen in `TranslationWorkbenchEntries` und die schreibenden Methoden von `TranslationWorkbenchBulkKeyWriter` und `TranslationWorkbenchScalarTransformWriter` verwenden jetzt `TranslationWorkbenchEditorAccess::authorize()`. Auch direkte Service-Aufrufe verlangen die aktuelle Rolle Admin oder Super-Admin; Gäste und Benutzer nach Rollenentzug werden vor dem Schreiben abgewiesen.

Bulk Equalize lädt seinen Auswahlkontext weiterhin innerhalb der Transaktion erneut. Die bestehenden Möglichkeiten zum Anlegen fehlender Schlüssel und Reaktivieren von Zuordnungen bleiben erhalten; die Einzelreview-Prüfung auf eine bereits aktive Zuordnung wird hier ausdrücklich nicht angewendet.

Scalar → Array erhält weiterhin serverseitig aufgelöste Kandidaten aus der Workbench. Die bestehenden Zielschlüssel-, Duplikat- und Konfliktprüfungen sowie atomare Historisierung bleiben erhalten. Die vollständige Kandidatenauflösung ist noch keine eigenständige Anwendungsgrenze. Optimistische Versionsprüfungen und ein Systemakteur-Protokoll sind weiterhin offen.

Die neuen Regressionstests prüfen beide UI-Aktionen und direkte Schreibaufrufe für Gäste und Benutzer ohne aktuelle Admin-Rolle sowie unveränderte Schlüssel, Zuordnungen, Werte, Reviews und Timeline-Ereignisse. Bestehende Erfolgstests prüfen weiterhin die fachlichen Sonderfälle. Es werden keine produktiven Daten oder Sprach-/Quelldateien veröffentlicht.

## Umgesetzt: Rollenprüfung für Konflikt-, Cleanup- und Provenance-Reviews

Die vier Livewire-Schreibaktionen `saveCodeUpdateConflictReview`, `saveLangCleanupReview`, `confirmObsoleteSourceValue` und `reviewSuspiciousKeyProvenance` prüfen vor der Verarbeitung die aktuelle Admin-/Super-Admin-Rolle über `TranslationWorkbenchEditorAccess::authorize()`. Dieselbe Prüfung erfolgt beim direkten Aufruf von `TranslationWorkbenchConflictReviewWriter::save`, `TranslationWorkbenchCleanupReviewWriter::save/markSourceObsolete` und `TranslationWorkbenchProvenanceReviewWriter::save`, auch vor möglichen No-op-Rückgaben.

Die bestehenden Entscheidungsregeln, Historisierung und Statusänderungen bleiben erhalten. Provenance-Reviews dürfen weiterhin ohne verknüpften Schlüssel/Finding gespeichert werden; Cleanup und einzelne Quellwerte setzen ebenfalls keine aktive Schlüssel-/Finding-Zuordnung voraus. Eine allgemeine Einzelreview-Zuordnungsprüfung wurde deshalb nicht hinzugefügt.

Dieser Schritt sichert die Rolle ab. Vollständige fachliche Kontextvalidierung an der Service-Grenze und Versionsprüfungen sind damit noch nicht umgesetzt. Die Verknüpfung dynamischer Quellen bleibt als nächster Schreibbereich zu prüfen.

Regressionstests decken alle vier Aktionen jeweils für Gäste und Benutzer nach Rollenverlust ab, sowohl über Livewire als auch direkt am Service. Sie prüfen unveränderte Schlüssel, Zuordnungen, Inventar, Werte, Reviews, Timeline, Quelldatei und Provenance-Report sowie ausbleibende Artisan-Aufrufe. Die Tests laufen ausschließlich mit isolierten Daten und temporären Dateien.

## Umgesetzt: Rollenprüfung für dynamische Quellenverknüpfungen

`confirmDynamicSourceLink` und `unlinkDynamicSourceLink` in Livewire sowie `TranslationWorkbenchDynamicSourceLinkWriter::confirm/unlink` prüfen jetzt vor der Verarbeitung die aktuelle Rolle Admin oder Super-Admin. Direkte Service-Aufrufe besitzen dieselbe Prüfung; sie greift auch vor möglichen No-op-Rückgaben.

Die Auswahl zulässiger Runtime- und zugehöriger Quellen erfolgt weiterhin in der Workbench. Dieser Schritt ersetzt keine vollständige Kontextvalidierung an der Service-Grenze und führt keine neue Pflicht für eine aktive Schlüssel-/Finding-Zuordnung ein. Bestehende Bestätigungen, Options-Snapshots, wiederholte Reviews, Deaktivierung statt Löschung, Reaktivierung und atomare Historisierung bleiben erhalten. Übersetzungen und Original-Quellwerte werden nicht verändert.

Neue Tests prüfen Bestätigung und Lösen einer bestehenden Verknüpfung für Gäste sowie nach Rollenverlust, jeweils über Livewire und direkt am zuvor aufgelösten Service. Quellen, Quellwerte, Kandidaten, dynamische Übersetzungswerte, Schlüssel, Reviews und Timeline bleiben dabei unverändert. Die vorhandenen Erfolgstests decken weiterhin Reaktivierung, selektives Lösen, fehlenden/veralteten Kontext und Rollback bei Historisierungsfehlern ab.

Nächster Prüfschritt: die übrigen schreibenden Livewire-Aktionen und Artisan-Aufrufe systematisch erfassen und gegen die bereits geschützten Grenzen abgleichen. Die bisherigen Rollenprüfungen sind noch kein vollständiges Akteurs-, Kontext- oder Versionsprotokoll.

## Abgleich: verbleibende UI-Schreibaktionen und Artisan-Übergänge

Der Abgleich in `TranslationWorkbenchEntries` umfasst öffentliche Fachaktionen, direkte Modelländerungen, Reportdateien und Artisan-/Prozessaufrufe. Zusätzlich wurden die Aufrufstellen der extrahierten Review-/Link-Writer im Projekt gesucht. Ergebnis für die bisher betrachtete Foundation-Workbench:

| Einstieg | Wirkung / nachgelagerter Aufruf | Stand |
|---|---|---|
| Einzelwerte, dynamische Werte, Schlüssel-/Klassifikationsreviews | Editor-Dienste und interne Writer | Rollenprüfung an den beschriebenen Anwendungsgrenzen; interne Persistence-Writer sind keine eigenständige öffentliche API |
| Bulk Equalize, Scalar → Array, Konflikt/Cleanup/Provenance, Quellenverknüpfung | jeweilige Writer | aktuelle Rolle in UI und Schreibmethode geprüft |
| `refreshFindingsCurrentTab` | abhängig vom Tab einer der untenstehenden Refresh-Aufrufe | eigene Rollenprüfung ergänzt |
| `refreshFindingsAllTabs` | Exportplan, Codeplan, Apply-Dry-Run, Shared- und Suspicious-Kandidaten | eigene Rollenprüfung ergänzt |
| `refreshCodeUpdateApplyReport` | `translation-workbench:apply-code-updates` ohne `--write` | eigene Rollenprüfung ergänzt |
| `startCompletePipelineRun` | Run-Datensatz, Log, Hintergrundprozess `translation:workbench --complete --pipeline-run-id=…` | eigene Rollenprüfung vor Preflight/Run-Erstellung ergänzt |
| Status-Polling, Auswahl, Modalwechsel, Formular-Kopieraktionen | Anzeige/Formularzustand; teilweise eigene Benutzer-UI-Einstellungen | keine neue Fachschreiboperation; bestehender Routenschutz bleibt |

### Was Refresh tatsächlich ausführt

| Interner Refresh | Aufruf | Auswirkungen |
|---|---|---|
| Exportreport | `TranslationWorkbenchLangFileExporter::export(write: false)` | Exportplan und JSON-Report; keine Veröffentlichung von Sprachdateien |
| Codeplan | `translation-workbench:plan-code-updates` | Plan/Reports |
| Code-Apply-Report | `translation-workbench:apply-code-updates` ohne `--write` | Dry-Run/Report, keine Quellcode-Veröffentlichung |
| Shared candidates | `translation-workbench:detect-shared-key-candidates` ohne `--dry-run` | **Datenbankänderungen**: Kandidaten anlegen/aktualisieren, stale/obsolete gegebenenfalls auf pending setzen und nicht mehr gefundene Kandidaten obsolete setzen; außerdem Report |
| Suspicious keys | `translation-workbench:detect-suspicious-keyed-additions --source-locale=… --skip-raw-data` | Scan und Detektorreport |

Die internen Refresh-Helfer werden von den geschützten öffentlichen Refresh- oder Review-Aktionen aufgerufen. Ihre bisherigen Fehlerbehandlungen und Aufrufoptionen wurden nicht verändert.

### Offene Grenze: UI → Hintergrundprozess / CLI

Der UI-Start speichert `started_from=ui` und `user_id` im Pipeline-Run und startet einen separaten PHP-Prozess. `RunTranslationWorkbench::handle()` lädt den Run über `--pipeline-run-id`, prüft bislang aber die aktuelle Rolle des auslösenden Benutzers nicht erneut. Ein Rollenentzug zwischen Start und späterer Verarbeitung wird dadurch noch nicht abgefangen. Die jetzt ergänzte Startprüfung schließt diese spätere Grenze ausdrücklich nicht.

Die eigenständigen CLI-Kommandos und die Legacy-Orchestrierung `project:translations` bleiben unverändert. Ihnen pauschal die HTTP-Benutzerprüfung aufzuerlegen würde reguläre Konsolenläufe blockieren. Ein explizites Akteursprotokoll für UI-Runs und eigenständige Systemläufe bleibt offen, ebenso Kontext-/Versionsvalidierung und die abschließende Ablösung paralleler Legacy-Schreibpfade. Dieser Abgleich ist kein vollständiges Sicherheits-Audit aller administrativen Seiten.

Regressionen prüfen die vier UI-Einstiege für Gäste und Benutzer ohne aktuelle Rolle, bevor Commands, Exporter oder Pipeline-Tracker aufgerufen werden. Ein Erfolgstest erhält die bisherige Command-Sequenz für Shared-Candidates-Refresh und Apply-Dry-Run. Es wurde keine reale Pipeline ausgeführt.

## Umgesetzt: Benutzerprüfung im UI-Hintergrundlauf

Die oben als offen beschriebene UI→Hintergrundprozess-Grenze ist jetzt auf Rollenebene abgesichert. `TranslationWorkbenchPipelineAccess` lädt bei jedem Check den gespeicherten Run und den auslösenden Benutzer über den konfigurierten Auth-Provider erneut. Für Runs mit `--pipeline-run-id` sind ein gültiger pending/running-Run für `translation:workbench`, `started_from=ui` und eine aktuelle Admin-/Super-Admin-Rolle erforderlich. Fehlende, abgeschlossene oder ungültige Runs sowie fehlende Benutzer werden abgewiesen; kein Rückfall auf einen eigenständigen CLI-Lauf.

`RunTranslationWorkbench` prüft vor dem Start und unmittelbar vor jedem Unterkommando. Ein unzulässiger Start erzeugt keine Pipeline-Schritte. Ein Rollenentzug zwischen Schritten verhindert das nächste Unterkommando und verwendet die vorhandene Fehlerbehandlung mit Run-/Schrittstatus und Fehlermeldung. Bereits abgeschlossene Schritte werden nicht rückgängig gemacht; eine laufende Einzeloperation wird nicht unterbrochen. Die Prozess-Session wird nicht als Benutzer angemeldet; eine durchgängige Akteurszuordnung aller nachgelagerten Audit-Ereignisse ist damit noch nicht umgesetzt.

Ohne `--pipeline-run-id` bleibt der eigenständige CLI-Aufruf unverändert beim bisherigen Betriebssystem-Zugriffsmodell. Das ist kein implementiertes Systemakteur-/Scope-Protokoll. Die Rollenprüfung bindet auch noch nicht sämtliche Kommandooptionen an den gespeicherten Auftrag und verhindert keine konkurrierte Doppel-Ausführung eines laufenden Runs.

Tests prüfen Rollenverlust, verschwundenen Benutzer, fehlende Identität, ungültige/abgeschlossene Runs, CLI ohne Run-ID, abgewiesenen Command-Start und Abbruch vor dem zweiten Unterkommando. Im Ablauf-Test ist das erste Unterkommando ein wirkungsloser Ersatz; reale Pipeline-Schreibvorgänge werden nicht ausgeführt. Zusätzlich wurde ein fehlerhafter Klassenname im Mock des vorherigen Pipeline-Starttests korrigiert, damit er tatsächlich den Pipeline-Tracker ersetzt.

## Umgesetzt: Optionsbindung und einmalige Übernahme eines UI-Runs

Der UI-Start speichert jetzt alle kommandoeigenen Optionen aus `RunTranslationWorkbench::executionDefaults()` und überschreibt `--complete` mit true. Defaults stammen unmittelbar aus der Kommando-Signatur; Tracking-ID und allgemeine Konsolen-Darstellungsoptionen gehören nicht zum Ausführungsauftrag. `TranslationWorkbenchPipelineAccess::validateOptions()` vergleicht den vollständigen Satz strikt vor der Verarbeitung und vor jedem Unterkommando mit den gespeicherten Optionen. Abweichende Pfade, Sprache, Schreib-/Truncate-/Skip-Optionen sowie unvollständige ältere Snapshots werden abgewiesen. Ein neu aus der UI gestarteter Run erhält den vollständigen Satz.

`TranslationWorkbenchPipelineRunTracker::claim()` übernimmt einen pending-Run durch ein bedingtes einzelnes UPDATE von pending auf running. Bereits laufende, abgeschlossene oder nicht vorhandene Runs können nicht erneut übernommen werden. Der Runner behandelt eine abgewiesene Übernahme getrennt von einem Fehler im eigenen Run: Ein zweiter Prozess verändert insbesondere weder Status noch Fehlermeldung des bereits laufenden Runs. Rollen-/Optionsfehler nach erfolgreicher Übernahme markieren dagegen den eigenen Run als fehlgeschlagen.

Die Sicherung betrifft denselben Run, nicht parallele eigenständige CLI-Aufrufe oder mehrere unterschiedliche Run-IDs. Ein Prozessabbruch kann einen Run weiterhin auf running stehen lassen; es gibt absichtlich keinen automatischen Reset oder Retry, der bereits ausgeführte Schreibschritte wiederholen könnte. Normale Statuspflege darf diese Sperre nicht umgehen. Ein explizites Verfahren zur Erkennung und kontrollierten Behandlung abgebrochener Runs bleibt offen. Eigenständige CLI-Läufe ohne Run-ID bleiben unverändert.

Tests prüfen erfolgreiche einmalige Übernahme, abgewiesenen Zweitstart mit unverändertem Run, Optionsgleichheit, verschiedene Abweichungen, unvollständige Snapshots und Abbruch vor Unterkommandos bei Optionsfehlern. Der Parallelitätsschutz beruht auf dem atomaren bedingten Datenbank-UPDATE; der Regressionstest simuliert zwei aufeinanderfolgende Übernahmeversuche und startet keine parallelen Produktionsprozesse.

## Umgesetzt: kontrollierter Abschluss unterbrochener lokaler Pipeline-Runs

`TranslationWorkbenchPipelineProcess` ergänzt eine pro Run gehaltene lokale Dateisperre unter `storage/translation-workbench/pipeline-locks`. Der Runner hält die Sperre über seinen gesamten Ablauf und gibt sie im finally frei; beim Prozessabbruch erfolgt die Freigabe durch das Betriebssystem. Sperrdateien werden nicht entfernt: Löschen oder Ersetzen einer Datei während eines aktiven Runs würde die Sperrgarantie unterlaufen. Die Ausführung erfordert verlässliches lokales flock und lesbare Sperrdateien für den UI-Prozess. Der Host wird bei der atomaren Übernahme im Run gespeichert.

Die vorhandene Pipeline-Statusanzeige zeigt für einen running-Run mit freier Sperre einen Hinweis und die ausdrücklich zu bestätigende Flux-Aktion „Mark as interrupted“. Unbekannte Zustände (anderer Host, fehlende/unlesbare Sperre oder fehlende Metadaten) werden nicht als Prozessabbruch behandelt. Pending-Runs, die nie vom Hintergrundprozess übernommen wurden, werden durch dieses Verfahren ebenfalls nicht automatisch beendet.

Die Aktion prüft die aktuelle Admin-/Super-Admin-Rolle, die Sperre nochmals und den Run unter Datenbanksperre. Sie markiert nur den Run und einen noch laufenden Schritt als failed; fertige Schritte bleiben erhalten, noch nicht gestartete Schritte bleiben pending. Benutzer-ID und Zeitpunkt der Bestätigung werden im Run dokumentiert. Keine Schritte werden wiederholt oder zurückgerollt, keine Quelldateien/Übersetzungen verändert. Ein neuer Lauf muss weiterhin bewusst gestartet werden. Ein Prozess kann hängen und seine Sperre trotzdem halten; dafür wird kein automatischer Timeout oder Prozessabbruch eingeführt.

Der UI-Start aktualisiert PID und Logpfad nun mit einzelnen JSON-Feldupdates, damit parallel vom Worker gesetzte Metadaten nicht durch einen älteren Modelstand überschrieben werden.

Tests verwenden echte lokale Dateisperren mit temporären Dateien: aktive Sperre und Zweitstart, Freigabe, ausdrücklicher Abschluss, unveränderte fertige Schritte, Fremdhost/fehlende Sperre, wiederholte Korrektur und fehlende Berechtigung. Die Lösung ist eine lokale Prozessdiagnose, kein verteilter Cluster-Lock und kein vollständiges Retry-/Recovery-Protokoll.

## Umgesetzt: Pipeline-Initiator getrennt von Review-Autoren

`TranslationWorkbenchPipelineContext` hält einen begrenzten Verarbeitungskontext mit `run_id`, `origin` (`ui`/`cli`) und `initiated_by_user_id`. Der Runner setzt ihn erst nach erfolgreicher Zugriffs-/Optionsprüfung. Der UI-Initiator stammt aus dem gespeicherten Run. Eigenständige CLI-Pipelines haben keine initiierende Benutzer-ID. Vor der Ausführung wird ein äußerer Kontext zwischengespeichert, während der Prüfung zunächst ausgeblendet und im finally auch bei Fehlern wiederhergestellt. Der Service ist scoped registriert; es gibt keinen statischen globalen Benutzerzustand.

`TranslationWorkbenchTimelineRecorder` ergänzt diesen Kontext unter `context.pipeline`; die gemeinsame Activity-Protokollierung in `ConfirmsTranslationWorkbenchTruncate` ergänzt ihn unter `properties.pipeline`. Vorhandene Kontextfelder bleiben erhalten. Außerhalb eines Pipeline-Laufs bleibt die bisherige Struktur unverändert. Der JSON-Raw-Data-Reportvertrag wurde in diesem Schritt nicht verändert.

Bewusste Grenze: Es wird keine Anmeldung/Impersonation vorgenommen. `created_by_user_id`, Review-Autoren und automatische Klassifikationen behalten ihre bisherige Semantik. Die auslösende Person ist nicht automatisch die Person, die ein einzelnes Ergebnis geprüft oder freigegeben hat. Die Pipeline-Initiator-Metadaten sind Audit-Zuordnung, keine weitere Zugriffsberechtigung. Rückwirkend werden keine historischen Ereignisse geändert.

Tests führen UI-/CLI-Runner mit einem wirkungslosen Ersatz-Unterkommando aus, das eine isolierte Testbeobachtung schreibt und anschließend eine Exception auslöst. Sie prüfen Initiator, unveränderte Autorenfelder, fehlende neue Reviews, Erhalt fachlicher Kontextfelder und sauberes Ende des Pipeline-Kontexts. Die bisherige Rollen-/Optionsprüfung bleibt separat bestehen.

## Wiederholbarer Datenbankvergleich vor der Zusammenführung

Neu: `php artisan translation-workbench:compare-values`, optional `--json` für vollständige Einzeldaten auf stdout. Der Command schreibt weder Datenbankwerte noch Reports/Sprachdateien. PostgreSQL wird in einer REPEATABLE-READ/READ-ONLY-Transaktion gelesen; isolierte SQLite-Tests verwenden eine normale Lesetransaktion. Die erforderlichen Tabellen werden geprüft; fehlende Tabellen führen zu einem Fehler statt zu einer scheinbar leeren Vergleichsmenge.

Verglichen werden `translation_values` samt vollständigem Legacy-Key aus `translation_keys` und `translation_workbench_lang_values`, gruppiert nach unverändertem Key und normalisierter Locale (Kleinschreibung, Unterstrich→Bindestrich). Texte bleiben exakt; null und Leerstring unterscheiden sich. Kategorien: identical, different, legacy_only, workbench_only, ambiguous. Mehrere Zeilen auf einer nur einseitig vorhandenen Zuordnung bleiben in der einseitigen Kategorie und erhalten zusätzlich multiple_rows; beidseitige Mehrfachzuordnungen werden ambiguous. IDs, Original-Locale, Originalstatus und Legacy-is_base_duplicate bleiben in JSON erhalten.

Statusunterschiede werden nur bei beidseitig genau einer Zeile bewertet; Legacy ok entspricht für diesen Vergleich active. Statusunterschiede und Mehrfachzeilen sind Zusatzmerkmale, keine weiteren disjunkten Kategorien. Fehlende Keys oder Locales werden separat als unassigned ausgegeben. Der Command wählt weder den neuesten noch einen bevorzugten Text, schreibt keine Review-Entscheidung und bescheinigt keine Exportfähigkeit. Gefundene Unterschiede sind ein erfolgreich ausgeführter Vergleich (Exit 0), kein Command-Fehler.

Dieser wiederholbare Vergleich ist bewusst enger als die einmalige Bestandsaufnahme vom 13.10.2026: Er umfasst keine Sprachdateien, Fallback-Auflösung, Key-Review-Historie, dynamischen Optionswerte oder ältere Package-Value-Tabellen. Er ersetzt weder Exportplan noch Freigabeentscheidung. Bestehende Legacy-Editoren/Exporter und Foundation-Workflows wurden nicht umgeschaltet. Die historischen Zahlen im separaten Vergleichsdokument bleiben eine Momentaufnahme; sie wurden in diesem Schritt nicht aktualisiert.

Nächster fachlicher Schritt: anhand des aktuellen Vergleichs echte nichtleere Textkonflikte, Statuskonflikte und Base-Duplicate-/Fallback-Fälle getrennt prüfen, bevor Legacy-Schreibwege delegiert oder stillgelegt werden.
