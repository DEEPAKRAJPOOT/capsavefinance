<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ProdessionalFormRequest extends Request
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
    {   $prof_status=$this->request->get('prof_status');
        $last_monthly_salary=$this->request->get('last_monthly_salary');

        $rules['prof_status']='required';
        
        if($prof_status == '8'){
            $rules['other_prof_status']='required';
            
        }
        
        if(in_array($prof_status,['1','3','4'])){
            $rules['prof_detail']='required';
            $rules['position_title']='required';
        }
        
        if(in_array($prof_status,['1','3','4','6'])){
            $rules['date_employment']='required';
            $rules['last_monthly_salary']='required|regex:/^-?[0-9]+(?:\.[0-9]{1,2})?$/';
        }else if($last_monthly_salary!=''){
            $rules['last_monthly_salary']='regex:/^-?[0-9]+(?:\.[0-9]{1,2})?$/';
        }

        return $rules;
    }


    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        $messages['prof_status.required']   =   trans('common.error_messages.req_this_field');
        $messages['other_prof_status.required']   =   trans('common.error_messages.req_this_field');
       
        $messages['prof_detail.required']   =   trans('common.error_messages.req_this_field');
        $messages['position_title.required']   =   trans('common.error_messages.req_this_field');
        $messages['date_employment.required']   =   trans('common.error_messages.req_this_field');
        $messages['last_monthly_salary.required']   =   trans('common.error_messages.req_this_field');
        $messages['last_monthly_salary.regex']   =   trans('common.error_messages.invalid_amount');
        
        return $messages;
    }
}
