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
            'type_id' => 'required',
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
            'comp_name.required' => 'Agency name is required',
            'type_id.required' => 'Type is required',
            'comp_email.required' => 'Email is required',
            'comp_email.email' => trans('error_messages.invalid_email'),
            'comp_phone.required' => 'Contact number is required',
            'comp_addr.required' => 'Address is required',            
            'comp_state.required' => 'State is required',
            'comp_city.required' => 'City name is required',
            'comp_zip.required' => 'Pin code is required',
            'comp_phone.numeric'=>trans('error_messages.invalid_phone'), 
            'comp_zip.numeric'=>trans('error_messages.invalid_zip') 
        ];
    }
}