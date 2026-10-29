# Wertquellen und Optionsargumente – 19.10.2026

Untersucht wurden die 31 offenen Übersetzungsquellen sowie alle 28 Optionsquellen aus dem gesicherten Quellstand. Die Prüfung führt keinen Anwendungscode aus, öffnet keine Datenbank und verändert weder Findings noch Keys oder Sprachdateien. Der maschinenlesbare [Nachweis](source-arguments-2026-10-19.json) enthält alle 59 Capture-IDs, genaue Ausdrücke, Quellpositionen und Prüfsummen.

## Die 31 offenen Übersetzungsquellen

| Befund | Fundstellen | Konsequenz |
|---|---:|---|
| Konkrete Produzenten untersucht | 7 | Beobachtete Werte/Ausdrücke mit Herkunft separat dokumentiert |
| Überschreibbarer Prop-Default | 3 | Default belegt; tatsächlicher Aufruferwert bleibt offen |
| Weiterhin laufzeitabhängig | 11 | Keine vollständige Wertemenge behauptet |
| Literal/Key-Entscheidung erforderlich | 10 | Keine automatische Umbenennung oder Ersatz-Key-Wahl |

Die sieben Fundstellen greifen auf sechs Produzenten zu:

| Produzent | Beobachtete Ausdrücke | Statische Strings |
|---|---:|---:|
| Settings / Zusammenfassung | 4 | 4 |
| Settings / Gruppentitel | 2 | 2 |
| Settings / Gruppenbeschreibung | 2 | 2 |
| Kontext-Badges (zwei Verwendungen) | 2 | 2 |
| Kritische Dataset-Beschreibungen | 13 | 13 |
| Publication-Konfliktmeldungen | 21 | 20 |

Die Producer-Zuordnung wurde anhand des gespeicherten Codes manuell nachvollzogen. Der AST-Reader extrahiert die dort beobachteten Ausdrücke; er ist keine allgemeine Datenflussanalyse und beweist weder Laufzeiterreichbarkeit noch eine vollständige Laufzeit-Wertemenge. Gleiche Texte an verschiedenen Aufrufen bleiben getrennte Beobachtungen. Comments werden nicht als Methodenaufrufe gezählt.

Die 21 Publication-Aufrufe enthalten unter anderem eine Verkettung mit `$pending['id']`. Diese bleibt ein Laufzeitausdruck. Die ID wird weder geraten noch entfernt, und der zusammengesetzte Text wird nicht als festes Literal importiert. Eine spätere Umstellung auf einen Text mit Platzhalter wäre eine gesonderte Quellcodeänderung.

Die elf weiterhin dynamischen Fundstellen sind zwei Berechnungsbegründungen aus dem `details`-Prop, sechs Status-/Review-Ausgaben sowie drei aus Feldnamen oder Dokumentattributen erzeugte Überschriften. Ihre vollständigen Wertbereiche wurden nicht nachgewiesen. Insbesondere sind Statuswerte aus Datensätzen keine neuen unveränderlichen Literale der aufrufenden View.

## Optionsquellen

Für jeden der 28 Aufrufe werden separat gespeichert:

- Expliziter Options-Key (`owner`). Dieser bezeichnet keine skalare Translation und keinen besitzenden Finding-Datensatz.
- Exakter Ausdruck für die Optionsidentität, etwa `(string) $value` oder `$document->type`.
- Exakter Fallback-Ausdruck, etwa `$documentTypeOptions[$document->type] ?? $document->type`.
- Explizites Locale-Argument oder der Hinweis auf den Helper-Default.

Nur echte Stringknoten erhalten einen `literal`-Wert. Bei Variablen, Casts, Arrayzugriffen und Verkettungen bleibt dieser NULL. Leere Strings bleiben als leere Strings erhalten und werden nicht mit NULL verwechselt. Benannte und entpackte Argumente werden ausdrücklich abgewiesen, solange ihre Bindung nicht unterstützt wird.

Der gesicherte Helper bildet den Lookup aus Key, `.options.` und der kodierten Identität. Leere Identität liefert den Fallback. Für andere Identitäten erfolgt der Translator-Lookup; ein fehlender oder nicht skalarer Treffer liefert ebenfalls den Fallback. Locale-Fallbacks des Translators bleiben davon getrennt. Die Helper-Datei ist im Bericht per Hash belegt; sie wird nicht ausgeführt. Die bereits ermittelten 102 englischen Optionswerte beweisen nicht die Vollständigkeit aller möglichen Optionsidentitäten.

## Ergebnis und nächster Umsetzungsschritt

Die Trennung ist jetzt für alle 59 Aufrufe nachvollziehbar. Die ursprünglichen Pending-Entscheidungen bleiben NULL: zusätzliche Herkunftsevidenz ist noch keine Freigabe. Es wurden keine neuen Findings, Keys, Zuordnungen oder Translations erzeugt.

16 neue Argument-/Producer-Assertions, acht bestehende Review-Assertions und 15 Rollen-Assertions bestanden. Die Tests prüfen unter anderem exakte dynamische Ausdrücke, leere Strings, Locale-Argumente, falsche Helper, benannte/unpacked Argumente, Kommentare und unbekannte Laufzeitwerte.

Als nächstes kann diese Evidenz im isolierten Schema als getrennte Quellen-/Argumentnachweise persistiert werden. Für die eigentliche Übernahme bleiben Optionsidentität, Aufrufer-Overrides und die zehn Literal/Key-Entscheidungen bewusst überprüfbar; die fehlenden Werte werden nicht durch Annahmen aufgefüllt.
