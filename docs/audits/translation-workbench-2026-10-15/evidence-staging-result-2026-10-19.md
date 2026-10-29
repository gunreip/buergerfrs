# Getrennte Nachweisablage – 19. Oktober 2026

Ergebnis: `evidence_staged_and_verified`. Der Lauf arbeitete ausschließlich im bereits geprüften, isolierten PostgreSQL-Testcluster. Laravel wurde nicht gestartet und die produktive Datenbank nicht verbunden.

| Prüfung / Umfang | Ergebnis |
| --- | --- |
| Tabellen gegen das Sicherungsmanifest geprüft | 33 |
| Tabellen als unveränderte Nachweise kopiert | 26 |
| Kopierte Datensätze | 122.133 |
| Nur im restaurierten Archiv belassene History-/Lauftabellen | 7 |
| Verifizierte Quelldateien, mit Pfad und Hash erfasst | 3.595 |
| Abgewiesene Änderungs-, Lösch- und Duplikatversuche | 3 |
| Aktive Generation angelegt | Nein |
| Testcluster anschließend gestoppt | Ja |

## Herkunft und Inhalt

Jeder Datensatz trägt Sicherungskennung, ursprüngliche Tabelle und ID, den unveränderten JSON-Zeilentext und dessen SHA-256. Anzahl und Inhaltsfingerabdruck jeder kopierten Tabelle wurden aus den tatsächlich gespeicherten Nutzdaten erneut berechnet und mit dem ursprünglichen Sicherungsmanifest verglichen. Die restaurierte Quelldatenbank wurde in einer Read-only-Transaktion gelesen.

Die Ablage ist keine neue Workbench-Datenbank: Alte Reviews, Schlüsselverknüpfungen und Statuswerte sind hier ausschließlich Nachweise. Sie erhalten dadurch keine neue Gültigkeit. Insbesondere werden alte Timeline-Ereignisse nicht in eine neue Arbeitsgeschichte eingespielt. Die sieben nur archivierten Tabellen sind im vollständigen Original-Dump weiterhin enthalten; ihre Anzahl und Inhaltsfingerabdrücke sind in der Nachweisablage registriert.

## Sicherung und Grenzen

Ein zusätzlicher privater Dump der Nachweisablage wurde erstellt (18.211.057 Bytes). Pfad und SHA-256 stehen im begleitenden JSON-Ergebnis. Das Dump-Inhaltsverzeichnis wurde erfolgreich gelesen; eine zusätzliche Wiederherstellungsprobe dieses neuen Dumps wurde noch nicht durchgeführt. Die ursprüngliche Vollsicherung wurde bereits zuvor vollständig restauriert und geprüft.

Die SQL-Trigger verhindern gewöhnliche UPDATE-/DELETE-Zugriffe; sie sind kein Schutz gegen administrative Eingriffe oder TRUNCATE. Die Herkunftseindeutigkeit verhindert doppelte Nachweise innerhalb einer Charge. Jeder erneute Skriptlauf erstellt eine neue isolierte Datenbank; ein fortsetzbarer Import in eine aktive Generation ist damit noch nicht implementiert.

## Nächster Schritt

Aus diesen Nachweisen einen expliziten Importplan für die neue Generation ableiten: unveränderliche Rohfelder und getrennte Herkunft für `rawSourceCodeLiteral`, `rawLiteral`, `suggestedLiteral` und `suggestedKey`. Fehlende Nachweise bleiben NULL; widersprüchliche Werte bleiben Prüfaufgaben. Alte SuggestedKeys dürfen nicht nachträglich als scannerbestätigte Vorschläge ausgegeben werden. Erst danach erfolgt die Probeübernahme in das neue Schema, weiterhin ausschließlich auf der Kopie.
