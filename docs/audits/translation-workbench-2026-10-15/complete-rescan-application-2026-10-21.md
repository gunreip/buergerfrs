# Transaktionale Folgescans – 21.10.2026

## Umfang

Der vollständige Scan-Abgleich wird auf einer isolierten Kopie der aktuellen
Generation jetzt auch transaktional gespeichert. Der neue `CompleteRescanWriter`
liegt ausschließlich unter `tests/Support`; kein produktiver Command und keine
Workbench-Umschaltung wurden vorgenommen. Die gesicherte Ausgangsgeneration und
der originale Quellcode bleiben unverändert.

Die Speicherung verwendet das vorhandene `BaselineOperation`-Protokoll. Keine
zusätzlichen Tabellen oder konkurrierenden produktiven Schreibwege wurden eingeführt.

## Geprüftes Verhalten

31 Assertions im tatsächlichen PostgreSQL-Lauf bestanden:

- Ein vollständiger unveränderter Folgescan bestätigt 18.878 Findings, erzeugt
  keine neuen Findings und verschiebt oder stilllegt keine. Die 39 Dateien mit
  ungeklärten Erfassungen bleiben zurückgestellt.
- Die Prüffälle einschließlich erfasster Rohinformationen, erhaltenen Finding-IDs,
  Dateiinventur und Verlagerungskandidaten stehen im `scope_snapshot` des Scan-Laufs.
  Sie sind damit gespeichert, aber noch nicht als bearbeitbare UI-Aufgaben angeschlossen.
- Derselbe Operationsauftrag liefert das gespeicherte Ergebnis wieder, ohne
  zusätzliche Scan-/Finding-/Link-Zeilen. Eine geänderte Anfrage mit derselben ID wird abgewiesen.
- Ein absichtlicher Fehler nach der ersten gespeicherten Verschiebung rollt sämtliche
  Tabellenänderungen zurück. Der anschließende Wiederanlauf mit derselben ID gelingt.
- Der Nachfolger übernimmt den bestehenden Key-Link; der Vorgänger wird stillgelegt.
  Die ursprünglichen Suggested-Felder werden bei einer Verschiebung übernommen.
- Änderungen am Quellcode zwischen Planung und Anwendung werden abgewiesen.
- Eine simulierte Quellcodeänderung während der Speicherung führt ebenfalls zum
  vollständigen Rollback. Nach Wiederherstellung der Ausgangsdatei gelingt der Wiederanlauf.
- Ein Plan mit inzwischen geändertem Datenbankstand wird abgewiesen.
- Ungeklärte Ausdrücke erhalten die vorherigen Findings und werden als Prüffall gespeichert.
- Eine im vollständigen Verzeichnisinventar belegte Dateilöschung legt die betroffene
  Fundstelle still und beendet ihren aktiven Key-Link.
- Wiederauftauchen erzeugt ein neues Finding ohne erfundenen Vorgänger. Neue Key-Fundstellen
  erhalten in diesem Prüfwriter weiterhin keine automatische Key-Zuordnung.
- Bestehende Keys, Key-Namen, veröffentlichte Werte und Findings außerhalb der
  gezielt veränderten Datei bleiben unverändert.
- Keine historischen oder erfundenen Benutzerereignisse werden importiert.

Rollback und Wiederholung wurden über Fingerprints aller Tabellen geprüft.
PostgreSQL-Sequenzen dürfen bei zurückgerollten INSERTs Nummernlücken behalten;
behauptet wird unveränderter Tabelleninhalt, nicht lückenlose ID-Vergabe.

## Konsistenzgrenzen

Der Writer sperrt die beteiligten Tabellen und prüft den Datenbankstand vor dem
Schreiben erneut. Der Quellplan wird neu berechnet und mit dem vorbereiteten
Plan verglichen. Vor dem Commit werden Dateihashes und negative Dateinachweise
erneut kontrolliert. Das Dateisystem und PostgreSQL bilden keine gemeinsame
Transaktion: Eine Änderung nach dieser letzten Dateiprüfung bleibt technisch
möglich und muss durch den nächsten Scan erkannt werden.

Wiederholung desselben Auftrags ist von einem neuen Scannerlauf zu unterscheiden:
Ein Replay liefert den damaligen Abschluss zurück. Ein neuer Lauf benötigt eine
neue Operations-ID und einen neu vorbereiteten Plan.

Die Abbruchprüfung verwendet eine gezielt geworfene Exception innerhalb der
Transaktion. Ein tatsächlicher Prozessabbruch sowie echte parallele Prozesse
wurden in dieser Serie noch nicht getestet. Die Sperren allein ersetzen diesen
Nachweis nicht. Offene Prüffälle stehen derzeit im Scan-Snapshot, nicht in einem
separaten entscheidbaren UI-Workflow. Die fachliche Datenbearbeitung bleibt zurückgestellt.

## Artefakte

- Runner: `rehearse-complete-rescan-application.php` in diesem Verzeichnis.
- Privater Ergebnisbericht: `storage/app/private/dev/workbench-rehearsal/current-generation-20261020-192844-f86176d77474/rescan_3836b6d750db/result.json`.
- Ergebnisdump und veränderte Quellkopie liegen neben dem Bericht.
- Der isolierte Prüfcluster wurde gestoppt.
- PHP-Syntaxprüfung, Pint und `git diff --check` bestanden.

Im ersten Prüflauf wurde ein Testvergleich korrigiert: JSONB ändert die Reihenfolge
von Objektschlüsseln. Die Prüfung vergleicht jetzt den Inhalt, statt gleiche
Schlüsselreihenfolge zu verlangen. Der vollständige Lauf wurde danach auf einer
frischen Datenbankkopie erfolgreich wiederholt.

## Nächster Prüfblock

Echte parallel gestartete Aufträge sowie Prozessabbruch/Wiederanlauf auf der
isolierten Generation testen. Anschließend die gespeicherten Prüffälle und ihre
Entscheidungen an die Workbench anschließen; eindeutige Key-Zuordnung neuer
Fundstellen und Shared-Candidate-Zustände weiterhin gesondert absichern.
