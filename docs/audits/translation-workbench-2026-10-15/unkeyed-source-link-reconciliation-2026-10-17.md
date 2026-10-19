# Unbenannte Kandidaten: historische Fehlverknüpfungen

## Ursache

Die Historie der 298 Findings mit mehreren unbenannten Key-Kandidaten zeigt frühere dateiübergreifende Wiederverwendung derselben Finding-ID durch `translation-workbench:sync-foundation`. Dabei wurden `source_file_id` und `suggested_key` geändert, während frühere Key-Verknüpfungen aktiv blieben. Das ist kein Beleg für eine bewusste gemeinsame Übersetzungsaufgabe.

Beispiel Finding #116 (`Ready`): zuerst `app/Livewire/Admin/TranslationUsageAudit.php`, dann Filter und Modal der Translation Usage Audit. Die Timeline enthält 80 Änderungen der Dateizuordnung sowie die ursprüngliche Erfassung. Die zwei Vorkommen im PHP-Code, eines im Filter und eines im Modal besitzen inzwischen getrennte Findings. Für `Needs key` sind entsprechend fünf Vorkommen vorhanden. Die aktuellen neun Findings #2994, #2995, #2999, #3002, #3003, #4408, #4410, #116 und #117 wurden im aktuellen Quellcode bestätigt.

## Abgrenzung der Reparatur

Ein Link durfte nur archiviert werden, wenn alle Voraussetzungen erfüllt waren:

- Aktuelles Finding eindeutig im Quellcode bestätigt; Literal, keine dynamische Aufgabe.
- Genau ein bereits verknüpfter Key passt zum aktuellen SuggestedKey.
- Alle beteiligten Keys open/pending, ohne TranslationKey, Freigabe oder eigene scalar/dynamic Key-Werte.
- Die Timeline belegt einen vom alten Foundation-Scanner durchgeführten Dateiwechel vom SuggestedKey des fremden Keys weg.
- Dieser fremde Key hat inzwischen eine andere aktive, im aktuellen Code bestätigte Fundstelle in einer anderen Datei, mit passendem SuggestedKey und identischem Ausdruck.
- Der zu entfernende Link ist eine automatisch erzeugte Candidate-Verknüpfung.

Die Keys wurden ausdrücklich **nicht zusammengeführt**. Ihre eigenständigen Aufgaben bleiben bestehen. Nur die falsche Verknüpfung zum fremden Finding erhält `obsolete` und den Grund `historical_cross_file_finding_reuse`.

## Ergebnis

- 492 Links bei 235 Findings archiviert.
- 492 Timeline-Einträge `key_assignment_reconciled`, mit ursprünglichen/neuen Link-Daten, historischem Beleg und IDs der unabhängigen aktuellen Fundstellen.
- 199 Findings damit vollständig von Mehrfachzuordnungen bereinigt; 36 der bearbeiteten Findings besitzen noch andere ungeklärte Zusatzlinks.
- Von 298 unbenannten Mehrfachzuordnungen verbleiben 99 Findings mit 143 Zusatzlinks. Dort fehlt eine unabhängige bestätigte Fundstelle. Kein Key wurde deshalb auf Verdacht archiviert.
- Zusammen mit den zuvor separat untersuchten 32 benannten Konfliktfällen verbleiben insgesamt 131 Findings mit Mehrfachzuordnungen.

Die transaktionalen Vorher-/Nachher-Prüfsummen bestätigen unveränderte SourceFiles, Findings, Keys, Reviews, scalar/dynamic Key-Werte, Lang-Werte und unbeteiligte Links. Die Quelldateien wurden nicht geändert. Der erneute Plan ergibt **0 Aktionen**, 99 unbenannte Gruppen und dieselben 143 zurückgestellten Links.

## Validierung und Fortsetzung

15 gezielte Pest-Regressionstests bestanden, 210 Assertions. Der ergänzte Test erfasst gleiche Literale mehrfach in drei Dateien, hält die Findings und Keys getrennt und prüft nach zwei Scans in unterschiedlicher Dateireihenfolge, dass eine archivierte fremde Verknüpfung unverändert inaktiv bleibt. Keine Laufzeitlogik geändert; kein vollständiger Testlauf.

Als Nächstes sind für die zurückgestellten Links die ehemaligen Quellkontexte zu prüfen: Existiert das Literal noch, fehlt lediglich ein eigenständiges Finding, ist es inzwischen gekeyt, verschoben oder entfernt? Eine fehlende unabhängige Fundstelle allein ist keine Archivierungsentscheidung.

- [Reparaturskript](reconcile-unkeyed-source-links-2026-10-17.php): standardmäßig lesender Plan; Apply verlangt exakt denselben frisch reproduzierten Plan unter Tabellensperren.
- [Aktionsliste, Timeline-IDs und Prüfsummen](unkeyed-source-link-reconciliation-2026-10-17.json).
