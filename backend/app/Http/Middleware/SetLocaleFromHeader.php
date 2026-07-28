<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Models\Language;

class SetLocaleFromHeader
{
    /**
     * Read the Accept-Language header sent by the frontend and set the
     * application locale so that TranslationService::translate() and
     * any dynamic data responses can use the correct language.
     */
    public function handle(Request $request, Closure $next)
    {
        $lang = $request->header('Accept-Language');

        if ($lang) {
            // Validate against known active language codes to prevent abuse
            $valid = Language::where('is_active', true)
                ->pluck('code')
                ->toArray();

            if (in_array($lang, $valid)) {
                App::setLocale($lang);
            }
        }

        return $next($request);
    }
}
