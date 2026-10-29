# SuggestedKey: Bestandskorrektur

Stand: 2026-10-22. Die folgenden Prüfungen waren ausschließlich lesend.

## Befund

- 20.485 Findings, davon 20.425 ursprüngliche Findings.
- 60 Vorgänger-/Nachfolgerketten; alle Findings sind über ihre ursprünglichen Findings erreichbar.
- Keine Kette enthält unterschiedliche nichtleere SuggestedKeys.
- 2.215 Findings ohne SuggestedKey: 2.207 aktiv, 8 obsolete.
- 1.624 Gruppen mehrfach belegter SuggestedKeys unter ursprünglichen Findings.
- Der frühere Capture-Adapter setzte nur für Literale einen Vorschlag; für bereits vorhandene Keys blieb er leer. Die bisherigen Importprüfungen erwarteten dieses falsche Verhalten teilweise ausdrücklich.
- Der Vorschlag aus Pfad und Text wurde ohne übergreifende Namensreservierung gespeichert. Wiederholte Texte innerhalb einer Datei führten daher zu gleichen Vorschlägen.

## Reparaturverfahren

Die Korrektur ist eine ausdrücklich dokumentierte Bestandsreparatur, kein normales Scanner-Verhalten. Im Normalbetrieb bleibt ein SuggestedKey unveränderlich.

1. Einen vollständigen Plan innerhalb einer konsistenten Datenbanksicht erstellen. Alle Findings, einschließlich archivierter, und ihre Quelldateipfade berücksichtigen.
2. Nach Vorgänger-/Nachfolgerketten gruppieren. Verwaiste Beziehungen, Zyklen oder widersprüchliche Vorschläge blockieren die Anwendung; nicht stillschweigend korrigieren.
3. Alle vorhandenen Vorschläge reservieren. Bei unabhängigen Ketten mit gleichem Vorschlag behält die Kette mit der kleinsten ursprünglichen Finding-ID den Namen. Dies ist nur die deterministische Reihenfolge der Reparatur und keine Aussage über das Eigentum an einem TranslationKey.
4. Weitere Ketten erhalten den nächsten freien ordinalen Suffix `_2`, `_3` usw. Bereits vorhandene Namen dürfen dabei nicht überschrieben werden.
5. Für Ketten ohne Vorschlag den Ausgangswert aus der unveränderlichen Erfassung des ursprünglichen Findings und dessen Quelldateipfad erzeugen: tatsächliches Literal, vorhandener Source-Key oder dynamischer Ausdruck. Den heutigen Quellcode nicht als historische Erfassung ausgeben. Bei unzureichender Evidenz den Fall blockieren.
6. Innerhalb einer Kette denselben Vorschlag für alle Mitglieder vorsehen. Keine Findings, Keys oder Verknüpfungen zusammenführen.
7. Vor Anwendung eine prüfbare Liste ausgeben: ursprüngliche Finding-ID, betroffene IDs, Datei, alter/neuer Vorschlag, Grund und Evidenz. Plan mit Fingerprint der Eingangsdaten versehen.
8. Anwendung separat, mit Sicherung und Transaktion. Bei verändertem Datenbestand abbrechen. Originalwerte und Reparaturgrund dauerhaft protokollieren; zuvor die vorhandenen Unveränderlichkeitsregeln des Schemas prüfen. Keine pauschale Abschaltung von Schutzmechanismen.
9. Danach prüfen: kein fehlender Vorschlag, Eindeutigkeit zwischen unabhängigen Ketten, identischer Vorschlag innerhalb jeder Kette. TranslationKeys, Übersetzungen, Zuweisungen und Quellcode müssen unverändert sein. Erneute Planung darf keine weiteren Änderungen ergeben.

## Testbestand und Tests

Der UI-Testbestand ist keine Datenbankkopie. `WorkbenchTestEnvironment::start()` erstellt eine separate Datenbank und eigene Quelldateien. `WorkbenchTestFixtures::seed()` baut mehrere definierte Szenarien auf. Bei erneuter Aktivierung wird ein frischer Bestand erzeugt; alte Teständerungen werden nicht übernommen.

Die anfänglichen Findings werden direkt eingetragen. Nachfolgende Zuweisungen, Entwürfe, Reviews und Veröffentlichungen verwenden die Workbench-Dienste. Dieser Aufbau testet UI-Zustände, ersetzt aber keinen Test der echten Scanner-Erfassung.

Für die Reparatur sind gesonderte Tests nötig: fehlende Vorschläge, wiederholte Literale, vorhandene ordinale Namen, reservierte archivierte Namen, mehrere Nachfolger, ungültige Ketten, veralteter Plan, Rollback und Wiederholbarkeit. Scanner-Integrationstests müssen dieselben Regeln unabhängig von den UI-Fixtures absichern.

## Aktueller Stand

Die künftige Erfassung und die UI-Fixtures wurden angepasst. Der bestehende Arbeitsbestand wurde nicht repariert. Dieses Dokument beschreibt den geprüften Ausgangszustand und das vorgesehene Reparaturverfahren; es ist kein ausgeführter Änderungsbericht.
