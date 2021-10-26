<?php

namespace App\Http\Requests\Lms;

use Illuminate\Foundation\Http\FormRequest;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;

class AdjustmentRequest extends FormRequest
{

  public function __construct(InvLmsRepoInterface $lms_repo, InvUserRepoInterface $user_repo){
     $this->lmsRepo = $lms_repo;
     $this->userRepo = $user_repo;
  }

	 /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(){
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
     public function rules(){
        return [
           'user_id' => 'required|numeric|min:1',
        ];
    }

    public function withValidator($validator){
        $formData = $validator->getData();
        $validator->after(function ($validator) use ($formData) {
            $lmsUser = $this->userRepo->lmsGetCustomer($formData['user_id']);
            if(!$lmsUser){
                $validator->errors()->add("check.required", trans('error_messages.apport_invalid_user_id'));
            }
            if (empty($formData['check']) || !is_array($formData['check'])) {
              $validator->errors()->add("check.required", 'Atleast a field is require to Adustment');
            }
            foreach ($formData['check'] as $key => $value) {
                $selectedPayment = $formData['refund'][$key] ?? 0;
                $transDetail = $this->lmsRepo->getTransDetail(['trans_id' => $key]);
                $outstandingAmount = $transDetail->settled_outstanding;
                if (empty($selectedPayment)) {
                    $validator->errors()->add("refund.{$key}", 'Field is required against selected transaction');
                }
                if (round($outstandingAmount,2) < round($selectedPayment,2)) {
                    $validator->errors()->add("refund.{$key}", 'Filed must be less than and equal to the remaining amount.');
                }
            }
        });
    }

    public function messages(){
      $messages = [];
      return $messages;
    }
}