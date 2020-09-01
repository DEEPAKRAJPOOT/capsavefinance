<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class CoLenderRequest extends FormRequest {

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
        return [
            'employee' => 'required',
            'comp_name' => 'required',
            // 'email' => 'required|email',
            'phone' => 'required|numeric|digits:10',
            'pan_no' => 'required|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
            'gst' => 'required|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
            'state' => 'required',
            'city' => 'required',
            'comp_addr' => 'required',
            'pin_code' => 'required|numeric|digits:6',
            'is_active' => 'required'
        ];
    }

    public function messages()
    {
        return $messages = [
            'employee.required' => trans('master_messages.required'),
            'comp_name.required' => trans('master_messages.required'),
            // 'email.required' => trans('master_messages.required'),
            'phone.required' => trans('master_messages.required'),
            'gst.required' => trans('master_messages.required'),
            'gst.regex' => trans('master_messages.gstno'),
            'pan_no.required' => trans('master_messages.required'),
            'pan_no.regex' => trans('master_messages.panno'),
            'state.required' => trans('master_messages.required'),
            'city.required' => trans('master_messages.required'),
            'comp_addr.required' => trans('master_messages.required'),
            'pin_code.required' => trans('master_messages.required'),
            'is_active.required' => trans('master_messages.required'),
        ];
    }

}
