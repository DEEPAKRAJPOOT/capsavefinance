<?php

namespace App\Http\Requests\Lms;

use Illuminate\Foundation\Http\FormRequest;

class SubProgramRequest extends FormRequest {

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
            'product_name' => 'required',
            'anchor_sub_limit' => "required",
            'interest_borne_by' => "required",
            'anchor_limit' => "required",
            'margin' => "required",
            'status' => "required",
            'min_loan_size' => 'required',
            'max_loan_size' => 'required',
            'interest_rate' => 'required',
            'overdue_interest_rate' => 'required',
            'is_adhoc_facility' => 'required',
            'disburse_method' => 'required',
        ];
    }

    public function messages()
    {
        return [];
//        $messages['anchor_id.required'] = trans('error_messages.required', ['field' => 'Program name']);
//        $messages['prgm_name.required'] = trans('error_messages.required', ['field' => 'Anchor sub limit Name']);
//        $messages['prgm_type.required'] = trans('error_messages.required', ['field' => 'Interest Linkage']);
//        $messages['industry_id.required'] = trans('error_messages.required', ['field' => 'Margin']);
//        $messages['sub_industry_id.required'] = trans('error_messages.required', ['field' => 'Sub Industry']);
//        $messages['anchor_limit.required'] = trans('error_messages.required', ['field' => 'Anchor Limit']);
//        $messages['anchor_limit.numeric'] = trans('error_messages.addProduct.anchor_limit_number');
//        '';
//        $messages['is_fldg_applicable.required'] = trans('error_messages.required', ['field' => 'FLDG Applicable']);
//        return $messages;
    }

}
