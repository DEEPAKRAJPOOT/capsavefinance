<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class PdNotesRequest extends Request
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
            'type' => 'required',
            'title' => 'required',
            'comments' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'type.required' => 'Type is required',
            'title.required' => 'Title is required',
            'comments.required' => 'Comment is required',
        ];
    }
}
