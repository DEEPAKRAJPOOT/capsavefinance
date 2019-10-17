<?php

namespace App\Http\Requests\Company;

use App\Http\Requests\Request;

class ShareholderFormRequest extends Request
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
    //share_parent_id;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {   $arrIds = $this->request->get('share_parent_id');
        //dd($ids);
        $rules=[];
        foreach($arrIds as $id){
            $rules['share_type_'.$id.'.*']='required';
            $rules['company_name_'.$id.'.*']='required';
            $rules['passport_no_'.$id.'.*']='required';
            $rules['share_percentage_'.$id.'.*']='required';
            $rules['share_value_'.$id.'.*']='required';
            
        }
        //dd($rules);
        return $rules;
    }


    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        $arrIds = $this->request->get('share_parent_id');
        //dd($ids);
        $messages=[];
        /*foreach($arrIds as $id){
            $messages['share_type_'.$id.'[].required']='Individual/Company is required.';
            $messages['company_name_'.$id.'[].required']='Individual name/company name is required.';
            $messages['passport_no_'.$id.'[].required']='Passport No./ License No. is required.';
            $messages['share_percentage_'.$id.'[].required']='Sharingholding Percentage is required.';
            $messages['share_value_'.$id.'[].required']='Value in USD is required.';
            
        }*/
        return $messages ;
    }
}
