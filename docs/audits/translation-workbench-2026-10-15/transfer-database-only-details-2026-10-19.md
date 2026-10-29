# Prüfung der 122 DB-only-Kombinationen

Schreibgeschützte DB-Abfrage: 2026-10-19T09:36:12+02:00. Vergleichsbasis: 2026-10-19T07:41:38+02:00.

Alle 122 Evidenzmengen sind unverändert. Jeder aktuelle Text ist mit Quelle, Datensatz-ID, Locale, Key und Hash im zugehörigen JSON gesichert. Das ist ein gezielter Nachweis, kein vollständiges Datenbankbackup.

| Einordnung | Anzahl | Bedeutung |
| --- | ---: | --- |
| Gleicher Text, Locale anders geschrieben | 71 | Beispielsweise DB de-AT und Datei de-at. Keine automatische Normalisierung. |
| Gleicher Text in Basislocale de | 21 | Regionaler DB-Wert entspricht de. Kein Nachweis, dass die Runtime aktuell genau diesen Fallback verwendet. |
| Gleicher Text unter anderem Options-Key | 16 | Aktuelle Views verwenden den anderen Parent-Key; keine automatische Verlinkung/Umbenennung. |
| Eigenständige skalare DB-Nachweise | 14 | Sieben Keys in de/en, ohne aktive Finding-Verweise; vorerst sichern. |

## Wichtige Abgrenzungen

- Die 92 regionalen Legacy-Werte teilen sich in 71 Schreibvarianten und 21 Basislocale-Übereinstimmungen. Findings verwenden Keys, nicht automatisch eine bestimmte Locale; 75 aktive Finding-Bezüge sind deshalb kein Beleg für tatsächlich aufgerufene regionale Übersetzungen.
- Die 16 aktiven Optionswerte verwenden noch den Parent-Key mit `.documents.add_modal.document_category_options`. Die aktuellen Dokumenten-Views rufen `.documents.document_category_options` auf. Alle acht Optionsidentitäten in beiden Sprachen stimmen textlich mit dessen Sprachdateiwerten überein. Die Identitäten bleiben getrennt; die Untersuchung legt keinen gemeinsamen Besitzer fest.
- Die 14 übrigen Workbench-Werte enthalten unter anderem Test-/DEV-Bezeichnungen. Namen allein rechtfertigen keine Löschung. Alle Werte bleiben erhalten, ohne Reaktivierung oder Freigabe.
- Die Sprachdateiprüfung ist statisch. Weder Runtime-Provider noch Übersetzungs-Fallbacks wurden ausgeführt.
- Der Bericht ist kein Importauftrag und ersetzt nicht das vollständige Backup vor einem Reset.

## Optionskontext

- `resources/views/components/management/people/edit-person/sections/⚡documents.blade.php:7`
- `resources/views/components/management/people/edit-person/sections/⚡documents.blade.php:154`
- `resources/views/components/management/people/edit-person/sections/documents/⚡add-modal.blade.php:265`
- `resources/views/components/management/people/edit-person/sections/documents/⚡archive-modal.blade.php:170`

## Die 14 eigenständigen skalaren Nachweise

| Locale | Key | Workbench-ID | Text |
| --- | --- | --- | --- |
| de | management.people.edit_person.sections.documents.archive_modal.category | 1537 | Kategorie |
| de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.dev_playground | 1612 | DEV-Spielwiese |
| de | ui.assignable-through-ui | 1998 | Über die Benutzeroberfläche zuweisbar |
| de | ui.label | 1928 | Falsche Kennzeichnung |
| de | ui.label.label-testing-2 | 1931 | Etikettenprüfung 2 |
| de | ui.literal.literal-testing | 1921 | Literalprüfung |
| de | ui.literal.literal-testing-2 | 1927 | Literalprüfung 2 |
| en | management.people.edit_person.sections.documents.archive_modal.category | 1536 | category |
| en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.dev_playground | 1611 | DEV-Playground |
| en | ui.assignable-through-ui | 2002 | Assignable through UI |
| en | ui.label | 1929 | False labeling |
| en | ui.label.label-testing-2 | 1930 | Label-Testing 2 |
| en | ui.literal.literal-testing | 1920 | Literal testing |
| en | ui.literal.literal-testing-2 | 1926 | Literal testing 2 |

Textsuche in den aktuellen Bytes der 1.684 Manifestdateien (keine neuen Dateien erfasst, kein Scannerlauf):

- `management.people.edit_person.sections.documents.archive_modal.category`: 0 Texttreffer.
- `packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.dev_playground`: 0 Texttreffer.
- `ui.assignable-through-ui`: 2 Texttreffer.
  - `resources/views/components/admin/partials/role-list/⚡modal-create.blade.php:70`: {{-- i18n-native: __('ui.assignable-through-ui') --}}
  - `resources/views/components/admin/partials/role-list/⚡modal-edit.blade.php:92`: {{-- i18n-native: __('ui.assignable-through-ui') --}}
- `ui.label`: 0 Texttreffer.
- `ui.label.label-testing-2`: 0 Texttreffer.
- `ui.literal.literal-testing`: 0 Texttreffer.
- `ui.literal.literal-testing-2`: 0 Texttreffer.

Die Treffer für ui.assignable-through-ui liegen in auskommentierten i18n-native-Zeilen; kein aktiver Übersetzungsaufruf. Andere Keys wie ui.label.* werden nicht als exakter ui.label-Treffer mitgezählt.

## Vollständige Zuordnung

<details><summary>Alle 122 Kombinationen</summary>

| Locale | Key | Quelle / ID | Text | Einordnung | Identischer Datei-Nachweis |
| --- | --- | --- | --- | --- | --- |
| de | management.people.edit_person.sections.documents.add_modal.document_category_options.options.correspondence | dynamic_workbench #116 | Korrespondenz | same_text_other_option_key | lang/de/management.php → management.people.edit_person.sections.documents.document_category_options.options.correspondence |
| de | management.people.edit_person.sections.documents.add_modal.document_category_options.options.documentation | dynamic_workbench #118 | Dokumentation | same_text_other_option_key | lang/de/management.php → management.people.edit_person.sections.documents.document_category_options.options.documentation |
| de | management.people.edit_person.sections.documents.add_modal.document_category_options.options.identity | dynamic_workbench #120 | Identität | same_text_other_option_key | lang/de/management.php → management.people.edit_person.sections.documents.document_category_options.options.identity |
| de | management.people.edit_person.sections.documents.add_modal.document_category_options.options.insurance | dynamic_workbench #122 | Versicherung | same_text_other_option_key | lang/de/management.php → management.people.edit_person.sections.documents.document_category_options.options.insurance |
| de | management.people.edit_person.sections.documents.add_modal.document_category_options.options.medical | dynamic_workbench #124 | Medizin | same_text_other_option_key | lang/de/management.php → management.people.edit_person.sections.documents.document_category_options.options.medical |
| de | management.people.edit_person.sections.documents.add_modal.document_category_options.options.other | dynamic_workbench #126 | Sonstiges | same_text_other_option_key | lang/de/management.php → management.people.edit_person.sections.documents.document_category_options.options.other |
| de | management.people.edit_person.sections.documents.add_modal.document_category_options.options.residence | dynamic_workbench #128 | Wohnort | same_text_other_option_key | lang/de/management.php → management.people.edit_person.sections.documents.document_category_options.options.residence |
| de | management.people.edit_person.sections.documents.add_modal.document_category_options.options.tax | dynamic_workbench #130 | Steuern | same_text_other_option_key | lang/de/management.php → management.people.edit_person.sections.documents.document_category_options.options.tax |
| de | management.people.edit_person.sections.documents.archive_modal.category | workbench #1537 | Kategorie | separate_database_evidence |  |
| de | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.dev_playground | workbench #1612 | DEV-Spielwiese | separate_database_evidence |  |
| de | ui.assignable-through-ui | workbench #1998 | Über die Benutzeroberfläche zuweisbar | separate_database_evidence |  |
| de | ui.label | workbench #1928 | Falsche Kennzeichnung | separate_database_evidence |  |
| de | ui.label.label-testing-2 | workbench #1931 | Etikettenprüfung 2 | separate_database_evidence |  |
| de | ui.literal.literal-testing | workbench #1921 | Literalprüfung | separate_database_evidence |  |
| de | ui.literal.literal-testing-2 | workbench #1927 | Literalprüfung 2 | separate_database_evidence |  |
| de-AT | admin.permissions.actions.edit_role_permissions | legacy #383 | Rollenberechtigungen bearbeiten | same_text_locale_spelling | lang/de-at/admin.php |
| de-AT | admin.translation_list.filter.status_missing | legacy #6320 | Status: Fehlend | same_text_locale_spelling | lang/de-at/admin.php |
| de-AT | admin.translation_list.filter.status_native | legacy #3620 | Status: Native | same_text_locale_spelling | lang/en/admin.php; lang/de-at/admin.php |
| de-AT | admin.translation_list.filter.type_backfill | legacy #5892 | Typ: Rückwärts eingetragen in DB | same_text_locale_spelling | lang/de-at/admin.php |
| de-AT | admin.translation_list.filter.type_native | legacy #5914 | Typ: Originaltext | same_text_locale_spelling | lang/de-at/admin.php |
| de-AT | admin.translation_list.meta.total_keys | legacy #5773 | Gesamtzahl der Schlüssel | same_text_locale_spelling | lang/de-at/admin.php |
| de-AT | admin.translation_list.modal_edit.sub_language_values | legacy #5508 | Werte für Sprachvarianten | same_text_locale_spelling | lang/de-at/admin.php |
| de-AT | admin.translation_list.modal_history.event | legacy #5855 | Ereignis | same_text_base_de | lang/de/admin.php |
| de-AT | admin.translation_list.modal_history.events | legacy #5839 | Ereignisse | same_text_base_de | lang/de/admin.php |
| de-AT | admin.translation_list.table.history | legacy #5879 | Historie | same_text_base_de | lang/de/admin.php |
| de-AT | admin.translation_list.table.open_history | legacy #5786 | Historie öffnen | same_text_locale_spelling | lang/de-at/admin.php |
| de-AT | admin.translation_list.table.target_sub_language_values | legacy #5903 | Werte für die Sprachvaritionen der Zielsprache | same_text_locale_spelling | lang/de-at/admin.php |
| de-AT | ui.table.per_page_selector.per_page | legacy #6498 | Pro Seite | same_text_locale_spelling | lang/de-at/ui.php |
| de-BE | admin.translation_list.filter.status_missing | legacy #6321 | Status: Fehlend | same_text_locale_spelling | lang/de-be/admin.php |
| de-BE | admin.translation_list.filter.status_native | legacy #3621 | Status: Native | same_text_locale_spelling | lang/en/admin.php; lang/de-be/admin.php |
| de-BE | admin.translation_list.filter.type_backfill | legacy #5893 | Typ: Rückwärts eingetragen in DB | same_text_locale_spelling | lang/de-be/admin.php |
| de-BE | admin.translation_list.filter.type_native | legacy #5915 | Typ: Originaltext | same_text_locale_spelling | lang/de-be/admin.php |
| de-BE | admin.translation_list.meta.total_keys | legacy #5774 | Gesamtzahl der Schlüssel | same_text_locale_spelling | lang/de-be/admin.php |
| de-BE | admin.translation_list.modal_edit.sub_language_values | legacy #5509 | Werte für Sprachvarianten | same_text_locale_spelling | lang/de-be/admin.php |
| de-BE | admin.translation_list.modal_history.event | legacy #5856 | Ereignis | same_text_base_de | lang/de/admin.php |
| de-BE | admin.translation_list.modal_history.events | legacy #5840 | Ereignisse | same_text_base_de | lang/de/admin.php |
| de-BE | admin.translation_list.table.history | legacy #5880 | Historie | same_text_base_de | lang/de/admin.php |
| de-BE | admin.translation_list.table.open_history | legacy #5787 | Historie öffnen | same_text_locale_spelling | lang/de-be/admin.php |
| de-BE | admin.translation_list.table.target_sub_language_values | legacy #5904 | Werte für die Sprachvaritionen der Zielsprache | same_text_locale_spelling | lang/de-be/admin.php |
| de-BE | ui.table.per_page_selector.per_page | legacy #6499 | Pro Seite | same_text_locale_spelling | lang/de-be/ui.php |
| de-CH | admin.permissions.actions.edit_role_permissions | legacy #384 | Rollenberechtigungen bearbeiten | same_text_locale_spelling | lang/de-ch/admin.php |
| de-CH | admin.translation_list.filter.status_missing | legacy #6322 | Status: Fehlend | same_text_locale_spelling | lang/de-ch/admin.php |
| de-CH | admin.translation_list.filter.status_native | legacy #3622 | Status: Native | same_text_locale_spelling | lang/en/admin.php; lang/de-ch/admin.php |
| de-CH | admin.translation_list.filter.type_backfill | legacy #5894 | Typ: Rückwärts eingetragen in DB | same_text_locale_spelling | lang/de-ch/admin.php |
| de-CH | admin.translation_list.filter.type_native | legacy #5916 | Typ: Originaltext | same_text_locale_spelling | lang/de-ch/admin.php |
| de-CH | admin.translation_list.meta.total_keys | legacy #5775 | Gesamtzahl der Schlüssel | same_text_locale_spelling | lang/de-ch/admin.php |
| de-CH | admin.translation_list.modal_edit.sub_language_values | legacy #5510 | Werte für Sprachvarianten | same_text_locale_spelling | lang/de-ch/admin.php |
| de-CH | admin.translation_list.modal_history.event | legacy #5857 | Ereignis | same_text_base_de | lang/de/admin.php |
| de-CH | admin.translation_list.modal_history.events | legacy #5841 | Ereignisse | same_text_base_de | lang/de/admin.php |
| de-CH | admin.translation_list.table.history | legacy #5881 | Historie | same_text_base_de | lang/de/admin.php |
| de-CH | admin.translation_list.table.open_history | legacy #5788 | Historie öffnen | same_text_locale_spelling | lang/de-ch/admin.php |
| de-CH | admin.translation_list.table.target_sub_language_values | legacy #5905 | Werte für die Sprachvaritionen der Zielsprache | same_text_locale_spelling | lang/de-ch/admin.php |
| de-CH | ui.table.per_page_selector.per_page | legacy #6500 | Pro Seite | same_text_locale_spelling | lang/de-ch/ui.php |
| de-DE | admin.translation_list.filter.status_missing | legacy #6323 | Status: Fehlend | same_text_locale_spelling | lang/de-de/admin.php |
| de-DE | admin.translation_list.filter.status_native | legacy #3623 | Status: Native | same_text_locale_spelling | lang/en/admin.php; lang/de-de/admin.php |
| de-DE | admin.translation_list.filter.type_backfill | legacy #5895 | Typ: Rückwärts eingetragen in DB | same_text_locale_spelling | lang/de-de/admin.php |
| de-DE | admin.translation_list.filter.type_native | legacy #5917 | Typ: Originaltext | same_text_locale_spelling | lang/de-de/admin.php |
| de-DE | admin.translation_list.filter.workflow_history | legacy #3611 | Ablauf: Historie | same_text_locale_spelling | lang/de-de/admin.php |
| de-DE | admin.translation_list.meta.backfill | legacy #2776 | Rückwärts eingetragen | same_text_locale_spelling | lang/de-de/admin.php |
| de-DE | admin.translation_list.meta.only_problems | legacy #2787 | Nur Problemfälle | same_text_locale_spelling | lang/de-de/admin.php |
| de-DE | admin.translation_list.meta.total_keys | legacy #5776 | Gesamtzahl der Schlüssel | same_text_locale_spelling | lang/de-de/admin.php |
| de-DE | admin.translation_list.modal.key_suggestion_check | legacy #3443 | Überprüfung der vorgeschlagenen Schlüssel | same_text_locale_spelling | lang/de-de/admin.php |
| de-DE | admin.translation_list.modal_edit.sub_language_values | legacy #5511 | Werte für Sprachvarianten | same_text_locale_spelling | lang/de-de/admin.php |
| de-DE | admin.translation_list.modal_edit.usage_locations | legacy #3456 | Verwendung im Code | same_text_locale_spelling | lang/de-de/admin.php |
| de-DE | admin.translation_list.modal_history.event | legacy #5858 | Ereignis | same_text_base_de | lang/de/admin.php |
| de-DE | admin.translation_list.modal_history.events | legacy #5842 | Ereignisse | same_text_base_de | lang/de/admin.php |
| de-DE | admin.translation_list.table.history | legacy #5882 | Historie | same_text_base_de | lang/de/admin.php |
| de-DE | admin.translation_list.table.open_history | legacy #5789 | Historie öffnen | same_text_locale_spelling | lang/de-de/admin.php |
| de-DE | admin.translation_list.table.target_sub_language_values | legacy #5906 | Werte für die Sprachvaritionen der Zielsprache | same_text_locale_spelling | lang/de-de/admin.php |
| de-DE | ui.table.per_page_selector.per_page | legacy #6501 | Pro Seite | same_text_locale_spelling | lang/de-de/ui.php |
| de-IT | admin.permissions.actions.edit_role_permissions | legacy #385 | Rollenberechtigungen bearbeiten | same_text_locale_spelling | lang/de-it/admin.php |
| de-IT | admin.translation_list.filter.status_missing | legacy #6324 | Status: Fehlend | same_text_locale_spelling | lang/de-it/admin.php |
| de-IT | admin.translation_list.filter.status_native | legacy #3624 | Status: Native | same_text_locale_spelling | lang/en/admin.php; lang/de-it/admin.php |
| de-IT | admin.translation_list.filter.type_backfill | legacy #5896 | Typ: Rückwärts eingetragen in DB | same_text_locale_spelling | lang/de-it/admin.php |
| de-IT | admin.translation_list.filter.type_native | legacy #5918 | Typ: Originaltext | same_text_locale_spelling | lang/de-it/admin.php |
| de-IT | admin.translation_list.meta.total_keys | legacy #5777 | Gesamtzahl der Schlüssel | same_text_locale_spelling | lang/de-it/admin.php |
| de-IT | admin.translation_list.modal_edit.sub_language_values | legacy #5512 | Werte für Sprachvarianten | same_text_locale_spelling | lang/de-it/admin.php |
| de-IT | admin.translation_list.modal_history.event | legacy #5859 | Ereignis | same_text_base_de | lang/de/admin.php |
| de-IT | admin.translation_list.modal_history.events | legacy #5843 | Ereignisse | same_text_base_de | lang/de/admin.php |
| de-IT | admin.translation_list.table.history | legacy #5883 | Historie | same_text_base_de | lang/de/admin.php |
| de-IT | admin.translation_list.table.open_history | legacy #5790 | Historie öffnen | same_text_locale_spelling | lang/de-it/admin.php |
| de-IT | admin.translation_list.table.target_sub_language_values | legacy #5907 | Werte für die Sprachvaritionen der Zielsprache | same_text_locale_spelling | lang/de-it/admin.php |
| de-IT | ui.table.per_page_selector.per_page | legacy #6502 | Pro Seite | same_text_locale_spelling | lang/de-it/ui.php |
| de-LI | admin.translation_list.filter.status_missing | legacy #6325 | Status: Fehlend | same_text_locale_spelling | lang/de-li/admin.php |
| de-LI | admin.translation_list.filter.status_native | legacy #3625 | Status: Native | same_text_locale_spelling | lang/en/admin.php; lang/de-li/admin.php |
| de-LI | admin.translation_list.filter.type_backfill | legacy #5897 | Typ: Rückwärts eingetragen in DB | same_text_locale_spelling | lang/de-li/admin.php |
| de-LI | admin.translation_list.filter.type_native | legacy #5919 | Typ: Originaltext | same_text_locale_spelling | lang/de-li/admin.php |
| de-LI | admin.translation_list.meta.total_keys | legacy #5778 | Gesamtzahl der Schlüssel | same_text_locale_spelling | lang/de-li/admin.php |
| de-LI | admin.translation_list.modal_edit.sub_language_values | legacy #5513 | Werte für Sprachvarianten | same_text_locale_spelling | lang/de-li/admin.php |
| de-LI | admin.translation_list.modal_history.event | legacy #5860 | Ereignis | same_text_base_de | lang/de/admin.php |
| de-LI | admin.translation_list.modal_history.events | legacy #5844 | Ereignisse | same_text_base_de | lang/de/admin.php |
| de-LI | admin.translation_list.table.history | legacy #5884 | Historie | same_text_base_de | lang/de/admin.php |
| de-LI | admin.translation_list.table.open_history | legacy #5791 | Historie öffnen | same_text_locale_spelling | lang/de-li/admin.php |
| de-LI | admin.translation_list.table.target_sub_language_values | legacy #5908 | Werte für die Sprachvaritionen der Zielsprache | same_text_locale_spelling | lang/de-li/admin.php |
| de-LI | ui.table.per_page_selector.per_page | legacy #6503 | Pro Seite | same_text_locale_spelling | lang/de-li/ui.php |
| de-LU | admin.translation_list.filter.status_missing | legacy #6326 | Status: Fehlend | same_text_locale_spelling | lang/de-lu/admin.php |
| de-LU | admin.translation_list.filter.status_native | legacy #3626 | Status: Native | same_text_locale_spelling | lang/en/admin.php; lang/de-lu/admin.php |
| de-LU | admin.translation_list.filter.type_backfill | legacy #5898 | Typ: Rückwärts eingetragen in DB | same_text_locale_spelling | lang/de-lu/admin.php |
| de-LU | admin.translation_list.filter.type_native | legacy #5920 | Typ: Originaltext | same_text_locale_spelling | lang/de-lu/admin.php |
| de-LU | admin.translation_list.meta.total_keys | legacy #5779 | Gesamtzahl der Schlüssel | same_text_locale_spelling | lang/de-lu/admin.php |
| de-LU | admin.translation_list.modal_edit.sub_language_values | legacy #5514 | Werte für Sprachvarianten | same_text_locale_spelling | lang/de-lu/admin.php |
| de-LU | admin.translation_list.modal_history.event | legacy #5861 | Ereignis | same_text_base_de | lang/de/admin.php |
| de-LU | admin.translation_list.modal_history.events | legacy #5845 | Ereignisse | same_text_base_de | lang/de/admin.php |
| de-LU | admin.translation_list.table.history | legacy #5885 | Historie | same_text_base_de | lang/de/admin.php |
| de-LU | admin.translation_list.table.open_history | legacy #5792 | Historie öffnen | same_text_locale_spelling | lang/de-lu/admin.php |
| de-LU | admin.translation_list.table.target_sub_language_values | legacy #5909 | Werte für die Sprachvaritionen der Zielsprache | same_text_locale_spelling | lang/de-lu/admin.php |
| de-LU | ui.table.per_page_selector.per_page | legacy #6504 | Pro Seite | same_text_locale_spelling | lang/de-lu/ui.php |
| en | management.people.edit_person.sections.documents.add_modal.document_category_options.options.correspondence | dynamic_workbench #115 | Correspondence | same_text_other_option_key | lang/en/management.php → management.people.edit_person.sections.documents.document_category_options.options.correspondence |
| en | management.people.edit_person.sections.documents.add_modal.document_category_options.options.documentation | dynamic_workbench #117 | Documentation | same_text_other_option_key | lang/en/management.php → management.people.edit_person.sections.documents.document_category_options.options.documentation |
| en | management.people.edit_person.sections.documents.add_modal.document_category_options.options.identity | dynamic_workbench #119 | Identity | same_text_other_option_key | lang/en/management.php → management.people.edit_person.sections.documents.document_category_options.options.identity |
| en | management.people.edit_person.sections.documents.add_modal.document_category_options.options.insurance | dynamic_workbench #121 | Insurance | same_text_other_option_key | lang/en/management.php → management.people.edit_person.sections.documents.document_category_options.options.insurance |
| en | management.people.edit_person.sections.documents.add_modal.document_category_options.options.medical | dynamic_workbench #123 | Medical | same_text_other_option_key | lang/en/management.php → management.people.edit_person.sections.documents.document_category_options.options.medical |
| en | management.people.edit_person.sections.documents.add_modal.document_category_options.options.other | dynamic_workbench #125 | Other | same_text_other_option_key | lang/en/management.php → management.people.edit_person.sections.documents.document_category_options.options.other |
| en | management.people.edit_person.sections.documents.add_modal.document_category_options.options.residence | dynamic_workbench #127 | Residence | same_text_other_option_key | lang/en/management.php → management.people.edit_person.sections.documents.document_category_options.options.residence |
| en | management.people.edit_person.sections.documents.add_modal.document_category_options.options.tax | dynamic_workbench #129 | Tax | same_text_other_option_key | lang/en/management.php → management.people.edit_person.sections.documents.document_category_options.options.tax |
| en | management.people.edit_person.sections.documents.archive_modal.category | workbench #1536 | category | separate_database_evidence |  |
| en | packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.dev_playground | workbench #1611 | DEV-Playground | separate_database_evidence |  |
| en | ui.assignable-through-ui | workbench #2002 | Assignable through UI | separate_database_evidence |  |
| en | ui.label | workbench #1929 | False labeling | separate_database_evidence |  |
| en | ui.label.label-testing-2 | workbench #1930 | Label-Testing 2 | separate_database_evidence |  |
| en | ui.literal.literal-testing | workbench #1920 | Literal testing | separate_database_evidence |  |
| en | ui.literal.literal-testing-2 | workbench #1926 | Literal testing 2 | separate_database_evidence |  |

</details>

## Nächster Schritt

Die noch nicht wertweise erfassten dynamischen Altformate und Paketdateien auf ergänzende Inhalte prüfen. Danach ist die vollständige Übernahmesicherung mit einer Generalprobe auf Kopie vorzubereiten. Keine der hier dokumentierten Übereinstimmungen löst automatisch eine Zusammenführung aus.
