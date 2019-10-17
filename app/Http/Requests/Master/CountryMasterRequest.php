<?php

namespace App\Http\Requests\Master;

use App\Http\Requests\Request;

class CountryMasterRequest extends Request
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
            'country_name' => 'required|string',
            'country_code' => 'required|string|max:3',
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
            'country_name.required' => trans('master.country.country_name'),
            'country_code.required' => trans('master.country.country_code'),
            'status.required' => trans('master.country.is_active'),
            
        ];
    }
}
