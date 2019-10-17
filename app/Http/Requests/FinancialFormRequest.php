<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class FinancialFormRequest extends Request
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
    {   $prof_status=$this->request->get('source_funds');
        $wealth_source=$this->request->get('wealth_source');
        $rules['source_funds'] ='required';
        $rules['jurisdiction_funds'] ='required';
        $rules['annual_income'] ='required';
        if($prof_status=='7'){
           $rules['other_source'] ='required'; 
        }
        
        $rules['estimated_wealth'] ='required';
        
        $rules['wealth_source'] ='required';
        if($wealth_source=='4'){
            $rules['other_wealth_source'] ='required';
        }
        
        $rules['tin_code'] ='required';
        $rules['is_abandoned'] ='required';
        
        $rules['date_of_abandonment'] ='required';
        $rules['abandonment_reason'] ='required';
        $rules['justification'] ='required';
        $rules['tin_country_name'] ='required';
        $rules['tin_number'] ='required';
        
        return $rules ;
    }


    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return $messages = [
            'source_funds.required' => trans('common.error_messages.req_this_field'),
            'jurisdiction_funds.required' => trans('common.error_messages.req_this_field'),
            'annual_income.required' => trans('common.error_messages.req_this_field'),
            'other_source.required' => trans('common.error_messages.req_this_field'),
            'estimated_wealth.required' => trans('common.error_messages.req_this_field'),
            'wealth_source.required' => trans('common.error_messages.req_this_field'),
            'other_wealth_source.required' => trans('common.error_messages.req_this_field'),
            'tin_code.required' => trans('common.error_messages.req_this_field'),
            'is_abandoned.required' => trans('common.error_messages.req_this_field'),
            'date_of_abandonment.required' => trans('common.error_messages.req_this_field'),
            'abandonment_reason.required' => trans('common.error_messages.req_this_field'),
            'justification.required' => trans('common.error_messages.req_this_field'),
            'tin_country_name.required' => trans('common.error_messages.req_this_field'),
            'tin_number.required' => trans('common.error_messages.req_this_field'),
        ];
    }
}
