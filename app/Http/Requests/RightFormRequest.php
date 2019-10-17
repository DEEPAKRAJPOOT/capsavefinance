<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class RightFormRequest extends Request
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
        $draft = $this->request->get('draft');
        
        if ($draft == 1) {
            return $rules = [
                'title' => 'required'            
            ];
        } else {
            return $rules = [
                'title' => 'required',
                'type_id' => 'required|numeric',
//                'number' => 'required',
                'inventor' => 'required',
//                'assignee' => 'required',
                'cluster_id' => 'required|numeric',
                'date' => 'required',
                'description' => 'required',
                //'org_attachment_name.0'=> 'required|mimes:zip',
            ];
        }
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        $draft = $this->request->get('draft');
        if ($draft == 1) {
            return $messages = [
                'title.required' => trans('error_messages.req_right_title')                
            ];
        } else {
            return $messages = [
                'title.required' => trans('error_messages.req_right_title'),
                'type_id.required' => trans('error_messages.req_right_type'),
                'type_id.numeric' => trans('error_messages.req_right_type_numeric'),
//                'number.required' => trans('error_messages.req_right_number'),
                'inventor.required' => trans('error_messages.req_right_inventor'),                
//                'assignee.required' => trans('error_messages.req_right_assignee'),
                'cluster_id.required' => trans('error_messages.req_right_cluster'),
                'cluster_id.numeric' => trans('error_messages.req_right_cluster_numeric'),
                'date.required' => trans('error_messages.req_right_date'),
                'description.required' => trans('error_messages.req_right_description'),
                //'org_attachment_name.0.mimes'=>'Sorry! only zip files are allowed'
            ];
        }
    }
}