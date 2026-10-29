# Sicherung und Wiederherstellungsprüfung

Am 2026-10-19 erfolgreich ausgeführt. Die Anwendungsdatenbank wurde ausschließlich in READ-ONLY-Transaktionen gelesen. Wiederherstellung und Prüfungen liefen in einem frisch erzeugten privaten PostgreSQL-Cluster ohne TCP-Listener.

| Prüfung | Ergebnis |
| --- | --- |
| Vollständiger logischer Datenbank-Dump | 326.519.713 Bytes, PostgreSQL Custom-Format |
| Ausgangsdatenbank | 3.367.474.879 Bytes belegter Speicher |
| Tabelleninhalte | 79 Tabellen, 2.630.711 Zeilen identisch |
| Spaltenstruktur | Identisch |
| Sequenzstände | 69 identisch |
| Source-/Projektdateien | 3.595 gesichert; Hashvergleich vor/nach Sicherung identisch |
| Stabilität während Aufnahme | Tabelleninhalte, geprüfte Struktur und Sequenzen unverändert |
| Restore | pg_restore --exit-on-error erfolgreich; danach Inhaltsvergleich |
| Testcluster | Nach Prüfung gestoppt; Kopie bleibt vorhanden |
| Temporäre Zugangsdaten | .pgpass entfernt |
| Dateirechte | Sicherungsverzeichnis 0700, Dump 0600 |

## Speicherort

Privates, Git-ignoriertes Verzeichnis:

`storage/app/private/dev/workbench-rehearsal/20261019-075456-18dfd037/`

- `database.dump`: vollständiger logischer Dump der Anwendungsdatenbank aus einem exportierten PostgreSQL-Snapshot.
- `source/`: versionierte sowie nicht ignorierte unversionierte Projektdateien zum gesicherten Stand.
- `manifest.json`: Hashes, Tabellen-/Zeilennachweise, Struktur, Sequenzen und Pfad zum gestoppten Restore-Cluster.

Der gestoppte Restore-Cluster liegt unter `/tmp/tw-restore-02a13da822d9ebe0`. Er ist eine Arbeitskopie, keine dauerhafte Backupablage. Die dauerhafte Ausgangssicherung ist der private Dump mit Source-Kopie und Manifest. Es existiert in diesem Schritt keine zusätzliche Offsite-Sicherung.

Der [öffentliche Ergebnisnachweis](backup-restore-result-2026-10-19.json) enthält ausschließlich Metadaten/Hashes, keine Datenbankinhalte oder Zugangsdaten. Der Dump-Hash wurde nach Abschluss nochmals geprüft. Keine Sicherungsdaten wurden nach Git übernommen.

## Vergleichsverfahren und Grenzen

Pro Tabelle wurden MD5-Zeilenhashes der PostgreSQL-JSON-Darstellung als sortierte Multimenge verglichen, einschließlich mehrfach vorkommender identischer Zeilen und NULLs. Die sortierten Hashfolgen sind zusätzlich mit SHA256 gesichert. Das ist eine praktische Integritätsprüfung, kein kryptographischer Beweis gegen absichtlich konstruierte MD5-Kollisionen. Zeilenzahlen, Spaltenmetadaten und Sequenzstände wurden zusätzlich verglichen.

DB-Dump und erster DB-Inhaltsvergleich verwendeten denselben exportierten REPEATABLE-READ-Snapshot. Ein weiterer frischer schreibgeschützter Vergleich prüfte Veränderungen während der Aufnahme. Die Source-Dateihashes wurden vor/nach Aufnahme verglichen. Dies belegt beobachtete Stabilität; es wurde kein produktiver Wartungsmodus oder vollständiger Anwendungsschreibstopp aktiviert.

Der logische Dump ist **kein vollständiges Server-/Deployment-Backup**:

- Keine globalen PostgreSQL-Rollen; Restore mit isoliertem Benutzer, `--no-owner --no-privileges`. Produktive Eigentümer/Rechte wurden nicht wiederhergestellt oder als identisch bestätigt. Der Dump selbst wurde ohne diese Ausschlüsse erstellt.
- Keine `.env`-Dateien, Uploads/Personendokumente, externen Speicherdienste, installierten `vendor`-/`node_modules`-Verzeichnisse oder Git-Objektdatenbank in der Source-Kopie. Abhängigkeits-Lockdateien sind enthalten.
- Die Restore-Kopie darf nicht als normal gestartete Anwendung mit produktiver Konfiguration betrieben werden. Queue-/Scheduler-/Mail-/Publikationswege müssen vor einer späteren Anwendungsausführung gesondert isoliert werden.
- Es wurde noch keine neue Workbench-Generation aufgebaut und keine alte Timeline in einen neuen Datenbestand übernommen.

## Weiteres Vorgehen

Die Eingangssicherung ist geprüft. Als Nächstes wird ein **separates Evidenz-Staging auf der Kopie** aufgebaut: unveränderte Herkunft, Text- und ID-Nachweise, offene Zuordnungen getrennt von aktivem Arbeitsbestand. Die restaurierte Ausgangskopie bleibt Vergleichsbasis. Erst danach folgen der echte Neuaufbau und die Workflow-Generalprobe.

Das frühere Vorbereitungsmanifest bleibt als historischer Stand unverändert. Die dortigen ersten beiden offenen Schritte sind durch diesen separaten Ergebnisnachweis erfüllt; die Gesamtgeneralprobe und der produktive Wechsel sind weiterhin offen.
