# Gespeicherte Prüffälle und erneute Scannerläufe

## Zweck und aktueller Stand

Prüfergebnisse gehören in die neue Datenbankgeneration, nicht ausschließlich in Audit-Dateien. Eine Fundstelle, ein Translation Key, ein historischer Wert und eine Prüfaufgabe bleiben unterschiedliche Objekte. Gleicher Text verbindet diese nicht automatisch.

Implementiert ist zunächst eine explizit installierte v2-Prüffallablage mit einem isolierten Persistenzlauf. Sie ist **noch nicht an die produktive Scanner-Pipeline oder eine schreibende Workbench-UI angebunden**. Die reguläre Datenbank wird nicht migriert. Die neue SQL-Datei ist keine automatisch ausgeführte Laravel-Migration.

## Datenstruktur

- `workbench_review_cases`: stabile Fall-ID und Herkunftsidentität, Verweis auf die aktuelle Nachweisversion.
- `workbench_review_versions`: versionierte Nachweise, Prüfgrund, erforderliche Handlung und Kennzeichen, ob eine Entscheidung nötig ist.
- `workbench_review_observations`: welcher Lauf welche Version gesehen hat. Wiederholung desselben Laufs bleibt idempotent; widersprüchliche Wiederholungen werden zurückgewiesen.
- `workbench_review_decisions`: Entscheidung mit Benutzerkennung, Begründung, Zeitpunkt und fortlaufender Entscheidungsrevision. Eine Folgerevision ersetzt keine ältere Zeile.

Historische Identität: verifizierte Sicherung + Quelltabelle + Datensatz-ID. Ein späterer aktueller Scannerlauf verwendet seine eigene stabile Fundstellen-/Prüfgegenstandsidentität. Die Sicherungsidentität darf nicht unverändert als Identitätskonzept für laufende Scans übernommen werden.

Die aktuelle Nachweisversion hängt von Inhalt, Prüfgrund und erforderlicher Handlung ab, nicht von Lauf-ID oder Beobachtungszeit. Ein zweiter unveränderter Lauf erzeugt nur eine weitere Beobachtung. Entscheidungen gelten für die geprüfte Version. Bei veränderten Nachweisen bleibt die alte Entscheidung historisch erhalten und die neue Version ist erneut offen. Ein verspätetes Replay eines bereits verarbeiteten Laufs setzt die aktuelle Version nicht zurück. Zeitliche Ordnung tatsächlich neuer Scanläufe muss die Scanner-Orchestrierung garantieren.

## Zustände und Entscheidungen

| Zustand | Bedeutung |
| --- | --- |
| `recorded` | Nachweis erfasst; aktuell keine manuelle Entscheidungsaufgabe |
| `needs_review` | Konkrete Entscheidung erforderlich |
| `clarification` | Benutzer fordert weitere Klärung; nicht erledigt |
| `decided` | Entscheidung zur aktuellen Version dokumentiert; noch keine Veröffentlichung |

Vorgesehene historische Entscheidungen: veröffentlichten Wert behalten, historischen Wert als Entwurf vorschlagen, ausschließlich archivieren oder Klärung anfordern. Die Speicherung einer Entscheidung darf keine stillschweigende Key-Zusammenführung, Quelldateiänderung oder Veröffentlichung auslösen. Diese Folgeaktionen brauchen eigene geprüfte Operationen und Nachweise.

Historische Rohtexte liegen weiterhin in der verifizierten Evidenzsicherung. Die Prüffallversion speichert Herkunft, Key, Locale, Status und Vergleichshashes. Die spätere Detailansicht muss die Originalwerte über diese Herkunft laden und deren Hash prüfen; aus Hashes allein lässt sich kein Text beurteilen.

## Vertrag für neue Scannerläufe

| Scan-Ergebnis | Erwartete Behandlung |
| --- | --- |
| Unveränderte Fundstelle | Bestehende aktive Fundstelle bestätigen; keinen neuen Key, keine neue Aufgabe anlegen |
| Eindeutig verschoben, gleiche Datei | Alte Fundstelle archivieren, Nachfolger verknüpfen; bestehende Key-Verknüpfung nachvollziehbar übertragen |
| Zusätzliches Vorkommen | Eigene Fundstelle; keine Zusammenlegung aufgrund gleichen Texts |
| Geänderter Literaltext | Neue Erfassung, alte Fundstelle gemäß abgeschlossenem Abgleich archivieren; keine Umdeutung unveränderlicher Rohdaten |
| Mehrdeutige Zuordnung | Prüfaufgabe mit Kandidaten; keine automatische Auswahl |
| Nicht mehr vorhanden | Nur nach erfolgreicher vollständiger Prüfung des betroffenen Bereichs archivieren |
| Datei nicht lesbar, ausgeschlossen oder Scan abgebrochen | Kein Abwesenheitsnachweis; nichts deswegen stilllegen |
| Bekanntes Prüfproblem unverändert | Vorhandenen Fall bestätigen, Entscheidung erhalten |
| Neues oder verändertes Prüfproblem | Neue Fall- bzw. Nachweisversion, konkrete Handlungsanforderung |

Der bisherige isolierte `CopiedSourceRescan` bestätigt gleiche Position/Expression, erkennt eindeutige Verschiebungen innerhalb derselben Datei und schützt vor Stilllegung bei fehlenden/ausgeschlossenen Dateien. Bei Mehrdeutigkeit bricht er derzeit den Datei-Abgleich ab. **Die Überführung solcher Mehrdeutigkeiten in diese Prüffallablage ist noch zu implementieren.** Ebenso fehlt die gemeinsame produktive Ergebnis- und Laufsteuerung. Die Prüffallablage allein ersetzt sie nicht.

Ein Scan-Ergebnis soll getrennte Zähler liefern: bestätigt, neu, verschoben, archiviert, ungeklärt, ausgeschlossen und fehlgeschlagen. Prüfaufgaben werden zusätzlich als neu, unverändert, geändert oder bereits entschieden bilanziert; diese Zahlen sind keine zusätzlichen Findings.

## UI-Anbindung als nächster Integrationsschritt

Die bestehende Workbench bleibt der Arbeitsort. Dort braucht es eine Prüfliste mit Status, Prüfgrund, Key/Locale, Herkunft, letzter Beobachtung und erforderlicher Entscheidung. Die Detailansicht zeigt Originalwert, veröffentlichten Wert und Quellen, bevor eine Flux-Bestätigung die Entscheidung speichert. Bei veränderter Nachweis- oder Entscheidungsrevision muss die Ansicht neu geladen werden.

Vor der Umschaltung sind insbesondere noch offen: Integration mit den v2-Scanläufen, konkrete Herkunftsauflösung der Texte, Berechtigungen und schreibende Livewire-/Flux-Bedienung sowie geprüfte Folgeoperationen. Kein bestehender UI-Button ist durch diese Testimplementierung bereits mit der neuen Ablage verbunden.

## Umgesetzt: lesende UI-Vorschau

Workflow → Baseline preview → Historical value review cases zeigt den privaten
Export der 518 offenen Fälle, filterbar und mit Flux-Pagination (20 pro Seite).
Die Detailansicht zeigt hashgeprüfte historische und veröffentlichte Originaltexte
nebeneinander. Case-ID und Nachweisversion bleiben einsehbar. Die Anzeige ist
explizit lesend; schreibende UI und produktive Scanner-Anbindung bleiben offen.

## Aktualisierung: Arbeitsort und Gruppierung

Die aktive Ansicht befindet sich jetzt in der Workbench oberhalb der Findings,
nicht mehr im Workflow. 223 Key-Gruppen enthalten die 518 offenen Fälle und
zusätzliche Kontextzeilen (insgesamt 1.940). Jeder historische Status bleibt
seiner ursprünglichen Tabellenzeile und Locale zugeordnet. Source-Art, gespeicherter
Sprachdateipfad und veröffentlichter Sprachdateipfad werden getrennt ausgegeben;
unbekannte Code-Fundstellen werden nicht geraten. Locale-Flaggen verwenden
die vorhandene gemeinsame Komponente. Schreibende Entscheidungen bleiben offen.
