<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ResetPasswordFormRequest extends Request
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
            'email' => 'required|string|email',
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
            'email.required' => trans('error_messages.req_email'),
            'email.email' => trans('error_messages.invalid_email'),
        ];
    }
}
