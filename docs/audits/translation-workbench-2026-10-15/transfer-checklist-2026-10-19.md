# Übernahme-Prüfliste Translation Workbench

Auditstand: 2026-10-19T07:41:38+02:00. Keine aktuelle Datenbankabfrage.
Snapshot-SHA256: `caaec6d5a8d2723cfab94f2dc5c01894b22ded5c4f167c2f6ae0dad04ddc089f`

Diese Liste entscheidet nichts automatisch. Übereinstimmende Hashes sind ein Inhaltsnachweis, keine Freigabe.
Keine Texte werden aus Keys abgeleitet; NULL ist fehlender Nachweis und kein leerer Text.
Die alten namespace-Spalten sind Herkunftsangaben, keine bestätigten Laravel-Paket-Namespaces.
Historische/obsolete Einträge dienen nur als Nachweis; sie werden weder reaktiviert noch als aktuelle Gewinner gewählt.

| Klasse | Identitäten (exakte Locale + Key) |
| --- | ---: |
| database_only | 122 |
| file_only | 17 |
| matching_evidence | 1987 |
| missing_value_evidence | 16 |
| value_conflict | 10 |

| Zusätzlicher Prüfhinweis | Identitäten |
| --- | ---: |
| key_requires_nested_array_review | 26 |
| locale_spelling_review | 170 |
| multiple_records_per_source | 6 |

Prüfhinweise können sich überschneiden; sie sind nicht zu den Klassen zu addieren.

## Vorgehen

1. Wertabweichungen mit den aufgeführten Datensatz-IDs und Dateien prüfen. Kein pauschales „DB gewinnt“ oder „Datei gewinnt“.
2. DB-only-Werte sichern und Verwendung/Freigabe prüfen; file-only-Werte nicht als fehlende Übersetzung behandeln.
3. Locale-Varianten, mehrfach vorhandene Datensätze und Scalar-/Optionsidentitäten getrennt klären.
4. Nicht gelesene Paketdateien und dynamische Altformate ergänzen, bevor Vollständigkeit behauptet wird.
5. Vor einer Generalprobe frischen Snapshot und Dateihashes vergleichen. Die Liste ist kein Importplan.

## Offene Abdeckung

- 31 Dateien waren im Snapshot nicht als Wert-Evidenz lesbar.
- 6806 Identitäten besitzen ausschließlich historische/nicht ausgewählte Evidenz und sind keine aktuellen Übernahmekandidaten.
- Alte dynamic_values, native Werte und strukturierte Optionsbeobachtungen sind hier noch nicht vollständig wertweise enthalten.
- Die aggregierten Finding-/Link-Prüfungen enthalten keine einzelnen IDs; daraus werden keine erfundenen Korrekturaufträge erstellt.
- Framework-Dateien sind Herkunftskontext und werden nicht als Anwendungseinträge übernommen.

- active_findings: 18870 (Snapshotzählung, keine Übernahmeentscheidung).
- active_links_to_obsolete_findings: 12 (Snapshotzählung, keine Übernahmeentscheidung).
- multiple_active_named_key_ids: 27 (Snapshotzählung, keine Übernahmeentscheidung).

## Locale-Varianten

- de-AT, de-at
- de-BE, de-be
- de-CH, de-ch
- de-DE, de-de
- de-IT, de-it
- de-LI, de-li
- de-LU, de-lu

## Nicht gelesene Dateien

- `lang/vendor/backup/bg/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/es/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/no/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/fr/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/nl/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/it/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/zh_CN/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/fa/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/id/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/ko/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/en/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/ro/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/fi/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/pt/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/pl/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/da/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/tr/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/pt_BR/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/he/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/uk/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/hi/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/sk/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/ar/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/kk/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/de/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/ru/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/zh_TW/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/hr/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/cs/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/bn/notifications.php`: Outside current prototype: JSON, namespace or nested file path
- `lang/vendor/backup/ja/notifications.php`: Outside current prototype: JSON, namespace or nested file path

## value_conflict (10)

<details>
<summary>Einträge mit Herkunft anzeigen</summary>

| Review-ID | Locale | Key | Herkunft / Datensatz / Wert-SHA256 (gekürzt) | Zusätzliche Prüfung |
| --- | --- | --- | --- | --- |
| 3a6ba8b355fbb67f | de | ui.all.all-entries | file #lang/de/ui.php [3801a35c5182]; legacy #7655 [3801a35c5182]; workbench #1736 [75a00fa7761b] |  |
| 2d6e2e67f08c5ff9 | de | ui.all.all-types | file #lang/de/ui.php [3801a35c5182]; legacy #7664 [3801a35c5182]; workbench #1740 [83162aa995db] |  |
| 2f3678707683a2e6 | de | ui.button.activate_deactivate.activate_language | file #lang/de/ui.php [9a78a91da42f]; legacy #6607 [9a78a91da42f]; workbench #674 [fafa5af9abe4] |  |
| 2910e45a656b284a | de | ui.button.activate_deactivate.deactivate_language | file #lang/de/ui.php [fde3de43ecaf]; legacy #6489 [fde3de43ecaf]; workbench #675 [fd373f88e418] |  |
| 5b80abfbeaa0cfb6 | de | ui.table.pagination.to | file #lang/de/ui.php [82cefa8283d3]; legacy #6508 [82cefa8283d3]; workbench #715 [96147cdbfd69] |  |
| d3255e58569e8f1a | de | ui.type | file #lang/de/ui.php [1b024183cb33]; legacy #8321 [baaddf70fb5d]; workbench #1785 [1b024183cb33] | key_requires_nested_array_review |
| a417059e2aa4fd45 | en | admin.permission_list.filter.filter_by_category | file #lang/en/admin.php [4c94485e0c21]; legacy #1444 [4c94485e0c21]; workbench #993 [a9b0a2e6303f] |  |
| 383c6ef1ed3a4760 | en | ui.all.all-entries | file #lang/en/ui.php [a52ace420f21]; legacy #7663 [a52ace420f21]; workbench #1735 [75a00fa7761b] |  |
| bb25614e437bd4c0 | en | ui.all.all-types | file #lang/en/ui.php [a52ace420f21]; legacy #7672 [a52ace420f21]; workbench #1739 [f10988e79e8d] |  |
| 7bcc0fe503561119 | en | ui.states.all-statuses | file #lang/en/ui.php [05f567125eb0]; legacy #8293 [05f567125eb0]; workbench #1737 [8ee57323a6f2] |  |

</details>

## missing_value_evidence (16)

<details>
<summary>Einträge mit Herkunft anzeigen</summary>

| Review-ID | Locale | Key | Herkunft / Datensatz / Wert-SHA256 (gekürzt) | Zusätzliche Prüfung |
| --- | --- | --- | --- | --- |
| 26513d1a444bf9db | de | admin.permissions.filters.assignment.label | legacy #5 [NULL] |  |
| 5d7f0f8be36769f9 | de | admin.permissions.filters.system.label | legacy #17 [NULL] |  |
| f034e8352d71c4e9 | de | admin.permissions.filters.title | legacy #19 [NULL] |  |
| bbced55646c087e4 | de | admin.permissions.labels.sort_order | legacy #23 [NULL] |  |
| 69d8b2e5bd2e8b92 | de | admin.permissions.modals.edit.title | file #lang/de/admin.php [481c4f665586]; legacy #53 [NULL]; workbench #238 [481c4f665586] |  |
| 6c79a3f5c398a586 | de | admin.permissions.overview.title | legacy #83 [NULL] |  |
| 2102b76b467cbbfd | de | admin.permissions.table.columns.assigned_roles | legacy #93 [NULL] |  |
| 7ae70b86ca574e8c | de | ui.actions.save | legacy #107 [NULL] |  |
| 77b0cc4c87586b06 | en | admin.permissions.filters.assignment.label | legacy #6 [NULL] |  |
| 049fec9c016d3692 | en | admin.permissions.filters.system.label | legacy #18 [NULL] |  |
| ea0e85a5e2e93fb6 | en | admin.permissions.filters.title | legacy #20 [NULL] |  |
| 8a40020a518eb23b | en | admin.permissions.labels.sort_order | legacy #24 [NULL] |  |
| 1895614b3eaf887f | en | admin.permissions.modals.edit.title | file #lang/en/admin.php [3eb352609d09]; legacy #54 [NULL]; workbench #1040 [3eb352609d09] |  |
| f122e6c9a510ee15 | en | admin.permissions.overview.title | legacy #84 [NULL] |  |
| a4badf8a71e67d87 | en | admin.permissions.table.columns.assigned_roles | legacy #94 [NULL] |  |
| ff8ce15b681fda7a | en | ui.actions.save | legacy #108 [NULL] |  |

</details>

## database_only (122)

<details>
<summary>Einträge mit Herkunft anzeigen</summary>

| Review-ID | Locale | Key | Herkunft / Datensatz / Wert-SHA256 (gekürzt) | Zusätzliche Prüfung |
| --- | --- | --- | --- | --- |
| 2df875a7f5fd5d6a | de | management.people.edit_person.sections.documents.add_modal.document_category_options.options.correspondence | dynamic_workbench #116 [18dc9a30fc6a] |  |
| c293b7e09478aaf9 | de | management.people.edit_person.sections.documents.add_modal.document_category_options.options.documentation | dynamic_workbench #118 [c2ec0b49f5bf] |  |
| db42a3b3aa299bed | de | management.people.edit_person.sections.documents.add_modal.document_category_options.options.identity | dynamic_workbench #120 [09841f4a4ee7] |  |
| 5034613dbf81493d | de | management.people.edit_person.sections.documents.add_modal.document_category_options.options.insurance | dynamic_workbench #122 [b6c15460e774] |  |
| 26d4735eaaa32ad6 | de | management.people.edit_person.sections.documents.add_modal.document_category_options.options.medical | dynamic_workbench #124 [ee82f298717a] |  |
| 85091eb2a887a570 | de | management.people.edit_person.sections.documents.add_modal.document_category_options.options.other | dynamic_workbench #126 [9f3d5f8d94cf] |  |
| f000cfcabbb49f2e | de | management.people.edit_person.sections.documents.add_modal.document_category_options.options.residence | dynamic_workbench #128 [58c2b0c92aeb] |  |
| 17b1ea480a4b0b16 | de | management.people.edit_person.sections.documents.add_modal.document_category_options.options.tax | dynamic_workbench #130 [4045fd1653e6] |  |
| 02b33a26a99b0694 | de | management.people.edit_person.sections.documents.archive_modal.category | workbench #1537 [52f2a87a419c] |  |
| c8f2f03ee3ce91df | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.dev_playground | workbench #1612 [9b2b10db427e] |  |
| 2181784dde411211 | de | ui.assignable-through-ui | workbench #1998 [f34512aa29dd] | key_requires_nested_array_review |
| 8697ed7d366dc6ae | de | ui.label | workbench #1928 [297470ed588d] | key_requires_nested_array_review |
| 10c3624544917303 | de | ui.label.label-testing-2 | workbench #1931 [5f72b0be29c1] |  |
| 217305f723069e9f | de | ui.literal.literal-testing | workbench #1921 [c27e5fe45509] |  |
| aa06e50d7f298cc1 | de | ui.literal.literal-testing-2 | workbench #1927 [8ad22f360fdb] |  |
| eac58bb9176b3815 | de-AT | admin.permissions.actions.edit_role_permissions | legacy #383 [74f749437ff2] | locale_spelling_review |
| debacfd2d62b8d90 | de-AT | admin.translation_list.filter.status_missing | legacy #6320 [7210a0ef6114] | locale_spelling_review |
| 31cb1902f614d6f8 | de-AT | admin.translation_list.filter.status_native | legacy #3620 [ce37d9866725] | locale_spelling_review |
| ee10d5c412275b82 | de-AT | admin.translation_list.filter.type_backfill | legacy #5892 [0979de66414f] | locale_spelling_review |
| cb8198f730804ec1 | de-AT | admin.translation_list.filter.type_native | legacy #5914 [0dd528f7136f] | locale_spelling_review |
| c624c2ff82f8c1d8 | de-AT | admin.translation_list.meta.total_keys | legacy #5773 [1d8dbdcddf00] | locale_spelling_review |
| 15dd1dd2cd51ebf7 | de-AT | admin.translation_list.modal_edit.sub_language_values | legacy #5508 [b133f5c46c37] | locale_spelling_review |
| 6550b700d1c01e72 | de-AT | admin.translation_list.modal_history.event | legacy #5855 [af9e77ea9137] | locale_spelling_review |
| bc7cc061a0bd84db | de-AT | admin.translation_list.modal_history.events | legacy #5839 [39ad234261d7] | locale_spelling_review |
| 43f45499abe261ca | de-AT | admin.translation_list.table.history | legacy #5879 [4eaa806899cc] | locale_spelling_review |
| 55dfcdafd34223eb | de-AT | admin.translation_list.table.open_history | legacy #5786 [f16a78d7e327] | locale_spelling_review |
| 31a7038f05d37c5d | de-AT | admin.translation_list.table.target_sub_language_values | legacy #5903 [f91397124b2d] | locale_spelling_review |
| 0d149c9d718a944e | de-AT | ui.table.per_page_selector.per_page | legacy #6498 [fe7c4b7cd613] | locale_spelling_review |
| 157556853e99cf59 | de-BE | admin.translation_list.filter.status_missing | legacy #6321 [7210a0ef6114] | locale_spelling_review |
| c49c9637acb42cda | de-BE | admin.translation_list.filter.status_native | legacy #3621 [ce37d9866725] | locale_spelling_review |
| bf4ba2c78898a01c | de-BE | admin.translation_list.filter.type_backfill | legacy #5893 [0979de66414f] | locale_spelling_review |
| 3716d9a416904ab8 | de-BE | admin.translation_list.filter.type_native | legacy #5915 [0dd528f7136f] | locale_spelling_review |
| bca91dece849cc6e | de-BE | admin.translation_list.meta.total_keys | legacy #5774 [1d8dbdcddf00] | locale_spelling_review |
| ee9c6fa65780dea8 | de-BE | admin.translation_list.modal_edit.sub_language_values | legacy #5509 [b133f5c46c37] | locale_spelling_review |
| 68876ce9380ce17b | de-BE | admin.translation_list.modal_history.event | legacy #5856 [af9e77ea9137] | locale_spelling_review |
| b981e16b3d4da10c | de-BE | admin.translation_list.modal_history.events | legacy #5840 [39ad234261d7] | locale_spelling_review |
| 99d2d9ca3eebd53c | de-BE | admin.translation_list.table.history | legacy #5880 [4eaa806899cc] | locale_spelling_review |
| 4b0626e1bbe359bf | de-BE | admin.translation_list.table.open_history | legacy #5787 [f16a78d7e327] | locale_spelling_review |
| 4c3aba5a0dadde3f | de-BE | admin.translation_list.table.target_sub_language_values | legacy #5904 [f91397124b2d] | locale_spelling_review |
| aa498a2ab179df67 | de-BE | ui.table.per_page_selector.per_page | legacy #6499 [fe7c4b7cd613] | locale_spelling_review |
| 2bcc1f87592399b2 | de-CH | admin.permissions.actions.edit_role_permissions | legacy #384 [74f749437ff2] | locale_spelling_review |
| cc28ad372df0c927 | de-CH | admin.translation_list.filter.status_missing | legacy #6322 [7210a0ef6114] | locale_spelling_review |
| d9c74a396f448f3f | de-CH | admin.translation_list.filter.status_native | legacy #3622 [ce37d9866725] | locale_spelling_review |
| 6df9135e22383646 | de-CH | admin.translation_list.filter.type_backfill | legacy #5894 [0979de66414f] | locale_spelling_review |
| 9ba5435fc4d080a0 | de-CH | admin.translation_list.filter.type_native | legacy #5916 [0dd528f7136f] | locale_spelling_review |
| 1c39a94beb81d646 | de-CH | admin.translation_list.meta.total_keys | legacy #5775 [1d8dbdcddf00] | locale_spelling_review |
| 1dc9b3a6fd29b623 | de-CH | admin.translation_list.modal_edit.sub_language_values | legacy #5510 [b133f5c46c37] | locale_spelling_review |
| 1dc70ca5eba9808b | de-CH | admin.translation_list.modal_history.event | legacy #5857 [af9e77ea9137] | locale_spelling_review |
| 2848bd91cd5aeba2 | de-CH | admin.translation_list.modal_history.events | legacy #5841 [39ad234261d7] | locale_spelling_review |
| bb18d50c3c8a7636 | de-CH | admin.translation_list.table.history | legacy #5881 [4eaa806899cc] | locale_spelling_review |
| a3c96214a7a605f1 | de-CH | admin.translation_list.table.open_history | legacy #5788 [f16a78d7e327] | locale_spelling_review |
| 950143a3fab9fb3c | de-CH | admin.translation_list.table.target_sub_language_values | legacy #5905 [f91397124b2d] | locale_spelling_review |
| c0a8582f522642b8 | de-CH | ui.table.per_page_selector.per_page | legacy #6500 [fe7c4b7cd613] | locale_spelling_review |
| eaaef6e820b4d773 | de-DE | admin.translation_list.filter.status_missing | legacy #6323 [7210a0ef6114] | locale_spelling_review |
| b892eb6fc5c4d598 | de-DE | admin.translation_list.filter.status_native | legacy #3623 [ce37d9866725] | locale_spelling_review |
| b8eeffb811a7d950 | de-DE | admin.translation_list.filter.type_backfill | legacy #5895 [0979de66414f] | locale_spelling_review |
| 1342fc91a2fb815d | de-DE | admin.translation_list.filter.type_native | legacy #5917 [0dd528f7136f] | locale_spelling_review |
| a502fbf01c2d4894 | de-DE | admin.translation_list.filter.workflow_history | legacy #3611 [56a505df5905] | locale_spelling_review |
| a4bf86b958f3e44c | de-DE | admin.translation_list.meta.backfill | legacy #2776 [6fce93c4b2a2] | locale_spelling_review |
| e757779f0927cd6b | de-DE | admin.translation_list.meta.only_problems | legacy #2787 [3d84a59b4930] | locale_spelling_review |
| cf23e9b0c02d4d60 | de-DE | admin.translation_list.meta.total_keys | legacy #5776 [1d8dbdcddf00] | locale_spelling_review |
| 7b231738399902c2 | de-DE | admin.translation_list.modal.key_suggestion_check | legacy #3443 [f458163a5a66] | locale_spelling_review |
| 983ce3ee9101b327 | de-DE | admin.translation_list.modal_edit.sub_language_values | legacy #5511 [b133f5c46c37] | locale_spelling_review |
| 504bcef4243c957e | de-DE | admin.translation_list.modal_edit.usage_locations | legacy #3456 [902c8c04704b] | locale_spelling_review |
| 33a93ad649118190 | de-DE | admin.translation_list.modal_history.event | legacy #5858 [af9e77ea9137] | locale_spelling_review |
| 0f477a9665805fb8 | de-DE | admin.translation_list.modal_history.events | legacy #5842 [39ad234261d7] | locale_spelling_review |
| fc31e8f371e79cb1 | de-DE | admin.translation_list.table.history | legacy #5882 [4eaa806899cc] | locale_spelling_review |
| af98c80cdd5ab1bc | de-DE | admin.translation_list.table.open_history | legacy #5789 [f16a78d7e327] | locale_spelling_review |
| a0d858569079ceb9 | de-DE | admin.translation_list.table.target_sub_language_values | legacy #5906 [f91397124b2d] | locale_spelling_review |
| 49ffae977d16dea2 | de-DE | ui.table.per_page_selector.per_page | legacy #6501 [fe7c4b7cd613] | locale_spelling_review |
| f8f9bea790b1f349 | de-IT | admin.permissions.actions.edit_role_permissions | legacy #385 [74f749437ff2] | locale_spelling_review |
| abfa0ae973d00e1c | de-IT | admin.translation_list.filter.status_missing | legacy #6324 [7210a0ef6114] | locale_spelling_review |
| 12f1156321adf552 | de-IT | admin.translation_list.filter.status_native | legacy #3624 [ce37d9866725] | locale_spelling_review |
| 2028b73972e41f2f | de-IT | admin.translation_list.filter.type_backfill | legacy #5896 [0979de66414f] | locale_spelling_review |
| 7cef0ab9145d299c | de-IT | admin.translation_list.filter.type_native | legacy #5918 [0dd528f7136f] | locale_spelling_review |
| 6e5a6f621b9d9be2 | de-IT | admin.translation_list.meta.total_keys | legacy #5777 [1d8dbdcddf00] | locale_spelling_review |
| 2ba88039dfca0a1d | de-IT | admin.translation_list.modal_edit.sub_language_values | legacy #5512 [b133f5c46c37] | locale_spelling_review |
| 7ca81527c48cc05b | de-IT | admin.translation_list.modal_history.event | legacy #5859 [af9e77ea9137] | locale_spelling_review |
| 8a0646a3dfb9f19f | de-IT | admin.translation_list.modal_history.events | legacy #5843 [39ad234261d7] | locale_spelling_review |
| af25027157fbc25c | de-IT | admin.translation_list.table.history | legacy #5883 [4eaa806899cc] | locale_spelling_review |
| def59d1b2595a0ca | de-IT | admin.translation_list.table.open_history | legacy #5790 [f16a78d7e327] | locale_spelling_review |
| f3912dc6cd34763e | de-IT | admin.translation_list.table.target_sub_language_values | legacy #5907 [f91397124b2d] | locale_spelling_review |
| eed700a9ea780b57 | de-IT | ui.table.per_page_selector.per_page | legacy #6502 [fe7c4b7cd613] | locale_spelling_review |
| a2de04df34bdbf54 | de-LI | admin.translation_list.filter.status_missing | legacy #6325 [7210a0ef6114] | locale_spelling_review |
| 5a24a948ce101770 | de-LI | admin.translation_list.filter.status_native | legacy #3625 [ce37d9866725] | locale_spelling_review |
| 958b598a674fb48c | de-LI | admin.translation_list.filter.type_backfill | legacy #5897 [0979de66414f] | locale_spelling_review |
| 53fbde6c06cef9d4 | de-LI | admin.translation_list.filter.type_native | legacy #5919 [0dd528f7136f] | locale_spelling_review |
| bf4a6208231e4cef | de-LI | admin.translation_list.meta.total_keys | legacy #5778 [1d8dbdcddf00] | locale_spelling_review |
| d5a4dfe7642205f4 | de-LI | admin.translation_list.modal_edit.sub_language_values | legacy #5513 [b133f5c46c37] | locale_spelling_review |
| 5e95d58a01d6302d | de-LI | admin.translation_list.modal_history.event | legacy #5860 [af9e77ea9137] | locale_spelling_review |
| 633af3c6e28a3e81 | de-LI | admin.translation_list.modal_history.events | legacy #5844 [39ad234261d7] | locale_spelling_review |
| 1234d4851b257d90 | de-LI | admin.translation_list.table.history | legacy #5884 [4eaa806899cc] | locale_spelling_review |
| b17e86bb3e9b140e | de-LI | admin.translation_list.table.open_history | legacy #5791 [f16a78d7e327] | locale_spelling_review |
| ddadd9d63be60d54 | de-LI | admin.translation_list.table.target_sub_language_values | legacy #5908 [f91397124b2d] | locale_spelling_review |
| 81f5c5e5d395d0c2 | de-LI | ui.table.per_page_selector.per_page | legacy #6503 [fe7c4b7cd613] | locale_spelling_review |
| 2acd120e61b3e07e | de-LU | admin.translation_list.filter.status_missing | legacy #6326 [7210a0ef6114] | locale_spelling_review |
| 1ceab48071971380 | de-LU | admin.translation_list.filter.status_native | legacy #3626 [ce37d9866725] | locale_spelling_review |
| 386e081b4b41bb76 | de-LU | admin.translation_list.filter.type_backfill | legacy #5898 [0979de66414f] | locale_spelling_review |
| 93ee4068cbd01fef | de-LU | admin.translation_list.filter.type_native | legacy #5920 [0dd528f7136f] | locale_spelling_review |
| 38ac173dba979398 | de-LU | admin.translation_list.meta.total_keys | legacy #5779 [1d8dbdcddf00] | locale_spelling_review |
| eef2b435ffc8ec13 | de-LU | admin.translation_list.modal_edit.sub_language_values | legacy #5514 [b133f5c46c37] | locale_spelling_review |
| 60b617374087b490 | de-LU | admin.translation_list.modal_history.event | legacy #5861 [af9e77ea9137] | locale_spelling_review |
| ef7467ec740b54ce | de-LU | admin.translation_list.modal_history.events | legacy #5845 [39ad234261d7] | locale_spelling_review |
| 7f9dbe4fce3be546 | de-LU | admin.translation_list.table.history | legacy #5885 [4eaa806899cc] | locale_spelling_review |
| baa2c6d013e3e646 | de-LU | admin.translation_list.table.open_history | legacy #5792 [f16a78d7e327] | locale_spelling_review |
| 2ddb3b300667c90d | de-LU | admin.translation_list.table.target_sub_language_values | legacy #5909 [f91397124b2d] | locale_spelling_review |
| 4192dbca500b9d63 | de-LU | ui.table.per_page_selector.per_page | legacy #6504 [fe7c4b7cd613] | locale_spelling_review |
| d5fcc0a1818fe0a4 | en | management.people.edit_person.sections.documents.add_modal.document_category_options.options.correspondence | dynamic_workbench #115 [dcdd73c5c353] |  |
| ccaf8841c0c3a1b9 | en | management.people.edit_person.sections.documents.add_modal.document_category_options.options.documentation | dynamic_workbench #117 [c205924de0fe] |  |
| 5da97a9cd9b6fa5a | en | management.people.edit_person.sections.documents.add_modal.document_category_options.options.identity | dynamic_workbench #119 [999f23fcd7be] |  |
| 87687d47923765b8 | en | management.people.edit_person.sections.documents.add_modal.document_category_options.options.insurance | dynamic_workbench #121 [4594b737568a] |  |
| 97664b10ec008dac | en | management.people.edit_person.sections.documents.add_modal.document_category_options.options.medical | dynamic_workbench #123 [fec80bef0d8a] |  |
| 41e68a507a517cb0 | en | management.people.edit_person.sections.documents.add_modal.document_category_options.options.other | dynamic_workbench #125 [f97e9da0e3b8] |  |
| 1cdf88d95cf81e26 | en | management.people.edit_person.sections.documents.add_modal.document_category_options.options.residence | dynamic_workbench #127 [a2b57adda68b] |  |
| 816ffd6c73967ce4 | en | management.people.edit_person.sections.documents.add_modal.document_category_options.options.tax | dynamic_workbench #129 [47e2886ee5d3] |  |
| 2fb105d6cf617a38 | en | management.people.edit_person.sections.documents.archive_modal.category | workbench #1536 [edb2cd3b74c9] |  |
| 13df2b8801821054 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.dev_playground | workbench #1611 [414c3300a2dd] |  |
| 159317d087ba3109 | en | ui.assignable-through-ui | workbench #2002 [4abb94c0a83d] | key_requires_nested_array_review |
| f6285d7a705cc1e8 | en | ui.label | workbench #1929 [7f5cabd82b20] | key_requires_nested_array_review |
| 575e8f063470ddba | en | ui.label.label-testing-2 | workbench #1930 [fe69e3478ebb] |  |
| bb2636d9bcded744 | en | ui.literal.literal-testing | workbench #1920 [3ff1863ee7a8] |  |
| 03e9cc978919a7f2 | en | ui.literal.literal-testing-2 | workbench #1926 [1f6cd111db08] |  |

</details>

## file_only (17)

<details>
<summary>Einträge mit Herkunft anzeigen</summary>

| Review-ID | Locale | Key | Herkunft / Datensatz / Wert-SHA256 (gekürzt) | Zusätzliche Prüfung |
| --- | --- | --- | --- | --- |
| fd4c22f8795c4894 | de-at | admin.permissions.actions.edit_role_permissions | file #lang/de-at/admin.php [74f749437ff2] | locale_spelling_review |
| fe16715f884aeb48 | de-at | admin.translation_list.table.open_history | file #lang/de-at/admin.php [f16a78d7e327] | locale_spelling_review |
| 51b61973b5bdebc2 | de-at | ui.table.per_page_selector.per_page | file #lang/de-at/ui.php [fe7c4b7cd613] | locale_spelling_review |
| 1cf3ad687d2b4cb3 | de-be | admin.translation_list.table.open_history | file #lang/de-be/admin.php [f16a78d7e327] | locale_spelling_review |
| 5aaeba3be2e5ebdc | de-be | ui.table.per_page_selector.per_page | file #lang/de-be/ui.php [fe7c4b7cd613] | locale_spelling_review |
| aa8267f12584b8bb | de-ch | admin.permissions.actions.edit_role_permissions | file #lang/de-ch/admin.php [74f749437ff2] | locale_spelling_review |
| 96204f5a351474fd | de-ch | admin.translation_list.table.open_history | file #lang/de-ch/admin.php [f16a78d7e327] | locale_spelling_review |
| 43eb6d9714384cef | de-ch | ui.table.per_page_selector.per_page | file #lang/de-ch/ui.php [fe7c4b7cd613] | locale_spelling_review |
| f66263857afd1675 | de-de | admin.translation_list.table.open_history | file #lang/de-de/admin.php [f16a78d7e327] | locale_spelling_review |
| 844d5a86d3e1b360 | de-de | ui.table.per_page_selector.per_page | file #lang/de-de/ui.php [fe7c4b7cd613] | locale_spelling_review |
| a1f2a73fc3da5abf | de-it | admin.permissions.actions.edit_role_permissions | file #lang/de-it/admin.php [74f749437ff2] | locale_spelling_review |
| 074b47e51ed12288 | de-it | admin.translation_list.table.open_history | file #lang/de-it/admin.php [f16a78d7e327] | locale_spelling_review |
| 0c90c8468f346dcc | de-it | ui.table.per_page_selector.per_page | file #lang/de-it/ui.php [fe7c4b7cd613] | locale_spelling_review |
| f4303ce4112997fc | de-li | admin.translation_list.table.open_history | file #lang/de-li/admin.php [f16a78d7e327] | locale_spelling_review |
| b0bb10a5dddeea0c | de-li | ui.table.per_page_selector.per_page | file #lang/de-li/ui.php [fe7c4b7cd613] | locale_spelling_review |
| 0822687c4ee11683 | de-lu | admin.translation_list.table.open_history | file #lang/de-lu/admin.php [f16a78d7e327] | locale_spelling_review |
| c765e08ba77d46fe | de-lu | ui.table.per_page_selector.per_page | file #lang/de-lu/ui.php [fe7c4b7cd613] | locale_spelling_review |

</details>

## matching_evidence (1987)

<details>
<summary>Einträge mit Herkunft anzeigen</summary>

| Review-ID | Locale | Key | Herkunft / Datensatz / Wert-SHA256 (gekürzt) | Zusätzliche Prüfung |
| --- | --- | --- | --- | --- |
| 609350c6cec02314 | de | account.preferences.application_preferences.admin_users_per_page | file #lang/de/account.php [d742d5a2c900]; legacy #1568 [d742d5a2c900]; workbench #1 [d742d5a2c900] |  |
| df92b8e4d650f6db | de | account.preferences.application_preferences.application_preferences | file #lang/de/account.php [66867a47e97c]; legacy #1574 [66867a47e97c]; workbench #2 [66867a47e97c] |  |
| 0105ad4d6986d702 | de | account.preferences.application_preferences.english | file #lang/de/account.php [983ea57f2212]; legacy #1586 [983ea57f2212]; workbench #3 [983ea57f2212] |  |
| 7c253b4953cdf69d | de | account.preferences.application_preferences.german | file #lang/de/account.php [801cfe0fa118]; legacy #1584 [801cfe0fa118]; workbench #4 [801cfe0fa118] |  |
| 14e9e617d550c012 | de | account.preferences.application_preferences.preferred_locale | file #lang/de/account.php [1c9ad5d8dd50]; legacy #1576 [1c9ad5d8dd50]; workbench #5 [1c9ad5d8dd50] |  |
| 1f0b5d3366a1f89b | de | account.preferences.application_preferences.stored_preference | file #lang/de/account.php [ce9c4c4ac7a1]; legacy #1570 [ce9c4c4ac7a1]; workbench #6 [ce9c4c4ac7a1] |  |
| 6b9b6efcdf0f5b4f | de | account.preferences.application_preferences.the_locale_preference_is_stored_now_and_can_be_connected_to_application_locale_h | file #lang/de/account.php [9c8b178b78d6]; legacy #1572 [9c8b178b78d6]; workbench #7 [9c8b178b78d6] |  |
| 4d29b390470022bb | de | account.preferences.current_user.edit_profile | file #lang/de/account.php [4f2308fdddb2]; legacy #1560 [4f2308fdddb2]; workbench #8 [4f2308fdddb2] |  |
| 6898188ee406772c | de | account.preferences.current_user.nickname | file #lang/de/account.php [53a6ab7017fb]; legacy #1562 [53a6ab7017fb] |  |
| 68b87f06fbfdc2f1 | de | account.preferences.manage_your_personal_application_preferences | file #lang/de/account.php [e28a10b13c51]; legacy #1580 [e28a10b13c51]; workbench #10 [e28a10b13c51] |  |
| c46ff9be25a89535 | de | account.preferences.preferences_saved | file #lang/de/account.php [8352e27413cd]; legacy #1566 [8352e27413cd]; workbench #11 [8352e27413cd] |  |
| 07cffa54ddcdcfd8 | de | account.preferences.save_preferences | file #lang/de/account.php [337972dc6f1d]; legacy #1582 [337972dc6f1d]; workbench #12 [337972dc6f1d] |  |
| a60a44b716509b3f | de | account.preferences.stored_settings.stored_settings | file #lang/de/account.php [0f044f276da0]; legacy #1558 [0f044f276da0]; workbench #13 [0f044f276da0] |  |
| 68922dbd305ff68e | de | account.preferences.stored_settings.these_values_are_stored_in_your_personal_users_settings_jsonb_column | file #lang/de/account.php [645b42ee928d]; legacy #1556 [645b42ee928d]; workbench #14 [645b42ee928d] |  |
| 70e3338ff90772b5 | de | account.preferences.user_preferences | file #lang/de/account.php [3ef6f6ffaab5]; legacy #1578 [3ef6f6ffaab5]; workbench #15 [3ef6f6ffaab5] |  |
| e62de29301b9e24f | de | account.preferences.your_personal_application_preferences_have_been_updated | file #lang/de/account.php [4bfd14038f10]; legacy #1564 [4bfd14038f10]; workbench #16 [4bfd14038f10] |  |
| d2802e0032e4841c | de | admin.app_settings.app_settings | file #lang/de/admin.php [8376ed06e2e4]; legacy #1638 [8376ed06e2e4] |  |
| ab93449c80f8047f | de | admin.app_settings.application_language_updated | file #lang/de/admin.php [95a1afe9fc83]; legacy #1848 [95a1afe9fc83]; workbench #20 [95a1afe9fc83] |  |
| 096e9f290a80df7a | de | admin.app_settings.at_least_one_application_language_must_remain_enabled | file #lang/de/admin.php [5b643e3fbe90]; legacy #1854 [5b643e3fbe90]; workbench #21 [5b643e3fbe90] |  |
| b83b257fe24c0c80 | de | admin.app_settings.available_app_locales_updated | file #lang/de/admin.php [e44d31154fb0]; legacy #2000 [e44d31154fb0]; workbench #22 [e44d31154fb0] |  |
| da6e33d75bb10163 | de | admin.app_settings.available_application_languages_updated | file #lang/de/admin.php [7df8fe8b4f7e]; legacy #1996 [7df8fe8b4f7e]; workbench #23 [7df8fe8b4f7e] |  |
| edb5ce36997433dc | de | admin.app_settings.global_app_locale_changed | file #lang/de/admin.php [bc84f23a0f2d]; legacy #1846 [bc84f23a0f2d]; workbench #24 [bc84f23a0f2d] |  |
| a50b2669147805dc | de | admin.app_settings.invalid_application_language | file #lang/de/admin.php [f7c055450ac8]; legacy #1844 [f7c055450ac8]; workbench #25 [f7c055450ac8] |  |
| 8a07638824dab218 | de | admin.app_settings.locale.application_languages | file #lang/de/admin.php [6e32943af9c8]; legacy #1838 [6e32943af9c8]; workbench #26 [6e32943af9c8] |  |
| c43c9de8fde2acf5 | de | admin.app_settings.locale.configure_which_languages_are_available_for_the_app_ui_and_choose_the_global_def | file #lang/de/admin.php [0bf20eabfbcf]; legacy #1840 [0bf20eabfbcf]; workbench #27 [0bf20eabfbcf] |  |
| 1e9967aef224781e | de | admin.app_settings.locale.current | file #lang/de/admin.php [f4d865ae1b77]; legacy #1842 [f4d865ae1b77] |  |
| d63c2451cee0f1b7 | de | admin.app_settings.locale.deselect_all | file #lang/de/admin.php [663e32137db7]; legacy #1822 [663e32137db7]; workbench #29 [663e32137db7] |  |
| d67d208959526955 | de | admin.app_settings.locale.do_you_really_want_to_deactivate_this_language | file #lang/de/admin.php [452d9a49faab]; legacy #1836 [452d9a49faab] |  |
| 2fbc9bd37065b72d | de | admin.app_settings.locale.enable_the_language_and_any_sub_languages_you_may_have_enabled_above_that_should | file #lang/de/admin.php [7e9e457305d9]; legacy #1812 [7e9e457305d9]; workbench #31 [7e9e457305d9] |  |
| 07df344287f6fca2 | de | admin.app_settings.locale.global_app_ui_language | file #lang/de/admin.php [d9dbbd93ae25]; legacy #1810 [d9dbbd93ae25]; workbench #32 [d9dbbd93ae25] |  |
| c1d356826df49e4c | de | admin.app_settings.locale.narrow_down_your_selection | file #lang/de/admin.php [ffabe8c9ca1d]; legacy #1800 [ffabe8c9ca1d]; workbench #33 [ffabe8c9ca1d] |  |
| a377628bb2d41b81 | de | admin.app_settings.locale.no_sub_languages_are_available_for_the_selected_primary_language | file #lang/de/admin.php [3914bec40ea1]; legacy #1808 [3914bec40ea1]; workbench #34 [3914bec40ea1] |  |
| 0543e7a2d9da6eec | de | admin.app_settings.locale.number_of_sub_languages_available_for_this_primary_language | file #lang/de/admin.php [deb1ab274f54]; legacy #1828 [deb1ab274f54]; workbench #35 [deb1ab274f54] |  |
| b3da5c13f9f25912 | de | admin.app_settings.locale.please_select_a_primary_language | file #lang/de/admin.php [822da2e5ba66]; legacy #1640 [822da2e5ba66]; workbench #36 [822da2e5ba66] |  |
| 24c96a071ec462b8 | de | admin.app_settings.locale.primary_languages | file #lang/de/admin.php [f827b874deda]; legacy #1804 [f827b874deda]; workbench #37 [f827b874deda] |  |
| be741b9b0ac3d02a | de | admin.app_settings.locale.select_all | file #lang/de/admin.php [051da525afd3]; legacy #1824 [051da525afd3]; workbench #38 [051da525afd3] |  |
| de686b25808264a7 | de | admin.app_settings.locale.select_available_languages | file #lang/de/admin.php [80e5a15cf1f0]; legacy #1768 [80e5a15cf1f0]; workbench #39 [80e5a15cf1f0] |  |
| 2d9138674eab6c4f | de | admin.app_settings.locale.select_the_entries_you_want_from_the_sub_languages_listed_below_that_correspond_ | file #lang/de/admin.php [36c665d77ff0]; legacy #1806 [36c665d77ff0]; workbench #40 [36c665d77ff0] |  |
| 7e604a17e4eab961 | de | admin.app_settings.locale.select_the_top_languages_by_their_usage | file #lang/de/admin.php [60ba90099bd3]; legacy #1798 [60ba90099bd3]; workbench #41 [60ba90099bd3] |  |
| 3301f30dc8807560 | de | admin.app_settings.locale.select_this_primary_language_to_manage_its_sub_languages_and_activation_state | file #lang/de/admin.php [dd823a3ad4c2]; legacy #1832 [dd823a3ad4c2]; workbench #42 [dd823a3ad4c2] |  |
| 12d7a970b553a48d | de | admin.app_settings.locale.select_which_languages_should_be_available_for_the_app_ui_you_can_choose_from_al | file #lang/de/admin.php [4cc226324f6f]; legacy #1802 [4cc226324f6f]; workbench #43 [4cc226324f6f] |  |
| 3a335ba718b016d8 | de | admin.app_settings.locale.sub_languages | file #lang/de/admin.php [96b98a69136a]; legacy #1642 [96b98a69136a]; workbench #44 [96b98a69136a] |  |
| 1a1d58c399b31eae | de | admin.app_settings.locale.the_country_language_flag_of_the_global_available_app_ui_language | file #lang/de/admin.php [b78ec0474a2a]; legacy #1830 [b78ec0474a2a] |  |
| 4c1a0ad11969c006 | de | admin.app_settings.locale.the_number_of_sub_languages_selected_for_this_primary_language | file #lang/de/admin.php [57d46ce77a40]; legacy #1834 [57d46ce77a40]; workbench #46 [57d46ce77a40] |  |
| 20666c0dcf170bc8 | de | admin.app_settings.locale.this_setting_defines_the_global_default_app_ui_language_user_specific_language_s | file #lang/de/admin.php [9e783894652e]; legacy #1814 [9e783894652e]; workbench #47 [9e783894652e] |  |
| 75ae4dabe41b03bc | de | admin.app_settings.locale.toggle_the_availability_of_this_sub_language_for_the_app_ui | file #lang/de/admin.php [9f381bb75a69]; legacy #1826 [9f381bb75a69]; workbench #48 [9f381bb75a69] |  |
| f688fd0bd4d68bf9 | de | admin.app_settings.locale.top_languages | file #lang/de/admin.php [b73e5171c362]; legacy #1790 [b73e5171c362]; workbench #49 [b73e5171c362] |  |
| 182102f1b4b92aed | de | admin.app_settings.meta_health.badge_configs_without_role | file #lang/de/admin.php [17b23dc43b61]; legacy #1648 [17b23dc43b61]; workbench #50 [17b23dc43b61] |  |
| 6949899729495c42 | de | admin.app_settings.meta_health.badge_settings_that_reference_roles_that_no_longer_exist | file #lang/de/admin.php [44a2e789480d]; legacy #1650 [44a2e789480d]; workbench #51 [44a2e789480d] |  |
| 87edec22d2bca463 | de | admin.app_settings.meta_health.roles_that_exist_in_the_database_but_have_no_configured_badge_settings | file #lang/de/admin.php [c52006a4697f]; legacy #1646 [c52006a4697f]; workbench #52 [c52006a4697f] |  |
| f75b130196f2472b | de | admin.app_settings.meta_health.roles_without_badge_config | file #lang/de/admin.php [b566661ee7f1]; legacy #1644 [b566661ee7f1]; workbench #53 [b566661ee7f1] |  |
| 369099dce33abbc8 | de | admin.app_settings.meta_health.settings_health | file #lang/de/admin.php [309a761a93cc]; legacy #1816 [309a761a93cc]; workbench #54 [309a761a93cc] |  |
| a6d1aed3223a2739 | de | admin.app_settings.meta_overview.allowed_role_user_management_icons_registered_in_config | file #lang/de/admin.php [8a1148530125]; legacy #1666 [8a1148530125]; workbench #55 [8a1148530125] |  |
| 04a01ca1e92aee37 | de | admin.app_settings.meta_overview.app_language | file #lang/de/admin.php [4401d4afafa5]; legacy #1658 [4401d4afafa5]; workbench #56 [4401d4afafa5] |  |
| 437f18f64415f830 | de | admin.app_settings.meta_overview.configured_role_badge_mappings_stored_in_app_settings | file #lang/de/admin.php [4ae49716af19]; legacy #1662 [4ae49716af19]; workbench #57 [4ae49716af19] |  |
| 63b732dadbcd2595 | de | admin.app_settings.meta_overview.global_locale_currently_used_by_the_application | file #lang/de/admin.php [d3e61eafb0a9]; legacy #1660 [d3e61eafb0a9]; workbench #58 [d3e61eafb0a9] |  |
| 84dd1ff3a1634e71 | de | admin.app_settings.meta_overview.missing_icons | file #lang/de/admin.php [955a11726b5c]; legacy #1668 [955a11726b5c]; workbench #59 [955a11726b5c] |  |
| e8541ecbb8f97871 | de | admin.app_settings.meta_overview.overview | file #lang/de/admin.php [4d2bb368f919]; legacy #1652 [4d2bb368f919] |  |
| 10c2184ea433be1a | de | admin.app_settings.meta_overview.primary_spatie_settings_group_used_for_display_settings | file #lang/de/admin.php [df74bf0cfc7c]; legacy #1656 [df74bf0cfc7c]; workbench #61 [df74bf0cfc7c] |  |
| 0e61ec88c28b610d | de | admin.app_settings.meta_overview.registered_icons | file #lang/de/admin.php [8f5cbe6c57e0]; legacy #1664 [8f5cbe6c57e0]; workbench #62 [8f5cbe6c57e0] |  |
| a2d6451928383ef1 | de | admin.app_settings.meta_overview.registered_icons_whose_flux_view_is_currently_unavailable | file #lang/de/admin.php [787a7e24b75d]; legacy #1670 [787a7e24b75d]; workbench #63 [787a7e24b75d] |  |
| 5cc55414a56cb0ee | de | admin.app_settings.meta_overview.role_badge_entries | file #lang/de/admin.php [42de7df893f9]; legacy #1818 [42de7df893f9]; workbench #64 [42de7df893f9] |  |
| 42801022fce8bb81 | de | admin.app_settings.meta_overview.settings_group | file #lang/de/admin.php [9426130fb4e6]; legacy #1654 [9426130fb4e6]; workbench #65 [9426130fb4e6] |  |
| 0c2fdbc6f91254aa | de | admin.app_settings.primary_locale_availability_updated | file #lang/de/admin.php [1d82202d5e28]; legacy #1856 [1d82202d5e28]; workbench #66 [1d82202d5e28] |  |
| c9021981705066c3 | de | admin.app_settings.review_global_application_settings_role_badge_configuration_and_icon_registry_he | file #lang/de/admin.php [eda015b1acd1]; legacy #1636 [eda015b1acd1]; workbench #67 [eda015b1acd1] |  |
| 102badb18932b4fa | de | admin.app_settings.sub_locales_updated | file #lang/de/admin.php [f4f3cfb6feca]; legacy #2004 [f4f3cfb6feca]; workbench #68 [f4f3cfb6feca] |  |
| 0e2ca671cdcec02e | de | admin.app_settings.table_icon_registry.available | file #lang/de/admin.php [3aa55273aef4]; legacy #1684 [3aa55273aef4] |  |
| f93b7c1ee16395df | de | admin.app_settings.table_icon_registry.icon | file #lang/de/admin.php [ba0e4afa9340]; legacy #1676 [ba0e4afa9340] |  |
| 172fe95796045d93 | de | admin.app_settings.table_icon_registry.icon_registry | file #lang/de/admin.php [199430301242]; legacy #1672 [199430301242]; workbench #71 [199430301242] |  |
| 00223a8dc41ccf35 | de | admin.app_settings.table_icon_registry.label | file #lang/de/admin.php [7e1c54731e1f]; legacy #1678 [7e1c54731e1f] |  |
| fa616619e494043f | de | admin.app_settings.table_icon_registry.missing | file #lang/de/admin.php [380f98c64bde]; legacy #1686 [380f98c64bde] |  |
| 3e6b54fc1e7380d6 | de | admin.app_settings.table_icon_registry.status | file #lang/de/admin.php [920e413c7d41]; legacy #1682 [920e413c7d41] |  |
| 51c181e6a1349fd2 | de | admin.app_settings.table_icon_registry.text | file #lang/de/admin.php [334359b90efe]; legacy #1674 [334359b90efe] |  |
| 15a54594538ef899 | de | admin.app_settings.table_icon_registry.view | file #lang/de/admin.php [ccb27f03f03e]; legacy #1680 [ccb27f03f03e] |  |
| 0cb4dd57120f2d8e | de | admin.app_settings.table_role_badges.color | file #lang/de/admin.php [a952c8f76f58]; legacy #1778 [a952c8f76f58] |  |
| 676904fa86733602 | de | admin.app_settings.table_role_badges.icon | file #lang/de/admin.php [ba0e4afa9340]; legacy #1782 [ba0e4afa9340] |  |
| 6ca27e5580dfffd6 | de | admin.app_settings.table_role_badges.icon_fallback | file #lang/de/admin.php [be31cddc3b46]; legacy #1820 [be31cddc3b46]; workbench #79 [be31cddc3b46] |  |
| e2196ab5eac0c02c | de | admin.app_settings.table_role_badges.icon_ok | file #lang/de/admin.php [fef7434d45f0]; legacy #1796 [fef7434d45f0]; workbench #80 [fef7434d45f0] |  |
| f8f07c5c22d5aebb | de | admin.app_settings.table_role_badges.missing_role | file #lang/de/admin.php [b605c015d53a]; legacy #1794 [b605c015d53a]; workbench #81 [b605c015d53a] |  |
| ffb403a42fd18d95 | de | admin.app_settings.table_role_badges.preview | file #lang/de/admin.php [69337f6de914]; legacy #1776 [69337f6de914] |  |
| b14d5c36a15db292 | de | admin.app_settings.table_role_badges.pseudo_state | file #lang/de/admin.php [e725231e0fc9]; legacy #1766 [e725231e0fc9]; workbench #83 [e725231e0fc9] |  |
| 14cd77b1ee5ef188 | de | admin.app_settings.table_role_badges.role | file #lang/de/admin.php [0038a9cf8661]; legacy #1774 [0038a9cf8661] |  |
| 784bc4f4566ac2e5 | de | admin.app_settings.table_role_badges.role_badge_settings | file #lang/de/admin.php [ade58ad57964]; legacy #1770 [ade58ad57964]; workbench #85 [ade58ad57964] |  |
| ff2057a736aad2da | de | admin.app_settings.table_role_badges.role_does_not_exist | file #lang/de/admin.php [99679ebda3db]; legacy #1788 [99679ebda3db]; workbench #86 [99679ebda3db] |  |
| 5f98897c70a223fd | de | admin.app_settings.table_role_badges.role_ok | file #lang/de/admin.php [ad55c99d4f49]; legacy #1792 [ad55c99d4f49]; workbench #87 [ad55c99d4f49] |  |
| 5ad88db2e7a1740f | de | admin.app_settings.table_role_badges.status | file #lang/de/admin.php [920e413c7d41]; legacy #1784 [920e413c7d41] |  |
| fa8510c809966608 | de | admin.app_settings.table_role_badges.text | file #lang/de/admin.php [334359b90efe]; legacy #1772 [334359b90efe] |  |
| b3d6f3c25549971c | de | admin.app_settings.table_role_badges.ui_state_not_a_database_role | file #lang/de/admin.php [8caee938ac51]; legacy #1786 [8caee938ac51]; workbench #90 [8caee938ac51] |  |
| b5b7b8269862c611 | de | admin.app_settings.table_role_badges.variant | file #lang/de/admin.php [a0f6dc6453d1]; legacy #1780 [a0f6dc6453d1] |  |
| e2ce606d12129b36 | de | admin.app_settings.the_current_application_language_cannot_be_disabled | file #lang/de/admin.php [028522fcdad7]; legacy #1852 [028522fcdad7]; workbench #92 [028522fcdad7] |  |
| eb8920604139c1d2 | de | admin.app_settings.the_current_application_language_cannot_be_removed_from_the_available_languages | file #lang/de/admin.php [1457c2e9d89e]; legacy #1998 [1457c2e9d89e]; workbench #93 [1457c2e9d89e] |  |
| 95193fb52cc4356d | de | admin.app_settings.this_application_language_is_mandatory_and_cannot_be_disabled | file #lang/de/admin.php [3b519b49a07c]; legacy #1850 [3b519b49a07c]; workbench #94 [3b519b49a07c] |  |
| 19466eb16e003853 | de | admin.app_settings.without_role | file #lang/de/admin.php [7344c73d335d]; legacy #2002 [7344c73d335d] |  |
| 69c681975bbf267f | de | admin.client_list.client_management | file #lang/de/admin.php [4154488d922e]; legacy #2023 [4154488d922e]; workbench #96 [4154488d922e] |  |
| 61dfdba92176d4aa | de | admin.client_list.filter.all_statuses | file #lang/de/admin.php [23ee74c4a81b]; legacy #2141 [23ee74c4a81b] |  |
| 757ea17fec73e82f | de | admin.client_list.filter.all_types | file #lang/de/admin.php [83162aa995db]; legacy #2123 [83162aa995db] |  |
| 972e160d247f549f | de | admin.client_list.filter.enter_a_search_term_to_filter_clients_by_name_legal_name_client_number_or_descri | file #lang/de/admin.php [5d89bf0cf9fa]; legacy #2149 [5d89bf0cf9fa]; workbench #99 [5d89bf0cf9fa] |  |
| 265608dd05296420 | de | admin.client_list.filter.filter_by_people | file #lang/de/admin.php [cb83e940d15a]; legacy #2117 [cb83e940d15a]; workbench #100 [cb83e940d15a] |  |
| 633f003b2e5da28d | de | admin.client_list.filter.filter_by_status | file #lang/de/admin.php [66803be010de]; legacy #2125 [66803be010de] |  |
| 60e6fc10494217f3 | de | admin.client_list.filter.filter_by_type | file #lang/de/admin.php [e243e2d726cd]; legacy #2157 [e243e2d726cd]; workbench #102 [e243e2d726cd] |  |
| 6fd79ba9d6854dd1 | de | admin.client_list.filter.filtering | file #lang/de/admin.php [922befcd77fa]; legacy #2077 [922befcd77fa] |  |
| c7a3d5b8c236256c | de | admin.client_list.filter.refine_the_client_list_with_powerful_filters_search_by_name_legal_name_client_nu | file #lang/de/admin.php [407b05b92b92]; legacy #2082 [407b05b92b92]; workbench #104 [407b05b92b92] |  |
| c6e144d81e232ee7 | de | admin.client_list.filter.search_by_name_legal_name_client_number_or_description | file #lang/de/admin.php [7909965fc764]; legacy #2119 [7909965fc764]; workbench #105 [7909965fc764] |  |
| 79f5419295f5bcb7 | de | admin.client_list.filter.search_clients | file #lang/de/admin.php [09a89d77a4d8]; legacy #2151 [09a89d77a4d8]; workbench #106 [09a89d77a4d8] |  |
| e48db9ad43bf0dbd | de | admin.client_list.filter.select_a_people_filter_to_filter_the_client_list | file #lang/de/admin.php [22e044e8db1c]; legacy #2143 [22e044e8db1c]; workbench #107 [22e044e8db1c] |  |
| 3b7949b31ca0b6af | de | admin.client_list.filter.select_a_status_to_filter_the_client_list | file #lang/de/admin.php [4d60151d7f55]; legacy #2137 [4d60151d7f55]; workbench #108 [4d60151d7f55] |  |
| ad472b021eaf90cf | de | admin.client_list.filter.select_a_type_to_filter_the_client_list | file #lang/de/admin.php [ed9d533e63aa]; legacy #2115 [ed9d533e63aa]; workbench #109 [ed9d533e63aa] |  |
| 4ba6956d5e1cc465 | de | admin.client_list.filter.with_people | file #lang/de/admin.php [e89e38f26de8]; legacy #2153 [e89e38f26de8] |  |
| 81f39462a60eb85d | de | admin.client_list.filter.without_people | file #lang/de/admin.php [aa0895dbcef3]; legacy #2147 [aa0895dbcef3] |  |
| 14f042b8c65c4b04 | de | admin.client_list.meta.active_clients | file #lang/de/admin.php [0158855e91d7]; legacy #2129 [0158855e91d7]; workbench #112 [0158855e91d7] |  |
| 243f08036035a3de | de | admin.client_list.meta.clients_assigned_to_at_least_one_person | file #lang/de/admin.php [81ddcc9a3dfe]; legacy #2135 [81ddcc9a3dfe]; workbench #113 [81ddcc9a3dfe] |  |
| 064e517997a474c9 | de | admin.client_list.meta.clients_currently_marked_as_active | file #lang/de/admin.php [a1c5ad560f2f]; legacy #2121 [a1c5ad560f2f]; workbench #114 [a1c5ad560f2f] |  |
| 85e4a9e70eac9fba | de | admin.client_list.meta.clients_waiting_for_activation_or_verification | file #lang/de/admin.php [8e56f0ccce79]; legacy #2145 [8e56f0ccce79]; workbench #115 [8e56f0ccce79] |  |
| 77b1eb09744665ae | de | admin.client_list.meta.clients_without_assigned_people | file #lang/de/admin.php [c8ab0990b5f0]; legacy #2087 [c8ab0990b5f0]; workbench #116 [c8ab0990b5f0] |  |
| 304240e6715fe41f | de | admin.client_list.meta.get_a_quick_snapshot_of_client_statistics_including_total_clients_pending_activa | file #lang/de/admin.php [6a875c0e45cf]; legacy #2107 [6a875c0e45cf]; workbench #117 [6a875c0e45cf] |  |
| ace127d2027c443a | de | admin.client_list.meta.pending_clients | file #lang/de/admin.php [6d36884af7db]; legacy #2101 [6d36884af7db]; workbench #118 [6d36884af7db] |  |
| 96f6e7cd02e2f73f | de | admin.client_list.meta.total_clients | file #lang/de/admin.php [f30e03f3949b]; legacy #2099 [f30e03f3949b]; workbench #119 [f30e03f3949b] |  |
| 7c1d7c07df1d358c | de | admin.client_list.meta.total_number_of_registered_clients | file #lang/de/admin.php [c48f15760ff3]; legacy #2127 [c48f15760ff3]; workbench #120 [c48f15760ff3] |  |
| 9670117f8cf87ba8 | de | admin.client_list.meta.with_people | file #lang/de/admin.php [e89e38f26de8]; legacy #2105 [e89e38f26de8] |  |
| 12662dcb91e94a74 | de | admin.client_list.meta.without_people | file #lang/de/admin.php [aa0895dbcef3]; legacy #2103 [aa0895dbcef3] |  |
| 46439005c1bc83e0 | de | admin.client_list.review_organizations_client_status_and_assigned_people | file #lang/de/admin.php [2c789169776b]; legacy #2021 [2c789169776b]; workbench #123 [2c789169776b] |  |
| 332cd1a67e29a19d | de | admin.client_list.table.browse_and_manage_clients_with_ease_view_client_details_sort_by_name_number_type | file #lang/de/admin.php [f00a921a806b]; legacy #2155 [f00a921a806b]; workbench #124 [f00a921a806b] |  |
| 8bbb272cc6a37223 | de | admin.client_list.table.client_list | file #lang/de/admin.php [3d64ec3101fa]; legacy #2089 [3d64ec3101fa]; workbench #125 [3d64ec3101fa] |  |
| 2b32d0e61d8de481 | de | admin.client_list.table.client_number | file #lang/de/admin.php [b84c1f9c9325]; legacy #2109 [b84c1f9c9325] |  |
| b00cf09b39c8203e | de | admin.client_list.table.created | file #lang/de/admin.php [1155fc66f4db]; legacy #2043 [1155fc66f4db] |  |
| a7b0bb7b11a8b776 | de | admin.client_list.table.current_status_of_the_client_indicating_their_activity_and_engagement | file #lang/de/admin.php [322aea3ac3e6]; legacy #2113 [322aea3ac3e6]; workbench #128 [322aea3ac3e6] |  |
| 5296157b3a46d39a | de | admin.client_list.table.date_when_the_client_was_created_useful_for_tracking_client_history | file #lang/de/admin.php [62e8671c54f7]; legacy #2045 [62e8671c54f7]; workbench #129 [62e8671c54f7] |  |
| 9ff8781c79102fae | de | admin.client_list.table.name_of_the_client_useful_for_identification | file #lang/de/admin.php [b9e441ddbc00]; legacy #2097 [b9e441ddbc00]; workbench #130 [b9e441ddbc00] |  |
| 50e45362ca76c0e7 | de | admin.client_list.table.no_clients_found | file #lang/de/admin.php [7078d5b62c40]; legacy #2031 [7078d5b62c40]; workbench #131 [7078d5b62c40] |  |
| 8d70100de1a17553 | de | admin.client_list.table.number_of_people_associated_with_the_client_useful_for_understanding_client_size | file #lang/de/admin.php [bd6a804ae927]; legacy #2027 [bd6a804ae927]; workbench #132 [bd6a804ae927] |  |
| dce2cc053bac2dc6 | de | admin.client_list.table.pagination | file #lang/de/admin.php [e14d4b6dd1a2]; legacy #2039 [e14d4b6dd1a2] |  |
| b5cda76899f431f4 | de | admin.client_list.table.people | file #lang/de/admin.php [b1a03b358fed]; legacy #2041 [b1a03b358fed] |  |
| 49f09b49a4d3e390 | de | admin.client_list.table.status | file #lang/de/admin.php [920e413c7d41]; legacy #2037 [920e413c7d41] |  |
| 23f075fc8a158bf2 | de | admin.client_list.table.type | file #lang/de/admin.php [1b024183cb33]; legacy #2033 [1b024183cb33] |  |
| 970ae6983b7f0fca | de | admin.client_list.table.type_of_the_client_useful_for_categorization_and_filtering | file #lang/de/admin.php [d3964271d2a2]; legacy #2131 [d3964271d2a2]; workbench #137 [d3964271d2a2] |  |
| e4e64c0a1df79107 | de | admin.client_list.table.unique_identifier_of_the_client_useful_for_tracking_and_reference | file #lang/de/admin.php [d86d148f0fa3]; legacy #2139 [d86d148f0fa3]; workbench #138 [d86d148f0fa3] |  |
| 197cefdb2d56f818 | de | admin.client_list.table.unique_number_assigned_to_the_client_useful_for_tracking_and_reference | file #lang/de/admin.php [a0922e158c47]; legacy #2133 [a0922e158c47]; workbench #139 [a0922e158c47] |  |
| ec6ab8b7e4c244de | de | admin.country_reference_list.audit_imported_country_reference_data_address_formats_and_available_subdivisions | file #lang/de/admin.php [873e62e646f3]; legacy #2249 [873e62e646f3]; workbench #140 [873e62e646f3] |  |
| 40b482ffe96f56d9 | de | admin.country_reference_list.filter.active | file #lang/de/admin.php [8163454f378f]; legacy #2430 [8163454f378f] |  |
| 0f6556ba09857fb8 | de | admin.country_reference_list.filter.data_quality | file #lang/de/admin.php [fe0880dd803a]; legacy #2308 [fe0880dd803a]; workbench #142 [fe0880dd803a] |  |
| e80bf24dd72be4a8 | de | admin.country_reference_list.filter.eea | file #lang/de/admin.php [70540e81f598]; legacy #2258 [70540e81f598] |  |
| b26a7a39676a07f0 | de | admin.country_reference_list.filter.enter_a_search_term_to_filter_countries_by_iso_code_name_phone_code_capital_city | file #lang/de/admin.php [f8780fe3b8ce]; legacy #2294 [f8780fe3b8ce]; workbench #144 [f8780fe3b8ce] |  |
| 2cb2e1437ef8cec6 | de | admin.country_reference_list.filter.eu | file #lang/de/admin.php [9170a8b2fb32]; legacy #2262 [9170a8b2fb32] |  |
| 6aa03cad087009b4 | de | admin.country_reference_list.filter.filter_by_data_quality | file #lang/de/admin.php [a1af968be7c7]; legacy #2314 [a1af968be7c7]; workbench #146 [a1af968be7c7] |  |
| 1c263c88e40de85b | de | admin.country_reference_list.filter.filter_by_membership | file #lang/de/admin.php [5be3b73a6391]; legacy #2326 [5be3b73a6391]; workbench #147 [5be3b73a6391] |  |
| ccdea7684adfdfb4 | de | admin.country_reference_list.filter.filter_by_region | file #lang/de/admin.php [307ccc55dda5]; legacy #2298 [307ccc55dda5]; workbench #148 [307ccc55dda5] |  |
| 72c3bdd27345da94 | de | admin.country_reference_list.filter.inactive | file #lang/de/admin.php [bf7c9171cb49]; legacy #2332 [bf7c9171cb49] |  |
| c9cef4b6d766fd18 | de | admin.country_reference_list.filter.iso_name_phone_capital_region | file #lang/de/admin.php [28813d739b8d]; legacy #2253 [28813d739b8d]; workbench #150 [28813d739b8d] |  |
| 74189829894df23d | de | admin.country_reference_list.filter.membership | file #lang/de/admin.php [00672fd30cc2]; legacy #2265 [00672fd30cc2]; workbench #151 [00672fd30cc2] |  |
| d709c8a1e1793671 | de | admin.country_reference_list.filter.missing_address_format | file #lang/de/admin.php [ad392e070a1a]; legacy #2403 [ad392e070a1a]; workbench #152 [ad392e070a1a] |  |
| aeacb44911535627 | de | admin.country_reference_list.filter.missing_capital | file #lang/de/admin.php [d8317ffaa0c6]; legacy #2304 [d8317ffaa0c6] |  |
| 1ff6cca0cc0f15de | de | admin.country_reference_list.filter.missing_phone_code | file #lang/de/admin.php [dff679295513]; legacy #2306 [dff679295513]; workbench #154 [dff679295513] |  |
| 588ac840cbf4cd53 | de | admin.country_reference_list.filter.refine_the_list_of_reference_countries_by_searching_for_specific_attributes_or_a | file #lang/de/admin.php [8198d0b1ea12]; legacy #2300 [8198d0b1ea12]; workbench #155 [8198d0b1ea12] |  |
| b32e1e0b63fd1d82 | de | admin.country_reference_list.filter.region | file #lang/de/admin.php [d3a008ef1335]; legacy #2255 [d3a008ef1335] |  |
| 3454a14e21371b9e | de | admin.country_reference_list.filter.schengen | file #lang/de/admin.php [936eeab4a5a3]; legacy #2260 [936eeab4a5a3]; workbench #157 [936eeab4a5a3] |  |
| 04b97dc8e414d75c | de | admin.country_reference_list.filter.select_a_data_quality_filter_to_filter_the_list_of_reference_countries | file #lang/de/admin.php [9b77da7f06d9]; legacy #2399 [9b77da7f06d9]; workbench #158 [9b77da7f06d9] |  |
| 18113f85d159c41f | de | admin.country_reference_list.filter.select_a_membership_to_filter_the_list_of_reference_countries | file #lang/de/admin.php [9e6d546c079a]; legacy #2316 [9e6d546c079a]; workbench #159 [9e6d546c079a] |  |
| 72844a46e1e73b05 | de | admin.country_reference_list.filter.select_a_region_to_filter_the_list_of_reference_countries | file #lang/de/admin.php [993f2e00a3d7]; legacy #2296 [993f2e00a3d7]; workbench #160 [993f2e00a3d7] |  |
| 97334413026adc37 | de | admin.country_reference_list.filter.select_a_status_to_filter_the_list_of_reference_countries | file #lang/de/admin.php [159fbed5061b]; legacy #2302 [159fbed5061b]; workbench #161 [159fbed5061b] |  |
| 6b22619588124038 | de | admin.country_reference_list.filter.with_subdivisions | file #lang/de/admin.php [a7b0e85f0143]; legacy #2334 [a7b0e85f0143]; workbench #162 [a7b0e85f0143] |  |
| 5853086c5b89b6fe | de | admin.country_reference_list.filter.without_subdivisions | file #lang/de/admin.php [89d02ee418df]; legacy #2330 [89d02ee418df]; workbench #163 [89d02ee418df] |  |
| 1c65d96ddae1d923 | de | admin.country_reference_list.meta.active | file #lang/de/admin.php [8163454f378f]; legacy #2322 [8163454f378f] |  |
| b37560bd48071e04 | de | admin.country_reference_list.meta.address_formats | file #lang/de/admin.php [5456d3ef6c6d]; legacy #2324 [5456d3ef6c6d]; workbench #165 [5456d3ef6c6d] |  |
| debe9e5906e0b7e5 | de | admin.country_reference_list.meta.eu | file #lang/de/admin.php [9170a8b2fb32]; legacy #2264 [9170a8b2fb32] |  |
| b8d3ee35a8753d7a | de | admin.country_reference_list.meta.missing_capital | file #lang/de/admin.php [73c1b87983db]; legacy #2320 [73c1b87983db] |  |
| 938f6d1753ad026f | de | admin.country_reference_list.meta.missing_phone | file #lang/de/admin.php [94af2dcd1115]; legacy #2310 [94af2dcd1115]; workbench #168 [94af2dcd1115] |  |
| 42a75f5227aed4dc | de | admin.country_reference_list.meta.subdivisions | file #lang/de/admin.php [d37efb752dfc]; legacy #2318 [d37efb752dfc]; workbench #169 [d37efb752dfc] |  |
| 8d1f94e0d40b2a9d | de | admin.country_reference_list.meta.summary_of_imported_country_reference_data_address_formats_and_available_subdivi | file #lang/de/admin.php [ff75a6d79a13]; legacy #2328 [ff75a6d79a13]; workbench #170 [ff75a6d79a13] |  |
| a78f825125fa2eed | de | admin.country_reference_list.reference_countries | file #lang/de/admin.php [efcd19d38711]; legacy #2251 [efcd19d38711]; workbench #171 [efcd19d38711] |  |
| 6f33e2e434346ae3 | de | admin.country_reference_list.table.active | file #lang/de/admin.php [8163454f378f]; legacy #2426 [8163454f378f] |  |
| 95a5f7aff48de991 | de | admin.country_reference_list.table.active_status_of_the_country_useful_for_identification_and_reference | file #lang/de/admin.php [f13a9179f03c]; legacy #2422 [f13a9179f03c]; workbench #173 [f13a9179f03c] |  |
| 1b94cce556d19b8e | de | admin.country_reference_list.table.address | file #lang/de/admin.php [79e5cf20de0b]; legacy #2434 [79e5cf20de0b] |  |
| c8f2853e6c5de2fa | de | admin.country_reference_list.table.address_format_availability_of_the_country_useful_for_identification_and_referen | file #lang/de/admin.php [232bc3868ef5]; legacy #2418 [232bc3868ef5]; workbench #175 [232bc3868ef5] |  |
| 59a8bf4c9b477897 | de | admin.country_reference_list.table.address_format_missing | file #lang/de/admin.php [d8cefac1b402]; legacy #2424 [d8cefac1b402]; workbench #176 [d8cefac1b402] |  |
| 63857ed5c1f5032d | de | admin.country_reference_list.table.capital | file #lang/de/admin.php [c969e9f15ac6]; legacy #2414 [c969e9f15ac6]; workbench #177 [c969e9f15ac6] |  |
| 23b9920991959f77 | de | admin.country_reference_list.table.capital_of_the_country_useful_for_identification_and_reference | file #lang/de/admin.php [e50e48d7c60b]; legacy #2432 [e50e48d7c60b]; workbench #178 [e50e48d7c60b] |  |
| c3900ef377180735 | de | admin.country_reference_list.table.inactive | file #lang/de/admin.php [bf7c9171cb49]; legacy #2428 [bf7c9171cb49] |  |
| c7b9c67ee40b05fc | de | admin.country_reference_list.table.iso | file #lang/de/admin.php [6dc87bf3ac76]; legacy #2405 [6dc87bf3ac76]; workbench #180 [6dc87bf3ac76] |  |
| 00f289f18078a7ef | de | admin.country_reference_list.table.list_of_imported_country_reference_data_address_formats_and_available_subdivisio | file #lang/de/admin.php [f50a055d3ba5]; legacy #2312 [f50a055d3ba5]; workbench #181 [f50a055d3ba5] |  |
| cbad195d2fd8d555 | de | admin.country_reference_list.table.number_of_subdivisions_of_the_country_useful_for_identification_and_reference | file #lang/de/admin.php [5652dc6067c7]; legacy #2440 [5652dc6067c7]; workbench #182 [5652dc6067c7] |  |
| 796f7142aec02ce5 | de | admin.country_reference_list.table.official_name | file #lang/de/admin.php [a51c51933524]; legacy #2409 [a51c51933524]; workbench #183 [a51c51933524] |  |
| f619b0c5986ef1d3 | de | admin.country_reference_list.table.phone_code_of_the_country_useful_for_identification_and_reference | file #lang/de/admin.php [43b1cd8c1b3a]; legacy #2438 [43b1cd8c1b3a]; workbench #184 [43b1cd8c1b3a] |  |
| 1173efc95902e4fe | de | admin.country_reference_list.table.region_of_the_country_useful_for_identification_and_reference | file #lang/de/admin.php [d22e1e452baf]; legacy #2443 [d22e1e452baf]; workbench #185 [d22e1e452baf] |  |
| 9e088ac13c2cd7e1 | de | admin.country_reference_list.table.subdivisions | file #lang/de/admin.php [d37efb752dfc]; legacy #2420 [d37efb752dfc] |  |
| 0990c6bcb217de5f | de | admin.country_reference_list.table.unique_identifier_of_the_country_useful_for_tracking_and_reference | file #lang/de/admin.php [00a614fe66ef]; legacy #2401 [00a614fe66ef]; workbench #187 [00a614fe66ef] |  |
| ca3ef0589b63767f | de | admin.pages.settings.two_factor_setup_modal.or_enter_the_code_manually | file #lang/de/admin.php [04bf10fe7f72]; workbench #1597 [04bf10fe7f72] |  |
| f9325cb11d3182c0 | de | admin.pages.settings.two_factor_setup_modal.two_factor_authentication_is_now_enabled_scan_the_qr_code_or_enter_the_setup_key_in_y | file #lang/de/admin.php [00c6e5b31932]; legacy #6795 [00c6e5b31932]; legacy #6809 [00c6e5b31932] | multiple_records_per_source |
| 0eaa38cdc50cfb83 | de | admin.partials.country_reference_list.table.address_format_available | file #lang/de/admin.php [a5bafedb5202]; workbench #1588 [a5bafedb5202] |  |
| 9a7c6e3ebfde48fa | de | admin.partials.html_view_audit.table.show_finding_details | file #lang/de/admin.php [951b0ccff4fc]; workbench #1547 [951b0ccff4fc] |  |
| f0c39f6ab563140e | de | admin.partials.person_list.filter.filter_by_client | file #lang/de/admin.php [aa5a3e61bfc4]; workbench #1590 [aa5a3e61bfc4] |  |
| 6ae50e4c3712a25a | de | admin.partials.role_list.modal_create.role_badge_variant_options.options.outline | dynamic_workbench #20 [eabbf3abaf8d]; file #lang/de/admin.php [eabbf3abaf8d] |  |
| 78974821199434fa | de | admin.partials.role_list.modal_create.role_badge_variant_options.options.pill | dynamic_workbench #22 [9945c78b5f89]; file #lang/de/admin.php [9945c78b5f89] |  |
| a39cf6fbe4f4decb | de | admin.partials.role_list.modal_create.role_badge_variant_options.options.solid | dynamic_workbench #24 [b8b311b0f273]; file #lang/de/admin.php [b8b311b0f273] |  |
| 3f325be78cfec4ec | de | admin.partials.role_list.modal_create.role_badge_variant_options.options.subtle | dynamic_workbench #26 [3eadecfbd533]; file #lang/de/admin.php [3eadecfbd533] |  |
| 56a20590969dcaa5 | de | admin.partials.role_list.modal_edit.role_badge_color_options.options.amber | dynamic_workbench #28 [a5cccbcead7d]; file #lang/de/admin.php [a5cccbcead7d] |  |
| ea88db60535f5ed4 | de | admin.partials.role_list.modal_edit.role_badge_color_options.options.blue | dynamic_workbench #30 [ec7d56a01607]; file #lang/de/admin.php [ec7d56a01607] |  |
| 361a1d8891733a78 | de | admin.partials.role_list.modal_edit.role_badge_color_options.options.cyan | dynamic_workbench #32 [e95bb222e07f]; file #lang/de/admin.php [e95bb222e07f] |  |
| 9cbf781ebf5af2bb | de | admin.partials.role_list.modal_edit.role_badge_color_options.options.emerald | dynamic_workbench #34 [af1ccb31304d]; file #lang/de/admin.php [af1ccb31304d] |  |
| e6725ed4ce039058 | de | admin.partials.role_list.modal_edit.role_badge_color_options.options.fuchsia | dynamic_workbench #36 [458315813c0b]; file #lang/de/admin.php [458315813c0b] |  |
| ad1a739747afad7f | de | admin.partials.role_list.modal_edit.role_badge_color_options.options.green | dynamic_workbench #38 [d486dfbd5fb5]; file #lang/de/admin.php [d486dfbd5fb5] |  |
| 1169e8d82ad3d07b | de | admin.partials.role_list.modal_edit.role_badge_color_options.options.indigo | dynamic_workbench #40 [d81ab149e977]; file #lang/de/admin.php [d81ab149e977] |  |
| a2001b0dfc7e3637 | de | admin.partials.role_list.modal_edit.role_badge_color_options.options.lime | dynamic_workbench #42 [c9665c96a867]; file #lang/de/admin.php [c9665c96a867] |  |
| b1aa53594f5ffd70 | de | admin.partials.role_list.modal_edit.role_badge_color_options.options.orange | dynamic_workbench #44 [78e7771b8b46]; file #lang/de/admin.php [78e7771b8b46] |  |
| 1e51765fe1b2e50c | de | admin.partials.role_list.modal_edit.role_badge_color_options.options.pink | dynamic_workbench #46 [bd38ed773240]; file #lang/de/admin.php [bd38ed773240] |  |
| ddedac00a54586cc | de | admin.partials.role_list.modal_edit.role_badge_color_options.options.purple | dynamic_workbench #48 [7d465fb9b931]; file #lang/de/admin.php [7d465fb9b931] |  |
| 7cb636e605af9473 | de | admin.partials.role_list.modal_edit.role_badge_color_options.options.red | dynamic_workbench #50 [ba19e9c3d5f4]; file #lang/de/admin.php [ba19e9c3d5f4] |  |
| 7a9322cf4a3de776 | de | admin.partials.role_list.modal_edit.role_badge_color_options.options.rose | dynamic_workbench #52 [e10d850954a8]; file #lang/de/admin.php [e10d850954a8] |  |
| 2b4980378309356d | de | admin.partials.role_list.modal_edit.role_badge_color_options.options.sky | dynamic_workbench #54 [b72804afefc3]; file #lang/de/admin.php [b72804afefc3] |  |
| e2fa630661e8accd | de | admin.partials.role_list.modal_edit.role_badge_color_options.options.teal | dynamic_workbench #56 [fa15a5c1d610]; file #lang/de/admin.php [fa15a5c1d610] |  |
| a338db57a79e3ce0 | de | admin.partials.role_list.modal_edit.role_badge_color_options.options.violet | dynamic_workbench #58 [2a36428839a3]; file #lang/de/admin.php [2a36428839a3] |  |
| 7b5fbbfafd062a5e | de | admin.partials.role_list.modal_edit.role_badge_color_options.options.yellow | dynamic_workbench #60 [19dd83f11752]; file #lang/de/admin.php [19dd83f11752] |  |
| f6a3c118b7dffaae | de | admin.partials.role_list.modal_edit.role_badge_color_options.options.zinc | dynamic_workbench #62 [4f89fdf9f1d9]; file #lang/de/admin.php [4f89fdf9f1d9] |  |
| 8c13fea32ca5e52c | de | admin.partials.translation_list.modal_history.timeline.shared.affected_usages.show_usage_locations | file #lang/de/admin.php [e6592fc2245b]; workbench #1545 [e6592fc2245b] |  |
| a6d00bf9c537373a | de | admin.partials.user_list.modal.str_headline_category.options.other | dynamic_workbench #114 [9f3d5f8d94cf]; file #lang/de/admin.php [9f3d5f8d94cf] |  |
| 2ceb3ee763bf6c11 | de | admin.partials.user_list.table.overview_of_all_users_in_the_system_their_email_addresses_and_assigned_roles | file #lang/de/admin.php [7ac82e7b8a26]; workbench #1594 [7ac82e7b8a26] |  |
| 228fa58bd39aa354 | de | admin.permission_list.edit_role_permissions | file #lang/de/admin.php [74f749437ff2]; workbench #1596 [74f749437ff2] |  |
| b00b8ce035402dc8 | de | admin.permission_list.filter.enter_a_search_term_to_filter_permissions_by_name_guard_category_or_description_ | file #lang/de/admin.php [f4c44dc3a642]; legacy #1449 [f4c44dc3a642]; workbench #189 [f4c44dc3a642] |  |
| 2752bb64b2c9256c | de | admin.permission_list.filter.filter_by_assignment | file #lang/de/admin.php [466553ff806c]; legacy #1532 [466553ff806c]; workbench #190 [466553ff806c] |  |
| 946c837a6ec1075c | de | admin.permission_list.filter.filter_by_category | file #lang/de/admin.php [6aa12851c178]; legacy #1445 [6aa12851c178]; workbench #191 [6aa12851c178] |  |
| 847f60fe09cc5a8b | de | admin.permission_list.filter.filter_by_guard | file #lang/de/admin.php [2edd1723b0ff]; legacy #1441 [2edd1723b0ff]; workbench #192 [2edd1723b0ff] |  |
| c14c4b9c64ddbca5 | de | admin.permission_list.filter.filter_by_role | file #lang/de/admin.php [f2e185ec5922]; legacy #1453 [f2e185ec5922]; workbench #193 [f2e185ec5922] |  |
| 0c00a7576f21f4f3 | de | admin.permission_list.filter.filter_by_system | file #lang/de/admin.php [b59deda3b9b4]; legacy #1526 [b59deda3b9b4]; workbench #194 [b59deda3b9b4] |  |
| 3757009bf62242b4 | de | admin.permission_list.filter.search | file #lang/de/admin.php [dd097e77dd73]; legacy #1447 [dd097e77dd73] |  |
| eacb8e01d02409bc | de | admin.permission_list.filter.select_a_category_to_filter_the_list_of_permissions | file #lang/de/admin.php [037c718746a3]; legacy #1451 [037c718746a3]; workbench #196 [037c718746a3] |  |
| b76be9cc081d0f5c | de | admin.permission_list.filter.select_a_guard_to_filter_the_list_of_permissions | file #lang/de/admin.php [e5fed7876065]; legacy #1443 [e5fed7876065]; workbench #197 [e5fed7876065] |  |
| ad8605b819a599fd | de | admin.permission_list.filter.select_a_role_to_filter_the_list_of_permissions | file #lang/de/admin.php [03534d1e1050]; legacy #1534 [03534d1e1050]; workbench #198 [03534d1e1050] |  |
| 4a3f5425b2f22187 | de | admin.permission_list.filter.select_a_system_status_to_filter_the_list_of_permissions | file #lang/de/admin.php [197ba267503b]; legacy #1528 [197ba267503b]; workbench #199 [197ba267503b] |  |
| 3ea1201213e51674 | de | admin.permission_list.filter.select_an_assignment_status_to_filter_the_list_of_permissions | file #lang/de/admin.php [99539c26ec6b]; legacy #1530 [99539c26ec6b]; workbench #200 [99539c26ec6b] |  |
| a51bb7b3374dc102 | de | admin.permission_list.table_permissions.actions_available_for_the_permission_useful_for_identification_and_reference | file #lang/de/admin.php [4e34e92799bd]; legacy #1471 [4e34e92799bd]; workbench #201 [4e34e92799bd] |  |
| 477d41a7a7fb4c87 | de | admin.permission_list.table_permissions.category_of_the_permission_useful_for_identification_and_reference | file #lang/de/admin.php [dfa5dbda898e]; legacy #1459 [dfa5dbda898e]; workbench #202 [dfa5dbda898e] |  |
| 5c3c987137e955ea | de | admin.permission_list.table_permissions.description_of_the_permission_useful_for_identification_and_reference | file #lang/de/admin.php [c37e4b80e1ed]; legacy #1465 [c37e4b80e1ed]; workbench #203 [c37e4b80e1ed] |  |
| a588601bd1c82326 | de | admin.permission_list.table_permissions.flags_indicating_special_attributes_of_the_permission_such_as_system_or_custom_u | file #lang/de/admin.php [833ec9b76159]; legacy #1536 [833ec9b76159]; workbench #204 [833ec9b76159] |  |
| efe3aa8846c0bb03 | de | admin.permission_list.table_permissions.guard_of_the_permission_useful_for_identification_and_reference | file #lang/de/admin.php [c9045270614e]; legacy #1463 [c9045270614e]; workbench #205 [c9045270614e] |  |
| 3d4737d01ae2f1d4 | de | admin.permission_list.table_permissions.name_of_the_permission_useful_for_identification_and_reference | file #lang/de/admin.php [756914ba773a]; legacy #1457 [756914ba773a]; workbench #206 [756914ba773a] |  |
| 202a336b34f80b7c | de | admin.permission_list.table_permissions.number_of_roles_associated_with_the_permission_useful_for_identification_and_ref | file #lang/de/admin.php [fb22955474e6]; legacy #1467 [fb22955474e6]; workbench #207 [fb22955474e6] |  |
| ad78634720179d88 | de | admin.permission_list.table_permissions.pagination | file #lang/de/admin.php [e14d4b6dd1a2]; legacy #1473 [e14d4b6dd1a2] |  |
| cf649e106c0d25e6 | de | admin.permission_list.table_permissions.roles_assigned_to_the_permission_useful_for_identification_and_reference | file #lang/de/admin.php [0a1ed598531a]; legacy #1469 [0a1ed598531a]; workbench #209 [0a1ed598531a] |  |
| ccd299f7f5eb1256 | de | admin.permission_list.table_permissions.sequential_number_of_the_permission_in_the_current_list_useful_for_reference | file #lang/de/admin.php [63594196fdb2]; legacy #1455 [63594196fdb2]; workbench #210 [63594196fdb2] |  |
| d30c7ff4a0cfd7d8 | de | admin.permission_list.table_permissions.sort_order_of_the_permission_useful_for_identification_and_reference | file #lang/de/admin.php [e7588407af65]; legacy #1461 [e7588407af65]; workbench #211 [e7588407af65] |  |
| d3bdde71442e693d | de | admin.permissions.description | file #lang/de/admin.php [244dfb25ddbf]; legacy #3 [244dfb25ddbf]; workbench #213 [244dfb25ddbf] |  |
| a1c1ee91c304d9b3 | de | admin.permissions.filters.description | file #lang/de/admin.php [23126ce765d4]; legacy #9 [23126ce765d4]; workbench #216 [23126ce765d4] |  |
| f8358f4ec2c2ca6a | de | admin.permissions.filters.search.placeholder | file #lang/de/admin.php [a5e2038cfea9]; legacy #15 [a5e2038cfea9]; workbench #219 [a5e2038cfea9] |  |
| 9560245e0b4cb8b8 | de | admin.permissions.labels.assigned_roles | file #lang/de/admin.php [b84713cacaf2]; legacy #21 [b84713cacaf2] |  |
| 070ac4198244f91a | de | admin.permissions.labels.system_permission | file #lang/de/admin.php [bb2cc80ae380]; legacy #25 [bb2cc80ae380]; workbench #224 [bb2cc80ae380] |  |
| 84daad457c9f29d8 | de | admin.permissions.messages.no_changes.heading | file #lang/de/admin.php [e6c5344cca41]; legacy #27 [e6c5344cca41]; workbench #225 [e6c5344cca41] |  |
| 342a38b3acad8168 | de | admin.permissions.messages.no_changes.role_permissions_unchanged | file #lang/de/admin.php [a41968446d99]; legacy #29 [a41968446d99]; workbench #226 [a41968446d99] |  |
| b8c8f299c3146a47 | de | admin.permissions.messages.permission_saved.heading | file #lang/de/admin.php [be080e784937]; legacy #31 [be080e784937]; workbench #227 [be080e784937] |  |
| 0af9fc4e84aa0f13 | de | admin.permissions.messages.permission_saved.text | file #lang/de/admin.php [ae6ec21c5060]; legacy #33 [ae6ec21c5060]; workbench #228 [ae6ec21c5060] |  |
| 60a6f85b83a261dd | de | admin.permissions.messages.role_permissions_saved.heading | file #lang/de/admin.php [85797926a205]; legacy #35 [85797926a205]; workbench #229 [85797926a205] |  |
| 3ee2f3f6f5c8293c | de | admin.permissions.messages.role_permissions_saved.text | file #lang/de/admin.php [2662d7e0b443]; legacy #37 [2662d7e0b443]; workbench #230 [2662d7e0b443] |  |
| d3528529194721de | de | admin.permissions.modals.edit.category_placeholder | file #lang/de/admin.php [1eb028ecdc9b]; legacy #39 [1eb028ecdc9b]; workbench #231 [1eb028ecdc9b] |  |
| a1cb7f92440465a9 | de | admin.permissions.modals.edit.description | file #lang/de/admin.php [2902bc699b75]; legacy #41 [2902bc699b75]; workbench #232 [2902bc699b75] |  |
| fbf41a7c61e38e36 | de | admin.permissions.modals.edit.description_placeholder | file #lang/de/admin.php [64c88bf35241]; legacy #43 [64c88bf35241]; workbench #233 [64c88bf35241] |  |
| 18764bdcf09275c7 | de | admin.permissions.modals.edit.details_section | file #lang/de/admin.php [0d282c1ad6a2]; legacy #45 [0d282c1ad6a2]; workbench #234 [0d282c1ad6a2] |  |
| 96ec8c869493fd88 | de | admin.permissions.modals.edit.editable_scope | file #lang/de/admin.php [faaeda4ded9a]; legacy #47 [faaeda4ded9a]; workbench #235 [faaeda4ded9a] |  |
| dfec08fbce5d055e | de | admin.permissions.modals.edit.metadata_only | file #lang/de/admin.php [9a6bc2d81ac1]; legacy #49 [9a6bc2d81ac1]; workbench #236 [9a6bc2d81ac1] |  |
| 78ef283ae815e119 | de | admin.permissions.modals.edit.metadata_section | file #lang/de/admin.php [4407befaa868]; legacy #51 [4407befaa868]; workbench #237 [4407befaa868] |  |
| a32ac795c6c592a1 | de | admin.permissions.modals.roles.changes | file #lang/de/admin.php [34538baeda2c]; legacy #55 [34538baeda2c]; workbench #239 [34538baeda2c] |  |
| 8867e9d74ca7805d | de | admin.permissions.modals.roles.current_permissions | file #lang/de/admin.php [f3d4ff4971a3]; legacy #57 [f3d4ff4971a3]; workbench #240 [f3d4ff4971a3] |  |
| 26b5026ca4310333 | de | admin.permissions.modals.roles.description | file #lang/de/admin.php [9bbe6a65ae3e]; legacy #59 [9bbe6a65ae3e]; workbench #241 [9bbe6a65ae3e] |  |
| ef37ec3cb00f2b2b | de | admin.permissions.modals.roles.edit_section | file #lang/de/admin.php [74f749437ff2]; legacy #61 [74f749437ff2]; workbench #242 [74f749437ff2] |  |
| 0dd0dcfea21281c6 | de | admin.permissions.modals.roles.overview_for | file #lang/de/admin.php [e89d9ba78ca9]; legacy #63 [e89d9ba78ca9]; workbench #243 [e89d9ba78ca9] |  |
| 62e4bf1fbb562164 | de | admin.permissions.modals.roles.select_role | file #lang/de/admin.php [da9df21712d4]; legacy #65 [da9df21712d4]; workbench #244 [da9df21712d4] |  |
| b22d1c06898cdea4 | de | admin.permissions.modals.roles.selected_permissions | file #lang/de/admin.php [a70a04b43546]; legacy #67 [a70a04b43546]; workbench #245 [a70a04b43546] |  |
| adec268a72cf49b7 | de | admin.permissions.modals.roles.set_permissions_section | file #lang/de/admin.php [29a02b3d7e08]; legacy #69 [29a02b3d7e08]; workbench #246 [29a02b3d7e08] |  |
| 2dc206cf6761961d | de | admin.permissions.modals.roles.title | file #lang/de/admin.php [f7a2b4539419]; legacy #71 [f7a2b4539419]; workbench #247 [f7a2b4539419] |  |
| 45a5de311c4a8f2a | de | admin.permissions.overview.assigned.heading | file #lang/de/admin.php [53db723c7d01]; legacy #73 [53db723c7d01]; workbench #248 [53db723c7d01] |  |
| 761b5dab1229066b | de | admin.permissions.overview.assigned.text | file #lang/de/admin.php [de573a00d32d]; legacy #75 [de573a00d32d]; workbench #249 [de573a00d32d] |  |
| 3d72e80609ef8ab9 | de | admin.permissions.overview.description | file #lang/de/admin.php [f1a19daad79e]; legacy #77 [f1a19daad79e]; workbench #250 [f1a19daad79e] |  |
| 1734e394e84d9bf1 | de | admin.permissions.overview.guards.heading | file #lang/de/admin.php [948e5af90585]; legacy #79 [948e5af90585]; workbench #251 [948e5af90585] |  |
| 6d1b6b983512caae | de | admin.permissions.overview.guards.text | file #lang/de/admin.php [01a360e29319]; legacy #81 [01a360e29319]; workbench #252 [01a360e29319] |  |
| 97c632edae1fd1cc | de | admin.permissions.overview.total.heading | file #lang/de/admin.php [17a75c27a597]; legacy #85 [17a75c27a597]; workbench #254 [17a75c27a597] |  |
| 2b85ac27093852fa | de | admin.permissions.overview.total.text | file #lang/de/admin.php [26e36296fdaa]; legacy #87 [26e36296fdaa]; workbench #255 [26e36296fdaa] |  |
| 68afe6b1d9f53422 | de | admin.permissions.overview.unassigned.heading | file #lang/de/admin.php [32c16fbe352b]; legacy #89 [32c16fbe352b]; workbench #256 [32c16fbe352b] |  |
| da8b89fdbc48cfb7 | de | admin.permissions.overview.unassigned.text | file #lang/de/admin.php [5c99aece3427]; legacy #91 [5c99aece3427]; workbench #257 [5c99aece3427] |  |
| bb32bcd60ca05cb9 | de | admin.permissions.table.columns.flags | file #lang/de/admin.php [f38d9950af4f]; legacy #95 [f38d9950af4f]; workbench #259 [f38d9950af4f] |  |
| d97dec3a73805dd9 | de | admin.permissions.table.columns.sort | file #lang/de/admin.php [59ff9eb120be]; legacy #97 [59ff9eb120be]; workbench #260 [59ff9eb120be] |  |
| 39f9668584ce9c94 | de | admin.permissions.table.description | file #lang/de/admin.php [41ffa433f080]; legacy #99 [41ffa433f080]; workbench #261 [41ffa433f080] |  |
| 03b4815a3964da3e | de | admin.permissions.table.empty | file #lang/de/admin.php [4134deba62be]; legacy #101 [4134deba62be]; workbench #262 [4134deba62be] |  |
| 5a8da309bbcb913b | de | admin.permissions.table.title | file #lang/de/admin.php [4e1b486434c6]; legacy #103 [4e1b486434c6]; workbench #263 [4e1b486434c6] |  |
| 54fc5129e4ce7baf | de | admin.permissions.title | file #lang/de/admin.php [421c413b081d]; legacy #105 [421c413b081d]; workbench #264 [421c413b081d] |  |
| 430d451491c9204b | de | admin.roles.actions.create | file #lang/de/admin.php [5ca419548155]; legacy #147 [5ca419548155] |  |
| de1265d8108787fa | de | admin.roles.badge.color | file #lang/de/admin.php [a952c8f76f58]; legacy #149 [a952c8f76f58]; workbench #266 [a952c8f76f58] |  |
| b1b41dc2aabf8a86 | de | admin.roles.badge.icon | file #lang/de/admin.php [ba0e4afa9340]; legacy #151 [ba0e4afa9340]; workbench #267 [ba0e4afa9340] |  |
| 7f5957f8ba0c0590 | de | admin.roles.badge.title | file #lang/de/admin.php [2a79f6e2049f]; legacy #153 [2a79f6e2049f]; workbench #268 [2a79f6e2049f] |  |
| 193d4125934b9108 | de | admin.roles.badge.variant | file #lang/de/admin.php [a0f6dc6453d1]; legacy #155 [a0f6dc6453d1]; workbench #269 [a0f6dc6453d1] |  |
| 2376b071e509d745 | de | admin.roles.description | file #lang/de/admin.php [74974e50872e]; legacy #157 [74974e50872e]; workbench #270 [74974e50872e] |  |
| 26fab3aca65d8832 | de | admin.roles.labels.assignable_through_ui | file #lang/de/admin.php [f34512aa29dd]; legacy #159 [f34512aa29dd] |  |
| 943030450dbb8d92 | de | admin.roles.labels.assigned_users | file #lang/de/admin.php [68dd65e2f065]; legacy #161 [68dd65e2f065] |  |
| 3d074098afbb79ab | de | admin.roles.labels.sort_order | file #lang/de/admin.php [895f0813e563]; legacy #163 [895f0813e563] |  |
| e8f1f311cf6bbd2d | de | admin.roles.labels.system_role | file #lang/de/admin.php [fa0efc484412]; legacy #165 [fa0efc484412]; workbench #274 [fa0efc484412] |  |
| 598cc46bbed8fa62 | de | admin.roles.messages.not_allowed.heading | file #lang/de/admin.php [b96a7dd01794]; legacy #167 [b96a7dd01794]; workbench #275 [b96a7dd01794] |  |
| 554fc9d145ca1d89 | de | admin.roles.messages.not_allowed.only_super_admins_may_create_roles | file #lang/de/admin.php [e4f58d9dd878]; legacy #169 [e4f58d9dd878]; workbench #276 [e4f58d9dd878] |  |
| f611e929bafd3b5d | de | admin.roles.messages.role_created.heading | file #lang/de/admin.php [c63130bcbc14]; legacy #171 [c63130bcbc14]; workbench #277 [c63130bcbc14] |  |
| e9f676a45f4741e1 | de | admin.roles.messages.role_created.text | file #lang/de/admin.php [e089cdccee8a]; legacy #173 [e089cdccee8a]; workbench #278 [e089cdccee8a] |  |
| beea67209c995b58 | de | admin.roles.messages.role_saved.heading | file #lang/de/admin.php [e2861553effc]; legacy #175 [e2861553effc]; workbench #279 [e2861553effc] |  |
| 816784f1f364e2c1 | de | admin.roles.messages.role_saved.text | file #lang/de/admin.php [1090966172a9]; legacy #177 [1090966172a9]; workbench #280 [1090966172a9] |  |
| 120e4fa79d5f16f4 | de | admin.roles.modals.create.description | file #lang/de/admin.php [834329387a3b]; legacy #179 [834329387a3b]; workbench #281 [834329387a3b] |  |
| 9e7aa56974883e6e | de | admin.roles.modals.create.name_placeholder | file #lang/de/admin.php [6b8c85b8e92a]; legacy #181 [6b8c85b8e92a]; workbench #282 [6b8c85b8e92a] |  |
| 8528381dc2dff933 | de | admin.roles.modals.create.title | file #lang/de/admin.php [a5a7d0f12997]; legacy #183 [a5a7d0f12997]; workbench #283 [a5a7d0f12997] |  |
| 9fce6282d4ba5543 | de | admin.roles.modals.edit.title | file #lang/de/admin.php [67f49ac70353]; legacy #185 [67f49ac70353]; workbench #284 [67f49ac70353] |  |
| f76c0d746df130da | de | admin.roles.preview.new_role | file #lang/de/admin.php [40c2556b746c]; legacy #187 [40c2556b746c]; workbench #285 [40c2556b746c] |  |
| 6feb18413d5ba2d4 | de | admin.roles.title | file #lang/de/admin.php [57a94982dc45]; legacy #189 [57a94982dc45]; workbench #286 [57a94982dc45] |  |
| 228920ceee7d5e2b | de | admin.translation_list.all_visible_language_variation_values_have_been_cleared | file #lang/de/admin.php [175f65ddb123]; legacy #6041 [175f65ddb123]; workbench #287 [175f65ddb123] |  |
| 7de12bfb2cdfb768 | de | admin.translation_list.already_reviewed | file #lang/de/admin.php [1fcf3fe3076a]; legacy #1133 [1fcf3fe3076a]; workbench #288 [1fcf3fe3076a] |  |
| 9df397a99127fffe | de | admin.translation_list.changes_discarded | file #lang/de/admin.php [384aaa2c9000]; legacy #6224 [384aaa2c9000]; workbench #289 [384aaa2c9000] |  |
| 7a452f4ec5d2296a | de | admin.translation_list.cleared | file #lang/de/admin.php [ed04045dd2b0]; legacy #6043 [ed04045dd2b0]; workbench #290 [ed04045dd2b0] |  |
| 832762930d78691e | de | admin.translation_list.copied | file #lang/de/admin.php [c2756e0294f8]; legacy #6096 [c2756e0294f8]; workbench #291 [c2756e0294f8] |  |
| 61df60e4f1503c1b | de | admin.translation_list.filter.all_currently_code_relevant_translation_entries_this_number_changes_when_the_cur | file #lang/de/admin.php [7cfd64e01ee7]; legacy #6210 [7cfd64e01ee7]; workbench #292 [7cfd64e01ee7] |  |
| f24e065f5c5797b5 | de | admin.translation_list.filter.all_currently_code_relevant_translation_entry_types_that_belong_to_the_active_wo | file #lang/de/admin.php [ecc98aafd238]; legacy #6307 [ecc98aafd238]; workbench #293 [ecc98aafd238] |  |
| bb8d876044f944d5 | de | admin.translation_list.filter.all_groups | file #lang/de/admin.php [5fb59d169e89]; legacy #2803 [5fb59d169e89]; workbench #294 [5fb59d169e89] |  |
| 0256238149c49fe2 | de | admin.translation_list.filter.all_namespaces | file #lang/de/admin.php [679234106352]; legacy #2807 [679234106352]; workbench #295 [679234106352] |  |
| f958e1e44924a383 | de | admin.translation_list.filter.all_statuses_within_the_currently_selected_relevance_scope | file #lang/de/admin.php [12b4f518e06b]; legacy #6122 [12b4f518e06b]; workbench #296 [12b4f518e06b] |  |
| 02ac06e1362a7f28 | de | admin.translation_list.filter.archive | file #lang/de/admin.php [272074399332]; legacy #6330 [272074399332]; workbench #297 [272074399332] |  |
| 92e649600311aa65 | de | admin.translation_list.filter.code_relevant_translation_entries_that_are_still_current_but_have_already_been_m | file #lang/de/admin.php [eecf52b15faa]; legacy #5740 [eecf52b15faa]; workbench #298 [eecf52b15faa] |  |
| 982e864b6a7728c2 | de | admin.translation_list.filter.code_relevant_translation_entries_that_are_still_open_in_the_workflow_and_should | file #lang/de/admin.php [7a02a8756f87]; legacy #5716 [7a02a8756f87]; workbench #299 [7a02a8756f87] |  |
| 967635c814264858 | de | admin.translation_list.filter.code_relevant_translation_entries_with_status_ok_that_are_already_consistent_and | file #lang/de/admin.php [5f18a64d7a82]; legacy #5707 [5f18a64d7a82]; workbench #300 [5f18a64d7a82] |  |
| fb5d78a3dce0afa2 | de | admin.translation_list.filter.completed | file #lang/de/admin.php [38ee859a969e]; legacy #6340 [38ee859a969e]; workbench #301 [38ee859a969e] |  |
| aaec4a15eb6e3748 | de | admin.translation_list.filter.concrete_translation_keys_that_already_exist_or_become_real_translations_once_th | file #lang/de/admin.php [4a74567280d8]; legacy #5662 [4a74567280d8]; workbench #302 [4a74567280d8] |  |
| e058217b8def1b0a | de | admin.translation_list.filter.concrete_translation_keys_that_exist_in_code_but_still_miss_the_required_transla | file #lang/de/admin.php [018542a6d347]; legacy #3615 [018542a6d347]; workbench #303 [018542a6d347] |  |
| f3a46cd107c7a688 | de | admin.translation_list.filter.db_and_files_are_currently_in_sync | file #lang/de/admin.php [c035af46fa98]; legacy #6342 [c035af46fa98]; workbench #304 [c035af46fa98] |  |
| f723fa77f5fc85d9 | de | admin.translation_list.filter.db_and_files_are_currently_not_in_sync | file #lang/de/admin.php [e039da5a20fc]; legacy #3604 [e039da5a20fc]; workbench #305 [e039da5a20fc] |  |
| 43a74e49c0dd2d26 | de | admin.translation_list.filter.dynamic_translation_expressions_built_at_runtime_and_usually_left_as_special_cas | file #lang/de/admin.php [094a29c863a9]; legacy #5924 [094a29c863a9]; workbench #306 [094a29c863a9] |  |
| a88ac8e0feaeb1f4 | de | admin.translation_list.filter.enter_a_search_term_to_filter_the_list_of_translations_by_key_or_value_the_searc | file #lang/de/admin.php [b5350330f3c4]; legacy #3414 [b5350330f3c4]; workbench #307 [b5350330f3c4] |  |
| 297db6586beaa3ab | de | admin.translation_list.filter.entries_that_are_no_longer_currently_code_relevant_and_therefore_live_outside_th | file #lang/de/admin.php [0a58916980b4]; legacy #5671 [0a58916980b4]; workbench #308 [0a58916980b4] |  |
| 9a95af2174153a60 | de | admin.translation_list.filter.entries_that_were_derived_or_repaired_from_existing_translation_values_during_ba | file #lang/de/admin.php [3194b7c18e62]; legacy #5922 [3194b7c18e62]; workbench #309 [3194b7c18e62] |  |
| cbc52ae780f463d4 | de | admin.translation_list.filter.files_lang_files_currently_contain_entries_translation_entries_db_currently_has_ | file #lang/de/admin.php [641ef5a1fce8]; legacy #6332 [641ef5a1fce8]; workbench #310 [641ef5a1fce8] |  |
| 0155113e53038037 | de | admin.translation_list.filter.filter_by_group | file #lang/de/admin.php [024448ef1c30]; legacy #3462 [024448ef1c30]; workbench #311 [024448ef1c30] |  |
| babe6bb77e7a382f | de | admin.translation_list.filter.filter_by_namespace | file #lang/de/admin.php [75be2ac0441c]; legacy #2805 [75be2ac0441c]; workbench #312 [75be2ac0441c] |  |
| bafd4a03e50a9bd2 | de | admin.translation_list.filter.filter_by_search | file #lang/de/admin.php [bc918840d67a]; legacy #6338 [bc918840d67a]; workbench #313 [bc918840d67a] |  |
| 3b275783a311f068 | de | admin.translation_list.filter.filters_active | file #lang/de/admin.php [390ea62dcc70]; legacy #3466 [390ea62dcc70]; workbench #314 [390ea62dcc70] |  |
| 6abab37e569f060f | de | admin.translation_list.filter.invalid_translation_calls_that_definitely_need_correction_and_review | file #lang/de/admin.php [5ddcea8d8479]; legacy #6311 [5ddcea8d8479]; workbench #315 [5ddcea8d8479] |  |
| d7401bf24617dd58 | de | admin.translation_list.filter.literal_texts_found_in_code_that_still_need_review_and_usually_need_to_be_replac | file #lang/de/admin.php [9b23ec587993]; legacy #3617 [9b23ec587993]; workbench #316 [9b23ec587993] |  |
| 0516621d48e2cdf6 | de | admin.translation_list.filter.literal_texts_from_code_that_still_need_to_be_reviewed_and_typically_converted_i | file #lang/de/admin.php [77b9e6d8d865]; legacy #5691 [77b9e6d8d865]; workbench #317 [77b9e6d8d865] |  |
| a072e0433042bd05 | de | admin.translation_list.filter.main_language_files_locale | file #lang/de/admin.php [79ec4cdbc3ad]; legacy #6334 [79ec4cdbc3ad]; workbench #318 [79ec4cdbc3ad] |  |
| 43b57e05130a0acd | de | admin.translation_list.filter.obsolete_shows_two_counts_first_the_db_obsolete_work_items_then_the_file_obsolet | file #lang/de/admin.php [a39f22c9b2a9]; legacy #6315 [a39f22c9b2a9]; workbench #319 [a39f22c9b2a9] |  |
| a9727a733bc9b994 | de | admin.translation_list.filter.of | file #lang/de/admin.php [43ce6022b931]; legacy #3468 [43ce6022b931]; workbench #320 [43ce6022b931] |  |
| ffda0c285a230107 | de | admin.translation_list.filter.open | file #lang/de/admin.php [bc385a30b537]; legacy #6303 [bc385a30b537]; workbench #321 [bc385a30b537] |  |
| ce67dbe789481c8d | de | admin.translation_list.filter.refine_the_translation_list_by_key_value_language_and_translation_file | file #lang/de/admin.php [bad59fe68225]; legacy #5770 [bad59fe68225]; workbench #322 [bad59fe68225] |  |
| b233a4ffc4c58e44 | de | admin.translation_list.filter.reviewed_history_entries_including_older_or_no_longer_current_items_that_remain_ | file #lang/de/admin.php [26140eb91120]; legacy #6126 [26140eb91120]; workbench #323 [26140eb91120] |  |
| dc4331e0ee2a46ff | de | admin.translation_list.filter.runtime_generated_translation_values_these_are_special_cases_and_often_remain_dy | file #lang/de/admin.php [6ddcf68c10cb]; legacy #6317 [6ddcf68c10cb]; workbench #324 [6ddcf68c10cb] |  |
| 2a546f1cf752fb95 | de | admin.translation_list.filter.search_by_key_or_value | file #lang/de/admin.php [ba7ae7b5f617]; legacy #3420 [ba7ae7b5f617]; workbench #325 [ba7ae7b5f617] |  |
| d61441989340f591 | de | admin.translation_list.filter.select_a_target_language | file #lang/de/admin.php [2ed891401c5f]; legacy #6328 [2ed891401c5f]; workbench #326 [2ed891401c5f] |  |
| bc05bce323cbaec8 | de | admin.translation_list.filter.select_a_target_language_to_filter_the_list_of_translations | file #lang/de/admin.php [0f2904e6d6da]; legacy #3416 [0f2904e6d6da]; workbench #327 [0f2904e6d6da] |  |
| e811d0378f1fbd7f | de | admin.translation_list.filter.select_a_translation_group_within_the_current_namespace_and_filter_context | file #lang/de/admin.php [a36df7ca9b8e]; legacy #3464 [a36df7ca9b8e]; workbench #328 [a36df7ca9b8e] |  |
| 8ef16b67d82fc821 | de | admin.translation_list.filter.select_a_translation_namespace_to_narrow_the_list_to_the_corresponding_translati | file #lang/de/admin.php [e6a19e2e027a]; legacy #3412 [e6a19e2e027a]; workbench #329 [e6a19e2e027a] |  |
| 17a55ec676f1765c | de | admin.translation_list.filter.select_target_language | file #lang/de/admin.php [2602de5174b1]; legacy #6344 [2602de5174b1]; workbench #330 [2602de5174b1] |  |
| e616bc1368885661 | de | admin.translation_list.filter.showing | file #lang/de/admin.php [ccb27f03f03e]; legacy #3506 [ccb27f03f03e]; workbench #331 [ccb27f03f03e] |  |
| be1b672297c51338 | de | admin.translation_list.filter.status_all | file #lang/de/admin.php [937835581e91]; legacy #3613 [937835581e91]; workbench #332 [937835581e91] |  |
| 11a4dfa4fad62640 | de | admin.translation_list.filter.status_dynamic | file #lang/de/admin.php [d44d6d86ec26]; legacy #5738 [d44d6d86ec26]; workbench #333 [d44d6d86ec26] |  |
| b66a677428f0ec55 | de | admin.translation_list.filter.status_invalid | file #lang/de/admin.php [ce2e95781e9b]; legacy #6313 [ce2e95781e9b]; workbench #334 [ce2e95781e9b] |  |
| 29e994c0075f0cbc | de | admin.translation_list.filter.status_missing | file #lang/de/admin.php [b44283d9352c]; legacy #6319 [b44283d9352c]; workbench #335 [b44283d9352c] |  |
| 2f1936a75a8970b6 | de | admin.translation_list.filter.status_native | file #lang/de/admin.php [cac55bd61fc6]; legacy #3619 [cac55bd61fc6]; workbench #336 [cac55bd61fc6] |  |
| e216424c61f1071e | de | admin.translation_list.filter.status_obsolete | file #lang/de/admin.php [6ddef6b9107f]; legacy #6128 [6ddef6b9107f]; workbench #337 [6ddef6b9107f] |  |
| b419b098cb65bd3b | de | admin.translation_list.filter.status_ok | file #lang/de/admin.php [27aac15d4f4d]; legacy #5734 [27aac15d4f4d]; workbench #338 [27aac15d4f4d] |  |
| 41ffa3eefdecdb56 | de | admin.translation_list.filter.target_language | file #lang/de/admin.php [adadf8178c43]; legacy #3418 [adadf8178c43]; workbench #339 [adadf8178c43] |  |
| ebc89b1a269743a3 | de | admin.translation_list.filter.translations_that_are_already_coherent_and_can_be_actively_used_in_the_lang_file | file #lang/de/admin.php [b801e57cf123]; legacy #6336 [b801e57cf123]; workbench #340 [b801e57cf123] |  |
| 9b33dcff41f3f881 | de | admin.translation_list.filter.type_all | file #lang/de/admin.php [6b2acad69600]; legacy #6309 [6b2acad69600]; workbench #341 [6b2acad69600] |  |
| 3c6d815ad9cfe6d7 | de | admin.translation_list.filter.type_archive | file #lang/de/admin.php [c7ebb9bf7b96]; legacy #6305 [c7ebb9bf7b96]; workbench #342 [c7ebb9bf7b96] |  |
| f259f90f742e4d83 | de | admin.translation_list.filter.type_backfill | file #lang/de/admin.php [7ed6afebbf27]; legacy #5891 [7ed6afebbf27]; workbench #343 [7ed6afebbf27] |  |
| f4592755a755a7fd | de | admin.translation_list.filter.type_dynamic | file #lang/de/admin.php [08908fbb767b]; legacy #5680 [08908fbb767b]; workbench #344 [08908fbb767b] |  |
| 4724233d0cf0697c | de | admin.translation_list.filter.type_key | file #lang/de/admin.php [7d0761362007]; legacy #5911 [7d0761362007]; workbench #345 [7d0761362007] |  |
| 744cc76c657c6f77 | de | admin.translation_list.filter.type_native | file #lang/de/admin.php [283f4eae2cf5]; legacy #5913 [283f4eae2cf5]; workbench #346 [283f4eae2cf5] |  |
| 7a8c02bc36c9b853 | de | admin.translation_list.filter.type_vendor | file #lang/de/admin.php [80abb360599d]; legacy #5887 [80abb360599d]; workbench #347 [80abb360599d] |  |
| 93fb0ef79ce2369e | de | admin.translation_list.filter.vendor_translation_entries_imported_from_package_language_files | file #lang/de/admin.php [2ddec053a9aa]; legacy #5689 [2ddec053a9aa]; workbench #348 [2ddec053a9aa] |  |
| 25331be7e84ecfae | de | admin.translation_list.filter.workflow_all | file #lang/de/admin.php [3f76ead3c7a0]; legacy #6208 [3f76ead3c7a0]; workbench #349 [3f76ead3c7a0] |  |
| 673332f7e04bae84 | de | admin.translation_list.filter.workflow_completed | file #lang/de/admin.php [82cc8e692de8]; legacy #6124 [82cc8e692de8]; workbench #350 [82cc8e692de8] |  |
| d3c9f8f4347dbc8c | de | admin.translation_list.filter.workflow_history | file #lang/de/admin.php [ea9d9bd9fa49]; legacy #3610 [ea9d9bd9fa49]; workbench #351 [ea9d9bd9fa49] |  |
| fe57061f613713f7 | de | admin.translation_list.filter.workflow_open | file #lang/de/admin.php [5ccecf27ebcf]; legacy #6120 [5ccecf27ebcf]; workbench #352 [5ccecf27ebcf] |  |
| 919d8a3a76ff4773 | de | admin.translation_list.filter.workflow_reviewed | file #lang/de/admin.php [da7a35232551]; legacy #6130 [da7a35232551]; workbench #353 [da7a35232551] |  |
| 6b1855680a50c30f | de | admin.translation_list.meta.active_filters | file #lang/de/admin.php [75e581194056]; legacy #5736 [75e581194056]; workbench #354 [75e581194056] |  |
| c7d56addfa710aca | de | admin.translation_list.meta.backfill | file #lang/de/admin.php [f5b32e4786dd]; legacy #2775 [f5b32e4786dd]; workbench #355 [f5b32e4786dd] |  |
| 0c5df8d083f57a20 | de | admin.translation_list.meta.current_result | file #lang/de/admin.php [4e01424f0e8e]; legacy #2795 [4e01424f0e8e]; workbench #356 [4e01424f0e8e] |  |
| f20c66a47c580c9d | de | admin.translation_list.meta.filter_state | file #lang/de/admin.php [3b8b090b31fb]; legacy #2566 [3b8b090b31fb]; workbench #357 [3b8b090b31fb] |  |
| 9a9cdf37f03ae482 | de | admin.translation_list.meta.filtered | file #lang/de/admin.php [46dc7e7e4fcd]; legacy #2782 [46dc7e7e4fcd]; workbench #358 [46dc7e7e4fcd] |  |
| 9979a0a27689be8d | de | admin.translation_list.meta.group | file #lang/de/admin.php [b4c0226585b5]; legacy #2540 [b4c0226585b5] |  |
| 7b45a7c23d7d6ece | de | admin.translation_list.meta.language | file #lang/de/admin.php [f2a1cc71fa59]; legacy #2789 [f2a1cc71fa59]; workbench #360 [f2a1cc71fa59] |  |
| 636a26b059959fb1 | de | admin.translation_list.meta.matching_keys | file #lang/de/admin.php [bf841a2e1459]; legacy #5445 [bf841a2e1459]; workbench #361 [bf841a2e1459] |  |
| 65125efe14d7b00c | de | admin.translation_list.meta.missing_and_dynamic_translation_entries_requiring_review | file #lang/de/admin.php [05147c854857]; legacy #5872 [05147c854857]; workbench #362 [05147c854857] |  |
| 596c7404e8845cb7 | de | admin.translation_list.meta.namespace | file #lang/de/admin.php [884dc3c188cc]; legacy #2546 [884dc3c188cc] |  |
| 878204858b4f3fdc | de | admin.translation_list.meta.native_texts_reconstructed_from_existing_en_translation_values | file #lang/de/admin.php [b0539d452dfb]; legacy #2780 [b0539d452dfb]; workbench #364 [b0539d452dfb] |  |
| 2620adedd8bf13a7 | de | admin.translation_list.meta.no_active_filters | file #lang/de/admin.php [907224c3513d]; legacy #2536 [907224c3513d]; workbench #365 [907224c3513d] |  |
| 6621ee7fbd23e2bd | de | admin.translation_list.meta.no_filters_are_currently_active | file #lang/de/admin.php [0c25a38bc3ad]; legacy #5534 [0c25a38bc3ad]; workbench #366 [0c25a38bc3ad] |  |
| 873506ab0a98d55b | de | admin.translation_list.meta.obsolete | file #lang/de/admin.php [d9075b29ee27]; legacy #2799 [d9075b29ee27]; workbench #367 [d9075b29ee27] |  |
| d6fd26ee56b6974c | de | admin.translation_list.meta.ok | file #lang/de/admin.php [565339bc4d33]; legacy #5520 [565339bc4d33]; workbench #368 [565339bc4d33] |  |
| e4da3d46b6652dde | de | admin.translation_list.meta.one_or_more_filters_are_currently_active | file #lang/de/admin.php [e1149c67d040]; legacy #5610 [e1149c67d040]; workbench #369 [e1149c67d040] |  |
| e48a727e1e76c652 | de | admin.translation_list.meta.only_problems | file #lang/de/admin.php [9b4b2621981f]; legacy #2786 [9b4b2621981f]; workbench #370 [9b4b2621981f] |  |
| 8d84db767c53bed7 | de | admin.translation_list.meta.overview_of_the_active_translation_filters_and_the_currently_matching_result_set | file #lang/de/admin.php [53e1228dacd5]; legacy #2797 [53e1228dacd5]; workbench #371 [53e1228dacd5] |  |
| 9750074cab12da77 | de | admin.translation_list.meta.per_page | file #lang/de/admin.php [fe7c4b7cd613]; legacy #2542 [fe7c4b7cd613] |  |
| 0e03a34be33fc767 | de | admin.translation_list.meta.problems | file #lang/de/admin.php [e9f99fdf5a56]; legacy #5518 [e9f99fdf5a56]; workbench #373 [e9f99fdf5a56] |  |
| 5aba60f1f38140fe | de | admin.translation_list.meta.regular_audit_keys_without_special_origin_classification | file #lang/de/admin.php [093e27e5e7ed]; legacy #5528 [093e27e5e7ed]; workbench #374 [093e27e5e7ed] |  |
| 5b0ea6cfcd07d27f | de | admin.translation_list.meta.status_dynamic | file #lang/de/admin.php [c067003d946b]; legacy #6212 [c067003d946b]; workbench #375 [c067003d946b] |  |
| 0346d2bbede57976 | de | admin.translation_list.meta.status_native | file #lang/de/admin.php [3fb6d221c573]; legacy #5725 [3fb6d221c573]; workbench #376 [3fb6d221c573] |  |
| 22d7faad8f85b7a7 | de | admin.translation_list.meta.total_keys | file #lang/de/admin.php [be119911c2df]; legacy #5772 [be119911c2df]; workbench #377 [be119911c2df] |  |
| 187cd7f8cbec3f71 | de | admin.translation_list.meta.translated | file #lang/de/admin.php [22c9ef6d4970]; legacy #2534 [22c9ef6d4970]; workbench #378 [22c9ef6d4970] |  |
| 5cedb10bbe4aa9d6 | de | admin.translation_list.meta.translation_keys_classified_as_native_source_entries | file #lang/de/admin.php [f964aa55ed6d]; legacy #3544 [f964aa55ed6d]; workbench #379 [f964aa55ed6d] |  |
| 2736b52045604918 | de | admin.translation_list.meta.translation_keys_currently_known_in_the_audit_table | file #lang/de/admin.php [fc1408bd8397]; legacy #5870 [fc1408bd8397]; workbench #380 [fc1408bd8397] |  |
| 0dcfb0f3172ae19d | de | admin.translation_list.meta.translation_keys_currently_marked_as_obsolete | file #lang/de/admin.php [cc292cd9a0b5]; legacy #2554 [cc292cd9a0b5]; workbench #381 [cc292cd9a0b5] |  |
| 117961d6c7b7f8ef | de | admin.translation_list.meta.translation_keys_currently_marked_as_ok | file #lang/de/admin.php [881cc1336309]; legacy #5522 [881cc1336309]; workbench #382 [881cc1336309] |  |
| 04464a966959a8d8 | de | admin.translation_list.meta.translation_keys_currently_marked_with_dynamic_status | file #lang/de/admin.php [826d059140fe]; legacy #5614 [826d059140fe]; workbench #383 [826d059140fe] |  |
| 4300ad3c72c04f90 | de | admin.translation_list.meta.translation_keys_currently_marked_with_native_status | file #lang/de/admin.php [6824aabdba39]; legacy #5532 [6824aabdba39]; workbench #384 [6824aabdba39] |  |
| 9f6639cbc069cc63 | de | admin.translation_list.meta.translation_keys_imported_from_vendor_package_sources | file #lang/de/admin.php [ba84647e3fc3]; legacy #2784 [ba84647e3fc3]; workbench #385 [ba84647e3fc3] |  |
| 0fbe5b2243373f7e | de | admin.translation_list.meta.translation_keys_matching_the_current_filters | file #lang/de/admin.php [19eef6b88262]; legacy #5612 [19eef6b88262]; workbench #386 [19eef6b88262] |  |
| 5949b5941817098a | de | admin.translation_list.meta.translation_keys_with_missing_values_or_required_follow_up | file #lang/de/admin.php [599f650c1927]; legacy #5524 [599f650c1927]; workbench #387 [599f650c1927] |  |
| 432fda2816dd4ae2 | de | admin.translation_list.meta.type_native | file #lang/de/admin.php [42f2017b86e8]; legacy #5530 [42f2017b86e8]; workbench #388 [42f2017b86e8] |  |
| e8181828091ac1f0 | de | admin.translation_list.meta.unfiltered | file #lang/de/admin.php [f4b67b8cf66e]; legacy #2778 [f4b67b8cf66e]; workbench #389 [f4b67b8cf66e] |  |
| d4a527ddcda78a65 | de | admin.translation_list.meta.vendor | file #lang/de/admin.php [9080a78b3e99]; legacy #5526 [9080a78b3e99]; workbench #390 [9080a78b3e99] |  |
| 3d53ee7e9faa4b52 | de | admin.translation_list.meta.workflow | file #lang/de/admin.php [e797d8bcc97f]; legacy #2791 [e797d8bcc97f] |  |
| b5b2a6623a970257 | de | admin.translation_list.modal.copy_to_translation_key | file #lang/de/admin.php [c0200f62f929]; legacy #2801 [c0200f62f929]; workbench #392 [c0200f62f929] |  |
| cdf2fd706dffff2b | de | admin.translation_list.modal.current_raw | file #lang/de/admin.php [513406e9d83e]; legacy #6197 [513406e9d83e]; workbench #393 [513406e9d83e] |  |
| e308825d1da21a0a | de | admin.translation_list.modal.edit | file #lang/de/admin.php [84e45ee73411]; legacy #2683 [84e45ee73411] |  |
| 6a25934d99b24919 | de | admin.translation_list.modal.group | file #lang/de/admin.php [b4c0226585b5]; legacy #2564 [b4c0226585b5]; workbench #395 [b4c0226585b5] |  |
| 734bef9e4c26f92c | de | admin.translation_list.modal.key_obsolete_diff | file #lang/de/admin.php [45d18fa8ea97]; legacy #6218 [45d18fa8ea97]; workbench #396 [45d18fa8ea97] |  |
| 43236b78c0c16e12 | de | admin.translation_list.modal.key_suggestion_check | file #lang/de/admin.php [fda842664e3f]; legacy #3442 [fda842664e3f]; workbench #397 [fda842664e3f] |  |
| 5f4535d0399b4f87 | de | admin.translation_list.modal.line | file #lang/de/admin.php [e3ee87b136fa]; legacy #2679 [e3ee87b136fa]; workbench #398 [e3ee87b136fa] |  |
| 22999faeb67648d0 | de | admin.translation_list.modal.matches_suggested_key | file #lang/de/admin.php [709b146bf599]; legacy #2560 [709b146bf599]; workbench #399 [709b146bf599] |  |
| abfcfdea8526bdbe | de | admin.translation_list.modal.missing_key | file #lang/de/admin.php [70e5b8370cc9]; legacy #2556 [70e5b8370cc9]; workbench #400 [70e5b8370cc9] |  |
| 7489a9f5d9519867 | de | admin.translation_list.modal.namespace | file #lang/de/admin.php [884dc3c188cc]; legacy #2568 [884dc3c188cc]; workbench #401 [884dc3c188cc] |  |
| 0b6a7852c735d5aa | de | admin.translation_list.modal.native_text | file #lang/de/admin.php [1168506551b5]; legacy #3518 [1168506551b5]; workbench #402 [1168506551b5] |  |
| 6c66b6dfc3df518b | de | admin.translation_list.modal.no_key_shape_diff_detected_obsolete_is_likely_caused_by_missing_in_code_usage | file #lang/de/admin.php [75785fc7404b]; legacy #6024 [75785fc7404b]; workbench #403 [75785fc7404b] |  |
| 655cb6fe98f44bf3 | de | admin.translation_list.modal.no_original_raw | file #lang/de/admin.php [4aee009c2d77]; legacy #2677 [4aee009c2d77]; workbench #404 [4aee009c2d77] |  |
| d18fb1d9dc081b6f | de | admin.translation_list.modal.no_original_raw_reference | file #lang/de/admin.php [901af8f77724]; legacy #2675 [901af8f77724]; workbench #405 [901af8f77724] |  |
| fd54259047e03caf | de | admin.translation_list.modal.no_original_raw_reference_has_been_captured_for_this_usage_yet | file #lang/de/admin.php [16e59e4e0119]; legacy #2667 [16e59e4e0119]; workbench #406 [16e59e4e0119] |  |
| a658552f512e4f1d | de | admin.translation_list.modal.no_suggested_key_is_available_for_this_entry | file #lang/de/admin.php [2dd2c938d0cf]; legacy #6214 [2dd2c938d0cf]; workbench #407 [2dd2c938d0cf] |  |
| bd24b8df0e641426 | de | admin.translation_list.modal.no_suggestion | file #lang/de/admin.php [5694e0601a4d]; legacy #2686 [5694e0601a4d]; workbench #408 [5694e0601a4d] |  |
| 8dcf31312ee9d6b4 | de | admin.translation_list.modal.no_translation_key_is_set_the_suggested_key_can_be_used_as_a_starting_point | file #lang/de/admin.php [bc60f64c9753]; legacy #5447 [bc60f64c9753]; workbench #409 [bc60f64c9753] |  |
| a9df114d09a06e29 | de | admin.translation_list.modal.no_translation_key_selected | file #lang/de/admin.php [6f4e7ec67ee8]; legacy #2720 [6f4e7ec67ee8]; workbench #410 [6f4e7ec67ee8] |  |
| 2850c527e07bd799 | de | admin.translation_list.modal.no_translation_values_available | file #lang/de/admin.php [92c2852013b4]; legacy #3520 [92c2852013b4]; workbench #411 [92c2852013b4] |  |
| 5c0104ce446f4c42 | de | admin.translation_list.modal.open_next_review_entry | file #lang/de/admin.php [c7377d47948e]; legacy #3445 [c7377d47948e]; workbench #412 [c7377d47948e] |  |
| 3a8c3cdea96d87f0 | de | admin.translation_list.modal.original_changed | file #lang/de/admin.php [274268126829]; legacy #2673 [274268126829]; workbench #413 [274268126829] |  |
| db2ae01cfae1197e | de | admin.translation_list.modal.original_raw | file #lang/de/admin.php [287f00dcc5f6]; legacy #2681 [287f00dcc5f6]; workbench #414 [287f00dcc5f6] |  |
| 64f24f8604a059cd | de | admin.translation_list.modal.original_raw_differs | file #lang/de/admin.php [eb65e1a0ae9f]; legacy #6033 [eb65e1a0ae9f]; workbench #415 [eb65e1a0ae9f] |  |
| 3be4894849d18ee9 | de | admin.translation_list.modal.original_raw_unchanged | file #lang/de/admin.php [1998f64ca24b]; legacy #5889 [1998f64ca24b]; workbench #416 [1998f64ca24b] |  |
| e2f28d68471f00fc | de | admin.translation_list.modal.original_unchanged | file #lang/de/admin.php [ae2a7917c78c]; legacy #5443 [ae2a7917c78c]; workbench #417 [ae2a7917c78c] |  |
| fc0ccf9f6df6ae84 | de | admin.translation_list.modal.path | file #lang/de/admin.php [be2fbefc5c27]; legacy #2669 [be2fbefc5c27]; workbench #418 [be2fbefc5c27] |  |
| 67720990b5262747 | de | admin.translation_list.modal.read_only_review_of_the_selected_translation_key_its_values_and_usage_metadata | file #lang/de/admin.php [c0d6ff5c435d]; legacy #5608 [c0d6ff5c435d]; workbench #419 [c0d6ff5c435d] |  |
| 05be3e981013417c | de | admin.translation_list.modal.source | file #lang/de/admin.php [0378894a522e]; legacy #2558 [0378894a522e]; workbench #420 [0378894a522e] |  |
| bf7d094c22d530b9 | de | admin.translation_list.modal.suggested_key | file #lang/de/admin.php [c7e14c5bb66b]; legacy #6216 [c7e14c5bb66b] |  |
| f37cfb79161cdef4 | de | admin.translation_list.modal.the_current_key_differs_from_the_generated_suggestion_this_can_be_intentional_bu | file #lang/de/admin.php [0344368fc6e6]; legacy #2741 [0344368fc6e6]; workbench #422 [0344368fc6e6] |  |
| 028a028601379960 | de | admin.translation_list.modal.the_current_key_matches_the_generated_suggestion | file #lang/de/admin.php [23c4ff90072c]; legacy #5781 [23c4ff90072c]; workbench #423 [23c4ff90072c] |  |
| 0d1f3b06acc3d5c1 | de | admin.translation_list.modal.the_current_raw_usage_snippet_differs_from_the_original_raw_reference_the_origin | file #lang/de/admin.php [cbc537c05b5d]; legacy #2671 [cbc537c05b5d]; workbench #424 [cbc537c05b5d] |  |
| f95b4ddc04b20448 | de | admin.translation_list.modal.the_current_raw_usage_snippet_matches_the_original_raw_reference_captured_for_th | file #lang/de/admin.php [9419f2de1905]; legacy #5441 [9419f2de1905]; workbench #425 [9419f2de1905] |  |
| 787ef98210fce11d | de | admin.translation_list.modal.translation_key_review | file #lang/de/admin.php [b7a19660f67a]; legacy #2516 [b7a19660f67a]; workbench #426 [b7a19660f67a] |  |
| bbe8efed4926ca46 | de | admin.translation_list.modal.usage | file #lang/de/admin.php [947c0a678ed5]; legacy #2562 [947c0a678ed5]; workbench #427 [947c0a678ed5] |  |
| a19efcc8185590c5 | de | admin.translation_list.modal.usages | file #lang/de/admin.php [523fc8bc7355]; legacy #2665 [523fc8bc7355]; workbench #428 [523fc8bc7355] |  |
| 9f5a05dad91fcb05 | de | admin.translation_list.modal.values | file #lang/de/admin.php [6fac4487cf49]; legacy #5433 [6fac4487cf49]; workbench #429 [6fac4487cf49] |  |
| fe4d020a1fa26e09 | de | admin.translation_list.modal.wavy_underline_marks_only_the_differing_key_block | file #lang/de/admin.php [402f49e0968c]; legacy #6220 [402f49e0968c]; workbench #430 [402f49e0968c] |  |
| 089a563487968ae1 | de | admin.translation_list.modal_edit.below_are_the_translation_values_for_each_language_you_can_edit_the_values_and_s | file #lang/de/admin.php [e87c35f34378]; legacy #3548 [e87c35f34378]; workbench #431 [e87c35f34378] |  |
| f41b50917290029b | de | admin.translation_list.modal_edit.clear_all_language_variations | file #lang/de/admin.php [217a2893618c]; legacy #6195 [217a2893618c]; workbench #432 [217a2893618c] |  |
| 1ef33c28400ad28a | de | admin.translation_list.modal_edit.copy_to_all_language_variations | file #lang/de/admin.php [3d22a2078963]; legacy #6116 [3d22a2078963]; workbench #433 [3d22a2078963] |  |
| c9ec9a30c14ea099 | de | admin.translation_list.modal_edit.copy_to_en_value | file #lang/de/admin.php [2e412fbf5c7e]; legacy #3552 [2e412fbf5c7e]; workbench #434 [2e412fbf5c7e] |  |
| 6583ec71e55c061a | de | admin.translation_list.modal_edit.edit_mode | file #lang/de/admin.php [3a5aea5577cc]; legacy #3560 [3a5aea5577cc]; workbench #435 [3a5aea5577cc] |  |
| 69a21418e077e63a | de | admin.translation_list.modal_edit.edit_translation_values_for_the_selected_key_key_metadata_is_read_only | file #lang/de/admin.php [11d746ad6eeb]; legacy #2528 [11d746ad6eeb]; workbench #436 [11d746ad6eeb] |  |
| 71de64f1f0736112 | de | admin.translation_list.modal_edit.language_variations | file #lang/de/admin.php [2155d38e1796]; legacy #6118 [2155d38e1796]; workbench #437 [2155d38e1796] |  |
| d61e3420fbd1cb51 | de | admin.translation_list.modal_edit.mark_this_obsolete_entry_as_reviewed_so_it_is_removed_from_the_default_open_work | file #lang/de/admin.php [8f5c71750966]; legacy #2518 [8f5c71750966]; workbench #438 [8f5c71750966] |  |
| 8bcf1618e740b3d3 | de | admin.translation_list.modal_edit.no_usage_records_available | file #lang/de/admin.php [383b8496a00d]; legacy #3460 [383b8496a00d]; workbench #439 [383b8496a00d] |  |
| cb5909762b00d5a6 | de | admin.translation_list.modal_edit.only_translation_values_are_editable_keys_native_text_and_usage_metadata_are_rea | file #lang/de/admin.php [522e643187b3]; legacy #3508 [522e643187b3]; workbench #440 [522e643187b3] |  |
| 64e8371278b6168b | de | admin.translation_list.modal_edit.open_next_editable_entry | file #lang/de/admin.php [254ac8d04c4c]; legacy #2743 [254ac8d04c4c] |  |
| fd7790b0891006b3 | de | admin.translation_list.modal_edit.readonly_source_locations_where_this_translation_key_is_currently_used | file #lang/de/admin.php [dcae872c7c31]; legacy #3458 [dcae872c7c31]; workbench #442 [dcae872c7c31] |  |
| 4b5a00f133c1420e | de | admin.translation_list.modal_edit.reviewed | file #lang/de/admin.php [80086dbdadd3]; legacy #3558 [80086dbdadd3]; workbench #443 [80086dbdadd3] |  |
| 72bb43d804a15cae | de | admin.translation_list.modal_edit.sub_language_values | file #lang/de/admin.php [5c9736d2eb3d]; legacy #5449 [5c9736d2eb3d]; workbench #444 [5c9736d2eb3d] |  |
| d1bda1b4be4e95b1 | de | admin.translation_list.modal_edit.the_native_text_is_the_original_string_in_the_source_language_it_serves_as_a_ref | file #lang/de/admin.php [2f7ee5b5b9b1]; legacy #3453 [2f7ee5b5b9b1]; workbench #445 [2f7ee5b5b9b1] |  |
| b763c484654444c0 | de | admin.translation_list.modal_edit.translation_key | file #lang/de/admin.php [fd8bcb0bd7ca]; legacy #3562 [fd8bcb0bd7ca]; workbench #446 [fd8bcb0bd7ca] |  |
| 4f27e4674afe712f | de | admin.translation_list.modal_edit.translation_values | file #lang/de/admin.php [80a659f310c2]; legacy #3546 [80a659f310c2]; workbench #447 [80a659f310c2] |  |
| 67ad2535031ac21c | de | admin.translation_list.modal_edit.translation_values_edit | file #lang/de/admin.php [26649341d60c]; legacy #2552 [26649341d60c]; workbench #448 [26649341d60c] |  |
| d33787b508900424 | de | admin.translation_list.modal_edit.usage_locations | file #lang/de/admin.php [f28ddecad9fa]; legacy #3455 [f28ddecad9fa]; workbench #449 [f28ddecad9fa] |  |
| cd97f70fdcfeb16a | de | admin.translation_list.modal_history.audit_events_and_change_history_for_the_selected_translation_key | file #lang/de/admin.php [a110bc66d5c1]; legacy #2520 [a110bc66d5c1]; workbench #450 [a110bc66d5c1] |  |
| a4bb923954f95596 | de | admin.translation_list.modal_history.context | file #lang/de/admin.php [d2f889a15496]; legacy #2524 [d2f889a15496]; workbench #451 [d2f889a15496] |  |
| 3ba58d9ff8126e66 | de | admin.translation_list.modal_history.event | file #lang/de/admin.php [af9e77ea9137]; legacy #5854 [af9e77ea9137]; workbench #452 [af9e77ea9137] |  |
| 057725f5b9914a24 | de | admin.translation_list.modal_history.events | file #lang/de/admin.php [39ad234261d7]; legacy #5838 [39ad234261d7]; workbench #453 [39ad234261d7] |  |
| c93280a7c7db6c95 | de | admin.translation_list.modal_history.latest_audit_events_for_this_translation_key | file #lang/de/admin.php [d4c8d0462bf2]; legacy #5783 [d4c8d0462bf2]; workbench #454 [d4c8d0462bf2] |  |
| 545ecc368a491541 | de | admin.translation_list.modal_history.new_location | file #lang/de/admin.php [75b593e546fd]; legacy #2514 [75b593e546fd]; workbench #455 [75b593e546fd] |  |
| c73448f85e4f6e0b | de | admin.translation_list.modal_history.new_status | file #lang/de/admin.php [298a685d3bca]; legacy #2508 [298a685d3bca]; workbench #456 [298a685d3bca] |  |
| 7d993aea147f79f2 | de | admin.translation_list.modal_history.new_value | file #lang/de/admin.php [acd1fb5aaec3]; legacy #2510 [acd1fb5aaec3]; workbench #457 [acd1fb5aaec3] |  |
| 045ffd006167b542 | de | admin.translation_list.modal_history.no_history_events_available_for_this_translation_key | file #lang/de/admin.php [750236e5aba8]; legacy #2544 [750236e5aba8]; workbench #458 [750236e5aba8] |  |
| 8f9216009b91b5c4 | de | admin.translation_list.modal_history.old_location | file #lang/de/admin.php [22e43f85c26f]; legacy #2512 [22e43f85c26f]; workbench #459 [22e43f85c26f] |  |
| b208e74e9820d5b0 | de | admin.translation_list.modal_history.old_status | file #lang/de/admin.php [f97d2954f381]; legacy #2526 [f97d2954f381]; workbench #460 [f97d2954f381] |  |
| 809771701fd10978 | de | admin.translation_list.modal_history.old_value | file #lang/de/admin.php [c66592dbe902]; legacy #2522 [c66592dbe902]; workbench #461 [c66592dbe902] |  |
| 9b4fa38fa933fa2e | de | admin.translation_list.modal_history.reason | file #lang/de/admin.php [b45f40c737c3]; legacy #2530 [b45f40c737c3]; workbench #462 [b45f40c737c3] |  |
| 9dde3b126952010f | de | admin.translation_list.modal_history.translation_history | file #lang/de/admin.php [656902225e73]; legacy #2548 [656902225e73]; workbench #463 [656902225e73] |  |
| fdb41bd26c37b013 | de | admin.translation_list.no_suggested_key_available | file #lang/de/admin.php [785172fb4238]; legacy #1117 [785172fb4238]; workbench #464 [785172fb4238] |  |
| ec1ac2eb545e673e | de | admin.translation_list.obsolete_entry_reviewed | file #lang/de/admin.php [ef81a10e8910]; legacy #6039 [ef81a10e8910]; workbench #465 [ef81a10e8910] |  |
| b9382adb0efc57ac | de | admin.translation_list.only_obsolete_entries_can_be_marked_as_reviewed | file #lang/de/admin.php [e2912f5305c1]; legacy #1131 [e2912f5305c1]; workbench #466 [e2912f5305c1] |  |
| d8daaf0b2639b735 | de | admin.translation_list.review_not_applicable | file #lang/de/admin.php [fbb058462ff4]; legacy #1129 [fbb058462ff4]; workbench #467 [fbb058462ff4] |  |
| 848b88fb62145e79 | de | admin.translation_list.review_translation_keys_missing_values_native_strings_dynamic_calls_and_audit_st | file #lang/de/admin.php [71451c95d482]; legacy #1111 [71451c95d482]; workbench #468 [71451c95d482] |  |
| 19fd1ac585a00f1c | de | admin.translation_list.suggested_key_already_applied | file #lang/de/admin.php [f322577c88d5]; legacy #1121 [f322577c88d5]; workbench #469 [f322577c88d5] |  |
| 6f9b961b9d4fda61 | de | admin.translation_list.suggested_key_applied | file #lang/de/admin.php [8071b77da1a1]; legacy #1125 [8071b77da1a1]; workbench #470 [8071b77da1a1] |  |
| 983a359c09bd76f2 | de | admin.translation_list.table.current_status_of_the_translation_key_useful_for_identification_and_reference | file #lang/de/admin.php [def069a59058]; legacy #3526 [def069a59058]; workbench #471 [def069a59058] |  |
| 2dc9aab3e859e5f7 | de | admin.translation_list.table.differs_from_suggested_key | file #lang/de/admin.php [3f3fd5dddf0b]; legacy #2729 [3f3fd5dddf0b]; workbench #472 [3f3fd5dddf0b] |  |
| 670640499af5fd3d | de | admin.translation_list.table.history | file #lang/de/admin.php [4eaa806899cc]; legacy #5878 [4eaa806899cc]; workbench #473 [4eaa806899cc] |  |
| bb26e3a26e0c71ec | de | admin.translation_list.table.internal_database_id_of_the_translation_key | file #lang/de/admin.php [24121dedc775]; legacy #2739 [24121dedc775]; workbench #474 [24121dedc775] |  |
| 8c85826382790f73 | de | admin.translation_list.table.key | file #lang/de/admin.php [1fbd7a00cacb]; legacy #2731 [1fbd7a00cacb]; workbench #475 [1fbd7a00cacb] |  |
| 0bba6bfd0c85cb57 | de | admin.translation_list.table.key_suggested_key | file #lang/de/admin.php [5e48696b5fc8]; legacy #5536 [5e48696b5fc8]; workbench #476 [5e48696b5fc8] |  |
| ac1044fd96f291f1 | de | admin.translation_list.table.language_translations | file #lang/de/admin.php [8a7539868f08]; legacy #3550 [8a7539868f08]; workbench #477 [8a7539868f08] |  |
| 1ea3bfc8d5c1a92b | de | admin.translation_list.table.last_seen | file #lang/de/admin.php [32522f222b66]; legacy #6114 [32522f222b66]; workbench #478 [32522f222b66] |  |
| d4536e58849bc606 | de | admin.translation_list.table.mark_reviewed | file #lang/de/admin.php [cb2567017a07]; legacy #2737 [cb2567017a07]; workbench #479 [cb2567017a07] |  |
| 3ef8e794e032de6f | de | admin.translation_list.table.no_history_entries_available | file #lang/de/admin.php [19889258e577]; legacy #5794 [19889258e577] |  |
| fdf6676ea0574244 | de | admin.translation_list.table.no_translation_records_found | file #lang/de/admin.php [9442eaf78f26]; legacy #6105 [9442eaf78f26]; workbench #481 [9442eaf78f26] |  |
| 64124c268a48c3cb | de | admin.translation_list.table.obsolete_key | file #lang/de/admin.php [440f4c901f39]; legacy #2733 [440f4c901f39]; workbench #482 [440f4c901f39] |  |
| e13e30af1110a1b3 | de | admin.translation_list.table.open_history | file #lang/de/admin.php [7fbcddc197bc]; legacy #5785 [7fbcddc197bc] |  |
| feae5dabd4cd8ce4 | de | admin.translation_list.table.open_the_translation_key_review_modal | file #lang/de/admin.php [7107341db28b]; legacy #2727 [7107341db28b]; workbench #484 [7107341db28b] |  |
| 4910eeee65c1f987 | de | admin.translation_list.table.original_text_in_the_source_language_useful_for_identification_and_reference | file #lang/de/admin.php [31a16678c547]; legacy #5900 [31a16678c547]; workbench #485 [31a16678c547] |  |
| 61e65c13929af623 | de | admin.translation_list.table.refresh_the_translation_list_to_see_the_latest_changes | file #lang/de/admin.php [c45e9ae142c9]; legacy #3556 [c45e9ae142c9]; workbench #486 [c45e9ae142c9] |  |
| 5f6cdb05e307efab | de | admin.translation_list.table.reload_translation_list | file #lang/de/admin.php [4f42630a3617]; legacy #3522 [4f42630a3617]; workbench #487 [4f42630a3617] |  |
| ba39cc2336d3a074 | de | admin.translation_list.table.review_and_manage_translation_keys_their_values_across_languages_and_associated_ | file #lang/de/admin.php [8e089deead2d]; legacy #2538 [8e089deead2d]; workbench #488 [8e089deead2d] |  |
| 0054943ff63d8897 | de | admin.translation_list.table.review_translation_values_for_the_language_language_including_its_sub_languages_ | file #lang/de/admin.php [e13f8b131395]; legacy #3554 [e13f8b131395]; workbench #489 [e13f8b131395] |  |
| c967bc0139664d99 | de | admin.translation_list.table.source_language_value_en_native_text | file #lang/de/admin.php [f9e707b483f3]; legacy #5437 [f9e707b483f3]; workbench #490 [f9e707b483f3] |  |
| e4bc2ffa15951ace | de | admin.translation_list.table.suggested_key | file #lang/de/admin.php [c7e14c5bb66b]; legacy #6199 [c7e14c5bb66b]; workbench #491 [c7e14c5bb66b] |  |
| f03e4e7634e0d35d | de | admin.translation_list.table.target_language_values | file #lang/de/admin.php [ec6623c8007a]; legacy #5874 [ec6623c8007a]; workbench #492 [ec6623c8007a] |  |
| f250c2adc29d97f9 | de | admin.translation_list.table.target_sub_language_values | file #lang/de/admin.php [30dc2d5a2fc6]; legacy #5902 [30dc2d5a2fc6]; workbench #493 [30dc2d5a2fc6] |  |
| 33bfd61867fff6fc | de | admin.translation_list.table.this_key_is_currently_not_found_in_code_usage_it_can_indicate_a_legacy_export_mi | file #lang/de/admin.php [4eb8c50953d2]; legacy #2735 [4eb8c50953d2]; workbench #494 [4eb8c50953d2] |  |
| 7fc1fb5a30f26c7b | de | admin.translation_list.table.translated_values_for_the_key_across_different_main_languages_and_their_respecti | file #lang/de/admin.php [fd505a63f3a2]; legacy #5644 [fd505a63f3a2]; workbench #495 [fd505a63f3a2] |  |
| fa6f7037c886c52f | de | admin.translation_list.table.translation_key_or_suggested_key_useful_for_identification_and_reference | file #lang/de/admin.php [d8ccfcf1aec5]; legacy #5435 [d8ccfcf1aec5]; workbench #496 [d8ccfcf1aec5] |  |
| 59becee6f6dba3b9 | de | admin.translation_list.table.translation_list | file #lang/de/admin.php [11b0fd098eb6]; legacy #2550 [11b0fd098eb6]; workbench #497 [11b0fd098eb6] |  |
| 9d4d96eb16689d4a | de | admin.translation_list.table.usage | file #lang/de/admin.php [947c0a678ed5]; legacy #2660 [947c0a678ed5] |  |
| c25a5277d8eea0da | de | admin.translation_list.table.usage_information_of_the_translation_key_useful_for_identification_and_reference | file #lang/de/admin.php [0373c2cb39ed]; legacy #2725 [0373c2cb39ed]; workbench #499 [0373c2cb39ed] |  |
| 5d6dbe0b023ad202 | de | admin.translation_list.table.usage_s | file #lang/de/admin.php [203a9a398a0f]; legacy #5876 [203a9a398a0f]; workbench #500 [203a9a398a0f] |  |
| f19b6e91c4fcfcca | de | admin.translation_list.the_first_language_variation_value_was_copied_to_all_visible_language_variations | file #lang/de/admin.php [ee0a24eea95c]; legacy #6045 [ee0a24eea95c]; workbench #501 [ee0a24eea95c] |  |
| 052228581c450e8a | de | admin.translation_list.the_obsolete_entry_has_been_marked_as_reviewed_and_leaves_the_default_workflow_l | file #lang/de/admin.php [997825abec81]; legacy #6037 [997825abec81]; workbench #502 [997825abec81] |  |
| 74a8efa5dff3a2f6 | de | admin.translation_list.the_suggested_key_has_been_copied_to_the_translation_key | file #lang/de/admin.php [66d056cf31c5]; legacy #1127 [66d056cf31c5]; workbench #503 [66d056cf31c5] |  |
| bcc4d1f214e12d97 | de | admin.translation_list.the_translation_key_already_matches_the_suggested_key | file #lang/de/admin.php [36394a8d6c2d]; legacy #1123 [36394a8d6c2d]; workbench #504 [36394a8d6c2d] |  |
| 577fec3386934f59 | de | admin.translation_list.the_translation_values_have_been_saved_successfully | file #lang/de/admin.php [8967843c7f08]; legacy #1115 [8967843c7f08]; workbench #505 [8967843c7f08] |  |
| 6b78cc577b9001cb | de | admin.translation_list.there_is_no_suggested_key_to_apply_for_this_entry | file #lang/de/admin.php [5b6388194415]; legacy #1119 [5b6388194415]; workbench #506 [5b6388194415] |  |
| 1f5cf6ea12ad2372 | de | admin.translation_list.this_obsolete_entry_is_already_marked_as_reviewed | file #lang/de/admin.php [bbaf53de2456]; legacy #6035 [bbaf53de2456]; workbench #507 [bbaf53de2456] |  |
| 33f402d725fd6e16 | de | admin.translation_list.translation_key_missing | file #lang/de/admin.php [e70354b7bbe1]; workbench #1610 [e70354b7bbe1] |  |
| a8c158ed1f40688e | de | admin.translation_list.translation_management | file #lang/de/admin.php [39a32940a297]; legacy #1109 [39a32940a297]; workbench #508 [39a32940a297] |  |
| 6be31c4417d1ed8a | de | admin.translation_list.translation_values_saved | file #lang/de/admin.php [c2faf9c6a311]; legacy #1113 [c2faf9c6a311]; workbench #509 [c2faf9c6a311] |  |
| efe110a6a849830b | de | admin.translation_list.unsaved_translation_value_changes_have_been_discarded | file #lang/de/admin.php [2cb0e01bdbe9]; legacy #6222 [2cb0e01bdbe9]; workbench #510 [2cb0e01bdbe9] |  |
| eb0d90395f705af3 | de | admin.user_list.filter.without_role | file #lang/de/admin.php [7344c73d335d]; legacy #1351 [7344c73d335d]; workbench #511 [7344c73d335d] |  |
| 2418700d3ea3a058 | de | admin.user_list.manage_your_system_s_users_assign_roles_and_manage_permissions | file #lang/de/admin.php [76de8cd246b7]; legacy #1210 [76de8cd246b7]; workbench #512 [76de8cd246b7] |  |
| e79112e293062e3b | de | admin.user_list.meta.assignable_roles | file #lang/de/admin.php [c147291b80e8]; legacy #1372 [c147291b80e8] |  |
| 09274797af73753a | de | admin.user_list.meta.assignable_roles_grouped_by_role_category | file #lang/de/admin.php [64fc2dc32620]; legacy #1184 [64fc2dc32620]; workbench #514 [64fc2dc32620] |  |
| d3753708d27f92c0 | de | admin.user_list.meta.assigned_users | file #lang/de/admin.php [68dd65e2f065]; legacy #1186 [68dd65e2f065] |  |
| d979847f5e1ac6ec | de | admin.user_list.meta.no_system_roles_available | file #lang/de/admin.php [e8407fa42706]; legacy #1196 [e8407fa42706]; workbench #516 [e8407fa42706] |  |
| 9e629479f7848d59 | de | admin.user_list.meta.no_user_roles_available | file #lang/de/admin.php [996d48ef3943]; legacy #1382 [996d48ef3943]; workbench #517 [996d48ef3943] |  |
| e3513a931d16effc | de | admin.user_list.meta.number_of_users_assigned_to_each_system_role | file #lang/de/admin.php [a5a1f355b0ba]; legacy #1266 [a5a1f355b0ba]; workbench #518 [a5a1f355b0ba] |  |
| 01c2f32ae39386d5 | de | admin.user_list.meta.number_of_users_assigned_to_each_user_role | file #lang/de/admin.php [7c5a3e71e05f]; legacy #1192 [7c5a3e71e05f]; workbench #519 [7c5a3e71e05f] |  |
| 070b291d2f4564c7 | de | admin.user_list.meta.overview | file #lang/de/admin.php [4d2bb368f919]; legacy #1343 [4d2bb368f919] |  |
| c2db7d69a75cc928 | de | admin.user_list.meta.summary_of_users_in_the_system_their_assigned_roles_and_role_categories | file #lang/de/admin.php [48bb299aa8e1]; legacy #1353 [48bb299aa8e1]; workbench #521 [48bb299aa8e1] |  |
| 555a73c3b489071c | de | admin.user_list.meta.system | file #lang/de/admin.php [6725e7bbcd28]; legacy #1370 [6725e7bbcd28] |  |
| b5a1ae6f8567c606 | de | admin.user_list.meta.system_roles_users | file #lang/de/admin.php [b5eff49fca63]; legacy #1380 [b5eff49fca63]; workbench #523 [b5eff49fca63] |  |
| de6ee68d7271a030 | de | admin.user_list.meta.the_number_of_users_without_assigned_roles | file #lang/de/admin.php [9b49883384ec]; legacy #1188 [9b49883384ec]; workbench #524 [9b49883384ec] |  |
| 55c5991ad6f3fe00 | de | admin.user_list.meta.the_total_number_of_registered_users_in_the_system | file #lang/de/admin.php [fa498f229212]; legacy #1366 [fa498f229212]; workbench #525 [fa498f229212] |  |
| 3021c4241ff0af0f | de | admin.user_list.meta.total_users | file #lang/de/admin.php [492c3e4be273]; legacy #1361 [492c3e4be273]; workbench #526 [492c3e4be273] |  |
| cecd3412e32d3ff4 | de | admin.user_list.meta.user | file #lang/de/admin.php [80033f8258eb]; legacy #1323 [80033f8258eb]; workbench #527 [80033f8258eb] |  |
| 52e90193258a2df1 | de | admin.user_list.meta.user_roles_users | file #lang/de/admin.php [de33598eac1f]; legacy #1194 [de33598eac1f]; workbench #528 [de33598eac1f] |  |
| 4e7aedcb8f572e29 | de | admin.user_list.meta.users_grouped_by_assigned_role_category | file #lang/de/admin.php [4e37935cca8e]; legacy #1321 [4e37935cca8e]; workbench #529 [4e37935cca8e] |  |
| ac8bd5f6083dbaf3 | de | admin.user_list.meta.users_without_roles | file #lang/de/admin.php [71885564fca1]; legacy #1368 [71885564fca1]; workbench #530 [71885564fca1] |  |
| 41a50cc0d2bf4efc | de | admin.user_list.modal.change_assigned_roles_for | file #lang/de/admin.php [562b160839fa]; legacy #1386 [562b160839fa]; workbench #531 [562b160839fa] |  |
| 7301b834adfe620d | de | admin.user_list.modal.change_assigned_roles_for_the_selected_user | file #lang/de/admin.php [b17fdd81d6b7]; legacy #1384 [b17fdd81d6b7]; workbench #532 [b17fdd81d6b7] |  |
| 7e488e6ce0bbcf54 | de | admin.user_list.modal.change_role | file #lang/de/admin.php [8c045902e257]; legacy #1378 [8c045902e257]; workbench #533 [8c045902e257] |  |
| b3ea6e41d012b899 | de | admin.user_list.modal.edit_roles | file #lang/de/admin.php [b547bd6717a6]; legacy #1264 [b547bd6717a6]; workbench #534 [b547bd6717a6] |  |
| 70f43fbecf7de5a7 | de | admin.user_list.modal.roles | file #lang/de/admin.php [74fa1b2d740d]; legacy #1376 [74fa1b2d740d] |  |
| d553ad10e6aa3ea4 | de | admin.user_list.modal.without_role | file #lang/de/admin.php [7344c73d335d]; legacy #1374 [7344c73d335d] |  |
| bb2f4a35143e2f8f | de | admin.user_list.please_select_a_role | file #lang/de/admin.php [50e8334f3e03]; legacy #1212 [50e8334f3e03]; workbench #537 [50e8334f3e03] |  |
| 657a145a394e22ae | de | admin.user_list.please_select_a_valid_role | file #lang/de/admin.php [1b943284dab9]; legacy #1214 [1b943284dab9]; workbench #538 [1b943284dab9] |  |
| f7c01bd23e970708 | de | admin.user_list.table.actions | file #lang/de/admin.php [5656f92db78d]; legacy #1198 [5656f92db78d] |  |
| f9662940e0ced6fc | de | admin.user_list.table.e_mail | file #lang/de/admin.php [2fae6fb30b0d]; legacy #1392 [2fae6fb30b0d]; workbench #540 [2fae6fb30b0d] |  |
| 89e1c0e3cb4d1f74 | de | admin.user_list.table.edit_roles | file #lang/de/admin.php [78b2a449dd98]; legacy #1204 [78b2a449dd98] |  |
| 08cdb2592e6a6d66 | de | admin.user_list.table.full_name_of_the_user_useful_for_identification | file #lang/de/admin.php [dfc99d9f94ce]; legacy #1202 [dfc99d9f94ce]; workbench #542 [dfc99d9f94ce] |  |
| 392afb4de552f434 | de | admin.user_list.table.id | file #lang/de/admin.php [3843971dcfde]; legacy #1388 [3843971dcfde]; workbench #543 [3843971dcfde] |  |
| 6c66900a37bd5ac9 | de | admin.user_list.table.name | file #lang/de/admin.php [dcd1d5223f73]; legacy #1390 [dcd1d5223f73] |  |
| d0e37e517ee3ac92 | de | admin.user_list.table.roles | file #lang/de/admin.php [74fa1b2d740d]; legacy #1190 [74fa1b2d740d] |  |
| 5fafc741834fd65e | de | admin.user_list.table.roles_assigned_to_the_user_determining_their_permissions_and_access_levels | file #lang/de/admin.php [41204735aa35]; legacy #1206 [41204735aa35]; workbench #546 [41204735aa35] |  |
| 0c7e7f5b4f37166d | de | admin.user_list.table.unique_identifier_of_the_user_useful_for_tracking_and_reference | file #lang/de/admin.php [2b9394507204]; legacy #1268 [2b9394507204]; workbench #547 [2b9394507204] |  |
| 572d3fbdfc767ec8 | de | admin.user_list.table.user_list | file #lang/de/admin.php [85e6be2eacce]; legacy #1200 [85e6be2eacce]; workbench #548 [85e6be2eacce] |  |
| 57c500d4cbb98c09 | de | admin.user_list.table.without_role | file #lang/de/admin.php [7344c73d335d]; legacy #1394 [7344c73d335d] |  |
| 27c0436be964cfc1 | de | admin.user_list.the_role_for_user_has_been_changed_to_role | file #lang/de/admin.php [ddf8f57c1677]; legacy #1174 [ddf8f57c1677]; workbench #550 [ddf8f57c1677] |  |
| b18f37567d797775 | de | admin.user_list.user_role_management | file #lang/de/admin.php [183a938bfacd]; legacy #1208 [183a938bfacd]; workbench #551 [183a938bfacd] |  |
| d6ae746a27560ac0 | de | admin.user_list.user_role_updated | file #lang/de/admin.php [13c1496772b5]; legacy #1176 [13c1496772b5]; workbench #552 [13c1496772b5] |  |
| fcfd1fc14c3ff7df | de | auth.account.already-have-an-account | file #lang/de/auth.php [9a1503379ff6]; legacy #7610 [9a1503379ff6]; workbench #575 [9a1503379ff6] |  |
| 06364e430c139a8e | de | auth.email.a-new-verification-link-has-been-sent-to-the-email-address-you-provided-during-registration | file #lang/de/auth.php [f2e320b9a6bb]; legacy #7619 [f2e320b9a6bb]; workbench #1994 [f2e320b9a6bb] |  |
| 650eb9591655ea94 | de | dynamic.ui.button.show_hide.hide_label | file #lang/de/dynamic.php [042fae8a9e4a]; legacy #6787 [042fae8a9e4a] |  |
| 954276ce34bd2e49 | de | dynamic.ui.button.show_hide.show_label | file #lang/de/dynamic.php [ccb27f03f03e]; legacy #6789 [ccb27f03f03e] |  |
| 6d9d6b96424437fc | de | laravel_translation_workbench.resources.views.livewire.raw_data.translation_workbench_raw_data | file #lang/de/laravel_translation_workbench.php [4eac73d27436]; legacy #6799 [4eac73d27436] |  |
| 416b726c9ca87652 | de | layouts.header.search | file #lang/de/layouts.php [dd097e77dd73]; legacy #931 [dd097e77dd73] |  |
| 5e6d7f6d041fa493 | de | layouts.sidebar.administration.countries | file #lang/de/layouts.php [b655add844a1]; legacy #926 [b655add844a1]; workbench #566 [b655add844a1] |  |
| 98e84b7c15fe5fc2 | de | layouts.sidebar.administration.fallback_reports | file #lang/de/layouts.php [d0035031da64]; legacy #381 [d0035031da64]; workbench #567 [d0035031da64] |  |
| 1b59259b37137021 | de | layouts.sidebar.management.client | file #lang/de/layouts.php [d1da7fb39689]; legacy #936 [d1da7fb39689]; workbench #568 [d1da7fb39689] |  |
| 5ed3cd81a83e6cfe | de | layouts.sidebar.management.create | file #lang/de/layouts.php [5ca419548155]; legacy #941 [5ca419548155] |  |
| d840b6d0f460da8c | de | livewire.admin.pages.settings.two_factor.recovery_codes.2fa_recovery_codes | file #lang/de/livewire.php [66e6548dfa18]; legacy #6854 [66e6548dfa18]; workbench #1557 [66e6548dfa18] |  |
| 1e64b2495c22760d | de | management.people.create_person.form_person.document_type_options.options.document | dynamic_workbench #156 [836ae9356297]; file #lang/de/management.php [836ae9356297] |  |
| 314ebf4e91f61e29 | de | management.people.create_person.form_person.document_type_options.options.health_insurance_proof | dynamic_workbench #158 [41a87c650659]; file #lang/de/management.php [41a87c650659] |  |
| 17feb54f145a88bc | de | management.people.create_person.form_person.document_type_options.options.id_card_copy | dynamic_workbench #160 [96dd8334b926]; file #lang/de/management.php [96dd8334b926] |  |
| 2ba2d6c4db232cb8 | de | management.people.create_person.form_person.document_type_options.options.invoice | dynamic_workbench #162 [8659c5cf3c4e]; file #lang/de/management.php [8659c5cf3c4e] |  |
| ce0f843ccffbc914 | de | management.people.create_person.form_person.document_type_options.options.it_document | dynamic_workbench #163 [89e4520d0cc9]; file #lang/de/management.php [89e4520d0cc9] |  |
| de29d4f841b28066 | de | management.people.create_person.form_person.document_type_options.options.other | dynamic_workbench #165 [9f3d5f8d94cf]; file #lang/de/management.php [9f3d5f8d94cf] |  |
| 7ddd45f39783a1c6 | de | management.people.create_person.form_person.document_type_options.options.passport_copy | dynamic_workbench #167 [84cab833e906]; file #lang/de/management.php [84cab833e906] |  |
| 34a6eac897b633bf | de | management.people.create_person.form_person.document_type_options.options.receipt | dynamic_workbench #169 [5e568c375be2]; file #lang/de/management.php [5e568c375be2] |  |
| ea0b73cac6e592b7 | de | management.people.create_person.form_person.document_type_options.options.residence_permit_copy | dynamic_workbench #171 [15c9bdab4185]; file #lang/de/management.php [15c9bdab4185] |  |
| ec25221dfbe9fc7d | de | management.people.create_person.form_person.document_type_options.options.tax_document | dynamic_workbench #173 [c2ec9652e54b]; file #lang/de/management.php [c2ec9652e54b] |  |
| df6acb76bce71344 | de | management.people.create_person.form_person.document_type_options.options.wikipedia_pdf_dokument | dynamic_workbench #174 [27cb56218ac2]; file #lang/de/management.php [27cb56218ac2] |  |
| 7187c7d03aa7e0cb | de | management.people.create_person.form_person.emergency | file #lang/de/management.php [5c960dcb9f35]; legacy #361 [5c960dcb9f35] |  |
| f84176ff763774a9 | de | management.people.create_person.phone | file #lang/de/management.php [fa6906d76ee9]; workbench #1604 [fa6906d76ee9] |  |
| cc9c49b70ef66c2f | de | management.people.create_person.sections.address.country | file #lang/de/management.php [b6baff9358dd]; legacy #6724 [b6baff9358dd]; workbench #571 [b6baff9358dd] |  |
| 9fe2777d10e0d1a2 | de | management.people.create_person.sections.documents.document_type_options.options.document | dynamic_workbench #132 [836ae9356297]; file #lang/de/management.php [836ae9356297] |  |
| f59210bb65549054 | de | management.people.create_person.sections.documents.document_type_options.options.health_insurance_proof | dynamic_workbench #134 [41a87c650659]; file #lang/de/management.php [41a87c650659] |  |
| d3b41268f147d839 | de | management.people.create_person.sections.documents.document_type_options.options.id_card_copy | dynamic_workbench #136 [96dd8334b926]; file #lang/de/management.php [96dd8334b926] |  |
| 60c9b09e7897ffa4 | de | management.people.create_person.sections.documents.document_type_options.options.invoice | dynamic_workbench #138 [8659c5cf3c4e]; file #lang/de/management.php [8659c5cf3c4e] |  |
| 921f1f13afb266e0 | de | management.people.create_person.sections.documents.document_type_options.options.it_document | dynamic_workbench #140 [89e4520d0cc9]; file #lang/de/management.php [89e4520d0cc9] |  |
| 53666f25357ac0c8 | de | management.people.create_person.sections.documents.document_type_options.options.other | dynamic_workbench #142 [9f3d5f8d94cf]; file #lang/de/management.php [9f3d5f8d94cf] |  |
| aaaa29ab40d6a23e | de | management.people.create_person.sections.documents.document_type_options.options.passport_copy | dynamic_workbench #144 [84cab833e906]; file #lang/de/management.php [84cab833e906] |  |
| e824ca71bae2ea3d | de | management.people.create_person.sections.documents.document_type_options.options.receipt | dynamic_workbench #146 [5e568c375be2]; file #lang/de/management.php [5e568c375be2] |  |
| f5e11f7370d04184 | de | management.people.create_person.sections.documents.document_type_options.options.residence_permit_copy | dynamic_workbench #148 [15c9bdab4185]; file #lang/de/management.php [15c9bdab4185] |  |
| 8038351908d63c35 | de | management.people.create_person.sections.documents.document_type_options.options.tax_document | dynamic_workbench #150 [c2ec9652e54b]; file #lang/de/management.php [c2ec9652e54b] |  |
| 385738771a5b1adf | de | management.people.create_person.sections.documents.document_type_options.options.wikipedia_pdf_dokument | dynamic_workbench #152 [27cb56218ac2]; file #lang/de/management.php [27cb56218ac2] |  |
| f93cb36a45eedffc | de | management.people.create_person.sections.documents.please_enter_the_issuing_authority_for_the_document_this_is_important_for_correc | file #lang/de/management.php [422c262e3056]; legacy #6733 [422c262e3056]; workbench #572 [422c262e3056] |  |
| 1389b43bb0ad2705 | de | management.people.create_person.sections.emergency_contact.emergency_contact_relationship_options.options.caregiver | dynamic_workbench #2 [29f33cd183b0]; file #lang/de/management.php [29f33cd183b0] |  |
| 3f73306e3e5b94ce | de | management.people.create_person.sections.emergency_contact.emergency_contact_relationship_options.options.child | dynamic_workbench #4 [f5387f9bb6ed]; file #lang/de/management.php [f5387f9bb6ed] |  |
| c4ffe4ca98600664 | de | management.people.create_person.sections.emergency_contact.emergency_contact_relationship_options.options.friend | dynamic_workbench #6 [28a119f6ed6f]; file #lang/de/management.php [28a119f6ed6f] |  |
| 21d5c6061ac5245b | de | management.people.create_person.sections.emergency_contact.emergency_contact_relationship_options.options.guardian | dynamic_workbench #8 [68c58b6be2aa]; file #lang/de/management.php [68c58b6be2aa] |  |
| 214ea8e40991caa6 | de | management.people.create_person.sections.emergency_contact.emergency_contact_relationship_options.options.other | dynamic_workbench #10 [6039471db5f4]; file #lang/de/management.php [6039471db5f4] |  |
| da2548767c1a2ba0 | de | management.people.create_person.sections.emergency_contact.emergency_contact_relationship_options.options.parent | dynamic_workbench #12 [8cd3a9452aa4]; file #lang/de/management.php [8cd3a9452aa4] |  |
| f7af36f632e5fc88 | de | management.people.create_person.sections.emergency_contact.emergency_contact_relationship_options.options.partner | dynamic_workbench #14 [29035e784f93]; file #lang/de/management.php [29035e784f93] |  |
| bc2c7c719ab22891 | de | management.people.create_person.sections.emergency_contact.emergency_contact_relationship_options.options.sibling | dynamic_workbench #16 [e2209b3f32c6]; file #lang/de/management.php [e2209b3f32c6] |  |
| 1724e7a552cecf23 | de | management.people.create_person.sections.emergency_contact.emergency_contact_relationship_options.options.spouse | dynamic_workbench #18 [63f498a73f40]; file #lang/de/management.php [63f498a73f40] |  |
| 097f56bcbd23200d | de | management.people.create_person.sections.person_core.gender_options.options.diverse | dynamic_workbench #194 [ce9e9b6100fb]; file #lang/de/management.php [ce9e9b6100fb] |  |
| 57430704082eaba8 | de | management.people.create_person.sections.person_core.gender_options.options.female | dynamic_workbench #196 [1a8435c8b598]; file #lang/de/management.php [1a8435c8b598] |  |
| 9087cb544133ee5a | de | management.people.create_person.sections.person_core.gender_options.options.male | dynamic_workbench #198 [9fa84948be48]; file #lang/de/management.php [9fa84948be48] |  |
| 59a056d70ca3dba8 | de | management.people.create_person.sections.person_core.gender_options.options.unknown | dynamic_workbench #200 [276ffa1fb4f9]; file #lang/de/management.php [276ffa1fb4f9] |  |
| f6d7814753cdeff8 | de | management.people.create_person.sections.person_core.marital_status_options.options.divorced | dynamic_workbench #70 [5bc23c7572bb]; file #lang/de/management.php [5bc23c7572bb] |  |
| 534a5aa27cd9bee6 | de | management.people.create_person.sections.person_core.marital_status_options.options.married | dynamic_workbench #72 [719545706c43]; file #lang/de/management.php [719545706c43] |  |
| af528ec66158628a | de | management.people.create_person.sections.person_core.marital_status_options.options.registered_partnership | dynamic_workbench #74 [9d94dafe0bbe]; file #lang/de/management.php [9d94dafe0bbe] |  |
| 2a9fa1e6d625681a | de | management.people.create_person.sections.person_core.marital_status_options.options.separated | dynamic_workbench #76 [b0caf73f4640]; file #lang/de/management.php [b0caf73f4640] |  |
| ecc3f09cad097e5e | de | management.people.create_person.sections.person_core.marital_status_options.options.single | dynamic_workbench #78 [8888a029aaf6]; file #lang/de/management.php [8888a029aaf6] |  |
| 5b7df83bbd2472b1 | de | management.people.create_person.sections.person_core.marital_status_options.options.unknown | dynamic_workbench #80 [276ffa1fb4f9]; file #lang/de/management.php [276ffa1fb4f9] |  |
| 841e50e0f43ad39c | de | management.people.create_person.sections.person_core.marital_status_options.options.widowed | dynamic_workbench #82 [37cc7fc5bbd0]; file #lang/de/management.php [37cc7fc5bbd0] |  |
| 0686da3f51475a6a | de | management.people.create_person.sections.person_core.salutation_options.options.mr | dynamic_workbench #64 [eb08008ccd53]; file #lang/de/management.php [eb08008ccd53] |  |
| da7e7cf58b860eb8 | de | management.people.create_person.sections.person_core.salutation_options.options.mrs | dynamic_workbench #66 [a67c874c6211]; file #lang/de/management.php [a67c874c6211] |  |
| 769b64a18ce17080 | de | management.people.create_person.sections.person_core.salutation_options.options.mx | dynamic_workbench #68 [ce9e9b6100fb]; file #lang/de/management.php [ce9e9b6100fb] |  |
| f1f8d1fff553433d | de | management.people.edit_person.sections.documents.document_category_options.options.correspondence | dynamic_workbench #84 [18dc9a30fc6a]; file #lang/de/management.php [18dc9a30fc6a] |  |
| 88550aa1086d9697 | de | management.people.edit_person.sections.documents.document_category_options.options.documentation | dynamic_workbench #86 [c2ec0b49f5bf]; file #lang/de/management.php [c2ec0b49f5bf] |  |
| dcb73ac27a8c9351 | de | management.people.edit_person.sections.documents.document_category_options.options.identity | dynamic_workbench #88 [09841f4a4ee7]; file #lang/de/management.php [09841f4a4ee7] |  |
| 1d84504de485e5e8 | de | management.people.edit_person.sections.documents.document_category_options.options.insurance | dynamic_workbench #90 [b6c15460e774]; file #lang/de/management.php [b6c15460e774] |  |
| fc8201d65c4743ae | de | management.people.edit_person.sections.documents.document_category_options.options.medical | dynamic_workbench #92 [ee82f298717a]; file #lang/de/management.php [ee82f298717a] |  |
| 71588a045ac77edc | de | management.people.edit_person.sections.documents.document_category_options.options.other | dynamic_workbench #94 [9f3d5f8d94cf]; file #lang/de/management.php [9f3d5f8d94cf] |  |
| 0f92367a304b0ec6 | de | management.people.edit_person.sections.documents.document_category_options.options.residence | dynamic_workbench #96 [58c2b0c92aeb]; file #lang/de/management.php [58c2b0c92aeb] |  |
| a85b847beae8d790 | de | management.people.edit_person.sections.documents.document_category_options.options.tax | dynamic_workbench #98 [4045fd1653e6]; file #lang/de/management.php [4045fd1653e6] |  |
| 5ac9034f2dec25ed | de | management.people.edit_person.sections.emergency_contact.emergency_contact_relationship_options.options.caregiver | dynamic_workbench #176 [29f33cd183b0]; file #lang/de/management.php [29f33cd183b0] |  |
| e26c5555d8c6dedc | de | management.people.edit_person.sections.emergency_contact.emergency_contact_relationship_options.options.child | dynamic_workbench #178 [f5387f9bb6ed]; file #lang/de/management.php [f5387f9bb6ed] |  |
| 77ab6c3c1f25e1b9 | de | management.people.edit_person.sections.emergency_contact.emergency_contact_relationship_options.options.friend | dynamic_workbench #180 [28a119f6ed6f]; file #lang/de/management.php [28a119f6ed6f] |  |
| 14011610866cd09a | de | management.people.edit_person.sections.emergency_contact.emergency_contact_relationship_options.options.guardian | dynamic_workbench #182 [68c58b6be2aa]; file #lang/de/management.php [68c58b6be2aa] |  |
| 46cda98a394e57d8 | de | management.people.edit_person.sections.emergency_contact.emergency_contact_relationship_options.options.other | dynamic_workbench #184 [9f3d5f8d94cf]; file #lang/de/management.php [9f3d5f8d94cf] |  |
| a39a959e65dae5ca | de | management.people.edit_person.sections.emergency_contact.emergency_contact_relationship_options.options.parent | dynamic_workbench #186 [8cd3a9452aa4]; file #lang/de/management.php [8cd3a9452aa4] |  |
| f64193f7a3e13790 | de | management.people.edit_person.sections.emergency_contact.emergency_contact_relationship_options.options.partner | dynamic_workbench #188 [29035e784f93]; file #lang/de/management.php [29035e784f93] |  |
| c52f13a2cae83fab | de | management.people.edit_person.sections.emergency_contact.emergency_contact_relationship_options.options.sibling | dynamic_workbench #190 [e2209b3f32c6]; file #lang/de/management.php [e2209b3f32c6] |  |
| 382212f62495ac56 | de | management.people.edit_person.sections.emergency_contact.emergency_contact_relationship_options.options.spouse | dynamic_workbench #192 [63f498a73f40]; file #lang/de/management.php [63f498a73f40] |  |
| ac6ab89d1c503b6e | de | management.people.edit_person.sections.person_core.marital_status_options.options.divorced | dynamic_workbench #100 [5bc23c7572bb]; file #lang/de/management.php [5bc23c7572bb] |  |
| 6339af91ddb538b5 | de | management.people.edit_person.sections.person_core.marital_status_options.options.married | dynamic_workbench #102 [719545706c43]; file #lang/de/management.php [719545706c43] |  |
| 5c6692d7e91b30fc | de | management.people.edit_person.sections.person_core.marital_status_options.options.registered_partnership | dynamic_workbench #104 [9d94dafe0bbe]; file #lang/de/management.php [9d94dafe0bbe] |  |
| a0d3c75b79a0f44d | de | management.people.edit_person.sections.person_core.marital_status_options.options.separated | dynamic_workbench #106 [b0caf73f4640]; file #lang/de/management.php [b0caf73f4640] |  |
| fe271a40aae85c29 | de | management.people.edit_person.sections.person_core.marital_status_options.options.single | dynamic_workbench #108 [8888a029aaf6]; file #lang/de/management.php [8888a029aaf6] |  |
| 95a9e35a277852a4 | de | management.people.edit_person.sections.person_core.marital_status_options.options.unknown | dynamic_workbench #110 [276ffa1fb4f9]; file #lang/de/management.php [276ffa1fb4f9] |  |
| 4018eb6d6fad6789 | de | management.people.edit_person.sections.person_core.marital_status_options.options.widowed | dynamic_workbench #112 [37cc7fc5bbd0]; file #lang/de/management.php [37cc7fc5bbd0] |  |
| 67de745cb44be5be | de | packages.gunreip.laravel_translation_workbench.resources.bulk.entries.modal_edit_dynamic_multi.accept_all_matching_source_and_target_values | file #lang/de/packages.php [c5b01c8bb2e9]; workbench #1670 [c5b01c8bb2e9] |  |
| e16a81b2e11b6fa7 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.compact_read_only_foundation_dashboard_for_scanner_data_keys_lang_files_and_coverage | file #lang/de/packages.php [02d5bf76566c]; workbench #1601 [02d5bf76566c] |  |
| 65a2ffe555992736 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.dynamic | file #lang/de/packages.php [fa99d616a27c]; legacy #6863 [fa99d616a27c]; workbench #1614 [fa99d616a27c] |  |
| 63f63fbe44f9a738 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.array_value | file #lang/de/packages.php [271788e7e9e2]; workbench #2206 [271788e7e9e2] |  |
| eee24a7fc70c94aa | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.cleanup_history | file #lang/de/packages.php [87fd8f06a033]; workbench #2208 [87fd8f06a033] |  |
| 11afbb81058790a2 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.derived | file #lang/de/packages.php [a4b707ac6be0]; workbench #2210 [a4b707ac6be0] |  |
| 4d454c046606078f | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.event | file #lang/de/packages.php [1dfa7852694b]; workbench #2212 [1dfa7852694b] |  |
| f8111e59cbd9ab51 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.lang_key | file #lang/de/packages.php [e2a5722eed62]; workbench #2214 [e2a5722eed62] |  |
| f2919befa74d4ab8 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.locales | file #lang/de/packages.php [ac45b8b49a09]; workbench #2216 [ac45b8b49a09] |  |
| 2ebd709f8b67eb97 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.moved | file #lang/de/packages.php [b3cb58af2a74]; workbench #2218 [b3cb58af2a74] |  |
| ab07af4185b368b3 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.moved_to | file #lang/de/packages.php [dd87834fd459]; workbench #2220 [dd87834fd459] |  |
| b8985fdb7db77c66 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.namespace | file #lang/de/packages.php [884dc3c188cc]; workbench #2222 [884dc3c188cc] |  |
| 6e8d00a59b1654e8 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.outcome | file #lang/de/packages.php [cd8e22cf7439]; workbench #2224 [cd8e22cf7439] |  |
| 6e5021365d8538d5 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.already_current | file #lang/de/packages.php [79dc859a5363]; workbench #1620 [79dc859a5363] |  |
| 76cf846a1b02c22a | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.apply_ready_plan | file #lang/de/packages.php [9dc4e0888568]; workbench #1624 [9dc4e0888568] |  |
| bfa95c810fa065db | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.apply_result_freshness | file #lang/de/packages.php [c468e14a7fe2]; workbench #1626 [c468e14a7fe2] |  |
| a532a1297c1fdc91 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.apply_results | file #lang/de/packages.php [83ba26ebb5b7]; workbench #1628 [83ba26ebb5b7] |  |
| 0b2e1548133dabe4 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.code_update_plan | file #lang/de/packages.php [5facd88e2794]; workbench #1708 [5facd88e2794] |  |
| ae9bc539c5dec18e | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.controlled_write_workflow | file #lang/de/packages.php [249f954e8b90]; workbench #1630 [249f954e8b90] |  |
| 4812f41330e1638d | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.current_expression | file #lang/de/packages.php [ebd195fa4f39]; workbench #1632 [ebd195fa4f39] |  |
| 7242c687cbf5c928 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.dry_run | file #lang/de/packages.php [1cdad702c6e5]; workbench #1634 [1cdad702c6e5] |  |
| 3a3cf9bdabb9fbc3 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.duplicate_expressions | file #lang/de/packages.php [2671d1f559fc]; workbench #1636 [2671d1f559fc] |  |
| 3c71af6142307863 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.duplicates | file #lang/de/packages.php [5a0fe853cd29]; workbench #1638 [5a0fe853cd29] |  |
| a38929dc53c54f0f | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.review_duplicate_expression | file #lang/de/packages.php [0ef4eb845472]; workbench #1642 [0ef4eb845472] |  |
| 7f4b032274599a40 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.stores_how_this_duplicate_expression_should_be_handled_before_code_updates_are_applied | file #lang/de/packages.php [017170822788]; workbench #1644 [017170822788] |  |
| c453382ff3d34db2 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.table_work_findings.cell_actions.edit_translation_values | file #lang/de/packages.php [26649341d60c]; workbench #1660 [26649341d60c] |  |
| 216124a824d81e04 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.table_work_findings.cell_actions.review_finding | file #lang/de/packages.php [4ed6fd04c681]; workbench #1662 [4ed6fd04c681] |  |
| 39383fe3d96a3ed9 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings_table.candidate | file #lang/de/packages.php [3c49ac4c542b]; workbench #1700 [3c49ac4c542b] |  |
| 41c128848b790a55 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings_table.edit_dynamic_translation_values | file #lang/de/packages.php [c48e508f338d]; workbench #1658 [c48e508f338d] |  |
| 1558b6cc58b43af2 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings_table.show_timeline | file #lang/de/packages.php [a58854f32611]; workbench #1664 [a58854f32611] |  |
| f1a1d4a505adec10 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.modal_code_update_conflict_review.store_the_explicit_decision_for_a_duplicate_expression_before_source_code_updates_are_applied | file #lang/de/packages.php [789ead651580]; workbench #1646 [789ead651580] |  |
| 3fe00848fafc7907 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.modal_dynamic_review.cardinality | file #lang/de/packages.php [16ebcc7a2574]; workbench #1702 [16ebcc7a2574] |  |
| 24e1a6e5e3bf1e02 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.modal_dynamic_review.classification | file #lang/de/packages.php [658212991cae]; legacy #6872 [658212991cae]; workbench #1704 [658212991cae] |  |
| e43e0b2fd24da7fa | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.modal_dynamic_review.confidence | file #lang/de/packages.php [b73685f7c915]; workbench #1714 [b73685f7c915] |  |
| 32fcb46c58170b38 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.review.modal_edit_translation_key.segment_actions_are_disabled_because_the_translation_key_was_edited_manually_or_transformed | file #lang/de/packages.php [1542a82cf026]; workbench #1656 [1542a82cf026] |  |
| d3d0c9f0d73f02eb | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.review.modal_states.classification_review | file #lang/de/packages.php [6dbc7ea55be3]; workbench #1706 [6dbc7ea55be3] |  |
| 805e73849d424e85 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.review.modal_states.translation_key_missing | file #lang/de/packages.php [e70354b7bbe1]; workbench #1640 [e70354b7bbe1] |  |
| 563c662c2883d02e | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.show_overview | file #lang/de/packages.php [b01b3cff8264]; legacy #6881 [b01b3cff8264]; workbench #1543 [b01b3cff8264] |  |
| 99c0481967d72e19 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.raw_data.filters_event_types.common_filters | file #lang/de/packages.php [bfe1710144a4]; workbench #1712 [bfe1710144a4] |  |
| 713c4c0a3b2a7162 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.raw_data.table_card.column | file #lang/de/packages.php [d5dea2c1a65e]; workbench #1710 [d5dea2c1a65e] |  |
| 755625eca36a41c6 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.settings.inspect_and_reset_persisted_translation_workbench_ui_settings_for_the_current_user_an_a06c4c2605 | file #lang/de/packages.php [4ed77a31f41b]; workbench #2200 [4ed77a31f41b] |  |
| a621f159068fbe1b | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.settings.reset_all_translation_workbench_ui_settings_for_the_current_user | file #lang/de/packages.php [15cc79ed0fa6]; workbench #2202 [15cc79ed0fa6] |  |
| c624586bf10769b0 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.settings.reset_these_settings_for_the_current_user | file #lang/de/packages.php [d2cd6a6ef6d3]; workbench #2204 [d2cd6a6ef6d3] |  |
| a1753db6afa41caa | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.settings.settings_overview | file #lang/de/packages.php [ec042d0ddfc5]; workbench #2198 [ec042d0ddfc5] |  |
| 9bbb974b2db070c0 | de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.settings.user_settings_are_stored_on_the_user_record_export_files_are_diagnostic_snapshots_tha_d71eaffe64 | file #lang/de/packages.php [aeb5592034bd]; workbench #2196 [aeb5592034bd] |  |
| 810cb250cc5e9c35 | de | pages.auth.confirm_password.confirm_password | file #lang/de/pages.php [7d338266c180]; legacy #966 [7d338266c180] |  |
| da36e29b69b1efdb | de | pages.auth.register.already_have_an_account | file #lang/de/pages.php [9a1503379ff6]; legacy #1288 [9a1503379ff6] |  |
| b90659b785acb4bc | de | pages.auth.register.confirm_password | file #lang/de/pages.php [7d338266c180]; legacy #1298 [7d338266c180]; workbench #576 [7d338266c180] |  |
| cb533268176fa9de | de | pages.auth.register.create_account | file #lang/de/pages.php [d7e7f57a7cd4]; legacy #1294 [d7e7f57a7cd4]; workbench #577 [d7e7f57a7cd4] |  |
| 047ff69f34e51b4d | de | pages.auth.register.email_address | file #lang/de/pages.php [c2f9765ad5ce]; legacy #1296 [c2f9765ad5ce]; workbench #578 [c2f9765ad5ce] |  |
| 79a9e2f34268faed | de | pages.auth.register.full_name | file #lang/de/pages.php [113f501e7bbb]; legacy #1286 [113f501e7bbb]; workbench #579 [113f501e7bbb] |  |
| 888a9b363dc255a1 | de | pages.auth.register.log_in | file #lang/de/pages.php [a329a32263a4]; legacy #1336 [a329a32263a4]; workbench #580 [a329a32263a4] |  |
| a5a74830ffe96339 | de | pages.auth.register.name | file #lang/de/pages.php [dcd1d5223f73]; legacy #1331 [dcd1d5223f73] |  |
| 7c262779a57c5558 | de | pages.auth.register.password | file #lang/de/pages.php [a36c101570cc]; legacy #1290 [a36c101570cc]; workbench #582 [a36c101570cc] |  |
| 02bb36172005f7d4 | de | pages.auth.reset_password.reset_password | file #lang/de/pages.php [6c616ac63f2a]; legacy #1341 [6c616ac63f2a]; workbench #583 [6c616ac63f2a] |  |
| fe824b4cac4ece03 | de | pages.settings.appearance.system | file #lang/de/pages.php [6725e7bbcd28]; legacy #1284 [6725e7bbcd28] |  |
| c6f5cde7bd000e7d | de | pages.settings.profile.avatar_image | file #lang/de/pages.php [f7e3ae42f24e]; legacy #988 [f7e3ae42f24e]; workbench #585 [f7e3ae42f24e] |  |
| 770471baa39b46fe | de | pages.settings.profile.current_avatar | file #lang/de/pages.php [bed79d67ca91]; legacy #983 [bed79d67ca91]; workbench #586 [bed79d67ca91] |  |
| 2ccc58e1caa1c9f1 | de | pages.settings.profile.please_check_the_storage_permissions_for_storage_app_public_avatars | file #lang/de/pages.php [7324987b4285]; legacy #191 [7324987b4285]; workbench #587 [7324987b4285] |  |
| 0339b5da0a3475ef | de | pages.settings.security.confirm_password | file #lang/de/pages.php [7d338266c180]; legacy #998 [7d338266c180] |  |
| 6f4aaf4c34d8a8ef | de | pages.settings.security.disable_2fa | file #lang/de/pages.php [c47a3b7d8be6]; legacy #993 [c47a3b7d8be6]; workbench #589 [c47a3b7d8be6] |  |
| 3e53938c5bfab386 | de | pages.settings.security.enable_2fa | file #lang/de/pages.php [9e7579087c30]; legacy #1018 [9e7579087c30]; workbench #590 [9e7579087c30] |  |
| e73550a168a931d0 | de | pages.settings.security.manage_your_two_factor_authentication_settings | file #lang/de/pages.php [b5816dcba90b]; legacy #1016 [b5816dcba90b]; workbench #591 [b5816dcba90b] |  |
| 4cc1d65ab6dc61cb | de | pages.settings.security.save | file #lang/de/pages.php [f6b2ff39f540]; legacy #1000 [f6b2ff39f540] |  |
| 83ab9d1bfa5e35c4 | de | pages.settings.security.security_settings | file #lang/de/pages.php [37345267f77a]; legacy #971 [37345267f77a]; workbench #593 [37345267f77a] |  |
| 7cde670bec26f999 | de | pages.settings.security.two_factor_authentication | file #lang/de/pages.php [3a7e08b31b8e]; legacy #1002 [3a7e08b31b8e]; workbench #594 [3a7e08b31b8e] |  |
| 0b575005c03b1ecd | de | pages.settings.security.update_password | file #lang/de/pages.php [c08434e5b8da]; legacy #1014 [c08434e5b8da]; workbench #595 [c08434e5b8da] |  |
| 81249c9fc719fb59 | de | pages.settings.two_factor_setup_modal.back | file #lang/de/pages.php [548611ce58f7]; legacy #959 [548611ce58f7]; workbench #596 [548611ce58f7] |  |
| f5e29e0ed2faf75e | de | pages.settings.two_factor_setup_modal.close | file #lang/de/pages.php [b808f6e97075]; legacy #1022 [b808f6e97075] |  |
| b8bcb0eef25af61d | de | pages.settings.two_factor_setup_modal.confirm | file #lang/de/pages.php [15b63b4de303]; legacy #976 [15b63b4de303] |  |
| 73eba948e0384f5f | de | pages.settings.two_factor_setup_modal.continue | file #lang/de/pages.php [1e14bdf34c3b]; legacy #1028 [1e14bdf34c3b]; workbench #599 [1e14bdf34c3b] |  |
| 8359adcf53e12f64 | de | pages.settings.two_factor_setup_modal.enable_two_factor_authentication | file #lang/de/pages.php [f17dc79fc25e]; legacy #1030 [f17dc79fc25e]; workbench #600 [f17dc79fc25e] |  |
| 25d8c26de4d8dc55 | de | pages.settings.two_factor_setup_modal.enter_the_6_digit_code_from_your_authenticator_app | file #lang/de/pages.php [18890fdb7298]; legacy #1026 [18890fdb7298]; workbench #601 [18890fdb7298] |  |
| df5d4bfcacb3db64 | de | pages.settings.two_factor_setup_modal.to_finish_enabling_two_factor_authentication_scan_the_qr_code_or_enter_the_setup | file #lang/de/pages.php [5238ac4659a3]; legacy #950 [5238ac4659a3]; workbench #602 [5238ac4659a3] |  |
| c8b71428643cc8c8 | de | pages.settings.two_factor_setup_modal.two_factor_authentication_enabled | file #lang/de/pages.php [f158b7f27493]; legacy #1020 [f158b7f27493]; workbench #603 [f158b7f27493] |  |
| 7491cc7bb4492fd7 | de | pages.settings.two_factor_setup_modal.verify_authentication_code | file #lang/de/pages.php [5a6f1a315b08]; legacy #1024 [5a6f1a315b08]; workbench #604 [5a6f1a315b08] |  |
| 7a52250025823121 | de | partials.settings_heading.manage_your_profile_and_account_settings | file #lang/de/partials.php [95b0ac49dd71]; legacy #6791 [95b0ac49dd71]; legacy #6827 [95b0ac49dd71]; workbench #605 [95b0ac49dd71] | multiple_records_per_source |
| 55503a3c52c7066a | de | ui.action | file #lang/de/ui.php [a4ad259e71cb]; workbench #1559 [a4ad259e71cb] | key_requires_nested_array_review |
| 67b24bcf30a4e103 | de | ui.actions.close | file #lang/de/ui.php [b808f6e97075]; legacy #5652 [b808f6e97075]; workbench #670 [b808f6e97075] |  |
| 34646c5e9f0ed474 | de | ui.actions.save_changes | file #lang/de/ui.php [6c86b99088d0]; legacy #193 [6c86b99088d0]; workbench #672 [6c86b99088d0] |  |
| bbcaff35b9bdd731 | de | ui.actions.search | file #lang/de/ui.php [dd097e77dd73]; legacy #109 [dd097e77dd73]; workbench #673 [dd097e77dd73] |  |
| cc237ccc6b64af68 | de | ui.activity-log | file #lang/de/ui.php [4c305336ff37]; workbench #1720 [4c305336ff37] | key_requires_nested_array_review |
| 72359b97555cf604 | de | ui.actual | file #lang/de/ui.php [f4d865ae1b77]; workbench #1722 [f4d865ae1b77] | key_requires_nested_array_review |
| 007b9a1b9b0d4df5 | de | ui.add-document | file #lang/de/ui.php [fecd5b188a02]; workbench #1724 [fecd5b188a02] | key_requires_nested_array_review |
| 6911a09dc3aea2f3 | de | ui.add-primary-language | file #lang/de/ui.php [f9d9e4961098]; workbench #1726 [f9d9e4961098] | key_requires_nested_array_review |
| 9a5ff569d1018c6d | de | ui.address.address.address-line-2 | file #lang/de/ui.php [2f21a1bb2ae7]; workbench #1730 [2f21a1bb2ae7] |  |
| b5b58f64928664c7 | de | ui.all.all-decisions | file #lang/de/ui.php [2f051cc13af1]; workbench #1732 [2f051cc13af1] |  |
| f6f3edf10c524c49 | de | ui.all.all-dynamic | file #lang/de/ui.php [495013e8aad8]; workbench #1734 [495013e8aad8] |  |
| 8598eb0ca30c18e4 | de | ui.already-target | file #lang/de/ui.php [be392c886f89]; workbench #1742 [be392c886f89] | key_requires_nested_array_review |
| fc70e40a5ead951f | de | ui.app-settings | file #lang/de/ui.php [8376ed06e2e4]; workbench #1744 [8376ed06e2e4] | key_requires_nested_array_review |
| f67769fa27dc5661 | de | ui.appearance | file #lang/de/ui.php [7e3b7ad2611e]; workbench #1746 [7e3b7ad2611e] | key_requires_nested_array_review |
| a5074e7d544d13ca | de | ui.apply.applied | file #lang/de/ui.php [0b341bcccb79]; workbench #1748 [0b341bcccb79] |  |
| 2cacbd5ad228f3f7 | de | ui.apply.apply-conflict | file #lang/de/ui.php [1293e375eb1e]; workbench #1797 [1293e375eb1e] |  |
| 3104a88342593476 | de | ui.assign.assigned.assigned-roles | file #lang/de/ui.php [b84713cacaf2]; workbench #1807 [b84713cacaf2] |  |
| 55e24fdc0982d845 | de | ui.assign.assigned.assigned-users | file #lang/de/ui.php [68dd65e2f065]; workbench #1809 [68dd65e2f065] |  |
| 01c3513f9afdbf46 | de | ui.assignable.assignable | file #lang/de/ui.php [22b968107f6f]; workbench #1801 [22b968107f6f] |  |
| 9da9dc5bfbfb7162 | de | ui.assignable.assignable-through-ui | file #lang/de/ui.php [f34512aa29dd]; workbench #1805 [f34512aa29dd] |  |
| e396641ff4711d06 | de | ui.badge.bulk-equalized | file #lang/de/ui.php [b94d17748f6d]; workbench #1696 [b94d17748f6d] |  |
| 16d8f00d9702e028 | de | ui.badge.create_person.person_created | file #lang/de/ui.php [49d22e34d481]; workbench #1575 [49d22e34d481] |  |
| 46e10aa2b752ce58 | de | ui.badge.created_password.generated_temporary_password | file #lang/de/ui.php [4715aa2c3b71]; workbench #1561 [4715aa2c3b71] |  |
| ac9dfc5ae49572d9 | de | ui.badge.created_password.temporary_password | file #lang/de/ui.php [9e59d241bb4f]; workbench #1569 [9e59d241bb4f] |  |
| 04cfedbd70abd297 | de | ui.badge.created_password.tooltip.temporary_password_password_this_password_is_shown_once_here_and_is_also_written_to | file #lang/de/ui.php [a859b8cb6362]; workbench #1567 [a859b8cb6362] |  |
| b7f3be4f666eaa45 | de | ui.badge.created_person.tooltip.person_number_person_number_created_person_id_person_id_created_user_id_user_id | file #lang/de/ui.php [1ec585efe247]; workbench #1571 [1ec585efe247] |  |
| 130e806568b62620 | de | ui.badge.current.current | file #lang/de/ui.php [f4d865ae1b77]; workbench #1757 [f4d865ae1b77] |  |
| c24eae83b48b920a | de | ui.badge.informatic.nil | file #lang/de/ui.php [69e69621d904]; workbench #1563 [69e69621d904] |  |
| c28fa9dba9ee1f46 | de | ui.badge.informatic.no_date | file #lang/de/ui.php [43916378b4fd]; workbench #1585 [43916378b4fd] |  |
| 3c59b38f2faac586 | de | ui.badge.informatic.no_time | file #lang/de/ui.php [7406844e8163]; workbench #1583 [7406844e8163] |  |
| 796192909a32b842 | de | ui.badge.informatic.no_value | file #lang/de/ui.php [031cbe3a25e7]; workbench #1581 [031cbe3a25e7] |  |
| 8180beef98395448 | de | ui.badge.informatic.not_a_number | file #lang/de/ui.php [1d98e7760217]; workbench #1579 [1d98e7760217] |  |
| 03c5feaa3d913954 | de | ui.badge.test-data | file #lang/de/ui.php [25d6ce150e7a]; workbench #1565 [25d6ce150e7a] |  |
| f2d2a70d7b7b97c6 | de | ui.badge.timespan | file #lang/de/ui.php [cf6a7eb29f72]; workbench #1853 [cf6a7eb29f72] |  |
| 0225f9e71575ad8f | de | ui.badge.total | file #lang/de/ui.php [a36bce47fdac]; workbench #1851 [a36bce47fdac] |  |
| 2562a2d74ef2da44 | de | ui.badge.unresolved | file #lang/de/ui.php [38558e715fba]; workbench #1829 [38558e715fba] |  |
| 43822ff2df9f3c6b | de | ui.badge.updated | file #lang/de/ui.php [1b4933191f5e]; workbench #1827 [1b4933191f5e] |  |
| bdc91b75621f5bd8 | de | ui.button.cancel.cancel | file #lang/de/ui.php [f7ff1178af20]; legacy #6609 [f7ff1178af20]; workbench #676 [f7ff1178af20] |  |
| 872c8c112a938743 | de | ui.button.clear.clear | file #lang/de/ui.php [6c2d352161df]; legacy #6472 [6c2d352161df]; workbench #677 [6c2d352161df] |  |
| 4a888bf23d818afd | de | ui.button.close.close | file #lang/de/ui.php [b808f6e97075]; workbench #1608 [b808f6e97075] |  |
| c1834af625258601 | de | ui.button.confirm.confirm | file #lang/de/ui.php [15b63b4de303]; workbench #598 [15b63b4de303] |  |
| a2c6a893264378bc | de | ui.button.copy.copy | file #lang/de/ui.php [472050b730a6]; legacy #6481 [472050b730a6]; workbench #678 [472050b730a6] |  |
| c97d41341a8479ba | de | ui.button.create.create | file #lang/de/ui.php [5ca419548155]; workbench #265 [5ca419548155] |  |
| bd60a6ec4528677c | de | ui.button.delete.delete | file #lang/de/ui.php [6c2d352161df]; legacy #6611 [6c2d352161df]; workbench #679 [6c2d352161df] |  |
| 6cf272ab409664f3 | de | ui.button.edit.edit | file #lang/de/ui.php [84e45ee73411]; workbench #394 [84e45ee73411] |  |
| c77ed4662252ce4b | de | ui.button.open.open_next_editable_entry | file #lang/de/ui.php [254ac8d04c4c]; workbench #441 [254ac8d04c4c] |  |
| 4edc765d470dae8d | de | ui.button.open.open_previous_editable_entry | file #lang/de/ui.php [3768ad48acd4]; workbench #680 [3768ad48acd4] |  |
| a3f68606e6172c29 | de | ui.button.prev_edit.open_previous_editable_entry | file #lang/de/ui.php [3768ad48acd4]; legacy #6485 [3768ad48acd4] |  |
| 7f37bd67d0ebbfdf | de | ui.button.remove.remove | file #lang/de/ui.php [3828375a2d45]; legacy #6613 [3828375a2d45]; workbench #681 [3828375a2d45] |  |
| e058193e72ebd59c | de | ui.button.reset.reset | file #lang/de/ui.php [1ba0463c46e6]; legacy #6615 [1ba0463c46e6]; workbench #682 [1ba0463c46e6] |  |
| b532d98a190274ba | de | ui.button.reset.reset-the-form-to-its-initial-state | file #lang/de/ui.php [75aa1b2d7adc]; workbench #2006 [75aa1b2d7adc] |  |
| 7580bc9cd1d3c119 | de | ui.button.review.review | file #lang/de/ui.php [ee68813709ef]; legacy #6617 [ee68813709ef]; workbench #683 [ee68813709ef] |  |
| 0c12398025592300 | de | ui.button.save.save | file #lang/de/ui.php [f6b2ff39f540]; legacy #7862 [f6b2ff39f540]; workbench #671 [f6b2ff39f540] |  |
| 4e304028d8baf361 | de | ui.button.save.save_role | file #lang/de/ui.php [99015a929360]; legacy #6619 [99015a929360]; workbench #684 [99015a929360] |  |
| 742a274c0fd6c0b4 | de | ui.button.show-hide.hide | file #lang/de/ui.php [042fae8a9e4a]; workbench #1539 [042fae8a9e4a] |  |
| eda0683b6965e570 | de | ui.button.show-hide.hide-overview | file #lang/de/ui.php [9b8372904090]; workbench #2154 [9b8372904090] |  |
| 176e4e9c10e37329 | de | ui.button.show-hide.show | file #lang/de/ui.php [ccb27f03f03e]; workbench #1541 [ccb27f03f03e] |  |
| 3af883342a2912a5 | de | ui.button.tab_scroll.first_tab | file #lang/de/ui.php [a86f3dac6ca9]; workbench #2022 [a86f3dac6ca9] |  |
| 8fd9ffdcaf2cc426 | de | ui.button.tab_scroll.last_tab | file #lang/de/ui.php [11e2cc8b87c1]; workbench #2024 [11e2cc8b87c1] |  |
| e54875afede4f77d | de | ui.button.tab_scroll.next_tab | file #lang/de/ui.php [786e496809b4]; workbench #2026 [786e496809b4] |  |
| 66567c087ab95f22 | de | ui.button.tab_scroll.previous_tab | file #lang/de/ui.php [b421af96255e]; workbench #2028 [b421af96255e] |  |
| be41056847dea9c8 | de | ui.button.tab_scroll.scroll_tabs | file #lang/de/ui.php [3861da8f84f7]; workbench #2030 [3861da8f84f7] |  |
| 3830e41eb7a91fd0 | de | ui.client-number | file #lang/de/ui.php [b84c1f9c9325]; workbench #1781 [b84c1f9c9325] | key_requires_nested_array_review |
| d9e1af25a19aa6db | de | ui.count.min | file #lang/de/ui.php [dea79332147f]; workbench #2108 [dea79332147f] |  |
| 6bc4c134737cefcf | de | ui.date-time.ago | file #lang/de/ui.php [9b3ebe2b4009]; workbench #1650 [9b3ebe2b4009] |  |
| 98a87f31bd4a54e6 | de | ui.filter.filter | file #lang/de/ui.php [638e249f4a15]; workbench #103 [638e249f4a15] |  |
| e2bd89cf24fa06a2 | de | ui.filter.show-legacy-dynamic-entries-that-were-reactivated-from-stale-audit-state | file #lang/de/ui.php [fb2ad7cfda24]; workbench #2082 [fb2ad7cfda24] |  |
| 0478fef20b5f5858 | de | ui.filters.all-contexts | file #lang/de/ui.php [05a6b986c09b]; workbench #1672 [05a6b986c09b] |  |
| 2348872eb4f4e824 | de | ui.filters.all-domains | file #lang/de/ui.php [5307ea4ce826]; workbench #1674 [5307ea4ce826] |  |
| c0758c9ed43354be | de | ui.filters.all-extras | file #lang/de/ui.php [d81f19da7a59]; workbench #1676 [d81f19da7a59] |  |
| c06e2fd44224c732 | de | ui.filters.all-groups | file #lang/de/ui.php [5fb59d169e89]; workbench #1698 [5fb59d169e89] |  |
| 3f2353605b6611bb | de | ui.filters.all-locales | file #lang/de/ui.php [f67179a09461]; workbench #1680 [f67179a09461] |  |
| 5573a960f20f5569 | de | ui.filters.all-namespaces | file #lang/de/ui.php [679234106352]; workbench #1684 [679234106352] |  |
| 7156b3074266a7a6 | de | ui.filters.all-scopes | file #lang/de/ui.php [bc0b501bb318]; workbench #1686 [bc0b501bb318] |  |
| 11401c76ff017e9a | de | ui.filters.all-sections | file #lang/de/ui.php [b78777844ba5]; workbench #1688 [b78777844ba5] |  |
| 58ffdaf6fc3d44b2 | de | ui.filters.all-sources | file #lang/de/ui.php [755f2a2054a7]; workbench #1690 [755f2a2054a7] |  |
| f3d6e69a0700667a | de | ui.filters.eea | file #lang/de/ui.php [70540e81f598]; workbench #1787 [70540e81f598] |  |
| bf403c11f53ca84e | de | ui.filters.eu | file #lang/de/ui.php [9170a8b2fb32]; workbench #1789 [9170a8b2fb32] |  |
| 0d6e12e025fb157f | de | ui.filters.filter-by-status | file #lang/de/ui.php [66803be010de]; workbench #1775 [66803be010de] |  |
| 26ff2ab57712c262 | de | ui.filters.inactive | file #lang/de/ui.php [bf7c9171cb49]; workbench #1791 [bf7c9171cb49] |  |
| 2c49c91aea5487bd | de | ui.filters.missing-capital | file #lang/de/ui.php [c22f39f51daf]; workbench #1799 [c22f39f51daf] |  |
| 5cda79bd30172b9c | de | ui.filters.region | file #lang/de/ui.php [d3a008ef1335]; workbench #1793 [d3a008ef1335] |  |
| ea9b977580ad2eac | de | ui.filters.yes | file #lang/de/ui.php [cde9e58a9a4e]; workbench #1668 [cde9e58a9a4e] |  |
| 4b6d94717bab5d49 | de | ui.form.tab_status_dot.current_status | file #lang/de/ui.php [04eef89e7956]; legacy #6625 [04eef89e7956]; workbench #685 [04eef89e7956] |  |
| 3f5f07396087e85e | de | ui.form.tab_status_dot.fields | file #lang/de/ui.php [b26f0b1190e9]; legacy #6514 [b26f0b1190e9]; workbench #686 [b26f0b1190e9] |  |
| 05491bbc320455a8 | de | ui.form.tab_status_dot.form_tab | file #lang/de/ui.php [d6e0891de683]; legacy #6623 [d6e0891de683]; workbench #687 [d6e0891de683] |  |
| 55f30c3bd3e807dc | de | ui.form.tab_status_dot.form_tab_status_status | file #lang/de/ui.php [2bca2a699a4e]; legacy #6621 [2bca2a699a4e]; workbench #688 [2bca2a699a4e] |  |
| 5d68d84850f41aa1 | de | ui.form.tab_status_dot.legend | file #lang/de/ui.php [8256535b2421]; legacy #6510 [8256535b2421]; workbench #689 [8256535b2421] |  |
| d5b25d3daa25250a | de | ui.form.tab_status_dot.required | file #lang/de/ui.php [7d3aaaf632f4]; legacy #6512 [7d3aaaf632f4]; workbench #690 [7d3aaaf632f4] |  |
| 38b54cf3e748177a | de | ui.key.suggested-key | file #lang/de/ui.php [2f72817dbdcf]; workbench #1875 [2f72817dbdcf] |  |
| f185b3271dcf6c4c | de | ui.label.would-apply | file #lang/de/ui.php [82032c100265]; workbench #1817 [82032c100265] |  |
| 91341c75b26912ac | de | ui.label.writing | file #lang/de/ui.php [69baac0f2d2d]; workbench #1815 [69baac0f2d2d] |  |
| 86f4f13a70109ab1 | de | ui.label.written | file #lang/de/ui.php [708ea5543ba0]; workbench #1813 [708ea5543ba0] |  |
| 2f9cea13dcc49ef7 | de | ui.labels.address.address | file #lang/de/ui.php [79e5cf20de0b]; workbench #1728 [79e5cf20de0b] |  |
| 88090117cd9d827b | de | ui.labels.approved | file #lang/de/ui.php [9b3015a9dbf0]; workbench #1750 [9b3015a9dbf0] |  |
| 316cb1b310e062d0 | de | ui.labels.audit | file #lang/de/ui.php [ffdca298a34e]; workbench #1811 [ffdca298a34e] |  |
| 38cdb050b9a46385 | de | ui.labels.category | file #lang/de/ui.php [52f2a87a419c]; legacy #113 [52f2a87a419c]; workbench #692 [52f2a87a419c] |  |
| e485c16da69b4068 | de | ui.labels.created | file #lang/de/ui.php [1155fc66f4db]; workbench #1771 [1155fc66f4db] |  |
| 74fe172f621695e2 | de | ui.labels.description | file #lang/de/ui.php [e9262f007dfb]; legacy #115 [e9262f007dfb]; workbench #693 [e9262f007dfb] |  |
| 12beae9f2a8bc85a | de | ui.labels.guard | file #lang/de/ui.php [c8d3fc567bbc]; legacy #117 [c8d3fc567bbc]; workbench #694 [c8d3fc567bbc] |  |
| 2afdd869b13bff91 | de | ui.labels.label | file #lang/de/ui.php [7e1c54731e1f]; workbench #1763 [7e1c54731e1f] |  |
| 7de9e19150648ad2 | de | ui.labels.name | file #lang/de/ui.php [dcd1d5223f73]; legacy #119 [dcd1d5223f73]; workbench #695 [dcd1d5223f73] |  |
| 3cc069b8b03a822f | de | ui.labels.number | file #lang/de/ui.php [74181a2aa991]; legacy #195 [74181a2aa991]; workbench #696 [74181a2aa991] |  |
| ab07c2ea288a105f | de | ui.labels.number_short | file #lang/de/ui.php [334359b90efe]; legacy #121 [334359b90efe]; workbench #697 [334359b90efe] |  |
| 3eea13857fc36d7d | de | ui.labels.preview | file #lang/de/ui.php [69337f6de914]; legacy #197 [69337f6de914]; workbench #698 [69337f6de914] |  |
| 4ae5de27c6cefa82 | de | ui.labels.role | file #lang/de/ui.php [0038a9cf8661]; legacy #123 [0038a9cf8661]; workbench #699 [0038a9cf8661] |  |
| a9b750bb24e527d5 | de | ui.labels.roles | file #lang/de/ui.php [74fa1b2d740d]; legacy #125 [74fa1b2d740d]; workbench #700 [74fa1b2d740d] |  |
| 665ffaf9cc7666ca | de | ui.language.target-sub-language | file #lang/de/ui.php [ae2e17136e29]; workbench #1865 [ae2e17136e29] |  |
| 3e5b872f0c674726 | de | ui.languages.sub-languages | file #lang/de/ui.php [5f0cf8ee0a16]; workbench #1883 [5f0cf8ee0a16] |  |
| 5d821a5bd1fa9407 | de | ui.laravel_translation_workbench.resources.views.livewire.entries.table_entries.show_entries | file #lang/de/ui.php [874ee43d9403]; legacy #6797 [874ee43d9403] |  |
| bd9aef1d02936474 | de | ui.literal.dynamic.this-finding-is-treated-as-a-dynamic-multi-entry-multiple-runtime-option-values-may-n | file #lang/de/ui.php [7a77cc7fb75c]; workbench #1863 [7a77cc7fb75c] |  |
| aacef55ecb883cdc | de | ui.literal.dynamic.this-finding-is-treated-as-dynamic-the-displayed-value-is-resolved-from-runtime-data | file #lang/de/ui.php [8d57796b49d9]; workbench #1861 [8d57796b49d9] |  |
| d0e9c0cb02e393dd | de | ui.literal.literal | file #lang/de/ui.php [0e42526495fe]; workbench #1919 [0e42526495fe] |  |
| e0c0b124187df1c0 | de | ui.loading.lazy_indicator.loading | file #lang/de/ui.php [d0e1f091f141]; workbench #2156 [d0e1f091f141] |  |
| 354a934b91e29c14 | de | ui.messages.no_description_available | file #lang/de/ui.php [6f0c57bb9ab1]; legacy #127 [6f0c57bb9ab1]; workbench #702 [6f0c57bb9ab1] |  |
| 060237360eb097e7 | de | ui.meta.assignable-roles | file #lang/de/ui.php [c147291b80e8]; workbench #1803 [c147291b80e8] |  |
| 1afad9557459c0bb | de | ui.meta.with-people | file #lang/de/ui.php [e89e38f26de8]; workbench #1777 [e89e38f26de8] |  |
| 069dee821e929676 | de | ui.meta.without-people | file #lang/de/ui.php [aa0895dbcef3]; workbench #1779 [aa0895dbcef3] |  |
| 91d5375c22ba01e9 | de | ui.names.nickname | file #lang/de/ui.php [53a6ab7017fb]; workbench #1755 [53a6ab7017fb] |  |
| 2966bac8e99fe32e | de | ui.nouns.pagination | file #lang/de/ui.php [aaa870f45b6a]; workbench #1773 [aaa870f45b6a] |  |
| 70bc0db16b39b6f0 | de | ui.nouns.summary | file #lang/de/ui.php [9d589ca0f3ad]; workbench #1873 [9d589ca0f3ad] |  |
| 5590d79649903e21 | de | ui.nouns.view | file #lang/de/ui.php [ccb27f03f03e]; workbench #1769 [ccb27f03f03e] |  |
| dd1a80b9db776b1b | de | ui.nouns.workflow | file #lang/de/ui.php [e797d8bcc97f]; workbench #1819 [e797d8bcc97f] |  |
| 9c84736fc28db628 | de | ui.null.null | file #lang/de/ui.php [fb329000228c]; workbench #1577 [fb329000228c] |  |
| 98d8d4d7deb685f5 | de | ui.obsolete.the-translation-key-was-not-found-during-the-latest-audit-scan-and-was-marked-as-obsolete | file #lang/de/ui.php [d859beed430d]; workbench #2088 [d859beed430d] |  |
| a3a4a3e1a13ffc20 | de | ui.open | file #lang/de/ui.php [64509e1af49c]; workbench #1551 [64509e1af49c] | key_requires_nested_array_review |
| 0ce08707b146fd2b | de | ui.people.people | file #lang/de/ui.php [b1a03b358fed]; workbench #1783 [b1a03b358fed] |  |
| e9cf9372bf90ff7a | de | ui.per_page_selector.per_page | file #lang/de/ui.php [fe7c4b7cd613]; workbench #716 [fe7c4b7cd613] |  |
| c914a9df31ba97e9 | de | ui.remove.remove.choose-unlink-to-remove-the-active-relation-or-keep-link-to-leave-the-confirmed-decison | file #lang/de/ui.php [47f88c3d8853]; workbench #2146 [47f88c3d8853] |  |
| edc045fa0ab9cd2d | de | ui.remove.remove.remove-first-key-segment | file #lang/de/ui.php [b545f46c399d]; workbench #2122 [b545f46c399d] |  |
| e3748ccf6ada5366 | de | ui.remove.remove.remove-first-value-segment | file #lang/de/ui.php [eaf011ca5c4a]; workbench #2124 [eaf011ca5c4a] |  |
| b1cf68dd24341845 | de | ui.remove.remove.remove-hash-suffix | file #lang/de/ui.php [d90ed8749979]; workbench #2126 [d90ed8749979] |  |
| 79ce4a584a51e005 | de | ui.remove.remove.remove-or-restore-leading-translation-key-segments-while-the-key-has-not-been-manually-edited | file #lang/de/ui.php [65bebbae10c8]; workbench #2152 [65bebbae10c8] |  |
| 37595877a8284622 | de | ui.remove.removed.completed-lang-cleanup-actions-including-removed-or-moved-translation-values | file #lang/de/ui.php [24b36bbcbeef]; workbench #2144 [24b36bbcbeef] |  |
| c74b2b1039f942c8 | de | ui.remove.removed.no-lang-file-values-have-been-removed-yet | file #lang/de/ui.php [39dca61ecf86]; workbench #2128 [39dca61ecf86] |  |
| e21934d57d1e7c6b | de | ui.remove.removed.removed | file #lang/de/ui.php [d0124fb1d921]; workbench #2110 [d0124fb1d921] |  |
| 1da8b47e9c666440 | de | ui.remove.removed.removed-at | file #lang/de/ui.php [aad271000793]; workbench #2112 [aad271000793] |  |
| 3bdc82a858773c32 | de | ui.remove.removed.removed-lang-values | file #lang/de/ui.php [b3c17732ca03]; workbench #2116 [b3c17732ca03] |  |
| 5fbb61773af4dc2e | de | ui.remove.removed.removed-value | file #lang/de/ui.php [f01439e4b97f]; workbench #2114 [f01439e4b97f] |  |
| 57b7404e963f8a2c | de | ui.remove.removed.review-whether-this-source-language-value-should-be-removed-from-the-active-export-workflow | file #lang/de/ui.php [905235eec2a4]; workbench #2150 [905235eec2a4] |  |
| 4cb417b3f63565ec | de | ui.remove.removed.shows-lang-file-values-that-were-actually-removed-by-a-write-export-run-source-data | file #lang/de/ui.php [31733dbf10b5]; workbench #2130 [31733dbf10b5] |  |
| ad9db6703dba465c | de | ui.remove.removed.these-entries-are-present-in-lang-files-but-are-no-longer-active-export-values-in-dry | file #lang/de/ui.php [4b547f668daa]; workbench #2132 [4b547f668daa] |  |
| 3aea0f270150895b | de | ui.remove.removed.values-that-would-be-removed | file #lang/de/ui.php [89a78bc88975]; workbench #2118 [89a78bc88975] |  |
| 1335b94e3189f927 | de | ui.remove.removed.would-be-removed-from-lang-files | file #lang/de/ui.php [96a104043356]; workbench #2120 [96a104043356] |  |
| d41df68f9eda53fe | de | ui.remove.removes.keep-leaves-the-lang-values-active-needs-review-only-records-the-decision-mark-obsolete | file #lang/de/ui.php [972504a0679f]; workbench #2148 [972504a0679f] |  |
| 34c4878ba99cc996 | de | ui.remove.removes.removes-the-first-dot-separated-segment-from-the-current-search-text-useful-when-expo | file #lang/de/ui.php [8a5cdc3936cd]; workbench #2136 [8a5cdc3936cd] |  |
| 83f604149c3af643 | de | ui.remove.removes.removes-the-first-dot-separated-segment-from-the-current-value-search-text-useful-when | file #lang/de/ui.php [2e49041aae20]; workbench #2140 [2e49041aae20] |  |
| f3ddfdcd417b743e | de | ui.remove.removes.removes-the-selected-findings-from-the-bulk-translation-key-review | file #lang/de/ui.php [5adf3029426c]; workbench #2142 [5adf3029426c] |  |
| 53d95ce69f44a786 | de | ui.remove.removes.removes-the-selected-scalar-candidates-from-the-current-review-selection | file #lang/de/ui.php [c0aba167f6eb]; workbench #2134 [c0aba167f6eb] |  |
| ea98d43e1a1e8345 | de | ui.sort.sort.sort-order | file #lang/de/ui.php [895f0813e563]; workbench #2065 [895f0813e563] |  |
| b880f83819a50aa4 | de | ui.source.contains-source-path | file #lang/de/ui.php [91936af95222]; workbench #1992 [91936af95222] |  |
| eaa64ad4efe89392 | de | ui.source.source | file #lang/de/ui.php [0378894a522e]; workbench #1907 [0378894a522e] |  |
| e94384450232aeed | de | ui.source.source-language | file #lang/de/ui.php [892b191e91d7]; workbench #1903 [892b191e91d7] |  |
| 8b56d39ce334e2a7 | de | ui.source.source-locale-missing | file #lang/de/ui.php [5392b5fe1c6c]; workbench #1905 [5392b5fe1c6c] |  |
| 1bd4dafc1fd60985 | de | ui.source.source-path | file #lang/de/ui.php [82129ea4fc9f]; workbench #1901 [82129ea4fc9f] |  |
| 3dd850d24f22fcfe | de | ui.source.source-type | file #lang/de/ui.php [77b2bffcbc11]; workbench #1899 [77b2bffcbc11] |  |
| 8af1b3273b9df6b0 | de | ui.source.stale-source | file #lang/de/ui.php [fe4ba580466d]; workbench #1893 [fe4ba580466d] |  |
| e669ea707baf0e37 | de | ui.sources.sources | file #lang/de/ui.php [c7860048458f]; workbench #1897 [c7860048458f] |  |
| 53baff2a0f429aad | de | ui.stale.a-previously-stale-or-obsolete-translation-entry-was-found-again-and-reactivated | file #lang/de/ui.php [acf024c143ea]; workbench #2084 [acf024c143ea] |  |
| f8ab3e1a7311ab55 | de | ui.stale.marked-stale | file #lang/de/ui.php [15e4addc5f92]; workbench #2086 [15e4addc5f92] |  |
| fa2453572a699394 | de | ui.stale.reactivated-stale | file #lang/de/ui.php [9747fc2d8100]; workbench #2080 [9747fc2d8100] |  |
| 67d611634eb153a7 | de | ui.stale.stale | file #lang/de/ui.php [8e93f2e8507d]; workbench #1895 [8e93f2e8507d] |  |
| f16938bfbb5f74f3 | de | ui.stale.stale-usages | file #lang/de/ui.php [498850982d45]; workbench #2098 [498850982d45] |  |
| d487cc78f0a860fb | de | ui.stale.with-stale-usages | file #lang/de/ui.php [fab5599e47e3]; workbench #2096 [fab5599e47e3] |  |
| 05df003514b26a75 | de | ui.state.active | file #lang/de/ui.php [8163454f378f]; workbench #1716 [8163454f378f] |  |
| a89066079f48415d | de | ui.state.active-state | file #lang/de/ui.php [485787a1f1f3]; workbench #1718 [485787a1f1f3] |  |
| 6eec3cc979d165ca | de | ui.state.available | file #lang/de/ui.php [3aa55273aef4]; workbench #1759 [3aa55273aef4] |  |
| 5aba993920178b9d | de | ui.state.missing | file #lang/de/ui.php [380f98c64bde]; workbench #1761 [380f98c64bde] |  |
| 568a396426725e3e | de | ui.state.state | file #lang/de/ui.php [630d1a734a83]; workbench #1891 [630d1a734a83] |  |
| fe195910394b38bc | de | ui.state.status | file #lang/de/ui.php [920e413c7d41]; legacy #8276 [920e413c7d41]; workbench #1767 [920e413c7d41] |  |
| da0c5c962240da0a | de | ui.state.ui-translation-state | file #lang/de/ui.php [a8911c5574c9]; workbench #1831 [a8911c5574c9] |  |
| 4453758cde39b87d | de | ui.states.all | file #lang/de/ui.php [3801a35c5182]; legacy #129 [3801a35c5182]; workbench #703 [3801a35c5182] |  |
| 498b90d00c4d6a36 | de | ui.states.all-states | file #lang/de/ui.php [23ee74c4a81b]; workbench #1795 [23ee74c4a81b] |  |
| 95b11e835bb52f43 | de | ui.states.all-statuses | file #lang/de/ui.php [23ee74c4a81b]; legacy #8285 [23ee74c4a81b]; workbench #1738 [23ee74c4a81b] |  |
| 8f6a3798f1f1a2e5 | de | ui.states.assigned | file #lang/de/ui.php [4ebae7d1e177]; legacy #131 [4ebae7d1e177]; workbench #704 [4ebae7d1e177] |  |
| 47dc2f0fa0eee43a | de | ui.states.custom | file #lang/de/ui.php [b992755a8203]; legacy #133 [b992755a8203]; workbench #705 [b992755a8203] |  |
| 7a115156f3010882 | de | ui.states.no | file #lang/de/ui.php [90ebc1bde6f3]; legacy #199 [90ebc1bde6f3]; workbench #706 [90ebc1bde6f3] |  |
| e05ec60857472679 | de | ui.states.no_changes | file #lang/de/ui.php [e6c5344cca41]; legacy #135 [e6c5344cca41]; workbench #707 [e6c5344cca41] |  |
| 46d0776bd03f5965 | de | ui.states.non_system | file #lang/de/ui.php [1d403652e377]; legacy #137 [1d403652e377]; workbench #708 [1d403652e377] |  |
| dca6302defc4606f | de | ui.states.other | file #lang/de/ui.php [6039471db5f4]; legacy #139 [6039471db5f4]; workbench #709 [6039471db5f4] |  |
| 66467513c0cc3e2a | de | ui.states.system | file #lang/de/ui.php [6725e7bbcd28]; legacy #141 [6725e7bbcd28]; workbench #710 [6725e7bbcd28] |  |
| c1798f82177d8811 | de | ui.states.unassigned | file #lang/de/ui.php [da974576b029]; legacy #143 [da974576b029]; workbench #711 [da974576b029] |  |
| 234e06bfbd70aa60 | de | ui.states.unsaved_changes | file #lang/de/ui.php [77f120b20528]; legacy #145 [77f120b20528]; workbench #712 [77f120b20528] |  |
| 586fe0c423e04d37 | de | ui.states.yes | file #lang/de/ui.php [cde9e58a9a4e]; legacy #201 [cde9e58a9a4e] |  |
| 72a35bfd58ebc8ed | de | ui.storage.storage | file #lang/de/ui.php [7a1990c0e88c]; workbench #1889 [7a1990c0e88c] |  |
| 0032ae9eca326191 | de | ui.structure.structure | file #lang/de/ui.php [c92a0e03ae5c]; workbench #1885 [c92a0e03ae5c] |  |
| 6f8bd8938f3119b6 | de | ui.suggest.suggested | file #lang/de/ui.php [a687f295f5cc]; workbench #1881 [a687f295f5cc] |  |
| 6d95317dace3be72 | de | ui.table.headers.actions | file #lang/de/ui.php [5656f92db78d]; legacy #7295 [5656f92db78d]; workbench #1678 [5656f92db78d] |  |
| 74f50b3bab85bbd4 | de | ui.table.pagination.results | file #lang/de/ui.php [12ba6ae54b68]; legacy #6506 [12ba6ae54b68]; workbench #714 [12ba6ae54b68] |  |
| 4027a3ed9da36f14 | de | ui.table.per_page_selector.per_page | file #lang/de/ui.php [1dcbeb56852e]; legacy #6497 [1dcbeb56852e] |  |
| 3eaa596e7ab1a431 | de | ui.target.target-language | file #lang/de/ui.php [adadf8178c43]; workbench #1869 [adadf8178c43] |  |
| 177f3a8aa110895d | de | ui.target.target-locale-ready | file #lang/de/ui.php [f14011922945]; workbench #1871 [f14011922945] |  |
| 05a28724cee537b5 | de | ui.target.target-missing | file #lang/de/ui.php [9fd8722a2e3d]; workbench #1867 [9fd8722a2e3d] |  |
| 2ae73cc55324e13a | de | ui.text.copyable_field.copy_to_clipboard | file #lang/de/ui.php [2070a68a7d9d]; legacy #6491 [2070a68a7d9d]; workbench #717 [2070a68a7d9d] |  |
| 68f458a2f79b42f6 | de | ui.time.timeline | file #lang/de/ui.php [d93bc1ecbf87]; legacy #8294 [d93bc1ecbf87]; workbench #1859 [d93bc1ecbf87] |  |
| 0247fec5f7111efb | de | ui.time.timeline-relation-context | file #lang/de/ui.php [4d815531a058]; workbench #1857 [4d815531a058] |  |
| ab6dba834fc98ca3 | de | ui.time.timeline-value-context | file #lang/de/ui.php [27fb7ddc6cfb]; workbench #1855 [27fb7ddc6cfb] |  |
| a180b5f84def8a74 | de | ui.title.filter | file #lang/de/ui.php [638e249f4a15]; legacy #8303 [638e249f4a15]; workbench #221 [638e249f4a15] |  |
| 2dafec454ffdd0ba | de | ui.title.overview | file #lang/de/ui.php [4d2bb368f919]; legacy #8312 [4d2bb368f919]; workbench #2105 [4d2bb368f919] |  |
| 99705dc588494a2f | de | ui.tooltip.badge_required.required | file #lang/de/ui.php [7d3aaaf632f4]; legacy #6487 [7d3aaaf632f4] |  |
| 147d5687faa1b3e5 | de | ui.translation.transform-suggested-key-to-ui-translation-key | file #lang/de/ui.php [fd61ac265986]; workbench #1849 [fd61ac265986] |  |
| 28f9cd59754e53c3 | de | ui.translation.translation-exists | file #lang/de/ui.php [eb22a104713a]; workbench #1847 [eb22a104713a] |  |
| 9626677d1b35728b | de | ui.translation.translation-key | file #lang/de/ui.php [fd8bcb0bd7ca]; workbench #1845 [fd8bcb0bd7ca] |  |
| dc3a63c87784d39e | de | ui.translation.translation-key-state | file #lang/de/ui.php [55418849f926]; workbench #1841 [55418849f926] |  |
| 04f1087cab2d4961 | de | ui.translation.translation-values | file #lang/de/ui.php [80a659f310c2]; workbench #1839 [80a659f310c2] |  |
| 41a5c84417094032 | de | ui.translation.translation-workbench | file #lang/de/ui.php [e79db8b7f6f3]; workbench #1837 [e79db8b7f6f3] |  |
| a3f42633908fedbb | de | ui.ui.ui-candidate | file #lang/de/ui.php [400118f3efd0]; workbench #1835 [400118f3efd0] |  |
| e8f5e59c4572abba | de | ui.ui.ui-translation | file #lang/de/ui.php [355a34a994cf]; workbench #1833 [355a34a994cf] |  |
| 19f359c3d1276b59 | de | ui.usage.usage-state-after | file #lang/de/ui.php [5cb06c0f65bd]; workbench #2090 [5cb06c0f65bd] |  |
| 1e828ac4164a9334 | de | ui.usage.usage-state-before | file #lang/de/ui.php [d0af4ea63a6b]; workbench #2092 [d0af4ea63a6b] |  |
| 8858ee2a8ed878d3 | de | ui.user.user-id | file #lang/de/ui.php [82418fb54b8d]; workbench #1825 [82418fb54b8d] |  |
| 77b66492bf5d3953 | de | ui.user_avatar.avatar_for_name | file #lang/de/ui.php [2db64afa1f8a]; legacy #6495 [2db64afa1f8a]; workbench #719 [2db64afa1f8a] |  |
| 8178af0157e1b7b9 | de | ui.user_avatar.user_avatar | file #lang/de/ui.php [9d3abaafa1db]; legacy #6493 [9d3abaafa1db]; workbench #720 [9d3abaafa1db] |  |
| 37e46580bec82f00 | de | ui.value.value-key | file #lang/de/ui.php [c69bf4bfc3d2]; workbench #1823 [c69bf4bfc3d2] |  |
| 913c4e14ae457bb0 | de | ui.values.stored-values | file #lang/de/ui.php [631c512fe3c9]; workbench #1887 [631c512fe3c9] |  |
| c23d8ea1fc8c098e | de | ui.values.values | file #lang/de/ui.php [6fac4487cf49]; workbench #1909 [6fac4487cf49] |  |
| 48efa17f3dd2f3c6 | de | ui.variables.variable-missing | file #lang/de/ui.php [6a2b4913882b]; workbench #1879 [6a2b4913882b] |  |
| 100e1193a2f3389a | de | ui.variables.variables-ok | file #lang/de/ui.php [aec21bb2c34d]; workbench #1877 [aec21bb2c34d] |  |
| 6f111f56f21806ef | de | ui.welcome.welcome | file #lang/de/ui.php [c4dc122420dc]; legacy #6790 [c4dc122420dc]; legacy #6845 [c4dc122420dc] | multiple_records_per_source |
| e75be24904d51870 | de | view.components.ui.form.tab_status_dot.all_relevant_fields_are_filled | file #lang/de/view.php [5b3a13df9814]; legacy #1063 [5b3a13df9814]; workbench #722 [5b3a13df9814] |  |
| b34ebedc2074535c | de | view.components.ui.form.tab_status_dot.complete | file #lang/de/view.php [25d52d7e8665]; legacy #1094 [25d52d7e8665]; workbench #723 [25d52d7e8665] |  |
| cbbb8d8cb92471f8 | de | view.components.ui.form.tab_status_dot.empty | file #lang/de/view.php [f4e0ac08042c]; legacy #1098 [f4e0ac08042c]; workbench #724 [f4e0ac08042c] |  |
| a234db3b21beb174 | de | view.components.ui.form.tab_status_dot.missing_required_fields | file #lang/de/view.php [4a4121d955e7]; legacy #1069 [4a4121d955e7]; workbench #725 [4a4121d955e7] |  |
| 9a81eb1f727eb9fd | de | view.components.ui.form.tab_status_dot.no_relevant_field_is_filled | file #lang/de/view.php [31467dce2ad8]; legacy #1059 [31467dce2ad8]; workbench #726 [31467dce2ad8] |  |
| dc50f5e83a567f6f | de | view.components.ui.form.tab_status_dot.partially_filled | file #lang/de/view.php [07d4e561ffee]; legacy #1096 [07d4e561ffee]; workbench #727 [07d4e561ffee] |  |
| 809f4b04f273ab1b | de | view.components.ui.form.tab_status_dot.required_fields_are_missing | file #lang/de/view.php [4ac43e11da76]; legacy #1065 [4ac43e11da76]; workbench #728 [4ac43e11da76] |  |
| 95715344b6ac6ec1 | de | view.components.ui.form.tab_status_dot.some_fields_are_filled | file #lang/de/view.php [e1943195d6e4]; legacy #1061 [e1943195d6e4]; workbench #729 [e1943195d6e4] |  |
| ef1a3fcbf5592779 | de | view.components.ui.form.tab_status_dot.this_tab_contains_validation_errors | file #lang/de/view.php [a1c9f2a0a39a]; legacy #1067 [a1c9f2a0a39a]; workbench #730 [a1c9f2a0a39a] |  |
| 19767b0f452a0aac | de | view.components.ui.form.tab_status_dot.validation_errors | file #lang/de/view.php [2901a7c57b09]; legacy #1089 [2901a7c57b09]; workbench #731 [2901a7c57b09] |  |
| cf7cab2a0abf37cc | de-at | admin.translation_list.filter.status_missing | file #lang/de-at/admin.php [7210a0ef6114]; legacy #6443 [7210a0ef6114]; workbench #732 [7210a0ef6114] | locale_spelling_review |
| 4b2b02d08028a6b1 | de-at | admin.translation_list.filter.status_native | file #lang/de-at/admin.php [ce37d9866725]; legacy #4470 [ce37d9866725]; workbench #733 [ce37d9866725] | locale_spelling_review |
| f47a454901b44e20 | de-at | admin.translation_list.filter.type_backfill | file #lang/de-at/admin.php [0979de66414f]; legacy #5939 [0979de66414f]; workbench #734 [0979de66414f] | locale_spelling_review |
| bba5f125f3dc7d3d | de-at | admin.translation_list.filter.type_native | file #lang/de-at/admin.php [0dd528f7136f]; legacy #5953 [0dd528f7136f]; workbench #735 [0dd528f7136f] | locale_spelling_review |
| 7194e4febaef21b0 | de-at | admin.translation_list.meta.total_keys | file #lang/de-at/admin.php [1d8dbdcddf00]; legacy #5802 [1d8dbdcddf00]; workbench #736 [1d8dbdcddf00] | locale_spelling_review |
| 055fcefae66caac1 | de-at | admin.translation_list.modal_edit.sub_language_values | file #lang/de-at/admin.php [b133f5c46c37]; legacy #5487 [b133f5c46c37]; workbench #737 [b133f5c46c37] | locale_spelling_review |
| 59e637f09ede0a8b | de-at | admin.translation_list.table.target_sub_language_values | file #lang/de-at/admin.php [f91397124b2d]; legacy #6009 [f91397124b2d]; workbench #739 [f91397124b2d] | locale_spelling_review |
| fd5da5c524ad8cb0 | de-at | ui.per_page_selector.per_page | file #lang/de-at/ui.php [fe7c4b7cd613]; workbench #740 [fe7c4b7cd613] | locale_spelling_review |
| 6471fa39053d12f0 | de-be | admin.translation_list.filter.status_missing | file #lang/de-be/admin.php [7210a0ef6114]; legacy #6444 [7210a0ef6114]; workbench #741 [7210a0ef6114] | locale_spelling_review |
| 3e6787a15ab84d3f | de-be | admin.translation_list.filter.status_native | file #lang/de-be/admin.php [ce37d9866725]; legacy #4471 [ce37d9866725]; workbench #742 [ce37d9866725] | locale_spelling_review |
| 104e17d1d04c549b | de-be | admin.translation_list.filter.type_backfill | file #lang/de-be/admin.php [0979de66414f]; legacy #5940 [0979de66414f]; workbench #743 [0979de66414f] | locale_spelling_review |
| b8815d21de6e30b5 | de-be | admin.translation_list.filter.type_native | file #lang/de-be/admin.php [0dd528f7136f]; legacy #5954 [0dd528f7136f]; workbench #744 [0dd528f7136f] | locale_spelling_review |
| e70c5d5fb3bd57d6 | de-be | admin.translation_list.meta.total_keys | file #lang/de-be/admin.php [1d8dbdcddf00]; legacy #5803 [1d8dbdcddf00]; workbench #745 [1d8dbdcddf00] | locale_spelling_review |
| df2c3910f2ee02c1 | de-be | admin.translation_list.modal_edit.sub_language_values | file #lang/de-be/admin.php [b133f5c46c37]; legacy #5488 [b133f5c46c37]; workbench #746 [b133f5c46c37] | locale_spelling_review |
| cc95098bbee04a32 | de-be | admin.translation_list.table.target_sub_language_values | file #lang/de-be/admin.php [f91397124b2d]; legacy #6010 [f91397124b2d]; workbench #748 [f91397124b2d] | locale_spelling_review |
| 1852f41de8d3fea2 | de-be | ui.per_page_selector.per_page | file #lang/de-be/ui.php [fe7c4b7cd613]; workbench #749 [fe7c4b7cd613] | locale_spelling_review |
| 7a1331a2ed49dec9 | de-ch | admin.translation_list.filter.status_missing | file #lang/de-ch/admin.php [7210a0ef6114]; legacy #6445 [7210a0ef6114]; workbench #750 [7210a0ef6114] | locale_spelling_review |
| cdbba73213f510a1 | de-ch | admin.translation_list.filter.status_native | file #lang/de-ch/admin.php [ce37d9866725]; legacy #4472 [ce37d9866725]; workbench #751 [ce37d9866725] | locale_spelling_review |
| 5429ec26f50e034d | de-ch | admin.translation_list.filter.type_backfill | file #lang/de-ch/admin.php [0979de66414f]; legacy #5941 [0979de66414f]; workbench #752 [0979de66414f] | locale_spelling_review |
| 2154d92408e5f901 | de-ch | admin.translation_list.filter.type_native | file #lang/de-ch/admin.php [0dd528f7136f]; legacy #5955 [0dd528f7136f]; workbench #753 [0dd528f7136f] | locale_spelling_review |
| 1337f1cd3a51cf86 | de-ch | admin.translation_list.meta.total_keys | file #lang/de-ch/admin.php [1d8dbdcddf00]; legacy #5804 [1d8dbdcddf00]; workbench #754 [1d8dbdcddf00] | locale_spelling_review |
| d99025f1ad78a185 | de-ch | admin.translation_list.modal_edit.sub_language_values | file #lang/de-ch/admin.php [b133f5c46c37]; legacy #5489 [b133f5c46c37]; workbench #755 [b133f5c46c37] | locale_spelling_review |
| 562d99988fdeb87b | de-ch | admin.translation_list.table.target_sub_language_values | file #lang/de-ch/admin.php [f91397124b2d]; legacy #6011 [f91397124b2d]; workbench #757 [f91397124b2d] | locale_spelling_review |
| 5965dcad02b65b8d | de-ch | ui.per_page_selector.per_page | file #lang/de-ch/ui.php [fe7c4b7cd613]; workbench #758 [fe7c4b7cd613] | locale_spelling_review |
| 9ee2e25bb8416e79 | de-de | admin.translation_list.filter.status_missing | file #lang/de-de/admin.php [7210a0ef6114]; legacy #6446 [7210a0ef6114]; workbench #759 [7210a0ef6114] | locale_spelling_review |
| 38023dadd368f0c0 | de-de | admin.translation_list.filter.status_native | file #lang/de-de/admin.php [ce37d9866725]; legacy #4473 [ce37d9866725]; workbench #760 [ce37d9866725] | locale_spelling_review |
| d8794ce93bfba9a0 | de-de | admin.translation_list.filter.type_backfill | file #lang/de-de/admin.php [0979de66414f]; legacy #5942 [0979de66414f]; workbench #761 [0979de66414f] | locale_spelling_review |
| d672b3a3bfe28813 | de-de | admin.translation_list.filter.type_native | file #lang/de-de/admin.php [0dd528f7136f]; legacy #5956 [0dd528f7136f]; workbench #762 [0dd528f7136f] | locale_spelling_review |
| 69617a654009dc07 | de-de | admin.translation_list.filter.workflow_history | file #lang/de-de/admin.php [56a505df5905]; legacy #4483 [56a505df5905]; workbench #763 [56a505df5905] | locale_spelling_review |
| e58f6b50dc40b065 | de-de | admin.translation_list.meta.backfill | file #lang/de-de/admin.php [6fce93c4b2a2]; legacy #3083 [6fce93c4b2a2]; workbench #764 [6fce93c4b2a2] | locale_spelling_review |
| 84f9414eae9bf333 | de-de | admin.translation_list.meta.only_problems | file #lang/de-de/admin.php [3d84a59b4930]; legacy #3109 [3d84a59b4930]; workbench #765 [3d84a59b4930] | locale_spelling_review |
| 41cb10ba66b8f682 | de-de | admin.translation_list.meta.total_keys | file #lang/de-de/admin.php [1d8dbdcddf00]; legacy #5805 [1d8dbdcddf00]; workbench #766 [1d8dbdcddf00] | locale_spelling_review |
| 32df908db7141f98 | de-de | admin.translation_list.modal.key_suggestion_check | file #lang/de-de/admin.php [f458163a5a66]; legacy #3450 [f458163a5a66]; workbench #767 [f458163a5a66] | locale_spelling_review |
| c4f98c4de4669b6d | de-de | admin.translation_list.modal_edit.sub_language_values | file #lang/de-de/admin.php [b133f5c46c37]; legacy #5490 [b133f5c46c37]; workbench #768 [b133f5c46c37] | locale_spelling_review |
| a5ae250ec4d79c06 | de-de | admin.translation_list.modal_edit.usage_locations | file #lang/de-de/admin.php [902c8c04704b]; legacy #3503 [902c8c04704b]; workbench #769 [902c8c04704b] | locale_spelling_review |
| 5f7d7e99b67df289 | de-de | admin.translation_list.table.target_sub_language_values | file #lang/de-de/admin.php [f91397124b2d]; legacy #6012 [f91397124b2d]; workbench #771 [f91397124b2d] | locale_spelling_review |
| a1e4ddefa21dccae | de-de | ui.per_page_selector.per_page | file #lang/de-de/ui.php [fe7c4b7cd613]; workbench #772 [fe7c4b7cd613] | locale_spelling_review |
| 15c319f252880796 | de-it | admin.translation_list.filter.status_missing | file #lang/de-it/admin.php [7210a0ef6114]; legacy #6447 [7210a0ef6114]; workbench #773 [7210a0ef6114] | locale_spelling_review |
| 530374c052826bbe | de-it | admin.translation_list.filter.status_native | file #lang/de-it/admin.php [ce37d9866725]; legacy #4474 [ce37d9866725]; workbench #774 [ce37d9866725] | locale_spelling_review |
| d1b227884137e1b2 | de-it | admin.translation_list.filter.type_backfill | file #lang/de-it/admin.php [0979de66414f]; legacy #5943 [0979de66414f]; workbench #775 [0979de66414f] | locale_spelling_review |
| 1ef170834c05845d | de-it | admin.translation_list.filter.type_native | file #lang/de-it/admin.php [0dd528f7136f]; legacy #5957 [0dd528f7136f]; workbench #776 [0dd528f7136f] | locale_spelling_review |
| 85ce95676f10c1c4 | de-it | admin.translation_list.meta.total_keys | file #lang/de-it/admin.php [1d8dbdcddf00]; legacy #5806 [1d8dbdcddf00]; workbench #777 [1d8dbdcddf00] | locale_spelling_review |
| 065608b56bf80c6c | de-it | admin.translation_list.modal_edit.sub_language_values | file #lang/de-it/admin.php [b133f5c46c37]; legacy #5491 [b133f5c46c37]; workbench #778 [b133f5c46c37] | locale_spelling_review |
| 5d55ad3284d143ee | de-it | admin.translation_list.table.target_sub_language_values | file #lang/de-it/admin.php [f91397124b2d]; legacy #6013 [f91397124b2d]; workbench #780 [f91397124b2d] | locale_spelling_review |
| e413e2fb926ae11c | de-it | ui.per_page_selector.per_page | file #lang/de-it/ui.php [fe7c4b7cd613]; workbench #781 [fe7c4b7cd613] | locale_spelling_review |
| fad56341966e980c | de-li | admin.translation_list.filter.status_missing | file #lang/de-li/admin.php [7210a0ef6114]; legacy #6448 [7210a0ef6114]; workbench #782 [7210a0ef6114] | locale_spelling_review |
| 8f9cc1c4bce6168d | de-li | admin.translation_list.filter.status_native | file #lang/de-li/admin.php [ce37d9866725]; legacy #4475 [ce37d9866725]; workbench #783 [ce37d9866725] | locale_spelling_review |
| ac49ecb6a1e56c13 | de-li | admin.translation_list.filter.type_backfill | file #lang/de-li/admin.php [0979de66414f]; legacy #5944 [0979de66414f]; workbench #784 [0979de66414f] | locale_spelling_review |
| 01dc9d19cce04298 | de-li | admin.translation_list.filter.type_native | file #lang/de-li/admin.php [0dd528f7136f]; legacy #5958 [0dd528f7136f]; workbench #785 [0dd528f7136f] | locale_spelling_review |
| 10a23255215fe908 | de-li | admin.translation_list.meta.total_keys | file #lang/de-li/admin.php [1d8dbdcddf00]; legacy #5807 [1d8dbdcddf00]; workbench #786 [1d8dbdcddf00] | locale_spelling_review |
| 14779be543914b36 | de-li | admin.translation_list.modal_edit.sub_language_values | file #lang/de-li/admin.php [b133f5c46c37]; legacy #5492 [b133f5c46c37]; workbench #787 [b133f5c46c37] | locale_spelling_review |
| edb66a0c3e3240c9 | de-li | admin.translation_list.table.target_sub_language_values | file #lang/de-li/admin.php [f91397124b2d]; legacy #6014 [f91397124b2d]; workbench #789 [f91397124b2d] | locale_spelling_review |
| 79f298646710341f | de-li | ui.per_page_selector.per_page | file #lang/de-li/ui.php [fe7c4b7cd613]; workbench #790 [fe7c4b7cd613] | locale_spelling_review |
| 9a9f9e76ee22163f | de-lu | admin.translation_list.filter.status_missing | file #lang/de-lu/admin.php [7210a0ef6114]; legacy #6449 [7210a0ef6114]; workbench #791 [7210a0ef6114] | locale_spelling_review |
| 7a6d56ece894d407 | de-lu | admin.translation_list.filter.status_native | file #lang/de-lu/admin.php [ce37d9866725]; legacy #4476 [ce37d9866725]; workbench #792 [ce37d9866725] | locale_spelling_review |
| 5219c9e2bdd4d195 | de-lu | admin.translation_list.filter.type_backfill | file #lang/de-lu/admin.php [0979de66414f]; legacy #5945 [0979de66414f]; workbench #793 [0979de66414f] | locale_spelling_review |
| 637c841f30ddc66b | de-lu | admin.translation_list.filter.type_native | file #lang/de-lu/admin.php [0dd528f7136f]; legacy #5959 [0dd528f7136f]; workbench #794 [0dd528f7136f] | locale_spelling_review |
| ecb5d460a337a130 | de-lu | admin.translation_list.meta.total_keys | file #lang/de-lu/admin.php [1d8dbdcddf00]; legacy #5808 [1d8dbdcddf00]; workbench #795 [1d8dbdcddf00] | locale_spelling_review |
| 9288ad100b266bc9 | de-lu | admin.translation_list.modal_edit.sub_language_values | file #lang/de-lu/admin.php [b133f5c46c37]; legacy #5493 [b133f5c46c37]; workbench #796 [b133f5c46c37] | locale_spelling_review |
| 0a886fee46d85626 | de-lu | admin.translation_list.table.target_sub_language_values | file #lang/de-lu/admin.php [f91397124b2d]; legacy #6015 [f91397124b2d]; workbench #798 [f91397124b2d] | locale_spelling_review |
| 6e5724454d988b20 | de-lu | ui.per_page_selector.per_page | file #lang/de-lu/ui.php [fe7c4b7cd613]; workbench #799 [fe7c4b7cd613] | locale_spelling_review |
| 2d7c11330577d9a3 | en | account.preferences.application_preferences.admin_users_per_page | file #lang/en/account.php [efdc269ecbd3]; legacy #1567 [efdc269ecbd3]; workbench #800 [efdc269ecbd3] |  |
| 48685e277762b59f | en | account.preferences.application_preferences.application_preferences | file #lang/en/account.php [66d473c27486]; legacy #1573 [66d473c27486]; workbench #801 [66d473c27486] |  |
| 0203a983680e669b | en | account.preferences.application_preferences.english | file #lang/en/account.php [ba118bf7fc9c]; legacy #1585 [ba118bf7fc9c]; workbench #802 [ba118bf7fc9c] |  |
| edcd2b563348a97f | en | account.preferences.application_preferences.german | file #lang/en/account.php [a659b60d246d]; legacy #1583 [a659b60d246d]; workbench #803 [a659b60d246d] |  |
| 307d40a07a50976c | en | account.preferences.application_preferences.preferred_locale | file #lang/en/account.php [adfb7e0ee051]; legacy #1575 [adfb7e0ee051]; workbench #804 [adfb7e0ee051] |  |
| 679d5eabf953c807 | en | account.preferences.application_preferences.stored_preference | file #lang/en/account.php [e994615b992d]; legacy #1569 [e994615b992d]; workbench #805 [e994615b992d] |  |
| f486fffbc43e124f | en | account.preferences.application_preferences.the_locale_preference_is_stored_now_and_can_be_connected_to_application_locale_h | file #lang/en/account.php [9df025383e64]; legacy #1571 [9df025383e64]; workbench #806 [9df025383e64] |  |
| 31ecefbc3c0934f7 | en | account.preferences.current_user.edit_profile | file #lang/en/account.php [15c4aa13037e]; legacy #1559 [15c4aa13037e]; workbench #807 [15c4aa13037e] |  |
| 7f04b7a913aea1e6 | en | account.preferences.current_user.nickname | file #lang/en/account.php [d720f61c8c5e]; legacy #1561 [d720f61c8c5e] |  |
| cd2662c4507b9475 | en | account.preferences.manage_your_personal_application_preferences | file #lang/en/account.php [56d5826079d5]; legacy #1579 [56d5826079d5]; workbench #809 [56d5826079d5] |  |
| 0231715d01a0911d | en | account.preferences.preferences_saved | file #lang/en/account.php [3490b97459d0]; legacy #1565 [3490b97459d0]; workbench #810 [3490b97459d0] |  |
| d86cf78b97bd25c6 | en | account.preferences.save_preferences | file #lang/en/account.php [e5b8e6907faa]; legacy #1581 [e5b8e6907faa]; workbench #811 [e5b8e6907faa] |  |
| 63aca47b29e0ebe0 | en | account.preferences.stored_settings.stored_settings | file #lang/en/account.php [1aebb2b509fc]; legacy #1557 [1aebb2b509fc]; workbench #812 [1aebb2b509fc] |  |
| 6d18c5641c3fc93f | en | account.preferences.stored_settings.these_values_are_stored_in_your_personal_users_settings_jsonb_column | file #lang/en/account.php [02e21916c1e9]; legacy #1555 [02e21916c1e9]; workbench #813 [02e21916c1e9] |  |
| 7f9a1aaa5f09043b | en | account.preferences.user_preferences | file #lang/en/account.php [58bef1a2b38d]; legacy #1577 [58bef1a2b38d]; workbench #814 [58bef1a2b38d] |  |
| 8b6f556f44b0fd47 | en | account.preferences.your_personal_application_preferences_have_been_updated | file #lang/en/account.php [6d41809e1fff]; legacy #1563 [6d41809e1fff]; workbench #815 [6d41809e1fff] |  |
| fb1eb3cd28fa92b4 | en | admin.app_settings.app_settings | file #lang/en/admin.php [da645334193e]; legacy #1637 [da645334193e] |  |
| a4af52cc672816b7 | en | admin.app_settings.application_language_updated | file #lang/en/admin.php [11b131686c1b]; legacy #1847 [11b131686c1b]; workbench #819 [11b131686c1b] |  |
| 2d9642733ae95752 | en | admin.app_settings.at_least_one_application_language_must_remain_enabled | file #lang/en/admin.php [d529b456c9c8]; legacy #1853 [d529b456c9c8]; workbench #820 [d529b456c9c8] |  |
| 93b00aeb0ff7a2e7 | en | admin.app_settings.available_app_locales_updated | file #lang/en/admin.php [737b93d14d95]; legacy #1999 [737b93d14d95]; workbench #821 [737b93d14d95] |  |
| 8575378ce361ffd8 | en | admin.app_settings.available_application_languages_updated | file #lang/en/admin.php [765873793c68]; legacy #1995 [765873793c68]; workbench #822 [765873793c68] |  |
| 509b5c9dd015cb66 | en | admin.app_settings.global_app_locale_changed | file #lang/en/admin.php [19f66cf94c1c]; legacy #1845 [19f66cf94c1c]; workbench #823 [19f66cf94c1c] |  |
| dfb23da57b7bca38 | en | admin.app_settings.invalid_application_language | file #lang/en/admin.php [a069815ef6a9]; legacy #1843 [a069815ef6a9]; workbench #824 [a069815ef6a9] |  |
| 1249c6169f554e91 | en | admin.app_settings.locale.application_languages | file #lang/en/admin.php [61f7aad9047f]; legacy #1837 [61f7aad9047f]; workbench #825 [61f7aad9047f] |  |
| f8b8a8e5622ca4a7 | en | admin.app_settings.locale.configure_which_languages_are_available_for_the_app_ui_and_choose_the_global_def | file #lang/en/admin.php [9de8f24c9caa]; legacy #1839 [9de8f24c9caa]; workbench #826 [9de8f24c9caa] |  |
| a3ee19f72bc7fac9 | en | admin.app_settings.locale.current | file #lang/en/admin.php [e0d1b68224bf]; legacy #1841 [e0d1b68224bf] |  |
| 5766138542dd8e52 | en | admin.app_settings.locale.deselect_all | file #lang/en/admin.php [967549497036]; legacy #1821 [967549497036]; workbench #828 [967549497036] |  |
| f43e12734547b562 | en | admin.app_settings.locale.do_you_really_want_to_deactivate_this_language | file #lang/en/admin.php [15f5a1fa3776]; legacy #1835 [15f5a1fa3776] |  |
| 9db3fa0e1aa63521 | en | admin.app_settings.locale.enable_the_language_and_any_sub_languages_you_may_have_enabled_above_that_should | file #lang/en/admin.php [a4a4ac91eec5]; legacy #1811 [a4a4ac91eec5]; workbench #830 [a4a4ac91eec5] |  |
| 88bf614e8bfbb661 | en | admin.app_settings.locale.global_app_ui_language | file #lang/en/admin.php [ea7003adc39f]; legacy #1809 [ea7003adc39f]; workbench #831 [ea7003adc39f] |  |
| 84d90dd007f2135a | en | admin.app_settings.locale.narrow_down_your_selection | file #lang/en/admin.php [309692cbacd1]; legacy #1799 [309692cbacd1]; workbench #832 [309692cbacd1] |  |
| 825b891796470c85 | en | admin.app_settings.locale.no_sub_languages_are_available_for_the_selected_primary_language | file #lang/en/admin.php [1c0ea4fea6e4]; legacy #1807 [1c0ea4fea6e4]; workbench #833 [1c0ea4fea6e4] |  |
| 4643684f5f82d65a | en | admin.app_settings.locale.number_of_sub_languages_available_for_this_primary_language | file #lang/en/admin.php [1afe82db04cc]; legacy #1827 [1afe82db04cc]; workbench #834 [1afe82db04cc] |  |
| 8a699e7d9fa8f4cb | en | admin.app_settings.locale.please_select_a_primary_language | file #lang/en/admin.php [d17a694454d2]; legacy #1639 [d17a694454d2]; workbench #835 [d17a694454d2] |  |
| 36991e3f61ec9d79 | en | admin.app_settings.locale.primary_languages | file #lang/en/admin.php [f2c46db9f1fc]; legacy #1803 [f2c46db9f1fc]; workbench #836 [f2c46db9f1fc] |  |
| 193e32a4098abdbe | en | admin.app_settings.locale.select_all | file #lang/en/admin.php [1fc9a387654d]; legacy #1823 [1fc9a387654d]; workbench #837 [1fc9a387654d] |  |
| 0af61e45d2141eb8 | en | admin.app_settings.locale.select_available_languages | file #lang/en/admin.php [dc958800b8f0]; legacy #1767 [dc958800b8f0]; workbench #838 [dc958800b8f0] |  |
| 897bfac0ebe4e8dd | en | admin.app_settings.locale.select_the_entries_you_want_from_the_sub_languages_listed_below_that_correspond_ | file #lang/en/admin.php [c64041165ad3]; legacy #1805 [c64041165ad3]; workbench #839 [c64041165ad3] |  |
| 1b1b5e9dc4da68eb | en | admin.app_settings.locale.select_the_top_languages_by_their_usage | file #lang/en/admin.php [3c32fc81a35e]; legacy #1797 [3c32fc81a35e]; workbench #840 [3c32fc81a35e] |  |
| 6ca0b407388a94e3 | en | admin.app_settings.locale.select_this_primary_language_to_manage_its_sub_languages_and_activation_state | file #lang/en/admin.php [89c1d481574a]; legacy #1831 [89c1d481574a]; workbench #841 [89c1d481574a] |  |
| d38d337de25274e7 | en | admin.app_settings.locale.select_which_languages_should_be_available_for_the_app_ui_you_can_choose_from_al | file #lang/en/admin.php [fd56a9bcf1d8]; legacy #1801 [fd56a9bcf1d8]; workbench #842 [fd56a9bcf1d8] |  |
| 7bf3fb263c67c5af | en | admin.app_settings.locale.sub_languages | file #lang/en/admin.php [62c08fd6a52e]; legacy #1641 [62c08fd6a52e]; workbench #843 [62c08fd6a52e] |  |
| ad7daada6b59c447 | en | admin.app_settings.locale.the_country_language_flag_of_the_global_available_app_ui_language | file #lang/en/admin.php [95e80729fc3b]; legacy #1829 [95e80729fc3b] |  |
| 59a51890e7ed49f9 | en | admin.app_settings.locale.the_number_of_sub_languages_selected_for_this_primary_language | file #lang/en/admin.php [2d6d74bb2c04]; legacy #1833 [2d6d74bb2c04]; workbench #845 [2d6d74bb2c04] |  |
| e0aeefc755beca26 | en | admin.app_settings.locale.this_setting_defines_the_global_default_app_ui_language_user_specific_language_s | file #lang/en/admin.php [4cfcb01da836]; legacy #1813 [4cfcb01da836]; workbench #846 [4cfcb01da836] |  |
| f5b7e0457c6f3f26 | en | admin.app_settings.locale.toggle_the_availability_of_this_sub_language_for_the_app_ui | file #lang/en/admin.php [7f1b6dee9272]; legacy #1825 [7f1b6dee9272]; workbench #847 [7f1b6dee9272] |  |
| fba85937a56a695f | en | admin.app_settings.locale.top_languages | file #lang/en/admin.php [dc92a2fd8762]; legacy #1789 [dc92a2fd8762]; workbench #848 [dc92a2fd8762] |  |
| f87afbb3e1e84c11 | en | admin.app_settings.meta_health.badge_configs_without_role | file #lang/en/admin.php [fdd924292a1f]; legacy #1647 [fdd924292a1f]; workbench #849 [fdd924292a1f] |  |
| efe71bbc5bbb7a86 | en | admin.app_settings.meta_health.badge_settings_that_reference_roles_that_no_longer_exist | file #lang/en/admin.php [b9e2ab3013be]; legacy #1649 [b9e2ab3013be]; workbench #850 [b9e2ab3013be] |  |
| ada89207854050ec | en | admin.app_settings.meta_health.roles_that_exist_in_the_database_but_have_no_configured_badge_settings | file #lang/en/admin.php [1aa43dbc40b5]; legacy #1645 [1aa43dbc40b5]; workbench #851 [1aa43dbc40b5] |  |
| b137775dbdbe88ce | en | admin.app_settings.meta_health.roles_without_badge_config | file #lang/en/admin.php [aa08523cc96d]; legacy #1643 [aa08523cc96d]; workbench #852 [aa08523cc96d] |  |
| ac252775311987af | en | admin.app_settings.meta_health.settings_health | file #lang/en/admin.php [a6ca6b418723]; legacy #1815 [a6ca6b418723]; workbench #853 [a6ca6b418723] |  |
| 8452d797d6c5cd00 | en | admin.app_settings.meta_overview.allowed_role_user_management_icons_registered_in_config | file #lang/en/admin.php [19ecab430261]; legacy #1665 [19ecab430261]; workbench #854 [19ecab430261] |  |
| aa202a129423001a | en | admin.app_settings.meta_overview.app_language | file #lang/en/admin.php [b8352b44a588]; legacy #1657 [b8352b44a588]; workbench #855 [b8352b44a588] |  |
| 2fe879ec3f114bb9 | en | admin.app_settings.meta_overview.configured_role_badge_mappings_stored_in_app_settings | file #lang/en/admin.php [33b06cb73f1d]; legacy #1661 [33b06cb73f1d]; workbench #856 [33b06cb73f1d] |  |
| c331338232867c3e | en | admin.app_settings.meta_overview.global_locale_currently_used_by_the_application | file #lang/en/admin.php [65e7d90405a1]; legacy #1659 [65e7d90405a1]; workbench #857 [65e7d90405a1] |  |
| 7d3ee9db32d4091f | en | admin.app_settings.meta_overview.missing_icons | file #lang/en/admin.php [660f603fa64e]; legacy #1667 [660f603fa64e]; workbench #858 [660f603fa64e] |  |
| 46e5c9bc5b1f6244 | en | admin.app_settings.meta_overview.overview | file #lang/en/admin.php [d4b1ea5708dd]; legacy #1651 [d4b1ea5708dd] |  |
| 1212fa1ae21b12e7 | en | admin.app_settings.meta_overview.primary_spatie_settings_group_used_for_display_settings | file #lang/en/admin.php [62ed2ede5eb2]; legacy #1655 [62ed2ede5eb2]; workbench #860 [62ed2ede5eb2] |  |
| a6bed3eaba7b20aa | en | admin.app_settings.meta_overview.registered_icons | file #lang/en/admin.php [b0eefb86cf5e]; legacy #1663 [b0eefb86cf5e]; workbench #861 [b0eefb86cf5e] |  |
| 18f1b53dc68c59ff | en | admin.app_settings.meta_overview.registered_icons_whose_flux_view_is_currently_unavailable | file #lang/en/admin.php [8e164d08bb11]; legacy #1669 [8e164d08bb11]; workbench #862 [8e164d08bb11] |  |
| 335e845fc91ddb98 | en | admin.app_settings.meta_overview.role_badge_entries | file #lang/en/admin.php [a0289da2e614]; legacy #1817 [a0289da2e614]; workbench #863 [a0289da2e614] |  |
| 547fe8d5b43eb054 | en | admin.app_settings.meta_overview.settings_group | file #lang/en/admin.php [e2cb1e2e4344]; legacy #1653 [e2cb1e2e4344]; workbench #864 [e2cb1e2e4344] |  |
| 8520e0e65f202949 | en | admin.app_settings.primary_locale_availability_updated | file #lang/en/admin.php [138e177eeb85]; legacy #1855 [138e177eeb85]; workbench #865 [138e177eeb85] |  |
| 0a6a3ccfbb79ff71 | en | admin.app_settings.review_global_application_settings_role_badge_configuration_and_icon_registry_he | file #lang/en/admin.php [6f79b5f1a5f1]; legacy #1635 [6f79b5f1a5f1]; workbench #866 [6f79b5f1a5f1] |  |
| aaa4c3d9e1f153d0 | en | admin.app_settings.sub_locales_updated | file #lang/en/admin.php [e35de931c5a9]; legacy #2003 [e35de931c5a9]; workbench #867 [e35de931c5a9] |  |
| 37d1ce521c8b21b5 | en | admin.app_settings.table_icon_registry.available | file #lang/en/admin.php [e674447337e8]; legacy #1683 [e674447337e8] |  |
| 55355c4f3f4a74be | en | admin.app_settings.table_icon_registry.icon | file #lang/en/admin.php [a35abcd6dac9]; legacy #1675 [a35abcd6dac9] |  |
| 7dcd30931620f83e | en | admin.app_settings.table_icon_registry.icon_registry | file #lang/en/admin.php [bbd26f94411a]; legacy #1671 [bbd26f94411a]; workbench #870 [bbd26f94411a] |  |
| f2ff0ef1c7e4dc99 | en | admin.app_settings.table_icon_registry.label | file #lang/en/admin.php [0e66373f45dc]; legacy #1677 [0e66373f45dc] |  |
| 4ad257e1b17e2892 | en | admin.app_settings.table_icon_registry.missing | file #lang/en/admin.php [6be36ca49ee8]; legacy #1685 [6be36ca49ee8] |  |
| 965b76369cbf896c | en | admin.app_settings.table_icon_registry.status | file #lang/en/admin.php [920e413c7d41]; legacy #1681 [920e413c7d41] |  |
| 5e5427d244f77add | en | admin.app_settings.table_icon_registry.text | file #lang/en/admin.php [334359b90efe]; legacy #1673 [334359b90efe] |  |
| f313f88bfc58959e | en | admin.app_settings.table_icon_registry.view | file #lang/en/admin.php [dcc839a4015c]; legacy #1679 [dcc839a4015c] |  |
| d4cf308c64c7c780 | en | admin.app_settings.table_role_badges.color | file #lang/en/admin.php [6b73191a0a4b]; legacy #1777 [6b73191a0a4b] |  |
| 0a36547ca12783c5 | en | admin.app_settings.table_role_badges.icon | file #lang/en/admin.php [a35abcd6dac9]; legacy #1781 [a35abcd6dac9] |  |
| d3d81dd1111fbbda | en | admin.app_settings.table_role_badges.icon_fallback | file #lang/en/admin.php [913986a7ab2a]; legacy #1819 [913986a7ab2a]; workbench #878 [913986a7ab2a] |  |
| b499c7491f12e47c | en | admin.app_settings.table_role_badges.icon_ok | file #lang/en/admin.php [213463f36cb2]; legacy #1795 [213463f36cb2]; workbench #879 [213463f36cb2] |  |
| 68f381d69152c2c4 | en | admin.app_settings.table_role_badges.missing_role | file #lang/en/admin.php [21766fb7b016]; legacy #1793 [21766fb7b016]; workbench #880 [21766fb7b016] |  |
| 5a67ebf0ccc4eb4a | en | admin.app_settings.table_role_badges.preview | file #lang/en/admin.php [324b134f57c7]; legacy #1775 [324b134f57c7] |  |
| 46231ea1128f5079 | en | admin.app_settings.table_role_badges.pseudo_state | file #lang/en/admin.php [ae1ed60f2802]; legacy #1765 [ae1ed60f2802]; workbench #882 [ae1ed60f2802] |  |
| f04825be65bde1b7 | en | admin.app_settings.table_role_badges.role | file #lang/en/admin.php [14736a2eb9f4]; legacy #1773 [14736a2eb9f4] |  |
| 881370d01720079d | en | admin.app_settings.table_role_badges.role_badge_settings | file #lang/en/admin.php [38c0b5cef398]; legacy #1769 [38c0b5cef398]; workbench #884 [38c0b5cef398] |  |
| 0a0aabeb53abdbd0 | en | admin.app_settings.table_role_badges.role_does_not_exist | file #lang/en/admin.php [7b95d1740bae]; legacy #1787 [7b95d1740bae]; workbench #885 [7b95d1740bae] |  |
| 45a18f45e6782229 | en | admin.app_settings.table_role_badges.role_ok | file #lang/en/admin.php [fe04c7fb4f3c]; legacy #1791 [fe04c7fb4f3c]; workbench #886 [fe04c7fb4f3c] |  |
| 644681ab671c1478 | en | admin.app_settings.table_role_badges.status | file #lang/en/admin.php [920e413c7d41]; legacy #1783 [920e413c7d41] |  |
| 9c7e38a9d62a91c0 | en | admin.app_settings.table_role_badges.text | file #lang/en/admin.php [334359b90efe]; legacy #1771 [334359b90efe] |  |
| a6c1c80277a6bfa6 | en | admin.app_settings.table_role_badges.ui_state_not_a_database_role | file #lang/en/admin.php [96d1b1fd9bab]; legacy #1785 [96d1b1fd9bab]; workbench #889 [96d1b1fd9bab] |  |
| 6c2ea2739642cf3d | en | admin.app_settings.table_role_badges.variant | file #lang/en/admin.php [3f19fe84a2de]; legacy #1779 [3f19fe84a2de] |  |
| 8920b63cc9f2e1a2 | en | admin.app_settings.the_current_application_language_cannot_be_disabled | file #lang/en/admin.php [7f65db0d6a56]; legacy #1851 [7f65db0d6a56]; workbench #891 [7f65db0d6a56] |  |
| 3ca170bcca7ecb5f | en | admin.app_settings.the_current_application_language_cannot_be_removed_from_the_available_languages | file #lang/en/admin.php [8ca0e067875e]; legacy #1997 [8ca0e067875e]; workbench #892 [8ca0e067875e] |  |
| 3d073d41e003f089 | en | admin.app_settings.this_application_language_is_mandatory_and_cannot_be_disabled | file #lang/en/admin.php [0c7eafee376c]; legacy #1849 [0c7eafee376c]; workbench #893 [0c7eafee376c] |  |
| aa67400a5232c472 | en | admin.app_settings.without_role | file #lang/en/admin.php [0aeb7ebe5ea5]; legacy #2001 [0aeb7ebe5ea5] |  |
| 6d555ae9105340cc | en | admin.client_list.client_management | file #lang/en/admin.php [13bca07096cd]; legacy #2022 [13bca07096cd]; workbench #895 [13bca07096cd] |  |
| 965131de26df427d | en | admin.client_list.filter.all_statuses | file #lang/en/admin.php [8ee57323a6f2]; legacy #2140 [8ee57323a6f2] |  |
| 22120c88219f7b8f | en | admin.client_list.filter.all_types | file #lang/en/admin.php [f10988e79e8d]; legacy #2122 [f10988e79e8d] |  |
| b527c12428596f41 | en | admin.client_list.filter.enter_a_search_term_to_filter_clients_by_name_legal_name_client_number_or_descri | file #lang/en/admin.php [2425618808f1]; legacy #2148 [2425618808f1]; workbench #898 [2425618808f1] |  |
| 0f44cfad7634cf69 | en | admin.client_list.filter.filter_by_people | file #lang/en/admin.php [f397da4daf37]; legacy #2116 [f397da4daf37]; workbench #899 [f397da4daf37] |  |
| 40b3ca0ddb38d562 | en | admin.client_list.filter.filter_by_status | file #lang/en/admin.php [94d5a7703a82]; legacy #2124 [94d5a7703a82] |  |
| ecd937dc334c0160 | en | admin.client_list.filter.filter_by_type | file #lang/en/admin.php [51035b5b67dc]; legacy #2156 [51035b5b67dc]; workbench #901 [51035b5b67dc] |  |
| a3578a803e376453 | en | admin.client_list.filter.filtering | file #lang/en/admin.php [da12ab901ff9]; legacy #2076 [da12ab901ff9] |  |
| e2daed81e432eb50 | en | admin.client_list.filter.refine_the_client_list_with_powerful_filters_search_by_name_legal_name_client_nu | file #lang/en/admin.php [d7848f9ad1c4]; legacy #2081 [d7848f9ad1c4]; workbench #903 [d7848f9ad1c4] |  |
| ba22ab461425ebe7 | en | admin.client_list.filter.search_by_name_legal_name_client_number_or_description | file #lang/en/admin.php [189ee5011a7e]; legacy #2118 [189ee5011a7e]; workbench #904 [189ee5011a7e] |  |
| 6d34532b652789a7 | en | admin.client_list.filter.search_clients | file #lang/en/admin.php [8872b3c9c931]; legacy #2150 [8872b3c9c931]; workbench #905 [8872b3c9c931] |  |
| be4d586bdca84dd9 | en | admin.client_list.filter.select_a_people_filter_to_filter_the_client_list | file #lang/en/admin.php [15863670cc01]; legacy #2142 [15863670cc01]; workbench #906 [15863670cc01] |  |
| 66d7111481a594cc | en | admin.client_list.filter.select_a_status_to_filter_the_client_list | file #lang/en/admin.php [e504054e120c]; legacy #2136 [e504054e120c]; workbench #907 [e504054e120c] |  |
| cd0f945ba9f71db8 | en | admin.client_list.filter.select_a_type_to_filter_the_client_list | file #lang/en/admin.php [3925ccc1eef2]; legacy #2114 [3925ccc1eef2]; workbench #908 [3925ccc1eef2] |  |
| 9f990d8b77187fab | en | admin.client_list.filter.with_people | file #lang/en/admin.php [67de0b69545d]; legacy #2152 [67de0b69545d] |  |
| 4de13ea73f1c1b28 | en | admin.client_list.filter.without_people | file #lang/en/admin.php [c5a1f8f68593]; legacy #2146 [c5a1f8f68593] |  |
| 78f88eebc050d54a | en | admin.client_list.meta.active_clients | file #lang/en/admin.php [825ecd103995]; legacy #2128 [825ecd103995]; workbench #911 [825ecd103995] |  |
| 9d9e3e81c1be314c | en | admin.client_list.meta.clients_assigned_to_at_least_one_person | file #lang/en/admin.php [ad267cbe67ce]; legacy #2134 [ad267cbe67ce]; workbench #912 [ad267cbe67ce] |  |
| 89b60cf84888895a | en | admin.client_list.meta.clients_currently_marked_as_active | file #lang/en/admin.php [83e00a167d5f]; legacy #2120 [83e00a167d5f]; workbench #913 [83e00a167d5f] |  |
| be929c23b8d320ef | en | admin.client_list.meta.clients_waiting_for_activation_or_verification | file #lang/en/admin.php [ac3d985f1c03]; legacy #2144 [ac3d985f1c03]; workbench #914 [ac3d985f1c03] |  |
| 313478b965620596 | en | admin.client_list.meta.clients_without_assigned_people | file #lang/en/admin.php [749240593352]; legacy #2086 [749240593352]; workbench #915 [749240593352] |  |
| 8e8257a7aab0084f | en | admin.client_list.meta.get_a_quick_snapshot_of_client_statistics_including_total_clients_pending_activa | file #lang/en/admin.php [602fb7b90735]; legacy #2106 [602fb7b90735]; workbench #916 [602fb7b90735] |  |
| edcec2a66df14a91 | en | admin.client_list.meta.pending_clients | file #lang/en/admin.php [5786b0704830]; legacy #2100 [5786b0704830]; workbench #917 [5786b0704830] |  |
| 18e2d9efeb8a75fb | en | admin.client_list.meta.total_clients | file #lang/en/admin.php [c6e83d005759]; legacy #2098 [c6e83d005759]; workbench #918 [c6e83d005759] |  |
| 8029128daac8fd20 | en | admin.client_list.meta.total_number_of_registered_clients | file #lang/en/admin.php [7529cff5d261]; legacy #2126 [7529cff5d261]; workbench #919 [7529cff5d261] |  |
| 021f7afcf7848af8 | en | admin.client_list.meta.with_people | file #lang/en/admin.php [67de0b69545d]; legacy #2104 [67de0b69545d] |  |
| 7adaddac53b8fd8f | en | admin.client_list.meta.without_people | file #lang/en/admin.php [c5a1f8f68593]; legacy #2102 [c5a1f8f68593] |  |
| 20edb2f418484999 | en | admin.client_list.review_organizations_client_status_and_assigned_people | file #lang/en/admin.php [336a820c26ab]; legacy #2020 [336a820c26ab]; workbench #922 [336a820c26ab] |  |
| fe4d2995e366663c | en | admin.client_list.table.browse_and_manage_clients_with_ease_view_client_details_sort_by_name_number_type | file #lang/en/admin.php [454d72c1740d]; legacy #2154 [454d72c1740d]; workbench #923 [454d72c1740d] |  |
| 161835ceeba13a54 | en | admin.client_list.table.client_list | file #lang/en/admin.php [cd049826d583]; legacy #2088 [cd049826d583]; workbench #924 [cd049826d583] |  |
| 62f42fbbd9ed0ce7 | en | admin.client_list.table.client_number | file #lang/en/admin.php [c7936f0541d2]; legacy #2108 [c7936f0541d2] |  |
| 43062e5f73dd0863 | en | admin.client_list.table.created | file #lang/en/admin.php [d70b9e24bca2]; legacy #2042 [d70b9e24bca2] |  |
| a5faa228ef0d3634 | en | admin.client_list.table.current_status_of_the_client_indicating_their_activity_and_engagement | file #lang/en/admin.php [b04b731bb6d4]; legacy #2112 [b04b731bb6d4]; workbench #927 [b04b731bb6d4] |  |
| 56a7ea44b501567d | en | admin.client_list.table.date_when_the_client_was_created_useful_for_tracking_client_history | file #lang/en/admin.php [a7db968980b5]; legacy #2044 [a7db968980b5]; workbench #928 [a7db968980b5] |  |
| 312c8a028a7a7c8a | en | admin.client_list.table.name_of_the_client_useful_for_identification | file #lang/en/admin.php [46c977cfecce]; legacy #2096 [46c977cfecce]; workbench #929 [46c977cfecce] |  |
| 9684615a6e9389fc | en | admin.client_list.table.no_clients_found | file #lang/en/admin.php [cfd52c037ffb]; legacy #2030 [cfd52c037ffb]; workbench #930 [cfd52c037ffb] |  |
| 11d4f2eb2b33fc79 | en | admin.client_list.table.number_of_people_associated_with_the_client_useful_for_understanding_client_size | file #lang/en/admin.php [b08c8081c7bd]; legacy #2026 [b08c8081c7bd]; workbench #931 [b08c8081c7bd] |  |
| 2aa2497be7ad2449 | en | admin.client_list.table.pagination | file #lang/en/admin.php [7b3f674b625e]; legacy #2038 [7b3f674b625e] |  |
| c837267e997b644f | en | admin.client_list.table.people | file #lang/en/admin.php [7db20897053b]; legacy #2040 [7db20897053b] |  |
| c7ed46d13ec7a57e | en | admin.client_list.table.status | file #lang/en/admin.php [920e413c7d41]; legacy #2036 [920e413c7d41] |  |
| 7dc9b6c3f4e660cd | en | admin.client_list.table.type | file #lang/en/admin.php [baaddf70fb5d]; legacy #2032 [baaddf70fb5d] |  |
| 259e1dd1c36f87dd | en | admin.client_list.table.type_of_the_client_useful_for_categorization_and_filtering | file #lang/en/admin.php [90d9e22f3289]; legacy #2130 [90d9e22f3289]; workbench #936 [90d9e22f3289] |  |
| 43422eaca9d730cc | en | admin.client_list.table.unique_identifier_of_the_client_useful_for_tracking_and_reference | file #lang/en/admin.php [1f5c78ff1801]; legacy #2138 [1f5c78ff1801]; workbench #937 [1f5c78ff1801] |  |
| 5a50d37df28ea357 | en | admin.client_list.table.unique_number_assigned_to_the_client_useful_for_tracking_and_reference | file #lang/en/admin.php [37921cadc5f9]; legacy #2132 [37921cadc5f9]; workbench #938 [37921cadc5f9] |  |
| 9b9a96f87f573ebf | en | admin.country_reference_list.audit_imported_country_reference_data_address_formats_and_available_subdivisions | file #lang/en/admin.php [84d66eff090d]; legacy #2248 [84d66eff090d]; workbench #939 [84d66eff090d] |  |
| c73c4d9f011d53b3 | en | admin.country_reference_list.filter.active | file #lang/en/admin.php [92340695899b]; legacy #2429 [92340695899b] |  |
| 1223cd75046c2aa1 | en | admin.country_reference_list.filter.data_quality | file #lang/en/admin.php [ebeb78351a8e]; legacy #2307 [ebeb78351a8e]; workbench #941 [ebeb78351a8e] |  |
| 0822fc0cdeff95aa | en | admin.country_reference_list.filter.eea | file #lang/en/admin.php [5ef407e5cb98]; legacy #2257 [5ef407e5cb98] |  |
| f5b3972bd430c918 | en | admin.country_reference_list.filter.enter_a_search_term_to_filter_countries_by_iso_code_name_phone_code_capital_city | file #lang/en/admin.php [5d02983455e8]; legacy #2293 [5d02983455e8]; workbench #943 [5d02983455e8] |  |
| 42df6d6c948b1ee8 | en | admin.country_reference_list.filter.eu | file #lang/en/admin.php [9170a8b2fb32]; legacy #2261 [9170a8b2fb32] |  |
| d92fef3fe97d1f28 | en | admin.country_reference_list.filter.filter_by_data_quality | file #lang/en/admin.php [61683b23e31e]; legacy #2313 [61683b23e31e]; workbench #945 [61683b23e31e] |  |
| 5ae6721b87f23bf9 | en | admin.country_reference_list.filter.filter_by_membership | file #lang/en/admin.php [7b775018551f]; legacy #2325 [7b775018551f]; workbench #946 [7b775018551f] |  |
| 162440a0cf8812b8 | en | admin.country_reference_list.filter.filter_by_region | file #lang/en/admin.php [7ab09a4d5f2d]; legacy #2297 [7ab09a4d5f2d]; workbench #947 [7ab09a4d5f2d] |  |
| a110ac3e4534ff31 | en | admin.country_reference_list.filter.inactive | file #lang/en/admin.php [ac7c949f1211]; legacy #2331 [ac7c949f1211] |  |
| 4136f3417156c864 | en | admin.country_reference_list.filter.iso_name_phone_capital_region | file #lang/en/admin.php [d2b7222f6160]; legacy #2252 [d2b7222f6160]; workbench #949 [d2b7222f6160] |  |
| 6d73c5d17fa988eb | en | admin.country_reference_list.filter.membership | file #lang/en/admin.php [9feceb9333b5]; legacy #2256 [9feceb9333b5]; workbench #950 [9feceb9333b5] |  |
| e634a96374a3d108 | en | admin.country_reference_list.filter.missing_address_format | file #lang/en/admin.php [a7f5f17dc05b]; legacy #2402 [a7f5f17dc05b]; workbench #951 [a7f5f17dc05b] |  |
| 9f7ca683c46f2198 | en | admin.country_reference_list.filter.missing_capital | file #lang/en/admin.php [c5e8a1222801]; legacy #2303 [c5e8a1222801] |  |
| 8a525fc7409867e9 | en | admin.country_reference_list.filter.missing_phone_code | file #lang/en/admin.php [8b116f702367]; legacy #2305 [8b116f702367]; workbench #953 [8b116f702367] |  |
| a7b5362a73e06fbe | en | admin.country_reference_list.filter.refine_the_list_of_reference_countries_by_searching_for_specific_attributes_or_a | file #lang/en/admin.php [5bfe555e58e8]; legacy #2299 [5bfe555e58e8]; workbench #954 [5bfe555e58e8] |  |
| d1fefba8f4908135 | en | admin.country_reference_list.filter.region | file #lang/en/admin.php [d3a008ef1335]; legacy #2254 [d3a008ef1335] |  |
| 3a2730d851df9231 | en | admin.country_reference_list.filter.schengen | file #lang/en/admin.php [936eeab4a5a3]; legacy #2259 [936eeab4a5a3]; workbench #956 [936eeab4a5a3] |  |
| 084e4e3f25ad812d | en | admin.country_reference_list.filter.select_a_data_quality_filter_to_filter_the_list_of_reference_countries | file #lang/en/admin.php [b5aaed0e1eec]; legacy #2398 [b5aaed0e1eec]; workbench #957 [b5aaed0e1eec] |  |
| 227fb551169872f6 | en | admin.country_reference_list.filter.select_a_membership_to_filter_the_list_of_reference_countries | file #lang/en/admin.php [ace625b3a6c0]; legacy #2315 [ace625b3a6c0]; workbench #958 [ace625b3a6c0] |  |
| 029d990f4480be38 | en | admin.country_reference_list.filter.select_a_region_to_filter_the_list_of_reference_countries | file #lang/en/admin.php [2ab75be96799]; legacy #2295 [2ab75be96799]; workbench #959 [2ab75be96799] |  |
| a8751f2d6494b208 | en | admin.country_reference_list.filter.select_a_status_to_filter_the_list_of_reference_countries | file #lang/en/admin.php [05d772efd403]; legacy #2301 [05d772efd403]; workbench #960 [05d772efd403] |  |
| eb605bc78726209c | en | admin.country_reference_list.filter.with_subdivisions | file #lang/en/admin.php [dfb165f7a5fb]; legacy #2333 [dfb165f7a5fb]; workbench #961 [dfb165f7a5fb] |  |
| fadda109ac841166 | en | admin.country_reference_list.filter.without_subdivisions | file #lang/en/admin.php [76f5c829f049]; legacy #2329 [76f5c829f049]; workbench #962 [76f5c829f049] |  |
| 75ffba436ef08bcd | en | admin.country_reference_list.meta.active | file #lang/en/admin.php [92340695899b]; legacy #2321 [92340695899b] |  |
| e08a923b9e288cbe | en | admin.country_reference_list.meta.address_formats | file #lang/en/admin.php [70a4d149673e]; legacy #2323 [70a4d149673e]; workbench #964 [70a4d149673e] |  |
| b80dfae7a404c197 | en | admin.country_reference_list.meta.eu | file #lang/en/admin.php [9170a8b2fb32]; legacy #2263 [9170a8b2fb32] |  |
| af9ec6c67f5290aa | en | admin.country_reference_list.meta.missing_capital | file #lang/en/admin.php [c5e8a1222801]; legacy #2319 [c5e8a1222801] |  |
| 2261e8865726bbea | en | admin.country_reference_list.meta.missing_phone | file #lang/en/admin.php [4a647a26f716]; legacy #2309 [4a647a26f716]; workbench #967 [4a647a26f716] |  |
| 910635e44ad49ff0 | en | admin.country_reference_list.meta.subdivisions | file #lang/en/admin.php [93310e604d19]; legacy #2317 [93310e604d19]; workbench #968 [93310e604d19] |  |
| a91469c5acf50325 | en | admin.country_reference_list.meta.summary_of_imported_country_reference_data_address_formats_and_available_subdivi | file #lang/en/admin.php [393f7c2e29c7]; legacy #2327 [393f7c2e29c7]; workbench #969 [393f7c2e29c7] |  |
| 09ba36a95fd3fc21 | en | admin.country_reference_list.reference_countries | file #lang/en/admin.php [8fe0505bee8a]; legacy #2250 [8fe0505bee8a]; workbench #970 [8fe0505bee8a] |  |
| e1ab195f0b72922f | en | admin.country_reference_list.table.active | file #lang/en/admin.php [92340695899b]; legacy #2425 [92340695899b] |  |
| 0fdfb6a155f7e066 | en | admin.country_reference_list.table.active_status_of_the_country_useful_for_identification_and_reference | file #lang/en/admin.php [f1a3146e287b]; legacy #2421 [f1a3146e287b]; workbench #972 [f1a3146e287b] |  |
| ef3baf4603a84711 | en | admin.country_reference_list.table.address | file #lang/en/admin.php [56ef8f20955f]; legacy #2433 [56ef8f20955f] |  |
| 879a2d5182a78613 | en | admin.country_reference_list.table.address_format_availability_of_the_country_useful_for_identification_and_referen | file #lang/en/admin.php [88d0ba918f1f]; legacy #2417 [88d0ba918f1f]; workbench #974 [88d0ba918f1f] |  |
| ecdab7f9b7fa2709 | en | admin.country_reference_list.table.address_format_available | file #lang/en/admin.php [b35b369548c6]; legacy #2444 [b35b369548c6] |  |
| 518830fb18c73bd2 | en | admin.country_reference_list.table.address_format_missing | file #lang/en/admin.php [298d3b2bf56a]; legacy #2423 [298d3b2bf56a]; workbench #976 [298d3b2bf56a] |  |
| 6d22cd01cd3c9d76 | en | admin.country_reference_list.table.capital | file #lang/en/admin.php [1a1cf7925799]; legacy #2413 [1a1cf7925799]; workbench #977 [1a1cf7925799] |  |
| 6bebf2a225bb9088 | en | admin.country_reference_list.table.capital_of_the_country_useful_for_identification_and_reference | file #lang/en/admin.php [aad7ce97f59e]; legacy #2431 [aad7ce97f59e]; workbench #978 [aad7ce97f59e] |  |
| 129be8b1316a8d7b | en | admin.country_reference_list.table.inactive | file #lang/en/admin.php [ac7c949f1211]; legacy #2427 [ac7c949f1211] |  |
| dbb134e1c62a48d4 | en | admin.country_reference_list.table.iso | file #lang/en/admin.php [6dc87bf3ac76]; legacy #2404 [6dc87bf3ac76]; workbench #980 [6dc87bf3ac76] |  |
| 09330eab4628ac78 | en | admin.country_reference_list.table.list_of_imported_country_reference_data_address_formats_and_available_subdivisio | file #lang/en/admin.php [af0047397cf7]; legacy #2311 [af0047397cf7]; workbench #981 [af0047397cf7] |  |
| 404587e6e042ac74 | en | admin.country_reference_list.table.number_of_subdivisions_of_the_country_useful_for_identification_and_reference | file #lang/en/admin.php [be54e80d66a1]; legacy #2439 [be54e80d66a1]; workbench #982 [be54e80d66a1] |  |
| 320ff964d7f54486 | en | admin.country_reference_list.table.official_name | file #lang/en/admin.php [ffc88d5ceed2]; legacy #2408 [ffc88d5ceed2]; workbench #983 [ffc88d5ceed2] |  |
| 319aef453646d22a | en | admin.country_reference_list.table.phone | file #lang/en/admin.php [63dceb8800b2]; legacy #2412 [63dceb8800b2] |  |
| e05ac4f3741dc550 | en | admin.country_reference_list.table.phone_code_of_the_country_useful_for_identification_and_reference | file #lang/en/admin.php [f45dce4779e1]; legacy #2437 [f45dce4779e1]; workbench #985 [f45dce4779e1] |  |
| 74fe9cd15ee3b3f9 | en | admin.country_reference_list.table.region_of_the_country_useful_for_identification_and_reference | file #lang/en/admin.php [44a93ecc762a]; legacy #2442 [44a93ecc762a]; workbench #986 [44a93ecc762a] |  |
| 51093a35d87b9b3a | en | admin.country_reference_list.table.subdivisions | file #lang/en/admin.php [93310e604d19]; legacy #2419 [93310e604d19] |  |
| 0b7bcde8608197ee | en | admin.country_reference_list.table.unique_identifier_of_the_country_useful_for_tracking_and_reference | file #lang/en/admin.php [21b33e30f7c9]; legacy #2400 [21b33e30f7c9]; workbench #988 [21b33e30f7c9] |  |
| 66a46ce7903a464e | en | admin.pages.settings.two_factor_setup_modal.or_enter_the_code_manually | file #lang/en/admin.php [659d26a0e67c]; legacy #6793 [659d26a0e67c]; legacy #6808 [659d26a0e67c]; workbench #989 [659d26a0e67c] | multiple_records_per_source |
| a32d058b2351300a | en | admin.pages.settings.two_factor_setup_modal.two_factor_authentication_is_now_enabled_scan_the_qr_code_or_enter_the_setup_key_in_y | file #lang/en/admin.php [736ad67a2dc9]; legacy #6794 [736ad67a2dc9]; legacy #6817 [736ad67a2dc9] | multiple_records_per_source |
| 1565aad0913f9e77 | en | admin.partials.country_reference_list.table.address_format_available | file #lang/en/admin.php [b35b369548c6]; workbench #1587 [b35b369548c6] |  |
| 3260e078dff0e7b8 | en | admin.partials.html_view_audit.table.show_finding_details | file #lang/en/admin.php [ae4daf0af688]; workbench #1546 [ae4daf0af688] |  |
| f8294162f1a3f9d2 | en | admin.partials.person_list.filter.filter_by_client | file #lang/en/admin.php [382872b94b96]; workbench #1589 [382872b94b96] |  |
| 5b44afef299101f1 | en | admin.partials.role_list.modal_create.role_badge_variant_options.options.outline | dynamic_workbench #19 [eabbf3abaf8d]; file #lang/en/admin.php [eabbf3abaf8d] |  |
| 35a493c9076fd8b7 | en | admin.partials.role_list.modal_create.role_badge_variant_options.options.pill | dynamic_workbench #21 [9945c78b5f89]; file #lang/en/admin.php [9945c78b5f89] |  |
| 656354f71dd02de2 | en | admin.partials.role_list.modal_create.role_badge_variant_options.options.solid | dynamic_workbench #23 [b8b311b0f273]; file #lang/en/admin.php [b8b311b0f273] |  |
| 5cedd6095644d620 | en | admin.partials.role_list.modal_create.role_badge_variant_options.options.subtle | dynamic_workbench #25 [3eadecfbd533]; file #lang/en/admin.php [3eadecfbd533] |  |
| 38494eb66aecd1ef | en | admin.partials.role_list.modal_edit.role_badge_color_options.options.amber | dynamic_workbench #27 [a5cccbcead7d]; file #lang/en/admin.php [a5cccbcead7d] |  |
| 4d49d77a802148f8 | en | admin.partials.role_list.modal_edit.role_badge_color_options.options.blue | dynamic_workbench #29 [ec7d56a01607]; file #lang/en/admin.php [ec7d56a01607] |  |
| 5c56b4cf5b926ac1 | en | admin.partials.role_list.modal_edit.role_badge_color_options.options.cyan | dynamic_workbench #31 [e95bb222e07f]; file #lang/en/admin.php [e95bb222e07f] |  |
| ab50958856e300cc | en | admin.partials.role_list.modal_edit.role_badge_color_options.options.emerald | dynamic_workbench #33 [af1ccb31304d]; file #lang/en/admin.php [af1ccb31304d] |  |
| 31e4cea423e9eed1 | en | admin.partials.role_list.modal_edit.role_badge_color_options.options.fuchsia | dynamic_workbench #35 [458315813c0b]; file #lang/en/admin.php [458315813c0b] |  |
| 2d0a3ebbd10f2de8 | en | admin.partials.role_list.modal_edit.role_badge_color_options.options.green | dynamic_workbench #37 [d486dfbd5fb5]; file #lang/en/admin.php [d486dfbd5fb5] |  |
| cf43094878813911 | en | admin.partials.role_list.modal_edit.role_badge_color_options.options.indigo | dynamic_workbench #39 [d81ab149e977]; file #lang/en/admin.php [d81ab149e977] |  |
| 31f03ff1f5b38ac3 | en | admin.partials.role_list.modal_edit.role_badge_color_options.options.lime | dynamic_workbench #41 [c9665c96a867]; file #lang/en/admin.php [c9665c96a867] |  |
| 600576ab0a7e1699 | en | admin.partials.role_list.modal_edit.role_badge_color_options.options.orange | dynamic_workbench #43 [78e7771b8b46]; file #lang/en/admin.php [78e7771b8b46] |  |
| 6ceacd014a22a532 | en | admin.partials.role_list.modal_edit.role_badge_color_options.options.pink | dynamic_workbench #45 [bd38ed773240]; file #lang/en/admin.php [bd38ed773240] |  |
| 1a933d6d707c409d | en | admin.partials.role_list.modal_edit.role_badge_color_options.options.purple | dynamic_workbench #47 [7d465fb9b931]; file #lang/en/admin.php [7d465fb9b931] |  |
| c851542e111ac0ed | en | admin.partials.role_list.modal_edit.role_badge_color_options.options.red | dynamic_workbench #49 [ba19e9c3d5f4]; file #lang/en/admin.php [ba19e9c3d5f4] |  |
| 0b1bb002c90ff330 | en | admin.partials.role_list.modal_edit.role_badge_color_options.options.rose | dynamic_workbench #51 [e10d850954a8]; file #lang/en/admin.php [e10d850954a8] |  |
| c8736a0f9744c28f | en | admin.partials.role_list.modal_edit.role_badge_color_options.options.sky | dynamic_workbench #53 [b72804afefc3]; file #lang/en/admin.php [b72804afefc3] |  |
| 7df50637f87984de | en | admin.partials.role_list.modal_edit.role_badge_color_options.options.teal | dynamic_workbench #55 [fa15a5c1d610]; file #lang/en/admin.php [fa15a5c1d610] |  |
| 1fd22b50f7d913d2 | en | admin.partials.role_list.modal_edit.role_badge_color_options.options.violet | dynamic_workbench #57 [2a36428839a3]; file #lang/en/admin.php [2a36428839a3] |  |
| 1357d784165b601e | en | admin.partials.role_list.modal_edit.role_badge_color_options.options.yellow | dynamic_workbench #59 [19dd83f11752]; file #lang/en/admin.php [19dd83f11752] |  |
| d9ce713a0b8a5cff | en | admin.partials.role_list.modal_edit.role_badge_color_options.options.zinc | dynamic_workbench #61 [4f89fdf9f1d9]; file #lang/en/admin.php [4f89fdf9f1d9] |  |
| dc6a68dc46826963 | en | admin.partials.translation_list.modal_history.timeline.shared.affected_usages.show_usage_locations | file #lang/en/admin.php [8d0ce2baadae]; workbench #1544 [8d0ce2baadae] |  |
| 63792e84abf8e7ed | en | admin.partials.user_list.modal.str_headline_category.options.other | dynamic_workbench #113 [f97e9da0e3b8]; file #lang/en/admin.php [f97e9da0e3b8] |  |
| ead131a396e5f7b2 | en | admin.partials.user_list.table.overview_of_all_users_in_the_system_their_email_addresses_and_assigned_roles | file #lang/en/admin.php [47b8cda0025a]; workbench #1593 [47b8cda0025a] |  |
| b9a0dea72bfdf0cd | en | admin.permission_list.edit_role_permissions | file #lang/en/admin.php [53892711f1b5]; workbench #1595 [53892711f1b5] |  |
| fbc403da183f1403 | en | admin.permission_list.filter.enter_a_search_term_to_filter_permissions_by_name_guard_category_or_description_ | file #lang/en/admin.php [db56d9b90535]; legacy #1448 [db56d9b90535]; workbench #991 [db56d9b90535] |  |
| 7411e09096c1a953 | en | admin.permission_list.filter.filter_by_assignment | file #lang/en/admin.php [8d1ed1cef2ec]; legacy #1531 [8d1ed1cef2ec]; workbench #992 [8d1ed1cef2ec] |  |
| 1bfebb7bf1e5abd3 | en | admin.permission_list.filter.filter_by_guard | file #lang/en/admin.php [5501768942fa]; legacy #1440 [5501768942fa]; workbench #994 [5501768942fa] |  |
| ac3f68abfe91f373 | en | admin.permission_list.filter.filter_by_role | file #lang/en/admin.php [67fd9c1a7c7d]; legacy #1452 [67fd9c1a7c7d]; workbench #995 [67fd9c1a7c7d] |  |
| 7a9df7ad41958b9a | en | admin.permission_list.filter.filter_by_system | file #lang/en/admin.php [1383c4b0a420]; legacy #1525 [1383c4b0a420]; workbench #996 [1383c4b0a420] |  |
| 90c72f8b40fe40d7 | en | admin.permission_list.filter.search | file #lang/en/admin.php [49c266baaaa7]; legacy #1446 [49c266baaaa7] |  |
| 4ad0bc89ed555083 | en | admin.permission_list.filter.select_a_category_to_filter_the_list_of_permissions | file #lang/en/admin.php [3ac96b3e8b7e]; legacy #1450 [3ac96b3e8b7e]; workbench #998 [3ac96b3e8b7e] |  |
| 3d62badb8a89bb23 | en | admin.permission_list.filter.select_a_guard_to_filter_the_list_of_permissions | file #lang/en/admin.php [2e7e6605b6fd]; legacy #1442 [2e7e6605b6fd]; workbench #999 [2e7e6605b6fd] |  |
| 69668fec8e16eab7 | en | admin.permission_list.filter.select_a_role_to_filter_the_list_of_permissions | file #lang/en/admin.php [1072c2fed8cc]; legacy #1533 [1072c2fed8cc]; workbench #1000 [1072c2fed8cc] |  |
| f0fde934762603f3 | en | admin.permission_list.filter.select_a_system_status_to_filter_the_list_of_permissions | file #lang/en/admin.php [086ff0d4013d]; legacy #1527 [086ff0d4013d]; workbench #1001 [086ff0d4013d] |  |
| 6844a16b1f295fd1 | en | admin.permission_list.filter.select_an_assignment_status_to_filter_the_list_of_permissions | file #lang/en/admin.php [8dabe325f2b9]; legacy #1529 [8dabe325f2b9]; workbench #1002 [8dabe325f2b9] |  |
| 61ead19e641ec970 | en | admin.permission_list.table_permissions.actions_available_for_the_permission_useful_for_identification_and_reference | file #lang/en/admin.php [348e76db35f1]; legacy #1470 [348e76db35f1]; workbench #1003 [348e76db35f1] |  |
| 5687c95df9fb5100 | en | admin.permission_list.table_permissions.category_of_the_permission_useful_for_identification_and_reference | file #lang/en/admin.php [9da7c81217ea]; legacy #1458 [9da7c81217ea]; workbench #1004 [9da7c81217ea] |  |
| 4ca9f7356099a181 | en | admin.permission_list.table_permissions.description_of_the_permission_useful_for_identification_and_reference | file #lang/en/admin.php [aceade660220]; legacy #1464 [aceade660220]; workbench #1005 [aceade660220] |  |
| b95527c9fed84f22 | en | admin.permission_list.table_permissions.flags_indicating_special_attributes_of_the_permission_such_as_system_or_custom_u | file #lang/en/admin.php [f6babd72670f]; legacy #1535 [f6babd72670f]; workbench #1006 [f6babd72670f] |  |
| aaceefdb29bae1bb | en | admin.permission_list.table_permissions.guard_of_the_permission_useful_for_identification_and_reference | file #lang/en/admin.php [22737a823e76]; legacy #1462 [22737a823e76]; workbench #1007 [22737a823e76] |  |
| 182ee42c4063f7b3 | en | admin.permission_list.table_permissions.name_of_the_permission_useful_for_identification_and_reference | file #lang/en/admin.php [f3045aef8b2b]; legacy #1456 [f3045aef8b2b]; workbench #1008 [f3045aef8b2b] |  |
| 660f082db3113ce1 | en | admin.permission_list.table_permissions.number_of_roles_associated_with_the_permission_useful_for_identification_and_ref | file #lang/en/admin.php [6ec5f3de4a28]; legacy #1466 [6ec5f3de4a28]; workbench #1009 [6ec5f3de4a28] |  |
| fcc168474d2e68a5 | en | admin.permission_list.table_permissions.pagination | file #lang/en/admin.php [7b3f674b625e]; legacy #1472 [7b3f674b625e] |  |
| 1d45432d834689bb | en | admin.permission_list.table_permissions.roles_assigned_to_the_permission_useful_for_identification_and_reference | file #lang/en/admin.php [c923e5538626]; legacy #1468 [c923e5538626]; workbench #1011 [c923e5538626] |  |
| d636a9df2f3b28ac | en | admin.permission_list.table_permissions.sequential_number_of_the_permission_in_the_current_list_useful_for_reference | file #lang/en/admin.php [60c90200cb91]; legacy #1454 [60c90200cb91]; workbench #1012 [60c90200cb91] |  |
| a190833a5209baf6 | en | admin.permission_list.table_permissions.sort_order_of_the_permission_useful_for_identification_and_reference | file #lang/en/admin.php [c3077e44add5]; legacy #1460 [c3077e44add5]; workbench #1013 [c3077e44add5] |  |
| 81f7b9a3797951ae | en | admin.permissions.description | file #lang/en/admin.php [a10d8e966119]; legacy #4 [a10d8e966119]; workbench #1015 [a10d8e966119] |  |
| 5dea3c27857211b5 | en | admin.permissions.filters.description | file #lang/en/admin.php [81b748c75b43]; legacy #10 [81b748c75b43]; workbench #1018 [81b748c75b43] |  |
| f8a82081bb66aa59 | en | admin.permissions.filters.search.placeholder | file #lang/en/admin.php [742c9fdca21a]; legacy #16 [742c9fdca21a]; workbench #1021 [742c9fdca21a] |  |
| 58dbb5f013600a21 | en | admin.permissions.labels.assigned_roles | file #lang/en/admin.php [423dc5d9738b]; legacy #22 [423dc5d9738b] |  |
| 4547d33fd7ca7080 | en | admin.permissions.labels.system_permission | file #lang/en/admin.php [764c1572dede]; legacy #26 [764c1572dede]; workbench #1026 [764c1572dede] |  |
| e3101c5ec059dc6f | en | admin.permissions.messages.no_changes.heading | file #lang/en/admin.php [c699aa00b26d]; legacy #28 [c699aa00b26d]; workbench #1027 [c699aa00b26d] |  |
| 4be48ce9d3a3d720 | en | admin.permissions.messages.no_changes.role_permissions_unchanged | file #lang/en/admin.php [e354a5c13838]; legacy #30 [e354a5c13838]; workbench #1028 [e354a5c13838] |  |
| 3975cd027250d989 | en | admin.permissions.messages.permission_saved.heading | file #lang/en/admin.php [f1da3519d7b9]; legacy #32 [f1da3519d7b9]; workbench #1029 [f1da3519d7b9] |  |
| f9802d456aee94ca | en | admin.permissions.messages.permission_saved.text | file #lang/en/admin.php [fe3084ffa224]; legacy #34 [fe3084ffa224]; workbench #1030 [fe3084ffa224] |  |
| faa3cc57247d4865 | en | admin.permissions.messages.role_permissions_saved.heading | file #lang/en/admin.php [5553bbebc10c]; legacy #36 [5553bbebc10c]; workbench #1031 [5553bbebc10c] |  |
| 438fe4dae026dbf4 | en | admin.permissions.messages.role_permissions_saved.text | file #lang/en/admin.php [f0a7379a9dc5]; legacy #38 [f0a7379a9dc5]; workbench #1032 [f0a7379a9dc5] |  |
| e1222f303c1d9019 | en | admin.permissions.modals.edit.category_placeholder | file #lang/en/admin.php [c3087d8a0105]; legacy #40 [c3087d8a0105]; workbench #1033 [c3087d8a0105] |  |
| ca33a212d341307f | en | admin.permissions.modals.edit.description | file #lang/en/admin.php [9245c71a1c73]; legacy #42 [9245c71a1c73]; workbench #1034 [9245c71a1c73] |  |
| 4d17b6f2d4dca3a0 | en | admin.permissions.modals.edit.description_placeholder | file #lang/en/admin.php [bcb19a7cf0fc]; legacy #44 [bcb19a7cf0fc]; workbench #1035 [bcb19a7cf0fc] |  |
| a47a07a939026a80 | en | admin.permissions.modals.edit.details_section | file #lang/en/admin.php [34bfbee739b6]; legacy #46 [34bfbee739b6]; workbench #1036 [34bfbee739b6] |  |
| 5f985572734ad3c4 | en | admin.permissions.modals.edit.editable_scope | file #lang/en/admin.php [45701a660e05]; legacy #48 [45701a660e05]; workbench #1037 [45701a660e05] |  |
| f9f6b48703ab26f7 | en | admin.permissions.modals.edit.metadata_only | file #lang/en/admin.php [df0453d185c4]; legacy #50 [df0453d185c4]; workbench #1038 [df0453d185c4] |  |
| ddee6d2c9f27311c | en | admin.permissions.modals.edit.metadata_section | file #lang/en/admin.php [780cd66558e1]; legacy #52 [780cd66558e1]; workbench #1039 [780cd66558e1] |  |
| c0c371361616e3ec | en | admin.permissions.modals.roles.changes | file #lang/en/admin.php [bbd4b6a86bc6]; legacy #56 [bbd4b6a86bc6]; workbench #1041 [bbd4b6a86bc6] |  |
| a64394fc922ce8ff | en | admin.permissions.modals.roles.current_permissions | file #lang/en/admin.php [f44c8799dd5d]; legacy #58 [f44c8799dd5d]; workbench #1042 [f44c8799dd5d] |  |
| a2fd86c90e1fbd50 | en | admin.permissions.modals.roles.description | file #lang/en/admin.php [4b7d0302ff2a]; legacy #60 [4b7d0302ff2a]; workbench #1043 [4b7d0302ff2a] |  |
| 23d710241e466a71 | en | admin.permissions.modals.roles.edit_section | file #lang/en/admin.php [390111837c55]; legacy #62 [390111837c55]; workbench #1044 [390111837c55] |  |
| 623ef03f9c34b7c1 | en | admin.permissions.modals.roles.overview_for | file #lang/en/admin.php [02f1495c5eec]; legacy #64 [02f1495c5eec]; workbench #1045 [02f1495c5eec] |  |
| bacc9f569ca34825 | en | admin.permissions.modals.roles.select_role | file #lang/en/admin.php [ba07cd15125e]; legacy #66 [ba07cd15125e]; workbench #1046 [ba07cd15125e] |  |
| dda489d6bfe01485 | en | admin.permissions.modals.roles.selected_permissions | file #lang/en/admin.php [6f21fb778b0b]; legacy #68 [6f21fb778b0b]; workbench #1047 [6f21fb778b0b] |  |
| 7f7dbe0245f4c00b | en | admin.permissions.modals.roles.set_permissions_section | file #lang/en/admin.php [9a266102cd28]; legacy #70 [9a266102cd28]; workbench #1048 [9a266102cd28] |  |
| 5f694f448a57f9b9 | en | admin.permissions.modals.roles.title | file #lang/en/admin.php [661aa84c3ad9]; legacy #72 [661aa84c3ad9]; workbench #1049 [661aa84c3ad9] |  |
| a26eb74cc6970fc1 | en | admin.permissions.overview.assigned.heading | file #lang/en/admin.php [1211a7b0c405]; legacy #74 [1211a7b0c405]; workbench #1050 [1211a7b0c405] |  |
| 97a11def673afd32 | en | admin.permissions.overview.assigned.text | file #lang/en/admin.php [22749e55af25]; legacy #76 [22749e55af25]; workbench #1051 [22749e55af25] |  |
| 8c8fc34c17310af6 | en | admin.permissions.overview.description | file #lang/en/admin.php [de7a2af36bae]; legacy #78 [de7a2af36bae]; workbench #1052 [de7a2af36bae] |  |
| 21b4990757f2fafd | en | admin.permissions.overview.guards.heading | file #lang/en/admin.php [948e5af90585]; legacy #80 [948e5af90585]; workbench #1053 [948e5af90585] |  |
| 672bec6eb3a0b6e4 | en | admin.permissions.overview.guards.text | file #lang/en/admin.php [b3bfe128f9fa]; legacy #82 [b3bfe128f9fa]; workbench #1054 [b3bfe128f9fa] |  |
| 307b48b3d908d29d | en | admin.permissions.overview.total.heading | file #lang/en/admin.php [bf49d8d93d68]; legacy #86 [bf49d8d93d68]; workbench #1056 [bf49d8d93d68] |  |
| 243696a86fe96ef2 | en | admin.permissions.overview.total.text | file #lang/en/admin.php [e192144cea45]; legacy #88 [e192144cea45]; workbench #1057 [e192144cea45] |  |
| d959f9d3d074b43b | en | admin.permissions.overview.unassigned.heading | file #lang/en/admin.php [b6da48bfb337]; legacy #90 [b6da48bfb337]; workbench #1058 [b6da48bfb337] |  |
| 149d72ce30258ee9 | en | admin.permissions.overview.unassigned.text | file #lang/en/admin.php [9aa87e53d2fd]; legacy #92 [9aa87e53d2fd]; workbench #1059 [9aa87e53d2fd] |  |
| b3e240f45e85ee15 | en | admin.permissions.table.columns.flags | file #lang/en/admin.php [f38d9950af4f]; legacy #96 [f38d9950af4f]; workbench #1061 [f38d9950af4f] |  |
| ecc2ef693661255f | en | admin.permissions.table.columns.sort | file #lang/en/admin.php [bec69036aa27]; legacy #98 [bec69036aa27]; workbench #1062 [bec69036aa27] |  |
| 9fc74d4da634f8ee | en | admin.permissions.table.description | file #lang/en/admin.php [51d780742d1f]; legacy #100 [51d780742d1f]; workbench #1063 [51d780742d1f] |  |
| 87b497c4b8fa5da4 | en | admin.permissions.table.empty | file #lang/en/admin.php [559f07891878]; legacy #102 [559f07891878]; workbench #1064 [559f07891878] |  |
| 63e733287baa7cf7 | en | admin.permissions.table.title | file #lang/en/admin.php [7a3d8e7d99c3]; legacy #104 [7a3d8e7d99c3]; workbench #1065 [7a3d8e7d99c3] |  |
| 9743f793a5f1071e | en | admin.permissions.title | file #lang/en/admin.php [528dc2fe1676]; legacy #106 [528dc2fe1676]; workbench #1066 [528dc2fe1676] |  |
| b676799045de8e4a | en | admin.person_list.filter.filter_by_client | file #lang/en/admin.php [382872b94b96]; legacy #6714 [382872b94b96] |  |
| d32a2c958b9b6b53 | en | admin.roles.actions.create | file #lang/en/admin.php [4759498ac2a7]; legacy #148 [4759498ac2a7] |  |
| d930f66e16ec5ad8 | en | admin.roles.badge.color | file #lang/en/admin.php [6b73191a0a4b]; legacy #150 [6b73191a0a4b]; workbench #1069 [6b73191a0a4b] |  |
| 0294466b5dbd7ac1 | en | admin.roles.badge.icon | file #lang/en/admin.php [a35abcd6dac9]; legacy #152 [a35abcd6dac9]; workbench #1070 [a35abcd6dac9] |  |
| 14f5a57a959f070e | en | admin.roles.badge.title | file #lang/en/admin.php [002474e36821]; legacy #154 [002474e36821]; workbench #1071 [002474e36821] |  |
| ceb6aff8383f5741 | en | admin.roles.badge.variant | file #lang/en/admin.php [3f19fe84a2de]; legacy #156 [3f19fe84a2de]; workbench #1072 [3f19fe84a2de] |  |
| 294e5539d5d95814 | en | admin.roles.description | file #lang/en/admin.php [b9a576088690]; legacy #158 [b9a576088690]; workbench #1073 [b9a576088690] |  |
| 689ca563f15c66d1 | en | admin.roles.labels.assignable_through_ui | file #lang/en/admin.php [4abb94c0a83d]; legacy #160 [4abb94c0a83d] |  |
| aacb1af8218360f3 | en | admin.roles.labels.assigned_users | file #lang/en/admin.php [0ad1e44cdf83]; legacy #162 [0ad1e44cdf83] |  |
| 98ab847bad2e123b | en | admin.roles.labels.sort_order | file #lang/en/admin.php [3c9b30285f90]; legacy #164 [3c9b30285f90] |  |
| c7e2e41b5d436d00 | en | admin.roles.labels.system_role | file #lang/en/admin.php [91c86a3e258a]; legacy #166 [91c86a3e258a]; workbench #1077 [91c86a3e258a] |  |
| 6953fde1e0da01db | en | admin.roles.messages.not_allowed.heading | file #lang/en/admin.php [f0406a0e7eb1]; legacy #168 [f0406a0e7eb1]; workbench #1078 [f0406a0e7eb1] |  |
| a2650b4e9894ca7f | en | admin.roles.messages.not_allowed.only_super_admins_may_create_roles | file #lang/en/admin.php [138377d8adf5]; legacy #170 [138377d8adf5]; workbench #1079 [138377d8adf5] |  |
| 703f8734cc6a9e76 | en | admin.roles.messages.role_created.heading | file #lang/en/admin.php [decb4f059b53]; legacy #172 [decb4f059b53]; workbench #1080 [decb4f059b53] |  |
| 819c5c86df390c56 | en | admin.roles.messages.role_created.text | file #lang/en/admin.php [af070d2f5970]; legacy #174 [af070d2f5970]; workbench #1081 [af070d2f5970] |  |
| b7b3a00428d0fe2f | en | admin.roles.messages.role_saved.heading | file #lang/en/admin.php [9ff67cb05a23]; legacy #176 [9ff67cb05a23]; workbench #1082 [9ff67cb05a23] |  |
| 8d99f5517d73b430 | en | admin.roles.messages.role_saved.text | file #lang/en/admin.php [74cc639ff724]; legacy #178 [74cc639ff724]; workbench #1083 [74cc639ff724] |  |
| 10a9236fd5381483 | en | admin.roles.modals.create.description | file #lang/en/admin.php [e83118a8f9ac]; legacy #180 [e83118a8f9ac]; workbench #1084 [e83118a8f9ac] |  |
| 65bf60f91fb0c45d | en | admin.roles.modals.create.name_placeholder | file #lang/en/admin.php [9cdae25335fb]; legacy #182 [9cdae25335fb]; workbench #1085 [9cdae25335fb] |  |
| 3e44294a4c1f827d | en | admin.roles.modals.create.title | file #lang/en/admin.php [4900552e37b0]; legacy #184 [4900552e37b0]; workbench #1086 [4900552e37b0] |  |
| 1bce73587f92922e | en | admin.roles.modals.edit.title | file #lang/en/admin.php [f37b6f34a42f]; legacy #186 [f37b6f34a42f]; workbench #1087 [f37b6f34a42f] |  |
| 44b867c03d7b9ccb | en | admin.roles.preview.new_role | file #lang/en/admin.php [500f2f9d3956]; legacy #188 [500f2f9d3956]; workbench #1088 [500f2f9d3956] |  |
| 84f5c20235a5d3e2 | en | admin.roles.title | file #lang/en/admin.php [a5b449f8e7b1]; legacy #190 [a5b449f8e7b1]; workbench #1089 [a5b449f8e7b1] |  |
| 5a8aad0650cc0242 | en | admin.translation_list.all_visible_language_variation_values_have_been_cleared | file #lang/en/admin.php [9fbeae53993b]; legacy #6040 [9fbeae53993b]; workbench #1090 [9fbeae53993b] |  |
| 59ce3bf280e31560 | en | admin.translation_list.already_reviewed | file #lang/en/admin.php [01f782ac9a1d]; legacy #1132 [01f782ac9a1d]; workbench #1091 [01f782ac9a1d] |  |
| f969d6858d341860 | en | admin.translation_list.changes_discarded | file #lang/en/admin.php [6fb7a4651054]; legacy #6223 [6fb7a4651054]; workbench #1092 [6fb7a4651054] |  |
| ba95902191a82829 | en | admin.translation_list.cleared | file #lang/en/admin.php [15f3a2898add]; legacy #6042 [15f3a2898add]; workbench #1093 [15f3a2898add] |  |
| 26415afdae347ab5 | en | admin.translation_list.copied | file #lang/en/admin.php [8d525e5f158b]; legacy #6095 [8d525e5f158b]; workbench #1094 [8d525e5f158b] |  |
| 71ade9e93070f7d3 | en | admin.translation_list.filter.all_currently_code_relevant_translation_entries_this_number_changes_when_the_cur | file #lang/en/admin.php [a4e547300782]; legacy #6209 [a4e547300782]; workbench #1095 [a4e547300782] |  |
| 3ff8ea8ee86ee71d | en | admin.translation_list.filter.all_currently_code_relevant_translation_entry_types_that_belong_to_the_active_wo | file #lang/en/admin.php [35503dcdd371]; legacy #6306 [35503dcdd371]; workbench #1096 [35503dcdd371] |  |
| 66537e4e420019b6 | en | admin.translation_list.filter.all_groups | file #lang/en/admin.php [1b492be77224]; legacy #2802 [1b492be77224]; workbench #1097 [1b492be77224] |  |
| 84018132a3f1e4fe | en | admin.translation_list.filter.all_namespaces | file #lang/en/admin.php [50c3cb6e2284]; legacy #2806 [50c3cb6e2284]; workbench #1098 [50c3cb6e2284] |  |
| db138cf36d0063c5 | en | admin.translation_list.filter.all_statuses_within_the_currently_selected_relevance_scope | file #lang/en/admin.php [ede48156ea41]; legacy #6121 [ede48156ea41]; workbench #1099 [ede48156ea41] |  |
| a1b54f2dfc0a021a | en | admin.translation_list.filter.archive | file #lang/en/admin.php [66f4804ee23d]; legacy #6329 [66f4804ee23d]; workbench #1100 [66f4804ee23d] |  |
| 9d360f7a2ca8c2aa | en | admin.translation_list.filter.code_relevant_translation_entries_that_are_still_current_but_have_already_been_m | file #lang/en/admin.php [6fc3b4ddb014]; legacy #5739 [6fc3b4ddb014]; workbench #1101 [6fc3b4ddb014] |  |
| 3e512e232b417ce1 | en | admin.translation_list.filter.code_relevant_translation_entries_that_are_still_open_in_the_workflow_and_should | file #lang/en/admin.php [cbe84d4e614e]; legacy #5715 [cbe84d4e614e]; workbench #1102 [cbe84d4e614e] |  |
| bf0263686e35750e | en | admin.translation_list.filter.code_relevant_translation_entries_with_status_ok_that_are_already_consistent_and | file #lang/en/admin.php [ea641e481a73]; legacy #5706 [ea641e481a73]; workbench #1103 [ea641e481a73] |  |
| 5bb753e07bbc3f3f | en | admin.translation_list.filter.completed | file #lang/en/admin.php [22a970d2e5b1]; legacy #6339 [22a970d2e5b1]; workbench #1104 [22a970d2e5b1] |  |
| a6f2cfb3b6b030e7 | en | admin.translation_list.filter.concrete_translation_keys_that_already_exist_or_become_real_translations_once_th | file #lang/en/admin.php [a4706d08fc59]; legacy #5661 [a4706d08fc59]; workbench #1105 [a4706d08fc59] |  |
| cf249a408ab92eab | en | admin.translation_list.filter.concrete_translation_keys_that_exist_in_code_but_still_miss_the_required_transla | file #lang/en/admin.php [143b0a2c55da]; legacy #3614 [143b0a2c55da]; workbench #1106 [143b0a2c55da] |  |
| 1e2b7453cff1c0a1 | en | admin.translation_list.filter.db_and_files_are_currently_in_sync | file #lang/en/admin.php [c3932a5e0a10]; legacy #6341 [c3932a5e0a10]; workbench #1107 [c3932a5e0a10] |  |
| ab3dd4edc551ca15 | en | admin.translation_list.filter.db_and_files_are_currently_not_in_sync | file #lang/en/admin.php [e21cba7b501e]; legacy #3603 [e21cba7b501e]; workbench #1108 [e21cba7b501e] |  |
| cb26c9c24109ef5a | en | admin.translation_list.filter.dynamic_translation_expressions_built_at_runtime_and_usually_left_as_special_cas | file #lang/en/admin.php [5ba542bcac91]; legacy #5923 [5ba542bcac91]; workbench #1109 [5ba542bcac91] |  |
| 36b3c8b1daaf28dc | en | admin.translation_list.filter.enter_a_search_term_to_filter_the_list_of_translations_by_key_or_value_the_searc | file #lang/en/admin.php [eb9fcd572cd1]; legacy #3413 [eb9fcd572cd1]; workbench #1110 [eb9fcd572cd1] |  |
| eafeafd33d0d52ab | en | admin.translation_list.filter.entries_that_are_no_longer_currently_code_relevant_and_therefore_live_outside_th | file #lang/en/admin.php [c8d12ba3ae8f]; legacy #5670 [c8d12ba3ae8f]; workbench #1111 [c8d12ba3ae8f] |  |
| f9135c5a13ff29b0 | en | admin.translation_list.filter.entries_that_were_derived_or_repaired_from_existing_translation_values_during_ba | file #lang/en/admin.php [0b325bd331e1]; legacy #5921 [0b325bd331e1]; workbench #1112 [0b325bd331e1] |  |
| b0883c407d33609e | en | admin.translation_list.filter.files_lang_files_currently_contain_entries_translation_entries_db_currently_has_ | file #lang/en/admin.php [7123459b9bcb]; legacy #6331 [7123459b9bcb]; workbench #1113 [7123459b9bcb] |  |
| 75c27cde99caaa37 | en | admin.translation_list.filter.filter_by_group | file #lang/en/admin.php [803c1a66fcfb]; legacy #3461 [803c1a66fcfb]; workbench #1114 [803c1a66fcfb] |  |
| cdb993204e3f0c22 | en | admin.translation_list.filter.filter_by_namespace | file #lang/en/admin.php [e9d0316c879e]; legacy #2804 [e9d0316c879e]; workbench #1115 [e9d0316c879e] |  |
| 039ca0718325634c | en | admin.translation_list.filter.filter_by_search | file #lang/en/admin.php [456089f56ba6]; legacy #6337 [456089f56ba6]; workbench #1116 [456089f56ba6] |  |
| d2ac5c7135f89c51 | en | admin.translation_list.filter.filters_active | file #lang/en/admin.php [1334e750d364]; legacy #3465 [1334e750d364]; workbench #1117 [1334e750d364] |  |
| 55583ea5a19efd62 | en | admin.translation_list.filter.invalid_translation_calls_that_definitely_need_correction_and_review | file #lang/en/admin.php [cdcd1796895a]; legacy #6310 [cdcd1796895a]; workbench #1118 [cdcd1796895a] |  |
| 9e4d51dd069240d0 | en | admin.translation_list.filter.literal_texts_found_in_code_that_still_need_review_and_usually_need_to_be_replac | file #lang/en/admin.php [56ea807c57e9]; legacy #3616 [56ea807c57e9]; workbench #1119 [56ea807c57e9] |  |
| cf3ae2be700ce638 | en | admin.translation_list.filter.literal_texts_from_code_that_still_need_to_be_reviewed_and_typically_converted_i | file #lang/en/admin.php [ce710ce8d37b]; legacy #5690 [ce710ce8d37b]; workbench #1120 [ce710ce8d37b] |  |
| 0f14fa111260307d | en | admin.translation_list.filter.main_language_files_locale | file #lang/en/admin.php [5a380f5ad5e4]; legacy #6333 [5a380f5ad5e4]; workbench #1121 [5a380f5ad5e4] |  |
| 8c63f31b97be4b48 | en | admin.translation_list.filter.obsolete_shows_two_counts_first_the_db_obsolete_work_items_then_the_file_obsolet | file #lang/en/admin.php [b36c40ccca0c]; legacy #6314 [b36c40ccca0c]; workbench #1122 [b36c40ccca0c] |  |
| 490ac7e8721e1d69 | en | admin.translation_list.filter.of | file #lang/en/admin.php [28391d3bc64e]; legacy #3467 [28391d3bc64e]; workbench #1123 [28391d3bc64e] |  |
| a904ad34ec1f3855 | en | admin.translation_list.filter.open | file #lang/en/admin.php [ed077f3d8125]; legacy #6302 [ed077f3d8125]; workbench #1124 [ed077f3d8125] |  |
| 11bbc72f5734fe22 | en | admin.translation_list.filter.refine_the_translation_list_by_key_value_language_and_translation_file | file #lang/en/admin.php [0ac100508914]; legacy #5769 [0ac100508914]; workbench #1125 [0ac100508914] |  |
| 5bf01c59e0425826 | en | admin.translation_list.filter.reviewed_history_entries_including_older_or_no_longer_current_items_that_remain_ | file #lang/en/admin.php [cb4c355e00d3]; legacy #6125 [cb4c355e00d3]; workbench #1126 [cb4c355e00d3] |  |
| 1f9f5efc953786b7 | en | admin.translation_list.filter.runtime_generated_translation_values_these_are_special_cases_and_often_remain_dy | file #lang/en/admin.php [c69ea5658eba]; legacy #6316 [c69ea5658eba]; workbench #1127 [c69ea5658eba] |  |
| 46a90f748c585a32 | en | admin.translation_list.filter.search_by_key_or_value | file #lang/en/admin.php [ff216344e216]; legacy #3419 [ff216344e216]; workbench #1128 [ff216344e216] |  |
| 1df19368b1b57f3f | en | admin.translation_list.filter.select_a_target_language | file #lang/en/admin.php [29ea20f85b58]; legacy #6327 [29ea20f85b58]; workbench #1129 [29ea20f85b58] |  |
| 4d836eed53da9d48 | en | admin.translation_list.filter.select_a_target_language_to_filter_the_list_of_translations | file #lang/en/admin.php [c2ca59dd1611]; legacy #3415 [c2ca59dd1611]; workbench #1130 [c2ca59dd1611] |  |
| c82eb75dbd7dc3f3 | en | admin.translation_list.filter.select_a_translation_group_within_the_current_namespace_and_filter_context | file #lang/en/admin.php [1698a304827a]; legacy #3463 [1698a304827a]; workbench #1131 [1698a304827a] |  |
| 39bf2b0780715f4b | en | admin.translation_list.filter.select_a_translation_namespace_to_narrow_the_list_to_the_corresponding_translati | file #lang/en/admin.php [0c420dc47962]; legacy #3411 [0c420dc47962]; workbench #1132 [0c420dc47962] |  |
| e8d4559fd788db5b | en | admin.translation_list.filter.select_target_language | file #lang/en/admin.php [1a98f37e60f4]; legacy #6343 [1a98f37e60f4]; workbench #1133 [1a98f37e60f4] |  |
| 4d7574e4e7ea36fb | en | admin.translation_list.filter.showing | file #lang/en/admin.php [d604310a789a]; legacy #3505 [d604310a789a]; workbench #1134 [d604310a789a] |  |
| 04cdc42e483a825d | en | admin.translation_list.filter.status_all | file #lang/en/admin.php [02fc5f9ff0f5]; legacy #3612 [02fc5f9ff0f5]; workbench #1135 [02fc5f9ff0f5] |  |
| ecf00d2402b43bc2 | en | admin.translation_list.filter.status_dynamic | file #lang/en/admin.php [d0a07e0221da]; legacy #5737 [d0a07e0221da]; workbench #1136 [d0a07e0221da] |  |
| 95de662e7aaf2761 | en | admin.translation_list.filter.status_invalid | file #lang/en/admin.php [62c1eb77d1fc]; legacy #6312 [62c1eb77d1fc]; workbench #1137 [62c1eb77d1fc] |  |
| 5c4901073887c1c2 | en | admin.translation_list.filter.status_missing | file #lang/en/admin.php [e778af0e681c]; legacy #6318 [e778af0e681c]; workbench #1138 [e778af0e681c] |  |
| f1532308ed831a45 | en | admin.translation_list.filter.status_native | file #lang/en/admin.php [ce37d9866725]; legacy #3618 [ce37d9866725]; workbench #1139 [ce37d9866725] |  |
| 5bdbec09e3b790ca | en | admin.translation_list.filter.status_obsolete | file #lang/en/admin.php [89319b6e8dd2]; legacy #6127 [89319b6e8dd2]; workbench #1140 [89319b6e8dd2] |  |
| f2475630904367b3 | en | admin.translation_list.filter.status_ok | file #lang/en/admin.php [27aac15d4f4d]; legacy #5733 [27aac15d4f4d]; workbench #1141 [27aac15d4f4d] |  |
| 541db3c094df3be0 | en | admin.translation_list.filter.target_language | file #lang/en/admin.php [5714aa6c4bb2]; legacy #3417 [5714aa6c4bb2]; workbench #1142 [5714aa6c4bb2] |  |
| fbe98335439f65ff | en | admin.translation_list.filter.translations_that_are_already_coherent_and_can_be_actively_used_in_the_lang_file | file #lang/en/admin.php [abdd5d0ace66]; legacy #6335 [abdd5d0ace66]; workbench #1143 [abdd5d0ace66] |  |
| 211313fb77c29998 | en | admin.translation_list.filter.type_all | file #lang/en/admin.php [9dbfe9fc17a5]; legacy #6308 [9dbfe9fc17a5]; workbench #1144 [9dbfe9fc17a5] |  |
| c165f189d36b942a | en | admin.translation_list.filter.type_archive | file #lang/en/admin.php [228e2f3d653a]; legacy #6304 [228e2f3d653a]; workbench #1145 [228e2f3d653a] |  |
| 7d017f2c58dd4b94 | en | admin.translation_list.filter.type_backfill | file #lang/en/admin.php [9f98337dca84]; legacy #5890 [9f98337dca84]; workbench #1146 [9f98337dca84] |  |
| e2d16af9c82bed7f | en | admin.translation_list.filter.type_dynamic | file #lang/en/admin.php [ce8134df4561]; legacy #5679 [ce8134df4561]; workbench #1147 [ce8134df4561] |  |
| 763e165758615f53 | en | admin.translation_list.filter.type_key | file #lang/en/admin.php [6e9b05b59bf2]; legacy #5910 [6e9b05b59bf2]; workbench #1148 [6e9b05b59bf2] |  |
| 4c6196e383cd8115 | en | admin.translation_list.filter.type_native | file #lang/en/admin.php [9a9732542dbe]; legacy #5912 [9a9732542dbe]; workbench #1149 [9a9732542dbe] |  |
| cc636d41b2ed48b2 | en | admin.translation_list.filter.type_vendor | file #lang/en/admin.php [1ce43b231e84]; legacy #5886 [1ce43b231e84]; workbench #1150 [1ce43b231e84] |  |
| 1cf710a1a3765028 | en | admin.translation_list.filter.vendor_translation_entries_imported_from_package_language_files | file #lang/en/admin.php [2a21b8b457dc]; legacy #5688 [2a21b8b457dc]; workbench #1151 [2a21b8b457dc] |  |
| 8f64b3ccdc965c1b | en | admin.translation_list.filter.workflow_all | file #lang/en/admin.php [3df8999033cd]; legacy #6207 [3df8999033cd]; workbench #1152 [3df8999033cd] |  |
| 8aa28f96ee132036 | en | admin.translation_list.filter.workflow_completed | file #lang/en/admin.php [b81868763eac]; legacy #6123 [b81868763eac]; workbench #1153 [b81868763eac] |  |
| 881121f01d0c7dac | en | admin.translation_list.filter.workflow_history | file #lang/en/admin.php [282384c92a15]; legacy #3609 [282384c92a15]; workbench #1154 [282384c92a15] |  |
| f337455c9d123bef | en | admin.translation_list.filter.workflow_open | file #lang/en/admin.php [81a9e9bc2efc]; legacy #6119 [81a9e9bc2efc]; workbench #1155 [81a9e9bc2efc] |  |
| 2a0d7a1f532b9b3c | en | admin.translation_list.filter.workflow_reviewed | file #lang/en/admin.php [1338507fda2d]; legacy #6129 [1338507fda2d]; workbench #1156 [1338507fda2d] |  |
| cfee020b107a0080 | en | admin.translation_list.meta.active_filters | file #lang/en/admin.php [213a9822c38a]; legacy #5735 [213a9822c38a]; workbench #1157 [213a9822c38a] |  |
| 88153d5a3405e682 | en | admin.translation_list.meta.backfill | file #lang/en/admin.php [ddfbe4eb2a4b]; legacy #2774 [ddfbe4eb2a4b]; workbench #1158 [ddfbe4eb2a4b] |  |
| 8b574513d1abe3c7 | en | admin.translation_list.meta.current_result | file #lang/en/admin.php [67d07d5a8dc0]; legacy #2794 [67d07d5a8dc0]; workbench #1159 [67d07d5a8dc0] |  |
| f4b661f81d60d474 | en | admin.translation_list.meta.filter_state | file #lang/en/admin.php [8d57c5db38a1]; legacy #2565 [8d57c5db38a1]; workbench #1160 [8d57c5db38a1] |  |
| 3633f671184b3504 | en | admin.translation_list.meta.filtered | file #lang/en/admin.php [0ba993b39efb]; legacy #2781 [0ba993b39efb]; workbench #1161 [0ba993b39efb] |  |
| 422e3c8a4fc390e0 | en | admin.translation_list.meta.group | file #lang/en/admin.php [34ca0e766088]; legacy #2539 [34ca0e766088] |  |
| 750c240c5450bed4 | en | admin.translation_list.meta.language | file #lang/en/admin.php [a4fe65264ef7]; legacy #2788 [a4fe65264ef7]; workbench #1163 [a4fe65264ef7] |  |
| a8f795f98b162c6f | en | admin.translation_list.meta.matching_keys | file #lang/en/admin.php [c3cdcafe99be]; legacy #5444 [c3cdcafe99be]; workbench #1164 [c3cdcafe99be] |  |
| f0fcf24bee943f60 | en | admin.translation_list.meta.missing_and_dynamic_translation_entries_requiring_review | file #lang/en/admin.php [0d699d9b7039]; legacy #5871 [0d699d9b7039]; workbench #1165 [0d699d9b7039] |  |
| 27adb532dfb50aff | en | admin.translation_list.meta.namespace | file #lang/en/admin.php [c4e4e7abda20]; legacy #2545 [c4e4e7abda20] |  |
| ac1778da2b2c2f34 | en | admin.translation_list.meta.native_texts_reconstructed_from_existing_en_translation_values | file #lang/en/admin.php [56b5ffcca12d]; legacy #2779 [56b5ffcca12d]; workbench #1167 [56b5ffcca12d] |  |
| 48292e94c895fd7e | en | admin.translation_list.meta.no_active_filters | file #lang/en/admin.php [b4d922046d14]; legacy #2535 [b4d922046d14]; workbench #1168 [b4d922046d14] |  |
| c647b508dbedd5b2 | en | admin.translation_list.meta.no_filters_are_currently_active | file #lang/en/admin.php [acbbb76ee564]; legacy #5533 [acbbb76ee564]; workbench #1169 [acbbb76ee564] |  |
| f34fad96c2a49e79 | en | admin.translation_list.meta.obsolete | file #lang/en/admin.php [a97fc8713fcd]; legacy #2798 [a97fc8713fcd]; workbench #1170 [a97fc8713fcd] |  |
| 3900cdfb674a1a95 | en | admin.translation_list.meta.ok | file #lang/en/admin.php [565339bc4d33]; legacy #5519 [565339bc4d33]; workbench #1171 [565339bc4d33] |  |
| b7aee2f62175a205 | en | admin.translation_list.meta.one_or_more_filters_are_currently_active | file #lang/en/admin.php [ddbbb8b10fb0]; legacy #5609 [ddbbb8b10fb0]; workbench #1172 [ddbbb8b10fb0] |  |
| 56b744227b0f6b6a | en | admin.translation_list.meta.only_problems | file #lang/en/admin.php [0b880deb5843]; legacy #2785 [0b880deb5843]; workbench #1173 [0b880deb5843] |  |
| 925d3cc9659ce6c6 | en | admin.translation_list.meta.overview_of_the_active_translation_filters_and_the_currently_matching_result_set | file #lang/en/admin.php [02bd60b51f55]; legacy #2796 [02bd60b51f55]; workbench #1174 [02bd60b51f55] |  |
| d84d8b9bd0b0de8d | en | admin.translation_list.meta.per_page | file #lang/en/admin.php [960e4b5400ec]; legacy #2541 [960e4b5400ec] |  |
| db69ee74823f9a4e | en | admin.translation_list.meta.problems | file #lang/en/admin.php [a7b182f8c24e]; legacy #5517 [a7b182f8c24e]; workbench #1176 [a7b182f8c24e] |  |
| d8583443fd86fc4f | en | admin.translation_list.meta.regular_audit_keys_without_special_origin_classification | file #lang/en/admin.php [c382e97300bf]; legacy #5527 [c382e97300bf]; workbench #1177 [c382e97300bf] |  |
| 6b4a4b3795838808 | en | admin.translation_list.meta.status_dynamic | file #lang/en/admin.php [6e4307d64260]; legacy #6211 [6e4307d64260]; workbench #1178 [6e4307d64260] |  |
| 0268074fcd7516d9 | en | admin.translation_list.meta.status_native | file #lang/en/admin.php [714119f0b8f4]; legacy #5724 [714119f0b8f4]; workbench #1179 [714119f0b8f4] |  |
| cbd300641997b1cc | en | admin.translation_list.meta.total_keys | file #lang/en/admin.php [a7b29d5c9377]; legacy #5771 [a7b29d5c9377]; workbench #1180 [a7b29d5c9377] |  |
| 70eaf4fed540dad0 | en | admin.translation_list.meta.translated | file #lang/en/admin.php [abbc13c13e35]; legacy #2533 [abbc13c13e35]; workbench #1181 [abbc13c13e35] |  |
| c485d2fe3362e14a | en | admin.translation_list.meta.translation_keys_classified_as_native_source_entries | file #lang/en/admin.php [d1887044d0f1]; legacy #3543 [d1887044d0f1]; workbench #1182 [d1887044d0f1] |  |
| 94467661e3770511 | en | admin.translation_list.meta.translation_keys_currently_known_in_the_audit_table | file #lang/en/admin.php [3bb87548e838]; legacy #5869 [3bb87548e838]; workbench #1183 [3bb87548e838] |  |
| 0672b98df56c88b7 | en | admin.translation_list.meta.translation_keys_currently_marked_as_obsolete | file #lang/en/admin.php [416a693dbbce]; legacy #2553 [416a693dbbce]; workbench #1184 [416a693dbbce] |  |
| 89fdd578f3904c2c | en | admin.translation_list.meta.translation_keys_currently_marked_as_ok | file #lang/en/admin.php [e9d2494a2672]; legacy #5521 [e9d2494a2672]; workbench #1185 [e9d2494a2672] |  |
| 1f3b616f40605ffb | en | admin.translation_list.meta.translation_keys_currently_marked_with_dynamic_status | file #lang/en/admin.php [ad2ec7b0216e]; legacy #5613 [ad2ec7b0216e]; workbench #1186 [ad2ec7b0216e] |  |
| 0e61147972a29c12 | en | admin.translation_list.meta.translation_keys_currently_marked_with_native_status | file #lang/en/admin.php [828a6c3b3686]; legacy #5531 [828a6c3b3686]; workbench #1187 [828a6c3b3686] |  |
| 6de8ff4b569962ac | en | admin.translation_list.meta.translation_keys_imported_from_vendor_package_sources | file #lang/en/admin.php [f040b018b9fc]; legacy #2783 [f040b018b9fc]; workbench #1188 [f040b018b9fc] |  |
| 5ea265c85e3e9269 | en | admin.translation_list.meta.translation_keys_matching_the_current_filters | file #lang/en/admin.php [c4bcb59aaa91]; legacy #5611 [c4bcb59aaa91]; workbench #1189 [c4bcb59aaa91] |  |
| bd66b6cdf50b44b0 | en | admin.translation_list.meta.translation_keys_with_missing_values_or_required_follow_up | file #lang/en/admin.php [62ef0be38a83]; legacy #5523 [62ef0be38a83]; workbench #1190 [62ef0be38a83] |  |
| 71fa9e14cbc85a49 | en | admin.translation_list.meta.type_native | file #lang/en/admin.php [96d9cbd14147]; legacy #5529 [96d9cbd14147]; workbench #1191 [96d9cbd14147] |  |
| 25d0a23de56d9bc6 | en | admin.translation_list.meta.unfiltered | file #lang/en/admin.php [53a2a8b86e5f]; legacy #2777 [53a2a8b86e5f]; workbench #1192 [53a2a8b86e5f] |  |
| 9faf405318bca62c | en | admin.translation_list.meta.vendor | file #lang/en/admin.php [f8aa82b42d07]; legacy #5525 [f8aa82b42d07]; workbench #1193 [f8aa82b42d07] |  |
| 08f584743426224a | en | admin.translation_list.meta.workflow | file #lang/en/admin.php [2e2d5c5649b7]; legacy #2790 [2e2d5c5649b7] |  |
| c58c2e75ca939f70 | en | admin.translation_list.modal.copy_to_translation_key | file #lang/en/admin.php [2304c07f1a29]; legacy #2800 [2304c07f1a29]; workbench #1195 [2304c07f1a29] |  |
| 813aa2556c405668 | en | admin.translation_list.modal.current_raw | file #lang/en/admin.php [a76977f4b7b8]; legacy #6196 [a76977f4b7b8]; workbench #1196 [a76977f4b7b8] |  |
| 205b2112faf63269 | en | admin.translation_list.modal.edit | file #lang/en/admin.php [464c4ffd019e]; legacy #2682 [464c4ffd019e] |  |
| 2fbc9582ff5bc440 | en | admin.translation_list.modal.group | file #lang/en/admin.php [34ca0e766088]; legacy #2563 [34ca0e766088]; workbench #1198 [34ca0e766088] |  |
| cfd9bb1a88cbe2fe | en | admin.translation_list.modal.key_obsolete_diff | file #lang/en/admin.php [75b2c9380db2]; legacy #6217 [75b2c9380db2]; workbench #1199 [75b2c9380db2] |  |
| 52e53bd7749bfbb4 | en | admin.translation_list.modal.key_suggestion_check | file #lang/en/admin.php [898f032f9f86]; legacy #3441 [898f032f9f86]; workbench #1200 [898f032f9f86] |  |
| 437dfeede7da2e3b | en | admin.translation_list.modal.line | file #lang/en/admin.php [d7852cd0d245]; legacy #2678 [d7852cd0d245]; workbench #1201 [d7852cd0d245] |  |
| d6441564334aa032 | en | admin.translation_list.modal.matches_suggested_key | file #lang/en/admin.php [00a410505852]; legacy #2559 [00a410505852]; workbench #1202 [00a410505852] |  |
| 415f3ea9ee8677d3 | en | admin.translation_list.modal.missing_key | file #lang/en/admin.php [0c8a17a63a32]; legacy #2555 [0c8a17a63a32]; workbench #1203 [0c8a17a63a32] |  |
| 9f190eebb92f7536 | en | admin.translation_list.modal.namespace | file #lang/en/admin.php [c4e4e7abda20]; legacy #2567 [c4e4e7abda20]; workbench #1204 [c4e4e7abda20] |  |
| e7e192b6870478e7 | en | admin.translation_list.modal.native_text | file #lang/en/admin.php [016d5674998b]; legacy #3517 [016d5674998b]; workbench #1205 [016d5674998b] |  |
| 0ed633dc8bdd2846 | en | admin.translation_list.modal.no_key_shape_diff_detected_obsolete_is_likely_caused_by_missing_in_code_usage | file #lang/en/admin.php [a910d8e5fade]; legacy #6023 [a910d8e5fade]; workbench #1206 [a910d8e5fade] |  |
| f34ecda246902e40 | en | admin.translation_list.modal.no_original_raw | file #lang/en/admin.php [f002506ad833]; legacy #2676 [f002506ad833]; workbench #1207 [f002506ad833] |  |
| 1521093325efe6ee | en | admin.translation_list.modal.no_original_raw_reference | file #lang/en/admin.php [40b994112492]; legacy #2674 [40b994112492]; workbench #1208 [40b994112492] |  |
| 49b6f8b4ef506b4b | en | admin.translation_list.modal.no_original_raw_reference_has_been_captured_for_this_usage_yet | file #lang/en/admin.php [138d61d715b9]; legacy #2666 [138d61d715b9]; workbench #1209 [138d61d715b9] |  |
| 1dee388195b84037 | en | admin.translation_list.modal.no_suggested_key_is_available_for_this_entry | file #lang/en/admin.php [355d6882c5e7]; legacy #6213 [355d6882c5e7]; workbench #1210 [355d6882c5e7] |  |
| 69cd52bf457cdfb9 | en | admin.translation_list.modal.no_suggestion | file #lang/en/admin.php [c20483de384e]; legacy #2685 [c20483de384e]; workbench #1211 [c20483de384e] |  |
| 8c2bb40950a3353f | en | admin.translation_list.modal.no_translation_key_is_set_the_suggested_key_can_be_used_as_a_starting_point | file #lang/en/admin.php [d2388465b078]; legacy #5446 [d2388465b078]; workbench #1212 [d2388465b078] |  |
| 9f6d3b462f5da5be | en | admin.translation_list.modal.no_translation_key_selected | file #lang/en/admin.php [1896ef295e8e]; legacy #2684 [1896ef295e8e]; workbench #1213 [1896ef295e8e] |  |
| 20858077579763e0 | en | admin.translation_list.modal.no_translation_values_available | file #lang/en/admin.php [be5ad8d543f3]; legacy #3519 [be5ad8d543f3]; workbench #1214 [be5ad8d543f3] |  |
| d8b0e98928caf037 | en | admin.translation_list.modal.open_next_review_entry | file #lang/en/admin.php [69712279e4c9]; legacy #3444 [69712279e4c9]; workbench #1215 [69712279e4c9] |  |
| 7c4644f197f9f6c4 | en | admin.translation_list.modal.original_changed | file #lang/en/admin.php [f4f3c877cc8b]; legacy #2672 [f4f3c877cc8b]; workbench #1216 [f4f3c877cc8b] |  |
| 7c23965fe4e9081a | en | admin.translation_list.modal.original_raw | file #lang/en/admin.php [ba906822e40f]; legacy #2680 [ba906822e40f]; workbench #1217 [ba906822e40f] |  |
| e6c748386ebbcd36 | en | admin.translation_list.modal.original_raw_differs | file #lang/en/admin.php [e9645eccbc21]; legacy #6032 [e9645eccbc21]; workbench #1218 [e9645eccbc21] |  |
| 72abf9239ac52df8 | en | admin.translation_list.modal.original_raw_unchanged | file #lang/en/admin.php [2d260d241a2b]; legacy #5888 [2d260d241a2b]; workbench #1219 [2d260d241a2b] |  |
| bab0a590756f85bb | en | admin.translation_list.modal.original_unchanged | file #lang/en/admin.php [7d5ccb8dd7a5]; legacy #5442 [7d5ccb8dd7a5]; workbench #1220 [7d5ccb8dd7a5] |  |
| 30beacc9bf6c771a | en | admin.translation_list.modal.path | file #lang/en/admin.php [62fa5a5b0d3c]; legacy #2668 [62fa5a5b0d3c]; workbench #1221 [62fa5a5b0d3c] |  |
| c6aab77adacbaff5 | en | admin.translation_list.modal.read_only_review_of_the_selected_translation_key_its_values_and_usage_metadata | file #lang/en/admin.php [2d88d169b161]; legacy #5607 [2d88d169b161]; workbench #1222 [2d88d169b161] |  |
| ed5cef8f99fbf51e | en | admin.translation_list.modal.source | file #lang/en/admin.php [0e570ca6fabe]; legacy #2557 [0e570ca6fabe]; workbench #1223 [0e570ca6fabe] |  |
| a0c5eda9a0090644 | en | admin.translation_list.modal.suggested_key | file #lang/en/admin.php [91b64f5d3833]; legacy #6215 [91b64f5d3833] |  |
| 85389698285f19a4 | en | admin.translation_list.modal.the_current_key_differs_from_the_generated_suggestion_this_can_be_intentional_bu | file #lang/en/admin.php [9da027a9aa05]; legacy #2740 [9da027a9aa05]; workbench #1225 [9da027a9aa05] |  |
| 9eb946626e1a7db5 | en | admin.translation_list.modal.the_current_key_matches_the_generated_suggestion | file #lang/en/admin.php [1b07a9075216]; legacy #5780 [1b07a9075216]; workbench #1226 [1b07a9075216] |  |
| d3d6de1a5fb86fcb | en | admin.translation_list.modal.the_current_raw_usage_snippet_differs_from_the_original_raw_reference_the_origin | file #lang/en/admin.php [2f296452f116]; legacy #2670 [2f296452f116]; workbench #1227 [2f296452f116] |  |
| 2699c7ecd746cfef | en | admin.translation_list.modal.the_current_raw_usage_snippet_matches_the_original_raw_reference_captured_for_th | file #lang/en/admin.php [24730357dd74]; legacy #5440 [24730357dd74]; workbench #1228 [24730357dd74] |  |
| b606262c99d60e36 | en | admin.translation_list.modal.translation_key_review | file #lang/en/admin.php [d3082911e619]; legacy #2515 [d3082911e619]; workbench #1229 [d3082911e619] |  |
| f720750cf3c1e1d8 | en | admin.translation_list.modal.usage | file #lang/en/admin.php [8d59829c1e15]; legacy #2561 [8d59829c1e15]; workbench #1230 [8d59829c1e15] |  |
| e40c4b6505ffdefd | en | admin.translation_list.modal.usages | file #lang/en/admin.php [664f560b9fbc]; legacy #2664 [664f560b9fbc]; workbench #1231 [664f560b9fbc] |  |
| 2126df831be16637 | en | admin.translation_list.modal.values | file #lang/en/admin.php [53b09e104f60]; legacy #5432 [53b09e104f60]; workbench #1232 [53b09e104f60] |  |
| 129eec5b04c4a672 | en | admin.translation_list.modal.wavy_underline_marks_only_the_differing_key_block | file #lang/en/admin.php [81faac784cdb]; legacy #6219 [81faac784cdb]; workbench #1233 [81faac784cdb] |  |
| d8b0a6a6b1d3454b | en | admin.translation_list.modal_edit.below_are_the_translation_values_for_each_language_you_can_edit_the_values_and_s | file #lang/en/admin.php [c20c41fe8f03]; legacy #3547 [c20c41fe8f03]; workbench #1234 [c20c41fe8f03] |  |
| 3346cacf1103bae3 | en | admin.translation_list.modal_edit.clear_all_language_variations | file #lang/en/admin.php [fe61026a0041]; legacy #6194 [fe61026a0041]; workbench #1235 [fe61026a0041] |  |
| 45fa8cc41fd2b19e | en | admin.translation_list.modal_edit.copy_to_all_language_variations | file #lang/en/admin.php [a8af3a784df4]; legacy #6115 [a8af3a784df4]; workbench #1236 [a8af3a784df4] |  |
| 0cc9c84f463e977c | en | admin.translation_list.modal_edit.copy_to_en_value | file #lang/en/admin.php [fee2f5a95aeb]; legacy #3551 [fee2f5a95aeb]; workbench #1237 [fee2f5a95aeb] |  |
| bfb73729f79b11d7 | en | admin.translation_list.modal_edit.edit_mode | file #lang/en/admin.php [e7c3e7e5dd6c]; legacy #3559 [e7c3e7e5dd6c]; workbench #1238 [e7c3e7e5dd6c] |  |
| fcd1652115d04f67 | en | admin.translation_list.modal_edit.edit_translation_values_for_the_selected_key_key_metadata_is_read_only | file #lang/en/admin.php [8a90e78c1adc]; legacy #2527 [8a90e78c1adc]; workbench #1239 [8a90e78c1adc] |  |
| 588e01f4221d7b58 | en | admin.translation_list.modal_edit.language_variations | file #lang/en/admin.php [1a1aad0bbd69]; legacy #6117 [1a1aad0bbd69]; workbench #1240 [1a1aad0bbd69] |  |
| 5303436cbecc11b8 | en | admin.translation_list.modal_edit.mark_this_obsolete_entry_as_reviewed_so_it_is_removed_from_the_default_open_work | file #lang/en/admin.php [6989b076716e]; legacy #2517 [6989b076716e]; workbench #1241 [6989b076716e] |  |
| d17b241ae9582e2a | en | admin.translation_list.modal_edit.no_usage_records_available | file #lang/en/admin.php [ae6b1aa73b2d]; legacy #3459 [ae6b1aa73b2d]; workbench #1242 [ae6b1aa73b2d] |  |
| 44664feafd9c329d | en | admin.translation_list.modal_edit.only_translation_values_are_editable_keys_native_text_and_usage_metadata_are_rea | file #lang/en/admin.php [7716ad110cea]; legacy #3507 [7716ad110cea]; workbench #1243 [7716ad110cea] |  |
| dc4a9c741561ec05 | en | admin.translation_list.modal_edit.open_next_editable_entry | file #lang/en/admin.php [dcd8a91e9c66]; legacy #2742 [dcd8a91e9c66] |  |
| 0b4058bfd5ce274e | en | admin.translation_list.modal_edit.readonly_source_locations_where_this_translation_key_is_currently_used | file #lang/en/admin.php [280a79f7cc25]; legacy #3457 [280a79f7cc25]; workbench #1245 [280a79f7cc25] |  |
| 1e195ecf95c09366 | en | admin.translation_list.modal_edit.reviewed | file #lang/en/admin.php [fad6057bb540]; legacy #3557 [fad6057bb540]; workbench #1246 [fad6057bb540] |  |
| 7564298e4b40ad5f | en | admin.translation_list.modal_edit.sub_language_values | file #lang/en/admin.php [29fac4db120e]; legacy #5448 [29fac4db120e]; workbench #1247 [29fac4db120e] |  |
| 1fb760ac2f53804c | en | admin.translation_list.modal_edit.the_native_text_is_the_original_string_in_the_source_language_it_serves_as_a_ref | file #lang/en/admin.php [03b3559a2cc3]; legacy #3452 [03b3559a2cc3]; workbench #1248 [03b3559a2cc3] |  |
| 19ee735446a6c920 | en | admin.translation_list.modal_edit.translation_key | file #lang/en/admin.php [23f14a84c6ee]; legacy #3561 [23f14a84c6ee]; workbench #1249 [23f14a84c6ee] |  |
| a83bdda61538c718 | en | admin.translation_list.modal_edit.translation_values | file #lang/en/admin.php [e55b7c9287fb]; legacy #3545 [e55b7c9287fb]; workbench #1250 [e55b7c9287fb] |  |
| c48868aa3c01dd5b | en | admin.translation_list.modal_edit.translation_values_edit | file #lang/en/admin.php [19c480e8e686]; legacy #2551 [19c480e8e686]; workbench #1251 [19c480e8e686] |  |
| b78b7070fa05537a | en | admin.translation_list.modal_edit.usage_locations | file #lang/en/admin.php [04386841298c]; legacy #3454 [04386841298c]; workbench #1252 [04386841298c] |  |
| af571b3e60fa9e84 | en | admin.translation_list.modal_history.audit_events_and_change_history_for_the_selected_translation_key | file #lang/en/admin.php [346c4e41e2d5]; legacy #2519 [346c4e41e2d5]; workbench #1253 [346c4e41e2d5] |  |
| f730d01cfe2e7c0e | en | admin.translation_list.modal_history.context | file #lang/en/admin.php [a6e600a10fed]; legacy #2523 [a6e600a10fed]; workbench #1254 [a6e600a10fed] |  |
| fd2b4d8d689b5383 | en | admin.translation_list.modal_history.event | file #lang/en/admin.php [4e1f49a9c8ae]; legacy #5853 [4e1f49a9c8ae]; workbench #1255 [4e1f49a9c8ae] |  |
| 351fe7fb2b182d61 | en | admin.translation_list.modal_history.events | file #lang/en/admin.php [8d14f6e72de8]; legacy #5837 [8d14f6e72de8]; workbench #1256 [8d14f6e72de8] |  |
| 670f6a74736379ed | en | admin.translation_list.modal_history.latest_audit_events_for_this_translation_key | file #lang/en/admin.php [bcce11814d94]; legacy #5782 [bcce11814d94]; workbench #1257 [bcce11814d94] |  |
| 62756db8a1e3445d | en | admin.translation_list.modal_history.new_location | file #lang/en/admin.php [9a234df4269a]; legacy #2513 [9a234df4269a]; workbench #1258 [9a234df4269a] |  |
| 7fa27264a74910a9 | en | admin.translation_list.modal_history.new_status | file #lang/en/admin.php [be57ae1f5c6b]; legacy #2507 [be57ae1f5c6b]; workbench #1259 [be57ae1f5c6b] |  |
| 6723c4dec06840f3 | en | admin.translation_list.modal_history.new_value | file #lang/en/admin.php [7ca9b97db8e5]; legacy #2509 [7ca9b97db8e5]; workbench #1260 [7ca9b97db8e5] |  |
| 51ed0844dfd43d23 | en | admin.translation_list.modal_history.no_history_events_available_for_this_translation_key | file #lang/en/admin.php [10378fc6bd0f]; legacy #2543 [10378fc6bd0f]; workbench #1261 [10378fc6bd0f] |  |
| 051569bda432ea5a | en | admin.translation_list.modal_history.old_location | file #lang/en/admin.php [464795274708]; legacy #2511 [464795274708]; workbench #1262 [464795274708] |  |
| 33cb94f8d7c225e7 | en | admin.translation_list.modal_history.old_status | file #lang/en/admin.php [54128a1e16ac]; legacy #2525 [54128a1e16ac]; workbench #1263 [54128a1e16ac] |  |
| 8609e55e124924f0 | en | admin.translation_list.modal_history.old_value | file #lang/en/admin.php [a7c253b4a311]; legacy #2521 [a7c253b4a311]; workbench #1264 [a7c253b4a311] |  |
| 8feef65884f614b2 | en | admin.translation_list.modal_history.reason | file #lang/en/admin.php [f81ab834de5f]; legacy #2529 [f81ab834de5f]; workbench #1265 [f81ab834de5f] |  |
| f8741192dcd7748a | en | admin.translation_list.modal_history.translation_history | file #lang/en/admin.php [3ad4a3eea19a]; legacy #2547 [3ad4a3eea19a]; workbench #1266 [3ad4a3eea19a] |  |
| 33f23afafeb9b4bd | en | admin.translation_list.no_suggested_key_available | file #lang/en/admin.php [5162490893c8]; legacy #1116 [5162490893c8]; workbench #1267 [5162490893c8] |  |
| 5ef8c5178341f14d | en | admin.translation_list.obsolete_entry_reviewed | file #lang/en/admin.php [1b9cce68ce10]; legacy #6038 [1b9cce68ce10]; workbench #1268 [1b9cce68ce10] |  |
| c4883f3f056cb53d | en | admin.translation_list.only_obsolete_entries_can_be_marked_as_reviewed | file #lang/en/admin.php [1b4f8db086a7]; legacy #1130 [1b4f8db086a7]; workbench #1269 [1b4f8db086a7] |  |
| c5b9150f8f664a19 | en | admin.translation_list.review_not_applicable | file #lang/en/admin.php [8f1d6082bd08]; legacy #1128 [8f1d6082bd08]; workbench #1270 [8f1d6082bd08] |  |
| d64799d0654e4447 | en | admin.translation_list.review_translation_keys_missing_values_native_strings_dynamic_calls_and_audit_st | file #lang/en/admin.php [13b827bc457e]; legacy #1110 [13b827bc457e]; workbench #1271 [13b827bc457e] |  |
| f3cbbe8f09dfe71f | en | admin.translation_list.suggested_key_already_applied | file #lang/en/admin.php [1b841aaf92a5]; legacy #1120 [1b841aaf92a5]; workbench #1272 [1b841aaf92a5] |  |
| 0d0e893659723972 | en | admin.translation_list.suggested_key_applied | file #lang/en/admin.php [9fbeb7d88e18]; legacy #1124 [9fbeb7d88e18]; workbench #1273 [9fbeb7d88e18] |  |
| 9a0a9c76f3b7bc48 | en | admin.translation_list.table.current_status_of_the_translation_key_useful_for_identification_and_reference | file #lang/en/admin.php [343d7abf9f2b]; legacy #3525 [343d7abf9f2b]; workbench #1274 [343d7abf9f2b] |  |
| 7f992007ecac5064 | en | admin.translation_list.table.differs_from_suggested_key | file #lang/en/admin.php [adefbc4189eb]; legacy #2728 [adefbc4189eb]; workbench #1275 [adefbc4189eb] |  |
| a07f8ae0c1b84c9b | en | admin.translation_list.table.history | file #lang/en/admin.php [0e7696009337]; legacy #5877 [0e7696009337]; workbench #1276 [0e7696009337] |  |
| a46be553e3f6379e | en | admin.translation_list.table.internal_database_id_of_the_translation_key | file #lang/en/admin.php [1d75ae410fb2]; legacy #2738 [1d75ae410fb2]; workbench #1277 [1d75ae410fb2] |  |
| 3574ae02cdf85c68 | en | admin.translation_list.table.key | file #lang/en/admin.php [99a52df3ff3d]; legacy #2730 [99a52df3ff3d]; workbench #1278 [99a52df3ff3d] |  |
| 2d4853df194a54fe | en | admin.translation_list.table.key_suggested_key | file #lang/en/admin.php [8f5987c8f318]; legacy #5535 [8f5987c8f318]; workbench #1279 [8f5987c8f318] |  |
| 141144438c54b4c0 | en | admin.translation_list.table.language_translations | file #lang/en/admin.php [efe911070ae0]; legacy #3549 [efe911070ae0]; workbench #1280 [efe911070ae0] |  |
| 5b6bd3fc15c53feb | en | admin.translation_list.table.last_seen | file #lang/en/admin.php [21fd79c7de52]; legacy #6113 [21fd79c7de52]; workbench #1281 [21fd79c7de52] |  |
| 2321ce8c756f0fb7 | en | admin.translation_list.table.mark_reviewed | file #lang/en/admin.php [f73cf2143127]; legacy #2736 [f73cf2143127]; workbench #1282 [f73cf2143127] |  |
| 5ca3ccb9a0a39b64 | en | admin.translation_list.table.no_history_entries_available | file #lang/en/admin.php [399bbc3c2cb9]; legacy #5793 [399bbc3c2cb9] |  |
| 9d8baa1e18320ece | en | admin.translation_list.table.no_translation_records_found | file #lang/en/admin.php [0848b99f8187]; legacy #6104 [0848b99f8187]; workbench #1284 [0848b99f8187] |  |
| 999e1c4f7bc04b71 | en | admin.translation_list.table.obsolete_key | file #lang/en/admin.php [f0a1ff7759d9]; legacy #2732 [f0a1ff7759d9]; workbench #1285 [f0a1ff7759d9] |  |
| 49c8638f200c3875 | en | admin.translation_list.table.open_history | file #lang/en/admin.php [d5e6f0ff5a12]; legacy #5784 [d5e6f0ff5a12] |  |
| f44f5b38bb2d2f92 | en | admin.translation_list.table.open_the_translation_key_review_modal | file #lang/en/admin.php [25d1459e6a82]; legacy #2726 [25d1459e6a82]; workbench #1287 [25d1459e6a82] |  |
| 19c91e38a19d9856 | en | admin.translation_list.table.original_text_in_the_source_language_useful_for_identification_and_reference | file #lang/en/admin.php [aebfb142005a]; legacy #5899 [aebfb142005a]; workbench #1288 [aebfb142005a] |  |
| dafa0fa25eee1f87 | en | admin.translation_list.table.refresh_the_translation_list_to_see_the_latest_changes | file #lang/en/admin.php [88ade64cac70]; legacy #3555 [88ade64cac70]; workbench #1289 [88ade64cac70] |  |
| b0e748efb5d2c72d | en | admin.translation_list.table.reload_translation_list | file #lang/en/admin.php [c6c884c0153b]; legacy #3521 [c6c884c0153b]; workbench #1290 [c6c884c0153b] |  |
| 7475444ba376cf57 | en | admin.translation_list.table.review_and_manage_translation_keys_their_values_across_languages_and_associated_ | file #lang/en/admin.php [8986f0c4a86b]; legacy #2537 [8986f0c4a86b]; workbench #1291 [8986f0c4a86b] |  |
| 95c551f00b129d45 | en | admin.translation_list.table.review_translation_values_for_the_language_language_including_its_sub_languages_ | file #lang/en/admin.php [46cf33688503]; legacy #3553 [46cf33688503]; workbench #1292 [46cf33688503] |  |
| 738125e879a53867 | en | admin.translation_list.table.source_language_value_en_native_text | file #lang/en/admin.php [e7078e923758]; legacy #5436 [e7078e923758]; workbench #1293 [e7078e923758] |  |
| bb15199645dd18b8 | en | admin.translation_list.table.suggested_key | file #lang/en/admin.php [91b64f5d3833]; legacy #6198 [91b64f5d3833]; workbench #1294 [91b64f5d3833] |  |
| ff1cf04f49f5561b | en | admin.translation_list.table.target_language_values | file #lang/en/admin.php [5e3248faa264]; legacy #5873 [5e3248faa264]; workbench #1295 [5e3248faa264] |  |
| 7308eb23cc91691d | en | admin.translation_list.table.target_sub_language_values | file #lang/en/admin.php [c9ccc3834c19]; legacy #5901 [c9ccc3834c19]; workbench #1296 [c9ccc3834c19] |  |
| 38a247ab06f80741 | en | admin.translation_list.table.this_key_is_currently_not_found_in_code_usage_it_can_indicate_a_legacy_export_mi | file #lang/en/admin.php [0b41d8c281fa]; legacy #2734 [0b41d8c281fa]; workbench #1297 [0b41d8c281fa] |  |
| 9814e8b8e0c26967 | en | admin.translation_list.table.translated_values_for_the_key_across_different_main_languages_and_their_respecti | file #lang/en/admin.php [98a781bb2cb8]; legacy #5643 [98a781bb2cb8]; workbench #1298 [98a781bb2cb8] |  |
| d76b5d98261a6bc7 | en | admin.translation_list.table.translation_key_or_suggested_key_useful_for_identification_and_reference | file #lang/en/admin.php [103abb41cb5e]; legacy #5434 [103abb41cb5e]; workbench #1299 [103abb41cb5e] |  |
| 247ea7fbada2b6f8 | en | admin.translation_list.table.translation_list | file #lang/en/admin.php [28eae24c52fd]; legacy #2549 [28eae24c52fd]; workbench #1300 [28eae24c52fd] |  |
| 5f389df4352161e6 | en | admin.translation_list.table.usage | file #lang/en/admin.php [8d59829c1e15]; legacy #2659 [8d59829c1e15] |  |
| cc92d8c7963f67e0 | en | admin.translation_list.table.usage_information_of_the_translation_key_useful_for_identification_and_reference | file #lang/en/admin.php [8e23da02302f]; legacy #2724 [8e23da02302f]; workbench #1302 [8e23da02302f] |  |
| 266e4a59976dec80 | en | admin.translation_list.table.usage_s | file #lang/en/admin.php [2d0e1f8fe8b2]; legacy #5875 [2d0e1f8fe8b2]; workbench #1303 [2d0e1f8fe8b2] |  |
| 1f2d88a45d6f6d14 | en | admin.translation_list.the_first_language_variation_value_was_copied_to_all_visible_language_variations | file #lang/en/admin.php [3aa1db736dc4]; legacy #6044 [3aa1db736dc4]; workbench #1304 [3aa1db736dc4] |  |
| b2d8fb64e51ed24e | en | admin.translation_list.the_obsolete_entry_has_been_marked_as_reviewed_and_leaves_the_default_workflow_l | file #lang/en/admin.php [facabbcbfa7f]; legacy #6036 [facabbcbfa7f]; workbench #1305 [facabbcbfa7f] |  |
| 50127a741ca08859 | en | admin.translation_list.the_suggested_key_has_been_copied_to_the_translation_key | file #lang/en/admin.php [66d056cf31c5]; legacy #1126 [66d056cf31c5]; workbench #1306 [66d056cf31c5] |  |
| ca8b842661980b5d | en | admin.translation_list.the_translation_key_already_matches_the_suggested_key | file #lang/en/admin.php [5d6008a7cff3]; legacy #1122 [5d6008a7cff3]; workbench #1307 [5d6008a7cff3] |  |
| 8887c86ad233e008 | en | admin.translation_list.the_translation_values_have_been_saved_successfully | file #lang/en/admin.php [3f0f5b4ad598]; legacy #1114 [3f0f5b4ad598]; workbench #1308 [3f0f5b4ad598] |  |
| 10cf3b8ed4f0177d | en | admin.translation_list.there_is_no_suggested_key_to_apply_for_this_entry | file #lang/en/admin.php [215bdcd3e9df]; legacy #1118 [215bdcd3e9df]; workbench #1309 [215bdcd3e9df] |  |
| 0ebfba573edc91b2 | en | admin.translation_list.this_obsolete_entry_is_already_marked_as_reviewed | file #lang/en/admin.php [9fc329125fed]; legacy #6034 [9fc329125fed]; workbench #1310 [9fc329125fed] |  |
| 1e8c9a5aa1f92138 | en | admin.translation_list.translation_key_missing | file #lang/en/admin.php [eb27929fb7e5]; workbench #1609 [eb27929fb7e5] |  |
| 49aa2ff092687a0b | en | admin.translation_list.translation_management | file #lang/en/admin.php [cd7e883c6e12]; legacy #1108 [cd7e883c6e12]; workbench #1311 [cd7e883c6e12] |  |
| 89a3935cfc7957db | en | admin.translation_list.translation_values_saved | file #lang/en/admin.php [7380e1839781]; legacy #1112 [7380e1839781]; workbench #1312 [7380e1839781] |  |
| aeecda062595232f | en | admin.translation_list.unsaved_translation_value_changes_have_been_discarded | file #lang/en/admin.php [bcaca11011c4]; legacy #6221 [bcaca11011c4]; workbench #1313 [bcaca11011c4] |  |
| f573c12640d117b4 | en | admin.user_list.filter.without_role | file #lang/en/admin.php [0aeb7ebe5ea5]; legacy #1350 [0aeb7ebe5ea5]; workbench #1314 [0aeb7ebe5ea5] |  |
| cd0869a782fa7d55 | en | admin.user_list.manage_your_system_s_users_assign_roles_and_manage_permissions | file #lang/en/admin.php [6c2da68ffe96]; legacy #1209 [6c2da68ffe96]; workbench #1315 [6c2da68ffe96] |  |
| 1763a5090f52d8d5 | en | admin.user_list.meta.assignable_roles | file #lang/en/admin.php [1411cbacf3c7]; legacy #1371 [1411cbacf3c7] |  |
| 47dccb20a9547a7c | en | admin.user_list.meta.assignable_roles_grouped_by_role_category | file #lang/en/admin.php [2da26acc8cae]; legacy #1183 [2da26acc8cae]; workbench #1317 [2da26acc8cae] |  |
| a2f46e53e3965ec4 | en | admin.user_list.meta.assigned_users | file #lang/en/admin.php [0ad1e44cdf83]; legacy #1185 [0ad1e44cdf83] |  |
| 3501513a6b73c1eb | en | admin.user_list.meta.no_system_roles_available | file #lang/en/admin.php [c33232d31375]; legacy #1195 [c33232d31375]; workbench #1319 [c33232d31375] |  |
| 3d594562bb585e88 | en | admin.user_list.meta.no_user_roles_available | file #lang/en/admin.php [de32cd7cc458]; legacy #1381 [de32cd7cc458]; workbench #1320 [de32cd7cc458] |  |
| 6096d24bd31eb758 | en | admin.user_list.meta.number_of_users_assigned_to_each_system_role | file #lang/en/admin.php [ae4dbf431028]; legacy #1265 [ae4dbf431028]; workbench #1321 [ae4dbf431028] |  |
| e2b6c50fcb21371a | en | admin.user_list.meta.number_of_users_assigned_to_each_user_role | file #lang/en/admin.php [598ced3fee7d]; legacy #1191 [598ced3fee7d]; workbench #1322 [598ced3fee7d] |  |
| 0d43aad51777fa63 | en | admin.user_list.meta.overview | file #lang/en/admin.php [d4b1ea5708dd]; legacy #1342 [d4b1ea5708dd] |  |
| e08f3c1af9a94b60 | en | admin.user_list.meta.summary_of_users_in_the_system_their_assigned_roles_and_role_categories | file #lang/en/admin.php [d82c8d469e28]; legacy #1352 [d82c8d469e28]; workbench #1324 [d82c8d469e28] |  |
| 8c244fd6cba9ceab | en | admin.user_list.meta.system | file #lang/en/admin.php [6725e7bbcd28]; legacy #1369 [6725e7bbcd28] |  |
| d824675a7610af4e | en | admin.user_list.meta.system_roles_users | file #lang/en/admin.php [19905146bc7a]; legacy #1379 [19905146bc7a]; workbench #1326 [19905146bc7a] |  |
| 601dfe24b7b7d457 | en | admin.user_list.meta.the_number_of_users_without_assigned_roles | file #lang/en/admin.php [e8d063c272af]; legacy #1187 [e8d063c272af]; workbench #1327 [e8d063c272af] |  |
| 1411ef17f986336d | en | admin.user_list.meta.the_total_number_of_registered_users_in_the_system | file #lang/en/admin.php [e0778da4cb90]; legacy #1365 [e0778da4cb90]; workbench #1328 [e0778da4cb90] |  |
| 1d3fa1c31eedf5c5 | en | admin.user_list.meta.total_users | file #lang/en/admin.php [a002e72ab6bc]; legacy #1360 [a002e72ab6bc]; workbench #1329 [a002e72ab6bc] |  |
| 6d6293a0e0177621 | en | admin.user_list.meta.user | file #lang/en/admin.php [b512d97e7cbf]; legacy #1322 [b512d97e7cbf]; workbench #1330 [b512d97e7cbf] |  |
| 0c72a9f7b17b48ac | en | admin.user_list.meta.user_roles_users | file #lang/en/admin.php [de33598eac1f]; legacy #1193 [de33598eac1f]; workbench #1331 [de33598eac1f] |  |
| 464a329ed0b97458 | en | admin.user_list.meta.users_grouped_by_assigned_role_category | file #lang/en/admin.php [99732578f703]; legacy #1320 [99732578f703]; workbench #1332 [99732578f703] |  |
| dcd8330db8d32580 | en | admin.user_list.meta.users_without_roles | file #lang/en/admin.php [992d13e28e93]; legacy #1367 [992d13e28e93]; workbench #1333 [992d13e28e93] |  |
| eb6cf1d8fb7ec7b9 | en | admin.user_list.modal.change_assigned_roles_for | file #lang/en/admin.php [6b7a9c0aa58f]; legacy #1385 [6b7a9c0aa58f]; workbench #1334 [6b7a9c0aa58f] |  |
| 3d824bcf6aed5b1a | en | admin.user_list.modal.change_assigned_roles_for_the_selected_user | file #lang/en/admin.php [bd78c7c4ef90]; legacy #1383 [bd78c7c4ef90]; workbench #1335 [bd78c7c4ef90] |  |
| 66ea60848f3d38dc | en | admin.user_list.modal.change_role | file #lang/en/admin.php [05c50790b5f8]; legacy #1377 [05c50790b5f8]; workbench #1336 [05c50790b5f8] |  |
| f7cef95375191c63 | en | admin.user_list.modal.edit_roles | file #lang/en/admin.php [b547bd6717a6]; legacy #1263 [b547bd6717a6]; workbench #1337 [b547bd6717a6] |  |
| a756cb4a3285828e | en | admin.user_list.modal.roles | file #lang/en/admin.php [c25337055464]; legacy #1375 [c25337055464] |  |
| 3fdae4c4d02b75e3 | en | admin.user_list.modal.without_role | file #lang/en/admin.php [0aeb7ebe5ea5]; legacy #1373 [0aeb7ebe5ea5] |  |
| 6686f7944b9ecc97 | en | admin.user_list.please_select_a_role | file #lang/en/admin.php [acd8e30577ed]; legacy #1211 [acd8e30577ed]; workbench #1340 [acd8e30577ed] |  |
| f47a4370248d0515 | en | admin.user_list.please_select_a_valid_role | file #lang/en/admin.php [6683b7c0b252]; legacy #1213 [6683b7c0b252]; workbench #1341 [6683b7c0b252] |  |
| 46edba94eda63912 | en | admin.user_list.table.actions | file #lang/en/admin.php [ff8059dc6752]; legacy #1197 [ff8059dc6752] |  |
| 1f8b9449427e7c39 | en | admin.user_list.table.e_mail | file #lang/en/admin.php [2fae6fb30b0d]; legacy #1391 [2fae6fb30b0d]; workbench #1343 [2fae6fb30b0d] |  |
| ce8340170ad2b08d | en | admin.user_list.table.edit_roles | file #lang/en/admin.php [b547bd6717a6]; legacy #1203 [b547bd6717a6] |  |
| 760ea3fecf6f72af | en | admin.user_list.table.full_name_of_the_user_useful_for_identification | file #lang/en/admin.php [880e9c9ae2e8]; legacy #1201 [880e9c9ae2e8]; workbench #1345 [880e9c9ae2e8] |  |
| 262ff6981488023a | en | admin.user_list.table.id | file #lang/en/admin.php [3843971dcfde]; legacy #1387 [3843971dcfde]; workbench #1346 [3843971dcfde] |  |
| 6d062ed0069c2f26 | en | admin.user_list.table.name | file #lang/en/admin.php [dcd1d5223f73]; legacy #1389 [dcd1d5223f73] |  |
| 3e4925c1e228da40 | en | admin.user_list.table.overview_of_all_users_in_the_system_their_email_addresses_and_assigned_roles | file #lang/en/admin.php [47b8cda0025a]; legacy #1269 [47b8cda0025a] |  |
| fc0f86dc4f17915e | en | admin.user_list.table.roles | file #lang/en/admin.php [c25337055464]; legacy #1189 [c25337055464] |  |
| 46231eb3d9474a89 | en | admin.user_list.table.roles_assigned_to_the_user_determining_their_permissions_and_access_levels | file #lang/en/admin.php [ac182a732266]; legacy #1205 [ac182a732266]; workbench #1350 [ac182a732266] |  |
| c80b209663ec3c0b | en | admin.user_list.table.unique_identifier_of_the_user_useful_for_tracking_and_reference | file #lang/en/admin.php [f314939be44d]; legacy #1267 [f314939be44d]; workbench #1351 [f314939be44d] |  |
| b0015d8ba647845f | en | admin.user_list.table.user_list | file #lang/en/admin.php [b08c0af3db5b]; legacy #1199 [b08c0af3db5b]; workbench #1352 [b08c0af3db5b] |  |
| 92ca56247f8ab5e4 | en | admin.user_list.table.without_role | file #lang/en/admin.php [0aeb7ebe5ea5]; legacy #1393 [0aeb7ebe5ea5] |  |
| 2b78ea2ba422c340 | en | admin.user_list.the_role_for_user_has_been_changed_to_role | file #lang/en/admin.php [621b10bdb1b1]; legacy #1173 [621b10bdb1b1]; workbench #1354 [621b10bdb1b1] |  |
| 96926b29a590dc0c | en | admin.user_list.user_role_management | file #lang/en/admin.php [6057ead29594]; legacy #1207 [6057ead29594]; workbench #1355 [6057ead29594] |  |
| df377c92b28c3d40 | en | admin.user_list.user_role_updated | file #lang/en/admin.php [c832437569f8]; legacy #1175 [c832437569f8]; workbench #1356 [c832437569f8] |  |
| 2ab7103d08b8bd13 | en | auth.account.already-have-an-account | file #lang/en/auth.php [e77fea936d3e]; legacy #7618 [e77fea936d3e]; workbench #1379 [e77fea936d3e] |  |
| 8407fd9e31ffb315 | en | auth.email.a-new-verification-link-has-been-sent-to-the-email-address-you-provided-during-registration | file #lang/en/auth.php [bcd99ebd63a9]; legacy #7627 [bcd99ebd63a9]; workbench #1993 [bcd99ebd63a9] |  |
| 7224b4b8a6be9bbf | en | dynamic.ui.button.show_hide.hide_label | file #lang/en/dynamic.php [ac20a57bfde0]; legacy #6786 [ac20a57bfde0] |  |
| b7f4fcd668393d6f | en | dynamic.ui.button.show_hide.show_label | file #lang/en/dynamic.php [0df6f1cad36c]; legacy #6788 [0df6f1cad36c] |  |
| 9fd1040eb238d038 | en | laravel_translation_workbench.resources.views.livewire.raw_data.translation_workbench_raw_data | file #lang/en/laravel_translation_workbench.php [30a5548697e0]; legacy #6798 [30a5548697e0] |  |
| ceb0b4265e4ad371 | en | layouts.header.search | file #lang/en/layouts.php [49c266baaaa7]; legacy #935 [49c266baaaa7] |  |
| 437eb30629671a46 | en | layouts.sidebar.administration.countries | file #lang/en/layouts.php [8faf7ec7ab20]; legacy #930 [8faf7ec7ab20]; workbench #1370 [8faf7ec7ab20] |  |
| edf110aa38e40169 | en | layouts.sidebar.administration.fallback_reports | file #lang/en/layouts.php [a1621a8da718]; legacy #382 [a1621a8da718]; workbench #1371 [a1621a8da718] |  |
| f890b0bd6ef07af8 | en | layouts.sidebar.management.client | file #lang/en/layouts.php [0c77fe09ab33]; legacy #940 [0c77fe09ab33]; workbench #1372 [0c77fe09ab33] |  |
| 2647ecc35d60ccec | en | layouts.sidebar.management.create | file #lang/en/layouts.php [4759498ac2a7]; legacy #945 [4759498ac2a7] |  |
| f86777a69f55183d | en | livewire.admin.pages.settings.two_factor.recovery_codes.2fa_recovery_codes | file #lang/en/livewire.php [fc122e7ada76]; legacy #6862 [fc122e7ada76]; workbench #1556 [fc122e7ada76] |  |
| f7c7426737179515 | en | management.people.create_person.form_person.document_type_options.options.document | dynamic_workbench #155 [d6bd8c0aeee8]; file #lang/en/management.php [d6bd8c0aeee8] |  |
| a9b49994efd35734 | en | management.people.create_person.form_person.document_type_options.options.health_insurance_proof | dynamic_workbench #157 [d392e1bdd410]; file #lang/en/management.php [d392e1bdd410] |  |
| f9a64b7c35db61fb | en | management.people.create_person.form_person.document_type_options.options.id_card_copy | dynamic_workbench #159 [d21b1cba1386]; file #lang/en/management.php [d21b1cba1386] |  |
| 6dc73b2c3bf616bc | en | management.people.create_person.form_person.document_type_options.options.invoice | dynamic_workbench #161 [b832002da297]; file #lang/en/management.php [b832002da297] |  |
| 776a4e6cbbd608b4 | en | management.people.create_person.form_person.document_type_options.options.it_document | dynamic_workbench #153 [9c181018d470]; file #lang/en/management.php [9c181018d470] |  |
| 5c411267dd7dbf1d | en | management.people.create_person.form_person.document_type_options.options.other | dynamic_workbench #164 [f97e9da0e3b8]; file #lang/en/management.php [f97e9da0e3b8] |  |
| 327af0547b6644ad | en | management.people.create_person.form_person.document_type_options.options.passport_copy | dynamic_workbench #166 [80538065e57b]; file #lang/en/management.php [80538065e57b] |  |
| 108c1ddff80efb1d | en | management.people.create_person.form_person.document_type_options.options.receipt | dynamic_workbench #168 [dad5a9692313]; file #lang/en/management.php [dad5a9692313] |  |
| 09bc912468dcc5a2 | en | management.people.create_person.form_person.document_type_options.options.residence_permit_copy | dynamic_workbench #170 [b2c6a507fc64]; file #lang/en/management.php [b2c6a507fc64] |  |
| 51f8bb4dd8b46116 | en | management.people.create_person.form_person.document_type_options.options.tax_document | dynamic_workbench #172 [04762cdf2b75]; file #lang/en/management.php [04762cdf2b75] |  |
| 15a367d38c286e1b | en | management.people.create_person.form_person.document_type_options.options.wikipedia_pdf_dokument | dynamic_workbench #154 [91dd922ade93]; file #lang/en/management.php [91dd922ade93] |  |
| b2e95a2bfd805e4c | en | management.people.create_person.form_person.emergency | file #lang/en/management.php [4538f2dc67b5]; legacy #362 [4538f2dc67b5] |  |
| a11c247476808e13 | en | management.people.create_person.phone | file #lang/en/management.php [63dceb8800b2]; workbench #1603 [63dceb8800b2] |  |
| a0983bad71a0b8a9 | en | management.people.create_person.sections.address.country | file #lang/en/management.php [701d021d08c5]; legacy #6723 [701d021d08c5]; workbench #1375 [701d021d08c5] |  |
| 5d1b2be5a8d088fa | en | management.people.create_person.sections.documents.document_type_options.options.document | dynamic_workbench #131 [d6bd8c0aeee8]; file #lang/en/management.php [d6bd8c0aeee8] |  |
| 66f25b9e01d5a44e | en | management.people.create_person.sections.documents.document_type_options.options.health_insurance_proof | dynamic_workbench #133 [d392e1bdd410]; file #lang/en/management.php [d392e1bdd410] |  |
| 3eeb299b03097665 | en | management.people.create_person.sections.documents.document_type_options.options.id_card_copy | dynamic_workbench #135 [d21b1cba1386]; file #lang/en/management.php [d21b1cba1386] |  |
| e024d5fb22f2c29f | en | management.people.create_person.sections.documents.document_type_options.options.invoice | dynamic_workbench #137 [b832002da297]; file #lang/en/management.php [b832002da297] |  |
| 52be768a6d3166bc | en | management.people.create_person.sections.documents.document_type_options.options.it_document | dynamic_workbench #139 [c93971020342]; file #lang/en/management.php [c93971020342] |  |
| 9e23c647c232e4c9 | en | management.people.create_person.sections.documents.document_type_options.options.other | dynamic_workbench #141 [f97e9da0e3b8]; file #lang/en/management.php [f97e9da0e3b8] |  |
| db0d8120e28ca959 | en | management.people.create_person.sections.documents.document_type_options.options.passport_copy | dynamic_workbench #143 [80538065e57b]; file #lang/en/management.php [80538065e57b] |  |
| f7d6050845a95083 | en | management.people.create_person.sections.documents.document_type_options.options.receipt | dynamic_workbench #145 [dad5a9692313]; file #lang/en/management.php [dad5a9692313] |  |
| 2e8dbdb4bef7f1a1 | en | management.people.create_person.sections.documents.document_type_options.options.residence_permit_copy | dynamic_workbench #147 [b2c6a507fc64]; file #lang/en/management.php [b2c6a507fc64] |  |
| d0e3a587cd1dffe1 | en | management.people.create_person.sections.documents.document_type_options.options.tax_document | dynamic_workbench #149 [04762cdf2b75]; file #lang/en/management.php [04762cdf2b75] |  |
| 07df9ad1082001ea | en | management.people.create_person.sections.documents.document_type_options.options.wikipedia_pdf_dokument | dynamic_workbench #151 [91dd922ade93]; file #lang/en/management.php [91dd922ade93] |  |
| 76e3abf9b05a3a44 | en | management.people.create_person.sections.documents.please_enter_the_issuing_authority_for_the_document_this_is_important_for_correc | file #lang/en/management.php [cd555243a867]; legacy #6732 [cd555243a867]; workbench #1376 [cd555243a867] |  |
| eca850c0de6f0f43 | en | management.people.create_person.sections.emergency_contact.emergency_contact_relationship_options.options.caregiver | dynamic_workbench #1 [b45607f244d2]; file #lang/en/management.php [b45607f244d2] |  |
| a80d5550171ffbe3 | en | management.people.create_person.sections.emergency_contact.emergency_contact_relationship_options.options.child | dynamic_workbench #3 [8053321281f5]; file #lang/en/management.php [8053321281f5] |  |
| 7dfb292e7124ba43 | en | management.people.create_person.sections.emergency_contact.emergency_contact_relationship_options.options.friend | dynamic_workbench #5 [acd8f6644016]; file #lang/en/management.php [acd8f6644016] |  |
| e634d91f94537932 | en | management.people.create_person.sections.emergency_contact.emergency_contact_relationship_options.options.guardian | dynamic_workbench #7 [1d228f258f49]; file #lang/en/management.php [1d228f258f49] |  |
| 5812f31bb316d94b | en | management.people.create_person.sections.emergency_contact.emergency_contact_relationship_options.options.other | dynamic_workbench #9 [f97e9da0e3b8]; file #lang/en/management.php [f97e9da0e3b8] |  |
| 5a44f670d1d2923f | en | management.people.create_person.sections.emergency_contact.emergency_contact_relationship_options.options.parent | dynamic_workbench #11 [5f7953f7c9b6]; file #lang/en/management.php [5f7953f7c9b6] |  |
| 2c5ca0feaa32c78b | en | management.people.create_person.sections.emergency_contact.emergency_contact_relationship_options.options.partner | dynamic_workbench #13 [29035e784f93]; file #lang/en/management.php [29035e784f93] |  |
| f64bd7af3bc3c68c | en | management.people.create_person.sections.emergency_contact.emergency_contact_relationship_options.options.sibling | dynamic_workbench #15 [6c09e23e4807]; file #lang/en/management.php [6c09e23e4807] |  |
| 58f5ad1553f6d6f0 | en | management.people.create_person.sections.emergency_contact.emergency_contact_relationship_options.options.spouse | dynamic_workbench #17 [f8930c3632d9]; file #lang/en/management.php [f8930c3632d9] |  |
| db8966f3eae69cc4 | en | management.people.create_person.sections.person_core.gender_options.options.diverse | dynamic_workbench #193 [331a5642a7fd]; file #lang/en/management.php [331a5642a7fd] |  |
| 0935c1a2f0bbe099 | en | management.people.create_person.sections.person_core.gender_options.options.female | dynamic_workbench #195 [e8cca808ae5a]; file #lang/en/management.php [e8cca808ae5a] |  |
| e2ee73c7b79c5069 | en | management.people.create_person.sections.person_core.gender_options.options.male | dynamic_workbench #197 [03f8c1273e3d]; file #lang/en/management.php [03f8c1273e3d] |  |
| 9c9daa77ade203ce | en | management.people.create_person.sections.person_core.gender_options.options.unknown | dynamic_workbench #199 [b764cdc0eab7]; file #lang/en/management.php [b764cdc0eab7] |  |
| 3bd135bed0f7f3e7 | en | management.people.create_person.sections.person_core.marital_status_options.options.divorced | dynamic_workbench #69 [f08a00d39e28]; file #lang/en/management.php [f08a00d39e28] |  |
| b9c72a2121d9687a | en | management.people.create_person.sections.person_core.marital_status_options.options.married | dynamic_workbench #71 [64e113497d29]; file #lang/en/management.php [64e113497d29] |  |
| f90e241820293416 | en | management.people.create_person.sections.person_core.marital_status_options.options.registered_partnership | dynamic_workbench #73 [3e808a7ffe6b]; file #lang/en/management.php [3e808a7ffe6b] |  |
| ce295465c6191bc5 | en | management.people.create_person.sections.person_core.marital_status_options.options.separated | dynamic_workbench #75 [90be961b0645]; file #lang/en/management.php [90be961b0645] |  |
| c0e3f5f3e50b4f74 | en | management.people.create_person.sections.person_core.marital_status_options.options.single | dynamic_workbench #77 [8888a029aaf6]; file #lang/en/management.php [8888a029aaf6] |  |
| ad6a988ce2b6b2c8 | en | management.people.create_person.sections.person_core.marital_status_options.options.unknown | dynamic_workbench #79 [b764cdc0eab7]; file #lang/en/management.php [b764cdc0eab7] |  |
| ebd0a33c554da67f | en | management.people.create_person.sections.person_core.marital_status_options.options.widowed | dynamic_workbench #81 [0e3434cbbbdc]; file #lang/en/management.php [0e3434cbbbdc] |  |
| d209d3a25715845f | en | management.people.create_person.sections.person_core.salutation_options.options.mr | dynamic_workbench #63 [1a24b7688c19]; file #lang/en/management.php [1a24b7688c19] |  |
| 66a95143825696b2 | en | management.people.create_person.sections.person_core.salutation_options.options.mrs | dynamic_workbench #65 [26b341ee55d5]; file #lang/en/management.php [26b341ee55d5] |  |
| 200d8a43dfe29198 | en | management.people.create_person.sections.person_core.salutation_options.options.mx | dynamic_workbench #67 [d3903c926c0b]; file #lang/en/management.php [d3903c926c0b] |  |
| f6cd4e7d5898671b | en | management.people.edit_person.sections.documents.document_category_options.options.correspondence | dynamic_workbench #83 [dcdd73c5c353]; file #lang/en/management.php [dcdd73c5c353] |  |
| 81a95d1e6e05ce54 | en | management.people.edit_person.sections.documents.document_category_options.options.documentation | dynamic_workbench #85 [c205924de0fe]; file #lang/en/management.php [c205924de0fe] |  |
| b86b60d60cf14b48 | en | management.people.edit_person.sections.documents.document_category_options.options.identity | dynamic_workbench #87 [999f23fcd7be]; file #lang/en/management.php [999f23fcd7be] |  |
| 0b78bca38f320e92 | en | management.people.edit_person.sections.documents.document_category_options.options.insurance | dynamic_workbench #89 [4594b737568a]; file #lang/en/management.php [4594b737568a] |  |
| 9c1b7353ac242e7d | en | management.people.edit_person.sections.documents.document_category_options.options.medical | dynamic_workbench #91 [fec80bef0d8a]; file #lang/en/management.php [fec80bef0d8a] |  |
| 0fb95b1532e08a34 | en | management.people.edit_person.sections.documents.document_category_options.options.other | dynamic_workbench #93 [f97e9da0e3b8]; file #lang/en/management.php [f97e9da0e3b8] |  |
| 8fe76264378b8d0a | en | management.people.edit_person.sections.documents.document_category_options.options.residence | dynamic_workbench #95 [a2b57adda68b]; file #lang/en/management.php [a2b57adda68b] |  |
| 9e83aa543d6f4be5 | en | management.people.edit_person.sections.documents.document_category_options.options.tax | dynamic_workbench #97 [47e2886ee5d3]; file #lang/en/management.php [47e2886ee5d3] |  |
| f277d735b09c6237 | en | management.people.edit_person.sections.emergency_contact.emergency_contact_relationship_options.options.caregiver | dynamic_workbench #175 [b45607f244d2]; file #lang/en/management.php [b45607f244d2] |  |
| d0ad8861aa42a805 | en | management.people.edit_person.sections.emergency_contact.emergency_contact_relationship_options.options.child | dynamic_workbench #177 [8053321281f5]; file #lang/en/management.php [8053321281f5] |  |
| a3ede6941b00be7f | en | management.people.edit_person.sections.emergency_contact.emergency_contact_relationship_options.options.friend | dynamic_workbench #179 [acd8f6644016]; file #lang/en/management.php [acd8f6644016] |  |
| 67882f5012146396 | en | management.people.edit_person.sections.emergency_contact.emergency_contact_relationship_options.options.guardian | dynamic_workbench #181 [1d228f258f49]; file #lang/en/management.php [1d228f258f49] |  |
| b016244234536cc1 | en | management.people.edit_person.sections.emergency_contact.emergency_contact_relationship_options.options.other | dynamic_workbench #183 [f97e9da0e3b8]; file #lang/en/management.php [f97e9da0e3b8] |  |
| 274af6636d2b04f2 | en | management.people.edit_person.sections.emergency_contact.emergency_contact_relationship_options.options.parent | dynamic_workbench #185 [5f7953f7c9b6]; file #lang/en/management.php [5f7953f7c9b6] |  |
| a0fd62c9eaf93d29 | en | management.people.edit_person.sections.emergency_contact.emergency_contact_relationship_options.options.partner | dynamic_workbench #187 [29035e784f93]; file #lang/en/management.php [29035e784f93] |  |
| c7f1307c19a8a9c6 | en | management.people.edit_person.sections.emergency_contact.emergency_contact_relationship_options.options.sibling | dynamic_workbench #189 [6c09e23e4807]; file #lang/en/management.php [6c09e23e4807] |  |
| f752182bcba09341 | en | management.people.edit_person.sections.emergency_contact.emergency_contact_relationship_options.options.spouse | dynamic_workbench #191 [f8930c3632d9]; file #lang/en/management.php [f8930c3632d9] |  |
| 33d6f8191c43b78e | en | management.people.edit_person.sections.person_core.marital_status_options.options.divorced | dynamic_workbench #99 [f08a00d39e28]; file #lang/en/management.php [f08a00d39e28] |  |
| 00654012ce8ae0d0 | en | management.people.edit_person.sections.person_core.marital_status_options.options.married | dynamic_workbench #101 [64e113497d29]; file #lang/en/management.php [64e113497d29] |  |
| 25989582b347207c | en | management.people.edit_person.sections.person_core.marital_status_options.options.registered_partnership | dynamic_workbench #103 [3e808a7ffe6b]; file #lang/en/management.php [3e808a7ffe6b] |  |
| 01922580769b3f85 | en | management.people.edit_person.sections.person_core.marital_status_options.options.separated | dynamic_workbench #105 [90be961b0645]; file #lang/en/management.php [90be961b0645] |  |
| 2ac65c9859d306d3 | en | management.people.edit_person.sections.person_core.marital_status_options.options.single | dynamic_workbench #107 [8888a029aaf6]; file #lang/en/management.php [8888a029aaf6] |  |
| 64963a4fc3fad2fb | en | management.people.edit_person.sections.person_core.marital_status_options.options.unknown | dynamic_workbench #109 [b764cdc0eab7]; file #lang/en/management.php [b764cdc0eab7] |  |
| 88d2212895e36c27 | en | management.people.edit_person.sections.person_core.marital_status_options.options.widowed | dynamic_workbench #111 [0e3434cbbbdc]; file #lang/en/management.php [0e3434cbbbdc] |  |
| d62afa683becc5b0 | en | packages.gunreip.laravel_translation_workbench.resources.bulk.entries.modal_edit_dynamic_multi.accept_all_matching_source_and_target_values | file #lang/en/packages.php [f700f7e79a3f]; workbench #1669 [f700f7e79a3f] |  |
| 869a674d31f2f213 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.compact_read_only_foundation_dashboard_for_scanner_data_keys_lang_files_and_coverage | file #lang/en/packages.php [5a01a0880e50]; workbench #1600 [5a01a0880e50] |  |
| 9812a10c27a1cfdf | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.dynamic | file #lang/en/packages.php [b6ee7b3e8d9c]; legacy #6871 [b6ee7b3e8d9c]; workbench #1613 [b6ee7b3e8d9c] |  |
| 4ea87909c33817cf | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.array_value | file #lang/en/packages.php [c6f66a6ca503]; workbench #2205 [c6f66a6ca503] |  |
| 71f5de5b08b45477 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.cleanup_history | file #lang/en/packages.php [848a15453321]; workbench #2207 [848a15453321] |  |
| 5d4385bc15c956e0 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.derived | file #lang/en/packages.php [10809b749b01]; workbench #2209 [10809b749b01] |  |
| 9c52276f9b9a57ac | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.event | file #lang/en/packages.php [4e1f49a9c8ae]; workbench #2211 [4e1f49a9c8ae] |  |
| 5862ad47d0e3dc4a | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.lang_key | file #lang/en/packages.php [bc025666c6a2]; workbench #2213 [bc025666c6a2] |  |
| 31c111e93fccf515 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.locales | file #lang/en/packages.php [83b2852a3ee1]; workbench #2215 [83b2852a3ee1] |  |
| 2c63773966cae5b3 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.moved | file #lang/en/packages.php [b11c9047f351]; workbench #2217 [b11c9047f351] |  |
| 69917d9d03c67275 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.moved_to | file #lang/en/packages.php [44f8b879ca32]; workbench #2219 [44f8b879ca32] |  |
| be3fe924e2bd2643 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.namespace | file #lang/en/packages.php [c4e4e7abda20]; workbench #2221 [c4e4e7abda20] |  |
| cf9a8b6f3088a5da | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.cleanup_history.outcome | file #lang/en/packages.php [4e80abb5b146]; workbench #2223 [4e80abb5b146] |  |
| 49ef2f3bdc8cdf37 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.already_current | file #lang/en/packages.php [65aadd4328aa]; workbench #1619 [65aadd4328aa] |  |
| fed1de5e3f7fffef | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.apply_ready_plan | file #lang/en/packages.php [89f761e95cb1]; workbench #1623 [89f761e95cb1] |  |
| b39f713bbacfcd5f | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.apply_result_freshness | file #lang/en/packages.php [2f578ef535bb]; workbench #1625 [2f578ef535bb] |  |
| ea5432e4e9fc8de7 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.apply_results | file #lang/en/packages.php [fd065d5556ca]; workbench #1627 [fd065d5556ca] |  |
| 221c39ff34cfd1b6 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.code_update_plan | file #lang/en/packages.php [77fb63cc7911]; workbench #1707 [77fb63cc7911] |  |
| a0b81857c780c78e | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.controlled_write_workflow | file #lang/en/packages.php [f6abfdbfbd86]; workbench #1629 [f6abfdbfbd86] |  |
| c3cc56d9bb8e6967 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.current_expression | file #lang/en/packages.php [70085d01d599]; workbench #1631 [70085d01d599] |  |
| 0655b139b66129e7 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.dry_run | file #lang/en/packages.php [d5da154d9f00]; workbench #1633 [d5da154d9f00] |  |
| 66a60ce9dca6e50f | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.duplicate_expressions | file #lang/en/packages.php [efefa1ab91ee]; workbench #1635 [efefa1ab91ee] |  |
| fc8ad0e446d3ba86 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.duplicates | file #lang/en/packages.php [93c28c39252a]; workbench #1637 [93c28c39252a] |  |
| eb68fffd93422076 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.review_duplicate_expression | file #lang/en/packages.php [660fd2a6eb4f]; workbench #1641 [660fd2a6eb4f] |  |
| fd7f511f590b3be5 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.code_update_plan.stores_how_this_duplicate_expression_should_be_handled_before_code_updates_are_applied | file #lang/en/packages.php [1d7c0f90c8c7]; workbench #1643 [1d7c0f90c8c7] |  |
| eab853535be98ed2 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.table_work_findings.cell_actions.edit_translation_values | file #lang/en/packages.php [66b01fbcdc6a]; workbench #1659 [66b01fbcdc6a] |  |
| 2505d2347fcaf403 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings.table_work_findings.cell_actions.review_finding | file #lang/en/packages.php [9e77b15d6ec0]; workbench #1661 [9e77b15d6ec0] |  |
| 7724d9492eba7b76 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings_table.candidate | file #lang/en/packages.php [b2452d1eba32]; workbench #1699 [b2452d1eba32] |  |
| fdb0815e52db6cc5 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings_table.edit_dynamic_translation_values | file #lang/en/packages.php [c201a667fd9d]; workbench #1657 [c201a667fd9d] |  |
| 7fbc9e5e8e717d5c | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.findings_table.show_timeline | file #lang/en/packages.php [69fa63a0ae85]; workbench #1663 [69fa63a0ae85] |  |
| e59c8d51771aa6b7 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.modal_code_update_conflict_review.store_the_explicit_decision_for_a_duplicate_expression_before_source_code_updates_are_applied | file #lang/en/packages.php [eb12da01496c]; workbench #1645 [eb12da01496c] |  |
| c474adc01efba12e | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.modal_dynamic_review.cardinality | file #lang/en/packages.php [23c088c9f1c8]; workbench #1701 [23c088c9f1c8] |  |
| ccf535111c68e09b | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.modal_dynamic_review.classification | file #lang/en/packages.php [72f049954fdd]; legacy #6880 [72f049954fdd]; workbench #1703 [72f049954fdd] |  |
| d0021cbf2b28c9a0 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.modal_dynamic_review.confidence | file #lang/en/packages.php [6422e3e78f5e]; workbench #1713 [6422e3e78f5e] |  |
| 26fa1b1a9bc35eca | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.review.modal_edit_translation_key.segment_actions_are_disabled_because_the_translation_key_was_edited_manually_or_transformed | file #lang/en/packages.php [3f88cab0d609]; workbench #1655 [3f88cab0d609] |  |
| b85d3e13356587f1 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.review.modal_states.classification_review | file #lang/en/packages.php [683977614ece]; workbench #1705 [683977614ece] |  |
| 8c625af006f13926 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.review.modal_states.translation_key_missing | file #lang/en/packages.php [eb27929fb7e5]; workbench #1639 [eb27929fb7e5] |  |
| 6b5dc3269bcd9e8c | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.show_overview | file #lang/en/packages.php [0784415aa8db]; legacy #6889 [0784415aa8db]; workbench #1542 [0784415aa8db] |  |
| 32bc336172cae933 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.raw_data.filters_event_types.common_filters | file #lang/en/packages.php [124595bcc0c3]; workbench #1711 [124595bcc0c3] |  |
| 28d88dd6b31f5910 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.raw_data.table_card.column | file #lang/en/packages.php [3fdc9a587693]; workbench #1709 [3fdc9a587693] |  |
| a8a7650ee94b9fdc | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.settings.inspect_and_reset_persisted_translation_workbench_ui_settings_for_the_current_user_an_a06c4c2605 | file #lang/en/packages.php [a5009fffea5b]; workbench #2199 [a5009fffea5b] |  |
| c4d0afc5ed819b76 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.settings.reset_all_translation_workbench_ui_settings_for_the_current_user | file #lang/en/packages.php [8a855fa3aa7d]; workbench #2201 [8a855fa3aa7d] |  |
| 2a4956ea222d6092 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.settings.reset_these_settings_for_the_current_user | file #lang/en/packages.php [0d713cefe9cf]; workbench #2203 [0d713cefe9cf] |  |
| febe2cc0a988228d | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.settings.settings_overview | file #lang/en/packages.php [9ae5e8727ae2]; workbench #2197 [9ae5e8727ae2] |  |
| 1abfda9524cfbc18 | en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.settings.user_settings_are_stored_on_the_user_record_export_files_are_diagnostic_snapshots_tha_d71eaffe64 | file #lang/en/packages.php [50e4d02ff079]; workbench #2195 [50e4d02ff079] |  |
| ae758093d111c6c7 | en | pages.auth.confirm_password.confirm_password | file #lang/en/pages.php [5ac265f396a2]; legacy #970 [5ac265f396a2] |  |
| ed6b9d5a808a0f7d | en | pages.auth.register.already_have_an_account | file #lang/en/pages.php [e77fea936d3e]; legacy #1287 [e77fea936d3e] |  |
| fd9db31e9f44aacf | en | pages.auth.register.confirm_password | file #lang/en/pages.php [5ac265f396a2]; legacy #1297 [5ac265f396a2]; workbench #1380 [5ac265f396a2] |  |
| 126009e17e64e5d7 | en | pages.auth.register.create_account | file #lang/en/pages.php [798ca2ce18bf]; legacy #1293 [798ca2ce18bf]; workbench #1381 [798ca2ce18bf] |  |
| 7208bb7816c34244 | en | pages.auth.register.email_address | file #lang/en/pages.php [f2488fd4ef4a]; legacy #1295 [f2488fd4ef4a]; workbench #1382 [f2488fd4ef4a] |  |
| ca0c2c68c7e959ce | en | pages.auth.register.full_name | file #lang/en/pages.php [f13a64ba2fea]; legacy #1285 [f13a64ba2fea]; workbench #1383 [f13a64ba2fea] |  |
| e26f900a9f728991 | en | pages.auth.register.log_in | file #lang/en/pages.php [c189840cf7e2]; legacy #1335 [c189840cf7e2]; workbench #1384 [c189840cf7e2] |  |
| cd766a17606c0352 | en | pages.auth.register.name | file #lang/en/pages.php [dcd1d5223f73]; legacy #1330 [dcd1d5223f73] |  |
| be8d222f8a0f79eb | en | pages.auth.register.password | file #lang/en/pages.php [e7cf3ef4f17c]; legacy #1289 [e7cf3ef4f17c]; workbench #1386 [e7cf3ef4f17c] |  |
| c4d80fe702655abb | en | pages.auth.reset_password.reset_password | file #lang/en/pages.php [e0edfeb3a71a]; legacy #1340 [e0edfeb3a71a]; workbench #1387 [e0edfeb3a71a] |  |
| e84b462f6dbddaf2 | en | pages.settings.appearance.system | file #lang/en/pages.php [6725e7bbcd28]; legacy #1283 [6725e7bbcd28] |  |
| 6f9dbe1238287ea8 | en | pages.settings.profile.avatar_image | file #lang/en/pages.php [195eb84c2dc5]; legacy #989 [195eb84c2dc5]; workbench #1389 [195eb84c2dc5] |  |
| df550ab56c552235 | en | pages.settings.profile.current_avatar | file #lang/en/pages.php [ad406f65c17b]; legacy #987 [ad406f65c17b]; workbench #1390 [ad406f65c17b] |  |
| 09e4e790c01a208d | en | pages.settings.profile.please_check_the_storage_permissions_for_storage_app_public_avatars | file #lang/en/pages.php [78a521dc700e]; legacy #192 [78a521dc700e]; workbench #1391 [78a521dc700e] |  |
| 0509595a0c22564f | en | pages.settings.security.confirm_password | file #lang/en/pages.php [5ac265f396a2]; legacy #999 [5ac265f396a2] |  |
| 04172e8ff3f0ade6 | en | pages.settings.security.disable_2fa | file #lang/en/pages.php [1c3193f3a0b8]; legacy #994 [1c3193f3a0b8]; workbench #1393 [1c3193f3a0b8] |  |
| a337498b0105907a | en | pages.settings.security.enable_2fa | file #lang/en/pages.php [649b5dc5c210]; legacy #1017 [649b5dc5c210]; workbench #1394 [649b5dc5c210] |  |
| 22df206570d16b18 | en | pages.settings.security.manage_your_two_factor_authentication_settings | file #lang/en/pages.php [fc7efb90d3ad]; legacy #1015 [fc7efb90d3ad]; workbench #1395 [fc7efb90d3ad] |  |
| 53b2ce31c9d2cb8f | en | pages.settings.security.save | file #lang/en/pages.php [1509f561f241]; legacy #1001 [1509f561f241] |  |
| 863eebacfcaaea0b | en | pages.settings.security.security_settings | file #lang/en/pages.php [92ceebb5bdb9]; legacy #975 [92ceebb5bdb9]; workbench #1397 [92ceebb5bdb9] |  |
| d62bf56e412a8074 | en | pages.settings.security.two_factor_authentication | file #lang/en/pages.php [b6824cc8a91e]; legacy #1003 [b6824cc8a91e]; workbench #1398 [b6824cc8a91e] |  |
| 4215cded2fea3e20 | en | pages.settings.security.update_password | file #lang/en/pages.php [fe45b4014135]; legacy #1013 [fe45b4014135]; workbench #1399 [fe45b4014135] |  |
| c0843fbac799ac5e | en | pages.settings.two_factor_setup_modal.back | file #lang/en/pages.php [76900f1bfd16]; legacy #963 [76900f1bfd16]; workbench #1400 [76900f1bfd16] |  |
| 45448a1931a410b2 | en | pages.settings.two_factor_setup_modal.close | file #lang/en/pages.php [7d9eb7acb13e]; legacy #1021 [7d9eb7acb13e] |  |
| ae7779f9eccf9584 | en | pages.settings.two_factor_setup_modal.confirm | file #lang/en/pages.php [eebdd24a77d9]; legacy #980 [eebdd24a77d9] |  |
| bf0de33b52c3bbb2 | en | pages.settings.two_factor_setup_modal.continue | file #lang/en/pages.php [31fbef162594]; legacy #1027 [31fbef162594]; workbench #1403 [31fbef162594] |  |
| 9c9836fcc7c5de64 | en | pages.settings.two_factor_setup_modal.enable_two_factor_authentication | file #lang/en/pages.php [57fc14726892]; legacy #1029 [57fc14726892]; workbench #1404 [57fc14726892] |  |
| 181532df484cb32b | en | pages.settings.two_factor_setup_modal.enter_the_6_digit_code_from_your_authenticator_app | file #lang/en/pages.php [535dec144f72]; legacy #1025 [535dec144f72]; workbench #1405 [535dec144f72] |  |
| ee612b2a9e74373f | en | pages.settings.two_factor_setup_modal.to_finish_enabling_two_factor_authentication_scan_the_qr_code_or_enter_the_setup | file #lang/en/pages.php [d516576de855]; legacy #954 [d516576de855]; workbench #1406 [d516576de855] |  |
| b4127e9fe5cbb97d | en | pages.settings.two_factor_setup_modal.two_factor_authentication_enabled | file #lang/en/pages.php [3629a57414b7]; legacy #1019 [3629a57414b7]; workbench #1407 [3629a57414b7] |  |
| a83576f6b7c7faab | en | pages.settings.two_factor_setup_modal.verify_authentication_code | file #lang/en/pages.php [46a7218c4c19]; legacy #1023 [46a7218c4c19]; workbench #1408 [46a7218c4c19] |  |
| 49fe1582d9ef1377 | en | partials.settings_heading.manage_your_profile_and_account_settings | file #lang/en/partials.php [ba730e6730b3]; legacy #6835 [ba730e6730b3]; workbench #1586 [ba730e6730b3] |  |
| 06129f918b2a9ea9 | en | ui.action | file #lang/en/ui.php [64cff1319d2f]; workbench #1558 [64cff1319d2f] | key_requires_nested_array_review |
| f842d3822dd698f6 | en | ui.actions.close | file #lang/en/ui.php [7d9eb7acb13e]; legacy #5660 [7d9eb7acb13e]; workbench #1474 [7d9eb7acb13e] |  |
| 499567c80064e6ee | en | ui.actions.save_changes | file #lang/en/ui.php [35322b5bb5a2]; legacy #194 [35322b5bb5a2]; workbench #1476 [35322b5bb5a2] |  |
| 5213d8b207e7c830 | en | ui.actions.search | file #lang/en/ui.php [49c266baaaa7]; legacy #110 [49c266baaaa7]; workbench #1477 [49c266baaaa7] |  |
| df2615da81dc5fd1 | en | ui.activity-log | file #lang/en/ui.php [315dfb75f23c]; workbench #1719 [315dfb75f23c] | key_requires_nested_array_review |
| e4ec2b2dca6e5d50 | en | ui.actual | file #lang/en/ui.php [c406421c2b35]; workbench #1721 [c406421c2b35] | key_requires_nested_array_review |
| 85bd5ec95f601786 | en | ui.add-document | file #lang/en/ui.php [f384e6797d39]; workbench #1723 [f384e6797d39] | key_requires_nested_array_review |
| 7a8d0db78bd55c4e | en | ui.add-primary-language | file #lang/en/ui.php [442e01033bc1]; workbench #1725 [442e01033bc1] | key_requires_nested_array_review |
| e5e81a2043458f97 | en | ui.address.address.address-line-2 | file #lang/en/ui.php [350b2b85c4af]; workbench #1729 [350b2b85c4af] |  |
| d201992ce55eb749 | en | ui.all.all-decisions | file #lang/en/ui.php [1ee5677346f3]; workbench #1731 [1ee5677346f3] |  |
| 83656e8517b86cbb | en | ui.all.all-dynamic | file #lang/en/ui.php [dfd773a1062f]; workbench #1733 [dfd773a1062f] |  |
| 4ab082b1603c167d | en | ui.already-target | file #lang/en/ui.php [0da29ceb7bd8]; workbench #1741 [0da29ceb7bd8] | key_requires_nested_array_review |
| daf547b7ade62f31 | en | ui.app-settings | file #lang/en/ui.php [d43fb8a961ff]; workbench #1743 [d43fb8a961ff] | key_requires_nested_array_review |
| e527a565ca815d25 | en | ui.appearance | file #lang/en/ui.php [3907fa7f8072]; workbench #1745 [3907fa7f8072] | key_requires_nested_array_review |
| 9ac50d3fc487536e | en | ui.apply.applied | file #lang/en/ui.php [0c79a9c22284]; workbench #1747 [0c79a9c22284] |  |
| 0b3847ca2b4f2f57 | en | ui.apply.apply-conflict | file #lang/en/ui.php [087574059614]; workbench #1796 [087574059614] |  |
| af713cadf0e9b705 | en | ui.assign.assigned.assigned-roles | file #lang/en/ui.php [423dc5d9738b]; workbench #1806 [423dc5d9738b] |  |
| 403c2537cb5a99fd | en | ui.assign.assigned.assigned-users | file #lang/en/ui.php [0ad1e44cdf83]; workbench #1808 [0ad1e44cdf83] |  |
| edabe49d1c8bff14 | en | ui.assignable.assignable | file #lang/en/ui.php [a5fa69df71d1]; workbench #1800 [a5fa69df71d1] |  |
| 2fc9ac4248af9942 | en | ui.assignable.assignable-through-ui | file #lang/en/ui.php [4abb94c0a83d]; workbench #1804 [4abb94c0a83d] |  |
| 935764a2e171ac45 | en | ui.badge.bulk-equalized | file #lang/en/ui.php [b94d17748f6d]; workbench #1695 [b94d17748f6d] |  |
| 7e1f953af0398f6b | en | ui.badge.create_person.person_created | file #lang/en/ui.php [b35364b95d27]; workbench #1574 [b35364b95d27] |  |
| 3bebdd562252cf94 | en | ui.badge.created_password.generated_temporary_password | file #lang/en/ui.php [2b26821eefb9]; workbench #1560 [2b26821eefb9] |  |
| 3604f1e4e1bf5f48 | en | ui.badge.created_password.temporary_password | file #lang/en/ui.php [b20862a1a937]; workbench #1568 [b20862a1a937] |  |
| c9d00aad2d0c7403 | en | ui.badge.created_password.tooltip.temporary_password_password_this_password_is_shown_once_here_and_is_also_written_to | file #lang/en/ui.php [3d0f532a0362]; workbench #1566 [3d0f532a0362] |  |
| e73042d942e63b7c | en | ui.badge.created_person.tooltip.person_number_person_number_created_person_id_person_id_created_user_id_user_id | file #lang/en/ui.php [b799133abea2]; workbench #1570 [b799133abea2] |  |
| f6bf5b70da56e96f | en | ui.badge.current.current | file #lang/en/ui.php [e0d1b68224bf]; workbench #1756 [e0d1b68224bf] |  |
| f3058d4e7afcfb82 | en | ui.badge.informatic.nil | file #lang/en/ui.php [69e69621d904]; workbench #1562 [69e69621d904] |  |
| 1a2e10c3e358b8ee | en | ui.badge.informatic.no_date | file #lang/en/ui.php [a2cec8c8a4fa]; workbench #1584 [a2cec8c8a4fa] |  |
| 1e93a819bf3e9377 | en | ui.badge.informatic.no_time | file #lang/en/ui.php [ece384fb33f3]; workbench #1582 [ece384fb33f3] |  |
| 37957b0f4a5fa717 | en | ui.badge.informatic.no_value | file #lang/en/ui.php [5eff1127def1]; workbench #1580 [5eff1127def1] |  |
| 19d734fa920dd082 | en | ui.badge.informatic.not_a_number | file #lang/en/ui.php [e599360d32d3]; workbench #1578 [e599360d32d3] |  |
| 88eed022abf050d7 | en | ui.badge.test-data | file #lang/en/ui.php [e27c8214be8b]; workbench #1564 [e27c8214be8b] |  |
| fbcd731eeb956ef1 | en | ui.badge.timespan | file #lang/en/ui.php [18253f5432d3]; workbench #1852 [18253f5432d3] |  |
| 7980ba9718338c88 | en | ui.badge.total | file #lang/en/ui.php [c9b3c38247f7]; workbench #1850 [c9b3c38247f7] |  |
| d752576860a46ea0 | en | ui.badge.unresolved | file #lang/en/ui.php [36afca802c4d]; workbench #1828 [36afca802c4d] |  |
| 9654b42687e4ac71 | en | ui.badge.updated | file #lang/en/ui.php [3a5ecca188c0]; workbench #1826 [3a5ecca188c0] |  |
| b9458e544c45817d | en | ui.button.activate_deactivate.activate_language | file #lang/en/ui.php [0fda203a5604]; legacy #6606 [0fda203a5604]; workbench #1478 [0fda203a5604] |  |
| edca8d58e3f25354 | en | ui.button.activate_deactivate.deactivate_language | file #lang/en/ui.php [fcef8962c50a]; legacy #6488 [fcef8962c50a]; workbench #1479 [fcef8962c50a] |  |
| d04ece6e0254f6fd | en | ui.button.cancel.cancel | file #lang/en/ui.php [19766ed6ccb2]; legacy #6608 [19766ed6ccb2]; workbench #1480 [19766ed6ccb2] |  |
| a5d6cba526beda69 | en | ui.button.clear.clear | file #lang/en/ui.php [83b12c2216ef]; legacy #6471 [83b12c2216ef]; workbench #1481 [83b12c2216ef] |  |
| e7835df1e3ecf1f4 | en | ui.button.close.close | file #lang/en/ui.php [7d9eb7acb13e]; workbench #1607 [7d9eb7acb13e] |  |
| e013b3bf38947453 | en | ui.button.confirm.confirm | file #lang/en/ui.php [eebdd24a77d9]; workbench #1402 [eebdd24a77d9] |  |
| 4b56f4ba474d9002 | en | ui.button.copy.copy | file #lang/en/ui.php [e21f935f11d7]; legacy #6480 [e21f935f11d7]; workbench #1482 [e21f935f11d7] |  |
| 896826c733c42f20 | en | ui.button.create.create | file #lang/en/ui.php [4759498ac2a7]; workbench #1068 [4759498ac2a7] |  |
| 6b2c976ab03a6676 | en | ui.button.delete.delete | file #lang/en/ui.php [e2d0a54968ea]; legacy #6610 [e2d0a54968ea]; workbench #1483 [e2d0a54968ea] |  |
| 70c331aa5333be2a | en | ui.button.edit.edit | file #lang/en/ui.php [464c4ffd019e]; workbench #1197 [464c4ffd019e] |  |
| 000c1b75051d2af4 | en | ui.button.open.open_next_editable_entry | file #lang/en/ui.php [dcd8a91e9c66]; workbench #1244 [dcd8a91e9c66] |  |
| 1af282629104d3ac | en | ui.button.open.open_previous_editable_entry | file #lang/en/ui.php [0dfbf8db4c29]; workbench #1484 [0dfbf8db4c29] |  |
| bbbdf9d73221b92f | en | ui.button.prev_edit.open_previous_editable_entry | file #lang/en/ui.php [0dfbf8db4c29]; legacy #6484 [0dfbf8db4c29] |  |
| 2f0a084c787eb5b9 | en | ui.button.remove.remove | file #lang/en/ui.php [c3812fc4acb8]; legacy #6612 [c3812fc4acb8]; workbench #1485 [c3812fc4acb8] |  |
| 3ab0cd68827c1988 | en | ui.button.reset.reset | file #lang/en/ui.php [daee7606b339]; legacy #6614 [daee7606b339]; workbench #1486 [daee7606b339] |  |
| 78d309dafa1c4940 | en | ui.button.reset.reset-the-form-to-its-initial-state | file #lang/en/ui.php [dec761f47579]; workbench #2005 [dec761f47579] |  |
| 51e41ac3241b5c41 | en | ui.button.review.review | file #lang/en/ui.php [aff0766a5290]; legacy #6616 [aff0766a5290]; workbench #1487 [aff0766a5290] |  |
| 01e444a6b30e3641 | en | ui.button.save.save | file #lang/en/ui.php [1509f561f241]; legacy #7870 [1509f561f241]; workbench #1475 [1509f561f241] |  |
| abb933427098d88d | en | ui.button.save.save_role | file #lang/en/ui.php [8db97cbf8712]; legacy #6618 [8db97cbf8712]; workbench #1488 [8db97cbf8712] |  |
| 8692d2fcf5c278e3 | en | ui.button.show-hide.hide | file #lang/en/ui.php [ac20a57bfde0]; workbench #1538 [ac20a57bfde0] |  |
| 919ec6094cd36286 | en | ui.button.show-hide.hide-overview | file #lang/en/ui.php [22e17fa5177e]; workbench #2153 [22e17fa5177e] |  |
| 6187461ceca02390 | en | ui.button.show-hide.show | file #lang/en/ui.php [0df6f1cad36c]; workbench #1540 [0df6f1cad36c] |  |
| 3152315f77e3abdb | en | ui.button.tab_scroll.first_tab | file #lang/en/ui.php [4d0b9a9c122f]; workbench #2021 [4d0b9a9c122f] |  |
| c89f9a5c3168cf8a | en | ui.button.tab_scroll.last_tab | file #lang/en/ui.php [11164593151e]; workbench #2023 [11164593151e] |  |
| c423dce57c041fab | en | ui.button.tab_scroll.next_tab | file #lang/en/ui.php [1ef9522a9a8a]; workbench #2025 [1ef9522a9a8a] |  |
| 8497e195bb3a97d9 | en | ui.button.tab_scroll.previous_tab | file #lang/en/ui.php [c88d5e7ec3a8]; workbench #2027 [c88d5e7ec3a8] |  |
| 72545e47ba630fb6 | en | ui.button.tab_scroll.scroll_tabs | file #lang/en/ui.php [d07aba7f381b]; workbench #2029 [d07aba7f381b] |  |
| c1c3dc033afca22b | en | ui.client-number | file #lang/en/ui.php [c7936f0541d2]; workbench #1780 [c7936f0541d2] | key_requires_nested_array_review |
| f4e7cf955a72606d | en | ui.count.min | file #lang/en/ui.php [dea79332147f]; workbench #2107 [dea79332147f] |  |
| 7e0b91815441cce4 | en | ui.date-time.ago | file #lang/en/ui.php [49879ad6f891]; workbench #1649 [49879ad6f891] |  |
| e0b39547504bc489 | en | ui.filter.filter | file #lang/en/ui.php [638e249f4a15]; workbench #902 [638e249f4a15] |  |
| 6c2c3ca8addcab82 | en | ui.filter.show-legacy-dynamic-entries-that-were-reactivated-from-stale-audit-state | file #lang/en/ui.php [51bfa932a851]; workbench #2081 [51bfa932a851] |  |
| 7136ebd5e82396e9 | en | ui.filters.all-contexts | file #lang/en/ui.php [952fbe376e5c]; workbench #1671 [952fbe376e5c] |  |
| 3aa28b810d7976f4 | en | ui.filters.all-domains | file #lang/en/ui.php [0f97da22f84f]; workbench #1673 [0f97da22f84f] |  |
| c4ef3a28b3d1328c | en | ui.filters.all-extras | file #lang/en/ui.php [de74451d9300]; workbench #1675 [de74451d9300] |  |
| 07bb52e03e717f7b | en | ui.filters.all-groups | file #lang/en/ui.php [1b492be77224]; workbench #1697 [1b492be77224] |  |
| 7726918d74aa8e2f | en | ui.filters.all-locales | file #lang/en/ui.php [a9f854d579b6]; workbench #1679 [a9f854d579b6] |  |
| 54be0e0cfd4121fe | en | ui.filters.all-namespaces | file #lang/en/ui.php [50c3cb6e2284]; workbench #1683 [50c3cb6e2284] |  |
| d539d6e8e51466af | en | ui.filters.all-scopes | file #lang/en/ui.php [93c84327ecf9]; workbench #1685 [93c84327ecf9] |  |
| 65e91372c700955c | en | ui.filters.all-sections | file #lang/en/ui.php [1fb871c02fb1]; workbench #1687 [1fb871c02fb1] |  |
| 749b94cb980565d6 | en | ui.filters.all-sources | file #lang/en/ui.php [08e774c5bacc]; workbench #1689 [08e774c5bacc] |  |
| 2128265278965d98 | en | ui.filters.eea | file #lang/en/ui.php [5ef407e5cb98]; workbench #1786 [5ef407e5cb98] |  |
| 015f2c88cb5c45dd | en | ui.filters.eu | file #lang/en/ui.php [9170a8b2fb32]; workbench #1788 [9170a8b2fb32] |  |
| d16f0b1987d00037 | en | ui.filters.filter-by-status | file #lang/en/ui.php [94d5a7703a82]; workbench #1774 [94d5a7703a82] |  |
| edd6cbbe737ce29f | en | ui.filters.inactive | file #lang/en/ui.php [ac7c949f1211]; workbench #1790 [ac7c949f1211] |  |
| af354c8a48af2460 | en | ui.filters.missing-capital | file #lang/en/ui.php [c5e8a1222801]; workbench #1798 [c5e8a1222801] |  |
| ec944faa393f783c | en | ui.filters.region | file #lang/en/ui.php [d3a008ef1335]; workbench #1792 [d3a008ef1335] |  |
| 276b28fc96d1c06d | en | ui.filters.yes | file #lang/en/ui.php [85a39ab345d6]; workbench #1667 [85a39ab345d6] |  |
| 255e71fe65634315 | en | ui.form.tab_status_dot.current_status | file #lang/en/ui.php [b42613578508]; legacy #6624 [b42613578508]; workbench #1489 [b42613578508] |  |
| b3bba1301c5c3583 | en | ui.form.tab_status_dot.fields | file #lang/en/ui.php [616f48c1db19]; legacy #6513 [616f48c1db19]; workbench #1490 [616f48c1db19] |  |
| 6ea513c9389616cd | en | ui.form.tab_status_dot.form_tab | file #lang/en/ui.php [b26d84cb5e70]; legacy #6622 [b26d84cb5e70]; workbench #1491 [b26d84cb5e70] |  |
| 6f627ef951baae6c | en | ui.form.tab_status_dot.form_tab_status_status | file #lang/en/ui.php [d051bebb0b8e]; legacy #6620 [d051bebb0b8e]; workbench #1492 [d051bebb0b8e] |  |
| 38537a309974ab73 | en | ui.form.tab_status_dot.legend | file #lang/en/ui.php [7482e374b5bd]; legacy #6509 [7482e374b5bd]; workbench #1493 [7482e374b5bd] |  |
| da0bf1d7f0bf32cd | en | ui.form.tab_status_dot.required | file #lang/en/ui.php [4850b174b713]; legacy #6511 [4850b174b713]; workbench #1494 [4850b174b713] |  |
| 3783ba1c4bea8321 | en | ui.key.suggested-key | file #lang/en/ui.php [91b64f5d3833]; workbench #1874 [91b64f5d3833] |  |
| b93e4813ff8e8566 | en | ui.label.would-apply | file #lang/en/ui.php [673a519f3572]; workbench #1816 [673a519f3572] |  |
| 2bc90df567342a09 | en | ui.label.writing | file #lang/en/ui.php [a8bfae3eee94]; workbench #1814 [a8bfae3eee94] |  |
| 4a8dd3d4cbc65ea6 | en | ui.label.written | file #lang/en/ui.php [4c6031d1d40c]; workbench #1812 [4c6031d1d40c] |  |
| 1c6d957b4e35a321 | en | ui.labels.address.address | file #lang/en/ui.php [56ef8f20955f]; workbench #1727 [56ef8f20955f] |  |
| 9f5f1173d193d3c7 | en | ui.labels.approved | file #lang/en/ui.php [87b42e40c2a2]; workbench #1749 [87b42e40c2a2] |  |
| 83636c5636d07dd6 | en | ui.labels.audit | file #lang/en/ui.php [bb6aea287396]; workbench #1810 [bb6aea287396] |  |
| 5b0f21609d19406f | en | ui.labels.category | file #lang/en/ui.php [292c06f0045a]; legacy #114 [292c06f0045a]; workbench #1496 [292c06f0045a] |  |
| b541ed0db5e5ef15 | en | ui.labels.created | file #lang/en/ui.php [d70b9e24bca2]; workbench #1770 [d70b9e24bca2] |  |
| 06aa700d2533490b | en | ui.labels.description | file #lang/en/ui.php [526e0087cc3f]; legacy #116 [526e0087cc3f]; workbench #1497 [526e0087cc3f] |  |
| 34d61f6499f3101b | en | ui.labels.guard | file #lang/en/ui.php [c8d3fc567bbc]; legacy #118 [c8d3fc567bbc]; workbench #1498 [c8d3fc567bbc] |  |
| 5ac9029a117ea176 | en | ui.labels.label | file #lang/en/ui.php [0e66373f45dc]; workbench #1762 [0e66373f45dc] |  |
| 58fbd8e395c59985 | en | ui.labels.name | file #lang/en/ui.php [dcd1d5223f73]; legacy #120 [dcd1d5223f73]; workbench #1499 [dcd1d5223f73] |  |
| 60d01d29bc03a4b2 | en | ui.labels.number | file #lang/en/ui.php [2c79b0e17bf0]; legacy #196 [2c79b0e17bf0]; workbench #1500 [2c79b0e17bf0] |  |
| d3b7a75dfeb54fd6 | en | ui.labels.number_short | file #lang/en/ui.php [334359b90efe]; legacy #122 [334359b90efe]; workbench #1501 [334359b90efe] |  |
| 774c82da3348b44a | en | ui.labels.preview | file #lang/en/ui.php [324b134f57c7]; legacy #198 [324b134f57c7]; workbench #1502 [324b134f57c7] |  |
| 1e9ea91d7b53b125 | en | ui.labels.role | file #lang/en/ui.php [14736a2eb9f4]; legacy #124 [14736a2eb9f4]; workbench #1503 [14736a2eb9f4] |  |
| 54651d5cce85a716 | en | ui.labels.roles | file #lang/en/ui.php [c25337055464]; legacy #126 [c25337055464]; workbench #1504 [c25337055464] |  |
| c627746976953b7c | en | ui.language.target-sub-language | file #lang/en/ui.php [48ba56c146ec]; workbench #1864 [48ba56c146ec] |  |
| e3de3f479effa95c | en | ui.languages.sub-languages | file #lang/en/ui.php [e86ee8e607fa]; workbench #1882 [e86ee8e607fa] |  |
| 3236958c84535c2c | en | ui.laravel_translation_workbench.resources.views.livewire.entries.table_entries.show_entries | file #lang/en/ui.php [617948a6e6de]; legacy #6796 [617948a6e6de] |  |
| 6b519b83fadd5b04 | en | ui.literal.dynamic.this-finding-is-treated-as-a-dynamic-multi-entry-multiple-runtime-option-values-may-n | file #lang/en/ui.php [37cefeec3a34]; workbench #1862 [37cefeec3a34] |  |
| ce5687f53f08c3e0 | en | ui.literal.dynamic.this-finding-is-treated-as-dynamic-the-displayed-value-is-resolved-from-runtime-data | file #lang/en/ui.php [c2e776bce4f3]; workbench #1860 [c2e776bce4f3] |  |
| 9ae6b5ee444ad394 | en | ui.literal.literal | file #lang/en/ui.php [a6230c2a81ab]; workbench #1918 [a6230c2a81ab] |  |
| e45fc4858d47a971 | en | ui.loading.lazy_indicator.loading | file #lang/en/ui.php [dc380888c4e2]; workbench #2155 [dc380888c4e2] |  |
| 3ccfaca19611db0e | en | ui.messages.no_description_available | file #lang/en/ui.php [4d4c2197d70e]; legacy #128 [4d4c2197d70e]; workbench #1506 [4d4c2197d70e] |  |
| 4dc3f00a56be64d0 | en | ui.meta.assignable-roles | file #lang/en/ui.php [1411cbacf3c7]; workbench #1802 [1411cbacf3c7] |  |
| 98b3315f7b8334a2 | en | ui.meta.with-people | file #lang/en/ui.php [67de0b69545d]; workbench #1776 [67de0b69545d] |  |
| 0bdbcb173edbbbef | en | ui.meta.without-people | file #lang/en/ui.php [c5a1f8f68593]; workbench #1778 [c5a1f8f68593] |  |
| e9ea10f9b0a986b1 | en | ui.names.nickname | file #lang/en/ui.php [d720f61c8c5e]; workbench #1754 [d720f61c8c5e] |  |
| 7f41b46ca1db4b97 | en | ui.nouns.pagination | file #lang/en/ui.php [7b3f674b625e]; workbench #1772 [7b3f674b625e] |  |
| c5f75f146f4171c3 | en | ui.nouns.summary | file #lang/en/ui.php [8e76a94ac832]; workbench #1872 [8e76a94ac832] |  |
| 311fa5cbe64c8576 | en | ui.nouns.view | file #lang/en/ui.php [dcc839a4015c]; workbench #1768 [dcc839a4015c] |  |
| e0dff1bac57fb9ea | en | ui.nouns.workflow | file #lang/en/ui.php [2e2d5c5649b7]; workbench #1818 [2e2d5c5649b7] |  |
| 30c8d98ee5128dbb | en | ui.null.null | file #lang/en/ui.php [fb329000228c]; workbench #1576 [fb329000228c] |  |
| e87be7780852bccb | en | ui.obsolete.the-translation-key-was-not-found-during-the-latest-audit-scan-and-was-marked-as-obsolete | file #lang/en/ui.php [08c419fc83e3]; workbench #2087 [08c419fc83e3] |  |
| 38ab83bf9b6824b9 | en | ui.open | file #lang/en/ui.php [ed077f3d8125]; workbench #1550 [ed077f3d8125] | key_requires_nested_array_review |
| 2a44750b3d6f76b2 | en | ui.people.people | file #lang/en/ui.php [7db20897053b]; workbench #1782 [7db20897053b] |  |
| 8086cef1af3f816b | en | ui.per_page_selector.per_page | file #lang/en/ui.php [68409781a4a1]; workbench #1520 [68409781a4a1] |  |
| 7bf937e87d792319 | en | ui.remove.remove.choose-unlink-to-remove-the-active-relation-or-keep-link-to-leave-the-confirmed-decison | file #lang/en/ui.php [1e82779cbbe8]; workbench #2145 [1e82779cbbe8] |  |
| 73c01c9dd8506687 | en | ui.remove.remove.remove-first-key-segment | file #lang/en/ui.php [eda544490516]; workbench #2121 [eda544490516] |  |
| 877bb0745a073293 | en | ui.remove.remove.remove-first-value-segment | file #lang/en/ui.php [3c7ce3c46b42]; workbench #2123 [3c7ce3c46b42] |  |
| 6e2eeb9426fd023a | en | ui.remove.remove.remove-hash-suffix | file #lang/en/ui.php [3e16752070a1]; workbench #2125 [3e16752070a1] |  |
| 47891f62901b7038 | en | ui.remove.remove.remove-or-restore-leading-translation-key-segments-while-the-key-has-not-been-manually-edited | file #lang/en/ui.php [350f2a632865]; workbench #2151 [350f2a632865] |  |
| 400d0a5a94223757 | en | ui.remove.removed.completed-lang-cleanup-actions-including-removed-or-moved-translation-values | file #lang/en/ui.php [038b47b3b9c9]; workbench #2143 [038b47b3b9c9] |  |
| 4abbbb0fe705c6bc | en | ui.remove.removed.no-lang-file-values-have-been-removed-yet | file #lang/en/ui.php [3ffb647f0fad]; workbench #2127 [3ffb647f0fad] |  |
| e7b52c64b2f8b6bb | en | ui.remove.removed.removed | file #lang/en/ui.php [4118fb4fed0e]; workbench #2109 [4118fb4fed0e] |  |
| 1a6b1c0037f7929f | en | ui.remove.removed.removed-at | file #lang/en/ui.php [d362eafb9053]; workbench #2111 [d362eafb9053] |  |
| 669657c0025564e3 | en | ui.remove.removed.removed-lang-values | file #lang/en/ui.php [b961fc5da5a4]; workbench #2115 [b961fc5da5a4] |  |
| ad1ee3c238bffe44 | en | ui.remove.removed.removed-value | file #lang/en/ui.php [87f7c58feb31]; workbench #2113 [87f7c58feb31] |  |
| aece859ae46bb588 | en | ui.remove.removed.review-whether-this-source-language-value-should-be-removed-from-the-active-export-workflow | file #lang/en/ui.php [d47e04c74871]; workbench #2149 [d47e04c74871] |  |
| c8b58a41d22e7afe | en | ui.remove.removed.shows-lang-file-values-that-were-actually-removed-by-a-write-export-run-source-data | file #lang/en/ui.php [d0c190a44c1c]; workbench #2129 [d0c190a44c1c] |  |
| 48edfd8e4bb8cb08 | en | ui.remove.removed.these-entries-are-present-in-lang-files-but-are-no-longer-active-export-values-in-dry | file #lang/en/ui.php [d49604978197]; workbench #2131 [d49604978197] |  |
| 3565791100956e83 | en | ui.remove.removed.values-that-would-be-removed | file #lang/en/ui.php [fda3522e24b8]; workbench #2117 [fda3522e24b8] |  |
| 51dea7c8a742e8ef | en | ui.remove.removed.would-be-removed-from-lang-files | file #lang/en/ui.php [95386625478f]; workbench #2119 [95386625478f] |  |
| 3f88454b89937529 | en | ui.remove.removes.keep-leaves-the-lang-values-active-needs-review-only-records-the-decision-mark-obsolete | file #lang/en/ui.php [b4121aaff9da]; workbench #2147 [b4121aaff9da] |  |
| c46f7bcb4f8db631 | en | ui.remove.removes.removes-the-first-dot-separated-segment-from-the-current-search-text-useful-when-expo | file #lang/en/ui.php [c55fd46ae364]; workbench #2135 [c55fd46ae364] |  |
| 2c6c169495a94cc0 | en | ui.remove.removes.removes-the-first-dot-separated-segment-from-the-current-value-search-text-useful-when | file #lang/en/ui.php [6825c5795cee]; workbench #2139 [6825c5795cee] |  |
| 328748c899b8dca5 | en | ui.remove.removes.removes-the-selected-findings-from-the-bulk-translation-key-review | file #lang/en/ui.php [dad881a21662]; workbench #2141 [dad881a21662] |  |
| 95b2502fce860a4f | en | ui.remove.removes.removes-the-selected-scalar-candidates-from-the-current-review-selection | file #lang/en/ui.php [181687969ed0]; workbench #2133 [181687969ed0] |  |
| 32a1212ff75b405a | en | ui.sort.sort.sort-order | file #lang/en/ui.php [3c9b30285f90]; workbench #2074 [3c9b30285f90] |  |
| 6461930e2704dbab | en | ui.source.contains-source-path | file #lang/en/ui.php [6740ad6819f6]; workbench #1991 [6740ad6819f6] |  |
| e529354285df3c53 | en | ui.source.source | file #lang/en/ui.php [0e570ca6fabe]; workbench #1906 [0e570ca6fabe] |  |
| 476e0410c0749894 | en | ui.source.source-language | file #lang/en/ui.php [088b23e3327d]; workbench #1902 [088b23e3327d] |  |
| 2c61f35a389f8af6 | en | ui.source.source-locale-missing | file #lang/en/ui.php [d876051eda7b]; workbench #1904 [d876051eda7b] |  |
| 0c59db4dc5e24779 | en | ui.source.source-path | file #lang/en/ui.php [6a869dd1367c]; workbench #1900 [6a869dd1367c] |  |
| 7bfdc75be98fa576 | en | ui.source.source-type | file #lang/en/ui.php [24e59ff38c6b]; workbench #1898 [24e59ff38c6b] |  |
| a1110740f87c8398 | en | ui.source.stale-source | file #lang/en/ui.php [e3b169d31557]; workbench #1892 [e3b169d31557] |  |
| 91d6774113fac470 | en | ui.sources.sources | file #lang/en/ui.php [caf85b0888d7]; workbench #1896 [caf85b0888d7] |  |
| 0e828ba6c7dfc8c7 | en | ui.stale.a-previously-stale-or-obsolete-translation-entry-was-found-again-and-reactivated | file #lang/en/ui.php [8cff82844f64]; workbench #2083 [8cff82844f64] |  |
| 64b9dff8303c4565 | en | ui.stale.marked-stale | file #lang/en/ui.php [72e85d59d84f]; workbench #2085 [72e85d59d84f] |  |
| d40ba5b73dd31935 | en | ui.stale.reactivated-stale | file #lang/en/ui.php [ab84bbe0eb4e]; workbench #2079 [ab84bbe0eb4e] |  |
| f6913c30a0adac27 | en | ui.stale.stale | file #lang/en/ui.php [40c9e59c5e15]; workbench #1894 [40c9e59c5e15] |  |
| 014c74952a06f7d7 | en | ui.stale.stale-usages | file #lang/en/ui.php [1b8b6595da8e]; workbench #2097 [1b8b6595da8e] |  |
| d0c48fb821a1ce84 | en | ui.stale.with-stale-usages | file #lang/en/ui.php [3e9b3f8069da]; workbench #2095 [3e9b3f8069da] |  |
| b17129f6d8a85409 | en | ui.state.active | file #lang/en/ui.php [92340695899b]; workbench #1715 [92340695899b] |  |
| e331f3d61bc11fc0 | en | ui.state.active-state | file #lang/en/ui.php [c69d2adb8a5d]; workbench #1717 [c69d2adb8a5d] |  |
| eef409d0a74ff1d9 | en | ui.state.available | file #lang/en/ui.php [e674447337e8]; workbench #1758 [e674447337e8] |  |
| f0470560cc84bf19 | en | ui.state.missing | file #lang/en/ui.php [6be36ca49ee8]; workbench #1760 [6be36ca49ee8] |  |
| a95631f73de06ce4 | en | ui.state.state | file #lang/en/ui.php [a3b50c476732]; workbench #1890 [a3b50c476732] |  |
| 1d8a7923475eddc4 | en | ui.state.status | file #lang/en/ui.php [920e413c7d41]; legacy #8284 [920e413c7d41]; workbench #1766 [920e413c7d41] |  |
| d0e8e7ab2c0332e8 | en | ui.state.ui-translation-state | file #lang/en/ui.php [25e415939f70]; workbench #1830 [25e415939f70] |  |
| b95a0cf82dd04d6b | en | ui.states.all | file #lang/en/ui.php [a52ace420f21]; legacy #130 [a52ace420f21]; workbench #1507 [a52ace420f21] |  |
| 6ca75d4d44ea9a57 | en | ui.states.all-states | file #lang/en/ui.php [f189411452ed]; workbench #1794 [f189411452ed] |  |
| 92d53d02e3b89b03 | en | ui.states.assigned | file #lang/en/ui.php [8191888dd97c]; legacy #132 [8191888dd97c]; workbench #1508 [8191888dd97c] |  |
| 261fca412cad9030 | en | ui.states.custom | file #lang/en/ui.php [494ca78f7374]; legacy #134 [494ca78f7374]; workbench #1509 [494ca78f7374] |  |
| 2515024f4bfb0b32 | en | ui.states.no | file #lang/en/ui.php [1ea442a134b2]; legacy #200 [1ea442a134b2]; workbench #1510 [1ea442a134b2] |  |
| d1b01f35b185dacc | en | ui.states.no_changes | file #lang/en/ui.php [c699aa00b26d]; legacy #136 [c699aa00b26d]; workbench #1511 [c699aa00b26d] |  |
| e961ef069e9faccb | en | ui.states.non_system | file #lang/en/ui.php [e478bdfdfe54]; legacy #138 [e478bdfdfe54]; workbench #1512 [e478bdfdfe54] |  |
| 915c9335e3ad9de8 | en | ui.states.other | file #lang/en/ui.php [f97e9da0e3b8]; legacy #140 [f97e9da0e3b8]; workbench #1513 [f97e9da0e3b8] |  |
| f26840e728946336 | en | ui.states.system | file #lang/en/ui.php [6725e7bbcd28]; legacy #142 [6725e7bbcd28]; workbench #1514 [6725e7bbcd28] |  |
| 0b8e0614e71ce448 | en | ui.states.unassigned | file #lang/en/ui.php [14d33bd014e6]; legacy #144 [14d33bd014e6]; workbench #1515 [14d33bd014e6] |  |
| b3fa0b69d2809f22 | en | ui.states.unsaved_changes | file #lang/en/ui.php [a710c2b90913]; legacy #146 [a710c2b90913]; workbench #1516 [a710c2b90913] |  |
| 7aec6dc98d00d3b8 | en | ui.states.yes | file #lang/en/ui.php [85a39ab345d6]; legacy #202 [85a39ab345d6] |  |
| 148bc7b0e066b57f | en | ui.storage.storage | file #lang/en/ui.php [a69c4dece144]; workbench #1888 [a69c4dece144] |  |
| cafe53da70b51e4f | en | ui.structure.structure | file #lang/en/ui.php [7203c2b3dad3]; workbench #1884 [7203c2b3dad3] |  |
| 2b3b88f066f8e7bc | en | ui.suggest.suggested | file #lang/en/ui.php [93b63bab4c3a]; workbench #1880 [93b63bab4c3a] |  |
| 94b3f84ba7d1b764 | en | ui.table.headers.actions | file #lang/en/ui.php [ff8059dc6752]; legacy #7303 [ff8059dc6752]; workbench #1677 [ff8059dc6752] |  |
| fcc840bc361fa531 | en | ui.table.pagination.results | file #lang/en/ui.php [c099142bc318]; legacy #6505 [c099142bc318]; workbench #1518 [c099142bc318] |  |
| 340317b1d4cca8ee | en | ui.table.pagination.to | file #lang/en/ui.php [663ea1bfffe5]; legacy #6507 [663ea1bfffe5]; workbench #1519 [663ea1bfffe5] |  |
| b8f0003b00166899 | en | ui.table.per_page_selector.per_page | file #lang/en/ui.php [68409781a4a1]; legacy #6496 [68409781a4a1] |  |
| 04d7a35ffebe1a6e | en | ui.target.target-language | file #lang/en/ui.php [ea252b75a5db]; workbench #1868 [ea252b75a5db] |  |
| e246ac0f9c3b9364 | en | ui.target.target-locale-ready | file #lang/en/ui.php [0d7b150235c8]; workbench #1870 [0d7b150235c8] |  |
| 9c3285ea0783267d | en | ui.target.target-missing | file #lang/en/ui.php [c84c3fc3f648]; workbench #1866 [c84c3fc3f648] |  |
| 54f5cdf0a44e5b23 | en | ui.text.copyable_field.copy_to_clipboard | file #lang/en/ui.php [b749e205302c]; legacy #6490 [b749e205302c]; workbench #1521 [b749e205302c] |  |
| 824178cc49919089 | en | ui.time.timeline | file #lang/en/ui.php [9dcff98e275f]; legacy #8302 [9dcff98e275f]; workbench #1858 [9dcff98e275f] |  |
| d1995afc3ff9fdc2 | en | ui.time.timeline-relation-context | file #lang/en/ui.php [956332bade3b]; workbench #1856 [956332bade3b] |  |
| e1f825f6ab75232c | en | ui.time.timeline-value-context | file #lang/en/ui.php [d45b470f25a4]; workbench #1854 [d45b470f25a4] |  |
| b907bb0406d99598 | en | ui.title.filter | file #lang/en/ui.php [638e249f4a15]; legacy #8311 [638e249f4a15]; workbench #1023 [638e249f4a15] |  |
| 8cc7c0098a758d5b | en | ui.title.overview | file #lang/en/ui.php [d4b1ea5708dd]; legacy #8320 [d4b1ea5708dd]; workbench #2106 [d4b1ea5708dd] |  |
| f37e52f9b2d961a6 | en | ui.tooltip.badge_required.required | file #lang/en/ui.php [4850b174b713]; legacy #6486 [4850b174b713] |  |
| a2435bdaba1b6af7 | en | ui.translation.transform-suggested-key-to-ui-translation-key | file #lang/en/ui.php [784c9d19b1cc]; workbench #1848 [784c9d19b1cc] |  |
| 6cfe1c0bdd244622 | en | ui.translation.translation-exists | file #lang/en/ui.php [c83664803038]; workbench #1846 [c83664803038] |  |
| 72b312bc75bf7816 | en | ui.translation.translation-key | file #lang/en/ui.php [23f14a84c6ee]; workbench #1844 [23f14a84c6ee] |  |
| 1565c9fa94f8243b | en | ui.translation.translation-key-state | file #lang/en/ui.php [75e7cb47a9f7]; workbench #1840 [75e7cb47a9f7] |  |
| d38f5df4acc65b07 | en | ui.translation.translation-values | file #lang/en/ui.php [e55b7c9287fb]; workbench #1838 [e55b7c9287fb] |  |
| f83ef7367ddef803 | en | ui.translation.translation-workbench | file #lang/en/ui.php [e79db8b7f6f3]; workbench #1836 [e79db8b7f6f3] |  |
| 8ed2b6945d6a5154 | en | ui.type | file #lang/en/ui.php [baaddf70fb5d]; legacy #8329 [baaddf70fb5d]; workbench #1784 [baaddf70fb5d] | key_requires_nested_array_review |
| 366f3a74976481dd | en | ui.ui.ui-candidate | file #lang/en/ui.php [63beaf380b51]; workbench #1834 [63beaf380b51] |  |
| 2751261195d4c6f8 | en | ui.ui.ui-translation | file #lang/en/ui.php [510fe692a112]; workbench #1832 [510fe692a112] |  |
| 41ccf08483c2b1f4 | en | ui.usage.usage-state-after | file #lang/en/ui.php [be09a3d9c2ce]; workbench #2089 [be09a3d9c2ce] |  |
| ca01758f6124c47d | en | ui.usage.usage-state-before | file #lang/en/ui.php [43f1fd27a1f6]; workbench #2091 [43f1fd27a1f6] |  |
| 66bb5d4250d6166f | en | ui.user.user-id | file #lang/en/ui.php [7967e089a7b9]; workbench #1824 [7967e089a7b9] |  |
| c9371ae0cdc6d052 | en | ui.user_avatar.avatar_for_name | file #lang/en/ui.php [02c6ce4c94c9]; legacy #6494 [02c6ce4c94c9]; workbench #1523 [02c6ce4c94c9] |  |
| 21202c2b9c8078ac | en | ui.user_avatar.user_avatar | file #lang/en/ui.php [e75e0e7cad5f]; legacy #6492 [e75e0e7cad5f]; workbench #1524 [e75e0e7cad5f] |  |
| f80544dddd099d5f | en | ui.value.value-key | file #lang/en/ui.php [2b3b0c7bf472]; workbench #1822 [2b3b0c7bf472] |  |
| c6dff7ef4215bb1a | en | ui.values.stored-values | file #lang/en/ui.php [11a9ad37c112]; workbench #1886 [11a9ad37c112] |  |
| a8bb3645db65ad8c | en | ui.values.values | file #lang/en/ui.php [53b09e104f60]; workbench #1908 [53b09e104f60] |  |
| 25bfec1d9e0b1e2a | en | ui.variables.variable-missing | file #lang/en/ui.php [e23521dfc535]; workbench #1878 [e23521dfc535] |  |
| 77bb391b4b9ed53f | en | ui.variables.variables-ok | file #lang/en/ui.php [2ec2fd24af7c]; workbench #1876 [2ec2fd24af7c] |  |
| 594928b201fe0511 | en | ui.welcome.welcome | file #lang/en/ui.php [0e2226b5235f]; legacy #6792 [0e2226b5235f]; legacy #6853 [0e2226b5235f] | multiple_records_per_source |
| 10375eeac4731d3a | en | view.components.ui.form.tab_status_dot.all_relevant_fields_are_filled | file #lang/en/view.php [84c1df363a41]; legacy #1062 [84c1df363a41]; workbench #1526 [84c1df363a41] |  |
| aea78caf8e07454a | en | view.components.ui.form.tab_status_dot.complete | file #lang/en/view.php [143b270a3260]; legacy #1093 [143b270a3260]; workbench #1527 [143b270a3260] |  |
| e48795db7a91d6fe | en | view.components.ui.form.tab_status_dot.empty | file #lang/en/view.php [c6c094bc0054]; legacy #1097 [c6c094bc0054]; workbench #1528 [c6c094bc0054] |  |
| d9428b82dd6e3579 | en | view.components.ui.form.tab_status_dot.missing_required_fields | file #lang/en/view.php [4a4121d955e7]; legacy #1068 [4a4121d955e7]; workbench #1529 [4a4121d955e7] |  |
| 0483cedff732eed0 | en | view.components.ui.form.tab_status_dot.no_relevant_field_is_filled | file #lang/en/view.php [d40a7760c581]; legacy #1058 [d40a7760c581]; workbench #1530 [d40a7760c581] |  |
| 2b28fd0c7334fdf1 | en | view.components.ui.form.tab_status_dot.partially_filled | file #lang/en/view.php [e1603e9c5775]; legacy #1095 [e1603e9c5775]; workbench #1531 [e1603e9c5775] |  |
| b761eaa593421d1d | en | view.components.ui.form.tab_status_dot.required_fields_are_missing | file #lang/en/view.php [add46aeaae9a]; legacy #1064 [add46aeaae9a]; workbench #1532 [add46aeaae9a] |  |
| 253c4b606a28458b | en | view.components.ui.form.tab_status_dot.some_fields_are_filled | file #lang/en/view.php [3c238108975d]; legacy #1060 [3c238108975d]; workbench #1533 [3c238108975d] |  |
| 3f7678ad0c803dac | en | view.components.ui.form.tab_status_dot.this_tab_contains_validation_errors | file #lang/en/view.php [d5e4fd90fdef]; legacy #1066 [d5e4fd90fdef]; workbench #1534 [d5e4fd90fdef] |  |
| 0fb8702fcbae730a | en | view.components.ui.form.tab_status_dot.validation_errors | file #lang/en/view.php [b81f8fe779b2]; legacy #1088 [b81f8fe779b2]; workbench #1535 [b81f8fe779b2] |  |

</details>

Vollständige Hashes, Optionsidentitäten, Quell-IDs und historische Evidenz stehen in der zugehörigen JSON-Datei.
