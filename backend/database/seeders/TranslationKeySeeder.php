<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TranslationKey;


class TranslationKeySeeder extends Seeder
{

    public function run(): void
    {


        $keys = [

            [
                'key'=>'dashboard.title',
                'module'=>'dashboard',
                'description'=>'Dashboard page title'
            ],


            [
                'key'=>'button.save',
                'module'=>'common',
                'description'=>'Save button'
            ],


            [
                'key'=>'button.cancel',
                'module'=>'common',
                'description'=>'Cancel button'
            ],


            [
                'key'=>'login',
                'module'=>'auth',
                'description'=>'Login text'
            ],


            [
                'key'=>'logout',
                'module'=>'auth',
                'description'=>'Logout text'
            ],


            [
                'key'=>'patient',
                'module'=>'patient',
                'description'=>'Patient label'
            ],


            [
                'key'=>'doctor',
                'module'=>'doctor',
                'description'=>'Doctor label'
            ],


            [
                'key'=>'appointment',
                'module'=>'appointment',
                'description'=>'Appointment label'
            ],


            [
                'key'=>'telehealth',
                'module'=>'telehealth',
                'description'=>'Telehealth label'
            ],

        ];



        foreach($keys as $key)
        {

            TranslationKey::updateOrCreate(

                [
                    'key'=>$key['key']
                ],

                $key

            );

        }


    }

}