<?php

namespace App\Http\Requests\Master;

use App\Http\Requests\Request;

class RightTypeMasterRequest extends Request
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
            'title' => 'required|string',
            'status' => 'required',
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
            'title.required' => trans('master.cluster.title'),
            'status.required' => trans('master.country.is_active'),
            
        ];
    }
}
