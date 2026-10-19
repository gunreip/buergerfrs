# Archivierte Quellpositionen: Migration und gemeinsamer Abgleich

## Umsetzung

- `TranslationWorkbenchOccurrenceMatcher` trifft die Vorkommensentscheidung für SourceReconciliation und FoundationSyncer gemeinsam. Aktuelle exakte Positionen zuerst, dann verbleibende Kandidaten; obsolete Findings sind keine aktuellen Kandidaten.
- Der Sync erstellt je Datei vor dem Schreiben einen Zuordnungssnapshot über die gesamte Datei, auch bei ausgewählten Teilmengen. Mehrdeutigkeit stoppt die gesamte Transaktion statt weitere Findings zu erzeugen. Unter PostgreSQL serialisiert eine SHARE ROW EXCLUSIVE-Sperre auf der Finding-Tabelle die konkurrierenden Schreibzugriffe während der Zuordnung.
- Eindeutige Verschiebungen behalten die Finding-ID. Ein neues Vorkommen ohne passenden aktiven Kandidaten erhält eine neue ID, selbst wenn dieselbe Signatur bereits archiviert ist. Ein Folgescan verwendet die neue aktive ID wieder.
- Leere Translation-Aufrufe bleiben irrelevant; deren bereits obsoleter Datensatz wird nicht bei jedem Scan neu erzeugt. Es erfolgt keine Reaktivierung.
- Signaturbasierte Nachschlagen in DetectSuspiciousKeyedAdditions und im zugehörigen UI-Kontext berücksichtigen nur nicht archivierte Findings.
- Die neue Migration ersetzt globale source_signature-Eindeutigkeit durch einen partiellen Unique-Index für status != obsolete plus normalen historischen Suchindex. PostgreSQL und SQLite unterstützt; andere Treiber werden ausdrücklich abgewiesen. Ein Rollback mit inzwischen mehrfach belegten historischen Signaturen wird verweigert, statt Historie zu löschen.

## Produktive Ausführung

Nur die einzelne Migration wurde ausgeführt. Prüfsummen über sämtliche Spalten und Zeilen bestätigen unverändert **23216 Findings** und **25816 Key-Finding-Verknüpfungen**. Der neue partielle Unique-Index ist vorhanden. Keine Findings aktiviert, verschoben, hinzugefügt oder deaktiviert; kein produktiver Foundation-Sync ausgeführt. Die zuletzt festgestellten mehrdeutigen Modal-Vorkommen bleiben ungeklärt.

## Tests

- Vollständige Workbench-Suite: 757 Tests / 4770 Assertions bestanden.
- Anschließend zusätzlicher Migrationstest: 1 Test / 5 Assertions bestanden.
- Isoliertes PostgreSQL: 96 von 96 Ergebnissen bestanden, einschließlich echter Unique-Verletzung bei einem zweiten aktuellen Datensatz und Koexistenz mit dem unveränderten Archiv.
- Geänderte Erwartung: Ein ausgewähltes Vorkommen aus einer nicht eindeutig zuordenbaren Wiederholungsgruppe führt nun zum Rollback, nicht zur vorsorglichen Neuanlage.
- git diff --check für die bearbeiteten Dateien bestanden.

Dies ersetzt die im Bericht archive-aware-occurrences-2026-10-17.md dokumentierte temporäre Sperre bei jedem obsoleten Signaturtreffer. Weiter offen: konkrete Kontextzuordnung der mehrdeutigen Gruppen und fachliche Prüfung unbekannter/copierter TranslationKeys einschließlich Key-Ownership. Diese Migration trifft keine neue Übersetzungs- oder Key-Zusammenführungsentscheidung.
