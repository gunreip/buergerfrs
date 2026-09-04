# TW Graph Data Driven Cases

This document lists the dataset constellations that the data-driven renderer
should cover. It is a test and design checklist, not a replacement for runtime
classification code.

## Purpose

The data-driven graph should make lifecycle meaning visible:

- where a key starts;
- whether it is active or ended;
- whether it received origins through merge/shared-key behavior;
- whether records ended after merge;
- whether a key moved into or came from another key;
- whether high-volume events were compacted.

Each case should be rendered by general rules, not by timeline-chain-specific
patches.

## Single Active

A key exists and is still active.

Expected graph:

- `strang.trunk`;
- no merge, branch or rekey unless the data actually requires it;
- active state visible near the end;
- high-volume events compacted when necessary.

Important checks:

- no unnecessary empty trunk stems;
- trunk-only start labels sit at the start when possible;
- compacted events stay chronologically ordered with normal events.

Known stress shape:

- single active with many events.

## Single Inactive

A key existed and is no longer active.

Expected graph:

- `strang.trunk`;
- end state visible;
- no branch/rekey unless the dataset proves a continuation or divergence.

Important checks:

- inactive must not look like the record vanished without explanation;
- obsolete/source state should be visible when it is part of the conclusion.

## Shared / Bulk With Few Origins

Several origins entered the current key/shared state, but the number is small
enough to render directly.

Expected graph:

- `strang.trunk`;
- `strang.merge` plus merge extensions as needed;
- no aggregate section if every origin can be rendered directly.

Important checks:

- no fake placeholder origins;
- merge labels show concrete finding/key/origin data;
- left/right distribution remains readable.

## Shared / Bulk With Many Origins

Many origins entered the current key/shared state.

Expected graph:

- direct head merge entries;
- aggregate merge section for the compacted middle;
- direct tail merge entries;
- counts and sample entries visible.

Important checks:

- aggregate count must match real hidden records;
- no `finding ID ?` labels;
- aggregate entries must still preserve enough sample information;
- vertical staggering should be baseline layout, not collision compensation.

## Ended After Merge

An origin entered a shared/current key and later ended or no longer contributes
to the target state.

Expected graph:

- visible merge relation;
- branch representation for ended-after-merge records;
- branch end label explaining the ended group;
- record-specific labels with finding id, origin key and timestamp.

Important checks:

- branch rows must not be silently dropped;
- branch step labels may explain shared reasons such as `source inactive`;
- branch end labels should describe the group, not duplicate one record label.

## Ended Before Target

A record ended before it could reach the final target/current key state.

Expected graph:

- separate visible handling from ended-after-merge;
- enough label context to distinguish `obsolete`, `inactive` and target state;
- end label that describes the group.

Important checks:

- do not mix ended-before-target into ended-after-merge counts;
- keep the distinction visible because it changes lifecycle meaning.

## Rekey Source

The current key came from another key.

Expected graph:

- `strang.rekey-source`;
- source key id and source translation key;
- target/current key id;
- first and last relevant timestamps where available.

Important checks:

- source-side label collisions may need mirroring;
- bridge extension may not solve trunk-side label collisions;
- applied decisions must show in DEV output.

## Rekey Target

The current key continues into another key.

Expected graph:

- `strang.rekey-target`;
- target key id and target translation key;
- clear visual indication that the current graph ends here but continues
  elsewhere.

Important checks:

- trunk end should still describe the current key ending;
- rekey target should be inspectable as a transition, not inline the full next
  graph;
- trunk-end label collisions should be compensated generally.

## High-Volume Event Compaction

Many timeline events of similar type would make the trunk unreadable.

Expected graph:

- compacted trunk entries;
- event type;
- count;
- sample event id;
- key/finding ids where available;
- timestamp information.

Important checks:

- compacted entries stay in chronological order;
- event types are not split into a detached block;
- special classifications such as `dead_dev_event` remain visible as summary
  information when they affect interpretation.

## Missing Or Partial Data

Some datasets may lack active UI-locale values, lang values, source details or
expected relation records.

Expected graph:

- render as much as is trustworthy;
- show honest fallback/unknown state in DEV mode;
- avoid inventing missing records.

Important checks:

- no placeholder ids as if they were real;
- fallback routes are traceable;
- labels should make uncertainty visible where it matters.

## Case Checklist

When testing the renderer, include at least one dataset for:

- trunk only;
- trunk only with many events;
- few merge origins;
- many merge origins with aggregation;
- ended after merge;
- ended before target;
- rekey source;
- rekey target;
- missing active UI-locale value;
- compacted/dead development events.
