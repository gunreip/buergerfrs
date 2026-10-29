# Bestehende Key-Verwendungen – Zuordnungsprobe 19.10.2026

Die zuletzt gesicherte Herkunftsnachweis-Datenbank wurde in eine neue isolierte PostgreSQL-Datenbank restauriert. Die Prüfung verwendet ausschließlich den gesicherten Capture-Bestand und per Hash geprüfte englische PHP-Sprachdateien. Keine produktive Verbindung, keine Änderungen an Quellcode oder Sprachdateien.

## Ergebnis

| Umfang | Anzahl |
|---|---:|
| Findings mit exaktem Key-/Sprachdateibeleg | 2.207 |
| Unterschiedliche belegte Keys | 779 |
| In der Testgeneration angelegte Keys | 768 |
| Getrennte Finding-/Key-Verknüpfungen | 1.792 |
| Wegen Key-Struktur blockierte Findings | 415 |
| Unterschiedliche blockierte Keys | 11 |

Die Zuordnung rekonstruiert bereits im Quellcode vorhandene Key-Verwendung. Unterschiedliche Findings bleiben unabhängig, auch wenn sie denselben Key benutzen. Unterschiedliche Keys mit gleichem englischen Text werden nicht zusammengelegt. Die 17.733 Literal-Findings erhalten keine automatische Zuordnung aus Textähnlichkeit oder SuggestedKey. Die zuvor offenen Quellen bleiben offen.

Blockiert sind `ui.type`, `ui.add-primary-language`, `ui.client-number`, `ui.open`, `ui.actual`, `ui.action`, `ui.already-target`, `ui.activity-log`, `ui.app-settings`, `ui.add-document` und `ui.appearance`. Sie erfüllen nicht die vereinbarte Struktur aus Dateigruppe und verschachteltem Arraypfad. `ui.type` betrifft 387 Fundstellen. Der Bestand wurde nicht automatisch umbenannt; die blockierten Verwendungen werden weiterhin mit Capture-/Finding-ID nachgewiesen. Diese Probe baut noch keine produktive UI-Fehleranzeige.

## Speicherung und Herkunft

Die Testgeneration bekommt Einträge in `translation_workbench_keys`, `translation_workbench_key_names` und `translation_workbench_key_findings`. Dabei werden neue IDs verwendet, keine historischen Key-IDs übernommen. `existing_key_imports` speichert den vollständigen Prüfplan und seine Identität; `existing_key_observations` bewahrt den Nachweis pro Finding einschließlich blockierter Fälle. Beide Nachweistabellen sind gegen normale Updates und Deletes geschützt.

Dies ist eine technische Baseline-Rekonstruktion, keine manuelle Freigabe und keine Behauptung über den historischen Benutzer. Es werden keine künstlichen Benutzer-Events oder Reviews erzeugt. Verknüpfungszeiten dokumentieren die Testübernahme, nicht den ursprünglichen Entstehungszeitpunkt. Die englischen Werte bleiben zunächst Belege; Value-Slots und Revisionen werden noch nicht erzeugt.

## Prüfungen

Zehn Planner-Assertions prüfen getrennte Findings, identische Texte unter verschiedenen Keys, blockierte Struktur, unzugeordnete Literale, doppelte Captures, mehrdeutige/fehlende Sprachbelege und abweichende Rohfelder.

Elf PostgreSQL-Assertions prüfen den isolierten Cluster, vollständigen Rollback nach einem Fehler am Ende des Plans, Zuordnungszahlen, idempotenten Retry, exakte Übereinstimmung der Key-Namen mit den Quellargumenten, blockierte Fälle ohne Zuordnung, unveränderte Finding-Identitäten sowie den Schutz der Übernahmenachweise. Die Inhaltsfingerabdrücke der 19 nicht für die Zuordnung vorgesehenen bestehenden Tabellen sind unverändert: insbesondere Rohfelder, SuggestedKeys, Werte, Reviews und bisherige Evidenz.

Die Ausgangsgeneration hatte noch keine Keys. Der Importer ist ausdrücklich für diesen Zustand gebaut und lehnt einen anderen bereits befüllten Key-Bestand ab. Dies ist keine allgemeine produktive Konfliktauflösung. Untersucht wurden nur unnamespaced Keys mit exaktem englischen Beleg; keine Laufzeit-Fallbacks oder Namespaces wurden erraten. Der gesicherte Stand ist nicht automatisch identisch mit dem heutigen Arbeitsbaum.

Der private Ergebnisdump wurde erstellt und der Testcluster beendet. Sein erneuter Restore wurde noch nicht ausgeführt. [Prüfergebnis und Prüfsummen](existing-key-links-2026-10-19.json).

## Nächster Schritt

Die bereits veröffentlichten Sprachwerte als eigenständige Baseline mit genauer Herkunft übernehmen. Dabei müssen ursprünglicher Sprachdateibeleg, neuer Bearbeitungsstand und spätere bewusste Freigabe getrennt bleiben. Blockierte Key-Strukturen und ungeklärte Quellen dürfen diese Übernahme nicht stillschweigend in eine automatische Korrektur verwandeln.
