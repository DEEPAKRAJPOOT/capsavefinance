<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Helpers;

class CommercialFormRequest extends Request
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
     
        $relation_exchange_company=$this->request->get('relation_exchange_company');
        
        $rules['comm_name']='required';
        $rules['date_of_establish']='required';
        $rules['country_establish_id']='required';
        $rules['comm_reg_no']='required';
        $rules['comm_reg_place']='required';
        $rules['country_activity']='required';
        $rules['syndicate_no']='required';
        $rules['taxation_no']='required';
        $rules['taxation_id']='required';
        $rules['annual_turnover']='required';
        $rules['main_suppliers']='required';
        $rules['main_clients']='required';
        $rules['authorized_signatory']='required';
        $rules['buss_country_id']='required';
        $rules['buss_city_id']='required';
        $rules['buss_region']='required';
        $rules['buss_building']='required';
        $rules['buss_floor']='required';
        $rules['buss_street']='required';
        $rules['buss_postal_code']='required';
        $rules['buss_po_box_no']='required';
        $rules['buss_email']='required';
        $rules['buss_telephone_no']='required';
        $rules['buss_mobile_no']='required';
       // $rules['buss_fax_no']='required';
        
        $rules['is_hold_mail']='required';
        $rules['mailing_address']='required';
        $rules['relation_exchange_company']='required';
        
        if($relation_exchange_company=='1'){
            $rules['concerned_party']='required';
            $rules['details_of_company']='required';
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
        return $messages = [
            'comm_name.required' => trans('common.error_messages.req_this_field'),
            'date_of_establish.required' => trans('common.error_messages.req_this_field'),
            'country_establish_id.required' => trans('common.error_messages.req_this_field'),
            'comm_reg_no.required' => trans('common.error_messages.req_this_field'),
            'comm_reg_place.required' => trans('common.error_messages.req_this_field'),
            'country_activity.required' => trans('common.error_messages.req_this_field'),
            'syndicate_no.required' => trans('common.error_messages.req_this_field'),
            'taxation_no.required' => trans('common.error_messages.req_this_field'),
            'taxation_id.required' => trans('common.error_messages.req_this_field'),
            'annual_turnover.required' => trans('common.error_messages.req_this_field'),
            'main_suppliers.required' => trans('common.error_messages.req_this_field'),
            'main_clients.required' => trans('common.error_messages.req_this_field'),
            'authorized_signatory.required' => trans('common.error_messages.req_this_field'),
            'buss_country_id.required' => trans('common.error_messages.req_this_field'),
            'buss_city_id.required' => trans('common.error_messages.req_this_field'),
            'buss_region.required' => trans('common.error_messages.req_this_field'),
            'buss_building.required' => trans('common.error_messages.req_this_field'),
            'buss_floor.required' => trans('common.error_messages.req_this_field'),
            'buss_street.required' => trans('common.error_messages.req_this_field'),
            'buss_postal_code.required' => trans('common.error_messages.req_this_field'),
            'buss_po_box_no.required' => trans('common.error_messages.req_this_field'),
            'buss_email.required' => trans('common.error_messages.req_this_field'),
            'buss_telephone_no.required' => trans('common.error_messages.req_this_field'),
            'buss_mobile_no.required' => trans('common.error_messages.req_this_field'),
            'buss_fax_no.required' => trans('common.error_messages.req_this_field'),
            'is_hold_mail.required' => trans('common.error_messages.req_this_field'),
            'mailing_address.required' => trans('common.error_messages.req_this_field'),
            'relation_exchange_company.required' => trans('common.error_messages.req_this_field'),
            'concerned_party.required' => trans('common.error_messages.req_this_field'),
            'details_of_company.required' => trans('common.error_messages.req_this_field'),
        ];
    }
}
