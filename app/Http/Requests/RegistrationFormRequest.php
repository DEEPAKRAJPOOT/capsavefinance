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
        //dd($this->request);
        return $rules = [
            'country_id' => 'required|numeric',
            'first_name' => 'required|max:50|regex:/^[a-z A-Z]/u|',
            'middle_name' => 'max:50|regex:/^[a-z A-Z]/u|',
            'last_name' => 'required|max:50|regex:/^[a-z A-Z]/u|',
            'dob' => 'required',
            'email' => 'required|email|max:50|unique:users',
            'phone' => 'required|digits:10|min:0',
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
            'country_id.required' => trans('error_messages.req_country'),
            'country_id.numeric' => trans('error_messages.invalid_country'),
            'first_name.required' => trans('error_messages.req_first_name'),
            'first_name.regex' => trans('error_messages.invalid_first_name'),
            'first_name.max' => trans('error_messages.first_name_max_length'),
            
            'middle_name.regex' => trans('error_messages.invalid_middle_name'),
            'middle_name.max' => trans('error_messages.middle_name_max_length'),
            'last_name.required' => trans('error_messages.req_last_name'),
            'last_name.regex' => trans('error_messages.invalid_last_name'),
            'last_name.max' => trans('error_messages.last_name_max_length'),
            'dob.required' => trans('error_messages.req_dob_name'),
            'email.required' => trans('error_messages.req_email'),
            'email.max' => trans('error_messages.email_max_length'),
            'email.email' => trans('error_messages.invalid_email'),
            'email.unique' => trans('error_messages.email_already_exists'),
            'phone.required'=>trans('error_messages.req_phone'),

            'phone.min'=>trans('error_messages.phone_minlength'),
            'phone.max'=>trans('error_messages.phone_maxlength'),
            'phone.numeric'=>trans('error_messages.invalid_phone'),
        ];
    }
}