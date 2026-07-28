<?php

namespace App\Services;

use App\Models\Language;
use App\Models\Translation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;


class TranslationService
{


    /*
    |--------------------------------------------------------------------------
    | Get Translation
    |--------------------------------------------------------------------------
    |
    | Example:
    |
    | dashboard.title
    | am
    |
    | returns:
    |
    | ዳሽቦርድ
    |
    |--------------------------------------------------------------------------
    */


    public function translate(
        string $key,
        ?string $languageCode = null
    ): string
    {


        /*
        |--------------------------------------------------------------------------
        | Determine Language
        |--------------------------------------------------------------------------
        */

        $languageCode =
            $languageCode
            ??
            $this->getCurrentUserLanguage();




        /*
        |--------------------------------------------------------------------------
        | Cache Key
        |--------------------------------------------------------------------------
        */


        $cacheKey =
            "translation_{$languageCode}_{$key}";




        return Cache::remember(
            $cacheKey,
            now()->addHours(24),
            function () use (
                $key,
                $languageCode
            ) {


                /*
                |--------------------------------------------------------------------------
                | Find Translation
                |--------------------------------------------------------------------------
                */


                $translation =
                    Translation::whereHas(
                        'translationKey',
                        function($query) use ($key){

                            $query->where(
                                'key',
                                $key
                            );

                        }
                    )
                    ->whereHas(
                        'language',
                        function($query) use ($languageCode){

                            $query->where(
                                'code',
                                $languageCode
                            );

                        }
                    )
                    ->first();




                /*
                |--------------------------------------------------------------------------
                | If Translation Exists
                |--------------------------------------------------------------------------
                */


                if($translation){

                    return $translation->value;

                }




                /*
                |--------------------------------------------------------------------------
                | Fallback To English
                |--------------------------------------------------------------------------
                */


                if($languageCode !== 'en'){


                    $english =
                        Translation::whereHas(
                            'translationKey',
                            function($query) use ($key){

                                $query->where(
                                    'key',
                                    $key
                                );

                            }
                        )
                        ->whereHas(
                            'language',
                            function($query){

                                $query->where(
                                    'code',
                                    'en'
                                );

                            }
                        )
                        ->first();



                    if($english){

                        return $english->value;

                    }

                }





                /*
                |--------------------------------------------------------------------------
                | Last fallback
                |--------------------------------------------------------------------------
                */


                return $key;



            }
        );


    }





    /*
    |--------------------------------------------------------------------------
    | Get User Language
    |--------------------------------------------------------------------------
    */


    private function getCurrentUserLanguage(): string
    {
        // 1. Authenticated user's saved preference
        if (Auth::check() && Auth::user()->language) {
            return Auth::user()->language->code;
        }

        // 2. Locale set by SetLocaleFromHeader middleware (Accept-Language header)
        $appLocale = app()->getLocale();
        if ($appLocale && $appLocale !== 'en') {
            return $appLocale;
        }

        // 3. App default
        return config('app.locale', 'en');
    }



}