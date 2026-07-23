<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;


class LanguageSeeder extends Seeder
{

    public function run(): void
    {

        $languages = [

            [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'direction' => 'ltr',
                'is_active' => true,
                'is_default' => true,
            ],


            [
                'code' => 'am',
                'name' => 'Amharic',
                'native_name' => 'አማርኛ',
                'direction' => 'ltr',
                'is_active' => true,
                'is_default' => false,
            ],


            [
                'code' => 'om',
                'name' => 'Afaan Oromo',
                'native_name' => 'Afaan Oromoo',
                'direction' => 'ltr',
                'is_active' => true,
                'is_default' => false,
            ],


            [
                'code' => 'ti',
                'name' => 'Tigrinya',
                'native_name' => 'ትግርኛ',
                'direction' => 'ltr',
                'is_active' => true,
                'is_default' => false,
            ],

        ];



        foreach ($languages as $language) {

            Language::updateOrCreate(
                [
                    'code'=>$language['code']
                ],
                $language
            );

        }

    }

}