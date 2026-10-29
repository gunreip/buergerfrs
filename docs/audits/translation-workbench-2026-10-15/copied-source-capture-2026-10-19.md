# Quellerfassung auf der Sicherungskopie

Stand: 19. Oktober 2026. Der vorhandene `TranslationWorkbenchScanner` wurde mit einem isolierten Container auf der verifizierten Quellkopie ausgeführt. Kein Laravel-Anwendungsbootstrap, keine Projekt-Provider, keine `.env`, kein Datenbankzugriff. Das Scan-Ergebnis ist ein **Kandidatenmanifest**, keine aktive neue Workbench-Generation.

## Tatsächliches Ergebnis

| Umfang / Kategorie | Anzahl |
| --- | ---: |
| Gelesene Quelldateien, einschließlich Dateien ohne Treffer | 1.684 |
| Statisch gelesene Sprachdateien, explizite Locale `en` | 20 |
| Scanner-Treffer mit eindeutiger Capture-ID | 20.120 |
| Literal-Kandidaten | 17.718 |
| Key-Kandidaten mit exaktem Sprachdatei-Beleg | 2.207 |
| Auskommentierte Treffer, ausgeschlossen | 121 |
| Dynamische oder nichtskalare Argumente | 33 |
| Key-ähnliche Argumente ohne Sprachdatei-Beleg | 10 |
| Unvollständige Ausdrücke | 3 |
| Spezielle Aufrufe mit weiterem Kontextbedarf | 28 |

Alle nicht ausgeschlossenen Treffer bleiben `pending_review`. Die Zahlen sind **keine Anzahl neu aktivierter Findings** und belegen noch keine vollständige Erkennung sämtlicher PHP-/Blade-Kontexte.

## Erfasste Herkunft

Jeder Treffer trägt Sicherungskennung (über die Capture-ID), relativen Dateipfad, SHA-256, Byte-Offset, Zeile und Spalte. Spalten zählen Bytes; UTF-8-Zeichen können deshalb mehrere Spalten belegen. Der vollständige Ausdruck wird ohne Normalisierung aus den Quelldateibytes gewonnen. Der Argumentinhalt wird statisch mit PHP-Parser geprüft, nicht ausgeführt.

- `raw_source_code_literal`: statischer Argumentinhalt, Literal oder Key.
- `raw_literal`: nur ein als Literal klassifizierter statischer String; keine dynamischen Präfixe oder aus Keys abgeleiteten Texte.
- `suggested_key`: für Literal-Kandidaten mit der vorhandenen SuggestedKeyFactory neu berechnet; für vorhandene Keys NULL. Implementierung und Eingaben sind über Code-/Quellhashes belegbar.
- `suggested_literal`: ausschließlich exakter skalarer Treffer zum Key in der gesicherten `lang/en/<group>.php`, mit Dateihash, Locale und Key als Herkunft. Kein stiller Fallback, kein Vergleich mit ähnlich lautenden Keys.

Der bisherige Normalizer erzeugt `literalTextSuggested` auch aus dem letzten Key-Segment. Diese heuristische Ableitung wird bewusst **nicht** in das neue Herkunftsfeld übernommen. Der produktive Scanner wurde dafür nicht geändert.

Die Scan-Auswahl stammt aus den fünf literal gespeicherten Scope-Arrays der gesicherten Package-Konfiguration. Diese wurde als AST gelesen, nicht ausgeführt. Die Ausschlüsse `_old` und `-old` bleiben wirksam. Die eingesetzte Scanner-Implementierung stammt aus dem aktuellen Checkout und ist separat gehasht; sie wird nicht mit dem gesicherten Quellcode gleichgesetzt. Source-/Language-Hashes werden vor und nach dem Lauf geprüft, ebenso die Implementierungsdateien. Eine abweichende aktuelle Laufzeitkonfiguration wurde nicht geladen.

## Prüfungen

14 Adapter-Assertions mit dem echten Scanner bestanden: gleiche Aufrufe an verschiedenen Positionen, Whitespace, mehrzeilige Ausdrücke, PHP-String-Escapes, Key/Literal-Trennung, fehlender Sprachbeleg, Konkatenation, Interpolation, dynamisches Argument, Inline-PHP- und Blade-Kommentare, Hash-Abweichung und keine Aktivierung. Anschließend wurden alle 20.120 gespeicherten Capture-IDs auf Eindeutigkeit, Zähler auf Übereinstimmung und die gesetzten Textvorschläge auf Herkunft geprüft. Pint und Diff-Whitespace-Prüfung bestanden.

Die Rohdaten bleiben unter `storage/app/private/dev/workbench-rehearsal/…/capture-…/` außerhalb von Git. Das begleitende öffentliche JSON enthält Zähler, Konfiguration, Dateiverweis und Prüfsummen, keine vollständigen Rohtexte.

## Nächste notwendige Prüfung

Die Discovery des bestehenden Scanners beruht teilweise auf regulären Ausdrücken. Auch ein statisch parsebarer Ausschnitt kann innerhalb eines Beispiels oder eines PHP-Strings stehen. Deshalb beweist die Argumentprüfung noch keinen ausführbaren Aufruf im umgebenden Quelltext. Die drei unvollständigen Ausdrücke liegen etwa in `ApplySuspiciousKeyRestores.php`, Zeilen 329–331, und bleiben offen.

Vor einem aktiven Import muss die Kontextprüfung für PHP und Blade angeschlossen werden. Dabei sind Code-Beispiele/Stringinhalte von tatsächlichen Übersetzungsaufrufen zu unterscheiden und dynamische Spezialfälle ausdrücklich zu bilanzieren. Unmarkierte freie UI-Texte sind mit diesem Übersetzungsaufruf-Scanner ebenfalls nicht automatisch vollständig erfasst. Erst danach können geprüfte Aufnahmen in das neue Schema übernommen werden. Alte Findings oder Timeline-Ereignisse wurden in diesem Schritt weder geändert noch reaktiviert.
