<?php

namespace App\Http\Requests\Master;

use Illuminate\Validation\Rule;
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
            'acc_name' => 'required|regex:/^[a-zA-Z ]+$/|max:50',
            'acc_no' => 'required|alpha_num|min:6|max:18',
                        Rule::unique('user_bank_account')->ignore($this->request->get('bank_account_id')),
            'confim_acc_no' => 'required|alpha_num|min:6|max:18|same:acc_no',
            'bank_id' => 'required',
            'ifsc_code' => 'required|alpha_num|max:11',
            'branch_name' => 'required|regex:/^[a-zA-Z0-9 -]+$/|max:30',
            'is_active' => 'required',
            'sponser_bank' => 'required',
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
        $messages['acc_no.unique'] = trans('master_messages.unique_acc_no');
        $messages['sponser_bank.required'] = trans('error_messages.required', ['field' => 'Sponser Branch Code']);
        return $messages;
    }

}
