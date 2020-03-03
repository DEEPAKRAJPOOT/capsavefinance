<?php

namespace App\Http\Requests\Backend;

use App\Http\Requests\Request;

class CreateJeConfigRequest extends Request
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
        if(!empty(request()->get('jeConfigId'))) {
            return [
                'variable' => 'required'
            ];
        } else {
            return [
                'trans_type' => 'required',
                'variable' => 'required',
                'journal' => 'required'
            ];
        }
        
    }

    public function messages()
    {
        return [
        ];
    }
}
