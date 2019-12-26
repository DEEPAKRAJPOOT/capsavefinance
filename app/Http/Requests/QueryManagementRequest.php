<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class QueryManagementRequest extends Request
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
            'assignRoleId' => 'required',
            'qms_cmnt' => 'required',
            'doc_file' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'assignRoleId.required' => 'Please select role.',
            'qms_cmnt.required' => 'Please enter query.',
            'doc_file.required' => 'Please select file',
        ];
    }
}
