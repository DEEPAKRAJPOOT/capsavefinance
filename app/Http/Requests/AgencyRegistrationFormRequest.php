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
        return $rules = [
            //'employee' => 'required|min:2|max:50|alpha_dash|alpha',
            'comp_name' => 'required|min:5|max:50|unique:agency'.(($this->request->has('agency_id'))? ',comp_name,'.$this->request->get('agency_id').',agency_id':''),
            'type_id' => 'required',
            'comp_email'  => 'required|email|max:50',
            'comp_phone' => 'required|numeric|digits:10',
            'comp_addr'   => 'required|min:5|max:100',
            'comp_state'   => 'required',
            'comp_city' => 'required|min:3|max:50|alpha_spaces',
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
            'comp_name.unique' => 'Agency name already exist',
            'type_id.required' => 'Type is required',
            'comp_email.required' => 'Email is required',
            'comp_email.email' => trans('error_messages.invalid_email'),
            'comp_phone.required' => 'Contact number is required',
            'comp_phone.numeric'=>trans('error_messages.invalid_phone'), 
            'comp_phone.digits'=>'Contact number must be 10 digits.', 
            'comp_addr.required' => 'Address is required',            
            'comp_addr.min' => 'Please enter correct address',            
            'comp_addr.max' => 'Address is too long',            
            'comp_state.required' => 'State is required',
            'comp_city.required' => 'City name is required',
            'comp_city.min' => 'Please enter correct city name',
            'comp_city.max' => 'City name is too long',
            'comp_city.alpha_spaces' => 'City name may only contain letters and spaces.',
            'comp_zip.required' => 'Pin code is required',
            'comp_zip.numeric'=>trans('error_messages.invalid_zip') 
        ];
    }
}