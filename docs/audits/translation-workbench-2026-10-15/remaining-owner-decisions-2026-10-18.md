# Verbleibende Zuordnungsentscheidungen – 18. Oktober 2026

Die 29 verbliebenen Findings wurden ausschließlich lesend mit ihren Reviews und aktiven Verknüpfungen abgeglichen. Keine Datenänderungen.

## Begriffe

**Direkter Review**: `review.finding_id` entspricht der untersuchten Fundstelle. **Bulk-Bezug**: Die Fundstelle steht im `selected_finding_ids`-Array eines Reviews dieses Keys. Ein Bulk-Bezug bedeutet Beteiligung an einer gemeinsamen Aktion; er ist kein Beleg dafür, dass der andere Key archiviert werden sollte. Auch ein direkter Review ist nicht automatisch eine exklusive Eigentumsentscheidung.

| Finding | Key | Direkte Review-IDs | Reviews mit Bulk-Bezug (Anzahl) |
| --- | --- | --- | ---: |
| #2965 | #5 | #349 | 31 |
| #2965 | #243 | — | 22 |
| #2966 | #5 | #350 | 31 |
| #2966 | #243 | — | 22 |
| #4617 | #3145 | — | 0 |
| #4617 | #5240 | — | 0 |
| #4763 | #699 | — | 6 |
| #4763 | #3560 | #1014, #1015, #1332 | 1 |
| #4949 | #335 | — | 0 |
| #4949 | #4582 | — | 0 |
| #4951 | #421 | — | 1 |
| #4951 | #4597 | #728 | 1 |
| #5293 | #809 | — | 1 |
| #5293 | #993 | #657 | 9 |
| #5294 | #809 | — | 1 |
| #5294 | #993 | #658 | 9 |
| #5296 | #809 | — | 1 |
| #5296 | #993 | #659 | 9 |
| #5302 | #809 | — | 1 |
| #5302 | #993 | #664 | 9 |
| #5701 | #809 | — | 1 |
| #5701 | #993 | #665 | 9 |
| #6007 | #656 | — | 0 |
| #6007 | #1212 | — | 0 |
| #6009 | #656 | — | 0 |
| #6009 | #1212 | — | 0 |
| #6022 | #656 | — | 0 |
| #6022 | #1212 | — | 0 |
| #6032 | #656 | — | 0 |
| #6032 | #1212 | — | 0 |
| #6033 | #656 | — | 0 |
| #6033 | #1212 | — | 0 |
| #6061 | #656 | — | 0 |
| #6061 | #1212 | — | 0 |
| #6062 | #656 | — | 0 |
| #6062 | #1212 | — | 0 |
| #6064 | #656 | — | 0 |
| #6064 | #1212 | — | 0 |
| #6066 | #656 | — | 0 |
| #6066 | #1212 | — | 0 |
| #6072 | #656 | — | 0 |
| #6072 | #1212 | — | 0 |
| #6077 | #656 | — | 0 |
| #6077 | #1212 | — | 0 |
| #6097 | #656 | — | 0 |
| #6097 | #1212 | — | 0 |
| #6100 | #656 | — | 0 |
| #6100 | #1212 | — | 0 |
| #6103 | #656 | — | 0 |
| #6103 | #1212 | — | 0 |
| #6115 | #656 | — | 0 |
| #6115 | #1212 | — | 0 |
| #6116 | #656 | — | 0 |
| #6116 | #1212 | — | 0 |
| #6124 | #656 | — | 0 |
| #6124 | #1212 | — | 0 |
| #6126 | #656 | — | 0 |
| #6126 | #1212 | — | 0 |

## Reset #4617

Key #3145 wurde bereits für die ursprüngliche Reset-Fundstelle erfasst. Die Freigaben von #5240 betreffen #7936/#7937/#7938. Der Scanner ergänzte die Verbindung #10950 von #5240 zu #4617 erst später (Event #124857). Für #4617 selbst ist keine direkte Freigabe oder Auswahl in diesen Bulk-Reviews dokumentiert. Deshalb darf der ungeprüfte ursprüngliche Key nicht allein wegen des grünen Prüfstatus des zusätzlichen Keys stillgelegt werden. Ebenso wäre eine Entfernung des neueren Links ohne Prüfung der aktuellen Scanner-Zuordnungsregel nur eine möglicherweise vorübergehende Datenkorrektur.

## Grenzen der Automatik

Bei neun Findings gibt es direkte Reviews an einem der aktiven Keys. Die weiteren Bulk-Bezüge müssen jedoch berücksichtigt werden: mehrere Keys wurden gemeinsam bearbeitet. Die Tabelle ist daher eine Prüfgrundlage, kein automatischer Reparaturplan. Bei den übrigen zwanzig Findings fehlt ein direkter Review an den aktuell verknüpften Keys.

Unterschiedliche SuggestedKeys und unabhängige Aufgaben zu demselben Literal bleiben zulässig. Die bisherige Forderung, `ui.type` in der UI statt automatisch zu korrigieren, bleibt bestehen.

Als nächster Implementierungsschritt ist eine explizite Entscheidung über die betroffenen Fundstellen und aktiven Keys in der Workbench nötig, einschließlich einer Prüfung, dass der Scanner diese Entscheidung bei einem erneuten Scan beibehält. Keine pauschale Regel „geprüft gewinnt“, „ältester gewinnt“ oder „gleicher Text wird zusammengeführt“.

Nachweise: `remaining-owner-decisions-2026-10-18.json`; reproduzierbare lesende Abfrage: `audit-remaining-owner-decisions-2026-10-18.php`.
