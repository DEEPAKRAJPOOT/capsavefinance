<?php
namespace App\Inv\Repositories\Contracts\Traits;

use Auth;
use Carbon\Carbon;
use Helpers;
use Illuminate\Support\Facades\DB;
use App\Helpers\ApportionmentHelper;
use App\Inv\Repositories\Models\Payment;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Models\BizPanGst;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Lms\InterestAccrual;
use App\Inv\Repositories\Models\Lms\Refund\RefundReqTrans;

use App\Inv\Repositories\Models\Lms\InvoiceDisbursed;
use App\Inv\Repositories\Models\Lms\RefundTransactions;
use App\Inv\Repositories\Models\Lms\InvoiceRepaymentTrail;
use App\Helpers\ManualApportionmentHelper;

trait LmsTrait
{
    /**
     * Calculate Interest
     * 
     * @param float $principalAmt
     * @param float $interestRate
     * @param integer $tenorDays
     * 
     * @return mixed
     */
    protected function calInterest($principalAmt, $interestRate, $tenorDays)
    {
        $interest = round(($principalAmt * ($interestRate / config('common.DCC')))/100,2);                
        return $tenorDays * $interest;        
    }  

    protected function calPercentage($amount, $percenttage){
        return round(($amount*$percenttage)/100,2);
    }  

    protected function calMargin($amt, $val)
    {
        return ($amt*$val)/100;
    }   
    
    protected function calculateTenorDays($invoice = [])
    {
        $now = strtotime((isset($invoice['invoice_due_date'])) ? $invoice['invoice_due_date'] : ''); // or your date as well
        $your_date = strtotime((isset($invoice['invoice_date'])) ? $invoice['invoice_date'] : '');
        $datediff = abs($now - $your_date);

        $tenor = round($datediff / (60 * 60 * 24));
        return $tenor;        
    } 

    protected function calculateFundedAmount($invoice = [], $margin)
    {
        return $invoice['invoice_approve_amount'] - ((float)($invoice['invoice_approve_amount']*$margin)/100);
    }   
    
    /**
     * Add No Of Days to Date
     * 
     * @param Date $currentDate
     * @param integer $noOfDays
     * @return Date
     */
    protected function addDays($currentDate, $noOfDays)
    {
        $calDate = date('Y-m-d', strtotime($currentDate . "+ $noOfDays days"));
        return $calDate;
    }

    protected function subDays($currentDate, $noOfDays)
    {
        $calDate = date('Y-m-d', strtotime($currentDate . "- $noOfDays days"));
        return $calDate;
    }
    
    protected function calDisbursalAmount($principalAmount, $deductions)
    {
        $totalDeductions = 0;
        foreach($deductions as $deduction) {
            $totalDeductions += $deduction;
        }
        $balPrincipal = $principalAmount - $totalDeductions;
        return $balPrincipal;
    }

    /**
     * Prepare Disbursal Data
     * 
     * @param array $data
     * @return mixed
     */
    protected function prepareDisbursalData($requestData, $addlData)
    {
        $disbursalData = [];
        foreach($requestData['invoices'] as $invoice) {
            $data = [];
            $data['user_id'] = isset($requestData['user_id']) ? $requestData['user_id'] : null;
            $data['app_id'] = isset($requestData['app_id']) ? $requestData['app_id'] : null;
            $data['invoice_id'] = isset($requestData['invoice_id']) ? $requestData['invoice_id'] : null;
            $data['prgm_offer_id'] = isset($requestData['prgm_offer_id']) ? $requestData['prgm_offer_id'] : null;
            $data['bank_id'] = isset($requestData['bank_id']) ? $requestData['bank_id'] : null;
            $data['disburse_date'] = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
            $data['bank_id'] = isset($requestData['bank_id']) ? $requestData['bank_id'] : null;
            $data['bank_name'] = isset($requestData['bank_name']) ? $requestData['bank_name'] : null;
            $data['ifsc_code'] = isset($requestData['ifsc_code']) ? $requestData['ifsc_code'] : null;
            $data['acc_no'] = isset($requestData['acc_no']) ? $requestData['acc_no'] : null;            
            $data['virtual_acc_id'] = isset($requestData['virtual_acc_id']) ? $requestData['virtual_acc_id'] : null;
            
            $data['customer_id'] = isset($requestData['customer_id']) ? $requestData['customer_id'] : null;
            $data['principal_amount'] = isset($requestData['principal_amount']) ? $requestData['principal_amount'] : null;
            
            $data['inv_due_date'] = isset($requestData['inv_due_date']) ? $requestData['inv_due_date'] : null;
            $data['tenor_days'] = isset($requestData['tenor_days']) ? $requestData['tenor_days'] : null;
            $data['interest_rate'] = isset($requestData['interest_rate']) ? $requestData['interest_rate'] : null;
            
            $data['total_interest'] = $this->calInterest($data['principal_amount'], $data['interest_rate'], $data['tenor_days']);
            
            $data['margin'] = isset($requestData['margin']) ? $requestData['margin'] : null;
            
            $deductions['margin']         = $data['margin'];
            $deductions['total_interest'] = $data['total_interest'];
            $data['disburse_amount'] = $this->calDisbursalAmount($data['principal_amount'], $deductions);
            
            $data['total_repaid_amt'] = 0;
            $data['status'] = 0;
            $data['settlement_date'] = null;
            $data['accured_interest'] = null;
            $data['interest_refund'] = null;
            
            $disbursalData[] = $data;
        }
        return $data;
    }

    /**
     * Prepare Invoice Disbursed Data
     * 
     * @param array $data
     * @return mixed
     */
    protected function createInvoiceDisbursedData($invoice, $disburseType = 2)
    {
        /**
        * disburseType = 1 for online and 2 for manually
        */
        $disbursalData = [];
        $disburseDate = $invoice['disburse_date'];
        $fundDate = date("Y-m-d h:i:s", strtotime(str_replace('/','-',$disburseDate)));
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$disburseDate)) {
            $str_to_time_date = strtotime($disburseDate);
        } else {
            $str_to_time_date = strtotime(\Carbon\Carbon::createFromFormat('d/m/Y', $disburseDate)->setTimezone(config('common.timezone'))->format('Y-m-d'));
        }
        $bankId = $invoice['program_offer']['bank_id'];
        $oldIntRate = $invoice['program_offer']['interest_rate'] - $invoice['program_offer']['base_rate'];
        $interestRate = ($invoice['is_adhoc'] == 1) ? (float)$invoice['program_offer']['adhoc_interest_rate'] : (float)$invoice['program_offer']['interest_rate'];
        $Obj = new ManualApportionmentHelper($this->lmsRepo);
        $bankRatesArr = $Obj->getBankBaseRates($bankId);
        if ($bankRatesArr && $invoice['is_adhoc'] != 1) {
          $actIntRate = $Obj->getIntRate($oldIntRate, $bankRatesArr, $str_to_time_date);
        } else {
          $actIntRate = $interestRate;
        }
        $interest= 0;
        $diffDays= 0;
        $margin= 0;

        $tenor = $this->calculateTenorDays($invoice);
        $margin = $this->calMargin($invoice['invoice_approve_amount'], $invoice['program_offer']['margin']);
        $fundedAmount = $invoice['invoice_approve_amount'] - $margin;
        $banchMarkDateFlag = $invoice['program_offer']['benchmark_date'];
        
        if ($banchMarkDateFlag == 1) {
            $tenor = $this->calDiffDays($invoice['invoice_due_date'], $fundDate);
        }
        $tInterest = $this->calInterest($fundedAmount, $actIntRate, $tenor);

        if($invoice['program_offer']['payment_frequency'] == 1 && $invoice['program']['interest_borne_by'] == 2) {
            $interest = $tInterest;
        }
        //$disburseAmount = round($fundedAmount - $interest, config('lms.DECIMAL_TYPE')['AMOUNT_TWO_DECIMAL']);
        $disburseAmount = round($fundedAmount, config('lms.DECIMAL_TYPE')['AMOUNT_TWO_DECIMAL']);

        $disbursalData['disbursal_id'] = $invoice['disbursal_id'] ?? null;
        $disbursalData['invoice_id'] = $invoice['invoice_id'] ?? null;
        $disbursalData['customer_id'] = $invoice['lms_user']['customer_id'] ?? null;
        $disbursalData['disbursal_api_log_id'] = $invoice['disbursal_api_log_id'] ?? null;
        $disbursalData['disburse_amt'] = $disburseAmount ?? null;
        $disbursalData['inv_due_date'] = $invoice['invoice_due_date'] ?? null;
        $disbursalData['payment_due_date'] = null;
        $disbursalData['tenor_days'] =  $tenor ?? null;
        $disbursalData['interest_rate'] = $actIntRate ?? null;
        $disbursalData['total_interest'] = $interest;
        $disbursalData['processing_fee'] = $invoice['processing_fee'] ?? 0.00;
        $disbursalData['processing_fee_gst'] = $invoice['processing_fee_gst'] ?? 0.00;
        $disbursalData['margin'] = $invoice['program_offer']['margin'] ?? null;
        $disbursalData['status_id'] = 10;
        
        $disbursalData['int_accrual_start_dt'] = ($disburseType == 1 && !empty($invoice['disburse_date'])) ?  date("Y-m-d", strtotime(str_replace('/','-',$invoice['disburse_date']))) : null;
        $disbursalData['grace_period'] = $invoice['program_offer']['grace_period'] ?? null;
        $disbursalData['overdue_interest_rate'] = $invoice['program_offer']['overdue_interest_rate'] ? $invoice['program_offer']['overdue_interest_rate'] + $interestRate : 0;
        $disbursalData['is_adhoc'] = $invoice['is_adhoc'] ?? 0;
        
        $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
                        
        $dataArr['created_by'] = Auth::user()->user_id;
        $dataArr['created_at'] = $curData;
        $dataArr['sys_created_at'] = $invoice['sys_created_at'] ?? null;
        
        return $disbursalData;
    }

    /**
     * Prepare Disbursal Data
     * 
     * @param array $data
     * @return mixed
     */
    protected function createDisbursalData($user, $disburseAmount, $disburseType = 2)
    {
        /**
        * disburseType = 1 for online and 2 for manually
        */
        $disbursalData = [];
        
        $disbursalData['user_id'] = $user['user_id'] ?? null;

        $disbursalData['disburse_date'] = (!empty($user['disburse_date'])) ? date("Y-m-d h:i:s", strtotime(str_replace('/','-',$user['disburse_date']))) : \Carbon\Carbon::now()->format('Y-m-d h:i:s');
        $disbursalData['disburse_amount'] = $disburseAmount ?? null;
        $disbursalData['disbursal_batch_id'] = $user['disbursal_batch_id'] ?? null;
        $disbursalData['ref_no'] = $user['ref_no'] ?? null;
        $disbursalData['tran_id'] = $user['tran_id'] ?? null;
        
        $disbursalData['bank_account_id'] = ($user['is_buyer'] == 2) ? $user['anchor_bank_details']['bank_account_id'] : $user['supplier_bank_detail']['bank_account_id'];
        $disbursalData['bank_name'] = ($user['is_buyer'] == 2) ? $user['anchor_bank_details']['bank']['bank_name'] : $user['supplier_bank_detail']['bank']['bank_name'] ;
        $disbursalData['ifsc_code'] = ($user['is_buyer'] == 2) ? $user['anchor_bank_details']['ifsc_code'] : $user['supplier_bank_detail']['ifsc_code'];
        $disbursalData['acc_no'] = ($user['is_buyer'] == 2) ? $user['anchor_bank_details']['acc_no'] : $user['supplier_bank_detail']['acc_no'];            
        $disbursalData['account_holder_name'] = ($user['is_buyer'] == 2) ? $user['anchor_bank_details']['acc_name'] : $user['supplier_bank_detail']['acc_name'];            
        $disbursalData['virtual_acc_id'] = $user['lms_user']['virtual_acc_id'] ?? null;
       
        $disbursalData['status_id'] = 10;
        $disbursalData['disburse_type'] = $disburseType;
       
        $disbursalData['funded_date'] = ($disburseType == 1) ? \Carbon\Carbon::now()->format('Y-m-d h:i:s') : null;
        $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
        $disbursalData['status_update_time'] = $curData;
                        
        $disbursalData['created_by'] = Auth::user()->user_id;
        $disbursalData['created_at'] = $curData;
        return $disbursalData;
    }

    /**
     * Prepare Disbursal Data
     * 
     * @param array $data
     * @return mixed
     */
    protected function createTransactionData($userId = null, $data = 0, $transType = 0, $entryType = 0)
    {
        $soaFlag = 0;
        if(isset($data['soa_flag'])){
            $soaFlag = $data['soa_flag'];
        }else{
            $soaFlag = in_array($transType,[10]) ? 0 : 1;
        }

        $transactionData = [];
        $transactionData['link_trans_id'] = $data['link_trans_id'] ?? null;
        $transactionData['parent_trans_id'] = $data['parent_trans_id'] ?? null;
        $transactionData['invoice_disbursed_id'] = $data['invoice_disbursed_id'] ?? null;
        $transactionData['user_id'] = $userId ?? null;
        $transactionData['trans_date'] = (!empty($data['trans_date'])) ? date("Y-m-d h:i:s", strtotime(str_replace('/','-',$data['trans_date']))) : \Carbon\Carbon::now()->format('Y-m-d h:i:s');
        $transactionData['trans_type'] = $transType ?? 0;
        $transactionData['amount'] = $data['amount'] ?? 0;
        $transactionData['from_date'] = $data['from_date'] ?? null;
        $transactionData['to_date'] = $data['to_date'] ?? null;
        $transactionData['due_date'] = $data['due_date'] ?? null;
        $transactionData['entry_type'] =  $entryType ?? 0;
        $transactionData['gst'] = $data['gst'] ?? 0;
        $transactionData['tds_per'] = null;
        $transactionData['gl_flag'] = 1;
        $transactionData['soa_flag'] = $soaFlag;
        $transactionData['trans_by'] = 2;
        $transactionData['pay_from'] = ($transType == 16) ? 3 : $this->appRepo->getUserTypeByUserId($userId);
        $transactionData['is_settled'] = 0;
        $transactionData['is_posted_in_tally'] = 0;
        $transactionData['base_amt'] = $data['base_amt'] ?? null;
        $transactionData['gst_amt'] = $data['gst_amt'] ?? null;
        $transactionData['chrg_gst_id'] = $data['chrg_gst_id'] ?? null;
        $transactionData['trans_mode'] = $data['trans_mode'] ?? null;
        if(isset($data['created_at'])){
            $transactionData['created_at'] = $data['created_at']; 
        }
        return $transactionData;
    }

    /**
     * Prepare Disbursal Data
     * 
     * @param array $data
     * @return mixed
     */
    protected function createBatchFileData($file = null)
    {
        /**
        * disburseType = 1 for online and 2 for manually
        */
        $data = [];

        $data['file_path'] = $file['file_path'] ?? '';
        $data['file_type'] = $file['file_type'] ?? '';
        $data['file_name'] = $file['file_name'] ?? '';
        $data['file_size'] = $file['file_size'] ?? '';
        $data['file_encp_key'] =  !empty($path) ? md5(basename($path)) : md5('2');
        $data['created_by'] = 1;
        $data['updated_by'] = 1;
        
        return $data;
    }    
    /**
     * Get Overdue Interest
     * 
     * @param integer $invoice_id
     * @return float
     */
    protected function getOverDueInterest($invoice_id)
    {   
        $invDisbData = InvoiceDisbursed::where('invoice_id','=',$invoice_id)->first();
        if (!$invDisbData) return null;
        
        $invoice_disbursed_id = $invDisbData->invoice_disbursed_id;
        
        $monthlyIntCond = [];
        $monthlyIntCond['invoice_disbursed_id'] = $invoice_disbursed_id;
        $monthlyIntCond['overdue_interest_rate_not_null'] = '1';
        $accuredInterest = $this->lmsRepo->sumAccruedInterest($monthlyIntCond);
        $accuredInterestCount =  $this->lmsRepo->countAccruedInterest($monthlyIntCond);
        return array('penal_amount' => $accuredInterest, 'penal_days'=>$accuredInterestCount);
    }
    
    protected  function businessInformation($attr)
    {
      try
        { 
          $date = Carbon::now();
          $id = Auth::user()->user_id;
          $business = Business::find($attr->biz_id);
          $obj =   $business->replicate();
                $obj->biz_id = "";
                $obj->created_by = $id;
                $obj->created_at = $date;
                $obj->save();
        return $obj;
      } catch (Exception $ex) {
           return false;
      }
       
    }
    protected  function bizPanGst($biz_details)
    {
      try
        { 
          $date = Carbon::now();
          $id = Auth::user()->user_id;
          $business = BizPanGst::find($biz_details->biz_pan_gst_id);
          dd($business);
          $obj =   $business->replicate();
                $obj->biz_pan_gst_id = "";
                $obj->biz_id = $biz_details->biz_id;
                $obj->created_by = $id;
                $obj->created_at = $date;
                $obj->save();
        return $obj->biz_id;
      } catch (Exception $ex) {
           return false;
      }
       
    }
     protected  function applicationSave($app_id,$biz_id)
    {
       try
       {   
            $date = Carbon::now();
            $id = Auth::user()->user_id;
            $app = Application::find($app_id);
            $obj =   $app->replicate();
            $obj->app_id = "";
            $obj->biz_id = $biz_id;
            $obj->created_by = $id;
            $obj->created_at = $date;
            $obj->save(); 
        return $obj->app_id;
       } catch (Exception $ex) {

       }
    }
     protected  function managementInformation($attr)
    {
       dd($attr);
    }
     protected  function document($attr)
    {
       dd($attr);
    } 
    
    protected function getRefundData($transId, $includeTrans=true)
    {   
        $data = [];
        $result = $this->lmsRepo->getRefundData($transId);        
        foreach($result as $row) {
            if ($row->name == 'MARGIN') {
                $data[$row->name][] = $row;
            } else {
                $data[$row->name] = $row;
            }
        }
        
        if ($includeTrans) {
            $repayment = $this->lmsRepo->getTransactions(['trans_id' => $transId, 'trans_type' => config('lms.TRANS_TYPE.REPAYMENT')])->first();
            $repaymentTrails = $this->lmsRepo->getTransactions(['payment_id' => $transId]);

            $transaction = [];
            $transactions = [];

            $transaction['TRANS_DATE'] = $repayment->trans_date;
            $transaction['VALUE_DATE'] = $repayment->created_at;

            if ($repayment->transType->chrg_master_id != '0') {
                $transaction['TRANS_TYPE'] = $repayment->transType->charge->chrg_name;
            } else {
                $transaction['TRANS_TYPE'] = $repayment->transType->trans_name;
            }

            if ($repayment->disbursal_id && $repayment->disburse && $repayment->disburse->invoice) {
                $transaction['INV_NO'] = $repayment->disburse->invoice->invoice_no;
            } else {
                $transaction['INV_NO'] = '';
            }      

            if ($repayment->entry_type == '0') {
                $transaction['DEBIT'] = $repayment->amount;
            } else {
                $transaction['DEBIT'] = '';
            }

            if ($repayment->entry_type == '1') {
                $transaction['CREDIT'] = $repayment->amount;
            } else {
                $transaction['CREDIT'] = '';
            }

            $transactions[] = $transaction;

            foreach ($repaymentTrails as $repay) {
                $transaction = [];
                
                
                $transaction['TRANS_DATE'] = $repay->trans_date;
                $transaction['VALUE_DATE'] = $repay->created_at;

                if ($repay->transType->chrg_master_id != '0') {
                    $transaction['TRANS_TYPE'] = $repay->transType->charge->chrg_name;
                } else {
                    $transaction['TRANS_TYPE'] = $repay->transType->trans_name;
                }

                if ($repay->disbursal_id && isset($repay->disburse->invoice) && $repay->disburse->invoice->invoice_no) {
                    $transaction['INV_NO'] = $repay->disburse->invoice->invoice_no;
                } else {
                    $transaction['INV_NO'] = '';
                }      

                if ($repay->entry_type == '0') {
                    $transaction['DEBIT'] = $repay->amount;
                } else {
                    $transaction['DEBIT'] = '';
                }

                if ($repay->entry_type == '1') {
                    $transaction['CREDIT'] = $repay->amount;
                } else {
                    $transaction['CREDIT'] = '';
                }

                $transactions[] = $transaction;   
            }

            $data['TRANSACTIONS'] = $transactions;
        }
        return $data;
    }
    
    protected function createApprRequest($requestType, $addlData=[]) 
    {
        $wf_stage_types = config('lms.REQUEST_TYPE');
        
        $assignRequest = false;        
        //$wf_stage_type = isset($wf_stage_types[$requestType]) ? $wf_stage_types[$requestType] : '';
        $wf_stage_type = $requestType;
        
        $reqData=[];
        $reqLogData=[];
        
        //Prepare Request Data        
        $reqData['req_type'] = $wf_stage_type;
        $reqData['status'] = config('lms.REQUEST_STATUS.NEW_REQUEST');        
        
        //Prepare Request Log Data
        $reqLogData['status'] = $reqData['status'];        
            
        if ($requestType == config('lms.REQUEST_TYPE.REFUND') ) {
            $reqData['trans_id'] = $addlData['trans_id'];
            $reqData['amount'] = $addlData['amount'];            
        }
        
        $saveReqData = $this->lmsRepo->saveApprRequestData($reqData);
        $req_id = $saveReqData->req_id;        
        $saveReqData = $this->lmsRepo->saveApprRequestData(['ref_code' => \Helpers::formatIdWithPrefix($req_id, $type='REFUND')], $req_id);
        $reqLogData['req_id'] = $req_id;
        
        if($reqData['trans_id'] && $req_id){
            RefundTransactions::saveRefundTransactions($reqData['trans_id'], $req_id);
        }

        $wf_stages = $this->lmsRepo->getWfStages($wf_stage_type);
        foreach($wf_stages as $wf_stage) {
            $wf_stage_code = $wf_stage->stage_code;
            $wf_order_no = $wf_stage->order_no;
            
            $wfData = $this->lmsRepo->getWfDetailById($wf_stage_code);
            if ($wfData) {
                $wfAppStageData = $this->lmsRepo->getRequestWfStage($wf_stage_code, $req_id);
                if (!$wfAppStageData) {
                    $arrData = [
                        'wf_stage_id' => $wfData->wf_stage_id,
                        'req_id' => $req_id,
                        'wf_status' => config('lms.WF_STAGE_STATUS.PENDING'),
                    ];
                    $this->lmsRepo->saveWfDetail($arrData);
                }
            }
            if ($wf_order_no == '0') {
                $result = $wf_stage;
                $assignRequest = true;
            }
        }
        
        if ($assignRequest) {
            
            $data = $result;
            $this->lmsRepo->updateRequestAssignById((int) $req_id, ['is_owner' => 0]);
            //update assign table
            $assignRequests=[];
            $allReqLogData=[];
            $assignRoles = explode(',', $data->assign_role);
            foreach($assignRoles as $role_id) {
                $assignedUsers = $this->lmsRepo->getBackendUsersByRoleId($role_id);
                if (count($assignedUsers) > 0) {
                    foreach($assignedUsers as $auser) {
                        $dataArr = [];
                        $dataArr['from_id'] = \Auth::user()->user_id;
                        $dataArr['to_id'] = $auser->user_id;
                        $dataArr['role_id'] = null;
                        $dataArr['req_id'] = $req_id;
                        $dataArr['assign_status'] = '0';
                        $dataArr['assign_type'] = '2';
                        $dataArr['sharing_comment'] = isset($addlData['sharing_comment']) ? $addlData['sharing_comment'] : '';
                        $dataArr['is_owner'] = 1;  
                        
                        $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
                        
                        $dataArr['created_by'] = Auth::user()->user_id;
                        $dataArr['created_at'] = $curData;
                        $dataArr['updated_by'] = Auth::user()->user_id;
                        $dataArr['updated_at'] = $curData;
                        
                        $assignRequests[] = $dataArr;
                        
                        //Save Request Log Data
                        $allReqLogData[] = $reqLogData + [
                            'comment' => isset($addlData['sharing_comment']) ? $addlData['sharing_comment'] : '',
                            'assigned_user_id' => $auser->user_id, 
                            'wf_stage_id' => $data->wf_stage_id,
                            'created_by' => \Auth::user()->user_id,
                            'created_at' => $curData,
                            'updated_by' => \Auth::user()->user_id,
                            'updated_at' => $curData               
                        ];
                    }
                } 
                /*else { 
                    $assignRequests['from_id'] = \Auth::user()->user_id;    
                    $assignRequests['req_id'] = $req_id;
                    $assignRequests['assign_status'] = '0';
                    $assignRequests['assign_type'] = '2';
                    $assignRequests['sharing_comment'] = isset($addlData['sharing_comment']) ? $addlData['sharing_comment'] : '';
                    $assignRequests['is_owner'] = 1;                
                    $assignRequests['to_id'] = null;
                    $assignRequests['role_id'] = $role->role_id;
                }*/
            }
            $this->lmsRepo->assignRequest($assignRequests);
            $this->lmsRepo->saveApprRequestLogData($allReqLogData);
            return $data;
        }
        
    }

    protected function finalRefundTransactions(int $refundReqId, $actualRefundDate)
    {
        $eventDate = Helpers::getSysStartDate();
        $actualRefundDate = carbon::parse($actualRefundDate)->format('Y-m-d');
        $transactions = RefundReqTrans::where('refund_req_id','=',$refundReqId)
        ->whereHas('transaction',function($query){
            $query->whereIn('trans_type',[config('lms.TRANS_TYPE.REFUND'),config('lms.TRANS_TYPE.MARGIN'),config('lms.TRANS_TYPE.NON_FACTORED_AMT')]);
        })
        ->get();
        $transactionsTds = RefundReqTrans::where('refund_req_id','=',$refundReqId)
        ->whereHas('refundReq', function($query){
            $query->whereHas('payment',function($query){
                $query->where('is_refundable','=',1);
            });
        })
        ->whereHas('transaction',function($query){
            $query->whereIn('trans_type',[config('lms.TRANS_TYPE.TDS')]);
        })
        ->get();

        $transactions = $transactions->merge($transactionsTds); 
        
        foreach ($transactions as $key => $trans) {
            $transDetail = $trans->transaction;
            if($trans->req_amount > 0 && $transDetail->settled_outstanding > 0 && $transDetail->trans_type == 32 && $transDetail->entry_type == 1){
                $refundData = $this->createTransactionData($transDetail->user_id, [
                    'amount' => $transDetail->settled_outstanding,
                    'trans_date'=>$actualRefundDate,
                    'tds_per'=>0,
                    'invoice_disbursed_id'=>$transDetail->invoice_disbursed_id,
                    'parent_trans_id'=>$transDetail->parent_trans_id??$transDetail->trans_id,
                    'link_trans_id'=>$transDetail->trans_id,
                    'soa_flag'=>1,
                    'created_at'=>$eventDate
                ], config('lms.TRANS_TYPE.REFUND'), 0);

                $trans_data =  Transactions::saveTransaction($refundData);
                if($trans_data){
                    $updateData = [
                        'refund_trans_id'=> $trans_data->trans_id
                    ];
                    RefundReqTrans::saveRefundReqTransData($updateData,$trans->refund_req_trans_id);
                }
            }
        }
    }

    protected function updateApprRequest($reqId, $addlData=[]) 
    {        
        $apprReqData = $this->lmsRepo->getApprRequestData($reqId);
        if(!$apprReqData) return false;
                
        $wf_stage_type = $apprReqData->req_type;
        $reqStatus = $addlData['status'];
                
        //Get Current workflow stage
        $wfStage = $this->lmsRepo->getCurrentWfStage($reqId);
        $wf_stage_code = $wfStage ? $wfStage->stage_code : '';
        $wf_stage_id = $wfStage ? $wfStage->wf_stage_id : '';
                        
        //Update Request Log Data
        $updateReqLogData = ['is_active' => '0'];
        $whereCond=[];
        $whereCond['req_id'] = $reqId;
        $whereCond['assigned_user_id'] = \Auth::user()->user_id;
        $whereCond['wf_stage_id'] = $wf_stage_id;
        $this->lmsRepo->updateApprRequestLogData($whereCond, $updateReqLogData);
                        
        //Insert Request Log Data
        $reqLogData=[];
        $reqLogData['req_id'] = $reqId;
        $reqLogData['status'] = $reqStatus;
        $reqLogData['comment'] = $addlData['sharing_comment'];
        $reqLogData['assigned_user_id'] = \Auth::user()->user_id;
        $reqLogData['wf_stage_id'] = $wf_stage_id;
        $this->lmsRepo->saveApprRequestLogData($reqLogData);
                             
        if (in_array($wf_stage_code, ['refund_approval', 'adjustment_approval']) && config('lms.REQUEST_STATUS.REFUND_QUEUE') != $reqStatus) {
            
            //Get Assigned Request for Approval
            $whereCond=[];
            $whereCond['req_id'] = $reqId;
            $assignedReqData = $this->lmsRepo->getAssignedReqData($whereCond);            
            $cntAssignedReqStatus = count($assignedReqData);
                                     
            //Get Request Log with Status for Approval
            $whereCond=[];
            $whereCond['req_id'] = $reqId;
            $whereCond['wf_stage_id'] = $wf_stage_id;
            $apprReqLogData = $this->lmsRepo->getApprRequestLogData($whereCond);
            $cntUpdatedReqStatus=count($apprReqLogData);            
            
            $cntApprReqStatus=0;
            foreach($apprReqLogData as $rLog) {
                if ($rLog->status == config('lms.REQUEST_STATUS.APPROVED')) {
                    $cntApprReqStatus++;
                }
            }
            if ($cntAssignedReqStatus == $cntUpdatedReqStatus) {
                $reqStatus = $cntUpdatedReqStatus == $cntApprReqStatus ? config('lms.REQUEST_STATUS.APPROVED') : config('lms.REQUEST_STATUS.REJECTED');

                //$wf_stage_status = config('lms.WF_STAGE_STATUS.COMPLETED');
            }
        }
        
        $updateReqData=[];
        $updateReqData['status'] = $reqStatus;
        $this->lmsRepo->saveApprRequestData($updateReqData, $reqId);
                
        $wf_stage_status = config('lms.REQUEST_STATUS.REFUND_QUEUE') == $reqStatus ? config('lms.WF_STAGE_STATUS.COMPLETED') : config('lms.WF_STAGE_STATUS.IN_PROGRESS');
        $updateWfStage=[];
        $updateWfStage['wf_status'] = $wf_stage_status;        
        $this->lmsRepo->updateWfStage($wf_stage_id, $reqId, $updateWfStage);
                       
        return true;
    }    
    
    protected function assignRequest($reqId, $wfStage, $reqStatus, $addlData)
    {
        
        $data = $wfStage;
        $this->lmsRepo->updateRequestAssignById((int) $reqId, ['is_owner' => 0]);
        //update assign table
        $assignRequests=[];
        $allReqLogData=[];
        $assignRoles = explode(',', $data->assign_role);
        
        $reqLogData=[];
        $reqLogData['req_id'] = $reqId;
        $reqLogData['status'] = $reqStatus;
        
        foreach($assignRoles as $role_id) {
            $assignedUsers = $this->lmsRepo->getBackendUsersByRoleId($role_id);
            if (count($assignedUsers) > 0) {
                foreach($assignedUsers as $auser) {
                    $dataArr = [];
                    $dataArr['from_id'] = \Auth::user()->user_id;
                    $dataArr['to_id'] = $auser->user_id;
                    $dataArr['role_id'] = null;
                    $dataArr['req_id'] = $reqId;
                    $dataArr['assign_status'] = '0';
                    $dataArr['assign_type'] = '2';
                    $dataArr['sharing_comment'] = isset($addlData['sharing_comment']) ? $addlData['sharing_comment'] : '';
                    $dataArr['is_owner'] = 1;
                    
                    $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');

                    $dataArr['created_by'] = \Auth::user()->user_id;
                    $dataArr['created_at'] = $curData;
                    $dataArr['updated_by'] = \Auth::user()->user_id;
                    $dataArr['updated_at'] = $curData;
                        
                    $assignRequests[] = $dataArr;

                    //Save Request Log Data
                    $allReqLogData[] = $reqLogData + [
                        'comment' => isset($addlData['sharing_comment']) ? $addlData['sharing_comment'] : '',
                        'assigned_user_id' => $auser->user_id, 
                        'wf_stage_id' => $data->wf_stage_id,
                        'created_by' => \Auth::user()->user_id,
                        'created_at' => $curData,
                        'updated_by' => \Auth::user()->user_id,
                        'updated_at' => $curData                          
                    ];
                }
            } 
            /*else { 
                $assignRequests['from_id'] = \Auth::user()->user_id;    
                $assignRequests['req_id'] = $reqId;
                $assignRequests['assign_status'] = '0';
                $assignRequests['assign_type'] = '2';
                $assignRequests['sharing_comment'] = isset($addlData['sharing_comment']) ? $addlData['sharing_comment'] : '';
                $assignRequests['is_owner'] = 1;                
                $assignRequests['to_id'] = null;
                $assignRequests['role_id'] = $role->role_id;
            }*/
        }
        $this->lmsRepo->assignRequest($assignRequests);
        
        $updateReqLogData = ['is_active' => '0'];
        $whereCond=[];
        $whereCond['req_id'] = $reqId;
        //$whereCond['assigned_user_id'] = \Auth::user()->user_id;
        //$whereCond['wf_stage_id'] = $data->wf_stage_id;          
        $this->lmsRepo->updateApprRequestLogData($whereCond, $updateReqLogData);
        
        $this->lmsRepo->saveApprRequestLogData($allReqLogData);        
    }

    protected function moveRequestToNextStage($reqId, $addlData=[])
    {
        $apprReqData = $this->lmsRepo->getApprRequestData($reqId);
        if(!$apprReqData) return false;
                
        $wf_stage_type = $apprReqData->req_type;
        
        //Get Current workflow stage
        $curWfStage = $this->lmsRepo->getCurrentWfStage($reqId);
        if (!$curWfStage) return false;
                
        $cur_wf_stage_code = $curWfStage ? $curWfStage->stage_code : '';
        $cur_wf_stage_id = $curWfStage ? $curWfStage->wf_stage_id : '';
        $cur_wf_order_no = $curWfStage ? $curWfStage->order_no : '';
        
        $cur_wf_stage_status = config('lms.WF_STAGE_STATUS.COMPLETED');
        $updateWfStage=[];
        $updateWfStage['wf_status'] = $cur_wf_stage_status;        
        $this->lmsRepo->updateWfStage($cur_wf_stage_id, $reqId, $updateWfStage);
        
        //Get Next workflow stage
        $nextWfStage = $this->lmsRepo->getNextWfStage($wf_stage_type, $cur_wf_order_no);
        if (!$nextWfStage) return false;
                       
        $next_wf_stage_code = $nextWfStage ? $nextWfStage->stage_code : '';
        $next_wf_stage_id = $nextWfStage ? $nextWfStage->wf_stage_id : '';
        $next_wf_order_no = $nextWfStage ? $nextWfStage->order_no : '';
        
        $next_wf_stage_status = config('lms.WF_STAGE_STATUS.IN_PROGRESS');
        $updateWfStage=[];
        $updateWfStage['wf_status'] = $next_wf_stage_status;
        $this->lmsRepo->updateWfStage($next_wf_stage_id, $reqId, $updateWfStage);
        
        //Assign Request
        $reqStatus =  config('lms.REQUEST_STATUS.IN_PROCESS');
        $this->assignRequest($reqId, $nextWfStage, $reqStatus, $addlData);
        
        $updateReqData=[];
        $updateReqData['status'] = $reqStatus;
        $this->lmsRepo->saveApprRequestData($updateReqData, $reqId);
        
        return $nextWfStage;
    }
    
    protected function getRequestPrevStage($reqId)
    {
        $apprReqData = $this->lmsRepo->getApprRequestData($reqId);
        if(!$apprReqData) return false;
                
        $wf_stage_type = $apprReqData->req_type;
        
        //Get Current workflow stage
        $curWfStage = $this->lmsRepo->getCurrentWfStage($reqId);
        if (!$curWfStage) return false;
                
        $cur_wf_stage_code = $curWfStage ? $curWfStage->stage_code : '';
        $cur_wf_stage_id = $curWfStage ? $curWfStage->wf_stage_id : '';
        $cur_wf_order_no = $curWfStage ? $curWfStage->order_no : '';        
        
        //Get Previous workflow stage
        $prevWfStage = $this->lmsRepo->getPrevWfStage($wf_stage_type, $cur_wf_order_no);
        if (!$prevWfStage) return false;

        return $prevWfStage;
    }
    
    protected function getRequestNextStage($reqId)
    {
        $apprReqData = $this->lmsRepo->getApprRequestData($reqId);
        if(!$apprReqData) return false;
                
        $wf_stage_type = $apprReqData->req_type;
        
        //Get Current workflow stage
        $curWfStage = $this->lmsRepo->getCurrentWfStage($reqId);
        if (!$curWfStage) return false;
                
        $cur_wf_stage_code = $curWfStage ? $curWfStage->stage_code : '';
        $cur_wf_stage_id = $curWfStage ? $curWfStage->wf_stage_id : '';
        $cur_wf_order_no = $curWfStage ? $curWfStage->order_no : '';        
        
        //Get Previous workflow stage
        $nextWfStage = $this->lmsRepo->getNextWfStage($wf_stage_type, $cur_wf_order_no);
        if (!$nextWfStage) return false;

        return $nextWfStage;
    }    
    
    protected function moveRequestToPrevStage($reqId, $addlData=[])
    {
        $apprReqData = $this->lmsRepo->getApprRequestData($reqId);
        if(!$apprReqData) return false;
                
        $wf_stage_type = $apprReqData->req_type;
        
        //Get Current workflow stage
        $curWfStage = $this->lmsRepo->getCurrentWfStage($reqId);
        if (!$curWfStage) return false;
                
        $cur_wf_stage_code = $curWfStage ? $curWfStage->stage_code : '';
        $cur_wf_stage_id = $curWfStage ? $curWfStage->wf_stage_id : '';
        $cur_wf_order_no = $curWfStage ? $curWfStage->order_no : '';        
        
        //Get Previous workflow stage
        $prevWfStage = $this->lmsRepo->getPrevWfStage($wf_stage_type, $cur_wf_order_no);
        if (!$prevWfStage) return false;
        
        $prev_wf_stage_code = $prevWfStage ? $prevWfStage->stage_code : '';
        $prev_wf_stage_id = $prevWfStage ? $prevWfStage->wf_stage_id : '';
        $prev_wf_order_no = $prevWfStage ? $prevWfStage->order_no : '';
        
        for ($wf_order_no=$prev_wf_order_no;$wf_order_no<=$cur_wf_order_no;$wf_order_no++) {
            $wf_stage_status = config('lms.WF_STAGE_STATUS.IN_PROGRESS');
            
            $wfStage = $this->lmsRepo->getWfDetailByOrderNo($wf_stage_type, $wf_order_no);
            if ($wfStage) {
                $wf_stage_id = $wfStage->wf_stage_id;
                $updateWfStage=[];
                $updateWfStage['wf_status'] = $wf_stage_status;
                $this->lmsRepo->updateWfStage($wf_stage_id, $reqId, $updateWfStage);
            }
        }
        
        //Assign Request
        $reqStatus =  config('lms.REQUEST_STATUS.NEW_REQUEST');
        $this->assignRequest($reqId, $prevWfStage, $reqStatus, $addlData);
        
        $updateReqData=[];
        $updateReqData['status'] = $reqStatus;
        $this->lmsRepo->saveApprRequestData($updateReqData, $reqId);
        
        return $prevWfStage;
    }
    
    protected function calculateRefund($transId)
    {
        $repayment = Payment::getPayments(['is_settled' => 1, 'payment_id' => $transId])->first();
        $repayment = $repayment->get();
        $repaymentTrails = $this->lmsRepo->getTransactions(['payment_id'=>$transId]);
        
        $interestRefundTotal = 0;
        $interestOverdueTotal = 0;
        $marginTotal = 0;
        $nonFactoredAmount = 0;
        $totalTdsAmount = 0;
        
        foreach ($repaymentTrails as $key => $trans) {
            if($trans->entry_type == '1' && $trans->trans_type == config('lms.TRANS_TYPE.REFUND')){
                $interestRefundTotal +=$trans->refundoutstanding;
            }elseif($trans->entry_type == '1' && $trans->trans_type == config('lms.TRANS_TYPE.INTEREST_OVERDUE')){
                $interestOverdueTotal +=$trans->refundoutstanding;
            }elseif($trans->entry_type == '1' && $trans->trans_type == config('lms.TRANS_TYPE.MARGIN')){
                $marginTotal +=$trans->refundoutstanding;
            }elseif($trans->entry_type == '1' && $trans->trans_type == config('lms.TRANS_TYPE.NON_FACTORED_AMT')){
                $nonFactoredAmount +=$trans->refundoutstanding;
            }elseif($trans->entry_type == '1' && $trans->trans_type == config('lms.TRANS_TYPE.TDS')){
                $totalTdsAmount +=$trans->refundoutstanding;
            }
        }
        
        $refundableAmount = $nonFactoredAmount+$marginTotal+$interestRefundTotal+$totalTdsAmount;

        return ['repaymentTrails' => $repaymentTrails, 
        'repayment'=>$repayment,
        'nonFactoredAmount' => $nonFactoredAmount,
        'interestRefund'=>$interestRefundTotal,
        'interestOverdue'=>$interestOverdueTotal,
        'marginTotal'=>$marginTotal,
        'refundableAmount'=>$refundableAmount,
        'transId' => $transId
        ];
    }
    
    protected function saveRefundData($transId, $refundData=[])
    {
        if (empty($refundData)) {
            $refundData = $this->calculateRefund($transId);
        }
        
        $saveRefundData=[];
        $variables = $this->lmsRepo->getVariables();
        foreach($variables as $variable) {
            if (isset($refundData[$variable->name])) {
                $refund=[];
                if ($variable->name == 'MARGIN') {
                    foreach($refundData[$variable->name] as $key => $item) {
                        $refund['trans_id'] = $transId;
                        $refund['variable_id'] = $variable->id;                        
                        $refund['variable_type'] = $item['MARGIN_TYPE'];
                        $refund['variable_value'] = $item['MARGIN_PER_OR_FIXED'];
                        $refund['amount'] = $item['MARGIN_AMOUNT'];
                        
                        $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');

                        $refund['created_by'] = \Auth::user()->user_id;
                        $refund['created_at'] = $curData;
                        $refund['updated_by'] = \Auth::user()->user_id;
                        $refund['updated_at'] = $curData;
                        
                        $saveRefundData[] = $refund;
                    }
                } else {
                    $refund['trans_id'] = $transId;
                    $refund['variable_id'] = $variable->id;                    
                    $refund['variable_type'] = null;
                    $refund['variable_value'] = null;
                    $refund['amount'] = $refundData[$variable->name];
                    
                    $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
                    
                    $refund['created_by'] = \Auth::user()->user_id;
                    $refund['created_at'] = $curData;
                    $refund['updated_by'] = \Auth::user()->user_id;
                    $refund['updated_at'] = $curData;
                    
                    $saveRefundData[] = $refund;                        
                }                
            }
        }
        
        $result = $this->lmsRepo->saveRefundData($saveRefundData);
        return $result;
    }

    /**
     * Prepare Disbursal Data
     * 
     * @param array $data
     * @return mixed
     */
    protected function createDisbusalApiLogData($fileData, $response = [], $data = [])
    {
        /**
        * disburseType = 1 for online and 2 for manually
        */
        $disbursalData = [];
        
        $disbursalData['disbursal_batch_id'] = $data['disbursal_batch_id'] ?? null;

        $disbursalData['bank_type'] = $data['bank_type'] ?? null;
        $disbursalData['txn_id'] = $response['result']['header']['Tran_ID'] ?? null;
        $disbursalData['enq_txn_id'] = $data['enq_txn_id'] ?? $disbursalData['txn_id'] ?? null;
        $disbursalData['url'] = $response['result']['url'] ?? null;
        
        $disbursalData['header'] = $response['result']['http_header'] ?? null;
        $disbursalData['req_text'] = $response['result']['payload'] ?? null ;
        $disbursalData['res_text'] = $response['result']['response'] ?? null;
        $disbursalData['file_id'] = $fileData['file_id'] ?? null;            
        $disbursalData['status'] = ($response['status'] == 'success') ? 1 : 0;

        $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
                        
        $disbursalData['created_by'] = Auth::user()->user_id ?? 0;
        $disbursalData['created_at'] = $curData;
        return $disbursalData;
    }
    /**
     * Prepare Nach Req Data
     * 
     * @param array $data
     * @return mixed
     */
    protected function createNachReqData($data = [], $nachBatchId = false)
    {
        $reqData = [];
        
        $reqData['req_batch_id'] = $nachBatchId ?? null;
        $reqData['users_nach_id'] = $data['users_nach_id'] ?? null;
        $reqData['user_id'] = $data['user_id'] ?? null;
        $reqData['ref_no'] = $data['Customer_Transaction_ref_Number'] ?? null ;
        $reqData['req_date'] = $data['Settlement_date'] ?? null;
        $reqData['amount'] = $data['Amount'] ?? null;            
        $reqData['status'] = 1;
        $reqData['comment'] = '';

        $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
                        
        $reqData['created_by'] = Auth::user()->user_id;
        $reqData['created_at'] = $curData;
        return $reqData;
    }

    /**
     * Prepare Nach Req Data
     * 
     * @param array $data
     * @return mixed
     */
    protected function createNachTransData($data = [], $nachReqId = false)
    {
        $reqData = [];
        
        $reqData['nach_repayment_req_id'] = $nachReqId ?? null;
        $reqData['trans_id'] = $data['trans_id'] ?? null;
        $reqData['amt'] = $data['amount'] ?? null ;
        $reqData['status'] = 1;

        $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
                        
        $reqData['created_by'] = Auth::user()->user_id;
        $reqData['created_at'] = $curData;
        return $reqData;
    }
    
    protected function calDiffDays($pastDate = '', $nowDate = '')
    {
        $now = strtotime((isset($pastDate)) ? $pastDate : ''); // or your date as well
        $your_date = strtotime((isset($nowDate)) ? $nowDate : '');
        $datediff = abs($now - $your_date);

        $days = round($datediff / (60 * 60 * 24));
        return $days;        
    }

    protected function calDifferenceInDays($pastDate = '', $nowDate = '') {
        $pastDateTime = Carbon::parse($pastDate ?? null);
        $nowDateTime = Carbon::parse($nowDate ?? null);
        if ($pastDateTime === null || $nowDateTime === null) {
            throw new \Exception('Invalid past date or now date');
        }
        $pastDateTimeString = $pastDateTime->format('Y-m-d');
        $nowDateTimeString = $nowDateTime->format('Y-m-d');
        return Carbon::parse($pastDateTimeString)->diffInDays($nowDateTimeString);
    }
    /**
     * Calculate upfront interest for a given invoice
     * @param  array $invoice
     * @return float|null
     */
    protected function calculateUpfrontInterest($invoice){
        if (!$invoice['program_offer'] || $invoice['program_offer']['payment_frequency'] != 1) {
            return null;
        }
        $tenor = $this->calDifferenceInDays($invoice['invoice_due_date'],$invoice['invoice_date']);
        $margin = $this->calMargin($invoice['invoice_approve_amount'], $invoice['program_offer']['margin']);
        $fundedAmount = $invoice['invoice_approve_amount'] - $margin;
        $interestRate = (float) $invoice['program_offer']['interest_rate'];
        if ($invoice['program_offer']['benchmark_date'] == 1) {
            $currentDate =  \Helpers::getSysStartDate();
            $curDate = Carbon::parse($currentDate)->setTimezone(config('common.timezone'))->format('Y-m-d');
            $tenor = $this->calDifferenceInDays($invoice['invoice_due_date'], $curDate);
        }
        $upfrontInterest = $this->calInterest($fundedAmount, $interestRate, $tenor);
        return round($upfrontInterest, config('lms.DECIMAL_TYPE')['AMOUNT_TWO_DECIMAL']);
    }
}
