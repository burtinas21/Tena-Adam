<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use App\Models\Translation;



class TranslationController extends Controller
{


    public function __construct(
        private TranslationService $service
    ){}




    public function translate(Request $request)
    {


        $request->validate([

            'key'=>'required|string',

            'language'=>'nullable|string'

        ]);



        $translation =
            $this->service->translate(

                $request->key,

                $request->language

            );



        return response()->json([

            'key'=>$request->key,

            'translation'=>$translation

        ]);

    }
    public function all(Request $request)
{

    $request->validate([
        'language'=>'required|string'
    ]);


    $language = $request->language;


   $translations = Translation::whereHas(
    'language',
    function($query) use ($language){

        $query->where('code', $language);

    }
)
->with('translationKey')
->get()
->filter(function($item){

    return $item->translationKey != null;

})
->mapWithKeys(function($item){

    return [
        $item->translationKey->key => $item->value
    ];

});


    return response()->json(
        $translations
    );

}



}