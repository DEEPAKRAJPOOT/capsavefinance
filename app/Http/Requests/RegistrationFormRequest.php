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
            'f_name' => 'required|min:2|max:50|alpha_dash|alpha',
            'l_name' => 'required|min:2|alpha_dash|alpha',
            'business_name' => 'required',
            'email'  => 'required|email|max:50|unique:users',
            'mobile_no' => 'required|digits:10|min:0',
            'password'   => 'required',
            'password_confirm' => 'required|same:password',
            'g-recaptcha-response' => 'required', 
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
            'f_name.alpha_dash' => trans('error_messages.invalid_first_name'),
            'f_name.max' => trans('error_messages.first_name_max_length'),
            'f_name.string' => trans('error_messages.first_name_allow_string'),
            'l_name.required' => trans('error_messages.req_last_name'),
            'l_name.alpha_dash' => trans('error_messages.invalid_last_name'),
            'l_name.max' => trans('error_messages.last_name_max_length'),
            'business_name.required' => trans('error_messages.buis_business'),
            'email.required' => trans('error_messages.req_email'),
            'email.max' => trans('error_messages.email_max_length'),
            'email.email' => trans('error_messages.invalid_email'),
            'email.unique' => trans('error_messages.email_already_exists'),
            'mobile_no.required'=>trans('error_messages.req_phone'),
            'password.required'=>trans('error_messages.req_password'),
            'password_confirm.required'=>trans('error_messages.req_confirm_password'),

            'mobile_no.min'=>trans('error_messages.phone_minlength'),
            'mobile_no.max'=>trans('error_messages.phone_maxlength'),
            'mobile_no.numeric'=>trans('error_messages.invalid_phone'),
            'g-recaptcha-response.required'=>'Recaptcha is required',  
        ];
    }
}