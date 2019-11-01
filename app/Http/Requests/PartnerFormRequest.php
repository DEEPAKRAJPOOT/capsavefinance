<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartnerFormRequest extends FormRequest
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
       
        return $rules = [
            'f_name' => 'required',
            'l_name' => 'required',
            'date_of_birth' => 'required',
            'gender' => 'required',
            'pan_no' => 'required',
            'share_per' => 'required',
            'edu_qualification' => 'required',
            'other_ownership' => 'required',
            'networth' => 'required',
            'address' => 'required',
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
