<?php

namespace App\Http\Requests\Backend;

use App\Http\Requests\Request;

class CreateAccountRequest extends Request
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
        return [
            'account_code' => 'required',
            'account_name' => 'required',
            'is_active' => 'required'
        ];
    }

    public function messages()
    {
        return [
        ];
    }
}
