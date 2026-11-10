#!/usr/bin/env bash
# Local development only: explicitly grants PHP access to editable source trees.
set -euo pipefail
project_root="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/../.." && pwd -P)"
project_group=buergerfrs-dev
editor_user=gunreip
web_user=www-data
if [[ ${1:-} != --apply-local || $EUID != 0 ]]; then
    echo 'Usage: sudo bash scripts/permissions/setup-local-workbench.sh --apply-local' >&2
    echo 'Local development only. Do not run on a production server.' >&2
    exit 1
fi
command -v setfacl >/dev/null
getent passwd "$editor_user" >/dev/null
getent passwd "$web_user" >/dev/null
getent group "$project_group" >/dev/null || groupadd "$project_group"
usermod -aG "$project_group" "$editor_user"
usermod -aG "$project_group" "$web_user"
# Keep aligned with translation-workbench.paths when adding editable source roots.
write_roots=(storage bootstrap/cache lang app routes resources/views
    packages/gunreip/laravel-translation-workbench/resources/views
    packages/gunreip/laravel-translation-workbench/src)
for relative in "${write_roots[@]}"; do
    target="$project_root/$relative"
    [[ -d "$target" && ! -L "$target" && "$(realpath "$target")" == "$target" ]] || { echo "Unsafe or missing directory: $target" >&2; exit 1; }
    # No symlink traversal. Preserve ownership, executable bits and unrelated ACLs.
    find -P "$target" \( -type d -o -type f \) -exec chgrp "$project_group" {} +
    find -P "$target" -type d -exec chmod g+s {} +
    find -P "$target" \( -type d -o -type f \) -exec setfacl -m "u:$editor_user:rwX,u:$web_user:rwX,g::rwX" {} +
    find -P "$target" -type d -exec setfacl -m "d:u::rwx,d:u:$editor_user:rwx,d:u:$web_user:rwx,d:g::rwx,d:m::rwx,d:o::r-x" {} +
    echo "Configured: $relative"
done
# Named user ACLs take effect immediately, including in already-running PHP workers.
# .git, .env, vendor, node_modules and project configuration are deliberately excluded.
echo 'Local shared access configured. No application data or source content changed.'
