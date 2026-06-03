<?php

return [

    /*
    |-----------------------------------------------------------------
    | Flag Audit Reference Paths
    |-----------------------------------------------------------------
    |
    | Paths used by the admin flag reference view.
    |
    */

    'audit_report_path' => 'docs/reports/flag-audit-2026-06-01.json',

    'comments_path' => 'app/reference/flag-reference-comments.json',

    /*
    |-----------------------------------------------------------------
    | Country Aliases
    |-----------------------------------------------------------------
    |
    | Maps short or alternative region tokens to blade-flags country tokens.
    |
    */

    'country_aliases' => [
        'eu' => 'european_union',
        'uk' => 'gb',
    ],

    /*
    |-----------------------------------------------------------------
    | Numeric Macroregion Aliases
    |-----------------------------------------------------------------
    |
    | CLDR-style numeric regions (e.g. 150 = Europe, 419 = Latin America)
    | can be mapped to blade-flags country tokens if desired.
    |
    */

    'macroregion_aliases' => [
        '150' => 'european_union',
        // '001' => 'world', // no world-country icon token in current package
        // '419' => 'latin_america', // no matching country token in current package
    ],

    /*
    |-----------------------------------------------------------------
    | Language + Numeric Region Overrides
    |-----------------------------------------------------------------
    |
    | Fine-grained fallback when ll-### occurs. Per-language mapping wins over
    | macroregion_aliases. Value must be a blade-flags country token.
    |
    */

    'language_numeric_region_overrides' => [
        'en' => [
            '150' => 'european_union',
            '001' => 'gb',
        ],
        'es' => [
            '419' => 'es',
        ],
    ],

];
