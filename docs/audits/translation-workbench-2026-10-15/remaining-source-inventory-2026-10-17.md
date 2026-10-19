# Wiederaufnahme: Erfassung und Zuordnungsbestand (17.10.2026)

Rein lesender Quellenabgleich und PostgreSQL-Transaktion (REPEATABLE READ READ ONLY). Keine produktiven Scans mit Schreibwirkung, keine Datenreparatur, keine Sprachdateiänderung.

## Ergebnis

- 335 Fundstellen mit mehrfacher aktiver Zuordnung, unverändert gegenüber dem letzten Reparaturbericht. Zusammengefasst 309 Kombinationen beteiligter Key-IDs.
- 334 dieser Fundstellen haben übereinstimmende Key-Texte; 37 der 335 haben mindestens einen geprüften Key. Gleichlautender Key-Text allein autorisiert keine Zusammenführung.
- Ein abweichender Fall: Finding #5093, aktuell im Quellenabgleich missing; aktive Keys #4507 (`ui.button.save.save`, reviewed) und #4613 (`ui.save`, pending). Fehlende Fundstelle und widersprüchliche Key-Zuordnung sind getrennt zu prüfen.
- 99 Fundstellen ohne aktive Zuordnung: 96 im Abgleich ignored, drei reappeared. `ignored` bedeutet hier kommentierter Quellkontext oder leeres Literal gemäß Reconciliation; es ist keine Aussage über eine ausdrückliche manuelle Ignore-Entscheidung. Diese 96 sind nicht pauschal zu reaktivieren.
- Wiederfunde #6566, #7072, #7075 im Key-Editor: Datenbankstatus obsolete, Grund `not_seen_in_latest_foundation_sync`; historische Links zu den Keys sind weiterhin vorhanden. Vor Reaktivierung aktive Gegenstücke und kanonische Owner prüfen.
- 304 Positionsverschiebungen seit dem letzten Abgleich; das ist keine erneute Dublettenbildung. Hinzu kommen 981 new, 159 ambiguous, 47 missing, 129 ignored und 18344 unchanged. Diese Scannerkategorien sind keine pauschale Fehlerzahl.

## Reihenfolge für die Fortsetzung

1. Erfassung absichern: Für die drei Wiederfunde prüfen, dass vorhandene Findings und die bestätigten kanonischen Keys wiederverwendet werden, ohne weitere Key-Datensätze anzulegen. Fehlende oder widersprüchliche Belege sperren die automatische Reparatur.
2. Die Mehrfachzuordnungen gruppenweise nach Historie, Review und gespeicherten Werten auflösen. Größte Kombinationen: #656/#1212 (18 Findings), #809/#993 (5), #225/#4517 (5), #5/#243 (2). Keine Auswahl allein nach Alter oder ID.
3. Positionsabgleich separat über den bestehenden signierten Reparaturplan durchführen; keine beiläufige Veränderung von Übersetzungsentscheidungen.
4. Erst danach einen begrenzten wiederholten Erfassungslauf prüfen: Wiederverwendung statt Neuerfassung und unveränderte kanonische Zuordnung beim zweiten Lauf.

Die vollständige gruppierte Arbeitsliste steht in [remaining-source-inventory-2026-10-17.json](remaining-source-inventory-2026-10-17.json). Es wurden keine Laufzeitänderungen vorgenommen; deshalb keine neue Testausführung. Die Datenbankabfrage und der Quellenabgleich liefen erfolgreich.

## Vertiefung der drei als reappeared gemeldeten Fundstellen

Die Einzelprüfung findet je eine historische candidate-Verknüpfung: #6566 → Key #339, #7072 → Key #473, #7075 → Key #339. Beide Zielkeys sind open/reviewed. Die Links wurden mit `finding_not_seen_in_latest_foundation_sync` obsolet gesetzt.

Allerdings bestehen im selben File aktive Findings identischer Ausdrücke: unter anderem #7149/#7155/#7159/#7160/#8800 für `ui.translation.translation-key` und #7131/#7132/#7151/#7156 für `ui.key.suggested-key`. Der aktuelle Abgleich behandelt deren übrige Vorkommen bereits als ambiguous, während exakte historische Signaturtreffer vorher als reappeared beansprucht werden. Eine historische Positionsübereinstimmung allein beweist daher keinen echten zusätzlichen Wiederfund. Die frühere Bezeichnung „echte Wiederfunde“ ist entsprechend zu präzisieren: Es sind drei Scanner-Kandidaten, deren Identität ungeklärt ist.

Keine Reaktivierung durchgeführt. Nächste notwendige Korrektur: konkurrierende aktive Positionskandidaten vor Reaktivierung eines obsoleten Signaturtreffers berücksichtigen und solche Fälle fail-closed als mehrdeutig ausweisen. Erst danach darf ein Reparaturplan neue Aktivierungen anbieten. Bestandsaufnahme in `/tmp/workbench-reappeared-inspect.json`; keine Datenbankänderung.
