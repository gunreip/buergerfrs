# Abschluss der unbenannten Mehrfachzuordnungen

## Ergebnis

Die zuletzt offenen 83 Findings mit ausschließlich unbenannten Key-Kandidaten sind bereinigt. Der aktuelle Gesamtabgleich findet **keine unbenannten Mehrfachzuordnungen mehr**.

Verbleibend: 32 Findings mit benannten Keys, davon 31 mit gleichem TranslationKey und ein Fall mit unterschiedlichen TranslationKeys. Freigaben und historische Owner-Entscheidungen sind dort gesondert zu prüfen; es wurde keine automatische Wahl nach Alter/ID getroffen.

## Wichtige Präzisierung der Quellenprüfung

Ein unterschiedlicher `expression_fingerprint` beweist nicht, dass der Literaltext verschwunden ist. Bei den Keys #2181, #2185 und #2200 existieren die Texte weiterhin in `modal-history/timeline.blade.php`, nur die Schreibweise des Aufrufs unterscheidet sich von den ausgelagerten Partial-Views. Die frühere Einordnung „85 Ausdrücke fehlen“ bezog sich auf den identischen Ausdruck, nicht zuverlässig auf den Literaltext.

Die vertiefte Prüfung vergleicht deshalb zusätzlich den tatsächlichen Literaltext. Die drei Keys mit berechtigten Original-Findings #4150, #4151 und #4156 blieben aktiv. Nur die falschen Zusatzlinks zu #1513, #1517 und #1526 wurden archiviert. Das Audit-Reparaturskript verlangt weiterhin historische Belege, getrennte Dateien, passenden SuggestedKey, eindeutigen aktuellen Quellenabgleich und fehlende Freigaben/Werte; lediglich identischer Literaltext und gleiche Übersetzungsfunktion ersetzen die unnötig strenge Gleichheit der formatierten Ausdrücke beim unabhängigen Zeugen.

## Durchgeführte Schritte

1. Original-Findings #4797 und #8050 waren im vollständigen Quellenabgleich `missing`. Archivierung über `reconcileMissingFindings`, einschließlich ihrer zwei Links. Rohdaten und historische Zuordnung bleiben erhalten. Beide Keys hatten zu diesem Zeitpunkt noch den fremden Link und wurden in diesem Schritt nicht archiviert.
2. Für 82 historische Keys ist der ursprüngliche Literaltext in der jeweiligen verfügbaren, eingeschlossenen Quelldatei nicht mehr vorhanden. Kein Key besitzt Freigaben, Übersetzungswerte oder eine weitere aktive Verknüpfung. Ihre 82 fremden Links und danach die Keys wurden mit `historical_source_literal_removed` archiviert. Die heutigen Findings und ihre passenden Keys blieben unverändert.
3. Die drei oben genannten berechtigten Keys blieben bestehen; ausschließlich ihre fremden Links wurden archiviert.
4. Letzter Fall #3863: Findings #3243/#3246 sind im Git-Stand `929f61a710891d9669a057c7d5b55931e852954f` eindeutig über ihre verschiedenen Kontextzeilen belegt: `$runtimeSource['key_id']` beziehungsweise `$linkPreviewRelatedSource['key_id']`. Zwei vorangestellte Zeilen verschoben die Positionen von 88/132 auf 90/134. Identische, eindeutige vollständige Kontextzeilen sowie die ursprünglichen Signaturen sichern die Nachführung ab. Danach wurde der letzte fremde Link #3866 zu Finding #482 archiviert; Key #3863 und seine beiden richtigen Findings bleiben aktiv.

Insgesamt in diesem Schritt: **82 Keys archiviert, 86 fremde Links bereinigt, zwei fehlende Findings mit ihren zwei Links archiviert und zwei Finding-Positionen korrigiert**. Sämtliche Änderungen sind in der Timeline dokumentiert. Es wurden keine Übersetzungen veröffentlicht, keine Quelltexte geändert und keine historischen Findings reaktiviert.

Ein zunächst noch auf den vorherigen Gesamtbestand bezogener Drei-Link-Plan wurde nach der ersten Archivierung erwartungsgemäß als veraltet abgewiesen. Erst der frisch erstellte Plan wurde angewendet.

## Prüfung

- Vorher-/Nachher-Prüfsummen der Archivierungsbatches bestätigen unveränderte Übersetzungswerte, Reviews, unbeteiligte Keys/Links und aktuelle Findings. Die zwei ausdrücklich archivierten Findings wurden zusätzlich auf Erhalt ihrer ursprünglichen Rohfelder geprüft.
- Wiederholter Archivierungsplan für die 82 Keys: **0 Aktionen**.
- Abschließender Gesamtbestand: **32 benannte, 0 unbenannte Mehrfachzuordnungen**.
- 18 gezielte Pest-Regressionstests bestanden, 230 Assertions. Der Test für dateiübergreifende Unabhängigkeit umfasst jetzt auch unterschiedlich formatierte Aufrufe desselben Literals und wiederholte Scans in anderer Dateireihenfolge.
- PHP-Syntax und scoped whitespace geprüft; kein vollständiger Testlauf und keine neue Laufzeitlogik.

## Nachweise

- [Ergebnisse, Timeline-IDs, Prüfsummen und verbleibende Gruppen](final-unkeyed-origin-cleanup-2026-10-17.json)
- [Archivierung der fehlenden Original-Findings](archive-removed-literal-findings-2026-10-17.php)
- [Geprüfte 82 historische Ursprünge](absent-unkeyed-origins-2026-10-17.json)
- [Archivierung der unbenutzten Kandidaten](retire-absent-unkeyed-origins-2026-10-17.php)
- [Kontextbelegte Positionskorrektur der Key-Badges](reconcile-key-badge-context-2026-10-17.php)
