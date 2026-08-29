Ja, ein paar Konstellationen sollten wir noch gezielt im Hinterkopf behalten, bevor wir daraus Regeln ableiten:

1. `single active` mit vielen Events
   Beispiel `#590`, `#594`: wenig Graph-Breite, aber viele Timeline-Events. Wichtig für: Trunk-only, Event-Verdichtung, keine unnötigen Merge/Branch-Stränge.

2. `single inactive`
   Wichtig für: “Datensatz endet einfach” vs. “wurde rekeyed/merged”. Hier muss klar sein, ob ein End-Label reicht oder ob ein Rekey-/Branch-Hinweis nötig ist.

3. `moved` als Quelle und als Ziel
   Haben wir angerissen, aber beide Richtungen sollten mit mehreren Datensätzen geprüft werden: `rekey-source` und `rekey-target`.

4. `bulk/shared` mit sehr vielen Origins
   Haben wir mit `#1014` und Stress-Samples. Wichtig für Aggregation/Ellipsis und spätere Layout-Regeln.

5. `bulk/shared` mit wenigen Origins
   Damit prüfen wir, ob Aggregation nicht zu früh greift und kleine Graphen nicht künstlich aufblasen.

6. “Ended before target” / “Ended after merge” Mischfälle
   Wichtig für Branch-/Branch-End-/Step-Darstellung. Da hatten wir bereits erste Fälle, aber die Regeln sollten später sauber aus Outcome-Gruppen kommen.

7. Pending shared candidates
   Also Datensätze, die noch nicht final shared/merged sind. Wichtig, weil sie eher “Vorschlag/Review” als “Historie abgeschlossen” darstellen.

8. Chains ohne lang values oder ohne active UI locale value
   Dann müssen Labels/Fallbacks sichtbar und ehrlich bleiben, ohne still etwas zu unterschlagen.

Kurz: wir haben die wichtigsten graphischen Bausteine abgedeckt. Was jetzt fehlt, ist weniger neue Geometrie, sondern ein Regelwerk: wann wird welcher Strang gebaut, wann aggregiert, wann endet etwas, wann ist es nur ein Hinweis.
