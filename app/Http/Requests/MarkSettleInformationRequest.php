<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;

class MarkSettleInformationRequest extends FormRequest
{

  public function __construct(InvLmsRepoInterface $lms_repo){
     $this->lmsRepo = $lms_repo;
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
     public function rules(){
        return [
           'user_id' => 'required|numeric|min:10|max:200',
           'payment_id' => 'required|numeric|min:200|max:500',
        ];
    }

    public function withValidator($validator){
          $formData = $validator->getData();
          if (empty($formData['check']) || !is_array($formData['check'])) {
            $validator->errors()->add("check.required", 'Atleast a payment is require to settle');
          }
          $totalselectedAmount = 0;
          $totalRePayAmount = -1;
          foreach ($formData['check'] as $key => $value) {
              $selectedPayment = $formData['payment'][$key] ?? 0;
              $transDetail = $this->lmsRepo->getTransDetail(['trans_id' => $key]);
              $outstandingAmount = $transDetail->getOutstandingAttribute();
              if (empty($selectedPayment)) {
                $validator->errors()->add("payment.{$key}", 'Payment is required against trans id: ' . $key);
              }
              if ($outstandingAmount < $selectedPayment) {
                $validator->errors()->add("payment.{$key}", 'Payment must be less than: ' . $outstandingAmount);
              }
              $totalselectedAmount += $selectedPayment;
          }
          if ($totalselectedAmount > $totalRePayAmount) {
                $validator->errors()->add("totalRepayAmount", 'totalRepayAmount must be less than: '. $totalRePayAmount);
          }
          dd($validator->errors());
    }
}