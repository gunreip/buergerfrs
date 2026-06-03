# Flag Audit 2026-06-01

## Scope
- DB code inventory from:
  - translation_values.locale
  - translation_languages.locale
  - locales.code
  - country_names.locale
  - language_names.locale
- Icon inventory from:
  - vendor/outhebox/blade-flags/resources/svg-circle/circle-country-*.svg
  - vendor/outhebox/blade-flags/resources/svg-flat/flat-country-*.svg
  - vendor/outhebox/blade-flags/resources/svg-circle/circle-language-*.svg

## Summary
- Distinct DB codes: 682
- Type distribution:
  - ll: 142
  - ll-CC: 451
  - ll-###: 6
  - ll-special: 24
  - other: 59
- Icon inventory:
  - circle-country: 406
  - flat-country: 271
  - circle-language: 276
- Resolution quality (current runtime strategy):
  - Resolved: 667
  - Needs review: 15
  - Match by candidate order:
    - position 1: 593
    - position 2: 50
    - position 3: 24

## Special Codes Observed
- ll-### in DB:
  - ar-001
  - en-001
  - en-150
  - eo-001
  - es-419
  - ia-001
- en-100 was not found in DB.

## Needs Review (Unresolved)
- ce
- ff
- ff-Adlm
- ff-Latn
- fy
- gv
- ii
- ks
- ks-Arab
- ks-Deva
- oc
- or
- os
- sh
- wo

## Notes
- `en-001` currently resolves to `circle-country-gb` via `language_numeric_region_overrides`.
- `en-150` currently resolves to `circle-country-european_union` via macroregion override.
- `es-419` currently resolves to `circle-country-es` via `language_numeric_region_overrides`.
- `eu` alias handling is available (`eu -> european_union`) where country-token based matching is used.
- There is a `circle-country-european_union.svg`, but no `flat-country-european_union.svg` in this installed package version.

## Decision Draft For Unresolved Codes
- Goal: keep locale displays stable, avoid misleading country flags, and minimize manual per-code mapping.
- Proposed default for unresolved `ll`: prefer `circle-language-ll` if available; otherwise initials fallback.
- Proposed default for unresolved script variants (`ll-Script`): resolve to `circle-language-ll`; do not map script tags to country flags.
- Proposed default for historic/legacy code aliases (example: `sh`): map to modern language target only if product/domain agrees; otherwise initials fallback.

### Recommended Actions By Code Group
- Script variants:
  - `ff-Adlm`, `ff-Latn`, `ks-Arab`, `ks-Deva`
  - Policy: normalize to base language (`ff`, `ks`) and resolve as language flag.
- Base languages without direct icon token:
  - `ce`, `ff`, `fy`, `gv`, `ii`, `ks`, `oc`, `or`, `os`, `wo`
  - Policy: keep as language code; if no language icon exists, show initials fallback.
- Legacy code:
  - `sh`
  - Policy option A: map to `sr` for backwards compatibility.
  - Policy option B: keep `sh` as-is and show initials fallback.

### Suggested Next Step
- Add an explicit `unresolved_code_overrides` block to `config/buergerfrs-flags.php` once product decisions are confirmed.

## Output Files
- Full machine report: docs/reports/flag-audit-2026-06-01.json
- Generator script: scripts/flag_audit.php
