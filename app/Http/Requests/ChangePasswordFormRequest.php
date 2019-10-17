<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ChangePasswordFormRequest extends Request
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
            'old_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6',
            'confirm_password' => 'required|string|min:6|same:new_password',
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
            'old_password.required' => trans('error_messages.admin.req_old_password'),
            'old_password.min' => trans('error_messages.admin.minlen_password'),
            'new_password.required' => trans('error_messages.admin.req_new_password'),
            'new_password.min' => trans('error_messages.admin.minlen_password'),
            'confirm_password.required' => trans('error_messages.admin.req_confirm_password'),
            'confirm_password.min' => trans('error_messages.admin.minlen_confirm_password'),
            'confirm_password.same' => trans('error_messages.admin.same_confirm_password'),
        ];
    }
}
