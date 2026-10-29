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

### Ausbau: Revisionsfreigabe und Publikationsberechtigung — 2026-10-19

Der isolierte `BaselinePrototype` enthält nun ein unveränderliches Entscheidungsprotokoll `approvals`. `reviewSnapshot()` liefert den konkreten Textstand, seine Revisions-/Key-ID, den aktuellen Key-Namen, die Locale und die aktiven Finding-Verwendungen mit Fingerprint. Die UI eines späteren Systems kann genau diesen Stand zur Entscheidung vorlegen; im Prototyp erfolgt der Aufruf nur aus Tests.

`decideRevision()` prüft diesen Fingerprint innerhalb der Änderungstransaktion und protokolliert Freigabe beziehungsweise Widerruf mit Akteur, Zeitpunkt und Grund. Eine identische Wiederholung erzeugt keinen zweiten fachlichen Eintrag. Frühere Entscheidungen bleiben erhalten. Ein Recorder-Fehler rollt die Entscheidung zurück.

`publicationPreview()` erstellt ausschließlich ein **Manifest der fachlich freigegebenen Revisionen**, keinen ausführbaren Dateischreibplan. Jede gewählte Revision muss aktuell sein und eine gültige Freigabe im aktuellen Verwendungskontext besitzen. Neue Entwürfe, neue/veränderte Zuordnungen, Widerruf und ein anderer Key-Name verhindern die Verwendung einer alten Freigabe. Die Auswahl ist explizit je Revision/Locale; andere Sprachen oder Fallback-Kopien werden nicht automatisch ergänzt. Root-Scalar-Keys werden vor der Publikation zurückgewiesen, aber nicht umbenannt. Kollisionen zwischen skalaren und Container-Pfaden innerhalb der ausgewählten Manifest-Einträge werden geprüft.

`verifyPublicationPreview()` prüft den Fingerprint erneut. Der Fingerprint enthält auch die konkrete Freigabe-ID; Widerruf und anschließende Neufreigabe reaktivieren deshalb kein bereits geprüftes altes Manifest. Ein leerer oder doppelter Revisionssatz wird abgelehnt. Das Öffnen/Prüfen einer Vorschau erzeugt kein Arbeitsereignis.

**Prototyp-Regel, vor UI-Umsetzung noch abschließend abzustimmen:** Freigaben sind hier konservativ an den exakten aktiven Verwendungskontext gebunden. Eine erneute Zuordnung desselben Keys ist ein neuer Kontext. Außerdem darf dieser neue Publikationsvorgang nur die jüngste Entwurfsrevision wählen. Das löscht oder entwertet keine historische Veröffentlichung einer älteren Revision; ein bewusster Rollback auf einen älteren Wert wäre eine eigene neue Revision/Entscheidung. Ob reine Positionsnachfolger später eine Freigabe nach expliziter Prüfung fortführen dürfen, ist hier nicht vorausgesetzt.

Unverändert offen: echte Dateibeobachtung beim Publizieren, Vergleich mit allen bereits vorhandenen Dateiknoten, Vollständigkeit der Locale-Konfiguration, Namensversionen, Rechteprüfung, PostgreSQL-Sperren, atomarer Dateiaustausch und Recovery. Das Manifest schützt noch nicht vor Änderungen zwischen späterer Prüfung und tatsächlichem Schreiben; der produktive Writer muss beides unter dem abgesicherten Publikationsablauf verbinden. Der Prototyp schreibt **keine** Sprachdateien und erzeugt **keine** `published`-Ereignisse. Übernommene Baseline-Werte gelten weiterhin weder als neu freigegeben noch als neu veröffentlicht.

Validierung: `BaselinePrototypeTest`, `BaselineValueReaderTest` und der neue `BaselineApprovalTest` bestehen gemeinsam mit **38 Tests / 169 Assertions**. Geprüft sind insbesondere alte Dialoge, neue Revision mit wieder identischem Text, Zuordnungswechsel, Widerruf/Neufreigabe, Retry, atomarer Rollback, Baseline ohne Freigabe, getrennte Sprachen und Root-Scalar-Keys. Pint und Whitespace-Prüfung erfolgreich. Nur Test-Prototyp, Tests und diese Dokumentation geändert; Produktionsschema, Commands, UI und Datenbestand bleiben unverändert.

### Ausbau: Dateiveröffentlichung und Wiederanlauf im temporären Testbereich — 2026-10-19

`tests/Support/TranslationWorkbench/BaselinePublisher.php` ergänzt den abgesicherten Dateischritt. Der Publisher erzeugt selbst einen zufälligen privaten Unterordner im System-Tempverzeichnis; sein Konstruktor akzeptiert keinen Zielpfad. Damit kann dieser Prototyp nicht auf die produktiven `/lang`-Dateien gerichtet werden. Die In-Memory-Datenbank bleibt die aus den bisherigen Tests bekannte isolierte Instanz.

Ablauf:

1. Das freigegebene Revisionsmanifest wird geprüft. Statische vorhandene PHP-Sprachdateien werden als Syntaxbaum gelesen, niemals ausgeführt. Der geplante Zielinhalt enthält die ausgewählten Änderungen und bewahrt andere Werte sowie leere Container. Konflikte gegen vorhandene, nicht ausgewählte Scalar-/Container-Knoten werden abgewiesen.
2. Ein Journal speichert Run-ID, Akteur/Grund, Revisionsmanifest-Fingerprint und pro Datei den erwarteten Vorher-Hash (NULL bei fehlender Datei), Nachher-Hash und geplante Bytes. Ohne tatsächliche Dateiänderung gibt es keinen neuen Publish-Lauf. Ein weiterer offener Lauf darf nicht dieselben Dateien betreffen.
3. Vor dem ersten Schreiben werden **alle** Dateien geprüft; vor jedem einzelnen Schreiben nochmals Datei und Freigabestand. Ein fremder Zwischenstand wird nicht überschrieben. Dateien werden über eine temporäre Datei im selben Verzeichnis und Rename einzeln ersetzt. Symlink-Ziele und unsichere Pfade werden zurückgewiesen.
4. Bereits auf dem geplanten Nachher-Hash stehende Dateien werden beim Wiederanlauf nicht erneut geschrieben. Erst nachdem sämtliche Nachher-Hashes geprüft sind, wird ein idempotentes fachliches `revisions_published`-Ereignis mit Revisions-/Freigabebelegen, Akteur, Grund und Datei-Hashes gespeichert. Anschließend wird das Journal abgeschlossen.
5. Unterbrechungen hinterlassen das Journal als `interrupted`. Bei einem fehlgeschlagenen DB-Receipt bleiben die bereits geschriebenen Dateien erhalten; das Journal ermöglicht die kontrollierte Fortsetzung. Es wird kein DB-/Datei-übergreifender Rollback behauptet.
6. Wurde das Receipt bereits erfolgreich gespeichert und nur der Journalabschluss unterbrochen, kann dieser Abschluss ohne erneutes Publizieren erfolgen. Eine zwischenzeitlich neue Entwurfsrevision macht den bereits erfolgten historischen Erfolg nicht unwahr. Stimmen die Dateien jedoch nicht mehr mit dem Receipt überein, wird der Abschluss verweigert statt aktuelle Dateien zu überschreiben.

Eine widerrufene Freigabe stoppt einen noch nicht quittierten Teillauf. Eventuell schon geschriebene Dateien bleiben als realer Teilzustand erhalten; es entsteht kein Ereignis, das die gesamte Veröffentlichung als erfolgreich behauptet. Die technischen Journalinformationen sind Grundlage für eine spätere sichtbare Konflikt-/Abschlussentscheidung. Eine produktive UI für Teilergebnisse und das bewusste Schließen ohne Weiterschreiben ist noch nicht Bestandteil dieses Prototyps.

Neue Tests in `tests/Unit/TranslationWorkbench/BaselinePublisherTest.php` simulieren Fehler unmittelbar nach Rename, nach Receipt und beim Recorder. Zusätzlich prüfen sie alle Dateien vor Beginn, externe Dateiänderung, Revision-/Freigabeprüfung beim Wiederanlauf, unveränderte Wiederholung, überlappende Läufe, Symlinks und Scalar-/Container-Kollisionen. Zusammen mit Erfassung, Werteaufnahme und Freigabe bestehen **50 Tests / 228 Assertions**. Formatierung und Whitespace-Prüfung erfolgreich.

Grenzen: Der Testwriter erzeugt die PHP-Arraydatei mit `var_export` neu; er bewahrt Inhalte und leere Container, aber nicht Kommentare oder ursprüngliche Formatierung einer veränderten Datei. Das ist kein festgelegtes Verhalten für den späteren produktiven Exporter. Unveränderte Dateien werden nicht umformatiert. Nicht unterstützte Sprachdateisyntax wird zurückgewiesen. Das Dateischloss koordiniert nur Teilnehmer dieses Writers; konkurrierende fremde Editoren können nicht allein durch `flock` ausgeschlossen werden. Ein atomarer Rename pro Datei ist weder eine atomare Mehrdatei-Publikation noch eine garantierte Stromausfallsicherung. PostgreSQL-Nebenläufigkeit, dauerhafte DB-/Journal-Wiederaufnahme über Prozessneustarts, Rechteprüfung, vollständige Locale-/Namespace-Unterstützung und eine produktive Freigabe-/Closure-UI bleiben gesonderte Umsetzungsschritte.

Keine produktiven Sprachdateien, Tabellen, UI oder Commands geändert. Die nächste Prüfung sollte die bewusste Behandlung eines unterbrochenen Teilergebnisses abdecken: Zustand anzeigen, betroffene Dateien/Revisionen vergleichen und den Lauf entweder sicher fortsetzen oder mit begründeter Entscheidung ohne weitere Schreiboperation schließen.

### Ausbau: Teilveröffentlichung bewusst schließen — 2026-10-19

Der isolierte Publisher kann einen vorbereiteten oder unterbrochenen Lauf jetzt nach einer
erneuten Prüfung mit Bearbeiter und Begründung schließen. Die Vorschau enthält je Datei
Ausgangs-, Ziel- und aktuellen Hash sowie den Zustand: unverändert, noch im Ausgangszustand,
passend zum geplanten Ergebnis oder abweichend. „Passend zum Ergebnis“ beweist nicht,
wer die Datei geschrieben hat. Eine inzwischen ungültige Freigabe wird angezeigt,
verhindert aber nicht das bewusste Schließen.

Die Bestätigung ist an den Fingerprint dieser Vorschau gebunden. Änderungen zwischen
Vorschau und Bestätigung erfordern eine neue Prüfung. Schließen schreibt ausschließlich
Journal und Arbeitsdokumentation; bereits geschriebene Dateien bleiben bestehen, fehlende
werden nicht ergänzt, externe Änderungen nicht überschrieben. Das Ereignis
`publication_closed_without_writing` enthält den geprüften Teilzustand, Bearbeiter und
Begründung. Es ersetzt ausdrücklich keine erfolgreiche Veröffentlichung.

Der Abschlussbeleg wird vor dem Journalstatus gespeichert. Scheitert die Speicherung des
Belegs, bleibt der Lauf fortsetzbar. Scheitert erst die Journalaktualisierung, verhindert
bereits der Beleg jedes weitere Veröffentlichen dieses Laufs; dieselbe Abschlussentscheidung
kann ohne doppeltes Ereignis nachgetragen werden. Abweichende Wiederholungen werden abgewiesen.
Ein geschlossener Lauf gibt seine Dateireservierung für einen neuen, frisch geprüften Plan
frei. Bereits erfolgreich belegte Veröffentlichungen können nicht nachträglich als
unvollständig geschlossen werden.

Verifikation: **55 Tests / 267 Assertions** über die vier Baseline-Prototyp-Suites.
Enthalten sind Teilstand mit widerrufener Freigabe, externe Dateiänderung, veraltete
Vorschau, fehlende Begründung, Recorder-Fehler, Abbruch nach Abschlussbeleg, Wiederholung
und bereits vorhandener Erfolgsbeleg.

Weiterhin ausschließlich Testprototyp: temporäre Dateien und In-Memory-Datenbank.
Keine Änderung an Produktivdaten, produktiven Commands oder `/lang`.
Die zuvor dokumentierten Grenzen zu externen Schreibzugriffen, Prozessneustart,
Autorisierung und Mehrdatei-Atomarität bleiben bestehen. Der nächste Integrationsschritt
ist die Übertragung des geprüften Lebenszyklus in ein konkretes persistentes Zielschema
mit eindeutigem Umstellungsplan; der Prototyp selbst ist kein produktiver Ersatz.

## Persistentes Zielschema und Umstellungsplan — 2026-10-19

**Status: Implementierungsentwurf, noch keine produktive Migration.** Dieser Abschnitt
ergänzt den relationalen Zielentwurf oben. Die Prototyp-Abschnitte dokumentieren jeweils
ihren damaligen Ausbauzustand. Dauerhaftes Recovery über Prozessneustarts und
PostgreSQL-Nebenläufigkeit sind durch die bisherigen SQLite-Tests nicht nachgewiesen.

### Ablage, Identitäten und unveränderliche Belege

Die nächste Implementierung erfolgt in einer eigenen PostgreSQL-Testdatenbank.
Die Schema-Erstellung bleibt zunächst außerhalb der automatisch geladenen
Produktivmigrationen. Alte Migrationen werden nicht nachträglich umgeschrieben.
`TW_` steht weiterhin für `translation_workbench_`; es entsteht kein zweites Fachmodell.

Fachliche IDs: bigint; Vorgangs-/Scan-/Publication-IDs: UUID; Zeitpunkte: timestamptz;
Texte: text; unveränderliche strukturierte Belege: jsonb; SHA-256-Hashes: validierte
64-stellige Hexstrings. Eine Bestandskennung `generation_id` (UUID je Neuaufbau)
bindet externe Links, Jobs, Journale und Exportmanifeste an den richtigen Bestand.
Zufällig gleiche alte und neue numerische IDs dürfen keine Verknüpfung erzeugen.
Anfragen mit alter Bestandskennung werden abgewiesen, nicht auf neue IDs umgedeutet.

Fachliche Identitäten werden archiviert/versioniert, nicht kaskadierend gelöscht.
Benutzerreferenz und unveränderlicher Akteur-Snapshot bleiben getrennt: eine spätere
Benutzerlöschung darf weder Ereignisse löschen noch deren damaligen Urheber umdeuten.

| Bereich | Datenbankgarantie | Ergänzende Service-Regel |
| --- | --- | --- |
| Findings | FK Quelldatei/Erfassungslauf; partielle Unique-Quellsignatur für active; unique predecessor_id, sofern gesetzt; positive Position; konsistente Status-/Stilllegungsfelder | Vorgänger sperren; eindeutigen Umzug belegen; keine Reaktivierung, Kopien-Zusammenlegung oder Nachfolgerzyklen |
| Rohbefund | UPDATE-Trigger schützt Position, Ausdruck, vier Capture-Felder und Herkunft | Neue Version statt Überschreiben; spätere Klassifikation nicht rückwirkend zum Rohbefund erklären |
| Key-Namen | Ein aktueller Name je Key; ein aktueller vollständiger Name je Namespace; globale Namespace-Kennung nicht NULL | Historische Namenswiederbelegung ausdrücklich entscheiden; ungültige vorhandene Keys erfassen und Veröffentlichung sperren |
| Verwendung | FK Finding/Key; höchstens ein offener Link je statischem Finding; gültige Zeitintervalle | Wechsel unter Finding-/Key-Sperre; Kandidaten sind keine Verwendung; dynamische Optionsbezüge separat |
| Wertslots | Partielle Unique-Indizes Key/Locale für scalar und Key/Locale/Optionsidentität für option; CHECK kind/option_identity | Keine Fallback-Kopien; Optionsidentität verlustfrei prüfen |
| Revisionen | Unique slot_id/revision_number; unveränderliche Revision; zusammengesetzter FK current_revision_id/slot_id auf Revision id/slot_id | Slot vor Revisionsvergabe sperren; unveränderter Text erzeugt keine Revision |
| Scan-Beobachtung | Unique Scan-Datei/Finding; FK auf beide Belege | Finding muss zur Quelldatei gehören; fehlgeschlagener oder unvollständiger Scan bestätigt keine Abwesenheit |

Die Entscheidungen des Prototyps werden in **einem** typisierten `TW_reviews`
fortgeführt, nicht zusätzlich in einer produktiven approvals-Tabelle. Konkrete Felder:
id, operation_id, decision_type, revision_id, key_name_id, finding_id, key_id,
context_fingerprint, context_snapshot, decision, Akteur, Grund und Zeitpunkt.
Bezugsspalten sind je Entscheidungstyp nullable; CHECK-Regeln verlangen die passenden
Referenzen. Revisionsfreigaben referenzieren die konkrete Revision und Namensversion.
Entscheidungen bleiben unveränderlich. Ein zweites unabhängig gepflegtes approved am
Key entfällt. Der konservative Kontextvertrag des Prototyps bleibt bis zu einer
ausdrücklich dokumentierten Änderung bestehen.

### Dauerhafter Vorgang, Dateiplan und Abschluss

Diese Tabellen konkretisieren publication_runs/items und den Idempotenzvertrag.
Sie sind keine zusätzliche Quelle redaktioneller Übersetzungswerte.

| Tabelle | Konkrete Kernfelder | Regeln |
| --- | --- | --- |
| `TW_operations` | id UUID PK, kind, request_fingerprint, result JSONB, completed_at | Ergebnis atomar mit fachlicher Wirkung speichern. Gleiche ID mit anderem Request ablehnen. Keine stundenlange DB-Transaktion für Dateioperationen. |
| `TW_publication_runs` | id UUID PK, prepare_operation_id, generation_id, state, manifest_fingerprint, plan_fingerprint, configuration_snapshot, Akteur/Grund, Zeitpunkte, error_summary | CHECK prepared/writing/interrupted/complete/closed; Plan nach Vorbereitung unveränderlich. Terminaler Beleg ist maßgeblich, nicht allein der Betriebsstatus. |
| `TW_publication_files` | id, run_id, target_path, before_hash nullable, after_hash, artifact_reference, artifact_hash | Unique run_id/target_path. NULL bedeutet zuvor fehlende Datei. Unveränderliche geplante Bytes als gesichertes Artefakt vorhalten; Hash allein reicht nicht zur Wiederaufnahme. |
| `TW_publication_items` | id, run_id, publication_file_id, revision_id, key_name_id, review_id, context_fingerprint | Unique run_id/revision_id. Zusammengesetzte FKs sichern gleiche Run-ID von Datei/Item. Locale und Key müssen zum Datei-/Revisionsbezug passen. |
| `TW_file_reservations` | target_path PK, run_id, publication_file_id | Ein offener Writer pro kanonischem Zielpfad; FK auf Datei desselben Runs. Unterbrechung behält Reservierung; Freigabe nur mit terminalem Beleg. |
| `TW_publication_outcomes` | run_id PK, operation_id UNIQUE, kind, review_fingerprint, file_snapshot JSONB, Akteur/Grund/Zeitpunkt | CHECK completed/closed_without_writing. Genau ein unveränderlicher terminaler Beleg je Run; Erfolg nicht nachträglich zum Abbruch umdeuten. |

Das neue `TW_events` enthält id, operation_id, event_index, event_type, actor_type,
Benutzerreferenz/Actor-Snapshot, occurred_at, reason_code, reason_text, typisierte
fachliche Referenzen und details. Unique operation_id/event_index verhindert
Wiederholungsereignisse, erlaubt aber zusammengehörige Teiländerungen. DB-Wirkung,
Entscheidung, Operationsergebnis und Ereignis werden gemeinsam committed.
TW-Graph-Chains sind daraus neu erzeugbare Projektionen.

**Maßgebliche Ablage:** DB-Plan und Outcome entscheiden über den fachlichen Zustand;
das Dateijournal dokumentiert Schreibversuche und beobachtete Hashes. Kein zweiter
Receipt-Speicher in generischen Review-Metadaten. Ein Journal darf niemals eine
neuere DB-Abschlussentscheidung überstimmen. Artefakte und Journal sind an Generation,
Run-ID und Planhash gebunden.

### Sperren und Recovery

- Alle UI-/Command-Schreibwege benutzen dieselben Services. Atomare Reservierung
  der Operation-ID mit Requestvergleich verhindert doppelte Ausführung. Ein SELECT
  vor INSERT allein reicht nicht; Unique-Konflikte benötigen definiertes Retry-Verhalten.
- Zuordnung, Rename, Speichern und Freigabe sperren konsistent dieselben
  Finding-/Key-/Slot-Identitäten. Auch eine neue Verwendung muss die Kontextsperre
  ihres Keys beachten. Sperrreihenfolge zentral festlegen und konkurrierend testen.
- Dateireservierungen in sortierter Pfadreihenfolge anlegen. Eine PostgreSQL-Sperre
  je Run serialisiert Publish/Close/Resume. DB-Sperren schützen keine fremden Editoren;
  dafür bleiben gemeinsamer Writer und eine definierte Betriebsgrenze erforderlich.
- Vor Schreiben Plan, Belegfreiheit, Freigabe und sämtliche Datei-Hashes prüfen.
  Während der Schreibphase müssen Kontext-/Freigabeänderungen koordiniert blockiert
  oder vor jedem Schritt zuverlässig erkannt werden. Die genaue Lock-Lebensdauer
  ist im PostgreSQL-Test nachzuweisen, nicht durch SQLite vorauszusetzen.
- Nach Neustart DB-Plan, Artefakte, Journal und Dateien vergleichen: Vorher-Hash =
  offen, Nachher-Hash = entspricht Zielstand, anderer Hash = Klärung. Der passende
  Hash beweist nicht, welcher Prozess die Datei geschrieben hat.
- Erfolg speichert Outcome, Arbeitsereignis, Operationsergebnis und Freigabe der
  Reservierungen in einer Transaktion. Journalabschluss danach ist wiederholbar.
  Schließen ohne Schreiben dokumentiert stattdessen den geprüften Teilstand und
  verändert keine Zieldatei. Beide Abschlüsse schließen weiteres Schreiben aus.
- Spätere externe Dateiänderungen bleiben als aktuelle Abweichung sichtbar;
  der historische Abschluss bleibt bestehen. Ein beendeter Run darf nach Freigabe
  seiner Reservierungen keine alten Artefakte über einen späteren Run zurückschreiben.
- Fehlende Artefakte blockieren Recovery sichtbar. Journale aus alten Generationen
  bleiben Archiv und dürfen nicht im neuen Bestand fortgesetzt werden.

Code-Ersetzungen erhalten einen eigenen Vorgangstyp mit Finding-Version,
Quellhash und Key-Namensversion. Sie benötigen ebenfalls Plan, Hashprüfung und
Recovery. Eine Publication ersetzt nicht nebenbei Source-Literale; ein geänderter
Quellstand benötigt eine belegte Folgeerfassung statt Überschreiben des Rohbefunds.

### Umstellung mit prüfbaren Etappenzielen

| Etappe | Ergebnis | Abnahme |
| --- | --- | --- |
| 1. PostgreSQL-Testbasis | Neues Schema ausschließlich in eigener Testdatenbank | Constraints, Unveränderlichkeit, konkurrierende Operationen und neu geöffnete Verbindung geprüft |
| 2. Gemeinsame Services | Erfassung, Zuordnung, Revision, Entscheidung, Publication und Abschluss gegen persistentes Schema | 55 bisherige Prototyp-Fälle übertragen; zusätzliche Neustart-/Nebenläufigkeitsfälle erfolgreich |
| 3. Vollständige Aufnahme | Frischer read-only Bericht über Code, DB-Werte/Entwürfe, Sprachdateien, Locales, dynamische Optionen und Namespaces | Jede Werteart hat sicheren Leser und Herkunft; nicht unterstützte Inhalte sind Blocker, keine still ausgelassenen Einträge |
| 4. Generalprobe | Neuaufbau auf Kopie, neue IDs und neue Arbeitsdokumentation | Erst-/Zweitscan, Umzug, Entfernung, copied/handmade, SharedCandidate, Review, Teilpublikation; kein exklusiver Entwurf verloren |
| 5. UI-/Command-Anbindung | Workbench-Formular und Commands verwenden dieselben neuen Services, Graph neue Projektion | UI-Durchlauf, Autorisierung und Ausschluss alter Schreibwege/Jobs nachgewiesen |
| 6. Wechselbericht | Archivmanifest, Wertevergleich, Konflikte, Wiederherstellungsprobe und Wartungsfenster | Konkreten Produktionswechsel erst anhand dieses prüfbaren Ergebnisses freigeben |
| 7. Wechsel | Alte Writer/Jobs stoppen, zusammengehörigen Stand sichern, neue Generation aktivieren | Smoke-Test und unveränderter Rescan; keine Vermischung alter Links/Journale/Caches mit neuen IDs |
| 8. Archivphase | Alter Bestand separat nur lesbar; nicht mehr benötigter Code nach _old | Kein sofortiges Löschen; alte Historie nicht als neue Timeline importieren |

**Kein Dual-Write:** Bis zur Generalprobe läuft die neue Implementierung isoliert.
Beim Wechsel gibt es genau eine aktive Generation und einen fachlichen Schreibweg.
Anwendungs-/Benutzerdaten außerhalb dieses Neuaufbaus werden nicht zurückgesetzt.
Quellcode und veröffentlichte Sprachdateien bleiben gesicherte Ausgangsbasis;
Literale werden nicht massenweise zurückgeschrieben.

**Rückkehrplan:** Vor neuen produktiven Änderungen kann gemeinsam auf den gesicherten
alten Stand zurückgeschaltet werden. Nach neuen Bearbeitungen oder Dateiänderungen
ist ein einfacher DB-Restore kein sicherer Rollback. Erst die neuen Änderungen
sichern, Dateien und Daten gemeinsam abgleichen und bewusst wiederherstellen oder
vorwärts korrigieren. Seit der Umschaltung geleistete Arbeit darf nicht verloren gehen.

### Offene Nachweise und nächster begrenzter Umsetzungsschritt

Dynamische Optionen, Namespaces, JSON-Sprachdateien, Source-Replacement,
Autorisierung und vollständige Locale-Unterstützung sind weiterhin verpflichtende
Gates vor Produktion. Der skalare Prototyp belegt sie nicht automatisch.

Als nächstes entsteht die PostgreSQL-Testbasis für Erfassung, Keys/Zuordnungen,
Revisionen sowie Operationen/Ereignisse. Zu prüfen sind Bewegung ohne Reaktivierung,
konkurrierende Zuordnung, konkurrierendes Speichern und idempotente Wiederholung
über eine neu geöffnete Verbindung. Danach folgen Freigabe und persistenter Dateiplan.
Der vorhandene Launcher in
`tests/Integration/TranslationWorkbench/source-link-concurrency.php` dient als
Sicherheitsmuster: eigener temporärer Cluster, keine App-DB-Zugangsdaten. Er ist
kein bereits bestandener Nachweis für das neue Schema.

In diesem Schritt ausschließlich den Entwurf konkretisiert und mit vorhandenen
Migrationen/Prototypen abgeglichen. Keine Produktivmigration, kein Reset und keine
Bestandsübernahme ausgeführt. Whitespace-Prüfung erfolgreich; keine Laufzeittests
für diese reine Dokumentationsänderung erforderlich.

### Umsetzung: isolierte PostgreSQL-Testbasis — 2026-10-19

Die erste persistente Teilmenge liegt jetzt in
`tests/Support/TranslationWorkbench/postgres/baseline.sql` und
`tests/Support/TranslationWorkbench/PostgresBaseline.php`.
Sie wird ausschließlich vom expliziten Integrationstest
`tests/Integration/TranslationWorkbench/baseline-persistence.php` geladen:

```bash
php tests/Integration/TranslationWorkbench/baseline-persistence.php
```

Der Launcher erstellt einen eigenen PostgreSQL-18-Cluster unter einem zufälligen
privaten Tempverzeichnis, erreichbar ausschließlich über seinen Unix-Socket.
Er lädt weder den Laravel-App-Bootstrap noch Produktiv-Zugangsdaten. Der Adapter
akzeptiert nur dieses Tempverzeichnis-Muster mit existierendem Testcluster.
Nach dem Test wird der eigene Server gestoppt und sein Verzeichnis entfernt.
Der Launcher benötigt die Erlaubnis, einen lokalen Unix-Socket anzulegen; das
ist in der eingeschränkten Sandbox nicht möglich. Der ausgeführte Test nutzte
entsprechend die Freigabe für diesen Runner.

**Tatsächlich implementierte Teilmenge:** Source-Dateien, abgeschlossene/fehlgeschlagene
Scan-Läufe, unveränderliche Finding-Erfassung, Beobachtungen, Key-Identität und
Namensstände, aktuelle/historische Verknüpfungen, skalare Locale-Slots und
Wertrevisionen, atomare Operationsergebnisse und fachliche Ereignisse.
Die Capture-Methode nimmt ausdrücklich vorbereitete Testbefunde beziehungsweise
bereits geprüfte Positionsbewegungen entgegen. Sie ersetzt nicht den Scanner und
behauptet keine produktive Umzugserkennung. Der im Fixture gespeicherte SuggestedKey
ist ein Testwert, kein neu eingeführter Generator.

Nachgewiesen:

- Unveränderter Befund bestätigt dasselbe Finding ohne neues Änderungsereignis.
- Explizit geprüfter Positionsnachfolger archiviert den Vorgänger und führt dessen
  aktive Zuordnung fort. Ein archivierter Vorgänger wird weder geändert noch reaktiviert.
- Ein zusätzliches gleichlautendes Finding bleibt eigenständig und erhält keine
  automatische Kopie der Key-Zuordnung.
- Datenbank-Trigger verhindern Änderungen des Capture-/SuggestedKey und vorhandener
  Revisionen. Partielle Eindeutigkeit verhindert zwei aktuelle statische Zuordnungen.
- Ein zusammengesetzter Foreign Key verhindert einen aktuellen Revisionszeiger
  auf den Slot eines anderen Keys. Unverändertes Speichern erzeugt keine Revision.
- Fehler beim Ereignisschreiben rollen Wertrevision, aktuellen Zeiger und
  Operationsergebnis gemeinsam zurück.
- Zwei getrennte PHP-Prozesse werden tatsächlich an PostgreSQL-Sperren angehalten
  (über pg_stat_activity nachgewiesen). Nach Freigabe gewinnt eine Zuordnung bzw.
  Textänderung; die konkurrierende Bestätigung mit veraltetem Kontext wird abgelehnt.
- Zwei gleiche parallele Operationen liefern dasselbe Ergebnis mit nur einem Ereignis.
  Dieselbe Operation-ID mit verschiedenen Requests wird auch parallel zurückgewiesen.
- Ein neuer PHP-Prozess und anschließend ein tatsächlicher PostgreSQL-Serverneustart
  liefern beim Retry den gespeicherten Abschluss ohne erneute fachliche Wirkung.

Ergebnis: **46 Assertions erfolgreich**, davon 44 im Hauptlauf mit vier
Konkurrenzszenarien und zwei nach dem Serverneustart. Zusätzlich bestehen die vier
bisherigen SQLite-Prototyp-Suites mit **55 Tests / 267 Assertions** unverändert.
Pint, PHP-Syntax und Whitespace-Prüfung erfolgreich.

**Abgrenzung:** Noch keine vollständige Umsetzung des Zielschemas. Dynamische
Optionsslots, vollständige Scan-Dateimanifeste/Hashes und Matching, produktive
Namensversionierung, Bestandskennung, Autorisierung, Freigabe und Dateipublikation
sind noch nicht an diesen PostgreSQL-Kern angeschlossen. Die Benutzer-IDs sind
Testakteure. Der Runner ist explizit auszuführen, nicht bereits automatisch Teil
jedes normalen Pest-/Diagnostics-Laufs. Die Koordination von Key-Kontextänderungen
mit Freigaben ist erst beim nächsten Ausbau zu implementieren und zu testen;
die bisherigen Zuordnungs-/Speichersperren belegen diese noch nicht.

Nächster Schritt: revisionsgebundene Entscheidungen/Freigaben an diesen persistenten
Kern anschließen, einschließlich konkurrierender Zuordnungs- und Textänderungen.
Danach folgt der persistente Datei-/Abschlussplan. Kein Produktivschema, UI,
Artisan-Schreibweg oder realer Datenbestand wurde durch diesen Schritt geändert.

### Ausbau: persistente revisionsgebundene Freigaben — 2026-10-19

Der PostgreSQL-Testkern verwendet jetzt `translation_workbench_reviews` für
unveränderliche Freigaben und Widerrufe. Gespeichert werden konkrete Revision,
Key-Namensversion, Kontext-Snapshot/-Hash, Operation, Akteur, Grund und Zeitpunkt.
Die Operation-Referenz wird wie beim Arbeitsereignis spätestens beim Commit geprüft;
ein Recorder-Fehler rollt Entscheidung, Ereignis und Operation gemeinsam zurück.
Eine produktive UI-/Command-Anbindung findet weiterhin nicht statt.

`reviewSnapshot()` liest den Dialogstand mit einem einzelnen SQL-Statement als
zusammenhängenden MVCC-Snapshot. Er enthält Textrevision, aktuellen Revisionszeiger,
Locale, Key-Namensversion sowie die sortierten aktiven Finding-/Link-Identitäten.
Der Bestätigungs-Fingerprint enthält zusätzlich die letzte Entscheidungs-ID.
Damit kann ein vor einem Widerruf geöffneter Dialog nicht still die vorherige
Freigabe wiederherstellen. Der gespeicherte Kontext-Hash selbst enthält diese
Entscheidungs-ID nicht: Eine Entscheidung soll nicht durch ihr eigenes Anlegen
sofort ungültig werden.

`decide()` sperrt die Key-Identität und prüft den Snapshot erneut. Freigabe benötigt
die aktuelle Revision und mindestens eine aktive Verwendung; Widerruf bleibt
auch für einen historischen Stand möglich. Identische Entscheidungen im gleichen
Kontext erzeugen keine weitere Review-/Ereigniszeile. Gleiche Operation-ID mit
gleichem Request liefert den historischen Abschluss, nicht eine erneute Freigabe.
Akteur und nichtleere Begründung sind erforderlich.

Die anderen beteiligten Test-Schreibwege beachten dieselbe Kontextgrenze:
Zuordnungen sperren nach dem Finding sowohl alten als auch neuen Key in aufsteigender
ID-Reihenfolge; Positionsnachfolger sperren nach Datei/Finding den bisherigen Key.
Speichern sperrt Key vor Slot, Entscheidungen benötigen nur den Key und erwerben
keine nachgelagerte Finding-Sperre. Dadurch entsteht zwischen diesen implementierten
Schreibwegen kein umgekehrter Key→Finding-Sperrpfad. Ein kommender Rename-/Retirement-
Service muss diese Reihenfolge ebenfalls einhalten; direkte beliebige SQL-Änderungen
werden durch diesen Servicevertrag nicht abgesichert.

Der konservative Freigabevertrag bleibt: neue Revision, geänderte Verwendung und
Positionsnachfolger machen einen bisherigen Kontext ungültig, ohne die historische
Entscheidung zu löschen. Ein Wechsel zu einem anderen Key betrifft beide Kontexte.
Freigabe einer Locale autorisiert keine andere Locale. `approvalIsCurrent()` ist
nur eine Diagnose des gelesenen Zustands, keine Schreibberechtigung für einen späteren
Dateiexport; dessen erneute Prüfung und Sperren folgen mit dem Publication-Ausbau.

**Verifikation:** PostgreSQL-Runner erfolgreich mit **94 Assertions** (90 im Hauptlauf,
vier nach echtem Serverneustart) und acht nachgewiesenen Konkurrenzszenarien.
Neu geprüft sind Freigabe parallel zu Textänderung, zusätzlicher Verwendung und
Positionsnachfolger sowie konkurrierende Freigabe/Widerruf. Unabhängig davon, welcher
Prozess zuerst fortgesetzt wird, bleibt kein neuer Kontext durch eine alte Entscheidung
freigegeben. Zusätzlich geprüft: veralteter Dialog, historische Revision,
fehlende Verwendung, unveränderliche Reviews, fehlender Akteur/Grund, Recorder-Rollback,
Key-Wechsel beidseitig, getrennte Locales und Review-Retry nach Prozess-/Serverneustart.
Die bisherigen **55 SQLite-Prototyp-Tests / 267 Assertions** bleiben erfolgreich.
Pint und Whitespace-Prüfung bestanden.

Nächster begrenzter Ausbau: den geprüften Datei-/Abschlussablauf an diesen persistenten
Kern anbinden, einschließlich Dateireservierung, Belegen und Recovery nach Neustart.
Dynamische Optionen, vollständiger Scanner, produktive Rechteprüfung und UI bleiben
weiterhin getrennte offene Integrationsschritte; Produktivschema und Datenbestand
wurden nicht verändert.

### Ausbau: persistente Dateiveröffentlichung und Recovery — 2026-10-19

`tests/Support/TranslationWorkbench/PostgresPublisher.php` bindet den geprüften
Dateiablauf an den isolierten PostgreSQL-Kern an. Das Zielverzeichnis wird aus dem
validierten Testcluster abgeleitet; ein produktiver Zielpfad kann nicht übergeben
werden. Die neuen Testtabellen speichern Runs, unveränderliche Datei-Hashpläne,
eindeutige Dateireservierungen und unveränderliche terminale Outcomes.

Vorbereitung prüft die freigegebenen Revisionen unter sortierten Key-Sperren und
liest vorhandene statische PHP-Arrays mit dem bestehenden BaselineValueReader,
ohne deren PHP-Code auszuführen. Bestehende ungewählte Werte bleiben erhalten;
Scalar-/Container-Kollisionen werden zurückgewiesen. Plan und Reservierungen werden
atomar gespeichert. Eine konkurrierende Reservierung derselben Datei verhindert
einen weiteren offenen Plan. Wiederholung derselben Vorbereitung liefert denselben
Plan; abweichende Eingaben unter derselben Operation-ID bleiben ein Fehler.

**Konkrete Persistenzentscheidung im Test:** Die geplanten Bytes liegen vorerst
unveränderlich im DB-Plantext samt SHA-256; es gibt keine zusätzliche externe
Artefaktdatei und keinen zweiten Dateijournal-Writer. Der Run-Status wird vor Beginn
der Schreibtransaktion dauerhaft auf writing gesetzt. Damit bleibt nach einem
Prozessabbruch ein erkennbarer offener Run mit vollständigem Wiederaufnahmeplan.
Das ist eine begrenzte Testausprägung des Entwurfs, noch keine Entscheidung über
Speichergrenzen und Artefaktablage für beliebig große produktive Publikationen.

Publish/Close werden durch eine Session-Advisory-Sperre je Run serialisiert.
Der Writer hält während der kurzen Test-Dateischreibphase die Key-Zeilensperren
in einer Transaktion: ein kooperierender Widerruf/Save/Assignment wartet bis zum
Commit oder Abbruch. Vor dem ersten Schreiben werden sämtliche Dateien,
Reservierungen und die exakten Freigabe-Snapshots geprüft. Dateien werden einzeln
über Tempdatei/Rename ersetzt. Vorhandener Nachher-Hash wird beim Resume nicht
nochmals geschrieben. Erst nach Prüfung aller Zielhashes werden Outcome,
Arbeitsereignis, Reservierungsfreigabe und terminaler Run-Status gemeinsam committed.

Ein DB-Rollback kann bereits geschriebene Dateien nicht zurückrollen. Bei
Recorder-Fehler oder Prozessabbruch bleiben Plan und Reservierungen erhalten.
Nach einem Neustart werden die tatsächlichen Hashes mit Vorher/Nachher verglichen;
abweichende Dateien werden nicht überschrieben. Ein vollständiger Beleg ist
historisch: weder späterer Widerruf noch externe spätere Dateiänderung berechtigen
einen Retry, alte Bytes erneut zu schreiben.

Schließen ohne Schreiben erfordert einen aktuellen Hash-Snapshot, Akteur und Grund.
Es erzeugt einen eigenen terminalen Outcome und das entsprechende Arbeitsereignis,
aber keinen Veröffentlichungserfolg und keine Dateikorrektur. Gleiche Wiederholung
liefert den Abschluss; abweichende oder widersprüchliche Abschlussentscheidung wird
abgewiesen. Auch ein wegen widerrufener Freigabe blockierter Lauf kann so geschlossen
werden. Der Event-Bezug benutzt im Test die ursprüngliche Run-/Prepare-Operation;
der Abschluss selbst ist zusätzlich über den eindeutigen Outcome abgesichert.
Eine eigene persistente Request-ID je UI-Abschlussaktion ist damit noch nicht umgesetzt.

Die neuen Fälle in `tests/Integration/TranslationWorkbench/baseline-publication-cases.php`
werden vom vorhandenen isolierten Runner eingebunden. Nachgewiesen sind:

- Wiederholbare Vorbereitung und Rollback bei überlappender Dateireservierung.
- Abrupter Exit des separaten PHP-Writers direkt nach der ersten Datei, ohne
  normales Exception-/Rollback-Handling des Publishers: Status bleibt writing,
  keine Erfolgsquittung, Reservierungen bleiben erhalten.
- Wiederaufnahme dieses Teilstands nach echtem PostgreSQL-Neustart in einem weiteren
  PHP-Prozess; die erste Datei behält ihren gesetzten mtime, die zweite wird ergänzt.
- Externe Änderungen und veraltete Closure-Vorschau werden erkannt; Closure
  überschreibt nichts. Ein neuer Plan kann freigegebene Reservierungen benutzen.
- Recorder-Ausfall nach Dateischreiben, Retry ohne erneutes Schreiben sowie Abbruch
  nach bereits committed Receipt ohne doppeltes Erfolgsereignis.
- Ungewählte Dateiinhalte bleiben erhalten; ein historischer Retry überschreibt
  keine späteren externen Änderungen.
- Widerruf vor Publish verhindert Schreiben. Ein während Publish gestarteter
  Widerruf wartet nachweislich auf die Key-Sperre und wird anschließend wirksam,
  ohne den historischen Erfolg umzudeuten.

**Validierung:** PostgreSQL-Runner mit **129 Assertions** erfolgreich (122 im Hauptlauf,
sieben nach Serverneustart), einschließlich neun Konkurrenzszenarien. Die bisherigen
**55 Prototyp-Tests / 267 Assertions** bleiben erfolgreich. Pint, PHP-Syntaxprüfung
des neuen Publishers und Whitespace-Prüfung bestanden.

Weiterhin keine Produktivmigration oder echte Datei-/Bestandsveröffentlichung.
Der Testwriter bewahrt Inhalte, nicht die Formatierung/Kommentare veränderter Dateien.
Er unterstützt nur den geprüften statischen skalaren PHP-Arrayfall. Es besteht keine
atomare Mehrdatei- oder Stromausfallgarantie. Externe Editoren respektieren keine
DB-Sperren; der unvermeidbare externe Hashcheck/Rename-Zwischenraum ist nicht durch
PostgreSQL abgesichert. Mehrhost-Betrieb, Bestandskennung, Artefaktgrößen, Rechteprüfung,
Namespace-/Optionsdaten und UI-Anbindung bleiben separate Produktionsvoraussetzungen.

Damit sind die grundlegenden isolierten Erfassungs-, Bearbeitungs-, Freigabe- und
Publikationsabläufe ausführbar belegt. Nächster sinnvoller Schritt ist der Abgleich
mit den real vorhandenen Datenformaten und Schreibwegen anhand eines frischen
read-only Übernahmeberichts; weitere Produktionsfunktionen sollten nicht auf einer
unvollständigen skalaren Bestandsannahme aufgebaut werden.

## Aktueller read-only Übernahmebericht — 2026-10-19, 07:41:38 +02:00

Ausführung: inspect-transfer-readiness.php. Vollständige Evidenz in
[transfer-readiness-2026-10-19.json](transfer-readiness-2026-10-19.json).
PostgreSQL bestätigte **repeatable read / read_only=on**. Kein Scannerlauf,
Import, Export oder Reparaturcommand ausgeführt. Der vorhandene Scanner wurde
nur nach seiner Dateiauswahl gefragt; Sprachdateien wurden per AST gelesen.

DB-Abfragen bilden einen gemeinsamen Snapshot; Dateien wurden anschließend mit
Hashes geprüft. Das ist kein atomarer DB-/Dateisystem-Snapshot. Bei den geprüften
Dateien wurde während der jeweiligen Prüfung keine Änderung festgestellt.
Für einen tatsächlichen Wechsel bleibt ein neuer gemeinsamer Sicherungsstand
im Wartungsfenster erforderlich.

### Umfang

| Bereich | Aktueller Befund | Bedeutung |
| --- | ---: | --- |
| Translation-bezogene Tabellen | 33 | Keine automatische Löschfreigabe |
| Findings | 23.238: 18.870 active / 4.237 obsolete / 131 commented_out | Neuerfassung aus geprüftem Code; alte Historie archivieren |
| Aktive skalare Workbench-Werte | 1.631 | Herkunft und Konflikte prüfen |
| Aktive dynamische Key-Werte | 200 | Optionsrevisionen fehlen im PostgreSQL-Testkern |
| Ältere dynamische Werte | 43, Status ok, Locale en | Exklusive Entwürfe nicht verlieren |
| Dynamische Quellen | 96: 22 active / 48 needs_review / 26 obsolete | Nicht pauschal als bestätigt übernehmen |
| Dynamische Quellwerte | 247 active | Beobachtung ist keine freigegebene Übersetzung |
| Optionsbeobachtungen | 26: 12 JSON-Objekte / 14 JSON-Arrays | Beide Strukturen verlustfrei behandeln |
| Alte native/Optionswerte | 952 | Herkunft/Beobachtung, nicht automatisch Übersetzungsrevision |
| Ausgewählte Quelldateien | 1.696 | Manifest, keine Aussage über enthaltene Findings |
| Sprachdateien aus CLI-Loader-Pfaden | 89 PHP-Dateien | 54 Anwendung, 4 Framework, 31 Paket-Overrides unter lang/vendor/backup |
| Aktive Links auf obsolete Findings | 12 | Keine aktuelle Verwendung daraus ableiten |
| Aktive Findings mit mehreren benannten aktiven Key-IDs | 27 | Kandidaten-/Relationsbedeutung prüfen, nicht pauschal konkurrierende bestätigte Zuordnungen |

translation_workbench_key_values bleibt leer. Das beweist nicht, dass alle Leser
bereits entfernt werden können. Große alte Historientabellen bleiben unangetastet.

### Belegte Wertevergleiche

Verglichen werden exakter vollständiger Key und exakte Locale, ohne Fallback,
Case-Folding oder Umbenennung. Die alte Spalte lang_values.namespace bezeichnet
laut LangValueImporter die Dateigruppe (z. B. ui), nicht den Laravel-Paketnamespace.
Sie darf nicht ungeprüft in die neue package_namespace-Bedeutung übernommen werden.

| Vergleich | Gleich | Unterschiedlich | Nur links | Nur rechts |
| --- | ---: | ---: | ---: | ---: |
| Legacy-Werte Status ok, nicht-NULL ↔ aktive skalare Workbench-Werte | 1.167 | 10 | 290 | 454 |
| Aktive skalare Workbench-Werte ↔ gelesene Anwendungsdateiwerte | 1.608 | 9 | 14 | 399 |
| Aktive dynamische Key-Werte ↔ Anwendungsdateiwerte | 184 | 0 | 16 | 1.832 |

Gezählt werden Key-/Locale-Identitäten, nicht beliebige Tabellenzeilen. Dynamische
Keys wurden mit der vorhandenen reinen Funktion TranslationWorkbenchOption::key()
gebildet. Die rechte Menge enthält alle gelesenen skalaren Anwendungsdateiwerte;
1.832 „nur rechts“ sind deshalb keine fehlenden dynamischen Übersetzungen.
Auch die 399 reinen Dateieinträge des skalaren Vergleichs sind keine pauschale
Fehlerliste: darin können dynamische und anders verwaltete Werte liegen.

14 skalare und 16 dynamische Werte ohne Dateigegenstück sind Übernahme-Prüfkandidaten,
nicht automatisch verlorene oder zu veröffentlichende Übersetzungen. Ebenso können
die 290 Legacy-Identitäten ohne aktives skalares Workbench-Gegenstück in archivierten
Zeilen oder anderen Modellen enthalten sein. IDs, Status und Inhaltshashes bleiben
im Bericht erhalten; keine Seite wird automatisch als maßgeblich ausgewählt.

### Fehlende Formatabdeckung

- Alle 89 PHP-Dateien bestehen die syntaktische Baumprüfung des Testwriters.
  Das belegt nicht dessen Zielpfad-/Namespace-Unterstützung. Der Baseline-Leser
  akzeptiert 58 Dateien; 31 Paket-Overrides sind ausdrücklich nicht abgedeckt.
- In den erfassten Loader-Wurzeln wurden keine JSON-Sprachdateien gefunden.
  Das beweist weder JSON-Unterstützung noch die Erfassung erst in anderem
  Laufzeitkontext registrierter Paketquellen. Der CLI-Loader hatte keine
  expliziten Namespace-Hints.
- Die Sprachverwaltung enthält de und en. Legacy-Werte enthalten zugleich
  de-at/de-AT, de-de/de-DE und weitere Varianten. Vor Zusammenführung sind
  Locale-Regeln und Wertkonflikte zu prüfen. Workbench-Quellsprache en und
  Default-Sprache de haben unterschiedliche Rollen und sind kein automatisch
  zu korrigierender Widerspruch.
- 200 dynamische Übersetzungen brauchen Optionsidentität plus Key-/Locale-Bezug;
  scalar-only Slots reichen nicht. Die 43 älteren dynamischen Werte sind noch
  nicht einzeln auf exklusive Inhalte abgeglichen.

### Konkreter Scan-Umfang-Befund

Die reale Dateiauswahl enthält zwölf Dateien aus _old-Verzeichnissen: sieben alte
TW-Graph-Strang-Views und fünf alte Command-/Foundation-Dateien. Die Liste steht
unter archived_directories_in_scan_scope im JSON. Konfigurierte Pfadausschlüsse
und Dateinamensmarker schließen diese Verzeichnisebene derzeit nicht generell aus.

Das ist ein Blocker für einen sauberen Erstscan, da diese Dateien ausdrücklich
als inaktiv eingestuft wurden. Es ist kein Beleg für neu erzeugte Findings:
Der Bericht führte keinen Scan aus. Die Auswahlregeln wurden noch nicht verändert.

### Daraus folgende Reihenfolge

1. Archivierte Verzeichnisse im Scanner-Umfang konsistent abgrenzen und die
   Dateiauswahl testen; noch keinen schreibenden Rescan ausführen.
2. Optionsidentitäten und Locale-/Namespace-Behandlung anhand der real
   vorkommenden Formate im isolierten Schema/Leser ergänzen.
3. Reine DB-Werte und Abweichungen mit sämtlicher Herkunft als Übernahme-Prüfliste
   aufbereiten. Kein stilles „Datei gewinnt“ oder „Datenbank gewinnt“.
4. Erst dann die vollständige Generalprobe auf gesicherter Kopie durchführen.
   Dieser Bericht ist kein vollständiger Datenexport und keine Reset-Freigabe.

Geändert: Audit-Script, JSON-Bericht und diese Dokumentation. PHP-Syntax, Pint und
Whitespace geprüft. Kein produktiver Datenbestand oder Schreibweg verändert.

### Korrektur des Scanner-Umfangs — 2026-10-19

Die zentrale Konfiguration enthält jetzt exclude_directory_names mit den exakten
Verzeichnisnamen _old und -old. Die vorhandene zentrale Pfadprüfung des Scanners
wendet diese Regel auf jeder Verzeichnisebene an. Keine Teilstring-Erkennung:
old-tools, _older und eine Datei _old.php bleiben regulär auswählbar.
Die Regel ist konfigurierbar und wird nicht parallel in einzelnen Commands gepflegt.

Abgedeckt sind Verzeichnis-Discovery, explizit angegebene Dateien, direkter scanFile-
Aufruf, Scan-Manifest und die Prüfung vermeintlich fehlender bekannter Dateien.
Ausgeschlossene Archive liegen außerhalb des Reconciliation-Scopes. Ihr Ausschluss
ist kein Löschbeleg und archiviert daher nicht automatisch bestehende Findings.

Die reine Dateiauswahl des echten Projekts wurde mit dem vorherigen Bericht verglichen:
**1.696 → 1.684 Dateien**, genau die zwölf zuvor gefundenen _old-Dateien entfernt,
keine anderen Dateien hinzugefügt/entfernt, keine archivierten Pfade verbleiben.
Nachweis: [archive-scope-2026-10-19.json](archive-scope-2026-10-19.json).
Der ursprüngliche Übernahmebericht bleibt als Vorher-Snapshot erhalten.

TranslationScanPathsTest prüft die Ausschlüsse auch über die drei vorhandenen
Command-Konsumenten, ohne deren schreibende handle-Methoden auszuführen.
Zusätzlich wurden die Workflow-Regressionsfälle ausgeführt:
**781 Tests / 5.176 Assertions erfolgreich** (ScanPaths und TranslationWorkflow).
Syntax-/Whitespace-Prüfung erfolgreich. Kein produktiver Scannerlauf, keine
Datenbankbereinigung und keine Löschung archivierter Dateien durchgeführt.
Die zuvor vorhandenen Findings/Links bleiben bis zur geplanten Übernahmeprüfung
unverändert. Als Nächstes folgt die Formatabdeckung für dynamische Optionswerte.

### Dynamische Optionswerte im isolierten PostgreSQL-Modell — 2026-10-19

Wert-Slots unterscheiden jetzt scalar und option. Ein skalarer Slot hat keine
Optionsidentität; ein Options-Slot benötigt eine nichtleere Zeichenfolge.
Die Eindeutigkeit gilt getrennt pro Key/Locale beziehungsweise
Key/Locale/Optionsidentität. Diese Identitätsfelder sind nach Erstellung unveränderlich.
Der vorhandene Revisions-, Freigabe- und Ereignispfad wird gemeinsam verwendet;
es gibt keinen zweiten Speicherweg für Optionsübersetzungen.

Optionsidentitäten werden unverändert gespeichert. Insbesondere bleiben 0, 01,
1, 1.0, a.b und a%2Eb unterscheidbar. Diese Prüfung betrifft Zeichenfolgen,
nicht eine zusätzliche Unterscheidung von Integer 1 und String 1. Leerer
Übersetzungstext ist zulässig und wird nicht mit einer fehlenden Option verwechselt.

Die Freigabe bindet auch Slot-Art und Optionsidentität. Änderungen einer Option
oder Sprache erteilen keiner anderen Option eine Freigabe und entziehen auch
keiner unveränderten Option ihre Freigabe. Neue Revisionen müssen selbst geprüft
werden. Konkurrierende Änderungen desselben Slots werden weiterhin über die
erwartete Revision abgesichert.

Der bisherige skalare PostgreSQL-Dateiexporter weist Optionsrevisionen ausdrücklich
zurück. Erst ein optionsbewusster Dateiplan darf deren Zielpfade bestimmen; andernfalls
könnte der Wert versehentlich unter dem übergeordneten skalaren Key landen.
Optionspublikation, Übernahme dynamischer Quellbeobachtungen sowie vollständige
Locale-/Namespace-Abdeckung sind damit noch nicht umgesetzt.

Nachweis: **156 Assertions und zehn Konkurrenzfälle**, zusätzlich **neun Assertions
nach echtem PostgreSQL-Serverneustart**, insgesamt **165 Assertions erfolgreich**.
Der wiederholte Optionsauftrag liefert nach Neustart dieselbe Revision und erzeugt
keinen zweiten Ereigniseintrag. Geprüft werden außerdem Identitätsconstraints,
slotfremde Revisionszeiger, getrennte Freigaben und die Exportsperre.

Geändert wurden ausschließlich das isolierte PostgreSQL-Testmodell, dessen
Integrationstests und diese Dokumentation. Keine Produktionsmigration, kein
Import, kein Scannerlauf und keine Änderung des vorhandenen Datenbestands.
Als Nächstes ist die optionsbewusste Dateizuordnung einschließlich numerischer
und punktierter Optionsidentitäten zu prüfen.

### Optionsbewusster Testexport — 2026-10-19

Die zuvor dokumentierte pauschale Options-Exportsperre wurde im isolierten
PostgreSQL-Publisher durch eine explizite Dateizuordnung ersetzt. Verwendet wird
unverändert die vorhandene TranslationWorkbenchOption::key-Funktion:
`<Key>.options.<kodierte Optionsidentität>`. Die Datenbankidentität selbst wird
nicht umgeschrieben. Punkte, Prozentzeichen und Unicode werden ausschließlich
für den Dateipfad kodiert; 1 und 01 bleiben verschiedene Optionen.

Der statische AST-Leser kann für diesen Export numerische Schlüssel innerhalb
von options-Arrays lesen. PHP schreibt beispielsweise den Schlüssel '1' als
Integer 1 zurück. Außerhalb solcher Container bleiben numerische Schlüssel
abgewiesen; doppelte Schlüssel, unkodierte Punkte und ausführbare PHP-Ausdrücke
werden weiterhin abgewiesen. PHP-Dateien werden zur Prüfung nicht ausgeführt.
Der bisherige strenge Leser bleibt ohne explizite Freigabe unverändert.

Die Integration prüft Veröffentlichung und erneutes Einlesen aller getesteten
Optionsidentitäten, die Auflösung über den Runtime-Key und Arr::get, getrennte
Sprachen sowie die gezielte Änderung einer Option bei erhaltenen Geschwistern.
Ein skalarer Wert am selben Key kollidiert mit dem Optionscontainer und wird
abgewiesen, statt automatisch verschoben oder ersetzt zu werden. Zwei ausgewählte
Revisionen mit demselben physischen Zielpfad werden ebenfalls abgewiesen.
Dies ersetzt noch keine globale Prüfung sämtlicher unselektierter Keys auf
potenziell überlappende Dateipfade; diese gehört vor einen produktiven Export.

Der Unicode-Freigabetest deckte auf, dass initdb mit --no-locale bisher SQL_ASCII
verwendete. Der temporäre Testcluster wird jetzt ausdrücklich mit --encoding=UTF8
initialisiert. Keine Änderung an der Anwendungsdatenbank.

Ergebnis: **172 PostgreSQL-Assertions / zehn Konkurrenzfälle**, zusätzlich
**neun Assertions nach Serverneustart**, insgesamt **181 Assertions erfolgreich**.
Die vier bestehenden Basissuiten einschließlich der neuen Leserprüfung bestehen
mit **56 Tests / 274 Assertions**. Pint erfolgreich.

Keine produktive Migration oder Veröffentlichung, keine Übernahme von Bestandsdaten.
Locale-/Namespace-Regeln, globale Dateipfadkonflikte und die Übernahme-Prüfliste
bleiben vor einer vollständigen Generalprobe zu bearbeiten.

### Übergreifende Exportziele und Locale-/Namespace-Grenzen — 2026-10-19

Der isolierte PostgreSQL-Publisher prüft jetzt die ausgewählten Ziele gegen alle
aktuellen Wert-Slots mit aktuellem Key-Namen, auch wenn deren Revision nicht
freigegeben oder nicht ausgewählt ist. Gleiche Dateipfade und verschachtelte
Scalar-/Container-Konflikte werden vor Erstellung eines Plans und erneut vor
Dateischreibzugriffen abgewiesen. Die Prüfung verändert keine Identitäten.
Slots ohne aktuelle Revision und historische Key-Namen belegen dabei kein
aktuelles Ziel; diese Regel ist noch keine automatische Bereinigungsstrategie.

Ein nach der Planung ergänzter, nicht freigegebener Kind-Key blockiert im Test
auch die Veröffentlichung seines zuvor geplanten Elternwerts. Es wird keine
Datei geschrieben und kein Erfolg quittiert; der Plan kann regulär ohne Schreiben
geschlossen werden. Konflikte mit nicht ausgewählten Options-/Scalar-Aliaszielen
werden ebenfalls geprüft.

Zur Stabilisierung des vollständigen Identitätsbestands während der Transaktion
verwendet das Testmodell SHARE-Tabellensperren auf Key-Namen und Wert-Slots mit
NOWAIT, vor den vorhandenen Key-Zeilensperren. Bei einem bereits laufenden
Identitätsschreibzugriff wird sofort abgebrochen, nicht mit einer unsicheren
Momentaufnahme fortgefahren. Diese grobe Sperre ist ein isolierter Korrektheitsnachweis,
keine Produktivempfehlung für große Datenbestände. Eine produktive Lösung benötigt
gezielte Zielreservierungen samt konsistenter Sperrreihenfolge aller Schreibwege.

Locale-Schreibweisen bleiben exakt erhalten. Gleichzeitige Varianten, die sich
nur in Groß-/Kleinschreibung oder Bindestrich/Unterstrich unterscheiden, werden
als Mehrdeutigkeit gemeldet. Berücksichtigt werden aktuelle Slots desselben
Namespace sowie vorhandene Locale-Verzeichnisse. Das ist eine Kollisionsprüfung,
keine automatische BCP-47-Normalisierung oder Änderung der Fallback-Kaskade.
Ein gültiger de-AT-Export schreibt tatsächlich nach de-AT, nicht nach de-at.
Pfadbestandteile wie ../en werden abgewiesen.

Nichtleere Laravel-Namespaces bleiben ausdrücklich gesperrt, bis eine geprüfte
Loader-Root-Zuordnung vorliegt. Der bestehende Legacy-Dateigruppenbezeichner wird
nicht als Paket-Namespace interpretiert. Auch wird kein Namespace stillschweigend
auf das Anwendungs-lang-Verzeichnis abgebildet. Ein unabhängiger gültiger Export
bleibt trotz solcher anderweitigen Konflikte/ungültigen Einträge möglich.
Externe Dateisystemänderungen außerhalb des Publishers sind durch die Datenbank-
Tabellensperren nicht ausgeschlossen; die bestehenden Hash-Prüfungen bleiben nötig.

Nachweis: **186 PostgreSQL-Assertions / zehn Konkurrenzfälle**, zusätzlich
**neun Assertions nach Serverneustart**, insgesamt **195 Assertions erfolgreich**.
Pint und git diff --check erfolgreich. Neue Fälle stehen in
baseline-destination-cases.php; bestehende Optionsfälle berücksichtigen nun die
strengere Prüfung nicht ausgewählter aktueller Slots. Die dortige Freigabe des
Optionscontainers nach Konfliktnachweis ist eine ausdrückliche Testfixture-Änderung,
kein neuer produktiver Bereinigungs-/Archivierungsweg.

Keine Produktivdaten, Source-Dateien oder Sprachdateien verändert. Nächster
fachlicher Schritt: Herkunfts-/Konfliktliste für die tatsächliche Datenübernahme
und explizite Loader-/Locale-Zuordnung vor einer Generalprobe auf Kopie.

### Konkrete Übernahme-Prüfliste aus dem Snapshot — 2026-10-19

[transfer-checklist-2026-10-19.md](transfer-checklist-2026-10-19.md) fasst die
bereits erfassten aktuellen Evidenzen je exakter Locale-/Key-Kombination zusammen.
Die gleichnamige JSON-Datei enthält vollständige Wert-Hashes, Herkunfts-IDs,
Dateihashes, Optionsidentitäten und zugehörige historische Evidenzen.
build-transfer-checklist.py reproduziert beide Dateien ausschließlich aus dem
unveränderten transfer-readiness-2026-10-19.json; kein Bootstrap, keine DB-Verbindung.

2.152 Identitäten: 1.987 mit übereinstimmender DB-/Dateievidenz, zehn Wertkonflikte,
16 mit mindestens einem NULL-Wertnachweis, 122 nur DB, 17 nur Anwendungsdateien.
Das ist die vereinigte Sicht über Legacy-ok, Workbench-active, dynamische aktive
Werte und gelesene Anwendungsdateien, nicht die Summe früherer paarweiser Vergleiche.
NULL in einem Legacy-Eintrag bleibt auch dann ein Prüfpunkt, wenn andere Quellen
übereinstimmen. Identische Konfliktmengen in zwei Quellen werden ausdrücklich
nicht als übereinstimmender einzelner Wert behandelt.

Zusätzliche, überlappende Hinweise: 170 Identitäten mit Locale-Schreibvarianten,
sechs mit mehreren Datensätzen innerhalb derselben Quelle und 26 mit weniger
als drei Key-Segmenten. Letzteres ist ein Strukturhinweis entsprechend der
gewünschten verschachtelten Sprachdateien, keine automatische Umbenennung.
Alle Entscheidungen bleiben NULL. Gleichlautende Literale werden nicht über
unterschiedliche Keys hinweg zusammengeführt. Alte namespace-Werte werden
nicht als bestätigte Laravel-Paketzuordnung interpretiert.

Der Snapshot enthält ausschließlich Hashes, keine vollständigen Übersetzungstexte.
Die Liste zeigt deshalb Herkunft und Abweichung, entscheidet aber nicht über
inhaltliche Qualität. Noch nicht wertweise abgedeckte dynamische Altformate,
31 nicht gelesene Paketdateien und nur aggregierte Finding-/Link-Befunde sind
explizit als offene Abdeckung aufgeführt. Vor einer Generalprobe ist der Stand
frisch zu erheben; ein aktueller Scan oder Reset wurde nicht ausgeführt.

Acht Offline-Regressionstests erfolgreich (transfer-checklist-test.py), darunter
Konflikte innerhalb einer Quelle, NULL vs. Leerwertnachweis, historische Evidenz,
Locale-Varianten, Framework-Abgrenzung und deterministische Gruppierung.

### Inhaltliche Prüfung der zehn bekannten Wertkonflikte — 2026-10-19

[transfer-conflict-details-2026-10-19.md](transfer-conflict-details-2026-10-19.md)
listet die tatsächlichen Texte, Quell-IDs und kontextbezogenen Empfehlungen.
Das gleichnamige JSON enthält vollständige aktuelle Evidenz und aktive
Finding-Verweise. inspect-transfer-conflicts.php liest ausschließlich die zehn
bekannten Identitäten in REPEATABLE READ / READ ONLY; Sprachdateien werden
statisch geparst. Alle zehn Evidenzmengen stimmen noch mit dem Snapshot überein.
Alle Sprachdateihashes waren nach dem Lesen unverändert.

Vier Workbench-Vorschläge werden durch den Quellcode-Kontext gestützt:
Filter by category statt v, bis statt zu, und zwei Sprachbutton-Texte mit
:language statt generischem Sprache. Der deutsche Workbench-Wert All entries
ist selbst korrekturbedürftig; weitere All-Texte sind redaktionelle Unterschiede.
Bei ui.type stimmen Workbench und Datei bereits in Typ überein, nur Legacy
enthält Type. Die unzureichende Key-Tiefe bleibt eine getrennte UI-Entscheidung.

Keine Werte angewendet oder freigegeben. DB-Verweise sind kein neuer Scannerlauf;
insbesondere die 387 aktiven Verweise für ui.type wurden nicht alle neu verifiziert.
Syntax/Pint und JSON-Konsistenz geprüft: zehn Fälle, read_only=on, identische
Evidenz, unveränderte Dateien. Nächster separater Prüfblock sind die 16 fehlenden
Wertnachweise; danach die ausschließlich in der DB vorhandenen Werte.

### Die 16 NULL-Wertnachweise — 2026-10-19

[transfer-missing-details-2026-10-19.md](transfer-missing-details-2026-10-19.md)
und die gleichnamige JSON-Datei dokumentieren die aktuelle schreibgeschützte Prüfung.
Das vorhandene inspect-transfer-conflicts.php akzeptiert dafür ausdrücklich
missing_value_evidence; der bisherige Wertkonflikt-Aufruf bleibt unverändert nutzbar.

Alle 16 Evidenzmengen entsprechen weiterhin dem Ausgangssnapshot. Zwei Fälle
(admin.permissions.modals.edit.title, de/en) besitzen identische aktive
Workbench-/Dateitexte; nur Legacy #53/#54 ist NULL. Die übrigen 14 Fälle betreffen
sieben Keys mit Legacy-Status ok bei NULL, historischen obsolete Workbench-Texten
und ohne aktive Finding-Verweise. Keine aktuelle Datei enthält diese sieben Keys.

Ergänzende Textsuche in den aktuellen Bytes der 1.684 verbleibenden Manifestdateien:
keine fehlenden Pfade, eine seit dem Snapshot geänderte Datei. Für die sieben Keys
wurden ausschließlich ein beschreibender Critical-Datasets-Eintrag und zwei
TW-Graph-Beispieltexte gefunden. Die Suche ist ausdrücklich kein Scannerlauf;
neue Dateien außerhalb des gespeicherten Manifests sind nicht abgedeckt.

Kein Datenverlustausgleich durch automatische Reaktivierung. Vollständige Texte
bleiben Herkunftskandidaten, NULL darf sie nicht überschreiben. Die historischen
Keys erhalten ohne neue relevante Erfassung keine neue aktive Aufgabe.
Alle Entscheidungen bleiben offen; keine Datenbank-/Sprachdateiänderungen.
PHP-Syntax, Pint, Whitespace und Berichtskonsistenz (16 Fälle, 2 aktuell abgedeckt,
14 historische Fälle, READ ONLY, stabile Datei- und Evidenzhashes) geprüft.

### Die 122 ausschließlich exakt DB-seitig belegten Kombinationen — 2026-10-19

[transfer-database-only-details-2026-10-19.md](transfer-database-only-details-2026-10-19.md)
und das zugehörige JSON sichern tatsächliche Texte, Quell-IDs, Optionsidentitäten,
Finding-Verweise und vergleichbare Dateitexte. Alle 122 aktuellen Evidenzmengen
entsprechen dem Snapshot. Abfrage: inspect-transfer-conflicts.php database_only,
REPEATABLE READ / READ ONLY; Dateien statisch geparst und Hashstabilität geprüft.

Die 92 Legacy-Werte regionaler Locales sind in 71 Fällen in anders geschriebenen
Locale-Verzeichnissen identisch vorhanden, in weiteren 21 Fällen in de. Dies
ist kein Runtime-Fallback-Nachweis und keine automatische Locale-Normalisierung.
Die 16 dynamischen Werte sind identisch unter dem anderen Options-Parent-Key
vorhanden, den die aktuellen Dokumenten-Views verwenden (documents statt
documents.add_modal). Der Vergleich wurde anhand dieser konkreten Aufrufe
geprüft, nicht als allgemeine Key-Umschreiberegel implementiert.

14 skalare Workbench-Werte (sieben Keys in de/en) besitzen keine aktiven
Finding-Verweise. Die ergänzende Textsuche in 1.684 Manifestpfaden findet nur
zwei auskommentierte i18n-native-Zeilen für ui.assignable-through-ui. Test-/DEV-
Bezeichnungen bleiben dennoch gesicherte Evidenz; keine automatische Löschung.
Neue Dateien außerhalb des alten Manifests sind durch diese Textsuche nicht erfasst.

Alle 122 Texte sind gezielt dokumentiert; der Bericht ersetzt kein vollständiges
Backup. Keine Werte angewendet, Keys vereinheitlicht, Locales umbenannt oder
obsolete Einträge reaktiviert. PHP/Pint, Whitespace und Konsistenzprüfungen
(122 Fälle = 71 + 21 + 16 + 14; stabile Dateihashes; READ ONLY) erfolgreich.
Offen bleiben insbesondere dynamische Altformate und bislang nicht wertweise
inventarisierte Paketdateien vor der vollständigen Generalprobe auf Kopie.

### Dynamische Altformate und Paketübersetzungen — 2026-10-19

[transfer-extra-evidence-2026-10-19.md](transfer-extra-evidence-2026-10-19.md)
und das gleichnamige JSON erfassen die bislang offenen Formate inhaltlich.
inspect-transfer-extra-evidence.php liest PostgreSQL mit REPEATABLE READ / READ
ONLY, rollt zurück und parst Sprachdateien statisch ohne Provider-Aufrufe.

43 alte dynamische Werte gehören zu sechs Entries ohne translation_key. Für alle
43 existieren aktive aktuelle Optionswerte mit identischer Locale, Optionsidentität
und Text; dies bestätigt keine fachliche Key-Zuordnung. Die Kandidaten sind
explizit unverbindlich gelistet. SuggestedKeys werden nicht als Ersatz-Zuordnung
verwendet und bleiben unverändert.

96 Quellen, 247 Quellwerte, 121 Kandidaten und 26 Optionsbeobachtungen wurden mit
Payloads und IDs erfasst. 14 Beobachtungen enthalten Listen, zwölf Objekte.
Alle 247 Quellwerte heißen active, aber nur 177 hängen an aktiven Quellen;
70 hängen an obsolete Quellen. Diese Kaskade muss bei der Übernahme beachtet
werden. Native Labels ohne explizite Locale sind keine automatisch freigegebenen
en-Übersetzungen. Keine Reaktivierung oder Statuskorrektur durchgeführt.

31 lang/vendor/backup-Dateien ergeben 1.054 statisch lesbare Texte: 1.051 stimmen
mit der installierten Paketversion überein, drei deutsche Overrides unterscheiden
sich sprachlich (gepeichert, Gesund, dass). Beide Fassungen und Datei-Hashes sind
gesichert. Provider-/PackageTools-Code belegt den Namespace backup; dies schaltet
keinen neuen produktiven Veröffentlichungsweg frei.

Prüfungen: READ ONLY bestätigt, 43 Vergleichsfälle, alle 31 Dateien lesbar,
1.054 = 1.051 + 3, stabile Hashes der Projekt- und Paketdateien; PHP-Syntax,
Pint und Whitespace erfolgreich. Keine Produktionsdaten geändert.
Die bereits gezählten 952 nativen Legacy-Werte sind noch auf exklusive Inhalte
zu prüfen. Die Berichte ersetzen weiterhin keine vollständige Sicherung und
keine Generalprobe auf Datenbankkopie.

### Inhaltliche Prüfung der 952 nativen Altwerte — 2026-10-19

[transfer-native-values-2026-10-19.md](transfer-native-values-2026-10-19.md)
und das gleichnamige JSON sichern alle 952 Originalzeilen mit Herkunft und Meta.
inspect-transfer-native-values.php liest READ ONLY; build-native-value-review.py
klassifiziert offline ohne Schreibzugriff auf Anwendungsdaten.

909 Zeilen stammen laut source_type aus Sprachdateien, 43 aus fest definierten
Optionsarrays. Die 952 Zeilen enthalten 810 unterschiedliche native_label-Texte,
keine NULL-/Leertexte. Es handelt sich nicht um 952 freigegebene Übersetzungen.
Alle Wertzeilen heißen open, die Entries dagegen 798 open / 142 obsolete /
12 commented_out. Sprachherkunft muss aus gespeicherter Evidenz stammen;
keine neue Locale oder SuggestedKey-Zuordnung wurde erfunden.

797 Labels stimmen noch am gespeicherten Sprachdatei-Key überein, 136 sind
anderswo textgleich belegt, 13 enthalten JSON-Strukturen mit 39 textgleichen
Textblättern. Sechs Texte haben keinen aktuellen Zeugen im geprüften Umfang:
All guards, All roles, Assignment, Edit Role Management, No Value, Not A Number.
Alle sechs gehören zu obsolete Entries und bleiben gesicherte historische
Textnachweise. Kein automatisches Löschen oder Reaktivieren.

Textgleichheit begründet keine Finding-/Key-Zusammenführung. Dynamische
Quelllabels zählen für die aktuelle Textabdeckung nur bei active Quellwert UND
active übergeordneter Quelle. Die 13 JSON-Zeichenfolgen werden nicht als einzelne
Übersetzungen importiert; Original und Blattvergleich bleiben getrennt erhalten.

Prüfungen: READ ONLY bestätigt, 952 eindeutige Zeilen und Vergleiche,
952 = 797 + 136 + 13 + 6, 39/39 JSON-Textblätter mit Textzeugen,
elf referenzierte Dateien hashstabil; Syntax/Pint/Whitespace geprüft.
Keine Produktionsdaten verändert. Die gezielten Inhaltsprüfungen sind damit
für die bislang aufgelisteten Altformate durchgeführt, aber kein vollständiges
Backup oder Importplan. Nächster Schritt: offene Entscheidungen konsolidieren
und die Generalprobe auf gesicherter Kopie vorbereiten.

### Konsolidierte Entscheidungen und Vorbereitung der Generalprobe — 2026-10-19

[transfer-rehearsal-plan-2026-10-19.md](transfer-rehearsal-plan-2026-10-19.md)
trennt vollständige Evidenzaufnahme von aktivem Arbeitsbestand. Das zugehörige
Manifest bindet sieben Berichte per SHA256 und enthält 114 offene Arbeitseinträge
in acht Themenblöcken. Keine Entscheidung wird automatisch getroffen; Themen
und Texte dürfen sich überschneiden und sind nicht als Verlustzählung zu addieren.

prepare-transfer-rehearsal.py erstellt/prüft ausschließlich offline. Sechs Tests
prüfen Evidenzdrift, doppelte IDs, unzulässige Entscheidungen, falsche Backup-
Behauptungen sowie Reproduzierbarkeit des tatsächlichen Manifests. Alle erfolgreich.
Der Validator meldet bewusst rehearsal_ready=false / reset_ready=false.

pg_dump/pg_restore vorhanden, pg_dump 18.6. Noch kein vollständiger Dump, Restore,
Evidenzimport oder Neuaufbau durchgeführt. Die vorhandenen Berichte sind mehrere
zeitversetzte Inhaltsnachweise, kein zusammenhängendes Backup. Fixture-Capture
ersetzt nicht den echten Scanner. Der Plan benennt die sieben ausstehenden
Abnahmeschritte und die separate spätere UI-/Command-Anbindung ausdrücklich.
Nächster Schritt ist eine geschützte Sicherung mit Wiederherstellungsprüfung in
einem neuen isolierten Cluster; kein produktiver Reset und keine Datenänderung
in diesem Vorbereitungsschritt.

### Vollständiger DB-Dump und geprüfter Restore — 2026-10-19

[backup-restore-result-2026-10-19.md](backup-restore-result-2026-10-19.md)
und das gleichnamige JSON dokumentieren die erfolgreich ausgeführte Sicherung.
backup-and-restore-rehearsal.php verwendet einen exportierten READ-ONLY-Snapshot,
privates Git-ignoriertes Backup-Verzeichnis und einen neuen eigenen PostgreSQL-
Cluster ohne TCP. Kein Schreibzugriff auf die Anwendungsdatenbank.

Ergebnis: 326.519.713 Bytes Dump; 79 Tabellen / 2.630.711 Zeilen,
Spaltenstruktur und 69 Sequenzstände nach Restore identisch. 3.595 Projektdateien
kopiert und hashstabil; kein beobachteter DB-/Struktur-/Sequenzdrift während Aufnahme.
Cluster anschließend gestoppt; temporäre .pgpass entfernt; privates Verzeichnis
0700 / Dump 0600 geprüft, Dump-SHA256 nach Abschluss erneut verifiziert.

Sicherung unter storage/app/private/dev/workbench-rehearsal/20261019-075456-18dfd037.
Wiederherstellungsquelle ist der private Dump; gestoppter /tmp-Cluster ist nur
Arbeitskopie. Keine Offsite-Kopie. Kein vollständiges Deployment-Backup: globale
Rollen, .env, Uploads, Abhängigkeitsinstallationen und externe Dienste nicht in
Source-Kopie; Restore mit isoliertem Owner ohne produktive ACLs.

Die ersten beiden Vorbereitungsschritte sind damit ausgeführt; das ursprüngliche
Manifest bleibt historisch unverändert. Kein neuer Workbench-Datenbestand,
kein produktiver Reset und keine Übernahmeentscheidung. Nächster Schritt:
separates Evidenz-Staging aus der geprüften Kopie, danach echter Neuaufbau.
# Nachweisablage auf der geprüften Kopie – 2026-10-19

Die isolierte Nachweisablage wurde erfolgreich aufgebaut: 122.133 unveränderte Datensätze aus 26 Tabellen, 3.595 Dateinachweise und sieben ausschließlich im restaurierten Archiv belassene History-/Lauftabellen. Alle 33 Tabellen wurden gegen die Sicherung geprüft. Drei negative Integritätsprüfungen bestanden. Ein eigener privater Dump wurde erstellt; der Testcluster ist gestoppt. Kein produktiver Datenbankzugriff und keine aktive Generation wurden durchgeführt.

Details und Grenzen: [evidence-staging-result-2026-10-19.md](evidence-staging-result-2026-10-19.md), maschinenlesbares Ergebnis: [evidence-staging-result-2026-10-19.json](evidence-staging-result-2026-10-19.json).

## Zuordnung zur neuen Generation – 2026-10-19

Der offline erzeugte [Übernahmeplan](generation-mapping-2026-10-19.md) ordnet alle 33 gesicherten Tabellen ausdrücklich ein. Rohfelder werden durch eine neue statische Quellerfassung belegt; gleichnamige oder ähnliche Altfelder sind kein ausreichender Herkunftsnachweis. Alte Freigaben und Timeline-Ereignisse werden nicht aktiv übernommen. Der maschinenlesbare [Plan](generation-mapping-2026-10-19.json) bindet Sicherung, Staging-Dump und Quelltabellen-Fingerabdrücke. Sieben Tests bestanden. Kein aktiver Import und kein produktiver Datenbankzugriff; nächster offener Schritt ist der Adapter für die echte Quellerfassung auf der Sicherungskopie.

## Quellerfassung auf der Sicherungskopie – 2026-10-19

Der vorhandene Scanner wurde ohne Anwendungsbootstrap und Datenbankzugriff auf 1.684 gesicherte Quelldateien angewendet. Ergebnis: 20.120 getrennte Kandidaten, darunter 121 ausgeschlossene Kommentar-Treffer; 2.207 Key-Kandidaten haben einen exakten Textbeleg in den 20 statisch gelesenen englischen Sprachdateien. Keine aktiven Findings erzeugt. 14 Adapter-Assertions bestanden. Der nächste notwendige Schritt ist die Validierung des umgebenden PHP-/Blade-Kontexts; die bisherige Mustersuche allein reicht für eine aktive Übernahme nicht aus. Details: [Quellerfassung](copied-source-capture-2026-10-19.md), [Prüfsummen und Zähler](copied-source-capture-2026-10-19.json).

## Kontextprüfung der Sicherungserfassung – 2026-10-19

Die statische Kontextprüfung ordnet sämtliche 20.120 vorhandenen Kandidaten zu: 20.012 syntaktische Aufrufe, 105 Kommentare, drei PHP-Stringinhalte. Die Capture-IDs bleiben unverändert. 16 zuvor pauschal als Kommentar behandelte Treffer wurden korrigiert; keine aktiven Findings erzeugt. 42 Adapter-/Kontextprüfungen und sieben Mappingtests bestanden. Details und Grenzen: [Kontextbericht](copied-source-context-2026-10-19.md), [maschinenlesbares Ergebnis](copied-source-context-2026-10-19.json). Die Vollständigkeit bezieht sich nur auf entdeckte Kandidaten, nicht auf eine vollständige Erkennung aller möglichen Quellkonstrukte.

## Probe-Erfassung im neuen Schema – 2026-10-19

19.940 bestätigte statische Kandidaten wurden als neue Findings in einer separaten Testdatenbank erfasst; 108 ausgeschlossen, 72 offen. Alle 20.120 Kandidaten sind über einen unveränderlichen Ergebnisnachweis bilanziert. Rücklesevergleich, identischer Retry, SuggestedKey-Schreibschutz und Rollback nach einem Teilimport bestanden. Keine Keys, Zuordnungen, Revisionen, Freigaben oder Timeline-Events angelegt; produktiver Bestand unverändert. Details: [Probe-Erfassung](static-capture-import-2026-10-19.md), [Prüfergebnis](static-capture-import-2026-10-19.json). Nächster Schritt: Verhalten eines echten erneuten Scans mit unveränderten, verschobenen und entfernten Fundstellen auf der Kopie.

## Erneute Scan-Zyklen – 2026-10-19

Auf einer weiteren restaurierten Testdatenbank wurden sieben echte Scan-Zyklen an einer kopierten Quelldatei geprüft: unverändert (zweimal), verschoben, zusätzlich kopiert, entfernt, erneut vorhanden und textuell geändert. Vier unsichere Fälle wurden ohne Änderungen abgewiesen. 16 Integrations-Assertions und erneut 42 Kontext-/Adapterprüfungen bestanden. Findings außerhalb der Datei und die gesicherte Ausgangsdatei blieben unverändert. Keine produktiven Anpassungen. Details und Grenzen: [Rescan-Probe](copied-rescan-2026-10-19.md), [Ergebnis](copied-rescan-2026-10-19.json). Als nächster fachlicher Schritt bleiben die 72 offenen Kandidaten und danach die getrennte Key-/Finding-Zuordnung.

## Untersuchung der 72 offenen Kandidaten – 2026-10-19

Vollständig und ohne produktive Änderungen untersucht: sieben reine JS-Serialisierungen, sechs serialisierte Props mit übersetztem Default, 28 explizite Optionsquellen (zwölf Keys, 102 englische Optionswerte), drei überschreibbare Literal-Prop-Defaults, 18 dynamische Übersetzungsargumente sowie zehn Literal/Key-Prüffälle. Acht neue Prüfungen bestanden. Keine automatischen Entscheidungen; die bisherige Datenbankbilanz bleibt unverändert. Details: [Bericht](pending-capture-review-2026-10-19.md), [Einzelnachweise für alle 72 Kandidaten](pending-capture-review-2026-10-19.json).

## Quellenrollen auf der Testkopie – 2026-10-19

Alle 20.120 Captures haben einen zusätzlichen unveränderlichen Rollennachweis: 19.940 erfasste und 31 offene Übersetzungsquellen, 28 Optionsquellen, 13 Verwendungen und 108 ausgeschlossene Kontexte. Die 19.940 Findings blieben vollständig unverändert. Keine produktiven Änderungen, keine zusätzlichen skalaren Findings aus Optionen/Serialisierung. 15 Rollen- und 11 PostgreSQL-Assertions bestanden; acht Review- und 42 Kontextprüfungen ebenfalls. Die 72 ursprünglichen Prüffälle bleiben im Importnachweis offen. Details und Grenzen: [Quellenrollen](source-roles-2026-10-19.md), [Prüfergebnis](source-roles-2026-10-19.json).

## Wertquellen und Optionsargumente – 2026-10-19

Alle 31 offenen Übersetzungsquellen und 28 Optionsquellen sind weiter aufgeschlüsselt. Sieben Fundstellen haben konkrete Producer-Beobachtungen, drei einen überschreibbaren Default, elf benötigen weitere Laufzeitevidenz und zehn eine Literal/Key-Entscheidung. Die 28 Optionsaufrufe enthalten getrennte Owner-, Identitäts-, Fallback- und Locale-Nachweise. Keine Datenbank- oder Quellcodeänderung; keine automatischen Entscheidungen. 16 neue Argumenttests sowie acht Review- und 15 Rollentests bestanden. [Bericht](source-arguments-2026-10-19.md), [Einzelnachweise](source-arguments-2026-10-19.json).

## Persistierte Herkunftsnachweise – 2026-10-19

Auf einer frisch restaurierten Quellenrollen-Kopie wurden drei additive Evidenztabellen angelegt. 59 Fundstellennachweise und sechs Wertlieferanten wurden gespeichert und zurückgelesen. Retry, Vollständigkeit, falsche Rollen/Reviews, Teilimport-Rollback und Schreibschutz geprüft: 82 PostgreSQL-Assertions sowie erneut 16 Argument- und 15 Rollentests bestanden. Alle 19 bestehenden Tabellen blieben inhaltlich unverändert. Kein produktiver Eingriff; alle fachlich offenen Entscheidungen bleiben offen. [Bericht](source-argument-persistence-2026-10-19.md), [Ergebnis](source-argument-persistence-2026-10-19.json).

## Key-/Finding-Zuordnung auf der Kopie – 2026-10-19

2.207 bestehende Key-Verwendungen sind exakt belegt. In einer neuen restaurierten Testgeneration wurden 768 Keys und 1.792 Finding-Verknüpfungen angelegt. Elf strukturell problematische Keys mit 415 Findings bleiben blockiert; keine automatische Umbenennung und keine Zusammenlegung gleichlautender Literale. Zehn Planner- und elf PostgreSQL-Assertions bestanden. Die 19 übrigen bestehenden Tabellen blieben unverändert, insbesondere Findings, SuggestedKeys, Werte und Reviews. Kein produktiver Eingriff. [Bericht](existing-key-links-2026-10-19.md), [Ergebnis](existing-key-links-2026-10-19.json).

## Sprachdatei-Baseline auf der Kopie – 2026-10-19

2.016 Werte aus 54 App-Sprachdateien in neun Locales separat gespeichert: 1.597 exakt verknüpft, 22 wegen Key-Struktur blockiert, 397 unzugeordnet erhalten. 32 Vendor-Dateien explizit außerhalb dieses Umfangs dokumentiert. Keine künstlichen Regional-Fallback-Werte, Revisionen, Benutzerereignisse oder Freigaben erzeugt. Alle 24 vorhandenen Tabellen blieben unverändert. Zwölf PostgreSQL- und sechs Reader-Assertions bestanden. [Bericht](published-values-2026-10-19.md), [Ergebnis](published-values-2026-10-19.json).

## Getrennter Leseweg – 2026-10-19

Read-only-Modell für veröffentlichten Ausgangswert, aktuellen skalaren Entwurf, kontextgebundenes Review und ausdrücklich übergebene Locale-Kette implementiert. 768 Keys mit `de-at → de → en` geprüft, darunter 760 Fallbacks. 2.318 Assertions bestanden; alle 27 Baseline-Tabellen unverändert. Synthetische Review-/Entwurfsfälle ausschließlich in einer separaten, nicht exportierten Fixture-Datenbank. Bestehender Persistenztest nach gemeinsamem Review-Kontext-Refactoring: 186 Assertions, zehn Konkurrenzfälle und neun Neustart-Assertions bestanden. Keine produktive/UI-Anbindung. [Bericht](baseline-reader-2026-10-19.md), [Ergebnis](baseline-reader-2026-10-19.json).

## Lesende Vorschau in Workflow – 2026-10-19

Tab **Baseline preview** ergänzt. Ein privater, expliziter Export stellt 972 Keys aus der isolierten Generation bereit; der Webrequest verbindet sich nicht mit der Testdatenbank. Dateiwert, Entwurf, Review und Fallback sind getrennt sichtbar. Drei neue Pest-Tests / 15 Assertions bestanden. Fehlender Export wird erklärt; echte Entwürfe/Reviews sind im Snapshot noch nicht vorhanden. [Aufruf, Umfang und Aktualisierung](baseline-preview-2026-10-19.md).

## Kontext der unzugeordneten Werte – 2026-10-19

397 unzugeordnete Werte weiter untersucht: 184 Werte / 92 Keys besitzen eine exakt belegte Optionsquelle; 213 Werte / 101 Keys bleiben hinsichtlich ihrer Verwendung offen. Keine neuen Key-Zuordnungen oder Änderungen an Werten. Baseline preview zeigt den zusätzlichen Kontext mit Quellaufrufen, ohne Laufzeitverwendung jeder Option zu behaupten. Zehn Klassifikator-Assertions und fünf Vorschau-Pest-Tests / 23 Assertions bestanden. [Bericht](unassigned-context-2026-10-19.md), [Einzelnachweise](unassigned-context-2026-10-19.json).

## Vollständiger Vorschau-Abgleich – 2026-10-19

Alle 972 exportierten Keys und drei Fallbackketten offline gegen die gesicherten
Sprachdateien geprüft: 25.861 Prüfungen erfolgreich. Ein Key besitzt ausschließlich
einen de-at-Wert; für de/en bleibt der Leerzustand ausdrücklich erhalten. Keine
Datenbankverbindung oder Datenänderung. Die aktuelle Vorschau umfasst drei der neun
Baseline-Locales. [Ergebnisse und Grenzen](baseline-preview-2026-10-19.md#vollständiger-offline-abgleich-der-vorschau),
[maschinenlesbares Ergebnis](baseline-preview-verification-2026-10-19.json).

## Korrekturvorschläge getrennt persistieren – 2026-10-19

Additiven Persistenzvertrag auf einer frisch restaurierten Baseline geprüft:
Vorschläge mit Bearbeiter/Begründung und Ausgangsfingerprint, exakter Retry,
abweichende Wiederholung, fehlender Kontext sowie Unveränderlichkeit. 17 Assertions
bestanden. Alle 27 vorher vorhandenen Tabellen unverändert. Ein synthetischer
Vorschlag wurde ausschließlich auf der Kopie gespeichert; keine Korrektur
angewendet und kein produktiver/UI-Schreibweg aktiviert.
[Bericht und Grenzen](key-correction-proposal-2026-10-19.md),
[Ergebnis](key-correction-proposal-2026-10-19.json).

## Bestätigte Key-Korrektur auf der Kopie – 2026-10-19

DB-Anwendung des synthetischen Vorschlags erprobt: ein regulärer Key, 387 getrennte
Finding-Verknüpfungen, Anwendungsnachweis und Ereignis. Roh-Findings und die beiden
ursprünglichen Sprachwerte blieben unverändert; keine Datei-/Produktivänderungen.
Rollback nach der ersten Verknüpfung, nachträglich belegter Ziel-Key, Retry und
doppelte Anwendung geprüft. [Bericht](key-correction-application-2026-10-19.md),
[Prüfergebnis](key-correction-application-2026-10-19.json).

## Arbeitswerte nach Key-Korrektur lesen – 2026-10-19

Gemeinsamen Baseline-Leseweg um einen separaten Reader für ausdrücklich angewendete
Korrekturen ergänzt. Die alten Dateiwertnachweise werden je Locale als
Arbeitsgrundlage mit voller Herkunft gelesen; keine Veröffentlichung unter neuem
Namen und keine erzeugten Revisionen/Freigaben. Erweiterte isolierte Probe:
53 Assertions erfolgreich, lesender Zugriff ohne Tabellenänderungen.
[Details](key-correction-application-2026-10-19.md#gesicherte-werte-als-arbeitsgrundlage-lesen).

## Arbeitsgrundlage in einen Entwurf übernehmen – 2026-10-19

Explizite Übernahme eines vorhandenen Locale-Werts unter dem korrigierten Key auf
der isolierten Kopie ergänzt. Gemeinsamer Revisionsschreibweg, Herkunftsnachweis,
Kontextprüfung, Überschreibschutz und atomarer Rollback; keine Freigabe oder
Veröffentlichung. 65 Assertions erfolgreich. Bestehende Persistenzprüfung:
186 Assertions, zehn Parallelitätsfälle und neun Neustart-Assertions erfolgreich.
[Details](key-correction-application-2026-10-19.md#arbeitsgrundlage-ausdrücklich-als-entwurf-übernehmen).

## Freigabezyklus des übernommenen Entwurfs – 2026-10-19

Gemeinsamen Review-Schreibweg extrahiert und am übernommenen Entwurf geprüft:
explizite Freigabe, Kontextänderung, Retry und Widerruf. Herkunft bleibt erhalten;
Freigabe erzeugt keine Veröffentlichung. 79 Assertions erfolgreich, bestehende
Persistenz-/Veröffentlichungsprüfungen ebenfalls erfolgreich. Datei-Export dieses
konkreten übernommenen Entwurfs bleibt ein nachfolgender Integrationsschritt.
[Details](key-correction-application-2026-10-19.md#übernommenen-entwurf-prüfen-und-freigabe-widerrufen).

## Datei-Export nach Entwurfsübernahme – 2026-10-20

Bestehenden Publisher mit der isolierten Wiederherstellung verbunden; Ausgabe
ausschließlich unter dem temporären Testcluster. Freigabe, späterer Widerruf,
Dateischreiben, Unterbrechung und Wiederanlauf am übernommenen Wert geprüft.
94 Assertions erfolgreich; bestehende Persistenz-/Publikationstests ebenfalls
erfolgreich. Produktive Sprachdateien und UI bleiben unverändert.
[Details](key-correction-application-2026-10-19.md#datei-export-des-übernommenen-entwurfs--2026-10-20).

## Veröffentlichungsstand wieder einlesen – 2026-10-20

Separaten read-only Verifikationsreader ergänzt: abgeschlossener Export,
Datei-Prüfsumme, statisch gelesener Spracheintrag, unveränderliche Revision und
Herkunftsbeleg werden gemeinsam geprüft. Fehlende/veränderte Dateien und nicht
veröffentlichte Revisionen werden abgewiesen. Ursprüngliche Baseline unverändert.
Integrierter Probelauf: 103 Assertions erfolgreich.
[Details](key-correction-application-2026-10-19.md#exportierten-stand-wieder-einlesen--2026-10-20).

## Prüfbericht in der Baseline-Vorschau – 2026-10-20

Die Baseline-Vorschau zeigt am Ende einen eigenen Flux-Callout mit aufklappbaren
Prüfschritten des synthetischen Probelaufs. Die Anzeige liest ausschließlich den
festen JSON-Prüfbericht; sie startet keine Befehle und verbindet keine Datenbank.
Zeitpunkt, Test-Key und Assertions werden angezeigt. Fehlende Schrittbelege werden
nicht als Erfolg gewertet; abweichende Dump-Prüfsummen zur angezeigten Baseline
werden kenntlich gemacht. Der Bericht ist unabhängig von der aktuellen Key-Auswahl
und enthält ausdrücklich keine produktive Freigabe.

Der Probelauf wurde erneut ausgeführt: 103 Assertions erfolgreich. Die neuen
UI-/Validierungstests bestehen. Im gemeinsamen Lauf mit BaselineReaderPreviewTest
bleibt eine bestehende Markup-Erwartung offen: Der Mehrzeiligkeitstest erwartet
`pre`/`div.whitespace-pre-wrap`; die individuell überarbeitete View verwendet
inzwischen Flux-Textarea beziehungsweise Callout-Text. Diese Darstellung wurde
für die Berichtsanzeige nicht umgebaut.

### Leserechte des UI-Prüfberichts korrigiert

Die UI liest nun `storage/app/private/workbench-previews/workflow-rehearsal.json`
statt direkt den mit 0600 geschützten Auditbericht. Der Probelauf stellt diese
separate Kopie atomar mit 0644 bereit, entsprechend der bestehenden
Baseline-Vorschau. Der Auditbericht bleibt privat. Fehlende, unlesbare oder
ungültige Vorschau-Dateien liefern keine Daten statt einer unbehandelten Exception.

## Quellcode-Änderungsvorschau – 2026-10-20

`SourceKeyChangePreview` erzeugt ausschließlich vorgeschlagene Bytes, keine
Dateiänderungen. Voraussetzung sind passende Datei-Prüfsumme, exakte Byteposition,
unveränderte erfasste Expression und bestätigter Aufrufkontext. Nur das statische
erste Argument wird ersetzt; weitere Argumente und Anführungszeichen bleiben
unverändert. Doppelte/überlappende Positionen sowie Kommentare werden abgewiesen.

Der Offline-Probelauf `preview-key-source-changes.php` prüfte alle 387 belegten
Fundstellen von `ui.type` in 44 gesicherten Dateien mit dem synthetischen Ziel
`rehearsal.correction.type`. Keine Datenbankverbindung und keine Änderungen an
Quell-/Sprachdateien. Ergebnis inklusive einzelner Vorher-/Nachher-Argumente:
[source-key-change-preview-2026-10-20.json](source-key-change-preview-2026-10-20.json).
Drei gezielte Tests mit sieben Assertions bestanden.

Die Vorschau ist zusätzlich in den integrierten Korrekturprobelauf eingebunden;
dieser konnte diesmal nicht erneut ausgeführt werden, weil der bisherige
`/tmp/tw-restore-*`-Cluster nicht mehr vorhanden ist. Sein letzter erfolgreicher
Bericht bleibt unverändert. Der erfolgreiche Offline-Abgleich ist kein Nachweis
für den aktuellen produktiven Quellstand und keine Freigabe zum Umschreiben.

### Integrierte Prüfung wiederhergestellt – 2026-10-20

Der Korrekturprobelauf legt nun für jeden Lauf einen eigenen isolierten
PostgreSQL-Cluster unter `/tmp` an. Die geprüften Sicherungen bleiben unverändert;
ein historischer temporärer Cluster ist keine Voraussetzung mehr. Der neue
Cluster bietet ausschließlich einen privaten Unix-Socket und wird nach dem
Probelauf gestoppt.

Vollständiges Ergebnis: 193 Assertions erfolgreich, einschließlich der
Quellcode-Änderungsvorschau für 387 getrennte Fundstellen in 44 gesicherten Dateien.
Der aktuelle JSON-Bericht wurde auch als lesbare UI-Vorschau bereitgestellt.
Die Schritttabelle zeigt nun zusätzlich die rein lesende Quellcode-Vorschau.
Quellcode-Umschreiben und produktive Schreibaktionen bleiben unangebunden.

## Quellcodeänderung auf Arbeitskopie und Rescan – 2026-10-20

`rehearse-key-source-application.php` erstellt ein neues privates Arbeitsverzeichnis
mit den 44 betroffenen Dateien. Nach Prüfung der Ausgangsprüfsummen werden die
vorgeschlagenen Bytes atomar in diese Arbeitskopie geschrieben. Der bestehende
Scanner wird ohne produktiven Laravel-Bootstrap und ohne Datenbank davor und
danach ausgeführt. Der bestehende Capture-Adapter bestätigt den Aufrufkontext.

Alle 387 ausgewählten Fundstellen liefern den synthetischen neuen Key. Die
übrigen Scanner-Aufrufe werden unter Berücksichtigung verschobener Bytepositionen
abgeglichen; ihre Expressions und Argumentwerte bleiben unverändert. Es entstehen
keine zusätzlichen Scanner-Aufrufe. 12.009 Assertions erfolgreich.

Die gesicherte Ausgangskopie und produktive Dateien bleiben unverändert.
Einzeländerungen liegen in `changes.json` des im Ergebnis angegebenen privaten
Arbeitsverzeichnisses. Das Ergebnis ist in
[source-key-application-2026-10-20.json](source-key-application-2026-10-20.json)
festgehalten. Noch nicht enthalten: Datenbank-Reconciliation des Rescans,
Nachfolgerverweise und Historienereignisse für diese tatsächliche Codeänderung.

## Explizite Rewrite-Nachfolger in der Datenbank – 2026-10-20

Der Arbeitskopie-Rescan liefert nun einen prüfsummengesicherten Übergangsnachweis
je ausgewähltem Finding. Der integrierte Probelauf prüft diese Dateien gegen die
bestätigte Vorschau und verwendet `CorrectedSourceReconciler` ausschließlich für
diese expliziten Übergänge. Keine Textgleichheits-Heuristik und keine
Reaktivierung obsoleter Einträge.

Ergebnis auf der isolierten Kopie: 387 bisherige Findings stillgelegt, 387
getrennte Nachfolger mit `predecessor_id` erstellt und jeweils mit derselben
Key-ID verknüpft. Alte Rohdaten und SuggestedKeys bleiben erhalten. Scanbeleg,
Nachfolger, Links und ein nachvollziehbares Ereignis werden atomar geschrieben.
Ein Fehler nach dem ersten Nachfolger rollt alles zurück. Derselbe Auftrag wird
ohne Schreiben wiederholt; ein neuer Auftrag mit identischem Rescan bestätigt
die Nachfolger ohne zusätzliche Findings oder Historienereignisse (nur ein
Operationsbeleg). Abweichende, doppelte und veraltete Übergänge werden abgewiesen.

Die veränderten aktiven Finding-Verknüpfungen machen die bisherige Review-Freigabe
ungültig. Der historische Veröffentlichungsbeleg und die exportierten Bytes
bleiben weiter verifizierbar. Gesamtlauf: 1.027 Assertions erfolgreich;
Arbeitskopie/Rescan: weiterhin 12.009 Assertions erfolgreich.

Grenze: Dies ist die explizite Reconciliation der ausgewählten Rewrite-Findings,
kein vollständiger neuer Scanlauf über den gesamten Datenbestand. Eventuell nur
positionsverschobene, nicht ausgewählte Aufrufe müssen separat abgeglichen werden.
Produktive Daten und Quelldateien bleiben unverändert.

## Nicht ausgewählte Positionsverschiebungen – 2026-10-20

Der Arbeitskopie-Rescan ermittelt zusätzlich 16 unveränderte Literal-Aufrufe,
deren Spalte durch die längeren Keys um jeweils 18 Bytes verschoben wird.
Der gespeicherte Übergangsnachweis enthält jeweils die vollständige alte und
neue Erfassung. Identifikation erfolgt anhand Datei, Zeile, Byte-Spalte und
unveränderter Expression, nicht anhand gleicher Literaltexte.

Der vorhandene `CorrectedSourceReconciler` verarbeitet nun auch explizite reine
Positionsverschiebungen. In diesem Modus müssen Expression und Literalinhalt
gleich bleiben und die Position tatsächlich abweichen. Raw-Literal,
SuggestedLiteral und SuggestedKey werden erhalten; die bestehende Zuordnung
bleibt gleich, einschließlich fehlender Verknüpfung. Vorgänger werden archiviert,
Nachfolger verweisen auf diese. Rollback und identischer erneuter Rescan sind
auch hierfür geprüft. Es entsteht kein neuer Key durch eine Positionsänderung.

Gesamter isolierter Probelauf: 1.082 Assertions erfolgreich, 387 geänderte
Key-Aufrufe und 16 zusätzlich verschobene Aufrufe. Die Baseline-Vorschau zeigt
den zusätzlichen Prüfschritt. Produktive Dateien und Daten bleiben unverändert.

## Quellcode-Vergleich in der Baseline-Vorschau – 2026-10-20

Neuer aufklappbarer Callout „Recorded source changes“ mit einer durchsuchbaren
Flux-Dateiauswahl. Pro Datei zeigt die Tabelle Finding-ID der isolierten Generation,
ursprüngliche Zeile/Byte-Spalte sowie altes und vorgeschlagenes Key-Argument.
Eine weitere Tabelle zeigt unveränderte Aufrufe mit verschobener Position.

Die Anzeige ist ausdrücklich unabhängig von der Key-Auswahl oben und beschreibt
nur den synthetischen Probelauf. Sie liest ausschließlich den atomar mit 0644
bereitgestellten Snapshot `storage/app/private/workbench-previews/source-changes.json`.
Dateiauswahl ist nur ein Filter im Snapshot, kein Dateizugriff. Fehlende/unlesbare
oder ungültige Snapshots werden abgefangen. Keine Scan- oder Schreibaktionen.

Arbeitskopie/Rescan erneut erfolgreich: 12.009 Assertions, 44 Dateien, 387
Key-Änderungen und 16 Positionsverschiebungen. UI-Auswahl, unbekannte Pfade und
fehlende/ungültige/unlesbare Snapshots sind durch gezielte Tests abgedeckt.

## Aktueller Quellcode gegen aufgezeichnete Änderungen – 2026-10-20

Die ausgewählte Datei wird zusätzlich anhand ihrer Prüfsumme mit dem
aufgezeichneten Ausgangsstand und dem vorgeschlagenen Ergebnis verglichen.
Die vorhandene `BaselineFileFreshness`-Prüfung übernimmt Dateizugriff und
Projektgrenzen; nur eindeutig im Snapshot enthaltene Dateien werden geprüft.
Unbekannte Pfade aus der Auswahl führen zu keinem Dateizugriff.

Die UI unterscheidet Ausgangsstand, vorgeschlagenen Stand, abweichenden Inhalt,
fehlende/unlesbare Dateien und fehlende Prüfnachweise. Prüfzeit und „Check again“
machen den Zeitpunkt nachvollziehbar. Übereinstimmung mit dem Ergebnis ist
ausdrücklich kein Nachweis einer Workbench-Anwendung und keine Schreibfreigabe.

Der lesende Abgleich ergab für alle 44 Dateien Übereinstimmung mit dem
gesicherten Ausgangsstand. Produktive Quelldateien und Datenbank bleiben
unverändert. Gezielte Tests decken Statuswechsel, unveränderte Dateibytes,
Pfadgrenzen und die gerenderte Anzeige ab.

## Gemeinsame Prüfung der Baseline-Bausteine – 2026-10-20

Der bisher offene Mehrzeiligkeitstest wurde auf die aktuelle Flux-Darstellung
umgestellt. Er prüft den tatsächlichen Textarea-Inhalt und die getrennten
veröffentlichten Werte/Entwürfe im gerenderten DOM, statt ein nicht mehr
verwendetes `pre` vorauszusetzen. Dabei wurde die fehlende Erhaltung von
Zeilenumbrüchen bei veröffentlichten Tabellenwerten ergänzt. Die Ergebnis-
Textarea ist ausdrücklich readonly; das vom Nutzer angepasste Layout bleibt.

Diese Prüfung betrifft Vorschau, Freigabe-, Veröffentlichungs- und
Quelländerungsbausteine im Testbetrieb. Sie ist keine Freigabe eines produktiven
Resets oder einer automatischen Übernahme. Der bisherige produktive Workflow
wird durch diese Anzeigeänderungen nicht umgeschaltet.

Gemeinsamer Pest-Lauf: alle 94 Tests mit 435 Assertions erfolgreich.
Gezielter Pint-Check der geänderten Testdatei erfolgreich. Ein zusätzlich
gestarteter `--dirty`-Check meldet bestehende Formatabweichungen in drei älteren
Audit-Skripten und dem Scanner; diese Dateien wurden hier nicht umformatiert.

## Vollständiger Quellabgleich in der Vorschau – 2026-10-20

„Check all recorded source files“ aktiviert in „Recorded source changes“ den
lesenden Abgleich des gesamten aufgezeichneten Dateisatzes. Solange aktiviert,
wird er bei weiteren Renderings frisch berechnet. Zusammenfassung und Prüfzeit
zeigen den aktuellen Stand; Abweichungen sind über „Inspect“ direkt auswählbar.
Der bestehende Einzeldatei-Prüfer wird wiederverwendet.

Bereits dem vorgeschlagenen Ergebnis entsprechende Dateien bleiben prüfbedürftig:
Die Bytes allein beweisen keine erfolgreiche Workbench-Operation. Fehlende,
unlesbare oder abweichende Dateien sowie fehlende Prüfsummen und mehrdeutige
doppelte Pfade werden nicht als passender Ausgangsstand gewertet. Ein leerer
Dateisatz gilt ebenfalls nicht als vollständig geprüft.

Ergebnis auf den aktuellen Quelldateien: 44 von 44 entsprechen dem Ausgangsstand.
Gezielte Pest-Prüfung: 9 Tests, 58 Assertions erfolgreich; Pint erfolgreich.
Der Abgleich prüft weder aktuelle Datenbankzuordnungen noch Sprachdateien und
erteilt keine Schreibfreigabe. Produktive Daten bleiben unverändert.

## Quelländerungsvorschau für einen eingegebenen Ersatz-Key – 2026-10-20

Nach „Check proposal“ steht bei einem strukturell gültigen und im Snapshot
konfliktfreien Vorschlag „Source changes for your proposed key“ bereit.
Die durchsuchbare Dateiauswahl enthält ausschließlich die für diesen Key
aufgezeichneten Quelldateien. Die Tabelle zeigt Finding-ID der Baseline,
Zeile/Byte-Spalte und das alte/neue Key-Argument. Änderungen am eingegebenen
Key oder an der Key-Auswahl entfernen die bisherige Vorschau.

`BaselineProposedSourceChanges` prüft den Vorschlag und die Dateievidenz erneut.
Prüfsummen, Projektgrenze, konkrete Positionen und statischer Aufruf müssen
übereinstimmen. Der Parser prüft die Prüfsumme nochmals anhand der tatsächlich
gelesenen Bytes. Fehlende, geänderte, außerhalb liegende oder widersprüchliche
Quellen werden abgewiesen. Kein Scan, keine Dateiänderung, keine DB-Verbindung.
Andere Argumente und der übrige Quelltext bleiben in den vorgeschlagenen Bytes
unverändert; vollständige Dateiinhalte werden nicht an die UI ausgegeben.

Die bereits geprüften Klassen `SourceKeyChangePreview` und `CopiedSourceContext`
wurden aus `tests/Support/TranslationWorkbench` nach
`packages/gunreip/laravel-translation-workbench/src/Support` verschoben.
Die bisherigen Tests und Audit-Skripte verwenden dieselben Klassen; keine
zweite Parser-Implementierung und keine Kompatibilitätsaliase. `nikic/php-parser`
ist nun ausdrücklich als Laufzeitabhängigkeit in Projekt und Package deklariert.
Composer hat keine Paketversionen verändert.

Geänderte Anbindung:
- `src/Livewire/TranslationWorkbenchWorkflow.php`
- `resources/views/livewire/workflow/baseline-key-correction.blade.php`
- neue View `resources/views/livewire/workflow/baseline-proposed-source-changes.blade.php`
- neuer Test `tests/Unit/TranslationWorkbench/BaselineProposedSourceChangesTest.php`

Auf die gemeinsamen Klassen umgestellte Aufrufer:
- `tests/Unit/TranslationWorkbench/SourceKeyChangePreviewTest.php`
- `tests/Integration/TranslationWorkbench/copied-source-capture.php`
- `tests/Support/TranslationWorkbench/CopiedSourceCapture.php`
- `tests/Support/TranslationWorkbench/PendingCaptureReview.php`
- Audit-Skripte `capture-copied-source.php`, `preview-key-source-changes.php`,
  `rehearse-key-source-application.php`, `rehearse-key-correction-application.php`

Validierung: 97 Pest-Tests / 471 Assertions erfolgreich, 42 bestehende
Scanner-/Kontext-Assertions erfolgreich. Lesende Vorschau auf den 44 aktuellen
Quelldateien: alle 387 aufgezeichneten Key-Argumente überprüft. Dafür wurde der
bekannte synthetische Ziel-Key verwendet; kein Vorschlag gespeichert oder
angewendet. Composer-Validierung erfolgreich.

## Vollständiger eingegebener Quelländerungsvorschlag – 2026-10-20

„Check all proposed changes“ prüft den eingegebenen Ersatz-Key über den
vollständigen aufgezeichneten Fundstellenbestand. Der bestehende
`BaselineProposedSourceChanges`-Prüfer führt die Einzeldateiprüfungen zusammen;
es gibt keine zweite Ersetzungslogik. Referenzierte Quell- und Sprachdateien
werden vor und nach der Parserprüfung mit ihren erfassten Prüfsummen verglichen.

Ein vollständiger Vorschlag setzt eindeutige Finding-IDs, prüfbare Positionen,
passende Prüfsummen und eine vollständige Bilanz aller Fundstellen voraus.
Teilweise erfolgreiche Prüfungen erhalten keinen Vorschlagsfingerabdruck.
Fehlende, geänderte oder unprüfbare Dateien erscheinen mit Grund in der UI.

Der Fingerabdruck bindet Schema, vollständigen Snapshot, Quell-/Ziel-Key,
konkrete Ersetzungen und Referenzprüfsummen. Prüfzeitpunkte sind nicht Teil
des Fingerabdrucks; eine identische Wiederholungsprüfung bleibt vergleichbar.
Das Ergebnis wird erst auf expliziten Klick berechnet, als gesperrte
Livewire-Property angezeigt und beim Wechsel von Key, Eingabe, Filter oder
Snapshot verworfen. Die Anzeige beschreibt den Prüfzeitpunkt; sie behauptet
keine fortlaufende Aktualität und keine Schreibfreigabe.

Lesender Gesamtlauf mit dem synthetischen Ziel-Key: 44 Quelldateien und 387
Fundstellen vollständig geprüft, keine Abweichung in referenzierten Dateien.
Es wurden weder Quell-/Sprachdateien noch produktive Daten geändert.

Weiterhin ausdrücklich offen: Die aktuellen Datenbankzuordnungen sind nicht
Teil dieser Snapshot-Prüfung. Vor einer echten Anwendung müssen diese und die
Dateien erneut unter dem gemeinsamen Schreibablauf geprüft werden. Der
Fingerabdruck allein ist kein Anwendungs- oder Veröffentlichungsbeleg.

Validierung: 98 Pest-Tests mit 502 Assertions erfolgreich. Enthalten sind
gemischte gültige/ungültige Fundstellen, Sprachdateiänderungen, fehlende Dateien,
doppelte IDs, deterministische Fingerabdrücke sowie das Verwerfen einer
UI-Prüfung bei einem neuen Snapshot. Whitespace-Prüfung erfolgreich.

## Vorschau gegen den isolierten Datenbankstand – 2026-10-20

`BaselineProposalEvidence` vergleicht die geladene Baseline-Vorschau mit dem
transaktional gelesenen Kontext von `KeyCorrectionProposal`. Verglichen werden
Baseline, Quell-/Ziel-Key, exakt dieselben Finding-IDs, Dateipfade, Positionen,
Expressions sowie die veröffentlichten Ausgangswerte je Sprache und Datei.
Eine gleiche Anzahl allein genügt nicht. Die Klasse öffnet selbst keine
Datenbankverbindung; die Belege stammen aus dem bestehenden Prüfablauf.

Der Datenbankkontext enthält jetzt auch den Dateipfad. Außerdem müssen alle
aktiven Findings zum Quell-Key in den Baseline-Beobachtungen enthalten sein.
Zusätzliche aktive Findings ohne diesen Nachweis blockieren den Vorgang.
Die vorhandene transaktionale Prüfung vor Erfassung und Anwendung bleibt
maßgeblich; Dateipfade sind nun auch in die zugehörigen Tabellensperren und
den Kontextfingerabdruck einbezogen.

Der vollständige isolierte Probelauf wurde erneut mit frischer restaurierter
Testdatenbank ausgeführt: 1.098 Assertions erfolgreich. Der neue Vergleich
bestätigt vor Anwendung 387 Fundstellen und zwei veröffentlichte Ausgangswerte.
Zusätzliche Prüfungen weisen archivierte Findings, neue aktive Fundstellen,
anderweitige Key-Zuordnungen, belegte Eltern-/Kindpfade des Ziel-Keys und einen
geänderten Quellpfad zurück. Sämtliche Testmutationen werden zurückgerollt;
Tabellenfingerabdrücke prüfen dies nach jedem Fall.

Die UI zeigt diesen Nachweis unter „Recorded isolated workflow rehearsal“.
Dies ist ausdrücklich ein Ergebnis für die isolierte neue Generation. Ihre IDs
werden nicht gegen den alten produktiven Datenbestand interpretiert.
Produktive Datenbank, Quell- und Sprachdateien bleiben unverändert; der
temporäre PostgreSQL-Cluster wurde abschließend gestoppt.

Zusätzlich: 110 Pest-Tests / 518 Assertions erfolgreich, gezielter Pint-Check
und `git diff --check` erfolgreich. Neuer Unit-Test:
`tests/Unit/TranslationWorkbench/BaselineProposalEvidenceTest.php`.

## Gemeinsamer Datei-/Datenbank-Prüfstand – 2026-10-20

`BaselineCorrectionPreflight` führt die bestehenden vollständigen
Dateivorschläge und den Datenbank-/Snapshot-Abgleich zusammen. Der Aufrufer
liefert den vorhandenen transaktionalen Read-only-Datenbankleser. Der Ablauf
prüft Dateien, Datenbankkontext, nochmals Dateien und nochmals den Kontext.
Bei widersprüchlichen Ergebnissen entsteht kein gemeinsamer Prüfnachweis.

Der gemeinsame Fingerabdruck bindet Baseline-Snapshot, Quell-/Ziel-Key sowie
Datei- und Datenbankfingerabdruck. Identische Wiederholungen bleiben unabhängig
von der Prüfzeit vergleichbar. Der isolierte Schreibprobelauf verwendet den
Datenbankfingerabdruck dieses gemeinsamen Ergebnisses; seine bestehenden
transaktionalen Prüfungen unmittelbar vor dem Schreiben bleiben erhalten.

Die neue Prüfung ersetzt weder die Sperren noch eine erneute Prüfung vor einer
echten Anwendung. Sie behauptet keine atomare Transaktion zwischen Dateisystem
und Datenbank und ist keine dauerhafte Freigabe. Es wurde keine produktive
Schreibaktion an die UI angeschlossen.

Der wiederholte Gesamtlauf bestand mit 1.102 Assertions: 44 Quelldateien,
387 Fundstellen und zwei veröffentlichte Ausgangswerte passen gemeinsam.
Der Bericht enthält den Prüfnachweis und zeigt ihn unter „Recorded isolated
workflow rehearsal“. Die temporäre Datenbank wurde anschließend gestoppt.

Neue gezielte Tests prüfen Änderungen an Quell-/Sprachdateien zwischen den
Prüfschritten, einen wechselnden Datenbankkontext, falsche Ziel-Keys oder
Generationen, deterministische Fingerabdrücke und den Abbruch vor einem
Datenbankzugriff bei bereits fehlerhafter Dateievidenz.

Zusätzlich bestanden 117 Pest-Tests mit 534 Assertions sowie der gezielte
Pint-Check und die Whitespace-Prüfung.

## Bestätigung unmittelbar vor dem isolierten Schreiben – 2026-10-20

`KeyCorrectionApplication` verlangt nun den gemeinsamen Prüffingerabdruck,
den zugehörigen Snapshot und einen expliziten Quellwurzelpfad. Der bisherige
reine Datenbankfingerabdruck genügt nicht mehr. Unter den bestehenden
Datenbanksperren werden der gespeicherte Vorschlagskontext und der vollständige
gemeinsame Prüfstand erneut überprüft, bevor ein Key oder eine Zuordnung
angelegt wird.

Nach den Zuordnungsschreibvorgängen und vor dem Commit werden die referenzierten
Dateiprüfsummen nochmals kontrolliert. Eine Abweichung rollt die Datenbank-
Transaktion zurück. Geänderte Dateien werden dabei weder überschrieben noch
automatisch repariert. Der Vorgangsbeleg und das History-Ereignis enthalten
den bestätigten gemeinsamen Fingerabdruck; die History hält außerdem den
Dateifingerabdruck und den Snapshot-Hash fest.

Ein identischer Retry eines bereits abgeschlossenen Vorgangs liefert den
ursprünglichen Beleg ohne neue Schreibvorgänge zurück. Er behauptet damit
keine erneute Prüfung des inzwischen möglichen Dateistands. Eine geänderte
Bestätigungsanfrage wird weiterhin zurückgewiesen.

Die neuen Fehlertests ändern ausschließlich eine zusätzliche Wegwerfkopie der
referenzierten Dateien. Getestet werden geänderte Quell- und Sprachdateien vor
der Bestätigung und während der Zuordnungsschreibvorgänge sowie ein Retry nach
einer späteren Dateiänderung. Die ursprünglichen Sicherungsdateien werden
abschließend per Prüfsumme auf Unverändertheit geprüft.

Grenze: Dies bleibt ein isolierter Datenbank-Schreibprobelauf. Er schreibt keine
Quell- oder Sprachdateien und behauptet keine atomare Transaktion mit externen
Editoren. Produktive Schreibaktionen bleiben von der Vorschau getrennt.

Validierung: vollständiger isolierter Probelauf mit 1.166 Assertions erfolgreich;
die temporäre PostgreSQL-Instanz wurde gestoppt. Zusätzlich 11 gezielte
Pest-Tests mit 35 Assertions erfolgreich. Der Bericht in der Baseline-Vorschau
enthält den neuen Bestätigungs-/Rollback-Prüfschritt.

## Key-Korrektur als Paketdienst – 2026-10-20

`KeyCorrectionProposal` und `KeyCorrectionApplication` wurden aus
`tests/Support/TranslationWorkbench` nach
`packages/gunreip/laravel-translation-workbench/src/Support` verschoben.
Beide Generalproben verwenden die Paketklassen; keine Alias-Klassen bleiben
zurück. Die Fachlogik und die bisherigen Transaktions-/Prüfregeln sind unverändert.

Die Verbindung wird weiterhin ausdrücklich als PDO übergeben. Die Dienste
öffnen keine Default-Verbindung und erwarten das Schema der neuen Generation.
Eine aktive produktive UI-/Command-Anbindung ist damit noch nicht implementiert.

Der vollständige Lauf mit den Paketklassen bestand mit 1.166 Assertions;
die isolierte Instanz wurde beendet. Zusätzlich bestanden 11 Pest-Tests mit
35 Assertions und Pint für beide Klassen. Der aktuelle
[Umsetzungsstand](workbench-implementation-status-2026-10-20.md) trennt die
abgeschlossenen Nachweise von den verbleibenden fünf Umsetzungspaketen.

## Explizite Datenbankbindung für Key-Korrekturen – 2026-10-20

Die beiden Paketdienste verlangen nun `KeyCorrectionDatabase` statt einer
ungebundenen PDO-Verbindung. Datenbankname, Schema, Baseline-Hash und
Sicherungsmanifest-Hash müssen ausdrücklich angegeben werden. Die Prüfung der
benötigten Tabellen/Spalten und ihrer tatsächlichen Suchpfadauflösung erfolgt
innerhalb der Transaktion vor fachlichen Zugriffen, auch bei einem Replay.
ACCESS-SHARE-Sperren schützen die Tabellen gegen DDL-Änderungen im Vorgang.

Die Generalprobe testet fehlerhafte Bindungen, fehlende Schemaelemente,
temporäre Überschattung und veränderten Suchpfad. Sie besteht mit
1.182 Assertions; 12 bisher unveränderte Tabellen bleiben unverändert.
Testcluster beendet, keine produktive Verbindung. Ergänzend 11 Pest-Tests
mit 36 Assertions und Pint für die drei Paketklassen erfolgreich.

Dies ersetzt weder eine vollständige versionierte Schema-Prüfung einschließlich
Constraints/Triggern noch Benutzerberechtigungen oder den Isolationsnachweis
des Clusters. Der Guard richtet keine Datenbank ein und löst keine implizite
Standardverbindung auf. Die Baseline-Vorschau und die Plan-/Praxis-Notizen
weisen den neuen Teilumfang gesondert aus.

## Versionierte leere Arbeitsgeneration – 2026-10-20

Die acht bestehenden SQL-Definitionen der aktiven Arbeitsgeneration liegen
unverändert im Package unter `database/generations/v1`. Die bisher eingebetteten
Definitionen für `capture_imports`/`capture_outcomes` liegen dort zusätzlich als
`capture-ledger.sql`. Alle bisherigen PHP-Aufrufer wurden auf die Paketdateien
umgestellt; die historische `evidence-staging.sql` bleibt getrennt.

`WorkingGenerationSchema` installiert ausdrücklich in ein neues, benanntes
Schema und protokolliert UUID, Version, SQL-Manifest und Strukturfingerabdruck.
Installation und Nachweis sind transaktional; vorhandene Schemata werden
abgewiesen. Der Suchpfad bleibt nach dem Vorgang unverändert.

Die Prüfung erkennt geänderte Spaltentypen, Constraints, Indizes, Funktionen
und deaktivierte Trigger. Sie vergleicht den aufgezeichneten Installationsstand,
keine frei erfundene Aussage über produktive Bereitschaft. Rollenrechte und
Manipulationen durch Administratoren sind nicht damit abgedeckt.

Validierung: 1.200 Assertions in der isolierten Generalprobe, 186 Assertions
einschließlich zehn Parallelitätsszenarien in der Persistenzsuite und neun
Assertions nach Serverneustart; zwei gezielte UI-Tests mit 138 Assertions.
Beide temporären PostgreSQL-Cluster wurden gestoppt. Die neue Schema-Version
enthält noch keine aktiven Daten; die restaurierte Baseline wurde nicht als
neue Generation registriert. Der nächste Schritt ist die ausdrückliche
Übernahme ausgewählter Nachweise auf der Kopie.

## Ausgewählte Übernahme in Schema-Version 1 – 2026-10-20

Der isolierte Testadapter `SelectedGenerationTransfer` überträgt die
387 einzelnen Fundstellen und zwei veröffentlichten Werte des ausgewählten
blockierten Keys in die frisch installierte Generation. Die 44 Dateinachweise
erhalten eigene IDs; Finding-Herkunft und unveränderte Rohfelder bleiben
nachvollziehbar. Ein eigener Baseline-Hash kennzeichnet den Teilbestand.

Aktive Entscheidungen und alte History bleiben ausgeschlossen. Der
Übernahme-Scan-Nachweis ist ausdrücklich als `evidence_transfer_not_new_scan`
markiert. Ein Fehler nach der zweiten Fundstelle rollt die Übernahme zurück;
eine bereits befüllte Generation wird nicht nochmals importiert.

Die Paketdienste prüfen, bestätigen und schreiben anschließend die synthetische
Korrektur direkt in diese Generation. Der identische Retry erzeugt keinen
weiteren Event. Der vollständige Lauf besteht mit 1.218 Assertions.
Die Quellbaseline bleibt unverändert, die Schema-Prüfung besteht auch nach
dem Schreiben, der temporäre Cluster ist gestoppt.

Der Adapter ist weiterhin ausschließlich Testinfrastruktur. Vollständige
Namensraum-/Konfliktprüfung und weitere Übernahmekategorien fehlen noch.
Bearbeitung und Export auf der neu befüllten Generation sind noch keine
abgenommenen Funktionen; die vorhandenen Exportprüfungen verwenden weiterhin
die restaurierte Test-Baseline.

## Vollständiges Namensinventar bei Teilübernahmen – 2026-10-20

Schema-Version 2 ergänzt die eigenständige immutable Tabelle
`baseline_name_inventories`. Version 1 bleibt unverändert. Der Paketdienst
`BaselineNameInventory` erfasst Namespace und Key-Name aus veröffentlichten
Nachweisen und aktiven benannten Keys, normalisiert die Reihenfolge und bindet
die Liste an Anzahl und SHA-256.

Die Generalprobe überträgt alle 972 belegten Namen, während nur die ausgewählten
387 Findings und zwei Textnachweise aktiv übernommen werden. Dies aktiviert
keine zusätzlichen Key-Zuordnungen. Vorschau und Datenbankprüfung erkennen
damit auch Konflikte außerhalb dieser Auswahl. Der Inventar-Hash ist Teil des
Vorschlagskontexts. Fehlendes Inventar wird nicht als freier Namensraum behandelt.

1.228 Assertions der isolierten Generalprobe sowie 43 Pest-Tests mit
101 Assertions bestanden. Exakte Namen, Scalar-/Array-Pfade, Segmentgrenzen,
getrennte Namespaces und fehlende/defekte Inventare sind abgedeckt.
Die alten Statusnotizen bleiben als historischer Verlauf erhalten; diese
Ergänzung schließt die zuvor dokumentierte Namensraumgrenze für den
gesicherten Ausgangsstand. Kein produktiver Schemawechsel wurde ausgeführt.
