# Versionierte Arbeitsgeneration

Aktueller Installationsstand: **Version 2**. Sie verwendet die unveränderten
Definitionen aus `v1/` und ergänzt `v2/name-inventory.sql`. Vorhandene
Version-1-Schemata werden nicht automatisch umgeschrieben oder als Version 2
registriert; `verify()` weist einen abweichenden Versionsstand zurück.

`v1/` ist die zentrale PostgreSQL-Schemagrundlage der neuen Arbeitsgeneration.
Sie ist **nicht** als automatisch laufende Laravel-Migration registriert.
Die acht bisherigen SQL-Dateien wurden unverändert aus den Testhilfen
verschoben; damit bleiben ihre historischen Prüfsummen gültig. Kommentare
zum früheren Rehearsal-Einsatz dokumentieren ihre Herkunft.
`capture-ledger.sql` ersetzt die bisher in `StaticCaptureImporter` eingebetteten
DDL-Anweisungen. Alle aktiven PHP-Aufrufer verwenden diese Paketdateien.

## Einrichtung

`WorkingGenerationSchema::install()` benötigt ausdrücklich:

- eine PostgreSQL-PDO-Verbindung mit Exception-Modus;
- den erwarteten Datenbanknamen;
- einen noch nicht existierenden Schemanamen;
- eine neue, ausdrücklich übergebene Generations-UUID.

`public`, Systemschemata, vorhandene Schemata und eine bereits laufende
Transaktion werden abgewiesen. Es gibt keine Übernahme vorhandener Tabellen,
kein automatisches Upgrade und kein Löschen bestehender Daten. Das neue Schema,
seine Tabellen und der Generationsnachweis entstehen in einer Transaktion.
Der Suchpfad wird nur transaktionslokal gesetzt und danach wiederhergestellt.

Die Reihenfolge steht ausschließlich in `WorkingGenerationSchema`: Grundmodell,
Capture-Nachweise, Source-Rollen/-Argumente, vorhandene Key-Zuordnungen,
veröffentlichte Baseline, Korrekturvorschläge/-Anwendungen und Entwurfsimporte.
Die separate historische Evidenzablage (`evidence-staging.sql`) gehört nicht
zum aktiven Arbeitsschema.

## Prüfung

`verify()` prüft Generations-ID, Version, SQL-Dateiprüfsummen und den bei
Installation festgehaltenen Strukturfingerabdruck. Er umfasst:

- Tabellen/Relationen einschließlich Row-Security-Flags;
- Spalten mit Typen, Defaults, NULL-Regeln und Identity-Eigenschaften;
- Constraints und Validierungszustand;
- Indexdefinitionen und Gültigkeit;
- Triggerdefinitionen einschließlich Aktivierungszustand;
- Funktionsdefinitionen;
- Sequenzkonfiguration, nicht den laufenden Zählerstand.

Neue Daten, normale Sequenzfortschritte und spätere Transaktionen verändern
diesen Fingerabdruck nicht. Die Generationszeile ist über den vorhandenen
Immutable-Trigger gegen gewöhnliche Updates und Deletes geschützt.

Dies ist eine nachvollziehbare Prüfung gegen den Installationsstand, keine
Absicherung gegen Administratoren, die Nachweise und Schema gemeinsam
manipulieren können. Rollen-/Dateirechte, Extensions, Betriebskonfiguration und
Benutzerfreigaben sind nicht Bestandteil des Fingerabdrucks. Die Formatierung
der PostgreSQL-Katalogdefinitionen kann sich mit Serverversionen ändern; ein
Upgrade muss daher erneut geprüft werden, statt den gespeicherten Hash
automatisch anzugleichen.

## Noch keine produktive Umschaltung

Eine erfolgreiche leere Installation enthält keine Baseline, Findings oder
Übersetzungen. Die explizite Datenbankbindung der Key-Korrektur verlangt
weiterhin passende Baseline-/Herkunftsnachweise. Installation, Übernahme der
Nachweise und fachliche Freigabe sind getrennte Schritte. Es gibt noch keinen
UI- oder Artisan-Einstieg, der dieses Schema in der Anwendungsdatenbank anlegt.

Die Generalprobe installiert Version 1 in einem eigenen Schema innerhalb ihres
temporären Clusters und prüft Strukturabweichungen. Das wiederhergestellte
Baseline-Schema bleibt dabei unverändert; es wird nicht nachträglich als neue
Generation registriert.

## Ausgewählte Übernahmeprobe

Die Generalprobe befüllt dieses frische Schema inzwischen anschließend mit
den ausdrücklich ausgewählten Nachweisen **eines strukturell blockierten
Keys**. `SelectedGenerationTransfer` bleibt ein isolierter Testadapter, kein
produktiver Importdienst oder allgemeiner Migrationsweg.

Neue Datei-/Finding-Identitäten werden von der Zieldatenbank vergeben.
Der gespeicherte Übernahmeplan und die Finding-Provenienz halten ursprüngliche
Baseline, Quellschema, Finding-ID, Scan-ID und Provenienz fest. Ein neuer
Baseline-Hash kennzeichnet den ausgewählten Teilbestand; er wird nicht als
vollständige Kopie des ursprünglichen Bestands ausgegeben.

Der Capture-Lauf ist ausdrücklich als `evidence_transfer_not_new_scan`
gekennzeichnet. Er behauptet keinen neuen Scannerlauf gegen die Anwendung.
Rohfelder, SuggestedKey und Textnachweise bleiben unverändert. History,
Reviews, aktive Key-Zuordnungen und bearbeitete Revisionen werden nicht
importiert. Erst die anschließende bewusste Testkorrektur erzeugt neue
Verknüpfungen und einen neuen History-Eintrag.

Ein Fehler während der Übernahme rollt ihre Tabellenänderungen zurück.
Sequenznummern können dabei PostgreSQL-üblich verbraucht werden; sie werden
nicht zurückgesetzt. Ein bereits befülltes Zielschema wird abgewiesen.

Diese Teilprobe prüft noch nicht sämtliche Key-Namenskonflikte des vollständigen
Bestands: Der synthetische Test-Zielkey wurde zuvor gegen den vollständigen
Ausgangsbestand geprüft. Aus der Teilprobe folgt keine Freigabe für beliebige
Key-Korrekturen oder eine Übernahme aller offenen Kandidaten. Der nächste
Ausbau muss den vollständigen Namensraum sowie die übrigen Übernahmekategorien
ausdrücklich berücksichtigen.

## Ergänzung in Version 2: vollständiger Namensbestand

`baseline_name_inventories` hält den vollständigen belegten Namensbestand
der ausgewählten Ausgangsbaseline fest: veröffentlichte Key-Namen sowie
aktive benannte Keys, jeweils mit Namespace. Liste, Anzahl, SHA-256 und
ursprünglicher Baseline-Hash sind von aktiven Übersetzungsdaten getrennt.
Die Tabelle ist gegen gewöhnliche Updates/Deletes geschützt.

Die Teilübernahme übernimmt dieses Inventar vollständig, ohne deshalb die
zugehörigen Findings, Werte oder Reviews zu aktivieren. Vor der Übernahme
wird der aktuelle Namensbestand erneut mit dem festgehaltenen verglichen.
Fehlende, beschädigte oder inzwischen abweichende Evidenz blockiert.

Vorschau und DB-Prüfung berücksichtigen das Inventar sowie die aktiven
Zielnamen. Exakter Name, vorhandener skalarer Elternpfad und vorhandene
Kindpfade blockieren. Segmentgrenzen und Namespaces bleiben erhalten:
`ui.controls.type` ist nicht `ui.controls.types`, und ein Name in einem
Package-Namespace belegt nicht automatisch denselben globalen Namen.
Der Inventar-Hash geht in den Vorschlagskontext und dessen Fingerprint ein.

Der Generalprobenstand vom 20. Oktober prüft 972 Namen und 387 ausgewählte
Findings. Damit ist die zuvor beschriebene Namensraumgrenze dieser Teilprobe
für den gesicherten Stand geschlossen. Neue Namen außerhalb der gespeicherten
Ausgangsbaseline und einer überprüften aktiven Generation werden dadurch
nicht automatisch entdeckt; dafür bleibt der aktuelle Scan-/Abgleichweg nötig.
