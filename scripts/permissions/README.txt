Local workbench filesystem permissions
====================================

Run from the project root:
  sudo bash scripts/permissions/setup-local-workbench.sh --apply-local
  sudo bash scripts/permissions/verify-local-workbench.sh

The setup is repeatable and does not modify source contents or database records.
It assigns group buergerfrs-dev to the listed runtime, language and editable source
roots, enables setgid on directories and default ACL inheritance. Named ACLs for
gunreip and www-data also work in existing processes without a new login.
Existing ownership, executable flags and unrelated ACL entries remain intact.
Foreign user IDs may appear unmapped inside a sandbox; inspect ACLs on the host
before interpreting numeric identities. No existing ACL identities are removed.
The verification uses disposable files to test editor creation, PHP atomic
replacement and subsequent editing/deletion by the editor in every root.

Do not use on production servers. .env, .git, vendor, node_modules and configuration
are excluded. When translation-workbench.paths grows, review write_roots explicitly.
Never use chmod -R 777. Running Composer/Artisan with sudo is unnecessary and may
create root-owned files. External chmod, archive restoration or moving files from
outside the configured trees can override inherited ACLs; rerun setup after such
operations if needed. ACL inheritance cannot override an explicit restrictive mode.

SourceReplacementWriter and GenerationPublisher share ReplacementFileAccess:
- reject non-writable destination directories and tempnam fallback to /tmp;
- retain existing file permissions plus the parent's shared group read/write mask;
- distinguish temporary creation, writing, chmod, source drift and rename failures.
The original file is only replaced after preparation succeeds.

Local compiled Blade caches are separated by effective user ID at runtime in
AppServiceProvider. This also works with cached configuration. Explicit custom
VIEW_COMPILED_PATH and test/production environments are unchanged. Laravel's
explicit timestamp update requires ownership even with shared write permissions.
Local view:clear/view:cache affect the executing user's compiled cache. The old
common compiled cache is no longer used by the default local configuration.

Production recommendation (not implemented by these scripts):
- deploy account owns immutable application releases; PHP reads source;
- PHP writes only storage and bootstrap/cache (and explicitly provisioned data);
- source replacements are reviewed patches applied during build/deployment;
- language publication either ships in the release or uses a dedicated writable
  language-data directory, with explicit deployment/cache/rollback rules;
- do not enable local source-write permissions on production hosts.
Reference: https://laravel.com/docs/13.x/deployment#directory-permissions
