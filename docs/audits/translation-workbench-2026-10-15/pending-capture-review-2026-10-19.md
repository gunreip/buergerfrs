# Untersuchung der 72 offenen Kandidaten

Stand: 19. Oktober 2026. Alle 72 Capture-IDs sind einzeln geprüft und mit Quellpfad, Zeile, Dateihash, Ausdruck und kurzem Quellausschnitt im begleitenden JSON dokumentiert. Grundlage ist ausschließlich die gesicherte Quelle einschließlich der 20 englischen PHP-Sprachdateien. Kein App-Bootstrap, kein Datenbankzugriff, keine Änderungen an Findings, Quellcode oder Sprachdateien.

## Ergebnis und empfohlene Behandlung

| Kategorie | Anzahl | Behandlung |
| --- | ---: | --- |
| Reine `@js`-Serialisierung | 7 | Kein eigener Übersetzungsaufruf. Als Ausgabe-/Verwendungsnachweis behandeln, keine Übersetzungsaufgabe erzeugen. |
| `@js` mit bereits übersetztem Prop-Default | 6 | Der `__()`-Aufruf im Default ist die Übersetzungsquelle. Die wiederholte JS-Ausgabe ist kein neuer Key. Overrides bleiben möglich. |
| Expliziter Options-Key | 28 | Dynamische Optionsquelle: Key, Optionsidentität und Fallback getrennt erhalten. Der Options-Key selbst ist keine skalare Translation. |
| Überschreibbarer Literal-Prop-Default | 3 | Default ist belegt; er beweist nicht den tatsächlich zur Laufzeit übergebenen Text. Als Defaultnachweis verknüpfen, nicht pauschal als rawLiteral setzen. |
| Dynamischer Übersetzungswert | 18 | Produzenten bzw. endliche Wertebereiche untersuchen, gegebenenfalls Laufzeitnachweis benötigen. rawLiteral bleibt NULL. |
| Key-ähnlicher Text ohne exakten en-Beleg | 10 | Literal/Key-Klassifizierung und gegebenenfalls fehlenden Key in der UI prüfen. Keine automatische Umbenennung oder Zuordnung. |

Alle Entscheidungen bleiben im Bericht NULL. Die bisherigen 72 `pending`-Ergebnisse der Datenbank-Probe wurden nicht nachträglich verändert. Diese Untersuchung ist die Grundlage für den nächsten, getrennten Verarbeitungsschritt.

## Die 13 Serialisierungen

Sieben Stellen übertragen DEV-Schalter, Scrollziel, Copy-Wert oder Copy-Verfügbarkeit. `@js` serialisiert diese Werte und übersetzt sie nicht. Sechs weitere Stellen zeigen `showLabel`, `hideLabel`, `copyLabel` oder `copiedLabel`. Deren `@props`-Defaults enthalten bereits echte Übersetzungsaufrufe. Ausgaben derselben Props dürfen keine zusätzlichen TranslationKeys erzeugen. Der Reviewer prüft den Default statisch; eine `@props`-Zeile innerhalb eines Kommentars wird nicht als Beleg akzeptiert.

## Die 28 Options-Aufrufe

Alle verwenden `TranslationWorkbenchOption::label(...)` mit einem expliziten ersten Argument. Es gibt zwölf unterschiedliche Options-Keys und insgesamt 102 exakte englische Blattwerte darunter. Die statische Existenz dieser Werte beweist noch nicht, welche Optionen zur Laufzeit verwendet werden oder ob alle möglichen Optionen vollständig abgedeckt sind.

Die gesicherte Helper-Implementierung bildet den Lookup aus `owner + '.options.' + kodierter Optionsidentität` und liefert bei fehlendem Treffer den übergebenen Fallback zurück. Ein Import des Owners als einzelner skalarer Wert wäre daher falsch. Gleiche Optionswerte unter unterschiedlichen Keys bleiben getrennt; insbesondere werden create-/edit- oder form-/section-Kontexte nicht anhand gleicher Texte zusammengelegt.

## Die drei Prop-Defaults und 18 dynamischen Werte

Die drei Separator-Components haben ein Literal als Default von `$text`; Aufrufer können diesen Wert ersetzen. Ein Default ist deshalb belegte Zusatzinformation, keine unveränderliche Behauptung über jeden Aufruf.

Die übrigen 18 Aufrufe betreffen unter anderem Berechnungsbegründungen, Status-/Review-Werte, Labels aus Settings-/Export-Daten, Feldnamen, Dataset-Beschreibungen und Fehlermeldungen. Der Quellaufruf ist belegt, aber der konkrete Wert hängt vom Produzenten ab. Kein Ausdruck wird ausgeführt und keine Textmenge aus Vermutungen erzeugt. Die automatische Unterkategorie bleibt hier bewusst `runtime_translation_argument`.

## Zehn Key-ähnliche Texte

| Argument | Fundstellen | Befund / nächster Prüfschritt |
| --- | ---: | --- |
| `translation-workbench.php` | 1 | Im Workflow als Config-Dateiname dargestellt; spricht für einen technischen Literaltext. |
| `Copied.` | 1 | Sichtbare Statusmeldung nach erfolgreichem Kopieren; der abschließende Punkt macht daraus keinen belegten Key. |
| `segments.fusion` | 1 | Technische Component-Bezeichnung im Dokumentationsbeispiel; Literal/Key muss ausdrücklich unterschieden werden. |
| `ui.actions.cancel` | 2 | Key-förmig, aber im geprüften englischen Bestand nicht exakt vorhanden. Keine automatische Wahl eines anderen Cancel-Keys. |
| `ui.Show`, `ui.Hide` | jeweils 1 | Key-förmig, exakter englischer Beleg fehlt. |
| `ui.Show-usage-details`, `ui.Hide-usage-details` | jeweils 1 | Key-förmig, exakter englischer Beleg fehlt. |
| `admin.common.ui.example_admin_actions_save` | 1 | Wird als Placeholder übersetzt; fehlender Key-Beleg bleibt eine Prüfaufgabe. |

Die Punkt-Heuristik des bestehenden Scanners ist somit kein hinreichender Beweis für einen TranslationKey. Fehlender englischer Beleg bedeutet hier ausschließlich: kein exakter Treffer im untersuchten Snapshot der App-Sprachdateien. Daraus wird weder eine globale Nicht-Existenz noch ein Ersatz-Key abgeleitet. Im gesicherten Bestand sind keine JSON-Sprachdateien unter `lang/` enthalten.

## Prüfungen und Grenzen

Acht neue Assertions bestanden: Serialisierung, übersetzter Prop-Default, überschreibbarer Literal-Default, ignorierter Kommentar-Default, offene Literal/Key-Entscheidung, exakter Optionsprefix, keine automatische Aktivierung und Ablehnung abweichender Quelldateien. Alle 72 IDs sind eindeutig; keine Quelle wurde aus der Liste ausgelassen. Jeder Quelltext und jede gelesene Sprachdatei wurde per Hash verifiziert. Der Bericht enthält keine ausgeführten Entscheidungen.

Der nächste Schritt ist, diese unterschiedlichen Arten auch im Erfassungsmodell sauber abzubilden: **Übersetzungsquelle, Optionsquelle und reine Verwendung/Serialisierung**. Die 13 Serialisierungen sollen keine neuen Übersetzungsaufgaben werden; die 28 Optionsquellen dürfen nicht als skalare Texte importiert werden. Die zehn Literal/Key-Fälle bleiben sichtbar korrigierbar statt automatisch zurechtgebogen zu werden.
