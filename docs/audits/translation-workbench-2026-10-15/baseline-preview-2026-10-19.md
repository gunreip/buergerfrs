# Lesende Baseline-Vorschau – 19.10.2026

Aufruf: Translation Workbench → Workflow → **Baseline preview** (`/admin/translation-workbench/workflow`). Die bestehende Berechtigung der Workflow-Seite gilt unverändert.

Der neue Tab verwendet einen expliziten JSON-Export der zuletzt geprüften, isolierten Baseline. Der Webrequest startet keinen PostgreSQL-Testcluster und verbindet sich nicht mit dessen Datenbank. Die produktive Workbench-Datenbank wird für diese Vorschau weder gelesen noch beschrieben. Die übrigen Workflow-Tabs laden den Export nicht.

Der am 19.10.2026 um 09:20:15 UTC erzeugte Export enthält 972 unterschiedliche Keys. Verfügbar sind die ausdrücklich angegebenen Vorschauketten `de-at → de → en`, `de → en` und `en`. Die Auswahl ist keine Änderung der produktiven Locale-Konfiguration. Weitere Regionalsprachen der gespeicherten Baseline sind in diesem ersten Vorschau-Export noch nicht enthalten.

Die Tabelle zeigt pro Locale den exakten Dateiwert mit Pfad/Prüfsumme und Zuordnungsstatus, den aktuellen Entwurf sowie dessen Review. Darüber steht der tatsächlich verwendete Dateiwert mit Herkunfts-Locale und Fallback-Kennzeichnung. Blockierte und unzugeordnete Werte bleiben als solche sichtbar. Die Test-Key-ID wird ausdrücklich als Test-ID bezeichnet.

Der echte Snapshot enthält noch keine Entwürfe oder Reviews. Deshalb erscheinen dort keine erfundenen Beispiele, sondern entsprechende Leerzustände. Die Darstellung eines freigegebenen Entwurfs wurde mit einer separat gemockten Test-Fixture geprüft. Fehlender Export und ungültige Auswahl ergeben einen lesbaren Hinweis, keine automatische Verbindung zum Testcluster.

## Dateien und Aktualisierung

Der private Export liegt unter `storage/app/private/workbench-previews/reader-preview.json` und wird nicht über eine öffentliche Dateiroute ausgeliefert. Er enthält nur die Vorschauwerte und deren Herkunft, keine Datenbank-Zugangsdaten. Er wird atomar ersetzt; der vorherige Export bleibt bis zur vollständigen Erstellung des neuen erhalten. Die eigentlichen Sicherungsverzeichnisse bleiben unverändert geschützt.

`export-baseline-reader-preview.php` restauriert den per Prüfsumme gebundenen Baseline-Dump in eine neue isolierte Datenbank, liest deren Werte über den geprüften Reader, erzeugt die Vorschau und beendet den Cluster wieder. Der Export ist ein Snapshot, keine Live-Abfrage des heutigen Quellcodes. Das Erzeugungsdatum steht deshalb sichtbar im Callout. Eine Aktualisierung muss ausdrücklich über diesen Exportweg erfolgen.

## Prüfung

Drei neue Pest-Tests mit 15 Assertions prüfen die Trennung von Fallback/Entwurf/Dateiwert, leere Strings, ungültige Auswahl, die gerenderte Flux-/Livewire-Ansicht, Locale-Wechsel und fehlenden Export. Eine bei der ersten Prüfung erkannte Blade-Kompilierungsabweichung wurde vor Abschluss korrigiert. Kein visueller Browser-Abgleich durchgeführt.

Die bestehenden Workflow-Tests bestanden ebenfalls: 15 Tests mit 277 Assertions. Insgesamt damit 18 Tests und 292 Assertions erfolgreich.

## Vollständiger Offline-Abgleich der Vorschau

Nach der Überarbeitung der Tabelle wurde der tatsächliche Export mit den statisch
geparsten Sprachdateien der geprüften Sicherung verglichen. Der wiederholbare
Prüflauf `php docs/audits/translation-workbench-2026-10-15/verify-baseline-preview.php`
benötigt weder einen Laravel-Bootstrap noch eine Datenbankverbindung.

Ergebnis: 972 Keys, 25.861 erfolgreiche Prüfungen. Geprüft wurden Vollständigkeit
der Key-Auswahl, exakte Werte einschließlich leerer Strings, Locale, Dateipfad,
Prüfsumme, Konsistenz von Zuordnungsstatus/Test-Key-ID und alle drei Fallbackketten.
Die Zuordnungsentscheidung selbst wird hier nicht erneut fachlich bestätigt.

| Vorschaukette | Exakte Werte | Fallback | Kein Wert |
| --- | ---: | ---: | ---: |
| de-at → de → en | 11 | 961 | 0 |
| de → en | 967 | 4 | 1 |
| en | 971 | 0 | 1 |

`admin.permissions.actions.edit_role_permissions` ist in dieser Sicherung nur
unter `lang/de-at/admin.php` vorhanden. Weder `de` noch `en` enthalten einen Wert.
Die Vorschau meldet diesen Leerzustand korrekt. Keine automatische Ergänzung,
Umbenennung oder Zuordnung vorgenommen.

Die drei sichtbaren Locales enthalten insgesamt 1.949 Dateiwertnachweise:
1.544 verknüpft, 383 unzugeordnet und 22 strukturell blockiert. Diese Zahlen sind
kein Widerspruch zur vollständigen Baseline mit neun Locales und 2.016 Werten:
67 Werte aus den übrigen sechs Regionalsprachen liegen außerhalb der aktuellen
Vorschauketten. Das Ergebnis ist ein Snapshot-Abgleich, keine Überprüfung des
heutigen produktiven Datenbestands oder eine visuelle Browserprüfung.

Die UI-Prüfung umfasst inzwischen sechs Vorschautests mit 29 Assertions und
15 bestehende Workflow-Tests mit 277 Assertions. Mehrzeilige Werte und Entwürfe
werden mit einer separaten Fixture geprüft; der echte Export enthält weiterhin
keine Entwürfe oder Reviews.

Nächster fachlicher Schritt: Die blockierten Key-Strukturen und offenen
Zuordnungen über eine explizite Bearbeitungsentscheidung zugänglich machen.
Die Baseline-Nachweise bleiben unveränderlich; eine Entscheidung muss separat
mit Bezug auf Key/Finding und Ausgangsnachweis gespeichert werden. Insbesondere
ist ein nur regional vorhandener Dateiwert weder automatisch unbenutzt noch ein
Grund, einen englischen Ausgangstext zu erfinden.

## Gezielte Auswahl nach Zuordnungsstatus

Die Vorschau bietet jetzt ein Flux-Select „Assignment status“. Im aktuellen
Export sind 768 Keys verknüpft, elf strukturell blockiert, 92 unzugeordnet mit
Optionsquellennachweis und 101 mit offener Quellenzuordnung. Die Zahlen zählen
Keys über alle exportierten Locales, keine einzelnen Werte. Bei unterschiedlichen
Locale-Zuständen darf ein Key in mehreren Gruppen vorkommen.

Die Key-Auswahl wird entsprechend eingeschränkt. Ein passender ausgewählter Key
bleibt erhalten; andernfalls wird der erste passende gewählt. Leere Gruppen
zeigen einen Leerzustand ohne die vorherigen Details. Ein Wechsel der
Fallbackkette verändert die Zuordnungsgruppe nicht. Die Loading-Anzeige umfasst
auch den Statuswechsel.

Dies ist der lesende Einstieg für die anschließende Bearbeitung. Es wird keine
Schreibaktion gegen Test- oder produktive IDs angeboten. Der ausdrücklich
zurückgestellte Fall `admin.permissions.actions.edit_role_permissions` bleibt
unverändert, ohne Sonderbehandlung oder automatische Korrektur.

## Strukturblockade mit Fundstellen

Bei Auswahl eines strukturell blockierten Keys erscheint ein rotes Callout mit
der geltenden Regel (Dateigruppe plus mindestens zwei Key-Segmente). Ein
vorhandener Dateiwert hebt die Strukturblockade nicht auf. Es wird kein
Ersatzname vorgeschlagen oder automatisch geschrieben.

Das aufklappbare Quellenverzeichnis zeigt getrennte Test-Findings mit Pfad,
Zeile/Spalte und escaped erfasstem Ausdruck. Alle 415 Fundstellen der elf
blockierten Keys sind aus der isolierten Datenbank exportiert; die IDs sind
keine produktiven Finding-IDs. Ein älterer Export ohne Detailnachweise zeigt
explizit einen Hinweis und behauptet nicht, dass der Key unbenutzt sei.

Der neue Export wurde erneut aus dem geprüften Dump erstellt und der temporäre
Testcluster anschließend gestoppt. Vollständiger Abgleich einschließlich der
Fundstellen gegen den unveränderten Importplan: 26.693 Prüfungen erfolgreich.
Der produktive Datenbestand bleibt unverändert. Die UI bietet weiterhin keine
schreibende Korrekturaktion; deren Grundlage ist jetzt konkret sichtbar.

## Explizite Korrekturvorschau für blockierte Keys

Das Struktur-Callout bietet ein leeres Feld „Proposed replacement key“ und den
Button „Check proposal“. Die Eingabe ist eine hypothetische Ersetzung des
gewählten Keys; sie speichert keine Entscheidung und schreibt weder Quellen,
Findings, Zuordnungen noch Sprachdateien. Es gibt keine automatisch erzeugten
Ersatznamen und keine Zusammenführung anhand gleichlautender Werte.

Die separate, rein lesende Prüflogik kontrolliert die vereinbarte Key-Struktur,
einen unveränderten Zielnamen, exakte Belegung und Konflikte zwischen skalaren
Werten und über-/untergeordneten Array-Pfaden. Der zu ersetzende Ausgangs-Key
wird bei dieser hypothetischen Prüfung ausgenommen. Beispielsweise blockiert
`ui.type` allein deshalb nicht den ausdrücklich eingegebenen Vorschlag
`ui.type.type`; andere bereits vorhandene Werte unter diesem Pfad werden
weiterhin geprüft. Ein vorhandener Ziel-Key wird nicht automatisch übernommen.

Das Ergebnis nennt die erfassten Fundstellen und die in der Vorschau belegten
Locales. Fehlende Quellendetails eines älteren Exports gelten als unbekannt,
nicht als null betroffene Fundstellen. „No conflict found in this snapshot“
bedeutet ausdrücklich keine Freigabe: weitere Locales und heutige Quellen
müssen vor einer echten Ausführung separat geprüft werden.

Eine geänderte Eingabe, ein anderer Key oder Statusfilter verwirft die bisherige
Prüfanzeige. Die übrigen Workflow-Tabs laden weiterhin keinen Baseline-Export.
Die neue Logik ist getrennt mit Struktur-, Pfadkonflikt- und Unveränderlichkeitstests
sowie über Livewire mit Eingabe- und Auswahlwechseln abgesichert.

## Vollständige Locale-Abdeckung für die Korrekturvorschau

Der aktuelle Export enthält jetzt alle neun gesicherten App-Locales:
`de`, `de-at`, `de-be`, `de-ch`, `de-de`, `de-it`, `de-li`, `de-lu`, `en`.
Damit sind sämtliche 2.016 Dateiwertnachweise sichtbar und in der
Korrekturvorschau berücksichtigt: 1.597 verknüpft, 397 unzugeordnet, 22 blockiert.
Die früher dokumentierte Einschränkung auf drei exportierte Locales entfällt.

Die zusätzlichen Locales werden mit jeweils eigener Ein-Locale-Abfrage gelesen;
sie bilden keine neue Fallbackkette. Die vorhandenen drei Auswahlketten und ihre
Ergebnisse bleiben unverändert. „Show all exported locales“ erweitert nur die
Tabelle auf alle gespeicherten Locales. Fehlende Werte bleiben fehlend; es werden
keine abgeleiteten Regionalwerte erzeugt. Die Korrekturvorschau listet alle
betroffenen veröffentlichten Locales, auch wenn sie in der gewählten
Tabellen-/Fallbackansicht nicht vorkommen. Leere veröffentlichte Strings zählen
als vorhandene Werte.

Der Offline-Abgleich fordert jetzt die vollständige Locale-Menge für jeden Key
und prüft alle Werte gegen die gespeicherten Dateien. 39.584 Prüfungen erfolgreich.
Zusätzliche Tests sichern die Trennung von Tabellenumfang und Fallback sowie
regionale Korrekturauswirkungen ab. Produktive Daten, Quellen und Sprachdateien
unverändert; Testcluster nach dem Export wieder gestoppt.

## Vergleich mit dem heutigen Dateistand

„Check proposal“ vergleicht jetzt zusätzlich die gespeicherten Prüfsummen der
betroffenen Quell- und Sprachdateien mit den aktuellen Projektdateien. Die
Quellprüfsummen stammen aus dem unveränderten Sicherungsmanifest und wurden dem
Export hinzugefügt. Sprachprüfsummen stammen weiterhin aus der Dateiwert-Baseline.
Dateien werden nur gelesen, nicht ausgeführt oder verändert.

Das separate Callout „Current file comparison“ unterscheidet unverändert,
geändert, fehlend, unlesbar sowie fehlende/widersprüchliche Ausgangsnachweise.
Mehrere Findings in einer Datei werden für diesen Vergleich zusammengefasst;
die Findings selbst bleiben getrennt. Pfade außerhalb des Projektverzeichnisses,
inklusive entsprechender Symlinks, werden nicht gelesen. Fehlende Nachweise
älterer Exporte werden nicht als erfolgreiche Prüfung behandelt.

Der unmittelbare Prüfstand für alle elf blockierten Keys zeigte ausschließlich
unveränderte referenzierte Dateien (ui.type: 46 Dateien). Dies ist eine
Zeitpunktaufnahme, kein automatischer Rescan, kein Nachweis der Abwesenheit neuer
Fundstellen und keine Prüfung des aktuellen Datenbankzustands. Eine Änderung
irgendwo in einer Datei gilt als Abweichung, ohne daraus eine Änderung des
Literals abzuleiten. Vor einer echten Schreibaktion müssen Voraussetzungen
innerhalb des Schreibablaufs erneut geprüft werden.

Der vollständige Export-Abgleich einschließlich 415 Quellprüfsummen bestand
mit 39.999 Prüfungen. Der ausdrücklich zurückgestellte Regionalsprachenfall
bleibt unangetastet.
