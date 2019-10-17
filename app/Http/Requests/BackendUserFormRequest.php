<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class BackendUserFormRequest extends Request
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
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|max:10',
            'addr1' => 'required',
            'country_id' => 'required|numeric',
            'zip_code' => 'required|max:6',
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
            'first_name.required' => trans('error_messages.req_first_name'),
            'first_name.string' => trans('error_messages.invalid_first_name'),
            'first_name.max' => trans('error_messages.first_name_max_length'),
            'last_name.required' => trans('error_messages.req_last_name'),
            'last_name.string' => trans('error_messages.invalid_last_name'),
            'last_name.max' => trans('error_messages.last_name_max_length'),
            'phone.min'=>trans('error_messages.phone_minlength'),
            'phone.max'=>trans('error_messages.phone_maxlength'),
            'phone.numeric'=>trans('error_messages.invalid_phone'),
            'country_id.required' => trans('error_messages.req_country'),
            'country_id.numeric' => trans('error_messages.invalid_country'),
            'zip_code.min' => trans('error_messages.zip_code_min_length'),
            'zip_code.max' => trans('error_messages.zip_code_max_length'),
            'zip_code.numeric' => trans('error_messages.zip_code_numeric'),
            'addr1.required' => 'Address 1 field is required.'
        ];
    }
}