# Archivierte Findings und aktuelle Vorkommen

## Umgesetzt

Der lesende SourceReconciliation-Abgleich verwendet obsolete Findings nicht mehr als Signaturkandidaten. Unveränderte aktive Stellen werden zuerst zugeordnet. Anschließend werden die verbleibenden Vorkommen mit den verbleibenden aktiven Kandidaten verglichen: eine eindeutige Paarung ist moved, ein zusätzlicher Treffer nach vollständig zugeordneten Bestandsvorkommen ist new, konkurrierende Kandidaten bleiben ambiguous. Es wird nicht nach Zeilenreihenfolge geraten.

FoundationSyncer stoppt bei einem exakten obsoleten Finding-Treffer transaktional statt diesen zu reaktivieren. Ausnahme sind bereits als irrelevant behandelte leere Literale: diese bleiben obsolete. Dieser Schutz ist eine Zwischenstufe, kein fertig implementierter Neuanlageweg für archivierte Positionen.

## Aktueller Bestand (nur gelesen)

In modal-edit-translation-key.blade.php: fünf aktuelle Vorkommen von ui.translation.translation-key zu fünf aktiven Findings (#7149, #7155, #7159, #7160, #8800); vier Vorkommen von ui.key.suggested-key zu vier aktiven Findings (#7131, #7132, #7151, #7156). Die konkrete Identitätszuordnung bleibt mehrdeutig. Die archivierten #6566/#7072/#7075 sind keine aktuellen Kandidaten mehr. Keine produktiven Daten geändert.

## Noch erforderlich

Die bestehende UNIQUE-Bedingung auf source_signature umfasst auch archivierte Findings. Um wirklich neue Vorkommen an historischen Positionen als neue Findings anzulegen, braucht es eine gezielte Migration sowie eine gemeinsame Zuordnungsentscheidung im lesenden und schreibenden Ablauf. Bis dahin stoppt der Schutz solche Schreibläufe mit Fehlermeldung und Rollback. Den regulären Sync deshalb nicht als Bereinigungslauf für diese Gruppen verwenden.

Danach müssen manuell eingetragene/unbekannte TranslationKeys und zusätzliche kopierte Vorkommen als eigene Erfassungsfälle abgesichert werden: vorhandene aktive Key-Zuordnung wiederverwenden, unbekannte Key-Texte als ungeklärte Zuordnung behandeln, keine Übersetzungswerte erzeugen. Keine pauschale Reaktivierung historischer Keys oder Findings. Die bereits vorhandene Mehrdeutigkeitsprüfung geprüfter Key-Owner bleibt erhalten.

## Verifikation

Workbench-Suite: 757 Tests / 4763 Assertions bestanden. Neue Regressionen prüfen archivierte Positionstreffer, konkurrierende aktive Findings, zusätzliche kopierte Key-Vorkommen und unveränderte Daten/Historie bei abgebrochenem Sync. Lesender produktiver Abgleich erfolgreich; keine Migration und kein produktiver schreibender Sync ausgeführt.
