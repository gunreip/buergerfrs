# TranslationList / Foundation: Werteabgleich vom 16.10.2026

Rein lesende Bestandsaufnahme mit `translation-workbench:compare-values --json`. Der Vergleich verwendet eine konsistente, schreibgeschützte PostgreSQL-Transaktion. Keine Übernahme, Freigabe, Veröffentlichung oder Statusänderung.

## Ergebnis

| Befund | Key-/Sprachpaare |
| --- | ---: |
| Legacy ohne Text, Foundation mit Text | 412 |
| Identischer Wert und vergleichbarer Status | 1131 |
| Identischer Wert, unterschiedlicher Status | 246 |
| Nur Legacy, ohne Text und ohne Mehrdeutigkeit | 6271 |
| Nur Legacy, Text bereits obsolete | 10 |
| Mehrere Zeilen auf mindestens einer Seite | 102 |
| Unterschiedliche, nicht leere Texte | 10 |
| Nur Foundation | 336 |

Diese Kategorien teilen alle 8,518 Key-/Sprachpaare überschneidungsfrei auf. Mehrfachzeilen haben Vorrang vor jeder inhaltlichen Bewertung. `null` und Leerstring bleiben im Vergleich verschieden; Leerzeichen werden nicht entfernt.

Die ursprünglichen **6.302 Legacy-only-Paare sind keine 6.302 fehlenden Übersetzungen**: 6.271 eindeutige Paare ohne Text, zehn obsolete Texte und 21 Paare mit mehreren Legacy-Zeilen. Es gibt in dieser Momentaufnahme keinen eindeutigen, nicht obsoleten Legacy-only-Text als unmittelbaren Übernahmekandidaten.

Die 21 mehrdeutigen Legacy-only-Paare betreffen drei Keys in jeweils sieben regionalen deutschen Sprachen: `admin.translation_list.modal_history.event`, `admin.translation_list.modal_history.events` und `admin.translation_list.table.history`. Je Paar existieren ein Text mit Status `ok` und ein leerer Eintrag mit Status `missing`; beide tragen `is_base_duplicate=true`. Das Flag ist kein Beleg für eine Freigabe oder für die richtige fachliche Zuordnung.

Unter `en` und `de` bestehen insgesamt 24 Legacy-only-Paare: 14 leere missing-Einträge und zehn obsolete Texte. Die fünf obsoleten Keys betreffen alte Header-/Sidebar-Einträge (Search, Countries, Fallback Reports, Client, Create). IDs und unveränderte Werte stehen im Evidenzbericht.

## Zehn tatsächliche Textabweichungen

| Sprache | Key | TranslationList | Foundation |
| --- | --- | --- | --- |
| de | ui.all.all-entries | Alle | All entries |
| de | ui.all.all-types | Alle | Alle Typen |
| de | ui.button.activate_deactivate.activate_language | Sprache aktivieren | :language aktivieren |
| de | ui.button.activate_deactivate.deactivate_language | Deaktivieren: Sprache | Deaktivieren: :language |
| de | ui.table.pagination.to | zu | bis |
| de | ui.type | Type | Typ |
| en | admin.permission_list.filter.filter_by_category | v | Filter by category |
| en | ui.all.all-entries | All | All entries |
| en | ui.all.all-types | All | All types |
| en | ui.states.all-statuses | All Statuses | All statuses |

Insbesondere `de / ui.all.all-entries` enthält auf Foundation-Seite englischen Text. Das ist ein Prüfhinweis, keine Berechtigung, den Wert automatisch zu ersetzen. Auch Unterschiede bei Platzhaltern müssen anhand der Verwendung geprüft werden.

## Nächste Entscheidungen

1. Für die zehn Textabweichungen aktive Fundstellen, zuständige Findings und Veröffentlichungs-/Freigabehistorie feststellen. Erst danach einen fachlich richtigen Wert wählen.
2. Die 102 Paare mit Mehrfachzeilen getrennt prüfen: 81 auf beiden Seiten vorhandene mehrdeutige Paare und 21 nur auf Legacy-Seite. Keine automatische Auswahl der ersten Zeile.
3. Bei 246 identischen Werten mit Statusabweichung den Lebenszyklus prüfen. Textgleichheit bedeutet weder gleiche Gültigkeit noch gleiche Freigabe.
4. Leere oder obsolete Legacy-only-Einträge erst nach dieser Prüfung behandeln; sie begründen keine neue Übersetzungsaufgabe allein durch ihre Existenz.

## Nachweis und Grenzen

[Evidenz mit IDs, Originalwerten und Status](legacy-foundation-value-review-2026-10-16.json) enthält die oben beschriebenen Prüffälle einschließlich aller en/de Legacy-only-Paare. Die Auswahl und der SHA-256 des vollständigen Rohberichts sind hinterlegt. Der Rohbericht ist mit dem genannten Lesebefehl erneut erzeugbar; ein späterer Lauf kann einen anderen Datenstand abbilden.

Der Vergleich normalisiert Locale-Schreibweisen, nicht die Texte. Legacy `ok` wird ausschließlich zum Statusvergleich als `active` betrachtet. Die 503 Statusabweichungen des Rohberichts überschneiden sich mit seinen Textkategorien und sind keine zusätzliche Anzahl fehlender Übersetzungen.

Sprachdateien, Runtime-Fallbacks, aktive Quellcodeverwendung, dynamische Optionswerte und fachliche Eigentümerschaft sind nicht Gegenstand dieses Abgleichs. Aus diesen Zahlen folgt weder eine Löschentscheidung noch eine Migrations- oder Veröffentlichungsfreigabe.
