# Translation Workbench: Übergang von der Generalprobe zur Anwendung

Stand: 20. Oktober 2026. Diese Übersicht ersetzt die offenen Statusangaben der ursprünglichen Vorbereitung, nicht die historischen Berichte oder deren Nachweise.

**Die isolierten Abläufe sind weitgehend nachgewiesen. Die neue Generation ist noch nicht als schreibende Workbench angebunden.** Die Baseline-Vorschau liest exportierte Nachweise; ihre Prüfungen führen keinen produktiven Schreibvorgang aus. Erfolgreiche Rehearsal-Tests sind keine Freigabe eines produktiven Datenbankwechsels.

## Erledigt und belegt

| Arbeitspaket | Ergebnis / Nachweis | Grenze |
| --- | --- | --- |
| Zusammenhängende Sicherung und Restore | [79 Tabellen und 2.630.711 Zeilen verglichen; 3.595 Dateien gesichert](backup-restore-result-2026-10-19.md) | Gesicherter Stand, keine Aussage über seitdem geänderte Dateien/Daten. |
| Getrennte Herkunftsnachweise | [122.133 Datensätze aus 26 Tabellen erhalten](evidence-staging-result-2026-10-19.md) | Alte Entscheidungen werden dadurch nicht aktiv. Alte History bleibt im Archiv. |
| Neue Erfassung auf der Quellkopie | [Capture](copied-source-capture-2026-10-19.md), [Import](static-capture-import-2026-10-19.md) und [Rescan](copied-rescan-2026-10-19.md) | Die Rescan-Probe an einer realen Datei ersetzt keine vollständige Abnahme aller produktiven Scanner-Einstiege. |
| Bestehende Keys und veröffentlichte Werte | [Eigenständige Keys/Finding-Links](existing-key-links-2026-10-19.md) und [veröffentlichte Baseline](published-values-2026-10-19.md) | Blockierte Struktur und offene Zuordnung bleiben offen; keine automatische Text-/Key-Zusammenführung. |
| Lesbare Vorschau in der UI | [Baseline-Vorschau](baseline-preview-2026-10-19.md), Datei- und Änderungsvorschau im Paket | Keine aktive Verbindung der UI zum neuen schreibenden Datenmodell. |
| Zusammenhängender Korrekturablauf | [Aktuelles maschinenlesbares Prüfergebnis](key-correction-application-2026-10-19.json): 1.363 Assertions; Korrekturfall mit 44 Quelldateien, 387 Fundstellen, 972 Namensnachweisen und 16 Positionsverschiebungen; zusätzlich vollständige statische Erfassung mit 19.940 Findings, Rollen-/Argumentnachweisen und 2.016 veröffentlichten Werten | Synthetischer Zielkey `rehearsal.correction.type`, isolierte Datenbank und temporäre Sprachdatei. |
| Bestätigung gegen zwischenzeitliche Änderungen | Gemeinsamer Datei-/DB-Fingerprint, erneute Prüfung vor dem Schreiben, Rollback bei Dateiänderung während des Vorgangs | Kein dauerhaft atomarer Snapshot von Dateisystem und Datenbank. Kein produktiver Source-Schreibweg. |

Der letzte Lauf wurde am `2026-10-20T15:26:27+00:00` mit den Paketdiensten, der expliziten Datenbankbindung und den Übernahmeproben in Schema-Version 2 aufgezeichnet; sein Cluster wurde gestoppt. Die Zahlen sind historische Prüfergebnisse, keine Live-Zähler. Die JSON-Ergebnisse bleiben die maßgeblichen Einzelnachweise.

## Was noch implementiert werden muss

### Plan und Praxis sichtbar getrennt halten

Die Ziel-Graphs bleiben als ursprünglicher fachlicher Plan erhalten. In beiden
Ziel-Tabs ergänzt „Plan and implementation“ datierte Notizen mit Graph-ID,
ursprünglicher Absicht, tatsächlicher Umsetzung, Prüfgrenze und Nachweis.
Ein Haken bestätigt ausschließlich den dort benannten Umfang. Isolierte
Generalprobe und produktiv bedienbare Funktion sind unterschiedliche Stände.
Nicht aufgeführte Schritte gelten weder als erledigt noch als geprüft.

Spätere Abweichungen und zusätzliche Aufgaben werden mit Datum ergänzt,
statt ältere Aussagen rückwirkend als ursprünglichen Plan umzuschreiben.
Die ersten Notizen dokumentieren insbesondere Nachfolge-Findings bei
Positionsänderungen, den Neustart der aktiven History und die konkretisierte
Datei-/DB-Prüfung. Die Notizen sind ein manueller Nachweisstand, kein Live-Monitor.

**Der Workflow bestimmt die UI-Anforderungen.** Das vorhandene Workbench-Formular
ist ein Ausgangspunkt, keine Beschränkung für Navigation, Aufgabentrennung,
Darstellung oder Bestätigung. Seine Tauglichkeit muss anhand der fachlichen
Arbeitsschritte überprüft und bei Bedarf angepasst werden. Eine bestehende
Schaltfläche oder Tabellenstruktur ist kein Grund, den Workflow zu verkürzen.

Die folgenden fünf Arbeitspakete bilden den verbleibenden Übergang zur Anwendung. Weitere Einzeltests werden innerhalb des jeweiligen Pakets ergänzt, wenn dessen Implementierung sie benötigt.

| Reihenfolge | Arbeitspaket | Fertig, wenn … |
| --- | --- | --- |
| 1 | Gemeinsamer Anwendungsdienst für Key-Korrekturen | Vorschlag, verbindliche Prüfung, Bestätigung und idempotente Anwendung im Paket liegen; UI und Command denselben Dienst verwenden können. Die geprüften Regeln werden übernommen, nicht parallel neu implementiert. |
| 2 | Neue Generation als explizites Datenbankziel | Versioniertes Schema, Verbindung und Generationsidentität vorhanden sind; Repository und Transaktionen eindeutig an dieses Ziel gebunden sind; eine fehlende Konfiguration nicht auf die alte Anwendungsdatenbank zurückfällt. Zunächst nur eine isolierte Arbeitsgeneration verwenden. |
| 3 | Bedienbarer Ablauf im bestehenden Workbench-Formular | Ein berechtigter Benutzer Vorschlag und Auswirkungen sieht, per Flux-Modal bestätigt, verständliche Konflikte erhält und Ergebnis/History wiederfindet. Akteur kommt aus der Anmeldung; ein alter Browserstand darf keine geänderten Daten bestätigen. Keine zweite dauerhafte Verwaltungsoberfläche aufbauen. |
| 4 | Scanner, Revisionen und Veröffentlichung über dieselben Dienste | Scan/Rescan, Zuordnung, Bearbeitung, Freigabe, Sprachdatei-Ausgabe und Quellcodeänderungen auf der isolierten Generation zusammen funktionieren. Commands sind Einstiegspunkte, keine unabhängigen Writer mit abweichenden Regeln. Dateischreiben besitzt Wiederaufnahme und überprüfbaren Fehlerzustand. |
| 5 | Übernahmebilanz und Umschaltung | Jeder gesicherte Nachweis als übernommen, offen oder archiviert bilanziert ist; die UI-Abnahme erfolgt ist; alte schreibende Einstiege stillgelegt sind; Sicherung, Rückweg und konkreter produktiver Wechsel geprüft und beschlossen sind. |

Diese Pakete sind noch keine bereits angeschlossene Funktionalität. Insbesondere genügt es nicht, die aktuellen Testklassen aus der UI aufzurufen oder einen produktiven DSN in ein Rehearsal-Skript einzutragen.

## Nächster konkreter Umsetzungsschritt

**Der erste Teil von Paket 1 ist umgesetzt:** Der Key-Korrektur-Dienst liegt jetzt im Package. Beide Rehearsal-Aufrufer verwenden diese Implementierung; die bisherigen Testklassen wurden verschoben, ohne Aliase oder parallele Kopien.

Vorhandene Verantwortlichkeiten:

| Baustein | Aktueller Ort | Aufgabe beim Übergang |
| --- | --- | --- |
| `BaselineKeyCorrectionPreview`, `BaselineProposedSourceChanges` | Package `src/Support` | Bestehende Vorschlags-/Dateiprüfung beibehalten. |
| `BaselineProposalEvidence`, `BaselineCorrectionPreflight` | Package `src/Support` | Gemeinsame Evidenzprüfung beibehalten; DB-Kontext vom gebundenen Repository erhalten. |
| `KeyCorrectionProposal`, `KeyCorrectionApplication` | Package `src/Support` | Überführt; verlangen weiterhin eine ausdrücklich übergebene PostgreSQL-Verbindung mit dem neuen Schema. Keine automatische Laravel-Default-Verbindung. |
| PostgreSQL-Schema der Arbeitsgeneration | Package `database/generations/v1` | Zentrale versionierte Definitionen; bestehende Generalproben lesen dieselben SQL-Dateien. Keine automatisch registrierten produktiven Migrationen. |
| Rehearsal-Skript | Audit-Verzeichnis | Testadapter für denselben Paketdienst bleiben; darf keine zweite Fachlogik enthalten. |

Abnahmekriterien für diesen ersten Schritt:

- Die bestehende isolierte Generalprobe verwendet die Paketimplementierung und besteht weiterhin.
- Keine produktive Verbindung und keine Änderungen an produktiven Findings, Keys, Sprachdateien oder Source-Dateien.
- Kein synthetischer Akteur, Testkey oder Backup-Pfad als Default im Paketdienst.
- Findings bleiben getrennt; Rohfelder/SuggestedKey bleiben unveränderlich; archivierte Findings werden nicht reaktiviert.
- Wiederholte Bestätigung desselben Vorgangs erzeugt keine weiteren Links oder History-Einträge.
- Abweichende Datei-/DB-Nachweise werden vor dem Schreiben abgewiesen; Fehler innerhalb des Vorgangs rollen seine DB-Änderungen zurück.

Validiert: isolierte Generalprobe mit 1.166 Assertions, 11 begleitende Pest-Tests mit 35 Assertions, Pint für beide verschobenen Klassen und Diff-Whitespace-Prüfung erfolgreich. Keine PHP-Aufrufer des alten Test-Namespace mehr vorhanden.

**Danach folgt die explizite Bindung an die isolierte Arbeitsgeneration:** Schema-/Generationsprüfung und Verbindungsadapter für die Anwendung festlegen, bevor ein UI-Schreibaufruf angeschlossen wird. Die Überführung der Klassen allein macht das bisherige Anwendungsschema nicht kompatibel. Berechtigung, bestätigter Benutzer und persistenter Bestätigungszustand müssen beim späteren UI-Einstieg ergänzt werden; sie werden nicht durch eine beliebig übergebene positive Akteur-ID ersetzt.

### Ergänzung: explizite Datenbankbindung – 20. Oktober 2026

`KeyCorrectionDatabase` bindet die beiden Paketdienste nun an eine ausdrücklich
übergebene PostgreSQL-Verbindung, einen erwarteten Datenbanknamen, ein Schema,
einen Baseline-Hash und dessen Sicherungsmanifest-Hash. Es gibt keinen Default-
Verbindungsnamen und keinen Fallback auf die Laravel-Anwendungsdatenbank.

Die Prüfung erfolgt innerhalb der Transaktion, auch vor der Rückgabe einer
bereits ausgeführten Operation: Datenbank/Baseline müssen passen; benötigte
Tabellen und Spalten müssen im vorgesehenen Schema vorhanden sein. Die tatsächlich
aufgelösten Tabellen müssen dieselben Tabellen sein. Temporäre Tabellen oder
andere Suchpfade dürfen keine gleichnamigen Tabellen unterschieben.
Vollqualifizierte ACCESS-SHARE-Sperren halten die geprüften Tabellen während
des Vorgangs gegen DDL-Änderungen stabil.

Das ist eine Prüfung des benötigten Schemaumfangs, **noch keine vollständige
Schema-Migrations- oder Berechtigungsprüfung**. Datentypen, sämtliche Constraints,
Trigger und der zukünftige versionierte Generationsdatensatz sind damit nicht
vollständig attestiert. Eine korrekt konfigurierte Verbindung ist ebenfalls
keine Benutzerfreigabe und kein Beweis, dass ein Cluster isoliert ist. Die
Generalprobe prüft ihre eigene Cluster-Isolation weiterhin separat.

Offen für Paket 2 bleiben das versionierte Anwendungsschema und seine explizite
Einrichtung als Arbeitsgeneration. Erst anschließend folgt die UI-Bestätigung
gegen diese Generation. Der Guard erstellt keine Tabellen und ändert keine
Verbindungs- oder Suchpfadkonfiguration.

Validierung dieser Ergänzung: 1.182 Assertions der isolierten Generalprobe
erfolgreich, einschließlich falschem Datenbanknamen, falscher Baseline/Herkunft,
fehlendem Schema, fehlender Tabelle/Spalte, temporärer Tabellenüberschattung,
geändertem Suchpfad und Ablehnung vor dem Schreiben. Zusätzlich 11 begleitende
Pest-Tests mit 36 Assertions erfolgreich.

### Ergänzung: versionierte leere Generation – 20. Oktober 2026

`WorkingGenerationSchema` richtet Version 1 ausdrücklich in einem neuen Schema
ein und prüft den festgehaltenen Installationsstand. Die SQL-Grundlage liegt
jetzt im Package; die bisher eingebetteten Capture-Ledger-Definitionen sind
ebenfalls dorthin überführt. Die historische Evidenzablage bleibt separat.

Neben der Generations-ID und den SQL-Prüfsummen werden Spalten/Typen/Defaults,
Constraints, Index- und Triggerdefinitionen einschließlich Aktivierungszustand,
Funktionen sowie Sequenzkonfiguration verglichen. Bereits vorhandene Schemata
werden weder übernommen noch überschrieben. Es gibt noch keine automatische
Upgrade- oder Reparaturfunktion.

Die Generalprobe installiert die leere Generation neben ihrer restaurierten
Baseline und prüft absichtliche Strukturabweichungen. 1.200 Assertions bestanden;
die bestehende Persistenzsuite bestand zusätzlich mit 186 Assertions und zehn
Parallelitätsszenarien sowie neun Assertions nach PostgreSQL-Neustart.

**Nächster Schritt:** Die neue leere Generation mit ausdrücklich ausgewählten
Baseline-/Capture-Nachweisen auf der Kopie befüllen und die Paketdienste gegen
diese Generation prüfen. Die bisherigen Schreibtests laufen noch auf der
separat restaurierten Baseline. Deren erfolgreiche Tests allein belegen keine
fertige Übernahme in das frisch installierte Schema.

Grenzen und Einrichtung sind im Package unter
`database/generations/README.md` dokumentiert. Rechte, Cluster-Isolation und
Benutzerfreigaben bleiben separate Anforderungen. Ein Administrator, der
Struktur und Nachweis gemeinsam ändert, wird durch diesen Fingerabdruck nicht
abgewehrt.

### Ergänzung: ausgewählte Nachweise und Korrektur in der neuen Generation – 20. Oktober 2026

Der nächste Nachweis ist erbracht: Die frisch installierte Generation wird
auf der Kopie mit den Nachweisen eines ausdrücklich ausgewählten, strukturell
blockierten Keys befüllt. Es sind 387 getrennte Findings aus 44 Dateien und
zwei veröffentlichte Textnachweise. Der isolierte Testadapter
`SelectedGenerationTransfer` übernimmt weder aktive Key-Entscheidungen noch
Reviews, Revisionen oder alte History.

Datei-/Finding-IDs werden im Ziel vergeben. Original-Baseline, ursprüngliche
Finding-/Scan-ID und Provenienz bleiben als Herkunft erhalten. Rohfelder,
SuggestedKey und Position werden gegen den Ausgangsbestand verglichen.
Der Teilbestand erhält einen eigenen Baseline-Hash; sein Capture-Lauf ist
ausdrücklich als Nachweisübernahme und nicht als neuer Scannerlauf markiert.

Ein absichtlich abgebrochener Import lässt keine Teil-Findings oder
Importnachweise zurück. Ein erneuter Import in die befüllte Generation wird
abgewiesen. Nach erfolgreicher Übernahme verwenden `KeyCorrectionProposal`,
gemeinsame Preflight-Prüfung und `KeyCorrectionApplication` diese neue
Generation über eine eigene Verbindung mit ausdrücklich gesetztem Suchpfad.
387 Links werden erstellt; identischer Retry liefert denselben fachlichen
Beleg und erzeugt keinen weiteren History-Eintrag. Die Schema-Prüfung besteht
auch nach den Datentransaktionen; die restaurierte Ausgangsbasis bleibt
unverändert.

Validierung: 1.218 Assertions der vollständigen Generalprobe erfolgreich.
Der anfängliche neue Retry-Test wurde korrigiert, weil JSONB keine
Objektschlüsselreihenfolge garantiert. Er vergleicht jetzt nach Sortierung
der Objektschlüssel weiterhin streng Datentypen, Werte und Listenreihenfolge.

**Grenze und nächster Schritt:** Dies ist kein allgemeiner Importer. Der
vollständige Key-Namensraum ist im ausgewählten Zielbestand noch nicht enthalten;
der synthetische Test-Zielkey wurde zuvor gegen den vollständigen Ausgangsbestand
geprüft. Die übrigen Übernahmekategorien, die vollständige Namenskonfliktprüfung
sowie Bearbeitung/Freigabe/Export auf dieser neuen Generation bleiben offen.
Die bisherigen Exportprüfungen laufen weiterhin auf der restaurierten
Test-Baseline. Keine produktiven Daten oder Dateien wurden verändert.

### Ergänzung: Namenskonflikte außerhalb der Teilübernahme – 20. Oktober 2026

Schema-Version 2 ergänzt `baseline_name_inventories`, ohne die V1-SQL-Dateien
zu verändern. `BaselineNameInventory` erfasst veröffentlichte und aktive
Key-Namen getrennt nach Namespace. In der Generalprobe sind dies 972 Namen.
Inventar, Umfang und Prüfsumme sind immutable Nachweise, keine aktiven
Key-Entscheidungen oder Übersetzungen.

Die ausgewählte Übernahme trägt dieses vollständige Inventar mit. Vorschau
und `KeyCorrectionProposal` prüfen damit auch exakte, übergeordnete und
untergeordnete Pfadkonflikte außerhalb der 387 übernommenen Findings.
Fehlt das Inventar, blockieren Teilvorschau und Datenbankprüfung.
Geänderte Inventare ändern den Vorschlagskontext; vor der Übernahme wird
zusätzlich der aktuelle Quellen-Namensbestand verglichen.

Die im vorigen Abschnitt ausdrücklich offene Namensraumprüfung ist damit für
den gesicherten Ausgangsstand ergänzt. Neue aktive Namen werden zusätzlich
in der jeweiligen Generation geprüft. Namespaces und Segmentgrenzen werden
nicht vermischt; keine automatische Zusammenführung nach Textgleichheit.

Validierung: 1.228 Assertions der isolierten Generalprobe, 43 gezielte Pest-Tests
mit 101 Assertions. Die bisherige Teilübernahme und ihre Korrektur-/Replay-Tests
bleiben enthalten. Keine produktive Verbindung, Cluster beendet.

Als nächster Schritt bleiben Bearbeitung, Freigabe und Veröffentlichung auf
der frisch befüllten Generation sowie die weiteren Übernahmekategorien.
Version 2 wird nur in ein neues Schema installiert; automatische Upgrades
bestehender Generationen sind noch nicht implementiert.

### Ergänzung: Bearbeitung und Veröffentlichung in der neuen Generation

Am 20. Oktober wurde der Ablauf auch auf dem frisch befüllten Schema geprüft:
Nach dem expliziten Import eines EN-Entwurfs wird dieser freigegeben, als neue
Revision bearbeitet, erneut freigegeben und in eine temporäre Sprachdatei
geschrieben. Die vorherige Freigabe gilt nach der Änderung nicht mehr.
Rücklesen prüft den tatsächlich geschriebenen Wert gegen Revision und Receipt.

`SelectedGenerationPublication` verwendet die bestehenden Testdienste, keine
zweite Implementierung der Writer. Replay erzeugt keine weitere Revision;
veraltete Bearbeitungsstände, ungeprüfte Exporte und Dateidrift werden abgewiesen.
Findings und Importprovenienz bleiben unverändert. Der Test-Publisher trennt
die Dateiverzeichnisse zusätzlich nach Schema, damit die neue Generation nicht
die Exportdateien der restaurierten Baseline verwendet.

Ergebnis: **1.247 Assertions**, `selected_generation_publication_verified=true`.
Keine produktive Verbindung, Cluster gestoppt. Die Workflow-Notizen ergänzen
diesen Nachweis, ohne die ursprünglichen Graphen zu ändern.

Damit ist der im vorherigen Abschnitt offene Bearbeitungs-/Publikationsablauf
für die ausgewählte Generation nachgewiesen. Weitere Übernahmekategorien
und die Anbindung der schreibenden Workbench bleiben offen. Die Writer für
Entwürfe, Review und Export sind weiterhin isolierte Testdienste; der erfolgreiche
Lauf ist keine produktive UI-Integration oder vollständige Migration.

### Ergänzung: vollständige statische Erfassung im frischen Schema

`FreshGenerationCapture` verwendet die vorhandenen `StaticCaptureImporter`
und `ExistingKeyImporter` mit dem geprüften Capture-Manifest und Key-Plan.
Vorher werden Sicherungszugehörigkeit, Prüfsummen und die referenzierten
Quell-/Sprachdateien kontrolliert. Dies ist kein neuer Scan des heutigen
Produktivbestands, sondern die Übernahme der gesicherten statischen Erfassung.

| Ergebnis | Anzahl |
| --- | ---: |
| Eigenständige erfasste Findings | 19.940 |
| Noch ungeklärte Erfassungen ohne aktives Finding | 72 |
| Ausgeschlossene Stellen ohne aktives Finding | 108 |
| Eindeutig belegte, strukturell gültige Keys | 768 |
| Finding-Key-Verknüpfungen | 1.792 |
| Erfasste Findings mit blockierter Key-Struktur, ohne Key-Zuordnung | 415 |

Die 415 blockierten Findings sind Teil der 19.940 erfassten Findings, keine
zusätzliche Erfassungskategorie. Literale erhalten hier keine automatischen
Key-Verknüpfungen. Textgleichheit wird nicht zur Konsolidierung verwendet.
Für jede Erfassung werden Rohfelder und Position verglichen. Replay erzeugt
keinen zweiten Scan oder doppelte Links; ein veränderter Key-Plan wird
einschließlich bereits begonnener Zuordnungen zurückgerollt. Die geprüfte
Schema-Struktur und die restaurierte Ausgangsdatenbank bleiben unverändert.

Gesamtlauf: 1.265 Assertions; zusätzlich vier Report-Tests mit 27 Assertions,
Syntax-, Pint- und Whitespace-Prüfung. Report-Flag:
`fresh_generation_capture_verified=true`.

Offen bleiben insbesondere die Rollen-/Argumentnachweise für dynamische
Quellen und Optionen, veröffentlichte Werte in dieser vollständigen Generation
und die aktive UI-Anbindung. Diese Probe importiert keine alten Revisionen,
Freigaben oder History. Sie ersetzt weder die vollständige Übernahmebilanz
aller gesicherten Tabellen noch eine produktive Umschaltung.

### Ergänzung: Rollen- und Argumentnachweise in der frischen Generation

Die am 20. Oktober ergänzte Probe verwendet die bestehende Klassifizierung
`CaptureSourceRole` und den bestehenden `SourceArgumentImporter`.
`SourceRoleImporter` ergänzt eine transaktionale, wiederholbare Speicherung
der Rollenprojektion; `FreshGenerationArguments` prüft den Gesamtvorgang.

Die 72 offenen Erfassungen bleiben getrennt:

| Rolle | Anzahl |
| --- | ---: |
| Übersetzungsquelle mit ausstehender Klärung | 31 |
| Optionsquelle mit ausstehender Klärung | 28 |
| Reine Wertverwendung | 13 |

Für die 59 Übersetzungs-/Optionsquellen bleiben 59 Argumentnachweise erhalten:
3 überschreibbare Defaults, 11 benötigte Laufzeitwerte, 7 Beobachtungen zu
Datenlieferanten, 10 Literal-/Key-Entscheidungsfälle und 28 Optionsargumente.
Sechs Datenlieferanten werden separat beschrieben; daraus folgt keine
behauptete Vollständigkeit aller Laufzeitwerte.

Capture-, Review- und Argument-Prüfsummen sowie referenzierte Dateien,
Helper und Datenlieferanten werden gegen die gesicherte Quellkopie geprüft.
Der Roundtrip erhält die gesamten Argument-/Producer-Payloads.
Importabbruch, Replay, abweichender Review, fehlender Nachweis, untergeschobene
Freigabe und Wertverwendung als Übersetzungsquelle sind geprüft.
Findings, Keys, Verknüpfungen, Revisionen, Reviews und Events bleiben unverändert.

Gesamtergebnis: **1.347 Assertions**, Cluster gestoppt, keine produktive Verbindung.
Report-Flag: `fresh_generation_arguments_verified=true`.
Dies ergänzt die im vorherigen Abschnitt offene Nachweisübernahme; die
inhaltlichen Entscheidungen bleiben weiterhin offen. Als nächster Schritt
folgen veröffentlichte Werte in dieser vollständigen Generation, bevor die
schreibende UI angebunden wird. Die Schema-Version bleibt 2.

### Ergänzung: veröffentlichte Werte in der vollständigen Generation

`FreshGenerationPublishedValues` verwendet `PublishedValueBaseline` und
`BaselineTranslationReader` unverändert. Aus der gesicherten Quellkopie
werden 2.016 veröffentlichte Werte aus 54 App-PHP-Sprachdateien und neun
Locales übernommen. 32 Dateien außerhalb dieses Scopes bleiben explizit
ausgeschlossen; das ist keine vollständige Vendor-/JSON-/Optionsübernahme.

| Zustand der veröffentlichten Nachweise | Anzahl |
| --- | ---: |
| Einem aktiven Key zugeordnet | 1.597 |
| Wegen Key-Struktur blockiert | 22 |
| Ohne aktiven Key | 397 |

Alle Werte werden exakt mit Pfad und Datei-Prüfsumme zurückgelesen.
Für sämtliche 972 beobachteten Key-Namen wird die ausdrücklich vorgegebene
Lesekette `de-at → de → en` geprüft; 961 nutzen einen Fallback.
Fehlende Locale-Werte bleiben NULL. Dies definiert keine neue globale
Fallback-Konfiguration und normalisiert keine Locale-Schreibweisen.

Ein absichtlich fehlerhafter Import hinterlässt keine Teilbasis; Replay
dupliziert keine Nachweise. Löschversuche gegen die immutable Baseline und
das ergänzte Namensinventar werden abgewiesen. Alle anderen Tabellen der
Generation bleiben unverändert, insbesondere Findings, Key-Verknüpfungen,
Argumentnachweise, Entwürfe, Freigaben und History.

Gesamtlauf: **1.363 Assertions**, keine produktive Verbindung, Cluster gestoppt.
Report-Flag: `fresh_generation_published_values_verified=true`.
Als nächster Schritt folgt eine zusammenhängende Übernahmebilanz: übernommene
Nachweise, offene Aufgaben und bewusst ausgeschlossene Bestände sichtbar
trennen. Diese Teilbilanz ersetzt noch nicht die Bilanz aller gesicherten
Tabellen oder eine produktive Umschaltung.

### Ergänzung: sichtbare Übernahmebilanz

Unter **Workflow → Baseline preview → Recorded isolated workflow rehearsal →
Workflow verification details → Recorded transfer balance** werden die
aufgezeichneten Nachweise jetzt gemeinsam dargestellt. Die vorhandene
Generalprobe wird beim Öffnen nicht gestartet; es gibt keine Datenbankabfrage
und keinen Schreibaufruf.

`BaselineTransferBalance` prüft die Zusammenhänge der Zähler, bevor Zahlen
angezeigt werden. Fehlende Schritte gelten nicht als Null; falsche Datentypen,
negative Zahlen, unvollständige oder widersprüchliche Teilmengen blockieren
die Bilanz mit einem Hinweis. Jede Flux-Accordion-Tabelle besitzt eine eigene
Einheit; eine Gesamtsumme über alle Tabellen wird bewusst nicht gebildet.

| Abschnitt | Einheit / Umfang |
| --- | --- |
| Quellerfassung | 20.120 Stellen = 19.940 erfasst + 72 offen + 108 ausgeschlossen |
| Key-Zuordnung der erfassten Findings | 19.940 = 1.792 verknüpft + 415 strukturell blockiert + 17.733 weitere unverknüpfte |
| Rollen der offenen Erfassungen | 72 = 31 Übersetzungsquellen + 28 Optionsquellen + 13 Wertverwendungen |
| Veröffentlichte Sprachwerte | 2.016 = 1.597 zugeordnet + 22 strukturell blockiert + 397 ohne aktiven Key |
| Sprachdatei-Scope | 86 = 54 einbezogen + 32 ausgeschlossen |

768 Keys, neun Locales, 59 Argumentnachweise und sechs Producer-Beschreibungen
sind ergänzende Informationen, keine weiteren Findings. Der separate
synthetische Korrekturfall wird nicht dazuaddiert.

Die Darstellung nennt den Stand ausdrücklich eine Bilanz der gesicherten
Testgeneration. Alte History und Freigaben bleiben außerhalb; eine vollständige
Archivzählung aller Tabellen ist daraus nicht ableitbar. Die 397 unzugeordneten
Sprachwerte werden nicht pauschal als unnötig oder löschbar eingestuft.
Unveröffentlichte historische Werte und weitere Übernahmekategorien brauchen
weiterhin eigene Zuordnungen und Entscheidungen.

Der nächste Schritt ist die datensatzbezogene Aufschlüsselung der offenen
Bestände für eine überprüfbare UI-Bearbeitung, ohne dadurch automatisch Keys,
Werte oder Finding-Verknüpfungen zu ändern.

### Klarstellung: welcher Datenbestand derzeit benutzt wird

Die reguläre Workbench/Findings arbeitet weiterhin mit der bisherigen
Anwendungsdatenbank. Die neue Generation wurde real in isolierten
PostgreSQL-Testdatenbanken aufgebaut und geprüft; deren Cluster werden
anschließend gestoppt. Eine produktive Migration oder Umschaltung ist
noch nicht erfolgt.

Workflow/Baseline preview liest `reader-preview.json` und
`workflow-rehearsal.json` unter `storage/app/private/workbench-previews`.
Diese Dateien sind exportierte Nachweise, keine Live-Verbindung zur neuen
Generation. Der Hinweis am Anfang der Vorschau benennt das jetzt ausdrücklich.

### Ergänzung: offene veröffentlichte Werte nach Key

Unter der Übernahmebilanz steht „Open published values by key“. Der aktuelle
Export liefert 204 Key-/Status-Zeilen für 419 Locale-Werte: 22 strukturell
blockierte und 397 nicht aktiv zugeordnete Werte. Das sind keine 419
verschiedenen Findings. Die übrigen offenen Findings und dynamischen Fälle
sind in dieser Liste nicht enthalten.

`BaselineOpenValues` verlangt eine erfolgreiche Bilanz, dieselbe Baseline,
denselben Ausgangs-Dump und übereinstimmende Locale-Wertzahlen. Bei fehlendem
oder abweichendem Detailnachweis wird keine Liste gezeigt. „Select key“
wählt den betreffenden Key in der bestehenden Vorschau, setzt einen eventuell
einschränkenden Filter zurück und verwirft vorherige Vorschlagsprüfungen.
Es wird weder eine Zuordnung gespeichert noch ein Datensatz verändert.

Die neue Datenbank ist damit noch nicht als Arbeitsbestand aktiviert.
Davor bleiben die vollständige Übernahmebilanz einschließlich unveröffentlichter
Altwerte, die schreibende UI-Anbindung, Abnahme und ein konkreter beschlossener
Umschaltvorgang offen.

### Ergänzung: historische Werte gegen gesicherte Veröffentlichung

Der [historische Werteabgleich](historical-scalar-review-2026-10-20.md)
erfasst 10.500 Zeilen aus `translation_values` und
`translation_workbench_lang_values`: 3.278 exakt identisch, 10 abweichend,
508 ohne exakten Nachweis und 6.704 ohne String-Wert. Diese Zeilenzahlen
sind keine zusätzlichen eindeutigen Übersetzungen und werden nicht mit
der neuen Baseline summiert. Vier weitere historische Wertetabellen
mit zusammen 1.442 Zeilen bleiben getrennt zu prüfen.

Der Vergleich nutzt ausschließlich die verifizierte Sicherung, arbeitet
lesend in einem isolierten Cluster und importiert nichts. Der Cluster
ist gestoppt. Alle Übernahmeentscheidungen bleiben offen. 13 Tests / 61
Assertions sichern den Vergleich ab.

### Ergänzung: persistente Prüffälle statt ausschließlich Audit-Listen

Die neue [Prüffallablage und der Rescan-Vertrag](review-cases-and-rescan-contract-2026-10-20.md)
wurden im isolierten PostgreSQL-Cluster erprobt: 10.500 Fälle, davon
518 `needs_review` und 9.982 `recorded`. Ein zweiter Durchlauf erzeugte
21.000 Beobachtungen, aber weiterhin nur 10.500 Fälle und Nachweisversionen.
Es wurden keine Benutzerentscheidungen erfunden und keine Werte aktiviert.
Ein privater Datenbank-Dump erhält die Prüffalltabellen; Pfad und Hash stehen
im `review_queue`-Abschnitt des historischen JSON-Berichts. Der Cluster ist gestoppt.

Die Evidenzabfragen blieben READ ONLY. Nur das separate Schema
`review_generation` des temporären Clusters wurde beschrieben. Die produktive
Datenbank blieb unberührt. Die SQL-Struktur wird nicht automatisch migriert.
17 Unit-Tests mit 83 Assertions prüfen Wertevergleich und Fall-/Entscheidungsablage.

Noch offen: produktive Scan-Anbindung einschließlich mehrdeutiger Zuordnungen,
Originaltext-Auflösung und schreibende Workbench-UI. Eine gespeicherte Entscheidung
ist ausdrücklich noch keine ausgeführte Übernahme oder Veröffentlichung.

### Ergänzung: lesende Prüffallansicht mit Originalwerten

Unter Workflow → Baseline preview steht jetzt „Historical value review cases“.
Die Ansicht liest den privaten Export `workbench-previews/historical-review.json`
aus der isolierten Datenbankablage. 518 offene Fälle lassen sich nach Key,
Locale, Quelltabelle oder #ID filtern und mit Flux-Pagination in Seiten zu
20 Zeilen anzeigen. „Inspect“ zeigt die beiden Originaltexte, Quellstatus,
Herkunft und erforderliche Entscheidung.

Originalwerte werden beim Export gegen die gespeicherten Hashes geprüft.
Der Reader prüft erneut Text-Hashes, Fallidentität, Nachweisversion, Zustände
und Zeilenzahl. Inkonsistente Exporte werden nicht angezeigt. Die UI gibt
Texte escaped aus; fehlende Veröffentlichungsevidenz wird nicht als leerer
veröffentlichter Text dargestellt.

Es gibt bewusst noch keine Schreibaktion: keine verborgenen Entscheidungen
in JSON und keine Verbindung zur regulären Datenbank. Die schreibende UI
benötigt einen dauerhaft angebundenen v2-Arbeitsbestand samt Berechtigungen
und geprüften Folgeoperationen. 28 View-/Reader-/Workflow-Tests mit 318
Assertions bestanden; der reale Export mit 518 Fällen wurde zusätzlich
validiert. Der PostgreSQL-Testcluster ist gestoppt.

### Ergänzung: gruppierte Prüfung gehört in die Workbench

Die aktive Prüfansicht wurde aus dem Workflow in die Workbench verschoben
(`Historical translation review`, oberhalb der Findings). Der Workflow enthält
nur noch einen Link. Eine eigene Livewire-Komponente hält Filter und Pagination
von der übrigen Workbench getrennt; verwendet werden Flux und `x-ui.locale.flag`.

Die 518 offenen Fälle betreffen 223 vollständige Keys. Der Export enthält jetzt
1.940 zugehörige historische Zeilen einschließlich identischer Werte und
fehlender Textwerte, damit der Locale-/Status-Kontext nicht ausgeblendet wird.
Zusätzlich sind die veröffentlichten Werte dieser Keys aus derselben Sicherung
aufgeführt. Gruppierung verbindet nur die Darstellung, nicht die Datensätze.

Für `admin.permissions.filters.guards.all` sind vier Zeilen `obsolete`
(de/en in zwei Tabellen) und sieben Zeilen `missing` vorhanden. Herkunft:
Legacy-Werte aus `audit` ohne gespeicherten Dateipfad; importierte Sprachwerte
mit `lang/de/admin.php` bzw. `lang/en/admin.php`. Dies sind keine nachgewiesenen
Source-Code-Fundstellen. `obsolete` wird als historischer Einzelzeilenstatus
gezeigt, nicht als pauschale Aussage über den Key, Textalter oder Archiventscheidung.

Die Ansicht bleibt lesend, mit explizitem Hinweis auf die Sicherung statt
aktueller Live-Daten. Keine produktiven Statusänderungen oder Entscheidungen.
26 Tests mit 311 Assertions bestanden; der reale Export wurde vollständig validiert.

### Richtungswechsel: aktueller Scan als Neustart-Grundlage

Die historische Werteliste ist nicht mehr die aktive Aufgabenbasis. Ein
[frischer Quellcode-Scan](current-source-scan-2026-10-20.md) ersetzt sie in der
Workbench: 1.717 Dateien, 1.792 Fundstellen zu 768 wiederverwendbaren Keys,
18.127 Literal-Fundstellen, 425 Fundstellen zu 20 Key-Prüfgruppen und 62
ungeklärte Ausdrücke. Historische Fälle ohne aktuellen Fund werden nicht
weiter als abzuarbeitende Aufgaben behandelt. Die Sicherungen bleiben erhalten.

Keine Live-Datenbankverbindung, keine Aktivierung oder Übernahme. Die neue
Prüfvorschau liest abgeschlossene aktuelle Scan-Nachweise. 36 Tests / 329
Assertions bestanden, reale Nachweise zusätzlich geprüft.

### Aktueller Scan in separater Generation importiert

Der [isolierte Import mit Wiederherstellungsprüfung](current-generation-import-2026-10-20.md)
ist abgeschlossen: 20.334 bestätigte Findings, 768 Keys, 1.792 Links und
1.597 zugeordnete veröffentlichte Locale-Werte. 415 strukturell blockierte
Key-Fundstellen bleiben ohne automatische Zuordnung. 72 weitere Erfassungen
bleiben im Prüfledger; 112 sind ausgeschlossen. 397 Werte ohne aktuellen
Key-Verweis wurden nicht übernommen. Keine historische Timeline importiert.

Wiederholungsimport unverändert, Dump-Restore einschließlich Schema erfolgreich,
144.343 Assertions im Importlauf. PostgreSQL-interne Triggernamen werden bei der
Schema-Prüfung jetzt ohne installationsabhängige OIDs verglichen; deaktivierte
Fremdschlüssel-Trigger bleiben erkennbar. Produktive Workbench nicht umgeschaltet.
Der Anschluss der UI und die Abstimmung späterer geänderter Scans sind noch offen.

### Scannerläufe vor fachlicher Datenbearbeitung

Die [Scannerlauf-Serie](current-generation-rescans-2026-10-20.md) ist dokumentiert:
zwei weitere vollständige Erfassungen stimmen bytegleich mit der Importgrundlage
überein. Auf einer isolierten Kopie der aktuellen Generation wurden 14
aufeinanderfolgende Datei-Scans und vier abgewiesene Versuche geprüft; 44
Assertions bestanden. Zeilen-/Spaltenbewegungen, zusätzliche Kopie, Änderungen,
Entfernung, Auskommentieren und Wiederauftauchen verhalten sich wie beschrieben.

Kein regulärer Produktiv-Scanner angeschlossen. Die Datenbearbeitung bleibt
zurückgestellt: vollständige Scan-Abstimmung, Datei-Löschungen/-Umbenennungen,
sichtbare Prüfentscheidungen, Shared Candidates und Wiederanlauf sind noch
abzusichern. Der vollständige Bericht unterscheidet diese Grenzen ausdrücklich.

### Vollständiger lesender Scan-Abgleich (21.10.2026)

Der [Gesamtabgleich](complete-rescan-plan-2026-10-21.md) auf der aktuellen,
isoliert wiederhergestellten Generation ist geprüft: 1.717 Dateien,
18.878 unverändert bestätigte Findings und 1.456 erhaltene Findings in 39
Dateien mit ungeklärten Erfassungen. Im unveränderten Bestand werden keine
neuen/verschobenen/entfallenen Findings vorgeschlagen.

Neun zusätzliche Dateiszenarien unterscheiden fehlende Inventur, bestätigte
Löschung, Ausschluss, Verlagerungskandidaten und Kopien. 1.691 Assertions bestanden;
alle Datenbanktabellen unverändert. Keine produktive Umschaltung. Die
transaktionale Anwendung eines vollständigen Plans, sichtbare Prüfentscheidungen
und Wiederanlauf/Konkurrenz sind noch abzusichern.

### Transaktionale Folgescans (21.10.2026)

Die [Speicherung vollständiger Folgescans](complete-rescan-application-2026-10-21.md)
ist auf einer isolierten Generation geprüft. Vorhandenes Operationsprotokoll,
Tabellensperren, erneuter Quell-/Datenbankabgleich und abschließende Inventurprüfung.
31 Assertions bestanden: vollständiger Lauf, Replay, absichtlicher Abbruch,
Wiederanlauf, veraltete Pläne, Quelländerung während der Speicherung,
Prüffallerhalt, Dateilöschung und Wiederauftauchen.

Prüffälle mit Rohinformationen liegen im Scan-Snapshot. Noch keine produktive
Anbindung oder UI-Bearbeitung. Echte Prozesskonkurrenz und Prozessabbruch bleiben
als nächster Prüfblock offen; die Exception-/Rollbackprüfung ersetzt diese nicht.

### Echte Prozesskonkurrenz und Prozessabbruch (21.10.2026)

Die [Parallel-/Abbruchserie](concurrent-rescans-2026-10-21.md) ist bestanden:
30 Assertions mit getrennten PHP-Workern und beobachteten PostgreSQL-Lock-Wartezuständen.
Gleicher Auftrag speichert genau einmal; konkurrierender anderer Auftrag mit
veraltetem Plan wird abgewiesen. SIGKILL nach tatsächlichen Schreiboperationen
rollt zurück; Wiederanlauf mit derselben ID gelingt ohne Duplikate.

Keine Änderung am Writer erforderlich. Keine produktive Umschaltung. Ein Scope
mit echten Mutationen wurde geprüft, kein paralleler Vollprojekt-Lasttest.
Als nächstes bleiben eindeutige Key-Zuordnung neuer Fundstellen und
Shared-Candidate-Zustände sowie später die UI-Prüfentscheidungen abzusichern.

### Key-Zuordnung und Shared Candidates (21.10.2026)

Die [Folgescan-Regeln](rescan-candidate-rules-2026-10-21.md) sind im isolierten
Writer integriert: neue Fundstellen vorhandener Keys werden nur anhand exakter
Key-/Source-Locale-Evidenz verknüpft. Keine Key-Erzeugung oder textbasierte Zuordnung.
Shared Candidates werden aus aktiven identischen Roh-Literalen mit mindestens
zwei Findings und zwei unterschiedlichen SuggestedKeys abgeleitet; keine
Finding-Zusammenlegung. Ergebnisse liegen im Operationsprotokoll.

24 Regelprüfungen plus erneut 30 Parallel-/Abbruchprüfungen bestanden. Geänderte
Locale-Evidenz während der Speicherung rollt vollständig zurück. Produktive
Commands/UI weiterhin nicht umgeschaltet; deren Anschluss und die Darstellung
und Bearbeitung offener Prüfzustände stehen noch aus.

### Lesende Prüffallansicht in der Workbench (21.10.2026)

[Isolated rescan review](rescan-review-ui-2026-10-21.md) ergänzt die vorhandene
Scan-Vorschau: 39 Dateien, 72 Fragen, Pfadsuche, Flux-Pagination, Roh-Ausdrücke,
Zeile/Spalte und erhaltene Finding-IDs. Klar als isolierte Generation gekennzeichnet;
keine Verknüpfung dieser IDs mit alten Live-Datensätzen. Kein Scan auf Seitenaufruf.

Export aus erneut geprüftem Gesamtabgleich (1.691 Assertions), generation- und
hashgebunden. 12 Pest-Tests / 32 Assertions bestanden, alle Exportdetails geprüft.
Noch keine UI-Entscheidungen oder produktive Datenbankumschaltung.

## Bewusst offen

Prioritätskorrektur nach Nutzerfeedback: Weitere Inventuren zurückstellen.
Die neue Generation ist weiterhin nur lesend an die Workbench angebunden; der
UI-Schreibweg fehlt. Nächster funktionaler Meilenstein ist ein vollständig
bearbeitbarer Fall mit persistierter Entscheidung (übersetzen/DatabaseValue),
Statusaktualisierung und Erhaltung beim Folgescan. Keine weitere separate
Prüf-Datenhaltung als Ersatz dafür einführen. Die DB-Optionsausgabe ist nun
außerhalb der verschachtelten Accordions sichtbar, mit Sprunglink am Seitenkopf.

DB-Optionsbeobachtung: Ein expliziter PostgreSQL-READ-ONLY-Lauf liefert 11 aktive
Dokumentarten, 6 Dokumentkategorien und 1 Rollenkategorie aus zuweisbaren Rollen.
Datierte private Momentaufnahme mit Quellnachweisen; unter „Observed database
option values“ lesend sichtbar. Keine automatische Übersetzung, Zuordnung oder
DatabaseValue-Entscheidung. 17 Tests / 60 Assertions bestanden. Kein Scan oder
DB-Zugriff auf Seitenaufruf, keine produktiven DB-Schreiboperationen.

Listennachweise in der UI: 14 Fundstellen besitzen jetzt ausdrücklich geprüfte,
hashgebundene Verbindungen zu ihren festen Listen. Accordion „Default list
evidence“ zeigt Paare und Herkunft; 14 DB-/Laufzeitaufrufe bleiben ohne feste
Liste. Keine Erledigung/DatabaseValue-Markierung. 29 Tests / 84 Assertions
bestanden. Die Vorschau bleibt eine datierte lesende Momentaufnahme.

Konstantennachweise: Die 18 zuvor offenen Kontaktbeziehungs-Einträge sind über
öffentliche String-Konstanten in explizit gelieferter Quelle belegt. Insgesamt
68 Paare in zehn statischen Listen; Datei/Hash/Zeile je Abhängigkeit. 12 Tests /
32 Assertions bestanden. Noch kein Import, keine automatische Erledigung und
keine Änderung produktiver Daten. Details im Optionsproduzenten-Bericht.

Feste Optionslisten und DatabaseValue: Die lesende Inventur erfasst jetzt
50 konkrete Paare aus acht exakt adressierten Defaults/Config-Listen. 18 weitere
Einträge benötigen Konstantennachweise. Nutzerentscheidung `DatabaseValue`
als geplanten UI-Pfad dokumentiert und im Target workflow UI erläutert:
Übersetzungsprüfung erledigt, unveränderte Ausgabe, keine automatische Übersetzung
oder Veröffentlichung. Noch keine produktive Entscheidung gespeichert.

Optionsherkunft 21.10.2026: Die [Inventur der Produzenten](option-producer-review-2026-10-21.md)
ordnet alle 28 Aufrufe ein: 4 konfigurationsbasierte, 10 codebasierte und 14
datenbankabhängige/veränderliche Aufrufe. Reproduzierbarer lesender AST-Bericht
für neun Quelldateien mit Hashes und Zeilen; keine Provider-Ausführung, keine DB.
Die Fälle bleiben offen bis zur kontextgebundenen Übernahme fester Listen bzw.
einer explizit lesenden Laufzeitbeobachtung. Keine alten Collector-Schreibwege
als Abkürzung verwenden.

Optionskontext 21.10.2026: Alle 28 Optionsaufrufe besitzen nun getrennte,
quellgetreue Angaben zu Gruppen-Key, Optionswert-Ausdruck, Fallback und Locale
in der lesenden Prüfansicht. Keine Übernahme des Gruppen-Keys als skalarer Key.
59 Fragen bleiben offen, da die dynamischen Werte noch einen Herkunftsnachweis
benötigen. Neuer isolierter Abgleich und UI-Export verifiziert; siehe
[Prüffallansicht](rescan-review-ui-2026-10-21.md).

Aktualisierung 21.10.2026: Reine `@js($variable)`-Serialisierungen werden nicht
mehr als Übersetzungsaufrufe erfasst. Echte darin verschachtelte Übersetzungen
bleiben erhalten. Der erneut verifizierte isolierte Abgleich bestätigt 18.914
Findings und erhält 1.420 in 32 Prüfdateien. UI-Export aktualisiert;
1.698 Abgleich-Assertions sowie 7 Scanner-Tests / 40 Assertions bestanden.
Details und Berichtspfade: [Prüffallansicht](rescan-review-ui-2026-10-21.md).


- `admin.permissions.actions.edit_role_permissions` bleibt wie vereinbart zur späteren sichtbaren UI-Korrektur stehen.
- Inhaltliche Konflikte aus dem historischen Entscheidungsmanifest werden nicht durch einen pauschalen „ready“-Status erledigt.
- Die neue Timeline dokumentiert tatsächliche neue Arbeitsvorgänge; alte Test-/Entwicklungsereignisse werden nicht als neue History importiert.
- Bestehende Übersetzungskeys im Quellcode bleiben Ausgangsbasis. Ein flächiges Zurückschreiben als Literale ist kein Bestandteil dieses Plans.

### Bearbeitbare Quellenentscheidungen · 21.10.2026

Direkt oben unter `/admin/translation-workbench` steht jetzt **Review source usage**.
59 aktuelle Prüfstellen sind mit der verifizierten Generation verbunden. Die bisher
isolierte Generation wurde additiv in `current_generation_v2` der Anwendungsdatenbank
bereitgestellt; die bisherigen öffentlichen Workbench-Tabellen bleiben unverändert.

**Review → Translate / DatabaseValue → Begründung → Save decision** speichert eine
Entscheidung mit Benutzer, Zeitpunkt und Vorgänger im bestehenden unveränderlichen
Operations-/Eventjournal. Unter **All cases including decisions** lässt sie sich erneut
ansehen und revidieren. DatabaseValue schließt diese Quellenprüfung ab; Translate
kennzeichnet weiteren Übersetzungsbedarf. Dies veröffentlicht keine Übersetzung und
schreibt keinen Quellcode um. Der Übersetzungseditor für diese neuen Fälle ist noch
nicht angebunden. Das Fallverzeichnis bleibt ein hashgeprüfter Evidenz-Snapshot;
Entscheidungen selbst liegen in PostgreSQL, nicht in einer weiteren Reportdatei.

Speichern prüft Quellhash und konkurrierende Entscheidungen. Die inzwischen geänderte
`workflow/target-ui.blade.php` benötigt für ihren einen Fall frische Scan-Evidenz;
58 weitere Fälle entsprechen dem erfassten Quellstand. Es wurden keine Entscheidungen
für den Benutzer vorweggenommen. Der isolierte PostgreSQL-Test prüft Persistenz,
Revision, Wiederholung, Konkurrenz und veränderten Quellcode. Das Einfügen eines
weiteren Scan-Datensatzes erhält Entscheidungen; eine vollständige automatische
Übertragung dieser UI-Fallliste bei zukünftigen Scannerläufen bleibt noch offen.

### Umstieg: regulärer Generationsscanner · 21.10.2026

Der Umstieg ist **noch nicht abgeschlossen**. `public` enthält weiterhin 25
Workbench-Tabellen; Findings, Editoren und Timeline der bisherigen Hauptansicht
verwenden diese Tabellen. Die UI kennzeichnet beide Datenquellen jetzt ausdrücklich.
Kein alter Schreibweg wurde durch bloßes Umstellen von `search_path` auf ein anderes
Datenmodell umgebogen.

Der geprüfte Rescan-Code liegt nun einmalig unter `src/Generation` statt unter
`tests/Support`. Test- und Prüfskripte verwenden dieselben Klassen; die bisherigen
Kopien wurden entfernt. Der reguläre Befehl lautet:

```sh
php artisan translation-workbench:generation-scan
php artisan translation-workbench:generation-scan --apply
```

Ohne `--apply` bleibt der Lauf lesend. Das Ziel ist die separate Verbindung mit
Suchpfad `current_generation_v2,pg_catalog`, ohne Fallback auf `public`. Vor dem
Scan werden Generation und Tabellenstruktur verifiziert. Der Umfang stammt aus
`translation-workbench.paths`. Vorhandene englische Werte dienen nur mit eindeutigem,
aktuellem Dateihash als Nachweis; fehlender Nachweis erzeugt keine automatische
Key-Zuordnung. Ungeklärte Dateien bleiben erhalten. Der Befehl ersetzt noch nicht
den regulären bisherigen Pipeline-Befehl und aktualisiert noch nicht das dateibasierte
UI-Prüffallverzeichnis. Er verändert weder Quellcode noch Sprachdateien.

Vor dem ersten schreibenden Lauf wurde die komplette Anwendungsdatenbank gesichert:
`storage/app/private/backups/workbench-cutover-20261021-060557-148bf61e/database.dump`
(331.004.910 Bytes, SHA-256 im benachbarten Manifest). Das Archivverzeichnis wurde
geprüft; eine vollständige Rücksicherung dieser Sicherung wurde noch nicht erprobt.
Die Sicherung ist privat und gehört nicht ins Repository.

Erster regulärer Lauf: 18.854 bestätigt, 91 neu, 60 Verschiebungen, keine entfernten
Fundstellen, 32 Prüfdateien. Scan-ID `77171b72-b051-4356-9dfd-f9dea65757bf`.
Der anschließend unveränderte Scanplan bestätigt 19.005 Fundstellen und enthält
keine Neuanlagen, Verschiebungen oder Entfernungen.

Vor der Entfernung des Altbestands verbleiben konkret:

1. Findings-/Key-/Wertebearbeitung und deren Modals auf das neue Datenmodell umstellen;
   veröffentlichte Baseline-Werte und bearbeitbare Revisionen dabei unterscheiden.
2. Die Prüffallliste direkt an Scan-/Entscheidungsdaten anbinden, damit ein weiterer
   Scan nicht einen veralteten Dateisnapshot neben aktuellen Findings stehen lässt.
3. Bisherige Pipeline-/Editor-Schreibwege stilllegen, vorhandene Raw-Data-/Graph-Leser
   umstellen und anschließend den gesicherten Altbestand entfernen.

Der alte Bestand wurde nicht gelöscht: insbesondere die 1.340.120 alten Events und
175.456 Timeline-Events gehören weiterhin zu den noch nicht migrierten Lesern.
Dies ist eine verbleibende Umstellung, keine gewünschte Dauerarchitektur.

Der Wiederholungslauf wurde ebenfalls gespeichert:
`03b50384-64b5-46de-95c4-5add52ff67d7`, ohne weitere Findings anzulegen oder zu
archivieren. Prüfungen: 18 Pest-Tests / 79 Assertions, 42 Capture-Prüfungen und
31 isolierte PostgreSQL-Schreibprüfungen bestanden. Die veraltete Erwartung
39 Prüfdateien im alten Integrationstest wurde auf den seit der @js-Korrektur
bestehenden Stand von 32 Prüfdateien angepasst.

### Haupt-Workbench auf neue Findings und Entwürfe umgestellt · 21.10.2026

`/admin/translation-workbench` und der stabile Livewire-Name
`translation-workbench.entries` zeigen jetzt `GenerationWorkbench`. Die bisherige
`TranslationWorkbenchEntries`-Implementierung bleibt als noch nicht entfernte
Referenz im Repository, ist aber nicht mehr die Hauptseiten-Route/Registrierung.
Es wurde keine zweite Menüseite hinzugefügt.

Die neue Hauptseite liest Findings, aktive Key-Verknüpfungen, Key-Namen und
Entwurfsrevisionen ausschließlich über die separate Generationsverbindung.
Sie bietet ID-/Textsuche, aktive/archivierte Findings und Flux-Pagination.
Im Modal sind Originalerfassung, Vorgänger/Nachfolger, Key-Zuordnung,
veröffentlichte Dateievidenz, Sprachentwurf, verknüpfte Findings und neue
Arbeitsereignisse getrennt sichtbar. Die ersten 100 verknüpften Findings und
50 Arbeitsereignisse werden mit entsprechenden Hinweisen angezeigt.

Key-Zuordnung und Erzeugen eines neuen Keys erfolgen ausdrücklich mit Grund und
Flux-Bestätigungsmodal. Eine Fundstelle bleibt eigenständig; gleiche Texte erzeugen
keine automatische Zusammenlegung. Erfassungswerte und SuggestedKey bleiben
unverändert. Namens-/Pfadkonflikte werden abgelehnt. Der bisher isoliert verwendete
`BaselineDraftWriter` liegt jetzt einmalig in `src/Generation`; Tests und neue UI
nutzen denselben Writer. Gespeichert werden Revisionen und Arbeitsereignisse mit
Benutzer, Zeit und Begründung. Eine aktive Zuordnung sowie die erwartete Revision
werden im Schreibvorgang erneut geprüft. Archivierte Findings sind nicht editierbar.

Veröffentlichte Dateievidenz wird nicht automatisch zum Entwurf. Eine eindeutige,
hashaktuelle Datei kann mit **Copy published value into draft** explizit übernommen
werden; erst **Save draft** schreibt eine Revision. Leere Strings und `0` bleiben
unterscheidbar und sind zulässige Werte. Es wird weder publiziert noch Quellcode
umgeschrieben. Die Auswahl der Sprachen verwendet die vorhandene Locale-Konfiguration.

Der Tab **Source usage decisions** enthält die bisherige Quellenentscheidung und
die bestehende lesende Vorschau beobachteter Datenbankwerte. Die Prüffallliste
beruht noch auf dem bestehenden Evidenz-Snapshot; dessen Ablösung bleibt offen.
Raw Data, TW-Graph und bisherige Pipeline-Befehle sind noch nicht auf den neuen
Bestand umgestellt. Dies wird auf der Hauptseite benannt. Keine Alttabelle wurde
in diesem Schritt entfernt.

Verifikation: 13 Pest-Tests / 53 Assertions; 27 neue isolierte PostgreSQL-Prüfungen
für diesen Editor; vorhandene Persistenzsuite mit 186 Assertions / 10 Race-Szenarien
und 9 Restart-Prüfungen bestanden. Serverseitiger Seitenaufruf mit dem vorhandenen
Testkonto: HTTP 200. Echtes Finding #1 / Key #768 einschließlich englischer
Dateievidenz sowie Wechsel zur Quellenprüfung erfolgreich lesend gerendert.
Dabei keine Arbeitsereignisse erzeugt. Eine manuelle Browserprüfung ist noch offen.

### Gespeicherte Revisionen in der Workbench prüfen · 21.10.2026

Im neuen Bearbeitungsmodal steht nach **Save draft** die **Revision review** bereit.
**Review and approve revision** öffnet ein eigenes Flux-Bestätigungsmodal mit
Key, Sprache, gespeicherter Revision, Übersetzungswert und Anzahl aktiver
verknüpfter Fundstellen. Eine Begründung ist Pflicht. Ungespeicherte Textänderungen
verhindern die Freigabe; ein Wechsel der Fundstelle, Sprache oder Zuordnung
verwirft eine noch offene Bestätigung. Eine bestehende Freigabe lässt sich über
**Revoke approval** ausdrücklich widerrufen. Die letzte Entscheidung einschließlich
Benutzer, Zeitpunkt und Grund ist sichtbar, ebenso die Ereignisse in der Historie.

`BaselineRevisionReviewer` und `RevisionReviewContext` wurden aus dem Testbereich
nach `src/Generation` verschoben. UI, Persistenztests und Veröffentlichungsproben
verwenden dieselben Implementierungen. Die Freigabe gilt für die gespeicherte
Revision, den aktuellen Key-Namen und die aktiven Quellverknüpfungen. Änderungen
an diesem Kontext machen sie ungültig; eine alte Bestätigung wird beim Speichern
abgelehnt. Neue Revisionen erben keine Freigabe. Leerer String und `0` bleiben
zulässige, ausdrücklich prüfbare Übersetzungswerte.

Dieser Schritt schreibt ausschließlich Freigabeentscheidungen und zugehörige
Arbeitsereignisse in die neue Generation, wenn der Benutzer bestätigt. Im echten
Datenbestand wurden zur Verifikation keine Entscheidungen erzeugt. `/lang` und
Quellcode bleiben unverändert. Die Veröffentlichung aus der Haupt-Workbench ist
weiterhin offen: Der bisherige Publisher ist absichtlich auf isolierte Testcluster
beschränkt. Seine Anbindung benötigt insbesondere eine konkrete Dateivorschau,
Bestätigung sowie Wiederanlauf-/Abschlussmöglichkeiten für unterbrochene Läufe.
Die sichtbare Seiteninformation benennt diese Grenze weiterhin.

Verifikation: 16 Pest-Tests / 75 Assertions; 35 isolierte PostgreSQL-Editorprüfungen;
Persistenzsuite 186 Assertions / 10 Race-Szenarien sowie 9 Restart-Prüfungen bestanden.
Das echte Modal für Finding #1 / Key #768 wurde mit dem Testkonto lesend gerendert;
Arbeitsereignisse blieben unverändert. Keine manuelle Browserprüfung durchgeführt.

### Veröffentlichung aus der neuen Workbench · 21.10.2026

Nach der Freigabe ist im Bearbeitungsmodal **Prepare publication preview** verfügbar.
Der Benutzer gibt einen Grund an; die Workbench prüft den gespeicherten
Freigabekontext erneut und erstellt einen dauerhaften Veröffentlichungsplan.
Die separate Flux-Abfrage zeigt Zieldatei, Key, Sprache, Revision sowie alten und
neuen Wert. Erst **Confirm / resume publication** schreibt die Sprachdatei.
Ungespeicherte Änderungen im Entwurfsfeld verhindern die Vorbereitung.

`GenerationPublisher` ist der aus der Persistenzprobe übernommene gemeinsame
Schreibkern. Der Testadapter `PostgresPublisher` enthält nur noch den abgesicherten
Aufbau seiner isolierten Fixture-Verbindung und des temporären Dateiverzeichnisses.
Die Anwendung bindet ausschließlich die verifizierte neue Generation und
`lang_path()` ein. Der unveränderliche Plan ist an dieses reale Verzeichnis
gebunden. `BaselineValueReader` liegt ebenfalls einmalig in `src/Generation`.
Die Dateiinhalte werden statisch geparst und nicht ausgeführt. Der neue
`TranslationFileEditor` erhält Kommentare und Formatierung unveränderter Einträge;
vorhandene Dateirechte bleiben beim atomaren Ersetzen erhalten. Neue Dateien
werden mit 0644 erstellt. Vorherige Dateiinhalte sind im Veröffentlichungsplan
aufbewahrt. OPcache wird für ersetzte Dateien invalidiert.

Vor Schreiben werden aktuelle Freigabe, Revision, Key-/Quellverknüpfungen,
reservierte Dateiziele und Dateihashes erneut geprüft. Die Identitäts- und
Quelltabellen sind während dieses kurzen Vorgangs gegen konkurrierende Änderungen
geschützt; bei einem Lockkonflikt wird abgebrochen statt lange gewartet. Bei
Dateiänderungen außerhalb des Plans wird nicht überschrieben. Dateiweise atomare
Ersetzung ist keine globale Dateisystemtransaktion: Ein Abbruch nach einem
Schreibvorgang bleibt deshalb als offener/unterbrochener Lauf sichtbar. Wiederaufnahme
überspringt bereits passend geschriebene Dateien und vervollständigt den Beleg.

**Publication runs** zeigt alle offenen und die letzten 20 abgeschlossenen Läufe.
Ein Lauf lässt sich auch nach Navigation wieder öffnen. Bestätigen/Wiederaufnehmen
kann der vorbereitende Benutzer. Andere berechtigte Bearbeiter können den Lauf
prüfen und mit eigener Begründung schließen. **Review closure without further
writes** zeigt dazu die ausdrückliche Bestätigung: Bereits geschriebene Dateien
werden nicht zurückgesetzt. Abschluss gibt die Reservierungen frei und protokolliert
den beobachteten Zustand. Der Editor verlinkt den letzten Lauf zum Key/Locale;
dessen Ereignis erscheint ebenfalls in der Arbeitsgeschichte. Die ursprüngliche
Dateievidenz bleibt eine unveränderte Erfassung und wird nicht rückwirkend umgedeutet.

Der UI-Weg veröffentlicht zunächst einzelne skalare Revisionen ohne Namespace
in statische PHP-Sprachdateien. Namespace-Ziele ohne explizite Loader-Zuordnung,
mehrdeutige Locale-Verzeichnisse, ungültige Key-Strukturen und nicht statisch
lesbare Dateien werden abgelehnt. Der Quellcode wird noch nicht umgeschrieben.
Raw Data, TW-Graph, bisherige Pipeline-Kommandos und die Ablösung der alten
Tabellen bleiben separate offene Migrationspunkte.

Verifikation: 48 Pest-Tests / 235 Assertions, 48 isolierte Editor-/Publikationsprüfungen,
186 Persistenz-Assertions / 10 Race-Szenarien und 9 Restart-Prüfungen bestanden.
Darunter Dateiformaterhalt, leere Werte, Freigabedrift, externe Dateiänderungen,
Dateirechte, expliziter Abschluss und Fortsetzen nach bereits geschriebenem Wert.
Echtes Modal für Finding #1 / Key #768 lesend erfolgreich; Ereignisanzahl unverändert.
Keine echten Sprachdateien veröffentlicht und keine produktiven Freigaben oder
Veröffentlichungspläne zum Testen angelegt. Manuelle Browserprüfung bleibt offen.

### Exakte Quellcode-Vorschau im neuen Editor · 21.10.2026

Im Source-Callout steht bei aktiven, zugeordneten Findings jetzt **Inspect source
replacement**. Das eigene Flux-Modal zeigt den aktuellen und vorgeschlagenen
Aufruf, Finding-/Key-ID und Datei mit Byteposition. Es ist ausdrücklich lesend;
ein Schreib-/Bestätigungsbutton ist noch nicht angeschlossen.

`SourceReplacementPreview` prüft die konkrete gespeicherte Position und den
aktuellen Aufruf über die gemeinsame `CopiedSourceContext`-Erkennung. Es wird
nicht nach ähnlich lautenden Texten an anderer Stelle gesucht. Verschobene,
veränderte, auskommentierte, dynamische oder nicht unterstützte Aufrufe werden
abgelehnt. Nur das erste statische Stringargument unterstützter Übersetzungsaufrufe
wird für die Vorschau ersetzt; weitere Argumente, Platzhalter und umgebender Code
bleiben erhalten. Bereits passende Keys werden ohne Änderung der Anführungszeichen
als erledigte Quellcode-Umstellung angezeigt. Pfadtraversierung und Symlinks werden
abgelehnt. Die Key-Zuordnung und der Freigabekontext werden vor/nach der Vorschau
auf Änderungen geprüft.

Die Prüfung benutzt unabhängig von der gerade ausgewählten Bearbeitungssprache
die konfigurierte Ausgangssprache. Bei einer echten Ersetzung sind eine aktuelle
Freigabe und der identische skalare Wert in der zugehörigen Sprachdatei erforderlich.
Fehlende Freigabe oder Veröffentlichung erscheinen als Hindernis in der Vorschau;
leere Strings und `0` bleiben zulässige Werte. Datei-Hashes prüfen Änderungen
während der Vorschauerstellung.

Der alte `translations:rewrite-literals`-Befehl wurde nicht verwendet: Er arbeitet
mit dem alten Literal-zu-Key-Bestand statt der expliziten neuen Finding-Zuordnung.
Für den nächsten Schreibschritt fehlt noch die dauerhafte Verbindung von
Dateiplan, Dateischreiben und Nachfolger-Erfassung mit sicherem Wiederanlauf.
Die bisher isolierte `CorrectedSourceReconciler`-Implementierung dokumentiert
bereits die Archivierung des Vorgängers und die getrennte Nachfolger-Fundstelle;
sie allein ist aber kein transaktionaler Dateischreiber. Es wurden deshalb noch
keine echten Source-Dateien umgeschrieben oder Findings archiviert.

Verifikation: 30 Pest-Tests / 132 Assertions und 49 isolierte Datenbankprüfungen
bestanden. Echtes Finding #1 / Key #768 mit dem Testkonto lesend aufgerufen:
Vorschau geöffnet, zugeordneter Key bereits im Aufruf, keine Fehler und keine
neuen Arbeitsereignisse. Manuelle Browserprüfung bleibt offen.

### Quellcode schreiben und Nachfolger erfassen · 21.10.2026

Die Quellcode-Vorschau hat jetzt einen nächsten Schritt: **Prepare source
replacement** speichert mit Begründung einen unveränderlichen Plan im vorhandenen
Operationsjournal. Erst das separate Flux-Modal mit **Confirm source replacement /
resume** schreibt die Datei. Eine zusätzliche **Source replacement runs**-Liste
macht vorbereitete und abgeschlossene Vorgänge nach Navigation wieder auffindbar.
Es wurden dafür keine zusätzlichen Tabellen und keine neue Datenbankgeneration
angelegt.

`SourceReplacementWriter` benutzt die verifizierte Generationsverbindung. Der
Plan hält Originaldatei, geplante Datei, Sprachevidenz, Freigabekontext und aktuelle
Fundstellen/Zuordnungen fest. Pro Datei darf nur ein offener Plan existieren.
Schreiben setzt unveränderte Zuordnungen, Freigabe und veröffentlichte
Ausgangssprache voraus; das Dateiergebnis wird vor der Vorbereitung durch denselben
Scanner im Speicher erneut erfasst. Temporäre Datei und atomare Umbenennung
bewahren den bisherigen Dateimodus. Der vorbereitende Benutzer bestätigt den Lauf.

Der bisherige `CorrectedSourceReconciler` liegt nun einmalig in `src/Generation`.
Er kann im äußeren Schreibvorgang dieselbe Datenbanktransaktion benutzen: Das
Original-Finding wird archiviert, der Nachfolger erhält `predecessor_id`, die
aktive Key-Verknüpfung wird auf den Nachfolger übertragen, der unveränderliche
SuggestedKey wird beibehalten. Die neue Erfassung enthält den Key als aktuellen
Source-Inhalt und `raw_literal=NULL`. Originalerfassung, Key und andere Findings
werden nicht zusammengelegt. Erfolgreiche Bestätigung öffnet im Editor den Nachfolger.

Ein Dateisystemschreibvorgang ist nicht Bestandteil der PostgreSQL-Transaktion.
Der dauerhafte Plan ist deshalb bereits vor dem Schreiben vorhanden. Nach einem
Abbruch mit bereits geschriebenem Ergebnis und zurückgerollter Datenbank erkennt
der Wiederanlauf den exakten Dateihash/-inhalt: Er schreibt nicht erneut, sondern
vervollständigt die Nachfolger-Erfassung und den Beleg. In diesem Zustand gelten
die gespeicherte Schreibfreigabe und der damalige Sprachbeleg für die bereits
vollzogene Änderung; eine spätere Review-Änderung verhindert nicht die reine
Nachverbuchung. Die ursprünglichen Findings/Zuordnungen müssen jedoch weiterhin
zum Plan passen. Ein erfolgreicher Beleg wird bei Wiederholung zurückgegeben,
ohne weitere Findings oder Ereignisse anzulegen.

Schließen ohne Schreiben verlangt einen Grund und die unveränderte Originaldatei.
Eine bereits geschriebene Datei kann nicht durch bloßes Schließen ohne Nachfolger
zurückgelassen werden. Externe Änderungen oder zwischenzeitlich abweichende
Findings blockieren die automatische Fortsetzung und erfordern eine gesonderte
Klärung. Das Modal zeigt dafür `pending/complete/closed` und
`original/written/changed` getrennt an.

Bewusste erste Grenze: Mehrzeilige Aufrufe und weitere gespeicherte Findings hinter
dem ausgewählten Aufruf auf derselben Zeile werden vor der Plananlage abgewiesen.
Deren Positionsverschiebungen müssen gemeinsam erfasst werden und werden nicht
stillschweigend ignoriert. Der alte pauschale Rewrite-Befehl bleibt unbenutzt.
Die Migration der bisherigen Commands/Raw-Data-/TW-Graph-Leser und die spätere
Entfernung alter Tabellen ist damit noch nicht abgeschlossen.

Verifikation: 32 Pest-Tests / 142 Assertions und 28 isolierte PostgreSQL-Prüfungen
bestanden. Enthalten sind Abbruch direkt nach atomarem Dateischreiben,
Nachverbuchung mit frischer Verbindung, genau ein Nachfolger/Arbeitsereignis,
SuggestedKey-/Dateimodus-Erhalt, falscher Benutzer, paralleler Plan für dieselbe
Datei, externe Source-Änderung, Review-/Sprachdatei-/Zuordnungsdrift und Ablehnung
verschiebungsrelevanter Fundstellen. Echtes Finding #1 / Key #768 nur lesend
geöffnet: keine Fehler, kein neues Arbeitsereignis. Keine echten Source-Dateien
über den Writer geändert und keine echten Pläne/Freigaben zum Testen angelegt.

### Mehrzeilige Quellcode-Aufrufe und gemeinsame Positionskorrektur · 21.10.2026

Der SourceReplacementWriter berücksichtigt jetzt den exakten Parser-Bereich des ersetzten Stringarguments. Der geplante Dateiinhalt wird einmal mit dem regulären Scanner erfasst. Alle gespeicherten aktiven Fundstellen der Datei werden an ihren bisherigen Positionen geprüft; verschobene Fundstellen werden gemeinsam mit dem umgestellten Aufruf als Nachfolger erfasst. Ihre jeweils eigene Key-Verknüpfung (auch ohne Key), SuggestedKey und ursprünglichen Literalwerte bleiben erhalten. Unveränderte Positionen behalten ihre Finding-ID. Das gilt auch, wenn ein mehrzeiliges Literal durch einen einzeiligen Key ersetzt wird und dadurch nachfolgende Zeilennummern wechseln.

Der Bestätigungsdialog zeigt zusätzlich die verschobenen Finding-IDs mit Zielzeile und Spalte. Alle Vorgänger werden vor dem Einfügen der Nachfolger innerhalb derselben Transaktion stillgelegt. Das bestehende dauerhafte Schreibjournal erlaubt weiterhin die Wiederaufnahme nach einem Abbruch zwischen Dateischreiben und Datenbankabschluss. Bereits vorbereitete ältere Einzelpläne bleiben lesbar.

Überlappende weitere gespeicherte Aufrufe, deren Inhalt durch dieselbe Argumentänderung ebenfalls verändert würde, werden vor dem Schreiben ausdrücklich abgewiesen. Keine heuristische Zuordnung oder automatische Key-Zusammenfassung.

Validierung: 41 Prüfungen mit temporärer PostgreSQL-Datenbank und temporären Quelldateien; darunter unabhängige Keys, unveränderte Fundstellen, mehrzeilige Literale, gemeinsame Wiederaufnahme und idempotente Wiederholung. 32 Pest-Tests mit 142 Assertions bestanden. Keine produktiven Quelldateien durch den Writer umgestellt und keine Arbeitsdaten verändert.

### Betroffene Findings vor dem Schreiben prüfen · 21.10.2026

Das Bestätigungsmodal zeigt jetzt eine Flux-Tabelle aller betroffenen Findings: ID, Änderungsart (Argument ersetzt oder Position verändert), bisherige/neue Zeile und Byte-Spalte sowie die jeweils eigene Key-ID und den Key-Namen. Nicht verknüpfte Findings werden ausdrücklich als nicht zugeordnet dargestellt. Die Übersicht wird aus dem dauerhaft vorbereiteten Plan abgeleitet und bleibt nach Wiederöffnung nachvollziehbar; unveränderte Findings sind nicht Teil dieser Liste. Die Tabelle ist höhenbegrenzt und scrollbar.

Verschachtelte Aufrufe mit überlappender gespeicherter Erfassung bleiben ausdrücklich gesperrt. Ein neuer Integrationstest prüft den inneren Rewrite innerhalb eines ebenfalls gespeicherten äußeren Übersetzungsaufrufs: Ablehnung vor der Plananlage, unveränderte Quelldatei, beide Findings weiterhin aktiv und keine zusätzliche Operation. Dies ist eine weiterhin offene funktionale Grenze, keine automatische Erfassung des äußeren Nachfolgers.

Validierung: 45 isolierte PostgreSQL-Prüfungen und 14 Livewire/Pest-Tests mit 88 Assertions bestanden, einschließlich Darstellung eines unabhängig verknüpften verschobenen Findings im Bestätigungsdialog. Keine Änderungen an produktiven Arbeitsdaten oder Quelldateien durch den Source-Writer.

### Schreibbeleg in der History aller betroffenen Findings · 21.10.2026

Die History berücksichtigt zusätzlich zur direkten Finding-/Key-Zuordnung die im Ereignis gespeicherten Vorgänger- und Nachfolger-IDs. Damit erscheint ein gemeinsamer Quellcode-Schreibbeleg auch bei verschobenen Findings ohne Key sowie bei archivierten Vorgängern ohne aktive Key-Verknüpfung. Unbeteiligte Findings bekommen diesen Beleg nicht allein deshalb, weil sie in derselben Datei stehen.

In der Workbench werden die Quellcode-Ereignisse verständlich bezeichnet und die Vorgänger-/Nachfolger-Paare als direkt aufrufbare Finding-Buttons angezeigt. Bestehende Ereignisse werden nur gelesen, nicht umgeschrieben. Die Fehlermeldung bei einer unbekannten Finding-ID lautet sachlich „Finding not found.“.

Validierung: 52 isolierte PostgreSQL-Prüfungen und 15 Livewire/Pest-Tests mit 91 Assertions bestanden. Die Tests prüfen insbesondere die Zuordnung zu beiden Seiten aller drei Übergänge eines gemeinsamen Schreibvorgangs, den Ausschluss eines unbeteiligten Findings und die Finding-Links in der UI. Keine produktiven Datenänderungen.

### Scanner nach bestätigter Quellcode-Umstellung · 21.10.2026

Der Source-Replacement-Integrationstest führt jetzt anschließend den regulären CompleteRescanWriter mit dem vollständigen Scanner aus. Zwei unveränderte Läufe bestätigen die drei bereits erfassten Nachfolger, ohne weitere Findings, Key-Verknüpfungen oder Arbeitsereignisse zu erzeugen. Die Scanläufe selbst werden weiterhin protokolliert.

Eine danach eingefügte Leerzeile führt zu drei nachvollziehbaren Nachfolgern der bereits umgestellten beziehungsweise verschobenen Findings. SuggestedKey, Quellinhalt und jeweils eigene Key-Zuordnung bleiben erhalten, auch ohne Key. Ein weiterer unveränderter Scan bestätigt diese Nachfolger erneut ohne zusätzliche Findings. Der Test benutzt einen ausdrücklich auf die temporäre Quelldatei begrenzten Scanumfang und bereitgestellte Sprachevidenz.

Ergebnis: 66 isolierte PostgreSQL-Prüfungen bestanden. Für diesen Ablauf war keine Änderung am produktiven Scanner oder Writer nötig. Geändert wurde der bestehende Integrationstest einschließlich des für vollständige Scannerläufe benötigten Test-Bootstraps. Keine produktiven Datenbank- oder Quellcodeänderungen. Dies bestätigt den geprüften Anschluss zwischen Writer und Scanner, nicht die vollständige Ablösung sämtlicher bisheriger Commands und Leser.

### Scanneränderungen in der Finding-History · 21.10.2026

Die Finding-History liest nun zusätzlich vorhandene complete_rescan-Belege aus dem Operationsjournal. Angezeigt werden ausschließlich explizit betroffene IDs aus created, removed und moved. Bei Verschiebungen erscheint der Beleg auf beiden Seiten mit aufrufbarem Vorgänger-/Nachfolger-Paar. Neue Fundstellen werden als erfasst, nicht mehr vorhandene als archiviert beschrieben. Als Auslöser erscheint „Scanner“; ein Benutzer wird nicht nachträglich erfunden.

Unveränderte Bestätigungen erzeugen keine History-Zeilen. Die gemeinsame Anzeige von Arbeitsereignissen und Scanbelegen ist auf die jüngsten 50 Einträge begrenzt. Es wurden keine Tabellen ergänzt und keine bestehenden Belege umgeschrieben.

Validierung: 75 isolierte PostgreSQL-Prüfungen sowie 16 Livewire/Pest-Tests mit 94 Assertions bestanden. Enthalten sind wiederholte Scans, beide Seiten von Verschiebungen, Neuerfassung, Archivierung, Ausschluss unbeteiligter Findings und die Scanner-Beschriftung in der UI. Produktive Daten unverändert.

### Gespeicherte Scan-Ergebnisse in der Workbench · 21.10.2026

Das Callout „Scan results“ zeigt die letzten 20 gespeicherten vollständigen Scanläufe mit Zeitpunkt und Anzahl bestätigter, erfasster, verschobener, archivierter Findings sowie zurückgestellter Dateien. Die Zahlen beschreiben ausdrücklich den damaligen Scanbefund.

„Open“ öffnet ein Flux-Modal mit Kategorienauswahl und Flux-Pagination (25 Einträge pro Seite). Erfasste und archivierte Findings sind direkt aufrufbar; Verschiebungen bieten Vorgänger und Nachfolger an. Zurückgestellte Dateien werden als Dateipfade ohne erfundene Finding-ID angezeigt. Die Anzeige entscheidet oder genehmigt diese Fälle nicht; deren Bearbeitung bleibt ein eigener Schritt. Unveränderte Bestätigungen werden als Anzahl angezeigt.

Die Listen lesen das bestehende Operationsjournal. Nur die gerade gewählte Ergebnisseite wird aufgelöst; große Ergebnisarrays werden nicht vollständig an Livewire übertragen. Keine neuen Tabellen, keine produktiven Scanläufe und keine Datenänderungen.

Verifikation: 83 isolierte PostgreSQL-Prüfungen und 17 Livewire/Pest-Tests mit 97 Assertions bestanden. Geprüft wurden Kategorien, historische Finding-Verweise, Review-Dateien, ungültige Lauf-/Kategorieangaben, Seitenbegrenzung und Kategorie-Wechsel in der UI.

### Prüfgründe zurückgestellter Scan-Dateien · 21.10.2026

„Scan results → Files requiring review“ zeigt nun die vorhandenen issues aus dem gespeicherten Scan-Snapshot: verständliche Begründung, technischen Reason-Code, erfasste Zeile/Byte-Spalte und Anzahl unverändert beibehaltener Findings. Fehlende Sprachevidenz, dynamische Argumente, mehrdeutige Verschiebungen, mögliche Dateiumzüge, unvollständige Ausdrücke und Kontextentscheidungen werden unterschieden. Nicht vorhandene Positions- oder Detailangaben werden nicht erfunden. Für ältere Belege ohne nähere Gründe gibt es einen ausdrücklichen Hinweis.

Die Abfrage lädt ausschließlich Prüfgründe, Zustand und Anzahl der beibehaltenen Findings für die aktuell paginierten Dateien, keine vollständigen Source-Captures. Lange Begründungslisten sind höhenbegrenzt und scrollbar. Dies ergänzt die Befunddarstellung, ist aber noch keine Entscheidung oder automatische Korrektur der zurückgestellten Dateien.

Validierung: 85 isolierte PostgreSQL-Prüfungen sowie 18 Livewire/Pest-Tests mit 101 Assertions bestanden. Geprüft wurden der echte Scanbeleg bei fehlender Sprachevidenz, die genaue Quellposition, fehlende künstliche Finding-Verknüpfungen und die sichtbare Erklärung. Produktive Daten unverändert.

### Beibehaltene Findings direkt aus dem Scanbefund öffnen · 21.10.2026

Zurückgestellte Dateien mit bestehenden Findings bieten „Open preserved findings“. Die Detailansicht im selben Flux-Modal verwendet die im Scan gespeicherten preserved-IDs, nicht eine unscharfe Suche über Dateinamen. Sie zeigt Finding-ID, erfasste Quellposition und Source-Wert sowie aktuellen Status und aktuelle Key-Zuordnung. Die IDs öffnen den vorhandenen Finding-Editor. Der Rückweg bewahrt Ergebniskategorie und Ergebnispagination.

Die Detailansicht verwendet eine eigene Flux-Pagination mit 25 Einträgen. Lauf-ID und Zugehörigkeit der Datei zum ausgewählten Review-Befund werden serverseitig geprüft. Eine Datei ohne vorherige Findings erhält keine künstlichen Finding-Verweise. Der historische Scanbestand und der aktuelle Bearbeitungsstand werden in der Erklärung ausdrücklich unterschieden.

Validierung: 90 isolierte PostgreSQL-Prüfungen und 19 Livewire/Pest-Tests mit 106 Assertions bestanden, einschließlich tatsächlicher Zurückstellung eines bestehenden Dateibestands wegen eines dynamischen Aufrufs, Key-Zuordnung, unzulässiger Dateiauswahl, leerer Bestände und Rücknavigation. Keine produktiven Datenänderungen.

### Konkrete Quellaufrufe zurückgestellter Dateien · 21.10.2026

„Inspect captured source“ zeigt die im ausgewählten Scan gespeicherten einzelnen Aufrufe mit Position, unverändertem Ausdruck, tatsächlichem Literal, gefundenem TranslationKey und Capture-Reason. Die Datei-Prüfsumme ist als Evidenz aufklappbar. Die Ansicht verwendet Flux-Pagination mit 25 Aufrufen und lädt keine vollständigen Capture-Arrays in den Livewire-Zustand. Fehlende Captures, etwa bei einem früheren mehrdeutigen Bewegungsbefund, werden ausdrücklich als nicht gespeichert ausgewiesen.

Die Ansicht unterscheidet erfasste Aufrufe von bereits gespeicherten Finding-IDs. Sie liest ausschließlich den historischen Scanbeleg, auch wenn sich die Quelldatei inzwischen geändert hat. Zugehörigkeit zum ausgewählten Review-Befund wird serverseitig geprüft; Quelltext wird escaped dargestellt.

Offene Anbindung: SourceCaseReview/GenerationCaseDecisions beziehen ihre Fälle weiterhin aus dem früheren Erfassungs-Snapshot und capture_outcomes. Neue Scan-Captures werden nicht anhand textueller Ähnlichkeit damit verbunden. Eine explizite Entscheidung für diese neuen Aufrufe und ihre Berücksichtigung im folgenden Scannerlauf sind noch zu implementieren; diese Ansicht behauptet keine solche Entscheidung.

Validierung: 95 isolierte PostgreSQL-Prüfungen und 20 Livewire/Pest-Tests mit 112 Assertions bestanden. Enthalten sind historische Stabilität nach Dateiänderung, Literal-/Key-Trennung, unzulässige Pfade/Lauf-IDs und HTML-Escaping der Quelltexte. Keine produktiven Datenänderungen.

### Entscheidungen für dynamische Aufrufe aus gespeicherten Scans · 21.10.2026

„Inspect captured source → Classify source usage“ ermöglicht nun für bestätigte dynamische Übersetzungsaufrufe eine explizite Entscheidung DatabaseValue/Translate mit Benutzer, Zeitpunkt und Begründung. Die Entscheidung wird im bestehenden Operations-/Ereignisjournal gespeichert; zusätzliche Tabellen oder eine Verknüpfung zum früheren Capture-Manifest sind nicht nötig. Frühere Entscheidungen bleiben unverändert und werden über previous_event_id verbunden.

Der Geltungsbereich umfasst exakt Pfad, Datei-SHA-256, Byte-Offset, Ausdruck und bestätigten Capture-Kontext. Vor und nach Speicherung wird der Quellbestand geprüft. Eine zwischenzeitliche Entscheidung wird über die erwartete Event-ID erkannt. Statische Keys ohne Sprachevidenz, unbestätigte Aufrufe und ungültige Scan-/Capture-Auswahl können nicht über diese Funktion als DatabaseValue deklariert werden.

CompleteRescanWriter liest die gespeicherten Entscheidungen. DatabaseValue entfernt nur den unveränderten dynamischen Aufruf aus der Übersetzungserfassung; Translate hält ihn als translation_work_required offen. Bestehende Findings werden dadurch nicht automatisch stillgelegt: Ein Positions-/Ausdruckskonflikt bleibt zur Prüfung zurückgestellt. Die Scanplanung berücksichtigt Änderungen der Entscheidungen in ihrem Datenbank-Fingerprint; die Anwendung sperrt konkurrierende Entscheidungsänderungen innerhalb der Transaktion. Der Scanbeleg enthält die berücksichtigten Entscheidungen und zeigt sie in „Usage decisions considered“ mit Event-ID.

Änderungen am Dateiinhalt oder Ausdruck verhindern die automatische Übernahme einer früheren Entscheidung. Wiederholte Scans bei unverändertem Source erzeugen keine zusätzlichen Entscheidungsereignisse. Der frühere SourceCaseReview bleibt für seinen Erfassungsbestand bestehen; die neue Funktion ist ausdrücklich für die gespeicherten dynamischen Scan-Captures zuständig. Andere Review-Typen bleiben offen und werden nicht als erledigt ausgegeben.

Validierung: 111 isolierte PostgreSQL-Prüfungen sowie 24 Livewire/Pest-Tests mit 135 Assertions bestanden. Geprüft wurden Entscheidungsspeicherung, UI-Bestätigung, Scannerwirkung beider Optionen, Änderungsverlauf, wiederholte Scans, veraltete Scanpläne, konkurrierende Entscheidungen, geänderter Quelltext, ungültige Capture-Auswahl und Schutz bestehender Findings. Keine produktiven Entscheidungen, Scannerläufe oder Quellcodeänderungen zum Testen ausgeführt.


### Begründungsvorschläge für Scan-Nutzungsentscheidungen · 21.10.2026

- Neben dem Freitext steht ein durchsuchbares Flux-Select mit Vorgaben für DatabaseValue beziehungsweise Translate. Eine Auswahl übernimmt den weiterhin editierbaren Text. Ein Wechsel der Klassifizierung überschreibt den Freitext nicht.
- Erfolgreich gespeicherte Begründungen aus `scan_source_usage_decided` ergänzen die Vorschläge für dieselbe Klassifizierung, dedupliziert und begrenzt auf die 100 zuletzt verwendeten Texte. Abgelehnte Speicherungen erzeugen keine Vorschläge. Keine zusätzliche Tabelle oder Änderung am Produktivbestand.
- Prüfung: GenerationWorkbench-Pest 23 Tests / 131 Assertions; isolierter Source-Replacement-Integrationstest 114 Checks bestanden, ohne Produktionsverbindung.


### Nutzungsentscheidungen direkt im Scanbefund · 21.10.2026

Die Liste der erfassten Quellaufrufe zeigt bei bestätigten dynamischen Aufrufen nun die jüngste gespeicherte Nutzungsentscheidung mit Begründung, Benutzer und Zeitpunkt. Ohne Entscheidung erscheint ein offener Status. Translate wird ausdrücklich als weiterhin offene Arbeit angezeigt; eine gespeicherte DatabaseValue-Entscheidung behauptet keine nachträgliche Änderung des historischen Scanergebnisses. Der nächste Scan prüft ihre Anwendbarkeit.

Die Zuordnung erfolgt ausschließlich über die vollständige Capture-Identität. Das Journal wird auf die Identitäten der sichtbaren Seite beschränkt abgefragt. Nicht bestätigte dynamische Aufrufe erhalten keinen Klassifizierungsbutton.

Validierung: 24 Livewire/Pest-Tests mit 137 Assertions und 117 isolierte PostgreSQL-Prüfungen bestanden. Keine produktiven Datenänderungen.


### Vom Scanbeleg zur Nutzungsentscheidung zurückkehren · 21.10.2026

Usage decisions considered bietet jetzt „Review source and current decision“. Der Verweis wird serverseitig gegen den gewählten Scanbeleg geprüft und über das darin referenzierte Entscheidungsereignis auf die ursprüngliche Quellerfassung aufgelöst. Die Ansicht öffnet deren passende Ergebnisseite und die jüngste Entscheidung. Die im Ausgangsscan berücksichtigte Ereignis-ID bleibt historisch unverändert. Ein Hinweis erklärt die Unterscheidung; Back to scan results kehrt zum Ausgangsscan zurück.

Dies funktioniert auch dann, wenn die Datei nach DatabaseValue im Folgescan nicht mehr zur Prüfung zurückgestellt wurde. Geänderter Source wird beim Speichern weiterhin durch die bestehende Prüfsummenprüfung abgewiesen. Keine produktiven Datenänderungen.

Validierung: 26 Livewire/Pest-Tests mit 152 Assertions, 120 isolierte PostgreSQL-Prüfungen und scoped git diff --check bestanden. Geprüft sind Navigation, Ergebnisseite, Rückkehr, Ablehnung scanfremder Ereignisse sowie historische Entscheidung gegenüber später geänderter Entscheidung.


### Anschluss vom Entscheiden zum erneuten Scan · 21.10.2026

Das Scan-Callout erklärt den vorhandenen Command für Preview und Scan mit Speicherung (--apply), einschließlich konfiguriertem Umfang und Schreibwirkung. Ein Refresh-Button liest die gespeicherten Ergebnisse erneut. Eine zusätzliche Anzahl berücksichtigter Nutzungsentscheidungen öffnet unmittelbar die entsprechende Ergebniskategorie. Der Command zeigt diese Anzahl ebenfalls und verweist nach Speicherung auf die aktuelle Scanübersicht statt auf einen nicht ersetzten alten UI-Snapshot.

Kein zweiter Scannerpfad und kein produktiver Scan wurden eingeführt beziehungsweise ausgeführt. Ein UI-Start des Scanners ist damit nicht implementiert; ausgeführt wird weiterhin der bestehende Command im Terminal.

Validierung: 28 Livewire/Pest-Tests mit 164 Assertions, 121 isolierte PostgreSQL-Prüfungen, PHP-Syntaxprüfung des Commands und scoped git diff --check bestanden.


### Verzeichnisbereinigung · 21.10.2026

102 unbenötigte Dateien (722.149 Bytes) gelöscht, nicht in einen weiteren Archivordner verschoben:

- `resources/old`: 46 alte Protocol-Views.
- `resources/dev/tw-graph-legacy`: 25 alte Entwicklungs-Komponenten.
- `resources/views/livewire/old`: neun Views der stillgelegten Workbench; dazu `TranslationWorkbenchOldEntries.php` und deren auskommentierte Registrierung/Route entfernt.
- Archivierte IF-Kette in `strang/_old` samt eigenem Archivtest und überholtem Review-Dokument entfernt. Inventartest prüft nun, dass die entfernte Kette nicht mehr erscheint.
- Fünf archivierte Console-/Foundation-Implementierungen in `src/_old`, HistoricalValueReview samt zwei archivierten Views und der deaktivierte LegacyScanReconciliation-Test entfernt. Die Workflow-Grafiken bleiben bestehen; ehemalige Dateiverweise sind als entfernte historische Implementierung gekennzeichnet.
- Zwei Python-Bytecode-Dateien entfernt; `.gitignore` verhindert erneutes Einchecken.

Im BaselineReviewCasesTest nur die beiden Tests der entfernten Archiv-UI gelöscht; die neun Prüfungen der weiterhin vorhandenen Datenvalidierung bleiben bestehen. Audit-Belege und Hilfsdateien unter docs/audits sind nicht pauschal gelöscht: Vorschau-Leser und Tests verwenden daraus weiterhin Eingaben. Hier ist vor weiterem Löschen eine Entkopplung ihrer Datenquellen erforderlich. Produktive Daten, Sprachdateien und aktuelle Graph-Komponenten unverändert.

Validierung: 90 Pest-Tests mit 1.560 Assertions bestanden (Workbench, Workflow, Baseline-Datenvalidierung, TW-Graph-Inventar und Dokumentations-Lazy-Loading). Keine aktiven Referenzen auf die gelöschten Implementierungen in app/routes/tests/Package gefunden; scoped git diff --check bestanden.
