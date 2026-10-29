# Vorbereitung der Translation-Workbench-Generalprobe

Stand: 2026-10-19. Die inhaltlichen Teilprüfungen sind konsolidiert. **Keine Generalprobe, Wiederherstellung oder produktive Migration wurde mit diesem Schritt ausgeführt.**

> Historischer Vorbereitungsstand: Die nachfolgenden Statusangaben gelten für den Zeitpunkt dieser Planung. Sicherung, Restore und weitere isolierte Abläufe wurden inzwischen durchgeführt. Der [Umsetzungsstand vom 20. Oktober mit den verbleibenden Arbeitspaketen](workbench-implementation-status-2026-10-20.md) führt die Nachweise und den nächsten Umsetzungsschritt zusammen. Das ursprüngliche Entscheidungsmanifest bleibt unverändert.

## Zentrale Entscheidungsübersicht

Das [Manifest](transfer-rehearsal-manifest-2026-10-19.json) bindet sieben Evidenzdateien über SHA256 und enthält 114 offene Einträge. Jede Entscheidung ist noch NULL. Die Einträge sind nach Themen gebündelt; gleiche Texte oder gemeinsam bearbeitete Themen erlauben keine automatische Zusammenführung von Findings/Keys.

| Themenblock | Einträge | Behandlung auf der Kopie |
| --- | ---: | --- |
| Abweichende Übersetzungstexte | 10 | Alle Fassungen samt Herkunft erhalten; redaktionelle Entscheidung separat. |
| Locale-Schreibweisen | 7 | Zuordnung und Fallback ausdrücklich festlegen; keine stillen Umbenennungen. |
| Alte Optionszuordnungen | 43 | SuggestedKey bleibt unverändert; textgleiche Kandidaten sind keine bestätigte Verlinkung. |
| DB-only-Kontexte | 30 | 16 Optionswerte unter anderem Key und 14 eigenständige skalare Nachweise erhalten. |
| Native Sonderfälle | 19 | Sechs historische Texte und 13 strukturierte Payloads erhalten; keine Reaktivierung. |
| Paket-Overrides | 3 | Projekt- und Paketfassung getrennt behalten. |
| Finding-/Link-Integrität | 1 Prüfauftrag | Die aggregierten Befunde (u. a. 12 Links zu obsolete Findings und 27 Findings mit mehreren benannten Keys) auf der frischen Kopie mit konkreten IDs prüfen. |
| Übergeordneter Quellenstatus | 1 Prüfauftrag | 70 aktive Quellwerte an obsolete Quellen dürfen nicht allein wegen ihres eigenen Status aktiviert werden. |

Diese Themenblöcke sind keine neue Anzahl exklusiver Übersetzungstexte. Beispielsweise können Optionsbezüge und native Nachweise denselben fachlichen Kontext berühren. Die Liste ist für Nachverfolgung und Prüfung gedacht, nicht als Importauftrag.

Bereits geklärte Inhaltsnachweise bleiben in den Berichten: zwei NULL-Fälle mit vorhandener Übersetzung, 14 historische NULL-Fälle, 71 Locale-Schreibvarianten, 21 Basislocale-Übereinstimmungen, 1.051 identische Pakettexte und weitere native Vergleichsnachweise. „Geklärt“ bedeutet hier erklärt/gesichert, nicht aktiv freigegeben.

## Was die Generalprobe leisten muss

Zwei Ergebnisse bleiben getrennt:

1. **Vollständige Evidenzaufnahme:** Jeder gesicherte Text und jede Herkunft ist auf der Kopie wieder auffindbar. Offene Zuordnungen bleiben als offene Evidenz erhalten.
2. **Aktiver Arbeitsbestand:** Nur ausdrücklich zugeordnete Findings/Keys und überprüfte Werte werden aktiv. Keine alten Freigaben oder Timeline-Einträge werden still in die neue Generation übertragen.

Die alte Historie bleibt im Backup/Archiv; die neue Arbeitsdokumentation beginnt bei null. Alte IDs dienen nur als Herkunftsreferenzen, nicht als neue produktive Identität. Source-Literale werden nicht massenhaft zurückgeschrieben; bestehender SourceCode und veröffentlichte Sprachdateien bleiben Ausgangsbasis.

## Verbindliche Reihenfolge

| Schritt | Konkretes Ergebnis | Aktueller Stand |
| --- | --- | --- |
| 1. Zusammenhängende Sicherung | Vollständiger DB-Dump sowie passender Quell-/lang-Stand mit Hashmanifest, Versionsstand und dokumentierten Abhängigkeiten | Noch nicht erstellt |
| 2. Wiederherstellungsprüfung | Restore in eigenem temporären PostgreSQL-Cluster; Tabellen-/Wertvergleiche gegen Sicherung | Noch nicht durchgeführt |
| 3. Evidenz-Staging | Import ausschließlich aus gesicherter Quelle in isoliertes Schema; jedes Original mit Herkunft/ID, keine impliziten aktiven Zuordnungen | Noch zu implementieren |
| 4. Echter Neuaufbau | Scanner gegen kopierten SourceCode; Capture-Felder, Erst-/Zweitscan, Umzug, Entfernen, copied/handmade und SharedCandidate prüfen | Noch zu integrieren |
| 5. Offene Fälle isolieren | Nicht entschiedene Kandidaten verhindern keine Archivaufnahme, gelangen aber nicht in Freigabe/Publikation | Noch zu integrieren |
| 6. Arbeitsabläufe durchspielen | Zuordnung, Bearbeitung, Freigabe, Teilpublikation, Wiederaufnahme und Fehlerfälle auf der Kopie | Fixture-Nachweise vorhanden; echte Kopie noch offen |
| 7. Abschlussvergleich | Kein exklusiver Text verloren; keine Reaktivierung alter Findings; keine alte History als neue Timeline | Noch offen |

PostgreSQL-Werkzeuge `pg_dump` und `pg_restore` sind vorhanden; `pg_dump --version` meldet 18.6. Das ist kein Nachweis einer bereits erstellten oder getesteten Sicherung.

## Sicherungs- und Isolationsbedingungen

- DB-Dump und Dateien müssen einen zusammenpassenden Stand bilden. Während der Sicherung darf keine unbemerkte Quell-/lang-/Workbench-Änderung stattfinden; Änderungen führen zur Wiederholung des Sicherungsschritts, nicht zur Verwendung gemischter Stände.
- DB-Snapshot allein garantiert keine zeitgleiche Dateisicherung. Dateihashes vorher/nachher, Schreibzustand und Zeitraum müssen mit dokumentiert werden.
- Vollständige Dumps enthalten mehr als Workbench-Daten: geschützt und außerhalb versionierter Auditdateien ablegen. Keine Zugangsdaten in Berichte, Git oder Toolausgaben schreiben.
- Restore ausschließlich in einen frisch erzeugten eigenen Cluster ohne TCP-Listener. Keine Bereinigung vorhandener Datenbanken, kein `migrate:fresh` gegen die Anwendung, keine bestehenden DB-Namen als Ziel.
- Für den Neuaufbau getrenntes Testschema beziehungsweise separate Testdatenbank verwenden. Die restaurierte Ausgangskopie bleibt Vergleichsbasis.
- Commands/Services dürfen erst nach nachgewiesener Isolation laufen; Queue-, Scheduler-, Mail- und Veröffentlichungspfade dürfen keine produktiven Ziele erreichen.
- Dateien nur innerhalb des zugehörigen Kopieverzeichnisses schreiben. Originalquellen und produktives `/lang` bleiben unverändert.
- Erfolgreicher Restore plus Inhaltsvergleich ist erforderlich; ein erfolgreiches `pg_restore --list` allein genügt nicht.

## Bereits vorhandene technische Nachweise und Grenzen

Das PostgreSQL-Fixture-Modell prüft Constraints, Revisionen, Freigaben, konkurrierende Vorgänge, Neustart und abgesicherte Dateiveröffentlichung. Sein Capture-Vorgang ist keine Implementierung des realen Scanners. Die groben Tabellensperren des Testexporters sind keine bereits optimierte Produktivarchitektur. Paket-Veröffentlichung bleibt ohne explizite Loader-Zielzuordnung gesperrt.

Ein grüner Fixture-Test bedeutet daher nicht, dass produktive Commands oder UI bereits das neue Schema benutzen. Die UI-/Command-Anbindung und ein gemeinsamer Schreibweg sind nach der Generalprobe eigene Abnahmepunkte.

## Reproduzierbare Prüfung des Manifests

```bash
python3 docs/audits/translation-workbench-2026-10-15/prepare-transfer-rehearsal.py check docs/audits/translation-workbench-2026-10-15/transfer-rehearsal-manifest-2026-10-19.json
python3 tests/Integration/TranslationWorkbench/transfer-rehearsal-manifest-test.py
```

Der Validator meldet `rehearsal_ready=false` und `reset_ready=false`. Er erkennt geänderte Evidenzhashes, doppelte Bearbeitungs-IDs und unzulässige automatische Entscheidungen. Ein existierendes Manifest wird vom Generator nicht überschrieben. Für tatsächlich beschlossene Entscheidungen ist ein eigener belegter Entscheidungs-/Importweg nötig, nicht das Umstellen eines Bool-Werts in dieser Vorbereitung.

**Nächster konkreter Arbeitsschritt:** Sicherung und Wiederherstellung auf einem isolierten Cluster vorbereiten und ausführen; danach anhand dieser Kopie den Evidenz-Import entwickeln. Ein produktiver Reset folgt daraus nicht automatisch.
