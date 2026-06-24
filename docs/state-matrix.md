Hier ist die kompakte Matrix für die tatsächlich relevanten Zustände im aktuellen Workflow.

| Zustand | Ebene | Bedeutung | Typische Ursache |
|---|---|---|---|
| `ok` | `translation_keys.status` | Ein echter Translation-Key existiert im Code und ist im Sprachbestand vorhanden. | Key wird im Code verwendet und passende Lang-Einträge existieren. |
| `missing` | `translation_keys.status` | Ein echter Translation-Key existiert im Code, aber die Übersetzung fehlt noch. | Neuer Key im Code, aber noch kein Eintrag in `lang/*` bzw. DB für den Zielbestand. |
| `obsolete` | `translation_keys.status` | Ein früher vorhandener Key existiert noch im Bestand, aber im aktuellen Code gibt es keinen passenden Translation-Key mehr. Er wird aus heutiger Sicht nicht mehr benutzt. Das ist der DB-/Key-Obsolete-Fall. | Key-Umbenennung, Feature entfernt, Code umgebaut, Namespace/Group geändert. |
| `native` | `translation_keys.status` | Im Code wurde ein Literaltext statt eines Translation-Keys gefunden. | `__('Save')` statt `__('pages.settings.save')`. |
| `dynamic` | `translation_keys.status` | Im Code wird ein dynamischer Übersetzungswert verwendet, der nicht stabil als konkreter Key auflösbar ist. | Variablen, String-Konkatenation, dynamisch gebaute Keys. |
| `invalid` | `translation_keys.status` | Der Übersetzungsaufruf ist aus Auditsicht ungültig oder leer. | Leerer Wert, kaputter oder unbrauchbarer Call. |
| `open` | `translation_keys.workflow_status` | Das Work-Item ist noch aktiv im Bearbeitungsfluss. | Noch nicht geprüft, noch nicht bewusst abgeschlossen. |
| `reviewed` | `translation_keys.workflow_status` | Das Work-Item wurde geprüft oder bewusst aus dem aktiven Arbeitskorb genommen. | Manuell reviewed, oder automatisch als historisch/superseded markiert. |
| `key` | `translation_keys.classification` | Der Eintrag repräsentiert einen echten konkreten Translation-Key. | Compare-Audit hat einen realen Key erkannt. |
| `native` | `translation_keys.classification` | Der Eintrag stammt aus Literaltext im Code. | Audit findet Text statt Key. |
| `dynamic` | `translation_keys.classification` | Der Eintrag stammt aus dynamischer Key-Erzeugung. | Audit kann keinen festen Key ableiten. |
| `invalid` | `translation_keys.classification` | Der Eintrag stammt aus einem ungültigen Übersetzungsaufruf. | Leerer oder fehlerhafter Call. |
| `vendor` | `translation_keys.classification` | Repo-spezifische Sonderklassifikation für Vendor-Übersetzungen. | Import/Abgleich aus `lang/vendor/*`. |
| `backfill_by_translation` | `translation_keys.classification` | Repo-spezifische Sonderklassifikation für rückgefüllte Einträge aus vorhandenen Übersetzungswerten. | Backfill-/Reparaturlauf auf Basis bestehender Values. |
| `ok` | `translation_values.status` | Für diese konkrete Locale gibt es einen nichtleeren verwendbaren Wert. | Wert wurde importiert oder manuell gepflegt. |
| `missing` | `translation_values.status` | Für diese konkrete Locale fehlt der Wert oder ist leer. | Noch nicht übersetzt oder bewusst geleert. |
| `obsolete` | `translation_values.status` | Der Locale-Wert gehört zu einem Key, der insgesamt obsolet ist. Das deckt nur den DB-/Key-Obsolete-Fall ab. | Key existiert noch im Bestand, aber nicht mehr im Code. |

Die wichtigste fachliche Definition bleibt:
`obsolete` bedeutet nicht “gerade unpraktisch” oder “noch nicht geprüft”, sondern: der Eintrag ist noch im Bestand vorhanden, aber es gibt im aktuellen Code keinen passenden Translation-Key mehr, daher ist er überflüssig geworden.

Zusätzlich gibt es einen zweiten fachlichen Obsolete-Befund außerhalb der aktuellen DB-Statusfelder:

| Befund | Ebene | Bedeutung | Typische Ursache |
|---|---|---|---|
| `file_obsolete` | Audit-/Dateibestand | Ein Eintrag ist noch in `lang/*` vorhanden, würde aus der aktuellen DB-/Export-Sicht aber nicht mehr geschrieben. | Altbestand in Dateien, bewusst nicht-destruktiver Export, noch nicht reviewte Drift zwischen DB und Dateibestand. |

Die wichtigste Trennung ist:
- `status`: fachlicher Maschinenzustand
- `workflow_status`: menschlicher Bearbeitungszustand
- `classification`: Art/Herkunft des Eintrags
- `translation_values.status`: Zustand je Sprache/Locale

`file_obsolete` ist bewusst kein automatischer Löschvorgang. Solche Einträge müssen zuerst reviewed/markiert werden und dürfen erst danach aus `lang/*` entfernt werden. Die DB bleibt dabei der dauerhafte History-/Statistik-Bestand.

## Counter Definitions

| Source | Counter-Name | Resulting by | Comment |
| --- | --- | --- | --- |
| Code | All | Code audit | All statically detected translation calls in application code. |
| Code | Native | Code audit | Calls containing a literal/native source text instead of a stable translation key. |
| Code | Translation Keys | Code audit | Calls containing a concrete translation key. |
| Code | Dynamic | Code audit | Calls whose key or value cannot be resolved statically. |
| Database | Overall | `translation_keys` | Persistent translation work items, including active and historical rows. |
| Files | All | `lang/{locale}` | Entries currently present in the language-file source for a locale. |

Counters shown together in the UI must be calculated from explicitly defined row sets. Table rows and counters for the same filter context must use equivalent query semantics.
