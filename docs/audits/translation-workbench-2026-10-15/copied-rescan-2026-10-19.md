# Erneute Scan-Zyklen auf einer separaten Kopie

Stand: 19. Oktober 2026. Ergebnis: `rescan_rehearsal_verified`.

Der Dump der vorherigen Probe-Erfassung wurde erfolgreich in eine **weitere neue Testdatenbank** restauriert. Geprüft wurde eine echte Quelldatei aus der Sicherung: `packages/gunreip/laravel-translation-workbench/resources/views/components/ui/tw-graph/code-box.blade.php`. Ausschließlich eine zusätzliche Arbeitskopie dieser Datei wurde verändert. Gesicherte Ausgangsdatei und produktiver Bestand blieben unverändert.

## Sieben erfolgreiche Scan-Zyklen

| Szenario | Ergebnis |
| --- | --- |
| Unveränderter Scan | Finding #291 bestätigt; keine neue Finding-ID. |
| Nochmals unverändert | Wieder #291; eigener Scan-Nachweis, keine Duplikate. |
| Zwei zusätzliche Zeilen vor der Datei | Neue Finding-ID #19942, Vorgänger #291 archiviert und über `predecessor_id` verknüpft; SuggestedKey erhalten. |
| Zusätzlicher gleichlautender Aufruf | #19942 bestätigt, separate neue Fundstelle #19943; kein Zusammenlegen. |
| Alle Aufrufe aus der Datei entfernt | Beide aktiven Fundstellen archiviert. |
| Ursprünglicher Inhalt wieder vorhanden | Neues Finding #19944 ohne Reaktivierung oder Vorgänger-Verknüpfung zu einem archivierten Finding. |
| Literaltext verändert | #19944 archiviert, neues Finding #19945; keine behauptete Verschiebung. |

Die genannten IDs gehören ausschließlich zu dieser Testdatenbank. Sie sind keine produktiven IDs und keine durchgängigen Identitäten zwischen alten und neuen Datenbeständen.

## Vier abgewiesene Szenarien

- Mehrere gleichlautende Aufrufe gleichzeitig verschoben: Herkunft ist nicht eindeutig; keine Änderungen geschrieben.
- Dynamischer, noch ungeklärter Aufruf: keine Archivierung bestehender Findings aufgrund eines unvollständig ausgewerteten Dateiinhalts.
- Datei fehlt: kein Beweis einer bewussten Entfernung; keine Archivierung.
- Datei vom Scanner ausgeschlossen: kein zulässiger Scan-Umfang; keine Archivierung.

Eine nachweislich gelesene Datei ohne Aufrufe darf innerhalb dieses expliziten Dateiumfangs archivieren. Eine nicht gelesene oder nicht eindeutig auswertbare Datei darf das nicht.

## Matching und Herkunft

Unverändert bedeutet gleicher Dateipfad, gleiche Zeile/Byte-Spalte und identischer Ausdruck. Alle bestätigten Findings werden mit dem neuen Scan verknüpft; ihre unveränderlichen Erfassungsfelder werden nicht umgeschrieben. Der neue Dateihash steht im Scan-Snapshot.

Eine Verschiebung wird nur bei exakt einem nicht bereits bestätigten alten und einem neuen identischen Ausdruck innerhalb derselben Datei abgeleitet. Das ist eine ausdrücklich begrenzte Matching-Regel, kein Beweis der Benutzerabsicht: Entfernen und identisches Neueinfügen können gleich aussehen. Mehrdeutige Mehrfachtreffer blockieren den Dateiabgleich. Dateiübergreifende Bewegungen werden nicht automatisch zugeordnet.

Archivierung und neue Aufnahme erfolgen in einer Transaktion. Neue Scan-Snapshots dokumentieren Umfang, Hash, Matching-Regel und Ergebniszahlen. Alte Timeline-Einträge werden nicht importiert. Eine fachliche Event-/Akteur-Anbindung ist weiterhin kein Bestandteil dieses isolierten Prototyps.

## Prüfung und Grenzen

16 Integrations-Assertions bestanden. Zusätzlich bestanden erneut die 42 Kontext-/Adapterprüfungen sowie Pint und Diff-Whitespace-Prüfung. Der Inhalt sämtlicher Findings außerhalb der geprüften Datei wurde vor und nach den Scan-Zyklen per Fingerabdruck verglichen: unverändert. Der originale Sicherungsquelltext wurde ebenfalls erneut geprüft.

Die Tests belegen die genannten Abläufe an **einer realen Datei**, keinen vollständigen zweiten Scan sämtlicher 1.684 Dateien und keinen allgemeinen Nachweis vollständiger Scanner-Discovery. Die produktiven Scan-Commands wurden nicht angepasst. Der Dateiabgleich sperrt konkurrierende eigene Prototypläufe; Parallelität mit sämtlichen anderen schreibenden Workbench-Diensten wurde hier nicht geprüft. Die Zuordnungsweitergabe bei bereits verknüpften Keys ist ebenfalls nicht Gegenstand dieser Testfälle, da die Ausgangs-Probeerfassung noch keine Key-Verknüpfungen besitzt.

Der Ergebnisbestand wurde erneut privat als Dump gesichert; SHA-256 geprüft, Testcluster gestoppt. Für diesen neuen Ergebnisdump steht noch eine eigene Restore-Prüfung aus. Der vorherige Importdump wurde durch diesen Lauf tatsächlich restauriert.

## Nächster fachlicher Schritt

Die 72 offenen Kandidaten aus der Gesamt-Erfassung gezielt untersuchen: 34 dynamische/nichtskalare Argumente, 28 spezielle Aufrufe und zehn Key-ähnliche Argumente ohne Sprachdatei-Beleg. Diese müssen nachvollziehbar behandelt werden, bevor eine vollständige Übernahme beansprucht werden kann. Anschließend sind unabhängige Key-Identitäten und Finding-Zuordnungen auf der Kopie aufzubauen; gleiche Literaltexte rechtfertigen weiterhin keine automatische Zusammenführung.
