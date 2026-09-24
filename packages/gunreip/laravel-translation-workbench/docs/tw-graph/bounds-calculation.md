# Canvas bounds: ownership and verification

## One model

Every visible drawing primitive declares its geometry through `PrimitiveBounds` and a
`data-tw-graph-bounds` record. This includes line endpoint dots/caps, arc nodes, joint arrows,
connectors and node images. DEV counters and diagnostic boxes are not drawing content.

`tw-graph.blade.php` collects the actual rendered records after the component chain has
finished. `BoundsRegistry::capture()` resets that graph's previous entries, resolves the
root's line/node/arc sizes, and builds the canvas bounds. The same records are serialized
for the browser. Both handmade and protocol-driven graphs use this model.

Parts, paths and strangs no longer register independent approximate canvas rectangles.
DEV boxes no longer influence canvas size. Their diagnostic outlines remain available.

The existing coordinate policy is retained: bounds include the coordinate origin;
`horizontal-padding` offsets that origin from the left content boundary. `min-width` and
viewport width can leave additional space on the right. No automatic centering or
component-length compensation is performed by the bounds calculation.

## Explicit browser-dependent entries

Text records are marked `kind: text`. The server provides a provisional rectangle, not a
claim about exact font layout. Once fonts and layout are available, the browser replaces
only these text rectangles with their measured dimensions and aggregates the same model.
The DEV status identifies this transition.

Generated line jumps publish their own `kind: derived` rectangles from the existing
line-jump renderer's geometry. These are added to the model when that renderer creates
the drawing. The bounds checker does not infer replacement route geometry from pixels.

All geometry records are compared with the rendered primitive, including pseudo-element
endpoint dots and caps. Differences greater than 1.1 CSS pixels (border/subpixel rounding)
produce a diagnostic with the component ID and expected/actual limits. Geometry mismatches
never replace the declared geometry. An unresolvable length prevents a partial aggregate
from replacing the server calculation. Unknown numeric expressions remain `null` on the
server instead of silently disappearing from numeric bounds.

Canvas dimensions, the content outline and the left/center/right diagnostic values use the
same resolved model. A DEV mismatch badge and `tw-graph-bounds-checked` event expose errors;
`data-tw-graph-bounds-issues` also remains available with DEV disabled.

## Regression checks

- `PrimitiveBoundsViewTest`: checks server-only coverage, global/local node sizes, stale
  render cleanup, and bounds contracts in every existing documentation example.
- `BoundsRegistryTest`: checks aggregation and explicitly unresolved dimensions.
- `tests/Js/tw-graph-bounds.test.mjs`: checks the coordinate policy and mismatch handling.
- `tests/Js/tw-graph-bounds.browser.cjs`: compares all saved examples in Chrome, then
  deliberately corrupts a line length. It verifies that the fault is reported without
  redefining the geometry, and checks zoom, DEV changes, hidden content and text refinement.
  Each page is checked independently; failures are collected rather than stopping the audit.

Run the browser audit after building assets:

```sh
npm run build
php tests/Js/render-tw-graph-bounds-fixtures.php /tmp/tw-graph-bounds-audit
node tests/Js/tw-graph-bounds.browser.cjs /tmp/tw-graph-bounds-audit
```

An external Playwright installation and Chrome executable can be supplied with
`PLAYWRIGHT_MODULE` and `CHROMIUM_PATH`. Results are written to `result.json` alongside the
rendered test fixtures. These generated fixtures are not application source.

## Verified snapshot — 2026-09-21

- 111 documentation pages, 225 graphs, 9,943 bounds records: no geometry mismatches.
- Deliberately altered line geometry: mismatch reported; declared bounds retained.
- Missing CSS variable: explicitly reported; no zero-value fallback and no partial bounds replacement.
- 1,042 TwGraph Pest tests passed.
- The three largest rendered examples (`flow-if-nested-5`, `-6`, `-7`) also rendered under
  a 128 MiB PHP limit; measured CLI peak was 62.5 MiB per process.

This verifies the current examples and tested browser configuration. It does not claim
that arbitrary future CSS overrides or new primitives are automatically correct; the
coverage and mismatch checks are intended to expose those regressions.
