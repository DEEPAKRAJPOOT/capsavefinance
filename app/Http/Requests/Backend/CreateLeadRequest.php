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
            'f_name'            => 'required|regex:/^[a-zA-Z ]*$/|min:3|max:50',
            'l_name'            => 'required|regex:/^[a-zA-Z ]*$/|min:3|max:50',
            'comp_name'         => 'required|regex:/^[a-zA-Z0-9. ]*$/|max:50',
            'email'             => 'required|email|max:50|unique:non_anchor_leads,email|unique:users,email',
            'phone'             => 'required|numeric|digits:10',
            'is_buyer'          => 'required|numeric',
            'assigned_sale_mgr' => 'required|numeric',
            'product_type'      => 'required|numeric'
        ];
    }

    public function attributes()
    {
        return [
            'f_name'            => "First name",
            'l_name'            => "Last name",
            'comp_name'         => "Business name",
            'is_buyer'          => "User type",
            'assigned_sale_mgr' => "Sales manager",
            'product_type'      => "Product type",
        ];
    }

    public function messages()
    {
        return [
            'f_name.required'            => 'First name is required',
            'f_name.regex'               => 'First name should only contain letters and space',
            'l_name.required'            => 'Last name is required',
            'l_name.regex'               => 'Last name should only contain letters and space',
            'comp_name.required'         => 'Business name is required',
            'comp_name.regex'            => 'Business name should only contain letters, numbers, space and dot',
            'email.required'             => 'Email is required',
            'phone.required'             => 'Phone is required',
            'is_buyer.required'          => 'User type is required',
            'assigned_sale_mgr.required' => 'Sales manager is required',
            'product_type.required'      => 'Product type is required',
        ];
    }
}
