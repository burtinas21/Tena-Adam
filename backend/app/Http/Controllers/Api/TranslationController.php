<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TranslationService;
use App\Models\Language;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TranslationController extends Controller
{
    public function __construct(
        private TranslationService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | GET /api/languages
    |--------------------------------------------------------------------------
    | Return all active languages for the language switcher
    */
    public function languages()
    {
        $languages = Language::where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'native_name', 'direction', 'is_default']);

        return response()->json($languages);
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/translations?key=xxx&language=am
    |--------------------------------------------------------------------------
    | Translate a single key
    */
    public function translate(Request $request)
    {
        $request->validate([
            'key'      => 'required|string',
            'language' => 'nullable|string|max:10',
        ]);

        $translation = $this->service->translate(
            $request->key,
            $request->language
        );

        return response()->json([
            'key'         => $request->key,
            'translation' => $translation,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/translations/all?language=am
    |--------------------------------------------------------------------------
    | Return ALL translation keys for a language as a flat { key: value } map.
    | Used by the frontend on language switch to hydrate vue-i18n.
    */
    public function all(Request $request)
    {
        $request->validate([
            'language' => 'nullable|string|max:10',
        ]);

        // Use query param → Accept-Language header → fallback 'en'
        $language = $request->language
            ?? $request->header('Accept-Language')
            ?? app()->getLocale()
            ?? 'en';

        $translations = Translation::whereHas(
            'language',
            fn($q) => $q->where('code', $language)
        )
        ->with('translationKey')
        ->get()
        ->filter(fn($item) => $item->translationKey !== null)
        ->mapWithKeys(fn($item) => [
            $item->translationKey->key => $item->value
        ]);

        return response()->json($translations);
    }

    /*
    |--------------------------------------------------------------------------
    | PUT /api/user/language
    |--------------------------------------------------------------------------
    | Save the authenticated user's language preference
    */
    public function saveUserLanguage(Request $request)
    {
        $request->validate([
            'language_code' => 'required|string|max:10|exists:languages,code',
        ]);

        $language = Language::where('code', $request->language_code)->firstOrFail();

        Auth::user()->update(['language_id' => $language->id]);

        return response()->json(['message' => 'Language preference saved.']);
    }
}
