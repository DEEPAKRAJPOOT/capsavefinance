<?php

namespace App\Http\Requests\Master;

use Session;
use Illuminate\Validation\Rule;
use App\Http\Requests\Request;

class CompanyRegRequest extends Request
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
     * Get the validation rules that apply to the request.^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$
     *
     * @return array
     */
    public function rules()
    {
//      dd($this->request);
        return $rules = [
            'cmp_name' => 'required',
            'cmp_add' => 'required',
            Rule::unique('mst_company')->ignore($this->request->get('comp_addr_id')),
            'cmp_email' => 'required|email',
            'cmp_mobile' => 'required|numeric',
            'gst_no' => 'required|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
            'pan_no'  => 'required|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
            'cin_no' => 'required|regex:/^[U,L]{1}[0-9]{5}[A-Z]{2}[0-9]{4}[P,C,L,T,N,G,O,S]{3}[0-9]{6}$/',
            'is_active' => 'required',
            'is_reg' => 'required',
            'utility_code' => 'required',
            'member_id' => 'required',
            'member_branch_code' => 'required',
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
            'cmp_name.required' => trans('master_messages.required'),
            'cmp_add.required' => trans('master_messages.required'),
            'cmp_add.unique' => trans('master_messages.unique_address'),
            'cmp_email.required' => trans('master_messages.required'),
            'cmp_mobile.required' => trans('master_messages.required'),
            'gst_no.required' => trans('master_messages.required'),
            'gst_no.regex' => trans('master_messages.gstno'),
            'pan_no.required' => trans('master_messages.required'),
            'pan_no.regex' => trans('master_messages.panno'),
            'cin_no.required' => trans('master_messages.required'),
            'cin_no.regex' => trans('master_messages.cinno'),
            'is_active.required' => trans('master_messages.required'),
            'is_reg.required' => trans('master_messages.required'),
            'utility_code.required' => trans('master_messages.required'),
            'member_id.required' => trans('master_messages.required'),
            'member_branch_code.required' => trans('master_messages.required'),
        ];
    }
}