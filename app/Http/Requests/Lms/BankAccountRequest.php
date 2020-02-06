<?php

namespace App\Http\Requests\Lms;

use Illuminate\Foundation\Http\FormRequest;

class BankAccountRequest extends FormRequest {

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
            'acc_name' => 'required',
            'acc_no' => 'required',
            'bank_id' => 'required',
            'ifsc_code' => 'required',
            'branch_name' => 'required',
            'is_active' => 'required',
        ];
    }

    public function messages()
    {
        $messages['acc_name.required'] = trans('error_messages.required', ['field' => 'Account Holder Name']);
        $messages['acc_no.required'] = trans('error_messages.required', ['field' => 'Account Number']);
        $messages['bank_id.required'] = trans('error_messages.required', ['field' => 'Bank Name']);
        $messages['ifsc_code.required'] = trans('error_messages.required', ['field' => 'IFSC Code']);
        $messages['branch_name.required'] = trans('error_messages.required', ['field' => 'Branch Name ']);
        $messages['is_active.required'] = trans('error_messages.required', ['field' => 'Status']);
        return $messages;
    }

}
