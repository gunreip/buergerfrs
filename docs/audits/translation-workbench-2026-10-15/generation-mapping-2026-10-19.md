# Zuordnung zur neuen Workbench-Generation

Stand: 19. Oktober 2026. Ergebnis ist ein **Übernahmeplan**, kein ausgeführter Import. Grundlage sind ausschließlich das verifizierte Sicherungsmanifest und die daraus erzeugte Nachweisablage. Der Generator liest weder Laravel-Konfiguration noch eine Datenbank und verändert keine Quelldateien.

## Rohfelder einer neuen Fundstelle

| Zielfeld | Verlässliche Herkunft | Wenn nicht belegbar |
| --- | --- | --- |
| `raw_expression` | Vollständiger Ausdruck einer neuen statischen Erfassung im gesicherten Quellcode; Datei-Hash, Position und Parser-Version gehören zur Herkunft. | Keine Fundstelle erzeugen. |
| `rawSourceCodeLiteral` / `raw_source_code_literal` | Tatsächlicher Argumentinhalt im Quellcode, unabhängig davon, ob Literal oder bereits TranslationKey. Klassifizierung separat erfassen. | NULL; keine Ableitung durch Zusammenfügen alter Felder. |
| `rawLiteral` / `raw_literal` | Tatsächlich als Literal klassifizierter Quelltext. Ein TranslationKey ist kein Literal. | NULL; Mehrdeutigkeit als Prüfaufgabe erhalten. |
| `suggestedLiteral` / `suggested_literal` | Eindeutiger skalarer Text zum exakt erfassten Key und expliziter Locale in der gesicherten Sprachdatei, einschließlich Datei-Hash und Lookup-Pfad. | NULL; abweichende Kandidaten bleiben Nachweise. |
| `suggestedKey` / `suggested_key` | Einmaliger Vorschlag des neuen Scanners für ein Literal, mit Generatorversion und Eingaben. | NULL; keinen aktuellen TranslationKey als historischen Vorschlag ausgeben. |

Bereits vorliegende alte `suggested_key`, `literal_text` und `literal_text_suggested` bleiben unverändert in der Nachweisablage. Sie werden nicht ungeprüft in die neuen unveränderlichen Rohfelder kopiert. Der neue Scan dokumentiert den aktuellen gesicherten Zustand, keine rückwirkend rekonstruierte Erstaufnahme. Für bereits vorhandene TranslationKeys werden Key-Erfassung, eventuelle Textableitung und Zuordnung getrennt behandelt. Die Sprache eines alten `native_label` darf nicht allein aus dem Text geraten werden.

## Tabellen und Zielaufgaben

| Aufgabe | Quellen | Vorgehen |
| --- | --- | --- |
| Frische Erfassung | Findings, Entries, Occurrences, Usages, Source files | Aus dem kopierten Quellcode neu erfassen. Alte Positionen sind Vergleichsnachweise. Obsolete Findings bleiben archiviert. |
| Schlüsselidentität | Legacy keys, Workbench keys, Key inventory | Namespace und vollständigen Key prüfen; neue IDs vergeben, Herkunft ausdrücklich verknüpfen. |
| Finding-Zuordnung | Key findings | Eigenständige Findings mit eigenständigen Keys verknüpfen. Kein Finding besitzt einen Key. |
| Übersetzungsrevisionen | Legacy values, Key values, Lang values, Dynamic key values | Locale, Zielkey und scalar/option prüfen. Widersprüche nicht automatisch entscheiden. Neue Revisionen erhalten tatsächlichen Import-Akteur und Grund. |
| Dynamischer Kontext | Dynamic sources/candidates/source values, alte Dynamic values, Option discoveries | Elternstatus und konkrete Option-Identität prüfen. Gleicher Text allein beweist keine Zuordnung. |
| Native Texte | Workbench values | Originalpayload einschließlich strukturierter Inhalte erhalten. Nicht automatisch in skalare Übersetzungen umwandeln. |
| Kandidaten | Duplicate candidates, Shared key candidates | Auf Basis neuer aktiver Fundstellen neu berechnen; kein automatisches Zusammenführen gleichlautender Literale. |
| Konfiguration | Languages | Locale- und Fallback-Konfiguration ausdrücklich prüfen, Schreibweisen nicht automatisch normalisieren. |
| Alte Entscheidungen | Reviews, Ballast decisions, Usage audit decisions/usages | Nachweise erhalten. Keine alten Freigaben als neue Publikationsfreigaben übernehmen. |
| History/Läufe | Audit events, Workbench events, Timeline events/chains, Pipeline runs/steps, Event types | Im alten Archiv belassen. Neue Timeline startet mit tatsächlichen neuen Arbeitsvorgängen. |

Das JSON führt jede der 33 Tabellen mit Anzahl und Inhaltsfingerabdruck einzeln auf. Kein Datensatz ist darin zum automatischen aktiven Import freigegeben.

## Identitäten und Nachvollziehbarkeit

Ein Herkunftsverweis besteht aus **Sicherungskennung + Quelltabelle + ursprünglicher ID**. Eine spätere Zielzuordnung besteht aus **Generationskennung + Zieltabelle + neuer ID** und einer begründeten Entscheidung. Alte IDs werden nicht pauschal als neue IDs übernommen. Ein numerisch gleicher Finding- und Key-Wert stellt keine Beziehung her.

Die spätere Übernahme muss jeden Nachweis als zugeordnet, noch zu prüfen oder archiviert bilanzieren. Auch mehrere belegte Quellen für eine bestätigte neue Identität bleiben getrennt nachvollziehbar. Textgleichheit allein erlaubt weder das Zusammenlegen von Findings noch von Keys.

## Ausgeführte Prüfungen

- Generator gegen das tatsächlich geprüfte Sicherungsmanifest ausgeführt: **33 Tabellen klassifiziert, 0 aktive Importe**.
- Manifest- und Staging-Dump-SHA-256 geprüft, referenzierte Quellspalten vorhanden.
- Sieben isolierte Tests bestanden: vollständige Zuordnung, neue/unbekannte Tabelle, fehlende Collection, fehlende Spalte, falsche History-Zuordnung, abweichende Anzahl und unzulässiger Laufzustand.
- Vorhandene Plandateien werden nicht überschrieben.

## Noch offen

Der Adapter für eine echte neue statische Quellcode-Erfassung ist noch nicht angeschlossen. Der bisherige PostgreSQL-Prototyp erfasst ausdrückliche Test-Fixtures; er ersetzt keinen Scanner. Als nächster Schritt muss der Adapter auf der gesicherten Quellkopie arbeiten und zunächst ein überprüfbares Erfassungsmanifest erzeugen: Datei-Hash, Zeile/Spalte, Ausdruck, Argumenttyp und getrennte Rohfelder. Dynamische oder mehrdeutige Ausdrücke bleiben ausdrücklich offen. Danach folgen datensatzbezogene ID-Zuordnung, überprüfte Wertübernahme, Verlustbilanz und Workflow-Probelauf – weiterhin auf der Kopie.
