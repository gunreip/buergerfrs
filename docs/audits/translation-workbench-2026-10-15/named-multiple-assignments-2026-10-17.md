# Verbleibende Mehrfachzuordnungen – 17. Oktober 2026

Die Bestandsaufnahme ist ausschließlich lesend. Es wurden keine Findings, Key-Zuordnungen, Übersetzungen oder Sprachdateien verändert.

Grundlage: `named-multiple-assignments-2026-10-17.json`, reproduzierbar mit `audit-named-multiple-assignments-2026-10-17.php`.

## Ergebnis

32 aktive Findings besitzen mehrere aktive Key-Zuordnungen mit eingetragenem TranslationKey:

| Gruppe | Findings | Bedeutung |
| --- | ---: | --- |
| Zwei geprüfte Key-Datensätze, gleicher TranslationKey | 28 | Frühere Entscheidungen und Zuordnungshistorie berücksichtigen; kein automatischer Gewinner. |
| Geprüfter und ungeprüfter Key-Datensatz, gleicher TranslationKey | 3 | Findings #6731, #6716 und #4617 gezielt anhand ihrer Herkunft prüfen. |
| Unterschiedliche TranslationKeys | 1 | Finding #5093: Key #4507 (`ui.button.save.save`) und #4613 (`ui.save`). |

31 Fundstellen sind beim aktuellen Quellcode-Abgleich als `position_refined` identifizierbar. Die Fundstelle #5093 wird als `missing` gemeldet. Diese Klassifikation führt hier zu keiner Positionsänderung oder Archivierung.

18 der 28 Findings mit zwei geprüften Datensätzen betreffen `ui.type` (Keys #656 und #1212). Die bekannte strukturelle Key-Problematik bleibt zur ausdrücklichen Korrektur in der UI bestehen.

## Abgrenzung

Gleichlautende Literale können unabhängige Übersetzungsaufgaben mit unterschiedlichen SuggestedKeys und TranslationKeys sein. Textgleichheit allein rechtfertigt keine Zusammenführung. Diese Auswertung untersucht mehrere aktive Zuordnungen **derselben Fundstelle**, nicht sämtliche gleichen Literale des Projekts.

Auch ein gleicher TranslationKey bei zwei Key-Datensätzen beweist nicht, welcher Datensatz entbehrlich ist. Die gespeicherten Prüfentscheidungen sind in der JSON-Auswertung enthalten. Vor weiteren Änderungen sind Herkunft, Entscheidungen und abhängige Zuordnungen zu bewerten.

## UI-Unterstützung

Im bestehenden Prüfabschnitt des Modals „Edit translation key“ zeigt „Active key records“ jetzt eine Flux-Tabelle mit Key-ID, SuggestedKey, Status, Prüfstatus und Prüfdatum. Der Abschnitt untersucht weiterhin den jeweiligen konkreten TranslationKey. Ein Hinweis erläutert, dass verschiedene Keys zu gleichem Literal nicht automatisch zusammengeführt werden sollen.

## Prüfung

- Gezielte Inspector-/Workflow-/View-Tests: 6 Tests, 45 Assertions bestanden.
- Vollständige `ValueApprovalViewTest`-Datei: 12 Tests, 78 Assertions bestanden (enthält Überschneidungen mit dem gezielten Lauf).
- Audit-Skript: PHP-Syntaxprüfung bestanden.
- Whitespace-Prüfung der betroffenen Implementierungs- und Testdateien bestanden.
