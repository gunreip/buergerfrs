# Parallele Scanner-Aufträge und Prozessabbruch – 21.10.2026

## Aufbau

Zwei eigenständige PHP-Worker verwenden den bestehenden `CompleteRescanWriter`
auf einer neu isoliert wiederhergestellten Kopie der aktuellen Generation.
Der Quellcode wird ausschließlich in einer privaten Kopie verändert. Der erste
Worker hält seine Transaktion nach einer tatsächlichen Finding-Verschiebung
und Link-Übertragung offen. Bei Konkurrenz wird über `pg_stat_activity`
belegt, dass der zweite Worker tatsächlich auf eine Datenbanksperre wartet.
Es handelt sich nicht um eine lediglich nacheinander ausgeführte Simulation.

Es wurden keine produktiven Commands angeschlossen und keine produktiven
Datenbankzugangsdaten geladen. Der Writer selbst musste für diese Prüfserie
nicht geändert werden.

## Ergebnisse

30 Assertions bestanden:

| Fall | Nachgewiesenes Verhalten |
| --- | --- |
| Gleiche Operations-ID, gleicher Auftrag, parallel | Zweiter Prozess wartet; beide erhalten dasselbe Ergebnis; genau ein neuer Scan und ein Nachfolger |
| Unterschiedliche Operations-IDs, gleicher Ausgangsplan, parallel | Zweiter Prozess wartet; erster schließt ab; zweiter wird mit `Stale rescan database state` abgewiesen |
| Prozess nach Datenänderung, vor Commit mit SIGKILL beendet | PostgreSQL-Verbindung endet, Transaktion wird zurückgerollt, alle Tabellen-Fingerprints stimmen mit dem vorherigen Stand überein |
| Neuer Prozess nach SIGKILL, gleiche Operations-ID | Auftrag wird erfolgreich genau einmal gespeichert |
| Erneute Anfrage nach abgeschlossenem Worker | Gespeichertes Ergebnis wird zurückgegeben; keine weiteren Datenbankzeilen |

In jedem erfolgreichen Fall entsteht genau ein Nachfolger mit der bestehenden
aktiven Key-Verknüpfung. Keys, Key-Namen und veröffentlichte Übersetzungswerte
bleiben unverändert. Während der offenen Transaktion sind die Teiländerungen
für die andere Verbindung nicht sichtbar.

Die Prüfung beendet nur den von ihr selbst gestarteten Worker. Anschließend
wird ausdrücklich abgewartet, bis dessen PostgreSQL-Backend verschwunden ist.
Erst danach werden Tabellen-Fingerprints und Wiederanlauf geprüft. Aus der
Existenz von Sperren wird damit nicht nur auf korrektes Verhalten geschlossen;
das Verhalten wurde unter tatsächlicher Prozesskonkurrenz beobachtet.

## Grenzen

- Die Konkurrenzprüfung verwendet einen abgegrenzten Datei-Scope mit echten
  Schreibvorgängen auf der vollständigen Generation. Sie ist kein Last- oder
  Laufzeittest zweier vollständiger Projekt-Scans.
- PostgreSQL-Sequenzen dürfen durch zurückgerollte INSERTs Nummernlücken erhalten.
  Geprüft werden Tabelleninhalte und fachlich eindeutige Nachfolger, nicht lückenlose IDs.
- Der reguläre Scannercommand und die produktive Workbench sind weiterhin nicht umgeschaltet.
- Neue Key-Fundstellen, Shared Candidates und die Bearbeitung gespeicherter
  Prüfentscheidungen müssen noch gesondert abgesichert beziehungsweise angeschlossen werden.

Die technischen Prüfblöcke für vollständige Folgescan-Speicherung, Wiederholung,
Rollback, veraltete Pläne, echte Prozesskonkurrenz und Prozessabbruch sind damit
für den isolierten Writer durchlaufen. Das ersetzt nicht die spätere Prüfung
des produktiven Command- und UI-Anschlusses.

## Artefakte

- Runner: `rehearse-concurrent-rescans.php` in diesem Verzeichnis.
- Privater Ergebnisbericht: `storage/app/private/dev/workbench-rehearsal/current-generation-20261020-192844-f86176d77474/rescan_0d60ebedb0e9/result.json`.
- Ergebnisdump, Worker-Ergebnisse und Quellkopie liegen neben diesem Bericht.
- Testworker sind beendet, der isolierte PostgreSQL-Cluster wurde gestoppt.
- PHP-Syntaxprüfung, Pint und `git diff --check` bestanden.

Als nächster fachlicher Scanner-Prüfblock: neue Fundstellen bereits vorhandener
Keys ausschließlich anhand eindeutiger Key-Nachweise zuordnen und Shared-Candidate-
Zustände bei Hinzufügen/Entfernen gleichlautender Literale prüfen. Historische
Findings werden dabei weiterhin nicht reaktiviert oder miteinander verschmolzen.
