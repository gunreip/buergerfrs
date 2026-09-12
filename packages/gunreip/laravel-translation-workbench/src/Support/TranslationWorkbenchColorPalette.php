<?php

namespace Gunreip\TranslationWorkbench\Support;

final class TranslationWorkbenchColorPalette
{
    public static function rgb(?string $color, ?string $default = null): ?string
    {
        return match ($color) {
            'zinc' => '113 113 122',
            'red' => '239 68 68',
            'orange' => '249 115 22',
            'amber' => '245 158 11',
            'yellow' => '234 179 8',
            'lime' => '132 204 22',
            'green' => '44 144 103',
            'emerald' => '16 185 129',
            'teal' => '20 184 166',
            'cyan' => '6 182 212',
            'sky' => '14 165 233',
            'blue' => '59 130 246',
            'indigo' => '99 102 241',
            'violet' => '139 92 246',
            'purple' => '168 85 247',
            'fuchsia' => '217 70 239',
            'pink' => '236 72 153',
            'rose' => '244 63 94',
            default => $default,
        };
    }

    public static function surfaceRgb(?string $color, ?string $default = null): ?string
    {
        return match ($color) {
            'zinc' => '241 241 242',
            'red' => '254 234 234',
            'orange' => '254 239 226',
            'amber' => '254 245 222',
            'yellow' => '254 247 220',
            'lime' => '241 251 225',
            'green' => '228 250 236',
            'emerald' => '225 248 240',
            'teal' => '224 249 245',
            'cyan' => '222 248 252',
            'sky' => '225 245 254',
            'blue' => '231 242 254',
            'indigo' => '236 238 254',
            'violet' => '242 238 254',
            'purple' => '246 237 255',
            'fuchsia' => '252 235 254',
            'pink' => '253 234 244',
            'rose' => '254 234 237',
            default => $default,
        };
    }

    public static function darkSurfaceRgb(?string $color, ?string $default = null): ?string
    {
        return match ($color) {
            'zinc' => '79 79 84',
            'red' => '114 60 61',
            'orange' => '115 73 40',
            'amber' => '115 91 31',
            'yellow' => '114 96 25',
            'lime' => '80 106 37',
            'green' => '44 103 67',
            'emerald' => '35 99 77',
            'teal' => '32 99 93',
            'cyan' => '14 98 113',
            'sky' => '37 90 115',
            'blue' => '53 80 116',
            'indigo' => '66 70 115',
            'violet' => '81 70 116',
            'purple' => '91 67 117',
            'fuchsia' => '107 63 116',
            'pink' => '112 60 89',
            'rose' => '115 60 69',
            default => $default,
        };
    }
}
