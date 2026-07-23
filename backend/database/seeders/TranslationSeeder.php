<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Models\Translation;
use App\Models\TranslationKey;



class TranslationSeeder extends Seeder
{

    public function run(): void
    {


        $translations = [

            /*
            |--------------------------------------------------------------------------
            | Dashboard
            |--------------------------------------------------------------------------
            */

            'dashboard.title'=>[

                'en'=>'Dashboard',

                'am'=>'ዳሽቦርድ',

                'om'=>'Daashboordii',

                'ti'=>'ዳሽቦርድ',

            ],



            /*
            |--------------------------------------------------------------------------
            | Common Buttons
            |--------------------------------------------------------------------------
            */


            'button.save'=>[

                'en'=>'Save',

                'am'=>'አስቀምጥ',

                'om'=>'Kuusii',

                'ti'=>'ኣቐምጥ',

            ],



            'button.cancel'=>[

                'en'=>'Cancel',

                'am'=>'ሰርዝ',

                'om'=>'Haqi',

                'ti'=>'ሰርዝ',

            ],




            /*
            |--------------------------------------------------------------------------
            | Authentication
            |--------------------------------------------------------------------------
            */


            'login'=>[

                'en'=>'Login',

                'am'=>'ግባ',

                'om'=>'Seeni',

                'ti'=>'እቶ',

            ],



            'logout'=>[

                'en'=>'Logout',

                'am'=>'ውጣ',

                'om'=>'Bai',

                'ti'=>'ውጻ',

            ],




            /*
            |--------------------------------------------------------------------------
            | Healthcare
            |--------------------------------------------------------------------------
            */


            'patient'=>[

                'en'=>'Patient',

                'am'=>'ታካሚ',

                'om'=>'Dhukkubsataa',

                'ti'=>'ሕሙም',

            ],



            'doctor'=>[

                'en'=>'Doctor',

                'am'=>'ሐኪም',

                'om'=>'Doktara',

                'ti'=>'ሓኪም',

            ],



            'appointment'=>[

                'en'=>'Appointment',

                'am'=>'ቀጠሮ',

                'om'=>'Beellama',

                'ti'=>'ቆጸራ',

            ],



            'telehealth'=>[

                'en'=>'Telehealth',

                'am'=>'የርቀት ህክምና',

                'om'=>'Telehealth',

                'ti'=>'ርሑቕ ሕክምና',

            ],


        ];





        foreach($translations as $key=>$languages)
        {


            $translationKey =
                TranslationKey::where(
                    'key',
                    $key
                )
                ->first();



            foreach($languages as $code=>$value)
            {


                $language =
                    Language::where(
                        'code',
                        $code
                    )
                    ->first();



                if($language)
                {

                    Translation::updateOrCreate(

                        [
                            'translation_key_id'=>$translationKey->id,

                            'language_id'=>$language->id,
                        ],


                        [
                            'value'=>$value
                        ]

                    );

                }


            }


        }


    }

}