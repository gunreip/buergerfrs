# Alte IF-Kette: Fähigkeitenvergleich und Archivierung

Stand: 18.09.2026. Ergebnis: sieben Komponenten nach `resources/views/components/ui/tw-graph/strang/_old/` verschoben, nicht gelöscht. Acht bestehende Testdefinitionen wurden aus `ManualPartsViewTest.php` in `ArchivedIfViewTest.php` überführt. Die Tests laufen weiterhin und verwenden ausdrücklich `strang._old.*`.

## Entscheidung

Für die derzeit aktiven Graphen und Dokumentationsbeispiele wird die alte Kette nicht mehr benötigt. Außer Tests gibt es im untersuchten Repository keinen äußeren Aufruf von `if-else-endif` und keine unabhängige aktive Verwendung seiner sechs Bausteine. Die aktuellen IF-Stränge verwenden diese Kette nicht.

Das bedeutet **keine vollständige Gleichheit der APIs oder Geometrie**. Die alte Kette besitzt spezielle Darstellungsoptionen, die im Archiv mitsamt ihren Tests erhalten bleiben. Externe Paketnutzer oder dynamisch zusammengesetzte Aufrufe außerhalb des geprüften Quellcodes sind damit nicht ausgeschlossen.

## Fähigkeitenvergleich

| Alte Fähigkeit | Aktueller Stand | Bewertung |
|---|---|---|
| IF mit optionaler Vorbeiführung | `flow-if`, `ifStart`/`ifEnd` und Fallback ohne Action | Benötigte Kontrollstruktur ist vorhanden; andere Geometrie und andere Prop-Namen. |
| Eine oder mehrere ELSEIF-Bedingungen | `flow-if-elseif`, `flow-if-elseif-multi`, `elseifs` | Benötigte Kontrollstruktur ist vorhanden. |
| Links/rechts mit lesbaren Labels | `side` bei aktuellen IF-Strängen; aktuelle Tests und Nested-Beispiele | Abgedeckt. Alte Spiegelungstests bleiben als Archivnachweis erhalten. |
| Gemeinsame Breite aller Bedingungslabels | Alte `condition-set`/`elseif-group` überschreiben Labelbreiten auf gemeinsame Breite | Kein identischer öffentlicher Vertrag: aktuelle Multi-Komponente gleicht Action-Spannweiten aus. Alte Breiten-Normalisierung nicht in die neue API übertragen. |
| Intro-/ENDIF als Inline-Block, unabhängig links/rechts ausgerichtet | Alte `introLabel.side`, `endLabel.side`, Wrapper-eigene Anfangs-/Endgeometrie | Kein direkter Ersatzprop. Im neuen Ansatz sind Informationslabels und zusätzliche Bausteine eigenständig zu komponieren. Historische Umsetzung bleibt erhalten. |
| Intro-/ENDIF als Node-Information ohne Verschiebung der IF-Geometrie | Alte `placement=node`-Option | Aktuelle Segment-Labels bieten Informationslabels an Ankern; kein behaupteter 1:1-Ersatz für den alten Wrapper. |
| THEN-Abschluss als Stem, Bogen oder ohne Fortsetzung | Alte `thenContinuation`, `thenEndSide`, `leftStem` | Spezifische alte Anschlussgeometrie. Aktuelle `actionLabel.return=false`/Rückführungsanker lösen Nested-Einstiege anders. Alte Bogenwahl bleibt im Archiv. |
| ENDIF-Fortsetzung `bridge` oder `none` | Alte `endContinuation` | Wrapper-spezifische Darstellung; kein eigener Kontrollfluss. Kein identischer aktueller Prop. |
| Label-Farben, Übergänge und Bridge-Begrenzungen | Gemeinsame TextLabel-/LabelBridge-/Segment-Bausteine; aktuelles IF mit Zweigfarben | Grundfunktion vorhanden; alte exakte Verlaufstests bleiben im Archiv. Begrenzungen sind kein Beleg für uneingeschränktes Durchreichen jedes Wertes. |
| Alte Anchor-Namen (`conditions.then`, `conditions.left`, `endif`) | Neue IFs veröffentlichen eigene gemeinsame Ausgänge und Nested-Rückführungsanker | Nicht kompatibel umbenennen. Historische IDs und Registry-Schlüssel im Archiv unverändert erhalten. |
| Nested IF, unabhängige Rückführungen, mehrfache Verschachtelung | Aktuelle IFs mit offenen Zweigen, ReturnColorRegistry und dokumentierten Nested-Beispielen | Wird bereits ohne alte Kette realisiert. |

## Archivinhalt

- `_old/if-else-endif.blade.php`
- `_old/flow-if-start.blade.php`
- `_old/flow-if-end.blade.php`
- `_old/flow-if-bypass.blade.php`
- `_old/flow-if-condition.blade.php`
- `_old/flow-if-condition-set.blade.php`
- `_old/flow-if-elseif-group.blade.php`

Nur Komponenten-Aufrufpfade innerhalb der Kette und ihrer Tests wurden auf `_old` umgestellt. Verhalten, authored IDs, Default-IDs, `sourceType` und exportierte Anker bleiben als historischer Stand erhalten. Am alten öffentlichen Pfad gibt es keine Alias-Dateien.

## Abgrenzung

Die Archivierung ist keine Änderung der aktuellen IF- oder SWITCH-Geometrie. Bei der Prüfung fiel außerdem der bestehende interne Default `returnOffset ??= '12rem'` bei offenen ELSEIF-Zweigen in `flow-if-elseif-multi` auf. Dieser gehört zur aktuellen Kette und sollte getrennt im Props-Vertrag und in der Dokumentation geprüft werden; er wurde hier weder entfernt noch als unproblematisch bestätigt.

Für spätere Wiederverwendung einer Archivfähigkeit: Bedarf und öffentliche Props zuerst festlegen, Verhaltenstest anlegen und dann auf passender Ebene integrieren. Nicht die gesamte alte API wieder als parallelen Einstieg aktivieren.
