<?php

namespace App\Http\Requests;

use Auth;
use App\Http\Requests\Request;
use Session;

class PersonalProfileFormRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $userId = (int) Auth::user()->id;
        $guardian_name = $this->request->get('guardian_name');
        $m_name = $this->request->get('m_name');
        //$document_type_id = $this->request->get('document_type_id');
        //$social_media_id = $this->request->get('document_type_id');
       
        

        $rules['title'] = 'required';
        $rules['f_name'] = 'required|string|regex:/^[A-Za-z0-9_-]+$/u|min:3|max:60';
        $rules['l_name'] = 'required|string|regex:/^[A-Za-z0-9_-]+$/u|min:3|max:60';
        if($m_name!=''){
            $rules['m_name'] = 'regex:/^[A-Za-z0-9_-]+$/u|max:60';
        }
        $rules['gender'] = 'required';
        $rules['date_of_birth'] = 'required';
        $rules['father_name']   = 'required|string|regex:/^[A-Za-z0-9\s_-]+$/u|min:3|max:60';
        $rules['mother_f_name'] = 'required|string|regex:/^[A-Za-z0-9_-]+$/u|min:3|max:60';
        $rules['mother_m_name'] = 'required|string|regex:/^[A-Za-z0-9_-]+$/u|min:3|max:60';
        
        $rules['birth_country_id'] = 'required';
        $rules['birth_city_id'] = 'required|string|regex:/^[A-Za-z0-9\s_-]+$/u|min:3|max:100';
        
        $rules['reg_no'] = 'required|string|alpha_num|max:30';
        $rules['reg_place'] = 'required|regex:/^[A-Za-z0-9\s_-]+$/u|max:100';
        $rules['f_nationality_id'] = 'required';
        $rules['residence_status'] = 'required';
        $rules['family_status']    = 'required';
        $rules['educational_level'] = 'required';
        $rules['is_residency_card'] = 'required';
        if ($guardian_name != '' && $guardian_name != null) {
            $rules['legal_maturity_date'] = 'required';
        }

        $rules['document_type_id.*'] = 'required';
        $rules['social_media_id.*'] = 'required';
        $rules['document_number.*'] = 'alpha_num|max:20';
        $rules['social_media_link.*'] = 'url|max:120';

        return $rules;
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages() {
        $messages['title.required'] = trans('common.error_messages.req_this_field');
        
        $messages['f_name.required'] = trans('common.error_messages.req_this_field');
        $messages['f_name.regex']=trans('common.error_messages.alphnum_hyp_uscore');
        $messages['f_name.max'] = trans('common.error_messages.max_60_chars');
        $messages['f_name.min'] = trans('common.error_messages.least_3_chars');
        
        $messages['l_name.required'] = trans('error_messages.req_this_field');
        $messages['l_name.regex']=trans('common.error_messages.alphnum_hyp_uscore');  
        $messages['l_name.max'] = trans('common.error_messages.max_60_chars');
        $messages['l_name.min'] = trans('common.error_messages.least_3_chars');
 
        $messages['m_name.regex']=trans('common.error_messages.alphnum_hyp_uscore');
        $messages['m_name.max'] = trans('common.error_messages.max_60_chars');
        $messages['m_name.min'] = trans('common.error_messages.least_3_chars');
        
        $messages['gender.required'] =  trans('common.error_messages.req_this_field');
        $messages['date_of_birth.required'] =  trans('common.error_messages.req_this_field');
        
      
        $messages['father_name.required']   =   trans('common.error_messages.req_this_field');
        $messages['father_name.regex']      =   trans('common.error_messages.alphnum_hyp_uscore_space');  
        $messages['father_name.max']        =   trans('common.error_messages.max_60_chars');
        $messages['father_name.min']        =   trans('common.error_messages.least_3_chars');
        
        $messages['mother_f_name.required'] =   trans('error_messages.req_this_field');
        $messages['mother_f_name.regex']    =   trans('common.error_messages.alphnum_hyp_uscore');
        $messages['mother_f_name.max']      =   trans('common.error_messages.max_60_chars');
        $messages['mother_f_name.min']      =   trans('common.error_messages.least_3_chars');
        
        $messages['mother_m_name.required'] =  trans('error_messages.req_this_field');
        $messages['mother_m_name.regex']    =   trans('common.error_messages.alphnum_hyp_uscore');
        $messages['mother_m_name.max']      =   trans('common.error_messages.max_60_chars');
        $messages['mother_m_name.min']      =   trans('common.error_messages.least_3_chars');
        
        $messages['birth_country_id.required'] =  trans('error_messages.req_this_field');

        $messages['birth_city_id.required'] =  trans('common.error_messages.req_this_field');
        $messages['birth_city_id.regex']    =  trans('common.error_messages.alphnum_hyp_uscore_space');
        $messages['birth_city_id.max']      =  trans('common.error_messages.max_60_chars');
        $messages['birth_city_id.min']      =   trans('common.error_messages.least_3_chars');
        
        $messages['reg_no.required']  =  trans('common.error_messages.req_this_field');
        $messages['reg_no.alpha_num'] =  trans('common.error_messages.alpha_num');
        $messages['reg_no.max']       = trans('common.error_messages.max_60_chars');

        
        $messages['reg_place.required']    =  trans('common.error_messages.req_this_field');
        $messages['reg_place.regex']       =  trans('common.error_messages.alphnum_hyp_uscore_space');
        $messages['reg_place.max']         =  trans('common.error_messages.max_60_chars');
        $messages['reg_place.min']         = trans('common.error_messages.least_3_chars');
        
        $messages['f_nationality_id.required']  =  trans('common.error_messages.req_this_field');
        $messages['residence_status.required']  =  trans('common.error_messages.req_this_field');
        $messages['family_status.required']     =  trans('common.error_messages.req_this_field');
        $messages['educational_level.required'] =  trans('common.error_messages.req_this_field');
        $messages['is_residency_card.required'] =  trans('common.error_messages.req_this_field');

        $messages['legal_maturity_date.required'] =  trans('common.error_messages.req_this_field');


        $messages['document_type_id.*.required'] =   trans('common.error_messages.req_this_field');
        $messages['social_media_id.*.required']  =   trans('common.error_messages.req_this_field');
        
        $messages['social_media_link.*.url']  =   trans('common.error_messages.enter_valid_url');
        $messages['social_media_link.*.max'] = trans('common.error_messages.max_120_chars');
     
        $messages['document_number.*.alpha_num'] = trans('common.error_messages.alpha_num');
        $messages['document_number.*.max'] = trans('common.error_messages.max_20_chars');
        
      
        return $messages;
    }

}
