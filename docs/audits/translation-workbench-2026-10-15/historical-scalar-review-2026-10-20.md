# Historische Übersetzungswerte – Abgleich vom 20.10.2026

## Umfang und Ergebnis

Verglichen wurden zwei historische Tabellen aus der verifizierten Sicherung mit den PHP-Sprachdateien **derselben Sicherung**. Keine Produktionsverbindung, keine Importe und keine Freigabeentscheidungen. Der temporäre PostgreSQL-Cluster ist gestoppt; die Abfragen liefen in einer READ ONLY-Transaktion.

| Tabelle | Zeilen | Exakt identisch | Abweichender Text | Kein exakter Veröffentlichungsnachweis | Kein String-Wert |
| --- | ---: | ---: | ---: | ---: | ---: |
| translation_values | 8.284 | 1.455 | 1 | 124 | 6.704 |
| translation_workbench_lang_values | 2.216 | 1.823 | 9 | 384 | 0 |
| Gesamt | 10.500 | 3.278 | 10 | 508 | 6.704 |

Das sind historische Datenbankzeilen, keine Anzahl eindeutiger Übersetzungen. Beide Tabellen können denselben Key/Locale-Wert enthalten. Gleichheit bedeutet weder Freigabe noch Löschbarkeit; der historische Status bleibt erhalten.

## Exakt zugeordnete, abweichende Werte

| Tabelle | ID | Key | Locale | Historischer Status |
| --- | ---: | --- | --- | --- |
| translation_values | 8321 | `ui.type` | de | ok |
| translation_workbench_lang_values | 1735 | `ui.all.all-entries` | en | active |
| translation_workbench_lang_values | 1736 | `ui.all.all-entries` | de | active |
| translation_workbench_lang_values | 1737 | `ui.states.all-statuses` | en | active |
| translation_workbench_lang_values | 1739 | `ui.all.all-types` | en | active |
| translation_workbench_lang_values | 1740 | `ui.all.all-types` | de | active |
| translation_workbench_lang_values | 674 | `ui.button.activate_deactivate.activate_language` | de | active |
| translation_workbench_lang_values | 675 | `ui.button.activate_deactivate.deactivate_language` | de | active |
| translation_workbench_lang_values | 715 | `ui.table.pagination.to` | de | active |
| translation_workbench_lang_values | 993 | `admin.permission_list.filter.filter_by_category` | en | active |

## Grenzen und weitere Prüfung

- Identität ist der vollständige Key plus exakte Locale. Kein Fallback, keine Key-Umschreibung und keine Zuordnung nur anhand gleichen Textes.
- Das historische Feld `namespace` bezeichnet hier das erste Key-Segment bzw. die PHP-Datei. Ein Widerspruch zum vollständigen Key wird gesondert gemeldet.
- Die 508 Zeilen ohne exakten Nachweis sind **nicht automatisch unveröffentlicht**. Der Vergleich umfasst ausschließlich die gesicherten statisch lesbaren App-PHP-Sprachdateien; andere Quellen und Locale-Abweichungen müssen getrennt bewertet werden.
- Die 6.704 Zeilen ohne String-Wert werden nicht als leere Übersetzungen übernommen. Leere Strings und `"0"` bleiben dagegen echte Werte.
- Texte werden unverändert verglichen, auch wenn sie wie JSON aussehen. Keine implizite Interpretation strukturierter Inhalte.
- Noch getrennt zu prüfen: `translation_workbench_values` (952), `translation_workbench_dynamic_key_values` (200), `translation_workbench_dynamic_source_values` (247) und `translation_workbench_dynamic_values` (43). `translation_workbench_key_values` ist leer.
- Die zehn Abweichungen und die fehlenden exakten Nachweise erfordern nachvollziehbare Entscheidungen; dieser Bericht trifft keine.

## Nachweise

[Einzelzeilen mit IDs, Status und SHA-256](historical-scalar-review-2026-10-20.json) enthalten keine vollständigen Übersetzungstexte. Jede Entscheidung bleibt `null`.

[Reproduzierbarer isolierter Vergleich](rehearse-historical-scalar-values.php) prüft Sicherungs-, Staging- und Payload-Hashes sowie vollständige Tabellenzählungen.

13 Unit-Tests mit 61 Assertions prüfen unter anderem fehlenden Fallback, Namespace-Semantik, historische Status, Null, Leerstring und unveränderten Textvergleich.

## Ergänzung: Datenbankablage der Prüffälle

Mit `--persist-review-cases` wurden die Ergebnisse zusätzlich im separaten
Schema einer isolierten PostgreSQL-Datenbank gespeichert und privat gesichert:
10.500 Fälle, 518 offene Prüfaufgaben und 9.982 dokumentierte Nachweise.
Der zweite Lauf erzeugte keine doppelten Fälle oder Nachweisversionen.
Die Evidenzabfragen liefen weiterhin lesend; geschrieben wurde ausschließlich
in das neue Prüffallschema. Details: [Prüffälle und Rescan-Vertrag](review-cases-and-rescan-contract-2026-10-20.md).

Die reguläre Workbench und ihr Scanner sind damit noch nicht umgeschaltet.
