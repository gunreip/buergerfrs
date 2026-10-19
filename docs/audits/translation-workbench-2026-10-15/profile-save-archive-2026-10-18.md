# Profil-Save: entfallene Fundstelle #5093

## Quellcode-Abgleich

Die ältere Bestandsaufnahme nannte den Aufruf `ui.button.save.save` in Zeile 254 des Profil-Templates. Die vollständige Kontextprüfung zeigt: Dieser Aufruf steht innerhalb eines Blade-Kommentars. Auch der Scanner kennzeichnet ihn mit `source_context = commented_out` und `translation_relevant = false`.

Der aktive Profil-Button wird durch `<x-ui.button.save>` erzeugt. Dessen Default-Label wird in `resources/views/components/ui/button/save.blade.php:13` übersetzt und ist bereits als aktive Fundstelle #8588 mit Key #3576 über Verknüpfung #10983 erfasst.

Die alte Fundstelle #5093 enthält dagegen `__('ui.save')` im Profil-Template. Sie ist dort nicht mehr aktiv vorhanden. Eine Verschiebung auf die Button-Komponente würde deren bereits vorhandene eigenständige Erfassung verfälschen. Deshalb wurde ausschließlich die entfallene Fundstelle archiviert.

## Durchgeführte Änderung

Der vorhandene `TranslationWorkbenchFoundationSyncer::reconcileMissingFindings()` archivierte gezielt:

- Finding #5093.
- Verknüpfungen #6697 und #7163.
- Keys #4507 und #4613, da beide danach keine aktive Fundstelle mehr besitzen.

Fünf Timeline-Ereignisse dokumentieren diese Änderungen. Die bisherigen Rohdaten, TranslationKeys und Freigabeinformationen wurden erhalten. Keine Datensätze wurden gelöscht oder auf andere Findings umgehängt.

Die Komponentenerfassung #8588, alle anderen Findings/Verknüpfungen/Keys, die Übersetzungswerte und sämtliche Reviews blieben unverändert. Quellcode- und Sprachdateien wurden nicht bearbeitet.

## Absicherung und Ergebnis

Der Reparaturplan wurde lesend erstellt und vor Anwendung unter Tabellensperren erneut vollständig verglichen. Hash-Vergleiche sichern unveränderte übrige Datensätze und Quelldateien ab. Die erlaubten Änderungen an Finding und Keys sind auf Status/Metadaten und entsprechende Zeitstempel beschränkt.

- Nachprüfung: `already_applied`, keine erneute Änderung nötig.
- Verbleibende Findings mit mehreren aktiven Zuordnungen: **29** (28 mit mehreren geprüften Keys, eines mit geprüftem/ungeprüftem Key).
- Sechs gezielte Regressionstests bestanden, 33 Assertions: selektives Archivieren, Ablehnung vorhandener/verschobener/mehrdeutiger Fundstellen, keine Wiederbelebung archivierter Kandidaten und unabhängige gleichlautende Literale.

Plan, Ergebnis, unveränderte Tabellen-Hashes und Nachprüfung: `profile-save-archive-2026-10-18.json`.
