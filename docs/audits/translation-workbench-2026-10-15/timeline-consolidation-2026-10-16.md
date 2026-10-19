# Historisch belegte Wiederaktivierungen bereinigt

Plan `b0694a770afb3fb54cbf17d3ddd13245` wurde nach Fortsetzungsauftrag angewendet: **92 Keys**, **100 aktive Dubletten-Verknüpfungen**. Finding **891** besitzt danach ausschließlich den aktiven Key **1236**. Key **1858** ist obsolete mit dokumentiertem kanonischem Ziel. Keine Löschung von Findings, Keys oder Historie; keine Änderungen an Quellcode-Literalen oder Sprachdateien.

## Entscheidung und Schutz

`TranslationWorkbenchConsolidationEvidence` rekonstruiert frühere Zusammenführungen aus Timeline-Ereignissen, deren alte Werte eine konkrete `duplicate_key_consolidation` enthalten und deren neue Werte eine automatische Wiederaktivierung durch Foundation-Sync belegen. Das Alter oder die ID allein bestimmt keinen Gewinner. Die bestehende Reparaturprüfung fordert für jede aktive Quellverknüpfung passende Belege und einen eindeutig aktuellen Quellenbezug; eigene Übersetzungswerte, Reviews, widersprüchliche Ziele und Konsolidierungsketten blockieren die Reparatur.

Spätere manuelle oder unbekannte Entscheidungen verhindern die neue Ableitung. Eine automatische Klassifikation der Lang-Knotenform ohne Änderung des Key-Texts darf bestehen bleiben; der Test prüft diesen Fall ausdrücklich. Timeline-Belege werden mit ihrem Hash in den signierten Plan aufgenommen und beim Anwenden erneut geprüft. Die Transaktion sperrt auch Reviews und Timeline gegen konkurrierende Änderungen während dieser Prüfung. Der bestehende Foundation-Sync beachtet anschließend die bestätigte kanonische Zuordnung.

## Verifikation

- 92 Aktionen angewendet; alle 100 abgelösten Links merged, Zielverknüpfungen aktiv.
- 92 historische Belegereignisse unverändert; keine Abweichung bei der Einzelprüfung.
- Wiederholung: alle 92 Aktionen übersprungen; identische Prüfung und Timeline-Anzahl (174435).
- Lesender Gesamtabgleich: Mehrfachzuordnungszeilen von 435 auf **335** reduziert; 99 Zeilen ohne aktive Zuordnung unverändert. Diese verbleibenden Fälle wurden nicht pauschal korrigiert und sind bei Bedarf gesondert zu prüfen.
- Workbench-Suite: **749 Tests / 4714 Assertions bestanden**.
- Isolierter PostgreSQL-Integrationslauf: **95 von 95 Ergebnissen bestanden**. Neue Timeline-Fälle sind durch fünf SQLite-Workflow-Testvarianten abgedeckt.
- `git diff --check` bestanden. Keine neue interaktive Browserprüfung.

Die Zuordnungen und Prüfergebnisse stehen in [timeline-consolidation-applied-2026-10-16.json](timeline-consolidation-applied-2026-10-16.json). Der vollständige signierte Plan liegt privat unter `storage/app/private/translation-workbench/source-repairs/`.
