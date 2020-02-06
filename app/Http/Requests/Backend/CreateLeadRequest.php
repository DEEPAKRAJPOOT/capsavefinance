<?php

namespace App\Http\Requests\Backend;

use App\Http\Requests\Request;

class CreateLeadRequest extends Request
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
            'full_name' => 'required|string|min:3|max:30',
            'comp_name' => 'required',
            'email' => 'required',
            'phone' => 'required|numeric',
            'is_buyer' => 'required|numeric',
            'assigned_sale_mgr' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'full_name.required' => 'Full name is required',
            'full_name.string' => 'Full name should be string',
            'comp_name.required' => 'Business name is required',
            'email.required' => 'Email is required',
            'phone.required' => 'Phone is required',
            'is_buyer.required' => 'User Type is required',
            'assigned_sale_mgr.required' => 'Sales manager is required'
        ];
    }
}
