# Getrennter Leseweg – Probe 19.10.2026

Der Leser arbeitet auf einer explizit ausgewählten Sprachdatei-Baseline und erhält eine ausdrückliche Locale-Reihenfolge. Er liest Dateibestand, aktuellen skalaren Bearbeitungsstand und Review getrennt innerhalb einer konsistenten, nur lesenden PostgreSQL-Transaktion. Keine produktive Anbindung und keine neue UI in diesem Schritt.

## Rückgabe und Regeln

- `effective_published`: erster tatsächlich vorhandener Dateiwert in der übergebenen Locale-Kette, einschließlich tatsächlichem Locale, Fallback-Kennzeichnung, Quelldatei und Prüfsumme. Ohne Treffer NULL.
- `locales[locale].published`: exakter Dateibeleg mit Einordnung `linked`, `blocked_structure` oder `unassigned_key`; eine fehlende Übersetzung bleibt NULL.
- `locales[locale].editing`: aktuelle Revision und separater Review-Status. Ein unreviewter oder freigegebener Entwurf wird nicht als veröffentlicht ausgegeben.
- `approval`: `unreviewed`, `approved`, `revoked` oder `stale`. Die ursprünglich gespeicherte Entscheidung steht zusätzlich in `recorded_decision`, damit eine veraltete Freigabe nachvollziehbar bleibt.

Die Review-Kontextberechnung wurde unverändert aus `PostgresBaseline` in den gemeinsam verwendeten `RevisionReviewContext` extrahiert. Der bestehende Review-Ablauf und der neue Leser verwenden dadurch dieselbe Grundlage aus aktueller Revision, aktuellem Key-Namen und aktiven Finding-Verwendungen. Die Anzeige ist keine Autorisierung für einen späteren Schreibvorgang; dieser muss seinen Kontext erneut prüfen.

Ein strukturell blockierter, aber in der Sprachdatei vorhandener Wert bleibt im Dateibestand sichtbar. Er bekommt dadurch weder eine Key-Zuordnung noch eine Freigabe. Dies ist eine diagnostische Darstellung des Ausgangsbestands und keine Aufhebung der Blockierung.

Leere Strings und `0` bleiben vorhandene Strings. Die Baseline wird deshalb nicht wegen eines PHP-Truthiness-Checks durch einen Fallback überschrieben. Ein unbekannter Key liefert keinen erfundenen Literaltext. Welche Code-Ausgabe bei fehlendem Key erfolgt, gehört zum späteren Runtime-Adapter.

## Prüfungen

2.318 Assertions auf einer frisch restaurierten Testkopie bestanden. Alle 768 Keys wurden mit der ausdrücklich für diesen Test gewählten Kette `de-at → de → en` gelesen. Acht haben einen eigenen de-at-Wert; 760 verwenden einen Fallback. Alle haben exakte de- und en-Dateibelege. Die Kette ist keine neue globale Konfiguration: de-DE, weitere Sprachen, Locale-Aktivierung und regionale Zuordnungen werden nicht vom Leser erfunden oder normalisiert.

Zusätzliche Fälle prüfen unbekannte Keys, blockierte Keys, ungültige/mehrdeutige Locale-Ketten sowie in einer zweiten, separaten Fixture-Datenbank:

1. Ein neuer Entwurf bleibt von der veröffentlichten Ausgabe getrennt.
2. Eine aktuelle Freigabe macht den Entwurf noch nicht veröffentlicht.
3. Ein Widerruf wird angezeigt.
4. Eine Änderung der aktiven Finding-Verwendungen macht die Freigabe veraltet.
5. Ein leerer String und `0` lösen keinen künstlichen Fallback aus.

Die Fixture verwendet ausdrücklich synthetische Testdaten und Actor-ID 999999. Sie wurde nicht exportiert und gehört nicht zum Ausgangsbestand. Die Inhaltsfingerabdrücke aller 27 Tabellen der eigentlichen Baseline sind vor und nach sämtlichen Tests identisch. Beide Datenbanken liegen im isolierten Cluster, der am Ende beendet wurde.

Der bestehende PostgreSQL-Persistenztest bestand nach dem gemeinsamen Review-Refactoring ebenfalls: 186 Assertions, zehn Konkurrenzfälle und neun Assertions nach Server-Neustart. Damit bleiben bestehende Review-/Persistenzverträge geprüft.

## Grenzen und nächster Schritt

Der Leser ist weiterhin ein Rehearsal-Baustein unter `tests/Support`, kein produktiver Translator. Er behandelt hier skalare App-Keys; Optionsidentitäten und Vendor-Namespaces sind noch nicht angeschlossen. Das tatsächliche Veröffentlichen eines Entwurfs wird nicht simuliert oder vorweggenommen.

Als nächster Schritt bietet sich eine prüfbare, lesende Darstellung dieses Modells an: veröffentlichter Ausgangswert, Entwurf, Review und Fallback nebeneinander. Dazu zuerst die Datenanbindung für die isolierte Generation festlegen; die produktive Workbench darf nicht stillschweigend auf den Testbestand umgestellt werden.

[Maschinenlesbares Prüfergebnis](baseline-reader-2026-10-19.json)
