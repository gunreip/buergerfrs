# Vollständiger Scan-Abgleich – 21.10.2026

## Zweck und Grenze

Vor der fachlichen Datenbearbeitung wurde ein vollständiger **lesender** Abgleich
auf einer isolierten Kopie der aktuellen Generation geprüft. Er verwendet den
vorhandenen Scanner mit seiner abgeschlossenen Dateiinventur und den vorhandenen
Capture-Adapter. Der neue `CompleteRescanPlan` liegt ausschließlich unter
`tests/Support`, ist nicht in den regulären Scannercommand eingebunden und
ändert weder Findings noch Links oder History.

Der geprüfte Generation-Dump und alle gesicherten Quelldateien wurden unverändert
übernommen. Dateiänderungen erfolgten nur in einer separaten privaten Quellkopie.
Der PostgreSQL-Prüfcluster wurde anschließend gestoppt.

## Vollständiger Ausgangsabgleich

| Ergebnis | Anzahl |
| --- | ---: |
| Vollständig inventarisierte Quelldateien | 1.717 |
| Dateien ohne ungeklärte Erfassungen | 1.678 |
| Dateien mit offenen Erfassungsfragen | 39 |
| Unverändert bestätigte Finding-IDs | 18.878 |
| Vorläufig erhaltene Findings in Prüfdateien | 1.456 |
| Summe bestehender aktiver Findings | 20.334 |

Für die 1.678 prüfbaren Dateien wird kein neuer, verschobener oder entfallener
Datensatz vorgeschlagen. Die 1.456 erhaltenen Findings sind nicht pauschal fehlerhaft:
Wenn eine Datei ungeklärte Ausdrücke enthält, hält dieser erste Abgleich sämtliche
bisherigen Findings der Datei zurück. Damit kann ein nicht verstandener Ausdruck
nicht versehentlich eine Stilllegung auslösen. Die übrigen Dateien bleiben prüfbar.

## Zusätzliche Fälle

| Änderung in der Quellkopie | Erwartetes und geprüftes Ergebnis |
| --- | --- |
| Eindeutige Zeilenverschiebung | Nachfolger-Vorschlag mit Vorgänger-ID |
| Dynamisch ungeklärter Ausdruck | Prüfstatus für die Datei; alte Findings erhalten |
| Erfolgreich gelesene Datei ohne Treffer | Stilllegungsvorschlag für frühere Findings |
| Gelöschte Datei in vollständig inventarisiertem Verzeichnis | Fehlende Datei nachgewiesen; Stilllegungsvorschlag |
| Einzeln angeforderte, fehlende Datei | Keine Löschfreigabe; außerhalb bestätigter Inventur |
| Gelöschte, aber vom Scan ausgeschlossene Datei | Keine Löschfreigabe |
| Datei fehlt, gleicher Aufruf in neuer Datei | Mögliche Verlagerung als Prüffall für beide Seiten |
| Zwei mögliche neue Zieldateien | Beide Kandidaten sichtbar; keine erratene Zuordnung |
| Ursprungsdatei vorhanden plus zusätzliche Kopien | Original bestätigt; Kopien als eigenständige neue Fundstellen |

Ein gleicher Ausdruck in einer neuen Datei beweist keine Umbenennung. Der Plan
stellt mögliche Zusammenhänge dar, führt aber keine Findings zusammen und vergibt
keine Key-Verknüpfung aufgrund gleichen Textes. `relocation_review` sperrt die
betroffenen Dateien als Ganzes für eine spätere automatische Anwendung. Eventuelle
Detailvorschläge darin sind ohne Auflösung dieses Prüfstatus nicht anzuwenden.

Der Scanner wird am Ende erneut zur Inventarisierung benutzt. Dateihashes,
gelesene Dateien, bestätigte Fehlstellen und durchsuchte Verzeichnisse müssen
weiterhin übereinstimmen; andernfalls wird kein Plan zurückgegeben. Das ist eine
Konsistenzprüfung während der Planung, keine Garantie für unveränderte Dateien
bei einer erst später erfolgenden Anwendung.

## Prüfungen und Artefakte

1.691 Assertions bestanden. Vollständige Tabellen-Fingerprints vor und nach der
Serie stimmen überein. Keine produktive Datenbankverbindung, keine Änderung am
originalen Quellcode, keine Umschaltung der Workbench. PHP-Syntaxprüfung, Pint und
`git diff --check` bestanden.

- Runner: `rehearse-complete-rescan-plan.php` in diesem Verzeichnis.
- Privater Ergebnisbericht: `storage/app/private/dev/workbench-rehearsal/current-generation-20261020-192844-f86176d77474/rescan_b0e8e0c93ceb/result.json`.
- Neben dem Bericht liegen `full-plan.json` und die einzelnen Fallpläne mit
  konkreten Pfaden, Finding-IDs, Positionen und Prüfgründen sowie der Ergebnisdump.

## Weiter offen vor der fachlichen Aufarbeitung

Der nächste Schritt ist die transaktionale Anwendung **eines vollständigen
geprüften Plans**, mit erneuter Prüfung von Quellnachweisen und Datenbankzustand,
Wiederanlaufsicherheit und Tests für konkurrierende Aufrufe. Erst damit lässt
sich nachweisen, dass ein ganzer Folgescan sauber gespeichert wird; dieser
lesende Plan allein beweist das noch nicht.

Prüfentscheidungen für Verlagerungen und ungeklärte Dateien müssen persistiert
und in der UI sichtbar gemacht werden. Automatische eindeutige Key-Zuordnung
neuer Fundstellen, Shared Candidates und Darstellung der Scan-History bleiben
weitere Prüfpunkte. Die fachliche Datenbearbeitung bleibt zurückgestellt.
