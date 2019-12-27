<?php

namespace App\Http\Requests\Lms;

use Illuminate\Foundation\Http\FormRequest;

class ProgramRequest extends FormRequest {

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
            'anchor_id' => 'required',
            'prgm_name' => "required",
            'prgm_type' => 'required',
            'industry_id' => "required",
            'sub_industry_id' => "required",
            'anchor_limit' => "required|Numeric",
            'is_fldg_applicable' => "required",
        ];
    }

    public function messages()
    {
        $messages['anchor_id.required'] = trans('error_messages.required', ['field' => 'Anchor Name']);
        $messages['prgm_name.required'] = trans('error_messages.required', ['field' => 'Program Name']);
        $messages['prgm_type.required'] = trans('error_messages.required', ['field' => 'Program Detail']);
        $messages['industry_id.required'] = trans('error_messages.required', ['field' => 'Industry']);
        $messages['sub_industry_id.required'] = trans('error_messages.required', ['field' => 'Sub Industry']);
        $messages['anchor_limit.required'] = trans('error_messages.required', ['field' => 'Anchor Limit']);
        $messages['anchor_limit.numeric'] =   trans('error_messages.addProduct.anchor_limit_number'); '';
        $messages['is_fldg_applicable.required'] = trans('error_messages.required', ['field' => 'FLDG Applicable']);
        return $messages;
    }

}
