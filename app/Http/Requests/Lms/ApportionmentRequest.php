<?php

namespace App\Http\Requests\Lms;

use App\Inv\Repositories\Models\BizInvoice;
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
        //echo "<pre>";print_r($formData);
        //die;
        $validator->after(function ($validator) use ($formData) {
            $totalselectedAmount = 0;
            $totalRePayAmount = 0;

            $lmsUser = $this->userRepo->lmsGetCustomer($formData['user_id']);
            $payment = $this->lmsRepo->getPaymentDetail($formData['payment_id'], $formData['user_id']);
            $unInvCnt = BizInvoice::where('supplier_id', $formData['user_id'])->whereHas('invoice_disbursed')->where('is_repayment','0')->count();
            $showSuggestion = isset($formData['paySug']) && $formData['paySug'] ? true : false; 
        
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
            if(isset($formData['settlement']) && $formData['settlement'] == 'TDS'){
                if(isset($formData['check'])){
                    foreach ($formData['check'] as $key => $value) {
                        $selectedPayment = $formData['payment'][$key] ?? 0;
                        $transDetail = $this->lmsRepo->getTransDetail(['trans_id' => $key]);
                        $outstandingAmount = $transDetail->getTDSAmountAttribute();
                        if (empty($selectedPayment)) {
                            $validator->errors()->add("payment.{$key}", 'Pay is required against selected transaction');
                        }
                        if (round($selectedPayment,2) < 0) {
                            $validator->errors()->add("payment.{$key}", 'Pay filed must be greater than 0');
                        }
                        if (round($outstandingAmount,2) < round($selectedPayment,2)) {
                            $validator->errors()->add("payment.{$key}", 'Pay filed must be less than and equal to the outsanding amount');
                        }
                        $totalselectedAmount += $selectedPayment;
                    }
                }

                if (round($totalselectedAmount,2) > round($totalRePayAmount,2)) {
                        $validator->errors()->add("totalRepayAmount", 'Sum of pay must be less than: '. $totalRePayAmount);
                }
            } else {
                if(isset($formData['check'])){
                    $formData['check'] = $formData['check'];
                    $formData['payment'] = $formData['payment'];
                    if(isset($formData['type']) && $formData['type'] == 'uploadCsv'){
                        $formData['check'] = unserialize(base64_decode($formData['check']));
                        $formData['payment'] = unserialize(base64_decode($formData['payment']));
                    }
                    foreach ($formData['check'] as $key => $value) {
                        $selectedPayment = $formData['payment'][$key] ?? 0;
                        $selectedPayment = str_replace(",","",$selectedPayment);
                        $transDetail = $this->lmsRepo->getTransDetail(['trans_id' => $key]);
                        $outstandingAmount = $transDetail->outstanding;
                        $realOurstandingAmount = $transDetail->getTempInterestAttribute();
                        if (empty($selectedPayment)) {
                            $validator->errors()->add("payment.{$key}", 'Pay is required against selected transaction');
                        }
                        if (!is_numeric($selectedPayment)){
                            $validator->errors()->add("payment.{$key}", 'Payment value must be numeric.');
                        }
                        if (round($selectedPayment,2) < 0) {
                            $validator->errors()->add("payment.{$key}", 'Pay filed must be greater than 0');
                        }
                        if (is_numeric($selectedPayment)){
                            if ($outstandingAmount < $selectedPayment) {
                                $validator->errors()->add("payment.{$key}", 'Pay filed must be less than and equal to the outsanding amount');
                            }
                        }
                        if (is_numeric($selectedPayment)){
                            if($showSuggestion && $transDetail->invoice_disbursed_id && !($transDetail->invoiceDisbursed->invoice->program_offer->payment_frequency == 1 && $transDetail->invoiceDisbursed->invoice->program->interest_borne_by == 1 && $transDetail->trans_type == config('lms.TRANS_TYPE.INTEREST'))){
                                if ($realOurstandingAmount < $selectedPayment && in_array($transDetail->trans_type,[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')])) {
                                    $validator->errors()->add("payment.{$key}", 'Pay filed must be less than and equal to the Suggested outsanding amount'); 
                                }
                            }
                        }  
                        if (is_numeric($selectedPayment)){
                            $totalselectedAmount += $selectedPayment;
                        }
                    }
                }

                if ( round($totalselectedAmount,2) > round($totalRePayAmount,2)) {
                        $validator->errors()->add("totalRepayAmount", 'Sum of pay must be less than: '. $totalRePayAmount);
                }
            }
        });
    }

    public function messages(){
      $messages = [];
      return $messages;
    }
}