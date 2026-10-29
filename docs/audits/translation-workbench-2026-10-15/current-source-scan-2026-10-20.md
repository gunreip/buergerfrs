# Frischer Quellcode-Scan – 20.10.2026

Run: `20261020-192821-b3d8958008b8` · Abschluss: 2026-10-20T19:28:36+00:00

Der Scan wurde nach der Korrektur der Schema-Prüfung wiederholt. Die Befundzahlen sind gegenüber dem ersten Scan unverändert.

## Ergebnis

| Befund | Fundstellen | Gruppen |
| --- | ---: | ---: |
| Wiederverwendbarer Key | 1792 | 768 |
| Literal neu zu erfassen | 18127 | 18127 |
| Key-Prüfung | 425 | 20 |
| Ausdruck prüfen | 62 | 62 |
| Ausgeschlossen | 112 | — |

1717 Quelldateien und 54 PHP-Sprachdateien wurden gelesen. Keine Sprachdatei musste wegen eines Parsefehlers ausgelassen werden. Der konfigurierte Source-Locale ist `en`.

Wiederverwendbar bedeutet: exakter Key mit mindestens drei Segmenten, belegter Source-Locale-Wert und bestätigte Aufrufstelle. Das ist ein Übernahmevorschlag, keine Freigabe oder bereits ausgeführte Migration. Fehlende Zielsprachen werden nicht aufgefüllt. Gleichlautende Literale bleiben getrennte Fundstellen. „Neu zu erfassen“ bedeutet neu für die geplante Generation, nicht nachweislich erst seit dem letzten Scan entstanden.

## Aktuelle Key-Prüfgruppen

| Key/Argument | Fundstellen | Vorhandene Locales |
| --- | ---: | --- |
| `Copied.` | 1 | — |
| `admin.common.ui.example_admin_actions_save` | 1 | — |
| `segments.fusion` | 1 | — |
| `translation-workbench.php` | 1 | — |
| `ui.Hide` | 1 | — |
| `ui.Hide-usage-details` | 1 | — |
| `ui.Show` | 1 | — |
| `ui.Show-usage-details` | 1 | — |
| `ui.action` | 2 | de, en |
| `ui.actions.cancel` | 2 | — |
| `ui.activity-log` | 2 | de, en |
| `ui.actual` | 2 | de, en |
| `ui.add-document` | 6 | de, en |
| `ui.add-primary-language` | 2 | de, en |
| `ui.already-target` | 2 | de, en |
| `ui.app-settings` | 2 | de, en |
| `ui.appearance` | 2 | de, en |
| `ui.client-number` | 2 | de, en |
| `ui.open` | 6 | de, en |
| `ui.type` | 387 | de, en |

## Arbeitsort und Grenzen

Workbench → Current source scan — restart baseline ersetzt die historische Prüfliste. Standardmäßig werden die 82 aktuellen Prüfgruppen geladen. Wiederverwendbare Keys, Literale und alle Ergebnisse sind über den Filter erreichbar. Jede Gruppe zeigt aktuelle Datei, Zeile, Byte-Spalte, Rohaufruf und veröffentlichte Werte mit Locale-Flagge. Detaildateien werden bedarfsgesteuert geladen und per Hash geprüft.

`admin.permissions.filters.guards.all` kommt im aktuellen Befund nicht vor. Historische Einträge ohne aktuellen Fund werden nicht in eine neue Aufgabenliste übertragen. Die abgelösten historischen Vorschau-Views und deren Livewire-Komponente liegen unter `_old` und sind vom Scan ausgeschlossen.

Der Scan untersucht die konfigurierten Übersetzungsaufrufe, nicht jeden unmarkierten Text und nicht die Laufzeiterreichbarkeit sämtlicher Dateien. `_old`, `-old` und die übrigen konfigurierten Ausschlüsse gelten weiterhin. JSON-/Vendor-Übersetzungen und dynamische Laufzeitwerte werden nicht aufgelöst. Solche fehlenden Nachweise dürfen nicht als globaler Abwesenheitsbeweis verwendet werden.

Der Lauf bootet keine Anwendungsprovider, liest keine Produktionsdatenbank und schreibt weder Quelldateien noch Sprachdateien. Es wurde keine Generation aktiviert. Die bisherige Findings-Tabelle bleibt daher weiterhin der bisherige Live-Bestand; nur die Prüfgrundlage darüber ist ersetzt.

## Nachvollziehbarkeit

Private Scan-Manifeste enthalten Konfiguration, Datei- und Scanner-Hashes sowie sämtliche Rohbefunde als JSONL. Quelldatei- und Sprachdatei-Inventar und Hashes werden zum Abschluss erneut geprüft. Erst ein erfolgreicher Lauf ersetzt atomar den Vorschau-Verweis. Alte Sicherungen bleiben erhalten, dienen aber nicht als aktive Aufgabenliste.

Prüfung: 36 Tests / 329 Assertions bestanden. Zusätzlich wurden der reale UI-Reader, alle 82 Problem-Details und sämtliche Abschluss-Dateihashes geprüft.

Nächster Schritt: die 20 Key-Gruppen und 62 Ausdrücke fachlich einordnen; anschließend die tatsächliche Übernahme aus diesem aktuellen Befund in die neue Generation anschließen. Keine weitere pauschale Altbestandsbereinigung vorziehen.
