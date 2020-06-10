<?php 

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Event;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use App\Inv\Repositories\Models\FinanceModel;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Payment;
use App\Inv\Repositories\Models\Lms\Refund\RefundReq;
use App\Libraries\Bsa_lib;
use App\Libraries\Perfios_lib;
use Storage;

/**
 * 
 */
class ApiController
{
	//protected $secret_key = "Rentalpha__vkzARY";
  protected $secret_key = "0702f2c9c1414b70efc1e69f2ff31af0";
  protected $download_xlsx = true;
  protected $voucherNo = 0;
  protected $selectedTxnData = [];
  protected $selectedPaymentData = [];
	function __construct(){
		
	}

  private function createJournalData($journalData, $batch_no) {
    $journalPayments = [];
    foreach ($journalData as $jrnls) {
      $accountDetails = $jrnls->userRelation->companyBankDetails ?? NULL;
      if (empty($accountDetails)) {
        continue;
      }
      $is_interest_or_charge = false;
      if ($jrnls->transType->id == config('lms.TRANS_TYPE.INTEREST') || $jrnls->transType->chrg_master_id != 0) {
        $is_interest_or_charge = true;
      }
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

        if ($jrnls->trans_type == config('lms.TRANS_TYPE.WAVED_OFF')) {
          if ($parentRecord->is_invoice_generated == 0 || ($invoice_date > $jrnls->trans_date && $parentRecord->is_invoice_generated == 1)) {
            continue;
          }
        }
      }
      if (is_null($jrnls->parent_trans_id) && $jrnls->entry_type == 0 && $jrnls->getOutstandingAttribute() > 0 && $is_interest_or_charge) {
       continue;
      }
      $this->voucherNo = $this->voucherNo + 1;
      $entry_type = $jrnls->entry_type == 1 ? 'Credit' : 'Debit';
      $this->selectedTxnData[] = $jrnls->trans_id;
      $JournalRow = [
          'batch_no' =>  $batch_no,
          'transactions_id' =>  $jrnls->trans_id,
          'voucher_no' => $this->voucherNo,
          'voucher_type' => 'Journal',
          'voucher_date' => $jrnls->trans_date,
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
          'cheque_amount' =>  '',
          'cross_using' => '',
          'mode_of_pay' => '',
          'inst_no' =>  NULL,
          'inst_date' =>  NULL,
          'favoring_name' =>  NULL,
          'remarks' => NULL,
          'generated_by' => '0',
          'narration' => 'Being '.$trans_type_name.' towards Invoice No '. $invoice_no .' & Batch no '. $batch_no,
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
            $totalGST = 18;
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
              $gst_trans_type = 'SGST + CGST';
              break;
            case 'cgst':
              $gst_trans_type = 'SGST + CGST';
              break;
            case 'igst':
              $gst_trans_type = 'IGST';
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
              $gst_trans_type = 'SGST + CGST';
              break;
            case 'cgst':
              $gst_trans_type = 'SGST + CGST';
              break;
            case 'igst':
              $gst_trans_type = 'IGST';
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
     if ($jrnls->trans_type == config('lms.TRANS_TYPE.REVERSE')) {
        $reversalPayment = $this->createReversalData($jrnls, $batch_no);
        $journalPayments = array_merge($journalPayments, $reversalPayment);
     }
    }
    return $journalPayments;
  }

  private function createReversalData($rvrslRow, $batch_no) {
    $reversalPayment = [];
    $settledTransactoionsFromReversal = $rvrslRow->getReversalParent->getSettledTxns ?? [];
    if (!empty($settledTransactoionsFromReversal)) {
      foreach ($settledTransactoionsFromReversal as  $rvrsl) {
        $accountDetails = $rvrsl->userRelation->companyBankDetails ?? NULL;
        if (empty($accountDetails)) {
          continue;
        }
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
        $this->selectedPaymentData[] = $rvrsl->payment_id;
        $reversalRow = [
            'batch_no' =>  $batch_no,
            'transactions_id' =>  $rvrsl->trans_id,
            'voucher_no' => $this->voucherNo,
            'voucher_type' => 'Journal',
            'voucher_date' => $rvrsl->trans_date,
            'is_debit_credit' => $rvrsl->entry_type == 1 ? 'Credit' : 'Debit',
            'trans_type' =>  $trans_type_name,
            'invoice_no' =>   $invoice_no,
            'invoice_date' =>  $invoice_date,
            'ledger_name' =>  $bizName,
            'amount' =>  $rvrsl->amount,
            'ref_no' =>  $invoice_no,
            'ref_amount' =>  $rvrsl->amount,
            'acc_no' =>  $accountDetails->acc_no ?? '',
            'ifsc_code' =>  $accountDetails->ifsc_code ?? '',
            'bank_name' =>  $accountDetails->bank->bank_name ?? '',
            'cheque_amount' =>  '',
            'cross_using' => '',
            'mode_of_pay' => '',
            'inst_no' =>  NULL,
            'inst_date' =>  NULL,
            'favoring_name' =>  NULL,
            'remarks' => NULL,
            'generated_by' => '0',
            'narration' => 'Being '.$trans_type_name.' towards Invoice No '. $invoice_no .' & Batch no '. $batch_no,
       ];
       $reversalPayment[] = $reversalRow;
      }
    }
    return $reversalPayment;
  }

  private function createRefundData($refundData, $batch_no) {
    $refundPayment = [];
    foreach($refundData as $rfnd){
      $this->voucherNo = $this->voucherNo + 1;
      $accountDetails = $rfnd->userRelation->companyBankDetails ?? NULL;
      if (empty($accountDetails)) {
        continue;
      }
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
      $this->selectedTxnData[] = $rfnd->trans_id;
      $CustomerRow = [
          'batch_no' =>  $batch_no,
          'transactions_id' =>  $rfnd->trans_id,
          'voucher_no' => $this->voucherNo,
          'voucher_type' => 'Payment',
          'voucher_date' => $rfnd->trans_date,
          'is_debit_credit' =>  'Debit',
          'trans_type' =>  $trans_type_name,
          'invoice_no' =>   $invoice_no,
          'invoice_date' =>  $invoice_date,
          'ledger_name' =>  $userName,
          'amount' =>  $rfnd->amount,
          'ref_no' =>  $invoice_no,
          'ref_amount' =>  $rfnd->amount,
          'acc_no' =>  '',
          'ifsc_code' =>  '',
          'bank_name' =>  '',
          'cheque_amount' =>  '',
          'cross_using' => '',
          'mode_of_pay' => '',
          'inst_no' =>  NULL,
          'inst_date' =>  NULL,
          'favoring_name' =>  NULL,
          'remarks' => NULL,
          'generated_by' => '0',
          'narration' => 'Being '.$trans_type_name.' towards Invoice No '. $invoice_no .' & Batch no '. $batch_no,
     ];
     $refundPayment[] = $CustomerRow;
     $BankRow = [
          'batch_no' =>  $batch_no,
          'transactions_id' =>  NULL,
          'voucher_no' => $this->voucherNo,
          'voucher_type' => 'Payment',
          'voucher_date' => NULL,
          'is_debit_credit' =>  'Credit',
          'trans_type' =>  '',
          'invoice_no' =>   '',
          'invoice_date' =>  NULL,
          'ledger_name' =>  $accountDetails->bank->bank_name,
          'amount' =>  $rfnd->amount,
          'ref_no' =>  $invoice_no,
          'ref_amount' =>  $rfnd->amount,
          'acc_no' =>  $accountDetails->acc_no ?? '',
          'ifsc_code' =>  $accountDetails->ifsc_code ?? '',
          'bank_name' =>  $accountDetails->bank->bank_name ?? '',
          'cheque_amount' =>  '',
          'cross_using' => '',
          'mode_of_pay' => 'e-Fund-Transfer',
          'inst_no' =>  $inst_no,
          'inst_date' =>  $inst_date,
          'favoring_name' =>  $userName,
          'remarks' => NULL,
          'generated_by' => '1',
          'narration' => 'Being '.$trans_type_name.' towards Invoice No '. $invoice_no .' & Batch no '. $batch_no,
     ];
     $refundPayment[] = $BankRow;
    }
    return $refundPayment;
  }  

  private function createDisbursalData($disbursalData, $batch_no) {
    $disbursalPayment = [];
    foreach($disbursalData as $dsbrsl){
      $this->voucherNo = $this->voucherNo + 1;
      $accountDetails = $dsbrsl->userRelation->companyBankDetails ?? NULL;
      if (empty($accountDetails)) {
        continue;
      }
      $userName = $dsbrsl->user->biz_name;
      $invoice_no = $dsbrsl->invoiceDisbursed->invoice->invoice_no ?? NULL;
      $invoice_date = $dsbrsl->invoiceDisbursed->invoice->invoice_date ?? NULL;
      $disburse_amt = $dsbrsl->invoiceDisbursed->disburse_amt;
      $total_interest = $dsbrsl->invoiceDisbursed->total_interest;
      $cheque_amount = round($disburse_amt - $total_interest, 2);
      $this->selectedTxnData[] = $dsbrsl->trans_id;
      $CustomerRow = [
              'batch_no' =>  $batch_no,
              'transactions_id' =>  $dsbrsl->trans_id,
              'voucher_no' => $this->voucherNo,
              'voucher_type' => 'Payment',
              'voucher_date' => $dsbrsl->trans_date,
              'is_debit_credit' =>  'Debit',
              'trans_type' =>  $dsbrsl->getTransNameAttribute(),
              'invoice_no' =>   $invoice_no,
              'invoice_date' =>  $invoice_date,
              'ledger_name' =>  $userName,
              'amount' =>  $disburse_amt,
              'ref_no' =>  $invoice_no,
              'ref_amount' =>  $disburse_amt,
              'acc_no' =>  '',
              'ifsc_code' =>  '',
              'bank_name' =>  '',
              'cheque_amount' =>  '',
              'cross_using' => '',
              'mode_of_pay' => '',
              'inst_no' =>  NULL,
              'inst_date' =>  NULL,
              'favoring_name' =>  NULL,
              'remarks' => NULL,
              'generated_by' => '0',
              'narration' => 'Being  Payment Disbursed towards Invoice No '. $invoice_no .' & Batch no '. $batch_no,
     ];
     $disbursalPayment[] = $CustomerRow;
     $BankRow = [
              'batch_no' =>  $batch_no,
              'transactions_id' =>  NULL,
              'voucher_no' => $this->voucherNo,
              'voucher_type' => 'Payment',
              'voucher_date' => NULL,
              'is_debit_credit' =>  'Credit',
              'trans_type' =>  $dsbrsl->getTransNameAttribute(),
              'invoice_no' =>   $invoice_no,
              'invoice_date' =>  $invoice_date,
              'ledger_name' =>  $accountDetails->bank->bank_name,
              'amount' =>  $cheque_amount,
              'ref_no' =>  $invoice_no,
              'ref_amount' =>  $cheque_amount,
              'acc_no' =>  $accountDetails->acc_no ?? '',
              'ifsc_code' =>  $accountDetails->ifsc_code ?? '',
              'bank_name' =>  $accountDetails->bank->bank_name ?? '',
              'cheque_amount' =>  $cheque_amount,
              'cross_using' => '',
              'mode_of_pay' => 'e-Fund-Transfer',
              'inst_no' =>  $dsbrsl->invoiceDisbursed->disbursal->tran_id ?? NULL,
              'inst_date' =>  $dsbrsl->invoiceDisbursed->disbursal->funded_date ?? NULL,
              'favoring_name' =>  $userName,
              'remarks' => NULL,
              'generated_by' => '1',
              'narration' => 'Being  Payment Disbursed towards Invoice No '. $invoice_no .' & Batch no '. $batch_no,
     ];
     $disbursalPayment[] = $BankRow;
     if (!empty($total_interest) && $total_interest > 0) {
       $InterestRow = [
              'batch_no' =>  $batch_no,
              'transactions_id' =>  NULL,
              'voucher_no' => $this->voucherNo,
              'voucher_type' => 'Payment',
              'voucher_date' => NULL,
              'is_debit_credit' =>  'Credit',
              'trans_type' =>  'Interest',
              'invoice_no' =>   $invoice_no,
              'invoice_date' =>  $invoice_date,
              'ledger_name' =>  'Interest',
              'amount' =>  $total_interest,
              'ref_no' =>  $invoice_no,
              'ref_amount' =>  $total_interest,
              'acc_no' =>  '',
              'ifsc_code' =>  '',
              'bank_name' =>  '',
              'cheque_amount' =>  '',
              'cross_using' => '',
              'mode_of_pay' => '',
              'inst_no' =>  NULL,
              'inst_date' =>  NULL,
              'favoring_name' =>  NULL,
              'remarks' => NULL,
              'generated_by' => '1',
              'narration' => 'Being Interest Booked towards Invoice No '. $invoice_no .' & Batch no '. $batch_no,
     ];
     $disbursalPayment[] = $InterestRow;
     }
    }
    return $disbursalPayment;
  }

  private function createReceiptData($receiptData, $batch_no) {
    $receiptPayment = [];
    foreach($receiptData as $rcpt){
     $this->voucherNo = $this->voucherNo + 1;
     $settledTransactoions =  $rcpt->getSettledTxns;
     $userName = $rcpt->user->biz_name;
     $accountDetails = $rcpt->userRelation->companyBankDetails ?? NULL;
     if (empty($accountDetails)) {
        continue;
     }
     $inst_no = $rcpt->refundReq->tran_no ?? NULL;
     $inst_date = $rcpt->refundReq->actual_refund_date ?? NULL;
     $this->selectedPaymentData[] = $rcpt->payment_id;
     switch ($rcpt->payment_type) {
       case '1':
         $mode_of_pay = 'e-Fund-Transfer';
         break;
      case '2':
         $mode_of_pay = 'Cheque';
         break;
      case '3':
         $mode_of_pay = 'Nach';
         break; 
      case '4':
         $mode_of_pay = 'Cash';
         break; 
      default:
         $mode_of_pay = 'e-Fund-Transfer';
         break;
     }
     $BankRow = [
              'batch_no' =>  $batch_no,
              'transactions_id' =>  NULL,
              'voucher_no' => $this->voucherNo,
              'voucher_type' => 'Receipt',
              'voucher_date' => $rcpt->date_of_payment,
              'is_debit_credit' =>  'Debit',
              'trans_type' =>  'Re-Payment',
              'invoice_no' =>   '',
              'invoice_date' =>  NULL,
              'ledger_name' =>  $accountDetails->bank->bank_name,
              'amount' =>  $rcpt->amount,
              'ref_no' =>  '',
              'ref_amount' =>  $rcpt->amount,
              'acc_no' =>  $accountDetails->acc_no ?? '',
              'ifsc_code' =>  $accountDetails->ifsc_code ?? '',
              'bank_name' =>  $accountDetails->bank->bank_name ?? '',
              'cheque_amount' =>  '',
              'cross_using' => $rcpt->payment_type == 2 ? 'a/c payee' : NULL,
              'mode_of_pay' => $mode_of_pay,
              'inst_no' =>  $inst_no,
              'inst_date' =>  $inst_date,
              'favoring_name' =>  $userName,
              'remarks' => NULL,
              'generated_by' => '1',
              'narration' => 'Being Repayment towards Batch no '. $batch_no,
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
          $this->selectedTxnData[] = $stldTxn->trans_id;
          $settledRow = [
              'batch_no' =>  $batch_no,
              'transactions_id' =>  $stldTxn->trans_id,
              'voucher_no' => $this->voucherNo,
              'voucher_type' => 'Receipt',
              'voucher_date' => $stldTxn->trans_date,
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
              'inst_date' =>  NULL,
              'favoring_name' =>  NULL,
              'remarks' => NULL,
              'generated_by' => '0',
              'narration' => 'Being '.$trans_type_name.' towards Invoice No '. $invoice_no .' & Batch no '. $batch_no,
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

  public function tally_entry() {
    $response = array(
      'status' => 'failure',
      'message' => 'Request method not allowed to execute the script.',
    );
    if (strpos(php_sapi_name(), 'cli') !== false) {
        $response['sapi'] = php_sapi_name();
        return $this->_setResponse($response, 405);
    }
    $latestRecord = \DB::select('select * from rta_tally_entry order by tally_entry_id DESC limit 1');
    $lastVoucherNo = 0;
    if (!empty($latestRecord)) {
      $lastVoucherNo = $latestRecord[0]->voucher_no ?? 0;
    }
    $this->voucherNo = $this->voucherNo + 1;
    $batch_no = _getRand(15);
    $where = ['is_posted_in_tally' => '0'];
    $journalData = Transactions::getJournalTxnTally($where);
    $disbursalData = Transactions::getDisbursalTxnTally($where);
    $refundData = Transactions::getRefundTxnTally($where);
    $receiptData = Payment::getPaymentReceipt($where);

    $journalArray = $this->createJournalData($journalData, $batch_no);
    $disbursalArray = $this->createDisbursalData($disbursalData, $batch_no);
    $receiptArray = $this->createReceiptData($receiptData, $batch_no);
    $refundArray = $this->createRefundData($refundData, $batch_no);
    $tally_data = array_merge($disbursalArray, $journalArray , $receiptArray, $refundArray);
    try {
        if (empty($tally_data)) {
           $response['message'] =  'No Records are selected to Post in tally.';
           return $response;
        }
        $res = \DB::table('tally_entry')->insert($tally_data);
    } catch (\Exception $e) {
        $errorInfo  = $e->errorInfo;
        $res = $errorInfo;
    }
    $selectedTxnData = $this->selectedTxnData;
    $selectedPaymentData = $this->selectedPaymentData;
    if ($res === true) {
      $totalTxnRecords = 0;
      if (!empty($selectedTxnData)) {
        $totalTxnRecords = \DB::update('update rta_transactions set is_posted_in_tally = 1 where trans_id in(' . implode(', ', $selectedTxnData) . ')');
      }
      $totalPaymentsRecords = 0;
      if (!empty($selectedPaymentData)) {
        $totalPaymentsRecords = \DB::update('update rta_payments set is_posted_in_tally = 1 where payment_id in(' . implode(', ', $selectedPaymentData) . ')');
      }
      $totalRecords = $totalTxnRecords + $totalPaymentsRecords;
      $recordsTobeInserted = count($selectedTxnData) + count($selectedPaymentData);
      if (empty($totalRecords)) {
        $response['message'] =  'Some error occured. No Record can be posted in tally.';
      }else{
        $response['status'] = 'success';
        $batchData = [
          'batch_no' => $batch_no,
          'record_cnt' => $recordsTobeInserted,
          'created_at' => date('Y-m-d H:i:s'),
        ];
        $tally_inst_data = FinanceModel::dataLogger($batchData, 'tally');
        $response['message'] =  ($recordsTobeInserted > 1 ? $recordsTobeInserted .' Records inserted successfully' : '1 Record inserted.');
      }
    }else{
      $response['message'] =  ($res[2] ?? 'DB error occured.').' No Record can be posted in tally.';
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
      $myfile = fopen(storage_path('app/public/user').'/'.$file_name, "w");
      \File::put(storage_path('app/public/user').'/'.$file_name, file_get_contents($result['pdfDownloadLink'])); 
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
		$headers = getallheaders();
		if ($request->isMethod('post')) {
			$content_type = $headers['Content-Type'];
			if ($content_type != 'application/x-www-form-urlencoded') {
				$response['message'] =  'Content Type is not valid';
				return print(json_encode($response));
			}
    		$postdata = $request->all();

    		$perfiostransactionid = $postdata['perfiosTransactionId'];
    		$prolitustxnid = $postdata['clientTransactionId'];
    		$status = $postdata['status'];
    		$err_code = $postdata['errorCode'];
    		$err_msg = $postdata['errorMessage'];
    		if (strtolower($status) != 'completed') {
    			$response['message'] =  $err_msg ?? "Some error occured.";
    			return print(json_encode($response));
    		}
    		$perfios_data = FinanceModel::getPerfiosData($perfiostransactionid);
    		if (empty($perfios_data)) {
    			$response['message'] = "Perfios Transaction Id is not valid.";
    			return print(json_encode($response));
    		}
    		$appId = $perfios_data['app_id'];
    		$final = $this->_getFinanceReport($perfiostransactionid, $prolitustxnid, $appId);
    		if ($final['status'] != 'success') {
    			$response['message'] = $final['message'] ?? "Some error occured.";
    		}else{
    			$response['status'] = "success";
    			$response['message'] = "success";
    		}
    		return print(json_encode($response));
		}else{
			return print(json_encode($response));
		}
		
	}

	public function bsa_callback(Request $request){
		$response = array(
			'status' => 'fail',
			'message' => 'Request method not allowed',
		);
		$headers = getallheaders();
		if ($request->isMethod('post')) {
			$content_type = $headers['Content-Type'];
			if ($content_type != 'application/x-www-form-urlencoded') {
				$response['message'] =  'Content Type is not valid';
				return print(json_encode($response));
			}
    		$postdata = $request->all();

    		$perfiostransactionid = $postdata['perfiosTransactionId'];
    		$prolitustxnid = $postdata['clientTransactionId'];
    		$status = $postdata['status'];
    		$err_code = $postdata['errorCode'];
    		$err_msg = $postdata['errorMessage'];
    		if (strtolower($status) != 'completed') {
    			$response['message'] =  $err_msg ?? "Some error occured.";
    			return print(json_encode($response));
    		}

    		$perfios_data = FinanceModel::getPerfiosData($perfiostransactionid);
    		if (empty($perfios_data)) {
    			$response['message'] = "Perfios Transaction Id is not valid.";
    			return print(json_encode($response));
    		}
    		$appId = $perfios_data['app_id'];
    		$final = $this->_getBankReport($perfiostransactionid, $prolitustxnid, $appId);
    		if ($final['status'] != 'success') {
    			$response['message'] = $final['message'] ?? "Some error occured.";
    		}else{
    			$response['status'] = "success";
    			$response['message'] = "success";
    		}
    		return print(json_encode($response));
		}else{
			return print(json_encode($response));
		}
		
	}

  private function getToUploadPath($appId, $type = 'banking'){
      $touploadpath = storage_path('app/public/user/docs/'.$appId);
      if(!Storage::exists('public/user/docs/' .$appId)) {
          Storage::makeDirectory('public/user/docs/' .$appId.'/banking', 0777, true);
          Storage::makeDirectory('public/user/docs/' .$appId.'/finance', 0777, true);
          $touploadpath = storage_path('public/user/docs/' .$appId);
      }
      return $touploadpath .= ($type == 'banking' ? '/banking' : '/finance');
  }

  private function getLatestFileName($appId, $fileType='banking', $extType='json'){
      $scanpath = $this->getToUploadPath($appId, $fileType);
      if (is_dir($scanpath) == false) {
        $files = [];
      }else{
        $files = scandir($scanpath, SCANDIR_SORT_DESCENDING);
      }
      $files = array_diff($files, [".", ".."]);
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
      $file_no = str_replace($appId, '', $included_no);
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
        if ($this->download_xlsx) {
          $final_res = $perfios->api_call(Perfios_lib::GET_STMT, $req_arr);
          if ($final_res['status'] != 'success') {
              $final_res['api_type'] = Perfios_lib::GET_STMT;
              $final_res['prolitusTransactionId'] = $prolitus_txn;
              $final_res['perfiosTransactionId'] = $perfiostransactionid;
              return $final_res;
          }else{
          	 $myfile = fopen($this->getToUploadPath($appId, 'finance').'/'.$file_name, "w");
          	 \File::put($this->getToUploadPath($appId, 'finance').'/'.$file_name, $final_res['result']);
          }
        }
        $file= url("storage/user/docs/$appId/finance/". $file_name);
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
          $myfile = fopen($this->getToUploadPath($appId, 'finance') .'/'.$json_file_name, "w");
          \File::put($this->getToUploadPath($appId, 'finance') .'/'.$json_file_name, $final_res['result']);
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
        $perfios = new Perfios_lib();
        $apiVersion = '2.1';
        $vendorId = 'capsave';
        $nameArr = $this->getLatestFileName($appId, 'banking', 'xlsx');
        $file_name = $nameArr['new_file'];
        $req_arr = array(
            'perfiosTransactionId' => $perfiostransactionid,
            'types' => 'xlsx',
        );
        if ($this->download_xlsx) {
          $final_res = $bsa->api_call(Bsa_lib::GET_REP, $req_arr);
          if ($final_res['status'] != 'success') {
              $final_res['api_type'] = Bsa_lib::GET_REP;
              $final_res['prolitusTransactionId'] = $prolitus_txn;
              $final_res['perfiosTransactionId'] = $perfiostransactionid;
              return $final_res;
          }else{
          	$myfile = fopen($this->getToUploadPath($appId, 'banking').'/'.$file_name, "w");
            \File::put($this->getToUploadPath($appId, 'banking').'/'.$file_name, $final_res['result']);
          } 
        }
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
          $myfile = fopen($this->getToUploadPath($appId, 'banking') .'/'.$json_file_name, "w");
          \File::put($this->getToUploadPath($appId, 'banking') .'/'.$json_file_name, $final_res['result']);
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
      $main_dir = storage_path('app/public/user/');
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
          if (!file_exists($final_dir)) {
            mkdir($final_dir, 0777, true);
          }
        }
        $my_file = $final_dir . $w_filename;
        $handle = fopen($my_file, 'w');
        return fwrite($handle, PHP_EOL . $data . PHP_EOL);
      } else {
        $my_file = $hour_dir . date('ymd') . '.log';
        $handle = fopen($my_file, 'a');
        $time = date('H:i:s');
        fwrite($handle, PHP_EOL . 'Log ' . $time);
        return fwrite($handle, PHP_EOL . $data . PHP_EOL);
      }
      return FALSE;
  }
}


 ?>