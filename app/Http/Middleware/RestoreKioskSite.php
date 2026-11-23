<?php

namespace App\Http\Middleware;

use App\Models\Site;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

/**
 * Keeps a tablet on its site. The site is remembered in an encrypted
 * long-lived cookie, so it survives the session timeout and restarts.
 * A site that was disabled is dropped.
 */
class RestoreKioskSite
{
    public const COOKIE = 'kiosk_site';

    /** About 400 days (the browser maximum); renewed on every request. */
    public const COOKIE_MINUTES = 400 * 24 * 60;

    public function handle(Request $request, Closure $next)
    {
        $siteId = $request->session()->get('attendance_site_id')
            ?? $request->cookie(self::COOKIE);

        if ($siteId) {
            $site = Site::where('active', true)->find((int) $siteId);

            $site ? self::remember($request, $site) : self::forget($request);
        }

        return $next($request);
    }

    public static function remember(Request $request, Site $site): void
    {
        $request->session()->put('attendance_site_id', $site->id);
        Cookie::queue(self::COOKIE, (string) $site->id, self::COOKIE_MINUTES);
    }

    public static function forget(Request $request): void
    {
        $request->session()->forget(['attendance_site_id', 'face_verified']);
        Cookie::queue(Cookie::forget(self::COOKIE));
    }
}
