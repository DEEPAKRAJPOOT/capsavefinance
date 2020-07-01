<?php

namespace App\Http\Requests\Lms;

use Illuminate\Foundation\Http\FormRequest;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;

class BankAccountRequest extends FormRequest {

    public function __construct(InvAppRepoInterface $appRepo){
        $this->appRepo = $appRepo;
     }
   
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
        // dd($this->request);
        return [
            'acc_name' => 'required|regex:/^[a-zA-Z ]+$/|max:50',
            'acc_no' => 'required|numeric|digits_between:9,18',
            // 'confim_acc_no' => 'required|numeric|digits_between:9,18|same:acc_no',
            'bank_id' => 'required',
            'ifsc_code' => 'required|alpha_num|max:11',
            'branch_name' => 'required|regex:/^[a-zA-Z0-9 -]+$/|max:30',
            'is_active' => 'required',
        ];
    }

    public function withValidator($validator){
        $formData = $validator->getData();
        
        
        $validator->after(function ($validator) use ($formData) {
            $acc_no = $formData['acc_no'];
            $ifsc_code = $formData['ifsc_code'];
            $bank_account_id = NULL;
            if(!empty($formData['bank_account_id'])){
                $bank_account_id = \Crypt::decrypt($formData['bank_account_id']);
            }
            
            $status = $this->appRepo->getBankAccByCompany(['acc_no' => $acc_no, 'ifsc_code' => $ifsc_code]);

            // dd($bank_account_id,$status);

            if(!empty($status) && ((!empty($bank_account_id) && $status->bank_account_id != $bank_account_id) || $bank_account_id == NULL)){
                $validator->errors()->add("acc_no", 'This account number is already exists with entered IFSC Code.');
            }
 
            // if($status){
            //     $validator->errors()->add("acc_no", 'This account number is already exists with entered IFSC Code.');
            // }
            
        });
        
    }

    public function messages()
    {
        $messages['acc_name.required'] = trans('error_messages.required', ['field' => 'Account Holder Name']);
        $messages['acc_no.required'] = trans('error_messages.required', ['field' => 'Account Number']);
        $messages['bank_id.required'] = trans('error_messages.required', ['field' => 'Bank Name']);
        $messages['ifsc_code.required'] = trans('error_messages.required', ['field' => 'IFSC Code']);
        $messages['branch_name.required'] = trans('error_messages.required', ['field' => 'Branch Name ']);
        $messages['is_active.required'] = trans('error_messages.required', ['field' => 'Status']);
        return $messages;
    }

}
