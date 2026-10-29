# Korrekturvorschlag getrennt speichern – isolierte Probe

Die elf strukturell blockierten Namen besitzen im Neuaufbau noch keinen regulären
Key-Datensatz. Eine spätere Korrektur ist deshalb nicht einfach ein Rename eines
bereits angelegten Keys. Ein gewünschter Zielname und seine Auswirkungen müssen
zuerst ausdrücklich festgehalten werden.

## Implementierter Schritt

`KeyCorrectionProposal` und die additive Tabelle
`baseline_key_correction_proposals` speichern ausschließlich einen Vorschlag:
Operation-ID, Baseline, Ausgangsname, gewünschter Zielname, Bearbeiter,
Begründung, unveränderlicher Kontextnachweis und Zeitpunkt. Der Kontext enthält
alle getrennten Finding-IDs und die betroffenen veröffentlichten Ausgangswerte.
Es entstehen dabei keine Keys, Verknüpfungen, Translation-Revisionen, Freigaben
oder Publikationsaufträge. Die Findings und ihre Rohdaten bleiben unverändert.

Die Speicherung prüft erneut den blockierten Ausgangszustand, aktive unverknüpfte
Fundstellen, Zielstruktur, vorhandene Ziel-/Array-Pfade und den Fingerabdruck der
Vorschau. Ein exakter Retry derselben Operation liefert das bestehende Ergebnis;
eine veränderte Wiederholung wird abgewiesen. Fehlende Begründung oder Bearbeiter
werden abgewiesen. Ein SQL-Trigger verhindert Änderungen und Löschung gespeicherter
Vorschläge. Unterschiedliche Operationen sind separate Vorschläge, keine implizite
Ersetzung oder Ausführung eines älteren Vorschlags.

## Tatsächliche Probe

Eine neue Datenbank wurde aus dem geprüften Baseline-Dump im isolierten Cluster
restauriert. Für `ui.type` wurde ausschließlich der synthetische Vorschlag
`rehearsal.correction.type` mit Test-Bearbeiter 999999 gespeichert. Dieser Name
ist keine fachliche Empfehlung und keine beschlossene Korrektur. Die Probe
wurde nicht in den UI-Snapshot exportiert.

17 Assertions erfolgreich: Umfang mit 387 Findings und zwei Sprachwerten,
veralteter Fingerabdruck, fehlende Begründung/Bearbeiter, ungültiges/belegtes Ziel,
Rollback fehlgeschlagener Versuche, Retry, abweichender Retry, Rücklesen und
Schreibschutz. Sämtliche 27 vorher vorhandenen Tabellen wurden vor/nach der
Speicherung inhaltlich verglichen und blieben unverändert. Keine produktive
Verbindung, keine Dateiänderung. Testcluster wieder gestoppt.

Beim ersten Lauf war der Rücklesetest unnötig von der Objekt-Key-Reihenfolge
abhängig. PostgreSQL JSONB ordnet Objekt-Keys anders; der Vergleich prüft jetzt
die Datenstruktur unabhängig von dieser Reihenfolge. Der vollständige zweite
Lauf auf einer frischen Restore-Datenbank bestand.

## Grenzen und nächster Schritt

Dies ist ein Persistenzvertrag unter `tests/Support`, keine produktive Migration
und kein neuer UI-Speicherbutton. Der bisherige Export bleibt read-only. Ein
Vorschlag ist keine ausführbare Freigabe: aktuelle Quellen, zusätzliche
Fundstellen und Sprachdateien müssen für eine tatsächliche Anwendung geprüft
werden. Berechtigungen und echte Benutzeridentität müssen durch die spätere
Anwendungsanbindung geliefert werden; die positive Test-ID ist kein
Autorisierungsnachweis. Die groben Tabellenlocks sind ausdrücklich Teil der
isolierten Probe und keine Performance-Zusage für den Produktivbetrieb.

Als nächstes muss die Anwendung einer ausdrücklich bestätigten Entscheidung auf
der Kopie geprüft werden: neuer regulärer Key, getrennte Verknüpfungen der
betroffenen Findings und nachvollziehbare Herkunft der Ausgangswerte. Schreiben
von Quellcode und Sprachdateien bleibt ein eigener nachgelagerter Schritt.
