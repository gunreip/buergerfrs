# Gespeicherter Quellcode-Reparaturplan

## Bedienung

```bash
# Weiterhin ausschließlich lesen:
php artisan translation-workbench:reconcile-sources
# Plan erzeugen und ID ausgeben; nur private Plandatei schreiben:
php artisan translation-workbench:reconcile-sources --save-plan
# Später einen konkret geprüften Plan ausführen:
php artisan translation-workbench:reconcile-sources --apply=PLAN_ID
```

--paths begrenzt Prüfung/Planerstellung. --apply erlaubt keine neue Pfadauswahl. Pläne liegen unter storage/app/private/translation-workbench/source-repairs mit zufälliger ID, Dateimodus 0600 und HMAC über den gespeicherten Payload. Eine ungültige Signatur wird abgelehnt. CLI verwendet wie andere eigenständige Workbench-Commands den OS-Zugriff; keine neue UI-Aktion ist angebunden.

## Ausführbare Aktionen

**move:** eindeutig identifizierte aktive Fundstelle innerhalb derselben Datei. Nur source_line, source_signature und source_fingerprint werden angepasst. Finding-ID, erster Erfassungszeitpunkt, ursprünglicher fingerprint, Ausdruck, Keys und Sprachwerte bleiben erhalten. Timeline enthält vorherige/nachfolgende Positionsfelder.

**consolidate:** nur eindeutige historical duplicate_key_consolidation-Metadaten mit übereinstimmendem Source-/Target-Key-Text. Für jede aktive Quellverknüpfung muss ein Beleg für genau dieses Finding und eine bereits aktive candidate-Zielverknüpfung existieren. Die aktuelle Fundstelle muss im Scan eindeutig gefunden und ihr Code-Key identisch sein. Geprüfte Quellkeys, dynamische Keys und Quellkeys mit eigenen key_values/dynamic_key_values bleiben ausgeschlossen. Keine Ketten-/Zyklenausführung innerhalb eines Plans. Unklare Fälle werden nicht als ausführbare Aktion aufgenommen.

Bei Ausführung werden Quellverknüpfungen merged und der abgelöste Key obsolete. Eine explizite reconciled_canonical_key_id hält das bestätigte Ziel fest; dies wird vom Sync vor dem Wiederbeleben des alten Fingerprint-Kandidaten berücksichtigt. Ungültige, geänderte oder verkettete Ziele werden abgewiesen. Es werden weder Findings noch Übersetzungswerte gelöscht/verschoben. Timeline protokolliert Links und Key-Status samt Ziel.

## Frischeprüfung und Transaktion

Vor Anwendung wird unter Datenbanksperren ein neuer Plan aus dem aktuellen Quellcode erstellt. Nur exakt wiederholbare Aktionen mit denselben Ausgangsdaten und Dateihashes werden ausgeführt. Veraltete, bereits erledigte oder nicht mehr eindeutige Aktionen werden mit Begründung übersprungen. Die übrigen Aktionen laufen in einer gemeinsamen Transaktion; Protokollierungsfehler rollen diese zurück. Dateihashes werden vor und nach Schreiben geprüft; Änderungen führen ebenfalls zum Rollback. Externe Editoren können nicht durch DB-Sperren blockiert werden; es besteht keine atomare Dateisystem-/Datenbanktransaktion.

Ein Wiederholungslauf desselben erledigten Plans meldet skipped und schreibt keine weiteren Historieneinträge. Bei skipped liefert das Command einen Fehlerstatus, damit Teilanwendungen nicht als vollständiger Erfolg erscheinen. Ein neuer Plan enthält bereits erledigte Aktionen nicht mehr.

## Konkreter Stand

Plan-ID: 8814b817b6216bab8b46d260a72e2631. 508 move-Aktionen und 190 consolidate-Aktionen. **Nach anschließender Nutzerfreigabe produktiv angewendet; siehe Nachprüfung unten.** Die JSON-Zusammenfassung steht in source-repair-plan-2026-10-16.json. Der vollständige Plan liegt privat im Storage; temporäre lesbare Ausgabe: /tmp/workbench-source-repair-plan.json.

Für das untersuchte Beispiel sind 1249/1263/1283 nach 1236 mit Links 6712/6718/6725 enthalten. 1858 bleibt ungeklärt; dessen historischer Timeline-Hinweis wird nicht automatisch in eine neue Zuordnungsentscheidung umgedeutet.

## Verifikation / Grenzen

6 neue gezielte Fälle, 32 Assertions: Speicherung, Signaturprüfung, unveränderte Daten vor Apply, Positionskorrektur, Wiederholung, veraltete Quelldatei, Protokollierungs-Rollback, belegte Zusammenführung und zwei normale Folgescans, Ausschluss ohne Beleg bzw. mit eigenen Werten. Gesamtsuite 744 Tests / 4696 Assertions bestanden. Bestehende isolierte PostgreSQL-Suite: 95 Ergebnisse bestanden. Die neuen Reparaturfälle selbst laufen in der SQLite-Workflow-Suite; keine produktive Apply-Abnahme durchgeführt.

Neu-Erfassung, Deaktivierung entfallener Fundstellen, Dateiwechsel sowie mehrdeutige Zuordnungen sind weiterhin nicht Teil von --apply. Der Prüfbericht listet diese Fälle, verändert sie aber nicht. Die zuvor reine Berichtsfunktion ist damit erweitert, nicht durch einen allgemeinen automatischen Voll-Sync ersetzt.

## Produktive Anwendung und Nachprüfung

Der Benutzer hat die Fortsetzung nach Vorlage des konkreten Plans bestätigt. Alle 508 Positionskorrekturen und 190 Zusammenführungen wurden ausgeführt, keine Aktion war veraltet. Insgesamt 227 Quellverknüpfungen wurden merged. Schreibgeschützte Nachprüfung: alle Positionsfelder und die erhaltenen Felder fingerprint, first_seen_at, raw_expression und literal_text entsprechen dem Plan; alle Quellkeys sind obsolete mit korrektem kanonischen Ziel. Keine Prüfabweichungen.

Historie: 508 finding_position_reconciled, 227 key_assignment_reconciled, 190 key_consolidation_reconciled. Der identische Plan wurde anschließend erneut aufgerufen: 698 skipped, keine angewendete Aktion. Exitcode 1 ist hierbei die dokumentierte skipped-Semantik. Eine zweite schreibgeschützte Nachprüfung lieferte exakt dieselben Resultate und Historienanzahlen.

Erneuter vollständiger lesender Scan: 18650 unverändert, keine moved-Zeile mehr; übrige Kategorien unverändert (974 new, 157 ambiguous, 47 missing, 3 reappeared, 129 ignored). Mehrfachzuordnungszeilen 662 → 435, weiterhin 99 ohne aktive Zuordnung. Diese Zahlen sind Berichtskategorien, keine pauschalen Fehler-/Reparaturaufträge.

Findings 3618, 3630, 3640 und 3652 besitzen nun jeweils nur die aktive Zuordnung zu 1236. Für 891 bleiben 1236 und 1858 bestehen; die nicht ausreichend belegte Zusammenführung von 1858 wurde nicht erzwungen. Neue/entfallene/mehrdeutige Fundstellen wurden nicht geschrieben oder deaktiviert. Kein produktiver allgemeiner Foundation-Sync, keine Quell-/Sprachdateiänderungen.

Nachweise: source-repair-applied-2026-10-16.json. Vorhandene Regressionstests zum Folgescan stammen aus dem vorherigen Implementierungsschritt; dieser Schritt hat keine neue Laufzeitlogik geändert.
