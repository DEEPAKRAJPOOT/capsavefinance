<?php

namespace App\Http\Requests\Lms;

use Illuminate\Foundation\Http\FormRequest;

class ApportionmentRequest extends FormRequest {

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
        $rules = [
            "check.*" => 'required|string|min:1',
        ];

       
        return $rules;
    }

    public function messages()
    {

        $messages['check.*.min'] = trans('error_messages.required', ['field' => 'Please select at least one']);
        return $messages;
    }

}
