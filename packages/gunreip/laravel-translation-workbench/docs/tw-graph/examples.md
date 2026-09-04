# TW Graph Examples

TW Graph examples should demonstrate real usage patterns. They are not only
visual showcases; they should also explain which layer is responsible for which
part of the graph.

## Example Types

`Data Driven / Datasets`
: Renders real translation-workbench timeline chain data. This page is used to
  inspect many datasets, verify layout rules and demonstrate how a graph can be
  assembled from records.

`Samples / Resume A. Einstein`
: A hand-authored design sample. It demonstrates `parts.*`, custom chaining,
  image labels, label widths and direction handling.

`Samples / Bug Lifecycle`
: A hand-authored technical sample. It demonstrates how a process graph can use
  trunk, merge-like paths, branch-like paths and explicit authoring values
  without being tied to translation timeline data.

## Data Driven Datasets

The datasets page should behave like a consumer-facing preview with optional
DEV tools.

Expected controls:

- random dataset;
- reload current dataset;
- mini history of recently opened datasets;
- DEV mode;
- coordinate badges as a DEV sub-option.

When DEV mode is off, the page should focus on the rendered graph. Debug tables
and inspection callouts belong to DEV mode.

Useful dataset cases:

- single active with many events;
- single inactive;
- moved/rekey source;
- moved/rekey target;
- bulk/shared with many origins;
- bulk/shared with few origins;
- ended before target;
- ended after merge;
- pending or incomplete data;
- chains with missing active UI-locale values.

## Resume Sample

The resume sample is intentionally more visual and illustrative.

It should show:

- bottom-to-top chaining;
- top-to-bottom chaining;
- `parts.chain` coordinate flow;
- `parts.start`;
- `parts.sideways`;
- `parts.end`;
- fixed label width modes;
- optional image labels;
- direction-aware start/end labels.

The resume sample may use explicit lengths because it is hand-authored. Those
explicit values should still be readable and grouped so a future user can copy
the pattern without hunting through unrelated logic.

## Bug Lifecycle Sample

The bug lifecycle sample should feel more technical than the resume sample.

It should show:

- a central process trunk;
- side paths for duplicate reports, needs-info branches, reopen flows or
  similar process states;
- step labels that explain why a path changes state;
- end labels for process outcomes;
- DEV mode as a graph-authoring aid.

This sample is a good place to demonstrate how a manually authored graph can be
used for reports, lifecycle diagrams or workflow explanations.

## Sample Discipline

Samples may be manually tuned, but the tuning should stay local to the sample.

A sample should not change package defaults unless the same behavior should
apply to all graphs.

A sample should not introduce new low-level special cases when an existing
`parts`, `strang`, `paths` or `segments` component can be extended instead.

## Future Examples

Good future examples would be:

- release lifecycle;
- review/approval workflow;
- incident timeline;
- migration plan;
- access/permission flow;
- import/export process.

Each new example should demonstrate one new authoring idea instead of becoming
a second data-driven renderer.
