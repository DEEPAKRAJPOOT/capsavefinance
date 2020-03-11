<?php

namespace App\Http\Requests\Backend;

use App\Http\Requests\Request;

class CreateJiConfigRequest extends Request
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
            'label' => 'required',
            'account' => 'required',
            'is_partner' => 'required',
            'value_type' => 'required',
            'config_value' => 'required'
        ];
    }

    public function messages()
    {
        return [
        ];
    }
}
