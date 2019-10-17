<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class FamilyFormRequest extends Request
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
    {   $is_professional_status = $this->request->get('is_professional_status');
        $spouse_profession = $this->request->get('spouse_profession');
        $spouse_employer = $this->request->get('spouse_employer');
        $child_name = $this->request->get('child_name');
        
        $rules['spouse_f_name']='required|string|regex:/^[A-Za-z0-9_-]+$/u|min:3|max:60';
        $rules['spouse_m_name']='required|string|regex:/^[A-Za-z0-9_-]+$/u|min:3|max:60';
        $rules['is_professional_status']='required';
        
        if($is_professional_status=='1' || $is_professional_status=='3'){
            
            $rules['spouse_profession']='required|string|regex:/^[A-Za-z0-9\s_-]+$/u|min:3|max:100';
        }else if($spouse_profession!=null ||$spouse_profession!=''){
            $rules['spouse_profession'] =   'regex:/^[A-Za-z0-9\s_-]+$/u|min:3|max:60';
        }
        
        if($is_professional_status=='1'){
            
            $rules['spouse_employer']   =   'required|string|regex:/^[A-Za-z0-9\s_-]+$/u|min:3|max:100';
        }else if($spouse_employer!=null ||$spouse_employer!=''){
            $rules['spouse_employer']   =   'regex:/^[A-Za-z0-9\s_-]+$/u|min:3|max:60';
        }
        
        if($child_name!=null && is_array($child_name) && count($child_name) && $child_name[0]!=null){
            $rules['child_name.*']   =   'regex:/^[A-Za-z0-9\s_-]+$/u|min:3|max:60';
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
        $messages['spouse_f_name.required'] = trans('common.error_messages.req_this_field');
        $messages['spouse_f_name.regex']=trans('common.error_messages.alphnum_hyp_uscore');
        $messages['spouse_f_name.max'] = trans('common.error_messages.max_60_chars');
        $messages['spouse_f_name.min'] = trans('common.error_messages.least_3_chars');
        
        $messages['spouse_m_name.required'] = trans('common.error_messages.req_this_field');
        $messages['spouse_m_name.regex']=trans('common.error_messages.alphnum_hyp_uscore');
        $messages['spouse_m_name.max'] = trans('common.error_messages.max_60_chars');
        $messages['spouse_m_name.min'] = trans('common.error_messages.least_3_chars');
        
        $messages['is_professional_status.required'] = trans('common.error_messages.req_this_field');
        $messages['spouse_profession.required'] = trans('common.error_messages.req_this_field');
        $messages['spouse_profession.regex']=trans('common.error_messages.alphnum_hyp_uscore_space');
        $messages['spouse_profession.max'] = trans('common.error_messages.max_60_chars');
        $messages['spouse_profession.min'] = trans('common.error_messages.least_3_chars');
        $messages['spouse_employer.required'] = trans('common.error_messages.req_this_field');
        $messages['spouse_employer.regex']=trans('common.error_messages.alphnum_hyp_uscore_space');
        $messages['spouse_employer.max'] = trans('common.error_messages.max_60_chars');
        $messages['spouse_employer.min'] = trans('common.error_messages.least_3_chars');
        
        $messages['child_name.*.regex']=trans('common.error_messages.alphnum_hyp_uscore_space');
        $messages['child_name.*.max'] = trans('common.error_messages.max_60_chars');
        $messages['child_name.*.min'] = trans('common.error_messages.least_3_chars');
        
        return $messages ;
    }
}
