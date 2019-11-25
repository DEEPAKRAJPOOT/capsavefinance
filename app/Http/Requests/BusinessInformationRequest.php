<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class BusinessInformationRequest extends Request
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
            'biz_gst_number' => 'required|min:15|max:15',
            'biz_pan_number' => 'required|string|min:10|max:10',
            'biz_entity_name' => 'required|string|max:100',
            'biz_type_id' => 'required|numeric',
            'segment' => 'required|numeric',
            'loan_amount' => 'required|numeric',
            'incorporation_date' => 'required',
            'biz_constitution' => 'required|numeric',
            // 'biz_email' => 'required|email',
            // 'biz_mobile' => 'required|numeric|digits:10',
            'entity_type_id' => 'required|numeric',
            // 'biz_cin' => 'required|string|max:50',
            'biz_address' => 'required|string|max:100',
            'biz_city' => 'required|string|max:50',
            'biz_state' => 'required|string|max:50',
            'biz_pin' => 'required|numeric|digits:6',
            // 'biz_corres_address' => 'required|string|max:100',
            // 'biz_corres_city' => 'required|string|max:50',
            // 'biz_corres_state' => 'required|string|max:50',
            // 'biz_corres_pin' => 'required|numeric|digits:6',
        ];
    }

    public function messages()
    {
        return [
            'biz_gst_number.required' => 'GST number is required',
            'biz_pan_number.required' => 'PAN number is required',
            'biz_entity_name.required' => 'Business name is required',
            'biz_type_id.required' => 'Type of industry is required',
            'segment.required' => 'Segment is required',
            'loan_amount.required' => 'Loan amount is required',
            'incorporation_date.required' => 'Incorporation date is required',
            'biz_constitution.required' => 'Business constitution is required',
            //'biz_email.required' => 'Business email is required',
            //'biz_mobile.required' => 'Business mobile is required',
            'entity_type_id.required' => 'Entity type is required',
            //'biz_cin' => 'Business CIN is required',
            'biz_address.required' => 'Business address is required',
            'biz_city.required' => 'Business city is required',
            'biz_state.required' => 'Business state is required',
            'biz_pin.required' => 'Business PIN is required',
            // 'biz_corres_address.required' => 'Correspondence address is required',
            // 'biz_corres_city.required' => 'Correspondence city is required',
            // 'biz_corres_state.required' => 'Correspondence state is required',
            // 'biz_corres_pin.required' => 'Correspondence PIN is required',
        ];
    }
}
