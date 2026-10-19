# Zuordnungshistorie der vier offenen Sonderfälle

Stand: 18. Oktober 2026. Ausschließlich lesende Prüfung; keine Datenbestandsänderungen.

## Findings #6716 und #6731 – Stored values

Die heutige Quelle enthält jeweils `ui.values.stored-values`. Beide Findings sind Key #321 und zusätzlich den ungeprüften Keys #4826 beziehungsweise #4827 zugeordnet.

Die Historie belegt eine ausdrückliche gemeinsame Entscheidung für genau diese beiden Findings:

- Events #107205/#107206: gemeinsame Freigabe am 30. Juli 2026 um 16:56:19 für `ui.stored-values`.
- Events #107208/#107210: Der Scanner legt 20 Sekunden später zusätzliche Zuordnungen zu #4826/#4827 an.
- Reviews #913/#914 und Events #107642/#107643: ausdrückliche gemeinsame Freigabe beider Findings auf #321 mit `ui.values.stored-values` um 17:09:30.
- Die beiden zusätzlichen Keys haben keine eigenen Reviews. Die abgefragten sieben Keys haben keine Einträge in den Tabellen für eigene skalare oder dynamische Key-Werte; veröffentlichte Lang-Werte sind davon getrennt.

Damit besteht konkrete Evidenz für historische Scanner-Doppelzuordnungen. Eine gezielte Bereinigung kann auf der belegten Entscheidung für diese Findings aufbauen, statt Textgleichheit als Zusammenführungsgrund zu verwenden. Vor einer Anwendung müssen alle weiteren aktiven Verknüpfungen der beiden zusätzlichen Keys erneut geprüft werden.

## Finding #4617 – Reset

Die Quelle enthält `ui.button.reset.reset`. Key #3145 ist ungeprüft, Key #5240 geprüft. Die Reviews #1135–#1137 von #5240 beziehen sich jedoch auf Findings #7936/#7937/#7938, nicht auf #4617.

Die zusätzliche Verbindung von #5240 zu #4617 entstand später durch den Scanner (Event #124857). Der Prüfstatus des Keys beweist hier keine bewusste Übernahme dieser konkreten Fundstelle. Keine automatische Stilllegung von #3145 auf dieser Grundlage.

## Finding #5093 – Save

Aktiv verknüpft sind #4507 (`ui.button.save.save`) und #4613 (`ui.save`). Der aktuelle Quellcode-Abgleich meldet die gespeicherte Fundstelle als `missing`. Im heutigen Profil-Template existiert ein Aufruf von `ui.button.save.save` in Zeile 254.

Das allein beweist keine Identität mit der alten Fundstelle: Zunächst sind die vollständigen Findings des betreffenden Files und die Änderungshistorie abzugleichen. Eine neue Erfassung, Verschiebung und bereits anderweitig erfasste Ersatzfundstelle müssen unterschieden werden. Keine Reaktivierung obsoleter Findings und keine pauschale Umschreibung von `ui.save`.

## Konsequenz

Prüfstatus und Prüfdatum eines Keys sind hilfreich, reichen als Begründung für eine Zuordnungsbereinigung aber nicht aus. Entscheidend ist zusätzlich, welche konkrete Fundstelle Gegenstand der Freigabe war. Unabhängige Übersetzungsaufgaben mit gleichem Literal bleiben davon unberührt.

Nachweise: `reviewed-pending-history-2026-10-18.json`. Die Abfrage läuft in einer ausschließlich lesenden Transaktion; das zugehörige PHP-Skript liefert bei Fehlern einen Fehlerstatus.
