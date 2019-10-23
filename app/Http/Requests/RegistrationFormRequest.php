<?php

namespace App\Http\Requests;

use Session;
use App\Http\Requests\Request;

class RegistrationFormRequest extends Request
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
      ///dd($this->request);
        return $rules = [
            'f_name' => 'required|max:50|regex:/^[a-z A-Z]/u|',
            'm_name' => 'max:50|regex:/^[a-z A-Z]/u|',
            'l_name' => 'required|max:50|regex:/^[a-z A-Z]/u|',
            'business_name' => 'required',
            'email' => 'required|email|max:50|unique:users',
            'mobile_no' => 'required|digits:10|min:0',
            'password'         => 'required',
            'password_confirm' => 'required|same:password',  
            
            ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return $messages = [
           // 'country_id.required' => trans('error_messages.req_country'),
           // 'country_id.numeric' => trans('error_messages.invalid_country'),
                'f_name.required' => trans('error_messages.req_first_name'),
            'f_name.regex' => trans('error_messages.invalid_first_name'),
            'f_name.max' => trans('error_messages.first_name_max_length'),
            'm_name.regex' => trans('error_messages.invalid_middle_name'),
            'm_name.max' => trans('error_messages.middle_name_max_length'),
            'l_name.required' => trans('error_messages.req_last_name'),
            'l_name.regex' => trans('error_messages.invalid_last_name'),
            'l_name.max' => trans('error_messages.last_name_max_length'),
            ///'dob.required' => trans('error_messages.req_dob_name'),
            'email.required' => trans('error_messages.req_email'),
            'email.max' => trans('error_messages.email_max_length'),
            'email.email' => trans('error_messages.invalid_email'),
            'email.unique' => trans('error_messages.email_already_exists'),
            'mobile_no.required'=>trans('error_messages.req_phone'),

            'mobile_no.min'=>trans('error_messages.phone_minlength'),
            'mobile_no.max'=>trans('error_messages.phone_maxlength'),
            'mobile_no.numeric'=>trans('error_messages.invalid_phone'),
            
            
        ];
    }
}