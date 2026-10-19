# Bereinigung der Stored-values-Doppelzuordnungen

Am 18. Oktober 2026 wurden ausschließlich die durch die Reviews #913/#914 belegten Scanner-Doppelzuordnungen bereinigt.

| Finding | Beibehalten | Archivierter Key | Historische Doppelverknüpfung |
| --- | --- | --- | --- |
| #6716 | Key #321 | #4826 → obsolete | #8970 → merged |
| #6731 | Key #321 | #4827 → obsolete | #8971 → merged |

Beide archivierten Keys enthalten einen Verweis `reconciled_canonical_key_id = 321`. Jeweils zwei Timeline-Ereignisse halten die Änderung der Verknüpfung und des Keys einschließlich des zugrunde liegenden Reviews fest. Es wurden keine Datensätze gelöscht.

Vor der Anwendung wurden sämtliche Verknüpfungen der beiden Keys, fehlende eigene Werte/Reviews, die explizite Freigabe der jeweiligen Fundstelle und der aktuelle Quellcode geprüft. Der lesende Plan wurde innerhalb der Schreibtransaktion unter Tabellensperren erneut aufgebaut und vollständig verglichen. Unveränderte Tabelleninhalte außerhalb der beiden Keys und Verknüpfungen wurden per Hash kontrolliert; Quellcode-Hashes wurden ebenfalls nachgeprüft.

Alle Findings, Prüfentscheidungen, Übersetzungswerte und die übrigen Keys/Verknüpfungen blieben unverändert. Keine Quellcode- oder Sprachdateien wurden geschrieben.

## Nachprüfung

- Beide Reparaturpläne melden anschließend `already_applied`.
- Verbleibend: 30 Findings mit Mehrfachzuordnungen (vorher 32).
- Davon 28 mit mehreren geprüften Keys, eines mit geprüftem/ungeprüftem Key und eines mit unterschiedlichen TranslationKeys.
- Vier gezielte Regressionstests bestanden, 25 Assertions: getrennte gleichlautende Literale, keine Wiederbelebung archivierter Kandidaten, keine willkürliche Auswahl unter mehreren geprüften Keys sowie Konflikterkennung bei mehreren aktiven Eigentümern.

Dies ist eine gezielte historische Datenreparatur anhand konkreter Freigabeentscheidungen. Das Skript liegt ausschließlich im Audit-Verzeichnis und wird nicht vom regulären Scanner eingebunden. Es führt keine allgemeine Zusammenführung gleicher Literaltexte ein.

Nachweise: `stored-values-owner-reconciliation-2026-10-18.json`; Herkunftsprüfung: `reviewed-pending-history-2026-10-18.md` und die zugehörige JSON-Datei. Die Fälle „Reset“ und „Save“ bleiben offen und unverändert.
