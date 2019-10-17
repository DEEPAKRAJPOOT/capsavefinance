<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ResidentialFormRequest extends Request
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
        $rules['country_id']        =   'required';
        $rules['city_id']           =   'required|string|regex:/^[A-Za-z0-9\s_-]+$/u|min:3|max:100';
        $rules['region']            =   'required|regex:/^[A-Za-z0-9\s\/.&_-]+$/u|max:60';
        $rules['building_no']       =   'required|regex:/^[A-Za-z0-9\s\/.&_-]+$/u|max:30';
        $rules['floor_no']          =   'required|regex:/^[A-Za-z0-9\s\/_-]+$/u|max:30';
        $rules['street_addr']       =   'required|regex:/^[A-Za-z0-9\s\+_#.*!@&-]+$/u|min:3|max:120';
        $rules['postal_code']       =   'required|regex:/^[0-9\s-]*$/|min:2|max:10';
        $rules['post_box']          =   'required|regex:/^[A-Za-z0-9\s\/-]+$/u|min:6|max:15';
        $rules['addr_email']        =   'required|email';
        $rules['addr_mobile_no']    =   'required|regex:/^(\+\d{1,3}[- ]?)?\d{10}$/|min:10|max:20';
        $rules['addr_fax_no']       =   'required|regex:/^\+?[0-9]+$/|min:6|max:15';
        return $rules;
    }


    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        $messages['country_id.required']        =   trans('common.error_messages.req_this_field');
        
        $messages['city_id.required']           =   trans('common.error_messages.req_this_field');
        $messages['city_id.regex']              =   trans('common.error_messages.alphnum_hyp_uscore_space');
        $messages['city_id.max']                =   trans('common.error_messages.max_60_chars');
        $messages['city_id.min']                =   trans('common.error_messages.least_3_chars');
        
        $messages['region.required']            =   trans('common.error_messages.req_this_field');
        $messages['region.regex']               =   trans('common.error_messages.alphnum_spacial1');
        $messages['region.max']                 =   trans('common.error_messages.max_60_chars');
        $messages['region.min']                 =   trans('common.error_messages.least_3_chars');
        
        $messages['building_no.required']       =   trans('common.error_messages.req_this_field');
        $messages['building_no.regex']          =   trans('common.error_messages.alphnum_hyp_uscore_space_fslace');
        $messages['building_no.max']            =   trans('common.error_messages.max_30_chars');
        
        $messages['floor_no.required']          =   trans('common.error_messages.req_this_field');
        $messages['floor_no.regex']             =   trans('common.error_messages.alphnum_hyp_uscore_space_fslace');
        $messages['floor_no.max']               =   trans('common.error_messages.max_30_chars');

        $messages['street_addr.required']       =   trans('common.error_messages.req_this_field');
        $messages['street_addr.regex']               =   trans('common.error_messages.alphnum_space_spacial_chars');
        $messages['street_addr.max']                 =   trans('common.error_messages.max_120_chars');
        $messages['street_addr.min']                 =   trans('common.error_messages.least_3_chars');

        $messages['postal_code.required']       =   trans('common.error_messages.req_this_field');
        $messages['postal_code.regex']               =   trans('common.error_messages.num_hyp_space');
        $messages['postal_code.max']                 =   trans('common.error_messages.max_10_chars');
        $messages['postal_code.min']                 =   trans('common.error_messages.least_2_chars');


        $messages['addr_email.required']        =   trans('common.error_messages.req_this_field');
        $messages['addr_email.email']           =   trans('common.error_messages.invalid_email');
        
        $messages['addr_mobile_no.regex']       =   trans('common.error_messages.invalid_mobile');
        $messages['addr_mobile_no.required']    =   trans('common.error_messages.req_this_field');
        $messages['addr_mobile_no.max']         =   trans('common.error_messages.max_15_chars');
        $messages['addr_mobile_no.min']         =   trans('common.error_messages.least_10_chars');
      
        $messages['post_box.required']          =   trans('common.error_messages.req_this_field');
        $messages['post_box.regex']             =   trans('common.error_messages.invalid_post_box');
        $messages['post_box.max']               =   trans('common.error_messages.max_20_chars');
        $messages['post_box.min']               =   trans('common.error_messages.least_6_chars');
      
        $messages['addr_fax_no.required']       =   trans('common.error_messages.req_this_field');
        $messages['addr_fax_no.regex']          =   trans('common.error_messages.invalid_fax_no');
        $messages['addr_fax_no.max']            =   trans('common.error_messages.max_15_chars');
        $messages['addr_fax_no.min']            =   trans('common.error_messages.least_6_chars');
        
        return $messages;
    }
}
