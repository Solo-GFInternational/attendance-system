<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Festive themes an admin can switch on for the whole system
 * (Theme page, admin only). A theme only decorates: the tablet header band,
 * a greeting, and accent colours. Action colours (Sign In green, Sign Out red,
 * Returning blue) and the Emergency Roll Call page never change.
 *
 * The CSS for the active theme is written by partials/festive-theme.
 */
class FestiveTheme
{
    public const SETTING = 'festive_theme';

    public const DEFAULT = 'professional';

    /**
     * hero       header band gradient (white text sits on it in both light and dark mode)
     * accent     accent colour on light pages / on dark pages
     * soft       tint of the accent for backgrounds, light / dark
     * pattern    decoration drawn over the band: SVG shapes on a 120 x 120 tile
     */
    private const THEMES = [
        'professional' => [
            'name' => 'Professional',
            'emoji' => '💼',
            'greeting' => null,
            'description' => 'The standard Goodman Fielder look. Use this for everyday.',
            'hero' => 'linear-gradient(180deg, #0f1f3d 0%, #16294f 100%)',
            'accent' => ['#1f5fd6', '#4d8dff'],
            'soft' => ['#eaf1fd', 'rgba(80, 140, 255, .14)'],
            'pattern' => null,
        ],
        'christmas' => [
            'name' => 'Christmas',
            'emoji' => '🎄',
            'greeting' => 'Merry Christmas from Goodman Fielder',
            'description' => 'Evergreen and berry red with falling snowflakes.',
            'hero' => 'linear-gradient(135deg, #0f3d26 0%, #14532d 45%, #7f1d1d 100%)',
            'accent' => ['#b91c1c', '#f87171'],
            'soft' => ['#fdecec', 'rgba(248, 113, 113, .15)'],
            'pattern' => '<g fill="none" stroke="#fff" stroke-width="1.6" stroke-linecap="round" opacity=".22"><path d="M30 18v24M18 30h24M21.5 21.5l17 17M38.5 21.5l-17 17"/><path d="M90 70v20M80 80h20M83 73l14 14M97 73l-14 14"/></g><g fill="#fff" opacity=".25"><circle cx="70" cy="22" r="2"/><circle cx="104" cy="40" r="1.5"/><circle cx="18" cy="96" r="2"/><circle cx="52" cy="104" r="1.5"/></g>',
        ],
        'new_year' => [
            'name' => 'New Year',
            'emoji' => '🎆',
            'greeting' => 'Happy New Year from Goodman Fielder',
            'description' => 'Midnight blue with gold sparkles.',
            'hero' => 'linear-gradient(135deg, #0b0f1f 0%, #1c1f3d 55%, #4a3a0c 100%)',
            'accent' => ['#a16207', '#f2c94c'],
            'soft' => ['#fdf6e0', 'rgba(242, 201, 76, .15)'],
            'pattern' => '<g fill="#f2c94c" opacity=".35"><path d="M28 14l3 11 11 3-11 3-3 11-3-11-11-3 11-3z"/><path d="M92 66l2 7 7 2-7 2-2 7-2-7-7-2 7-2z"/></g><g fill="#fff" opacity=".3"><circle cx="76" cy="24" r="1.6"/><circle cx="104" cy="104" r="1.4"/><circle cx="20" cy="88" r="1.6"/><circle cx="56" cy="62" r="1.2"/></g>',
        ],
        'easter' => [
            'name' => 'Easter',
            'emoji' => '🐣',
            'greeting' => 'Happy Easter from Goodman Fielder',
            'description' => 'Soft purple and spring green with Easter eggs.',
            'hero' => 'linear-gradient(135deg, #3f2470 0%, #6b46c1 55%, #2f855a 100%)',
            'accent' => ['#6b46c1', '#b794f4'],
            'soft' => ['#f1ecfb', 'rgba(183, 148, 244, .15)'],
            'pattern' => '<g fill="none" stroke="#fff" stroke-width="1.6" opacity=".25"><ellipse cx="30" cy="34" rx="10" ry="13"/><path d="M21 33q4.5-4 9 0t9 0"/><ellipse cx="90" cy="86" rx="9" ry="12"/><path d="M82 88q4-3.5 8 0t8 0"/></g><g fill="#fde68a" opacity=".35"><circle cx="78" cy="26" r="2.2"/><circle cx="24" cy="94" r="2"/><circle cx="106" cy="46" r="1.6"/></g>',
        ],
        'diwali' => [
            'name' => 'Diwali',
            'emoji' => '🪔',
            'greeting' => 'Happy Diwali from Goodman Fielder',
            'description' => 'Deep maroon and marigold with glowing diyas.',
            'hero' => 'linear-gradient(135deg, #4a0d22 0%, #8a1c1c 50%, #c2410c 100%)',
            'accent' => ['#c2410c', '#fb923c'],
            'soft' => ['#fff1e6', 'rgba(251, 146, 60, .15)'],
            'pattern' => '<g opacity=".4"><path d="M18 40q12 10 24 0z" fill="#fbbf24"/><path d="M30 37q-4-6 0-13q4 7 0 13z" fill="#fde68a"/><path d="M78 96q12 10 24 0z" fill="#fbbf24"/><path d="M90 93q-4-6 0-13q4 7 0 13z" fill="#fde68a"/></g><g fill="none" stroke="#fde68a" stroke-width="1.2" opacity=".25"><circle cx="88" cy="30" r="9"/><circle cx="88" cy="30" r="4"/><circle cx="28" cy="92" r="7"/></g>',
        ],
        'eid' => [
            'name' => 'Eid',
            'emoji' => '🌙',
            'greeting' => 'Eid Mubarak from Goodman Fielder',
            'description' => 'Emerald green and gold with crescent moons and stars.',
            'hero' => 'linear-gradient(135deg, #052e2b 0%, #0f5c4d 55%, #14532d 100%)',
            'accent' => ['#0f766e', '#2dd4bf'],
            'soft' => ['#e6f6f3', 'rgba(45, 212, 191, .14)'],
            'pattern' => '<g fill="#f2c94c" opacity=".35"><path d="M36 16a14 14 0 1 0 10 24a11 11 0 1 1-10-24z"/><path d="M96 70a10 10 0 1 0 7 17a8 8 0 1 1-7-17z"/><path d="M78 22l1.8 4.2 4.5.4-3.4 3 1 4.4-3.9-2.3-3.9 2.3 1-4.4-3.4-3 4.5-.4z"/><path d="M26 86l1.4 3.2 3.4.3-2.6 2.3.8 3.3-3-1.8-3 1.8.8-3.3-2.6-2.3 3.4-.3z"/></g>',
        ],
        'fiji_day' => [
            'name' => 'Fiji Day',
            'emoji' => '🌺', // not the flag emoji: Windows shows flags as plain letters ("FJ")
            'greeting' => 'Happy Fiji Day from Goodman Fielder',
            'description' => 'Pacific blues and ocean waves for 10 October.',
            'hero' => 'linear-gradient(135deg, #0b2a5b 0%, #0e5a8a 55%, #2f9fd6 100%)',
            'accent' => ['#0e7bb8', '#5cc3f0'],
            'soft' => ['#e6f4fb', 'rgba(92, 195, 240, .15)'],
            'pattern' => '<g fill="none" stroke="#fff" stroke-width="1.6" stroke-linecap="round" opacity=".22"><path d="M0 30q15-10 30 0t30 0 30 0 30 0"/><path d="M0 90q15-10 30 0t30 0 30 0 30 0"/></g><g fill="#fff" opacity=".28"><path d="M60 52l1.6 3.6 3.9.4-2.9 2.6.8 3.8-3.4-2-3.4 2 .8-3.8-2.9-2.6 3.9-.4z"/><circle cx="20" cy="60" r="1.6"/><circle cx="100" cy="60" r="1.6"/></g>',
        ],
    ];

    /** @return array<string, array> every theme, keyed by its id, in display order */
    public static function all(): array
    {
        $themes = [];

        foreach (self::THEMES as $key => $theme) {
            $themes[$key] = self::shape($key, $theme);
        }

        return $themes;
    }

    public static function keys(): array
    {
        return array_keys(self::THEMES);
    }

    /** Read once per request: the header, nav and styles all ask for it. */
    private static ?string $currentKey = null;

    public static function currentKey(): string
    {
        if (self::$currentKey === null) {
            $key = Setting::value(self::SETTING, self::DEFAULT);
            self::$currentKey = isset(self::THEMES[$key]) ? $key : self::DEFAULT;
        }

        return self::$currentKey;
    }

    public static function save(string $key, ?\App\Models\User $by = null): void
    {
        Setting::put(self::SETTING, $key, $by);
        self::$currentKey = null;
    }

    /** For long-running processes and tests: read the setting again next time. */
    public static function forget(): void
    {
        self::$currentKey = null;
    }

    public static function current(): array
    {
        return self::shape(self::currentKey(), self::THEMES[self::currentKey()]);
    }

    private static function shape(string $key, array $theme): array
    {
        return $theme + [
            'key' => $key,
            'isFestive' => $key !== self::DEFAULT,
            // CSS background-image value for the pattern tile, or null.
            'patternUrl' => $theme['pattern']
                ? 'url("data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 120 120">' . $theme['pattern'] . '</svg>') . '")'
                : null,
        ];
    }
}
