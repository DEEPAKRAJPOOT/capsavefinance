<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;

class ApportionmentRequest extends FormRequest
{

  public function __construct(InvLmsRepoInterface $lms_repo){
     $this->lmsRepo = $lms_repo;
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
           'payment_id' => 'required|numeric|min:1',
        ];
    }

    public function withValidator($validator){
        $formData = $validator->getData();
        $validator->after(function ($validator) use ($formData) {
            if (empty($formData['check']) || !is_array($formData['check'])) {
              $validator->errors()->add("check.required", 'Atleast a payment is require to settle');
          }
          $totalselectedAmount = 0;
          $totalRePayAmount = 100000;
          foreach ($formData['check'] as $key => $value) {
              $selectedPayment = $formData['payment'][$key] ?? 0;
              $transDetail = $this->lmsRepo->getTransDetail(['trans_id' => $key]);
              $balancePaymentAmt = $this->lmsRepo->getUnsettledPaymentAmt($paymentId);
              $outstandingAmount = $transDetail->getOutstandingAttribute();
              if (empty($selectedPayment)) {
                $validator->errors()->add("payment.{$key}", 'Pay is required against selected transaction');
              }
              if ($outstandingAmount < $selectedPayment) {
                $validator->errors()->add("payment.{$key}", 'Pay filed must be less than and equal to the outsanding amount');
              }
              $totalselectedAmount += $selectedPayment;
          }
          if ($totalselectedAmount > $totalRePayAmount) {
                $validator->errors()->add("totalRepayAmount", 'Sum of pay must be less than: '. $totalRePayAmount);
          }
        });
    }

    public function messages(){
      $messages = [];
      return $messages;
    }
}