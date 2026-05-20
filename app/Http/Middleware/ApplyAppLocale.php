<?php

// app/Http/Middleware/ApplyAppLocale.php

namespace App\Http\Middleware;

use App\Settings\AppGeneralSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyAppLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $settings = app(AppGeneralSettings::class);

            $availableLocales = array_values(array_filter(
                $settings->availableLocales,
                static fn(mixed $locale): bool => is_string($locale) && $locale !== ''
            ));

            $locale = in_array($settings->locale, $availableLocales, true)
                ? $settings->locale
                : config('app.locale', 'de');
        } catch (\Throwable) {
            $locale = config('app.locale', 'de');
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
