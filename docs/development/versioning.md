# Component versions

`versions.json` is the authoritative, committed source for three independent
Major.Minor.Patch versions: `buergerfrs`, `translation-workbench`, and `tw-graph`.
The Workbench starts at its existing 0.7.0; the application and TW-Graph start
at 0.1.0. These numbers are not inferred from the number of previous commits.

## Updating a version

```bash
php artisan app:version
php artisan app:version tw-graph --bump=patch
php artisan app:version translation-workbench --bump=minor
php artisan app:version buergerfrs --set=1.0.0
```

Use patch for completed fixes, minor for completed features, and major for
intentional incompatible changes. Minor resets patch to zero; major resets
minor and patch. `--set` assigns an explicit version. No version changes happen
automatically on commit, push or rebuild. Commit `versions.json` together with
the corresponding implementation. Changes to TW-Graph do not automatically
increment the enclosing Workbench or application versions.

## Development display

The footer and `app:version` show the managed versions, live Git description,
and, when available for the current commit, the successful local Watch count.
`-dirty` uses Git's meaning: modified tracked files, not untracked files.

`clear:project --watch` increments the count only after successful rebuilds
triggered by file changes. Startup, idle polling and failed builds do not count.
The state contains the full commit ID and a UTC timestamp. If HEAD changes
during a rebuild, that run does not count. A new commit hides the previous count
and its next successful rebuild starts at 1. The count persists across watcher
restarts for the same commit.

The local file `storage/app/development/watch-version.json` is ignored by Git
and outside all watched source directories. Its updates cannot trigger a watch
loop. Clearing Laravel caches does not delete it. This count is a development
aid, not a reproducible release identifier.

## Releases

After committing the version and code, use:

```bash
php artisan app:release tw-graph
git push origin tw-graph/v0.1.0
```

Replace the example tag with the command's actual output. The release command
requires a clean working tree, verifies the version in HEAD, and creates an
annotated local tag such as `tw-graph/v0.1.0`. Existing tags are never overwritten.
It does not commit or push. Tags for the other components use `buergerfrs/v…`
and `translation-workbench/v…`.

## Deployments without Git

Include `versions.json` in the deployment. Before removing Git metadata, run
`php artisan app:write-app-version` to capture the Git description in
`public/version.txt`. The footer uses this snapshot only when `.git` is absent.
An unreadable or broken existing checkout shows `n/a` rather than stale metadata.
The standalone Workbench package retains its own fallback when the host does
not supply a component manifest.
