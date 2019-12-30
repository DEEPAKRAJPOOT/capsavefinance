<?php

namespace App\Http\Requests;

use Session;
use App\Http\Requests\Request;

class AgencyRegistrationFormRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
      ///dd($this->request);
        return $rules = [
            //'employee' => 'required|min:2|max:50|alpha_dash|alpha',
            'comp_name' => 'required|min:5|max:50|unique:agency',
            'comp_email'  => 'required|email|max:50',
            'comp_phone' => 'required|numeric|digits:10',
            'comp_addr'   => 'required|min:5|max:50',
            'comp_state'   => 'required',
            'comp_city' => 'required|min:3|max:50|alpha',
            'comp_zip' => 'required|numeric|digits:6', 
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
            'employee.required' => trans('error_messages.req_first_name'),
            'employee.alpha_dash' => trans('error_messages.invalid_first_name'),
            'employee.max' => trans('error_messages.first_name_max_length'),
            'employee.string' => trans('error_messages.first_name_allow_string'),
            'employee.required' => trans('error_messages.req_last_name'),            
            'business_name.required' => trans('error_messages.buis_business'),
            'email.required' => trans('error_messages.req_email'),
            'email.max' => trans('error_messages.email_max_length'),
            'email.email' => trans('error_messages.invalid_email'),
            'email.unique' => trans('error_messages.email_already_exists'),
            'mobile_no.required'=>trans('error_messages.req_phone'),

            'comp_phone.numeric'=>trans('error_messages.invalid_phone'), 
            'comp_zip.numeric'=>trans('error_messages.invalid_zip') 
        ];
    }
}