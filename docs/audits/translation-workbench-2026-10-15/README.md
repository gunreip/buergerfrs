# Translation Workbench: Status quo und Umbauentscheidung

Stand: **15.10.2026**. Untersucht wurde der aktuelle Arbeitsstand, einschließlich noch nicht committeter Änderungen. Die geprüften Quellstände sind in [source-manifest.json](source-manifest.json) festgehalten.

## Ergebnis

**Die Sorge vor konkurrierenden Schreibwegen ist begründet.** Es gibt weiterhin drei nebeneinander beschriebene Datenmodelle: die ursprünglichen `translation_*`-Tabellen, das ältere Workbench-Entry-/Occurrence-Modell und die neuere Foundation mit Findings, Keys, Reviews und Timeline. Alte Commands bleiben erreichbar; teilweise werden sie durch Projekt-Wrapper aufgerufen. Das Entfernen alter Menüpunkte hat diese Schreibwege nicht stillgelegt.

Ein normaler Import kann gespeicherte Übersetzungswerte durch den Stand der Sprachdateien ersetzen. Ein alter Teilscan kann Einträge außerhalb seines Scan-Bereichs obsolete setzen. Ein Export kann Dateien teilweise schreiben, einen Konflikt melden und trotzdem mit Erfolg enden. Diese Fälle wurden isoliert reproduziert, nicht nur aus dem Code vermutet.

**Empfehlung: Foundation und vorhandene Entscheidungen erhalten, die Zuständigkeiten für Scan, Bearbeitung und Veröffentlichung neu abgrenzen und die alten Schreibwege anschließend ablösen.** Ein vollständiger Neustart der Datenbank ist anhand der Ergebnisse nicht gerechtfertigt. Die strukturelle Integrität ist weitgehend vorhanden; mehrere bestehende Schutzmechanismen funktionieren. Ebenso wenig ist ein unveränderter Weiterbetrieb aller Pipelines empfehlenswert.

Während dieser Prüfung wurden weder Produktivdaten noch Quellcode-/Sprachdateien durch Translation-Commands verändert. Es wurden keine Commands deaktiviert, keine Tabellen bereinigt und keine Migrationen auf dem echten Bestand ausgeführt. Die folgenden Maßnahmen sind Empfehlungen, keine bereits vorgenommenen Reparaturen.

## Belege und Umfang

| Bestandteil | Ergebnis / Dokument |
| --- | --- |
| Command-Inventar | **46 Klassen**, einschließlich angrenzender Projekt-Wrapper; [Command-Matrix](commands.md), [Signaturen und Abhängigkeiten](commands.json) |
| Echter PostgreSQL-Bestand | **33 Tabellen**, Schema, Constraints, Größen, Statusverteilungen; [DB-Bewertung](database.md), [Messdaten](database.json) |
| Referenzintegrität | **47 tatsächliche Fremdschlüsselbeziehungen**, keine verwaisten referenzierten IDs |
| Kritische Verhaltensfälle | **15 isolierte Charakterisierungen**; [Resultate](reproductions.json), [ausführbares Prüfskript](reproduce.php) |
| Foundation mit Bestandskopie | Drei Läufe auf kopierten echten Daten und aktuellen Quelltexten; [Ergebnis](foundation-replay.json) |
| Sprachdatei-Import mit Bestandskopie | Import aktueller Sprachdateien ausschließlich in Kopie; [Ergebnis und Einordnung](import-replay.json) |
| Vorhandene Workbench-Tests | **220 bestanden, 1.324 Assertions**, 31,852 Sekunden; [Testprotokoll](existing-tests.json) |

Die Bestandskopie liegt ausschließlich unter `/tmp`, mit Dateimodus 0600. Hier im Repository liegen Schema-/Zählwerte und ausgewählte IDs/Keys, kein vollständiger Datenbankdump und kein Export der Übersetzungstexte.

## Wie heute geschrieben wird

| Weg | Verhalten | Überschneidung |
| --- | --- | --- |
| `project:translations` / Teile von `project:build` | Legacy-Audits, Legacy-Sync, Legacy-Export; Quellcode-Patch teilweise optional | Eigene DB- und Sprachdatei-Autorität neben Workbench |
| `translation:workbench` | Alter Entry-Scan, erneuter Foundation-Scan, Klassifikation, Import, Inventar und Berichte | Derselbe Quellbestand wird mehrfach verarbeitet, zwei Workbench-Modelle werden versorgt |
| `translation:workbench --complete` | Zusätzlich Restore-/Export-/Code-Apply-/Refresh-Schritte | Erfassung und Veröffentlichung in einem Auftrag; UI startet diesen Weg mit Run-ID |
| Workbench-Editor | Speichert skalare Werte in `translation_workbench_lang_values` | Dieselbe Tabelle wird durch Sprachdatei-Import beschrieben |
| Einzelcommands | Teilfunktionen direkt aufrufbar | Schutz durch Reihenfolge im Wrapper gilt nicht automatisch bei direktem Aufruf |
| Runtime-Dynamikerfassung | Beobachtet dynamische Quellen/Werte | Weitere Schreiber; Lifecycle muss mit statischem Scan abgestimmt werden |

Wichtige Einschränkungen: Der Complete-Wrapper verschiebt den Sprachdatei-Import bei schreibendem Export nach hinten. Das mindert einen konkreten Reihenfolgekonflikt, löst aber nicht die gemeinsame Autorität von Editor und Importer. `--dry-run` ist außerdem nicht gleichbedeutend mit vollständig schreibfrei: Berichte und Betriebsprotokolle können weiterhin entstehen.

## Befunde nach Risiko

### F01 — Teilscan kann außerhalb seines Bereichs archivieren · hoch · reproduziert

`ScanTranslationWorkbench::markMissingEntriesObsolete()` und die entsprechende Occurrence-Behandlung begrenzen die Archivierung nicht auf die erfolgreich geprüften Dateien. Ein Scan nur von Datei A setzt die zuvor erfasste Datei B ebenfalls obsolete/stale. Ein nicht vorhandener Scan-Pfad liefert keine Treffer und kann den gesamten erfassten Altbestand obsolete setzen.

Belege: `legacy_partial_scan_obsoletes_outside_scope`, `legacy_nonexistent_path_obsoletes_all` in [reproductions.json](reproductions.json); [ScanTranslationWorkbench.php](../../../packages/gunreip/laravel-translation-workbench/src/Console/ScanTranslationWorkbench.php).

**Ziel:** Fehlend darf nur heißen „in einer erfolgreich vollständig geprüften Quelle nicht mehr vorhanden“. Ein gemeinsamer Scan-Run muss angeforderte, tatsächlich besuchte, erfolgreich gelesene, ausgeschlossene und fehlgeschlagene Dateien auseinanderhalten.

### F02 — Foundation erkennt das Verschwinden des letzten Aufrufs nicht zuverlässig · hoch · reproduziert

Die Foundation leitet den Bereich für Obsolete-Prüfungen aus gefundenen Items ab. Enthält eine weiterhin vorhandene Datei nach einer Bearbeitung keinen Übersetzungsaufruf mehr, fehlt sie in dieser Liste. Das alte Finding und seine Beziehung bleiben aktiv. Fehlende Dateien benötigen ebenfalls eine explizite Scope-/Datei-Lifecycle-Behandlung.

Beleg: `foundation_last_occurrence_removed`; [FoundationSyncer](../../../packages/gunreip/laravel-translation-workbench/src/Foundation/TranslationWorkbenchFoundationSyncer.php), insbesondere `markMissingFindingsObsolete` und die Source-Path-Ermittlung.

**Ziel:** Dasselbe Datei-/Scan-Protokoll wie F01 verwenden; kein zweiter abweichender Mechanismus.

### F03 — Datei-Import und Editor überschreiben denselben Wertbestand · hoch · reproduziert

`TranslationWorkbenchLangValueImporter::syncValue()` ersetzt die Attribute eines vorhandenen Locale-/Namespace-/Key-Werts durch Dateiinhalte, einschließlich Herkunftsmetadaten. Ein gespeicherter, noch unveröffentlichter neuer Text wird dadurch wieder zum alten veröffentlichten Text. Der Schutz für eine explizite `lang_value_obsolete`-Review mit Entscheidung `source_value_marked_obsolete` existiert, schützt aber nicht pauschal jede fachliche Wert-/Statusentscheidung.

Auf der echten Bestandskopie wurden durch Import der aktuellen 40 Sprachdateien:

- **211** bestehende Werte von obsolete auf active gesetzt;
- **12** bestehende Werte von active auf obsolete gesetzt;
- **9** Texte geändert;
- **9** weitere Zeilen nur hinsichtlich ihrer Herkunftsmetadaten geändert;
- **184** Werte neu angelegt.

Die fachliche Richtigkeit jeder einzelnen Differenz wurde nicht entschieden. Es ist ein nachgewiesener Abgleichkonflikt, kein Beleg, dass alle 211 Reaktivierungen unerwünscht sind. Die vom Importer gemeldeten 1.832 Updates sind **nicht** mit 1.832 Textänderungen gleichzusetzen: In der SQLite-Kopie erzeugt insbesondere `0` gegenüber `false` zusätzliche Änderungsmeldungen. Die oben genannten Differenzen wurden separat für Text, Status, Metadaten und Key verglichen.

Belege: `import_overwrites_saved_unpublished_value`, [import-replay.json](import-replay.json), [Importer](../../../packages/gunreip/laravel-translation-workbench/src/Foundation/TranslationWorkbenchLangValueImporter.php), [TranslationWriter](../../../packages/gunreip/laravel-translation-workbench/src/Foundation/TranslationWorkbenchTranslationWriter.php).

**Ziel:** Dateibeobachtung, bearbeitete Revision und veröffentlichter Stand getrennt führen. Ein Import darf eine Abweichung melden, nicht unbemerkt den redaktionellen Stand ersetzen.

### F04 — Aktiver skalarer Wert ist exportierbar ohne reviewed-Key · hoch · reproduziert

Der skalare Export prüft aktive Werte und einen nicht-obsoleten Key, verlangt aber kein `review_status=reviewed`. Ein pending-Key mit aktivem Sprachwert wird exportiert. Für dynamische Werte bestehen strengere Review-Bedingungen; auch das ist ein uneinheitlicher Vertrag.

Beleg: `scalar_export_accepts_pending_key`; [LangFileExporter](../../../packages/gunreip/laravel-translation-workbench/src/Foundation/TranslationWorkbenchLangFileExporter.php), `exportableRows()`.

**Ziel:** Veröffentlichung auf einen explizit freigegebenen Wertstand beziehen. Welche bestehenden importierten Übersetzungen als bereits veröffentlicht gelten, muss die Migration übernehmen; sie dürfen nicht pauschal gesperrt oder verworfen werden.

### F05 — Teilweiser Export trotz Konflikt; Command liefert Erfolg · hoch · reproduziert

Dateien werden nacheinander geplant und geschrieben. Ein Konflikt in der zweiten Datei verhindert nicht das Schreiben der ersten. Das Export-Command gibt bei solchen Konflikten trotzdem Exit-Code 0 zurück. Eine Orchestrierung, die Schritt-Erfolg am Exit-Code festmacht, kann danach weiterlaufen.

Belege: `export_conflict_in_second_file_leaves_first_written`, `export_command_returns_success_despite_conflict`; [Export-Command](../../../packages/gunreip/laravel-translation-workbench/src/Console/ExportTranslationWorkbenchLangFiles.php).

**Ziel:** Vollständigen Veröffentlichungsplan vor dem Schreiben validieren; Konflikte als nicht erfolgreich behandeln. Für Datei-/DB-übergreifende Ausführung einen wiederaufnehmbaren Publication-Run mit klaren Teilergebnissen vorsehen. Eine DB-Transaktion allein macht Dateiänderungen nicht atomar.

### F06 — Alter Literal-Rewriter ist weiterhin ein kontextloser Schreibweg · hoch · reproduziert

`translations:rewrite-literals` schreibt standardmäßig. Bei gleichem Literaltext mit unterschiedlichen kontextbezogenen Keys wird der erste gefundene Legacy-Key für beide Stellen verwendet. Foundation-Review und ihre Provenienz sind nicht die Entscheidungsgrundlage dieses Wegs.

Beleg: `legacy_rewriter_chooses_first_key_for_all_contexts`; [TranslationsRewriteLiterals.php](../../../app/Console/Commands/TranslationsRewriteLiterals.php).

**Ziel:** Alten eigenständigen Writer stilllegen; vorhandenen Workbench-Planer/Applier als Grundlage des einzigen freigegebenen Code-Änderungswegs verwenden.

### F07 — Wiederholung erzeugt fachlich unnötige Änderungsereignisse · mittel · reproduziert

Identische Texte in verschiedenen Dateien können im alten Entry-Modell denselben Fingerprint besitzen. Quellenabhängige Attribute werden pro Vorkommen überschrieben und springen beim nächsten Scan erneut um. Der alte Sprachwertimport protokolliert auch eine bloße Veränderung von `last_seen_at` als Wertänderung. In der Foundation-Bestandskopie erzeugte selbst der dritte unveränderte Lauf noch zwei Metadaten-Änderungsereignisse für Key 7230.

Belege: `repeated_legacy_scan_identical_source`, `legacy_import_identical_source_relogs_observation`, [foundation-replay.json](foundation-replay.json).

**Ziel:** Beobachtung und fachliche Änderung trennen; identische Wiederholung darf keine erneute fachliche Mutation erzeugen. Die gesamte historische Ereignismenge wurde nicht rückwirkend als unnötig klassifiziert.

### F08 — Fundstellenidentität kollabiert unterschiedliche Vorkommen · mittel · reproduziert

Zwei identische Aufrufe in derselben Datei und Zeile ergeben nur ein Scanner-Item. Auch zwei Literale mit unterschiedlicher innerer Leerzeichenanzahl können durch die normalisierte Signatur auf derselben Zeile zusammenfallen. Positionsidentität und Textvergleich werden damit vermischt.

Belege: `same_line_identical_occurrences`, `distinct_literal_whitespace_on_same_line`; [FingerprintFactory](../../../packages/gunreip/laravel-translation-workbench/src/Scanner/TranslationFingerprintFactory.php), [Scanner](../../../packages/gunreip/laravel-translation-workbench/src/Scanner/TranslationWorkbenchScanner.php).

**Ziel:** Konkrete Vorkommen anhand syntaktischer Position identifizieren; exakten Rohtext erhalten. Eine gesonderte Vergleichsnormalisierung darf nicht die unveränderliche Erfassung ersetzen.

### F09 — Shared-Kandidatenerkennung entspricht noch nicht dem neuen SOLL · mittel · Codebefund

Das aktuelle `detect-shared-key-candidates` nutzt frühere Bulk-Shared-Entscheidungen als Grundlage. Es implementiert nicht allgemein „mindestens zwei aktive gleichlautende Literale mit unterschiedlichen SuggestedKeys → sharedCandidate; unter zwei → Markierung entfernen“.

Beleg: [DetectSharedKeyCandidates.php](../../../packages/gunreip/laravel-translation-workbench/src/Console/DetectSharedKeyCandidates.php).

**Ziel:** Die im Target Workflow UI festgelegte Regel als abgeleitete, jederzeit neu berechenbare Kandidatenklassifikation umsetzen. Eine Markierung darf noch keine automatische Key-Zusammenführung sein.

### F10 — Legacy-Sync kann Berichte verschiedener Läufe mischen · mittel · Codebefund

`project:translations` startet den DB-Sync nach dem Code-Audit, während Compare-/Lang-Berichte aus einem früheren Lauf vorliegen können. Der Sync prüft keine gemeinsame Run-ID oder passende Aktualität. Fehlende/ungültige Berichte werden als leere Listen eingelesen. Die Legacy-Stale-Behandlung überspringt leere Seen-Listen; daher wird hier ausdrücklich **nicht** behauptet, fehlende Berichte allein würden alles archivieren.

Belege: [ProjectTranslations.php](../../../app/Console/Commands/ProjectTranslations.php), [SyncTranslationAudits.php](../../../app/Console/Commands/SyncTranslationAudits.php).

**Ziel:** Ein konsistenter Scan-Snapshot pro Analyse-/Sync-Lauf; Berichte werden daraus erzeugt und sind keine unversionierte Datenquelle für spätere Schreibentscheidungen.

### F11 — Lifecycle und Publication sind im Datenmodell nicht ausreichend getrennt · hoch · Struktur-/Codebefund

`lang_values` ist gleichzeitig Dateiimport und Editor-Speicher. Der Writer setzt gespeicherte skalare Werte active; ein eigenständiger unveränderlicher Wertrevisions-/Publikationsstand fehlt in diesem Modell. Key-Review allein beschreibt nicht die Freigabe einer bestimmten Sprachwertrevision. Findings speichern Roh-Ausdrücke und Historie, sind aber selbst veränderliche Zeilen, keine ausdrücklich unveränderliche Erfassungsablage.

Zusätzlich können Rekey-Konflikte zugunsten eines vorhandenen Zielwerts aufgelöst werden, indem der alte Wert obsolete wird; eine universelle fachliche Konfliktentscheidung ist damit nicht automatisch gegeben. Diese bestehende Regel wird teilweise durch Tests bestätigt und muss als SOLL-Frage bewusst neu entschieden werden, statt sie unbemerkt umzudeuten.

**Ziel:** Erfassung → Zuordnung → Sprachwertrevision → Review → Veröffentlichung mit jeweils klarer Zuständigkeit. Alte und neue Werte samt Herkunft erhalten.

### F12 — Weitere Lifecycle-/Betriebsgrenzen · mittel · gemischte Evidenz

- Verschwindet die letzte Sprachdatei einer Locale, kann ein zuvor importierter Wert aktiv bleiben (`last_language_file_removed_leaves_active_value`). Auch hier fehlt ein vollständiger Quellenumfang.
- Kommentierte Aufrufe werden als `commented_out` erfasst; im Test entsteht dabei kein aktiver Key. Das weicht vom neuen SOLL „auskommentiert ignorieren“ ab, ist aber keine aktive Übersetzung aus einem Kommentar.
- Die dynamische Klassifikation löscht fehlende SourceValues, während andere dynamische Wege stale markieren. Eine einheitliche Aufbewahrungs-/Beobachtungspolitik fehlt.
- Der Pipeline-Lock ist an eine übergebene Run-ID gebunden. Unabhängige CLI-Aufrufe und andere Run-IDs besitzen keine gemeinsame Veröffentlichungssperre. Ein Parallelitätsfehler wurde nicht auf dem Live-System provoziert.

## Was bereits zuverlässig funktioniert und erhalten bleiben sollte

- Der isolierte Foundation-Sync erhält einen reviewed-Key samt geprüftem Namen. Nach einer einmaligen Metadatenangleichung ist dieser kleine Fall wiederholbar ohne weitere Ereignisse.
- Auf der tatsächlichen Bestandskopie blieben vorhandene Sprachwerte und Reviews über alle drei Foundation-Syncs unverändert. Ohne Obsolete-Option änderten sich die verglichenen vorhandenen Key-Namen, Review- und Statuswerte nicht.
- Mit Obsolete-Option wurden 39 Findings, 16 Keys und 40 Beziehungen archiviert. Das ist nicht pauschal ein Fehler: Ein konkret nachgeprüfter alter `ui.save`-Aufruf ist inzwischen durch eine Komponente ersetzt und auskommentiert.
- Der vorhandene Code-Update-Planer/Applier hat Review-/Quellstandprüfungen und Preflight. Der alte Export besitzt bereits eine Konfliktprüfung gegenüber aktiven Workbench-Werten. Solche Schutzmechanismen sollen beim Zusammenführen erhalten bleiben.
- UI-Pipelineanforderungen besitzen Zugriffskontrollen, gespeicherte Optionen und Betriebsprotokolle.
- Bestehende Workbench-Tests bestehen vollständig. Sie sichern jedoch die hier neu untersuchten negativen Fälle nicht ausreichend ab; „grün“ ist deshalb kein Nachweis für die Sicherheit jedes Gesamtworkflows.

## Datenbank: erhalten, konsolidieren oder neu aufsetzen?

Die 33 Tabellen belegen zusammen **3.123,98 MiB** einschließlich Indizes und TOAST. Allein die alte Workbench-Ereignistabelle umfasst **1.340.120 Zeilen / 2.286,62 MiB**. Die Breite des Modells und das Wachstum stammen also nicht vorwiegend aus redaktionellen Übersetzungstexten.

Die wichtigsten fachlichen Auffälligkeiten sind 12 aktive Beziehungen zu obsoleten Findings, zwei aktive Sprachwerte ohne nicht-obsoleten Key-Besitzer und zwei Findings mit jeweils zwei unterschiedlichen expliziten Key-Namen. Daneben unterscheiden sich 202 über Key/Locale vergleichbare Legacy-/Workbench-Wertepaare. Diese sind vor einer Migration einzeln bzw. nach nachvollziehbarer Regel abzustimmen.

Mehrere Key-Zeilen mit gleichem Namen sind im aktuellen Shared-Modell teilweise beabsichtigt. Sie dürfen nicht blind dedupliziert werden. Die vollständigen Zahlen und Einordnungen stehen in [database.md](database.md).

| Bereich | Empfehlung |
| --- | --- |
| Findings, vorhandene Rohinformationen, Reviews, fachliche Historie | Erhalten und in klare Erfassungs-/Revisionsverträge überführen |
| Legacy-Keys/Values und alte Workbench-Entries | Schreibwege ablösen; zunächst lesbar archivieren, exklusive Werte und Entscheidungen nachweisen |
| Skalarer Sprachwertspeicher / Import / Publication | Zuständigkeiten und Revisionen neu modellieren; kontrolliert migrieren |
| Scan-Identität und Scope | Gemeinsamen Vertrag neu aufbauen; bestehende Scanner-Fähigkeiten übernehmen und testen |
| Inventar, Timeline-Chains, Kandidaten | Als abgeleitete Projektionen behalten; nach Kernmigration gezielt neu aufbauen |
| Große Ereignistabellen | Zunächst erhalten; später eigenständiger Archivierungsplan nach Leser-/Provenienzprüfung |
| Leere `key_values` | Nicht allein wegen Leerstand löschen; Referenzen und verbleibenden Vertragszweck prüfen |

## Empfohlene Umsetzung in abgegrenzten Etappen

Die anschließende [Zuständigkeitsmatrix und Umstellungsreihenfolge](responsibilities.md) ordnet alle 46 Commands konkreten Zielverantwortlichkeiten zu. Sie ist die Implementierungsgrundlage; die hier dokumentierten Audit-Befunde bleiben unverändert.

1. **Schreibzuständigkeiten festlegen und alte Einstiegspunkte absichern.** Alte Writer und Projekt-Wrapper dürfen nicht unbemerkt denselben Bestand weiter pflegen. Vor Änderungen konkrete Aufrufer prüfen; keinen vorhandenen Benutzerweg stillschweigend entfernen. Bis dahin keine unkontrollierten produktiven Complete-/Legacy-Pipeline-Läufe empfehlen.
2. **Die reproduzierten Risiken als echte Regressionstests formulieren.** Erwartet werden der Erhalt außerhalb des Scope, Erkennung der letzten entfernten Fundstelle, Schutz unveröffentlichter Werte, Konflikt-Exit-Code und keine kontextlose Umschreibung. Die Audit-Skripte beschreiben das heutige Verhalten; sie sind keine Tests, die dieses Verhalten als richtig festschreiben.
3. **Einen gemeinsamen Scan-Run einführen.** Einmal lesen, vollständigen Dateiumfang und Fehler erfassen, konkrete Vorkommen und Rohdaten speichern; alle Analysen lesen denselben Snapshot. Erneute unveränderte Beobachtung erzeugt keinen fachlichen Änderungslog.
4. **Bearbeitung und Publication trennen.** Versionierte Sprachwerte/Freigaben sowie beobachteten Dateistand definieren. Import-Differenzen als Konflikt bzw. bewusste Übernahme behandeln. Export und Code-Ersetzung aus einem geprüften Plan ausführen.
5. **Bestand mit Zuordnungstabellen migrieren.** Herkunft, alte IDs, Reviews, unveröffentlichte Werte und fachliche Historie erhalten. Die 202 unterschiedlichen Vergleichspaare und weiteren Auffälligkeiten vor einer Autoritätsentscheidung auswerten. Kein Zurückschreiben sämtlicher Keys in Literale als Voraussetzung.
6. **Auf einer vollständigen PostgreSQL-Kopie abnehmen.** Ganze Pipeline, Fehlerfälle, Wiederholung, parallele Ausführung und Veröffentlichung testen; Vorher-/Nachher-Differenzen für Werte, Status, Freigaben und Dateien prüfen. Erst danach produktive Umschaltung und spätere Archivierung.

Der Target Workflow UI bleibt dabei die fachliche Orientierung. Wo der bestehende technische Vertrag davon abweicht, muss eine explizite Entscheidung fallen. Neue Tabellen- und Command-Namen sollten erst nach dieser Zuständigkeitsentscheidung festgelegt werden.

## Reproduktion und Grenzen

Die isolierten Charakterisierungen können vom Repository-Root ausgeführt werden:

```bash
php -d memory_limit=1G docs/audits/translation-workbench-2026-10-15/reproduce.php
php -d memory_limit=1G vendor/bin/pest tests/Unit/TranslationWorkbench --compact
```

Die Datenbank-Inventarisierung verwendet in [inspect-database.php](inspect-database.php) eine PostgreSQL-Transaktion `REPEATABLE READ READ ONLY`. [database-checks.php](database-checks.php) erstellt aus ausgewählten Tabellen eine private SQLite-Kopie unter `/tmp`. Die Replay-Skripte nehmen nur einen solchen Snapshot-Pfad an und erstellen davon jeweils eine weitere Arbeitskopie:

```bash
php -d memory_limit=1G docs/audits/translation-workbench-2026-10-15/replay-foundation.php /tmp/tw-status-snapshot-YYYYMMDD-HHMMSS.sqlite
php -d memory_limit=1G docs/audits/translation-workbench-2026-10-15/replay-import.php /tmp/tw-status-snapshot-YYYYMMDD-HHMMSS.sqlite
```

Die Replay-Kopie enthält die tatsächlich übernommenen Foundation-Quellen, Findings, Keys, Beziehungen, Sprachwerte, dynamischen Key-Werte, Reviews und Event-Typen. Umfangreiche Ereignistabellen und andere Tabellen wurden nicht vollständig kopiert; Schema-/FK-Integrität wurde separat am echten PostgreSQL-Bestand gelesen. Die SQLite-Kopie bildet insbesondere PostgreSQL-Typen, vollständige Constraints und Parallelität nicht vollständig nach.

Es wurde **kein vollständiger Complete-Workflow gegen eine vollständige PostgreSQL-Kopie** ausgeführt. PostgreSQL-spezifische Klassifikationspfade, alle UI-Interaktionen und externe Cron-/Deployment-Aufrufe sind damit nicht vollständig abgenommen. Die Live-Prüfung umfasste auch keine komplette nachträgliche fachliche Bewertung sämtlicher historischer Ereignisse. Ein gesamter Datenverlust oder eine Beschädigung aller bisherigen Korrekturen wurde nicht festgestellt und wird nicht behauptet.
