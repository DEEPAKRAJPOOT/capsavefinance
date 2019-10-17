<?php

namespace App\Http\Requests\Master;

use App\Http\Requests\Request;

class StateMasterRequest extends Request
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
            'country_id' => 'required',
            'state_name' => 'required|string',
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
            'country_id.required' => trans('master.country.country_name'),
            'state_name.required' => trans('master.state.state_name'),
            'status.required' => trans('master.country.is_active'),
            
        ];
    }
}
