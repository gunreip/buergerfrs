# Finding #891: Mehrfachzuordnungen nach Zusammenführung

Stand 16.10.2026. Produktive Daten ausschließlich in einer PostgreSQL-Transaktion mit REPEATABLE READ READ ONLY gelesen. Kein Scan/Sync und keine Bereinigung ausgeführt.

## Belegte Ereignisse

Key-Text: `admin.permissions.modals.roles.changes`.

- Fünf offene Key-Datensätze: 1236, 1249, 1263, 1283, 1858. Sie besitzen unterschiedliche kontextbezogene SuggestedKeys.
- Fünf verschiedene Findings, neun aktive Verknüpfungen. Finding 891 gehört aktiv zu 1236 und 1858; 3630 zu 1236 und 1249; 3640 zu 1236 und 1263; 3652 zu 1236 und 1283. Nur 3618 gehört allein zu 1236.
- Link 5234 wurde ursprünglich für Key 1249 erstellt (Timeline 87416). Heute gehört dieselbe Link-ID zu 1236, mit `meta.source=duplicate_key_consolidation`, `canonical_key_id=1236`, `duplicate_key_id=1249`. Entsprechend Links 5244/5256 für 1263/1283. Änderungszeit der Links: 26.07.2026 12:07:01, unveränderte Datenbankzeitangabe.
- Am 26.07.2026 12:45:16 erstellte sync-foundation Links 6712, 6718 und 6725 erneut zu 1249, 1263 und 1283 (Timeline 92020, 92032, 92046).
- Timeline 92274 belegt am 26.07.2026 12:45:18 die Reaktivierung von Link 1859, Key 1858 / Finding 891: `obsolete` mit Konsolidierungsmetadaten wurde durch `active` und `source=foundation_sync` ersetzt.
- Die älteren Links 1250, 1264 und 1284 zu Finding 891 stehen heute auf `merged`; Änderungszeit 12:56:27. Diese spätere Änderung beseitigte die neuen parallelen Links nicht.

Belege: `key-891-assignment-history-2026-10-16.json`. Der konkrete damalige Konsolidierungsaufruf/ausführende Benutzer ist aus diesen Relationsevents nicht belegt. Metadaten und ursprüngliche Event-Snapshots belegen die Umhängung; sie dürfen nicht als unveränderte heutige Zuordnung interpretiert werden.

## Heutiger Code

`src/Foundation/TranslationWorkbenchFoundationSyncer.php`:

1. `syncKey()` bildet die Kandidatenidentität aus Translation-Key **und** SuggestedKey. Derselbe Translation-Key kann deshalb mehrere kontextbezogene Kandidaten haben.
2. `reviewedKeyForTranslationKey()` bevorzugt einen anderen offenen, geprüften Key mit demselben Text. Bei mehreren passenden geprüften Keys wählt es per Sortierung den ersten; das ist keine explizite Besitzentscheidung.
3. Ohne diesen Treffer wird der Fingerprint verwendet. Ein obsoleter Key kann auf `open` gesetzt werden.
4. `syncKeyFinding()` erstellt fehlende Links oder setzt vorhandene Links pauschal auf `active` mit neuen Foundation-Metadaten. Eine frühere Zusammenführungsentscheidung wird hier nicht gesondert ausgewertet.
5. `obsoleteObsoleteSiblingKeyRelations()` räumt nur aktive Nebenverknüpfungen auf, deren Key selbst `obsolete` ist. Alle fünf untersuchten Keys sind `open`; diese Bereinigung greift hier nicht.

Die heutigen Guards vermeiden somit nicht allgemein das belegte Problem. Ob ein bestimmter kommender Lauf exakt dieselben Änderungen auslösen würde, hängt auch von Reviewstatus und Scan-Kontext ab; es wurde kein produktiver Probelauf ausgeführt.

## Konsequenz

Keine Anzeige-Deduplizierung als Reparatur. Keine automatische Wahl von Key 1236 allein aufgrund der kleinsten ID. Die historischen Konsolidierungsangaben sind Belege für eine zu prüfende Entscheidung, kein pauschaler Auftrag zur erneuten Datenmigration.

Vor einer produktiven Bereinigung benötigt der Sync eine ausdrückliche, dauerhafte Berücksichtigung von Zuordnungsentscheidungen: eine bestätigte Zusammenführung muss erhalten bleiben, eine bewusste Trennung ebenso. Mehrdeutige Eigentümer dürfen nicht durch `first()` stillschweigend entschieden werden. Fundstellenhistorie und Sprachwerte bleiben erhalten.

Erforderliche Regressionen für die anschließende Korrektur: erneuter Scan nach Zusammenführung; erneuter Scan nach Trennung; nicht reaktivierte abgelöste Zuordnung; mehrere ungeklärte Besitzer; wieder aufgetauchte echte Fundstelle; wiederholter unveränderter Scan ohne zusätzliche Links. Die Auswertung dieses Schritts ist dokumentarisch, keine Verhaltensänderung und kein neuer Testlauf.
