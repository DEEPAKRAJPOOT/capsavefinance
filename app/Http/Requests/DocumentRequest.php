<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class DocumentRequest extends Request
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

    public function rules()
    {
        
        return [
            'bank_docs' => 'required',
            'bank_docs.*' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'bank_docs.required' => 'Input files Required. '
        ];
    }
}
