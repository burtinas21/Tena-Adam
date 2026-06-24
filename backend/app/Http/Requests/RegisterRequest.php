<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;



class RegisterRequest extends FormRequest
{


public function authorize()
{

return true;

}



public function rules()
{


return [

'first_name'=>'required|string|max:100',

'last_name'=>'required|string|max:100',

'email'=>'required|email|unique:users',

'phone'=>'nullable|string|max:20',

'password'=>'required|min:8|confirmed'


];


}


}