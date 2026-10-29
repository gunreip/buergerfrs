# Aktueller Scan: isolierter Generation-Import

Stand: 20.10.2026. Der aktuelle Quellcode bildet die Ausgangsbasis. Kein historischer Datenbankbestand und keine historische Timeline wurden übernommen. Die reguläre Workbench ist noch nicht umgeschaltet.

## Nachweis

- Scan: `20261020-192821-b3d8958008b8`
- Generation: `614f31eb-bea8-4cd4-9375-cf4c7ff9ec8a`
- Schema: `current_generation_v2` in einer separaten PostgreSQL-Datenbank ohne TCP-Zugriff.
- Privater Import einschließlich vollständiger Quellcode-Sicherung und `generation.dump`: `storage/app/private/dev/workbench-rehearsal/current-generation-20261020-192844-f86176d77474`
- Maschinenlesbarer aktueller Bericht: `storage/app/private/workbench-previews/current-generation.json`.
- Der Prüfcluster wurde anschließend gestoppt; es handelt sich noch nicht um die laufende Workbench-Datenbank.

## Bestand

| Bestandteil | Anzahl | Behandlung |
| --- | ---: | --- |
| Bestätigte Fundstellen | 20.334 | Mit unveränderten Rohfeldern und Quellposition importiert |
| Davon neue Literal-Fundstellen | 18.127 | Eigenständige Findings, keine Zusammenlegung nach Text |
| Wiederverwendbare Keys | 768 | Exakter Key und belegter Source-Locale-Wert |
| Zugeordnete Key-Fundstellen | 1.792 | Explizite Links |
| Strukturell blockierte Key-Fundstellen | 415 | Erfasst, ohne automatische Key-Zuordnung |
| Ungeklärte Erfassungen | 72 | 10 Key-Nachweise und 62 Ausdrücke; im Erfassungsledger, nicht als aktive Findings |
| Ausgeschlossen | 112 | Erfassungsledger, nicht aktiv |
| Zugeordnete veröffentlichte Locale-Werte | 1.597 | Gesonderte veröffentlichte Evidenz, keine erfundenen Bearbeitungsrevisionen |
| Werte mit blockierter Key-Struktur | 22 | Unverändert als Evidenz erhalten |
| Werte ohne aktuellen Key-Verweis | 397 | Nicht importiert; Originaldateien in Sicherung erhalten |

Die 72 ungeklärten Erfassungen behalten ihre vollständigen Rohinformationen im gesicherten JSONL-Nachweis. Das Datenbankledger enthält ihren Prüfstatus und ihre Capture-ID. Sie sind noch keine direkt bearbeitbaren Workbench-Findings. Die 415 strukturell blockierten Fundstellen und die 10 ungeklärten Key-Nachweise ergeben die 425 Key-Prüffundstellen der Vorschau.

## Prüfung

144.343 Prüfassertionen im tatsächlichen Importlauf bestanden:

- Alle importierten Roh-/Suggested-Felder und Quellpositionen zurückgelesen.
- Manipulierte Nachweise abgelehnt, keine partiellen Key-/Wertimporte.
- Derselbe Import erneut ausgeführt: keine zusätzlichen Datensätze oder Ereignisse, unveränderte Tabellen-Fingerprints.
- Datenbankdump wiederhergestellt: alle Tabellen-Fingerprints und Schema-Prüfung stimmen überein.
- Deaktivierter interner Fremdschlüssel-Trigger wird weiterhin erkannt.
- Ein Scan-Lauf, keine historischen Events und keine erfundenen Autorenrevisionen.

Zusätzlich bestanden drei Unit-Tests mit 16 Assertions sowie 10 Key-Plan- und 6 Published-Baseline-Assertions. PHP-Syntaxprüfung, Pint und `git diff --check` bestanden.

Bei der Wiederherstellungsprüfung wurde ein Fehler im Schemakatalog-Vergleich korrigiert: interne PostgreSQL-Triggernamen enthalten installationsabhängige OIDs. Diese werden nun strukturell verglichen; zusammengesetzte Identitäten bleiben vollständiger Text statt auf den PostgreSQL-Typ `name` verkürzt zu werden. Triggerverhalten, Fremdschlüsselidentität und Aktivierungszustand bleiben geprüft. Bestehende Prüfsignaturen alter Testgenerationen wurden nicht umgeschrieben.

## Nächster Schritt

Die Workbench muss kontrolliert an diese Generation angeschlossen werden, einschließlich sichtbarer Prüfzustände und ihrer Bearbeitung. Danach ist ein neuer Scannerlauf mit tatsächlich geändertem Quellcode zu prüfen: unverändert, verschoben, hinzugekommen und entfernt. Der erfolgreiche Wiederholungsimport desselben Nachweises belegt noch nicht die Abstimmung eines späteren geänderten Scans.

Keine Quellcode-Keys ersetzt, keine Sprachdateien veröffentlicht und keine produktiven Daten gelöscht oder überschrieben.
