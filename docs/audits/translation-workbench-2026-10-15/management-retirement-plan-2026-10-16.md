# Management (Legacy): Funktionsabgleich und Ablösung

Stand: 16.10.2026. Statische Bestandsaufnahme des aktuellen Arbeitsstands; keine produktiven Schreibvorgänge, keine deaktivierten Routen und keine Browser-Abnahme.

## Ziel

**Workbench bleibt das zentrale Bearbeitungsformular einschließlich seiner Modals.** Management (Legacy) wird nach nachgewiesener Funktionsübernahme stillgelegt. Bestehende Daten und Historien werden dadurch nicht gelöscht. Zusätzliche Auswertungsseiten dürfen bleiben; parallele fachliche Schreibwege sollen entfallen.

„Vorhanden“ bedeutet unten: Ein entsprechender Einstieg existiert im Code. Es bedeutet nicht, dass Legacy-Datenmodell, Statussemantik und Bedienumfang bereits identisch sind.

## Funktionsmatrix

| Funktion | Bisheriger Einstieg | Workbench / Ziel | Noch zu klären oder zu übernehmen |
| --- | --- | --- | --- |
| Suche, Filter, Sortierung, Pagination | TranslationList | Vorhanden in TranslationWorkbenchEntries | Alte Filter fachlich zuordnen; Legacy-Statusnamen nicht ungeprüft übertragen. |
| Schlüssel prüfen und Vorschlag übernehmen | openTranslationKey, applySuggestedKey | Key-Editor und acceptSuggestedTranslationKey vorhanden | Mehrere aktive Besitzer und belegte Zielwerte benötigen eine ausdrückliche Konfliktentscheidung. |
| Dynamische Schlüssel / Multi-Klassifikation | setDynamicMulti | Klassifikationsaktionen und Dynamic-Multi-Editor vorhanden | Keine zweite Legacy-Metadatenpflege fortsetzen. |
| Übersetzungen je Sprache bearbeiten | saveTranslationEdit | saveTranslationValue und separater Dynamic-Multi-Editor vorhanden | Verbleibende Legacy-/Foundation-Wertkonflikte einzeln entscheiden; keine pauschale Übernahme. |
| Regionale Übersetzungen bearbeiten | Management und TranslationSubLanguages | selectedTargetSubLocales und targetSubTranslationValues im skalaren Editor vorhanden | Regionale Bearbeitung ist bereits möglich; nicht als neues Parallelformular nachbauen. |
| Regionales Basis-Duplikat oder bewusste Variante | markSubLanguageAsDuplicate, keepSubLanguageAsOverride | Noch kein gleichwertiger Entscheidungsweg in Entries festgestellt | In bestehendes Modal integrieren; Fallback, expliziter Wert, Status und Protokollierung gemeinsam festlegen. Nicht mit Source-equals-Target-Prüfung verwechseln. |
| Kopier-/Leeraktionen für mehrere regionale Sprachen | copyFirstSelectedSubLanguageValueToAllSelectedSubLanguages, clearSelectedSubLanguageValues | Teilweise allgemeine Quelltext-Kopieraktionen vorhanden | Bedienumfang gezielt abgleichen; Änderungen zunächst im Formular, erst beim Speichern persistieren. |
| Nächsten Eintrag prüfen | Legacy Next-Aktionen | openNextReviewFinding vorhanden | Aktiven Filter und Prüfungskontext erhalten. |
| Obsolete geprüft / neuer Key benötigt | markObsoleteAsReviewed, markNeedsNewKeyManually, clearNeedsNewKeyManually | Findings, Key-Review und Obsolete-Source-Prüfung vorhanden | Keine belegte 1:1-Statusgleichheit. Gründe und Folgewirkung zuordnen, bevor Legacy-Markierungen entfallen. |
| Verlauf ansehen | openTranslationHistory, loadOlderTranslationHistory | Workbench-Timeline und Graph vorhanden | Zugänglichkeit der historischen Legacy-Ereignisse sicherstellen; keine fingierte Workbench-Historie erzeugen. |
| Freigabe und Veröffentlichung | Historisch mit Legacy-Verarbeitung verbunden | Gespeicherte Revision freigeben, Vorschau, Veröffentlichung und Wiederaufnahme über gemeinsame Dienste vorhanden | Legacy reviewed_at ist keine Freigabe einer Workbench-Revision. Legacy-Exporter akzeptiert inzwischen nur dry-run. |
| Veröffentlichte Werte vergleichen | Kein gleichwertiger zentraler Legacy-Editorweg erforderlich | Compare published values im bestehenden Modal vorhanden | Vergleich bleibt lesend; ersetzt keine Freigabe und keine Konfliktentscheidung. |

## Weitere aktive Seiten sind separat zu berücksichtigen

| Seite | Tatsächliche Rolle | Ziel / Ablösekriterium |
| --- | --- | --- |
| Management (Legacy), admin.translations | TranslationList schreibt Legacy-Keys, Werte und Review-/Aktivitätsdaten. | Nach Funktions- und Datenabgleich Schreibaktionen serverseitig stilllegen, anschließend Navigation/Route ausmustern. |
| Sub-Languages | TranslationSubLanguages schreibt TranslationValue einschließlich is_base_duplicate. | Regionale Entscheidungen im bestehenden Workbench-Modal abbilden; danach eigenen Schreibweg stilllegen. |
| Usage | TranslationUsageAudit kann via createTranslationKeyFromUsageAudit Legacy-Keys erzeugen. saveUsageAuditDecision speichert Entscheidungen und Fundstellenzeilen für spätere Command-Verarbeitung. | Analyse kann bleiben. Key-/Fundstellenentscheidungen müssen auf die zentrale Workbench-Zuständigkeit umgestellt werden; nachgelagerte Verbraucher mitprüfen. |
| Lang Ballast | setDecisionStatus speichert TranslationLangBallastDecision; also nicht ausschließlich lesend. | Prüfentscheidungen mit vorhandener Workbench-Lang-Cleanup-Prüfung abgleichen. Keine doppelte Freigabe mit unterschiedlicher Wirkung. |
| Statistics | Eigene Auswertung. | Datenquellen auf Zielbestand prüfen; keine Voraussetzung für ein zweites Bearbeitungsformular. |
| Settings | TranslationWorkbenchSettings verwaltet derzeit insbesondere UI-Zustände und zeigt Konfiguration an. | Nicht als bereits vollständige Sprach-/Fallback-Verwaltung behandeln. Fachliche Einstellungen gesondert zuordnen. |
| Raw Data / Workflow / Diagnostics | Technische Ansicht, Dokumentation und Diagnose. | Ergänzende Werkzeuge beibehalten; keine parallele Übersetzungsbearbeitung einführen. |

## Warum eine reine UI-Umschaltung nicht genügt

Schon die beiden alten Werteformulare verhalten sich unterschiedlich:

- TranslationList::saveTranslationEdit speichert den eingegebenen String unverändert; trim dient zur Leerprüfung. Auch unveränderte Werte erhalten reviewed_at/reviewed_by neu.
- TranslationSubLanguages::saveTranslationEntryEdit trimmt den gespeicherten Text und überspringt unveränderte Werte. Beide verwenden Legacy-Status missing/ok und setzen beim Speichern Review-Angaben.
- Die Workbench delegiert skalare Änderungen an TranslationWorkbenchScalarEditor mit Fingerprint-Prüfung. Eine explizite Freigabe gespeicherter Revisionen ist ein eigener Vorgang.

Die vorhandenen historischen Datenaudits bleiben Grundlage der Wertkonfliktprüfung. Dieser Abgleich hat deren Zahlen nicht erneut gegen die produktive Datenbank erhoben. Weder „neuer Zeitstempel gewinnt“ noch „Legacy gewinnt“ ist eine zulässige automatische Konfliktentscheidung.

## Reihenfolge der Umsetzung

1. **Key-Konflikte im vorhandenen Key-Editor lösbar machen.** Zusammenführen und Trennen fachlich unterscheiden; Besitzer, Fundstellen, Sprachwerte und Verlauf vor einer Änderung zeigen. Belegte Ziele dürfen keine stillschweigende Auswahl oder Löschung auslösen. Dies ist der nächste konkrete Umsetzungsschritt.
2. **Regionale Entscheidung im vorhandenen Übersetzungsmodal ergänzen.** Basis-Fallback versus bewusst gespeicherte regionale Variante eindeutig machen; bestehende regionale Eingabefelder weiterverwenden.
3. **Verbleibende Legacy-Reviewfälle und Komfortaktionen abgleichen.** Insbesondere Needs-new-key, Obsolete-Prüfung, regionale Sammelaktionen und historische Nachvollziehbarkeit.
4. **Usage und Lang Ballast anbinden.** Ihre gespeicherten Entscheidungen und deren Command-Verbraucher in die zentrale Zuständigkeit überführen; keine unabhängigen Legacy-Keys mehr erzeugen.
5. **Datenübergang prüfen und Legacy-Schreiben stilllegen.** Erst nach expliziter Konfliktentscheidung und Nachweis vollständiger Zugänglichkeit. Direkte Livewire-Aktionen ebenfalls sperren; bloßes Entfernen des Menüpunktes reicht nicht. Lesende Historie und Daten bleiben erhalten.

Für jede tatsächliche Schreibwegänderung sind isolierte Tests für Erfolg, Berechtigung, Konflikt, veralteten Formularstand und unveränderte andere Datensätze erforderlich. Die reine Bestandsaufnahme benötigt keinen neuen Verhaltenstest.

## Geprüfte Code-Einstiege

- app/Livewire/Admin/TranslationList.php
- app/Livewire/Admin/TranslationSubLanguages.php
- app/Livewire/Admin/TranslationUsageAudit.php
- app/Livewire/Admin/TranslationLangBallast.php
- app/Livewire/Admin/TranslationStatistics.php
- packages/gunreip/laravel-translation-workbench/src/Livewire/TranslationWorkbenchEntries.php
- packages/gunreip/laravel-translation-workbench/src/Livewire/TranslationWorkbenchSettings.php
- routes/admin.php
- resources/views/layouts/app/sidebar/⚡administration.blade.php

Ergänzende vorhandene Audits: responsibilities.md, legacy-foundation-value-review-2026-10-16.md und value-conflict-context-2026-10-16.md im selben Verzeichnis.
