#!/usr/bin/env bash
set -euo pipefail

echo "Checking patch..."
git apply --check 'storage/audits/translations/diffs/latest.patch'
echo "Applying patch..."
git apply 'storage/audits/translations/diffs/latest.patch'
echo "Done."
