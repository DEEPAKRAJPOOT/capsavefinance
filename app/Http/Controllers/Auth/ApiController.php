<?php 

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use App\Inv\Repositories\Models\FinanceModel;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Payment;
use App\Inv\Repositories\Models\Lms\Refund\RefundReq;
use App\Libraries\Bsa_lib;
use App\Libraries\Perfios_lib;
use App\Inv\Repositories\Models\Master\TallyEntry;
use App\Helpers\Helper;
use App\Inv\Repositories\Models\Master\EmailTemplate;
use Storage;
use Session;
use Carbon\Carbon;
use DB;
use App\Inv\Repositories\Models\TallyFactVoucher;
use App\Inv\Repositories\Models\TransFactVoucher;
use App\Helpers\Helpers;
use Illuminate\Support\Facades\Mail;
use App\Inv\Repositories\Models\Lms\Apportionment;

/**
 * 
 */
class ApiController
{
	//protected $secret_key = "Rentalpha__vkzARY";
  protected $secret_key = "0702f2c9c1414b70efc1e69f2ff31af0";
  protected $download_xlsx = true;
  protected $voucherNo = 0;
  protected $voucherFormat = null;
  protected $paymentFactVoucherSeq = 0;
  protected $journalFactVoucherSeq = 0;
  protected $paymentFactStartVoucherNumber = null;
  protected $journalFactStartVoucherNumber = null;
  protected $paymentFactEndVoucherNumber = null;
  protected $journalFactEndVoucherNumber = null;
  protected $selectedTxnData = [];
  protected $selectedPaymentData = [];
  protected $journalTransFactVoucher = [];
  protected $disbursalTransFactVoucher = [];
  protected $receiptTransFactVoucher = [];
  protected $refundTransFactVoucher = [];
	function __construct(){
		
	}


  public function tally_recover() {
      $email_content = EmailTemplate::getEmailTemplate("SUPPLY_CHAIN_INVOICE_OVERDUE_ALERT");

      $activeMailemail = explode(',', env('CRONINVOICE_SEND_MAIL_TO'));
      $activeMailbcc = array_filter(explode(',', env('CRONINVOICE_SEND_MAIL_BCC_TO')));
      $activeMailcc = array_filter(explode(',', env('CRONINVOICE_SEND_MAIL_CC_TO')));
      $envMails = ['email' => $activeMailemail, 'cc' => $activeMailcc, 'bcc' => $activeMailbcc];

      $dynamicEmail = $data["email"] ?? 'anyId';
      $dynamiccc = array_filter(explode(',', $email_content->cc));
      $dynamicbcc = array_filter(explode(',', $email_content->bcc));
      $dynamicMails = ['email' => $dynamicEmail, 'cc' => $dynamiccc, 'bcc' => $dynamicbcc];
      
      $mailIds = ['envMails' => $envMails, 'dynamicMails' => $dynamicMails, 'sendigFrom' => (env('SEND_MAIL_ACTIVE') == 1) ? 'ENV' : 'Dynamically'];
      dd($mailIds);

    $disbursedRec= TallyEntry::where(['trans_type' => 'Payment Disbursed'])->whereNotNull('transactions_id')->get();
    $count = 0;
    foreach ($disbursedRec as $key => $value) {
       $disbursedRow = $value;
       $interestRow = $value->getDisbursedInterest;
       $disbursalDate = $disbursedRow->voucher_date;
       $where = ['trans_date' => $disbursalDate, 'trans_type' => config('lms.TRANS_TYPE.INTEREST'), 'entry_type' => 0];
       $interestBooked = $disbursedRow->getTransaction->getInterestForDisbursal($where);
       if (isset($interestRow) && isset($interestBooked->trans_id)) {
         $count++;
         $interestTransId = $interestBooked->trans_id ?? NULL;
         $interestRow->update(['transactions_id' => $interestTransId]);
       }
    }
    $response = array(
      'status' => 'success',
      'message' => $count . ' Record(s) updated successfully.',
    );
   return $response;
  }

  private function createJournalData($journalData, $batch_no) {
    $journalPayments = [];
    foreach ($journalData as $jrnls) {
      if(in_array($jrnls->trans_type, [config('lms.TRANS_TYPE.REVERSE'), config('lms.TRANS_TYPE.ADJUSTMENT')])){
        $reversalPayment = $this->createReversalData($jrnls, $batch_no);
        $journalPayments = array_merge($journalPayments, $reversalPayment);
      }else{
        $accountDetails = $jrnls->userRelation->companyBankDetails ?? NULL;
        if (empty($accountDetails)) {
          continue;
        }
        $is_charge = false;
        if ($jrnls->transType->chrg_master_id != 0) {
          $is_charge = true;
        }
        $payment_id = '';
        if(!empty(($jrnls->payment_id))){  
        $payment_id = " & Payment Id ".$jrnls->payment_id;
        }
        $user_id = Helper::formatIdWithPrefix($jrnls->user_id, 'CUSTID');
        $userName = $jrnls->user->biz_name;
        $trans_type_name = $jrnls->getTransNameAttribute();
        $invoice_no = $jrnls->userinvoicetrans->getUserInvoice->invoice_no ?? NULL;
        $invoice_date = $jrnls->userinvoicetrans->getUserInvoice->invoice_date ?? NULL;
        if (empty($invoice_no)) {
            $invoice_no = $jrnls->invoiceDisbursed->invoice->invoice_no ?? NULL;
            $invoice_date = $jrnls->invoiceDisbursed->invoice->invoice_date ?? NULL;
        }
        $invoice_date = !empty($invoice_date) ? $invoice_date .' 23:59:59' : NULL;
        $inst_no = $jrnls->refundReq->tran_no ?? NULL;
        $inst_date = $jrnls->refundReq->actual_refund_date ?? NULL;
        if (!empty($jrnls->parent_trans_id)) {
          $parentRecord  = $jrnls->getParentTxn();
          if (empty($invoice_no)) {
              $invoice_no = $parentRecord->userinvoicetrans->getUserInvoice->invoice_no ?? NULL;
              $invoice_date = $parentRecord->userinvoicetrans->getUserInvoice->invoice_date ?? NULL;
              if (empty($invoice_no)) {
                $invoice_no = $parentRecord->invoiceDisbursed->invoice->invoice_no ?? NULL;
                $invoice_date = $parentRecord->invoiceDisbursed->invoice->invoice_date ?? NULL;
              }
          }
          $invoice_date = !empty($invoice_date) ? $invoice_date .' 23:59:59' : NULL;
          if (empty($inst_no)) {
                $inst_no = $parentRecord->refundReq->tran_no ?? NULL;
                $inst_date = $parentRecord->refundReq->actual_refund_date ?? NULL;
          }
        }
        $getTransfactVoucherNumber = Helper::getTransfactVoucherNumber($jrnls->trans_id);
        if($getTransfactVoucherNumber){
          $factvoucherNumber = $getTransfactVoucherNumber->fact_voucher_no;
        }else{
          $this->journalFactVoucherSeq++;  
          $factvoucherNumber = 'SJV'.$this->voucherFormat.sprintf('%07d',$this->journalFactVoucherSeq);
          $transfactvoucherData = [
            'trans_id'=>$jrnls->trans_id,
            'fact_voucher_no'=>$factvoucherNumber,
            'created_at'=>\Carbon\Carbon::now()->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s')
          ];
          
          $this->journalTransFactVoucher[] = $transfactvoucherData;
        }
        $this->journalFactEndVoucherNumber = $factvoucherNumber;
        $this->voucherNo = $this->voucherNo + 1;
        $entry_type = $jrnls->entry_type == 1 ? 'Credit' : 'Debit';
        $this->selectedTxnData[] = $jrnls->trans_id;
        $JournalRow = [
            'batch_no' =>  $batch_no,
            'transactions_id' =>  $jrnls->trans_id,
            'fact_voucher_number'=>$factvoucherNumber,
            'voucher_no' => $this->voucherNo,
            'voucher_type' => 'Journal',
            'voucher_date' => $jrnls->trans_date,
            'transaction_date'=>$jrnls->created_at,
            'is_debit_credit' =>  $entry_type,
            'trans_type' =>  $trans_type_name,
            'invoice_no' =>   $invoice_no,
            'invoice_date' =>  $invoice_date,
            'ledger_name' =>  $userName,
            'amount' =>  $jrnls->amount,
            'ref_no' =>  $invoice_no,
            'ref_amount' =>  $jrnls->amount,
            'acc_no' =>  '',
            'ifsc_code' =>  '',
            'bank_name' =>  '',
            'company_bank_name'=>'',
            'company_bank_acc'=>'',
            'cheque_amount' =>  '',
            'cross_using' => '',
            'mode_of_pay' => '',
            'inst_no' =>  NULL,
            'utr_no'  => NULL,
            'inst_date' =>  NULL,
            'favoring_name' =>  '',
            'remarks' => '',
            'generated_by' => '0',
            'narration' => 'Being '.$trans_type_name.' Booked towards UserId ' . $user_id . ', Invoice No '. $invoice_no .' & Batch no '. $batch_no .$payment_id,
        ];
        $gstData = [];
        if ($jrnls->trans_type == config('lms.TRANS_TYPE.WAVED_OFF')) {
              $totalamount = $jrnls->amount;
              $userStateId = $parentRecord->userinvoicetrans->getUserInvoice->user_gst_state_id ?? NULL;
              $companyStateId = $parentRecord->userinvoicetrans->getUserInvoice->comp_gst_state_id ?? NULL;
              $base_amt = $totalamount;
              $igst_amt = 0;
              $igst_rate = 0;
              $cgst_amt = 0;
              $cgst_rate = 0;
              $sgst_amt = 0;
              $sgst_rate = 0;
              $totalGST = $parentRecord->gst_per;
              if ($parentRecord->gst == 1) {
                  $base_amt = $totalamount * 100/(100 + $totalGST);
                  if($userStateId == $companyStateId) {
                      $cgst_rate = ($totalGST/2);
                      $cgst_amt = round((($base_amt * $cgst_rate)/100),2);
                      $sgst_rate = ($totalGST/2);
                      $sgst_amt = round((($base_amt * $sgst_rate)/100),2);
                  } else {
                    $igst_rate = $totalGST;
                      $igst_amt = round((($base_amt * $igst_rate)/100),2); 
                  }
              }
            $gstData['base_amount'] = $base_amt;
            if ($sgst_amt != 0) {
                $gstData['sgst'] = $sgst_amt;
            } 
            if ($cgst_amt != 0) {
                $gstData['cgst'] = $cgst_amt;
            } 
            if ($igst_amt != 0) {
                $gstData['igst'] = $igst_amt;
            }
            foreach ($gstData as $gst_key => $gst_val) {
              $gst_trans_amount = $gst_val;
              switch ($gst_key) {
                case 'base_amount':
                  $gst_trans_type = $trans_type_name;
                  break;
                case 'sgst':
                  $gst_trans_type = 'SGST - '.$trans_type_name;
                  break;
                case 'cgst':
                  $gst_trans_type = 'CGST - '.$trans_type_name;
                  break;
                case 'igst':
                  $gst_trans_type = 'IGST - '.$trans_type_name;
                  break;
              }
              $JournalRow['trans_type'] =  $gst_trans_type;
              $JournalRow['amount'] =  $gst_trans_amount;
              $JournalRow['ref_amount'] =  $gst_trans_amount;
              $journalPayments[] = $JournalRow;
            }
        }else if (!empty($jrnls->userinvoicetrans->getUserInvoice->invoice_no)) {
          $gstData['base_amount'] = $jrnls->userinvoicetrans->base_amount;
          if ($jrnls->userinvoicetrans->sgst_amount != 0) {
              $gstData['sgst'] = $jrnls->userinvoicetrans->sgst_amount;
          } 
          if ($jrnls->userinvoicetrans->cgst_amount != 0) {
              $gstData['cgst'] = $jrnls->userinvoicetrans->cgst_amount;
          } 
          if ($jrnls->userinvoicetrans->igst_amount != 0) {
              $gstData['igst'] = $jrnls->userinvoicetrans->igst_amount;
          }
          foreach ($gstData as $gst_key => $gst_val) {
            $gst_trans_amount = $gst_val;
            switch ($gst_key) {
              case 'base_amount':
                $gst_trans_type = $trans_type_name;
                break;
              case 'sgst':
                $gst_trans_type = 'SGST - '.$trans_type_name;
                break;
              case 'cgst':
                $gst_trans_type = 'CGST - '.$trans_type_name;
                break;
              case 'igst':
                $gst_trans_type = 'IGST - '.$trans_type_name;
                break;
            }
            $JournalRow['trans_type'] =  $gst_trans_type;
            $JournalRow['amount'] =  $gst_trans_amount;
            $JournalRow['ref_amount'] =  $gst_trans_amount;
            $journalPayments[] = $JournalRow;
          }
        }else{
          $journalPayments[] = $JournalRow;
        }
      }
    }
    return $journalPayments;
  }

  private function createReversalData($rvrslRow, $batch_no) {
    $reversalPayment = [];
    $where = [['is_posted_in_tally', '=', '0'],['soa_flag', '=', '1']];
        $rvrsl = $rvrslRow;

        $accountDetails = $rvrsl->userRelation->companyBankDetails ?? NULL;
        if (empty($accountDetails)) {
          return $reversalPayment;
        }
        $user_id = Helper::formatIdWithPrefix($rvrsl->user_id, 'CUSTID');
        $bizName = $rvrsl->user->biz_name;
        $trans_type_name = $rvrsl->getTransNameAttribute();
        $invoice_no = $rvrsl->userinvoicetrans->getUserInvoice->invoice_no ?? NULL;
        $invoice_date = $rvrsl->userinvoicetrans->getUserInvoice->created_at ?? NULL;
        if (empty($invoice_no)) {
            $invoice_no = $rvrsl->invoiceDisbursed->invoice->invoice_no ?? NULL;
            $invoice_date = $rvrsl->invoiceDisbursed->invoice->invoice_date ?? NULL;
        }
        $inst_no = $rvrsl->refundReq->tran_no ?? NULL;
        $inst_date = $rvrsl->refundReq->actual_refund_date ?? NULL;
        if (!empty($rvrsl->parent_trans_id)) {
          $parentRecord  = $rvrsl->getParentTxn();
          if (empty($invoice_no)) {
              $invoice_no = $parentRecord->userinvoicetrans->getUserInvoice->invoice_no ?? NULL;
              $invoice_date = $parentRecord->userinvoicetrans->getUserInvoice->created_at ?? NULL;
              if (empty($invoice_no)) {
                $invoice_no = $parentRecord->invoiceDisbursed->invoice->invoice_no ?? NULL;
                $invoice_date = $parentRecord->invoiceDisbursed->invoice->invoice_date ?? NULL;
              }
          }
          if (empty($inst_no)) {
                $inst_no = $parentRecord->refundReq->tran_no ?? NULL;
                $inst_date = $parentRecord->refundReq->actual_refund_date ?? NULL;
          }
        }
        $this->selectedTxnData[] = $rvrsl->trans_id;

         $getTransfactVoucherNumber = Helper::getTransfactVoucherNumber($rvrsl->trans_id);
        if($getTransfactVoucherNumber){
          $factvoucherNumber = $getTransfactVoucherNumber->fact_voucher_no;
        }else{
          $this->journalFactVoucherSeq++;  
          $factvoucherNumber = 'SJV'.$this->voucherFormat.sprintf('%07d',$this->journalFactVoucherSeq);
          $transfactvoucherData = [
            'trans_id'=>$rvrsl->trans_id,
            'fact_voucher_no'=>$factvoucherNumber,
            'created_at'=>\Carbon\Carbon::now()->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s')
          ];
          
          $this->journalTransFactVoucher[] = $transfactvoucherData;
        }
        $this->journalFactEndVoucherNumber = $factvoucherNumber;
        $this->voucherNo = $this->voucherNo + 1;
        $linkTransFactNumber = $this->getLinkTransFactNo($this->journalTransFactVoucher,$rvrsl->link_trans_id);
        $reversalRow = [
            'batch_no' =>  $batch_no,
            'transactions_id' =>  $rvrsl->trans_id,
            'voucher_no' => $this->voucherNo,
            'fact_voucher_number'=>$factvoucherNumber??NULL,
            'voucher_type' => 'Journal',
            'voucher_date' => $rvrsl->trans_date,
            'transaction_date'=>$rvrsl->created_at,
            'is_debit_credit' => $rvrsl->entry_type == 1 ? 'Credit' : 'Debit',
            'trans_type' =>  $trans_type_name,
            'invoice_no' =>   $invoice_no,
            'invoice_date' =>  $invoice_date,
            'ledger_name' =>  $bizName,
            'amount' =>  ($rvrsl->amount),
            'ref_no' =>  $invoice_no,
            'ref_amount' =>  ($rvrsl->amount),
            'acc_no' =>  $accountDetails->acc_no ?? '',
            'ifsc_code' =>  $accountDetails->ifsc_code ?? '',
            'bank_name' =>  $accountDetails->bank->bank_name ?? '',
            'cheque_amount' =>  '',
            'cross_using' => '',
            'mode_of_pay' => '',
            'company_bank_name'=>'',
            'company_bank_acc'=>'',
            'inst_no' =>  NULL,
            'utr_no'  => NULL,
            'inst_date' =>  NULL,
            'favoring_name' =>  '',
            'remarks' => '',
            'generated_by' => '0',
            'narration' => 'Being '.$trans_type_name.' Booked towards UserId ' . $user_id . ', Invoice No '. $invoice_no .' & Batch no '. $batch_no. ' & Fact No '. $linkTransFactNumber,
       ];
       $reversalPayment[] = $reversalRow;
    return $reversalPayment;
  }

  private function createRefundData($refundData, $batch_no) {
    $refundPayment = [];
    foreach($refundData as $rfnd){
      $this->paymentFactVoucherSeq++;
      $getTransfactVoucherNumber = Helper::getTransfactVoucherNumber($rfnd->trans_id);
      if($getTransfactVoucherNumber){
        $factvoucherNumber = $getTransfactVoucherNumber->fact_voucher_no;
      }else{
        $factvoucherNumber = 'SRP'.$this->voucherFormat.sprintf('%07d',$this->paymentFactVoucherSeq);
        $transfactvoucherData = [
          'trans_id'=>$rfnd->trans_id,
          'fact_voucher_no'=>$factvoucherNumber,
          'created_at'=>\Carbon\Carbon::now()->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s')
        ];
        $this->refundTransFactVoucher[] = $transfactvoucherData;
      }
      $this->paymentFactEndVoucherNumber = $factvoucherNumber;
      $this->voucherNo = $this->voucherNo + 1;
      $accountDetails = $rfnd->userRelation->companyBankDetails ?? NULL;
      if (empty($accountDetails)) {
        continue;
      }
      $payment_id = '';
      if(!empty(($rfnd->payment_id))){  
       $payment_id = " & Payment Id ".$rfnd->payment_id;
      }
      $user_id = Helper::formatIdWithPrefix($rfnd->user_id, 'CUSTID');
      $userName = $rfnd->user->biz_name;
      $trans_type_name = $rfnd->getTransNameAttribute();
      $invoice_no = $rfnd->userinvoicetrans->getUserInvoice->invoice_no ?? NULL;
      $invoice_date = $rfnd->userinvoicetrans->getUserInvoice->created_at ?? NULL;
      $inst_no = $rfnd->refundReq->tran_no ?? NULL;
      $inst_date = $rfnd->refundReq->actual_refund_date ?? NULL;
      if (!empty($rfnd->parent_trans_id)) {
        $parentRecord  = $rfnd->getParentTxn();
        if (empty($invoice_no)) {
            $invoice_no = $parentRecord->userinvoicetrans->getUserInvoice->invoice_no ?? NULL;
            $invoice_date = $parentRecord->userinvoicetrans->getUserInvoice->created_at ?? NULL;
            if (empty($invoice_no)) {
              $invoice_no = $parentRecord->invoiceDisbursed->invoice->invoice_no ?? NULL;
              $invoice_date = $parentRecord->invoiceDisbursed->invoice->invoice_date ?? NULL;
            }
        }
        if (empty($inst_no)) {
            $inst_no = $parentRecord->refundReq->tran_no ?? NULL;
            $inst_date = $parentRecord->refundReq->actual_refund_date ?? NULL;
        }
      }
      $utr_no = $rfnd->refundTrans->refundReq->tran_no??NULL; //code by new add old NULL
      $this->selectedTxnData[] = $rfnd->trans_id;
      $CustomerRow = [
          'batch_no' =>  $batch_no,
          'transactions_id' =>  $rfnd->trans_id,
          'voucher_no' => $this->voucherNo,
          'fact_voucher_number'=>$factvoucherNumber,
          'voucher_type' => 'Payment',
          'voucher_date' => $rfnd->trans_date,
          'transaction_date'=>$rfnd->created_at,
          'is_debit_credit' =>  'Debit',
          'trans_type' =>  $trans_type_name,
          'invoice_no' =>   $invoice_no,
          'invoice_date' =>  $invoice_date,
          'ledger_name' =>  $userName,
          'amount' =>  ($rfnd->amount * -1),
          'ref_no' =>  $invoice_no,
          'ref_amount' =>  ($rfnd->amount * -1),
          'acc_no' =>  '',
          'ifsc_code' =>  '',
          'bank_name' =>  '',
          'cheque_amount' =>  '',
          'cross_using' => '',
          'mode_of_pay' => '',
          'company_bank_name'=>'',
          'company_bank_acc'=>'',
          'inst_no' =>  NULL,
          'utr_no'  => $utr_no, //code by new add
          'inst_date' =>  NULL,
          'favoring_name' =>  '',
          'remarks' => '',
          'generated_by' => '0',
          'narration' => 'Being '.$trans_type_name.' towards UserId ' . $user_id . ', Invoice No '. $invoice_no .' & Batch no '. $batch_no .$payment_id,
     ];
     $refundPayment[] = $CustomerRow;
     $BankRow = [
          'batch_no' =>  $batch_no,
          'transactions_id' =>  NULL,
          'voucher_no' => $this->voucherNo,
          'fact_voucher_number'=>$factvoucherNumber,
          'voucher_type' => 'Payment',
          'voucher_date' => $rfnd->trans_date, //code by new add old NULL
          'transaction_date'=>$rfnd->created_at,
          'is_debit_credit' =>  'Credit',
          'trans_type' =>  ($accountDetails->bank->bank_name ?? '') . ' - Refund',
          'invoice_no' =>   '',
          'invoice_date' =>  NULL,
          'ledger_name' =>  $accountDetails->bank->bank_name ?? '',
          'amount' =>  $rfnd->amount,
          'ref_no' =>  $invoice_no,
          'ref_amount' =>  $rfnd->amount,
          'acc_no' =>  $accountDetails->acc_no ?? '',
          'ifsc_code' =>  $accountDetails->ifsc_code ?? '',
          'bank_name' =>  $accountDetails->bank->bank_name ?? '',
          'cheque_amount' =>  '',
          'cross_using' => '',
          'company_bank_name'=>'',
          'company_bank_acc'=>'',
          'mode_of_pay' => 'e-Fund-Transfer',
          'inst_no' =>  $inst_no,
          'utr_no'  => $utr_no, //code by new add
          'inst_date' =>  $inst_date,
          'favoring_name' =>  $userName,
          'remarks' => '',
          'generated_by' => '1',
          'narration' => 'Being '.$trans_type_name.' towards UserId ' . $user_id . ', Invoice No '. $invoice_no .' & Batch no '. $batch_no .$payment_id,
     ];
     $refundPayment[] = $BankRow;
    }
    return $refundPayment;
  }  

  private function createDisbursalData($disbursalData, $batch_no) {
    $disbursalPayment = [];
    foreach($disbursalData as $dsbrsl){
      $this->paymentFactVoucherSeq++;  
      $getTransfactVoucherNumber = Helper::getTransfactVoucherNumber($dsbrsl->trans_id);
      if($getTransfactVoucherNumber){
        $factvoucherNumber = $getTransfactVoucherNumber->fact_voucher_no;
      }else{
        $factvoucherNumber = 'SRP'.$this->voucherFormat.sprintf('%07d',$this->paymentFactVoucherSeq);
        $transfactvoucherData = [
          'trans_id'=>$dsbrsl->trans_id,
          'fact_voucher_no'=>$factvoucherNumber,
          'created_at'=>\Carbon\Carbon::now()->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s')
        ];
        $this->disbursalTransFactVoucher[] = $transfactvoucherData;
      } 
      
      $this->paymentFactEndVoucherNumber = $factvoucherNumber;
      $this->voucherNo = $this->voucherNo + 1;
      $accountDetails = $dsbrsl->userRelation->companyBankDetails ?? NULL;
      if (empty($accountDetails)) {
        continue;
      }
      $payment_id = '';
      if(!empty(($dsbrsl->payment_id))){  
       $payment_id = " & Payment Id ".$dsbrsl->payment_id;
      }
       
      $user_id = Helper::formatIdWithPrefix($dsbrsl->user_id, 'CUSTID');
      $userName = $dsbrsl->user->biz_name;
      $invoice_no = $dsbrsl->invoiceDisbursed->invoice->invoice_no ?? NULL;
      $invoice_date = $dsbrsl->invoiceDisbursed->invoice->invoice_date ?? NULL;
      $disburse_amt = $dsbrsl->invoiceDisbursed->disburse_amt;
      $total_interest = $dsbrsl->invoiceDisbursed->total_interest;
      $this->selectedTxnData[] = $dsbrsl->trans_id;
      $CustomerRow = [
              'batch_no' =>  $batch_no,
              'transactions_id' =>  $dsbrsl->trans_id,
              'voucher_no' => $this->voucherNo,
              'fact_voucher_number'=>$factvoucherNumber,
              'voucher_type' => 'Payment',
              'voucher_date' => $dsbrsl->trans_date,
              'transaction_date'=>$dsbrsl->created_at,
              'is_debit_credit' =>  'Debit',
              'trans_type' =>  $dsbrsl->getTransNameAttribute(),
              'invoice_no' =>   $invoice_no,
              'invoice_date' =>  $invoice_date,
              'ledger_name' =>  $userName,
              'amount' =>  ($disburse_amt * -1),
              'ref_no' =>  $invoice_no,
              'ref_amount' =>  ($disburse_amt * -1),
              'acc_no' =>  '',
              'ifsc_code' =>  '',
              'bank_name' =>  '',
              'cheque_amount' =>  '',
              'cross_using' => '',
              'mode_of_pay' => '',
              'inst_no' =>  NULL,
              'utr_no'  => $dsbrsl->invoiceDisbursed->disbursal->tran_id ?? NULL, //new code add old NULL
              'inst_date' =>  NULL,
              'favoring_name' =>  '',
              'company_bank_name'=>'',
              'company_bank_acc'=>'',
              'remarks' => '',
              'generated_by' => '0',
              'narration' => 'Being  Payment Disbursed towards UserId ' . $user_id . ', Invoice No '. $invoice_no .' & Batch no '. $batch_no .$payment_id,
      ];
      $disbursalPayment[] = $CustomerRow;

      // Actual Disbursement in Bank
      $BankRow = [
        'batch_no' =>  $batch_no,
        'transactions_id' =>  NULL,
        'voucher_no' => $this->voucherNo,
        'fact_voucher_number'=>$factvoucherNumber,
        'voucher_type' => 'Payment',
        'voucher_date' => $dsbrsl->trans_date, //code by new add old NULL
        'transaction_date'=>$dsbrsl->created_at,
        'is_debit_credit' =>  'Credit',
        'trans_type' =>  ($accountDetails->bank->bank_name ?? ''). ' - Disbursement',//BY DJ
        'invoice_no' =>   $invoice_no,
        'invoice_date' =>  $invoice_date,
        'ledger_name' =>  $accountDetails->bank->bank_name ?? '',
        'amount' =>  $disburse_amt,
        'ref_no' =>  $invoice_no,
        'ref_amount' =>  $disburse_amt,
        'acc_no' =>  $accountDetails->acc_no ?? '',
        'ifsc_code' =>  $accountDetails->ifsc_code ?? '',
        'bank_name' =>  $accountDetails->bank->bank_name ?? '',
        'cheque_amount' =>  $disburse_amt,
        'cross_using' => '',
        'mode_of_pay' => 'e-Fund-Transfer',
        'inst_no' =>  $dsbrsl->invoiceDisbursed->disbursal->tran_id ?? NULL,
        'utr_no'  => $dsbrsl->invoiceDisbursed->disbursal->tran_id ?? NULL, //New Code add old NULL
        'inst_date' =>  $dsbrsl->invoiceDisbursed->disbursal->funded_date ?? NULL,
        'favoring_name' =>  $userName,
        'remarks' => '',
        'company_bank_name'=>'',
        'company_bank_acc'=>'',
        'generated_by' => '1',
        'narration' => 'Being  Payment Disbursed towards UserId ' . $user_id . ', Invoice No '. $invoice_no .' & Batch no '. $batch_no .$payment_id,
      ];
      
      $disbursalDate = $dsbrsl->trans_date;

      // Upfront Interest Deduction
      $where = ['trans_date' => $disbursalDate, 'payment_id' => NULL, 'trans_type' => config('lms.TRANS_TYPE.INTEREST'), 'entry_type' => 1];
      $interestDeduction = $dsbrsl->getInterestForDisbursal($where);
      if(isset($interestDeduction)){
        $interestTransId = $interestDeduction->trans_id;
        $getTransfactVoucherNumber = Helper::getTransfactVoucherNumber($interestTransId);
        if($getTransfactVoucherNumber){
          $factvoucherNumber = $getTransfactVoucherNumber->fact_voucher_no;
        }else{
          $transfactvoucherData = [
            'trans_id'=>$interestTransId,
            'fact_voucher_no'=>$factvoucherNumber,
            'created_at'=>\Carbon\Carbon::now()->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s')
          ];
          $this->disbursalTransFactVoucher[] = $transfactvoucherData;
        }
        $this->selectedTxnData[] = $interestTransId;
        $disburse_amt = round($disburse_amt - $interestDeduction->amount,2);
        $InterestRow = [
                'batch_no' =>  $batch_no,
                'transactions_id' =>  $interestTransId,
                'voucher_no' => $this->voucherNo,
                'fact_voucher_number'=>$factvoucherNumber,
                'voucher_type' => 'Payment',
                'voucher_date' => $dsbrsl->trans_date,
                'transaction_date'=>$dsbrsl->created_at,
                'is_debit_credit' =>  'Credit',
                'trans_type' =>  'Interest',
                'invoice_no' =>   $invoice_no,
                'invoice_date' =>  $invoice_date,
                'ledger_name' =>  'Interest',
                'amount' =>  $interestDeduction->amount,
                'ref_no' =>  $invoice_no,
                'ref_amount' =>  $interestDeduction->amount,
                'acc_no' =>  '',
                'ifsc_code' =>  '',
                'bank_name' =>  '',
                'cheque_amount' =>  '',
                'cross_using' => '',
                'mode_of_pay' => '',
                'inst_no' =>  NULL,
                'utr_no'  => $dsbrsl->invoiceDisbursed->disbursal->tran_id ?? NULL, //New Code add old NULL
                'inst_date' =>  NULL,
                'favoring_name' =>  '',
                'company_bank_name'=>'',
                'company_bank_acc'=>'',
                'remarks' => '',
                'generated_by' => '1',
                'narration' => 'Being Interest Payment towards UserId ' . $user_id . ', Invoice No '. $invoice_no .' & Batch no '. $batch_no .$payment_id,
        ];
        $disbursalPayment[] = $InterestRow;
      }
      
      // Invoice Level Processing Fee Deduction
      $where = ['trans_date' => $disbursalDate, 'payment_id' => NULL, 'trans_type' => config('lms.TRANS_TYPE.INVOICE_PROCESSING_FEE'), 'entry_type' => 1];
      $feeDeduction = $dsbrsl->getInterestForDisbursal($where);
      if(isset($feeDeduction)){
        $interestTransId = $feeDeduction->trans_id;
        $getTransfactVoucherNumber = Helper::getTransfactVoucherNumber($interestTransId);
        if($getTransfactVoucherNumber){
          $factvoucherNumber = $getTransfactVoucherNumber->fact_voucher_no;
        }else{
          $transfactvoucherData = [
            'trans_id'=>$interestTransId,
            'fact_voucher_no'=>$factvoucherNumber,
            'created_at'=>\Carbon\Carbon::now()->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s')
          ];
          $this->disbursalTransFactVoucher[] = $transfactvoucherData;
        }
        $this->selectedTxnData[] = $interestTransId;
        $disburse_amt = round($disburse_amt - $feeDeduction->amount,2);
        $chargeRow = [
                'batch_no' =>  $batch_no,
                'transactions_id' =>  $interestTransId,
                'voucher_no' => $this->voucherNo,
                'fact_voucher_number'=>$factvoucherNumber,
                'voucher_type' => 'Payment',
                'voucher_date' => $dsbrsl->trans_date,
                'transaction_date'=>$dsbrsl->created_at,
                'is_debit_credit' =>  'Credit',
                'trans_type' =>  'Invoice Processing Fee Payment',
                'invoice_no' =>   $invoice_no,
                'invoice_date' =>  $invoice_date,
                'ledger_name' =>  'Invoice Processing Fee Payment',
                'amount' =>  $feeDeduction->amount,
                'ref_no' =>  $invoice_no,
                'ref_amount' =>  $feeDeduction->amount,
                'acc_no' =>  '',
                'ifsc_code' =>  '',
                'bank_name' =>  '',
                'cheque_amount' =>  '',
                'cross_using' => '',
                'mode_of_pay' => '',
                'inst_no' =>  NULL,
                'utr_no'  => $dsbrsl->invoiceDisbursed->disbursal->tran_id ?? NULL, //New Code add old NULL
                'inst_date' =>  NULL,
                'favoring_name' =>  '',
                'company_bank_name'=>'',
                'company_bank_acc'=>'',
                'remarks' => '',
                'generated_by' => '1',
                'narration' => 'Being Invoice Processing Fee Payment towards UserId ' . $user_id . ', Invoice No '. $invoice_no .' & Batch no '. $batch_no .$payment_id,
        ];
        $disbursalPayment[] = $chargeRow;
      }
      $BankRow['amount'] =  $disburse_amt;
      $BankRow['ref_amount'] =  $disburse_amt;
      $BankRow['cheque_amount'] =  $disburse_amt;
      $disbursalPayment[] = $BankRow;
    }
    return $disbursalPayment;
  }

  private function createReceiptData($receiptData, $batch_no) {
    
    $receiptPayment = [];
    foreach($receiptData as $rcptApport) {
     $rcpt = $rcptApport->payment;
     $this->paymentFactVoucherSeq++;
     $factvoucherNumber = 'SRP'.$this->voucherFormat.sprintf('%07d',$this->paymentFactVoucherSeq); 
     $this->paymentFactEndVoucherNumber = $factvoucherNumber;
     $this->voucherNo = $this->voucherNo + 1;

     $paymentId = $rcpt->payment_id;

    $settledTransactoions = Transactions::where('payment_id', $paymentId)
      ->where('apportionment_id', $rcptApport->apportionment_id)
      ->where('is_posted_in_tally', 0)
      ->whereNotIn('trans_type', [17])
      ->get();

    $refrenceTxns = Transactions::where('payment_id', $paymentId)
      ->where('apportionment_id', $rcptApport->apportionment_id)
      ->where('trans_type', 17)
      ->where('is_posted_in_tally', 0)
      ->first();

     $user_id = Helper::formatIdWithPrefix($rcpt->user_id, 'CUSTID');
     $userName = $rcpt->user->biz_name;
     $accountDetails = $rcpt->userRelation->companyBankDetails ?? NULL;
     if (empty($accountDetails)) {
        continue;
     }
    $utr_no = $rcpt->getTransactionNoAttribute(); //new code for utr no

     $inst_no = $rcpt->refundReq->tran_no ?? NULL;
     $inst_date = $rcpt->refundReq->actual_refund_date ?? NULL;
     $this->selectedTxnData[] = $refrenceTxns->trans_id;
     $this->selectedPaymentData[] = $rcpt->payment_id;

     $getTransfactVoucherNumber = Helper::getTransfactVoucherNumber($refrenceTxns->trans_id);
      if($getTransfactVoucherNumber){
        $factvoucherNumber = $getTransfactVoucherNumber->fact_voucher_no;
      }else{
        $transfactvoucherData = [
          'trans_id'=>$refrenceTxns->trans_id,
          'fact_voucher_no'=>$factvoucherNumber,
          'created_at'=>\Carbon\Carbon::now()->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s')
        ];
        $this->receiptTransFactVoucher[] = $transfactvoucherData;
      }

     switch ($rcpt->payment_type) {
       case '1':
         $mode_of_pay = 'e-Fund-Transfer';
         break;
      case '2':
         $mode_of_pay = 'Cheque No : ' . $rcpt->cheque_no;
         break;
      case '3':
         $mode_of_pay = 'Nach';
         break; 
      case '4':
         $mode_of_pay = 'Other : ' . $rcpt->unr_no;
         break; 
      default:
         $mode_of_pay = 'e-Fund-Transfer';
         break;
     }
     $BankRow = [
              'batch_no' =>  $batch_no,
              'transactions_id' => NULL,
              'voucher_no' => $this->voucherNo,
              'fact_voucher_number'=>$factvoucherNumber,
              'voucher_type' => 'Receipt',
              'voucher_date' => $rcpt->date_of_payment,
              'transaction_date'=>$refrenceTxns->created_at??NULL,
              'is_debit_credit' =>  'Debit',
              'trans_type' =>  ($accountDetails->bank->bank_name ?? '').' - Receipt',
              'invoice_no' =>   '',
              'invoice_date' =>  NULL,
              'ledger_name' =>  $accountDetails->bank->bank_name ?? '',
              'amount' =>  ($rcpt->amount * -1),
              'ref_no' =>  '',
              'ref_amount' =>  ($rcpt->amount * -1),
              'acc_no' =>  $accountDetails->acc_no ?? '',
              'ifsc_code' =>  $accountDetails->ifsc_code ?? '',
              'bank_name' =>  $accountDetails->bank->bank_name ?? '',
              'cheque_amount' =>  '',
              'cross_using' => $rcpt->payment_type == 2 ? 'a/c payee' : NULL,
              'mode_of_pay' => $mode_of_pay,
              'inst_no' =>  $inst_no,
              'utr_no'  => $utr_no,
              'inst_date' =>  $inst_date,
              'favoring_name' =>  $userName,
              'company_bank_name'=>$rcpt->companyUserAccount?$rcpt->companyUserAccount->bank->bank_name:NULL,
              'company_bank_acc'=>$rcpt->companyUserAccount?$rcpt->companyUserAccount->acc_no:NULL,
              'remarks' => '',
              'generated_by' => '1',
              'narration' => 'Being Repayment towards UserId ' . $user_id . ' & Batch no '. $batch_no,
     ];
     $receiptPayment[] = $BankRow;
     if (!empty($settledTransactoions)) {
       foreach ($settledTransactoions as $stldTxn) {
          $trans_type_name = $stldTxn->getTransNameAttribute();
          $invoice_no = $stldTxn->userinvoicetrans->getUserInvoice->invoice_no ?? NULL;
          $invoice_date = $stldTxn->userinvoicetrans->getUserInvoice->created_at ?? NULL;
          if (empty($invoice_no) && !empty($stldTxn->parent_trans_id)) {
            $parentRecord  = $stldTxn->getParentTxn();
            $invoice_no = $parentRecord->userinvoicetrans->getUserInvoice->invoice_no ?? NULL;
            $invoice_date = $parentRecord->userinvoicetrans->getUserInvoice->created_at ?? NULL;
            if (empty($invoice_no)) {
              $invoice_no = $parentRecord->invoiceDisbursed->invoice->invoice_no ?? NULL;
              $invoice_date = $parentRecord->invoiceDisbursed->invoice->invoice_date ?? NULL;
            }
          }
          $getTransfactVoucherNumber = Helper::getTransfactVoucherNumber($stldTxn->trans_id);
          if($getTransfactVoucherNumber){
            $factvoucherNumber = $getTransfactVoucherNumber->fact_voucher_no;
          }else{
            $transfactvoucherData = [
              'trans_id'=>$stldTxn->trans_id,
              'fact_voucher_no'=>$factvoucherNumber,
              'created_at'=>\Carbon\Carbon::now()->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s')
            ];
            $this->receiptTransFactVoucher[] = $transfactvoucherData;
          }
          $this->selectedTxnData[] = $stldTxn->trans_id;
          $settledRow = [
              'batch_no' =>  $batch_no,
              'transactions_id' =>  $stldTxn->trans_id,
              'voucher_no' => $this->voucherNo,
              'fact_voucher_number'=>$factvoucherNumber,
              'voucher_type' => 'Receipt',
              'voucher_date' => $stldTxn->trans_date,
              'transaction_date'=>$stldTxn->created_at?$stldTxn->created_at:NULL,
              'is_debit_credit' =>  'Credit',
              'trans_type' =>  $trans_type_name,
              'invoice_no' =>   $invoice_no,
              'invoice_date' =>  $invoice_date,
              'ledger_name' =>  $userName,
              'amount' =>  $stldTxn->amount,
              'ref_no' =>  $invoice_no,
              'ref_amount' =>  $stldTxn->amount,
              'acc_no' =>  '',
              'ifsc_code' =>  '',
              'bank_name' =>  '',
              'cheque_amount' =>  '',
              'cross_using' => '',
              'mode_of_pay' => '',
              'inst_no' =>  NULL,
              'utr_no'  => $utr_no,
              'inst_date' =>  NULL,
              'favoring_name' =>  '',
              'company_bank_name'=>$rcpt->companyUserAccount?$rcpt->companyUserAccount->bank->bank_name:NULL,
              'company_bank_acc'=>$rcpt->companyUserAccount?$rcpt->companyUserAccount->acc_no:NULL,
              'remarks' => '',
              'generated_by' => '0',
              'narration' => 'Being '.$trans_type_name.' towards UserId ' . $user_id . ', Invoice No '. $invoice_no .' & Batch no '. $batch_no,
          ];
          if (in_array($stldTxn->trans_type, [config('lms.TRANS_TYPE.MARGIN'), config('lms.TRANS_TYPE.NON_FACTORED_AMT')])) {
            $settledRow['generated_by'] = 1; 
          }
          $receiptPayment[] = $settledRow;
       }
     }
    }
    return $receiptPayment;
  }

  private function createReceipReversalData($receiptReversalData, $batch_no) {
    
    $receiptPayment = [];
    foreach ($receiptReversalData as $rcptApport) {
     $rcpt = $rcptApport->payment;
     $this->paymentFactVoucherSeq++;
     $factvoucherNumber = 'SRP'.$this->voucherFormat.sprintf('%07d',$this->paymentFactVoucherSeq); 
     $this->paymentFactEndVoucherNumber = $factvoucherNumber;
     $this->voucherNo = $this->voucherNo + 1;
     $paymentId = $rcpt->payment_id;
      $settledTransactoions = Transactions::where('apportionment_id', $rcptApport->apportionment_id)
        ->whereHas('linkTransactions', function ($query) use ($paymentId) {
          $query->where('payment_id', $paymentId)
          ->whereNotIn('trans_type', [17]);
        })
        ->where('is_posted_in_tally', 0)
        ->get();

      $refrenceTxns = Transactions::where('payment_id', $rcpt->payment_id)
        ->where('apportionment_id', $rcptApport->apportionment_id)
        ->where('trans_type', 2)
        ->whereHas('linkTransactions', function ($query) use ($paymentId) {
          $query->where('payment_id', $paymentId)
            ->where('trans_type', 17);
        })
        ->where('is_posted_in_tally', 0)
        ->first();
     $user_id = Helper::formatIdWithPrefix($rcpt->user_id, 'CUSTID');
     $userName = $rcpt->user->biz_name;
     $accountDetails = $rcpt->userRelation->companyBankDetails ?? NULL;
     if (empty($accountDetails)) {
        continue;
     }
     $utr_no = $rcpt->getTransactionNoAttribute(); 

     $inst_no = $rcpt->refundReq->tran_no ?? NULL;
     $inst_date = $rcpt->refundReq->actual_refund_date ?? NULL;
     $this->selectedTxnData[] = $refrenceTxns->trans_id ;
     $this->selectedPaymentData[] = $rcpt->payment_id;

      $getTransfactVoucherNumber = Helper::getTransfactVoucherNumber($refrenceTxns->trans_id);
      if($getTransfactVoucherNumber){
        $factvoucherNumber = $getTransfactVoucherNumber->fact_voucher_no;
      }else{
        $transfactvoucherData = [
          'trans_id'=>$refrenceTxns->trans_id,
          'fact_voucher_no'=>$factvoucherNumber,
          'created_at'=>\Carbon\Carbon::now()->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s')
        ];
        $this->receiptTransFactVoucher[] = $transfactvoucherData;
      }

     $linkTransFactNumber = $this->getLinkTransFactNo($this->receiptTransFactVoucher,$refrenceTxns->link_trans_id);

     switch ($rcpt->payment_type) {
       case '1':
         $mode_of_pay = 'e-Fund-Transfer';
         break;
      case '2':
         $mode_of_pay = 'Cheque No : ' . $rcpt->cheque_no;
         break;
      case '3':
         $mode_of_pay = 'Nach';
         break; 
      case '4':
         $mode_of_pay = 'Other : ' . $rcpt->unr_no;
         break; 
      default:
         $mode_of_pay = 'e-Fund-Transfer';
         break;
     }
     $BankRow = [
              'batch_no' =>  $batch_no,
              'transactions_id' => NULL,
              'voucher_no' => $this->voucherNo,
              'fact_voucher_number'=>$factvoucherNumber,
              'voucher_type' => 'Receipt',
              'voucher_date' => $rcpt->date_of_payment,
              'transaction_date'=>$refrenceTxns->created_at??NULL,
              'is_debit_credit' =>  'Credit',
              'trans_type' =>  ($accountDetails->bank->bank_name ?? '').' - Receipt',
              'invoice_no' =>   '',
              'invoice_date' =>  NULL,
              'ledger_name' =>  $accountDetails->bank->bank_name ?? '',
              'amount' =>  ($rcpt->amount),
              'ref_no' =>  '',
              'ref_amount' =>  ($rcpt->amount),
              'acc_no' =>  $accountDetails->acc_no ?? '',
              'ifsc_code' =>  $accountDetails->ifsc_code ?? '',
              'bank_name' =>  $accountDetails->bank->bank_name ?? '',
              'cheque_amount' =>  '',
              'cross_using' => $rcpt->payment_type == 2 ? 'a/c payee' : NULL,
              'mode_of_pay' => $mode_of_pay,
              'inst_no' =>  $inst_no,
              'utr_no'  => $utr_no,
              'inst_date' =>  $inst_date,
              'favoring_name' =>  $userName,
              'company_bank_name'=>$rcpt->companyUserAccount?$rcpt->companyUserAccount->bank->bank_name:NULL,
              'company_bank_acc'=>$rcpt->companyUserAccount?$rcpt->companyUserAccount->acc_no:NULL,
              'remarks' => '',
              'generated_by' => '1',
              'narration' => 'Being Repayment Reversed towards UserId ' . $user_id . ' & Batch no '. $batch_no. ' & Fact No '. $linkTransFactNumber,
     ];
     $receiptPayment[] = $BankRow;
     if (!empty($settledTransactoions)) {
       foreach ($settledTransactoions as $stldTxn) {
          if ($stldTxn->amount == 0) {
            continue;
          }
          $trans_type_name = $stldTxn->getTransNameAttribute();
          $invoice_no = $stldTxn->userinvoicetrans->getUserInvoice->invoice_no ?? NULL;
          $invoice_date = $stldTxn->userinvoicetrans->getUserInvoice->created_at ?? NULL;
          if (empty($invoice_no) && !empty($stldTxn->parent_trans_id)) {
            $parentRecord  = $stldTxn->getParentTxn();
            $invoice_no = $parentRecord->userinvoicetrans->getUserInvoice->invoice_no ?? NULL;
            $invoice_date = $parentRecord->userinvoicetrans->getUserInvoice->created_at ?? NULL;
            if (empty($invoice_no)) {
              $invoice_no = $parentRecord->invoiceDisbursed->invoice->invoice_no ?? NULL;
              $invoice_date = $parentRecord->invoiceDisbursed->invoice->invoice_date ?? NULL;
            }
          }
          $getTransfactVoucherNumber = Helper::getTransfactVoucherNumber($stldTxn->trans_id);
          if($getTransfactVoucherNumber){
            $factvoucherNumber = $getTransfactVoucherNumber->fact_voucher_no;
          }else{
            $transfactvoucherData = [
              'trans_id'=>$stldTxn->trans_id,
              'fact_voucher_no'=>$factvoucherNumber,
              'created_at'=>\Carbon\Carbon::now()->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s')
            ];
            $this->receiptTransFactVoucher[] = $transfactvoucherData;
          }
          $this->selectedTxnData[] = $stldTxn->trans_id;
          $linkTransFactNumber = $this->getLinkTransFactNo($this->receiptTransFactVoucher,$stldTxn->link_trans_id);
          $settledRow = [
              'batch_no' =>  $batch_no,
              'transactions_id' =>  $stldTxn->trans_id,
              'voucher_no' => $this->voucherNo,
              'fact_voucher_number'=>$factvoucherNumber,
              'voucher_type' => 'Receipt',
              'voucher_date' => $stldTxn->trans_date,
              'transaction_date'=>$stldTxn->created_at?$stldTxn->created_at:NULL,
              'is_debit_credit' =>  'Debit',
              'trans_type' =>  $trans_type_name,
              'invoice_no' =>   $invoice_no,
              'invoice_date' =>  $invoice_date,
              'ledger_name' =>  $userName,
              'amount' =>  ($stldTxn->amount * -1),
              'ref_no' =>  $invoice_no,
              'ref_amount' => ($stldTxn->amount * -1),
              'acc_no' =>  '',
              'ifsc_code' =>  '',
              'bank_name' =>  '',
              'cheque_amount' =>  '',
              'cross_using' => '',
              'mode_of_pay' => '',
              'inst_no' =>  NULL,
              'utr_no'  => $utr_no,
              'inst_date' =>  NULL,
              'favoring_name' =>  '',
              'company_bank_name'=>$rcpt->companyUserAccount?$rcpt->companyUserAccount->bank->bank_name:NULL,
              'company_bank_acc'=>$rcpt->companyUserAccount?$rcpt->companyUserAccount->acc_no:NULL,
              'remarks' => '',
              'generated_by' => '0',
              'narration' => 'Being '.$trans_type_name.' towards UserId ' . $user_id . ', Invoice No '. $invoice_no .' & Batch no '. $batch_no. ' & Fact No '. $linkTransFactNumber,
          ];
          if (in_array($stldTxn->trans_type, [config('lms.TRANS_TYPE.MARGIN'), config('lms.TRANS_TYPE.NON_FACTORED_AMT')])) {
            $settledRow['generated_by'] = 1; 
          }
          $receiptPayment[] = $settledRow;
       }
     }
    }
    return $receiptPayment;
  }

  function displayDates($date1, $date2, $format = 'Y-m-d') {
    $dates = array();
    $current = strtotime($date1);
    $date2 = strtotime($date2);
    $stepVal = '+1 day';
    while($current <= $date2 ) {
       $dates[] = date($format, $current);
       $current = strtotime($stepVal, $current);
    }
    return $dates;
  }

  function getLinkTransFactNo($transFactVoucher,$linkTransId = NULL){
    $linkTransFactNumber = '';
    if($linkTransId){
      $key = array_search($linkTransId, array_column($transFactVoucher, 'trans_id'));
      if (!($key === false)) {
        $linkTransFactNumber = $transFactVoucher[$key]['fact_voucher_no'];
      }else{
        $getTransfactVoucherNumber = Helper::getTransfactVoucherNumber($linkTransId);
        $linkTransFactNumber = $getTransfactVoucherNumber->fact_voucher_no ?? '';
      }
    }
    return $linkTransFactNumber;
  }

  public function tally_entry_date_wise(){
    $activeDate = Carbon::now()->subDays(1)->setTimezone(config('common.timezone'))->format('Y-m-d');
    // $dates = $this->displayDates('2020-01-01', date('Y-m-d'));
    // foreach ($dates as $activeDate) {
      $startActiveDate  = "$activeDate 00:00:00"; 
      $endActiveDate = "$activeDate 23:59:59";
      self::tally_entry($startActiveDate,$endActiveDate);
    // }
  }

  public function tally_entry_Week_wise($weekName){
    $activeDate = Carbon::now()->subDays(1)->setTimezone(config('common.timezone'))->format('Y-m-d');
    // $dates = $this->displayDates('2022-01-01', date('Y-m-d'));
    // foreach ($dates as $activeDate) {
      if(in_array(strtolower(trim($weekName)),[strtolower(date('D',strtotime($activeDate))), strtolower(date('l',strtotime($activeDate)))])){
        $weekStartDate = date('Y-m-d',(strtotime ( '-6 day' , strtotime($activeDate))));
        $weekStartDate  = "$weekStartDate 00:00:00"; 
        $activeDate = "$activeDate 23:59:59";
        self::tally_entry($weekStartDate,$activeDate);
      }
    // }
  }

  public function tally_entry_month_wise(){
    $activeDate = Carbon::now()->setTimezone(config('common.timezone'))->format('Y-m-d');
    // $dates = $this->displayDates('2020-01-01', '2021-12-31');
    // foreach ($dates as $activeDate) {
      if(date("Y-m-t", strtotime($activeDate)) == $activeDate){
        $monthStartDate = date("Y-m-1", strtotime($activeDate));
        $monthStartDate  = "$monthStartDate 00:00:00"; 
        $activeDate = "$activeDate 23:59:59";
        self::tally_entry($monthStartDate,$activeDate);
      }
    // }
  }

  public function tally_entry($startDate, $endDate){  
    $startDate = Helper::istToUtc($startDate,'Y-m-d H:i:s', 'Y-m-d H:i:s');
    $endDate = Helper::istToUtc($endDate,'Y-m-d H:i:s', 'Y-m-d H:i:s');

    $this->selectedTxnData = [];
    $this->selectedPaymentData = [];
    $this->voucherNo = null;
    ini_set("memory_limit", "-1");
    $response = array(
      'status' => 'failure',
      'message' => 'Request method not allowed to execute the script.',
    );
    if (strpos(php_sapi_name(), 'cli') < 0) {
        $response['sapi'] = php_sapi_name();
        return $this->_setResponse($response, 405);
    }
    $latestRecord = \DB::select('select * from rta_tally_entry order by tally_entry_id DESC limit 1');
    $lastVoucherNo = 0;
    if (!empty($latestRecord)) {
      $lastVoucherNo = $latestRecord[0]->voucher_no ?? 0;
    }
    $lastFactVoucher = Helper::getfactVoucherNumber($startDate);
    $this->paymentFactVoucherSeq = (int)$lastFactVoucher['fact_srp_seq_number'];
    $this->journalFactVoucherSeq = (int)$lastFactVoucher['fact_sjv_seq_number'];
    $this->voucherFormat = $lastFactVoucher['voucher_format'];
    $this->journalFactStartVoucherNumber = 'SJV'.$this->voucherFormat.sprintf('%07d',$this->journalFactVoucherSeq+1);
    $this->paymentFactStartVoucherNumber = 'SRP'.$this->voucherFormat.sprintf('%07d',$this->paymentFactVoucherSeq+1);
    $batch_no = _getRand(15);
    $where = [['is_posted_in_tally', '=', '0'], ['created_at', '>=', $startDate],['created_at', '<=', $endDate],['soa_flag', '=', '1']];
    $journalData = Transactions::getJournalTxnTally($where);
    $disbursalData = Transactions::getDisbursalTxnTally($where);
    $refundData = Transactions::getRefundTxnTally($where);

    $receiptData = Apportionment::with('payment', 'payment.userRelation:user_id,company_id', 'payment.userRelation.companyBankDetails')
      ->where('apportionment_type', 1)
      ->whereHas('transaction', function ($query) use ($where) {
        $query->where($where);
      })
      ->whereHas('payment', function ($query2) {
        $query2->where(['trans_type' => config('lms.TRANS_TYPE.REPAYMENT'), 'action_type' => 1]);
      })
      ->get();

    $receiptReversalData = Apportionment::with('payment', 'payment.userRelation:user_id,company_id', 'payment.userRelation.companyBankDetails')
      ->where('apportionment_type', 2)
      ->whereHas('transaction', function ($query) use ($where) {
        $query->where($where);
      })
      ->whereHas('payment', function ($query2) {
        $query2->where(['trans_type' => config('lms.TRANS_TYPE.REPAYMENT'), 'action_type' => 1]);
      })
      ->get();

    $journalArray = $this->createJournalData($journalData, $batch_no);
    $disbursalArray = $this->createDisbursalData($disbursalData, $batch_no);
    $receiptArray = $this->createReceiptData($receiptData, $batch_no);
    $receiptReversalArray = $this->createReceipReversalData($receiptReversalData, $batch_no);
    $refundArray = $this->createRefundData($refundData, $batch_no);
    $allTransFactVoucher = array_merge($this->journalTransFactVoucher,$this->disbursalTransFactVoucher,$this->receiptTransFactVoucher,$this->refundTransFactVoucher);
    $tally_data = array_merge($disbursalArray, $journalArray , $receiptArray, $receiptReversalArray, $refundArray);
    $selectedTxnData = $this->selectedTxnData;
    $selectedPaymentData = $this->selectedPaymentData;
    DB::beginTransaction();

    try {
      $totalTxnRecords = 0;
      if (!empty($selectedTxnData)) {
        foreach(array_chunk($selectedTxnData,100,true) as $selTxnData){
          $totalTxnRecords = \DB::update('update rta_transactions set is_posted_in_tally = 1 where trans_id in(' . implode(', ', $selTxnData) . ')');
        }
      }
      $totalPaymentsRecords = 0;
      if (!empty($selectedPaymentData)) {
        foreach(array_chunk($selectedPaymentData,100,true) as $selPayData){
          $totalPaymentsRecords = \DB::update('update rta_payments set is_posted_in_tally = 1 where payment_id in(' . implode(', ', $selPayData) . ')');
        }
      }
      $recordsTobeInserted = count($selectedTxnData);
      if(1){
        $response['status'] = 'success';
        $batchData = [
          'batch_no' => $batch_no,
          'record_cnt' => $recordsTobeInserted,
          'start_date' => $startDate,
          'fact_payment_start_vn'=>$this->paymentFactEndVoucherNumber?$this->paymentFactStartVoucherNumber:NULL,
          'fact_payment_end_vn'=>$this->paymentFactEndVoucherNumber,
          'fact_journal_start_vn'=>$this->journalFactEndVoucherNumber?$this->journalFactStartVoucherNumber:NULL,
          'fact_journal_end_vn'=>$this->journalFactEndVoucherNumber,
          'end_date' => $endDate,
          'created_at' => now(),
        ];
        $tally_inst_data = FinanceModel::dataLogger($batchData, 'tally');
        if($tally_inst_data){
              array_walk($tally_data,function(&$value,$key) use ($tally_inst_data){ $value['tally_id'] = $tally_inst_data; });
              \DB::table('tally_entry')->insert($tally_data);
              unset($tally_data);
              array_walk($allTransFactVoucher,function(&$value,$key) use ($tally_inst_data){ $value['tally_id'] = $tally_inst_data; });
              \DB::table('trans_fact_voucher')->insert($allTransFactVoucher);
              unset($allTransFactVoucher);
          $tallyfactVoucher = [
            'tally_id'=>$tally_inst_data,
            'fact_year1'=>$lastFactVoucher['year1'],
            'fact_year2'=>$lastFactVoucher['year2'],
            'fact_month'=>$lastFactVoucher['month'],
            'fact_srp_seq_number'=>$this->paymentFactVoucherSeq,
            'fact_sjv_seq_number'=>$this->journalFactVoucherSeq,
          ];
          TallyFactVoucher::create($tallyfactVoucher);
        }
        $response['message'] =  ($recordsTobeInserted > 1 ? $recordsTobeInserted .' Records inserted successfully' : '1 Record inserted.');
      }
      DB::commit();
    } catch (\Exception $e) {
      DB::rollback();
      Helper::getExceptionMessage($e);
      $response['message'] =  ('DB error occured.').' No Record can be posted in tally.';
    }
    return $response;
  }

  public function karza_webhook(Request $request){
    $response = array(
      'status' => 'failure',
      'message' => 'Request method not allowed',
    );

    $headers = getallheaders();
    if ($request->method() === 'POST') {
       $content_type = $headers['Content-Type'] ?? '';
       $secret_key = $headers['key'] ?? '';

       if ($content_type != 'application/json') {
         $response['message'] =  'Content Type is not valid.';
         return $this->_setResponse($response, 431);
       }

      if ($secret_key != $this->secret_key) {
         $response['message'] =  'Secret Key is not valid';
         return $this->_setResponse($response, 401);
       }
      $result = $request->all();
      if (empty($result)) {
        $response['message'] = "No data found. Server rejected the request";
         return $this->_setResponse($response, 411);
      }

      if (!empty($result['statusCode']) && $result['statusCode'] != '101') {
        $response['message'] = "We are getting statusCode with error.";
         return $this->_setResponse($response, 403);
      }

      if (!empty($result['status'])) {
        $response['message'] = $result['error'] ?? "Unable to get success response.";
        return $this->_setResponse($response, 406);
      }

      $request_id =    $result['requestId'] ?? '';
      $result =    $result['result'];

      if (empty($request_id)) {
        $response['message'] = "Insufficiant data to update the report.";
        return $this->_setResponse($response, 417);
      }

      $gst_data = FinanceModel::getGstData($request_id);
      if (empty($gst_data)) {
         $response['message'] = "Unable to get record against the requestId.";
         return $this->_setResponse($response, 422);
      }
      $app_id = $gst_data['app_id'];
      $gst_no = $gst_data['gstin'];
      $fname = $app_id.'_'.$gst_no;
      $this->logdata($result, 'F', $fname.'.json');
      $file_name = $fname.'.pdf';
      Storage::put('public/user/'.$file_name,file_get_contents($result['pdfDownloadLink'])); 
      $response['message'] =  'Response generated Successfully';
      $response['status'] =  'success';
      return $this->_setResponse($response, 200);
    }else{
       return $this->_setResponse($response, 405);
    }
  }

	public function fsa_callback(Request $request){
    $response = array(
      'status' => 'fail',
      'message' => 'Request method not allowed',
    );
    
    // $dataString = json_encode($request->toArray());
    // Mail::raw($dataString, function ($message) {
    //   $message->to(['sudesh.kumar@zuron.in','amit.suman@zuron.in','hirdesh@zuron.in'])
    //     ->subject('FSA JSON FILE');
    // });

    $headers = getallheaders();
    if ($request->isMethod('post')) {
      $content_type = $headers['Content-Type'];
      if ($content_type != 'application/x-www-form-urlencoded') {
        $response['message'] =  'Content Type is not valid';
        return response()->json($response);
      }
        $postdata = $request->all();

        $perfiostransactionid = $postdata['perfiosTransactionId'];
        $prolitustxnid = $postdata['clientTransactionId'];
        $status = $postdata['status'];
        $err_code = $postdata['errorCode'];
        $err_msg = $postdata['errorMessage'];
        if (strtolower($status) != 'completed') {
          $err_detail = $postdata['financialYearErrorDetails'] ?? ($postdata['errorDetailsForFinancialYear'] ?? $err_msg);
          $logError = array(
            'perfios_log_id' => $perfiostransactionid,
            'req_file' => '',
            'res_file' => base64_encode($err_detail),
            'url' => '',
            'status' => 'fail'
          );
          FinanceModel::insertPerfios($logError,'biz_perfios_log');
          $response['message'] =  $err_msg ?? "Some error occured. While Parsing errorMessage";
          return response()->json($response);
        }
        $perfios_data = FinanceModel::getPerfiosData($perfiostransactionid);
        if (empty($perfios_data)) {
          $response['message'] = "Perfios Transaction Id is not valid.";
          return response()->json($response);
        }
        $appId = $perfios_data['app_id'];
        $final = $this->_getFinanceReport($perfiostransactionid, $prolitustxnid, $appId);
        if ($final['status'] != 'success') {
          $logError = array(
            'perfios_log_id' => $perfiostransactionid,
            'req_file' => '',
            'res_file' => base64_encode($final['message']),
            'url' => '',
            'status' => 'fail'
          );
          FinanceModel::insertPerfios($logError,'biz_perfios_log');
          $response['message'] = $final['message'] ?? "Some error occured.";
        }else{
          $response['status'] = "success";
          $response['message'] = "success";
        }
        return response()->json($response);
    }else{
      return response()->json($response);
    }
    
  }

  public function bsa_callback(Request $request){
    $response = array(
      'status' => 'fail',
      'message' => 'Request method not allowed',
    );

    // $dataString = json_encode($request->toArray());
    // Mail::raw($dataString, function ($message) {
    //   $message->to(['sudesh.kumar@zuron.in','amit.suman@zuron.in','hirdesh@zuron.in'])
    //     ->subject('BSA JSON FILE');
    // });


    $headers = getallheaders();
    if ($request->isMethod('post')) {
      $content_type = $headers['Content-Type'];
      if ($content_type != 'application/x-www-form-urlencoded') {
        $response['message'] =  'Content Type is not valid';
        return response()->json($response);
      }
        $postdata = $request->all();

        $perfiostransactionid = $postdata['perfiosTransactionId'];
        $prolitustxnid = $postdata['clientTransactionId'];
        $status = $postdata['status'];
        $err_code = $postdata['errorCode'];
        $err_msg = $postdata['errorMessage'];
        if (strtolower($status) != 'completed') {
          $response['message'] =  $err_msg ?? "Some error occured.";
          $logError = array(
            'perfios_log_id' => $perfiostransactionid,
            'req_file' => '',
            'res_file' => base64_encode($response['message']),
            'url' => '',
            'status' => 'fail'
          );
          FinanceModel::insertPerfios($logError,'biz_perfios_log');
          return response()->json($response);
        }

        $perfios_data = FinanceModel::getPerfiosData($perfiostransactionid);
        if (empty($perfios_data)) {
          $response['message'] = "Perfios Transaction Id is not valid.";
          return response()->json($response);
        }
        $appId = $perfios_data['app_id'];
        $final = $this->_getBankReport($perfiostransactionid, $prolitustxnid, $appId);
        if ($final['status'] != 'success') {
          $logError = array(
            'perfios_log_id' => $perfiostransactionid,
            'req_file' => '',
            'res_file' => isset($final['message']) ? base64_encode($final['message']) : '',
            'url' => '',
            'status' => 'fail'
          );
          FinanceModel::insertPerfios($logError,'biz_perfios_log');
          $response['message'] = $final['message'] ?? "Some error occured.";
        }else{
          $response['status'] = "success";
          $response['message'] = "success";
        }
        return response()->json($response);
    }else{
      return response()->json($response);
    }
    
  }

  private function getToUploadPath($appId, $type = 'banking'){
      $touploadpath = Storage::path('public/user/docs/'.$appId);
      if(!Storage::exists('public/user/docs/' .$appId)) {
          Storage::makeDirectory('public/user/docs/' .$appId.'/banking', 0777, true);
          Storage::makeDirectory('public/user/docs/' .$appId.'/finance', 0777, true);
          $touploadpath = Storage::path('public/user/docs/' .$appId);
      }
      $touploadpath .= ($type == 'banking' ? '/banking' : '/finance');
      $defaultPath = Storage::path('');
      return str_replace($defaultPath, '', $touploadpath);
  }

  private function getLatestFileName($appId, $fileType='banking', $extType='json'){
      $scanpath = $this->getToUploadPath($appId, $fileType);
      if (Storage::exists($scanpath) == false) {
        $files = [];
      }else{
        $files = Storage::allFiles($scanpath, SCANDIR_SORT_DESCENDING);
      }
      $files = array_diff($files, [".", ".."]);
      natsort($files);
      $files = array_reverse($files, false);
      $filename = "";
      if (!empty($files)) {
        foreach ($files as $key => $file) {
          $fileparts = pathinfo($file);
          $filename = $fileparts['filename'];
          $ext = $fileparts['extension'];
          if ($extType == $ext) {
             break;
          }
        }
      }
                
      $included_no = preg_replace('#[^0-9]+#', '', $filename);
      $file_no = substr($included_no, strlen($appId));
      if (empty($file_no) && empty($filename)) {
        $new_file = $appId.'_'.$fileType.".$extType";
        $curr_file = '';
      }else{
        $file_no = (int)$file_no + 1;
        $curr_file = $filename.".$extType";
        $new_file = $appId.'_'.$fileType.'_'.$file_no . ".$extType";
      }
      $fileArr = array(
        'curr_file' => $curr_file,
        'new_file' => $new_file,
      );
      return $fileArr;
    }


	private function _getFinanceReport($perfiostransactionid, $prolitus_txn, $appId) {
        $biz_perfios_id = $perfiostransactionid;
    	  $perfios = new Perfios_lib();
        $apiVersion = '2.1';
        $vendorId = 'capsave';
        $nameArr = $this->getLatestFileName($appId, 'finance', 'xlsx');
        $file_name = $nameArr['new_file'];
        $req_arr = array(
              'apiVersion' => $apiVersion,
              'vendorId' => $vendorId,
              'perfiosTransactionId' => $perfiostransactionid,
              'reportType' => 'xlsx',
              'txnId' => $prolitus_txn,
        );
        $fileFullPath = $this->getToUploadPath($appId, 'finance').'/'.$file_name;
        if ($this->download_xlsx) {
          $final_res = $perfios->api_call(Perfios_lib::GET_STMT, $req_arr);
          if ($final_res['status'] != 'success') {
              $final_res['api_type'] = Perfios_lib::GET_STMT;
              $final_res['prolitusTransactionId'] = $prolitus_txn;
              $final_res['perfiosTransactionId'] = $perfiostransactionid;
              return $final_res;
          }else{
          	Storage::put($fileFullPath, $final_res['result']);
          }
        }
        $file= Storage::url($fileFullPath);
        $req_arr['reportType'] = 'json';
        $final_res = $perfios->api_call(Perfios_lib::GET_STMT, $req_arr);
       	$final_res['api_type'] = Perfios_lib::GET_STMT;
        $final_res['file_url'] = $file;
	      $final_res['prolitusTransactionId'] = $prolitus_txn;
	      $final_res['perfiosTransactionId'] = $perfiostransactionid;
        if ($final_res['status'] == 'success') {
          $final_res['result'] = base64_encode($final_res['result']);
          $nameArr = $this->getLatestFileName($appId, 'finance', 'json');
          $json_file_name = $nameArr['new_file'];
          Storage::put($this->getToUploadPath($appId, 'finance') .'/'.$json_file_name, $final_res['result']);
          $log_data = array(
            'status' => $final_res['status'],
            'updated_by' => NULL,
          );
          FinanceModel::updatePerfios($log_data,'biz_perfios',$biz_perfios_id,'biz_perfios_id');
        }
        return $final_res;
    }


    private function _getBankReport($perfiostransactionid, $prolitus_txn, $appId) {
        $biz_perfios_id = $perfiostransactionid;
        $bsa = new Bsa_lib();
        $apiVersion = '2.1';
        $vendorId = 'capsave';
        $nameArr = $this->getLatestFileName($appId, 'banking', 'xlsx');
        $file_name = $nameArr['new_file'];
        $req_arr = array(
            'perfiosTransactionId' => $perfiostransactionid,
            'types' => 'xlsx',
        );
        $fileFullPath = $this->getToUploadPath($appId, 'banking').'/'.$file_name;
        if ($this->download_xlsx) {
          $final_res = $bsa->api_call(Bsa_lib::GET_REP, $req_arr);
          if ($final_res['status'] != 'success') {
              $final_res['api_type'] = Bsa_lib::GET_REP;
              $final_res['prolitusTransactionId'] = $prolitus_txn;
              $final_res['perfiosTransactionId'] = $perfiostransactionid;
              return $final_res;
          }else{
            Storage::put($fileFullPath, $final_res['result']);
          } 
        }
        $file= Storage::url($fileFullPath);
        $req_arr['types'] = 'json';
        $final_res = $bsa->api_call(Bsa_lib::GET_REP, $req_arr);
        $final_res['api_type'] = Bsa_lib::GET_REP;
        $final_res['file_url'] = $file;
        $final_res['prolitusTransactionId'] = $prolitus_txn;
        $final_res['perfiosTransactionId'] = $perfiostransactionid;
        if ($final_res['status'] == 'success') {
          $final_res['result'] = base64_encode($final_res['result']);
          $nameArr = $this->getLatestFileName($appId, 'banking', 'json');
          $json_file_name = $nameArr['new_file'];
          Storage::put($this->getToUploadPath($appId, 'banking') .'/'.$json_file_name, $final_res['result']);
          $log_data = array(
            'status' => $final_res['status'],
            'updated_by' => NULL,
          );
          FinanceModel::updatePerfios($log_data,'biz_perfios',$biz_perfios_id,'biz_perfios_id');
        }
        return $final_res;
    }

    private function _setResponse($response, $statusCode){
      $result = $response;
      $result['status_code'] = $statusCode;
      $logdata = json_encode($result);
      $this->logdata($logdata, 'F', 'error.log');
      return response($response, $statusCode)
                  ->header('Content-Type', 'application/json');
    }


    public function logdata($data, $w_mode = 'D', $w_filename = '', $w_folder = '') {
      list($year, $month, $date, $hour) = explode('-', strtolower(date('Y-M-dmy-H')));
      $main_dir = 'public/user/';
     /*$year_dir = $main_dir . "$year/";
      $month_dir = $year_dir . "$month/";
      $date_dir = $month_dir . "$date/";
      $hour_dir = $date_dir . "$hour/";

      if (!file_exists($year_dir)) {
        mkdir($year_dir, 0777, true);
      }
      if (!file_exists($month_dir)) {
        mkdir($month_dir, 0777, true);
      }
      if (!file_exists($date_dir)) {
        mkdir($date_dir, 0777, true);
      }
      if (!file_exists($hour_dir)) {
        mkdir($hour_dir, 0777, true);
      }*/
      $hour_dir = $main_dir;
      $data = is_array($data) || is_object($data) ? json_encode($data) : $data;
      $data = base64_encode($data);
      if (strtolower($w_mode) == 'f') {
        $final_dir = $hour_dir;
        $filepath = explode('/', $w_folder);
        foreach ($filepath as $value) {
          $final_dir .= "$value/";
          if (!Storage::exists($final_dir)) {
            Storage::makeDirectory($final_dir, 0777, true);
          }
        }
        $my_file = $final_dir . $w_filename;
        return Storage::put($my_file,PHP_EOL . $data . PHP_EOL);
      } else {
        $my_file = $hour_dir . date('ymd') . '.log';
        $time = date('H:i:s');
        Storage::append($my_file,PHP_EOL . 'Log ' . $time);
        return Storage::append($my_file,PHP_EOL . $data . PHP_EO);;
      }
  }

  public function changeFinancialYear(Request $request) {
    if($request->isMethod('post')) {
      $response = [
        'status' => 'fail',
        'message' => "Some error occured, Please try again later."
      ];
      
      $appId = $request->get('app_id');
      $year = explode('-', $request->get('year'));

      
      if (count($year) != 3 || ($year[0]-$year[1]) != ($year[1]-$year[2]) || ($year[0]-$year[1]) != 1) {

        Session::flash('error', trans('Three consecutive years in Desc Order are required to Change the years'));
        return redirect()->route('api_change_year');        
        // $response['message'] = 'Three consecutive years in Desc Order are required to Change the years';
        // return $response;
      }
      
      $nameArr = $this->getLatestFileName($appId, 'finance', 'json');
      if (empty($nameArr['curr_file'])) {
        Session::flash('error', trans('No file found to update the year for this application'));
        return redirect()->route('api_change_year');                
        // $response['message'] = 'No file found to update the year for this application';
        // return $response;
      }
      $toUploadPath = $this->getToUploadPath($appId, 'finance');
      $contents = json_decode(base64_decode(Storage::get($toUploadPath.'/'. $nameArr['curr_file'])),true);
      $fy = $contents['FinancialStatement']['FY'] ?? [];
      if (empty($fy)) {
        Session::flash('error', trans('No Content found to update the year'));
        return redirect()->route('api_change_year'); 
        // $response['message'] = 'No Content found to update the year';
        // return $response;
      }
      foreach ($fy as $key => $fyData) {
        $fy[$key]['year'] = $year[$key];
      }
      if (count($fy) == 2) {
        $fy['2'] = $fyData;
      }
      $contents['FinancialStatement']['FY'] = $fy;
      $json_file_name = $nameArr['new_file'];
      $changeContent = base64_encode(json_encode($contents));
      Storage::put($toUploadPath .'/'.$json_file_name, $changeContent);
        Session::flash('message', trans('Year changes successfully'));
        return redirect()->route('api_change_year'); 
      // $response['status'] = 'success';
      // $response['message'] = 'Year changes successfully';
      // return $response;
    }
    return view('change_financial_yr');
  }
}



 ?>