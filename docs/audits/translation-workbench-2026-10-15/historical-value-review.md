# Nachprüfung historischer Workbench-Werte

Stand: 2026-10-15T12:55:26+02:00

Der produktive Datenbankabgleich lief in einer PostgreSQL-Transaktion `REPEATABLE READ READ ONLY`. Es wurden keine Werte, Keys, Reviews oder Sprachdateien verändert. Der bisherige Vergleich bleibt unverändert streng: Ein SuggestedKey allein ist keine bestätigte Zuordnung. Diese ergänzende Untersuchung liefert Belege aus aktuellen Fundstellen und expliziten Key-Aufrufen.

## Optionswerte ohne historische Key-Zuordnung

| Legacy-Entry | Beobachtungen + englische Werte | Aktives Finding | Expliziter heutiger Key | Ergebnis |
| --- | --- | --- | --- | --- |
| 1401 | 4 + 4 | 9269 | `admin.partials.role_list.modal_create.role_badge_variant_options` | textgleich |
| 1407 | 18 + 18 | 9270 | `admin.partials.role_list.modal_edit.role_badge_color_options` | textgleich |
| 2099 | 3 + 3 | 9249 | `management.people.create_person.sections.person_core.salutation_options` | textgleich |
| 2101 | 4 + 4 | 9250 | `management.people.create_person.sections.person_core.gender_options` | textgleich |
| 2110 | 7 + 7 | 9251 | `management.people.create_person.sections.person_core.marital_status_options` | textgleich |
| 2318 | 7 + 7 | 9255 | `management.people.edit_person.sections.person_core.marital_status_options` | textgleich |

Alle 43 Optionsbezeichnungen und 43 englischen Übersetzungen sind in `dynamic_key_values` vorhanden. Geprüft wurden derselbe Quellpfad und dieselbe Position, aktive Finding-/Key-Verknüpfung, der explizite `TranslationWorkbenchOption::label`-Aufruf im aktuellen Quelltext sowie Optionsschlüssel, Locale, Wert und native Bezeichnung. Die Ausschnitte zeigen auch den jeweiligen Optionskontext. Ein bloß gleicher Text oder ähnlich benannter SuggestedKey wurde nicht als Zuordnung verwendet.

Auffällig sind veraltete SuggestedKeys: Beispielsweise verweist Entry 1401 auf `modal_edit`, obwohl seine Fundstelle `modal-create` ist und dort heute ausdrücklich der Create-Key verwendet wird. Bei Salutation/Gender findet sich dieselbe Create/Edit-Abweichung. Das erklärt, weshalb ein Abgleich nur über SuggestedKeys hier irreführend wäre.

## Alte JSON-Container

| Legacy-Wert | Container-Key | Einzeltexte | Textgleich unter vollständigem Unterkey |
| --- | --- | --- | --- |
| 663 | `ui.null` | 1 | 1 |
| 724 | `ui.address` | 1 | 1 |
| 747 | `ui.label` | 4 | 4 |
| 753 | `ui.people` | 1 | 1 |
| 765 | `ui.assignable` | 2 | 2 |
| 778 | `ui.values` | 2 | 2 |
| 803 | `ui.source` | 7 | 7 |
| 804 | `ui.stale` | 6 | 6 |
| 806 | `ui.state` | 7 | 7 |
| 810 | `ui.sources` | 1 | 1 |
| 812 | `ui.structure` | 1 | 1 |
| 813 | `ui.storage` | 1 | 1 |
| 816 | `ui.literal` | 5 | 5 |

Die 13 Container enthalten zusammen 39 Einzeltexte. Alle sind unter dem aus Container-Key und JSON-Pfad zusammengesetzten Key mit derselben Locale exakt vorhanden. Ein Vergleich des gesamten JSON-Texts mit dem heutigen skalaren Wert des Container-Keys ergibt dagegen zwangsläufig eine Abweichung. Es wurde weder JSON automatisch migriert noch ein skalarer Wert überschrieben.

## Konsequenz und Grenzen

Für die untersuchten 86 ungeklärten Zeilen und 13 Container ist kein zusätzlicher Textimport erforderlich. Zusammen mit den 896 bereits textgleichen Zeilen ist damit der Inhalt aller 995 historischen Wertzeilen nachvollzogen. Das ist keine Freigabe zum Löschen der Zeilen: Status, Autoren, Review-Historie und andere Metadaten wurden nicht als gleichwertig zertifiziert. Auch die Zuordnung historischer Optionsbeobachtungen im laufenden Discovery-Prozess ist damit noch nicht migriert.

Die drei alten Einzelcommands können als nächster Code-Schritt stillgelegt und ihre Implementierungen archiviert werden; historische Tabellen bleiben erhalten. Vorher aktive Registrierungen, direkte Aufrufer und Tests prüfen. Die Archivdateien unter `-old`/`_old` sind keine aktiven Verbraucher.

Reproduzierbare Erfassung: `php docs/audits/translation-workbench-2026-10-15/inspect-historical-values.php`. Rohbelege einschließlich IDs, Quelldatei-Hashes, Ausschnitten, Kandidaten und Werten stehen in `historical-value-evidence.json`. Der Bericht bezeichnet bewusst den geprüften Zeitpunkt; spätere Quellcode- oder Datenbankänderungen erfordern einen erneuten Abgleich.
