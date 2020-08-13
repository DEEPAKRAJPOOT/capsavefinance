<?php

namespace App\Http\Requests\Lms;

use Illuminate\Foundation\Http\FormRequest;
use App\Inv\Repositories\Contracts\LmsInterface as InvLmsRepoInterface;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;

class ApportionmentRequest extends FormRequest
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
           'payment_id' => 'required|numeric|min:1',
        ];
    }

    public function withValidator($validator){
        $formData = $validator->getData();

        $validator->after(function ($validator) use ($formData) {
            $totalselectedAmount = 0;
            $totalRePayAmount = 0;

            $lmsUser = $this->userRepo->lmsGetCustomer($formData['user_id']);
            $payment = $this->lmsRepo->getPaymentDetail($formData['payment_id'], $formData['user_id']);
            if(!$lmsUser){
                $validator->errors()->add("check.required", trans('error_messages.apport_invalid_user_id'));
            }
            if(!$payment){
                $validator->errors()->add("check.required", trans('error_messages.apport_invalid_repayment_id'));
            }else{
                $totalRePayAmount = $payment->amount;
            }

            //if (empty($formData['check']) || !is_array($formData['check'])) {
            //  $validator->errors()->add("check.required", 'Atleast a payment is require to settle');
            //}
            if(isset($formData['check'])){

                foreach ($formData['check'] as $key => $value) {
                    $selectedPayment = $formData['payment'][$key] ?? 0;
                    $transDetail = $this->lmsRepo->getTransDetail(['trans_id' => $key]);
                    $outstandingAmount = $transDetail->getOutstandingAttribute();
                    $realOurstandingAmount = $transDetail->getTempInterestAttribute();
                    if (empty($selectedPayment)) {
                        $validator->errors()->add("payment.{$key}", 'Pay is required against selected transaction');
                    }
                    if ($outstandingAmount < $selectedPayment) {
                        $validator->errors()->add("payment.{$key}", 'Pay filed must be less than and equal to the outsanding amount');
                    }
                    if($transDetail->invoice_disbursed_id && !($transDetail->invoiceDisbursed->invoice->program_offer->payment_frequency == 1 && $transDetail->invoiceDisbursed->invoice->program->interest_borne_by == 1)){
                        if ($realOurstandingAmount < $selectedPayment && in_array($transDetail->trans_type,[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')])) {
                            $validator->errors()->add("payment.{$key}", 'Pay filed must be less than and equal to the Suggested outsanding amount'); 
                        }
                    }
                    $totalselectedAmount += $selectedPayment;
                }
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