# Kontextprüfung der gesicherten Quellerfassung

Stand: 19. Oktober 2026. Der bisherige Kandidatenlauf wurde um eine statische Kontextprüfung ergänzt und auf derselben Sicherung wiederholt. Kein produktiver Datenbankzugriff, keine Veröffentlichung und keine aktive Generation. Die ursprünglichen Erfassungsberichte bleiben als Vergleich unverändert erhalten.

## Ergebnis

| Kontext der vorhandenen Scanner-Treffer | Anzahl |
| --- | ---: |
| Syntaktischer Funktions-/statischer Methodenaufruf bzw. unterstützte Blade-Direktive | 20.012 |
| Ausgeschlossener Kommentar | 105 |
| Ausgeschlossener PHP-Stringinhalt | 3 |
| Ungeklärter Quelltext-Kontext | 0 |
| Gesamt | 20.120 |

Die Capture-IDs stimmen vollständig mit dem vorherigen Lauf überein. Es wurden keine Treffer gelöscht oder zusammengelegt. 15 vorher als Kommentar behandelte Treffer sind Literal-Aufrufe, ein weiterer ist ein dynamischer Aufruf. Die drei zuvor unvollständigen Ausdrücke sind Textbausteine in `ApplySuspiciousKeyRestores.php` (Zeilen 329–331), keine Aufrufe.

Unter den 20.012 syntaktischen Aufrufen verbleiben 17.733 Literal-Kandidaten, 2.207 Key-Kandidaten mit exaktem Sprachdatei-Beleg, zehn Key-ähnliche Argumente ohne solchen Beleg, 34 dynamische/nichtskalare Argumente sowie 28 spezielle Aufrufe mit weiterem fachlichem Kontextbedarf. Alle behalten den Status `pending_review`. Eine syntaktische Bestätigung sagt nicht, dass ein Zweig zur Laufzeit tatsächlich erreicht wird, ein Key semantisch richtig ist oder eine Übersetzung freigegeben wurde.

## Verfahren

- PHP-Dateien und native PHP-Blöcke werden als Syntaxbaum gelesen. Nur echte Call-Nodes an der erfassten Byteposition bestätigen den Aufruf; Kommentare und Stringtokens erhalten eigene Ausschlussgründe.
- Blade wird konservativ in bekannte Codebereiche aufgeteilt: Echos, PHP-Blöcke und Inline-PHP, ausgewählte Standarddirektiven, Schleifenköpfe und gebundene Component-Attribute. Diese Inhalte werden statisch als PHP geprüft.
- Blade-Kommentare, HTML-Kommentare, `@verbatim` und escaped Echos werden getrennt behandelt. Gewöhnliche Attribute werden nicht als PHP angesehen. Attribut-Bags, Kommentare und Direktiven zwischen Attributen verhindern die Prüfung nachfolgender gebundener Attribute nicht.
- Unbekannte Direktiven, nicht unterstützte Bereiche und Parsefehler bleiben `unresolved`. Es werden keine Anwendungsdirektiven registriert oder ausgeführt und keine Views kompiliert/gerendert.
- Ausgeschlossene Treffer erzeugen keine Rohtextfelder oder SuggestedKeys. Der ursprüngliche Ausschnitt und seine Herkunft bleiben im privaten Kandidatenmanifest nachvollziehbar.

## Prüfungen und Grenzen

42 Assertions mit dem vorhandenen Scanner und dem Kontextprüfer bestanden, einschließlich PHP-Strings/Nowdocs, Inlinekommentaren, Methoden-Lookalikes, kaputtem PHP, Blade-Echos, Schleifen, Inline-PHP, unbekannten Direktiven, gebundenen/gewöhnlichen Attributen, Attribut-Bags, escaped Blade und falschen Block-Enden innerhalb von Strings. Die sieben bestehenden Mappingtests, PHP-Syntaxprüfung und Diff-Whitespace-Prüfung bestanden ebenfalls.

Alle 20.120 Capture-IDs wurden auf Eindeutigkeit und Identität zum vorigen Lauf geprüft. Zähler und Ergebnisdateihash stimmen überein. Source-/Language-/Implementierungshashes werden im Lauf erneut geprüft. Der öffentliche JSON-Bericht enthält Prüfsummen und den privaten Manifestpfad, keine vollständigen Quelltexte.

`source_context_validation_complete=true` gilt **nur für die vom vorhandenen Scanner entdeckten Treffer dieser Sicherung**. Es beweist keine vollständige Discovery aller möglichen PHP-/Blade-Konstrukte oder unmarkierten UI-Texte. Der Kontextprüfer ist ein isolierter Prototyp und wurde nicht in produktive Scan-Commands eingebaut. Er ersetzt auch keine Entscheidung über dynamische Quellen, Sprache, Key-Zuordnung oder Shared Candidates.

## Nächster Schritt

Die bestätigten statischen Kandidaten für eine Probe-Erfassung im neuen Schema vorbereiten und die dynamischen beziehungsweise semantisch offenen Fälle separat bilanzieren. Dabei müssen Scan-Herkunft und neue Finding-Identitäten erhalten bleiben; weder alte Finding-IDs noch alte Freigaben werden übernommen. Noch keine Veröffentlichung oder produktive Umschaltung.
