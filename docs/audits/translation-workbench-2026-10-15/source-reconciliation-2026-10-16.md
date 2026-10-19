# Quellcode/Fundstellen-Abgleich

## Aufruf

```bash
php artisan translation-workbench:reconcile-sources
php artisan translation-workbench:reconcile-sources --paths=resources/views/components/admin/partials/permission-list --json
```

Das Command ist derzeit ausschließlich lesend; eine --apply-Option ist noch nicht implementiert. Es verwendet scanWithManifest des vorhandenen Scanners und dessen konfigurierten Scan-Umfang. Kein Foundation-Sync, keine Änderung von Raw-Daten, Findings, Keys, Werten, Reviews, Historie, Quellcode oder Sprachdateien. Der Bericht ist eine Momentaufnahme, keine Schreibfreigabe.

## Interpretation

- unchanged: eindeutige gespeicherte Scanner-Signatur wiedergefunden und Finding aktiv. Key-Zuordnungsfehler werden unabhängig davon ausgewiesen.
- moved: innerhalb derselben Datei genau ein passender aktiver Altbefund und genau ein aktuelles Vorkommen mit passender Ausdrucksidentität, Art und SuggestedKey; alte/neue Zeile und Finding-ID werden ausgegeben.
- new: keine eindeutige bestehende Zuordnung erkannt; noch keine neue Datenbankzeile geschrieben.
- missing: aktiver Altbefund innerhalb des erfolgreich geprüften Manifests nicht gefunden, nicht als mehrdeutiger Kandidat reserviert; noch nicht deaktiviert.
- ambiguous: Identität nicht eindeutig. Keine automatische Zusammenführung oder Deaktivierung ableiten.
- reappeared: vorhandene eindeutige Signatur eines nicht aktiven Findings wiedergefunden.
- ignored: Kommentar-/Leertextbefund, nicht als regulärer aktiver Übersetzungsauftrag behandeln.

Der Bericht verwendet die vorhandene Scanner-Granularität (Zeile/Signatur), erfindet keine Spaltenpositionen. Dateiübergreifende Verschiebungen werden nicht als identischer Finding erkannt; diese können daher als new/missing erscheinen und sind vor einer späteren Anwendung zu klären. Unveränderte Signatur bedeutet keinen vollständigen Vergleich sämtlicher Metadaten. Unterschiedliche geprüfte Workbench-Keys und Code-Keys werden ausdrücklich als Information ausgegeben und nicht überschrieben.

## Stand des vollständigen lesenden Laufs

1668 Dateien geprüft. 18142 unchanged, 508 moved, 974 new, 47 missing, 157 ambiguous, 3 reappeared, 129 ignored. Die Zahlen zählen Berichtszeilen, nicht automatisch reparierbare Datensätze. Darunter 662 Zeilen mit mehreren aktiven Zuordnungen, 99 ohne aktive Zuordnung (auch ignorierte/inaktive Kontexte sind enthalten). Kein geprüfter Key-/Code-Unterschied in diesem Snapshot.

Für Findings 891, 3618, 3630, 3640 und 3652 stimmen Code und gespeicherte Position überein. Vier davon haben weiterhin doppelte aktive Zuordnungen. Das bestätigt ein Zuordnungsproblem, keine Positionskorrektur für diese fünf Findings.

Die erste breite Ausführung mit wiederholtem linearem Datenbank-Snapshot-Abgleich wurde ohne Änderungen abgebrochen. Gruppierte Signatur-/Ausdrucksindizes im Speicher beseitigen diese Wiederholungen; der erneute vollständige Lauf war erfolgreich. Zusammenfassung und die fünf ausgewählten Findings stehen in source-reconciliation-2026-10-16.json. Der vollständige lokale Laufbericht liegt unter /tmp/workbench-source-reconciliation.json (temporär).

## Verifikation und nächster Schritt

738 Workbench-Tests / 4664 Assertions bestanden; nach Indexoptimierung die drei neuen gezielten Tests / 13 Assertions erneut bestanden. Tests decken eindeutige Verschiebung, Mehrdeutigkeit, Scan-Grenze, unveränderte Daten/Historie, geprüfte Keys und Command-Ausgabe bei Mehrfachzuordnung ab. Syntax/Whitespace geprüft.

Noch offen: aus dem Bericht einen verbindlichen, bei Anwendung erneut validierten Änderungsplan mit expliziten Zuordnungsentscheidungen bilden und --apply transaktional implementieren. Insbesondere new/missing und historische Zusammenführungen dürfen nicht ungeprüft als Reparatur ausgeführt werden. Dieser Stand ersetzt keine vollständige Reparaturimplementierung.
