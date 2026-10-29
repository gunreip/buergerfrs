# Bestätigten Korrekturvorschlag auf der Kopie anwenden

Der nächste Persistenzschritt ist ausschließlich unter `tests/Support` umgesetzt
und auf einer frisch restaurierten isolierten Baseline erprobt. Eine produktive
Migration oder UI-Schreibaktion wurde nicht aktiviert.

## Verhalten

Die Anwendung verlangt die gespeicherte Vorschlags-ID, ihren erwarteten
Kontextfingerabdruck sowie ausdrücklich Bearbeiter und Begründung. Unter einer
Transaktion werden Ausgangszustand, aktive/unverknüpfte Findings und freie
Ziel-/Array-Pfade erneut durch dieselbe Kontextprüfung wie beim Vorschlag geprüft.
Die Probe verwendet grobe Tabellenlocks; das ist keine bereits optimierte
Produktivstrategie.

Bei Erfolg entstehen ein neuer regulärer Key mit Zielnamen, je eine getrennte
Verknüpfung pro betroffenem Finding, ein unveränderlicher Anwendungsnachweis und
ein nachvollziehbares Ereignis mit Bearbeiter, Grund, Ausgangs-/Zielnamen und
betroffenen IDs. Der allgemeine Operationsnachweis liefert bei identischem
Retry dasselbe Ergebnis. Eine veränderte Wiederholung oder zweite Anwendung
desselben Vorschlags wird abgewiesen. Alle Schreibschritte sind atomar.

Die Findings selbst werden weder zusammengelegt noch umgeschrieben. Insbesondere
bleiben rawSourceCodeLiteral, rawLiteral, suggestedLiteral, suggestedKey,
Quellpositionen und vorhandene Capture-/Importnachweise unverändert.

## Abgrenzung zu Werten und Veröffentlichung

Die ursprünglichen Sprachwerte bleiben unter ihrem gesicherten Namen in der
unveränderlichen Dateiwert-Baseline erhalten. Diese DB-Anwendung erzeugt noch
keine Value-Revisionen, Freigaben oder Sprachdateien unter dem Zielnamen.
Die Herkunft ist über Anwendung → Vorschlag → Baseline/Ausgangsname dokumentiert.
Der bisherige Baseline-Export wird nicht durch den synthetischen Test verändert.

Eine folgende explizite Wertübernahme muss die ursprünglichen Werte unter dem
neuen Key als Arbeitsgrundlage zugänglich machen, ohne sie als neu verfasste oder
freigegebene Benutzerübersetzungen auszugeben. Erst danach können Review und
Veröffentlichung beziehungsweise Quellcode-Ersetzung folgen.

## Nachweis

Ausgangspunkt der Probe ist der geprüfte Baseline-Dump. Ein ausdrücklich
synthetischer Vorschlag ersetzt für den DB-Test `ui.type` durch
`rehearsal.correction.type` (Test-Bearbeiter 999999). Dies ist keine beschlossene
Produktivkorrektur und keine Empfehlung für einen tatsächlichen Namen.

Geprüft wurden die Vorschlagsspeicherung, Bestätigung mit gültigem Kontext,
fehlende Bestätigungsdaten, falscher Fingerabdruck, vollständiger Rollback nach
einer absichtlich unterbrochenen ersten Verknüpfung, spätere Ziel-Key-Belegung,
identischer/geänderter Retry, doppelte Anwendung, getrennte Finding-IDs und die
Unveränderlichkeit der Herkunftsnachweise.

Ergebnis: ein neuer Key, 387 einzelne Verknüpfungen, ein Ereignis und eine
Operation. Die zwei ursprünglichen Sprachwerte bleiben unangetastet. 22 der
vorher vorhandenen 27 Tabellen bleiben vollständig unverändert; Änderungen
betreffen ausschließlich Keys, Namen, Verknüpfungen, Operationen und Ereignisse.
Die beiden neuen Tabellen enthalten Vorschlag und Anwendung. Keine
Quell-/Sprachdateien oder produktiven Daten geändert; Testcluster gestoppt.

Die Probe testet Transaktionen, späte Konflikte und Retry, aber noch keinen
parallelen Zwei-Prozess-Lauf. Eine echte Anwendung benötigt zusätzlich aktuelle
Quell-/Sprachdateiprüfung, Erkennung neuer Fundstellen und Benutzerautorisierung.

## Gesicherte Werte als Arbeitsgrundlage lesen

`CorrectedKeyValueReader` ergänzt den gemeinsamen Baseline-Leseweg um
`working_basis` je ausdrücklich angeforderter Locale. Die Zuordnung erfolgt
nur über den angewendeten Vorschlag und die aktuelle Key-ID, nicht durch
Textgleichheit oder Namensheuristiken. Jeder Wert enthält ursprünglichen
Key-Namen, Datei, Prüfsumme, Baseline sowie Vorschlags-/Anwendungs-ID.

`published` und `effective_published` bleiben davon getrennt: Der alte Dateiwert
ist nicht plötzlich unter dem neuen Key veröffentlicht. Ebenso entstehen beim
Lesen keine Entwürfe, Revisionen, Reviews oder Freigaben. Fehlende regionale
Werte bleiben NULL; die Arbeitsgrundlage wird nicht zwischen Locales kopiert.
Ein beliebiger unbekannter oder anderer bestehender Key erhält keine solche
Zuordnung.

Beide Lesewege verwenden eine gemeinsame Implementierung innerhalb einer
konsistenten read-only Transaktion. Der zusätzliche Reader wird nur aufgerufen,
wenn das Korrekturschema vorhanden ist; der bisherige Baseline-Reader verlangt
keine Korrekturtabellen und bleibt für unveränderte Baselines verwendbar.

Der erweiterte Anwendungs-/Leseprobelauf bestand mit 53 Assertions, einschließlich
vollständigem Vergleich der vorhandenen Tabellen vor/nach dem Lesen. Die Werte
sind damit als Arbeitsmaterial verfügbar, aber noch nicht in eine bearbeitbare
Revision übernommen. Dieser nächste Schreibschritt muss explizit erfolgen und
die Herkunft zur Baseline erhalten. Keine produktive/UI-Anbindung ergänzt.

Der bestehende vollständige Baseline-Reader-Probelauf wurde nach dem Refactoring
erneut erfolgreich ausgeführt: 2.318 Assertions einschließlich Fallback,
Entwürfen, Review/Revocation, leeren Strings und `0`. Der private Nachweis liegt
unter `storage/app/private/dev/workbench-rehearsal/20261019-075456-18dfd037/readmodel_47036ea7aa91/result.json`.

## Arbeitsgrundlage ausdrücklich als Entwurf übernehmen

`CorrectedValueDraftImporter` übernimmt einen exakt vorhandenen Locale-Wert nach
Bestätigung des Vorschau-Fingerabdrucks. Bearbeiter und Begründung sind Pflicht.
Ein fehlender regionaler Wert wird nicht durch einen Fallback ersetzt. Ein bereits
vorhandener Entwurf wird nicht überschrieben. Ein identischer Wiederholungsaufruf
liefert das ursprüngliche Ergebnis ohne zusätzliche Revision oder Ereignis.

Der Schreibweg nutzt die gemeinsam extrahierten `BaselineOperation` und
`BaselineDraftWriter`, die auch `PostgresBaseline` verwendet. Revision, Ereignis,
Operationsbeleg und unveränderlicher Herkunftsnachweis werden zusammen gespeichert
oder vollständig zurückgerollt. Der Herkunftsnachweis enthält Baseline, ursprünglichen
Key, Locale, Dateipfad und Prüfsumme sowie die bestätigte Korrekturanwendung.

Der neue Entwurf bleibt `unreviewed`; unter dem korrigierten Key ist weiterhin
nichts veröffentlicht. Roh-Findings, andere Locales und die ursprünglichen
Sprachdateien bleiben unverändert. Die Erweiterung ist ausschließlich ein
isolierter Prototyp ohne produktive UI-Anbindung.

Prüfergebnis: 65 Assertions erfolgreich; 20 der 27 ursprünglichen Tabellen bleiben
unverändert. Zusätzlich zu den fünf Tabellen der Key-Korrektur ändern sich nun
Wert-Slots und Revisionen. Herkunftsnachweise liegen in einer neuen Tabelle.
Der bestehende Persistenztest besteht weiterhin mit 186 Assertions, zehn
Parallelitätsfällen und neun zusätzlichen Assertions nach Server-Neustart.

## Übernommenen Entwurf prüfen und Freigabe widerrufen

Der bestehende Review-Schreibweg wurde ohne zweite Implementierung in
`BaselineRevisionReviewer` ausgelagert. `PostgresBaseline` delegiert daran;
damit verwendet auch dessen Veröffentlichungsprototyp dieselbe Prüfung einer
aktuellen Freigabe wie die Korrekturprobe.

Die Probe prüft nun den übernommenen Entwurf ausdrücklich: fehlender Bearbeiter,
fehlende Begründung und veralteter Vorschaukontext werden abgewiesen, ohne Daten
zu schreiben. Eine gültige Freigabe ist wiederholbar ohne doppeltes Ereignis.
Ändert sich eine aktive Finding-Verknüpfung, gilt die Freigabe nicht mehr.
Ein anschließender ausdrücklicher Widerruf wird im Reader sichtbar und sperrt
die Freigabeprüfung ebenfalls. Der Herkunftsbeleg der Übernahme bleibt unverändert.

Freigabe und Veröffentlichung bleiben getrennt. Der korrigierte Key erhält in
dieser Probe keine Sprachdatei und keinen veröffentlichten Wert. Ein vollständiger
Datei-Export genau dieses übernommenen Entwurfs ist hier noch nicht integriert;
die bestehenden Veröffentlichungsfälle laufen separat im Persistenztest.

Ergebnis: 79 Assertions erfolgreich, 19 der ursprünglichen 27 Tabellen unverändert
(zusätzlich werden jetzt Reviews geschrieben). Bestehende Persistenz- und
Veröffentlichungsfälle weiterhin erfolgreich: 186 Assertions, zehn
Parallelitätsfälle sowie neun Neustart-Assertions. Keine produktive Anbindung.

## Datei-Export des übernommenen Entwurfs – 2026-10-20

Der vollständige Weg verwendet jetzt den bestehenden `PostgresPublisher`.
Für die wiederhergestellte Kopie akzeptiert dieser zusätzlich eine Verbindung,
deren Datenverzeichnis auf den isolierten `/tmp/tw-restore-*`-Cluster zeigt,
deren Datenbank `application_<12 Hexzeichen>` heißt und die kein TCP anbietet.
Der bisherige Persistenztest behält seinen bisherigen Konstruktor und Schutz.
Ausgaben der Kopie liegen getrennt je Testdatenbank unter diesem temporären
Cluster; Projekt- und produktive Sprachverzeichnisse sind keine Exportziele.

Geprüft: widerrufener Entwurf kann nicht vorbereitet werden; Vorbereitung
schreibt keine Datei; Widerruf nach Vorbereitung verhindert das Schreiben.
Der abgebrochene Auftrag wird ausdrücklich geschlossen. Nach erneuter Freigabe
schreibt ein neuer Auftrag den exakten übernommenen Wert als verschachtelten
PHP-Array-Eintrag `correction.type` in `en/rehearsal.php`.

Ein injizierter Fehler nach dem Dateischreiben hinterlässt keinen Erfolgsbeleg.
Der Wiederanlauf erkennt die bereits passenden Bytes, schließt den Auftrag ab
und liefert bei Wiederholung denselben Beleg ohne zusätzliche Änderungen.
Herkunftsnachweis und ursprüngliche Baseline-/Finding-Daten bleiben erhalten.
Der Bericht enthält Exportpfad und SHA-256 zur Nachprüfung.

Ergebnis: 94 Assertions erfolgreich. Die 15 unveränderten Tabellen zählen
konservativ ohne die vier am Publikationsprozess beteiligten Tabellen;
die Reservierungstabelle ist nach Abschluss wieder leer. Bestehende Tests
weiterhin erfolgreich: 186 Assertions, zehn Parallelitätsfälle und neun
Neustart-Assertions. Keine produktive Veröffentlichung oder UI-Anbindung.
Der Baseline-Reader bleibt eine Sicht auf die ursprüngliche Baseline; das
Einlesen des neuen Exportstands in eine neue Lesesicht ist ein separater Schritt.

## Exportierten Stand wieder einlesen – 2026-10-20

`PublishedRevisionReader` liest eine ausdrücklich ausgewählte abgeschlossene
Veröffentlichung und Revision. Er prüft Plan-Fingerprint, Abschlussbeleg und
Datei-Prüfsumme und parst die PHP-Datei mit dem vorhandenen statischen Array-Reader
(kein Ausführen der Datei). Der konkrete Eintrag muss mit dem Wert im Plan und
der unveränderlichen Revision übereinstimmen. Die Antwort enthält Key, Locale,
Revision, Lauf, Dateipfad, Prüfsumme und den Herkunftsbeleg der Entwurfsübernahme.

Die Datenbank wird in einer read-only Snapshot-Transaktion gelesen. Die geprüften
Dateibytes beschreiben den Stand beim Lesen; dies ist keine dauerhafte Garantie
gegen spätere Dateiänderungen. Die ursprüngliche Baseline bleibt unverändert,
ebenso die dortige Sicht auf veröffentlichte Werte. Der zusätzliche Reader
verifiziert vorerst skalare, nicht namensraumgebundene Exporte im Testprototyp.

Prüfungen: passender Wert und Herkunft, abgebrochener/geschlossener Auftrag,
nicht enthaltene Revision, nachträglich veränderte Datei, fehlende Datei,
erneutes Lesen nach Wiederherstellung sowie keinerlei Datenbankschreibzugriffe.
Gesamter integrierter Probelauf: 103 Assertions erfolgreich; Cluster gestoppt.
Keine produktiven Daten, Sprachdateien oder UI geändert.

## Integrierte Quellcode-Vorschau – 2026-10-20

Der Probelauf erzeugt inzwischen einen frischen privaten Testcluster pro Lauf
aus der verifizierten Sicherung und stoppt ihn abschließend. Er ist damit nicht
mehr von der Existenz des ursprünglichen temporären Clusters abhängig.

Nach dem Export und Wiedereinlesen werden sämtliche 387 ursprünglichen
Fundstellen anhand der gesicherten Bytes und Prüfsummen geprüft. Für 44 Dateien
entsteht eine Änderungsvorschau; weder die Quelldateien noch die Roh-Findings
werden dabei verändert. Die Einzelvorschläge liegen unter
`storage/app/private/dev/workbench-rehearsal/20261019-075456-18dfd037/application_21147d292577/source-change-preview.json`.

Der vollständige Lauf besteht mit 193 Assertions. Der UI-Bericht enthält nun
auch den bestätigten Vorschau-Schritt. Dies bestätigt weiterhin einen
synthetischen Testfall, keine fachliche Freigabe für den Ziel-Key und keine
Anwendung der Änderungen auf den aktuellen produktiven Quellcode.
