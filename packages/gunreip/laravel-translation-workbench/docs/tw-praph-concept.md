Ja, genau. Und diese Trennung ist der Kern, damit das System nicht wieder in Einzelfall-Frickelei abrutscht.

So würde ich die Ebenen sauber benennen:

`primitives`
wissen nur, **wie ein elementarer Strich/Text/Node gerendert wird**.
Beispiele: `line`, `arc`, `connector`, `text`, `node`, `dev-node-counter`.

`segments`
wissen, **wie primitives zu einem konkreten graphischen Baustein zusammengesetzt werden**.
Beispiele: `path`, `arc`, `start`, `step`, `end`, `label`.

`segments.label` hängt an einem konkreten Node und beschreibt dort
datensatzspezifische Informationen. `segments.step` liegt dagegen zwischen
zwei Path-Abschnitten und beschreibt einen gemeinsamen Strang-Zustand oder
eine Begründung, zum Beispiel `source inactive`.

`paths`
wissen, **wie mehrere segments zu einem fachlichen Pfad zusammengesetzt werden**.
Beispiele: `branch`, `branch-extension`, `merge`, `merge-extension`, `trunk`.

`strang`
weiß, **wie paths zu einer Bausteingruppe mit gemeinsamen Anchors, Defaults, Bounds und Labels zusammengesetzt werden**.
Beispiele: `strang.trunk`, `strang.merge-left`, `strang.branch-left`.

`graph assembler / data-driven layer`
weiß, **welche Strangs aus den Daten entstehen sollen**.
Also: Outcome-Gruppen, Attach-Targets, Counts, Labels, Farben, fachliche Bedeutung.

`tw-graph`
ist der Rahmen: Canvas, globale Defaults, Registry, Dev-Mode, Coordinates, Bounds.

DEV-Bounds gehören ausdrücklich zum Debug-System. Wenn eine Layout-Regel wie
Branch-Collision oder Canvas-Bounds berechnete Flächen verwendet, müssen diese
Flächen im DEV-Mode sichtbar bleiben und dieselbe Datenbasis wie die eigentliche
Layout-Entscheidung benutzen. Sie sind kein temporärer Render-Test, sondern die
Kontrollschicht, mit der nachvollziehbar bleibt, warum ein Strang verschoben,
verlängert oder als kollidierend markiert wurde.

Verifizierte DEV-Bounds dürfen nicht stillschweigend als Nebenwirkung anderer
Layout-Arbeiten geändert werden. Für `strang.trunk`, `strang.merge`,
`strang.merge-extension`, `strang.rekey-source` und `strang.rekey-target`
gelten die aktuell geprüften Bounds als Vertrag:

- `strang.trunk` trennt reine Strang-Bounds, start/end Sub-Bounds,
  middle Sub-Bounds und label-inclusive Bounds.
- Eine optionale vertikale Entzerrung am `strang.trunk`-Start wird nicht als
  äußerer Offset gerendert. Wenn `trunk_start_shift_enabled=true` gesetzt ist,
  wird der erste Trunk-Stem nach dem Start-Segment
  (`trunk.center.1.stem-1` / `path1`) um `trunk_start_shift_length`
  verlängert.
  Entsteht danach weiterhin eine echte Kollision zwischen einem konkreten
  Trunk-Start-Node-Label und einem Side-Strang-Footprint, darf nur dieser erste
  Trunk-Stem zusätzlich kompensiert werden. Die Kompensation wird in
  `Applied Compensation` sichtbar gemacht; sie darf nicht als versteckter
  Offset an Side-Strängen oder Labels umgesetzt werden.
  Dadurch bleiben Gradient, AnchorRegistry, Debug-Bounds und angesetzte
  Stränge in derselben `strang.trunk -> paths.trunk -> segments.path`-Chain.
- `strang.merge` und `strang.merge-extension` trennen den label-inclusiven
  Start/Stem-Bereich, den Tail-Stem bis zum Arc und die Bridge-Bounds.
- `strang.rekey-source` verwendet dieselbe Start/Stem/Bridge-Bounds-Logik wie
  `strang.merge`, weil es über dieselbe `paths.merge`-Kette gerendert wird.
- `strang.rekey-target` verwendet die branch-artige Bridge/Body/End-Bounds-
  Logik. Trunk-Label-Kollisionen werden über die rekey-target-Bridge
  kompensiert; finale End-vs-Bridge-Kollisionen laufen durch dieselbe
  Trunk-Stem-Spacing-Schiene wie Branch-Stränge.

Wenn ein neuer Kollisions- oder Layout-Footprint benötigt wird, wird dafür eine
neue explizite Debug-Bounds-Schicht ergänzt. Bestehende, optisch geprüfte
Bounds werden nicht durch versteckte Offsets, pauschale Padding-Annahmen oder
datensatzspezifische Sonderfälle angepasst.

`strang.merge`-Layouts werden datengetrieben geplant. Die Anzahl der direkt
sichtbaren Merge-Kandidaten und der aktuelle Main/Extension/Tail-Stem-Rhythmus
werden als `merge_layout`-Defaults beschrieben. Der Builder entscheidet anhand
dieser Defaults, welche Origins einzeln, aggregiert oder als Tail gerendert
werden. Optische Nacharbeiten laufen danach über Collision-Compensation oder
Layout-Corrections, nicht über verstreute Sonderwerte im Merge-Builder.
`merge_layout.direct_per_side_before_aggregate` definiert, wie viele reale
Merge-Stränge pro Seite ohne Aggregate-Slot gerendert werden. Bei `5` gilt:
bis 10 Origins werden vollständig real als `strang.merge` plus
`strang.merge-extension` dargestellt; bei 11 Origins aggregiert nur die Seite
mit 6 Origins; bei größeren Mengen greift wieder Head + Aggregate + Tail.
Aggregate-Merge-Labels verwenden `colors.merge_aggregate`; der eigentliche
Merge-Strang behält `colors.merge`, damit Verdichtung sichtbar ist, ohne die
fachliche Merge-Kette in eine neue Strang-Art umzudeuten.
Die Merge-Collision-Policy besitzt dafür eine bevorzugte Richtung:
`merge_layout.preferred_compensation_direction=vertical|horizontal`. Diese
Policy wählt nur, welcher berechnete Kandidat bevorzugt angewendet werden
soll; die Collision selbst bleibt über die Debug-Bounds und die Tabelle
nachvollziehbar.

Die Merge-Staffelung ist Teil der Baseline, nicht Teil der Collision-
Compensation. `merge_layout.vertical_stagger_*` setzt echte
`stem_continuation`-Props auf jedes konfigurierte Merge-Sequence-Element
(`main=1`, `extension1=2`, `extension2=3`, ...; `odd` oder `even`). Der
konfigurierte Stem ist dabei ein Mindestziel: normale Extensions treffen damit
z. B. `stem2`, Aggregate-Extensions mit vielen Continuations treffen den letzten
vorhandenen Stem, z. B. `stem12`. Erst danach werden die gerenderten Sub-Bounds
gemessen. Eine automatische Merge-Compensation darf nur auf konkrete Sub-Bounds
wie Label-vs-Label oder Bridge-vs-Label reagieren und addiert ihren Delta auf
den bereits gesetzten Stagger-Wert. Breite Main-Bounds bleiben Diagnose, dürfen
aber keine automatische Vertikal-Verlängerung auslösen.

Wichtig ist dabei: Eine untere Ebene darf nicht plötzlich fachliche Entscheidungen treffen. `paths.branch-extension` darf wissen, wie eine Branch-Extension aussieht, aber nicht, warum sie existiert. Das „warum“ gehört in den Assembler. Und der Assembler darf wiederum nicht anfangen, einzelne Arcs und Lines per Hand zusammenzustecken, weil das in `paths/segments` gehört.

Das wäre die Regel, die wir jetzt wirklich konsequent halten sollten.

## Element IDs / Debug IDs / Correction Targets

Es gibt zwei unterschiedliche ID-Arten:

`component/render ids`
dürfen intern lang bleiben, weil sie die tatsächliche Component-Chain abbilden.
Beispiel: `strang.branch-left.1.main.path.branch.bridge1`.

`element ids`
sind die stabile fachliche Schreibweise für Tooltips, Debug-Bounds,
Collision-Reports und Correction-Config. Diese IDs sollen kurz, eindeutig und
ohne doppelte Ebenen formuliert werden.

Neue Zielschreibweise:

```text
strang.{side?}.{index?}.{kind}.{chapter?}.{element}
```

Regeln:

- `side`: nur wenn fachlich vorhanden, also `left` oder `right`.
- `index`: laufender Strang-Zähler innerhalb derselben `side`/`kind`-Gruppe.
- `kind`: `trunk`, `merge`, `branch`, `rekey.source`, `rekey.target`.
- `chapter`: `extension.{n}`, `return.{n}`, `start`, `end` oder
  eine vergleichbare fachliche Untergruppe.
- `element`: das konkret steuerbare Element, zum Beispiel `bridge1`,
  `stem2`, `arc1`, `step.label`, `start.label`, `end.cap`.
- `main` wird in element ids nicht ausgegeben. Was nicht `extension`,
  `return`, `start` oder `end` ist, liegt implizit im Hauptteil.
- `path.branch`, `path.merge`, `path.rekey-*` wird in element ids nicht
  wiederholt, wenn `kind` und `chapter` die fachliche Ebene bereits benennen.
- Nodes werden über den Anchor des steuerbaren Elements beschrieben:
  `.anchorStart`, `.anchorEnd`, `.nodeStart`, `.nodeEnd`, wenn die
  Unterscheidung gebraucht wird.
- Label- und Connector-Elemente hängen direkt an das Element, das sie
  beschreiben: `.label`, `.connector`, `.label.left`, `.label.right`.

Beispiele:

```text
strang.trunk.1.stem3
strang.trunk.1.stem3.anchorEnd
strang.trunk.1.start.label
strang.trunk.1.end.cap
strang.trunk.1.end.label

strang.left.1.merge.start.stem
strang.left.1.merge.start.label
strang.left.1.merge.stem1
strang.left.1.merge.bridge1
strang.left.1.merge.arc1
strang.left.1.merge.arc2
strang.left.1.merge.extension.1.start.stem
strang.left.1.merge.extension.1.stem1
strang.left.1.merge.extension.1.bridge1

strang.left.1.branch.entry.stem
strang.left.1.branch.arc1
strang.left.1.branch.bridge1
strang.left.1.branch.arc2
strang.left.1.branch.step.label
strang.left.1.branch.stem1
strang.left.1.branch.end.stem
strang.left.1.branch.end.cap
strang.left.1.branch.end.label

strang.left.1.rekey.source.start.stem
strang.left.1.rekey.source.bridge1
strang.left.1.rekey.source.compressed-stem
strang.right.1.rekey.target.bridge1
strang.right.1.rekey.target.end.label
```

Correction-Config und Collision-Reports sollen künftig diese element ids
verwenden. Die alte rendernahe Form kann vorübergehend als Alias unterstützt
werden, darf aber nicht als neue Zielschreibweise weiter ausgebaut werden.

`tooltip labels`
sind eine reine DEV-Anzeige und dürfen knapper sein als element ids. Sie lassen
generische Chain-Füllwörter wie `strang`, `path`, `paths`, `main`, `segment`
und `bounds` weg und zeigen zuerst die fachliche Zuordnung:

```text
branch.left.7.bridge-1
branch.left.7.end
branch.left.7.arc-east-north-1.anchorNode-end
merge.left.1.extension-3.stem-2.anchorNode-end
rekey.right.source.2.bridge-1
trunk.center.1.stem-3.label-2
```

Diese Kurzlabels sind nicht der Lookup-Key für Collision- oder
Correction-Logik. Die Logik arbeitet weiter mit den normalisierten element ids.
