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
`strang.merge-extension` und `strang.rekey-source` gelten die aktuell geprüften
Bounds als Vertrag:

- `strang.trunk` trennt reine Strang-Bounds, start/end Sub-Bounds,
  middle Sub-Bounds und label-inclusive Bounds.
- `strang.merge` und `strang.merge-extension` trennen den label-inclusiven
  Start/Stem-Bereich, den Tail-Stem bis zum Arc und die Bridge-Bounds.
- `strang.rekey-source` verwendet dieselbe Start/Stem/Bridge-Bounds-Logik wie
  `strang.merge`, weil es über dieselbe `paths.merge`-Kette gerendert wird.

Wenn ein neuer Kollisions- oder Layout-Footprint benötigt wird, wird dafür eine
neue explizite Debug-Bounds-Schicht ergänzt. Bestehende, optisch geprüfte
Bounds werden nicht durch versteckte Offsets, pauschale Padding-Annahmen oder
datensatzspezifische Sonderfälle angepasst.

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
strang.{kind}.{side?}.{index}.{chapter}.{element}
```

Regeln:

- `kind`: `trunk`, `merge`, `branch`, `rekey-source`, `rekey-target`.
- `side`: nur wenn fachlich vorhanden, also `left` oder `right`.
- `index`: laufender Strang-Zähler innerhalb derselben `kind`/`side`-Gruppe.
- `chapter`: `main`, `extension.{n}`, `return.{n}`, `start`, `end` oder
  eine vergleichbare fachliche Untergruppe.
- `element`: das konkret steuerbare Element, zum Beispiel `bridge1`,
  `stem2`, `arc1`, `step.label`, `start.label`, `end.cap`.
- `path.branch`, `path.merge`, `path.rekey-*` wird in element ids nicht
  wiederholt, wenn `kind` und `chapter` die fachliche Ebene bereits benennen.
- Nodes werden über den Anchor des steuerbaren Elements beschrieben:
  `.anchorStart`, `.anchorEnd`, `.nodeStart`, `.nodeEnd`, wenn die
  Unterscheidung gebraucht wird.
- Label- und Connector-Elemente hängen direkt an das Element, das sie
  beschreiben: `.label`, `.connector`, `.label.left`, `.label.right`.

Beispiele:

```text
strang.trunk.1.main.stem3
strang.trunk.1.main.stem3.anchorEnd
strang.trunk.1.start.label
strang.trunk.1.end.cap
strang.trunk.1.end.label

strang.merge.left.1.main.start.stem
strang.merge.left.1.main.start.label
strang.merge.left.1.main.stem1
strang.merge.left.1.main.bridge1
strang.merge.left.1.main.arc1
strang.merge.left.1.main.arc2
strang.merge.left.1.extension.1.start.stem
strang.merge.left.1.extension.1.stem1
strang.merge.left.1.extension.1.bridge1

strang.branch.left.1.main.entry.stem
strang.branch.left.1.main.arc1
strang.branch.left.1.main.bridge1
strang.branch.left.1.main.arc2
strang.branch.left.1.main.step.label
strang.branch.left.1.main.stem1
strang.branch.left.1.end.stem
strang.branch.left.1.end.cap
strang.branch.left.1.end.label

strang.rekey-source.left.1.main.start.stem
strang.rekey-source.left.1.main.bridge1
strang.rekey-source.left.1.main.compressed-stem
strang.rekey-target.right.1.main.bridge1
strang.rekey-target.right.1.end.label
```

Correction-Config und Collision-Reports sollen künftig diese element ids
verwenden. Die alte rendernahe Form kann vorübergehend als Alias unterstützt
werden, darf aber nicht als neue Zielschreibweise weiter ausgebaut werden.
