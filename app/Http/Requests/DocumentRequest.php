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
            'doc_file' => 'required|checkmime|max:1',
            'doc_file.*' => 'required|checkmime|max:1'
        ];
    }

    public function messages()
    {
        return [
            'doc_file.required' => 'Input file Required. ',
            'doc_file.checkmime' => 'Invalid format.'
        ];
    }
}
