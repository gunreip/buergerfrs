# Kontext unzugeordneter Sprachwerte – 19.10.2026

Die 397 Werte mit `unassigned_key` wurden auf einer neuen Kopie des geprüften Baseline-Dumps untersucht. Grundlage sind die gespeicherten Optionsargument-Nachweise. Datenbankwerte, Key-Zuordnungen und Freigaben wurden nicht geändert; nur der private Vorschau-Export und dieser Bericht wurden ergänzt.

| Ergebnis | Werte | Verschiedene Keys |
|---|---:|---:|
| Optionsquelle belegt | 184 | 92 |
| Verwendung weiterhin offen | 213 | 101 |

Die 184 Optionswerte sind jeweils 92 deutsche und englische Werte. Ein exakter Optionsgruppen-Key aus einem gescannten Aufruf und eine kanonisch kodierte Optionsidentität sind Voraussetzung. Ähnlich beginnende Namen, leere Identitäten, verschachtelte unkodierte Punkte oder abweichende Prozentkodierungen genügen nicht. Der Klassifikator erhält mehrere Quellenaufrufe getrennt.

Ein belegter Optionsgruppen-Aufruf beweist ausdrücklich nicht, dass jede veröffentlichte Option zur Laufzeit benutzt wird. Die bisherigen Datenbank-Zuordnungen bleiben deshalb unangetastet. Die 92 Keys sind nur der Optionsanteil des bisher unzugeordneten Bestands, nicht die vollständige Zahl aller Optionswerte in der Sicherung.

Die verbleibenden 213 Werte verteilen sich auf en 100, de 96 und 17 regionale Werte. Keine eindeutige Optionsquelle nachgewiesen bedeutet weder „unbenutzt“ noch „darf gelöscht werden“. Weitere Quellen, Aufrufe und dynamische Verwendungen müssen gesondert geprüft werden.

## Sichtbar in der Vorschau

Unter Translation Workbench → Workflow → **Baseline preview** bleibt der Status „Unassigned key“ erhalten. Ergänzend erscheint „Option source identified“ mit Optionsgruppen-Key, dekodierter Identität und einem Flux-Accordion der tatsächlichen Aufrufe. Es enthält Dateipfad/Zeile sowie Options- und Fallback-Ausdruck. Für andere unzugeordnete Werte wird die offene Zuordnung ausdrücklich erklärt.

Beispiel zum Anschauen: `admin.partials.role_list.modal_create.role_badge_variant_options.options.outline`. Ein Beispiel ohne belegte Optionsquelle ist `account.preferences.current_user.nickname`.

## Prüfung

Zehn Klassifikator-Assertions prüfen exakte Gruppen, ähnlich benannte Gruppen, Kodierung, leere/ungültige Identitäten, erhaltene Mehrfachaufrufe und fehlende Evidenz. Die Vorschau-Pest-Suite besteht jetzt aus fünf Tests mit 23 Assertions, einschließlich Optionsquellen, angezeigten Ausdrücken und dem Hinweis, dass offene Zuordnung keine Löschfreigabe bedeutet. Kein visueller Browser-Abgleich durchgeführt.

Der Export enthält weiterhin 972 Keys und die bisherigen drei Locale-Ketten. Alle neun Locales wurden für die Analyse der unzugeordneten Werte berücksichtigt; die Vorschau selbst zeigt weiterhin nur de-at/de/en. Der Testcluster wurde nach dem Export beendet. [Einzelnachweise und Prüfsummen](unassigned-context-2026-10-19.json).
