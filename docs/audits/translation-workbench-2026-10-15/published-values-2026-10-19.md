# Sprachdatei-Ausgangsbestand – 19.10.2026

Der geprüfte Key-/Finding-Dump wurde in eine neue isolierte PostgreSQL-Datenbank restauriert. Die App-Sprachdateien der Sicherung wurden per Hash geprüft und als PHP-Arrays statisch gelesen, nicht ausgeführt. Kein produktiver Datenbankzugriff und keine Änderungen an Quell- oder Sprachdateien.

## Ergebnis

54 App-Sprachdateien liefern 2.016 Werte: en 971, de 967, de-at 11, de-be 10, de-ch 11, de-de 15, de-it 11, de-li 10, de-lu 10. Locale-Schreibweisen bleiben exakt erhalten.

| Einordnung | Werte | Bedeutung |
|---|---:|---|
| linked | 1.597 | Exakter Name eines bereits in der Testgeneration belegten Keys |
| blocked_structure | 22 | Key entspricht nicht der vereinbarten verschachtelten Struktur; keine Korrektur |
| unassigned_key | 397 | Noch kein entsprechender Key in dieser Testgeneration; Wert bleibt erhalten |

Alle 768 bisher übernommenen Keys haben einen exakten englischen und deutschen Dateiwert. In de-de fehlen für 755 dieser Keys eigene Werte, in den übrigen sechs Regionaldateien jeweils für 760. Das sind keine automatisch zu füllenden Fehler: Der später konfigurierte Fallback bleibt eine Laufzeitfunktion. Die Probe erzeugt keine regionalen Kopien aus de oder en.

Die 397 unzugeordneten Werte sind nicht automatisch veraltet oder unbenutzt. Sie können unter anderem zu Optionsquellen oder zu Aufrufen außerhalb des bisher eindeutig zugeordneten Umfangs gehören. Die Baseline löscht und vereinheitlicht nichts. 32 Dateien unter `lang/vendor/` liegen außerhalb des jetzigen App-PHP-Gruppen-Umfangs; sie sind mit Pfad, Hash und Ausschlussgrund im Ergebnis dokumentiert. Namespaced Vendor-Werte werden nicht stillschweigend als App-Keys behandelt.

## Eigenständige Baseline

Drei additive Tabellen speichern die Übernahmeidentität, die Quelldateien und die Werte. Jeder Wert hat ein exaktes Locale, einen vollständigen Key, Quelldatei und Dateiprüfsumme; eine Key-Verknüpfung existiert ausschließlich bei exakter Übereinstimmung mit einem vorhandenen App-Key.

Diese Baseline ist ausdrücklich keine neue Benutzerrevision oder Freigabe. Die bestehenden Value-Slots, Value-Revisions, Reviews und Events bleiben unverändert. Es werden keine historischen Akteure erfunden. „Veröffentlicht“ bezeichnet hier den nachgewiesenen Dateibestand der Sicherung, nicht einen nachträglich bewiesenen damaligen Freigabevorgang oder die heutige produktive Laufzeit.

## Prüfungen und Grenzen

Zwölf PostgreSQL-Assertions bestanden: isolierte Verbindung, Rollback nach einem Fehler am Ende des Imports, Import/Retry ohne Duplikate, vollständiger Rücklesevergleich aller Texte einschließlich NULL-freier Stringwerte, exakte Key-Verknüpfung, blockierte Werte ohne Zuordnung, Löschschutz und unveränderte Inhaltsfingerabdrücke aller 24 zuvor vorhandenen Tabellen.

Sechs Reader-Assertions bestanden: Dateiumfang und Vendor-Abgrenzung, leere Strings und `0`, Zeilenumbrüche, unverändertes Regional-Locale ohne künstliche Fallback-Werte, Hash-Abweichung und Ablehnung ausführbarer PHP-Ausdrücke. Codeformatierung und Syntaxprüfung bestanden.

Die Baseline-Tabellen sind gegen normale Updates und Deletes geschützt, nicht gegen administrative Schemaänderungen oder TRUNCATE. Der Importer und das Schema bleiben Rehearsal-Bausteine unter `tests/Support`, keine produktive Migration. Der private Ergebnisdump wurde erstellt und der Testcluster beendet; der neue Dump wurde noch nicht erneut restauriert.

[Prüfergebnis mit Quellenumfang und Prüfsummen](published-values-2026-10-19.json).

## Nächster Schritt

Den Leseweg für einen Key prüfen: exakter Dateibaseline-Wert, späterer Bearbeitungsstand und explizite Freigabe müssen unterscheidbar sein. Ein Locale-Fallback darf dabei weder als vorhandene Übersetzung gespeichert noch als eigene Freigabe interpretiert werden. Die unzugeordneten Options-/Vendor-Werte und blockierten Keys bleiben separat sichtbar.
