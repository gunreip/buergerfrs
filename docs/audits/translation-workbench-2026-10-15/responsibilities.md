# Translation Workbench Zuständigkeiten und Umstellungsplan

Stand: 15.10.2026. Diese Zielvereinbarung konkretisiert den [Audit](README.md) und den [Target Workflow UI](../../../packages/gunreip/laravel-translation-workbench/resources/views/livewire/workflow/target-ui.blade.php). Sie beschreibt die nächste Implementierungsgrundlage; bestehende Commands, Datenbank und UI wurden damit noch nicht umgestellt. Der Audit bleibt die Dokumentation des IST-Zustands.

## Gemeinsame Grundlage

UI und Artisan sind Zugänge zu denselben fachlichen Services. Sie dürfen unterschiedliche Bedienoberflächen haben, aber keine unterschiedlichen Regeln für dieselbe Mutation. Ein fachlicher Bereich hat einen verantwortlichen Schreibweg; ein Service darf mehrere spezialisierte Helfer verwenden.

Die Kette lautet:

**Code → Scan-Beobachtung → Erfassung und Abgleich → Zuordnung und Bearbeitung → Freigabe → Veröffentlichungsplan → Sprachdateien → Quellcode-Verweise → Prüfung und Nutzung.**

Dateiimport, Runtime-Beobachtungen und Altbestandsübernahme sind zusätzliche Eingänge mit eigener Herkunft. Sie dürfen nicht nebenbei Übersetzungen, Freigaben oder bestätigte Zuordnungen ersetzen. Datenbank-Speicherung, Freigabe und Veröffentlichung sind drei unterschiedliche Entscheidungen.

## Zuständigkeitsmatrix

In dieser Tabelle sind Service-Namen ohne Namespace bestehende Klassen unter `src/Scanner` beziehungsweise `src/Foundation` des Pakets. „Neu erforderlich“ bezeichnet eine fehlende Verantwortung, noch keinen beschlossenen Klassen- oder Tabellennamen.

| Nr. | Verantwortung und Auslöser | Grundlage und Anpassung | Erlaubte Änderungen im Ziel | Ausgeschlossen |
| --- | --- | --- | --- | --- |
| Z01 | Scan starten; UI oder CLI mit sichtbarem Scope | `TranslationWorkbenchScanner`, `TranslationFingerprintFactory`; neu erforderlich: gemeinsamer Scan-Run mit Dateimanifest | Betriebsprotokoll, erfolgreiche/fehlgeschlagene Datei-Beobachtungen, exakte Vorkommen und Kontext | Anwendungscode ausführen, Übersetzungen speichern, Keys freigeben, Sprachdateien schreiben |
| Z02 | Erfolgreiche Scan-Ergebnisse erfassen und abgleichen | `TranslationWorkbenchFoundationSyncer` umbauen; Originalerfassung von veränderlichen Beobachtungen trennen | Neue Findings/raw_data/SuggestedKeys; eindeutige Ortsänderungen; bestätigte Inaktivität innerhalb erfolgreich geprüftem Scope; Beobachtungsbeziehungen | raw_data überschreiben; fehlenden Treffer mit globaler Archivierung gleichsetzen; bestehende Sprachwerte, Freigaben oder Klärungsstatus ersetzen |
| Z03 | Kandidaten und Klassifikation aus demselben Scan-Stand ableiten | Shared-/Dynamic-Commands, `TranslationWorkbenchLangNodeClassifier`; Verantwortung aus Commands in gemeinsame Analyse-Services verlagern | Klassifikation, Shared-Markierung, Konflikthinweise und Zuordnungsvorschläge | Automatisches Zusammenführen; bestätigte Zuordnung ändern; fehlende Dynamikwerte physisch löschen |
| Z04 | Kontext klären, Key bestätigen, Shared-Zuordnung entscheiden | `TranslationWorkbenchKeyReviewWriter`, `TranslationWorkbenchBulkKeyWriter`, `TranslationWorkbenchConflictReviewWriter`, `TranslationWorkbenchProvenanceReviewWriter` konsolidiert abgrenzen | Explizit ausgewählte Zuordnungen und redaktionelle Entscheidungen mit Historie | Durch Annahme eines SuggestedKeys bereits veröffentlichen; konkurrierende Sprachwerte still überschreiben |
| Z05 | Ausgewählte Übersetzungen/Entwürfe speichern | `TranslationWorkbenchTranslationWriter`; Scalar-/DynamicEditor als fachliche Vorbereitung; Revision und atomare Konfliktprüfung ergänzen | Bearbeitete Revision je Key/Sprache beziehungsweise dynamischem Wert; Draft; Änderungslog | raw_data ändern; andere Sprachen implizit ändern; Freigabe oder veröffentlichten Stand beim Speichern überschreiben |
| Z06 | Gespeicherten Stand prüfen und freigeben/zurückweisen | Neu erforderlich: Freigabe konkreter Zuordnungs- und Wertrevisionen; vorhandene Review-Infrastruktur weiterverwenden | Freigabe/Feedback für ausgewählte gespeicherte Revisionen und Sprachen | Key-Review pauschal als Wertfreigabe verwenden; Fallback oder spätere Änderungen automatisch freigeben |
| Z07 | Veröffentlichung ausdrücklich vorbereiten | `TranslationWorkbenchCodeUpdatePlanner`, Planungsanteil von `TranslationWorkbenchLangFileExporter`; neu erforderlich: gemeinsamer versionierter Plan | Plan mit Datenrevisionen, Locale-Scope, Quelldatei-/Sprachdateistand, Diffs und Konflikten | Dateien schreiben; neue Datenstände ohne neue Bestätigung in bestehenden Plan übernehmen |
| Z08 | Bestätigten aktuellen Plan ausführen und prüfen | `TranslationWorkbenchLangFileExporter`, `TranslationWorkbenchCodeUpdateApplier`; neu erforderlich: gemeinsame Ausführungskoordination und konkurrierende Schreibzugriffe absichern | Erst freigegebene Sprachwerte schreiben/verifizieren; dann exakte Quellvorkommen ändern/verifizieren; pro Ergebnis Provenienz und Publication-Status speichern | Ungeprüfte Werte exportieren; Quelländerung nach fehlgeschlagenem Sprachschritt; Teilerfolg als Gesamterfolg melden; außerhalb des Plans bereinigen |
| Z09 | Vorhandene Sprachdateien beobachten/vergleichen | `TranslationWorkbenchLangValueImporter` umbauen; `TranslationWorkbenchValueComparison` weiterverwenden | Separater beobachteter Dateistand und Abweichungen | Editor-Speicher überschreiben; obsolete-Werte still reaktivieren; Beobachtung als Freigabe behandeln |
| Z10 | Datei-/Legacy-Bestand bewusst übernehmen | Neu erforderlicher Übernahmeplan, vorhandene Vergleichs-/Review-Funktionen nutzen | Explizit entschiedene Übernahme mit Herkunft, alter ID, Wert und Freigabe-/Veröffentlichungsnachweis | „Datei gewinnt“ oder „DB gewinnt“ ohne Klärung; alle Altwerte pauschal als reviewed einstufen |
| Z11 | Published-Werte zur Laufzeit verwenden | Bestehende Laravel-/Locale-Anbindung prüfen; Sprachenkonfiguration ist Grundlage | Lesen der veröffentlichten Werte und konfigurierten Fallback-Kette; getrennte Diagnostik möglich | Fallback als eigene Übersetzung speichern; beim Seitenaufruf freigeben oder exportieren |
| Z12 | Inventar, Berichte und TW-Graph aktualisieren | Inventory-Analyse, `TranslationWorkbenchTimelineChainCollector`, bestehende Graph-Datenaufbereitung | Abgeleitete Projektionen und Berichte, referenziert auf konsistenten Stand | Fachliche Werte/Reviews/Zuordnungen ändern; Berichte unterschiedlicher Läufe als Schreibgrundlage mischen |
| Z13 | Dynamische Werte zur Laufzeit beobachten | `RuntimeDynamicTranslationCollector`, `TranslationWorkbenchDynamicSourceLinkWriter`; Beobachtung und bestätigte Verknüpfung trennen | Laufzeitbeobachtung und Kandidaten; explizit geprüfte Quellverknüpfung über redaktionellen Weg | Stillschweigende Übersetzung, Zuordnung oder Veröffentlichung beim Rendern |
| Z14 | Archivieren, Rekey, Shared-Split/Merge oder Recovery bewusst durchführen | Cleanup-/Bulk-/Key-/Provenance-Writer, bisherige Recovery-Commands; gemeinsamer Wartungsplan | Nach geprüfter Entscheidung ausgewählte Status-/Zuordnungsänderungen; Dateieingriffe über Z07/Z08 | Physische Löschung als Scan-Nebenwirkung; automatische Kompatibilitätsaliase; Verlust alter IDs/Historie |

## Regeln je Datenart

| Datenart | Verantwortlicher Schreibweg | Erhaltungsregel |
| --- | --- | --- |
| Ursprüngliches Literal/raw_data | Z02 bei Ersterfassung | Exakten Inhalt erhalten; Quelltextänderung ist neue Erfassung, keine Umschreibung des Originals |
| Aktueller Fundort/Scan-Beobachtung | Z01/Z02 | Positionsidentität von Textgleichheit trennen; zweideutige Verschiebungen bleiben offen |
| SuggestedKey | Z02 als Vorschlag bei Erfassung | Rescan ersetzt keine bestätigte Zuordnung; Vorschlagsänderung nicht als automatische Umbenennung behandeln |
| Shared-/Dynamik-Kandidaten | Z03/Z13 | Berechenbare Hinweise; keine Bearbeitungsentscheidung |
| Bestätigte Zuordnung/Kontext/Klärung | Z04/Z14 | Akteur, Auswahl und Vorher-/Nachher-Zustand erhalten; Scan setzt Klärung nicht zurück |
| Sprachwert/Draft | Z05; explizite Altübernahme Z10 nutzt denselben Speichervertrag | Revisionen und Herkunft erhalten; Prüfung veralteter Editor-Basis schützt den Schreibvorgang selbst |
| Freigabe | Z06 | Gilt ausschließlich für geprüfte Revision, Zuordnung und Sprachumfang |
| Beobachtete Sprachdatei | Z09 | Vergleichsstand, keine konkurrierende Übersetzungsautorität |
| Wirksamer veröffentlichter Stand | Z08, bei Bestandsübernahme nach Z10 nachweisen | Tatsächliche Datei-/Vorkommenergebnisse getrennt von gewünschtem Ergebnis speichern |
| Fachhistorie | Jeweils verantwortlicher Mutationsservice über `TranslationWorkbenchTimelineRecorder` | Tatsächliche Änderungen protokollieren; keine neue Änderung bei unverändertem Rescan/Speichern |
| Betriebsprotokoll | Orchestrierung über `TranslationWorkbenchPipelineRunTracker` als Grundlage | Auch unveränderte/fehlgeschlagene Läufe sind protokollierbar; keine Vermischung mit fachlicher Änderung |
| Timeline-Ketten/Inventar | Z12 | Neu aufbaubar; Historie und fachliche Stammdaten bleiben eigenständig |

Die physische Tabellenaufteilung wird erst im Datenmodellschritt festgelegt. Insbesondere wird `translation_workbench_lang_values` nicht einfach mit weiteren Statuswerten zum gleichzeitigen Import-, Draft-, Review- und Publication-Speicher erklärt.

## Fachliche Verträge aus Target Workflow UI

- Ein Literal kann mehrere eigenständige Fundstellen haben, auch auf derselben Zeile. Wiedererkennung derselben Fundstelle erzeugt keine neue Übersetzungsaufgabe.
- Ein sicher erkannter Umzug erhält Identität und Bearbeitungsstand. Ein anderer Text ist neue Erfassung plus Abgleich der alten Fundstelle. Mehrdeutigkeit rechtfertigt keine automatische Übernahme.
- Veröffentlichte Literal→Key-Ersetzungen werden über ihre Provenienz wiedererkannt. Der nächste Scan darf sie weder als verschwundenes Literal noch als neues ungeklärtes Literal duplizieren.
- Ausgeschlossene, fehlerhafte oder nicht geprüfte Quellen begründen keine Entfernung. Auskommentierte Aufrufe zählen nicht aktiv; alte Erfassung und Historie bleiben erhalten.
- `sharedCandidate` gilt für aktive gleichlautende Findings mit mindestens zwei Mitgliedern und unterschiedlichen SuggestedKeys. Betroffene Gruppen werden auch unter Einbezug des aktiven Bestands außerhalb eines Teilscans berechnet. Unterhalb der Bedingung entfällt die Markierung; Zusammenführung bleibt eine Entscheidung.
- Unaufgelöste Variablen/Ausdrücke bleiben klärungsbedürftig. Scanner führen keinen Anwendungscode aus, um Werte zu erraten. Platzhalter und Pluralformen werden gesondert geprüft.
- Speichern bedeutet weder Freigabe noch Veröffentlichung. Nicht ausgewählte Sprachen bleiben unangetastet. Ein Draft darf unvollständig sein und verändert nicht die wirksame Ausgabe.
- Aktivierte Sprachen brauchen nicht vollständig übersetzt zu sein. Fallback folgt der konfigurierten Elternkette, beispielsweise de-AT → de/de-DE → en, nicht beliebigen Geschwistersprachen. Die tatsächliche Elternzuordnung entscheidet. Vor Literalersetzung muss der notwendige Quellsprachwert vorhanden sein.
- Rekey, Shared-Merge/Split, Archivierung und Dateibereinigung sind eigene geprüfte Wartungsvorgänge. Bestandswerte werden nicht als Nebeneffekt von Vorschlagsannahme oder Scan gelöscht.
- Veröffentlichung umfasst Plan, Bestätigung, Prüfung unmittelbar vor den jeweiligen Writes und Ergebnisverifikation. Eine fehlgeschlagene Teilveröffentlichung bleibt sichtbar unvollständig; Wiederaufnahme erfolgt mit frischem Plan und Bezug auf den alten Lauf.

## Einordnung aller bestehenden Commands

**Behalten** bedeutet: fachliche Aufgabe bleibt, keine pauschale Unbedenklichkeitsbescheinigung. **Umbauen** bedeutet: Einstieg/Fähigkeit kann bleiben, Schreibvertrag muss geändert werden. **Zusammenführen** bedeutet: Fähigkeit in den genannten gemeinsamen Weg übernehmen und eigenständigen alten Command anschließend stilllegen. **Stilllegen** bedeutet: keine reguläre Ausführung mehr nach Ersatz/Prüfung der Aufrufer; Quellcode zunächst archivieren statt löschen. **Migration** bedeutet: nur als ausdrücklich ausgewähltes Bestandswerkzeug, nicht als Pipeline-Standard.

Die folgenden Zuordnungen erfassen alle 46 Klassen des Audits. Neue CLI-Namen werden hier nicht zusätzlich erfunden; insbesondere keine dauerhaft parallelen Alias-Pipelines.

| Bestehender Command | Zielentscheidung | Zuständigkeit | Konkrete Grenze / Ablösebedingung |
| --- | --- | --- | --- |
| [`project:bootstrap-data`](../../../app/Console/Commands/ProjectBootstrapData.php) | Behalten / abgrenzen | Z10 | Nur explizites Bootstrap; Translation-Seed/Import vor Nutzung gegen Übernahmevertrag prüfen. |
| [`project:build`](../../../app/Console/Commands/ProjectBuild.php) | Umbauen | Z08 | Eigenständigen Legacy-Sync/-Export entfernen; Build konsumiert bereitgestellten Sprachstand. |
| [`project:translations`](../../../app/Console/Commands/ProjectTranslations.php) | Stilllegen | Z01–Z12 | Nach Aufruferumstellung keine zweite Gesamtpipeline betreiben. |
| [`translation-workbench:apply-code-updates`](../../../packages/gunreip/laravel-translation-workbench/src/Console/ApplyTranslationWorkbenchCodeUpdates.php) | Umbauen | Z08 | Vorhandenen Preflight behalten; nur bestätigten aktuellen Publication-Plan ausführen. |
| [`translation-workbench:apply-suspicious-key-restores`](../../../packages/gunreip/laravel-translation-workbench/src/Console/ApplySuspiciousKeyRestores.php) | Umbauen | Z14 | Nur explizite Recovery mit geprüftem Plan; aus implizitem Vor-Scan entfernen. |
| [`translation-workbench:classify-dynamic-values`](../../../packages/gunreip/laravel-translation-workbench/src/Console/ClassifyDynamicValues.php) | Umbauen | Z03 | Beobachtungen aktualisieren; fehlende Werte nicht physisch aus Fachhistorie entfernen. |
| [`translation-workbench:classify-lang-node-types`](../../../packages/gunreip/laravel-translation-workbench/src/Console/ClassifyLangNodeTypes.php) | Behalten / anpassen | Z03 | Konsistenten Snapshot verwenden; nur abgeleitete Klassifikation ändern. |
| [`translation-workbench:classify-timeline-event-noise`](../../../packages/gunreip/laravel-translation-workbench/src/Console/ClassifyTimelineEventNoise.php) | Behalten | Z12 | Explizite Historienklassifikation; keine Reparatur durch Löschen oder Verbergen. |
| [`translation-workbench:cleanup-excluded-paths`](../../../packages/gunreip/laravel-translation-workbench/src/Console/CleanupExcludedTranslationWorkbenchPaths.php) | Umbauen | Z14 | Scope-Ausschluss von bestätigter Entfernung unterscheiden; expliziter Plan. |
| [`translation-workbench:collect-timeline-chains`](../../../packages/gunreip/laravel-translation-workbench/src/Console/CollectTimelineChains.php) | Behalten | Z12 | Weiterhin reine Projektion; Originalereignisse bewahren. |
| [`translation-workbench:compare-values`](../../../packages/gunreip/laravel-translation-workbench/src/Console/CompareTranslationWorkbenchValues.php) | Behalten | Z09/Z10 | Differenzen berichten; keine Seite automatisch gewinnen lassen. |
| [`translation-workbench:detect-duplicates`](../../../packages/gunreip/laravel-translation-workbench/src/Console/DetectDuplicateCandidates.php) | Zusammenführen | Z03 | Benötigte Analyse in gemeinsamen Kandidatenweg übernehmen; alten Entry-Schreiber ablösen. |
| [`translation-workbench:detect-shared-key-candidates`](../../../packages/gunreip/laravel-translation-workbench/src/Console/DetectSharedKeyCandidates.php) | Umbauen | Z03 | N≥2-Regel und Rücknahme implementieren; historische Entscheidungen als Kontext erhalten. |
| [`translation-workbench:detect-suspicious-keyed-additions`](../../../packages/gunreip/laravel-translation-workbench/src/Console/DetectSuspiciousKeyedAdditions.php) | Behalten / anpassen | Z03/Z14 | Diagnose und bewusste Recovery getrennt halten. |
| [`translation-workbench:discover-dynamic-options`](../../../packages/gunreip/laravel-translation-workbench/src/Console/DiscoverDynamicOptions.php) | Zusammenführen | Z03 | Optionsbeobachtung behalten, alte Entries/Values nicht weiter doppelt versorgen. |
| [`translation-workbench:discover-dynamic-source-candidates`](../../../packages/gunreip/laravel-translation-workbench/src/Console/DiscoverDynamicSourceCandidates.php) | Behalten / anpassen | Z03 | Kandidaten aus gemeinsamem Stand; keine implizite bestätigte Verknüpfung. |
| [`translation-workbench:export-lang-files`](../../../packages/gunreip/laravel-translation-workbench/src/Console/ExportTranslationWorkbenchLangFiles.php) | Umbauen | Z07/Z08 | Gilt hier nur für Workbench: revisionsgebundener Plan, gesamter Preflight, korrekter Fehlerstatus. |
| [`translation-workbench:import-existing`](../../../packages/gunreip/laravel-translation-workbench/src/Console/ImportExistingTranslations.php) | Zusammenführen | Z09/Z10 | Keine reguläre Alt-Values-Versorgung; historische/exklusive Daten vor Ablösung übernehmen. |
| [`translation-workbench:import-lang-values`](../../../packages/gunreip/laravel-translation-workbench/src/Console/ImportTranslationWorkbenchLangValues.php) | Umbauen | Z09 | Dateien nur beobachten; Wertübernahme separat entscheiden. |
| [`translation-workbench:inventory-keys`](../../../packages/gunreip/laravel-translation-workbench/src/Console/InventoryTranslationWorkbenchKeys.php) | Behalten / anpassen | Z12 | Inventar aus kanonischem Bestand ableiten; Legacy-Abhängigkeit nach Migration entfernen. |
| [`translation-workbench:plan-code-updates`](../../../packages/gunreip/laravel-translation-workbench/src/Console/PlanTranslationWorkbenchCodeUpdates.php) | Umbauen | Z07 | Teil des gemeinsamen Plans; gültige Review-/Wertrevision und exakte Vorkommen binden. |
| [`translation-workbench:profile-findings`](../../../packages/gunreip/laravel-translation-workbench/src/Console/ProfileTranslationWorkbenchFindings.php) | Behalten | Z12 | Lesende Diagnose. |
| [`translation-workbench:resolve-unknown-dynamic-sources`](../../../packages/gunreip/laravel-translation-workbench/src/Console/ResolveUnknownDynamicSources.php) | Umbauen | Z03/Z04 | Automatische Analyse von bestätigter Zuordnung trennen; zweideutige Fälle offen lassen. |
| [`translation-workbench:scan`](../../../packages/gunreip/laravel-translation-workbench/src/Console/ScanTranslationWorkbench.php) | Zusammenführen | Z01/Z02 | Alten Entry-/Occurrence-Schreiber durch gemeinsamen Scan/Foundation-Weg ersetzen; Scope bis dahin absichern. |
| [`translation-workbench:sync-foundation`](../../../packages/gunreip/laravel-translation-workbench/src/Console/SyncTranslationWorkbenchFoundation.php) | Umbauen | Z02 | Scan-Manifest konsumieren; kein erneuter unabhängiger Scan und keine globale Löschableitung. |
| [`translation:workbench`](../../../packages/gunreip/laravel-translation-workbench/src/Console/RunTranslationWorkbench.php) | Umbauen | Z01–Z14 | Dünner Auftragseinstieg; Scan, Analyse, Publication und Wartung explizit trennen. |
| [`translations:audit-code`](../../../app/Console/Commands/TranslationsAuditCode.php) | Zusammenführen | Z01/Z12 | Bericht aus gemeinsamem Scan; zweiten Parser als Autorität ablösen. |
| [`translations:audit-compare`](../../../app/Console/Commands/TranslationsAuditCompare.php) | Zusammenführen | Z09/Z12 | Vergleich auf benannten konsistenten Ständen statt gemischten JSON-Dateien. |
| [`translations:audit-duplicate-usage-literals`](../../../app/Console/Commands/TranslationsAuditDuplicateUsageLiterals.php) | Zusammenführen | Z03 | Gemeinsame Finding-/Shared-Analyse statt Legacy-Usages. |
| [`translations:audit-frequent-usage-literals`](../../../app/Console/Commands/TranslationsAuditFrequentUsageLiterals.php) | Zusammenführen | Z03/Z12 | Häufigkeit als Auswertung derselben Findings. |
| [`translations:audit-lang`](../../../app/Console/Commands/TranslationsAuditLang.php) | Zusammenführen | Z09 | Dateibeobachtung wiederverwenden. |
| [`translations:audit-lang-ballast`](../../../app/Console/Commands/TranslationsAuditLangBallast.php) | Zusammenführen | Z12/Z14 | Verwendungsprüfung als Wartungsvorschlag; keine implizite Dateibereinigung. |
| [`translations:audit-sub-language-redundancy`](../../../app/Console/Commands/TranslationsAuditSubLanguageRedundancy.php) | Umbauen | Z03/Z11 | Redundanzanalyse von fachlicher Mutation trennen; Fallback ist kein gespeicherter eigener Wert. |
| [`translations:backfill-audit-discovered-events`](../../../app/Console/Commands/BackfillTranslationAuditDiscoveredEvents.php) | Migration | Z10 | Nur nachgewiesene Historienlücken im expliziten Übernahmeplan bearbeiten. |
| [`translations:backfill-native-text-from-values`](../../../app/Console/Commands/TranslationsBackfillNativeTextFromValues.php) | Migration | Z10 | Herkunft erhalten; rekonstruierter Text ist nicht nachträglich exaktes raw_data. |
| [`translations:cleanup-stale-dynamic-keys`](../../../app/Console/Commands/CleanupStaleDynamicTranslationKeys.php) | Migration | Z10/Z14 | Gezielte Altbestandsentscheidung; kein regulärer Pipeline-Schritt. |
| [`translations:ensure-lang-directories`](../../../app/Console/Commands/TranslationsEnsureLangDirectories.php) | Behalten / abgrenzen | Z08 | Technische Provisionierung, keine Freigabe/Aktivierung von Sprachwerten. |
| [`translations:export-lang-files`](../../../app/Console/Commands/TranslationsExportLangFiles.php) | Stilllegen | Z08 | Nach geprüfter Wertübernahme und Ersatz; vorhandenen Workbench-Konfliktschutz in einzigen Publisher übernehmen. |
| [`translations:generate-literal-diffs`](../../../app/Console/Commands/TranslationsGenerateLiteralDiffs.php) | Zusammenführen | Z07 | Benötigte Fähigkeiten in gemeinsamen Planer übernehmen. |
| [`translations:lang-ballast:apply`](../../../app/Console/Commands/TranslationsApplyLangBallastDecisions.php) | Zusammenführen | Z14/Z08 | Geprüfte Bereinigung über gemeinsamen Publication-Vertrag. |
| [`translations:normalize-keyed-native-statuses`](../../../app/Console/Commands/TranslationsNormalizeKeyedNativeStatuses.php) | Migration | Z10 | Einmalige nachvollziehbare Übernahme, keine permanente zweite Statuskorrektur. |
| [`translations:rewrite-literals`](../../../app/Console/Commands/TranslationsRewriteLiterals.php) | Stilllegen | Z08 | Kontextlosen Writer sperren; bestätigte Vorkommen über Workbench-Applier ändern. |
| [`translations:sync-audits`](../../../app/Console/Commands/SyncTranslationAudits.php) | Stilllegen | Z02/Z10 | Nach Übernahme und Aufruferumstellung; Berichte nicht als zweite DB-Autorität verwenden. |
| [`translations:sync-dynamic-keys`](../../../app/Console/Commands/SyncDynamicTranslationKeys.php) | Zusammenführen | Z03/Z13 | Dynamikfähigkeiten übernehmen; zweiten Key-/Usage-Schreiber ablösen. |
| [`translations:usage-decisions:apply`](../../../app/Console/Commands/ApplyTranslationUsageAuditDecisions.php) | Zusammenführen | Z14/Z08 | Vorhandene Entscheidungen erhalten, Ausführung über gemeinsamen Plan. |
| [`translations:usage-decisions:preview`](../../../app/Console/Commands/PreviewTranslationUsageAuditDecisions.php) | Zusammenführen | Z14/Z07 | Vorschau im gemeinsamen Wartungs-/Publication-Plan erhalten. |

## Grenzen der Orchestrierung

`translation:workbench` kann als zentraler Einstieg erhalten bleiben, muss aber fachlich sichtbare Aufträge auslösen. Ein Scan-Auftrag endet mit dem Erfassungs-/Analyseergebnis; er darf nicht durch eine Voreinstellung Veröffentlichung oder Recovery einschließen. Ein einzelnes Command pro internem Helfer ist nicht erforderlich.

UI und CLI verwenden dieselben Scope-, Versions-, Konflikt- und Freigabeprüfungen. Eine Run-ID dient der Nachvollziehbarkeit; sie verhindert allein keine konkurrierenden Veröffentlichungen. Die Ausführung muss relevante konkurrierende Writes absichern und veraltete Pläne beim tatsächlichen Schreiben erkennen. Eine globale Sperre für beliebige unabhängige Leser ist nicht erforderlich.

`project:build` und Bootstrap dürfen nicht unbemerkt die Translation-Autorität übernehmen. Bevor bisherige Aufrufe entfernt werden, werden Dokumentation, UI-Prozessstarter, Tests und Projekt-/Deployment-Aufrufe erfasst. Ein stillgelegter Einstieg muss bei versehentlichem Aufruf verständlich abbrechen, statt scheinbar erfolgreich nichts oder einen anderen Workflow auszuführen. Archivierte Implementierungen bleiben außerhalb der Command-Registrierung.

## Umsetzungsreihenfolge mit Abnahmekriterien

| Paket | Konkrete Arbeit | Abnahme / Abhängigkeit |
| --- | --- | --- |
| U01 – Fehlerverträge | Audit-Fälle F01–F06/F08 und Scope-Fälle aus F12 als isolierte Soll-Tests formulieren; vorhandene Schutztests erhalten | Teilscan lässt fremden Bestand unverändert; leere/fehlgeschlagene Scopes unterscheiden sich; letzter Aufruf verschwindet korrekt; Draft wird nicht importüberschrieben; Konflikte ergeben keinen Erfolg; unterschiedliche Vorkommen bleiben unterscheidbar |
| U02 – Schutz der Schreibzugänge | Aufruferliste erstellen; Import-/Export-/Legacy-Writer gemäß Matrix absichern; schrittweise notwendige UI-Zugänge auf vorhandenen gemeinsamen Service legen | Kein stiller Funktionsverlust; gefährliche Operation meldet konkrete Ursache statt Ersatzhandlung; keine alten produktiven Writer ohne bewussten Migrationszweck |
| U03 – Ein Scan-Stand | Erfolgs-/Dateimanifest, Positionsidentität, Originalerfassung und Foundation-Abgleich implementieren; redundante Scans/alte Entries-Versorgung ablösen | Neu/gleich/verschoben/entfernt/kommentiert/veröffentlicht/mehrdeutig geprüft; unveränderte Wiederholung erzeugt keine fachliche Mutation; Shared-Regel folgt aktuellem aktiven Bestand |
| U04 – Revision und Bearbeitung | Migrationsfähiges Datenmodell entwerfen; Wertrevision, Draft, Importbeobachtung, Freigabe und atomare Edit-Konflikte implementieren | Import/Speichern/Review sind getrennt; Revisionänderung entwertet keine Historie, aber macht alte Freigabe für neue Revision unbrauchbar; andere Sprachen bleiben erhalten |
| U05 – Veröffentlichung | Gemeinsamen Plan und Ausführung koordinieren; Quellsprachabdeckung, Scope, Dateikonflikte, per-Step-Verifikation, Recovery und konkurrierende Writes prüfen | Fehler vor Beginn verhindert Schreiben; Fehler während Ausführung hält Folgeschritte an und dokumentiert Teilergebnis; erneute Ausführung erkennt erfolgreiche Schritte und veraltete Zustände |
| U06 – Übernahme und Umschaltung | Vollständige PostgreSQL-Kopie; Legacy-/Foundation-Differenzen entscheiden; IDs/Provenienz übernehmen; UI und CLI auf denselben Vertrag setzen; Altwege archivieren | Kein unerklärter Wert-/Reviewverlust; vollständiger Durchlauf einschließlich Wiederholung, Fehler und Recovery; geprüfter Sicherungs-/Rückkehrplan vor produktiver Migration |

U01 ist kein Auftrag, falsches Verhalten mit grünen Erwartungswerten zu konservieren. Neue Soll-Tests dürfen bei ihrer Erstellung rot sein; zum Abschluss eines Reparaturpakets müssen sie und die relevanten bisherigen Tests bestehen. Noch nicht implementierte Verträge werden als offene Arbeit ausgewiesen, nicht still übersprungen oder durch abgeschwächte Assertions „erfüllt“.

U02 und U03 werden bei abhängigen Einstiegspunkten gemeinsam ausgeliefert, damit die alte Erfassung erst entfällt, wenn ihre benötigten Fähigkeiten übernommen sind. Ein technisch riskanter Einzelwriter kann vorher klar gesperrt werden; ein pauschales Abschalten der gesamten Workbench ist kein Migrationsplan.

## Nächster konkreter Implementierungsschritt

Als erstes U01/U02 mit dem **Sprachdatei-Import versus gespeicherter Übersetzung (F03)** beginnen: Der belegte Verlust eines unveröffentlichten Werts wird zum Soll-Test. Anschließend wird der konkrete Writer so abgesichert, dass eine Dateidifferenz den bearbeiteten Wert samt Herkunft und Review nicht ersetzt. Konflikte müssen im Ergebnis sichtbar sein und die Orchestrierung darf keine abhängige Veröffentlichung als erfolgreich fortsetzen.

Die erste Absicherung ersetzt noch nicht U04. Falls das bestehende Schema eine sichere Unterscheidung nicht zulässt, ist ein expliziter Konflikt/Abbruch mit unveränderten Daten die Zwischenlösung. Kein zweiter versteckter Speicherpfad und kein stilles „ungeändert“-Ergebnis für einen ungelösten Konflikt.

Danach F05 (Export-Konflikt/Teilerfolg), F01/F02 (Scope) und die übrigen Fälle in den zugehörigen Paketen bearbeiten. Die Umstellung wird jeweils mit Dateien, Vertragsänderung, Tests und verbleibenden Grenzen dokumentiert.

## Umsetzungsstand Importabsicherung

Erster Teil von U01/U02 umgesetzt: Der Importer akzeptiert neue Werte und beobachtet übereinstimmende vorhandene Werte. Abweichungen bei Text, Werttyp, Key oder Status lösen einen Importkonflikt aus, unabhängig davon, ob die vorhandene Herkunft als Editor, Import oder Recovery ausgewiesen ist. Die gesamte Import-Transaktion wird zurückgerollt, einschließlich zuvor neu angelegter Werte und ihrer Timeline-Ereignisse. Die Fehlermeldung benennt Locale, Key, Zeilen-ID und abweichende Felder; zunächst wird der erste Konflikt ausgegeben.

Bei Übereinstimmung bleiben Herkunft, Quellhash und redaktionelle Metadaten erhalten. Lediglich Beobachtungszeit und Scan-Zähler werden aktualisiert. Der Command liefert bei Konflikt Exit-Code 1; die vorhandene Pipeline beendet ihre Folgeausführung. Ein direkter Import mit `--truncate-lang-values` wird bei nichtleerem Wertbestand ebenfalls zurückgewiesen, auch mit `--force-truncate`.

Diese Schutzgrenze ist ausdrücklich noch kein neuer Import-/Publikationsspeicher. Fehlende importierte Dateieinträge werden weiterhin nach der bisherigen Obsolete-Regel behandelt; die vollständige Scope-Korrektur folgt in U03. `--dry-run` bleibt eine reine Dateilesung, keine vollständige Konfliktvorprüfung. Andere Legacy-Writer und vorgelagerte Pipeline-Änderungen sind damit nicht abgesichert oder rückgängig gemacht. Insbesondere kann die schreibende Complete-Pipeline bereits vor dem nachgelagerten Import Dateien geschrieben haben; U05 bleibt erforderlich.

Geändert: `TranslationWorkbenchLangValueImporter`, neue fachliche Ausnahme `TranslationWorkbenchLangImportConflict`, Fehlerausgabe in `ImportTranslationWorkbenchLangValues` sowie Soll-Tests in `TranslationWorkflowTest`. Die ursprünglichen Audit-Ergebnisse bleiben als Vorher-Befund erhalten.

## Umsetzungsstand Exportabsicherung

F05 ist hinsichtlich vorab erkennbarer Sprachdatei-Strukturkonflikte abgesichert: `TranslationWorkbenchLangFileExporter` berechnet zuerst alle Pläne des ausgewählten Scopes einschließlich Pruning. Erst nach konfliktfreier Gesamtplanung beginnt das Schreiben. Ein Konflikt in einer späteren Datei verhindert auch Änderungen, Neuanlagen und Pruning in früheren Dateien. Die vorbereiteten Dateiinhalte bleiben intern; die bestehende JSON-Berichtsstruktur wird nicht um Quellcode-Payloads erweitert.

`ExportTranslationWorkbenchLangFiles` liefert bei Strukturkonflikten Exit-Code 1 sowohl mit als auch ohne `--write`. Betroffene Dateipfade, Keys und Konfliktgründe erscheinen direkt in der Ausgabe; der detaillierte JSON-Bericht bleibt verfügbar. Die Pipeline stoppt daraufhin vor der nachfolgenden Quellcode-Planung. Geplante Änderungen/Prunings im Bericht sind bei Konflikt keine ausgeführten Änderungen; `files_written` und Export-Timeline-Ereignisse bleiben null.

Ein nicht vollständiger/fehlgeschlagener Rückgabewert von `File::put` löst einen Fehler aus statt `written=true`. Das ist keine Dateitransaktion: Fehler während des Schreibens können weiterhin Teilergebnisse hinterlassen. Die koordinierte Erfassung solcher Teilergebnisse, Schutz gegen veraltete Datei-/Datenstände, Freigaberevisionen und Recovery bleiben U05/U04. Auch die skalare Review-Lücke F04 wird durch diesen Schritt noch nicht geschlossen.

Abgesichert durch Mehrdatei-Tests für Schreiben und Vorschau, Command-Fehlerstatus und JSON-Bericht, Pipeline-Stopp sowie fehlgeschlagenes Dateischreiben. Bestehende Tests prüfen weiterhin erfolgreiche Exporte, gezielten Scope, dynamische Werte und Pruning. Die Tests verwenden ausschließlich temporäre Dateien und eine isolierte Datenbank.

## Umsetzungsstand Scan-Abgrenzung

F01/F02 sind für erfolgreich gelesene vorhandene Dateien abgesichert. Der Scanner stellt über `scanWithManifest` Items und die Liste erfolgreich gelesener, zulässiger Dateien gemeinsam bereit. Die vorhandene reine `scan`-Abfrage verwendet denselben Scanner und liefert weiter nur Items. Beide Scan-Commands verwenden für den Entfernungsabgleich das Manifest einschließlich Dateien ohne Treffer. Ausgeschlossene oder nicht vorhandene Pfade begründen keine Archivierung. Ohne zulässige erfolgreich gelesene Dateien zeigt der Command einen Hinweis und führt keinen Entfernungsabgleich aus.

Im alten Entry-Modell werden zunächst nur fehlende Occurrences innerhalb dieses Dateiumfangs stale. Ein betroffener Entry wird nur dann obsolete, wenn keine nicht-stale Occurrence mehr existiert. Fundstellen außerhalb des Teilscans halten einen gemeinsam verwendeten Entry aktiv. Foundation verlangt bei `markObsolete=true` ausdrücklich `reconciliationPaths` aus dem Manifest; Items allein sind keine hinreichende Grundlage für die negative Aussage „nicht mehr vorhanden“.

Ein Fehler beim Lesen/Entdecken einer Datei bricht den Scan vor Beginn des Datenbank-Syncs ab. Die neuen Tests prüfen beide Commands, leere Trefferdateien, ausgeschlossene und nicht vorhandene Pfade, einen späteren Lesefehler und den gemeinsamen Legacy-Entry. Das Audit-Replay wurde an den expliziten Manifest-Vertrag angepasst; historische Ergebnisdateien bleiben unverändert.

Noch offen: dauerhaft gespeicherte Scan-Runs und Fehler-/Dateimanifeste sowie die einmalige gemeinsame Erfassung für die gesamte Pipeline. Die beiden Command-Einstiege scannen weiterhin separat. Die nachfolgende Erweiterung ergänzt vollständig gelöschte Dateien; aus einem fehlenden oder falsch angegebenen Scan-Pfad selbst wird weiterhin keine Entfernung abgeleitet.


## Umsetzungsstand Gelöschte Quelldateien

Beide Scan-Commands übergeben bei aktiviertem Entfernungsabgleich die bisher bekannten Quellpfade an den gemeinsamen Scanner. Dieser liefert nun getrennt erfolgreich gelesene Dateien, erfolgreich durchsuchte Verzeichnisse, darin bestätigte fehlende Dateien und den daraus gebildeten `reconciliation_paths`-Umfang. Der Scanner liest selbst keine Datenbanktabellen; die Commands liefern jeweils ihre bekannten Quellpfade.

Eine bekannte Datei kann als fehlend gelten, wenn sie unter einem tatsächlich vorhandenen, erfolgreich durchsuchten Verzeichnis liegt und weiterhin die geltenden Dateimuster/Ausschlussregeln erfüllt. Ein Dateipfad, der direkt als Scan-Ziel angegeben wird und nicht mehr existiert, liefert diesen Nachweis nicht. Ein komplett fehlendes Scan-Verzeichnis ebenso wenig. Vorhandene, aber nicht besuchte Dateien werden nicht als gelöscht behandelt. Verzeichnisgrenzen werden mit Pfadseparator geprüft; `resources/views-neighbor` gehört nicht zu `resources/views`. Versteckte/VCS-Pfade und geparkte Dateinamen begründen keine Entfernung.

Ein Lese- oder Inventarisierungsfehler verhindert weiterhin die Rückgabe eines verwertbaren Manifests und damit die Datenbankmutation. In der Ausgabe stehen die Anzahl erfolgreich gelesener Dateien und bestätigter fehlender Dateien getrennt. Foundation verwendet dafür den explizit benannten Parameter `reconciliationPaths`; er enthält nun gelesene **und** nachweislich fehlende Pfade, keinen pauschalen Verzeichnispräfix.

Die Tests ergänzen echte Dateilöschungen, fehlende direkte Ziele, Ausschlüsse/Dateimuster/geparkte Namen, benachbarte Verzeichnisnamen sowie Lesefehler bei gleichzeitig fehlenden Dateien. Eine parallele Änderung des Dateisystems nach der Beobachtung und ein dauerhaft gespeicherter gemeinsamer Scan-Run sind damit noch nicht vollständig abgesichert. Dieser Schritt implementiert keinen produktiven Neuscan oder Datenumbau.

## Umsetzungsstand Gemeinsamer Scan-Stand pro Pipeline-Phase

`translation:workbench` eröffnet jetzt eine laufgebundene `TranslationWorkbenchScanSession`. Der erste Scanner-Aufruf erfasst den vereinbarten Scope einmal. Legacy-Scan, Foundation-Sync, weitere Abfragen über den gemeinsamen Scanner und die zentralen Rohdatenberichte verwenden anschließend dieses Manifest. Commands ohne eigene Pfadangabe übernehmen dabei den Pipeline-Scope; eine abweichende explizite Scanner-Pfadliste wird zurückgewiesen. Die bekannten Dateipfade beider Modelle werden vor dem Lauf gemeinsam bereitgestellt. Einzelne Commands außerhalb der Pipeline bleiben eigenständige Scans.

Vor der Weitergabe an Datenbank-Consumer wird der Stand unter `storage/translation-workbench/scan-runs/<UUID>.json` gespeichert: Schema-Version, Scan-ID, optionale UI-Pipeline-ID, Phase, Zeitpunkte, Scope-Konfiguration, SHA-256 der tatsächlich gelesenen Dateiinhalte, Findings, erfolgreich gelesene Dateien/Verzeichnisse und bestätigte fehlende Pfade. Temporäre Datei und anschließendes Rename verhindern ein halb geschriebenes JSON-Ergebnis. Die Snapshot-Dateien erhalten Modus 0600. Der unveränderte `raw_data`-Berichtsvertrag wird durch die separaten Felder `scan_run` (aktueller Stand) und `scan_runs` (bisherige Stände dieses Laufs) ergänzt.

Fehlgeschlagene Erfassungen werden als Fehlerstand dokumentiert und innerhalb der Phase nicht automatisch erneut versucht. Kann der gemeinsame Stand nicht gespeichert werden, wird er nicht an die Synchronisierung weitergegeben. Der Pipeline-Abschluss-/Fehlerbericht startet keinen zusätzlichen Scan. Nach einem ausdrücklich schreibenden Quellcode-Schritt wird der Stand verworfen, auch wenn dieser Schritt fehlschlägt; die nächste Erfassung erhält eine neue ID. Bereits gespeicherte Stände bleiben als vorherige Beobachtung erhalten. Die Session wird im `finally` des Pipeline-Einstiegs beendet.

Verifikation: isolierte Tests für einmaliges Dateilesen über Scan, Foundation und Berichte, eingeschränkten Scope, identische Scan-IDs, unveränderten gespeicherten Vorher-Stand, neue Phase nach einem Quellcode-Writer, Lese- und Speicherfehler sowie Session-Aufräumen. Kein produktiver Scan und keine produktive Datenbankänderung wurden dafür ausgeführt.

Grenzen: Dies ist der gemeinsame Scanner-Stand, noch kein vollständiger Abschluss von U03. Die spezialisierte Dynamic-Options-Analyse verwendet weiterhin ihre eigene Erfassung; unmittelbare Dateiprüfungen vor Quellcodeänderungen bleiben absichtlich frisch. Es gibt noch keinen Wiederaufnahme-/Recovery-Vertrag, keine Aufbewahrungsbereinigung und keine neue Scan-Run-Datenbanktabelle. Ein Snapshot ist eine dokumentierte Beobachtung, keine atomare Sperre gegen gleichzeitige externe Dateiänderungen. Die noch offene Ablösung der doppelten Legacy-/Foundation-Datenversorgung, Positionsidentität und Originalerfassung bleiben Teil der weiteren Konsolidierung.

## Verbraucherprüfung vor Ablösung der Legacy Versorgung

Die gemeinsame Erfassung beseitigt Mehrfachscans, aber noch nicht die doppelte Datenversorgung. `RunTranslationWorkbench` führt weiterhin `scan`, `import-existing`, `discover-dynamic-options --sync` und `detect-duplicates` aus. Ein fehlender Menüpunkt bedeutet hier nicht, dass der zugehörige Datenbestand nicht mehr beschrieben wird.

### Aktive Abhängigkeiten

Alle nachfolgenden Klassennamen beziehen sich auf `packages/gunreip/laravel-translation-workbench/src/`.

| Verbraucher | Tatsächlich verwendete Daten | Konsequenz für die Ablösung |
| --- | --- | --- |
| `Console/ScanTranslationWorkbench` | Schreibt Entries, Occurrences und Events | Versorgung erst entfernen, wenn die verbleibenden Legacy-Consumer abgelöst sind. |
| `Console/ImportExistingTranslations` | Liest Legacy-Entries, schreibt Legacy-Values und Entry-Metadaten/Events | Kein notwendiger Wertlieferant für den geprüften Foundation-Editor. Historische Werte vor Stilllegung trotzdem vergleichen; Import ist nicht identisch mit `import-lang-values`. |
| `Console/DetectDuplicateCandidates` | Liest Legacy-Entries; ersetzt den Inhalt von DuplicateCandidates | Fingerprint-Diagnose, keine gleichwertige Implementierung der fachlichen Shared-Candidate-Regel. Nicht einfach durch `detect-shared-key-candidates` ersetzen. |
| `Console/DiscoverDynamicOptions` | Liest/erzeugt Entries; schreibt Values, DynamicValues, Events und OptionDiscoveries | Zentraler verbleibender Übergang zwischen Legacy und Foundation. Die Optionsbeobachtung muss erhalten bleiben, die Legacy-Kopien sollen entfallen. |
| `Console/ClassifyDynamicValues::matchingOptionDiscoveries` | Liest aktive OptionDiscoveries anhand Fundstelle und mehrerer Key-Varianten | Benötigt Optionsdaten, aber keine Entry-ID als fachliche Identität. Matching und Mehrdeutigkeiten beim Umbau erhalten bzw. ausdrücklich behandeln. |
| `Console/ResolveUnknownDynamicSources::bestDiscoveryForSource` | Liest OptionDiscoveries anhand Keys, Scope und Pfad; bewertet Treffer | Darf durch Abschalten der Discovery nicht seine Eingabedaten verlieren. |
| `Livewire/TranslationWorkbenchEntries::addFindingDynamicContextSelect` | Liest OptionDiscoveries für Anzahl, Optionsanzahl und Quelltypen | Sichtbare Informationen der aktiven Workbench hängen noch daran. |
| `Livewire/TranslationWorkbenchEntries::databaseTableCallouts` | Zeigt auch Zähler/Informationen zu Legacy-Tabellen | Diagnoseanzeige von Tabellen ist von fachlicher Datenversorgung zu unterscheiden. |
| `Foundation/TranslationWorkbenchDynamicOptions` | Liest DynamicKeyValues, DynamicSources, DynamicSourceValues und bestätigte SourceCandidates | Dynamischer Editor benötigt die alten Values-/DynamicValues-Tabellen nicht. Er benötigt aber die erfolgreich aufgeklärten Foundation-Quellen. |
| `Console/CleanupExcludedTranslationWorkbenchPaths` | Bearbeitet Foundation und Legacy einschließlich OptionDiscoveries | Bis zur Migration als ausdrücklich gemischten Wartungsschritt behandeln. |
| `Console/RunTranslationWorkbench` | Liest bekannte Occurrence- und SourceFile-Pfade für den gemeinsamen Scan | Diese direkte Legacy-Abhängigkeit muss bei Abschaltung der alten Versorgung ebenfalls entfallen. |

`TranslationWorkbenchOldEntries` und die alte Raw-Data-Route sind in `routes/admin.php` sowie den Livewire-Registrierungen auskommentiert. **Die Klasse `TranslationWorkbenchRawData` ist trotzdem nicht vollständig stillgelegt:** `TranslationWorkbenchRawDataNew` erbt von ihr und stellt darüber die aktuelle Foundation-Tabellenansicht bereit. Die alte Klasse kann erst nach Trennung der gemeinsam verwendeten Darstellung und Methoden archiviert werden. Die neue Tabellenliste enthält die Legacy-Tabellen nicht.

### Kein gleichwertiger Ersatz durch ähnlich benannte Commands

`detect-duplicates` gruppiert alte Entries nach Source-, Expression- und Semantic-Fingerprints. `detect-shared-key-candidates` sucht dagegen neue Findings passend zu bereits bulk-reviewed Shared-Entscheidungen. Keiner dieser beiden Zwecke darf beim Entfernen des jeweils anderen still als abgedeckt gelten. Die vereinbarte allgemeine Regel „mindestens zwei aktive gleichlautende Literale mit unterschiedlichen SuggestedKeys“ benötigt ihre eigene fachliche Abnahme einschließlich Rücknahme bei weniger als zwei.

Die OptionDiscovery speichert nicht nur `matched_entry_id`, sondern auch `workbench_suggested_key`, Suggested-State und Legacy-Metadaten. Aktuelle Consumer nutzen insbesondere die Key-Varianten. Deshalb wäre nur `ensureWorkbenchEntry()` zu entfernen und `matched_entry_id=null` zu setzen keine vollständige Umstellung: Die bisher aus dem Entry übernommenen Informationen müssen gegen Findings/Keys aufgelöst und mehrdeutige Zuordnungen kenntlich gemacht werden.

### Nachgewiesene unabhängige Bearbeitung

`TranslationWorkflowTest` ergänzt zwei Verhaltenstests, in denen die isolierte SQLite-Datenbank ohne `entries`, `occurrences`, `events`, `values`, `dynamic_values`, `duplicate_candidates` und `option_discoveries` betrieben wird. Foundation-Erfassung, Key-Annahme, skalare bzw. dynamische Editor-Aktionen, neue Timeline-Ereignisse und Export in temporäre Sprachdateien bestehen weiterhin. Der dynamische Fall verwendet bereits vorhandene Foundation-Optionswerte; er beweist nicht die automatische Erkennung neuer dynamischer Quellen. Auch das vollständige PostgreSQL-Rendering der Listen-/Diagnoseansicht ist damit nicht geprüft.

Die Prüfung verändert keine Produktionsdaten. Bestehende Legacy-Werte können weiterhin historische oder bislang nicht übernommene Informationen enthalten; die Code-Abhängigkeit allein ist kein Lösch- oder Migrationsnachweis.

### Nächster Umsetzungsschritt

Zuerst `DiscoverDynamicOptions` in reine Erfassung und Persistierung trennen, bei unverändertem beobachtbarem Ergebnis. Anschließend die Optionsbeobachtungen gegen Foundation-Findings/Keys zuordnen und die Legacy-Entry-/Value-Schreibzugriffe ersetzen. Vor Umschaltung müssen Blade-Arrays, Component-Properties, Provider-Methoden, mehrere mögliche Findings, Teilscans und verschwundene Optionen sowie der Erhalt bestehender Übersetzungen geprüft sein. Die drei aktiven Options-Consumer müssen dieselben Informationen behalten.

Erst danach die alten Import-/Duplikat-/Scan-Schritte aus der regulären Pipeline herauslösen, historische Differenzen separat übernehmen und die betroffenen Command-Einstiege ausdrücklich stilllegen/archivieren. Der laufende Stand wird nicht durch einen neuen, parallel dauerhaft gepflegten Writer ergänzt.

## Umsetzungsstand Trennung der dynamischen Optionserfassung

`DiscoverDynamicOptions` delegiert jetzt die Erfassung an `Scanner/TranslationWorkbenchDynamicOptionCollector` und die bestehende Datenbankverarbeitung an `Foundation/TranslationWorkbenchLegacyDynamicOptionSynchronizer`. Der Command enthält nur Optionen, Ablaufsteuerung, Konsolenausgabe und Berichtserstellung. Der Collector liefert eine Collection der beobachteten Optionen und enthält keine Workbench-Modelle oder Datenbankzugriffe. Der Synchronizer bekommt diese Collection, den Schreibmodus und die Quellsprache explizit übergeben; er durchsucht keine Dateien und liest keine Artisan-Optionen. Datenbankwarnungen werden an den Command zurückgemeldet.

Dieser Schritt ersetzt die bisherige Implementierung durch getrennte Zuständigkeiten; es existiert kein zusätzlicher paralleler Writer. Die Speicherung bleibt ausdrücklich Legacy: Entries, Values, DynamicValues, Events und OptionDiscoveries einschließlich bisherigem Matching, Status und Metadaten werden weitergeführt. Die gemeinsame Transaktion und der Vorrang von `--dry-run` vor `--sync` bleiben erhalten. Es ist noch keine Migration auf Foundation erfolgt.

Die Verhaltenstests wurden zunächst gegen den alten Command ausgeführt und bestehen nach der Auslagerung weiter: Public-Array-Optionen, unaufgelöste Quellen, reine Vorschau, Synchronisierung und wiederholte Ausführung. Weitere Tests prüfen Provider-Ergebnisse mit String-/Label-Payloads, eine gezielt ausgewählte Blade-Datei ohne weitere Views, Erfassung ohne Datenbankabfragen beim getesteten Provider sowie vollständigen Rollback bei einem späteren Schreibfehler.

Die bestehenden Erfassungsregeln sind unverändert: Provider-Methoden werden weiterhin tatsächlich aufgerufen und können eigene Nebenwirkungen haben; ein Collector-Aufruf ist deshalb nicht grundsätzlich eine Sandbox für fremden Provider-Code. Optionsquellen werden weiterhin übergreifend im App-Verzeichnis gesucht und anhand der Variablennamen zusammengeführt. Der gemeinsame Pipeline-Scan-Stand umfasst diese spezialisierte Erfassung noch nicht. Die nächsten fachlichen Änderungen betreffen die eindeutige Foundation-Zuordnung, Mehrdeutigkeiten und den Erhalt bearbeiteter Werte; sie werden nicht als bloße Auslagerung getarnt.

## Umsetzungsstand Foundation Zuordnungsprüfung für Optionen

`TranslationWorkbenchDynamicOptionAssignment` ergänzt eine ausschließlich lesende Vorprüfung vor der bisherigen Optionsspeicherung. Der Collector liefert dafür zusätzlich die Endzeile der beobachteten Schleife. Kandidaten müssen aus derselben aktiven Quelldatei und dem beobachteten Zeilenbereich stammen, aktiv sein und im gespeicherten `dynamic_option_context` zu Options-, Key- und Label-Variable passen. Berücksichtigt wird die direkt verwendete Label-Variable; transformierte Ausdrücke werden nicht als gleichwertige Optionszuordnung interpretiert. Ein gleicher SuggestedKey oder Scope irgendwo im Bestand reicht nicht als Treffer.

Die Prüfung unterscheidet `matched`, `missing_finding`, `ambiguous_finding`, `missing_key` und `ambiguous_key`. Nur genau ein Finding mit genau einem aktiven Link zu einem nicht obsoleten Key ergibt `matched`. Kandidaten-IDs bleiben im Bericht nachvollziehbar; bei Mehrdeutigkeit wird keine einzelne ID als Zuordnung ausgegeben. Auch mehrere Findings am gleichen Key bleiben zunächst eine Mehrdeutigkeit, keine stillschweigende Zusammenführung.

`discover-dynamic-options` zeigt die Zähler und problematischen Dateistellen an. Der bestehende gemeinsame JSON-Bericht enthält das vollständige Ergebnis unter `plan_summary.foundation_assignment`, gekennzeichnet als `read_only_preflight`; `raw_data` bleibt unverändert. Die bisherige Legacy-Synchronisierung läuft weiterhin nach ihrem bisherigen Vertrag. Die Vorschau ist weder eine ausgeführte Migration noch eine Freigabe zur automatischen Zuordnung. Insbesondere können reine Label-Ausgaben ohne erfasste Übersetzungsfunktion zunächst `missing_finding` ergeben; diese Fälle müssen vor Umschaltung ausdrücklich behandelt werden.

Verhaltenstests prüfen eindeutige Zuordnung trotz abweichendem vorgeschlagenen Key, andere Datei/Schleife/Variable, obsolete Findings/Keys, inaktive Links, mehrere aktive Keys und mehrere Findings. Die Prüfung führt in diesen Fällen ausschließlich SELECT-Abfragen aus; Timeline und Werte bleiben unverändert. Command-Tests prüfen zusätzlich die sichtbare Vorschau und ihre JSON-Ausgabe. Die Prüfung arbeitet mit vorhandener Foundation-Beobachtung, nicht mit einem atomar gesperrten Dateistand; ein späterer Writer benötigt weiterhin eine Prüfung auf veraltete Zuordnungen sowie einen Vertrag für fehlende und mehrdeutige Fälle.

## Umsetzungsstand Optionsbeobachtungen ohne Legacy Schreibzugriffe

`DiscoverDynamicOptions --sync` verwendet jetzt `TranslationWorkbenchDynamicOptionSynchronizer`. Dieser speichert ausschließlich OptionDiscoveries und Foundation-Timeline-Ereignisse. Es werden keine Legacy-Entries, Values oder DynamicValues mehr angelegt oder verändert. Die vorherige Implementierung wurde nach `src/_old/Foundation/TranslationWorkbenchLegacyDynamicOptionSynchronizer.php` verschoben; sie wird vom aktiven Command nicht mehr verwendet. Historische Legacy-Daten bleiben bestehen.

Die Zuordnung wird innerhalb des Synchronisierungsvorgangs frisch berechnet. Eindeutige Beobachtungen erhalten `status=open`, ungeklärte `status=needs_review`. Die genaue Ursache, Kandidaten-IDs und gegebenenfalls Finding-/Key-ID stehen in `meta.foundation_assignment` sowie im Bericht. Bei einem erneut beobachteten Datensatz bleiben ID, erste Erfassung und zusätzliche Metadaten erhalten; die alte Entry-Verknüpfung und daraus abgeleitete Zuordnungsfelder werden entfernt. Änderungen an Beobachtung oder Zuordnung erzeugen `dynamic_option_observation_recorded`; unveränderte Wiederholungen aktualisieren nur die Beobachtungszeit. Speicherung und Timeline laufen in derselben Transaktion.

`--source-locale` wurde aus diesem Command und seinen beiden Pipeline-Aufrufen entfernt: Eine Optionsbeobachtung ist keine Übersetzung in eine bestimmte Sprache. Der Command akzeptiert keinen still ignorierten Alias. Die übrigen Commands behalten ihre fachlich erforderlichen Sprachoptionen. Die Zusammenfassung berichtet nun Beobachtungen statt alter Value-Kopien; `plan_summary.foundation_assignment.mode` unterscheidet `read_only_preflight` und `persisted_observations`.

Klassifizierung und Auflösung unbekannter Quellen berücksichtigen für neu verwaltete Beobachtungen ausschließlich die exakt gespeicherte Finding-/Key-Zuordnung. `needs_review` wird nicht durch einen allgemeinen Key-/Scope-Treffer umgangen. Abfragen können den Datensatz über die explizite Finding-ID finden, auch wenn Schleifenzeile und Finding-Zeile oder die vorgeschlagenen Keys voneinander abweichen. Die aktive Workbench berücksichtigt diese Finding-ID ebenfalls in ihren Informationszählern. Historische Beobachtungen ohne neues Zuordnungsmerkmal behalten bis zu ihrer gezielten Neuerfassung das bisherige Matching; es findet keine pauschale rückwirkende Datenmigration statt.

Tests prüfen ungeklärte Beobachtungen ohne neue Legacy-Daten, Foundation-Klassifizierung mit abweichender Schleifen-/Finding-Zeile und Key-Bezeichnung, den Erhalt bearbeiteter dynamischer Übersetzungen, das Zurückweisen einer inzwischen anderen Key-ID, Mehrdeutigkeiten trotz allgemeinem Key-Treffer sowie Transaktionsrollback einschließlich Timeline bei einem späteren Speicherfehler. Diese Tests verwenden SQLite und temporäre Dateien. Die PostgreSQL-Listenansicht wurde in diesem Schritt nicht im Browser geprüft.

Weiterhin offen: bewusste Übernahme historischer Beobachtungen und Werte, Entfernungsabgleich nach vollständig erfolgreichem Optionsscan, Behandlung bisher gar nicht als Finding erfasster Plain-Labels und Mehrdeutigkeiten der übergreifenden Provider-/Variablensuche. Ungeklärte Fälle bleiben erhalten, werden aber noch nicht automatisch in neue Findings umgewandelt. Die reguläre Pipeline enthält weiterhin den separaten Legacy-Scan, Import und Duplikatlauf; deren Ablösung ist mit dieser Umstellung noch nicht abgeschlossen. Die Datenbank erhält keine neue Tabelle und der produktive Bestand wurde nicht verändert.


## Umsetzungsstand reguläre Pipeline ohne Legacy-Versorgung

`RunTranslationWorkbench` ruft weder beim Einstieg noch beim Refresh nach Codeänderungen `scan`, `import-existing` oder `detect-duplicates` auf. `sync-foundation` erfasst und synchronisiert die Findings; bekannte Pfade für den gemeinsamen Scan stammen nur noch aus SourceFiles. Die bisherigen Skip-Optionen `--skip-import` und `--skip-duplicates` sind entfernt, nicht als wirkungslose Aliase erhalten. Die übrigen Schalter behalten ihren Zweck; `--skip-foundation` überspringt diese Erfassung ausdrücklich. Die Truncate-Hilfe benennt Foundation und LangValues; historische Legacy-Tabellen werden durch die Pipeline nicht mehr geleert.

Die Workbench-Pipelineanzeige enthält dieselben aktiven Schritte; Scan-Zähler lesen den Foundation-Bericht statt eines veralteten Legacy-Scan-Berichts. Die ursprünglichen Workflow-Graphen sind als Bestandsaufnahme vor der Konsolidierung gekennzeichnet und enthalten einen sichtbaren Hinweis auf die geänderte Pipeline. Eine vollständige Neuzeichnung ist nicht Teil dieses Schritts.

Die drei bisherigen Einzelcommands bleiben vorerst registriert und können bei explizitem Aufruf weiterhin Legacy-Daten bearbeiten. Sie sind also noch nicht vollständig stillgelegt. Historische Werte müssen vor Archivierung und Datenmigration abgeglichen werden. Die allgemeine Shared-Candidate-Regel wird nicht als durch die entfernte Fingerprint-Diagnose oder den bestehenden Shared-Key-Command abgedeckt behauptet. Archivdateien unter `-old`/`_old` zählen nicht als aktive Verbraucher; aktive Referenzen auf Archivcode müssten gesondert entfernt werden.

Regressionen prüfen Foundation-Erfassung bei entfernten Legacy-Tabellen in SQLite, die Refresh-Schrittliste, identischen Scan-Stand für Foundation/Berichte, Fehlerabbruch und erneute Beobachtung nach einem Source-Writer. Es wurde kein produktiver Scan oder Datenbankumbau ausgeführt.


## Historischer Workbench-Werteabgleich

Der bestehende Diagnosecommand besitzt jetzt `--package-history`, optional zusammen mit `--json`. Der bisherige Vergleich der anwendungsweiten `translation_values` bleibt unverändert. Der neue Prüfbereich umfasst die Package-Tabellen `translation_workbench_values` und `translation_workbench_dynamic_values` gegenüber Foundation-LangValues, KeyValues und DynamicKeyValues. Der PostgreSQL-Lauf nutzt eine `REPEATABLE READ READ ONLY`-Transaktion. Er schreibt keine Workbench-Daten, Reviews, Reports in storage oder Sprachdateien.

Nur explizite `translation_key`/`existing_key` eines Entries begründen einen Kandidatenvergleich. Widersprüchliche Keys und fehlende Zuordnungen bleiben sichtbar; SuggestedKeys lösen keine automatische Verknüpfung aus. `source:<locale>` kennzeichnet die historischen Quellsprachwerte. Andere Optionsbezeichnungen ohne Sprache bleiben Beobachtungen. Dynamische Werte werden zusätzlich anhand ihres `value_key` verglichen. Normalisierte Locale-Kollisionen mit mehreren Zeilen desselben Zielstores sind mehrdeutig. Null und Leerstring sind verschieden. Textgleichheit bedeutet weder gleiche Review-/Statusdaten noch Löschfreigabe.

Ergebnis des produktiven **rein lesenden** Abgleichs: 995 Zeilen, davon 896 textgleich, 86 ohne expliziten Key und 13 abweichend. Die 86 ungeklärten Zeilen sind 43 Optionsbezeichnungen und 43 englische dynamische Werte. Die 13 Abweichungen betreffen obsolete Entries; ihre historischen Werte sind JSON-Objekte, während die Zielzeilen Einzeltexte enthalten. Beispielsweise steht unter `ui.label` historisch eine Gruppe von Labels, heute hingegen der aktive Einzeltext `False labeling`. Eine automatische Überschreibung wäre falsch. IDs und abweichende Inhalte stehen in `historical-workbench-values.json`; die vollständige Command-Ausgabe ist erneut abrufbar.

Grenzen: Keine Auflösung umbenannter Keys über History, kein Entpacken alter JSON-Container, keine Zusammenführung von Optionsbezeichnungen und Übersetzungen und kein Ableiten dynamischer Identitäten aus zusammengesetzten Lang-Datei-Keys. Ein fehlender Kandidat beweist daher noch keinen Datenverlust. Die nächsten Entscheidungen betreffen diese Zuordnungen, nicht die pauschale Migration aller historischen Werte. Die alten Einzelcommands bleiben bis dahin bestehen.

Tests decken exakte skalare und dynamische Treffer, abweichende Texte, Null/Leerstring, fehlende Zielwerte, SuggestedKey-only, widersprüchliche Keys, sprachlose Optionen und mehrdeutige Locale-Zeilen ab. Die geprüften Diagnoseaufrufe führen keine INSERT-/UPDATE-/DELETE-Abfragen aus.


## Nachprüfung der offenen historischen Werte

Die Nachprüfung in `historical-value-review.md` belegt alle 86 zunächst ungeklärten Zeilen über sechs aktuelle explizite Options-Key-Aufrufe und exakte dynamische Werte. Die 13 JSON-Container enthalten 39 Einzeltexte, die unter ihren vollständigen Unterkeys textgleich in LangValues vorhanden sind. Eine erneute Übernahme dieser Texte ist nicht nötig. Historische Tabellen und Metadaten bleiben erhalten; kein produktiver Schreibzugriff wurde ausgeführt. Die Belege sind in `historical-value-evidence.json` gespeichert und über `inspect-historical-values.php` erneut lesend erfassbar. Die Einzelcommands sind weiterhin registriert; ihre Archivierung ist der nächste gesonderte Code-Schritt.


## Stilllegung der drei Legacy-Writer

`ScanTranslationWorkbench`, `ImportExistingTranslations` und `DetectDuplicateCandidates` sind aus den Imports und Command-Registrierungen des ServiceProviders entfernt. Die Implementierungen liegen unverändert unter `src/_old/Console/`. Es gibt keine Weiterleitungsaliase; die bisherigen Namen `translation-workbench:scan`, `translation-workbench:import-existing` und `translation-workbench:detect-duplicates` sind in Artisan nicht mehr verfügbar. Die aktiven Verzeichnisse app/bootstrap/routes/config und das Package-src enthalten keine weiteren direkten Klassenaufrufer. Die historischen Workflow-Graphen behalten die alten Abläufe zur Gegenüberstellung, weisen aber ausdrücklich auf Archivierung und fehlende Artisan-Registrierung hin.

Die Foundation-Tests für Teilscans, Nulltreffer-Dateien, gelöschte Dateien, Bereichsgrenzen und Lesefehler bleiben aktiv. Ihre früheren Legacy-Varianten wurden als nicht ausführbarer historischer Ausschnitt unter `tests/_old/TranslationWorkbench/LegacyScanReconciliationTests.php.disabled` aufbewahrt. Drei neue Regressionen prüfen die fehlende Registrierung, CommandNotFound bei Aufruf und dabei ausbleibende Datenbankabfragen. `artisan list --raw` bestätigt die verbleibenden Foundation-Commands.

Keine Tabellen, historischen Werte oder Metadaten wurden gelöscht oder migriert. Der gemischte Wartungscommand für ausgeschlossene Pfade und die Behandlung historischer Optionsbeobachtungen bleiben eigene offene Arbeitspunkte. Die allgemeine Shared-Candidate-Regel ist weiterhin nicht mit der entfernten Fingerprint-Duplikaterkennung gleichzusetzen.


## Bereinigung ausgeschlossener Pfade und historische Optionsbeobachtungen

`cleanup-excluded-paths` berücksichtigt jetzt ausschließlich Foundation-Quelldateien, Findings, Key-Verknüpfungen, dynamische Quellen/Kandidaten und OptionDiscoveries. Historische Entries und Occurrences werden weder ausgewertet noch verändert. Der ursprüngliche gemischte Command ist unter `src/_old/Console/CleanupExcludedTranslationWorkbenchPaths.php` archiviert. Der aktive Command bleibt ein ausdrücklich aufrufbarer Wartungsschritt; er wurde hier nicht produktiv ausgeführt. Seine Vorschau protokolliert weiterhin ein Activity-Ereignis und ist deshalb nicht als vollständig schreibfrei zu bezeichnen.

Zusätzlich korrigiert: `exclude_paths` sind wörtliche Pfadpräfixe. `_`, `%` und das Escape-Zeichen `!` dürfen im SQL-LIKE-Ausdruck keine Platzhalterwirkung entfalten. Exakte Pfadtreffer und untergeordnete Dateien werden berücksichtigt, ähnlich benannte Nachbarverzeichnisse nicht. Tests prüfen Vorschau und Apply für alle drei Zeichen, unveränderte Legacy-Entries und bearbeitete LangValues sowie die weiterhin passende Behandlung von Findings, Key-Verknüpfungen und Optionsbeobachtungen. Keine bestehenden Übersetzungswerte werden durch diese Bereinigung gelöscht.

Der produktive rein lesende Options-Audit (`inspect-option-observations.php`, PostgreSQL `REPEATABLE READ READ ONLY`) ergibt 25 aktive historische Beobachtungen, keine mit `foundation_assignment`; neun Beobachtungen besitzen aktive verknüpfte dynamische Quellen. Belege: `option-observation-inventory.json`. Diese Zahlen beschreiben direkte Datenbankreferenzen, nicht sämtliche möglichen indirekten Consumer-Treffer.

Das historische Matching in `DynamicOptionSynchronizer::appliesTo` bleibt daher vorläufig bestehen. Es würde ohne Zuordnungsmerkmal weiterhin Key-/Scope-/Pfad-Kandidaten akzeptieren; das ist eine bekannte Übergangsstelle, keine bereits abgeschlossene Foundation-Zuordnung. Ein pauschales `return false` würde die aktive Versorgung verändern. Auch ein pauschaler Rescan ist noch keine sichere Migration: Die neue Zuordnungsprüfung erwartet `dynamic_option_context` mit direkter Label-Variable, während die sechs zuvor geprüften aktuellen `TranslationWorkbenchOption::label`-Findings dieses Merkmal im gespeicherten Bestand nicht besitzen. Vor Umschaltung müssen diese ausdrücklich gekeyten Aufrufe und ihre Optionsquelle nachvollziehbar unterstützt werden, einschließlich Mehrdeutigkeit und Bestandserhalt. Es wurden keine historischen Beobachtungen neu zugeordnet oder produktiven Provider aufgerufen.


## Explizite Optionsaufrufe in der Foundation-Zuordnungsprüfung

Scanner und Optionscollector verwenden `Scanner/WorkbenchOptionCall` für ausdrücklich vollqualifizierte `TranslationWorkbenchOption::label`-Aufrufe mit literalem Owner-Key, direkter Schlüsselvariable (optional `(string)`) und direkter Fallback-Labelvariable. Der Scanner ergänzt den vorhandenen foreach-Optionskontext; der Collector erkennt dieselbe Aufrufform als `workbench_option_label`. Transformationen und komplexe Fallback-Ausdrücke werden nicht als direkte Variablen gleichgesetzt. Der Lauf außerhalb einer entsprechenden foreach-Schleife begründet keine Optionszuordnung.

Die bestehende Zuordnungsprüfung verlangt weiterhin aktiven Quellpfad, passenden Zeilenbereich, dieselbe Options-/Key-/Labelvariable, ein eindeutiges Finding und einen eindeutigen aktiven Key. Für explizite Aufrufe muss dessen Name zusätzlich dem `option_owner_key` entsprechen; andernfalls lautet der Befund `owner_key_mismatch`. Es wird keine abweichende Key-Verknüpfung korrigiert. Der Report enthält diesen zusätzlichen Status, ungeklärte Beobachtungen bleiben `needs_review`.

Tests prüfen die vollständige Scanner–Foundation–Collector–Assignment-Kette sowie falsche Schlüsselvariable, transformierten Fallback, mehrere Findings und abweichenden Owner-Key. Dies betrifft die geprüfte direkte foreach-Aufrufform; indexierte Fallbacks außerhalb der Schleife und die übergreifende Providerauflösung sind damit nicht vollständig migriert. Historische gespeicherte Findings erhalten die neuen Metadaten erst durch eine bewusste Neuerfassung. Kein produktiver Scan und keine Migration wurden ausgeführt; das bisherige historische Matching bleibt bis zum geprüften Übergang bestehen.


## Indexierte Optionsaufrufe außerhalb von Schleifen

`WorkbenchOptionCall::indexedMatches` erkennt zusätzlich den ausdrücklich gekeyten vollqualifizierten Aufruf mit direktem Arrayzugriff, beispielsweise `Option::label('ui.status', (string) $selected, $statusOptions[$selected] ?? $emptyValue)`. Der Null-Coalescing-Fallback ist optional und wird in dieser Ausbaustufe nur als direkte Variable unterstützt. Auswahlvariable und Arrayindex müssen übereinstimmen; andere Indizes, Transformationen und komplexe Ausdrücke werden nicht geraten.

Der Scanner speichert dafür `dynamic_option_context` mit `source_expression_type=indexed_option`, Optionsvariable und Auswahlvariable. Es wird keine foreach-Labelvariable erfunden (`label_variable` bleibt leer). Der Collector erfasst solche Aufrufe auch in Dateien ohne foreach-Schleife und liefert ihren eigenen Zeilenbereich und expliziten Owner-Key. Assignment prüft denselben Ausdruckstyp, Options-/Auswahlvariable, Quellpfad, Zeilenbereich und Owner-Key sowie die schon bestehenden Eindeutigkeitsregeln. Die Speicherung bleibt beim vorhandenen Synchronizer; es gibt keinen zusätzlichen Writer.

Vier Verhaltenstests durchlaufen Erfassung, Foundation-Synchronisierung, Collector, Zuordnungsprüfung und Speicherung in der isolierten Datenbank. Sie prüfen direkte Indizierung mit/ohne variablem Fallback und das Zurückweisen falscher Indizes bzw. transformierter Ausdrücke. Die Erkennung eines konkreten Optionsaufrufs ist keine Bestätigung der bisherigen app-weiten Provider-/Variablenauflösung. Diese Herkunftsprüfung und die kontrollierte Neuerfassung historischer Beobachtungen bleiben offen. Es wurde kein produktiver Scan oder Provider-Aufruf durchgeführt und das historische Matching wurde nicht abgeschaltet.


## Trennung mehrdeutiger Optionsquellen

Der Collector führte bislang gleichnamige öffentliche Arrays dateiübergreifend zusammen; die nachfolgende Collection-Merge-Operation konnte dieses Ergebnis durch einen Provider überschreiben. Nun bleiben Quellen einzeln unter `source_candidates` erhalten. Genau ein Kandidat erhält `source_resolution=single_candidate`; mehrere Kandidaten, auch bei identischem Inhalt, erhalten `ambiguous_source` und keinen zusammengeführten Optionsbestand. Öffentliche Arrays und Provider konkurrieren nach derselben Regel. Verzeichnisse mit dem Segment `-old` oder `_old` liefern keine aktiven PHP-Quellen.

Die Zuordnungsprüfung meldet `ambiguous_source` vor einer erfolgreichen Finding-/Key-Zuordnung. Dadurch bleibt die Beobachtung `needs_review`, ohne freigegebene Finding-/Key-ID. Kandidaten einschließlich Quelle und Payload stehen im Report und in der gespeicherten Assignment-Metadatenstruktur. Bei zuvor gespeicherten Optionen hält `options_before_source_ambiguity` deren ersten Bestand und Herkunft fest; zusätzlich zeichnet die vorhandene Timeline die Änderung auf. Der aktive Optionsbestand wird nicht mit einem geratenen Ergebnis überschrieben; er ist während Mehrdeutigkeit leer und wird durch die bestehenden Assignment-Filter nicht konsumiert. Vorhandene Übersetzungswerte werden nicht verändert.

Tests prüfen widersprüchliche Arrays, identische Arrays verschiedener Dateien und Konkurrenz zwischen Array und Provider sowie den Erhalt der vorherigen Optionen und Ablehnung durch `appliesTo`. Weitere Tests prüfen den Ausschluss beider Archivverzeichnisse. Kein produktiver Scan, Provider-Aufruf oder Datenbankumbau wurde ausgeführt.

Verbleibende Grenze: `single_candidate` bezeichnet nur einen Kandidaten in der bisherigen app-weiten Variablensuche, keine nachgewiesene Verbindung zwischen Component und View. Die tatsächliche Herkunft über Render-/Include-Verbindungen muss noch geprüft werden. Provider werden weiterhin tatsächlich aufgerufen; sie sind nicht als nebenwirkungsfreie Sandbox zu verstehen. Vor kontrollierter produktiver Neuerfassung müssen insbesondere die nun sichtbar werdenden Mehrdeutigkeiten geprüft werden. Das historische Matching bleibt bis zu diesem Übergang bestehen.


## Statische View-Verbindungen der Optionsquellen

`TranslationWorkbenchOptionViewBinding` ergänzt eine lesende Erreichbarkeitsprüfung. Sie verfolgt literale `return view(...)`-Deklarationen einer Quelldatei über literale `@include(...)`/`@includeIf(...)` ohne Datenargumente zur betroffenen View. PHP-Kommentare der Ausgangsdatei und Blade-Kommentare der Views liefern keine Kanten. Besuchte Views verhindern Endlosschleifen. Fehlende Views werden angegeben. Dynamische Includes, Includes mit Datenargumenten, namespaced Views und Blade-Component-Tags werden nicht als nachgewiesene Verbindungen interpretiert.

Der Collector ergänzt jeden Quellenkandidaten um `view_binding` mit Status und Dateikette. Die bisherige Auswahlregel bleibt unverändert: mehrere gleichnamige Kandidaten bleiben `ambiguous_source`. `static_view_path` bedeutet statisch erreichbar, nicht bewiesene Laufzeit-Variablenherkunft. Insbesondere werden lokale Variablenzuweisungen und bedingte Laufzeitpfade noch nicht ausgewertet; auch eine `return view`-Deklaration belegt alleine nicht die tatsächlich ausgeführte Render-Methode. Diese Grenze verhindert, dass ein neuer Hinweis still zur Freigabe einer Quelle wird.

Das quelltextbasierte Audit `inspect-option-view-bindings.php` benötigt keine Datenbank und ruft keine Provider auf. Aus den zehn historischen Quellenreferenzen der sechs untersuchten Entries ergeben sich sechs statische View-Pfade und vier unbestätigte Verbindungen. Für jede Fundstelle ist genau eine Verbindung vorhanden: RoleList versorgt beide Rollen-Modals; CreatePerson die drei Create-Fundstellen und EditPerson die Edit-Fundstelle. Die jeweils mitgeführten Create-/Edit-Gegenstücke haben keinen statischen Pfad zur Ziel-View. Belege mit Dateiketten: `option-view-bindings.json`.

Fünf Verhaltenstests prüfen direkte und optionale Includes, Datenargumente, dynamische Includes, Blade-Kommentare und zyklische Includes. Die Produktionsdaten und die historische Matching-Regel bleiben unverändert. Der nächste Schritt ist die Prüfung von Variablenherkunft/Überschreibungen, bevor die statischen Verbindungen zur Kandidatenauswahl verwendet werden.


## Prüfung direkter Optionsvariablen-Überschreibungen

Die View-Binding-Diagnose ergänzt `variable_flow` pro Quellenkandidat. Geprüft werden die Dateien der statischen Kette einschließlich Ziel-View und Ausgangsdatei. Erkannt werden direkte Variablen-/Array-Zuweisungen, zusammengesetzte Zuweisungen, direkte Property-Schreibzugriffe, `unset`, Bindung der Optionsvariable als foreach-/forelse-Variable sowie `@props`/`@aware` als zusätzliche Component-Kontexte. Bei öffentlichen Arrayquellen werden gleichnamige Datenbindungen in der PHP-Datei als mögliche Überdeckung gemeldet. Provider-Referenzen werden hier getrennt behandelt, weil deren Datenbindung gerade die deklarierte Quelle ist. Verdachtsfälle enthalten Dateipfad, Zeile und Grund. Blade-Kommentare werden unter Erhalt der Zeilennummern ignoriert.

Die Diagnose meldet `potential_override`, `no_detected_override` oder `unverified`. Sie ist absichtlich konservativ und keine vollständige PHP-/Blade-Datenflussanalyse: indirekte Mutation, Referenzübergaben, unbekannte Methoden, View-Composer, Vererbung und globale Laufzeiteinflüsse sind nicht nachgewiesen ausgeschlossen. Ebenso können nicht ausgewertete Codekontexte Verdachtsmeldungen erzeugen. Diese Status ändern die bisherige Kandidatenauswahl nicht und begründen keine automatische Freigabe.

Die erneute rein dateibasierte Untersuchung der sechs bekannten Verbindungen ergibt jeweils `no_detected_override`; die vier nicht erreichbaren Gegenstücke bleiben `unverified`. Der Bericht `option-view-bindings.json` enthält diese Ergebnisse. Keine Provider wurden aufgerufen und keine Produktionsdaten geändert. Neun Tests prüfen direkte Zugriffe ohne Mutation sowie die erkannten Schreib-/Scope-Fälle und kommentierten Code.


## Indirekte Optionsquellen und Review-Erhalt beim Runtime-Collector

Die sechs untersuchten View-Verbindungen wurden zusätzlich anhand ihrer tatsächlichen Methodenaufrufe geprüft. RoleList übergibt die Rückgaben von `IconRegistry::roleUserManagementBadgeColors/Variants`; diese lesen die Kategorie-Konfiguration und sortieren eine lokale Arraykopie. CreatePerson und EditPerson reichen ihre öffentlichen Optionsarrays an `RuntimeDynamicTranslationCollector::options` ohne Referenzparameter weiter. Dessen Normalisierung erzeugt einen eigenen Bestand. In den geprüften Klassen wurden keine Schreibzugriffe auf diese Optionsproperties gefunden. Der anwendungseigene RoleList-Trait `InteractsWithUserSettings` bearbeitet User-Settings, nicht diese Arrays. Die Suche in aktivem app/bootstrap/Package-src fand keine direkten `View::composer/creator/share`- beziehungsweise `view()->composer/creator/share`-Aufrufe; in den Views keine `wire:model`-Bindung auf die fünf Optionsarray-Namen. Das ist ein eingegrenzter Quelltextbefund, kein Ausschluss dynamischer Registrierungen oder sämtlicher Framework-/Hydration-Einflüsse. Die automatische Quellenfreigabe bleibt unverändert.

Dabei konkret gefunden: `RuntimeDynamicTranslationCollector::syncDynamicSourceCandidate` enthielt `review_status=pending` in den gemeinsamen Insert-/Update-Attributen. Ein erneuter Aufruf konnte damit einen bereits anders bewerteten Runtime-Kandidaten zurücksetzen. `pending` wird jetzt ausschließlich beim Insert vergeben. Wiederholte Beobachtungen aktualisieren weiter den beobachteten Wertebestand, erhalten aber den vorhandenen Review-Status. Dies entspricht dem bereits dokumentierten Vertrag der Klasse. Die separaten, vom SourceLinkWriter erzeugten Verknüpfungskandidaten haben andere Referenzen; es wird nicht behauptet, dass deren konkrete UI-Entscheidungen im geprüften Bestand bereits zurückgesetzt wurden.

Drei Regressionen prüfen initial `pending`, anschließende unveränderte und veränderte Beobachtungen mit `pending`, `confirmed` und `unlinked`, Erhalt der Kandidaten-ID, Aktualisierung der beobachteten Labels, Stale-Markierung verschwundener Optionen und unveränderte leere Übersetzungstabellen. Der Test läuft ausschließlich in der isolierten SQLite-Datenbank. Kein produktiver Collector, Scan oder Datenbankumbau wurde ausgeführt. Bestehende historische Review-Verluste werden dadurch nicht rekonstruiert. Die weitere Zusammenführung von Runtime- und Scan-Beobachtungen sowie die kontrollierte Umstellung der 25 historischen Optionsbeobachtungen bleiben offen.


## Konkreter Übergangsplan für historische Optionsbeobachtungen

`plan-option-transition.php` erfasst rein lesend alle 25 aktiven Optionsbeobachtungen, ihre direkten dynamischen Quellenverbindungen und aktuelle explizite Optionsaufrufe. Der Scanner arbeitet im Speicher; der Optionscollector und damit dessen Provider-Aufrufe werden nicht verwendet. PostgreSQL erzwingt eine `REPEATABLE READ READ ONLY`-Transaktion. Quellenhashes und Einzelbelege stehen in `option-transition-plan.json`, Auswertung und Umstellungsreihenfolge in `option-transition-plan.md`.

15 Beobachtungen besitzen einen eindeutigen aktuellen Aufruf mit exakt passendem aktivem Finding und Key, benötigen aber aktualisierten gespeicherten Optionskontext. Acht haben keinen unterstützten expliziten Aufruf mit denselben Variablen; zwei Pfade wurden nicht gescannt. Die neun Beobachtungen mit aktiven Quellen besitzen zusammen zwölf aktive direkte Verbindungen; alle zwölf verwenden andere Finding-IDs als die heutigen Aufrufe. Daraus folgt keine automatische Lösch- oder Umhängefreigabe. Die Herkunft des Optionspayloads und die heuristischen Consumer-Treffer bleiben gesondert zu prüfen.

Der Plan unterscheidet Metadatenaktualisierung, Quellenprüfung, Übernahme bestehender Beobachtungsidentitäten und abschließende Entfernung des historischen Matchings. Ein einfacher Rescan könnte wegen des zeilenabhängigen Beobachtungsfingerprints zusätzliche Beobachtungen erzeugen und alte weiterhin aktiv lassen. Genau dieser Übergang muss vor der produktiven Umstellung isoliert geprüft werden. PHP-Syntax und Berichtsinvarianten sind geprüft; die Produktionsdaten und Laufzeitlogik bleiben unverändert.


## Identitätserhalt bei verschobenen Optionsfundstellen

Der DynamicOptionSynchronizer prüft jetzt vor dem ersten Schreibzugriff die Identität sämtlicher eingehender Beobachtungen. Bleibt eine bereits als `matched` gespeicherte Foundation-Zuordnung aus Finding-ID und Key-ID erhalten, kann eine verschobene Zeile dieselbe aktive Beobachtung weiterverwenden. Zusätzlich müssen Quellpfad und Options-/Key-/Labelvariable übereinstimmen. Der zeilenabhängige Fingerprint wird aktualisiert; Beobachtungs-ID, first_seen_at und bestehende Metadaten bleiben erhalten. Der vorhandene Timeline-Recorder dokumentiert alte und neue Position samt Fingerprint. Ein identischer Folgelauf erzeugt keinen weiteren Änderungseintrag.

Findet sich ohne exakten Fingerprint eine historische Beobachtung ohne Foundation-Zuordnung oder mehr als ein möglicher Vorgänger, brechen Vorschau und Synchronisierung mit Pfad, Optionsvariable und vorhandenen IDs ab. Es wird keine neue aktive Beobachtung daneben angelegt und keine historische Verbindung geraten. Doppelte Eingaben sowie mehrere Eingaben mit Anspruch auf dieselbe gespeicherte Beobachtung werden ebenfalls vor dem Schreiben zurückgewiesen. Andere eindeutig zugeordnete Findings derselben Variablennamen werden nicht zusammengeführt.

Diese Absicherung ist keine vollständige Migration der 25 historischen Beobachtungen: Ein Dateiumzug oder eine neue Finding-/Key-Identität wird nicht automatisch als bloße Positionsänderung interpretiert. Das bisherige Verhalten bei einem exakt wiedergefundenen Fingerprint bleibt bestehen; historische Quellenverbindungen werden nicht umgehängt und das historische Consumer-Matching bleibt vorläufig aktiv. Ein produktiver Übergang benötigt weiterhin den zuvor beschriebenen Quellen- und Verbraucherabgleich.

Regressionen durchlaufen eine echte Scanner-/Foundation-Aktualisierung nach eingefügten Leerzeilen, prüfen unveränderte Quellen, Quellwerte, bestätigte Kandidaten und bearbeitete Übersetzungen sowie Idempotenz. Weitere Tests prüfen historische Identitätskonflikte in Vorschau und Apply, doppelte Eingaben und mehrere gespeicherte Vorgänger. Alle Tests verwenden die isolierte SQLite-Datenbank; kein produktiver Scan oder Datenumbau wurde ausgeführt.


## Herkunft der zwölf historischen aktiven Quellenverbindungen

Der Übergangsplan enthält zusätzlich die bisherigen Findings und einen Vergleich von Dateipfad, Key, Options-/Key-/Labelvariable, Labelrolle und Ausdruckstyp. Acht Quellen zeigen denselben Optionskontext bei geändertem Aufruf (`__($label)` gegenüber explizitem Option::label). Drei Quellen sind mit Beobachtungen einer anderen View verbunden (RoleList edit/create, Person edit/create). Eine Quelle betrifft den indexierten Familienstands-Anzeigetext, während die Beobachtung eine foreach-Auswahl beschreibt. Alle zwölf bisherigen Findings sind obsolete.

Die acht kontextgleichen Fälle sind Übergangskandidaten, keine schon freigegebene Migration. Die drei dateifremden Quellen und der indexierte Einzelaufruf dürfen nicht pauschal auf die aus der Beobachtung abgeleiteten Schleifen-Findings umgehängt werden. Details und Quellen-IDs stehen in `option-transition-plan.md` und `.json`. Die Produktionsdaten und Consumer-Regeln wurden nicht geändert. Der Bericht wurde erneut innerhalb der schreibgeschützten PostgreSQL-Transaktion erzeugt; Syntax und Berichtszählungen wurden geprüft.


## Payload- und UI-Abgleich der acht Übergangskandidaten

Der neue rein lesende Audit `inspect-option-payloads.php` vergleicht die acht kontextgleichen Kandidaten mit den aktuellen Property-Defaults beziehungsweise den zuvor geprüften Konfigurationswerten. Keine Component wird instanziiert und keine Provider-/Render-Methode aufgerufen. Alle acht Optionsbestände sind exakt gleich, auch gegenüber den aktiven DynamicSourceValues. Ergebnisse und en/de-Ausgaben des tatsächlichen UI-Resolvers stehen in `option-payload-review.json`.

Quelle 24 besitzt eine aktive bestätigte Referenz über Kandidat 78 zu Runtime-Quelle 40. Der UI-Resolver findet diese Verknüpfung über den gemeinsamen Gender-Key für beide heutigen Person-Findings. Eine Übernahme muss deshalb Quell-ID, Kandidaten-ID, Review und Optionssnapshot erhalten. Zudem enthält der Klassifizierungsfingerprint Finding-ID und Beobachtungszeile: Der Erhalt der Beobachtungs-ID allein verhindert bei erneuter Klassifizierung noch keine neue Quellenzeile. Der isolierte Übernahmetest muss folglich den anschließenden Klassifizierungslauf einschließen. Es erfolgte kein produktiver Apply; Syntax, Berichtszählungen und Gleichheit der Optionsmengen im Resolver wurden geprüft.


## Quellenidentität im anschließenden Klassifizierungslauf

`ClassifyDynamicValues` verwendet bei Optionsquellen neben dem exakten Fingerprint die Kombination aus Finding-ID, Key-ID, Optionsbeobachtungs-ID und Quellpfad zur Wiedererkennung. Runtime-Quellen sind ausgeschlossen. Bei genau einer bestehenden Quelle kann ein geänderter Positionsfingerprint aktualisiert werden, ohne Quellen-ID oder first_seen_at zu verlieren. Eine andere Finding-/Key-Identität wird dadurch ausdrücklich nicht automatisch übernommen. Mehrere passende Quellen oder ein widersprüchlicher Zielfingerprint führen zum Fehler statt zu einer geratenen Auswahl.

Der schreibende Klassifizierungslauf ist jetzt transaktional. Das vorzeitige pauschale Markieren der Codequellen als obsolete entfällt; weiterhin ungesehene Quellen werden erst am Ende des erfolgreichen Durchlaufs durch die vorhandene Abgleichsfunktion stillgelegt. Damit bleiben die tatsächlichen Ausgangszustände während des Abgleichs erhalten, und ein späterer Fehler rollt Quellen-, Werte-, Key-/Finding- und Timeline-Änderungen zurück. Die Vorschau bleibt ohne Schreibtransaktion; eine vollständige Konfliktsimulation des Schreibpfades wird damit nicht behauptet.

Der Test für eine echte Zeilenverschiebung durch Scanner und Foundation umfasst jetzt zwei anschließende Klassifizierungsläufe: gleiche Quellen-ID und Erstbeobachtung, aktualisierter Fingerprint, unveränderte Quellwert-IDs, bestätigte Kandidaten und bearbeitete Übersetzungen. Weitere Tests erzwingen mehrdeutige Quellenidentität sowie einen Datenbankfehler beim nachgelagerten Werteupdate und prüfen vollständigen Rollback. Kein produktiver Klassifizierungslauf wurde ausgeführt.

Diese Korrektur deckt Positionsänderungen und Wiederholung ab. Für die acht historischen Quellen mit tatsächlich neuer Finding-ID bleibt eine explizite, separat geprüfte Übernahme erforderlich. Der normale Klassifizierer darf diese nicht aus Text- oder Key-Gleichheit selbst ableiten.


## Explizite Quellenübernahme einschließlich Folgelauf

`TranslationWorkbenchOptionSourceTransition` implementiert eine ausdrücklich angeforderte, transaktionale Übernahme von alten auf aktuelle Findings. Es gibt keine automatische Registrierung in der Pipeline und keinen neuen produktiven Artisan-Writer. Jeder Auftrag benennt Quellen-ID, alte/neue Finding-ID, bestehenden Key, geprüfte Optionen und Datei-Hash. Der Dienst prüft diese Voraussetzungen erneut gegen Datenbank und aktuellen Scanner-Ausdruck. Alter und neuer Optionskontext müssen in Options-/Key-/Labelvariable, Labelrolle und Ausdruckstyp übereinstimmen; Dateipfade, aktiver expliziter Owner-Key und historische Beobachtung müssen passen. Fehlende/mehrdeutige Ziele, abweichende Optionen, Dateiänderungen und bereits besetzte Zielquellen führen zum Fehler.

Die Übernahme erhält Quellen-ID, Beobachtungs-ID, Quellwerte und Übersetzungen. Sie setzt die explizite Foundation-Zuordnung an der Beobachtung, löst deren alten matched_entry_id-Verweis und aktualisiert die Finding-Verweise der direkt referenzierenden related_dynamic_source-Kandidaten. Deren IDs, Reviews und Optionssnapshots bleiben unverändert. Der Timeline-Eintrag enthält alte/neue Quelleigenschaften, vorherige Foundation-Zuordnung, betroffene Kandidaten und geprüften Datei-Hash. Der nächste Klassifizierungslauf aktualisiert den Fingerprint derselben Quelle. Ein unveränderter erneuter Übernahmeauftrag meldet already_transitioned und erzeugt kein zweites Ereignis.

Sechs Szenarien prüfen erfolgreiche Übernahme mit nachfolgender Klassifizierung und Wiederholung, veränderten Datei-Hash, geänderten Optionsbestand, falschen Key, belegte Zielquelle sowie vollständigen Batch-Rollback. Die Test-Fixture enthält ausdrücklich den bereits geprüften gemeinsamen Key des historischen und heutigen Findings; sie behauptet keine automatische Key-Zusammenführung durch den Scanner. Gesamter Workbench-Testlauf: 336 bestanden, 2019 Assertions.

`preview-option-source-transition.php` führt ausschließlich die Vorschau in einer PostgreSQL-READ-ONLY-Transaktion aus. Es prüft Plan-/Payload-Hashes und Quelldeklarationen vorab. Ergebnis für den echten Bestand: achtmal ready; Beleg in `option-source-transition-preflight.json`. Kein produktiver Apply wurde ausgeführt.

Vor einem produktiven Apply bleibt die Wiedererfassung der Optionsquellen zu lösen: Der normale Collector behandelt weiterhin gleichnamige Arrays aus verschiedenen Components als ambiguous_source. Ein späterer discover-dynamic-options-Lauf könnte eine übernommene Beobachtung dadurch wieder auf needs_review setzen und ihren aktiven Optionsbestand leeren. Die isoliert geprüfte Übernahme plus Klassifizierung alleine ist daher noch keine Freigabe der gesamten regulären Pipeline. Diese Grenze wird nicht durch eine Sonderbehandlung bestimmter Datensatz-IDs verdeckt.


## Component-bezogene Auswahl gleichnamiger Optionsarrays

Der Collector kann nun einen Kandidaten über seine statische Component-/View-Verbindung auswählen (`view_bound_candidate`). Voraussetzungen: genau ein erreichbarer Kandidat, vollständig verfolgte unterstützte return-view/include-Beziehungen bei allen Kandidaten und keine erkannte Variablenüberdeckung auf den erreichbaren Pfaden. Fehlende Views, dynamische oder nicht unterstützte Includes, mehrere erreichbare Quellen und erkannte Überschreibungen bleiben mehrdeutig. Sämtliche Kandidaten und ihre Diagnosen bleiben im Report erhalten; nur source_reference und der verwendete Optionsbestand zeigen auf die ausgewählte Quelle. Es gibt keine Datensatz-ID-Ausnahmen und keine Gleichsetzung nur anhand identischer Texte.

Die View-Diagnose prüft die Existenz auch der Ziel-View. Alternative Include-Pfade werden einzeln verfolgt, damit eine Überdeckung in einem zweiten Pfad nicht durch ein globales visited-Set verloren geht. Zyklen beziehungsweise Überschreiten der Traversierungsgrenze verhindern die Auswahl. Diese Regeln betreffen die unterstützten literalen return-view/include-Verbindungen; sie sind weiterhin keine vollständige Analyse von Framework-Hydration, beliebigen Component-Aufrufen, dynamischer PHP-Ausführung oder View-Composern. Die zuvor manuell geprüften aktiven Quellen bleiben der belegte Einsatzbereich.

Fünf Szenarien prüfen eindeutige Verbindung, zwei erreichbare Quellen, dynamisches Include, fehlende View und Variablenüberdeckung. Der eindeutige Fall durchläuft zusätzlich Foundation-Speicherung, wiederholte Discovery und zwei Klassifizierungen mit stabiler Quellen-ID und unveränderten Werten. Ein weiterer Test prüft eine Überdeckung auf dem zweiten Include-Pfad zu derselben Ziel-View.

Der dateibasierte Replay `inspect-option-source-selection.php` führt den tatsächlichen Arrayparser und die Discovery aus; für die zwei zuvor geprüften RoleList-Reader werden die Konfigurationswerte bereitgestellt, ohne Provider auszuführen. Ergebnis über die betroffenen Views: elf view_bound_candidate-Beobachtungen und neun unresolved. Die zusätzlichen Beobachtungen umfassen auch indexierte Anzeigen; neun unresolved-Werte sind keine nachgewiesenen leeren Optionsbestände. Der Replay ist kein vollständiger produktiver Providerlauf. Beleg: `option-source-selection.json`. Produktionsdaten bleiben unverändert.

## Statische Konstantenschlüssel und verbleibende Laufzeitoptionen

Die neun zunächst ungelösten Beobachtungen wurden nach Quellenart geprüft. Drei betreffen feste Notfallkontakt-Beziehungen mit `PersonContact::RELATIONSHIP_*` als Array-Schlüssel. Sechs betreffen Dokumenttypen beziehungsweise Dokumentkategorien, deren Optionen aus Laufzeit-/Datenbankwerten aufgebaut werden. Diese sechs sind weiterhin ungelöst und dürfen nicht als verbindlich leere Optionsbestände behandelt werden.

`Scanner/StaticOptionArray` ersetzt die einzelne Schlüssel-/Wertpaare herausgreifende Regex durch eine eingeschränkte Token-Auswertung des vollständigen Arrays. Unterstützt werden literale String-Schlüssel und öffentliche String-Konstanten aus explizit importierten oder vollqualifizierten App-Klassen. Die Quelldateien werden ausschließlich gelesen; weder Autoload noch Provider- oder Anwendungscode werden dafür ausgeführt. Nicht unterstützte Ausdrücke, dynamische Konstanten und gemischte Arrays mit unbekannten Einträgen ergeben keinen vermeintlich vollständigen Teilbestand. PHP-Kommentare werden ignoriert; Escape-Zeichen werden unter Wahrung der unterstützten PHP-String-Semantik übernommen. Nicht unterstützte Unicode-Escapes bleiben ungeklärt.

Der Collector berücksichtigt zudem Einträge auf der Öffnungszeile einer mehrzeiligen Array-Deklaration. Parser-Regressionen prüfen Konstanten, Aliase, unbekannte/private/dynamische Schlüssel, gemischte Arrays, Kommentare und Escapes. Ein Collector-Test prüft die vollständige Erfassung und identische Wiederholung mit einer Quelldatei, die bei tatsächlicher Ausführung sofort eine Exception werfen würde.

Der erneut ausgeführte dateibasierte Replay ergibt **14 view_bound_candidate und 6 unresolved**. Der Report `option-source-selection.json` enthält den aktualisierten Stand. Die Produktionsdaten wurden nicht verändert; ein vollständiger produktiver Providerlauf wurde weiterhin nicht ausgeführt. Als nächstes müssen die sechs Laufzeitbeobachtungen und die historischen, nicht kontextgleichen Quellenverbindungen getrennt geklärt werden, bevor die reguläre Pipeline für eine Bestandsübernahme freigegeben werden kann.

## Unbekannte Optionsbestände dürfen keine Quellwerte löschen

Die sechs offenen Beobachtungen betreffen drei Laufzeitquellen: Dokumenttypen in CreatePerson und EditPerson sowie Dokumentkategorien in EditPerson. `PersonDocumentType::options()` liest aktive Datenbankeinträge und verwendet bei fehlender Tabelle beziehungsweise leerem Ergebnis die definierten Defaults. EditPerson kombiniert für Kategorien die Konstantenliste mit vorhandenen Datenbankkategorien. Beide Components melden ihre tatsächlich aufgebauten Optionslisten über `RuntimeDynamicTranslationCollector::options()` mit `runtime_db_options`. Die statische Discovery soll hierfür keine Component instanziieren oder einen vollständigen Datenbankbestand vortäuschen. Eine statische Fundstellenzuordnung und ein geprüfter Laufzeitbestand bleiben unterschiedliche Nachweise.

Bei der Prüfung wurde eine konkrete Löschlücke im Klassifizierer gefunden: Ein nicht ermittelter Optionsbestand (`options = []`, Kardinalität unbekannt) führte bisher zum Löschen aller gespeicherten Quellwerte derselben Quelle. Der Werteabgleich überspringt jetzt solche Bestände und zählt die erhaltenen Zeilen als `source_values_preserved`; die Kommandoausgabe nennt diese Anzahl. Die Quelle bleibt `needs_review`, der aktuelle beobachtete Umfang ist weiterhin unbekannt. Vorhandene Werte werden als letzter bekannter Bestand erhalten, nicht als neu ermittelte Daten ausgegeben. Die bestehende UI-Fallback-/Resolverlogik bleibt unverändert; dies ist keine automatische Entwertung bereits bearbeiteter Übersetzungen.

Auch eine eindeutig zu einem Finding gehörende Beobachtung bleibt bei unbekannten Optionen `needs_review`. Die Zuordnung selbst bleibt `matched`, damit ihre Identität nicht verloren geht. Vor dem Ersetzen eines vorhandenen Optionspayloads durch einen ungeklärten Bestand speichert die Beobachtung den letzten Payload samt Herkunft in `meta.options_before_unresolved`; der vorhandene Timeline-Recorder dokumentiert die Änderung. Wiederholte ungeklärte Läufe überschreiben diesen Snapshot nicht mit einem leeren Bestand. Ein späterer belegter, nichtleerer Bestand setzt den normalen Werteabgleich fort. Der gegenwärtige Discovery-Vertrag kann keinen autoritativ leeren Gesamtbestand ausdrücken; eine automatische vollständige Löschung ist deshalb hier bewusst nicht vorgesehen.

Die Regression durchläuft bekannten Bestand → zweimal ungeklärte Erfassung und Klassifizierung → erneut bekannten, geänderten Bestand. Sie prüft Quellenidentität, erhaltene Wertzeilen und Snapshot, Review-Status und Kommandoausgabe, anschließend den regulären Werteabgleich sowie unveränderte bestätigte Kandidaten und bearbeitete Übersetzungen. Produktive Daten wurden nicht geschrieben. Die vier historisch nicht kontextgleichen Quellenverbindungen bleiben ausdrücklich außerhalb einer automatischen Übernahme.

## Vier abweichende historische Quellen: heutige Ziele belegt

`inspect-option-detached-sources.php` prüft die im Übergangsplan ausgewiesenen dateifremden beziehungsweise indexierten Quellen erneut gegen den echten Bestand. Die Abfrage läuft ausschließlich in einer PostgreSQL-Transaktion `REPEATABLE READ READ ONLY`. Gescannt wird die eigene Fundstellendatei der historischen Quelle, nicht pauschal die Datei ihrer alten Optionsbeobachtung. Der aktuelle Aufruf muss denselben Options-/Variablenkontext tragen und genau ein aktives Finding mit demselben aktiven Owner-Key besitzen. Für den historischen indexierten Familienstandsaufruf wird der Kontext aus einer eng begrenzten Literalform des Ausdrucks gelesen; es wird kein PHP evaluiert.

| Quelle | Heutiges Finding | Bisherige Beobachtung | Passende Beobachtung | Befund |
| --- | --- | --- | --- | --- |
| 2 | 9271, RoleList edit, Badge-Variante | 1, RoleList create | 3, RoleList edit | Vier identische Optionen; kein bestehender aktiver Zielquelleneintrag |
| 25 | 9253, EditPerson, Anrede | 6, CreatePerson | 16, EditPerson | Drei identische Optionen; kein bestehender aktiver Zielquelleneintrag |
| 23 | 9252, EditPerson, Geschlecht | 7, CreatePerson | 15, EditPerson | Vier identische Optionen; bestätigter Kandidat 77 verweist auf diese Quelle |
| 62 | 9255, EditPerson, angezeigter Familienstand | 17, foreach-Auswahl | Noch keine für den indexierten Aufruf | Sieben identische Optionen; Anzeige-Finding nicht mit Auswahl-Finding 9254 zusammenführen |

Alle vier aktuellen Payloads sind über die statische Component-/View-Verbindung zugeordnet und stimmen exakt mit den aktiven Quellwerten überein. Die zwei geprüften RoleList-Konfigurationsreader werden weiterhin ohne Provider-Aufruf aus der Konfiguration gelesen. Die tatsächlichen en/de-Ausgaben des UI-Resolvers, Runtime-Verbindungen, Quellenhashes und Kandidatenreferenzen sind in `option-detached-source-review.json` festgehalten. Der Resolver liefert jeweils die erwartete Optionsanzahl. Die Übersetzungszeilen pro gemeinsamem Key betragen 8, 6, 8 und 14; dies ist kein exklusiver Besitz der jeweiligen Fundstelle.

Wichtig: Dies ist eine belegte Zielsuche, noch keine Schreibfreigabe oder produktive Umstellung. Bei den ersten drei Quellen muss die Übernahme ausdrücklich auch die Beobachtungs-ID ändern; die bisherige Create-Beobachtung bleibt für ihre eigenen Verbraucher bestehen. Quelle 62 benötigt eine eigene indexierte Beobachtung. Der bisherige Übergangsdienst für acht kontextgleiche Quellen verlangt dagegen dieselbe Beobachtungsidentität und denselben Optionskontext. Diese Schranke darf nicht bloß entfernt werden. Nötig ist ein explizit geprüfter Übernahmeweg mit alter/neuer Beobachtung, Erhalt der Quell-ID und der bestätigten Kandidaten, anschließendem regulärem Discovery-/Klassifizierungslauf und Wiederholungsprüfung. Insbesondere Kandidat 77 muss mit Review und Optionssnapshot bestehen bleiben.

Prüfung: PHP-Syntax, vier eindeutige Ziele, gleiche Owner-Keys und Optionswerte sowie vollständige UI-Resolver-Zeilen wurden kontrolliert. Keine produktiven Findings, Beobachtungen, Quellen, Übersetzungen oder Kandidaten wurden geändert. In diesem Schritt wurden ausschließlich das Audit-Skript und seine Dokumentation/Ergebnisdatei ergänzt.

## Expliziter Wechsel der Beobachtungszuordnung

`TranslationWorkbenchOptionSourceTransition` unterstützt nun zusätzlich ausdrücklich benannte `old_observation_id` und `target_observation_id`. Beide müssen gemeinsam angegeben werden und verschieden sein. Die Zielbeobachtung muss bereits existieren und über die reguläre Foundation-Zuordnung exakt dem aktiven Ziel-Finding und dessen bestehendem Key zugeordnet sein. Eine fehlende Beobachtung wird im Übernahmedienst nicht geraten oder nebenbei erstellt: Für die indexierte Anzeige wird sie zuerst regulär erfasst; historische Zielbeobachtungen werden zuvor kontrolliert aktualisiert.

Geprüft werden alte und neue Beobachtungsidentität, eigener Quellpfad, unveränderter Datei-Hash, eindeutiger aktueller Aufruf, Owner-Key, Options-/Variablenkontext und identischer geprüfter Optionsbestand in Quelle und beiden Beobachtungen. Nur für den ausdrücklich angeforderten Beobachtungswechsel kann der alte indexierte Kontext aus der eng begrenzten Form `__($options[$key] ?? $fallback)` rekonstruiert werden. Allgemeine Ausdrücke oder wechselnde Keys werden nicht akzeptiert. Ein bereits belegtes Ziel oder ein nur teilweise vollzogener Finding-/Beobachtungswechsel wird zurückgewiesen.

Beim Apply bleibt die DynamicSource-ID bestehen; Finding-ID und Beobachtungs-ID werden zusammen geändert. Direkt referenzierende Kandidaten erhalten das neue Finding, behalten aber Review und Optionssnapshot. Die vorherige Beobachtung bleibt vollständig unverändert, weil andere Aufrufe sie weiter verwenden können. Das Timeline-Ereignis enthält jetzt auch alte und neue Beobachtungs-ID. Wiederholung prüft erneut die Voraussetzungen und erzeugt kein weiteres Übernahmeereignis.

Der Integrationstest deckte zusätzlich ein Problem im regulären Folgelauf auf: Historische Beobachtungen ohne Foundation-Zuordnung wurden neben einer inzwischen exakt zugeordneten Beobachtung erneut als Treffer herangezogen. Der Klassifizierer bevorzugt deshalb für ein Finding die bereits geprüften Foundation-Zuordnungen. Nur wenn keine solche Zuordnung vorhanden ist, bleibt das bisherige historische Matching als Übergangsverhalten bestehen. Es wird keine historische Beobachtung gelöscht oder global stillgelegt.

Sieben Szenarien prüfen fremde View, indexierte Anzeige, unzugeordnetes Ziel, veränderte Optionen, partiellen Übergang, widersprüchlichen Variablenkontext und Batch-Rollback. Die beiden Erfolgsfälle führen anschließend zweimal die tatsächliche Options-Discovery, Synchronisierung und Klassifizierung aus. Quell-ID, Quellwerte, Übersetzungen, bestätigte Kandidaten und die alte Beobachtung bleiben erhalten. Dies ist weiterhin ausschließlich in der isolierten Testdatenbank ausgeführt; die produktiven vier Verbindungen wurden nicht umgehängt. Vor einem produktiven Übergang müssen die drei bestehenden Zielbeobachtungen kontrolliert auf ihre aktuellen Findings aktualisiert und die eine indexierte Beobachtung regulär angelegt werden.

## Zielbeobachtungen vorbereiten: geprüfter Dienst und produktive Vorschau

Der interne Dienst `TranslationWorkbenchOptionObservationTransition` bereitet explizit geprüfte Zielbeobachtungen vor. Er wird nicht automatisch in die Pipeline aufgenommen. Ein Auftrag benennt bestehende Beobachtungs-ID oder ausdrücklich null für eine neue Beobachtung, aktives Ziel-Finding, unveränderten Key, Datei-Hash und die geprüfte Discovery. Der Dienst liest den aktuellen Aufruf erneut mit dem Scanner, prüft dessen Eindeutigkeit, Owner-Key, Quellpfad, Zeilenbereich, Ausdruckstyp und Variablenkontext. Er verlangt einen aufgelösten, nichtleeren Payload. Bei bestehenden Beobachtungen müssen Identität und Optionswerte passen; fremde Zuordnungen werden zurückgewiesen.

Bei Apply wird ausschließlich der aktuelle `dynamic_option_context` des geprüften Findings ergänzt beziehungsweise aktualisiert. Andere Finding-Metadaten, Keys und Key-Verbindungen werden nicht umgebaut. Eine historische Beobachtung erhält zunächst ihre explizite Foundation-Zuordnung, damit der reguläre Synchronizer anschließend dieselbe ID auch bei geänderter Zeile wiederverwendet. Der eigentliche Abgleich und das Anlegen einer neuen Beobachtung erfolgen durch diesen vorhandenen Synchronizer. Kontextaktualisierung, Vorbereitung und Synchronisierung werden über Timeline-Ereignisse dokumentiert. Der gesamte Batch ist transaktional; Konflikte führen zum Rollback. Ein unveränderter Wiederholungslauf erzeugt keine zusätzlichen Änderungseinträge.

Die Integrationstests beginnen nun vor der Vorbereitung: einmal mit einer historischen Beobachtung ohne aktuellen Kontext und abweichender Zeile, einmal ohne Beobachtung für den indexierten Aufruf. Sie führen Vorbereitung, Quellenübernahme und zweimal die reguläre Discovery/Synchronisierung/Klassifizierung zusammenhängend aus. Zusätzliche Fälle prüfen geänderten Datei-Hash, falschen Key, abweichende Optionen, widersprüchliche Variablen und Batch-Rollback. Insgesamt zwölf Übergangsszenarien; vollständige Workbench-Suite: **368 Tests bestanden, 2188 Assertions**. PHP-Syntax und Git-Whitespace-Prüfung sind erfolgreich.

`preview-option-observation-transition.php` führt ausschließlich die Vorschau aus und besitzt keine Apply-Option. Sie verwendet den tatsächlichen statischen Collector-Teil und die zwei zuvor geprüften RoleList-Konfigurationsreader, ohne Provider auszuführen. Die aktuellen Dateien werden gegen die Hashes des Quellen-Audits geprüft; der Dienst läuft in einer PostgreSQL-Transaktion `REPEATABLE READ READ ONLY`. Ergebnis im echten Bestand: **3 ready_to_refresh**, **1 ready_to_create**. Die bestehenden IDs sind 3, 16 und 15; neu wäre die indexierte Anzeige für Finding 9255. Ergebnis und konkrete Aufträge stehen in `option-observation-transition-preflight.json`.

Dies ist eine erfolgreiche Vorbereitungsvorschau, keine bereits ausgeführte Migration. Produktive Findings, Beobachtungen, Quellen, Übersetzungen und Kandidaten blieben unverändert. Für einen produktiven Durchlauf sind Vorbereitung und Quellenübernahme gemeinsam transaktional auszuführen; ein vorbereitender Klassifizierungslauf zwischen diesen Schritten könnte bereits Zielquellen anlegen und muss vermieden werden. Der anschließende reguläre Folgelauf ist in den isolierten Tests abgesichert, aber noch nicht auf dem produktiven Gesamtbestand ausgeführt.

## Vier abweichende Quellen produktiv übernommen und nachgeprüft

Am 15.10.2026 wurde `apply-detached-option-transition.php` zuerst als zurückgerollte Transaktionsprobe und danach mit `--apply` ausgeführt. Die Probe und der Apply verwenden eine SERIALIZABLE-Transaktion mit Advisory Lock, sichern den Ausgangsbestand im privaten Storage und führen Vorbereitung, Quellenübernahme und zwei begrenzte Folgeläufe gemeinsam aus. Jeder unerwartete Schreibzugriff auf andere Zeilen/Felder oder eine abweichende UI-Ausgabe führt zum Rollback. Die Sicherungsdatei liegt unter `storage/app/private`; ihr konkreter Pfad steht im lokalen Apply-Bericht.

| Quelle (unveränderte ID) | Aktuelles Finding | Beobachtung nach Übernahme |
| --- | --- | --- |
| 2 | 9271 | 3 (bestehend) |
| 25 | 9253 | 16 (bestehend) |
| 23 | 9252 | 15 (bestehend) |
| 62 | 9255 | 27 (neu, indexierte Anzeige) |

Die drei bestehenden Beobachtungen behalten ihre Erstbeobachtung. Die alte Familienstands-Auswahlbeobachtung 17 bleibt bestehen; Anzeige und Auswahl sind jetzt getrennt zugeordnet. Die frühere Probe hatte eine ungespeicherte Beobachtungs-ID 26 belegt; PostgreSQL-Sequenzen werden bei einem Rollback nicht zurückgesetzt. ID 27 ist die tatsächlich gespeicherte neue Beobachtung.

Geändert wurden ausschließlich die Metadaten der vier Ziel-Findings, die drei Zielbeobachtungen plus eine neue, die vier bestehenden Quellen und der Finding-Verweis samt updated_at des bestätigten Kandidaten 77. Der vorhandene Timeline-Recorder dokumentiert Vorbereitung, Übernahme und Klassifizierungsänderungen. Der Übergangsdienst erkennt die Wiederholung als already_transitioned.

Die Folgeläufe verwenden die tatsächlichen Auswahl-, Quellen- und Werteabgleichsmethoden von `ClassifyDynamicValues`, beschränkt auf die vier geprüften Findings. Es wurde ausdrücklich **kein globaler Artisan-Klassifizierungslauf**, kein globales obsoleteUnseenSources und kein Providerlauf ausgeführt. Der vollständige reguläre Befehlslauf ist weiterhin in den isolierten Integrationstests abgedeckt; die produktive Begrenzung vermeidet Änderungen an noch ungeklärten historischen Verbindungen.

`verify-detached-option-transition.php` prüft nach dem Commit unabhängig in einer PostgreSQL-READ-ONLY-Transaktion: alle vier resultierenden Zuordnungen und aktiven Quellen, unveränderte first_seen_at-Werte sowie unveränderten Kandidaten-Review und Optionssnapshot. Ganze Tabellen für DynamicSourceValues, DynamicKeyValues, LangValues, Reviews, Keys und KeyFindings sind exakt gleich zur Sicherung. Die tatsächlichen en/de-Resolver-Ausgaben wurden bereits innerhalb der schreibenden Transaktion vor/nach dem Übergang auf Gleichheit geprüft.

Belege: `option-detached-transition-rehearsal.json`, `option-detached-transition-applied.json` und `option-detached-transition-verified.json`. PHP-Syntax und Whitespace-Prüfung sind erfolgreich. Die Anwendungsdienste wurden in diesem Schritt nicht verändert; ihr letzter vollständiger isolierter Testlauf umfasst 368 bestandene Tests und 2188 Assertions. Die acht zuvor kontextgleich geprüften Quellen wurden bei diesem vierteiligen Übergang nicht umgehängt und bleiben ein eigener noch ausstehender Übernahmeschritt.

## Fünf weitere Quellen übernommen; drei historische Doppelverbindungen erkannt

Die erneute Vorschau für die acht zuvor geprüften Quellen brach korrekt mit `Option source 3: target source already exists` ab. Durch die Übernahme der vier abweichenden Quellen sind drei vormals freie Ziele inzwischen belegt. Die alte Acht-Quellen-Vorschau ist damit keine aktuelle Ausführungsfreigabe mehr. Der neue schreibgeschützte Abgleich `plan-remaining-option-transition.php` trennt fünf freie Ziele von drei belegten Zielen und prüft die aktuellen statischen Payloads erneut.

| Historische Quelle | Bereits aktive Quelle am heutigen Ziel | Aktuelles Finding | Befund |
| --- | --- | --- | --- |
| 3 | 2 | 9271 | Beide führen auf Beobachtung 3; identische aktive Quellwerte |
| 24 | 23 | 9252 | Beide führen auf Beobachtung 15; identische Werte, Kandidat 78 bestätigt die historische Quelle 24 |
| 26 | 25 | 9253 | Beide führen auf Beobachtung 16; identische aktive Quellwerte |

Diese drei Quellen dürfen nicht zusätzlich auf dasselbe Ziel umgehängt werden. Die bestätigte Verknüpfung 78 verweist auf Runtime-Quelle 40; für die bereits übernommene Quelle 23 existiert zusätzlich Kandidat 77. Die getrennten Review-/Herkunftsbelege sind bei einer späteren Konsolidierung zu erhalten. In diesem Schritt wurden weder diese drei Quellen noch ihre Kandidaten geändert, gelöscht oder stillgelegt.

Die fünf freien Ziele wurden mit `apply-remaining-option-transition.php` zunächst als zurückgerollte Transaktionsprobe und anschließend verbindlich übernommen. Die gleichen Sicherungs-, SERIALIZABLE-Transaktions-, Zeilen-/Feldprüfungen und UI-Vergleiche wie beim vorherigen Übergang gelten. Die Folgeläufe verwenden die regulären Auswahl-/Schreibmethoden nur für die fünf geprüften Findings; keine globale Stilllegung und kein Providerlauf.

| Quelle, ID erhalten | Aktuelles Finding | Beobachtung, ID erhalten |
| --- | --- | --- |
| 73 | 9269 | 1 |
| 1 | 9270 | 2 |
| 76 | 9249 | 6 |
| 77 | 9250 | 7 |
| 5 | 9251 | 8 |

`verify-remaining-option-transition.php` bestätigt nach dem Commit unabhängig und schreibgeschützt alle fünf aktiven Zuordnungen sowie unveränderte Erstbeobachtungen. Die Tabellen für Quellwerte, bearbeitete dynamische Übersetzungen, LangValues, Reviews, Keys und KeyFindings stimmen vollständig mit der Sicherung überein. Die en/de-Resolver-Ausgaben und sämtliche nicht freigegebenen Zeilen wurden innerhalb der schreibenden Transaktion verglichen. Es wurden keine Kandidaten geändert.

Belege: `remaining-option-transition-plan.json`, `option-remaining-transition-rehearsal.json`, `option-remaining-transition-applied.json`, `option-remaining-transition-verified.json`. Der konkrete Sicherungspfad steht im Apply-Bericht und liegt im privaten Storage. Anwendungsdienste wurden nicht verändert; Syntax-/Whitespace-Prüfungen und die tatsächliche Transaktionsprobe/Nachprüfung dienen der Verifikation dieses Migrationsschritts. Insgesamt sind jetzt neun der zwölf historischen Quellen auf aktuelle Findings überführt. Offen sind drei belegte Doppelverbindungen, keine weiteren einfachen Finding-Übernahmen. Deren Konsolidierung muss den bestehenden kanonischen Quellen folgen und Review-/Herkunftsdaten erhalten, statt neue parallele Quellen anzulegen.

## Drei historische Doppelquellen konsolidiert

`TranslationWorkbenchOptionSourceConsolidation` stellt einen expliziten, transaktionalen Konsolidierungsweg bereit. Er verlangt eine aktive kanonische Quelle mit exakt zugeordnetem aktivem Finding und Key, dieselbe Beobachtung und denselben Quellpfad/-typ sowie identische geprüfte aktive Optionswerte. Das alte Finding muss obsolete sein. Datei-Hash, Beobachtungspayload und sämtliche Kandidatenreferenzen werden erneut geprüft. Abweichungen und unbekannte Referenzen führen zum Abbruch. Es gibt weder eine automatische Auswahl anhand gleicher Texte noch eine Registrierung dieses Dienstes in der normalen Pipeline.

Historische Doppelquellen werden nicht gelöscht. Sie erhalten `status=obsolete` und `meta.superseded_by_source_id`; ihre IDs, Findings, Quellwerte und übrigen Herkunftsdaten bleiben bestehen. Eine bestätigte doppelte Kandidatenverknüpfung darf nur dann historisch werden, wenn eine ausdrücklich benannte aktive bestätigte Ersatzverknüpfung zur selben Runtime-Quelle mit gleichem Key und identischem Optionssnapshot besteht. Review, Kandidaten-ID und Snapshot bleiben erhalten; `meta.superseded_by_candidate_id` dokumentiert den Ersatz. Das zentrale Timeline-Protokoll speichert alte Zustände einschließlich Kandidaten und die konkreten Ersatzverweise. Wiederholung erzeugt keine weitere Änderung.

Nach erfolgreicher isolierter Prüfung sowie einer vollständig zurückgerollten SERIALIZABLE-Transaktionsprobe wurde am 15.10.2026 verbindlich konsolidiert:

| Historische Quelle | Weiterverwendete aktive Quelle | Kandidaten |
| --- | --- | --- |
| 3 | 2 | Keine zugehörige Kandidatenverknüpfung |
| 24 | 23 | Kandidat 78 historisch, Bestätigung/Snapshot erhalten; ersetzt durch aktiven bestätigten Kandidaten 77 |
| 26 | 25 | Keine zugehörige Kandidatenverknüpfung |

`consolidate-option-duplicates.php` legt vor Änderungen eine private Sicherung an und prüft alle Tabellenzeilen. Erlaubt sind ausschließlich Status, die zusätzlichen Ersatzverweise in den Metadaten und updated_at der drei historischen Quellen sowie des Kandidaten 78. Die aktiven Quellen 2, 23, 25 und Kandidat 77 bleiben vollständig unverändert. Alle Quellwert-, Übersetzungs-, Review-, Finding-, Key-, Beobachtungs- und Key-Verbindungszeilen bleiben erhalten und unverändert. Die tatsächlichen en/de-Ausgaben des UI-Resolvers werden vor/nach verglichen. Nach dem Commit bestätigt `--verify` unabhängig in einer PostgreSQL-READ-ONLY-Transaktion diese Eigenschaften gegen die Sicherung.

Regressionen prüfen erfolgreiche Konsolidierung, Wiederholung, anschließenden regulären Klassifizierungslauf ohne Wiedererzeugung der Doppelquelle, abweichende Werte, unbestätigte Ersatzverknüpfung, fehlende Kandidatenreferenzen und vollständigen Batch-Rollback. Vollständige Workbench-Suite: **373 Tests bestanden, 2334 Assertions**. Belege: `option-duplicate-consolidation-rehearsal.json`, `option-duplicate-consolidation-applied.json` und `option-duplicate-consolidation-verified.json`. Der Sicherungspfad steht im Apply-Bericht.

Damit sind die zwölf zuvor inventarisierten historischen Quellenverbindungen behandelt: neun behalten ihre IDs und sind auf aktuelle Findings überführt; drei sind als historische Duplikate mit explizitem Ersatzverweis erhalten. Dies schließt diesen Quellenübergang ab, nicht die gesamte Workbench-Konsolidierung. Die noch nicht unterstützten beziehungsweise außerhalb des Scanumfangs liegenden Optionsbeobachtungen und das verbleibende historische Matching ohne Foundation-Zuordnung müssen weiterhin gesondert geprüft werden. Ein globaler produktiver Scan-/Provider-/Klassifizierungslauf wurde auch in diesem Schritt nicht gestartet.

## Verbleibende historische Beobachtungen und Schutz der Quellenauflösung

Der erneute schreibgeschützte Bestandsabgleich zählt 26 Beobachtungen: neun mit Foundation-Zuordnung und 17 historische ohne Zuordnung. Keine dieser 17 hat direkt verknüpfte Quellen mit Status `active`; dies bedeutet jedoch nicht, dass sie unbenutzt sind. Quellen mit `needs_review` und historische heuristische Treffer werden dadurch nicht ausgeschlossen.

`inspect-remaining-option-consumers.php` untersucht deshalb die tatsächlichen Auswahlmethoden von `ClassifyDynamicValues` und `ResolveUnknownDynamicSources` in einer PostgreSQL-READ-ONLY-Transaktion. Die statische Collector-Prüfung führt keine Provider aus. Sechs Beobachtungen werden weiterhin durch den Klassifizierer ausgewählt. 15 werden statisch noch erkannt, zwei gehören zur ausgeschlossenen Old Workbench. Zu unterscheiden sind echte Optionsquellen, bereits übersetzte lokale Archive-Filter und sechs historische Tooltip-Beobachtungen, deren heutige Schleife nur bereits aufbereitete Informationen ausgibt. Diese Fälle werden nicht pauschal gelöscht oder zu neuen Übersetzungsquellen erklärt.

Dabei wurde ein konkreter Fehler im Resolver behoben: `unknownSources()` verlangt jetzt ein aktives Finding und eine aktive Quelldatei. Veraltete oder fehlende Fundstellen dürfen keine automatische Wiederauflösung auslösen. `bestDiscoveryForSource()` bevorzugt innerhalb der zulässigen Treffer explizit passende Foundation-Zuordnungen vor historischen Heuristiktreffern, auch wenn die exakt zugeordnete Beobachtung noch keine auflösbaren Werte enthält. Ein historischer Treffer mit höherem Score darf dort keine fremden Werte einsetzen.

Die lesende Vorher-/Nachher-Prüfung reduziert die Resolver-Treffer von 29 Quellen an acht Beobachtungen auf vier Quellen an zwei Beobachtungen: Beobachtung 11 mit Quellen 52/54 und Beobachtung 12 mit Quellen 11/12. Diese vier Treffer sind verbleibender Prüfbedarf, keine Freigabe für automatische Übernahme. Der Klassifizierer bleibt bei sechs beobachtungsbezogenen Treffern. Belege: `remaining-option-consumers-before-resolver-guard.json` und `remaining-option-consumers.json`.

Regressionen prüfen obsolete Findings, obsolete Quelldateien, fehlende Findings und den Vorrang einer exakten, noch ungelösten Foundation-Zuordnung vor einem historischen Treffer mit abweichenden Werten. In diesem Schritt wurde ausschließlich Anwendungscode abgesichert und der Bestand lesend untersucht; keine produktiven Quellen, Werte, Reviews oder Übersetzungen wurden geändert.

Vollständige isolierte Workbench-Suite nach der Resolver-Korrektur: **377 Tests bestanden, 2348 Assertions**. PHP-Syntax und `git diff --check` sind ebenfalls erfolgreich. Nächster fachlicher Schritt bleibt die explizite Trennung der aktiven Optionsquellen von reinen Anzeige-Schleifen und ausgeschlossenen historischen Beobachtungen; ein globaler produktiver Neuabgleich wurde nicht gestartet.

## Rollenprüfung der 17 Beobachtungen und mehrdeutige Key-Verknüpfung

Der erweiterte READ-ONLY-Abgleich vergleicht die heutigen statisch erkannten Optionsschleifen mit den aktuellen Übersetzungsaufrufen (Variablenkontext, expliziter Owner, Ausdruck, aktive Findings und aktive Keys). Er führt keine Provider aus. `remaining-option-consumers-before-role-review.json` bewahrt den vorherigen Bericht. Die neue Fassung `remaining-option-consumers.json` unterscheidet Kandidatenauswahl von der tatsächlichen Befehlsausführung und weist blockierende Vorprüfungen gesondert aus.

| Beobachtungen | Heutige Rolle | Weiteres Vorgehen |
| --- | --- | --- |
| 4, 9, 13 | Dokumenttypen; drei explizite Workbench-Aufrufe, gemeinsamer Key 3039; statisch ungelöste Wertequelle | Runtime-/DB-Optionsquelle prüfen und jeweils exakt zuordnen; keine Werte aus dem Variablennamen ableiten |
| 10 | Dokumentkategorien im Add-Modal; expliziter Owner Key 3267, Finding 9246; statisch ungelöst | Zuordnung von Werteauflösung trennen |
| 12 | Dokumentkategorien in der Liste; weiterhin `__($label)`; Finding 2145 hat zwei aktive Keys | Vor automatischer Verarbeitung den konkreten Owner und die bestehenden Verknüpfungen klären |
| 5, 14 | Emergency-contact-Auswahl; heute neun statisch auflösbare Optionen und explizite Owner; historische Payloads leer | Geprüften Payload-Übergang vorsehen; nicht die bisherige Prüfung identischer Alt-/Neuwerte umgehen |
| 17 | Familienstands-Auswahl mit sieben Optionen und explizitem Owner; historisch `plain_label` | Eigene Beobachtung zur Auswahl auf Finding 9254 vorbereiten; indexierte Anzeige (Beobachtung 27) bleibt getrennt |
| 11 | Archive-Filter; lokal bereits übersetzte Labels, anschließend nur Ausgabe | Als Anzeige-Beobachtung behandeln; keine automatische Optionsübernahme aus bloßer Dateinähe |
| 20–25 | Sechs historische Tooltip-Beobachtungen, heute dieselbe Ausgabe-Schleife | Historie erhalten; keine neuen Übersetzungsaufgaben aus bereits aufbereiteten Anzeigeinformationen erzeugen |
| 18, 19 | Ausgeschlossene Old Workbench | Historisch erhalten, nicht erneut scannen oder reaktivieren |

Eine pauschale Sperre für `plain_label` wäre falsch: Beobachtung 17 war früher eine solche Ausgabe und ist heute ein expliziter Übersetzungsaufruf. Auch unübersetzte statische Listen können zunächst nur als Ausgabe beobachtet werden. Entscheidend ist der aktuelle, nachgewiesene Aufruf und seine Zuordnung, nicht allein die alte Bezeichnung.

### Schutz vor stillschweigender Key-Auswahl

Bei Finding **2145** sind Key **3188** (`management.people.edit_person.sections.documents.add_modal.document_category_options`) und Key **3267** (`management.people.edit_person.sections.documents.document_category_options`) aktiv verknüpft. Beide automatischen Befehle verwendeten bisher `MIN(key_id)` für die Auswahl. Damit hätte die kleinere ID ohne fachliche Entscheidung gewonnen.

`TranslationWorkbenchDynamicKeyAssignment::conflicts()` prüft jetzt die betroffenen Findings auf mehrere unterschiedliche aktive, nicht obsolete Keys. `ClassifyDynamicValues` und `ResolveUnknownDynamicSources` rufen diese gemeinsame Vorprüfung vor ihren Schreibschleifen auf. Bei Konflikten liefern sie Exitcode 1 und die Finding-/Key-IDs; auch Dry-run meldet den Konflikt. Es werden keine Quellen, Werte oder Timeline-Einträge geschrieben. Der interne aggregierte Join ist damit keine Entscheidung zwischen konkurrierenden aktiven Keys mehr. Obsolete Keys und inaktive Verknüpfungen werden bereits aus diesem Join ausgeschlossen. Die Klassifizierung berücksichtigt außerdem nur aktive Quelldateien, entsprechend dem Resolver.

Die lesende Bestandsprüfung bestätigt für beide Befehle ausschließlich Finding 2145 als aktuellen Key-Konflikt. Die Verknüpfungen wurden nicht verändert. Ein globaler schreibender Produktionslauf fand nicht statt. Regressionen prüfen beide Befehle mit/ohne Dry-run ohne SQL-Schreibzugriffe, die Auswahl des aktuellen Owners trotz einer kleineren obsoleten Key-ID bzw. Verknüpfung und das Auslassen obsoleter Quelldateien.

Verifikation: vollständige isolierte Workbench-Suite **384 Tests bestanden, 2413 Assertions**; PHP-Syntax und Whitespace-Prüfung erfolgreich. Produktive Daten unverändert. Als nächster konkreter Konflikt ist die doppelte aktive Zuordnung von Finding 2145 anhand der bestehenden Übersetzungen, Runtime-Quellen und Reviews aufzulösen; die kleinere Key-ID ist ausdrücklich kein Entscheidungskriterium.

## Dokumentkategorien: eindeutiger Owner und historischer Abschluss der Doppelzuordnung

`inspect-category-key-conflict.php` hat beide Keys und sämtliche zugehörigen Findings, Verknüpfungen, Quellenwerte, Kandidaten, Reviews, Übersetzungen und Timeline-Ereignisse ausschließlich lesend erfasst. Der vollständige Bericht liegt wegen der enthaltenen Review-/Historieninformationen privat unter `storage/app/private/category-key-conflict.json`.

Die fachliche Zuordnung ist belegt: Key **3267** (`management.people.edit_person.sections.documents.document_category_options`) wird bereits vom Runtime-Collector in `EditPerson`, dem Add-Modal sowie den Kategorieanzeigen der Dokumentliste und des Archivs verwendet. Die beiden Keys enthalten jeweils dieselben 16 aktiven Übersetzungswerte (acht Kategorien in en/de). Runtime-Quelle 31 gehört zu Key 3267. Beide Keys besitzen historische Reviews und bestätigte Kandidatenbezüge; diese dürfen nicht gelöscht oder ungeprüft umgehängt werden.

Die einzige verbliebene Auswahl ohne expliziten Owner war `__($label)` in `resources/views/components/management/people/edit-person/sections/⚡documents.blade.php:154`. Sie verwendet nun wie die anderen Stellen `TranslationWorkbenchOption::label()` mit dem bestehenden Owner-Key, dem Optionswert und dem originalen Label als Fallback. Es wurde keine Übersetzung am Workbench-Datenbestand vorbei erstellt und kein Lang-Export ausgeführt.

`transition-category-owner.php` synchronisiert ausschließlich den geprüften neuen Aufruf mit dem bestehenden Foundation-Syncer. Die vorhandene Missing-Finding-Reconciliation prüft die vollständige erfolgreich gescannte Datei; sie wird nicht mit einem künstlich verkürzten Manifest ausgeführt. Dadurch wird genau das verschwundene Finding 2145 historisch, seine beiden aktiven Verknüpfungen 3190/3285 werden obsolete, und Key 3188 wird mangels weiterer aktiver Fundstellen historisch. Der neue Aufruf ist als **Finding 23230** eindeutig mit Key 3267 verbunden. Die IDs 23228/23229 stammen aus zurückgerollten Proben; PostgreSQL-Sequenzen werden dabei nicht zurückgesetzt.

Vor der verbindlichen Übernahme wurden ein isolierter Regressionstest, die vollständige Workbench-Suite und Transaktionsproben ausgeführt. Das Übergangsskript verwendet SERIALIZABLE mit Advisory Lock, sichert alle betroffenen Foundation-/Options-/Reviewtabellen im privaten Storage und vergleicht jede bestehende Zeile und jedes Feld. Erlaubt sind nur die protokollierten Status-/Metadatenänderungen, Source-Datei-Zählwerte sowie das neue Finding, seine Verknüpfung und zugehörige Timeline-Ereignisse. Unerwartete Änderungen führen zum Rollback. Der erneute begrenzte Abgleich innerhalb der Transaktion erzeugt keine weitere historische Stilllegung.

`verify-category-owner.php` bestätigt nach dem Commit unabhängig in einer READ-ONLY-Transaktion: der neue Owner ist eindeutig, die alte Doppelzuordnung ist nicht mehr aktiv und Key 3267 ist vollständig unverändert. Die gesamten Tabellen `dynamic_sources`, `dynamic_source_values`, `dynamic_source_candidates`, `dynamic_key_values`, `key_values`, `lang_values`, `reviews` und `option_discoveries` stimmen exakt mit der Sicherung überein. Bestehende Quellen und bestätigte Kandidaten behalten daher auch ihre historischen Finding-/Key-Bezüge. Sie wurden bei diesem Source-/Finding-Übergang ausdrücklich nicht als neue aktuelle Quellen interpretiert.

Belege: `category-owner-rehearsal.json`, `category-owner-applied.json`, `category-owner-verified.json`. Der aktualisierte `remaining-option-consumers.json` weist für beide automatischen Befehle **keine aktive Key-Mehrdeutigkeit** mehr aus. Die 17 unzugeordneten Optionsbeobachtungen bestehen weiterhin; der Resolver zieht jetzt nur noch Beobachtung 11 für aktive Quellen heran. Das ist kein Abschluss der gesamten Optionskonsolidierung und keine Freigabe eines globalen produktiven Klassifizierungs-/Providerlaufs.

Verifikation: **385 Tests bestanden, 2422 Assertions**; PHP-Syntax und Whitespace-Prüfung erfolgreich. Als nächster Schritt können die eindeutig belegten statischen Beobachtungen (insbesondere Familienstand) und anschließend die noch ungelösten bzw. bisher leeren Payloads getrennt überführt werden. Der neu geklärte Kategorie-Owner liefert dafür eine eindeutige Grundlage, ohne historische Übersetzungen oder Reviews zu opfern.

## Familienstands-Auswahl: bestehende Beobachtung eindeutig zugeordnet

Beobachtung **17** wurde mit dem bestehenden `TranslationWorkbenchOptionObservationTransition` eindeutig **Finding 9254 / Key 3452** zugeordnet. Der aktuelle explizite Workbench-Aufruf, die sieben statisch aufgelösten Optionen und der vollständige View-Bezug zu `app/Livewire/Management/People/EditPerson.php` stimmen mit der historischen Optionsliste überein. Die separate indexierte Einzelanzeige **Beobachtung 27** bleibt vollständig unverändert.

`transition-marital-observation.php` führt standardmäßig eine zurückgerollte SERIALIZABLE-Transaktionsprobe mit Advisory Lock aus. Vor Änderungen sichert es alle Foundation-/Options-/Reviewtabellen einschließlich Timeline privat. Es nutzt nur statische Quellenanalyse, keine Provider. Nach erfolgreicher Probe wurde mit `--apply` übernommen und anschließend mit `--verify` unabhängig in einer READ-ONLY-Transaktion gegen die Sicherung geprüft.

Erlaubt und tatsächlich geändert wurden lediglich `meta`/`updated_at` an Finding 9254 sowie die Zuordnungs-/Herkunftsmetadaten von Beobachtung 17. Ihre ID, Erstbeobachtung und sieben Optionswerte bleiben erhalten. Neue Zeilen sind ausschließlich die zugehörigen Timeline-Ereignisse. Sämtliche anderen Findings, Beobachtungen, Keys, Quellen, Quellenwerte, Kandidaten, Übersetzungen und Reviews sind unverändert. Insbesondere wurden keine historischen Quellen auf die Auswahl umgehängt. Die tatsächliche en/de-Ausgabe von `TranslationWorkbenchDynamicOptions::rows()` stimmt vor/nach überein. Wiederholte Vorbereitung erzeugt keine zusätzlichen Timeline-Ereignisse.

Die erste unabhängige Nachprüfung überschritt beim gleichzeitigen Laden der vollständigen Sicherung und des vollständigen aktuellen Historienbestands das Speicherlimit. Dies betraf ausschließlich den READ-ONLY-Prüfprozess nach bereits erfolgreicher Übernahme. Der Prüfer vergleicht die aktuellen Tabellen jetzt in 200-Zeilen-Abschnitten; die wiederholte Nachprüfung war erfolgreich, ohne das Speicherlimit weiter zu erhöhen oder Prüfungen auszulassen.

Belege: `marital-observation-rehearsal.json`, `marital-observation-applied.json`, `marital-observation-verified.json`. Der private Sicherungspfad und die geprüften Quellcode-Hashes stehen im Apply-Bericht. Der aktualisierte Verbraucherabgleich zählt **16 historische Beobachtungen**, davon fünf mit Klassifizierer-Kandidaten und eine mit Resolver-Kandidaten. Die bestehenden Key-Mehrdeutigkeitsprüfungen bleiben leer.

Verifikation: **17 einschlägige Übergangsregressionen bestanden, 255 Assertions**, zusätzlich Transaktionsprobe, Wiederholungsprüfung und unabhängiger Tabellen-/UI-Abgleich. Die Anwendungsdienste wurden nicht verändert; der letzte vollständige Suite-Lauf bleibt bei **385 Tests / 2422 Assertions**. PHP-Syntax und Whitespace-Prüfung erfolgreich. Als nächste getrennte Aufgabe stehen die Emergency-contact-Beobachtungen 5/14 an: aktuelle statische Listen mit neun Optionen, aber historisch leere Payloads. Die bisherige Schutzprüfung auf identische Alt-/Neuwerte darf dafür nicht einfach abgeschaltet werden.

## Expliziter Übergang leerer Optionsbeobachtungen

Die Emergency-contact-Beobachtungen 5 und 14 enthalten historisch keine aufgelösten Optionen. Die aktuellen Deklarationen in `CreatePerson` bzw. `EditPerson` lassen dagegen jeweils neun Optionen statisch und über einen eindeutigen View-Bezug auflösen. Ein bloßes Abschalten der bisherigen Gleichheitsprüfung würde auch widersprüchliche Werte freigeben und wurde deshalb nicht vorgenommen.

`TranslationWorkbenchOptionObservationTransition` unterstützt dafür einen zusätzlichen ausdrücklich benannten Review-Nachweis `resolve_empty_options` mit Beobachtungs-Fingerprint, Deklarationspfad und Datei-Hash. Eine abweichende neue Werteliste wird ausschließlich angenommen, wenn die alte Beobachtung wirklich leer ist, `options_count=0` und `source_type=unresolved` hat und keine früher aufgelösten bzw. wegen Mehrdeutigkeit archivierten Werte enthält. View, aktueller expliziter Owner, Finding/Key-Zuordnung und Kontext bleiben unverändert streng geprüft. `collectStatic()` im bestehenden Collector liest Deklarationen ohne Provider-Ausführung und bestätigt erneut eindeutigen View-Bezug, Deklarationsquelle und vollständige Payload. Dieser Nachweis wird auch bei einer Wiederholung erneut geprüft.

Der alte leere Zustand samt Herkunft und Nachweis bleibt in `reviewed_empty_options_resolution` erhalten. Das zentrale Timeline-Ereignis `dynamic_option_empty_payload_resolved` beschreibt die geprüfte Ergänzung; der reguläre Synchronizer schreibt anschließend die aktuelle Beobachtung. Der gesamte Übergang läuft in einer Transaktion. Eine normale Anfrage ohne den Zusatznachweis unterliegt weiterhin der bisherigen Prüfung identischer Werte. Eine spätere abweichende nichtleere Liste wird auch mit dem Zusatznachweis nicht überschrieben.

Regressionen prüfen erfolgreichen Übergang, idempotente Wiederholung, fehlenden Nachweis, veränderte Deklaration und Beobachtung, gefälschte Payload, nichtleere Altdaten, inkonsistente Zählwerte, archivierte frühere Werte, konkurrierende View-Quellen, Batch-Rollback und eine nach erfolgreichem Übergang geänderte Deklaration. Bereits bearbeitete Übersetzungen bleiben dabei unverändert.

### Emergency-contact-Beobachtungen übernommen

Nach erfolgreichen Transaktionsproben wurden beide geprüften Zuordnungen am 15.10.2026 übernommen:

| Beobachtung (ID erhalten) | Aktuelles Finding | Bestehender Key | Statisch belegte Optionen |
| --- | --- | --- | --- |
| 5 · CreatePerson | 9265 | 3062 | 9 |
| 14 · EditPerson | 9267 | 3315 | 9 |

`transition-emergency-observations.php --id=5/14` führt standardmäßig nur eine zurückgerollte Probe aus; `--apply` übernimmt und `--verify` prüft unabhängig in einer PostgreSQL-READ-ONLY-Transaktion. Jede Übernahme verfügt über eine eigene private vollständige Tabellensicherung und Quellcode-Hashes. Nur die Ziel-Finding-Metadaten sowie die jeweilige bestehende Beobachtung einschließlich der geprüften Optionsliste dürfen sich ändern. Die alten leeren Werte und der explizite Nachweis bleiben in den Beobachtungsmetadaten und der Timeline erhalten. ID und Erstbeobachtung bleiben unverändert. Wiederholung erzeugt keine zusätzlichen Timeline-Ereignisse.

Beide Nachprüfungen bestätigen die unveränderten übrigen Tabellenzeilen einschließlich aller vorhandenen Quellen, Quellenwerte, bearbeiteten Übersetzungen, Reviews und Kandidatenverknüpfungen. Die tatsächliche en/de-Ausgabe des Workbench-Resolvers wurde vor/nach identisch geprüft. Es gab keinen globalen Scanner-, Provider- oder Klassifizierungslauf und keinen Lang-Export. Die vorhandenen historischen Quellen wurden bei diesem Beobachtungsübergang nicht umgehängt oder neu klassifiziert.

Belege: `emergency-observation-5-rehearsal.json`, `emergency-observation-5-applied.json`, `emergency-observation-5-verified.json` sowie die entsprechenden drei Dateien für Beobachtung 14. Die speichersparende unabhängige Nachprüfung vergleicht den aktuellen Bestand in 200-Zeilen-Abschnitten gegen die Sicherung.

Verifikation: vollständige isolierte Workbench-Suite **396 Tests bestanden, 2491 Assertions**; Syntax-/Whitespace-Prüfung erfolgreich. Aktuell bestehen **26 Beobachtungen, davon zwölf exakt zugeordnet und 14 historisch ohne Zuordnung**. Drei historische Beobachtungen liefern noch Klassifizierer-Kandidaten, eine liefert Resolver-Kandidaten. Weiterhin keine aktive Key-Mehrdeutigkeit. Als Nächstes sind die Dokumenttypen/-kategorien mit Runtime-/DB-Wertequellen von den reinen Anzeige- und ausgeschlossenen historischen Beobachtungen getrennt zu behandeln; deren fehlende statische Payload darf nicht durch angenommene Standardwerte ersetzt werden.

## Dokumenttyp-/Kategorie-Beobachtungen: Identität und Runtime-Werte getrennt

Die fünf historisch leeren Beobachtungen 4, 9, 10, 12 und 13 wurden am 15.10.2026 den aktuellen, expliziten Foundation-Aufrufen zugeordnet. `TranslationWorkbenchOptionObservationTransition::process()` unterstützt hierfür den ausdrücklichen Modus `assign_unresolved=true`. Er verlangt eine bestehende, leere und ungelöste Beobachtung ohne archivierte frühere Optionswerte. Der aktuelle statische Collector muss diese ungelöste Quelle erneut bestätigen; View-Hash, eindeutiger aktueller Aufruf, Owner-Key, Finding-Zuordnung und Schleifenkontext werden weiterhin geprüft. Ein Aufruf ohne diesen Modus kann weiterhin keine ungelöste Payload übernehmen.

| Beobachtung | Aktuelles Finding | Key |
| --- | --- | --- |
| 4 · CreatePerson Dokumenttypen | 9256 | 3039 |
| 9 · Add-modal Dokumenttypen | 9258 | 3039 |
| 10 · Add-modal Kategorien | 9246 | 3267 |
| 12 · EditPerson Kategorien | 23230 | 3267 |
| 13 · EditPerson Dokumenttypen | 9264 | 3039 |

Dies ist ausschließlich eine Identitätszuordnung: `options=[]`, `options_count=0`, `source_type=unresolved` und `status=needs_review` bleiben erhalten. Die Quellen 42 (elf aktive Dokumenttypen) und 31 (acht aktive Kategorien) behalten ihre bereits erfassten Runtime-Werte. Insbesondere wären die acht eingebauten Dokumenttyp-Standardwerte kein vollständiger Ersatz für die elf Runtime-Werte. Es wurden weder Provider ausgeführt noch Standardwerte als vermeintlich statische Erfassung kopiert.

`transition-document-observations.php` prüft zuerst mit Rollback, übernimmt mit `--apply` und kontrolliert mit `--verify` unabhängig in einer READ-ONLY-Transaktion. Private Sicherungen werden je Tabelle geschrieben; die Nachprüfung arbeitet in 200-Zeilen-Abschnitten. Zugelassen sind ausschließlich Metadatenänderungen an den fünf Ziel-Findings und Beobachtungen sowie deren bekannte Timeline-Ereignisse. IDs, Erstbeobachtungen, sämtliche anderen Zeilen, Quellen-/Runtime-Werte, Übersetzungen, Reviews und Kandidatenverknüpfungen bleiben unverändert. Die tatsächlichen en/de-Resolverwerte stimmen vor/nach überein; Wiederholung erzeugt keine weiteren Timeline-Ereignisse. Die erste Probe wurde wegen eines im Prüfskript falsch benannten erwarteten Timeline-Ereignisses zurückgerollt; nach Korrektur auf den bestehenden Synchronizer-Ereignisnamen bestand die Probe vollständig.

Belege: `document-option-runtime.json`, `document-observations-rehearsal.json`, `document-observations-applied.json`, `document-observations-verified.json`. Kein globaler Scan, Klassifizierer-/Resolver-Schreiblauf oder Lang-Export wurde durchgeführt.

Verifikation: sieben neue Regressionen einschließlich gefälschter Werte, inzwischen statisch auflösbarer Quelle, fehlender ausdrücklicher Freigabe, archivierter/nonempty Payload und Batch-Rollback. Vollständige isolierte Workbench-Suite: **403 Tests bestanden, 2539 Assertions**. Der aktuelle Abgleich zählt **17 exakt zugeordnete und neun historische Beobachtungen**; letztere liefern keine Klassifizierer-Kandidaten und noch einen Resolver-Bezug. Dieser Rest besteht aus Anzeige-/ausgeschlossenen historischen Fundstellen und muss vor einer Stilllegung separat geprüft werden.

## Verbleibende neun Beobachtungen: Anzeige ist keine Optionsquelle

Die verbleibenden historischen Beobachtungen wurden anhand des aktuellen Quellcodes, Scan-Manifests und der tatsächlichen Kandidatenauswahl geprüft:

- **11 (`archiveFilters`)**: Die lokalen Array-Werte werden bereits beim Aufbau einzeln übersetzt. Die foreach-Schleife gibt `$label` lediglich aus. Darin existiert kein aktueller Übersetzungsaufruf für eine dynamische Optionsliste.
- **18/19 (`kindCounts`/`statusCounts`)**: historische Old-Workbench-Datei, laut aktuellem Scan-Manifest ausgeschlossen.
- **20–25 (`tooltipRows`)**: sechs historische Beobachtungen; aktuell eine gemeinsame reine Ausgabeschleife. Die Übersetzungen stehen beim Aufbau der Tooltip-Tabelle, nicht in der Ausgabe von `$label` und `$value`.

Ein Fehler bestand noch in `ResolveUnknownDynamicSources::bestDiscoveryForSource()`: Der Dateipfad allein genügte zur Aufnahme als Optionskandidat. Dadurch wurde Beobachtung 11 den Quellen 52/54 (Findings 2685/2687) zugerechnet, obwohl die reine Anzeige-Schleife keine belegte Optionsquelle dieser Aufrufe ist. Die allgemeine Regel wurde korrigiert: Eine übereinstimmende Datei oder nahe Zeile darf bestehende Identitätskandidaten gewichten, aber keine Optionsidentität begründen. Exakte Foundation-Zuordnungen sowie bestehende Key-/Scope-Treffer bleiben erhalten. Eine globale Ablehnung von `plain_label` wurde ausdrücklich nicht eingeführt, da dieser Typ an anderer Stelle noch unübersetzte echte Optionen enthalten kann.

Drei neue Regressionen prüfen die Ablehnung eines reinen Datei-/Zeilentreffers (auch mit nichtleerer Payload) sowie den Erhalt von Key- und Scope-Treffern über verschiedene Dateien hinweg. Der erneute READ-ONLY-Abgleich bestätigt **neun historische Beobachtungen, null Klassifizierer-Verbraucher und null Resolver-Verbraucher**; aktive Key-Konflikte bleiben leer. Alle neun Beobachtungen und deren Status bleiben zur Nachvollziehbarkeit erhalten. Keine Produktionsdaten wurden geändert; kein globaler schreibender Command wurde ausgeführt. Der aktuelle Beleg steht in `remaining-option-consumers.json`.

Verifikation dieses Schritts: vollständige isolierte Workbench-Suite **406 Tests bestanden, 2548 Assertions**; Whitespace-Prüfung erfolgreich. Die historische Optionsbeobachtungsprüfung ist damit abgeschlossen: 17 eindeutig zugeordnet, neun historische Anzeige-/ausgeschlossene Beobachtungen ohne aktuelle Verbraucher. Dies ist keine Freigabe aller globalen Schreibcommands; insbesondere die separate Runtime-Quellenzuordnung und deren bestehende Scope-Heuristik bleiben eigenständig zu beurteilen.

## Runtime-Scope-Zuordnung und Schutz vorhandener Reviews

Die lesende Prüfung der tatsächlichen `unknownSources()` und Runtime-Auswahl ergab aktuell keine unbekannten aktiven Verbraucher mit passenden Runtime-Scope-Quellen. Vorhanden sind 16 bestätigte aktive, eine bestätigte obsolete und zehn offene aktive `related_dynamic_source`-Verknüpfungen. Belege: `runtime-matching-before.json` und `runtime-matching-after.json`; beide stammen aus READ-ONLY-Transaktionen ohne Command-/Provider-Ausführung.

Im Resolver bestanden trotzdem zwei allgemeine Risiken: Mehrere gleichnamige Runtime-Scopes wurden nach Status, Werteanzahl und Aktualität auf eine einzige vermeintlich beste Quelle reduziert. Außerdem schützte die Aktualisierung einer bestätigten Verknüpfung lediglich deren `review_status`, nicht deren bestätigten Werte-Snapshot, Zielzuordnung und sonstige Attribute. Stillgelegte Quellen konnten reaktiviert und aufgehobene Verknüpfungen wieder als offene Vorschläge überschrieben werden.

`ResolveUnknownDynamicSources` berücksichtigt für neue Scope-Vorschläge jetzt ausschließlich aktive Runtime-Quellen mit vorhandenen aktiven/stale Werten und schließt die Verbraucherquelle selbst aus. Nur ein eindeutiger Kandidat wird vorgeschlagen; konkurrierende Quellen bleiben ungeklärt und zählen weiterhin als `sources_still_unknown`. Eine obsolete Quelle wird durch diesen Resolver nicht reaktiviert. Ein Scope-Treffer bleibt ein `pending`-Vorschlag, keine Bestätigung. Die bisherige Zulassung von stale Quellenwerten für einen offenen Vorschlag wurde nicht geändert.

Vorhandene Verknüpfungen werden nur aktualisiert, wenn sie `pending` und `active` sind. Bestätigte, abgelehnte, aufgehobene oder stillgelegte Verknüpfungen einschließlich ihrer Snapshots bleiben unverändert. Bewusste Änderungen erfolgen weiterhin über den bestehenden Review-Workflow. Regressionen prüfen konkurrierende/eindeutige/obsolete Quellen, bestätigte/aufgehobene/abgelehnte bzw. inaktive offene Verknüpfungen sowie die weiterhin mögliche Aktualisierung offener aktiver Vorschläge und deren Wiederholbarkeit.

Es wurden keine Produktionsdaten geändert. Die bestehenden Verknüpfungen wurden weder umgehängt noch automatisch bestätigt. Die Prüfung belegt die abgesicherte allgemeine Vorschlagsregel; sie ersetzt keine individuelle fachliche Prüfung der zehn bereits offenen Vorschläge.

Verifikation: **414 Tests bestanden, 2571 Assertions** in der vollständigen isolierten Workbench-Suite; PHP-Syntax und Whitespace-Prüfung erfolgreich. Acht neue Regressionen sichern diesen Schritt ab. Als nächste Prüfung verbleibt die Herkunft und heutige Gültigkeit der zehn offenen Runtime-Verknüpfungsvorschläge, ohne bestehende Reviews automatisch umzudeuten.

## Zehn offene Runtime-Vorschläge zu obsoleten Fundstellen

Der Einzelabgleich (`pending-runtime-links.json`) zeigt: Alle zehn aktiven offenen Vorschläge 86–95 referenzieren obsolete Findings ohne aktive Key-Verknüpfung. Die Verbraucherquellen 74/75/79/80/83–88 gehören weiterhin genau diesen historischen Findings. Die Runtime-Quellen 31/42/43 sind hingegen aktiv und werden nicht stillgelegt. Die Vorschläge werden nicht auf heutige Fundstellen umgehängt und nicht als fachlich bestätigt oder abgelehnt markiert.

Der neue explizite Dienst `TranslationWorkbenchRuntimeSuggestionRetirement` verlangt pro Kandidat dessen ID, Finding-ID, Verbraucherquellen-ID und Hash des geprüften Werte-Snapshots. Nur offene Runtime-Vorschläge zu einer obsoleten Fundstelle ohne aktive Key-Links sind zulässig. Er setzt ausschließlich den Kandidatenstatus auf `obsolete`, ergänzt den Stilllegungsnachweis und schreibt `dynamic_runtime_suggestion_retired` in die Timeline. Review-Status, bestehende Metadaten, Snapshot, Quellwerte und Identitäten bleiben erhalten. Wiederholung ist ohne zusätzliche Ereignisse möglich; ein später reaktiviertes Finding blockiert den Vorgang. Der Dienst wird nicht automatisch in die Scan-/Resolver-Pipeline eingebaut.

Sechs Regressionen prüfen den gültigen Übergang samt Dry-run und Wiederholung, aktive Findings, noch aktive Key-Verknüpfungen, bestätigte Entscheidungen, geänderte Snapshots und vollständigen Batch-Rollback. Das Audit `retire-pending-runtime-links.php` sichert sämtliche betroffenen Kandidaten privat und prüft die übrigen 13 Foundation-/Quell-/Übersetzungs-/Review-/Historientabellen über zeilenweise SHA-256-Abgleiche. Ausschließlich die zehn Status-/Metadatenänderungen und zehn neue Stilllegungsereignisse sind zulässig. Standard ist Rollback; `--apply` übernimmt, `--verify` prüft unabhängig lesend gegen die Sicherung.

Die Rollback-Probe, Übernahme und unabhängige READ-ONLY-Nachprüfung waren erfolgreich (`pending-runtime-retirement-{rehearsal,applied,verified}.json`). Bestand danach: **null offene aktive Runtime-Vorschläge**, zehn offene historische Vorschläge mit Status `obsolete`, unverändert 16 bestätigte aktive und eine bestätigte obsolete Verknüpfung. Es gab keinen globalen Scan-/Resolver-Lauf oder Lang-Export. Vollständige isolierte Workbench-Suite: **420 Tests bestanden, 2605 Assertions**; Syntax-/Whitespace-Prüfung erfolgreich. Die Bereinigung der offenen historischen Vorschläge ist abgeschlossen; eine fachliche Bestätigung neuer Runtime-Verknüpfungen wurde nicht vorgenommen.

## Bestätigte Runtime-Verknüpfungen: Historie und heutige Wirksamkeit

Der erweiterte lesende Prüfer `inspect-pending-runtime-links.php --confirmed` untersucht dieselben Herkunftsmerkmale auch für bestätigte Verknüpfungen. Von 16 bestätigten aktiven Datensätzen verweist nur Kandidat 77 auf einen weiterhin aktiven Verbraucher (Quelle 23 / Finding 9252 / Key 3120). Die übrigen 15 referenzieren obsolete Findings ohne aktive Key-Links; die ursprünglichen Bestätigungen und Werte-Snapshots sind weiterhin wertvolle Historie und wurden nicht verändert oder umgehängt. Beleg: `confirmed-runtime-links.json`.

`TranslationWorkbenchDynamicOptions::linkedRuntimeSourceIdsForFinding()` berücksichtigte bisher auch solche historischen Bestätigungen über die Key-Gleichheit. Der neue allgemeine Leseschutz verlangt deshalb eine aktive Eigentümer-Fundstelle, aktive Quelldatei, aktive Verbindung zum Kandidaten-Key und einen nicht obsoleten Key. Eine gültige bestätigte Verknüpfung kann weiterhin für weitere Fundstellen desselben Keys gelten. Kandidaten-/Review-/Timeline-Daten werden beim Lesen nicht verändert. Direkte Quellenzuordnung und gespeicherte Übersetzungswerte bleiben unverändert.

Vor der Korrektur wurde ihre Auswirkung mit einem ausschließlich lesenden Vergleich geprüft: 19 heutige Fundstellen zu den betroffenen Keys liefern mit und ohne historische Bestätigungen vollständig identische en/de-Optionszeilen. Nach der Korrektur bestätigt ein zweiter READ-ONLY-Lauf dieselben vollständigen Zeilen-Hashes gegen die zuvor gesicherte Baseline (`confirmed-runtime-impact.json`, `confirmed-runtime-impact-after.json`). Das belegt nicht nur gleiche Anzahlen, sondern gleiche Inhalte aller zurückgegebenen Felder. Der Vergleich betrifft den gemeinsamen Resolver für Editor/Preview; er ist kein visueller Browser-Test.

Sechs Regressionen sichern aktive Eigentümer, obsolete Findings/Dateien/Keys/Key-Links sowie weiterhin zulässige gemeinsame Keys ab. Bestätigungen, Reviews und Timeline bleiben in allen Lesefällen unverändert. Es gab keine Produktionsschreibzugriffe und keinen globalen Scan-/Resolver-Lauf.

Verifikation: vollständige isolierte Workbench-Suite **426 Tests bestanden, 2629 Assertions**; PHP-Syntax und Whitespace-Prüfung erfolgreich. Als verbleibender Prüfpunkt ist die direkte Quellenauflösung (`sources.finding_id` / `sources.key_id`) getrennt von bestätigten Kandidaten zu beurteilen: Auch dort dürfen historische Verbraucherquellen nicht unbeabsichtigt aktuelle Werte liefern. Die vorliegende Korrektur ändert diesen zweiten Leseweg ausdrücklich noch nicht.

## Direkte Optionsquellen über Finding und Key

Die READ-ONLY-Bestandsaufnahme (`inspect-direct-option-sources.php`) erfasst 22 nicht obsolete Quellen mit aktiven Werten: neun Quellen mit gültiger aktiver Finding-/Datei-/Key-Zuordnung und 13 eigenständige Runtime-Quellen ohne Finding. Der aktuelle Bestand weist hier keine veraltete nutzbare Quelle auf; im allgemeinen Leseweg fehlte jedoch der Schutz dagegen.

`TranslationWorkbenchDynamicOptions::rows()` unterscheidet nun explizit:

- Eigenständige `runtime_options`/`runtime_db_options` ohne Finding bleiben über ihren nicht obsoleten Key nutzbar.
- Finding-gebundene Quellen benötigen eine aktive Fundstelle und Datei sowie eine aktive Verbindung zum angefragten, nicht obsoleten Key. Ein ausdrücklich abweichender Quellen-Key wird nicht allein durch Finding-Gleichheit übergangen. Eine Quelle ohne eigenen Key ist nur über die passende aktive Finding-Verknüpfung zulässig.
- Explizit bestätigte Runtime-Verknüpfungen behalten ihren bereits abgesicherten separaten Leseweg.
- Bereits gespeicherte Übersetzungswerte werden unabhängig von der Gültigkeit einer Quellenbeobachtung weiterhin gelesen und nicht verändert.

Neun Regressionen prüfen gültige direkte und eigenständige Runtime-Quellen, verwaiste statische Quellen, obsolete Findings/Dateien/Key-Links/Keys, abweichende Quellen-Keys und weiterhin zulässige Nutzung über denselben Key. Der lesende Vorher-/Nachher-Abgleich bestätigt vollständige identische en/de-Optionszeilen für **42 aktuelle Finding-/Key-Kombinationen** (`direct-option-sources-before.json`, `direct-option-sources-after.json`). Keine Produktionsdaten wurden verändert, kein globaler schreibender Command ausgeführt.

Verifikation: vollständige isolierte Workbench-Suite **435 Tests bestanden, 2674 Assertions**; PHP-Syntax und Whitespace-Prüfung erfolgreich. Dieser Abschnitt schließt die Herkunftsprüfung der direkten Options-Lesewege ab. Die Auswahl bei mehreren gleichzeitig gültigen Quellen mit gleichem `value_key` bleibt ein gesonderter Prüfpunkt: Die vorhandene Zusammenfassung nimmt weiterhin den ersten Quellenwert; diese Prioritätsregel wurde in diesem Schritt nicht geändert.

## Mehrere gültige Quellen pro Optionsschlüssel

`inspect-option-source-conflicts.php` verwendet ausdrücklich die SQL-Abfrage des tatsächlichen `TranslationWorkbenchDynamicOptions::rows()`-Lesewegs, bevor die Ergebnisse im Speicher auf die erste Zeile reduziert werden. Dadurch wird keine abweichende Eligibility-Regel im Audit nachgebaut. Die Prüfung läuft vollständig READ ONLY, ohne Provider oder Schreibcommands auszuführen. Werte werden im Prüfbericht nur als SHA-256-Hashes dokumentiert.

Ergebnis für die 42 aktuellen Finding-/Key-Kombinationen der vorangegangenen Bestandsaufnahme: **65 Mehrfachgruppen, null widersprüchliche Gruppen**. Die 65 zählen Gruppen je geprüfter Finding-/Key-Kombination; dieselbe gemeinsame Quelle kann daher mehrfach vorkommen. Verglichen wurden die tatsächlichen `native_label`-Werte strikt, ohne Normalisierung, welche Unterschiede verbergen könnte. Beleg: `option-source-conflicts.json`.

Der bestehende Resolver sortiert nur nach `value_key` und verwendet innerhalb einer Gruppe die erste Zeile. Dies ist keine fachliche Quellenpriorität und wäre bei abweichenden Werten nicht eindeutig. Aktuell ist die Auswahl aufgrund identischer Werte ohne Auswirkung. Deshalb wurde weder eine künstliche Reihenfolge nach ID/Datum/Typ noch eine neue Prioritätsregel eingebaut. Die Abwesenheit heutiger Konflikte ist keine Absicherung für künftige Konflikte.

Vier Regressionen prüfen identische doppelte Runtime-Quellen in beiden Einfügereihenfolgen, jeweils mit und ohne gespeicherte Sprachwerte. Es wird genau eine Optionszeile geliefert; vorhandene bearbeitete Quell-/Zielübersetzungen behalten Vorrang vor den Quellenwerten. Die ursprüngliche Quellenbezeichnung (`native_label`) bleibt von der bearbeiteten Quellübersetzung getrennt. Keine Quell-/Übersetzungswerte werden beim Lesen verändert.

Für einen künftigen echten Konflikt ist eine explizite Konfliktanzeige mit gezielter Quellenprüfung sinnvoll, statt eine Quelle stillschweigend gewinnen zu lassen. Dafür müssen Anzeige und Bearbeitungsgrenze zusammen festgelegt werden; ein bloßer Austausch von `first()` durch eine neue Sortierung würde das Problem nur verdecken. In diesem Prüfschritt wurden ausschließlich Audit, Regressionstests und Dokumentation ergänzt; Produktionscode und Produktionsdaten blieben unverändert.

Verifikation: vollständige isolierte Workbench-Suite **439 Tests bestanden, 2698 Assertions**; Whitespace-Prüfung erfolgreich. Die nächste konkrete Erweiterung wäre eine gemeinsame Konflikterkennung im Optionsresolver mit sichtbarer Meldung im bestehenden dynamischen Editor und erneuter Prüfung vor dem Speichern, damit künftige widersprüchliche Quellen nicht unbemerkt übernommen werden.

## 16.10.2026 – Konflikte zwischen gültigen Optionsquellen sichtbar machen

Der gemeinsame `TranslationWorkbenchDynamicOptions`-Resolver vergleicht die tatsächlichen `native_label`-Werte innerhalb jedes Optionsschlüssels strikt. Identische Mehrfachwerte bleiben eine normale Optionszeile. Bei abweichenden Werten liefert er `source_conflicts` mit Quellen-ID, Herkunftsreferenz und Originalwerten; kein Quellenwert wird als Gewinner ausgewählt. Die Sortierung nach Quellen-ID stabilisiert ausschließlich die Darstellung der Konfliktliste, nicht die fachliche Auswahl. Ohne gespeicherte Quellübersetzung bleibt der Quellwert in diesem Fall leer; vorhandene Quell-/Zielübersetzungen bleiben sichtbar. Konfliktfreie Zeilen behalten ihre bisherige Datenstruktur.

Der bestehende dynamische Editor zeigt diese Konflikte in einem Flux-Warn-Callout mit Optionsschlüssel, Quellen und Werten. Inhalte werden mit den normalen Blade-Escapes ausgegeben. Eine Flux-Fehleranzeige macht auch die Validierungsantwort beim Speichern sichtbar. Neue Texte sind als übersetzbare Literale eingebaut; es wurde kein Export oder Scan ausgeführt.

`TranslationWorkbenchDynamicEditor` liest die Quellen bei jedem Speichern erneut serverseitig. Der Batch-Speichervorgang schreibt sämtliche Optionszeilen und wird daher bei einem enthaltenen Konflikt vor dem ersten Writer-Aufruf abgebrochen. Das Speichern einer einzelnen Quellzeile prüft nur diese Zeile; eine unabhängige konfliktfreie Zeile bleibt bearbeitbar. Bereits gespeicherte Übersetzungen oder clientseitig übergebene Werte heben einen Quellenkonflikt nicht auf. Nach tatsächlicher Auflösung der Quellenmehrdeutigkeit ist Speichern wieder möglich. Es wurde keine neue automatische Quellenentscheidung oder Review-Umdeutung eingeführt.

Sechs Workflow-Regressionen prüfen nachträglich entstehende Konflikte nach dem Öffnen, Herkunftsangaben, Erhalt gespeicherter Übersetzungen, blockierte Batch-/Einzelwrites ohne Timeline-Nebenwirkungen, unabhängige Einzelwrites und erneute Freigabe nach Quellenauflösung. Zwei echte Blade-/Flux-Renderprüfungen decken sichtbare/fehlende Konfliktmeldung sowie HTML-Escaping von Quelleninhalten ab. Die isolierte Renderfixture benötigte eine ViewErrorBag wie im normalen Webrequest; dies wurde in der Fixture ergänzt.

Der erneute READ-ONLY-Baselinevergleich bestätigt: Alle **42 aktuellen konfliktfreien Finding-/Key-Anzeigen sind vollständig unverändert**. Keine Produktionsdaten wurden geschrieben.

Abschlussverifikation: vollständige isolierte Workbench-Suite **447 Tests bestanden, 2758 Assertions**. Der erste Gesamtlauf hatte noch die vor Ergänzung der ViewErrorBag geladene Renderfixture verwendet; der vollständige erneute Lauf mit korrigierter Fixture ist grün. PHP-Syntax und Whitespace-Prüfung erfolgreich. Die Konfliktabsicherung umfasst Erkennung, sichtbare Provenienz und serverseitige Schreibprüfung; eine neue UI zur automatischen oder manuellen Auswahl eines Quellengewinners wurde nicht hinzugefügt.

## 16.10.2026 – Schreibgrenze der manuellen Quellenbestätigung

Die Anschlussprüfung zur Konfliktanzeige zeigt: Der vorhandene Quellen-Review bestätigt Zugehörigkeit zwischen Runtime-Provider und Verbraucher, nicht die fachliche Priorität einzelner widersprüchlicher Werte. Ein pauschaler Quellengewinner oder eine ungeprüfte Navigation mit Verlust ungespeicherter Editordaten wurde deshalb nicht ergänzt. Bei direkten Quellen muss die Herkunft korrigiert bzw. die Erfassung aktualisiert werden; eine bestehende bestätigte Verknüpfung kann über den vorhandenen Review-Workflow bewusst gelöst werden. Die Konflikterkennung bleibt davon unabhängig und prüft anschließend die tatsächlich noch wirksamen Quellen.

Dabei wurde eine konkrete Inkonsistenz im Bestätigungswriter gefunden: Er schrieb den Key des Runtime-Providers zusammen mit der Fundstelle des Verbrauchers in den Kandidaten. Bei unterschiedlichen Keys entstand damit eine Verbindung, die keiner aktiven Finding-/Key-Zuordnung entspricht und vom inzwischen abgesicherten Leser zu Recht nicht genutzt werden kann.

`TranslationWorkbenchDynamicSourceLinkWriter::confirm()` löst jetzt innerhalb seiner Transaktion die aktuelle Verbraucherquelle und deren aktive Fundstelle/Datei/Key-Verknüpfung erneut auf. Die übergebenen Verbraucher-IDs müssen dem aktuellen Bestand entsprechen. Genau eine aktive, nicht obsolete Key-Zuordnung ist erforderlich; ein expliziter abweichender Quellen-Key wird abgewiesen. Die Bestätigung speichert Finding und Key dieses Verbrauchers. Der Provider bleibt getrennt durch `dynamic_source_id` referenziert und sein eigener Key wird nicht geändert. Veränderte, obsolete oder mehrdeutige Zuordnungen erzeugen eine Validierungsfehlermeldung vor dem ersten Kandidaten-/Review-/Timeline-Schreibvorgang. Ein `flux:error` im bestehenden Bestätigungsdialog macht diesen Fehler sichtbar.

Sieben Workflow-Regressionen prüfen unterschiedliche Provider-/Verbraucher-Keys einschließlich Nutzung über den gemeinsamen Optionsresolver sowie obsolete Findings/Dateien/Quellen/Key-Links, zwischenzeitlich geänderte Keys und Mehrdeutigkeit. Eine zusätzliche echte Flux-Renderprüfung bestätigt die sichtbare Fehlermeldung. Vorhandene Bestätigungen, Snapshots und Produktionsdaten wurden nicht umgehängt oder verändert. Die allgemeine Quellenberechtigung und die Snapshot-Herkunft des Review-Writers sind weiterhin getrennte Prüfbereiche; diese Änderung behauptet keine vollständige Neuentwicklung des Quellenreviews.

Verifikation: vollständige isolierte Workbench-Suite **454 Tests bestanden, 2787 Assertions**. Die danach ergänzte Fehlermeldungs-Renderprüfung wurde zusammen mit den beiden vorhandenen Konflikt-Renderprüfungen separat erfolgreich ausgeführt (**3 Tests, 9 Assertions**). PHP-Syntax und Whitespace-Prüfung erfolgreich. Als nächster konkreter Prüfpunkt verbleibt, ob die Runtime-Provider und Werte-Snapshots bei der manuellen Bestätigung ebenfalls vollständig aus dem aktuellen Datenbestand statt aus übergebenen Vorschau-Arrays aufgelöst werden.

## 16.10.2026 – Autoritative Provider und Werte beim Bestätigen

`TranslationWorkbenchDynamicSourceLinkWriter::confirm()` liest jetzt sämtliche ausgewählten Provider und die Verbraucherquelle innerhalb seiner Transaktion erneut aus der Datenbank und sperrt die vorhandenen Quellen-/Wertezeilen für den Schreibvorgang. Provider müssen weiterhin als Runtime-Quellen vorliegen; obsolete/inaktive Quellen, veränderte Typen, IDs, Key-/Finding-Zuordnungen oder Herkunftsangaben werden abgewiesen. Die aktuelle vollständige Liste nicht obsoleter Werte wird gegen die übergebene serverseitige Vorschau geprüft. Alle Quellen werden vor dem ersten Kandidaten-Schreibvorgang geprüft, so dass ein ungültiger zweiter Provider keine Teilbestätigung hinterlässt.

Persistierte Snapshots und ihre Anzahl stammen anschließend ausschließlich aus den erneut gelesenen Daten. Eine manipulierte Vorschau-Anzahl wird nicht übernommen. Die bestehende Bedeutung bleibt erhalten: `candidate_values` enthält die beobachteten Verbraucherwerte, nicht ungefragt einen anderen Provider-Snapshot. Provider und Verbraucher bleiben getrennt referenziert. Quellwerte und bestehende Übersetzungen werden nicht verändert.

Der Vergleich berücksichtigt die bereits bestehende Anzeige-Normalisierung (Trim/leer zu null) der Review-Vorschau, damit z. B. ein unverändert mit Leerraum gespeicherter Originalwert nicht fälschlich als veraltet gilt. Gespeichert wird trotzdem der unveränderte rohe Datenbankwert. Die Prüfung bezieht sich auf die an den Writer übergebene Vorschau. Die Livewire-Aktion erstellt diese weiterhin unmittelbar vor dem Writer-Aufruf neu; eine gesonderte Bindung an den Zustand beim erstmaligen Öffnen des Dialogs ist damit noch nicht implementiert.

Neun neue Regressionen prüfen nach Vorschauerstellung geänderte Provider-/Verbraucherwerte, obsolete Provider, geänderten Typ/Herkunft, gefälschte Verbraucherwerte, einen untergeschobenen Nicht-Runtime-Provider, ignorierte falsche Anzahl und Erhalt roher Werte trotz Anzeige-Normalisierung. Fehler erzeugen weder Kandidaten noch Reviews/Timeline-Ereignisse. Kein Produktionsschreibzugriff und kein globaler Scan-/Resolver-Lauf wurden durchgeführt.

Verifikation: vollständige isolierte Workbench-Suite **464 Tests bestanden, 2838 Assertions**; PHP-Syntax und Whitespace-Prüfung erfolgreich. Der nächste klar abgegrenzte Schritt ist ein manipulationsgeschützter Vorschau-Nachweis vom Öffnen bis zur Bestätigung des Dialogs, damit auch zwischen diesen UI-Zeitpunkten geänderte Quellen eine erneute bewusste Prüfung erfordern.

## 16.10.2026 – Bestätigung an die geöffnete Quellen-Vorschau binden

Die Livewire-Aktion `openDynamicSourceLinkConfirm()` erzeugt nun einen authentifiziert verschlüsselten Nachweis über den angezeigten Quellenstand. Enthalten sind der angemeldete Benutzer, Finding/Key, Verbraucherquelle und die nach ID sortierte Provider-Liste einschließlich ihrer Werte und Herkunft. Der Client erhält nur den verschlüsselten Hash. Eine veränderte Livewire-Eigenschaft kann keinen gültigen Nachweis für einen anderen Stand erzeugen.

Vor `confirmDynamicSourceLink()` wird die aktuelle serverseitige Vorschau gegen diesen Nachweis geprüft. Geänderte Werte/Herkunft, hinzugefügte oder entfernte Provider, eine andere Verbraucherquelle oder ein anderer Benutzer sowie fehlende/manipulierte Nachweise verhindern die Bestätigung. Die vorhandene Fehlermeldungsfläche im Dialog fordert das Schließen und erneute Öffnen zur bewussten Prüfung. Beim erneuten Öffnen wird der alte Fehler zurückgesetzt; Schließen und erfolgreiche Bestätigung verwerfen den Nachweis. Die transaktionale erneute Quellenprüfung im Writer bleibt bestehen und prüft Änderungen zwischen UI-Prüfung und Schreibvorgang zusätzlich. Das Aufheben bestehender Links wurde in diesem Schritt nicht verändert.

Zehn neue Testfälle prüfen Ablehnung ohne Kandidaten-/Review-Schreibzugriffe und die anschließende erfolgreiche Bestätigung nach erneutem Öffnen. Der bestehende Test zur getrennten Provider-/Verbraucher-Key-Zuordnung öffnet den Dialog nun nach seiner absichtlichen Provider-Änderung erneut. Im Benutzerwechsel-Test wird vor dem anschließenden erfolgreichen Speichern wieder der tatsächliche Fixture-Benutzer angemeldet; der simulierte fremde Benutzer besitzt keinen Datenbankdatensatz für den Review-Fremdschlüssel.

Keine Produktionsdaten geändert; kein Scan, Resolver, Export oder Migration ausgeführt.

Verifikation: vollständige isolierte Workbench-Suite **474 Tests bestanden, 2898 Assertions**. PHP-Syntaxprüfungen und `git diff --check` erfolgreich. Die Prüfung ist automatisiert auf Komponenten-/Workflow-Ebene erfolgt; ein manueller Browserdurchlauf wurde nicht durchgeführt.

## 16.10.2026 – Quellenverknüpfungen kontrolliert aufheben

Auch `unlinkDynamicSourceLink()` prüft jetzt vor dem Writer-Aufruf den authentifizierten Nachweis der geöffneten Vorschau. Dieser umfasst zusätzlich die einzelnen bestehenden Kandidaten (Provider-ID, Eigentümer, Status, Review-Status und Werte-Snapshot). Damit wird eine zwischenzeitliche Änderung der zweiten Verknüpfung ebenfalls erkannt; der bisherige zusammengefasste erste Review-Status genügte dafür nicht.

`TranslationWorkbenchDynamicSourceLinkWriter::unlink()` prüft innerhalb seiner Transaktion die aktuelle Verbraucherzuordnung und liest Verbraucher-/Providerquellen einschließlich ihrer Werte erneut mit Sperren. Die betroffenen aktiven Kandidaten werden ebenfalls gesperrt und vor jeglicher Änderung auf die aktuelle Verbraucher-Key-/Finding-Zuordnung geprüft. Eine abweichende Zuordnung führt zum Abbruch statt zum stillen Aufheben fremder oder veralteter Verknüpfungen. Historische Bereinigungen sind weiterhin ein gesonderter Vorgang; dieser UI-Schreibweg setzt einen gültigen aktuellen Kontext voraus.

Die vorhandene Semantik bleibt bestehen: Aufheben setzt die ausgewählten aktiven Links auf `inactive`/`unlinked`, löscht keine Datensätze und verändert weder Übersetzungen noch Quellwerte. Andere Verbraucherlinks bleiben erhalten. Ohne aktive Verknüpfung entsteht kein zusätzlicher Review. Review und Timeline-Ereignis bleiben Teil derselben Transaktion und werden bei Fehlern gemeinsam zurückgerollt.

Sieben neue Regressionen prüfen veränderte Werte, den Status der zweiten Verknüpfung, eine geänderte Zielquelle und gefälschte Nachweise sowie Änderungen zwischen Vorschau und direktem Writer-Aufruf (Provider obsolet, Verbraucherwerte verändert, Kandidaten-Eigentümer verändert). Ablehnungen lassen Kandidaten, Reviews und Timeline unverändert. Nach erneutem Öffnen kann ein gültiger geänderter Stand aufgehoben werden.

Kein Produktionsschreibzugriff und kein Scan-/Resolver-/Exportlauf. Die Browserdarstellung wurde nicht manuell geprüft.

Verifikation: vollständige isolierte Workbench-Suite **481 Tests bestanden, 2930 Assertions**. Nach der ergänzten Absicherung der Vorschau gegen eine noch fehlende Kandidatentabelle erneut die sieben neuen Regressionen ausgeführt: **7 Tests, 32 Assertions**. PHP-Syntax und `git diff --check` erfolgreich.

## 16.10.2026 – Verknüpfungsstand innerhalb der Schreibtransaktion prüfen

Zwischen der Livewire-Vorschauprüfung und dem Writer-Aufruf bestand noch eine Lücke: Der Writer prüfte Quellen erneut, verglich vorhandene Kandidaten jedoch nicht mehr mit dem geprüften Verknüpfungsstand. Eine unmittelbar dazwischen erfolgte Status-/Snapshotänderung hätte dadurch überschrieben werden können.

`confirm()` und `unlink()` verlangen jetzt verbindlich einen vierten Parameter `expectedLinks`. Die Livewire-Aktionen übergeben den durch den authentifizierten Vorschau-Nachweis geprüften Stand. Ein gemeinsamer `linkSnapshot()`-Lesepfad definiert die Felder und ihre Reihenfolge sowohl für die Vorschau als auch für den transaktionalen Vergleich. Innerhalb der Transaktion werden nach den Quellensperren die Kandidaten gesperrt und verglichen, bevor der erste Kandidat geschrieben wird. Auch ein ursprünglich leerer Stand ist eine ausdrückliche Erwartung und wird nicht als fehlende Prüfung behandelt.

Bei einer Abweichung wird eine sichtbare Validierungsmeldung ausgelöst. Es entstehen keine Teilbestätigungen, Aufhebungen, Reviews oder Timeline-Ereignisse. Die API besitzt keinen optionalen Parameter zum Umgehen des Vergleichs; alle vorhandenen Aufrufer wurden angepasst.

Zehn neue Regressionen simulieren Änderungen nach der Vorschauerstellung unmittelbar vor beiden Writer-Aktionen: Review-Status, Werte-Snapshot, Entfernung, Eigentümerzuordnung und neu hinzugekommene Verknüpfungen. Sie prüfen insbesondere, dass eine Änderung des zweiten Providers nicht schon zu einem Schreiben am ersten Provider führt. Diese Tests prüfen deterministisch veraltete Vorschauen; sie sind kein Parallelitätstest mit zwei PostgreSQL-Verbindungen. Fremde Schreibwege, die Quellensperren nicht beachten, sind damit nicht pauschal abgesichert.

Keine Produktionsdaten verändert und keine Scan-, Resolver- oder Exportbefehle ausgeführt.

Verifikation: vollständige isolierte Workbench-Suite **491 Tests bestanden, 2980 Assertions**. PHP-Syntaxprüfungen und `git diff --check` erfolgreich. Kein manueller Browserdurchlauf.

## 16.10.2026 – Echter PostgreSQL-Paralleltest für Quellenverknüpfungen

Der bisher offene Paralleltest ist jetzt als expliziter Integrationslauf vorhanden:

```bash
php tests/Integration/TranslationWorkbench/source-link-concurrency.php
```

Voraussetzungen: PostgreSQL-Binaries (standardmäßig `/usr/lib/postgresql/18/bin`, alternativ `TW_TEST_POSTGRES_BIN`), `pdo_pgsql` und Erlaubnis zum Starten eines lokalen Unix-Socket-Servers. Der Runner erstellt ein privates zufälliges `/tmp/tw-source-links-*`-Verzeichnis, initialisiert einen eigenen Cluster und eine eigene Datenbank, deaktiviert TCP-Zugriffe und verwendet ausschließlich synthetische Fixtures. Die Workbench-Tabellen entstehen aus den tatsächlichen Paketmigrationen. Produktive Verbindungsdaten werden nicht für die Tests benutzt. Der Server wird abschließend gestoppt und das erzeugte Verzeichnis entfernt. Ein Fehler beim Serverstart gibt dessen Log vor der Bereinigung aus.

Zwei getrennte PHP-Prozesse verwenden den echten `TranslationWorkbenchDynamicSourceLinkWriter` einschließlich Reviews und Timeline-Recorder. Der erste hält nach dem Writer-Aufruf eine äußere Transaktion offen. Der zweite startet mit derselben ursprünglichen Vorschau. Der Test beobachtet über `pg_stat_activity` ausdrücklich `wait_event_type = Lock`, bevor die erste Transaktion beendet wird; ein bloßes zeitliches Nacheinander genügt nicht als Erfolg.

Vier Szenarien erfolgreich auf PostgreSQL 18 ausgeführt:

| Aktion | Erste Transaktion | Zweiter Writer | Ergebnis |
| --- | --- | --- | --- |
| Bestätigen | Commit | veraltete Vorschau abgewiesen | bestanden |
| Aufheben | Commit | veraltete Vorschau abgewiesen | bestanden |
| Bestätigen | Rollback | unverändert gültige Vorschau akzeptiert | bestanden |
| Aufheben | Rollback | unverändert gültige Vorschau akzeptiert | bestanden |

In allen Fällen bleiben genau zwei Kandidaten mit dem erwarteten Endstatus erhalten; insgesamt kommt genau ein Review und ein Timeline-Ereignis hinzu. Damit sind Sperrverhalten, Ablehnung nach konkurrierendem Commit und Weiterarbeit nach Rollback für diesen Writer nachgewiesen. Nicht geprüft sind beliebige externe Schreibwege, die das Sperrprotokoll nicht verwenden.

Der erste Start war innerhalb der Sandbox nicht möglich. Der anschließend genehmigte isolierte Lauf außerhalb der Sandbox war erfolgreich. Kein produktiver Scan, Resolver, Export oder Datenbank-Schreibzugriff wurde ausgeführt. Produktivcode musste für diesen Test nicht geändert werden. PHP-Syntax und `git diff --check` erfolgreich. Die zuletzt grüne isolierte Unit-Suite (491 Tests, 2980 Assertions) wurde für diesen ausschließlich neuen Integrationstest nicht nochmals unverändert ausgeführt. Der Integrationslauf ist bewusst explizit und gehört nicht automatisch zur SQLite-Unit-Suite.

## 16.10.2026 – Automatische Runtime-Vorschläge gegen parallele Reviews absichern

Die nächste Prüfung des automatischen Schreibwegs zeigte: `ResolveUnknownDynamicSources::syncRuntimeSourceLink()` schützte bereits bestehende Entscheidungen durch einen Statusvergleich, führte Lesen, Schreiben und Timeline-Erfassung aber nicht unter einer gemeinsamen Transaktion/Sperre aus. Ein parallel abgeschlossener manueller Review konnte zwischen Lesen und Aktualisieren stattfinden.

Der Vorschlagsweg sperrt jetzt innerhalb einer Transaktion zuerst die Verbraucherquelle, dann den Runtime-Provider und anschließend die vorhandenen Kandidaten. Diese Reihenfolge ist mit dem manuellen Review vereinbar. Nach dem Warten werden Herkunft/Zuordnung, Typ, Scope und Status der Quellen nochmals mit dem ausgewählten Kontext verglichen; obsolete oder zwischenzeitlich geänderte Quellen brechen den Vorgang ab. Erst danach wird die bestehende Entscheidung erneut gelesen. Nur aktive offene Vorschläge können aktualisiert werden. Bestätigte, abgelehnte, aufgehobene oder stillgelegte Verknüpfungen bleiben unverändert. Die dadurch nicht mehr erreichbare zusätzliche Sonderbehandlung eines einzelnen `review_status`-Feldes wurde entfernt.

Vorschlag und Timeline-Ereignis sind nun atomar: Scheitert die Historie, werden auch neue oder aktualisierte Kandidaten zurückgerollt. Vier neue Unit-Regressionen prüfen Rollback bei Neuanlage/Aktualisierung und zwischen Auswahl und Schreiben obsolet gewordene Verbraucher/Provider.

Der echte PostgreSQL-Paralleltest wurde um zwei Szenarien ergänzt: automatischer Resolver wartet auf manuelle Bestätigung beziehungsweise Aufhebung. Beide Fälle beobachten eine reale Datenbanksperre und prüfen danach unveränderte Kandidateninhalte sowie genau ein Review/Timeline-Ereignis insgesamt. Der Test ruft nur den begrenzten Vorschlagsweg auf synthetischen Fixtures auf; kein globaler Resolverlauf findet statt.

Diese Änderung betrifft den Runtime-Verknüpfungsvorschlag. Sie ist keine pauschale Freigabe aller übrigen Discovery-/Klassifizierungs-Schreibwege und ändert nicht die fachliche Quellenpriorität. Produktionsdaten unverändert.

Verifikation: **495 Unit-/Workflow-Tests bestanden, 2996 Assertions**, dazu **6 PostgreSQL-Parallelfälle bestanden** mit beobachteten Sperren und überprüften Kandidateninhalten. PHP-Syntax und `git diff --check` erfolgreich. Die temporären PostgreSQL-Instanzen wurden gestoppt und entfernt; kein manueller Browserdurchlauf.

## 16.10.2026 – Discovery-Auflösung pro Quelle atomar speichern

Der zweite Resolver-Schreibweg übernahm konkrete Discovery-Optionen bislang in getrennten Schritten: Quellenattribute mit Timeline-Ereignis, einzelne Optionswerte, anschließend der strukturierte Bearbeitungsstatus. Ein Fehler in einem späteren Schritt konnte einen teilweise übernommenen Zustand hinterlassen.

`ResolveUnknownDynamicSources::resolveDiscoverySource()` fasst diese Schritte nun in einer Transaktion pro ausgewählter Quelle zusammen. Quelle und Discovery werden gesperrt und ihre gespeicherten Felder mit dem ausgewählten Stand verglichen. Gelöschte, obsolete oder zwischenzeitlich veränderte Datensätze werden abgewiesen. Hinzugefügte Query-Aliase werden beim Vergleich nicht als persistierte Felder behandelt. Auch vorhandene Optionswerte werden vor ihrer Aktualisierung gesperrt. Die Ergebniszähler werden erst nach erfolgreichem Abschluss in die Command-Summary übernommen.

Die fachliche Matching-Regel und die Auswahl der Optionswerte wurden nicht geändert. Ebenso bleibt die Transaktionsgrenze ausdrücklich pro Quelle: Bereits erfolgreich abgeschlossene andere Quellen bleiben erhalten, falls eine spätere Quelle fehlschlägt. Der Dry-run führt weiterhin keine Schreibschritte aus. Dies ist keine Aussage über die vollständige Isolation sämtlicher konkurrierender Änderungen an Finding-, Datei- oder Key-Zuordnungen durch andere Dienste.

Acht Regressionen prüfen den echten Command-Erfolg mit Wiederholung ohne weitere Änderungen, Dry-run ohne Seiteneffekte, Rollback bei fehlgeschlagener Timeline-Erfassung, fehlgeschlagenem zweiten Optionswert (sowohl bei Neuanlage als auch nach Aktualisierung eines vorhandenen Wertes), Fehler beim strukturierten Finding-Status sowie vor dem Schreiben veränderte Quelle/Discovery. Der Vergleich umfasst Quellen, Werte, Findings, Keys, Timeline und gespeicherte Übersetzungen.

Kein produktiver Scan-/Resolverlauf, keine produktiven Datenänderungen. Die Tests verwenden ausschließlich isolierte Fixtures.

Verifikation: vollständige isolierte Workbench-Suite **503 Tests bestanden, 3019 Assertions**. PHP-Syntax und `git diff --check` erfolgreich. Für diesen Discovery-Schreibweg wurde in diesem Schritt kein eigener PostgreSQL-Paralleltest und kein manueller Browserdurchlauf ausgeführt.

## 16.10.2026 – Discovery-Auflösung unter echter PostgreSQL-Konkurrenz

Der bestehende isolierte Runner `tests/Integration/TranslationWorkbench/source-link-concurrency.php` enthält jetzt zusätzlich vier Discovery-Szenarien. Die Prüfung des tatsächlichen Wartens auf eine PostgreSQL-Sperre wurde für die bisherigen und neuen Fälle gemeinsam genutzt. Der neue Worker ruft den echten `resolveDiscoverySource()`-Schreibweg mit einem zuvor gelesenen Quellen-/Discovery-Stand auf.

| Erster Vorgang | Abschluss | Wartender Resolver | Ergebnis |
| --- | --- | --- | --- |
| Quelle auflösen | Commit | veralteten Quellenstand zurückweisen | bestanden |
| Quelle auflösen | Rollback | ursprünglichen Stand erfolgreich auflösen | bestanden |
| Discovery-Optionen ändern | Commit | veralteten Discovery-Stand zurückweisen | bestanden |
| Discovery-Optionen ändern | Rollback | unveränderte Discovery erfolgreich übernehmen | bestanden |

Jeder Fall beobachtet `wait_event_type = Lock`, bevor die erste Transaktion beendet wird. Nach einem Commit werden Quellenattribute, Optionswerte, Finding und Key vollständig gegen den Stand des ersten Prozesses verglichen. Nach erfolgreicher Auflösung werden Klassifizierung, beide konkreten Optionswerte und die strukturierten Finding-/Key-Zustände geprüft. Es entsteht genau ein Auflösungsereignis; bei ausschließlich geänderter Discovery mit abgewiesenem Resolver keines. Manuelle Reviews werden nicht angelegt. Eine bereits gespeicherte deutsche Übersetzung wird in allen vier Fällen unverändert erhalten.

Verifikation: **alle 10 PostgreSQL-Parallelfälle bestanden** (sechs vorhandene und vier neue). PHP-Syntax und `git diff --check` erfolgreich. Produktivcode wurde nicht geändert; die zuletzt vollständig grüne Unit-Suite bleibt bei **503 Tests / 3019 Assertions** und wurde für diese reine Integrationstest-Erweiterung nicht unverändert wiederholt. Der temporäre Cluster wurde gestoppt und entfernt. Keine produktiven Datenbank-Schreibzugriffe, kein globaler Resolver-/Scanlauf und kein Browserdurchlauf.

Die Ergebnisse belegen die geprüften Quellen-/Discovery-Schreibkonflikte. Änderungen durch andere Dienste an Finding-, Datei- oder Key-Zuordnungen sind damit nicht automatisch vollständig geprüft.

## 16.10.2026 – Aktive Eigentümerzuordnung während der Resolver-Transaktion prüfen

Die ausgewählte Quellenzeile konnte bislang unverändert sein, während Finding, Quelldatei oder Key-Zuordnung inzwischen stillgelegt beziehungsweise umgehängt wurden. Die vorgeschaltete Konfliktprüfung allein schließt dieses Zeitfenster nicht.

`TranslationWorkbenchDynamicKeyAssignment::lockCurrentSourceOwner()` ergänzt jetzt eine gemeinsame transaktionale Prüfung für beide Resolver-Schreibwege: Discovery-Auflösung und Runtime-Verknüpfungsvorschlag. Der Aufrufer muss die Quellenzeile bereits gesperrt haben. Anschließend werden Finding, Quelldatei, aktive Key-Verknüpfungen und deren Keys gesperrt und geprüft. Finding und Datei müssen aktiv sein, es muss genau ein nicht obsoleter verknüpfter Key vorhanden sein, und eine explizite Key-ID der Quelle muss dazu passen. Fehlende oder konkurrierende Zuordnungen werden abgewiesen.

`unknownSources()` liefert zusätzlich die ausgewählte Key-ID und Quelldatei-ID mit. Damit erkennt die Prüfung auch einen Wechsel der aufgelösten Zuordnung beziehungsweise der Datei zwischen Auswahl und Schreiben. Der ausgewählte Dateipfad wird ebenfalls abgeglichen. Bestehende fehlende explizite Quellen-Key-IDs werden nicht stillschweigend neu gesetzt. Die automatische Auflösung darf aber nur mit einer eindeutigen aktiven Zuordnung weiterarbeiten.

Beim Runtime-Vorschlag wurde die Sperrreihenfolge an den manuellen Review angeglichen: Verbraucherquelle, Eigentümerkontext, Provider, Kandidaten. Die gemeinsame Prüfung enthält keine datasetbezogenen Sonderfälle. Unveränderte gültige Kontexte werden weiterhin verarbeitet; Übersetzungen werden nicht verändert.

Sieben neue Unit-Szenarien prüfen beide Schreibwege jeweils nach Stilllegung von Finding, Datei, Key oder Key-Link, nach Änderung des Dateipfads, nach Ersetzung des Keys und bei konkurrierenden aktiven Keys. Sie vergleichen Quellen, Werte, Findings, Keys, Timeline, Übersetzungen und Kandidaten auf unveränderten Bestand nach Ablehnung.

Acht zusätzliche echte PostgreSQL-Parallelfälle prüfen Stilllegung von Finding, Datei, Key und Key-Link jeweils mit Commit und Rollback. Der zweite Resolver wartet nachweislich auf eine Datenbanksperre. Nach Commit erfolgt eine Ablehnung ohne Auflösungsereignis; nach Rollback wird vollständig aufgelöst. Die Fälle verwenden eigene synthetische Findings/Dateien/Keys im temporären Cluster. Insgesamt bestehen alle 18 Parallelfälle. Das ist ein Nachweis für diese geprüften Änderungen, keine pauschale Aussage über sämtliche externen Schreibwege.

Keine produktiven Datenänderungen, kein globaler Scan-/Resolver-/Exportlauf und kein manueller Browserdurchlauf.

Verifikation: vollständige isolierte Workbench-Suite **510 Tests bestanden, 3054 Assertions**, zusätzlich **18 PostgreSQL-Parallelfälle bestanden**. PHP-Syntax und `git diff --check` erfolgreich. Der temporäre PostgreSQL-Cluster wurde gestoppt und entfernt.

## 16.10.2026 – Konkrete Optionswertänderungen des Klassifizierers protokollieren

Die Bestandsprüfung von `ClassifyDynamicValues` bestätigt: Der schreibende Command läuft bereits in einer Transaktion für den gesamten Klassifizierungslauf. Bestehende Regressionen sichern Rollback bei uneindeutiger Quellenidentität und fehlgeschlagenen Wertänderungen ab. Eine zweite, abweichende Transaktionsstruktur wurde deshalb nicht eingeführt.

Eine konkrete Lücke lag in der Nachvollziehbarkeit: Die bisherigen Klassifizierungsereignisse protokollieren Quellenattribute, aber nicht zuverlässig die einzelnen geänderten Optionswerte. Bei gleicher Anzahl konnten so andere native Texte übernommen werden, ohne ihre Vorher-/Nachher-Werte im Klassifizierungsereignis zu sehen.

`syncDynamicSourceValues()` schreibt jetzt zusätzlich genau ein `dynamic_source_options_reconciled`-Timeline-Ereignis pro tatsächlich verändertem Optionsbestand einer Quelle. `old_values.values` und `new_values.values` enthalten die betroffenen `value_key`-Einträge mit nativer Beschriftung, Herkunft, Translation-Key-Referenz, Status und Metadaten. `null` bezeichnet eine vorher nicht vorhandene beziehungsweise anschließend entfernte Option. Unveränderte Einträge werden nicht erneut protokolliert. Der Kontext enthält `dynamic_source_id` und `option_discovery_id` sowie die vorhandenen Datei-/Zeilenangaben. Das Ereignis entsteht über den zentralen Timeline-Recorder und wird im Summary-Zähler mitgezählt.

Vorhandene oder zu entfernende Optionszeilen werden beim Lesen gesperrt. Die bisherige Abgleichregel bleibt erhalten: Bei einer konkreten nichtleeren Optionsbeobachtung werden entfallene Quelloptionen entfernt; ihre vorherigen Werte sind nun im selben atomaren Vorgang protokolliert. Ungelöste/leere Beobachtungen bewahren weiterhin den letzten bekannten Bestand. Gespeicherte Übersetzungen und bestätigte Kandidaten-Snapshots werden nicht geändert. Das ist keine neue Freigabe zum Löschen von Übersetzungen oder historischer Reviews.

Zwei neue Regressionen decken Neuanlage, Änderung, Hinzufügen, Entfernen, Wiederholung ohne weiteres Werte-Ereignis, ungelöste Beobachtungen, schreibfreien Dry-run und den vollständigen Rollback bei gezielt fehlgeschlagenem Werte-Historieneintrag ab. Die bestehenden Übersetzungen und bestätigten Kandidaten werden auf unveränderte Inhalte geprüft.

Keine Produktionsdaten geändert und kein produktiver Classifier-/Scan-/Resolverlauf. Die weitergehende Konkurrenzprüfung des Klassifizierers bezüglich seiner ausgewählten Finding-/Discovery-Kontexte bleibt ein gesonderter Prüfpunkt; dieser Schritt betrifft die konkrete Wertehistorie.

Verifikation: vollständige isolierte Workbench-Suite **512 Tests bestanden, 3082 Assertions**. PHP-Syntax und `git diff --check` erfolgreich. Kein manueller Browserdurchlauf und kein zusätzlicher PostgreSQL-Paralleltest in diesem Schritt.

## 16.10.2026 – Stilllegung auf den unveränderten Anfangsbestand begrenzen

`ClassifyDynamicValues` erfasst vor der Finding-Auswahl die nicht obsoleten, nicht zur Runtime gehörenden Quellen. Die abschließende Stilllegung betrachtet ausschließlich diese Anfangsmenge. Später angelegte Quellen werden nicht versehentlich als ungesehen stillgelegt. Erfolgreich klassifizierte Quellen werden anhand ihrer Datenbank-ID ausgenommen; eine zulässige Fingerprint-Änderung innerhalb desselben Laufs führt deshalb nicht zur Stilllegung oder falschen Konfliktmeldung.

Die verbleibenden Kandidaten werden in ID-Reihenfolge mit `lockForUpdate()` erneut gelesen. Fehlt eine Quelle oder unterscheidet sich ihr gespeicherter Zustand vom Anfangsbestand, wird eine `ValidationException` mit der Quellen-ID ausgelöst. Die bestehende Gesamttransaktion rollt damit auch vorausgegangene Klassifizierungsänderungen zurück. Die Prüfung verlangt explizit eine laufende Transaktion. Runtime-Quellen bleiben weiterhin ausgenommen. Matching, Optionswerte und Übersetzungen erhalten keine neuen fachlichen Regeln.

Fünf isolierte Regressionen prüfen unveränderte, neue, veränderte, gelöschte und im selben Lauf mit neuem Fingerprint verarbeitete Quellen, einschließlich Rollback vorheriger Schreibvorgänge. Zwei zusätzliche Command-Tests injizieren nach der Bestandsaufnahme eine Änderung beziehungsweise Neuanlage und überprüfen die tatsächliche Einbindung im Artisan-Ablauf. Diese deterministischen Zwischenänderungen sind kein PostgreSQL-Parallelitätsnachweis.

Verifikation: vollständige Workbench-Suite vor Hinzufügen der beiden Command-Tests **517 Tests / 3111 Assertions bestanden**; anschließend beide zusätzlichen Command-Tests **2 Tests / 10 Assertions bestanden**. PHP-Syntax und `git diff --check` erfolgreich. Keine Produktionsdaten verändert, kein produktiver Scanner-/Classifier-/Exportlauf.

Abgrenzung: Damit ist die Stilllegung gegenüber Änderungen an ihren Quellenzeilen abgesichert. Eine Änderung allein am Finding-, Key-Link- oder Discovery-Kontext ohne Änderung der Quellenzeile wird dadurch noch nicht erkannt. Die vollständige Absicherung der zuvor gelesenen Klassifizierungseingaben bleibt offen; dieser Schritt stellt keine pauschale Konkurrenzsicherheit des gesamten Klassifizierers her.

## 16.10.2026 – Ausgewählte Klassifizierungseingaben vor dem Schreiben erneut prüfen

`ClassifyDynamicValues::lockClassificationInput()` prüft nun pro ausgewähltem Finding vor dem ersten Quellen-Schreibzugriff den zuvor gelesenen Eingabestand. Bestehende Nicht-Runtime-Quellen einschließlich Quellen ohne `source_type`, Finding, Quelldatei, Key-Verknüpfungen und deren Keys werden gesperrt. Die Reihenfolge beginnt wie beim Quellenreview mit Quelle → Finding → Datei → Links → Keys. Danach werden die Finding-Auswahl und die Prüfung konkurrierender aktiver Keys erneut ausgeführt. Die passende Discovery-Auswahl wird unter Zeilensperren wiederholt und ihr Klassifizierungsergebnis verglichen. Eine Abweichung führt zur ValidationException mit Finding-ID und zum Rollback der bestehenden Gesamttransaktion.

Die Finding-Auswahl liefert dafür zusätzlich die Quelldatei-ID und kann auf eine einzelne Finding-ID eingeschränkt werden. Die fachlichen Matching-Regeln bleiben erhalten. Insbesondere bleiben Findings ohne zugewiesenen Key zulässig. Es wurde keine Resolver-Regel übernommen, die für diese Klassifizierung fälschlich zwingend genau einen Key verlangen würde. Dry-run bleibt schreibfrei und benötigt diese Sperren nicht.

Elf neue isolierte Fälle prüfen Stilllegung von Finding, Datei, Key und Link, Änderung von Ausdruck, Dateipfad und Optionsinhalt, obsolete Discovery, mehrdeutige Key-Zuordnung sowie unveränderte Eingaben mit und ohne Key. Ein zusätzlicher Artisan-Test verändert die Eingaben gezielt zwischen Auswahl und Sperrprüfung und weist den Rollback auch bereits erfolgter transaktionaler Wertänderungen nach.

Der PostgreSQL-Integrationstest wurde um zehn echte Parallelfälle ergänzt: Finding, Datei, Key, Link und Discovery jeweils mit Commit und Rollback der anderen Verbindung. Die zweite Verbindung wartet nachweislich auf die Sperre. Commit führt zur Ablehnung veralteter Eingaben; Rollback erlaubt die Validierung. Diese Fälle testen den neuen Eingabe-Guard, nicht einen vollständigen parallel ausgeführten globalen Klassifizierungslauf. Insgesamt bestehen alle **28 PostgreSQL-Parallelfälle**. Der temporäre Cluster wurde gestoppt und entfernt.

Verifikation: vollständige Workbench-Suite **530 Tests / 3161 Assertions bestanden**, danach der zusätzliche Artisan-Rollbacktest **1 Test / 5 Assertions bestanden**. PHP-Syntax und `git diff --check` erfolgreich. Keine Produktionsdaten verändert und kein produktiver Scan-/Classifier-/Exportlauf.

Verbleibende Abgrenzung: Geprüft werden ausgewählte Findings und passende bestehende Discoveries. Das ist keine globale Serialisierung sämtlicher Scanner-/Discovery-Inserts. Insbesondere bleibt die gesonderte Prüfung von Eigentümer-/Discovery-Änderungen bei ausschließlich zur Stilllegung vorgesehenen, nicht ausgewählten Quellen offen; deren Quellenzeile selbst ist bereits abgesichert.

## 16.10.2026 – Eigentümerkontext auch vor der Stilllegung prüfen

Die anfängliche Quellenaufnahme in `ClassifyDynamicValues` enthält jetzt zusätzlich den zugehörigen Kontext: Finding, Quelldatei, sämtliche Key-Verknüpfungen des Findings, deren Keys einschließlich des direkt an der Quelle hinterlegten Keys und die direkt zugeordnete Discovery. Vor der Stilllegung wird dieser Kontext unter Zeilensperren erneut gelesen und mit der Aufnahme verglichen. Eine Änderung oder Löschung führt zur bestehenden ValidationException mit Quellen-ID und zum Rollback des Klassifizierungslaufs. Bereits bei der Aufnahme inaktive Kontexte bleiben zulässig: unveränderte Inaktivität kann gerade der legitime Grund für die Stilllegung sein.

`created_at`/`updated_at` werden beim Kontextvergleich ausgenommen. Die eigenen `dynamic_data_state`-Schreibvorgänge werden dagegen nicht pauschal ignoriert: `retainOwnClassificationState()` übernimmt ausschließlich die vom Klassifizierer tatsächlich geschriebenen Finding-/Key-Zustände in den erwarteten Kontext. Andere Änderungen werden dadurch nicht legitimiert. Quellenattribute, Übersetzungen, Wertabgleich und fachliche Matching-Regeln erhalten keine neuen Sonderregeln.

Neun Regressionen prüfen Reaktivierung, Ausdruck, Dateipfad, Key, Link, geänderte/gelöschte Discovery, extern geänderten Klassifizierungszustand sowie unveränderte Inaktivität. Zwei zusätzliche Fälle prüfen eigene Zustandsänderungen mit und ohne zusätzliche unerwartete Kontextänderung. Zwei ältere Testfälle mussten präzisiert werden: Sie hatten auch im angeblich unveränderten Erfolgsfall einen Key-Zustand verändert. Diese absichtliche Änderung bleibt nun auf die Rollbackfälle beschränkt; die neue Prüfung wird nicht dafür abgeschwächt.

Zehn weitere echte PostgreSQL-Parallelfälle prüfen Finding, Datei, Key, Link und Discovery jeweils bei Commit und Rollback. Nach nachgewiesener Sperrwartezeit wird bei Commit die Stilllegung abgelehnt und die Quelle bleibt aktiv. Bei Rollback wird die unveränderte Quelle stillgelegt. Zusammen mit den bestehenden Fällen sind **38 Parallelfälle bestanden**. Der temporäre Cluster wurde gestoppt und entfernt.

Verifikation: vollständige Workbench-Suite **540 Tests / 3201 Assertions bestanden**, anschließend separat die zwei zusätzlichen Eigenzustandsfälle **2 Tests / 8 Assertions bestanden**. PHP-Syntax und `git diff --check` erfolgreich. Keine Produktionsdaten verändert, kein produktiver Scanner-/Classifier-/Exportlauf.

Damit ist die zuletzt offene Prüfung des bestehenden Eigentümerkontexts für Stilllegungskandidaten ergänzt. Die Prüfungen bilden weiterhin keine globale Serialisierung sämtlicher neu eingefügter Scanner-/Discovery-Datensätze. Insbesondere umfasst der Stilllegungskontext die direkt zugeordnete Discovery, nicht jede künftig mögliche neue Matching-Beobachtung.

## 16.10.2026 – Neue Discoveries während der Klassifizierung ausschließen

Ein erneutes Lesen unter Zeilensperren verhindert keine späteren Inserts: Eine noch nicht vorhandene passende Discovery kann nicht mit `FOR UPDATE` gesperrt werden. `ClassifyDynamicValues::lockDiscoveryInventory()` sichert deshalb beim schreibenden PostgreSQL-Lauf die Discovery-Tabelle mit `LOCK TABLE ... IN SHARE MODE`, bevor Quellenaufnahme und Finding-Auswahl beginnen. Die Sperre gehört zur bestehenden Gesamttransaktion und endet mit Commit oder Rollback. Sie erfasst auch neu hinzukommende oder durch Updates neu passend werdende Discoveries.

Normale Leser bleiben möglich. Discovery-Inserts, -Updates und -Deletes anderer Transaktionen warten während dieses Klassifizierungslaufs; umgekehrt wartet die Klassifizierung auf bereits laufende Discovery-Schreibtransaktionen. Es werden keine neuen Zuordnungsregeln, Datenbanktabellen oder Writer-Konventionen eingeführt. Die native PostgreSQL-Sperre erfasst auch direkte Schreibzugriffe. Dry-run nimmt diese Sperre nicht. Die Implementierung ist ausdrücklich PostgreSQL-spezifisch; die SQLite-Unit-Suite ist kein Beleg für paralleles Verhalten anderer Datenbanktreiber.

Acht zusätzliche PostgreSQL-Parallelfälle prüfen Insert, Update und Delete jeweils bei Commit und Rollback der Klassifizierungssperre sowie einen bereits laufenden Insert jeweils bei Commit und Rollback des Writers. Die Wartezeit wird über den PostgreSQL-Sperrzustand nachgewiesen. Während der Sperre bleibt der Discovery-Bestand identisch; anschließend wird der wartende Schreibvorgang durchgeführt. Bei umgekehrter Reihenfolge sieht der Leser den abgeschlossenen Transaktionsstand. Zusammen mit den bisherigen Fällen bestehen **46 Parallelfälle**; der temporäre Cluster wurde gestoppt und entfernt.

Die Sperre betrifft bewusst die gesamte Discovery-Tabelle für die Dauer des schreibenden Klassifizierungslaufs. Sie ist keine globale Sperre aller Workbench-Tabellen und kein Nachweis, dass sämtliche Scanner-/UI-/Exportabläufe bereits vollständig abgedeckt sind. Keine Produktionsdaten geändert, kein produktiver Scan-/Classifier-/Exportlauf.

Verifikation: vollständige Workbench-Suite **542 Tests / 3209 Assertions bestanden**, zusätzlich **46 PostgreSQL-Parallelfälle bestanden**. PHP-Syntax und `git diff --check` erfolgreich.

## 16.10.2026 – Unbekannte Quellen berücksichtigen und Stilllegung protokollieren

Die fachliche Prüfung des Stilllegungswegs ergab einen SQL-NULL-Fehler: `unknownClassification()` legt Quellen mit `source_type = NULL` an. Der bisherige `whereNotIn(source_type, runtimeTypes)`-Filter schloss diese Zeilen ebenfalls aus. Die Anfangsaufnahme berücksichtigt jetzt ausdrücklich NULL-Typen neben den übrigen Nicht-Runtime-Quellen. Eine weiterhin klassifizierte unbekannte Quelle bleibt über ihre ID ausgenommen. Entfällt ihre Fundstelle oder ersetzt eine konkrete Discovery-Quelle diese bisherige unbekannte Quelle, kann diese regulär stillgelegt werden. `runtime_options` und `runtime_db_options` bleiben geschützt.

Die Stilllegung schreibt nun pro tatsächlich stillgelegter Quelle ein `dynamic_source_obsoleted`-Ereignis über den zentralen Timeline-Recorder. Vorher-/Nachher-Status sowie Quellen-ID, Discovery-ID, Fingerprint, Datei/Zeile und der konkrete technische Anlass `not_classified_in_current_pass` werden festgehalten. Der Anlass behauptet keine Quellcode-Löschung: Er gilt auch beim Ersatz einer unbekannten Quelle durch eine konkrete Klassifizierung. Statusänderung und Historie liegen in derselben Gesamttransaktion. Schlägt die Historie fehl, wird auch die Stilllegung samt vorherigen Klassifizierungsschritten zurückgerollt. Der Summary-Zähler berücksichtigt die zusätzlichen Timeline-Ereignisse.

Vier neue Command-Regressionen prüfen entfallene Findings und den Übergang unbekannt → Discovery jeweils mit erfolgreicher und absichtlich fehlgeschlagener Historie. Sie prüfen außerdem Dry-run, weiterhin ausgewählte unbekannte Quellen, Wiederholung ohne doppeltes Stilllegungsereignis, Runtime-Ausnahmen sowie unveränderte Übersetzungen und bestätigte Kandidaten. Die bestehenden PostgreSQL-Stilllegungstests prüfen zusätzlich die korrekte Anzahl und Quellenzuordnung der Historieneinträge. Alle **46 Parallelfälle** bestehen weiterhin; der temporäre Cluster wurde gestoppt und entfernt.

Keine produktiven Daten geändert und kein produktiver Scanner-/Classifier-/Exportlauf. Die NULL-Korrektur kann bei einem späteren ausdrücklich gestarteten produktiven Klassifizierungslauf bislang übergangene unbekannte Quellen regulär stilllegen; genau diese Verhaltensänderung wird durch die neuen Tests und Ereignisse nachvollziehbar gemacht.

Verifikation: vollständige Workbench-Suite **546 Tests / 3274 Assertions bestanden**, zusätzlich **46 PostgreSQL-Parallelfälle bestanden**. PHP-Syntax und `git diff --check` erfolgreich.

## 16.10.2026 – Klassifizierungs-Rollback bis zum Pipeline-Fehlerbericht nachweisen

`RunTranslationWorkbench` beendet die Schrittkette bereits bei einer Exception oder einem von null verschiedenen Rückgabecode. Für die neu abgesicherten Klassifizierungsfehler war deshalb keine neue Ablaufsteuerung nötig. Die Prüfung fand jedoch eine Berichtslücke: Der Exception-Zweig las über `pipelineStepExecutionSnapshot()` einen möglicherweise noch vorhandenen Teilbericht eines früheren erfolgreichen Laufs und übernahm dessen Inhalte ohne Kennzeichnung in den aktuellen Fehlerbericht.

Der Exception-Zweig übergibt jetzt die aktuelle Exception an den Schritt-Snapshot. Dieser enthält deren Klasse und konkrete Meldung unter `error`. Der Teilbericht wird in diesem Fall nicht als Ergebnis übernommen: Pfad und Existenz bleiben sichtbar, ergänzt durch `used = false` und `reason = step_threw_exception`. Vorhandene Berichtsdateien werden nicht gelöscht oder überschrieben. Der gemeinsame `raw_data`-Vertrag bleibt unverändert; die Anpassung betrifft nur den Schritt-Snapshot des Pipeline-Exception-Zweigs. Erfolgsberichte und die Behandlung normaler Rückgabecodes bleiben unverändert.

Zwei neue isolierte Tests führen den echten Klassifizierer innerhalb des echten Pipeline-Commands aus, einmal ohne Tracking und einmal mit getracktem UI-Lauf. Eine gezielte Zwischenänderung nach der Auswahl löst den Validierungsfehler aus. Geprüft werden vollständiger Rollback der Klassifizierungsdaten, unveränderter älterer Teilbericht, aktuelle Exception im Pipeline-Bericht, keine Aufrufe von Resolver oder Export, fehlgeschlagener Lauf/Schritt, unangetastete nachfolgende Pending-Schritte und das Aufräumen von Scan-Session und Pipeline-Kontext. Nur die Berechtigungsprüfung des getrackten Testbenutzers wird wie in den bestehenden isolierten Pipeline-Tests ersetzt; Datenverarbeitung und Tracking laufen regulär.

Keine Produktionsdaten geändert, kein produktiver Pipeline-/Scanner-/Exportlauf. Die zuvor abgeschlossenen PostgreSQL-Paralleltests wurden in diesem Schritt nicht erneut ausgeführt, da keine Sperr- oder Datenbank-Schreiblogik verändert wurde.

Verifikation: vollständige Workbench-Suite **548 Tests / 3307 Assertions bestanden**. PHP-Syntax und `git diff --check` erfolgreich.

## 16.10.2026 – Fehler-Rückgabecodes und Teilberichte eindeutig zuordnen

Die Pipeline beendet sich auch bei einem Fehler-Rückgabecode ohne Exception bereits korrekt. Bisher blieben in diesem Fall jedoch die `error_message`-Felder von Run und Step leer, und vorhandene ältere Teilberichte wurden ungeprüft als Ergebnis des aktuellen Schritts eingebunden.

`RunTranslationWorkbench` merkt sich vor jedem Schritt einen SHA-256-Inhaltsfingerprint der vorhandenen Berichtsdatei. Bei einem Fehler-Rückgabecode wird ein unveränderter Bericht mit `used = false` / `reason = report_not_changed` gekennzeichnet, ohne seine bisherigen Ergebnisinhalte zu übernehmen. Fehlende oder nicht lesbare Dateien werden als `report_unavailable` behandelt. Ein veränderter lesbarer JSON-Bericht bleibt als Fehlerdiagnose verfügbar; ungültiges JSON erhält `readable = false` und wird nicht verwendet. Die Originaldateien werden dabei nicht gelöscht oder verändert. Ein bytegleich neu geschriebener Bericht gilt vorsichtshalber ebenfalls als nicht nachweislich geändert.

Der aktuelle Fehler nennt den betroffenen Command und dessen tatsächlichen Rückgabecode. Er wird in Pipeline-JSON, Run und Step sowie den vorhandenen Step-Fehlerlogs festgehalten. `TranslationWorkbenchPipelineRunTracker::finishStep()` und `finish()` nehmen dafür eine optionale Fehlermeldung entgegen; der tatsächliche Rückgabecode bleibt erhalten. Der Bericht im Tracking-Metadatum und im Pipeline-JSON ist derselbe Snapshot. Erfolgsverarbeitung und gemeinsamer `raw_data`-Vertrag bleiben unverändert.

Vier isolierte Pipeline-Tests decken unveränderten Altbericht, fehlenden Bericht, frisch geschriebenen Fehlerbericht und neu geschriebenes ungültiges JSON ab. Alle verwenden Rückgabecode 7 und prüfen dessen unveränderte Weitergabe, Fehlermeldung in Run/Step/JSON, Pending-Status nachfolgender Schritte sowie das Aufräumen von Scan-Session und Kontext.

Abgrenzung: Ein Inhaltswechsel beweist die Änderung während des Schritts, aber keine exklusive Autorenschaft bei mehreren gleichzeitig schreibenden Prozessen am selben Berichtspfad. Die bestehende Berichtspfadstruktur wurde hier nicht umgebaut. Keine produktiven Daten oder Berichte verändert, kein produktiver Pipeline-/Exportlauf. Keine erneuten PostgreSQL-Paralleltests, da dieser Schritt die Fehlerdarstellung und Berichtszuordnung betrifft.

Die bestehende Konsolenformulierung „pipeline stopped“ wurde beibehalten und um den tatsächlichen Rückgabecode ergänzt; die vorhandenen Import-/Export-Abbruchprüfungen bleiben dadurch ebenfalls gültig.

Verifikation: vollständige Workbench-Suite **552 Tests / 3373 Assertions bestanden**. PHP-Syntax und `git diff --check` erfolgreich.

## 16.10.2026 – Ergebniszähler und Key-Konflikte in dynamischen Berichten erhalten

`ClassifyDynamicValues` und `ResolveUnknownDynamicSources` berechneten bereits Ergebniszähler, riefen den gemeinsamen Berichtsschreiber aber ohne diese Summary auf. Bei konkurrierenden aktiven Keys wurde zudem ausschließlich eine Konsolenmeldung ausgegeben und ohne neuen Bericht abgebrochen. Beide Commands übergeben jetzt ihre vorhandenen Zähler an `writeTranslationWorkbenchReport(summary: ...)`, ergänzt um `dry_run` und `exit_code`.

Beim Key-Zuordnungskonflikt enthalten die Berichte den Fehlercode `ambiguous_active_key_assignments`, die Anzahl betroffener Findings sowie die vor dem Abbruch ermittelte Gesamtzahl ausgewählter Findings beziehungsweise Quellen. `plan_summary.key_assignment_conflicts` enthält die konkreten Finding-IDs und konkurrierenden Key-IDs. Fehlende erforderliche Tabellen werden mit `missing_required_tables` und Exitcode 1 ausgewiesen. Der bestehende gemeinsame `raw_data`-Vertrag wurde nicht verändert; es werden lediglich die dafür bereits vorgesehenen Summary-/Plan-Summary-Parameter genutzt.

Dry-run-Berichte sind ausdrücklich markiert. Die vorhandene Bedeutung der Zähler bleibt erhalten: Beim Resolver kann `sources_resolved` im Dry-run eine mögliche Auflösung zählen; Schreib-/Historienzähler bleiben null. Es wurden keine neuen Datenänderungen oder automatische Konfliktentscheidungen eingeführt.

Vier bestehende Konfliktfälle wurden um Prüfung der konkreten Report-Inhalte erweitert (beide Commands jeweils schreibend/Dry-run). Ihre Prüfung ausschließlich lesender SQL-Abfragen bleibt bestehen. Vier neue Fälle prüfen erfolgreiche Ergebniszähler gegen Datenbank-/Historienänderungen, schreibfreien Dry-run und explizite Meldung fehlender Tabellen. Die gezielten acht Fälle bestehen.

Keine Produktionsdaten oder produktiven Berichte verändert und kein produktiver Scan-/Resolver-/Exportlauf. Die Datenbanktransaktions- und Sperrlogik wurde nicht geändert; PostgreSQL-Paralleltests wurden dafür nicht erneut ausgeführt.

Verifikation: vollständige Workbench-Suite **556 Tests / 3432 Assertions bestanden**. PHP-Syntax und `git diff --check` erfolgreich.

## 16.10.2026 – Kandidatensuche respektiert Entscheidungen und schreibt Historie

`DiscoverDynamicSourceCandidates` konnte vorhandene Kandidaten bei einer Wiederholung wieder auf `pending` und `active` setzen. Die Synchronisierung schützt jetzt jeden Kandidaten, der nicht zugleich offen (`pending`) und aktiv ist. Bestätigte, abgelehnte, entkoppelte und inaktive Kandidaten bleiben einschließlich ihrer Werte unverändert. Die Summary weist diese Fälle als `candidates_preserved` aus und wird tatsächlich an den Berichtsschreiber übergeben.

Jede Kandidatenänderung läuft in einer eigenen Transaktion. Zuerst wird die dynamische Quelle gesperrt und gegen den ausgewählten Stand geprüft, dann der passende Kandidat gesperrt und erneut gelesen. Mehrdeutige Kandidatenidentitäten werden abgewiesen. Neue oder geänderte offene Vorschläge erhalten über den zentralen Timeline-Recorder ein `dynamic_source_candidate_discovered` beziehungsweise `dynamic_source_candidate_updated` mit Vorher-/Nachher-Werten, Quellen-/Kandidaten-ID und Fundstelle. Scheitert die Historie, wird die zugehörige Kandidatenänderung zurückgerollt. Frühere erfolgreiche Kandidaten bleiben bei einem späteren Fehler erhalten; dies ist keine Gesamttransaktion des Commands. Unveränderte Wiederholungen erzeugen keine zusätzlichen Ereignisse. JSON-Werte werden zum Vergleich dekodiert, damit reine PostgreSQL-Formatierungsunterschiede kein Update auslösen.

Sieben neue Unit-Fälle prüfen Entscheidungsschutz, inaktive Kandidaten, Dry-run, unveränderte Wiederholungen sowie erfolgreiche und fehlgeschlagene Historie bei Erstellung und Aktualisierung. Vier weitere PostgreSQL-Parallelfälle prüfen bestätigte und abgelehnte Kandidaten jeweils bei Commit und Rollback einer konkurrierenden Entscheidung. Die echte Sperrwartezeit wird nachgewiesen: Nach Commit bleibt die Entscheidung erhalten, nach Rollback darf der weiterhin offene Vorschlag aktualisiert werden.

Abgrenzung: Die vorgelagerte Auswahl und Zuordnung von Quellen zu aktiven Findings, Dateien und Keys ist damit noch nicht vollständig geprüft. Insbesondere ist die vorhandene Auswahl eines Keys über Aggregation separat zu untersuchen; dieser Schritt ändert diese Zuordnungsregeln nicht. Keine produktiven Daten geändert, kein produktiver Scanner-/Resolver-/Exportlauf.

Verifikation: vollständige Workbench-Suite **563 Tests / 3488 Assertions bestanden**, zusätzlich **50 PostgreSQL-Parallelfälle bestanden**. PHP-Syntax und `git diff --check` erfolgreich.

## 16.10.2026 – Aktuellen Quellenkontext für Kandidaten prüfen

`DiscoverDynamicSourceCandidates` berücksichtigte bisher auch stillgelegte Findings/Dateien und obsolete Keys. Die Aggregation über `MIN(key_id)` konnte außerdem einen von mehreren aktiven Keys verdeckt auswählen. Die Auswahl berücksichtigt jetzt aktive Findings und Quelldateien sowie ausschließlich nicht obsolete Keys an aktiven Links. Quellen mit vorhandenem `key_id` und Finding müssen zu dessen aktueller Zuordnung passen. Quellen ohne Key beziehungsweise eigenständige Quellen ohne Finding bleiben zulässig; bei eigenständigen Quellen wird ein vorhandener direkter Key für den Vorschlag berücksichtigt.

Vor dem ersten Kandidatenschreibzugriff prüft der gemeinsame Zuordnungshelfer alle ausgewählten Finding-IDs auf konkurrierende aktive Keys. Ein Konflikt beendet auch einen Dry-run mit Exitcode 1. Konsole und JSON-Bericht nennen Finding- und Key-IDs; der Bericht enthält `ambiguous_active_key_assignments`. MIN dient damit nur noch der einzeiligen Abfrage und nicht einer automatischen fachlichen Konfliktentscheidung.

Innerhalb der vorhandenen Kandidatentransaktion wird nach der Quelle auch deren Finding, Datei, aktive Links und beteiligte Keys gesperrt. Die Auswahl wird für genau diese Quelle erneut gelesen; entfallene, mehrdeutige oder gegenüber der Auswahl veränderte Kontexte werden vor dem Schreiben abgewiesen. Dazu gehören auch geänderte SuggestedKeys und Dateipfade. Die bisherige Transaktionsgrenze pro Kandidat bleibt bestehen. Bereits abgeschlossene Kandidaten anderer Quellen werden bei einem späteren Kontextfehler nicht zurückgerollt.

15 neue isolierte Tests prüfen aktive beziehungsweise stillgelegte Eigentümer, fehlende Links, keylose/eigenständige Quellen, Konfliktberichte in Schreib- und Dry-run sowie Kontextänderungen nach der Auswahl. Die vorhandenen 50 PostgreSQL-Parallelfälle wurden erneut erfolgreich ausgeführt; sie sind kein zusätzlicher Nachweis für jede denkbare konkurrierende Änderung der Quellenzuordnung. Keine produktiven Daten oder Berichte verändert und kein produktiver Scan-/Resolver-/Exportlauf.

Verifikation: vollständige Workbench-Suite **578 Tests / 3554 Assertions bestanden**, zusätzlich **50 PostgreSQL-Parallelfälle bestanden**. PHP-Syntax und `git diff --check` erfolgreich.

## 16.10.2026 – Modellspalten-Vorschläge als unveränderte Beobachtung kennzeichnen

Die heuristische Kandidatensuche ordnet die Variable `$document` der Tabelle `person_documents` zu und liest die im Ausdruck genannte vorhandene Spalte. Diese Zuordnung ist weiterhin nur ein Vorschlag, keine statisch nachgewiesene Model-/View-Verbindung. Bisher wurden gelesene Werte pauschal mit `headline()` verändert und nach 200 unterschiedlichen Werten ohne Hinweis abgeschnitten.

Die Beobachtung erhält jetzt Rohtexte unverändert als Kandidatenwerte, einschließlich Groß-/Kleinschreibung und Leerzeichen. Sie behauptet damit ausdrücklich keine Auswertung möglicher PHP-Transformationen des Quellausdrucks. `meta.value_observation` nennt Tabelle, Spalte, Limit, Beobachtungsstatus, `source_verified = false` und `label_origin = raw_database_value`. Die Abfrage liest maximal 201 unterschiedliche Nicht-NULL-Werte; der zusätzliche Wert dient ausschließlich zum Nachweis, dass die gespeicherte Auswahl von maximal 200 Werten unvollständig ist. `truncated = true`, eine Konsolenwarnung mit Quellen-ID und Kandidatenreferenz sowie der Summary-Zähler `truncated_value_samples` machen dies sichtbar – auch im Dry-run. Der Zähler beschreibt die aktuelle Beobachtung, nicht zwingend einen neu gespeicherten Kandidaten, da geprüfte Kandidaten weiterhin erhalten bleiben.

Die geprüften Editor-/Runtime-Verknüpfungen verwenden den separaten Typ `related_dynamic_source` und bestätigte aktive Zuordnungen. Die hier erzeugten Modellvorschläge bleiben `pending`; es werden keine Übersetzungen, Freigaben oder Quellenverknüpfungen daraus erzeugt. Eine komfortable eigene UI-Prüfung dieser heuristischen Modellvorschläge ist mit dieser Änderung nicht implementiert. Die vorhandene Variablenheuristik selbst wurde nicht erweitert oder als verlässlich erklärt.

Drei neue isolierte Command-Tests prüfen 3, genau 200 und 201 unterschiedliche Werte, doppelte Werte und NULL, unveränderte Rohtexte, Zähler/Metadaten/Warnung, schreibfreien Dry-run und Wiederholung ohne zusätzliche Historie. Keine Produktionsdaten oder produktiven Berichte verändert. Die Sperr-/Transaktionslogik blieb unverändert; PostgreSQL-Paralleltests wurden in diesem Schritt nicht erneut ausgeführt.

Verifikation: vollständige Workbench-Suite **581 Tests / 3636 Assertions bestanden**. PHP-Syntax und `git diff --check` erfolgreich.

## 16.10.2026 – Modellvorschläge behalten bei wechselndem Wertebestand ihre Identität

Die Kandidatenbildung unterscheidet `model_column` und `model_column_unresolved` anhand des beobachteten Wertebestands. Die bisherige Suche nach vorhandenen Kandidaten verwendete genau diesen Typ als Identitätsmerkmal. Wenn Werte hinzukamen oder verschwanden, konnte deshalb ein zweiter Kandidat für dieselbe dynamische Quelle und dieselbe Modellspaltenreferenz entstehen. Insbesondere konnte ein neuer offener Kandidat neben einer bereits abgelehnten oder bestätigten Variante angelegt werden.

Die gesperrte Kandidatensuche berücksichtigt für diese beiden Modellzustände jetzt beide Typen gemeinsam. Ein einzelner aktiver offener Kandidat wird mit gleicher ID und regulärer Änderungshistorie aktualisiert; der beobachtete Typ und Wertebestand dürfen sich dabei ändern. Geprüfte und inaktive Kandidaten bleiben einschließlich ihres bisherigen Zustands und Wertesnapshots unverändert. Andere Kandidatentypen und Referenzen werden nicht zusammengefasst.

Sind bereits mehrere Zeilen für dieselbe Modellspaltenidentität vorhanden, bricht der Schreibversuch mit Quellen-ID, Referenz und konkreten Kandidaten-IDs ab. Es findet weder eine automatische Zusammenführung noch eine Löschung oder Änderung bestehender Entscheidungen statt. Die bestehende Transaktionsgrenze pro Kandidat bleibt bestehen. Dry-run beobachtet weiterhin nur Vorschläge und führt die schreibseitige Identitätsprüfung nicht aus; der Abbruch bei bestehenden Doppelungen ist damit keine zusätzliche Dry-run-Garantie.

Acht neue isolierte Tests prüfen leer → Werte → leer mit gleichbleibender ID und Historie, unveränderte Wiederholungen, schreibfreien Dry-run, bestätigte/abgelehnte/inaktive Kandidaten in beiden Richtungen sowie den unverändernden Abbruch bei vorhandenen Doppelungen. Keine Produktionsdaten verändert und kein produktiver Command-Lauf.

Verifikation: vollständige Workbench-Suite **589 Tests / 3715 Assertions bestanden**, zusätzlich die bestehenden **50 PostgreSQL-Parallelfälle bestanden**. PHP-Syntax und `git diff --check` erfolgreich.

## 16.10.2026 – Kandidatenkonflikte bereits im Dry-run und vor allen Schreibzugriffen melden

Die im vorigen Schritt dokumentierte Dry-run-Lücke ist geschlossen. `DiscoverDynamicSourceCandidates` bildet zunächst den Vorschlagsplan und prüft alle darin angesprochenen Kandidatenidentitäten lesend. Erst wenn keine Doppelung vorliegt, beginnt der schreibende Lauf. Ein Konflikt bei einer späteren Quelle verhindert dadurch auch Updates früherer Kandidaten. Der Plan hält die beobachteten Kandidatenwerte fest, statt sie für die Schreibphase noch einmal neu zu generieren.

Dry-run und Schreibmodus liefern bei vorhandenen Doppelungen Exitcode 1, den Fehlercode `ambiguous_candidate_identities`, eine Konfliktanzahl und `plan_summary.candidate_identity_conflicts` mit Quellen-ID, Referenz und konkreten Kandidaten-IDs. Die Konsole nennt dieselben Identitäten. Die gemeinsame Methode `candidateMatches()` stellt sicher, dass Vorabprüfung und gesperrte Schreibprüfung dieselben Identitätsregeln verwenden. Die erneute Prüfung unter Sperren bleibt erhalten: Ein erfolgreicher Dry-run garantiert nicht, dass der Bestand bis zu einem späteren Schreibzugriff unverändert bleibt. Bei erst später auftretenden Fehlern gilt weiterhin die Transaktionsgrenze pro Kandidat.

Fehlende erforderliche Tabellen erhalten nun ebenfalls einen frischen Fehlerbericht mit `missing_required_tables`, Dry-run-Markierung und Exitcode 1 statt einer leeren Summary. Der gemeinsame Raw-Data-Berichtsvertrag bleibt unverändert.

Der bestehende Doppelungstest wurde für beide Modi erweitert: Ein früherer Kandidat müsste aktualisiert werden, während eine spätere Quelle eine Doppelung enthält; Kandidaten, Quellen und Historie bleiben dennoch vollständig unverändert. Die Tests prüfen außerdem den konkreten Report und die weiterhin abweisende gesperrte Schreibprüfung. Zwei weitere Fälle prüfen fehlende Tabellen im Dry-run und Schreibmodus. Keine Produktionsdaten oder produktiven Berichte verändert. Die bestehende Sperrlogik wurde nicht verändert; die PostgreSQL-Paralleltests wurden in diesem Schritt nicht erneut ausgeführt.

Verifikation: vollständige Workbench-Suite **592 Tests / 3759 Assertions bestanden**. PHP-Syntax und `git diff --check` erfolgreich.

## 16.10.2026 – Shared-Key-Nachfolgevorschläge nicht anhand eines Zehn-Minuten-Fensters stilllegen

Die erneute Prüfung bestätigt die fachliche Abgrenzung aus der Bestandsaufnahme: `DetectSharedKeyCandidates` erkennt neue Fundstellen zu früheren `translation_key_bulk_equalized`-Entscheidungen. Dies ist weiterhin nicht die allgemeine Markierung N≥2 aktiver gleichlautender Literale mit unterschiedlichen SuggestedKeys. Diese allgemeine Regel einschließlich Rücknahme bleibt offen; der historische Vorschlagsweg wird nicht stillschweigend dafür umgedeutet.

Ein konkreter Fehler dieses historischen Wegs ist behoben: `latestFindingSeenCutoff()` leitete aus dem neuesten `last_seen_at` aller Findings ein Zehn-Minuten-Fenster ab. Aktive Fundstellen außerhalb dieses Fensters wurden nicht mehr ausgewählt, ihre offenen Kandidaten anschließend als obsolete markiert. Ein unabhängiger Teilscan konnte damit gültige Vorschläge verdrängen. Die Zeitfenster-Auswahl und ihr Summary-Feld sind entfernt. Aktuelle Kandidaten setzen stattdessen eine aktive Fundstelle und eine aktive Quelldatei voraus; die Datei gehört nun auch zur Prüfung erforderlicher Tabellen. Die bestehende fachliche Voraussetzung „erstmals nach der historischen Entscheidung erfasst“ bleibt unverändert.

Drei isolierte Command-Tests erzeugen eine reale Bulk-Key-Entscheidung, eine spätere passende Fundstelle und einen unabhängigen aktuellen Scan. Sie prüfen den Erhalt des älteren aktiven Vorschlags, Stilllegung bei obsoleter Fundstelle beziehungsweise Datei, unveränderte Kandidaten-ID, schreibfreien Dry-run, Report-Zähler und unveränderte Findings/Keys/Zuordnungen/Reviews/Timeline. Für die PostgreSQL-spezifischen SQL-Funktionen verwenden diese SQLite-Tests lokale Funktions-/Aggregatabbildungen; sie sind kein PostgreSQL-Parallelitätsnachweis.

Keine Produktionsdaten oder produktiven Berichte verändert. Weitere Themen dieses Commands – historische Entscheidungsgrundlage, Mehrdeutigkeiten der Key-Zuordnung, atomare Historie und Konkurrenzschutz – werden durch diese gezielte Korrektur noch nicht als vollständig geprüft erklärt.

Verifikation: vollständige Workbench-Suite **595 Tests / 3801 Assertions bestanden**. PHP-Syntax und `git diff --check` erfolgreich.

## 16.10.2026 – Historische Shared-Key-Entscheidungen an ihrem Snapshot prüfen

`sharedDecisionGroups()` gruppierte bisher alte Bulk-Reviews nach dem heutigen `keys.translation_key`. Wurden die damals beteiligten Keys später umbenannt, konnte die alte Entscheidung deshalb fälschlich als Bestätigung des neuen gemeinsamen Namens dienen. Auch die damals betroffenen Fundstellen, Dateien und Zuordnungen wurden nicht auf ihren aktuellen Zustand geprüft.

Eine historische Review dient jetzt nur dann als Grundlage, wenn ihr `new_values.translation_key` mit dem heutigen Key übereinstimmt und `meta.normalized_literal` dem aktuell normalisierten Literal entspricht. Fundstelle und Datei müssen aktiv, der Key nicht obsolete und die konkrete Finding-/Key-Zuordnung aktiv sein. Ein weiterer aktiver, nicht obsoleter Key an derselben Fundstelle macht diese Review als eindeutige Grundlage unbrauchbar; obsolete konkurrierende Keys verhindern sie nicht. Die bestehende Mindestzahl von zwei unterschiedlichen passenden historischen Findings bleibt erhalten. Reviews ohne passenden Snapshot werden nicht als Beleg verwendet; sie bleiben gespeichert und werden nicht nachträglich ergänzt oder umgeschrieben.

Zehn neue isolierte Command-Fälle prüfen unveränderte Entscheidungen, stillgelegte Fundstellen/Dateien/Keys, entfernte Links, gemeinsame spätere Key-Umbenennung, geänderte Literale, fehlende Snapshots und aktive beziehungsweise obsolete konkurrierende Keys. Sie prüfen auch Dry-run, Stilllegung des offenen Nachfolgevorschlags und unveränderte Reviews/Keys/Findings/Timeline. Zusätzlich prüft der temporäre PostgreSQL-Runner die native JSON-Extraktion und Aggregation mit einem passenden Review-Paar sowie dessen Ablehnung nach gemeinsamer Key-Umbenennung. Dieser zusätzliche Fall ist ein SQL-Verhaltenstest, kein Parallelfall.

Keine Produktionsdaten oder produktiven Berichte geändert. Bei einem späteren produktiven Lauf können offene Vorschläge ohne weiterhin passende Entscheidungsgrundlage regulär obsolete werden; bestätigte Entscheidungen werden durch diese Auswahländerung nicht gelöscht. Die allgemeine N≥2-Markierung neuer Literale bleibt ein anderer fachlicher Schritt. Die historische Auswahl ist weiterhin keine vollständige Absicherung gegen alle gleichzeitig erfolgenden Änderungen oder ein Abschluss der noch offenen Schreibpfad-/Historienprüfung dieses Commands.

Verifikation: vollständige Workbench-Suite **605 Tests / 3881 Assertions bestanden**. PostgreSQL: **50 bestehende Parallelfälle und ein neuer JSON-Snapshot-Fall bestanden**. PHP-Syntax und `git diff --check` erfolgreich.

## 16.10.2026 – Shared-Key-Vorschläge mit Historie und geschützten Reviews speichern

Der Schreibpfad von `DetectSharedKeyCandidates` aktualisierte bisher auch die Snapshot-Felder bereits geprüfter Kandidaten und setzte bei Wiederholungen sogar deren `created_at` erneut. Außerdem fehlten Timeline-Ereignisse für Erstellung, Änderung, Reaktivierung und Stilllegung.

Nur `pending`, `stale` und automatisch obsolete Vorschläge werden nun aktualisiert. Andere Statuswerte bleiben mit sämtlichen gespeicherten Angaben unverändert. Vorhandene Kandidaten werden beim Schreiben gesperrt und erst dann geprüft. Erstellungsdatum und `first_seen_at` bleiben erhalten. `last_seen_at` darf für offene Vorschläge fortgeschrieben werden; reine Zeitstempeländerungen erzeugen keine zusätzliche Historie.

Fachliche Änderungen laufen über den zentralen Timeline-Recorder: `shared_key_candidate_discovered`, `shared_key_candidate_updated` und `shared_key_candidate_obsoleted` enthalten Kandidaten-ID, Ziel-Key und Vorher-/Nachher-Snapshot. Eine Reaktivierung wird als Statusänderung obsolete/stale → pending protokolliert. Kandidatenänderungen und Ereignisse gehören zur vorhandenen Gesamttransaktion der Schreibphase. Scheitert die Ereigniserstellung, werden auch Kandidatenänderungen zurückgerollt. Summary und Konsole nennen erhaltene geprüfte Kandidaten und erzeugte Ereignisse; „Proposed pending rows“ bezeichnet weiterhin den Vorschlagsplan und nicht den gespeicherten Status geschützter Kandidaten.

Sieben neue Tests prüfen bestätigte/abgelehnte/angewendete Snapshots, den gesamten offenen Lebenszyklus einschließlich unveränderter Wiederholung und Reaktivierung, erhaltene Erstellungsdaten sowie Rollback bei fehlgeschlagener Historie für Erstellung, Update und Stilllegung. Bestehende Tests prüfen weiterhin unveränderte fachliche Eingangsdaten; Timeline-Ereignisse sind nun beabsichtigte Ausgaben. Die gezielten 20 Shared-Key-Fälle bestehen.

Keine Produktionsdaten oder produktiven Berichte verändert. Die Eingangsauswahl erfolgt weiterhin vor der Schreibtransaktion; eine vollständig abgesicherte Behandlung gleichzeitig geänderter Findings/Reviews/Key-Zuordnungen ist damit noch nicht nachgewiesen. Die allgemeine N≥2-Markierung bleibt getrennt. Die neuen Sperren wurden hier durch isolierte SQLite-Verhaltenstests, nicht durch neue PostgreSQL-Parallelfälle geprüft.

Verifikation: vollständige Workbench-Suite **612 Tests / 3931 Assertions bestanden**. PHP-Syntax und `git diff --check` erfolgreich.

## 16.10.2026 – Shared-Key-Auswahl und Speicherung gemeinsam gegen konkurrierende Writes schützen

Die Shared-Key-Vorschlagsauswahl lag bisher vor der Schreibtransaktion. `DetectSharedKeyCandidates` führt im Schreibmodus jetzt die komplette `detect(false)`-Operation einschließlich Entscheidungsgruppen, Kandidatenplan, Aktualisierung, Stilllegung und Historie innerhalb einer Transaktion aus. Konsole und Bericht werden erst nach erfolgreichem Abschluss ausgegeben. Für von Laravel erkannte Transaktionskonflikte sind höchstens drei Versuche vorgesehen; der Plan wird bei jedem Versuch neu aufgebaut.

Vor der Auswahl nimmt PostgreSQL SHARE-Tabellensperren auf Source Files, Findings, Keys, Key-Findings und Reviews. Dies schützt auch vor neu eingefügten Datensätzen, deren Abwesenheit nicht durch vorhandene Zeilensperren abgesichert werden könnte. Auf Shared-Key-Candidates wird SHARE ROW EXCLUSIVE verwendet, damit parallele Detektoren und manuelle Kandidatenwrites nicht gleichzeitig die Bestandsabstimmung durchführen. Die Sperren enden mit Commit oder Rollback. Gewöhnliche SELECT-Leser bleiben möglich; parallele Scanner, Reviewer und sonstige Writer dieser Tabellen müssen gegebenenfalls warten. Dies ist bewusst eine tabellenweite Sperre für die Dauer dieses Commands, keine kurze Sperre nur der später betroffenen Findings. Andere Transaktionen mit abweichender Sperrreihenfolge können weiterhin einen Datenbank-Deadlock verursachen; nach ausgeschöpften Versuchen wird der Fehler weitergegeben statt ungesichert fortzufahren.

Dry-run nimmt diese Sperren nicht und schreibt keine Kandidaten. Die Sperrimplementierung gilt ausdrücklich für PostgreSQL; SQLite dient weiterhin den isolierten Verhaltenstests. Es wird keine entsprechende Parallelitätsgarantie für andere Datenbanktreiber behauptet.

14 neue PostgreSQL-Parallelfälle prüfen wartende Updates für jede der sechs Tabellen jeweils bei Commit und Rollback der Detektortransaktion. Zwei davon unabhängige Fälle in umgekehrter Reihenfolge prüfen die tatsächliche `detect(false)`-Operation bei einem schon laufenden Key-Writer: Nach Commit wird die stillgelegte Grundlage nicht verwendet, nach Rollback bleibt die gültige Gruppe verfügbar. Damit sind die neuen Fälle insgesamt zwölf Tabellen-Writer-Fälle plus zwei Fälle in umgekehrter Reihenfolge. Die Wartezeit wird über den PostgreSQL-Sperrzustand nachgewiesen, nicht durch angenommene Sleep-Zeiten. Inserts/Deletes werden durch denselben nativen Tabellen-Sperrmodus erfasst, sind in diesen neuen 14 Fällen jedoch nicht separat ausgeführt.

Keine Produktionsdaten oder produktiven Berichte verändert. Die Tests liefen ausschließlich im temporären PostgreSQL-Cluster. Die allgemeine N≥2-Markierung ist weiterhin ein eigener offener fachlicher Punkt.

Verifikation: vollständige Workbench-Suite **612 Tests / 3931 Assertions bestanden**. PostgreSQL: **64 Parallelfälle plus ein JSON-Snapshot-Fall bestanden**. PHP-Syntax und `git diff --check` erfolgreich.

## 16.10.2026 – Eindeutige aktive Key-Zuordnung auch für neue Shared-Key-Kandidaten

Der historische Shared-Key-Detektor wählte für neue Fundstellen bislang den kleinsten Key eines aktiven Links aus, unabhängig vom Status des Keys und ohne Konfliktmeldung. Ein obsoleter Key konnte so einen gültigen Ersatz-Key verdrängen; mehrere aktive Keys wurden verdeckt auf eine Zuordnung reduziert.

Die gemeinsame Methode `candidateFindingsQuery()` stellt jetzt die fachlich in Frage kommenden aktiven Fundstellen bereit: aktive Datei, passendes Literal, erste Erfassung nach der historischen Entscheidung und keine eigene frühere Bulk-Entscheidung. Diese Menge dient sowohl der Vorabprüfung mit `TranslationWorkbenchDynamicKeyAssignment::conflicts()` als auch der Kandidatenbildung. Bei mehreren aktiven, nicht obsoleten Keys bricht der Lauf vor Kandidatenupdates oder Stilllegungen mit Exitcode 1 ab. Konsole und Summary enthalten `ambiguous_active_key_assignments` sowie die konkreten Finding-/Key-IDs. Dry-run meldet denselben Konflikt. Die Prüfung findet im Schreibmodus innerhalb der bereits geschützten Gesamttransaktion statt.

Die Key-Aggregation berücksichtigt ausschließlich aktive Links zu nicht obsoleten Keys. MIN ist damit nach erfolgreicher Prüfung nur noch eine technische Aggregation. Ein gültiger Ersatz-Key wird auch dann ausgewählt, wenn ein älterer obsoleter Key eine kleinere ID hat. Ohne gültigen Key bleibt ein ansonsten zulässiger Vorschlag keylos; der Detektor ordnet keine Keys automatisch neu zu. Bereits gespeicherte Keys, Links, Reviews und Übersetzungen werden nicht geändert.

Vier neue isolierte Command-Fälle prüfen Konflikte in beiden Modi mit unverändertem Daten-/Historienbestand und konkretem Fehlerbericht sowie obsolete Keys mit und ohne gültigen Ersatz. Die gezielten 47 Shared-bezogenen Tests bestehen. Der gemeinsame Raw-Data-Berichtsvertrag bleibt unverändert; die Diagnose steht in der bestehenden Summary.

Keine Produktionsdaten oder produktiven Berichte verändert. Die allgemeine Shared-Candidate-Markierung N≥2 ist weiterhin nicht mit diesem historischen Nachfolgevorschlagsweg gleichzusetzen.

Verifikation: vollständige Workbench-Suite **616 Tests / 3973 Assertions bestanden**. Bestehende PostgreSQL-Prüfung: **64 Parallelfälle plus ein JSON-Snapshot-Fall bestanden**. PHP-Syntax und `git diff --check` erfolgreich.

## 16.10.2026 – Allgemeine Shared-Candidate-Erkennung in der UI zentral berechnen

Die weitere Bestandsaufnahme fand neben dem historischen Command bereits eine allgemeine, berechnete Shared-Anzeige in `TranslationWorkbenchEntries`: `bulkLiteralCountsQuery()` zählte bisher Fundstellen innerhalb der aktuellen UI-Suche/Filter und ohne Prüfung unterschiedlicher SuggestedKeys. Dadurch konnte eine Suche nach einer einzigen Datei die Shared-Eigenschaft verschwinden lassen; mehrere Fundstellen mit demselben SuggestedKey konnten dagegen fälschlich genügen. Die frühere Aussage, die allgemeine N≥2-Regel sei insgesamt noch nicht umgesetzt, wird damit präzisiert: Eine UI-Zählung existierte, erfüllte aber den vereinbarten Vertrag nicht.

`TranslationWorkbenchSharedLiteralCandidates::groups()` ist jetzt die gemeinsame lesende Grundlage für diese UI-Zählung. Sie berücksichtigt aktive Findings in aktiven Dateien mit nichtleerem Literal und nichtleerem SuggestedKey. Ein Literal-Cluster qualifiziert sich ab zwei unterschiedlichen SuggestedKeys; die Zahl zugehöriger Fundstellen wird separat gezählt. Key-Links werden nicht gejoint und können die Zahl daher nicht vervielfachen. Mehrere Fundstellen in derselben Datei sind zulässig. Obsolete, stale und commented_out Findings sowie inaktive Dateien zählen nicht. Die bestehende Bulk-Normalisierung (Kleinschreibung, Rand-/Whitespace-Normalisierung und bisheriger Fallback auf literal_text_suggested) bleibt ausdrücklich unverändert; dies ist keine neue bytegenaue Gleichheitsdefinition.

Die UI bezieht ihre Gruppen nun unabhängig von Suche, Namespace-, Sprach- und weiteren Listenfiltern. Diese Filter begrenzen weiterhin die angezeigten Zeilen. Für die Shared-Ansicht selbst werden nur aktive Fundstellen/Dateien mit vorhandenem SuggestedKey berücksichtigt. Entfällt die Voraussetzung – etwa durch Stilllegung oder Vereinheitlichung der SuggestedKeys –, verschwindet die berechnete Markierung beim nächsten Lesen automatisch; eine erneute gültige zweite Fundstelle lässt sie wieder erscheinen.

Es wird bewusst keine zusätzliche persistierte Boolean-Spalte oder zweite Kandidatentabelle eingeführt: Die aktuelle Eignung wird aus dem vorhandenen Datenbestand abgeleitet. Daher entsteht durch das bloße Lesen auch keine Timeline-Historie für die Markierung. Historische Shared-Key-Vorschläge, bestätigte Reviews, tatsächliche Key-Zuordnungen und Übersetzungen bleiben getrennt und unverändert. Ein scanseitig persistierter Marker mit eigener Änderungshistorie ist damit nicht implementiert.

Drei neue isolierte Tests prüfen N≥2, unterschiedliche SuggestedKeys, Rücknahme/Wiederkehr, Kommentare und inaktive Quellen, fehlende SuggestedKeys, mehrere Fundstellen in derselben Datei und das tatsächliche UI-Filterverhalten. Die bisherige UI-spezifische Zählfilter-Methode wurde entfernt, statt zwei Definitionen weiterzuführen. Ein zusätzlicher nativer PostgreSQL-Fall prüft dieselbe Gruppierung und den Wegfall bei gleichen SuggestedKeys. Keine Produktionsdaten oder produktiven Berichte verändert.

Verifikation: vollständige Workbench-Suite **618 Tests / 3995 Assertions bestanden**, anschließend zusätzlicher UI-Verhaltenstest **1 Test / 4 Assertions bestanden**. PostgreSQL: **64 Parallelfälle plus zwei SQL-Verhaltenstests bestanden**. PHP-Syntax und `git diff --check` erfolgreich.

## 16.10.2026 – Bulk-Auswahl vor Verarbeitung vollständig auf aktive Quellen prüfen

Die anschließende Prüfung des UI-Schreibwegs fand in `TranslationWorkbenchBulkKeyWriter::context()` keine Statusprüfung für ausgewählte Findings/Dateien. Zudem konnten nicht mehr vorhandene ausgewählte IDs verschwinden, während die verbleibenden Findings weiterhin eine bestätigbare Teilmenge bildeten.

Der gemeinsame Kontext berücksichtigt jetzt nur aktive Findings in aktiven Quelldateien. `can_confirm` setzt zusätzlich voraus, dass alle normalisierten ausgewählten IDs tatsächlich in dieser gültigen Menge vorhanden sind. Die UI-Vorschau und die erneute Prüfung im Writer verwenden dieselbe Methode. Ungültige Auswahlen werden insgesamt nicht verarbeitet. Bestehende Regeln für gemeinsame Literale und den Anschluss an einen bereits bestätigten Shared-Key bleiben erhalten; die allgemeine Kandidatenmarkierung ist keine zusätzliche Schreibfreigabe.

Fünf Regressionen prüfen obsolete/stale/commented_out Findings, eine obsolete Quelldatei und eine fehlende dritte ID neben zwei weiterhin gültigen Findings. Sie prüfen unveränderte Keys, Review-Anzahl und Timeline-Anzahl. Die gezielte Auswahl besteht mit 63 Tests / 575 Assertions.

Dies ist noch keine Absicherung gegen gleichzeitige Änderungen während der Transaktion: Die bestehenden Methoden haben hier weiterhin keine vollständige Sperrstrategie. Ebenfalls offen bleibt die MIN-Auswahl bei mehreren aktiven Key-Links und die gesonderte Prüfung der historischen Shared-Key-Zielgrundlage. Diese Punkte sind nicht durch die vorliegende Quellenvalidierung gelöst. Produktionsdaten und produktive Scan-/Exportprozesse wurden nicht verändert bzw. ausgeführt.

Verifikation: vollständige Workbench-Suite **624 Tests / 4029 Assertions bestanden**. PHP-Syntax und `git diff --check` erfolgreich.

## 16.10.2026 – Mehrdeutige Bulk-Key-Zuordnungen vor Verarbeitung melden

`TranslationWorkbenchBulkKeyWriter::context()` verwendet jetzt die bestehende gemeinsame Konfliktprüfung `TranslationWorkbenchDynamicKeyAssignment::conflicts()` für die ausgewählten Findings. Mehrere aktive Links zu unterschiedlichen nicht obsoleten Keys liefern einen nicht bestätigbaren Kontext mit konkreten Finding-/Key-IDs. Die UI zeigt die übersetzbare Konfliktmeldung im vorhandenen Warn-Toast und behält die Auswahl. Der direkte Writer-Aufruf prüft erneut und wirft bei diesem Konflikt eine ValidationException vor jeder Änderung.

Die Key-Auswahl berücksichtigt nur aktive Links zu nicht obsoleten Keys. MIN dient nach der Konfliktprüfung noch der technischen Aggregation; ein älterer obsoleter Key kann einen gültigen Ersatz nicht mehr verdrängen. Ohne gültigen Key greifen weiterhin die bestehenden Regeln für fehlende Keys bzw. den Anschluss an eine bestätigte Shared-Key-Grundlage. Obsolete Keys werden nicht reaktiviert oder verändert.

Drei Regressionen prüfen die konkreten Konflikt-IDs, direkte Verarbeitung und UI-Bestätigung mit unverändertem Key-/Link-/Review-/Timeline-Bestand sowie obsolete Keys mit und ohne gültigen Ersatz. Die gezielten Bulk-/Shared-Fälle bestehen mit 66 Tests / 592 Assertions.

Die getrennte Validierung der historischen Shared-Key-Zielgrundlage und die vollständige Absicherung gegen konkurrierende Änderungen bleiben offen; die Konfliktabfrage allein schließt keine Race Condition aus. Keine Produktionsdaten, Source-Dateien oder Sprachdateien verändert und keine produktiven Scan-/Exportprozesse gestartet.

Verifikation: vollständige Workbench-Suite **627 Tests / 4046 Assertions bestanden**. PHP-Syntax der beiden geänderten Klassen und `git diff --check` erfolgreich.

## 16.10.2026 – Historische Shared-Key-Zielgrundlage für Bulk-Anschluss validieren

`existingBulkSharedKeyForLiteral()` akzeptierte zuvor einen beliebigen Bulk-Review entweder am Finding oder am Key. Inaktive Dateien und obsolete Keys wurden nicht ausgeschlossen. Bei mehreren bestätigten Zielnamen wurde nach Häufigkeit und anschließend Namen ein Ziel gewählt.

Die Abfrage verlangt jetzt aktive Fundstellen in aktiven Dateien, einen nicht obsoleten Key und die exakte aktive Finding-/Key-Verknüpfung. Der historische Bulk-Review muss genau dieses Finding und diesen Key referenzieren; sein bestätigter TranslationKey und normalisiertes Literal müssen mit dem aktuellen Bestand übereinstimmen. Konkurrierende aktive Links zu anderen nicht obsoleten Keys schließen die betreffende Grundlage aus. Diese Bedingungen entsprechen dem bereits abgesicherten historischen Detektor, ohne dessen Mindestanzahl für neue automatische Vorschläge auf den manuellen Anschluss zu übertragen.

Nur genau ein gültiger Zielname wird als vorhandenes Shared-Ziel angeboten. Mehrere unterschiedliche gültige Zielnamen liefern kein automatisch gewähltes Ziel. Damit bleibt ein einzelner Anschluss nicht bestätigbar; eine eigenständig gültige Mehrfachauswahl mit explizitem Ziel bleibt nach den bestehenden Bulk-Regeln möglich. Es wird keine neue automatische Zusammenführung eingeführt.

Acht neue Regressionen prüfen inaktive Dateien, obsolete/umbenannte Keys, geänderte Literale, falsche Review-Findings, inaktive/konkurrierende Links und unterschiedliche bestätigte Zielnamen. Die gezielte Suite besteht mit 74 Tests / 630 Assertions. Ein zusätzlicher PostgreSQL-Fall prüft den tatsächlichen JSON-Abgleich und die Ablehnung nach einer Key-Umbenennung. Die Absicherung gegen parallele Änderungen bleibt gesondert offen. Keine produktiven Daten oder Exporte verändert.

Verifikation: vollständige Workbench-Suite **635 Tests / 4084 Assertions bestanden**. Isoliertes PostgreSQL: **64 Parallelfälle plus drei SQL-Verhaltenstests bestanden**. PHP-Syntax und `git diff --check` erfolgreich.

## 16.10.2026 – Bulk-Validierung und Verarbeitung unter PostgreSQL gemeinsam sperren

`TranslationWorkbenchBulkKeyWriter::equalize()` sperrt jetzt vor `context()` die Tabellen source_files, findings, keys, key_findings und reviews in SHARE ROW EXCLUSIVE MODE. Die Sperren gelten bis zum Abschluss der bestehenden Transaktion einschließlich Reviews und Timeline. Damit können auch neu eingefügte Links/Reviews die geprüfte Auswahl oder historische Zielgrundlage nicht während der Verarbeitung verändern. Normale SELECT-Abfragen bleiben möglich; andere Schreiber auf diesen fünf Tabellen warten. Die Sperre ist bewusst tabellenweit, nicht auf ausgewählte Findings begrenzt. Die Transaktion darf bei von Laravel erkannten Deadlocks/Konkurrenzfehlern bis zu drei Versuche ausführen.

Die Sperrmethode fordert eine laufende Transaktion. Die native Strategie gilt ausschließlich für PostgreSQL; SQLite bleibt der isolierte funktionale Testpfad, andere Treiber erhalten hier keine zusätzliche Parallelitätsgarantie. Eine veraltete UI-Vorschau wird weiterhin gegen den aktuellen Zustand neu ausgewertet; es gibt dadurch noch keinen optimistischen Versionsvergleich mit dem ursprünglich angezeigten Dialog und keine idempotente Operations-ID.

Zwölf neue native Parallelfälle prüfen für jede der fünf Eingangstabellen wartende Updates bis Commit bzw. Rollback sowie den tatsächlichen Bulk-Writer hinter einer bereits laufenden Finding-Stilllegung: Commit verhindert die Bulk-Änderung, Rollback erlaubt die weiterhin gültige Auswahl. Die Wartebedingung wird über PostgreSQLs Sperrzustand nachgewiesen. Inserts/Deletes sind vom nativen Sperrmodus erfasst, werden in diesen zwölf neuen Fällen aber nicht separat ausgeführt. Die bestehenden Tests für atomaren Rollback bei fehlgeschlagener Historie bleiben erhalten. Keine Produktionsdaten oder produktiven Prozesse verändert.

Verifikation: vollständige Workbench-Suite **635 Tests / 4084 Assertions bestanden**. Isoliertes PostgreSQL: **76 Parallelfälle plus drei SQL-Verhaltenstests bestanden**. PHP-Syntax und `git diff --check` erfolgreich.

## 16.10.2026 – Bulk-Dialog gegen geänderten Vorschaukontext absichern

Beim Öffnen des Bulk-Dialogs speichert die UI einen SHA-256-Fingerabdruck des fachlichen `context()` in einer Livewire-Locked-Property. Eine Bestätigung ohne zuvor geöffneten gültigen Dialog ist nicht mehr möglich. Der Writer vergleicht den übergebenen Fingerabdruck nach Erwerb der PostgreSQL-Sperren mit seinem frisch gelesenen Kontext. Bei Abweichung wirft er vor Änderungen eine ValidationException. Der Dialog enthält dafür ein eigenes flux:error; Auswahl und Dialog bleiben erhalten. Erneutes Öffnen setzt die Fehlermeldung zurück und übernimmt die neue Vorschau. Schließen entfernt den Fingerabdruck. Der eingegebene Ziel-Key gehört bewusst nicht zum Fingerabdruck und kann weiterhin bearbeitet werden.

Der Vergleich betrifft den fachlichen Bulk-Kontext (Auswahl, Literal, Key-Zuordnung, Review-Status und abgeleitetes Shared-Ziel), nicht sämtliche Datenbankspalten oder historische Zeilen. Er ist keine allgemeine Datensatzversion und keine idempotente Operations-ID. Direkte interne Writer-Aufrufe ohne optionalen Fingerabdruck behalten die Prüfung des aktuellen Zustands; der UI-Bestätigungsweg verlangt ihn. Der Modal-Einstieg erlaubt außerdem einen einzelnen Anschluss, wenn der bestehende Kontext diesen wegen einer bestätigten Shared-Grundlage bereits zulässt; zuvor widersprach die starre Zwei-Auswahl-Prüfung dem Writer.

Zwei Regressionen prüfen die Ablehnung einer inzwischen geänderten, weiterhin gültigen Literal-Gruppe ohne Key-/Review-Änderung, die erfolgreiche Bestätigung nach explizitem Neuöffnen und die Ablehnung ohne Vorschau. Die bestehenden Fixtures öffnen den Dialog jetzt auch tatsächlich. Zwei zusätzliche PostgreSQL-Parallelfälle prüfen den Vergleich nach einer wartenden Transaktion: Commit einer Literaländerung wird abgelehnt, Rollback erlaubt die unveränderte Vorschau. Keine Produktionsdaten oder produktiven Prozesse verändert.

Verifikation: vollständige Workbench-Suite **637 Tests / 4093 Assertions bestanden**. Isoliertes PostgreSQL: **78 Parallelfälle plus drei SQL-Verhaltenstests bestanden**. PHP-Syntax und `git diff --check` erfolgreich.

## 16.10.2026 – Wiederverwendung abgetrennter Key-Kandidaten im Bulk-Anschluss begrenzen

Die Nachprüfung des Pfads für Findings ohne aktive Key-Zuordnung fand eine weitere Lücke: `keyForBulkEqualizeRow()` nahm den ersten Fingerprint-Treffer ohne Status-/Review-/Belegungsprüfung und verwendete sonst den ersten leeren SuggestedKey-Treffer. Die zuvor abgesicherte aktive Key-Auswahl deckte diesen Wiederverwendungspfad nicht ab.

Die bestehende Suchmenge (exakter Foundation-Fingerprint oder passender SuggestedKey ohne gesetzten TranslationKey) wird jetzt gemeinsam geprüft. Nur genau ein nicht obsoleter Kandidat mit Review-Status pending, ohne TranslationKey und ohne aktive Verknüpfung zu einem anderen Finding darf wiederverwendet werden. Mehrere Kandidaten, obsolete/bestätigte/belegte Kandidaten und fremde aktive Zuordnungen führen zu einer ValidationException mit Finding- und Kandidaten-IDs. Ein belegter Fingerprint wird nicht durch Neuanlage eines künstlich abweichenden Fingerprints umgangen. Ohne Treffer bleibt die reguläre Neuanlage mit Historie erhalten; ein freier abgetrennter Kandidat kann weiterhin über den bestehenden protokollierten Anschlussweg verwendet werden.

Die Prüfung läuft im tatsächlichen Schreibweg innerhalb der bereits geschützten Transaktion. Der Vorschaukontext kann für diesen Fall noch grundsätzlich bestätigbar sein; die konkrete Wiederverwendungsprüfung erfolgt beim Bestätigen und erscheint im vorhandenen Auswahlfehlerfeld. Bei einem Konflikt wird die gesamte Transaktion zurückgerollt. Die frühere Aussage, obsolete Keys würden nicht verändert, galt bisher nur für die aktive Zuordnung und ist nun auch in diesem nachgelagerten Wiederverwendungspfad abgesichert.

Fünf Regressionen prüfen obsolete und bestätigte Kandidaten, bereits gesetzte fremde TranslationKeys, aktive fremde Finding-Zuordnungen und mehrdeutige Kandidaten. Keys, Links, Reviews und Timeline bleiben bei Ablehnung unverändert. Die bestehenden positiven Fälle für freie Kandidaten und Neuanlage bestehen weiterhin. Gezielte Prüfung: 81 Tests / 659 Assertions. Keine Produktionsdaten oder produktiven Scan-/Exportprozesse verändert.

Verifikation: vollständige Workbench-Suite **642 Tests / 4113 Assertions bestanden**. PHP-Syntax und `git diff --check` erfolgreich. Die PostgreSQL-Paralleltests wurden für diese Änderung nicht erneut ausgeführt; Sperrstrategie und Transaktionsgrenzen blieben unverändert.

## 16.10.2026 – Konsolidierte Restarbeit und Abschlussgrenzen

Diese Liste ordnet die vielen chronologischen Zwischenstände ein. Frühere Absätze mit „offen“ sind historische Momentaufnahmen und dürfen nicht ohne Abgleich mit späteren Reparaturen als heutige Restliste gelesen werden. Auch README.md beschreibt weiterhin das ursprüngliche Audit vom 15.10., nicht den vollständig nachgeführten Reparaturstand. Letzte erfolgreiche funktionale Suite: 642 Tests / 4113 Assertions. Diese Zahl ist kein Nachweis einer vollständigen Zielworkflow-Abnahme.

| Paket | Heutige Einordnung | Nächste Arbeit / Abschlussnachweis |
| --- | --- | --- |
| Shared-/Bulk-Abschluss | Aktive Quellen, eindeutige Keys, historische Zielgrundlage, Transaktionssperren, Vorschauvergleich und sichere Wiederverwendung abgesichert | Wiederverwendungskonflikte schon in die gemeinsame Vorschau übernehmen; den Benutzerweg einschließlich Fehleranzeige prüfen. Keine zweite abweichende Validierung einführen. |
| Bearbeitung und Freigabe (U04) | Vorhandene Writer/Reviews und Importkonfliktschutz vorhanden; nicht mit revisionsbezogener Wertfreigabe gleichzusetzen. TranslationWorkbenchTranslationWriter bezeichnet fehlenden stale-edit protection weiterhin ausdrücklich | Skalare/dynamische Speicher- und Reviewwege gezielt auf aktuelle Revision und Konflikte abgleichen. Gespeicherter Entwurf, Freigabe einer konkreten Sprach-/Wertrevision und veröffentlichter Stand müssen unterscheidbar sein. Bestehende Werte/Reviews bewahren; Schemaänderungen erst anhand dieses Vertrags planen. |
| Veröffentlichung (U05) | Sprachdatei-Gesamtplanung und Fehlerstatus für Strukturkonflikte abgesichert | Codeänderung und Lang-Export an denselben geprüften Daten-/Dateistand binden; Teilfehler, Wiederaufnahme und veraltete Pläne testen. Vorabprüfung allein macht DB-/Dateischritte nicht atomar. |
| Scan-/Command-Abschluss (U03/U02) | Gemeinsamer Scan-Stand je Phase und reguläre Pipeline ohne Legacy-Versorgung dokumentiert; drei alte Writer stillgelegt | Aktuelle Einstiegsmatrix und persistierte Run-/Manifestnachweise gegen den Zielvertrag abnehmen. Kein erneuter pauschaler Neuaufbau der bereits reparierten Scan-/Resolverwege. |
| Bestands-/Gesamtprobe (U06) | Umfangreiche isolierte Tests und begrenzte produktive, nachgeprüfte historische Übernahmen dokumentiert | Nach den offenen Vertragsänderungen vollständigen Workflow auf einer frischen isolierten PostgreSQL-Bestandskopie prüfen: Scan, Bearbeitung, Review, Plan, Veröffentlichung, Wiederholung und Fehler/Recovery. Dateninhalte und Historie vor/nach vergleichen, nicht nur Anzahlen. |
| UI und Dokumentation | Bestehende Oberfläche nutzbar, Target workflow UI als fachliche Orientierung vorhanden | Fehlermeldungen/Preview/Status und tatsächliche Aktionen mit dem Zielablauf abgleichen; aktuelle Abschlussmatrix aus Belegen nachführen. Keine pauschale optische Neugestaltung als Voraussetzung. |

Priorität: den eng begrenzten Bulk-Vorschaupunkt abschließen, anschließend U04 und U05 als zusammenhängende Pakete bearbeiten, danach Gesamtprobe und Abnahme. Zusätzliche allgemeine Idempotency-IDs, andere Datenbanktreiber, optische Verbesserungen und weitere Beispielgraphen sind keine automatisch hinzugefügten Pflichtpakete. Die breite PostgreSQL-Sperre bleibt ein dokumentierter Betriebsaspekt; eine feinere Sperrstrategie ist erst bei nachgewiesenem Bedarf erforderlich. Kein Datenbank-Neustart oder produktiver Komplettlauf wird durch diese Restliste ausgelöst.

## 16.10.2026 – Gemeinsame Wiederverwendungsprüfung bereits in der Bulk-Vorschau

Die Kandidatensuche samt Schutzprüfung ist in `missingKeyCandidate()` zusammengeführt. `context()` verwendet sie für Findings ohne aktiven Key, sofern ein vorhandenes Shared-Ziel den Anschluss grundsätzlich erlaubt. Ein Wiederverwendungskonflikt setzt `can_confirm=false` und liefert die konkrete Meldung in `selection_error`. Der Modal-Einstieg zeigt diese Meldung statt des pauschalen Hinweises auf eine unvollständige Einzelauswahl; ein gültiger Vorschau-Nachweis wird dabei nicht erstellt. Der Writer prüft denselben Kontext unter Sperre und ruft bei tatsächlicher Wiederverwendung erneut dieselbe Methode auf. Es gibt keine zweite abweichende Kandidatenregel.

Die IDs der vorgesehenen freien Kandidaten (bzw. null für Neuanlage) sind Bestandteil des Kontext-Fingerabdrucks. Die fünf bestehenden Negativfälle prüfen jetzt zusätzlich die nicht bestätigbare Vorschau, konkrete Meldung und den abgelehnten Modal-Einstieg ohne Nachweis. Die gezielte Suite besteht mit 81 Tests / 674 Assertions. Der tatsächliche visuelle Browserablauf wurde in diesem Schritt nicht interaktiv geprüft.

Damit ist der konkret offene Bulk-Vorschaupunkt umgesetzt. Als nächstes wird U04 an den skalaren und dynamischen Übersetzungseditoren begonnen. Die aktuelle Codeprüfung bestätigt: `TranslationWorkbenchTranslationWriter::save()` besitzt eine Transaktion, aber keinen Versionsnachweis aus dem geöffneten Editor; Berechtigungs-/Zuordnungsauflösung liegt laut Klassenvertrag bei den Aufrufern. Das ist der nächste konkrete Prüfgegenstand, kein Anlass zur erneuten Umgestaltung der abgeschlossenen Shared-/Bulk-Regeln. Keine Produktionsdaten verändert.

Verifikation: vollständige Workbench-Suite **642 Tests / 4128 Assertions bestanden**. PHP-Syntax und `git diff --check` erfolgreich. Keine erneute PostgreSQL-Parallelprüfung für diese gemeinsame Vorschauprüfung; die Transaktionssperren sind unverändert.

## 16.10.2026 – U04: veraltete skalare Editorstände vor dem Speichern erkennen

Der skalare UI-Editor erfasst vor dem Laden seiner Eingabewerte einen fachlichen Fingerabdruck in einer Livewire-Locked-Property. Enthalten sind Key-ID/Name/Status/Review-Status, Finding-/Dateistatus, aktive Finding-Links sowie vorhandene skalare Sprachwerte dieses Namespace-/Lang-Key-Paars mit IDs, Locale, Text, Typ und Status. Beobachtungszeitstempel gehören nicht dazu. Der Vergleich ist bewusst konservativ über alle gespeicherten Sprachen dieses Keys; auch eine Änderung einer anderen Sprache verlangt derzeit erneutes Öffnen. Es ist kein neues Wertrevisions- oder Freigabemodell.

`TranslationWorkbenchScalarEditor::savePreview()` verlangt diesen Nachweis. `save()` nimmt innerhalb der Transaktion vor der erneuten Zuordnungsauflösung PostgreSQL-Sperren auf source_files, findings, keys, key_findings und lang_values (SHARE ROW EXCLUSIVE); der Vergleich findet erst danach statt. Auch neu angelegte Sprachzeilen werden damit erfasst. Nach erfolgreichem Speichern wird der neue Fingerabdruck noch innerhalb derselben äußeren Transaktion ermittelt. Die UI kann dadurch mehrfach bewusst speichern, ohne einen ungeschützten nachträglichen Nachweis einzulesen. Der äußere Vorgang erlaubt bis zu drei datenbankseitige Konfliktwiederholungen.

Eine Abweichung ergibt eine ValidationException im neuen flux:error `scalarEditConflict`, ohne Übersetzung oder Historie zu verändern. Eingaben bleiben erhalten. Neuöffnen lädt den aktuellen Stand und entfernt die Fehlermeldung; Schließen entfernt den Nachweis. Ein Keywechsel bereits zwischen Auswahl und Öffnen wird ebenfalls abgefangen. UI-Speichern ohne Nachweis ist gesperrt. Bestehende interne `save()`-Aufrufe ohne optionalen Nachweis bleiben direkte aktuelle Schreibaufträge; der zugrunde liegende TranslationWriter ist damit nicht pauschal zu einem revisionspflichtigen Writer geworden.

Drei neue funktionale Fälle prüfen zwei Editorstände mit neuerem gespeichertem Wert, erneutes Öffnen und wiederholtes Speichern, Keywechsel und Wertstatusänderung. Bestehende UI-Fixtures öffnen jetzt den realen Editor. Der Vergleichstest zwischen direktem Writer und UI öffnet nach dem direkten Write bewusst neu, statt einen inzwischen veralteten Editorstand als gültig zu behandeln. Die dynamischen Optionseditoren sind noch nicht an diesen Vorschauvertrag angeschlossen und bilden den nächsten Teil von U04.

Zwei native PostgreSQL-Fälle verwenden den echten skalaren Speicherdienst hinter einer bereits laufenden Sprachwertänderung. Lediglich die Locale-Konfiguration ist im Test auf en/de festgelegt. Nach Commit muss die alte Vorschau abgelehnt werden; nach Rollback darf sie speichern. Der erste Testlauf scheiterte bereits beim Aufbau der neuen Fixture an deren fehlendem Pflichtfeld source_path; das Testfeld wurde ergänzt. Dieser abgebrochene Lauf gilt nicht als erfolgreiche Parallelprüfung. Keine Produktionsdaten oder produktiven Prozesse verändert.

Verifikation: vollständige Workbench-Suite **645 Tests / 4138 Assertions bestanden**; nach Ergänzung des Nachweis-Resets beim Schließen nochmals die drei neuen Editorfälle erfolgreich (10 Assertions). Korrigierter PostgreSQL-Lauf: **80 Parallelfälle plus drei SQL-Verhaltenstests bestanden**. PHP-Syntax und `git diff --check` erfolgreich. Keine interaktive Browser-Abnahme vorgenommen.

## 16.10.2026 – U04: Vorschauprüfung der dynamischen Optionseditoren

Der dynamische Optionseditor speichert beim Laden vor den Eingabewerten einen fachlichen Fingerabdruck in einer Livewire-Locked-Property. Er enthält den gemeinsamen skalaren Zuordnungskontext, Locale-Konfiguration, bestätigte wirksame Runtime-Quellen-IDs, direkt zugehörige bzw. verknüpfte Quellen mit Identität/Typ/Status, deren Quelloptionen, gespeicherte Optionswerte und die vom bestehenden Optionsresolver gelieferten Zeilen. Zeitstempel werden nicht aufgenommen; gespeicherte Optionswerte aller Sprachen und auch der skalare Key-Kontext sind konservativ enthalten. Es ist kein allgemeiner Hash jeder Quellen-Metadatenänderung.

`TranslationWorkbenchDynamicEditor::save()` und `saveSource()` vergleichen den optionalen Nachweis innerhalb ihrer Transaktion vor Optionsauflösung und Änderung. Der UI-Weg verlangt ihn. PostgreSQL sperrt die gemeinsamen Zuordnungstabellen sowie lang_values, dynamic_sources, dynamic_source_values, dynamic_source_candidates und dynamic_key_values in SHARE ROW EXCLUSIVE MODE. Das ist eine breite Schreibsperre für die Transaktionsdauer; normale Leser bleiben möglich. Direkte interne Aufrufe ohne Nachweis behalten ihre bisherigen aktuellen Schreibverträge. Andere Datenbanktreiber erhalten durch diese native Sperre keine zusätzliche Parallelitätsgarantie.

Konflikte erscheinen im vorhandenen flux:error dynamicMultiSourceValues; Eingaben bleiben erhalten. Nach tatsächlichen Änderungen lädt der bestehende UI-Weg die Optionsfelder samt neuem Nachweis erneut, mit Erfassung des Nachweises vor dem Lesen der Eingabewerte. Beim Einstieg in den dynamischen Review und beim Schließen des Editors wird der Nachweis entfernt. Für den dynamischen Einzeltext-Dialog, der bereits den skalaren Speicherweg verwendet, wurde dessen Konfliktfeld ergänzt.

Zwei neue funktionale Tests prüfen veraltete Zielübersetzungen bei zwei Editoren, Erhalt der Eingaben/neueren Werte/Timeline, erneutes Öffnen und wiederholtes Speichern sowie veraltete einzelne Quellwertkorrekturen. Die dynamischen Fixtures durchlaufen nach dem Anlegen der Optionen nun auch den vorhandenen Schritt continueDynamicEdit. Ein Vergleichstest mit vorgeschaltetem direktem Writer öffnet anschließend bewusst neu. Die ersten Testfehler zeigten diese veralteten Fixture-Vorschauen; die Schutzregel wurde dafür nicht abgeschwächt.

Zwei PostgreSQL-Parallelfälle verwenden den echten dynamischen Speicherdienst hinter einer bereits laufenden Optionswertänderung. Commit erfordert Ablehnung der alten Vorschau; Rollback erlaubt das Speichern. Locale-Konfiguration ist dabei auf en/de festgelegt. Die Prüfung einzelner Quellwertkorrekturen ist funktional abgedeckt; ein eigener paralleler saveSource-Fall wurde in diesem Schritt nicht ergänzt. Keine interaktive Browser-Abnahme und keine Produktionsänderungen.

Verifikation: vollständige Workbench-Suite **647 Tests / 4146 Assertions bestanden**. Isoliertes PostgreSQL: **82 Parallelfälle plus drei SQL-Verhaltenstests bestanden**. PHP-Syntax und git diff --check erfolgreich. Die Vorschauabsicherung ersetzt noch nicht das geplante Wertrevisions-/Freigabemodell; dieses ist der nächste fachliche Teil von U04.

## 16.10.2026 – U04: ausdrückliche Freigabe gespeicherter Sprachwert-Snapshots

`TranslationWorkbenchValueApproval` bietet eine gemeinsame Vorschau und ausdrückliche Freigabe für skalare Werte und Optionswerte. Geprüft werden Berechtigung, aktive Finding-/Dateizuordnung, eindeutiger und geprüfter Key sowie aktiver, nicht leerer Sprachwert mit passendem Eigentümer. Der Snapshot enthält Wertidentität, Sprache, Key/Finding, Optionsschlüssel, Text, Typ, natives Label und Status. Ein abweichender Vorschau-Fingerabdruck wird vor dem Schreiben abgelehnt.

Freigaben verwenden die bestehenden Reviews (`translation_value` / `translation_value_approved`) und den zentralen TimelineRecorder. Review und Timeline werden gemeinsam transaktional geschrieben; ein Historienfehler rollt die Freigabe zurück. Derselbe bereits freigegebene Snapshot erzeugt keinen zweiten Review-/Timeline-Eintrag. PostgreSQL sperrt Zuordnungs-, Wert- und Reviewtabellen in SHARE ROW EXCLUSIVE MODE; andere Treiber erhalten daraus keine zusätzliche Parallelitätsgarantie.

Dies ist eine additive Dienstgrundlage, noch keine Umstellung der UI oder des Exports. Vorhandene Veröffentlichungen, Sprachdateien und Werte bleiben unverändert. Insbesondere wird das bisherige automatische reviewed_at beim Speichern dynamischer Werte noch nicht entfernt. Der Export wertet den neuen Reviewtyp noch nicht aus. Die Übernahme vorhandener Freigaben und die UI-Anbindung müssen vor einer verbindlichen Exportprüfung geregelt werden.

Der Nachweis ist ausdrücklich inhaltsbezogen, keine monotone Wertrevision: Bei A → B → A ohne zwischenzeitliche Freigabe kann die frühere Freigabe wieder zum identischen Snapshot passen. Für eine Freigabe jeder einzelnen Bearbeitungsrevision ist deshalb noch ein durchgängiger Revisionsvertrag an den Schreibwegen erforderlich. `isApproved()` validiert den aktuellen Eigentümer und wirft bei ungültiger Zuordnung einen Validierungsfehler, statt diesen als bloß fehlende Freigabe zu verstecken.

Acht neue funktionale Fälle (je skalar/Option) prüfen unveränderte Werte/Dateien, wiederholte Freigabe ohne doppelte Historie, veraltete Vorschau, Historien-Rollback, getrennte Sprachfreigaben und nachträglich ungeprüften Key. Keine produktiven Scans, Exporte oder Datenänderungen; keine interaktive UI-Abnahme. Ein eigener nativer PostgreSQL-Paralleltest für diesen neuen Dienst steht noch aus.

Verifikation: vollständige Workbench-Suite **655 Tests / 4180 Assertions bestanden**. PHP-Syntax und `git diff --check` erfolgreich. Der zuvor bestandene PostgreSQL-Lauf (82 Parallelfälle plus drei SQL-Verhaltenstests) wurde für diesen additiven Dienst nicht wiederholt und gilt nicht als dessen Parallelitätsnachweis.

## 16.10.2026 – U04: Datenbankrevisionen für gespeicherte Sprachwerte

Die additive Migration `2026_10_16_000001_add_value_revisions.php` ergänzt `value_revision` (Bestand/Neuanlage: 1) an lang_values und dynamic_key_values. Datenbanktrigger erhöhen die Revision bei tatsächlich geänderten fachlichen Feldern: skalar Locale/Namespace/Lang-Key/Translation-Key/Text/Typ/Status; Optionen Key-ID/Optionsschlüssel/Locale/Text/natives Label/Status. Dadurch werden auch Query-Builder-Importe, Bulk-Änderungen und direkte Updates erfasst. Gleichbleibende Werte sowie reine Beobachtungs-, Herkunfts- und Zeitstempeländerungen erhöhen sie nicht. Eine zurückgerollte Änderung rollt auch die Revision zurück.

PostgreSQL verwendet BEFORE UPDATE und setzt die Revision aus dem alten Datenbankwert; SQLite verwendet AFTER UPDATE OF der fachlichen Spalten mit einem ausschließlich auf den Zähler gerichteten internen Update, das keine Rekursion auslöst. Anwendungscode darf den Zähler nicht selbst schreiben. Die Migration unterstützt PostgreSQL und SQLite explizit und bricht bei anderen Treibern vor Schemaänderungen ab. Es werden keine anderen Datenbanktreiber stillschweigend mit einem unvollständigen Revisionsvertrag bedient.

Der Freigabe-Snapshot enthält jetzt die Wertrevision. A → B → A verlangt damit eine neue Freigabe; die ältere bleibt historisch erhalten. Auch skalare und dynamische Editor-Fingerabdrücke enthalten die Revision, sodass dieselbe Änderungsfolge eine alte Editorvorschau nicht wieder gültig macht. Die Migration wird nicht produktiv ausgeführt. Während dieser vorbereiteten Einführung behalten die vorhandenen Editoren ohne Revisionsspalte ihren bisherigen Inhaltsvergleich; der neue Freigabedienst lehnt eine Vorschau ohne Migration ausdrücklich ab. Bereits geöffnete Editoren müssen nach späterer Migration neu geöffnet werden, weil sich ihr Nachweis ändert.

Dies ist eine Revision des gespeicherten Sprachwerts, kein vollständiger Versionszähler für sämtliche Key-, Finding- oder Quellenänderungen. Bestehende Reviews ohne Wertrevision werden nicht automatisch als neue ausdrückliche Wertfreigabe übernommen. UI-/Exportanbindung und geregelte Bestandsfreigabe bleiben separat offen; bisheriges reviewed_at und Exportverhalten sind unverändert. Kein produktiver Scan, Export oder Datenbankeingriff.

Acht zusätzliche funktionale Fälle prüfen A → B → A einschließlich neuer Freigabe und alter Historie, No-op/Metadaten/NULL/Rollback/Statuswechsel, Ablehnung alter Editorvorschauen und Migration down/up mit unveränderten Bestandswerten. Zwei native PostgreSQL-Verhaltenstests bestätigen die Trigger; vier Parallelfälle prüfen Freigaben hinter einem laufenden Wertupdate: Commit lehnt die Vorschau ab, Rollback erlaubt sie. Der erste native Lauf brach an einem fehlenden source_signature-Pflichtfeld in der neuen Test-Fixture ab; erst der korrigierte Lauf zählt als Nachweis. Bei den funktionalen Tests wurde außerdem die Wiederverwendung eines von SQLite-UPDATE veränderten Query Builders korrigiert (frische Kopie für Updates), nicht der Revisionsvertrag abgeschwächt.

PostgreSQL-Verifikation: **86 Parallelfälle plus fünf SQL-Verhaltenstests bestanden**; sämtliche 91 JSON-Ergebnisse geprüft. Migration einschließlich Rollback/erneuter Anwendung funktional mit SQLite getestet. PHP-Syntax und git diff --check erfolgreich. Keine interaktive Browser-Abnahme vorgenommen.

Abschließende vollständige Workbench-Suite: **663 Tests / 4220 Assertions bestanden**.

## 16.10.2026 – U04: Bestandswerte ausdrücklich im Editor prüfen und freigeben

Die skalaren, dynamischen Einzeltext- und Optionseditoren erhalten einen Flux-Einstieg „Review stored values“ mit gemeinsamem Modal. Die separate Vorschau zeigt ausschließlich gespeicherte aktive, nicht leere Werte der verfügbaren Editorsprachen, einschließlich Translation-Key/Optionsschlüssel, Locale, Wertrevision und vollständigem escaped Text. Nicht gespeicherte Eingaben bleiben im ursprünglichen Editor erhalten und werden durch diese Aktion weder gespeichert noch freigegeben. Der automatische Schließ-Countdown des skalaren Editors wird beim Öffnen der Vorschau angehalten.

`TranslationWorkbenchValueApproval::inspect()` ordnet jeden angezeigten Wert einem der Zustände unapproved, approved oder changed zu. Maßgeblich ist ausschließlich ein ausdrücklicher Review mit passendem Revisions-Fingerabdruck. Ein älterer Key-Review ist kein Wertreview; das bisher beim Speichern dynamischer Werte gesetzte reviewed_at wird nur als historischer Zeitstempel angezeigt. Es erfolgt keine automatische Massenübernahme bestehender Freigaben. Vorhandene Übersetzungen und bisherige Exportregeln bleiben unverändert; auch das Modal erklärt diese Trennung ausdrücklich.

Die Vorschauzeilen einschließlich IDs und Nachweisen sind Livewire-Locked. Bestätigung nimmt nur einen Zeilenindex entgegen, prüft erneut die Berechtigung und verwendet den bestehenden transaktionalen Freigabedienst zur Eigentümer-/Revisionsprüfung. Eine veraltete Vorschau oder eine ungültige Zuordnung ergibt valueApproval im vorhandenen Flux-Fehlerbereich. Schließen bzw. Initialisieren eines anderen Editors löscht die Vorschau; erneutes Öffnen beginnt ebenfalls mit leerem Zustand, damit ein fehlgeschlagener Aufbau keine vorherigen freigabefähigen Zeilen zurücklässt. Der neue Dienst verlangt weiterhin die vorbereitete Revisionsmigration; diese wurde nicht produktiv ausgeführt. Ohne Migration zeigt die Vorschau den konkreten Hinweis statt einen Datenbankfehler auszulösen.

Funktionale Ergänzungen prüfen unveränderte ungespeicherte Eingaben, alte Options-reviewed_at-Angaben ohne Freigabe, Bestätigung/Schließen, veralteten Dialog und anschließende neue Freigabe, ungeprüften Eigentümer, entzogene Berechtigung sowie Sprach-/Leerwertfilterung. Drei gerenderte Blade-/Flux-Fälle prüfen die Statusanzeigen und HTML-Escaping. Keine interaktive Browser-Abnahme, keine produktive Bestandsprüfung oder Migration; kein produktiver Scan/Export. Die PostgreSQL-Transaktionslogik des Freigabediensts bleibt unverändert gegenüber dem zuletzt geprüften Stand (86 Parallelfälle und fünf SQL-Verhaltenstests).

Das Schließen des Flux-Dialogs über seine Model-Bindung (z. B. Schließen-Icon/Escape) löscht den Nachweis über updatedValueApprovalModalOpen ebenfalls; ein eigener funktionaler Test deckt diesen Weg ab. Auch der Wechsel in den dynamischen Review entfernt die Vorschau. Im ersten Gesamtlauf scheiterte ausschließlich die neue Berechtigungs-Test-Fixture, weil Auth::logout den produktiven Logout-Listener auslöste; sie verwendet jetzt wie die bestehenden Gasttests auth()->forgetUser. Der gezielte Berechtigungstest besteht (1 Test / 2 Assertions), ebenso der zusätzliche Model-Schließtest (1 Test / 2 Assertions).

Verifikation: korrigierte vollständige Workbench-Suite **673 Tests / 4277 Assertions bestanden**; der danach ergänzte Model-Schließtest zusätzlich **1 Test / 2 Assertions bestanden**. PHP-Syntax, gerenderte Flux-Views und git diff --check erfolgreich.

## 16.10.2026 – U05: lesende Sprachdatei-Vorschau aus der Wertprüfung

Das gemeinsame Wertfreigabe-Modal bietet „Preview language files“. Die neue `TranslationWorkbenchPublicationPreview` verwendet ausdrücklich `TranslationWorkbenchLangFileExporter::export(write: false)` für genau den geprüften Owner-Key und die Editorsprachen. Keine zweite Dateiplanungs-/Formatierungslogik und keine neue Schreibaktion. Die Vorschau listet geplante Änderungen mit veröffentlichtem und gespeichertem Wert, aktuellem Freigabestatus sowie separat vorgesehene Löschungen und Strukturkonflikte.

Bereits veröffentlichte, unveränderte Texte werden als beibehalten gezählt, nicht nachträglich als freigegeben markiert. Neue/geänderte Zeichenketten werden mit dem geprüften Sprachwert-Snapshot abgeglichen; fehlt eine aktuelle ausdrückliche Freigabe, zählt die Änderung als nicht freigegeben. Nicht zuordenbare oder nicht-string-typisierte geplante Werte werden konservativ als unverified ausgewiesen. Optionen ohne historischen reviewed_at-Zeitstempel, die der unveränderte bisherige Exportfilter ausschließt, erhalten einen eigenen Zähler. Die explizite neue Wertfreigabe ändert diesen Filter noch nicht.

Wichtig: Diese getrennten Lesezugriffe bilden keinen atomaren DB-/Dateisnapshot. Auch ein Textvergleich zwischen Prüfung und Dateiplan kann keine zwischenzeitliche Änderung und Rückänderung oder spätere Dateiveränderung ausschließen. Der Bericht ist ausdrücklich advisory_only, enthält keinen canPublish-Schalter und wird nicht als Ausführungsnachweis verwendet. Die spätere schreibende Veröffentlichung braucht weiterhin eine gemeinsame Prüfung des aktuellen Daten-/Dateistands, Löschfreigaben und einen Wiederaufnahmevertrag. Vorhandene Exportkommandos werden durch diesen Schritt nicht auf Revisionsfreigaben umgeschaltet.

Der UI-Kontext und Bericht sind Locked; der Einstieg prüft erneut Berechtigung/Zuordnung und verlangt mindestens eine Sprache. Fehlerhafte dynamische Owner aus dem bestehenden Exporter werden als valueApproval-Validierungsfehler im Dialog angezeigt. Nach einer Freigabe, beim erneuten Öffnen und beim Schließen wird der alte Bericht verworfen. Ohne Revisionsmigration bleibt die Vorschau entsprechend gesperrt. Kein produktiver Scan, Export oder Datenbankeingriff.

Der neue Löschplan-Test deckte einen vorhandenen Exporterfehler auf: Bei ausschließlich inaktiven Werten blieb die leere activeScopes-Liste eine Eloquent-Collection; merge mit Scope-Arrays rief getKey auf einem Array auf. exportFileScopes wandelt die Wert-Collection vor der Scope-Abbildung jetzt explizit in eine Basis-Collection um. Die Auswahl-, Pruning- und Schreibregeln wurden dabei nicht verändert.

Sechs neue funktionale Fälle prüfen skalare/Options-Freigaben gegen Dateipläne, veränderte Werte nach Freigabe, unveränderte publizierte Werte ohne implizite Freigabe, reine Löschpläne ohne Dateieingriff, Report-Invalidierung und den unveränderten reviewed_at-Filter. Ein zusätzlicher gerenderter Flux-Test prüft Alt-/Neu-Vergleich, Löschungen/Konflikte und HTML-Escaping. Keine interaktive Browser-Abnahme; keine Änderung an den bereits nativ geprüften PostgreSQL-Schreibsperren.

Verifikation: vollständige Workbench-Suite **681 Tests / 4319 Assertions bestanden**. PHP-Syntax, gerenderte Flux-Vorschau und git diff --check erfolgreich.

## 16.10.2026 – U05: Nachprüfung des Daten-/Dateistands einer Vorschau

Der Sprachdatei-Exporter ergänzt in jedem Dateiplan source_hash (SHA-256, null bei fehlender Datei) und result_hash der geplanten Ausgabe. Vor und nach dem Lesen der bestehenden Sprachdatei werden ihre Bytes geprüft; ein währenddessen geänderter Stand wird abgewiesen. Ein vorhandener OPcache-Eintrag wird vor dem require invalidiert, damit der gelesene Array-Inhalt nicht aus einem älteren Cache-Eintrag stammt. Die gemeinsame assertSourceFilesUnchanged-Prüfung kontrolliert nach Abschluss der Vorschau nochmals sämtliche betroffenen Dateien. Es entsteht weder eine zweite Planungslogik noch eine neue Schreibaktion.

`TranslationWorkbenchPublicationPreview::preview()` liest unter einer Transaktion. PostgreSQL hält SHARE-Sperren auf Quellen/Findings/Keys/Zuordnungen, beiden Werttabellen und Reviews bis zum Abschluss. Dadurch können deren Schreiber während der Datenbank-Lesephase keinen Zwischenstand einschieben. Der Bericht erhält einen Fingerabdruck über Kontext, sortierte Sprachen, geprüfte Wertrevisionen/Freigabestatus und Dateipläne samt Datei-/Ergebnishashes. `verify()` erstellt denselben aktuellen Vergleich erneut und lehnt abweichende Fingerabdrücke ab, statt eine alte Benutzerentscheidung auf einen neuen Plan umzubiegen.

Im gemeinsamen Flux-Dialog steht „Check preview again“ bereit. Nur der Locked-Fingerabdruck der dort erstellten Vorschau wird verwendet. Eine fehlgeschlagene Prüfung entfernt den positiven Prüfstatus, behält aber den bisherigen Bericht zur Orientierung. Beim Neuladen der Vorschau, bei Wertfreigabe und beim Schließen wird der positive Status ebenfalls entfernt. Die Erfolgsmeldung bezieht sich ausdrücklich auf den letzten Prüfzeitpunkt und ist keine Veröffentlichungsfreigabe.

Grenzen: Die breite PostgreSQL-Sperre schützt nur die erfassten Datenbanktabellen während des Lesevorgangs. Locale-/Settings-Konfiguration wird nicht mit diesen Tabellen gesperrt. Dateien sind weiterhin externe Ressourcen; Hashkontrollen ersetzen weder eine gemeinsame DB-/Dateitransaktion noch einen gegen nicht kooperierende Dateischreiber geschützten Commit. Eine Dateiänderung mit zwischenzeitlicher Rückkehr zu identischen Bytes ist durch Hashes nicht historisch erkennbar. Der neue verify-Aufruf schreibt nichts; sein Ergebnis darf nicht als dauerhafte Erlaubnis für einen späteren Export dienen. Der reguläre Export wird weiterhin nicht nachträglich durch diese UI-Prüfung gesperrt oder freigegeben. Migration und Produktionsbestand unverändert.

Sechs funktionale Fälle ergänzen Wertänderung/Rückänderung, neue Freigabe, Datei anlegen/löschen/reine Kommentaränderung, Erhalt der alten UI-Vorschau bei Fehler sowie eine Dateiänderung zwischen Exportplanung und Abschluss der Vorschau. Zwei zusätzliche native PostgreSQL-Fälle lassen verify hinter einem laufenden Wertupdate warten: Commit erfordert Ablehnung, Rollback erlaubt den unveränderten Vergleich. Der native Runner verwendet dafür ausschließlich sein temporäres Sprachdateiverzeichnis und isolierte Locale-Settings.

Verifikation: vollständige Workbench-Suite **687 Tests / 4332 Assertions bestanden**. Isoliertes PostgreSQL: **88 Parallelfälle plus fünf SQL-Verhaltenstests bestanden**, sämtliche 93 JSON-Ergebnisse geprüft. PHP-Syntax und git diff --check erfolgreich. Keine produktiven Scans/Exporte, keine Migration und keine interaktive Browser-Abnahme.

## 16.10.2026 – U05: geprüfte Sprachdateien schreiben und unterbrochene Läufe fortsetzen

`TranslationWorkbenchPublicationRunner` ergänzt einen internen publish-/resume-Dienst für Sprachdateien. Er ist noch nicht als UI-Schreibaktion oder als Ersatz der bestehenden Exportkommandos angeschlossen. Er autorisiert jeden Aufruf, übernimmt eine eigene Datenbanktransaktion und hält unter PostgreSQL SHARE ROW EXCLUSIVE auf den bisherigen Zuordnungs-/Wert-/Reviewtabellen. Eine lokale exklusive flock-Sperre serialisiert die Läufe dieses Diensts. Vor dem ersten Schreiben wird der vollständige Vorschau-Fingerabdruck erneut geprüft; ungeprüfte Wertänderungen, Strukturkonflikte, vorgesehene Löschungen und durch den bisherigen Optionsfilter ausgeschlossene Werte sperren diesen neuen Weg.

Der vorhandene Exporter stellt über prepare die intern vorbereiteten Inhalte zur Verfügung; seine bisherige export-Report-Schnittstelle liefert diese weiterhin nicht aus. Source-/Result-Hashes der vorbereiteten Dateien müssen zur geprüften Vorschau passen. Der zusätzliche data_fingerprint beschreibt Werte/Freigaben/Key-Kontext unabhängig von den schon geschriebenen Dateien und wird bei einer Wiederaufnahme erneut verglichen.

Vor dem Schreiben entsteht ein Laufjournal unter storage/app/private/translation-workbench/publications mit UUID, Kontext, ursprünglichem Akteur, vorbereiteten Inhalten, Prüfsummen und Fortschritt. Journale werden über eine temporäre Datei und rename mit 0600 geschrieben; das neue Verzeichnis mit 0700 angelegt. Jede Sprachdatei wird ebenfalls vollständig in ihrem Zielverzeichnis vorbereitet und dann per rename ersetzt. Vorher wird ihre ursprüngliche Prüfsumme nochmals geprüft, nach dem gesamten Schreibschritt werden die Ergebnisprüfsummen der geschriebenen Dateien kontrolliert. Bestehende Dateimodi werden übernommen; neue Dateien erhalten 0644. Symlink-Ziele und unsichere Locale-/Namespace-Pfadsegmente werden abgewiesen.

Bei Teilfehlern bleiben bereits geschriebene Dateien erhalten und der Lauf wird als interrupted protokolliert. resume akzeptiert nur Dateien, die noch exakt dem ursprünglichen oder schon dem geplanten Ergebnisstand entsprechen. Geänderte Datenrevisionen/Freigaben oder andere Dateiinhalte blockieren die Fortsetzung. Ein neuer Lauf darf offene Journale auf denselben Dateien nicht übergehen. Eine automatische Rücknahme oder Konfliktbereinigung wird nicht durchgeführt; bei externen Änderungen ist eine explizite Auflösung erforderlich.

Nach den Dateien werden die bisherigen lang_file_value_exported-Timeline-Ereignisse mit publication_run_id und ein gemeinsamer Review-Beleg (publication / lang_files_published) in derselben Datenbanktransaktion geschrieben. Scheitert die Historie, bleibt kein halber DB-Beleg; das Datei-Journal ermöglicht die Fortsetzung. Existiert der DB-Beleg bei erneutem resume schon, werden nur die Ergebnisdateien geprüft und der Journalabschluss nachgezogen. So entstehen keine doppelten Timeline-Einträge, auch wenn nach dem Dateiersatz oder nach dem DB-Commit die letzte Journalaktualisierung ausfiel.

Grenzen: Es gibt weiterhin keine atomare Transaktion über mehrere Dateien und die Datenbank. Die Wiederaufnahme ist auf Prozess-/Schreibfehler ausgelegt, nicht als nachgewiesene Garantie bei Stromausfall ohne Dateisystem-fsync. Die flock-Sperre schützt nur kooperierende Aufrufe dieses Diensts am gemeinsamen Journalort; bestehende Legacy-Exporter und externe Editoren verwenden sie noch nicht. Prüfsummenprüfungen verkleinern das Konfliktfenster, können beliebige nicht kooperierende Schreiber zwischen Prüfung und rename aber nicht ausschließen. Code-Replacement, Löschfreigaben, UI-Ausführung und explizite Auflösung eines wegen Fremdänderungen blockierten Laufs bleiben getrennte nächste Schritte. Keine produktive Migration, Veröffentlichung oder Datenänderung durchgeführt.

Tests: erfolgreiche Veröffentlichung/Wiederaufnahme ohne doppelte Historie; Ablehnung ungeprüfter/veralteter Vorschauen; Fehler an Datei zwei mit Wiederaufnahme bzw. Fremdänderung; Historienfehler mit bereits geschriebenen Dateien; verlorener Journalfortschritt; DB-Beleg vorhanden bei fehlendem Journalabschluss; geänderte Werte vor Recovery. Der native PostgreSQL-Runner verwendet ausschließlich temporäre Sprachdateien, Storage/Journale und die isolierte Datenbank und prüft Veröffentlichung plus idempotentes resume.

Verifikation: vollständige Workbench-Suite vor der abschließenden Ergebnisdatei-Kontrolle **692 Tests / 4367 Assertions bestanden**. Isoliertes PostgreSQL: **88 Parallelfälle plus sechs SQL-Verhaltenstests bestanden**, alle 94 JSON-Ergebnisse geprüft. Die abschließende Dateikontrolle und der zusätzliche Datenänderungs-Recoveryfall werden mit der gezielten Veröffentlichungssuite nochmals geprüft. PHP-Syntax und git diff --check erfolgreich. Keine interaktive UI-Abnahme, da der schreibende Dienst noch nicht an die UI angeschlossen ist.

Abschließende gezielte Veröffentlichungssuite: **6 Tests / 42 Assertions bestanden**, einschließlich der letzten Dateikontrolle und geänderter Daten vor Recovery.

## 16.10.2026 – U05: Veröffentlichung und Wiederaufnahme im Wertfreigabe-Dialog

Der vorhandene Flux-Dialog bietet nach der Dateivorschau die explizite Aktion „Publish approved values to language files“. Sie ruft ausschließlich den zentralen PublicationRunner auf. Der Dienst überprüft den gesperrt gehaltenen Vorschau-Fingerprint unmittelbar vor dem Schreiben erneut. Fehlende Freigaben, Löschpläne, Strukturkonflikte und ausgeschlossene Optionswerte sperren auch die sichtbare Schaltfläche; maßgeblich bleibt die serverseitige Prüfung. Die Aktion ersetzt keinen Quellcode.

Eine getrennte Laufübersicht zeigt nur Journale des aktuellen Kind-/Key-/Finding-Kontexts, deren Abschlussstatus und Fehler sowie den aktuellen Dateivergleich: ursprünglicher Stand, vorbereitetes Ergebnis oder abweichender Inhalt. Der DB-Abschlussbeleg wird separat angezeigt. Vorbereitete Dateiinhalte werden nicht an Livewire ausgeliefert. „Recheck and resume publication“ verwendet eine gesperrte Lauf-Liste und prüft den Kontext im Dienst erneut. Berechtigungen werden bei jeder Aktion neu geprüft. Der Dateistand ist eine Momentaufnahme bei Laden/Aktualisieren der Übersicht; resume führt die maßgebliche Prüfung nochmals aus.

Veraltete Vorschauen und Laufzeitfehler werden im Dialog angezeigt; ein Schreibversuch verbraucht die Vorschau auch bei Teilfehlern. Bereits geschriebene Dateien und fehlende DB-Abschlüsse bleiben über das Journal nachvollziehbar. Es gibt kein erzwungenes Überschreiben oder Verwerfen blockierter Läufe. Eine explizite Konfliktauflösung bei geänderten Daten/Fremdänderungen bleibt offen, ebenso die Umstellung der bisherigen Kommandos auf diesen Dienst. Keine produktive Migration, Veröffentlichung oder Bestandsänderung.

Neue funktionale UI-Tests: Veröffentlichung und kontextgebundene Laufanzeige, verbrauchter/veralteter Nachweis, Abbruch bei Datei zwei mit Fortsetzung, entzogene Berechtigungen. Gerenderter Flux-Test: Fehler-/Dateistatus und Escaping. Gezielte Veröffentlichungstests: 21 Tests / 107 Assertions bestanden. Keine interaktive Browser-Abnahme.

Verifikation: vollständige Workbench-Suite **698 Tests / 4410 Assertions bestanden**. PHP-Syntax und git diff --check erfolgreich. Die PostgreSQL-Sperr-/Transaktionslogik wurde in diesem UI-Schritt nicht geändert; der native Lauf wurde deshalb nicht wiederholt.

## 16.10.2026 – U05: kontrolliertes Schließen blockierter Veröffentlichungsläufe

Der Wertfreigabe-Dialog bietet für offene Läufe ohne Veröffentlichungs-Abschlussbeleg „Review closure without further writing“. Die gesonderte Vorschau zeigt den Lauf und für jede Datei den ursprünglichen, geplanten oder abweichenden Stand. Erst die ausdrückliche Aktion „Keep files and close this attempt“ mit Begründung schließt den Versuch. Sie veröffentlicht nichts, bestätigt keine Übersetzungsrevision, nimmt keine Dateiänderung zurück und erzeugt keinen erfolgreichen Veröffentlichungsbeleg.

PublicationRunner prüft Administratorberechtigung, Laufkontext, fehlenden Abschlussbeleg sowie den Hash von Journal und aktuell beobachteten Dateiprüfsummen nochmals unter derselben Dateisperre und PostgreSQL-Tabellensperre. Die Entscheidung wird als Review publication / publication_closed_without_writing mit Begründung, Benutzer, Zeit, Lauf-ID und beobachteten Dateiprüfsummen gespeichert. Das ursprüngliche Journal einschließlich vorbereitetem Inhalt bleibt erhalten; nur sein Status wird geschlossen. Es werden weder vermeintlich erfolgreiche lang_file_value_exported-Ereignisse noch nachträgliche Wertefreigaben erzeugt.

Der DB-Beleg ist maßgeblich: Auch wenn anschließend die Statusaktualisierung im Journal fehlschlägt, zeigt die Laufübersicht „Closed without further writing“, resume verweigert weitere Schreibvorgänge dieses Laufs, und ein neuer Plan darf dessen Dateibereich benutzen. Ohne erfolgreichen DB-Beleg bleibt der Bereich gesperrt. Ein neuer Lauf benötigt weiterhin eine neue Vorschau und die regulären Prüfungen/Freigaben. Unveränderte Teilresultate gelten dabei als vorhandene Dateiinhalte, nicht als nachträglich erfolgreich abgeschlossener alter Lauf.

UI und Dienst prüfen unabhängig voneinander. Der Vorschau-Nachweis liegt als Locked-Property vor; die Begründung ist bearbeitbar und auf 2000 Zeichen beschränkt. Der Versuch verbraucht den Nachweis auch bei einer Ablehnung. Bereits abgeschlossene/geschlossene Läufe, falsche Kontexte, entzogene Rechte, leere Begründungen und nach der Vorschau geänderte Dateien werden abgewiesen.

Grenzen: Das Schließen ist eine dokumentierte Entscheidung, den aktuellen Dateistand zu behalten, keine automatische fachliche Konfliktlösung und kein Rollback. Beobachtete Prüfsummen belegen den Stand beim Prüfen, sind aber keine Inhaltskopie fremder Änderungen. Nicht kooperierende externe Dateischreiber bleiben außerhalb der gemeinsamen Sperre. Bestehende Exportkommandos sind noch nicht auf diesen Dienst umgestellt. Keine produktive Migration, Bestandsänderung oder Veröffentlichung; keine interaktive Browser-Abnahme.

Gezielte Suite: 29 Tests / 155 Assertions bestanden. Enthalten sind Teilveröffentlichung mit Fremdänderung, veralteter Abschlussnachweis, leere Begründung, scheiternder DB-Beleg, verlorener Journalabschluss, gesperrtes resume, erneute Planung sowie UI-Darstellung und Escaping.

Verifikation: vollständige Workbench-Suite **706 Tests / 4458 Assertions bestanden**. Isoliertes PostgreSQL: **95 JSON-Ergebnisse bestanden**, darunter der neue Fall publication-close-and-replan; alle Ergebniszeilen geprüft, keine zusätzlichen Fehlerausgaben. PHP-Syntax und git diff --check erfolgreich.

## 16.10.2026 – U05: Workbench-Artisan-Export über den gemeinsamen Veröffentlichungsdienst

translation-workbench:export-lang-files verwendet für Schreibvorgänge jetzt PublicationRunner statt LangFileExporter::export(write: true). Ein pauschales --write ist nicht mehr möglich. Der bisherige breite Bestandsbericht ohne Veröffentlichungsoptionen bleibt lesend verfügbar; seine Existenz ist kein Freigabenachweis.

Für eine konkrete Vorschau werden --actor=USER_ID, --finding=FINDING_ID, genau ein --key=TRANSLATION_KEY und mindestens ein --locale=LOCALE angegeben; für Optionswerte zusätzlich --dynamic-options. Der Benutzer wird über den konfigurierten Auth-Provider geladen und muss Admin/Super-Admin sein. Die Option benennt den verantwortlichen Benutzer für einen bereits zum CLI-Zugriff berechtigten Betreiber; sie ist keine zusätzliche Passwortauthentifizierung. Der vorherige Auth-Kontext wird anschließend wiederhergestellt.

Die Vorschau schreibt wie bisher den JSON-Bericht und zeigt dessen fingerprint. Veröffentlichung benötigt denselben Scope plus --write --fingerprint=HASH. Der zentrale Dienst prüft den aktuellen Daten-/Dateistand und Wertfreigaben neu und führt Journal, Dateiersatz und Historie aus. --resume=RUN_UUID mit --actor=USER_ID verwendet denselben Wiederaufnahmepfad wie die UI; die Kombination mit weiteren Export-Scope-/Schreiboptionen wird abgelehnt. Fehler liefern Exitcode 1. Es werden keine Freigaben durch den CLI-Befehl erzeugt.

translation:workbench lehnt automatische Sprachdateiveröffentlichung durch --write-lang-files bzw. --complete ohne --skip-lang-file-export vor dem ersten Verarbeitungsschritt ab. Der UI-Start speichert und übergibt nun explizit --complete --skip-lang-file-export; die Startmeldung erklärt den separaten Veröffentlichungsschritt. Die bisherige globale Sprachdatei-Schreibbarkeitsprüfung vor diesem UI-Pipelinestart entfällt, weil dieser Lauf keine Sprachdateien mehr schreibt. Die Prüfung der Quellcodedateien bleibt bestehen. Die eigentliche Sprachdateiveröffentlichung kontrolliert ihre Schreibvorgänge weiterhin im zentralen Dienst.

Noch offen und ausdrücklich nicht umgestellt: der separate App-Befehl translations:export-lang-files (TranslationList-Datenbestand), seine Aufrufer project:translation/ProjectBuild sowie die direkte schreibende LangFileExporter-API. Der Legacy-Export besitzt die bisherige Workbench-Konfliktsperre, aber noch keinen gemeinsamen Journal-/Revisionsvertrag. Diese Quelle darf nicht automatisch als Workbench-Wertfreigabe interpretiert werden. Daher ist die Vereinheitlichung aller Exportwege noch nicht abgeschlossen.

Keine produktive Veröffentlichung, Migration oder Datenänderung. Die ersten Gesamttests meldeten erwartungsgemäß zwei überholte Schreib-/Pipeline-Konflikt-Erwartungen sowie einen falschen Befehlsnamen in der neuen Test-Fixture. Die Tests unterscheiden jetzt frühe Ablehnung unbelegter Schreibaufträge und weiterhin detaillierte Konfliktberichte im Lesemodus. Der Befehlsname wurde auf translation:workbench korrigiert.

Gezielte abschließende Kommando-/Pipeline-Suite: **12 Tests / 57 Assertions bestanden**. Zusätzlich geprüft: falscher/nicht vorhandener CLI-Benutzer, Wiederherstellung des vorherigen Auth-Kontexts, veralteter/fehlender Nachweis, CLI-Wiederaufnahme ohne doppelten Beleg und vollständiger gespeicherter Pipeline-Aufruf mit explizit ausgeschlossenem Export. Dessen Test isoliert die bereits separat geprüfte Hintergrund-Autorisierung, prüft aber den gespeicherten Optionsvertrag tatsächlich. Der UI-Handler wurde angepasst; der derzeit auskommentierte Startknopf wurde nicht aktiviert. Keine interaktive UI-Abnahme.

Verifikation des abschließenden Stands: vollständige Workbench-Suite **713 Tests / 4488 Assertions bestanden**; PHP-Syntax und git diff --check erfolgreich. Die PostgreSQL-Sperr-/Journalimplementierung blieb unverändert; der native Runner wurde in diesem Kommando-Integrationsschritt nicht nochmals ausgeführt.

## 16.10.2026 – U05: Legacy-Export und direkte Exporter-Schreibwege stillgelegt

Der App-Befehl translations:export-lang-files akzeptiert nur noch --dry-run. Ein Aufruf ohne dieses Flag scheitert ausdrücklich vor dem Lesen der Legacy-Tabellen; er veröffentlicht nichts und verweist auf den geprüften Workbench-Veröffentlichungsweg. Der Vergleich selbst behält die vorhandene Auswahl-/Konfliktlogik, erzeugt aber keine Verzeichnisse und schreibt keine Sprachdateien. Die bisherige private Dateischreibfunktion wurde durch eine reine Statusermittlung ersetzt. Berichtszähler heißen nun „would be created/updated“; die protokollierte Anzahl geschriebener Dateien bleibt null.

ProjectTranslations (project:translation) und ProjectBuild rufen diesen Schritt explizit mit --dry-run auf und bezeichnen ihn als Legacy-Exportvergleich ohne Schreiben. Ein Vergleichskonflikt stoppt die Aufrufer nach ihrer bisherigen Fehlerregel. Die separate Redundanzprüfung erhält dieses Flag nicht versehentlich. Andere Schritte dieser Projektkommandos bleiben unverändert; diese Gesamtkommandos sind damit nicht pauschal schreibfrei.

TranslationWorkbenchLangFileExporter plant ausschließlich. export(write: true) meldet vor jeder Planung eine LogicException mit Verweis auf PublicationRunner; die Argumentstelle dient nur der expliziten Ablehnung, nicht als Alias oder anderer Schreibpfad. Der frühere File::put-Zweig wurde entfernt. prepare liefert weiterhin die intern benötigten Inhalte an den zentralen Veröffentlichungsdienst. UI und Workbench-Artisan nutzen bereits diesen Dienst mit Freigaben, Prüfsummen, Sperre, Journal und Wiederaufnahme. W05 in der sichtbaren Schreibvertrags-Dokumentation benennt diesen Stand.

Bestehende erfolgreiche Workflow-/Options-Publikationstests führen jetzt wirkliche Revisionsfreigaben und PublicationRunner aus. Die Tests für Bestandsdateien ohne Freigabe erzeugen ausdrücklich eine bestehende Datei-Fixture statt den alten Exporter als versteckten Schreibhelfer zu verwenden. Array-/Scalar-Konflikte und Pruning bleiben als reine Planprüfungen erhalten; tatsächliches Pruning benötigt weiterhin einen eigenen Freigabevertrag und wird durch den Publikationsdienst blockiert. Zusätzliche Tests prüfen frühe Ablehnung des Legacy-Schreibaufrufs ohne vorhandene Legacy-Tabellen, lesenden Vergleich ohne Workbench-Konflikt und das --dry-run-Argument des Projektkommandos.

Keine produktiven Kommandoläufe, Veröffentlichungen, Migrationen oder Datenänderungen. Der TranslationList-Datenbestand wird nicht automatisch umgedeutet oder als freigegeben übernommen. Die Entscheidung zur Übernahme verbleibender Legacy-Werte in Foundation ist eine getrennte fachliche Aufgabe. Weitere explizite Quellcode-/Cleanup-Werkzeuge sind keine durch diesen Schritt umgestellten Exportwege.

Zwischenprüfung: Workbench-Suite nach Umstellung der bisherigen Tests 713 Tests / 4488 Assertions bestanden; gezielte Export-/Legacy-Prüfung 42 Tests / 220 Assertions bestanden. Die abschließende Suite umfasst zusätzlich die neuen Ablehnungs-/Vergleichsfälle, die aktuelle W05-Dokumentation und die Kommando-Fortschrittsanzeige.

Abschließende Verifikation: **721 Tests / 4544 Assertions bestanden** (Workbench plus ProjectTranslationsProgressTest). Native isolierte PostgreSQL-Prüfung: **95 Ergebnisse bestanden**, sämtliche JSON-Zeilen geprüft, keine zusätzlichen Fehlerausgaben. PHP-Syntax und git diff --check erfolgreich. Keine interaktive Browser-Abnahme.

## 16.10.2026 – TranslationList-/Foundation-Bestandsabgleich nach Vereinheitlichung der Exportwege

Der bestehende lesende Vergleich TranslationWorkbenchValueComparison ergänzt review_category und review_counts. Die Kategorien unterscheiden leere Platzhalter, obsolete Legacy-only-Texte, echte Textabweichungen, Statusabweichungen und Mehrfachzeilen. Mehrfachzeilen haben Vorrang, auch wenn nur eine Seite Einträge besitzt. Es erfolgt keine Wertauswahl und keine Umdeutung in eine Freigabe. Der Artisan-Befehl zeigt diese Zusatzaufteilung in einer eigenen Tabelle; JSON enthält weiterhin die unveränderten Originalzeilen.

Der produktive Bestand wurde ausschließlich lesend mit translation-workbench:compare-values --json geprüft. Ergebnis: 6.302 Legacy-only-Paare bestehen aus 6.271 eindeutigen leeren Platzhaltern, zehn obsoleten Texten und 21 mehrdeutigen regionalen Paaren. Zehn tatsächliche Textabweichungen, insgesamt 102 Mehrfachzeilen-Paare und 246 identische Werte mit Statusabweichung sind gesondert dokumentiert. Keine produktive Datenänderung, Migration, Scan oder Veröffentlichung.

Nachweis: [Bestandsbericht](legacy-foundation-value-review-2026-10-16.md) und [ausgewählte Originalzeilen mit IDs](legacy-foundation-value-review-2026-10-16.json). Die Auswahl enthält 382 Paare; Gesamtzähler, Auswahlregel und SHA-256 des vollständigen Rohberichts sind enthalten. Keine Behauptung einer vollständigen Laufzeit-/Fundstellenprüfung: diese folgt für die Konflikte getrennt. Dynamische Optionswerte und Sprachdateien sind nicht Teil dieses Vergleichs.

Gezielte Vergleichstests: 3 Tests / 35 Assertions bestanden. Vollständige Workbench-Suite: **716 Tests / 4524 Assertions bestanden**. PHP-Syntax und git diff --check erfolgreich. Die neuen Tests sichern insbesondere die Abgrenzung zwischen Platzhaltern und Texten, Statusabweichungen sowie einseitige Duplikate; Leerzeichen bleiben unverändert.

## 16.10.2026 – Zehn Textkonflikte: Fundstellen und historische Veröffentlichungen

Mit inspect-value-conflicts.php wurde der aktuelle Bestand ausschließlich lesend geprüft. Für alle zehn Textkonflikte existieren historische Exportereignisse mit dem heutigen Foundation-Wert. Aktuelle Sprachdateien und CLI-Translator liefern in neun Fällen jedoch wieder den Legacy-Wert; ui.type/de liefert bereits Typ. Der spätere Überschreibvorgang ist dadurch nicht identifiziert. Die Sprachbuttons übergeben ausdrücklich language, während der heutige deutsche Dateitext keinen Platzhalter enthält. ui.type besitzt außerdem zwei nicht obsolete Key-Zuordnungen (656/1212); keine davon wurde automatisch gewählt.

[Kontextbericht](value-conflict-context-2026-10-16.md) und [Evidenz](value-conflict-context-2026-10-16.json) enthalten Werte, IDs, relevante Historie, Quellcode-Treffer und Dateiprüfsummen. Neue Revisionsfreigaben oder Publikationsbelege wurden für diese Keys nicht gefunden; historische Key-Reviews sind keine Ersatzfreigabe. Keine Daten-, Sprachdatei- oder Anwendungsänderung. Der Audit liest DB und Dateien zeitlich nacheinander, keinen gemeinsamen atomaren Snapshot. PHP-Syntax und git diff --check bestanden; keine erneute Anwendungstestsuite erforderlich, da nur Leseskript und Auditdokumentation ergänzt wurden.

## 16.10.2026 – Direkte skalare Sprachdatei-Einträge als UI-Strukturfehler

Auf ausdrücklichen Nutzerwunsch werden Keys wie ui.type nicht automatisch umbenannt. TranslationWorkbenchLangNodeClassifier::isRootScalarKey erkennt stattdessen skalare Blatt-Keys direkt unter einer PHP-Sprachdatei (auch package::ui.type). Verschachtelte Blätter wie ui.type.type und ui.button.type bleiben zulässig; die übersetzten Blattwerte selbst bleiben Strings.

Die gemeinsame root-scalar-warning-View zeigt einen roten Flux-Callout in Work findings und im vorhandenen Key-Editor. Im Editor bezieht sich die Meldung auf den bearbeiteten Key. Die Anzeige verweist auf die manuelle Korrektur im bestehenden Workflow. Bekannte Container und dynamische Multi-Option-Container werden nicht als skalare Blätter beanstandet. Der Klassifikationsstatus selbst wird durch die Anzeige nicht geändert.

PublicationPreview blockiert skalare Veröffentlichungen mit dieser Struktur vor der Werte-/Exportplanung. UI und CLI sowie PublicationRunner verwenden dieselbe Vorschauprüfung; Optionscontainer sind weiterhin zulässig, weil deren tatsächliche Blatt-Keys um den Optionspfad erweitert werden. Keine automatische Migration, Umbenennung, Freigabe oder Sprachdateiänderung. Die unabhängige doppelte Zuordnung von ui.type bleibt offen und wurde nicht stillschweigend bereinigt.

Verifikation: vollständige Workbench-Suite **719 Tests / 4539 Assertions bestanden**. Native isolierte PostgreSQL-Prüfung: **95 Ergebnisse bestanden**, alle JSON-Zeilen geprüft, keine zusätzlichen Fehlerausgaben. Bestehende Veröffentlichungstest-Fixtures nutzten teilweise selbst direkte skalare Keys; diese erhalten jetzt ausdrücklich verschachtelte Fixture-Keys. Der neue Negativtest behält ui.type und prüft Ablehnung ohne Daten-/Dateiänderung. Die Flux-Darstellung wird separat ohne den Notification-Mock der Workflowtests gerendert. PHP-Syntax und git diff --check bestanden. Keine interaktive Browser-Abnahme und keine produktive Datenänderung.

## 16.10.2026 – Mehrere aktive Datensätze für denselben Key sichtbar machen

TranslationWorkbenchKeyOwnership definiert den gemeinsamen lesenden Vergleich: exakter translation_key, Status ungleich obsolete. Mehrere Fundstellen desselben Key-Datensatzes sind keine Mehrfachzuordnung; mehrere nicht obsolete Key-Datensätze dagegen schon. Historische obsolete Datensätze bleiben unberücksichtigt.

Work findings lädt die Anzahl über eine korrelierte Unterabfrage in seiner bestehenden Tabellenabfrage, ohne eine zusätzliche Abfrage pro Tabellenzeile. Der bestehende Key-Kandidatencheck liefert die konkreten IDs an den Editor. Ein gemeinsamer roter Flux-Callout zeigt Anzahl und im Editor IDs. Die Meldung ist unabhängig von der direkten skalaren Strukturverletzung. PublicationPreview prüft dieselbe Eigentümerschaft vor Werte-/Exportplanung erneut; UI/CLI/Runner können damit keinen der konkurrierenden Datensätze automatisch bevorzugen. Keine Speicherung oder Bereinigung wird durch die Prüfung ausgelöst.

Die Tests erzeugen zwei aktive Besitzer eines gültigen verschachtelten Keys, prüfen die IDs und Veröffentlichungssperre ohne Seiteneffekte und zeigen anschließend, dass ein obsoleter historischer Besitzer die Vorschau nicht blockiert. Separater Flux-Renderingtest prüft Meldung und IDs sowie das Ausblenden bei einem einzelnen Besitzer. Der produktive ui.type-Bestand wurde nicht verändert.

Verifikation: **721 Tests / 4551 Assertions bestanden** in der vollständigen Workbench-Suite. Isoliertes PostgreSQL: **95 Ergebnisse bestanden**, sämtliche JSON-Zeilen kontrolliert, keine zusätzlichen Fehlerausgaben. PHP-Syntax und git diff --check bestanden. Keine produktiven Datenänderungen und keine interaktive Browser-Abnahme.

## 16.10.2026 – Fehlerfilter und Toggle-Icons

Commented out verwendet jetzt wie Only Errors den vorhandenen Flux-Icon-Slot mit expliziten outline-Icons. Seine bisherige Funktion bleibt bestehen. Die vom Nutzer angepassten Only-Errors-Icons (bug/list-checks, size-6) bleiben erhalten; der nicht angebundene Alpine-Ausdruck wurde durch wire:model.live=findingOnlyErrors ersetzt.

Only Errors begrenzt Work findings auf die beiden eingeführten Fehlerarten: direkte skalare Blatt-Keys und mehrere nicht obsolete Key-Datensätze mit identischem Key. Container/dynamische Multi-Option-Container sind keine direkten skalaren Blätter. Andere Filter werden zusätzlich angewendet; All schaltet nur den Fehlerfilter ab. Die SQL-Bedingung liegt vor der Pagination, umfasst auch Package-Keys und nutzt für Mehrfachzuordnungen den gemeinsamen Ownership-Scope. Der Zustand wird wie andere Filter gespeichert/wiederhergestellt, setzt bei Änderung die Pagination zurück und wird durch Reset filters ausgeschaltet.

Beide tatsächlichen Toggle-Blöcke wurden aus der Blade-Datei extrahiert und erfolgreich mit Flux gerendert. Ein isolierter Datenbanktest deckt fehlerhafte/korrekte/namespaced Keys, Container, aktive Duplikate, obsolete Historie, zusätzliche Filterbedingungen und den Pagination-Gesamtzähler ab. Keine produktiven Übersetzungsdaten geändert.

Verifikation: vollständige Workbench-Suite **722 Tests / 4554 Assertions bestanden**. Beide Toggle-Blöcke rendern erfolgreich; PHP-Syntax und git diff --check bestanden. Keine interaktive Browser-Abnahme.

## 16.10.2026 – Fehlerfilter: wiederholte Key-Zählungen beseitigen

Die vorherige korrelierte Duplikatprüfung führte in der lesenden PostgreSQL-Messung 18.435 vollständige Scans der Key-Tabelle aus. Die Zählabfrage benötigte 20.877,072 ms. TranslationWorkbenchKeyOwnership::duplicateKeys bildet die mehrfach belegten Keys nun mit GROUP BY/HAVING einmalig; der Fehlerfilter verwendet diese als unkorrelierte IN-Unterabfrage. Das Ergebnis enthält weiterhin exakt dieselben 1.121 geordneten Finding-IDs. Die neue Zählabfrage benötigte 22,902 ms. Zusätzlich erhält die sichtbare Tabelle owner_count aus einer gruppierten LEFT JOIN-Unterabfrage; die bisherige korrelierte Einzelzählung pro Zeile entfällt.

[Messpläne](error-filter-performance-2026-10-16.json): EXPLAIN ANALYZE in einer schreibgeschützten Transaktion, mit Default-Statusauswahl und Fehlerfilter. Die Messung umfasst die Zählabfrage, nicht sämtliche UI-Selects oder Browser-Rendering. Keine Änderung an Fehlerkriterien, Statusregeln, Daten oder Caching; keine Datenbankmigration nötig.

Verifikation: vollständige Workbench-Suite **722 Tests / 4554 Assertions bestanden**; PHP-Syntax und git diff --check bestanden. Die aktuelle Messung ist mit measure-error-filter.php wiederholbar. Die PostgreSQL-Vorher-/Nachher-Messung bestätigt identische Finding-IDs. Eine interaktive Browser-Zeitmessung wurde nicht durchgeführt.

## 16.10.2026 – Gespeicherte und veröffentlichte Werte direkt im Editor vergleichen

Der skalare Übersetzungseditor bietet jetzt Compare published values. Die Aktion autorisiert den Benutzer, prüft den geöffneten Editor und die aktive Key-/Finding-Zuordnung und verwendet ausschließlich den bestehenden LangFileExporter im Lesemodus für den ausgewählten Key und die Editor-Sprachen. Sie benötigt keine Wertfreigabe und ersetzt nicht die getrennte, strengere Veröffentlichungsvorschau. Daher bleiben Abweichungen auch vor einer Strukturkorrektur analysierbar.

Die neue gemeinsame View stored-publication-comparison zeigt geänderte/neue Werte zweispaltig (Published value / Stored value), Dateipfad, unveränderte Anzahl, Strukturkonflikte und mögliche Löschungen ausdrücklich ohne Ausführung. Vergleichsdaten sind Locked-Livewire-State und werden beim Öffnen eines anderen Editors bzw. Schließen verworfen. Der Vergleich ist ein ausdrücklich zu aktualisierender Snapshot; ungespeicherte Eingaben und Runtime-Fallbacks sind ausgeschlossen. Keine Freigabe, Veröffentlichung, automatisierte Auswahl oder Übernahme von Legacy-Werten erfolgt.

Der Workflowtest überprüft eine echte Dateiabweichung, unveränderte ungespeicherte Eingaben sowie unveränderte Dateien, Reviews und Timeline. Ein separater Flux-Renderingtest prüft zweispaltige Vergleichsinhalte und Escaping. Keine produktiven Datenänderungen.

Verifikation: vollständige Workbench-Suite **724 Tests / 4567 Assertions bestanden**. PHP-Syntax und git diff --check bestanden. Keine interaktive Browser-Abnahme. Die zehn produktiven Textabweichungen wurden weiterhin nicht automatisch korrigiert oder veröffentlicht.

## 16.10.2026 – Manuelle Key-Umbenennung: keine stillschweigende Wertauswahl

Bei der Prüfung des UI-Korrekturwegs wurde festgestellt: rekeyLangValuesForTranslationKey verschiebt Werte anhand des Key-Texts. Mehrere aktive Key-Datensätze könnten dadurch gegenseitig ihre Werte verlieren. Ein vorhandener Zielwert führte bisher automatisch zur Obsolete-Markierung des Quellwerts, auch ohne fachliche Konfliktentscheidung.

TranslationWorkbenchKeyReviewWriter blockiert eine tatsächliche manuelle Key-Änderung jetzt bei mehreren aktiven Besitzern des Quellkeys. Die Prüfung und das Verschieben liegen in derselben Transaktion; PostgreSQL sperrt Keys und Lang-Values gegen parallele Schreibvorgänge. Ein zwischenzeitlich geänderter Quellkey wird erkannt. Bereits belegte Zielwerte (auch obsolete) führen zu einem ValidationException-Fehler im vorhandenen translationKeyValue-Feld. Die gesamte Transaktion einschließlich Key, vorherigen Sprachverschiebungen, Review und Timeline wird zurückgerollt. Keine automatische Auswahl des Zieltextes und keine automatische Obsolete-Markierung.

Der bestehende erfolgreiche Umbenennungsweg mit freien Zielen bleibt erhalten und protokolliert Key-Änderung und Wertebewegung. Vorschlagsannahme, Batch-/Scalar-Transform-Aktionen und explizite Konfliktauflösung sind eigene Wege und wurden hier nicht umgedeutet. Die doppelte produktive ui.type-Zuordnung bleibt unverändert; für deren Auflösung ist eine ausdrückliche Zusammenführungs-/Trennentscheidung nötig.

Gezielte Tests: 8 Tests / 49 Assertions bestanden. Der bisherige Test für Wiederverwendung eines obsoleten Zielblatts wurde auf die nun notwendige Konfliktentscheidung umgestellt; Quell-/Zielwerte und Historie bleiben dabei unverändert. Isoliertes PostgreSQL: 95 Ergebnisse bestanden, sämtliche JSON-Zeilen geprüft, keine zusätzlichen Fehlerausgaben. Keine produktiven Datenänderungen oder Sprachdateiänderungen.

Abschließende vollständige Workbench-Suite: **725 Tests / 4573 Assertions bestanden**. PHP-Syntax und git diff --check bestanden. Keine interaktive Browser-Abnahme.

## 16.10.2026 – Management-Ablösung: Funktionsabgleich

Die Bestandsaufnahme [management-retirement-plan-2026-10-16.md](management-retirement-plan-2026-10-16.md) ordnet Legacy-Funktionen der zentralen Workbench mit bestehenden Modals zu. Neben Management schreiben auch Sub-Languages (regionale Werte und Basis-Duplikatentscheidung), Usage (Legacy-Key-Erstellung und Fundstellenentscheidungen) und Lang Ballast (Prüfentscheidungen). Diese Seiten sind deshalb nicht pauschal als lesende Auswertungen zu behandeln.

Noch abzugleichen sind insbesondere explizite Key-Konfliktauflösung, regionale Basis-/Override-Entscheidungen, Legacy-Reviewstatus und historische Zugänglichkeit. Settings verwaltet derzeit vor allem UI-Zustände und Konfigurationsanzeige. Keine Routen, Formulare, produktiven Daten oder Schreibwege geändert. Dokumentationsänderung ohne neue Testausführung; der zuletzt dokumentierte Teststand ist kein neuer Testlauf dieses Abgleichs.

## 16.10.2026 – Key-Editor: explizite Bestandsansicht vor Konfliktentscheidungen

Der vorhandene Key-Editor enthält nun Inspect key assignments and values. TranslationWorkbenchKeyConflictInspector liest für Quell- und vorgeschlagenen Zielkey aktive Key-Datensätze, deren Fundstellenverknüpfungen einschließlich inaktiver Links sowie gespeicherte skalare Lang-Werte einschließlich obsoleter Ziele. Gemeinsame Sprachwerte werden einmal pro Key-Text dargestellt und keinem einzelnen Besitzer zugeschrieben. Dynamische Optionswerte und veröffentlichte Dateien sind ausdrücklich nicht Teil dieser Ansicht.

Die Aktion autorisiert den Editorzugriff. Der Snapshot ist Locked, wird beim Öffnen/Schließen verworfen und nennt die untersuchten Quell-/Zielkeys; nach Änderung des Zielkeys muss ausdrücklich erneut geprüft werden. Die Flux-Ansicht maskiert Werte und Pfade und begrenzt die Höhe der Tabellen. Keine neue Schreibaktion, Zusammenführung oder Trennung freigeschaltet; diese Konfliktentscheidung ist weiterhin offen. Die bestehenden Schreibsperren bleiben erhalten.

Gezielte Tests prüfen gemeinsame Besitzer, Fundstellen, obsolete Zielwerte, unveränderte Daten/Historie, geschlossenen Editor, fehlende Berechtigung, Locked-Attribut und HTML-Escaping. Keine produktiven Datenänderungen.

## 16.10.2026 – Foundation-Sync schützt historische Zusammenführungen

Ausgehend von der belegten Wiederanlage/Reaktivierung bei Finding #891 prüft syncKey nun den Fingerprint-Kandidaten vor jeder Wiederverwendung auf gespeicherte Konsolidierungsbelege. Er berücksichtigt sowohl merged-Verknüpfungen des Kandidaten als auch duplicate_key_consolidation-Metadaten mit dessen duplicate_key_id, einschließlich bereits zum kanonischen Key umgehängter Links. Ein Treffer stoppt den Sync mit Key-/Link-/Finding-ID und rollt die gesamte Sync-Transaktion zurück. Historische Metadaten werden nicht automatisch als Umleitungsauftrag ausgeführt. Das ist ein Schutz vor erneuter Veränderung, keine automatische Bereinigung und noch keine vollständige UI-Auflösung.

Die Auswahl eines geprüften Owners verwendet nicht mehr einfach first() bei mehreren offenen geprüften Besitzern desselben Key-Texts: dieser Fall wird ebenfalls ausdrücklich abgewiesen. Bei wiederverwendeten kanonischen Links bleiben Konsolidierungsmetadaten erhalten. Gewöhnlich obsolete Fundstellenverknüpfungen ohne solche Entscheidungsmerkmale dürfen bei Wiederfund weiterhin aktiviert werden. Auch eine einzelne gemergte Relation ist vor direkter Reaktivierung geschützt.

Die Prüfung blockiert historische Konsolidierungen bewusst bis zur expliziten Klärung, auch wenn die Entscheidung nur einen Teil der Fundstellen betraf. Sie leitet keine globale Zuordnung aus einer solchen Teilentscheidung ab. Mehrere ungeprüfte Kandidaten sind dadurch nicht pauschal zusammengeführt oder vollständig aufgelöst. Der Schutz ist auch kein neu eingeführter Sperrmechanismus für konkurrierende Scanprozesse.

Gezielte Wiederholungstests: 5 Tests / 45 Assertions bestanden, einschließlich unveränderter Keys, Findings, Verknüpfungen und Timeline nach abgewiesenen Läufen; gewöhnlicher Wiederfund und Erhalt kanonischer Metadaten ebenfalls geprüft. Keine produktiven Scans, Datenänderungen oder Migrationen ausgeführt.

Abschließende Verifikation: vollständige Workbench-Suite 735 Tests / 4651 Assertions bestanden. Bestehender isolierter PostgreSQL-Integrationslauf: alle 95 JSON-Ergebnisse bestanden; keine zusätzlichen nicht-JSON-Fehlerausgaben. Die neuen Konsolidierungsfälle wurden in der SQLite-Workflow-Suite geprüft. PHP-Syntax und git diff --check bestanden.

## 16.10.2026 – Gespeicherter Reparaturplan und explizites Apply

Siehe [source-repair-plan-2026-10-16.md](source-repair-plan-2026-10-16.md): reconcile-sources unterstützt --save-plan und --apply=ID für eindeutig rekonstruierbare Positionskorrekturen und belegte historische Zusammenführungen. Frischeprüfung, signierte private Pläne, Transaktions-Rollback und Timeline-Protokollierung sind implementiert. Normale Folgescans beachten die explizit bestätigte kanonische Zuordnung. Produktiver Plan: 508 Positionskorrekturen, 190 Zusammenführungen, noch nicht ausgeführt. 744 Tests / 4696 Assertions und 95 bestehende PostgreSQL-Ergebnisse bestanden.

## 16.10.2026 – Produktive Ausführung des gespeicherten Reparaturplans

Plan 8814b817b6216bab8b46d260a72e2631 nach Fortsetzungsfreigabe angewendet: 508 Positionskorrekturen, 190 Zusammenführungen / 227 abgelöste aktive Links, 925 Timeline-Einträge. Lesende Einzelprüfung ohne Abweichung. Identischer zweiter Aufruf: alle 698 Aktionen skipped; Datenprüfung und Historienanzahlen anschließend identisch. Vollständiger lesender Abgleich zeigt keine moved-Fälle mehr und 435 statt 662 Mehrfachzuordnungszeilen. Finding 891 / Key 1858 bleibt ungeklärt. Details in source-repair-plan-2026-10-16.md und source-repair-applied-2026-10-16.json. Keine Quell-/Sprachdateien geändert.

## 16.10.2026 – Timeline-belegte Wiederaktivierungen bereinigt

92 weitere Keys / 100 Links anhand früherer Konsolidierungsentscheidungen repariert. Finding #891 hat jetzt nur #1236 aktiv, #1858 ist obsolete mit kanonischem Verweis. 92 historische Belege unverändert, Wiederholung vollständig skipped. Mehrfachzuordnungszeilen 435 → 335. Keine pauschale Altersregel. 749 Tests / 4714 Assertions und 95 PostgreSQL-Ergebnisse bestanden. Details: [timeline-consolidation-2026-10-16.md](timeline-consolidation-2026-10-16.md).

## 17.10.2026 – Aktuelle Vorkommen und Archivsignaturen

Gemeinsamer OccurrenceMatcher für Abgleich und Sync; mehrdeutige Gruppen brechen transaktional ab. Partielle Eindeutigkeit nicht archivierter Signaturen migriert, sämtliche 23216 Findings und 25816 Verknüpfungen per Prüfsumme unverändert. Kein produktiver Sync. 757 vollständige Tests plus ein Migrationstest, 96 PostgreSQL-Ergebnisse bestanden. Details: [finding-signature-migration-2026-10-17.md](finding-signature-migration-2026-10-17.md).
