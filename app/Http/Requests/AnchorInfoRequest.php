<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnchorInfoRequest extends FormRequest
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
           'year' => 'required|array',
           'year.*' => "required|string|min:4|max:4",
           'mt_type' => 'required|array',
           'mt_type.*' => "required|string|min:2|max:3",
           'year_of_association' => "required|numeric|min:1",
           'years' => "required|numeric|min:1",
           'payment_terms' => "required|string|min:1",
           'grp_rating' => "required|string",
           'contact_number' => "required|string|min:10|max:10",
           'security_deposit' => "required|numeric",
        ];
    }

    public function messages()
    {
    	      $messages = [];
        		$messages['year.0.required']  = 'Year field is required';
        		$messages['year.0.string']  = 'Year Field must be string only';
            $messages['year.0.min']  = 'Year Field must be exact 4 chars';
            $messages['year.0.max']  = 'Year Field must be exact 4 chars';
            $messages['year.1.required']  = 'Year field is required';
            $messages['year.1.string']  = 'Year Field must be string only';
            $messages['year.1.min']  = 'Year Field must be exact 4 chars';
            $messages['year.1.max']  = 'Year Field must be exact 4 chars';
            $messages['mt_type.0.required']  = 'MT Type field is required';
            $messages['mt_type.0.string']  = 'MT Type must be string only';
        		$messages['mt_type.0.min']  = 'MT Type must be at least 2 chars';
        		$messages['mt_type.0.max']  = 'MT Type must be at most 3 chars';
            $messages['mt_type.1.required']  = 'MT Type field is required';
            $messages['mt_type.1.string']  = 'MT Type must be string only';
            $messages['mt_type.1.min']  = 'MT Type must be at least 2 chars';
            $messages['mt_type.1.max']  = 'MT Type must be at most 3 chars';

        return $messages;
    }
}