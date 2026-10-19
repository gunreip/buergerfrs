# Datenbankbestand und Verantwortlichkeiten

Snapshot: `2026-10-15T03:47:40+02:00`. PostgreSQL-Abfragen in `REPEATABLE READ READ ONLY`. Größen aus `pg_total_relation_size`, einschließlich Indizes und TOAST; nicht reine Nutzdaten und keine Aussage über Bloat. Das vollständige Schema mit Constraints, Indizes, Statusverteilungen und Prüf-SQL steht in [database.json](database.json).

| Tabelle | Zeilen | MiB | Rolle | Inhalt | Schreiber |
| --- | ---: | ---: | --- | --- | --- |
| `translation_audit_events` | 857,724 | 543.98 | Legacy-Historie | Alte Ereignisse, besonders moved | Legacy-Sync und Review |
| `translation_keys` | 14,205 | 9.88 | Legacy | Altes Key-/Statusinventar | audit-sync, dynamic-sync, Legacy-Editor |
| `translation_lang_ballast_decisions` | 2 | 0.12 | Legacy-Review | Sprachdatei-Aufräumentscheidungen | Ballast-UI/Apply |
| `translation_languages` | 2 | 0.04 | Konfiguration | Aktivierung und Sortierung der Sprachen | Sprachverwaltung |
| `translation_usage_audit_decision_usages` | 10 | 0.14 | Legacy-Review | Ausgewählte Vorkommen einer Entscheidung | Usage-Audit-UI/Apply |
| `translation_usage_audit_decisions` | 4 | 0.11 | Legacy-Review | Redaktionelle Usage-Entscheidungen | Usage-Audit-UI |
| `translation_usages` | 14,075 | 6.95 | Legacy | Alte Fundstellen, eigener Fingerprint | audit-sync, dynamic-sync |
| `translation_values` | 8,284 | 1.30 | Legacy | Alte Sprachwerte je Key-ID/Locale | audit-sync (außer manual), Legacy-Editor |
| `translation_workbench_duplicate_candidates` | 109 | 0.20 | Abgeleitet alt | Duplikatgruppen aus Entries | detect-duplicates |
| `translation_workbench_dynamic_key_values` | 200 | 0.20 | Foundation | Dynamische Sprachwerte je Key/Value-Key/Locale | DynamicEditor/TranslationWriter |
| `translation_workbench_dynamic_source_candidates` | 121 | 0.32 | Foundation-Reviewhilfe | Kandidaten für dynamische Zuordnung | discover/resolve/runtime + Review |
| `translation_workbench_dynamic_source_values` | 247 | 0.17 | Foundation-Beobachtung | Beobachtete Optionswerte | classify/resolve/runtime; classify löscht fehlende Werte |
| `translation_workbench_dynamic_sources` | 96 | 0.40 | Foundation-Beobachtung | Dynamische Quellen und Klassifikation | classify, resolve, RuntimeDynamicTranslationCollector |
| `translation_workbench_dynamic_values` | 43 | 0.13 | Alte Workbench | Dynamische Übersetzungswerte am Entry | dynamic discovery/Alt-Editor |
| `translation_workbench_entries` | 3,730 | 7.45 | Alte Workbench | Text-/Funktionsbasierte Aggregate statt eindeutiger Fundstellen | scan, import-existing, dynamic discovery, Alt-Editor |
| `translation_workbench_event_types` | 52 | 0.10 | Konfiguration | Timeline-Typen und Darstellung | Migration/Recorder-Katalog |
| `translation_workbench_events` | 1,340,120 | 2286.62 | Alte Workbench-Historie | Entry-Änderungen/Beobachtungen | scan, import-existing, discovery |
| `translation_workbench_findings` | 23,215 | 41.20 | Foundation | Beobachteter Aufruf, Roh-Ausdruck, Zustand | sync-foundation, Review/Code-Update-Folgepfade |
| `translation_workbench_key_findings` | 25,815 | 8.62 | Foundation | Key/Finding-Beziehungen mit Status | sync-foundation, Review/Bulk |
| `translation_workbench_key_inventory` | 1,090 | 0.99 | Projektion | Zusammengefasstes Key-Inventar | inventory-keys --sync |
| `translation_workbench_key_values` | 0 | 0.05 | Foundation ungenutzt im Bestand | Sprachwerttabelle je Key-ID, derzeit leer | Noch in Models, Inventar und Raw-Data-Lesern referenziert |
| `translation_workbench_keys` | 14,940 | 15.79 | Foundation | Key-Kandidat, Zuordnung, Key-Review | sync-foundation, Review/Bulk/Runtime-Collector |
| `translation_workbench_lang_values` | 2,216 | 1.91 | Gemischte Verantwortung | Dateibeobachtung UND aktuelle editierte skalare Werte | Importer, TranslationWriter, Rekey/Cleanup/Recovery |
| `translation_workbench_occurrences` | 10,350 | 10.70 | Alte Workbench | Fundstellen des Entry-Modells | scan |
| `translation_workbench_option_discoveries` | 25 | 0.34 | Übergangsmodell | Statisch entdeckte Optionsquellen; verbindet beide Modelle | discover-dynamic-options |
| `translation_workbench_pipeline_run_steps` | 148 | 0.26 | Betriebsprotokoll | Schrittstatus und Metriken | PipelineRunTracker |
| `translation_workbench_pipeline_runs` | 4 | 0.11 | Betriebsprotokoll | UI-Pipelineanforderung und Laufzustand | PipelineRunTracker/UI |
| `translation_workbench_reviews` | 1,320 | 2.04 | Fachhistorie | Entscheidungen, Akteur, alter/neuer Zustand | Review-Writer |
| `translation_workbench_shared_key_candidates` | 193 | 0.29 | Foundation-Reviewhilfe | Vorschläge aus früher bestätigten Shared-Entscheidungen | detect-shared-key-candidates |
| `translation_workbench_source_files` | 818 | 0.86 | Foundation | Dateiidentität/Metadaten | sync-foundation |
| `translation_workbench_timeline_chains` | 1,098 | 1.57 | Projektion | Aggregierte Ketten/ID-Listen für TW-Graph | collect-timeline-chains --sync |
| `translation_workbench_timeline_events` | 173,212 | 180.28 | Fach-/Technikhistorie | Finding/Key/Review/Publication-Ereignisse | TimelineRecorder aus vielen Schreibern |
| `translation_workbench_values` | 952 | 0.84 | Alte Workbench | Native Quell-/Optionswerte | import-existing, dynamic discovery |

## Integrität

Alle 47 geprüften tatsächlichen Fremdschlüsselbeziehungen sind ohne verwaiste referenzierte IDs. Das belegt strukturelle Referenzintegrität, nicht die fachliche Eindeutigkeit oder Aktualität einer Zuordnung. String-Verknüpfungen wie `lang_values.translation_key` sind davon nicht abgedeckt.

| Prüfung | Anzahl |
| --- | ---: |
| `active_findings_without_active_relation` | 0 |
| `active_findings_multiple_active_relations` | 663 |
| `active_relations_to_obsolete_findings` | 12 |
| `active_relations_to_obsolete_keys` | 0 |
| `reviewed_nonobsolete_keys_without_active_findings` | 11 |
| `duplicate_nonobsolete_translation_keys` | 119 |
| `duplicate_nonobsolete_reviewed_keys` | 12 |
| `duplicate_lang_locale_namespace_key` | 0 |
| `active_values_without_nonobsolete_key` | 2 |
| `active_values_without_reviewed_key` | 1096 |
| `active_key_findings_owner_differs_from_code` | 1 |
| `legacy_workbench_common_values` | 1427 |
| `legacy_workbench_different_common_values` | 202 |

## Einordnung der Auffälligkeiten

- 663 aktive Findings haben mehr als eine aktive Beziehung. Davon besitzen 298 keine explizit benannte Key-Zuordnung, 363 genau einen unterschiedlichen expliziten Namen und **zwei Findings zwei unterschiedliche Namen**. Kandidaten neben einer bestätigten Zuordnung sind nicht pauschal ein Fehler. Zwei konkurrierende Namen müssen konkret geklärt werden.
- 119 mehrfach vorkommende nicht-obsolete Key-Namen, darunter 12 Gruppen mit mehreren reviewed-Key-Zeilen: Das bestehende Modell erlaubt bei Shared-Key-Entscheidungen mehrere Key-Datensätze mit gleichem Namen. Nicht blind deduplizieren; zuerst Kandidatenidentität und kanonischen Übersetzungsschlüssel auseinanderhalten.
- 12 aktive Beziehungen zeigen auf obsolete Findings; dafür fehlt eine konsistente Statusgrenze.
- Zwei aktive Sprachwerte (`ui.label`, de/en) haben keinen nicht-obsoleten Key-Besitzer. Ein Export kann solche Zeilen als Pruning-Kandidaten behandeln; vor Bereinigung Verwendung prüfen.
- 1.096 aktive Sprachwerte haben keinen reviewed-Key-Besitzer. Das sind nicht automatisch verlorene Übersetzungen: darunter importierte Bestandswerte. Es zeigt jedoch, dass „active“ und „freigegeben“ unterschiedliche Dinge sind.
- 202 von 1.427 über Key und Locale zusammenführbaren Legacy-/Workbench-Wertepaaren unterscheiden sich. Der Vergleich umfasst auch ältere Legacy-Zustände; er ist eine Abstimmungsliste, kein Auftrag, eine Seite pauschal zu überschreiben.
- Eine zunächst gefundene Code-/Besitzerabweichung betrifft Finding 5093 / Key 4507 (`ui.save` → `ui.button.save.save`). Der reale aktuelle View enthält dort inzwischen eine Save-Komponente und kommentierten Altcode. Der Kopien-Sync setzt den alten Finding-/Key-Zustand obsolete. Das ist keine belegte Zerstörung einer noch aktuellen Korrektur.

Weitere konkrete Gruppen und IDs stehen in [integrity-details.json](integrity-details.json).

## Warum die Historie so groß ist

Die alte Workbench enthält 1.018.387 `changed`- und 255.399 `existing_translation_value_changed`-Ereignisse. In den letzten 10.000 `changed`-Ereignissen entfallen 9.851 auf die vier häufigsten Feldkombinationen aus source_fingerprint, meta, suggested_key und Namespace/Gruppe. Das ist eine Stichprobe, keine Klassifikation der gesamten Historie.

Der isolierte Wiederholungstest erklärt zwei Mechanismen: identische Texte in verschiedenen Dateien teilen einen Entry-Fingerprint, dessen quellenabhängige Attribute pro Fundstelle hin- und herspringen; `import-existing` behandelt schon ein verändertes last_seen_at als Änderung. Die Legacy-Audithistorie enthält zusätzlich 819.766 `moved`-Ereignisse. Deren Häufigkeit ist mit der ortsübergreifenden Fallback-Suche in `SyncTranslationAudits::syncUsage()` vereinbar; nicht jedes historische Ereignis wurde einzeln neu bewertet.

Diese Tabellen werden nicht gelöscht oder gekürzt. Eine spätere Archivierung benötigt eine gesonderte Entscheidung und darf die TW-Graph-/Review-Provenienz nicht abschneiden.

## Empfehlung für das Datenmodell

Foundation-Findings, Beziehungen, Reviews und nachvollziehbare Historie erhalten. Die drei Generationen nicht dauerhaft parallel schreiben. Alte Tabellen zunächst als lesbaren, unveränderten Altbestand behandeln; erst nach nachgewiesener Übernahme ihrer noch exklusiven Werte/Entscheidungen migrieren.

Neu abzugrenzen sind: unveränderliche Erfassung samt Beobachtungen pro Scan-Run; kanonische Key-Zuordnung; Sprachwertrevision/Entwurf; Review auf genau diese Revision; veröffentlichter Wertstand und Publication-Run; Datei-Import als Vergleichsbeobachtung. `lang_values` kann diese Verantwortungen in der jetzigen Form nicht verlässlich gleichzeitig erfüllen.

Inventar, Shared-Kandidaten, dynamische Beobachtungen und Timeline-Ketten sind Projektionen bzw. Beobachtungsdaten. Sie dürfen rekonstruierbar sein, aber nicht unbemerkt redaktionelle Freigaben oder die unveränderliche Historie ersetzen. Die leere `key_values`-Tabelle alleine rechtfertigt noch keine Entfernung, da Leser und Schema-Verträge darauf verweisen.

---

## Neubewertung vor einem möglichen Neustart — 2026-10-19

**Ergebnis:** Die Foundation ist eine brauchbare fachliche Grundlage, aber ein unveränderter Reset würde mehrere strukturelle Unklarheiten übernehmen. Empfohlen ist eine gezielte Überarbeitung der Zuständigkeiten und Beziehungen vor dem Neustart, kein pauschaler Neubau aller Funktionen. Dies ist ein Entwurf, keine Freigabe zur Migration oder Löschung.

### Prüfstand und Grenzen

Die Zahlen und Integritätsbefunde oben sind ausdrücklich der historische Snapshot vom **15.10.**, keine aktuellen Zeilenzahlen. Diese Ergänzung untersucht den aktuellen Arbeitsbaum einschließlich seiner noch nicht committeten Änderungen. Es wurden keine Produktionsabfragen, Scannerläufe oder Schreiboperationen ausgeführt.

Die Migrationen definieren **33 Tabellen mit `translation_*`-Präfix**. Zugehörige Modelle, Schema-Dateien und PHP-/Blade-Fundstellen sind in [schema-references-2026-10-19.json](schema-references-2026-10-19.json) erfasst. Reproduzierbar mit `python3 docs/audits/translation-workbench-2026-10-15/inventory-schema-references.py`.

Die Referenzliste ist eine Suchhilfe, kein vollständiger Aufrufgraph: Kommentare zählen mit; dynamisch zusammengesetzte Tabellennamen können fehlen. Nicht in `_old`/`-old` zu liegen beweist keine aktive Registrierung. Modellnamen werden aus expliziten Tabellenangaben und einfachen Eloquent-Namenskonventionen zugeordnet. Die zentralen unten genannten Pfade wurden zusätzlich direkt gelesen. Vor einer Entfernung sind sämtliche Zugriffe und Fremdschlüssel der betroffenen Tabelle zu bearbeiten.

### Entscheidungsmatrix aller 33 Tabellen

`TW_` steht in dieser Tabelle ausschließlich als Schreibabkürzung für `translation_workbench_`, nicht für einen neuen Datenbanknamen.

| Tabelle | Einordnung / Vorschlag für das Zielmodell | Vorher zu klären |
| --- | --- | --- |
| `translation_keys` | Legacy-Keybestand archivieren | Exklusive Schlüssel/Metadaten sichern; verbliebene Verwaltungs-/Vergleichszugriffe ablösen |
| `translation_usages` | Legacy-Fundstellen archivieren | Keine konkurrierende Erfassung mehr; historische Quellenbelege erhalten |
| `translation_values` | Legacy-Werte kontrolliert übernehmen, dann archivieren | Unveröffentlichte und abweichende Übersetzungen nicht auslassen |
| `translation_audit_events` | Historisches Archiv | Nicht als neue fachliche Timeline importieren |
| `translation_languages` | Konfiguration erhalten / Zuständigkeit prüfen | Aktivierung und Locale-Verwaltung nicht mit Erfassungsdaten zurücksetzen |
| `translation_usage_audit_decisions` | Alte Entscheidungen archivieren | Noch wirksame Entscheidungen gesondert übernehmen |
| `translation_usage_audit_decision_usages` | Zusammen mit den alten Entscheidungen archivieren | Zusammengehörige Belege nicht trennen |
| `translation_lang_ballast_decisions` | Alte Entscheidungen archivieren | Aktuelle Auswirkungen auf Sprachdateien prüfen |
| `TW_entries` | Altes aggregiertes Entry-Modell archivieren | Historischer Wertevergleich und Options-Fremdschlüssel greifen noch darauf zurück |
| `TW_occurrences` | Alte Entry-Fundstellen archivieren | Keine zweite aktuelle Finding-Quelle |
| `TW_events` | Altes Entry-Ereignisarchiv | Bei Bedarf separat lesbar halten |
| `TW_values` | Altwerte sichern und archivieren | Übernahme exklusiver Werte prüfen |
| `TW_dynamic_values` | Alte dynamische Werte sichern und archivieren | Optionen, native Labels und Übersetzungen abgleichen |
| `TW_duplicate_candidates` | Alte Ableitung stilllegen | Neue Kandidaten aus aktuellen Findings bilden |
| `TW_option_discoveries` | Nicht einfach entfernen; Übergangsmodell entkoppeln | `matched_entry_id` verweist auf alte Entries; aktuelle Dynamic-Services verwenden Beobachtungen weiter |
| `TW_source_files` | Dateiidentität behalten | Path, Hash und Scan-Beobachtung klar voneinander abgrenzen |
| `TW_findings` | Erfassung beibehalten, Versionierung ausdrücklich modellieren | Rohdaten/SuggestedKey unveränderlich; Vorgänger/Nachfolger nicht nur als ungesicherte JSON-IDs |
| `TW_keys` | Grundlegend vereinfachen | Kanonische Key-Identität von Finding-Vorschlägen und Klassifikation trennen |
| `TW_key_findings` | Verknüpfung behalten und präzisieren | Kandidaten sind keine bestätigten Zuordnungen; eindeutiger aktueller Link pro statischer Fundstelle |
| `TW_key_values` | Ausmusterungskandidat | Im alten Snapshot leer; aktuelle Schutz-, Vergleichs- und Inventarpfade referenzieren die Tabelle weiterhin |
| `TW_lang_values` | Verantwortlichkeiten neu schneiden | Aktueller skalarer Arbeitswert ist zugleich Import-/Dateibestand; besser getrennte Beobachtung und Wertrevision |
| `TW_dynamic_key_values` | Dynamische Übersetzungswerte behalten | Einheitliche Revision/Freigabe mit skalaren Werten; Option-Identität erhalten |
| `TW_dynamic_sources` | Dynamische Quellen behalten, Zuordnung ordnen | Direkte Key-/Finding-IDs und Kandidatenbeziehungen haben unterschiedliche Rollen |
| `TW_dynamic_source_values` | Beobachtete Optionen behalten | Kein Ersatz für freigegebene Übersetzungswerte |
| `TW_dynamic_source_candidates` | Prüfbare Kandidaten/Entscheidungen behalten | Explizit von bestätigter Quellenanbindung trennen |
| `TW_reviews` | Entscheidungen und Freigaben behalten | Konkrete Finding-/Key-/Wertrevision adressieren; Zustand nicht nur aus beliebigen JSON-Feldern rekonstruieren |
| `TW_timeline_events` | Fachhistorie behalten | Wiederholte Beobachtung ist keine fachliche Änderung; Löschkaskaden für Historie überdenken |
| `TW_event_types` | Darstellungs-/Ereigniskatalog erhalten | Nicht als Scan-Daten zurücksetzen |
| `TW_shared_key_candidates` | Ableitbare Vorschläge, Entscheidungen gesondert erhalten | Gleichlautender Text darf niemals automatisch zur Zusammenlegung führen |
| `TW_key_inventory` | Projektion behalten oder als Abfrage ersetzen | Cleanup-Entscheidungen aus Metadaten vor Neuerzeugung sichern |
| `TW_timeline_chains` | TW-Graph-Projektion behalten | Muss aus autoritativer Fachhistorie neu aufbaubar sein |
| `TW_pipeline_runs` | Betriebsprotokoll behalten/neu beginnen | Scan-Umfang, Erfolg, Abbruch und konkrete Beobachtung müssen nachvollziehbar sein |
| `TW_pipeline_run_steps` | Schrittprotokoll behalten/neu beginnen | Keine zweite fachliche Timeline daraus machen |

### Aktuelle Zugriffe und konkrete Schwachstellen

1. **Drei Generationen, aber nicht drei gleich aktive Scanner.** Der ServiceProvider registriert die Foundation-Commands. `old-entries` und `raw-data` sind dort auskommentiert; alte Commands liegen unter `src/_old/Console`. `ProjectTranslations` führt den Legacy-Export ausdrücklich nur als Dry-run-Vergleich aus. Die frühere Schreiberliste oben ist deshalb nicht unbesehen auf heute übertragbar. Altbestand ist dennoch nicht abgekoppelt: `TranslationWorkbenchHistoricalValueComparison`, die aktuellen Entries und dynamische Übergangspfade haben weiterhin Referenzen.

2. **Key-Vorschlag und Key-Identität.** `create_translation_workbench_foundation_tables` enthält `suggested_key` sowohl an Findings als auch Keys. Am Key ist der Fingerprint unique, der tatsächliche `translation_key` dagegen nicht. `FoundationSyncer::syncKey()` bildet die Identität unter Einbeziehung des Vorschlags. Damit können verschiedene Ursprungsvorschläge zu mehreren Records mit demselben späteren Schlüssel führen. Der aktuelle Schutz des Finding-SuggestedKey verhindert dessen Überschreiben; er löst diese Modellfrage nicht vollständig.

   **Zielvorschlag:** SuggestedKey und dessen Herkunft bleiben beim Finding. Ein noch nicht zugeordnetes Finding benötigt keinen fingierten endgültigen Key. Ein kanonischer aktiver Key wird anhand seines vollständigen Namens einschließlich eines gegebenenfalls unterstützten Paket-Namespace eindeutig geführt. Mehrere Findings dürfen ihn benutzen. Entwürfe/Zuordnungskandidaten sind separate Zustände und dürfen nicht denselben eindeutigen Key-Namen mehrfach besetzen. Wie ein Rename historisch versioniert wird, muss vor der konkreten Unique-Constraint feststehen.

3. **Zwei Arten der Werteanbindung.** `TranslationWorkbenchTranslationWriter` schreibt skalare Werte in `lang_values` über den Schlüsseltext; dynamische Werte in `dynamic_key_values` über `key_id` und `value_key`. `key_values` wird im aktuellen Writer nicht verwendet, ist aber z. B. in `SourceRepair`, `KeyConsolidation`, `KeySeparation`, `UnusedKeyRetirement` und Inventarprüfungen enthalten. Eine leere Tabelle ist deshalb kein Freibrief zum Droppen.

   **Zielvorschlag:** Skalare Arbeitswerte ebenfalls an die kanonische Key-Identität binden. Dynamische Optionswerte dürfen ihre zusätzliche Option-Dimension behalten. Einheitliche Freigabe-/Versionsregeln sind wichtiger als das Zusammenpressen beider Formen in eine Tabelle.

4. **Import und Redaktion.** `TranslationWorkbenchLangValueImporter` und `TranslationWorkbenchTranslationWriter` verwenden dieselbe skalare Tabelle. Der Importer weist heute widersprüchliche vorhandene Werte zurück; er überschreibt sie nicht pauschal. Dennoch stehen Dateibeobachtung (`source_path`, `source_hash`, `scan_count`) und redaktioneller Arbeitswert zusammen. Das erzeugt zusätzliche Abgleichslogik.

   **Zielvorschlag:** Dateibeobachtungen, bearbeitbare Übersetzungen und veröffentlichte Stände unterscheiden. Ein Import aktualisiert die Beobachtung, nicht den Entwurf. Eine explizite Übernahme erzeugt eine neue Wertrevision.

5. **Revision ist noch kein Revisionsarchiv.** Die Migration vom 16.10. erhöht `value_revision` per Datenbanktrigger. `TranslationWorkbenchValueApproval` speichert Freigabe-Snapshots und Fingerprints in `reviews`. Das schützt vor veralteter Freigabe, ist aber keine eigene unveränderliche Tabelle sämtlicher Wertrevisionen. Eine Neufassung muss diese bereits implementierten Schutzfunktionen bewahren; sie darf sie nicht durch einen einfachen Boolean `approved` ersetzen.

6. **Verknüpfungs-Eindeutigkeit.** `key_findings` ist nur über `(key_id, finding_id, relation_type)` eindeutig. Das verhindert nicht mehrere konkurrierende aktive bestätigte Zuordnungen für ein Finding. Vorschläge und bestätigte Verwendung benötigen getrennte, überprüfbare Regeln. Dynamische Quellen-/Option-Beziehungen dürfen dabei nicht versehentlich auf die Kardinalität eines statischen Literals reduziert werden.

7. **Archivbeziehungen.** Die aktuelle Finding-Signatur ist seit 17.10. nur für nicht-obsolete Findings eindeutig. Das ist sinnvoll und sollte erhalten bleiben. Nachfolgebeziehungen liegen derzeit in Metadaten (`TranslationWorkbenchFindingVersioner`). Für einen Neuaufbau bieten sich explizite selbstreferenzielle IDs und Transaktionsregeln an. Alte Findings bleiben archiviert; ein neuer Auftritt reaktiviert sie nicht.

8. **Historie und Delete-Kaskaden.** Die Foundation-Migration definiert Cascade-Deletes von Findings/Keys auf Links, Reviews und Timeline-Ereignisse. Das widerspricht einem unabhängigen, langlebigen Archiv, falls später physisch gelöscht wird. Im Zielmodell entweder physische Löschung fachlicher Identitäten ausschließen oder Referenzen/Snapshots so gestalten, dass das Protokoll erhalten bleibt. Kein pauschales Abschalten aller Kaskaden ohne Lebenszyklusentscheidung.

9. **Mehr als Datenbank.** `TranslationWorkbenchPublicationRunner` führt wiederaufnehmbare Journale unter `storage/app/private/translation-workbench/publications` und Receipts/Abschlussentscheidungen in `reviews`. Ein DB-Reset bei erhaltenen alten Journals wäre inkonsistent. Archivierung muss DB, Quellcode, `/lang`, Konfiguration und diese Journale als zusammengehörigen Stand behandeln. Alte Runs dürfen im neuen Bestand nicht weiterlaufen. Der Session-/Manifest-Mechanismus in `TranslationWorkbenchScanSession` gehört ebenfalls in den Entwurf eines vollständigen Scan-Nachweises; Pipeline-Status allein ist kein Beleg für jede geprüfte Datei.

### Fachlicher Zielaufbau (noch keine Migration)

- **Erfassung:** Datei → konkrete versionierte Fundstelle; unveränderlicher Rohbefund und Ursprungsvorschlag. Scan-Beobachtung bestätigt Anwesenheit separat.
- **Zuordnung:** Finding → bestätigte Verwendung eines kanonischen TranslationKey; Kandidaten separat. Gleichlautende Literale bleiben unabhängige Findings.
- **Bearbeitung:** Key → Sprache → Wertrevision; bei dynamischen Quellen zusätzlich Option. Locale-Aktivierung und Fallback-Kaskade bleiben Konfiguration.
- **Freigabe/Publikation:** Entscheidung referenziert exakt die geprüfte Revision; Publication-Run dokumentiert tatsächlich geschriebene Dateien und ihre Hashes. Ein technischer Wiederanlauf muss dieselben Belege benutzen.
- **Historie:** fachliche Ereignisse, Akteur und Bezüge; Betriebsprotokolle für wiederholte Scan-Beobachtung. TW-Graph erhält daraus Projektionen und entscheidet nicht über den fachlichen Bestand.

### Empfohlene Umsetzungsschritte und Abnahmekriterien

1. Das Zielmodell mit konkreten Spalten, Kardinalitäten, Unique-/Foreign-Key-Regeln und Statusübergängen ausarbeiten. Insbesondere Key-Identität, Wertrevisionen, bestätigte Zuordnung und Scan-Beobachtung verbindlich trennen.
2. Eine Übernahmematrix pro Tabelle/Spalte und Datei erstellen. Exklusive Entwürfe, dynamische Werte, Locale-Konfiguration und wirksame Entscheidungen benötigen eine explizite Behandlung. Aktuelle Mengen erst dann read-only neu erfassen; den Snapshot vom 15.10. nicht als Reset-Grundlage verwenden.
3. Den neuen Aufbau isoliert implementieren und auf einer Kopie durchspielen. Kein destruktives Umschreiben alter Migrationen in der bestehenden Installation; Archivzugriff auf den alten Stand separat vorsehen.
4. Erstscan und unveränderter Zweitscan prüfen: keine zusätzlichen fachlichen Identitäten/Änderungsereignisse; Beobachtungszähler/Run-Protokolle dürfen sich ändern. Zusätzlich Verschieben, Entfernen, erneutes Auftauchen, Kopieren, gleiche Literale mit getrennten Keys sowie Shared-Key-Zuordnung prüfen.
5. Speichern, Freigeben, Teilpublikation/Wiederanlauf, dynamische Optionen, Sprachvarianten und TW-Graph gegen denselben Bestand testen. Freigegebene Werte müssen zu den konkreten Revisionen passen; Dateien dürfen keinen nicht freigegebenen Entwurf enthalten.
6. Erst nach geprüftem Übernahmebericht den konkreten Produktionswechsel vorlegen. Bis dahin bleiben Tabellen, Daten und bestehende UI unverändert.

**Keine aktuelle Löschliste:** Archivkandidaten sind keine bereits als sicher entfernbar nachgewiesenen Tabellen. Eine komplette dynamische Aufrufanalyse, frische Produktionszählungen und die spaltenweise Übernahmematrix sind noch offen. Die vorliegende Bestandsaufnahme grenzt diese Arbeiten gezielt ein.

### Erfassungsvertrag für vorhandene Literale und translationKeys — 2026-10-19

Dieser abgestimmte Teil des Zielmodells ermöglicht einen Neustart bei bereits übersetztem Quellcode. Er ist **noch keine Änderung am produktiven Schema oder Scanner**. Die camelCase-Bezeichnungen sind die fachlichen Feldnamen; die vorgeschlagenen Datenbankspalten folgen der bisherigen snake_case-Konvention. Es sind keine parallel zu pflegenden Aliase vorgesehen.

| Fachliches Feld | Vorgeschlagene DB-Spalte / Typ | Inhalt / Verantwortung |
| --- | --- | --- |
| `suggestedKey` | `suggested_key`, nullable text | Ursprungsvorschlag der Erfassung. Bei einem bereits im Code stehenden Key kann dieser übernommen werden; die Herkunft muss das ausdrücklich kennzeichnen. Danach unveränderlich. |
| `rawSourceCodeLiteral` | `raw_source_code_literal`, nullable text | Tatsächlich statisch ermittelbarer Argumentinhalt der Fundstelle, unabhängig davon, ob Text oder translationKey. Kein Übersetzungs-Lookup, keine Ersatzwerte. |
| `rawLiteral` | `raw_literal`, nullable text | Nur als tatsächlicher Literaltext klassifizierter Quellcodeinhalt. Bei einem Key oder nicht eindeutig statisch ermittelbarem Inhalt `NULL`. |
| `suggestedLiteral` | `suggested_literal`, nullable text | Aus einer belegbaren vorhandenen Übersetzung gewonnener Textvorschlag. Kein aus dem Key-Namen erzeugter Text und keine Behauptung über das historische Ursprungsliteral. |
| Vollständiger Quellausdruck | `raw_expression`, nullable text | Der unveränderte Ausdruck aus dem Quellcode, einschließlich Syntax/Argumenten. Ergänzt den extrahierten Inhalt und bleibt als Beleg erhalten. |

Alle Felder beziehen sich auf **diese Finding-Version zum Erfassungszeitpunkt**, nicht auf einen später überschreibbaren Live-Zustand. Source-Pfad, Position, Datei-/Ausdruckshash und Erfassungslauf gehören zum Nachweis. Wiederholte unveränderte Scans aktualisieren die Beobachtung, nicht diese Rohdaten. Ein Nachfolger bei geänderter Fundstelle ersetzt den Archivbeleg nicht. Ein Positionsnachfolger darf den ursprünglichen SuggestedKey mit dokumentierter Herkunft fortführen; eine tatsächlich neue Fundstelle erhält ihre eigene Erfassung.

`rawSourceCodeLiteral` meint bei `__('Cancel')` den Inhalt `Cancel`; die exakte Schreibweise `__('Cancel')` bleibt in `raw_expression`. Escaping, Quotes und Original-Whitespace dürfen in diesem Beleg nicht normalisiert werden. Such-/Vergleichsnormalisierung ist eine getrennte Ableitung. Ein echtes leeres Literal `''` ist nicht gleich `NULL`. Platzhalter wie `:name` bleiben Teil des Textes und werden bei der Erfassung nicht substituiert.

#### Beispiele und Grenzen

| Quellcode | rawSourceCodeLiteral | rawLiteral | suggestedLiteral |
| --- | --- | --- | --- |
| `__('Cancel')` | `Cancel` | `Cancel` | `NULL` |
| `__('Hello :name', ['name' => $name])` | `Hello :name` | `Hello :name` | `NULL` |
| `__('ui.button.cancel')`, englischer Wert belegt | `ui.button.cancel` | `NULL` | Gesicherter englischer Wert, z. B. `Cancel` |
| `__('ui.button.unknown')`, kein Wert gefunden | `ui.button.unknown` | `NULL` | `NULL` |
| `__($runtimeKey)`, statisch nicht auflösbar | `NULL` | `NULL` | `NULL`; Ausdruck bleibt in raw_expression |
| `__('')` | leerer String | leerer String | `NULL` |

Ein dynamischer Ausdruck wird nicht zur Laufzeit ausgeführt, nur um die Felder zu füllen. Statisch nachweisbare Variablenwerte können erfasst werden, benötigen aber einen Verweis auf ihre Definition/Ableitung. Die Abwesenheit einer Übersetzung beweist nicht, dass ein key-artiger String ein Literal ist. Umgekehrt beweist ein Punkt im Text nicht, dass es ein Key ist. Bei uneindeutiger Klassifikation bleibt der sichere Argumentinhalt erhalten, `rawLiteral` aber unbekannt; der Klassifikationsgrund muss prüfbar sein.

#### Herkunft von suggestedLiteral

Ein nicht-NULL-Vorschlag benötigt einen gespeicherten Herkunftsbeleg. Vorgeschlagen ist ein strukturiertes `suggested_literal_provenance` mit mindestens:

- verwendetem translationKey und exakter Locale;
- Quellenart (`lang_file` oder gespeicherte Übersetzungsrevision);
- Dateipfad und Hash bei einer Datei beziehungsweise stabiler Datensatz-/Revisionsreferenz bei einem DB-Wert;
- Ermittlungszeitpunkt und verwendeter Ermittlungsregel;
- bei einer ausdrücklich erlaubten Sprachfallback-Auflösung der tatsächlich durchlaufenen Kette.

Der gespeicherte Text bleibt auch dann nachvollziehbar, wenn die externe Quelle später geändert wird. Eine Dateiquelle muss deshalb im zugehörigen Baseline-Archiv erhalten bleiben; ein Hash allein rekonstruiert keinen Inhalt. Eine alte Datenbank-ID ohne Archivbezug reicht nach einem Reset ebenfalls nicht als Herkunft.

Die konfigurierte Quellsprache (derzeit `en`) ist die erste Wahl. Es darf kein impliziter Fallback der aktuellen UI-Sprache stattfinden. Ein Übersetzungsaufruf, der bei fehlendem Eintrag den Key selbst zurückliefert, ist kein Nachweis eines vorhandenen Textes. Existenz ist separat zu prüfen; ein tatsächlich gespeicherter Wert darf durchaus genauso lauten wie sein Key. Arrays/Container sind keine skalaren Literalvorschläge und werden nicht zu Strings gecastet. Sprachdateien müssen über die vorgesehenen sicheren Lesewege ausgewertet werden, nicht durch Ausführung beliebigen Quellcodes.

**Noch festzulegende Detailregel:** Wenn DB und Sprachdatei abweichen, ist die maßgebliche Quelle explizit zu wählen. Für die Bestandsübernahme wird vorgeschlagen, widersprüchliche Kandidaten als solchen Prüfbedarf zu dokumentieren und `suggestedLiteral` vorerst `NULL` zu lassen. Identische Kandidaten sind kein inhaltlicher Konflikt. Unveröffentlichte Entwürfe dürfen nicht stillschweigend als veröffentlichter Quelltext gelten. Zusätzliche Sprachen, Quellenpriorität und spätere Anreicherung werden vor Implementierung festgelegt.

Ein bei Ersterfassung fehlender Vorschlag bleibt in diesem Snapshot `NULL`. Eine spätere Auflösung kann eine gesonderte nachvollziehbare Anreicherung liefern; sie darf nicht rückwirkend einen vermeintlichen Erstbefund erzeugen. Die bearbeitbare Übersetzung bleibt unabhängig davon im Werte-/Revisionsmodell.

#### Abgleich mit dem heutigen Code und Übernahmeregeln

| Heutiges Feld / Pfad | Befund | Regel für die Neufassung |
| --- | --- | --- |
| `DiscoveredTranslation.rawExpression` → findings.raw_expression | Vollständiger erfasster Ausdruck | Als Quellbeleg behalten; bei Übernahme gegen den Baseline-Quellcode prüfen |
| `literalText` → findings.literal_text | Scanner setzt bei Literal-Erfassung den extrahierten Wert | Kandidat für rawLiteral, nur aus bestätigter aktueller Erfassung übernehmen |
| `translationKey` → findings.found_translation_key | Im Code erfasster Key | Mit rawLiteral zusammen mögliche Quelle für rawSourceCodeLiteral; nicht Current-/Existing-Key aus späterer Bearbeitung verwenden |
| `literalTextSuggested` → findings.literal_text_suggested | `TranslationFindingNormalizer::literalTextSuggestedFromTranslationKey()` nimmt das letzte Punktsegment und ersetzt Bindestriche/Unterstriche durch Leerzeichen | **Kein Übersetzungsnachweis. Nicht blind nach suggestedLiteral kopieren.** Neu aus belegbaren Werten ermitteln, sonst NULL |
| `suggestedKey` + suggested_key_source | Vorschlag plus Kennzeichnung `literal`/`existing_key_at_source` | Herkunft behalten; beim Neustart keine historische Literalerzeugung behaupten |
| `looksLikeTranslationKey()` | Aktuell heuristische Prüfung auf Zeichenmuster und Punkt | Klassifikation explizit dokumentieren; nicht mit sicherem Auflösungsnachweis gleichsetzen |

Direkt geprüfte Quellen: `src/Scanner/DiscoveredTranslation.php`, `src/Scanner/TranslationWorkbenchScanner.php` (extractLiteralTranslationCalls), `src/Scanner/TranslationFindingNormalizer.php` (literalTextSuggestedFromTranslationKey/looksLikeTranslationKey), `src/Foundation/TranslationWorkbenchFoundationSyncer.php` (Finding-Attribute), jeweils im Workbench-Package.

Für einen neuen Baseline-Scan ist der aktuelle Quellcode maßgeblich. Alte Daten können Belege oder Übernahmekandidaten liefern, aber fehlende Rohdaten werden nicht aus aktuellen Übersetzungen rekonstruiert. Aus einer vorhandenen Translation entsteht ausschließlich suggestedLiteral, niemals rawLiteral.

#### Abnahmefälle für die spätere Implementierung

1. Literal, vorhandener Key, fehlender Key und dynamischer Ausdruck ergeben die oben definierten Werte.
2. Placeholder, leere Strings, Unicode und mehrzeilige Literale bleiben ohne inhaltliche Umformung erhalten; der Quellausdruck bleibt exakt nachprüfbar.
3. Fehlender Übersetzungseintrag, Container oder widersprüchliche Quellen erzeugen keinen erfundenen Textvorschlag.
4. Ein gespeicherter Übersetzungswert gleich dem Key wird bei belegter Existenz korrekt erkannt.
5. Zweitscan, geänderte Übersetzung und anderer zugeordneter Key überschreiben weder Rohbefund noch ursprünglichen Vorschlag.
6. Positionsnachfolger und tatsächlich neue Fundstelle behalten unterscheidbare Erfassungs-/Herkunftsbelege.
7. Die UI unterscheidet sichtbar „aus Quellcode“, „aus Übersetzung abgeleitet“ und „nicht ermittelbar“; eine Übersetzung wird niemals als historisches Quellliteral ausgegeben.

## Konkreter relationaler Zielentwurf — 2026-10-19

**Status:** Entwurf zur Umsetzungsvorbereitung. Die fachlichen Regeln zu getrennten Findings/Keys, unveränderlichen Rohdaten, NULL bei unbekannter Herkunft und archivierten Finding-Versionen sind abgestimmt. Die folgenden Tabellenaufteilungen, Spaltennamen und Constraints sind technische Vorschläge; sie sind noch nicht implementiert. Bestehende Funktionen werden nicht allein aufgrund dieses Entwurfs entfernt. `TW_` bleibt hier eine Abkürzung für `translation_workbench_`.

### 1. Erfassung: Datei, Scan und Finding-Version

| Vorgeschlagene Tabelle | Wesentliche Spalten | Regeln |
| --- | --- | --- |
| `TW_source_files` | `id`, `path`, `source_type`, Zeitstempel | Eindeutiger normalisierter Projektpfad. Kein Rückschluss auf identische Datei allein aufgrund gleichen Inhalts. Eine erkannte Dateiumbenennung benötigt einen eigenen belegten Übergang. |
| `TW_scan_runs` | `id`, `pipeline_run_id` nullable, `status`, `started_at`, `finished_at`, `scope_snapshot`, `scope_hash`, `scanner_version`, `error_summary` | Jeder Standalone-Scan und Pipeline-Scan erhält eine Identität. Status: running/completed/failed/aborted. Ein unvollständiger Lauf darf keine Abwesenheit bestätigen. |
| `TW_scan_files` | `id`, `scan_run_id`, `source_file_id`, `content_hash` nullable, `status`, `error` nullable | Unique `(scan_run_id, source_file_id)`. Unterscheidet erfolgreich gelesen, nachweislich fehlend und Fehler. Der Run-Scope dokumentiert Ausschlüsse; nicht gescannt bedeutet nicht gelöscht. |
| `TW_findings` | `id`, `source_file_id`, `captured_scan_id`, `predecessor_id` nullable, `source_line`, `source_column`, `source_signature`, `source_hash`, `kind`, `function_name`, `raw_expression`, die vier abgestimmten Textfelder, Herkunftsbelege, `status`, `retired_at`, `retired_reason` | Rohfelder sind unveränderlich. Status active/obsolete; obsolete wird nicht reaktiviert. Unique aktuelle source_signature (nur active); gleicher Ausdruck an unterschiedlichen Positionen ist erlaubt. `predecessor_id` unique soweit gesetzt, FK auf findings. Nachfolger lässt sich darüber abfragen, kein zusätzliches separat zu pflegendes successor_id. |
| `TW_scan_findings` | `scan_file_id`, `finding_id`, `observed_at` | Unique `(scan_file_id, finding_id)`. Bestätigung einer bestehenden Finding-Version ohne Änderung ihrer Rohfelder. Last-seen/Scan-count sind daraus ableitbar; falls als Cache gespeichert, nicht autoritativ. |

Die eindeutige Nachfolgerbeziehung beschreibt einen belegten Umzug, keine Kopie und keine bloße Textgleichheit. Zyklen sowie ein Nachfolger an einer bereits abgeschlossenen Kette werden transaktional ausgeschlossen. Bei mehreren plausiblen Zuordnungen gibt es keine automatische Auswahl. Ein früher verschwundenes, später neu auftauchendes Literal ist eine neue Erfassung; ein möglicher historischer Zusammenhang kann separat als Hinweis dokumentiert werden.

`captured_scan_id` und `suggested_key_origin_finding_id` (optional bei belegter Fortführung eines Ursprungsvorschlags) unterscheiden Erfassung dieser Version und Herkunft des Vorschlags. Der vollständige Herkunftsbeleg für suggestedLiteral folgt dem vorherigen Erfassungsvertrag. Eine neue unbekannte Information wird nicht aus einem späteren Zustand rückdatiert.

**Abgrenzung zu Pipeline-Tabellen:** pipeline_runs/run_steps bleiben Betriebskoordination auch für Import, Review-Prüfung und Export. scan_runs beschreibt einen konkreten Erfassungsstand. Gemeinsame IDs/Referenzen vermeiden doppelte Durchführung; der Scan wird nicht zusätzlich erneut ausgeführt, um seine Protokolltabellen zu füllen.

### 2. TranslationKey und seine Verwendung

| Vorgeschlagene Tabelle | Wesentliche Spalten | Regeln |
| --- | --- | --- |
| `TW_keys` | `id`, `status`, `created_at` | Stabile fachliche Identität eines Übersetzungsschlüssels; kein Finding-Eigentümer, kein zweiter SuggestedKey. |
| `TW_key_names` | `id`, `key_id`, `full_name`, `package_namespace` nullable, `file_group`, `nested_path`, `valid_from`, `valid_to` nullable | Eine aktuelle Namensversion pro Key; ein aktueller vollständiger Name pro Namensraum. Ein Rename schließt die alte Version und erzeugt die neue. Alte Namen bleiben historische Belege, keine automatischen Laufzeit-Aliase. Zerlegte Felder werden aus full_name konsistent abgeleitet. |
| `TW_key_findings` | `id`, `finding_id`, `key_id`, `valid_from`, `valid_to` nullable, `decision_id` nullable, `source` | Nur bestätigte Verwendungen. Für statische Literal-/Key-Findings höchstens ein aktueller Link; mehrere Findings dürfen denselben Key verwenden. Kein Finding muss vor einer Zuordnungsentscheidung bereits einen Key haben. |
| `TW_assignment_candidates` | `id`, `finding_id`, `candidate_key_id` nullable, `proposed_key_name` nullable, `reason`, `status`, `evidence` | Vorschlag statt bestätigter Verwendung. Kandidat verweist entweder auf vorhandenen Key oder vorgeschlagenen Namen; keine gefälschte Key-Identität für jeden Scanner-Vorschlag. Eine Entscheidung kann ihn annehmen oder ablehnen. |

Die Namenshistorie verhindert, dass ein Rename sämtliche alten Quellen-/Publikationsbelege semantisch umdeutet. Gespeicherter Name und tatsächlich im Quellcode gefundener Name können während einer noch nicht ausgeführten Code-Aktualisierung abweichen; das ist ein sichtbarer Workflow-Zustand, kein Anlass, rawSourceCodeLiteral anzupassen.

Der vollständige Key-Name wird nicht aus dem aktuellen Finding-Pfad abgeleitet. Unterschiedliche Findings mit identischem Literal dürfen ausdrücklich verschiedene Keys verwenden. SharedCandidate ist eine Hilfe, kein Unique-Constraint auf Literaltext. Die abgestimmte Markierung berücksichtigt mindestens zwei aktive gleichlautende Literal-Fundstellen mit unterschiedlichen Ursprungsvorschlägen; sinkt die Zahl unter zwei, entfällt sie. Aus Übersetzungen abgeleitete suggestedLiteral werden nicht stillschweigend mit echten Roh-Literalen gleichgesetzt.

Die Vorgabe, skalare Sprachdatei-Werte als verschachtelte Einträge zu führen, bleibt erhalten. Ein ungeeigneter Root-Key aus vorhandenem Code darf erfasst und als Fehler angezeigt werden; keine automatische Umbenennung und keine Verhinderung seiner Erfassung. Die Publikationsfreigabe prüft die erlaubte Namensstruktur.

**Noch konkret festzulegen:** Darf ein historisch stillgelegter Key-Name später eine neue Key-Identität bekommen? Vorgeschlagen ist eine explizite UI-Entscheidung statt automatischer Wiederbelegung. Für Namespaces, JSON-Sprachdateien und Paketübersetzungen muss die tatsächliche Projektunterstützung vor Implementierung des Unique-Index vollständig aufgenommen werden.

### 3. Übersetzungswerte, Revisionen und Dateibeobachtung

| Vorgeschlagene Tabelle | Wesentliche Spalten | Regeln |
| --- | --- | --- |
| `TW_value_slots` | `id`, `key_id`, `locale`, `kind` (scalar/option), `option_identity` nullable, `current_revision_id` nullable | Ein Slot je Key/Locale für scalar; je Key/Option/Locale für option. Separate partielle Unique-Indizes verhindern NULL-bedingte Mehrdeutigkeit. FK-/Service-Prüfung bindet current_revision_id an denselben Slot. |
| `TW_value_revisions` | `id`, `slot_id`, `revision_number`, `value`, `origin`, `actor_id` nullable, `created_at`, `source_evidence` | Unveränderliche Textrevision. Unique `(slot_id, revision_number)`. Änderungen erzeugen eine Revision, nicht ein Überschreiben alter Werte. Herkunft unterscheidet UI, explizite Bestandsübernahme und andere erlaubte Schreibwege. |
| `TW_lang_observations` | `id`, `import_run_id`, `file_path`, `file_hash`, `locale`, `full_key_name`, `value_type`, `observed_value`, `observed_at` | Snapshot des tatsächlich gelesenen Sprachdateibestands. Kein redaktioneller Arbeitswert. Container werden als solche erkannt, nicht als skalare Übersetzungen freigegeben. Ein Import ersetzt keine Wertrevision. |

Die gemeinsame Slot-/Revisionsform ist ein Vorschlag, keine Pflicht, dynamische Daten mit Gewalt in skalare Felder zu pressen. Option-Identitäten müssen verlustfrei und eindeutig bleiben (einschließlich bisher unterstützter strukturierter Optionen). Falls die Untersuchung dieser Formate eine getrennte Value-Tabelle erfordert, bleiben Revision/Freigabe dennoch demselben fachlichen Vertrag unterworfen.

**Keine Fallback-Kopien:** Ein fehlender de-AT-Wert erzeugt nicht automatisch eine kopierte deutsche Revision. Locale-Aktivierung, Elternsprache und Quellsprache bleiben zentrale Konfiguration. Die tatsächlich verwendete Fallback-Auflösung ist Laufzeitverhalten; ein Publication-/Review-Snapshot dokumentiert bei Bedarf die damals verwendete Konfiguration. `is_source_locale`, `parent_locale` usw. müssen nicht redundant an jeder aktuellen Übersetzungszeile gepflegt werden.

Ein Import-Run wird als eigener beobachtbarer Lauf an die vorhandene Pipeline-/Betriebsprotokollierung angebunden; seine genaue physische Ablage ist mit Scan-/Pipeline-Runs abzustimmen. Ein nachgewiesen vorhandener leerer Übersetzungsstring ist von einem fehlenden Wert zu unterscheiden; ob leer freigegeben werden darf, ist eine explizite Validierungsregel.

### 4. Entscheidungen, Veröffentlichung und Historie

| Bereich | Vorschlag | Wesentliche Bedingung |
| --- | --- | --- |
| Entscheidungen | `TW_reviews` als Entscheidungsprotokoll weiterführen; typisierte Bezugsspalten auf Finding, Key, Wertrevision je Entscheidungsart | Freigabe betrifft exakt eine Revision und den geprüften Kontext, nicht bloß den aktuellen Key. Typ-/Bezugskonsistenz prüfen. |
| Veröffentlichung | `TW_publication_runs` und `TW_publication_items` mit Run-Zustand, geprüftem Key-Namensstand, Wertrevision, Zieldatei und Vorher-/Nachher-Hashes | Ein Item kann die konkrete Freigabe referenzieren. Status prepared/writing/complete/interrupted/closed dokumentiert den tatsächlichen Vorgang. |
| Recovery-Journal | Vorhandenes Dateijournal nicht vorschnell entfernen | DB-Transaktion und Dateischreiben sind nicht atomar. Journale bleiben bis zum nachgewiesenen Recovery-Konzept erforderlich; Run-ID und Manifest müssen eindeutig zusammenpassen. |
| Code-Aktualisierung | Eigenständiger geplanter/ausgeführter Vorgang mit Finding-Version, geprüftem Quellhash, Ziel-Key-Namensversion und Ergebnis | Keine Key-Ersetzung allein durch Speichern einer Zuordnung. Historischer Rohbefund bleibt erhalten; neuer Quellstand bekommt belegte Folgeerfassung. |
| Timeline | Fachliche Ereignisse referenzieren Entscheidungen und Übergänge; erforderliche Snapshots für dauerhafte Darstellung | Unveränderter Scan erzeugt keinen Finding-changed-Eintrag. Reviews und Timeline dürfen unterschiedliche Aufgaben haben, aber keine widersprüchlichen aktuellen Zustände führen. |

Ein neuer Entwurf darf neben der letzten veröffentlichten Revision existieren. Deshalb sind „Entwurf“, „freigegeben“ und „veröffentlicht“ **keine drei gegenseitig ausschließenden Statuswerte am Key**. Der aktuelle Entwurf, seine konkrete Freigabe und der tatsächlich veröffentlichte Stand sind getrennte Bezüge. Eine spätere Änderung entwertet nicht rückwirkend die historische Freigabe einer alten Revision, autorisiert aber auch nicht die neue Revision.

Der konkrete Code-Update-Übergang ist vor Implementierung mit der bisherigen Source-Versionierung abzugleichen. Der heutige spezielle Übergang innerhalb eines Findings darf nicht unbemerkt als Ausnahme vom vereinbarten unveränderlichen Rohbefund übernommen werden.

### 5. Dynamische Quellen und ableitbare Daten

`dynamic_sources`, beobachtete Optionswerte und bestätigte Quellenanbindungen bleiben fachlich erforderlich. Die heutige Brücke `option_discoveries.matched_entry_id` zum alten Entry-Modell soll entfallen; eine Beobachtung verweist künftig auf die neue Finding-/Scan-Identität oder eine ausdrücklich eigenständige Laufzeitquelle. Quellenkandidaten und tatsächliche Anbindung werden getrennt geführt. Eine fehlende statische Fundstelle darf eine legitime, registrierte Runtime-Quelle nicht automatisch löschen.

`key_inventory`, `shared_key_candidates` und `timeline_chains` sind Projektionen beziehungsweise Vorschlagshilfen. Sie können beibehalten oder neu aufgebaut werden, besitzen aber keine exklusive Übersetzung und keine ausschließlich dort gespeicherte Bearbeitungsentscheidung. Bestehende Entscheidungen in ihren Metadaten müssen bei einer Migration zuerst in das Entscheidungsmodell übernommen werden. Der TW-Graph konsumiert diese Daten, schreibt aber keine fachlichen Identitäten um.

### 6. Verbindliche Transaktionsgrenzen im Zielentwurf

| Vorgang | Zusammengehörige Änderung | Darf nicht passieren |
| --- | --- | --- |
| Unveränderter Scan | Run-/Dateibeobachtung und Finding-Bestätigung | Neue Finding-/Key-Identität oder erfundene fachliche Änderung |
| Belegter Umzug | Vorgänger archivieren, Nachfolger erzeugen, bestätigte Verwendung fortführen, Historie | Zwei aktuelle Versionen derselben eindeutig zugeordneten Fundstelle |
| Nachweislich entfernt | Finding archivieren und aktuellen Verwendungslink schließen | Schlüssel/Werte löschen, obwohl andere Findings sie verwenden |
| Key zuordnen | Kandidatenentscheidung, bisherigen Link schließen, neuen Link eröffnen, Historie | Findings zusammenlegen oder deren raw/suggested-Felder überschreiben |
| Key umbenennen | Namensversion wechseln, Prüfung auf Namenskonflikte, Folgearbeiten sichtbar machen | Alte Quellen- oder Publikationssnapshots auf den neuen Namen umschreiben |
| Übersetzung speichern | Neue Revision + aktueller Slot-Bezug + Historie | Sprachdatei automatisch veröffentlichen oder Original-Literal ändern |
| Freigeben | Entscheidung für geprüfte Revision/Prüfkontext | Parallele Änderung unbemerkt mitfreigeben |
| Publizieren | Validierter Plan, Journal, Dateioperationen, Receipts | DB-Erfolg melden, obwohl Dateischreiben fehlgeschlagen ist |

Die Datenbank erzwingt Identität und Referenzintegrität (FK/Unique/Checks). Services prüfen semantische Regeln und schreiben gemeinsam innerhalb von Transaktionen; UI und Commands benutzen dieselben Services. Nebenläufige Zuordnung/Rename/Freigabe muss unter Sperren oder versionsgebundenen Vergleichsbedingungen geprüft werden. Ein einmaliger UI-Check allein genügt nicht.

### 7. Umfang und nächster Umsetzungsschritt

Dieser Entwurf beschreibt sämtliche fachlichen Kernbereiche, aber keine künstlich vorgegebene Mindestzahl an Tabellen. Das Ausmisten betrifft vor allem doppelte Zuständigkeiten, unnötige Kopien und alte Schreibwege; für klare Revisionen und Nachweise können zugleich neue Tabellen erforderlich sein.

Als nächstes ist eine **spaltenweise Übernahme- und Ablösematrix** zu erstellen: bestehende Spalte/Quelle → Ziel oder Archiv → zugehöriger aktiver Leser/Schreiber → Nachweis, dass kein exklusiver Wert verloren geht. Danach folgt ein isoliertes Migrations-/Erfassungsmodell samt Abnahmetests. Es wird noch kein produktiver Reset, keine Tabellenumbenennung und kein neuer paralleler produktiver Writer eingeführt.

## Übernahme, Ablösung und neue Arbeitsdokumentation — 2026-10-19

### Verbindliche Präzisierung: Timeline/History beginnt bei null

**Neue Vorgabe des Nutzers:** Die neue Timeline übernimmt keinerlei alte Timeline-/History-Einträge. Sie soll die fachliche Arbeit am neuen Bestand nachvollziehbar machen: **wer hat was wann und warum getan?** Sie soll nicht das historische Entwicklungs-/Reparaturgeschehen in den neuen Datenbestand fortschreiben.

Diese Festlegung präzisiert die früheren Empfehlungen in diesem Dokument: „Historie behalten“ bedeutet für den Neustart ausschließlich **externes Backup/Archiv des alten Gesamtsystems**, nicht Import der alten Ereignisse in die neue Workbench. Ein solches Backup wird nicht automatisch im neuen TW-Graph eingebunden. Die fachliche Herkunft eines übernommenen Werts kann einen Archivbeleg besitzen, ohne alte Timeline-Einträge zu referenzieren oder zu importieren.

Nicht übernommen werden alte Ereignis-IDs, Finding-/Key-Historienketten, Review-Zeilen, Pipeline-Läufe, Timeline-Chains und alte Publication-Receipts als angeblich neue Entscheidungen. Ein alter User-/Zeitstempel darf nicht als Autor/Zeitpunkt einer neu erzeugten Revision erscheinen. Frühere Entscheidungen können Prüfmaterial für die Übernahme sein; ihre fortgeltende Wirkung muss durch eine **neue belegte Bestandsentscheidung** hergestellt werden, nicht durch ein Replay.

Neue Ereignisse dürfen erst tatsächliche Vorgänge im neuen Bestand dokumentieren: z. B. „aus vorhandenem Quellcode erfasst“ oder „veröffentlichte Übersetzung als Ausgangsstand übernommen“. Das ist keine Behauptung, der Text sei neu verfasst, gerade erst freigegeben oder gerade erst veröffentlicht worden. Der neue Erstscan legt keine fiktive Vorgeschichte an.

### Übernahmeaktionen und Nachweise

- **NEU ERFASSEN:** aktuellen Quellcode beziehungsweise aktuelle Datei-/Runtime-Beobachtung erfassen; keine alte Identität als neue Wahrheit übernehmen.
- **GEPRÜFT ÜBERNEHMEN:** konkreten Inhalt samt belegbarer Herkunft übernehmen; bei Widerspruch keine stille Auswahl.
- **NEU ABLEITEN:** aus dem neuen autoritativen Bestand berechnen.
- **NUR ARCHIV:** im vollständigen alten Backup erhalten, nicht in das neue operative Modell übertragen.
- **KONFIGURATION ERHALTEN:** aktive Einstellungen gesondert sichern und kontrolliert zuordnen.

Ein technischer Übernahmebericht enthält Quellarchiv-ID, Quelltabelle/-ID oder Datei/Hash, Ziel-ID, Inhaltsvergleich und Übernahmeentscheidung. Diese Zuordnung liegt im Migrationsnachweis, **nicht als fachliche Abhängigkeit von alten IDs** im neuen Modell. Neue IDs dürfen zufällig denselben Zahlenwert haben; daraus darf keine Identität abgeleitet werden. Alte Links/Bookmarks werden nicht stillschweigend auf einen zufälligen neuen Datensatz aufgelöst.

### Spaltenweise Kernmatrix: Erfassung und Zuordnung

Die Matrix gruppiert Spalten mit derselben Übernahmeregel. Sie ist eine Implementierungsvorlage, noch kein ausführbares Migrationsmapping. Unbekannte zusätzliche Felder werden nicht automatisch verworfen oder übertragen; sie blockieren die Freigabe der betreffenden Übernahmegruppe bis zur Bewertung. `TW_` steht weiterhin für den vollständigen Workbench-Präfix.

| Quelle / Spalten | Aktion und Ziel | Betroffene aktive Pfade / notwendige Ablösung |
| --- | --- | --- |
| `TW_source_files.id`, `path`, `source_type`, `extension` | NEU ERFASSEN aus konfiguriertem Scan-Umfang; neue Dateiidentitäten | Scanner, FoundationSyncer, SourceRepair/SourceReconciliation, RawDataNew |
| source_files: `status`, `first_seen_at`, `last_seen_at`, `scan_count`, `meta`, Path-Segmente | NEU ERFASSEN/ABLEITEN; neue Laufzeiten, Scope und Hashbelege; alte Metadaten NUR ARCHIV | ScanSession, PipelineContext und FoundationSyncer dürfen keinen alten Cache übernehmen |
| `TW_findings.id`, `source_file_id`, Signaturen/Fingerprints, `source_line`, `source_column`, `function_name`, `kind` | NEU ERFASSEN; neue Finding-Identität und geprüfte Klassifikation | Scanner/Normalizer und Matching-Pfade auf neue Erfassungsverträge ausrichten |
| findings: `raw_expression`, `literal_text`, `found_translation_key` | NEU ERFASSEN → raw_expression, rawSourceCodeLiteral, rawLiteral; aktuelle Quelle entscheidet | FoundationSyncer; Quelltext-/Code-Update-Planer müssen dieselbe Finding-Version verwenden |
| findings: `literal_text_suggested` | NUR ARCHIV als alter heuristischer Vorschlag; suggestedLiteral neu aus belegbarer Übersetzung ermitteln, sonst NULL | TranslationFindingNormalizer; neue Auflösung ohne Key-Segment-Heuristik |
| findings: `suggested_key`, Ursprungshinweise in `meta` | NEU ERFASSEN → neuer Baseline-Ursprungsvorschlag samt Herkunft; bestehender Code-Key ist kein ursprüngliches Literal | SuggestedKeyFactory, Scanner, Key-Modal; keine Behauptung eines historischen Erstscans |
| findings: `existing_key`, Namespace-/Group-/Path-/Scope-/Segmentfelder | Alte Werte NUR ARCHIV; erforderliche Codeklassifikation neu ableiten; kein zweiter autoritativer aktueller Key | Entries/Review-Views und Syncer auf Finding-Befund versus bestätigte Key-Verwendung trennen |
| findings: `status`, Zeitstempel/Zähler, `meta.predecessor_finding_id`, `successor_finding_id`, `root_finding_id` | NUR ARCHIV; im neuen Bestand keine Vorgängerbezüge auf alte Findings | FindingVersioner, Timeline-Modal, Key-Sources und TW-Graph-Projektion |
| findings: Kandidaten-/Dynamic-Felder und sonstiges `meta` | NEU ABLEITEN aus aktueller Quelle; belegte manuelle Klassifikation als Übernahmekandidat separat prüfen | ClassifyDynamicValues, DynamicOptions/SourceLink-Services; keine ungeprüfte Rücksetzung legitimer dynamischer Quellen |
| `TW_keys.id`, `fingerprint`, `suggested_key` | NUR ARCHIV; neue kanonische Identität ohne Finding-Vorschlag als Schlüsselidentität | FoundationSyncer::syncKey, KeyReviewWriter, KeyConsolidation/Separation |
| keys: `translation_key` und Namenssegmente | GEPRÜFT ÜBERNEHMEN beziehungsweise aus aktuellem Code/Sprachbestand erfassen → kanonischer Key + erste Namensversion | Unterschiedliche alte Key-IDs mit gleichem Namen werden nicht als mehrere neue kanonische Keys importiert; Werte-/Typkonflikte vorher prüfen |
| keys: `key_type`, UI-/Dynamic-/Multi-Flags und Review-Flags | Klassifikation neu prüfen; konsistente notwendige Information GEPRÜFT ÜBERNEHMEN; alte Freigabe-Flags nicht kopieren | Editor, Klassifikation und PublicationPreview; keine automatische Änderung von UI/dynamic aufgrund Namenspräfix allein |
| keys: `status`, `review_status`, `reviewed_at`, `reviewed_by_user_id`, sonstige Metadaten | Neue Zustände aus neuer Bestandsentscheidung; historische Reviewdaten NUR ARCHIV | Keine erfundene neue Freigabe eines übernommenen Entwurfs |
| `TW_key_findings`: sämtliche IDs, `relation_type`, `status`, `meta`, Zeitstempel | Alte Links NUR ARCHIV. Neue Links aus aktuellen Code-Keys oder neu bestätigter Zuordnung; alte Literal-Zuordnungen höchstens belegte Prüfkandidaten | Assignment-Services, SourceRepair, Bulk-Writer und UI auf bestätigte Verwendung versus Kandidat ausrichten |

**Baseline-Ausnahme nur hinsichtlich Beginn, nicht Identität:** Ein bereits im Code stehender eindeutig identifizierter translationKey darf beim Erstscan unmittelbar als beobachtete Verwendung verknüpft werden. Das ist keine rückwirkende redaktionelle Entscheidung und kein Nachweis einer vollständigen Übersetzung. Nicht auflösbare/ungültige Keys werden sichtbar als solche erfasst, nicht still korrigiert.

### Spaltenweise Kernmatrix: Werte, Quellen und Konfiguration

| Quelle / Spalten | Aktion und Ziel | Nachweis / betroffene Pfade |
| --- | --- | --- |
| `/lang/*`: Key-Pfad, Locale, Wert/Typ | GEPRÜFT ÜBERNEHMEN als beobachteter veröffentlichter Ausgangsstand plus erforderliche initiale Wertrevision; Dateien zunächst unverändert | Gesicherter Dateistand mit Hash; LangValueImporter/Exporter auf Beobachtung und Revision trennen |
| `TW_lang_values`: `value`, `locale`, `translation_key`, `namespace`, `lang_key`, `value_type` | Mit Dateien und übrigen Wertquellen vergleichen. Identische Werte einmal übernehmen; abweichende oder nur in DB vorhandene Werte als getrennte Entwurfs-/Konfliktkandidaten sichern | TranslationWriter, HistoricalValueComparison, Rekey- und Publication-Pfade; keine pauschale „Datei gewinnt“-Regel |
| lang_values: `value_revision`, Review-/Statusangaben, Zeitstempel | NUR ARCHIV als damaliger Zustand; neue Revision beginnt im neuen Bestand, keine Übernahme alter Freigabe | ValueApproval und PublicationPreview müssen neue Revisions-IDs prüfen |
| lang_values: `source_path`, `source_hash`, `scan_count`, `first_seen_at`, `last_seen_at` | Neue Dateibeobachtung statt redaktioneller Wahrheitsquelle; alte Werte NUR ARCHIV | Aktueller Hash wird unabhängig neu ermittelt |
| lang_values: `is_source_locale`, `locale_role`, `main_locale`, `parent_locale` | NEU ABLEITEN aus gesicherter aktiver Locale-Konfiguration; nicht pro Wert blind kopieren | EditorLocales, Resolver-/Fallback-Verhalten und Publikationskontext |
| `TW_dynamic_key_values`: `key_id`, `value_key`, `locale`, `value`, `native_label` | GEPRÜFT ÜBERNEHMEN unter neuem kanonischem Key und unveränderter Option-Identität; Übersetzungswert und beobachtetes natives Label unterscheiden | DynamicEditor/TranslationWriter, DynamicOptions und Exporter |
| dynamic_key_values: `status`, `source`, `meta`, `value_revision`, Review-/Zeitangaben | Inhaltliche Herkunft als neuer Übernahmebeleg; alte Zustände/Freigaben NUR ARCHIV; exklusive Informationen in meta vorher klassifizieren | Keine implizite Veröffentlichung durch Übernahme |
| `TW_key_values`: `key_id`, `locale`, `value`, alle Zusatzfelder | Vor Neustart aktuell prüfen, auch wenn früher leer. Eventuelle Inhalte als Werte-Kandidaten sichern; Tabelle als heutige alternative Schreibquelle ablösen | SourceRepair, KeyConsolidation/Separation, Retirement, Inventar und Raw-Data-Zugriffe anpassen |
| `translation_values`, `TW_values`, `TW_dynamic_values` | Inhalte vollständig in Übernahmevergleich einbeziehen; alte Tabellen NUR ARCHIV | Keine exklusiven DB-Entwürfe oder Optionswerte verlieren |
| `TW_option_discoveries`: `matched_entry_id`, Quellen-/Optionspayload, suggested-/scope-Felder | Aktuelle Quellen NEU ERFASSEN; exklusive Optionspayloads als Prüfkandidaten sichern; Entry-FK nicht übertragen | DynamicOptionSynchronizer, Classify/Resolve und OptionSourceTransition |
| `TW_dynamic_sources` / `dynamic_source_values`: Quellenreferenz, Typ, Ausdruck, value_key/native_label, Zuordnungs-IDs | Aktuelle Quelle neu beobachten; bestätigte erforderliche Anbindung kontrolliert neu etablieren. Alte IDs ersetzen, keine verketteten historischen Bezüge | RuntimeCollector, DynamicSourceLinkWriter, OptionObservationTransition |
| `TW_dynamic_source_candidates`: Vorschläge, Evidence, Reviewstatus | Kandidaten NEU ABLEITEN; alte Entscheidungen NUR ARCHIV. Aus alten belegten Einstellungen gegebenenfalls neue Bestandsentscheidung | Keine laufende Anwendung über veraltete Source-/Finding-IDs |
| `translation_languages`, Locale-/Fallback-/Scan-Konfiguration, Sprach-/Benutzereinstellungen | KONFIGURATION ERHALTEN und auf Konsistenz prüfen, nicht als Erfassungsbestand leeren | Users/Locales/Settings bleiben eigenständige Anwendungsdaten; aktive Sprachvarianten nicht verlieren |

Vorhandene Sprachdateien dürfen während der Übernahme nicht durch einen Export aus einer noch leeren neuen Datenbank beschnitten werden. Publikations-/Code-Update-Writer werden für den Übergang erst freigegeben, wenn der neue Bestand, die Übernahmebelege und ein nicht destruktiver Vergleich vollständig geprüft sind. Ein bestehender Dateiwert ist ein **beobachteter Ausgangsstand**, keine künstlich rückdatierte Publication.

### Vollständige Abgrenzung für Verlauf, Projektionen und Altbestand

| Bestand | Behandlung im Neustart |
| --- | --- |
| `translation_audit_events`, `TW_events`, `TW_timeline_events` | NUR ARCHIV. Null alte Ereignisse in der neuen Timeline. |
| `TW_reviews` | NUR ARCHIV. Keine alte Freigabe/Entscheidung als neue Review-Zeile kopieren. Noch benötigte aktuelle Sachentscheidungen müssen ausdrücklich neu bestätigt oder als dokumentierte Bestandsentscheidung neu getroffen werden. |
| `translation_usage_audit_decisions`, `translation_usage_audit_decision_usages`, `translation_lang_ballast_decisions` | NUR ARCHIV. Bereits resultierende reale Code-/Dateizustände werden neu beobachtet; die Entscheidungen selbst werden nicht replayed. |
| `TW_pipeline_runs`, `TW_pipeline_run_steps` | NUR ARCHIV; neue Betriebsprotokolle beginnen mit dem neuen Laufbestand. |
| `TW_timeline_chains` einschließlich root_key_id/root_finding_id, aller ID-Listen, Summaries/Zähler und Zeitstempel | Nicht übernehmen; ausschließlich aus neuen fachlichen Objekten/Ereignissen aufbauen. |
| `TW_key_inventory`, `TW_duplicate_candidates`, `TW_shared_key_candidates` | Nicht übernehmen, neu ableiten. Fachlich relevante ausschließlich dort abgelegte Informationen vorher sichern und beurteilen; keine blinde Löschung von Meta-Inhalten. |
| `TW_event_types` | Benötigter Ereignis-/Darstellungskatalog als Konfiguration neu bereitstellen; keine Referenz auf alte Event-IDs. |
| `translation_keys`, `translation_usages`, `TW_entries`, `TW_occurrences` | NUR ARCHIV nach vollständiger Sicherung/Prüfung ihrer exklusiven Inhalte. Keine parallelen operativen Identitäten neu importieren. |
| Publication-Journale und andere wiederaufnehmbare Alt-Reports unter storage | Zusammen mit altem DB-/Dateistand archivieren; nicht mit neuen IDs fortsetzen. Vor Umschaltung laufende/unterbrochene Jobs ausdrücklich schließen oder im alten Stand abschließen. |

### Vertrag der neuen Timeline: Arbeitsdokumentation

Jedes fachliche Ereignis benötigt:

| Information | Inhalt |
| --- | --- |
| Wer | Ausführender Benutzer oder ausdrücklich System/Scanner; auslösender Benutzer bei automatisiertem Auftrag separat. Kein erfundener menschlicher Bearbeiter. |
| Was | Konkrete Aktion mit neuer Finding-/Key-/Revisions-/Zuordnungsreferenz und verständlicher Kurzbeschreibung. |
| Wann | Tatsächlicher Zeitpunkt im neuen Bestand, nicht alte created_at-Werte. |
| Warum | Fachlicher Grundcode plus verständliche Begründung/Beleg. Bei automatischen Aktionen die konkrete Regel, bei erforderlicher Klärung die tatsächliche Benutzerentscheidung. Keine leere pauschale Begründung „updated“. |
| Änderung | Nur relevante Vorher-/Nachher-Werte beziehungsweise unveränderliche Revisionsbezüge, keine ungezielten kompletten Model-Dumps. |
| Zusammenhang | Gemeinsame Operation-ID, damit eine Benutzeraktion nicht wie mehrere unabhängige Arbeitsvorgänge aussieht. |

| Vorgang | Neue fachliche Timeline | Technisches Betriebsprotokoll |
| --- | --- | --- |
| Erstscan / Übernahme | Tatsächliche Erfassung/Bestandsübernahme mit Herkunft, bei Bulk-Vorgang gruppierbar | Dateiliste, Dauer, Parser-/Scope-Version, Mengen |
| Unveränderter Rescan | Kein fachlicher Eintrag | Lauf und bestätigte Beobachtungen |
| Finding eindeutig verschoben / entfernt | Nachweisbarer Übergang mit betroffenem Finding und Grund | Matching-/Scan-Details bei Bedarf |
| Key gewählt / Zuordnung geändert | Wer welchem Finding welchen Key zugeordnet hat und warum | Laufzeit/technische Ausführung |
| Übersetzung tatsächlich geändert | Betroffene Sprache, alte/neue Revision, Bearbeiter | technische Save-Diagnostik |
| Unverändert gespeichert / Preview geöffnet | Kein fachlicher Eintrag | Allenfalls Diagnose, kein verpflichtendes Dauerlog |
| Freigabe / Ablehnung / fachliche Korrektur | Konkrete Entscheidung mit Revisionsbezug und Grund | technische Prüfung |
| Veröffentlichung erfolgreich | Tatsächlich veröffentlichte Revisionen/Sprachen und Ergebnis | Dateien/Hashes/Recovery-Schritte |
| Veröffentlichung teilweise unterbrochen | Sichtbarer fachlicher Ergebnisstand, soweit schon reale Änderungen vorliegen; späterer Abschluss zusammenhängend | Fehler, Retry und einzelne Journal-Schritte |
| Fehlgeschlagene Änderung ohne Wirkung | Kein erfundener Änderungseintrag | Fehler/Validierung/Abbruch; UI muss Fehlschlag anzeigen |
| Rebuild von Inventory/TW-Graph oder Rechen-Cache | Kein fachlicher Eintrag | Rebuild-Metriken |

Eine bewusst vorgenommene spätere fachliche Korrektur gehört selbstverständlich in den neuen Verlauf. „Kein Hin und Her“ bedeutet, technische Wiederholung und alte DEV-Reparaturen nicht als Arbeitsgeschichte auszugeben; es bedeutet nicht, echte spätere Korrekturen zu verbergen.

Idempotenz wird pro logischer Operation abgesichert, nicht pauschal nach gleichem Text dedupliziert. Ein Wiederholungsversuch derselben erfolgreich abgeschlossenen Aktion erzeugt keinen zweiten Erfolgseintrag. Zwei unabhängige echte Bearbeitungen mit gleichem Ergebnis können unterschiedliche Vorgänge sein und müssen anhand ihrer Wirkung/Entscheidung beurteilt werden.

### Auswirkungen auf den heutigen zentralen Recorder

`TranslationWorkbenchTimelineRecorder::record()` schreibt derzeit jeden Aufruf direkt als Timeline-Zeile, ergänzt Pipeline-Kontext und verwendet gegebenenfalls Auth::id(). Er ist ein geeigneter zentraler Ansatzpunkt, enthält aber nicht selbst den vollständigen neuen fachlichen Ereignisvertrag. `TranslationWorkbenchFindingVersioner` schreibt für denselben Umzug getrennte Vorgänger-/Nachfolgerereignisse; diese brauchen im neuen Modell einen gemeinsamen Vorgangsbezug statt bloß zwei lose Meldungen.

Die Umsetzung muss zuerst an den fachlichen Services die Aktion und ihren Grund bestimmen. Ein bloßes Filtern alter Eventnamen in der Ansicht genügt nicht. Der Recorder prüft erforderliche Bezüge und akzeptiert nur strukturierte fachliche Ereignisse; technische Scan-/Retry-Diagnostik geht an das Betriebsprotokoll. Fachlicher Schreibvorgang und sein Ereignis werden gemeinsam transaktional gespeichert; Dateioperationen behalten ihre eigene abgesicherte Recovery-Abfolge.

### Abnahme des Neustarts

1. Im neuen Bestand existieren keine importierten alten Timeline-/Review-/Chain-IDs oder historischen Vorgängerbeziehungen. Das externe Backup ist separat identifizierbar.
2. Für jeden übernommenen Übersetzungsinhalt gibt es einen überprüfbaren Inhalts-/Herkunftsnachweis. Kein alter exklusiver Entwurf verschwindet ungeprüft.
3. Bestandsübernahme behauptet weder ursprüngliche Autorschaft noch eine gerade durchgeführte Freigabe/Veröffentlichung. Mehrdeutige Werte bleiben sichtbar zur Entscheidung stehen.
4. Ein unveränderter Zweitscan und unverändertes Speichern erzeugen keine neue fachliche Änderungs-Timeline; technische Läufe bleiben nachvollziehbar.
5. Reale Zuordnung, Textänderung, Freigabe, Veröffentlichung und Korrektur sind mit wer/was/wann/warum nachvollziehbar und verwenden ausschließlich die neuen Identitäten.
6. Eine fachliche Aktion, ihre Bulk-Unterobjekte und ein Wiederanlauf erscheinen als zusammengehöriger Vorgang; kein doppeltes Erfolgsereignis beim Retry.
7. Neue TW-Graph-Chains lassen sich ausschließlich aus neuem Bestand und neuer Fachhistorie rekonstruieren; zufällig gleiche numerische Alt-/Neu-IDs führen zu keinem Cross-Link.

**Stand dieses Arbeitsschritts:** Übernahme-/Ablöseregeln und der Timeline-Neustart sind dokumentiert; keine Daten gelöscht, keine Historie umgeschrieben und kein Scanner/Writer umgestellt. Vor einer ausführbaren Migration bleibt die Prüfung aller Zusatz-/JSON-Felder und exklusiven aktuellen DB-Werte erforderlich. Der nächste technische Schritt ist ein isolierter Prototyp für Erstscan → unveränderter Zweitscan → neue fachliche Änderung einschließlich der neuen Ereignisregeln, bevor Produktionsdaten angefasst werden.

## Isolierter ausführbarer Prototyp — 2026-10-19

Der erste Vertragstest ist implementiert in `tests/Support/TranslationWorkbench/BaselinePrototype.php`, mit Tests in `tests/Unit/TranslationWorkbench/BaselinePrototypeTest.php`. Die Klasse erzeugt selbst ausschließlich eine neue SQLite-`:memory:`-Datenbank. Sie nimmt keine DB-Konfiguration/Connection von außen an, ist weder im ServiceProvider registriert noch über UI oder Artisan erreichbar. Es gibt keine Produktionsmigration, keinen Import alter Tabellen und keinen neuen produktiven Writer.

Ausführen: `php -d memory_limit=1G vendor/bin/pest tests/Unit/TranslationWorkbench/BaselinePrototypeTest.php --compact`.

### Nachgewiesenes Verhalten

- Leerer Beginn ohne alte Timeline/Reviews; der Erstscan erzeugt neue Rohbefunde und verständliche Erfassungsereignisse.
- Ein unveränderter Zweitscan bestätigt Beobachtungen, ohne neue fachliche Identitäten/Ereignisse zu erzeugen. Auch veränderte SuggestedKey-Regeln überschreiben den Erstbefund nicht.
- Vorhandene Code-Keys verlinken auf eine gemeinsame kanonische Key-Identität; Literale brauchen noch keinen endgültigen Key.
- Gleichlautende kopierte Fundstellen bleiben eigene Findings. Eindeutige Bewegungen innerhalb derselben Datei erzeugen Nachfolger; Originalposition und Rohbefund bleiben erhalten. Nur eine zuvor aktive bestätigte Verknüpfung wird fortgeführt, keine frühere abgelöste Zuordnung.
- Ein geänderter Text erzeugt eine neue Erfassung und übernimmt nicht stillschweigend die Zuordnung des verschwundenen Textes. Ein erneut auftauchender Text reaktiviert kein archiviertes Finding.
- Unvollständige Scans und außerhalb des Scopes liegende Dateien begründen keine Abwesenheit. Mehrdeutige Umzüge brechen atomar ab statt eine Zuordnung zu raten.
- suggestedLiteral stammt nur aus übergebener Übersetzungsevidenz der angegebenen Quellsprache. Fehlende Werte, fremde Locale, Container und widersprüchliche Werte liefern NULL. Bewiesene leere Strings oder Werte gleich dem Key werden korrekt von fehlenden Einträgen unterschieden.
- Die heuristische literalTextSuggested-Angabe des vorhandenen Scanners wird nicht als Übersetzungsbeleg übernommen. Ein Test benutzt tatsächlich den vorhandenen Scanner mit einer isolierten temporären Blade-Datei; es wurde kein zweiter Parser geschrieben.
- Zuordnung und Textänderung dokumentieren Akteur, Zeitpunkt, Grund und betroffene Identitäten/Revisionen. Unverändertes Speichern oder erneute identische Auswahl erzeugt kein fachliches Ereignis.
- Wiederholung derselben Operation liefert das gespeicherte Ergebnis; dieselbe Operation-ID mit anderen Eingaben wird zurückgewiesen. Fachlicher Schreibvorgang und Ereignis sind atomar. Ein provozierter Recorder-Fehler rollt die neue Wertrevision zurück.
- Die Prototyp-Datenbank verhindert Änderungen der erfassten Rohfelder und vorhandener Wertrevisionen per Trigger.

**Validierung:** 19 Tests, 87 Assertions erfolgreich; Formatierung der beiden neuen PHP-Dateien mit Pint und Whitespace-Prüfung durchgeführt. Keine Produktionsdateien/Sprachdateien oder Produktionsdaten verändert.

### Bewusste Grenzen dieses Schritts

Dies ist ein prüfbarer Verhaltensprototyp, keine fertige neue Workbench. Er benutzt vereinfachte Tabellen für Keys, Links und skalare Revisionen; Namensversionen, dynamische Optionen, Freigabe/Publication und UI sind noch nicht implementiert. Das vollständige Zielschema aus dem Entwurf wird dadurch nicht stillschweigend ersetzt.

Die Scan-API nimmt ein vollständiges Dateimanifest und vorhandene Scanner-DTOs entgegen. Sie beweist nicht die Vollständigkeit eines realen Projekt-Scans oder die korrekte Klassifikation aller dynamischen Ausdrücke; die bestehende Key/Literal-Heuristik bleibt im Scanner vorerst unverändert. Ein fehlgeschlagener Lauf kann separat als Betriebsstatus erfasst werden. Eine wegen Mehrdeutigkeit abgebrochene Transaktion hinterlässt in diesem Prototyp keine Run-Zeile; eine spätere produktive Orchestrierung muss solche Fehler unabhängig davon im Betriebsprotokoll aufzeichnen.

Die Übersetzungsevidenz wird im Test explizit bereitgestellt. Ein echter Leser für archivierte DB-Werte/Sprachdateien samt verifizierten Hashes ist noch zu bauen. Leere oder fehlerhafte Daten werden hier nicht durch produktive Lookups ergänzt. Vorhandene globale Scanner-Regeln und Runtime-Fallbacks werden nicht geändert.

Die SQLite-Tests belegen keine PostgreSQL-Nebenläufigkeit. Operation-ID-Prüfung, Sperren, vollständige DB-Constraints, Autorisierung und Recovery müssen vor produktiver Verwendung getrennt unter PostgreSQL geprüft werden. Actor-IDs im Prototyp sind Testwerte, kein Ersatz für die vorhandene Zugriffsprüfung. Es findet kein Scan des echten Repository-Bestands statt.

Nächster sachlicher Ausbau: verifizierte Baseline-Werteaufnahme und Wertkonflikte mit eindeutiger Herkunft, anschließend konkrete Revisionsfreigabe/Publikationsgrenzen. Erst danach ist eine produktive Umstellung mit vollständigem Übernahmebericht sinnvoll.

### Ausbau: belegte Werteaufnahme im Prototyp — 2026-10-19

`tests/Support/TranslationWorkbench/BaselineValueReader.php` liest explizit ausgewählte PHP-Sprachdateien als Syntaxbaum (vorhandener PHP-Parser), ohne `require`/`eval`. Unterstützt sind statische zurückgegebene Arrays mit expliziten String-Keys und String-Werten. Nicht unterstützte Ausdrücke, doppelte Array-Keys und beim Flatten kollidierende Pfade werden zurückgewiesen statt still überschrieben. Der Hash wird gegen genau die gelesenen Bytes geprüft.

Für alte DB-Werte liest der Prototyp einen expliziten JSON-Archivexport (`archive_id`, `values` mit `table`, `id`, `key`, `locale`, `value`) mit geprüftem Dateihash. Er öffnet keine Produktionsverbindung. Dieser Export ist noch nicht automatisch aus dem realen Bestand erzeugt; dynamische Optionspayloads benötigen einen eigenen folgenden Ausbau.

`BaselinePrototype::captureValues()` speichert jeden Kandidaten mit Herkunft in der isolierten Beobachtungstabelle. Gleiche Key-/Locale-Werte werden unter Beibehaltung sämtlicher Herkunftsbelege als eine initiale Revision angelegt. Unterschiedliche Texte ergeben einen Konflikt, bleiben vollständig als Kandidaten erhalten und erzeugen keinen willkürlich ausgewählten Arbeitswert. Auch ausschließlich in DB-Archiven vorhandene Entwürfe gehen in den Vergleich ein. Es gibt noch keine Konfliktauflösung in dieser API.

Die neue Arbeitsdokumentation vermerkt nur die tatsächliche Bestandsaufnahme. Alte Review-Zeitpunkte, Timeline-IDs und Freigaben werden nicht importiert. Die Existenz eines Dateiwerts ist keine neue Publikation und keine implizite Freigabe. Die Aufnahme ist nur auf leerer Werte-Baseline erlaubt, wiederholbar über dieselbe Operation-ID und atomar mit dem neuen Ereignis. Bei einem Fehler im Recorder werden Kandidaten, Keys und Revisionen zurückgerollt. Der bereits vorhandene Scanner-/Produktiv-Importer wird dadurch nicht geändert.

Tests: `tests/Unit/TranslationWorkbench/BaselineValueReaderTest.php` prüft echte temporäre PHP-/JSON-Dateien, Hashabweichung, nicht ausgeführten PHP-Code, doppelte Keys, widersprüchliche Werte, exklusive DB-Werte, leere Texte, Herkunft, Retry und Rollback. Paket-Namespaces, JSON-Sprachdateien, numerische Optionskeys und nicht statische PHP-Sprachdateien sind ausdrücklich noch nicht unterstützt; der Leser lehnt diese gegebenenfalls ab statt eine unvollständige Übernahme als vollständig auszugeben.

Validierung vor Sicherungs-Commit: vollständige `tests/Unit/TranslationWorkbench`-Suite erfolgreich (882 Tests, 5.921 Assertions), einschließlich der isolierten Prototyp-/Werteaufnahmetests. PHP-Syntaxprüfung aller 166 zum Commit vorgemerkten PHP-Dateien ohne Fehler. Kein Produktionsreset und keine reale Bestandsübernahme ausgeführt.
Ergänzend: betroffene Command-/TW-Graph-Tests erfolgreich (209 Tests, 1.569 Assertions). Zusammen 1.091 Tests / 7.490 Assertions. Die PostgreSQL-Integration für Nebenläufigkeit wurde in diesem Schritt nicht ausgeführt.
