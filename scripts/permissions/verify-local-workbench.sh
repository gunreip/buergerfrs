#!/usr/bin/env bash
# Exercise ACL inheritance and atomic replacement as both real users. No app/DB bootstrap.
set -euo pipefail
project_root="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/../.." && pwd -P)"
[[ $EUID == 0 ]] || { echo 'Run with sudo bash scripts/permissions/verify-local-workbench.sh' >&2; exit 1; }
probe=''
cleanup() {
    if [[ -n "$probe" && -d "$probe" ]]; then
        rm -f -- "$probe/source.php"
        rmdir -- "$probe"
    fi
}
trap cleanup EXIT
for relative in storage bootstrap/cache lang app routes resources/views \
    packages/gunreip/laravel-translation-workbench/resources/views \
    packages/gunreip/laravel-translation-workbench/src; do
    probe="$project_root/$relative/.tw-permission-check-$(cat /proc/sys/kernel/random/uuid)"
    runuser -u gunreip -- php -r '
        if (!mkdir($argv[1], 0777) || file_put_contents($argv[1]."/source.php", "editor") !== 6) exit(1);
    ' "$probe"
    runuser -u www-data -- php -r '
        require $argv[1]."/vendor/autoload.php";
        $target=$argv[2]."/source.php";
        if (file_get_contents($target) !== "editor") exit(1);
        $temp=Gunreip\TranslationWorkbench\Generation\ReplacementFileAccess::temporary($target, ".check-");
        try {
            if (file_put_contents($temp,"web") !== 3) exit(1);
            Gunreip\TranslationWorkbench\Generation\ReplacementFileAccess::permissions($temp,$target);
            if (!rename($temp,$target)) exit(1);
        } finally { if (is_file($temp)) unlink($temp); }
    ' "$project_root" "$probe"
    runuser -u gunreip -- php -r '
        $target=$argv[1]."/source.php";
        if (file_get_contents($target) !== "web" || file_put_contents($target,"editor again") !== 12 || !unlink($target) || !rmdir($argv[1])) exit(1);
    ' "$probe"
    probe=''
    echo "PASS: editor → PHP atomic replacement → editor: $relative"
done
