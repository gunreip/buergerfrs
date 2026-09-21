# Archived IF composition

Archived on 2026-09-18. These seven Blade components preserve the former `if-else-endif` rendering chain. They are not current public authoring components and must not be used by active samples or production views.

The archive remains renderable through `ui.tw-graph.strang._old.*` solely for regression coverage and future comparison. Internal component calls point into this directory. No forwarding aliases remain at the old public locations.

Author-provided IDs, historical default IDs, `sourceType` metadata, anchor names and geometry were preserved. They describe the historical implementation and are not component lookup paths.

Regression coverage: `tests/Unit/TwGraph/ArchivedIfViewTest.php`. Existing tests were moved, not deleted or skipped. `clear:project --tw-graph-tests` discovers this file automatically.

See `docs/tw-graph/old-if-review.md` in this package for the capability comparison and reasons for archiving. No deletion is scheduled by this change.
