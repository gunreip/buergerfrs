# Key-Nachweise und Shared Candidates bei Folgescans – 21.10.2026

## Umsetzung im isolierten Prüfwriter

Neue Fundstellen erhalten nur dann einen Link zu einem **bestehenden** Key,
wenn folgende Nachweise zusammenpassen:

- bestätigter Key-Aufruf, vollständiger Key mit mindestens drei Segmenten;
- exakter aktiver Key-Name im Datenbestand;
- belegter veröffentlichter Source-Locale-Wert (`en`) für genau diesen Key;
- identischer Wert, Locale-Dateipfad und Dateihash zwischen Capture und gespeicherter Evidenz;
- unveränderte Locale-Datei in der gescannten Quellkopie.

Es werden keine Keys erzeugt, umbenannt oder anhand gleichen Übersetzungstextes
erraten. Unzureichende Nachweise erhalten einen Prüfzustand ohne Link.
`ui.type` wird nicht automatisch repariert. Mehrere Fundstellen desselben
vorhandenen Keys bleiben unterschiedliche Findings mit jeweils eigenem Link.

Der Writer berücksichtigt Key-Namen und veröffentlichte Evidenz im vorbereiteten
Datenbank-Fingerprint und sperrt die betreffenden Tabellen bei der Anwendung.
Neu verwendete Nachweise werden vor dem Commit erneut geprüft. Eine Änderung
führt zum Rollback. Diese Prüfung ersetzt keine gemeinsame Dateisystem-/DB-Transaktion.

## Shared-Candidate-Regel

`RescanCandidateRules::sharedCandidates()` leitet die Gruppen aus dem aktiven
Datenbestand ab:

- identisches `raw_literal`, ohne Trimmen oder Normalisieren;
- mindestens zwei aktive Findings;
- mindestens zwei unterschiedliche, vorhandene SuggestedKeys.

Damit entfällt die Markierung bei weniger als zwei aktiven Findings bzw. weniger
als zwei unterschiedlichen SuggestedKeys. Stillgelegte Findings, reine Key-Aufrufe
mit `raw_literal = NULL` und nur gleichlautende Übersetzungswerte zählen nicht dazu.

Eine Gruppe enthält die einzelnen Finding-IDs und SuggestedKeys. Es erfolgt
keine Zusammenlegung und keine Key-Zuordnung aufgrund dieser Gruppe.
Die aktuelle Markierung wird abgeleitet, nicht als möglicherweise veraltetes
Boolean an immutable Findings geschrieben. Jeder gespeicherte Scannerauftrag
hält seinen damaligen Gruppenstand im Operationsergebnis fest; ältere Ergebnisse
bleiben historische Momentaufnahmen und dürfen nicht als aktueller Status gelten.

Bei unvollständig geklärten Dateien beruht die Gruppe auf den weiterhin aktiven,
vorläufig erhaltenen Findings. Sie behauptet nicht, dass diese im letzten Scan
vollständig bestätigt wurden; der Prüfstatus der Datei bleibt zusätzlich relevant.

## Prüfserie

24 Assertions bestanden:

- Neue und zusätzliche Fundstellen eines vorhandenen Keys korrekt und getrennt verknüpft.
- Unveränderter Folgescan ohne neue Findings.
- Falscher Hash, unzulässig kurzer Key und nicht vorhandener Key ohne Zuordnung.
- Shared Candidates bei 1 → 2 → 3 → 2 → 1 aktiven Literal-Fundstellen.
- Zwei Fundstellen mit demselben SuggestedKey erzeugen allein keine Shared-Gruppe.
- Archivierte Findings werden aus der aktuellen Gruppe ausgeschlossen; Wiederauftauchen erstellt ein neues Finding.
- Unterschiedliche Groß-/Kleinschreibung bleibt getrennt.
- Literal mit identischem Text eines veröffentlichten Key-Werts bleibt unverknüpft.
- Änderung der Locale-Datei während der Anwendung führt zum vollständigen Rollback.
- Wiederanlauf mit wiederhergestelltem Nachweis sowie Replay ohne zusätzliche Datenbankzeilen.
- Bestehende Keys, Namen, Werte und ursprüngliche Findings unverändert.

Zusätzlich wurde die bestehende Parallel-/SIGKILL-Serie mit dem geänderten Writer
wiederholt: weitere 30 Assertions bestanden. Insgesamt 54 Prüfungen erfolgreich.
Die isolierten Prüfcluster wurden gestoppt. PHP-Syntaxprüfung, Pint und
`git diff --check` bestanden.

## Artefakte und Anschluss

- Regeln: `tests/Support/TranslationWorkbench/RescanCandidateRules.php`.
- Integration: `tests/Support/TranslationWorkbench/CompleteRescanWriter.php`.
- Runner: `rehearse-rescan-candidate-rules.php` in diesem Verzeichnis.
- Regelbericht: `storage/app/private/dev/workbench-rehearsal/current-generation-20261020-192844-f86176d77474/rescan_a4d77741d185/result.json`.
- Wiederholte Konkurrenzprüfung: `storage/app/private/dev/workbench-rehearsal/current-generation-20261020-192844-f86176d77474/rescan_64e451ca18e2/result.json`.

Diese Änderung gilt weiterhin ausschließlich für den isolierten Prüfwriter.
Die vorherigen Berichte mit „neue Key-Fundstellen bleiben unverknüpft“ beschreiben
den damaligen Stand; für eindeutig belegte vorhandene Keys ist diese Einschränkung
hiermit im Prüfwriter aufgehoben. Unbekannte/neue Keys und ungeklärte Nachweise
bleiben zur Entscheidung offen.

Die regulären Commands und die Workbench sind nicht umgeschaltet. Die Sichtbarkeit
und Bearbeitung der gespeicherten Prüfzustände/Shared Candidates sowie der Anschluss
des geprüften Ablaufs an einen regulären Scanner-Einstieg bleiben ausstehend.
