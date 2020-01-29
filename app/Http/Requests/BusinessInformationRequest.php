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
            'biz_pan_number' => 'required|string|min:10|max:10',
            'biz_gst_number' => 'required|min:15|max:15',
            'biz_entity_name' => 'required|string|max:100',
            'biz_type_id' => 'required|numeric',
            'incorporation_date' => 'required|date_format:d/m/Y',
            'biz_constitution' => 'required|numeric',
            'entity_type_id' => 'required|numeric',
            'segment' => 'required|numeric',
            'biz_turnover' => ['nullable','regex:/[0-9 \,]/'],
            'loan_amount' => ['required','regex:/[0-9 \,]/'],
            'tenor_days' => 'nullable|numeric|lt:1000',
            'biz_address' => 'required|string|max:100',
            'biz_city' => 'required|string|max:50',
            'biz_state' => 'required|string|max:50',
            'biz_pin' => 'required|numeric|digits:6',
            'share_holding_date' =>'required|date_format:d/m/Y',
            // 'biz_corres_address' => 'required|string|max:100',
            // 'biz_corres_city' => 'required|string|max:50',
            // 'biz_corres_state' => 'required|string|max:50',
            // 'biz_corres_pin' => 'required|numeric|digits:6',
        ];
    }

    public function messages()
    {
        return [
            'biz_pan_number.required' => 'PAN number is required',
            'biz_gst_number.required' => 'GST number is required',
            'biz_entity_name.required' => 'Business name is required',
            'biz_type_id.required' => 'Type of industry is required',
            'incorporation_date.required' => 'Incorporation date is required',
            'biz_constitution.required' => 'Business constitution is required',
            'entity_type_id.required' => 'Entity type is required',
            'segment.required' => 'Segment is required',
            'biz_turnover.regex' => 'Loan amount should be numeric',
            'loan_amount.required' => 'Loan amount is required & should be numeric',
            'tenor_days.numeric' => 'Tenor days should be numeric',
            'tenor_days.lt' => 'Tenor days less than 1000 days',
            'biz_address.required' => 'Business address is required',
            'biz_city.required' => 'Business city is required',
            'biz_state.required' => 'Business state is required',
            'biz_pin.required' => 'Business PIN is required',
            'share_holding_date.required' => 'Share Holding is required',

            // 'biz_corres_address.required' => 'Correspondence address is required',
            // 'biz_corres_city.required' => 'Correspondence city is required',
            // 'biz_corres_state.required' => 'Correspondence state is required',
            // 'biz_corres_pin.required' => 'Correspondence PIN is required',
        ];
    }
}
