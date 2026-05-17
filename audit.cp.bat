#!/usr/bin/env bash

set -euo pipefail

UPLOAD_ROOT="/tmp/buergerfrs-audit-upload"

rm -rf "$UPLOAD_ROOT"

mkdir -p "$UPLOAD_ROOT/project-build"
mkdir -p "$UPLOAD_ROOT/translations/code"
mkdir -p "$UPLOAD_ROOT/translations/lang"
mkdir -p "$UPLOAD_ROOT/translations/compare"

copy_if_exists() {
    local source="$1"
    local target="$2"

    if [ -f "$source" ]; then
        cp "$source" "$target"
    else
        echo "WARN: skipped missing file: $source"
    fi
}

copy_preview_dir() {
    local source_dir="$1"
    local target_dir="$2"

    if [ -d "$source_dir" ]; then
        find "$source_dir" \
            -maxdepth 1 \
            -type f \
            -name '*.preview.json' \
            -exec cp {} "$target_dir/" \;
    else
        echo "WARN: skipped missing directory: $source_dir"
    fi
}

copy_if_exists "app/Console/Commands/ProjectBuild.php" \
    "$UPLOAD_ROOT/project-build/Console-current-ProjectBuild.php"

copy_if_exists "audit.cp.bat" \
    "$UPLOAD_ROOT/project-build/current-audit.cp.bat"

copy_if_exists "app/Console/Commands/TranslationsAuditCode.php" \
    "$UPLOAD_ROOT/translations/code/Console-current-TranslationsAuditCode.php"

copy_if_exists "app/Console/Commands/TranslationsAuditLang.php" \
    "$UPLOAD_ROOT/translations/lang/Console-current-TranslationsAuditLang.php"

copy_if_exists "app/Console/Commands/TranslationsAuditCompare.php" \
    "$UPLOAD_ROOT/translations/compare/Console-current-TranslationsAuditCompare.php"

copy_preview_dir "storage/audits/translations/code" \
    "$UPLOAD_ROOT/translations/code"

copy_preview_dir "storage/audits/translations/lang" \
    "$UPLOAD_ROOT/translations/lang"

copy_preview_dir "storage/audits/translations/compare" \
    "$UPLOAD_ROOT/translations/compare"

copy_if_exists "app/Console/Commands/SyncTranslationAudits.php" \
    "$UPLOAD_ROOT/translations/compare/Console-current-SyncTranslationAudits.php"

echo
echo "Audit upload files prepared in:"
echo "$UPLOAD_ROOT"

echo
find "$UPLOAD_ROOT" -type f | sort
