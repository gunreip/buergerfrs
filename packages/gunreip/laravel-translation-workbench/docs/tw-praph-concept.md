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
