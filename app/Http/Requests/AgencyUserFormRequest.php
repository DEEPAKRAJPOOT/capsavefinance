<?php

namespace App\Http\Requests;

use Session;
use App\Http\Requests\Request;

class AgencyUserFormRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $rules = [
            //'employee' => 'required|min:2|max:50|alpha_dash|alpha',
            'f_name'  => 'required|max:50',
            'l_name'  => 'required|max:50',
            'email' => 'required|email|max:50|unique:users'.(($this->request->has('user_id'))? ',email,'.$this->request->get('user_id').',user_id':''),
            'mobile_no' => 'required|numeric|digits:10',
            'agency_id' => 'required'
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
            'f_name.required' => 'First name is required',
            'l_name.required' => 'Last name is required',
            'email.required' => 'Email is required',
            'email.email' => trans('error_messages.invalid_email'),
            'mobile_no.required' => 'Mobile number is required',
            'mobile_no.numeric'=>trans('error_messages.invalid_phone'), 
            'agency_id.required' => 'Agency is required'
        ];
    }
}