<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ForgotPasswordFormRequest extends Request
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
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|string|min:6|same:password',
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
            'password.required' => trans('error_messages.admin.req_new_password'),
            'password.min' => trans('error_messages.admin.minlen_password'),
            'password_confirmation.required' => trans('error_messages.admin.req_confirm_password'),
            'password_confirmation.min' => trans('error_messages.admin.minlen_confirm_password'),
            'password_confirmation.same' => trans('error_messages.admin.same_confirm_password'),
        ];
    }
}
