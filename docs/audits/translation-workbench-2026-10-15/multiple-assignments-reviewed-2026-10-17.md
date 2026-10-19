# Mehrfachzuordnungen: Quellen- und Owner-Abgleich

## Ergebnis

Von 335 Findings mit mehreren aktiven Key-Verknüpfungen wurden fünf eindeutig belegte Doppelzuordnungen bereinigt. Es verbleiben **330 Findings**:

| Fallgruppe | Findings | Bedeutung |
| --- | ---: | --- |
| Alle zugeordneten Keys ohne TranslationKey | 298 | Mehrere Kandidaten, nicht mehrere gleichnamige veröffentlichte Keys |
| Derselbe nichtleere TranslationKey | 31 | Freigaben und historische Zuordnung müssen pro Owner geprüft werden |
| Verschiedene TranslationKeys | 1 | Finding #5093: #4507 und #4613; zusätzlich im aktuellen Code nicht mehr vorhanden |

Die frühere Zusammenfassung „334 mit gleichem Schlüsseltext“ war zu ungenau: Darin wurden auch 298 Gruppen mit ausschließlich `null` mitgezählt. Daraus darf keine Zusammenführungsentscheidung abgeleitet werden.

Der Quellenabgleich findet bei den verbleibenden Mehrfachzuordnungen 329 eindeutige Vorkommen mit noch fehlender Spaltenpräzisierung (`position_refined`) und eine fehlende Fundstelle. Die Spalten wurden in diesem Schritt nicht nebenbei verändert.

## Bereinigt: Show overview

- Key #225 wurde am 23.07.2026 ausdrücklich freigegeben (Timeline #41237, Review #60). Die zwei Sprachwerte liegen in `translation_workbench_lang_values` unter seinem TranslationKey. Das ist von den hier leeren `key_values` zu unterscheiden.
- Key #4517 wurde am 24.07.2026 vom Foundation-Scanner für denselben TranslationKey angelegt (Timeline #69857). Er war weiterhin pending, ohne eigene Reviews oder scalar/dynamic Key-Werte.
- Alle fünf aktiven Fundstellen #2948, #4334, #4361, #4423 und #4845 sind im aktuellen Quellcode bestätigt und bereits jeweils aktiv mit #225 verknüpft.
- Die Links #4537, #5938, #5965, #6027 und #6449 zu #4517 wurden auf `merged` gesetzt. Key #4517 ist jetzt `obsolete` mit `reconciled_canonical_key_id = 225`. Die vorhandene Scanner-Logik berücksichtigt diese Weiterleitung.
- Sechs neue Timeline-Einträge protokollieren die fünf Link-Änderungen und die Key-Zusammenführung. Die historischen Einträge wurden nicht umgeschrieben.

Das zugehörige [Reparaturskript](consolidate-show-overview-2026-10-17.php) erstellt zunächst einen lesenden Plan. Apply verlangt denselben frisch reproduzierbaren Plan unter Tabellensperren, prüft die aktuellen Quellaufrufe und kontrolliert Dateihashes vor Abschluss. Unbeteiligte Keys/Links sowie sämtliche Findings, Reviews und Übersetzungswerttabellen wurden durch Vorher-/Nachher-Hashes auf Unverändertheit geprüft. Ein erneuter Aufruf meldet `already_applied`.

## Noch offen

- #656/#1212 (`ui.type`, 18 Findings): beide reviewed. Keine automatische Auswahl. Der bekannte Strukturfehler von `ui.type` bleibt eine sichtbare UI-Korrekturaufgabe; der Key wurde nicht umbenannt.
- #809/#993 (`ui.filters.yes`, fünf Findings): beide reviewed; historische Bulk-Freigaben berücksichtigen.
- #5/#243 (`ui.states.all`, zwei Findings): beide reviewed; ebenfalls keine Auswahl allein nach ID/Alter.
- 298 Findings mit ausschließlich unbenannten Kandidaten: ursprüngliche Fundstellenbindung, SuggestedKey und mögliche Kopier-/Verschiebungshistorie prüfen. `null == null` ist kein Beleg für dieselbe Übersetzungsaufgabe.
- #5093: fehlende Quellfundstelle und widersprüchliche Key-Zuordnung getrennt behandeln; keine Reaktivierung oder Veröffentlichung.

Die vollständige verbleibende Arbeitsliste, Quellenzustände und Reparatur-Prüfsummen stehen in [multiple-assignments-reviewed-2026-10-17.json](multiple-assignments-reviewed-2026-10-17.json).

Validierung: PHP-Syntaxprüfung erfolgreich; 14 bestehende Pest-Regressionstests zu Consolidation, konkurrierenden Ownern und Source-Zuordnungen bestanden (204 Assertions). Es wurde kein vollständiger Testlauf durchgeführt und keine Laufzeitlogik geändert.
