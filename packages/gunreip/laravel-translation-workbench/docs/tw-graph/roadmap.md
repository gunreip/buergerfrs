# TW Graph Roadmap

This roadmap lists open TW Graph work areas. It is intentionally grouped by
theme so implementation does not drift into scattered one-off changes.

## Documentation

Next useful documentation blocks:

- prop reference for `tw-graph`, `strang.*`, `parts.*`, `paths.*` and
  `segments.*`;
- data-driven render-plan examples;
- debug bounds examples with screenshots or diagrams;
- layout-correction examples;
- sample walkthroughs for Resume and Bug Lifecycle;
- package installation and publishing notes when the package boundary is ready.

## Component API

The component API should continue moving toward:

- consistent `whoWhat` prop names;
- no duplicate props for the same behavior;
- defaults passed through from the graph context;
- explicit deltas/factors when values modify defaults;
- stable canonical element references.

Known concepts to keep aligned:

- `stemLength` and `stemContinuation`;
- `bridgeLength` and `bridgeContinuation`;
- label width modes;
- direction-aware start/end labels;
- image labels;
- automatic joint markers when full dots are hidden.

## Data-Driven Renderer

The data-driven renderer should keep improving around general rules:

- classify lifecycle cases before rendering;
- keep event compaction chronological;
- avoid fixed minimum trunk paths;
- render small merge sets directly;
- aggregate only when count requires it;
- keep rekey source/target transitions inspectable;
- keep fallback states visible in DEV output.

Any discovered dataset issue should first be tested as a general case, not as a
timeline-chain-specific patch.

## Layout And Collision System

The collision system should keep moving toward precise sub-bound decisions:

- trunk vs branch;
- trunk vs rekey target;
- trunk vs rekey source;
- branch vs branch;
- merge start/stem/bridge overlaps;
- re-checks after applied compensation.

The debug table should remain the primary audit surface for:

- potential collisions;
- actual collision type;
- collision delta;
- applied compensation;
- final correction deltas.

## Configuration

Configuration should remain the place for:

- package defaults;
- graph-family defaults;
- color semantics;
- compensation gaps/factors/steps;
- layout corrections.

Open work:

- audit remaining hard-coded visual values;
- ensure fallbacks resolve through defaults;
- document all config keys with meaning and examples;
- keep sample-specific tuning inside sample pages.

## App Integration

Possible app-side documentation feature:

- add `Administration -> TW-Graph -> Documentation`;
- render the Markdown docs in a readable internal page;
- group docs by tabs or sidebar;
- keep examples linked from docs to sample pages.

This should come after the Markdown structure is stable.

## Testing

Useful future checks:

- snapshot/debug data for known dataset cases;
- render smoke checks for sample pages;
- assertions that `dev=false` does not disable layout calculation;
- assertions that `coordinates=false` hides badges only;
- detection of duplicate canonical element references;
- config/default override tests.

## Package Boundary

Before publication, check:

- all TW Graph assets live under the package;
- app-level graph leftovers are removed or intentionally kept outside package
  scope;
- routes/views are publishable or clearly registered;
- docs describe package usage instead of only the current app workspace;
- generated protocol/cache files are not treated as source.
