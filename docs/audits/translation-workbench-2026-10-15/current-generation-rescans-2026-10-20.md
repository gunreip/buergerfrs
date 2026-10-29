# Scannerlauf-Serie auf aktueller Generation – 20.10.2026

Vor der fachlichen Datenbearbeitung wird das Verhalten weiterer Scans überprüft.
Produktive Daten, Quellcode und Sprachdateien wurden nicht geändert. Die Testdatei
wurde ausschließlich in einer privaten Kopie verändert. Eine neue isolierte
PostgreSQL-Datenbank wurde aus dem geprüften Generation-Dump wiederhergestellt.
Der Prüfcluster wurde abschließend gestoppt.

## Vollständige Projekt-Scans

Verglichen wurden die Erfassung der Importgrundlage und zwei neue Scannerläufe:

- `20261020-192821-b3d8958008b8`
- `20261020-201422-7c13b0a758b1`
- `20261020-201449-dd09beb5c281`

Alle liefern dieselben 20.518 Erfassungen aus 1.717 Quelldateien und 54
Sprachdateien. Scope, sämtliche Dateihashes, Befundzahlen, Gruppenindizes und
die vollständigen Kandidatenbytes stimmen überein. Kandidaten-SHA256:
`18fcafa62a6383895197fdb104e3fb627331701a2f9423cccd18b9163c140532`.
Die beiden neuen vollständigen Scans sind Erfassungsnachweise, keine erneuten
Vollimporte in die Datenbank. Die Datenbank-Abstimmung wurde separat anhand
der folgenden tatsächlichen Dateiänderungen geprüft.

## Aufeinanderfolgende Änderungen

Echter Scanner und bestehender `CopiedSourceRescan`-Prüfadapter auf der aktuellen
Generation, anhand einer kopierten Quelldatei mit bestehender Key-Verknüpfung:
`packages/gunreip/laravel-translation-workbench/resources/views/livewire/entries/findings/table-work-findings/cell-literal.blade.php`.

| Lauf | Bestätigt | Neu | Verschoben | Stillgelegt | Ausgeschlossen |
| --- | ---: | ---: | ---: | ---: | ---: |
| unchanged | 1 | 0 | 0 | 0 | 0 |
| unchanged_again | 1 | 0 | 0 | 0 | 0 |
| moved | 0 | 0 | 1 | 0 | 0 |
| additional_copy | 1 | 1 | 0 | 0 | 0 |
| removed | 0 | 0 | 0 | 2 | 0 |
| reappeared | 0 | 1 | 0 | 0 | 0 |
| literal_changed | 0 | 1 | 0 | 1 | 0 |
| changed_literal_unchanged | 1 | 0 | 0 | 0 | 0 |
| literal_replaced | 0 | 1 | 0 | 1 | 0 |
| column_moved | 0 | 0 | 1 | 0 | 0 |
| commented_out | 0 | 0 | 0 | 1 | 1 |
| commented_out_again | 0 | 0 | 0 | 0 | 1 |
| literal_reappeared | 0 | 1 | 0 | 0 | 0 |
| final_unchanged | 1 | 0 | 0 | 0 | 0 |

14 abgeschlossene Datei-Scannerläufe; zusätzlich vier kontrolliert abgewiesene
Versuche: mehrdeutige Verschiebung identischer Aufrufe, dynamisch ungeklärter
Ausdruck, nicht vorhandene Datei und explizit ausgeschlossene Datei.

44 Assertions bestanden, darunter:

- Unverändert bedeutet gleiche Finding-ID mit Bestätigung in einem neuen Scan.
- Eindeutige Zeilen-/Spaltenverschiebung: neuer Nachfolger, alter Datensatz stillgelegt; vorhandene Key-Verknüpfung übertragen und alter Link beendet.
- SuggestedKey und ursprüngliche Rohfelder werden nicht überschrieben.
- Zusätzliche Kopie bleibt ein eigenes Finding ohne erratene Zuordnung.
- Textänderung erzeugt ein neues Finding, keine behauptete Verschiebung.
- Entfernen oder Auskommentieren beendet die aktive Fundstelle und deren aktive Links.
- Wiederauftauchen reaktiviert keinen stillgelegten Datensatz.
- Abgewiesene Versuche verändern keine Tabelleninhalte (vollständige Tabellen-Fingerprints verglichen).
- Keys, veröffentlichte Werte und Findings außerhalb der geprüften Datei unverändert.
- Anzahl der Scan-Datensätze entspricht dem ursprünglichen Import plus jedem erfolgreich abgeschlossenen Datei-Scan.

## Nachvollziehbarkeit und offene Grenzen

Das Scan-Journal enthält neue Scan-IDs, Quellhashes, Ergebniszahlen und bestätigte
Finding-IDs. Nachfolger verweisen auf Vorgänger; Stilllegungen und Link-Enden
sind gespeichert. Es werden keine historischen oder erfundenen Benutzeraktionen
in die Timeline eingetragen. Die spätere Timeline-Darstellung dieser
Scan-Vorgänge wurde hier noch nicht geprüft.

Dies ist eine bestandene Prüfserie, noch keine Freigabe des regulären
Produktions-Scannercommands. Der Adapter liegt bewusst unter `tests/Support`.

Vor der Datenbearbeitung bleiben insbesondere zu prüfen bzw. anzuschließen:

1. Abgleich eines vollständigen neuen Scans mit der Generation, auch wenn
   einzelne Dateien ungeklärte Erfassungen enthalten. Der aktuelle Datei-Adapter
   weist solche Dateien komplett zurück; er erzeugt noch keinen sichtbaren UI-Prüffall.
2. Tatsächlich gelöschte oder umbenannte Dateien anhand einer nachweislich
   vollständigen Dateiinventur. Eine fehlende Datei allein gilt derzeit zu Recht
   nicht als Löschfreigabe. Dateiverlagerungen werden noch nicht verknüpft.
3. Neue Fundstellen vorhandener Keys: getrennt erfasst, aber noch ohne automatische
   Zuordnung. Diese muss über einen eindeutigen Key-Nachweis oder eine UI-Entscheidung
   erfolgen, nicht über gleichlautenden Text.
4. Mehrdeutige Verschiebungen, Shared-Candidate-Zustände sowie die spätere
   Workbench-/Timeline-Darstellung der Prüfentscheidungen.
5. Abbruch, Wiederanlauf und konkurrierende komplette Scannerläufe auf genau
   dieser neuen Generation. Die vollständigen Rollback-Prüfungen dieser Serie
   ersetzen diese zusätzlichen Ablaufprüfungen nicht.

Die fachliche Aufarbeitung und die Umschaltung der regulären Workbench bleiben
bis zur Absicherung dieses Scan-Ablaufs zurückgestellt.

## Artefakte

- Wiederholbarer Runner: `rehearse-current-rescans.php` in diesem Verzeichnis.
- Privater detaillierter Ergebnisbericht einschließlich Scan- und Finding-IDs:
  `storage/app/private/dev/workbench-rehearsal/current-generation-20261020-192844-f86176d77474/rescan_2fab5e818ffa/result.json`.
- Ergebnisdump und Quellkopie liegen neben diesem Bericht; keine Verbindung zur produktiven Datenbank.
- PHP-Syntaxprüfung, Pint und `git diff --check` bestanden.
