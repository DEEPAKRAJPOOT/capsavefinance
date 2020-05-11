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
      $this->voucherNo = $this->voucherNo + 1;
      $userName = $jrnls->user->f_name. ' ' . $jrnls->user->m_name .' '. $jrnls->user->l_name;
      $trans_type_name = $jrnls->getTransNameAttribute();
      $invoice_no = $jrnls->userinvoicetrans->getUserInvoice->invoice_no ?? NULL;
      $invoice_date = $jrnls->userinvoicetrans->getUserInvoice->created_at ?? NULL;
      if (empty($invoice_no)) {
          $invoice_no = $jrnls->invoiceDisbursed->invoice->invoice_no ?? NULL;
          $invoice_date = $jrnls->invoiceDisbursed->invoice->invoice_date ?? NULL;
      }
      $inst_no = $jrnls->refundReq->tran_no ?? NULL;
      $inst_date = $jrnls->refundReq->actual_refund_date ?? NULL;
      if (!empty($jrnls->parent_trans_id)) {
        $parentRecord  = $jrnls->getParentTxn();
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
          'inst_no' =>  '',
          'inst_date' =>  '',
          'favoring_name' =>  '',
          'remarks' => '',
          'narration' => 'Being '.$trans_type_name.' towards Invoice No '. $invoice_no .' & Batch no '. $batch_no,
     ];
     $gstData = [];
     if (!empty($jrnls->userinvoicetrans->getUserInvoice->invoice_no)) {
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
       $JournalRow['transactions_id'] = NULL;
       $JournalRow['is_debit_credit'] = 'Credit';
       $JournalRow['ref_no'] = $JournalRow['ref_no'] . '(Req Change)';
       $journalPayments[] = $JournalRow;
     }
    }
    return $journalPayments;
  }

  private function createRefundData($refundData, $batch_no) {
    $refundPayment = [];
    foreach($refundData as $rfnd){
      $this->voucherNo = $this->voucherNo + 1;
      $accountDetails = $rfnd->userRelation->companyBankDetails ?? NULL;
      if (empty($accountDetails)) {
        continue;
      }
      $userName = $rfnd->user->f_name. ' ' . $rfnd->user->m_name .' '. $rfnd->user->l_name;
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
          'inst_no' =>  '',
          'inst_date' =>  '',
          'favoring_name' =>  '',
          'remarks' => '',
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
          'remarks' => '',
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
      $userName = $dsbrsl->user->f_name. ' ' . $dsbrsl->user->m_name .' '. $dsbrsl->user->l_name;
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
              'inst_no' =>  '',
              'inst_date' =>  NULL,
              'favoring_name' =>  '',
              'remarks' => '',
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
              'remarks' => '',
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
              'inst_no' =>  '',
              'inst_date' =>  NULL,
              'favoring_name' =>  '',
              'remarks' => '',
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
     $userName = $rcpt->user->f_name. ' ' . $rcpt->user->m_name .' '. $rcpt->user->l_name;
     $accountDetails = $rcpt->userRelation->companyBankDetails ?? NULL;
     if (empty($accountDetails)) {
        continue;
     }
     $this->selectedPaymentData[] = $rcpt->payment_id;
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
              'cross_using' => '',
              'mode_of_pay' => 'e-Fund-Transfer',
              'inst_no' =>  '',
              'inst_date' =>  '',
              'favoring_name' =>  $userName,
              'remarks' => '',
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
              'inst_no' =>  '',
              'inst_date' =>  '',
              'favoring_name' =>  '',
              'remarks' => '',
              'narration' => 'Being '.$trans_type_name.' towards Invoice No '. $invoice_no .' & Batch no '. $batch_no,
          ];
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

  public function tally_entryy(){
    $paymentRequired  = false;
    $response = array(
      'status' => 'failure',
      'message' => 'Request method not allowed to execute the script.',
    );
    if (strpos(php_sapi_name(), 'cli') !== false) {
        $response['sapi'] = php_sapi_name();
        return $this->_setResponse($response, 405);
    }
    $where = ['is_posted_in_tally' => '0', 'user_id' => 607]; //, 'is_invoice_generated' => '1'
    $txnsData = Transactions::getTallyTxns($where);
    $paymentData = new Collection;
    if ($paymentRequired) {
      $paymentData = Payment::getTallyTxns($where);
    }
    $batch_no = _getRand(15);
    $totalRecords = 0;
    $insertedData = array();
    $selectedPaymentData = array();
    $selectedData = array();
    $tally_data = [];
    if (!$txnsData->isEmpty() || !$paymentData->isEmpty()) {
        $i = 0;
        $ignored_txns = [];
        $parent_settled = [];
        if (!empty($paymentData) && !$paymentData->isEmpty()) {
          foreach ($paymentData as $key => $pmnt) {
            $i++;
            $accountDetails = $pmnt->userRelation->companyBankDetails ?? '';
            if (empty($accountDetails)) {
                 $response['message'] =  'No Relation Found between customer('. $pmnt->user_id .') and Company with Bank';
                 return $response;
            }
            $inst_no = $pmnt->refundReq->tran_no ?? NULL;
            $inst_date = $pmnt->refundReq->actual_refund_date ?? NULL;
            $userName = $pmnt->user->f_name. ' ' . $pmnt->user->m_name .' '. $pmnt->user->l_name;
            $tally_data[] = [
              'batch_no' =>  $batch_no,
              'transactions_id' =>  NULL,
              'is_debit_credit' =>  '1',
              'trans_type' =>  $pmnt->transType->trans_name,
              'tally_trans_type_id' =>  $pmnt->transType->tally_trans_type,
              'tally_voucher_id' =>  $pmnt->transType->voucher->tally_voucher_id,
              'tally_voucher_code' =>  $pmnt->transType->voucher->voucher_no,
              'tally_voucher_name' =>  $pmnt->transType->voucher->voucher_name,
              'tally_voucher_date' =>  $pmnt->date_of_payment,
              'invoice_no' =>   '',
              'invoice_date' =>  NULL,
              'ledger_name' =>  $userName,
              'amount' =>  $pmnt->amount,
              'ref_no' =>  '',
              'ref_amount' =>  $pmnt->amount,
              'acc_no' =>  $accountDetails->acc_no ?? '',
              'ifsc_code' =>  $accountDetails->ifsc_code ?? '',
              'bank_name' =>  $accountDetails->bank->bank_name ?? '',
              'cheque_amount' =>  0,
              'cross_using' =>  $pmnt->payment_type == 2 ? 'a/c payee' : '',
              'mode_of_pay' =>  $pmnt->payment_type,
              'inst_no' =>  $inst_no,
              'inst_date' =>  $inst_date,
              'favoring_name' =>  $userName,
              'remarks' => $pmnt->comment ?? '',
              'narration' => $pmnt->comment ?? '',
              ];
              $selectedPaymentData[] = $pmnt->payment_id;
          }
        }


        foreach ($txnsData as $key => $txn) {
            if (empty($txn->transType->tally_trans_type) || $txn->transType->tally_trans_type == 0 || $txn->trans_type == 17) {
                $ignored_txns[$txn->trans_id] = 'Tally Trans Type is empty or zero';
                continue;
            }
            $userName = $txn->user->f_name. ' ' . $txn->user->m_name .' '. $txn->user->l_name;
            $trans_type_name = $txn->getTransNameAttribute();
            $tally_voucher_type_id = $txn->transType->tally_trans_type;
            $cheque_amount = 0;
            if ($txn->trans_type == 16 && $txn->entry_type == 0) {
              $disburse_amt = $txn->invoiceDisbursed->disburse_amt;
              $total_interest = $txn->invoiceDisbursed->total_interest;
              $cheque_amount = round($disburse_amt - $total_interest, 2);
              if ($total_interest == 0) {
                  $cheque_amount = 0;
              }
            }
            $invoice_no = $txn->userinvoicetrans->getUserInvoice->invoice_no ?? NULL;
            $invoice_date = $txn->userinvoicetrans->getUserInvoice->created_at ?? NULL;
            if ($txn->trans_type == 16 && $txn->entry_type == 0) {
              $invoice_no = $txn->invoiceDisbursed->invoice->invoice_no ?? NULL;
              $invoice_date = $txn->invoiceDisbursed->invoice->invoice_date ?? NULL;
            }

            if (!empty($txn->parent_trans_id)) {
                $parentRecord  = $txn->getParentTxn();
                $invoice_no = $parentRecord->userinvoicetrans->getUserInvoice->invoice_no ?? NULL;
                $invoice_date = $parentRecord->userinvoicetrans->getUserInvoice->created_at ?? NULL;
                if ($parentRecord->trans_type == 16) {
                  $invoice_no = $parentRecord->invoiceDisbursed->invoice->invoice_no ?? NULL;
                  $invoice_date = $parentRecord->invoiceDisbursed->invoice->invoice_date ?? NULL;
                }
                if ($txn->trans_type == $parentRecord->trans_type && $tally_voucher_type_id == 3) {
                    $ignored_txns[$txn->trans_id] = 'Child and Parent are same type for journal';
                    continue;
                }
            }
            $accountDetails = $txn->userRelation->companyBankDetails ?? NULL;
            if (empty($accountDetails) && !empty($invoice_no)) {
                 $response['message'] =  'No Relation Found between customer('. $txn->user_id .') and Company with Bank';
                 return $response;
            }
            if ($txn->trans_type == 16 && empty($invoice_no)) {
                $ignored_txns[$txn->trans_id] = 'Disbursal without Invoice no';
                continue;
            }
            if (in_array($txn->trans_type, [config('lms.TRANS_TYPE.MARGIN')]) && $txn->entry_type == 0) {
                $ignored_txns[$txn->trans_id] = 'Margin debit entry.';
                continue;
            }
            if ($tally_voucher_type_id == 3 && is_null($txn->parent_trans_id) && ($txn->getOutstandingAttribute() > 0 || empty($txn->userinvoicetrans)) && $txn->entry_type == 0) {
               $ignored_txns[$txn->trans_id] = 'Outstanding > 0 || Invoice not generated';
               continue;
            }
            $txn_entry_type = $txn->entry_type;
            if (!empty($txn->payment_id) && $txn->entry_type == 1) {
                $tally_voucher_type_id = 2;
            }
            if (in_array($txn->trans_type, [config('lms.TRANS_TYPE.TDS'), config('lms.TRANS_TYPE.REFUND'), config('lms.TRANS_TYPE.NON_FACTORED_AMT'), config('lms.TRANS_TYPE.WAVED_OFF'), config('lms.TRANS_TYPE.MARGIN')]) && $txn->entry_type == 1) {
               $tally_voucher_type_id = 3;
            }
            if (in_array($txn->trans_type, [config('lms.TRANS_TYPE.REFUND')]) && $txn->entry_type == 0) {
               $tally_voucher_type_id = 1;
            }

            $inst_no = $txn->invoiceDisbursed->disbursal->tran_id ?? NULL;
            $inst_date = $txn->invoiceDisbursed->disbursal->funded_date ?? NULL;
            if (!empty($txn->payment_id)) {
              $inst_no = $txn->refundReq->tran_no ?? NULL;
              $inst_date = $txn->refundReq->actual_refund_date ?? NULL;
            }
            $common_array = [
                  'batch_no' =>  $batch_no,
                  'transactions_id' =>  $txn->trans_id,
                  'is_debit_credit' =>  $txn_entry_type,
                  'trans_type' =>  $trans_type_name,
                  'tally_trans_type_id' =>  $tally_voucher_type_id,
                  'tally_voucher_id' =>  $txn->transType->voucher->tally_voucher_id,
                  'tally_voucher_code' =>  $txn->transType->voucher->voucher_no,
                  'tally_voucher_name' =>  $txn->transType->voucher->voucher_name,
                  'tally_voucher_date' =>  $txn->trans_date,
                  'invoice_no' =>  $invoice_no,
                  'invoice_date' =>  $invoice_date,
                  'ledger_name' =>  $userName,
                  'amount' =>  $txn->amount,
                  'ref_no' =>  $invoice_no,
                  'ref_amount' =>  $txn->amount,
                  'acc_no' =>  $accountDetails->acc_no ?? '',
                  'ifsc_code' =>  $accountDetails->ifsc_code ?? '',
                  'bank_name' =>  $accountDetails->bank->bank_name ?? '',
                  'cheque_amount' =>  $cheque_amount,
                  'cross_using' =>  '',
                  'mode_of_pay' =>  1,
                  'inst_no' =>  $inst_no,
                  'inst_date' =>  $inst_date,
                  'favoring_name' =>  $userName,
                  'remarks' => '',
                  'narration' => '',
            ];
            if (in_array($txn->trans_type, [config('lms.TRANS_TYPE.MARGIN')]) && $txn->entry_type == 1) {
               $secondEntry = $common_array;
               $secondEntry['tally_trans_type_id'] = 2;
               $tally_data[] = $secondEntry;
               $i++;
            }
            if (!empty($txn->userinvoicetrans->getUserInvoice->invoice_no)) {
              $gstData['base_amount'] = $txn->userinvoicetrans->base_amount;
              if ($txn->userinvoicetrans->sgst_amount != 0) {
                  $gstData['sgst'] = $txn->userinvoicetrans->sgst_amount;
              } 
              if ($txn->userinvoicetrans->cgst_amount != 0) {
                  $gstData['cgst'] = $txn->userinvoicetrans->cgst_amount;
              } 
              if ($txn->userinvoicetrans->igst_amount != 0) {
                  $gstData['igst'] = $txn->userinvoicetrans->igst_amount;
              }
              foreach ($gstData as $gst_key => $gst_val) {
                 $gst_trans_amount = $gst_val;
                switch ($gst_key) {
                  case 'base_amount':
                    $gst_trans_type = $txn->transType->trans_name;
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
                $common_array['trans_type'] =  $gst_trans_type;
                $common_array['amount'] =  $gst_trans_amount;
                $common_array['ref_amount'] =  $gst_trans_amount;
                $tally_data[] = $common_array;
                $i++;
              }
            }else{
              $tally_data[] = $common_array;
              $i++;
            }
          $selectedData[] = $txn->trans_id;
        }
        // dd($ignored_txns);
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
        if ($res === true) {
          $totalTxnRecords = 0;
          if (!empty($selectedData)) {
            $totalTxnRecords = \DB::update('update rta_transactions set is_posted_in_tally = 1 where trans_id in(' . implode(', ', $selectedData) . ')');
          }
          $totalPaymentsRecords = 0;
          if (!empty($selectedPaymentData)) {
            $totalPaymentsRecords = \DB::update('update rta_payments set is_posted_in_tally = 1 where payment_id in(' . implode(', ', $selectedPaymentData) . ')');
          }
          $totalRecords = $totalTxnRecords + $totalPaymentsRecords;
          if ($totalRecords != (count($selectedData) + count($selectedPaymentData))) {
            $response['message'] =  'Some error occured. No Record can be posted in tally.';
          }else{
            $response['status'] = 'success';
            $batchData = [
              'batch_no' => $batch_no,
              'record_cnt' => $i,
              'created_at' => date('Y-m-d H:i:s'),
            ];
            $tally_inst_data = FinanceModel::dataLogger($batchData, 'tally');
            $response['message'] =  ($i > 1 ? $i .' Records inserted successfully' : '1 Record inserted.');
          }
        }else{
          $response['message'] =  ($res[2] ?? 'DB error occured.').' No Record can be posted in tally.';
        }
    }else{
      $response['message'] =  'No new record found to post in tally.';
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