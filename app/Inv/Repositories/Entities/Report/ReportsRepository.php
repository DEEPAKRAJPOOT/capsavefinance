<?php

namespace App\Inv\Repositories\Entities\Report;

use DB;
use Session;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Http\Requests\Request;
use App\Inv\Repositories\Models\Payment;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Models\Master\State;
use App\Inv\Repositories\Models\AppProgramLimit;
use App\Inv\Repositories\Models\Lms\UserInvoice;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\User as UserModel;
use App\Inv\Repositories\Contracts\ReportInterface;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursed;
use App\Inv\Repositories\Models\Lms\UserInvoiceTrans;
use App\Inv\Repositories\Models\LmsUser;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;


/**
 * User Invoice Repository class
 */
class ReportsRepository extends BaseRepositories implements ReportInterface {
	use CommonRepositoryTraits;

	public function __construct() {
	   parent::__construct();
	}

	
	/**
	 * Create method
	 *
	 * @param array $attributes
	 */
	protected function create(array $attributes) {        
	}

	/**
	 * Update method
	 *
	 * @param array $attributes
	 */
	protected function update(array $attributes, $id) {        
	}

	public function leaseRegisters($whereCondition=[], $whereRawCondition = NULL) {
		return UserInvoiceTrans::leaseRegisters($whereCondition, $whereRawCondition);
	}

	public function getInterestBreakupReport($whereCondition=[], $whereRawCondition = NULL) {
		return Transactions::getInterestBreakupReport($whereCondition, $whereRawCondition);
	}

	public function getChargeBreakupReport($whereCondition=[], $whereRawCondition = NULL) {
		return Transactions::getchargeBreakupReport($whereCondition, $whereRawCondition);
	}

	public function gettdsBreakupReport($whereCondition=[], $whereRawCondition = NULL) {
		return Transactions::gettdsBreakupReport($whereCondition, $whereRawCondition);
	}
	
	public function getCustomerDetail($userId) {
		$result = UserModel::getCustomerDetail((int) $userId);
		return $result ?: false;
	}
    
	public function tds($whereCondition=[], $whereRawCondition = NULL) {
		return Payment::getAllTdsTransaction($whereCondition, $whereRawCondition);
	}

	public function getMaturityReport($whereCondition=[], &$sendMail){
		$curdate = Helper::getSysStartDate();
		$curdate = Carbon::parse($curdate)->format('Y-m-d');

		$invDisbList = InvoiceDisbursed::with(['transactions' => function($query2){
			$query2->whereNull('payment_id')
			->whereNull('link_trans_id')
			->whereNull('parent_trans_id')
			->where('trans_type',config('lms.TRANS_TYPE.PAYMENT_DISBURSED'))
			->where('entry_type','0');
		},
		'invoice'=>function($query2) use($whereCondition){
			if(isset($whereCondition['anchor_id'])){
				$query2->where('anchor_id',$whereCondition['anchor_id']);
			}
		},
		'invoice.lms_user', 'invoice.business', 'disbursal'])
		->whereIn('status_id', [12,13,15,47])
		->whereHas('invoice', function($query3) use($whereCondition){
			if(isset($whereCondition['anchor_id'])){
				$query3->where('anchor_id',$whereCondition['anchor_id']);
			}
		})
		->whereDate('payment_due_date','>',$curdate)
		->get();
		
		$sendMail = ($invDisbList->count() > 0)?true:false;
		
		$result = [];
		foreach($invDisbList as $invDisb){

			$payment_due_date = date('Y-m-d',strtotime($invDisb->payment_due_date));
			$out_days =  (strtotime($payment_due_date) - strtotime($curdate))/86400 + $invDisb->grace_period;	

			$result[] = [
			'cust_name'=>$invDisb->invoice->business->biz_entity_name,
			'loan_ac'=>config('common.idprefix.APP').$invDisb->invoice->app_id,
			'virtual_ac'=>$invDisb->invoice->lms_user->virtual_acc_id,
			'trans_date'=>$invDisb->disbursal->disburse_date,
			'trans_no'=>$invDisb->disbursal->tran_id,
			'invoice_no'=>$invDisb->invoice->invoice_no,
			'invoice_date'=>$invDisb->invoice->invoice_date,
			'invoice_amt'=>$invDisb->invoice->invoice_amount,
			'margin_amt'=>$invDisb->invoice->invoice_approve_amount*$invDisb->margin/100,
			'disb_amt'=>$invDisb->invoice->invoice_amount,
			'out_amt'=>$invDisb->transactions->sum('outstanding'),
			'out_days'=>($out_days > 0)?0:$out_days,
			'tenor'=>$invDisb->tenor_days,
			'due_date'=>$invDisb->payment_due_date,
			'due_amt'=>$invDisb->invoice->invoice_amount,
			'od_days'=>$invDisb->accruedInterest()->whereNotNull('overdue_interest_rate')->get()->count(),
			'od_amt'=>$invDisb->accruedInterest()->whereNotNull('overdue_interest_rate')->sum('accrued_interest'),
			'remark'=>$invDisb->invoice->remark
			];
		}
		return $result;
	}

	public function getDisbursalReport($whereCondition=[], &$sendMail){
		$curdate = Carbon::now()->format('Y-m-d');
		$curdate = Carbon::parse($curdate)->addDays(1)->format('Y-m-d');
        $fromdate = Carbon::parse($curdate)->subDays(30)->format('Y-m-d');

        $invDisbList = InvoiceDisbursed::with([
		'transactions' => function($query2){
			$query2->whereNull('payment_id')
			->whereNull('link_trans_id')
			->whereNull('parent_trans_id')
			->where('trans_type',config('lms.TRANS_TYPE.PAYMENT_DISBURSED'))
			->where('entry_type','0');
		},
		'invoice'=>function($query2) use($whereCondition, $fromdate, $curdate){
			if(isset($whereCondition['anchor_id'])){
				$query2->where('anchor_id',$whereCondition['anchor_id']);
			}
		},
		'invoice.lms_user', 
		'invoice.business', 
		'disbursal' => function($query22) use($fromdate, $curdate){
			$query22->whereBetween('funded_date', [$fromdate, $curdate]);
		}])
		->whereIn('status_id', [12,13,15,47])
        ->whereHas('invoice', function($query3) use($whereCondition, $fromdate, $curdate){
			if(isset($whereCondition['anchor_id'])){
				$query3->where('anchor_id',$whereCondition['anchor_id']);
			}
		})
		->whereHas('disbursal', function($query33) use($fromdate, $curdate){
			$query33->whereBetween('funded_date', [$fromdate, $curdate]);
		})
		->get();

		$sendMail = ($invDisbList->count() > 0)?true:false;
		$result = [];
		
		foreach($invDisbList as $invDisb){
			
			$salesDetails = isset($invDisb->invoice->anchor->sales_user_id) ? $this->getCustomerDetail($invDisb->invoice->anchor->sales_user_id) : null;
			$financeType = '';
			if($invDisb->invoice->program->prgm_type == 1) {
				$financeType = 'Vendor Finance';
			}
			if($invDisb->invoice->program->prgm_type == 2) {
				$financeType = 'Channel Finance';
			}

			$interestAmount = 0;
			$fromDate = null;
			$toDate = null;
			$intrstRecDate = null;
			$interestBorneBy = $invDisb->invoice->program_offer->program->interest_borne_by;
			$paymentFrequency = $invDisb->invoice->program_offer->payment_frequency;

			if(($paymentFrequency == '1' && $interestBorneBy == '2' ) && (strtotime($invDisb->payment_due_date) >= strtotime($curdate)) ) {
				$intrstRecDate = $invDisb->int_accrual_start_dt;
				$interestAmount = $invDisb->total_interest;
				$fromDate = $invDisb->int_accrual_start_dt;
				$toDate = $invDisb->payment_due_date;
			} else {
				$interestAmount = $invDisb->sumInterestAccured($invDisb->invoice_disbursed_id);
				$fromDate = $invDisb->interests->min('interest_date');
				$toDate = $invDisb->interests->max('interest_date');
			}
			
			$disbursement_method = $paymentFrequency == 1 && in_array($interestBorneBy, [1,2]) ? ($interestBorneBy == 1 ? 'Gross' : 'Net') : '---';
			
			$tds_proce_fee = $invDisb->transactions->first()?$invDisb->transactions->first()->tdsProcessingFee():0;
			$result[] = [
			'cust_name'=>$invDisb->invoice->business->biz_entity_name,
			'rm_sales'=>isset($salesDetails) ? $salesDetails->f_name . ' ' . $salesDetails->l_name : '',
			'anchor_name'=>isset($invDisb->invoice->anchor) ? $invDisb->invoice->anchor->comp_name : '',
			'anchor_prgm_name'=>isset($invDisb->invoice->program) ? $invDisb->invoice->program->prgm_name : '',
			// 'vendor_ben_name'=>$invDisb->invoice->supplier_bank_detail->acc_name ?? '', // Amit sir
			'vendor_ben_name'=>$invDisb->disbursal->account_holder_name ?? '', // Amit sir
			'region'=>$invDisb->invoice->supplier_bank_detail->branch_name ?? '', // Amit sir
			'sanction_number'=>$invDisb->invoice->app_id,
			'sanction_date'=> $invDisb->invoice->appStatusLog->first()->created_at,
			'sanction_amount'=>$invDisb->invoice->program_offer->programLimit->limit_amt,
			'status'=>'', // blank
			'disbursal_month'=>$invDisb->disbursal->funded_date,
			'disburse_amount'=>$invDisb->disburse_amt,
			'disbursement_date'=>$invDisb->disbursal->funded_date,
			'disbursal_utr'=>$invDisb->disbursal->tran_id,
			'disbursal_act_no'=>$invDisb->disbursal->acc_no,
			'disbursal_ifc'=>$invDisb->disbursal->ifsc_code,
			'type_finance'=>$financeType,
			'supl_chan_type'=>$invDisb->invoice->program_offer->getFrequencyName(),
			'tenor'=>$invDisb->tenor_days,
			'interest_rate'=>$invDisb->interest_rate,
			'interest_amt'=>$interestAmount,
			'from'=>$fromDate,
			'to'=>$toDate,
			'tds_intrst'=>$invDisb->transactions->where('trans_type',7)->where('entry_type',1)->sum('amount'),
			'net_intrst'=>$interestAmount - ($invDisb->transactions->where('trans_type',7)->where('entry_type',1)->sum('amount')),
			'intrst_rec_date'=>$intrstRecDate, // blank
			'proce_fee'=>$invDisb->transactions->where('trans_type',62)->where('entry_type',0)->sum('base_amt'),
			'proce_amt'=>$invDisb->transactions->where('trans_type',62)->where('entry_type',0)->sum('base_amt'),
			'proce_fee_gst'=>$invDisb->transactions->where('trans_type',62)->where('entry_type',0)->sum('amount'),
			'tds_proce_fee'=>$tds_proce_fee,
			'net_proc_fee_rec'=>($invDisb->transactions->where('trans_type',62)->where('entry_type',0)->sum('amount')) - $tds_proce_fee,
			'proce_fee_rec'=>$invDisb->transactions->where('trans_type',62)->where('entry_type',1)->sum('amount'),
			'proce_fee_amt_date'=>'', // blank
			'balance'=> $invDisb->invoice->program_offer->programLimit->limit_amt - (($invDisb->transactions->where('trans_type',16)->where('entry_type',0)->sum('amount')) - ($invDisb->invoice->principal_repayment_amt)),
			'margin_amt'=>$invDisb->invoice->invoice_approve_amount*$invDisb->margin/100,
			'due_date'=>$invDisb->payment_due_date,
			'funds_received'=>'', // blank
			'principal_rece'=>$invDisb->transactions->where('trans_type',16)->where('entry_type',0)->sum('amount'),
			'received'=>$invDisb->invoice->principal_repayment_amt,
			'net_receivalble'=> ($invDisb->transactions->where('trans_type',16)->where('entry_type',0)->sum('amount')) - ($invDisb->invoice->principal_repayment_amt), /* AL-AM */
			'adhoc_int'=>'',
			'net_disbursement'=>$invDisb->invoice->invoice_amount,
			'gross'=>'', // blank
			'net_of_interest'=>'', // blank
			'interest_borne_by'=> $interestBorneBy == 1 ? 'Anchor' : ($interestBorneBy == 2 ? 'Customer' : ''),
			'customer_id'=> $invDisb->customer_id,
			'invoice_no'=> $invDisb->invoice->invoice_no,
			'disbursement_method'=> $disbursement_method,
			'grace_period'=> $invDisb->grace_period,
			'anchor_address'=> isset($invDisb->invoice->anchor) ? $invDisb->invoice->anchor->comp_addr : '',

			// 'loan_ac'=>config('common.idprefix.APP').$invDisb->invoice->app_id,
			// 'trans_date'=>$invDisb->disbursal->disburse_date,
			// 'trans_no'=>$invDisb->disbursal->tran_id,
			// 'invoice_no'=>$invDisb->invoice->invoice_no,
			// 'invoice_date'=>$invDisb->invoice->invoice_date,
			// 'invoice_amt'=>$invDisb->invoice->invoice_amount,
			// 'margin_amt'=>$invDisb->invoice->invoice_approve_amount*$invDisb->margin/100,
			// 'disb_amt'=>$invDisb->invoice->invoice_amount,
			/*'out_amt'=>$invDisb->transactions->sum('outstanding'),
			'out_days'=>(strtotime($invDisb->payment_due_date) - strtotime($curdate))/86400,
			'tenor'=>$invDisb->tenor_days,
			'due_date'=>$invDisb->payment_due_date,
			'due_amt'=>$invDisb->invoice->invoice_amount,*/
			// 'trans_utr'=>$invDisb->disbursal->tran_id,
			// 'remark'=>$invDisb->invoice->remark
			];
		}
		
		return $result;
	}

    public function getUtilizationReport($whereCondition=[], &$sendMail){
		ini_set("memory_limit", "-1");
		$curdate = Helper::getSysStartDate();
		$curdate = Carbon::parse($curdate)->format('Y-m-d');

        $invList = BizInvoice::whereIn('status_id',[8,9,10,12,13,15])
        ->with('invoice_disbursed');
        if(isset($whereCondition['anchor_id'])){
			$invList->where('anchor_id',$whereCondition['anchor_id']);
		}

        $invList = $invList->get();

		
		$sendMail = ($invList->count() > 0)?true:false;

		$result = [];
		$odCustCnt = [];
		foreach($invList as $inv){
			$overdueAmt = 0;
            $overdueDays = 0;
            $marginRate = null;
			$principalOdAmount = 0;
			$principalOdDays = 0;
			$invDisb = $inv->invoice_disbursed;
            if($invDisb){
				$overdueAmt = $invDisb->accruedInterest()->whereNotNull('overdue_interest_rate')->sum('accrued_interest');
                $overdueDays = $invDisb->accruedInterest()->whereNotNull('overdue_interest_rate')->get()->count();
                $marginRate = $invDisb->margin;
				$disbAmt = round($invDisb->disburse_amt,2) ?? 0;
				$prinRePayAmt = round($inv->principal_repayment_amt,2) ?? 0;
				$principalOdAmount = round(($disbAmt - $prinRePayAmt),2);
				$principalOdAmount = $principalOdAmount > 0 ? $principalOdAmount : 0;
				if(strtotime($invDisb->payment_due_date) <= strtotime($curdate) && $principalOdAmount > 0){
					$date = Carbon::parse($invDisb->payment_due_date);
					$now = Carbon::parse($curdate);
					$principalOdDays = $date->diffInDays($now);
				}

			}

            $invDetails =  $inv;
            $offerDetails = $invDetails->program_offer->toArray();
            $offerDetails['user_id'] = $invDetails->supplier_id;
            $prgmDetails = $invDetails->program;
            $anchorDetails = $invDetails->anchor;
            $salesUserDetails = $anchorDetails->salesUser;

            if(!$marginRate){
				$marginRate = $offerDetails['margin'];
            }

			if($overdueDays > 0){
				$odCustCnt[$invDetails->program_id][$invDetails->supplier_id] = 1; 
			}

			$sanctionCase[$invDetails->program_id][] = $invDetails->supplier_id.'-'.$invDetails->app_id;
			$test[$invDetails->program_id][$invDetails->supplier_id.'-'.$invDetails->app_id][] = $invDetails->invoice_id;
			$result[$invDetails->program_id]['anchor_name'] =  $anchorDetails->comp_name;
			$result[$invDetails->program_id]['prgm_name'] =  $prgmDetails->parentProgram->prgm_name;
			$result[$invDetails->program_id]['sub_prgm_name'] =  $prgmDetails->prgm_name;
			$result[$invDetails->program_id]['prgm_type'] =  $prgmDetails->prgm_type == 1 ? 'Vendor Finance' : ($prgmDetails->prgm_type == 2 ? 'Channel Finance' : '');
			$result[$invDetails->program_id]['client_sanction'] =  count(array_unique($sanctionCase[$invDetails->program_id]));
			$result[$invDetails->program_id]['ttl_od_customer'] =  count($odCustCnt[$invDetails->program_id]??[]);
			$result[$invDetails->program_id]['ttl_od_amt'] = ($result[$invDetails->program_id]['ttl_od_amt']??0) + $overdueAmt;
			$result[$invDetails->program_id]['disbursement'][$invDetails->supplier_id.'-'.$invDetails->app_id]['client_name'] = $invDetails->business->biz_entity_name;
			$result[$invDetails->program_id]['disbursement'][$invDetails->supplier_id.'-'.$invDetails->app_id]['loan_ac'] = config('common.idprefix.APP').$invDetails->app_id;
			$result[$invDetails->program_id]['disbursement'][$invDetails->supplier_id.'-'.$invDetails->app_id]['user_id'] = $invDetails->lms_user->customer_id;
			$result[$invDetails->program_id]['disbursement'][$invDetails->supplier_id.'-'.$invDetails->app_id]['virtual_ac'] = $invDetails->lms_user->virtual_acc_id;
			$result[$invDetails->program_id]['disbursement'][$invDetails->supplier_id.'-'.$invDetails->app_id]['client_sanction_limit'] = $offerDetails['prgm_limit_amt'];
			// $result[$invDetails->program_id]['disbursement'][$invDetails->supplier_id.'-'.$invDetails->app_id]['limit_utilize'] = Helper::invoiceAnchorLimitApprove($offerDetails);
			$result[$invDetails->program_id]['disbursement'][$invDetails->supplier_id.'-'.$invDetails->app_id]['limit_utilize'] = Helper::anchorSupplierPrgmUtilizedLimitByInvoice($offerDetails);
			$result[$invDetails->program_id]['disbursement'][$invDetails->supplier_id.'-'.$invDetails->app_id]['end_date'] = $invDetails->app->appLimit->end_date??'';
			$result[$invDetails->program_id]['disbursement'][$invDetails->supplier_id.'-'.$invDetails->app_id]['sub_prgm_name']= $prgmDetails->prgm_name;
			$result[$invDetails->program_id]['disbursement'][$invDetails->supplier_id.'-'.$invDetails->app_id]['limit_available'] = $result[$invDetails->program_id]['disbursement'][$invDetails->supplier_id.'-'.$invDetails->app_id]['client_sanction_limit'] - $result[$invDetails->program_id]['disbursement'][$invDetails->supplier_id.'-'.$invDetails->app_id]['limit_utilize'];
			$result[$invDetails->program_id]['disbursement'][$invDetails->supplier_id.'-'.$invDetails->app_id]['sales_person_name']= ($salesUserDetails->f_name.' '. $salesUserDetails->m_name.' '. $salesUserDetails->l_name);
			$result[$invDetails->program_id]['disbursement'][$invDetails->supplier_id.'-'.$invDetails->app_id]['invoice'][$invDetails->invoice_id] = [
				'invoice_no' => $invDetails->invoice_no,
				'invoice_date' => $invDetails->invoice_date,
				'invoice_amt' => $invDetails->invoice_amount,
				'margin_amt' => $invDetails->invoice_approve_amount*$marginRate/100,
				'approve_amt' => $invDetails->invoice_approve_amount,
				'disb_amt' => in_array($invDetails->status_id, [12,13,15])?$invDisb->disburse_amt:0,
				'od_days'=>$overdueDays,
				'od_amt'=> $overdueAmt,
				'principal_od_days' => $principalOdDays,
				'principal_od_amount' => $principalOdAmount,
			];
			
		}
		
		return $result;
	}

	private function getOverdueData($date){
		$result = [];

		$overdueData = DB::select('SELECT dr.invoice_disbursed_id,  ROUND((IFNULL(dr.od,0) - IFNULL(cr.od,0)),2) AS ttl_od, dr.cnt_od FROM ( SELECT invoice_disbursed_id, SUM(accrued_interest) AS od, COUNT(interest_accrual_id) AS cnt_od FROM rta_interest_accrual WHERE overdue_interest_rate IS NOT NULL AND interest_date <= ? GROUP BY invoice_disbursed_id ) AS dr LEFT JOIN ( SELECT a.`user_id`, a.`invoice_disbursed_id`, SUM(a.amount) AS od FROM rta_transactions AS a JOIN rta_transactions AS b ON a.`parent_trans_id` = b.`trans_id` WHERE b.`trans_type` = ? AND b.`entry_type` = ? AND a.`trans_type` IN (7,33,36) AND DATE(a.trans_date) <= ? GROUP BY a.`user_id`, a.`invoice_disbursed_id` ) AS cr ON cr.invoice_disbursed_id = dr.invoice_disbursed_id',[$date,'33','0',$date]);

		foreach($overdueData as $od){
			$result[$od->invoice_disbursed_id]['overdue'] = $od->ttl_od;
			$result[$od->invoice_disbursed_id]['days'] = $od->cnt_od;
		}
		return $result;
	}

	private function getInterestData($date){
		$result = [];

		$interestData = DB::select('SELECT dr.invoice_disbursed_id,  ROUND((IFNULL(dr.od,0) - IFNULL(cr.od,0)),2) AS ttl_od, dr.cnt_od FROM ( SELECT invoice_disbursed_id, SUM(accrued_interest) AS od, COUNT(interest_accrual_id) AS cnt_od FROM rta_interest_accrual WHERE interest_rate IS NOT NULL AND interest_date <= ? GROUP BY invoice_disbursed_id ) AS dr LEFT JOIN ( SELECT a.`user_id`, a.`invoice_disbursed_id`, SUM(a.amount) AS od FROM rta_transactions AS a JOIN rta_transactions AS b ON a.`parent_trans_id` = b.`trans_id` WHERE b.`trans_type` = ? AND b.`entry_type` = ? AND a.`trans_type` IN (7,9,36) AND DATE(a.trans_date) <= ? GROUP BY a.`user_id`, a.`invoice_disbursed_id` ) AS cr ON cr.invoice_disbursed_id = dr.invoice_disbursed_id',[$date,'9','0',$date]);

		foreach($interestData as $int){
			$result[$int->invoice_disbursed_id]['interest'] = ($int->ttl_od > 0)?$int->ttl_od:0;
			$result[$int->invoice_disbursed_id]['days'] = $int->cnt_od;
		}
		return $result;
	}

	private function getOutstandingData($date){
        $result = [];
		$invDisbData = DB::select("SELECT dr.invoice_disbursed_id, IFNULL(SUM(IF(dr.trans_type = 16, dr.amount,0)),0) + IFNULL(SUM(IF(dr.trans_type = 16, cdr.amount,0)),0)  AS priDr, SUM(IF(dr.trans_type = 16, cr.amount,0)) AS priCr, IFNULL(SUM(IF(dr.trans_type = 9, dr.amount,0)),0) + IFNULL(SUM(IF(dr.trans_type = 9, cdr.amount,0)),0) AS intDr, SUM(IF(dr.trans_type = 9, cr.amount,0)) AS intCr, IFNULL(SUM(IF(dr.trans_type = 33, dr.amount,0)),0) + IFNULL(SUM(IF(dr.trans_type = 33, cdr.amount,0)),0) AS odiDr, SUM(IF(dr.trans_type = 33, cr.amount,0)) AS odiCr, (SELECT COUNT(`interest_accrual_id`) FROM rta_interest_accrual WHERE overdue_interest_rate IS NOT NULL AND `invoice_disbursed_id` = dr.invoice_disbursed_id AND DATE(interest_date) <= '".$date."') AS odDays FROM ( SELECT `invoice_disbursed_id`,`trans_type`, SUM(`amount`) AS amount FROM rta_transactions WHERE `trans_type` IN (9,16 ,33) AND entry_type = '0' AND DATE(`trans_date`) <= '".$date."' GROUP BY `invoice_disbursed_id`, `trans_type` ) AS dr LEFT JOIN ( SELECT a.`invoice_disbursed_id`, a.`trans_type`, SUM(b.`amount`)AS amount FROM rta_transactions AS a LEFT JOIN rta_transactions AS b ON a.`trans_id` = b.`parent_trans_id` WHERE a.`trans_type` IN (9,16,33) AND b.`entry_type` = '1' AND DATE(a.`trans_date`) <= '".$date."' AND DATE(b.`trans_date`) <= '".$date."' GROUP BY a.`invoice_disbursed_id`,a.`trans_type` ) AS cr ON dr.invoice_disbursed_id = cr.invoice_disbursed_id AND dr.trans_type = cr.trans_type LEFT JOIN ( SELECT a.`invoice_disbursed_id`, a.`trans_type`, SUM(b.`amount`)AS amount FROM rta_transactions AS a LEFT JOIN rta_transactions AS b ON a.`trans_id` = b.`parent_trans_id` WHERE a.`trans_type` IN (9,16,33) AND b.`entry_type` = '0' AND DATE(a.`trans_date`) <= '".$date."' AND DATE(b.`trans_date`) <= '".$date."' GROUP BY a.`invoice_disbursed_id`,a.`trans_type` ) AS cdr ON cdr.invoice_disbursed_id = cr.invoice_disbursed_id AND cdr.trans_type = cr.trans_type GROUP BY dr.invoice_disbursed_id");

        foreach($invDisbData as $invDisb){
            $result[$invDisb->invoice_disbursed_id] = [
                'priDr' =>  ($invDisb->priDr > 0)?$invDisb->priDr:0,
                'priCr' =>  ($invDisb->priCr > 0)?$invDisb->priCr:0,
                'intDr' =>  ($invDisb->intDr > 0)?$invDisb->intDr:0,
                'intCr' =>  ($invDisb->intCr > 0)?$invDisb->intCr:0,
                'odiDr' =>  ($invDisb->odiDr > 0)?$invDisb->odiDr:0,
                'odiCr' =>  ($invDisb->odiCr > 0)?$invDisb->odiCr:0,
                'odDays' => ($invDisb->odDays > 0)?$invDisb->odDays:0,
            ];
        }

        unset($invDisbData);
        return $result;
    }
    
	public function getOverdueReportManual($whereCondition=[], &$sendMail){
		$curdate = Helper::getSysStartDate();
		$curdate = Carbon::parse($curdate)->format('Y-m-d');
		$curdate = $whereCondition['to_date']??$curdate;
		$invDisbList = InvoiceDisbursed::
		with([
			'invoice'=>function($query2) use($whereCondition){
					if(isset($whereCondition['anchor_id'])){
						$query2->where('anchor_id',$whereCondition['anchor_id']);
					}
					if(isset($whereCondition['user_id'])){
						$query2->where('supplier_id',$whereCondition['user_id']);
					}
				},
			'invoice.lms_user', 
			'invoice.anchor', 
			'invoice.program_offer',
			'invoice.business', 
			'disbursal'
		])
		->whereIn('status_id', [12,13,15,47])
		->whereHas('invoice', function($query3) use($whereCondition){
			if(isset($whereCondition['anchor_id'])){
				$query3->where('anchor_id',$whereCondition['anchor_id']);
			}
			if(isset($whereCondition['user_id'])){
				$query3->where('supplier_id',$whereCondition['user_id']);
			}
		})
		->whereDate('int_accrual_start_dt','<=',$curdate)
        ->get();

		$outstandingData = self::getOutstandingData($curdate);
		$sendMail = ($invDisbList->count() > 0)?true:false;
		$result = [];
		foreach($invDisbList as $invDisb){
			if(!isset($outstandingData[$invDisb->invoice_disbursed_id])){
				continue;
			}
			$principalDr = $outstandingData[$invDisb->invoice_disbursed_id]['priDr'];
			$principalCr = $outstandingData[$invDisb->invoice_disbursed_id]['priCr'];
			$principalOut = round((round($principalDr,2) - round($principalCr,2)),2);
			
			$interestDr = $outstandingData[$invDisb->invoice_disbursed_id]['intDr'];
			$interestCr = $outstandingData[$invDisb->invoice_disbursed_id]['intCr'];
			$interestOut = round((round($interestDr,2) - round($interestCr,2)),2);

			$overdueInterestDr = $outstandingData[$invDisb->invoice_disbursed_id]['odiDr'];
			$overdueInterestCr = $outstandingData[$invDisb->invoice_disbursed_id]['odiCr'];
			$overdueInterestOut = round((round($overdueInterestDr,2) - round($overdueInterestCr,2)),2);

			$curOddays = $outstandingData[$invDisb->invoice_disbursed_id]['odDays'];
			$odDaysWithoutGrace = ($curOddays)?$curOddays - $invDisb->grace_period:0;

			$diff=date_diff(date_create($invDisb->payment_due_date),date_create($curdate));
			$maturityDays = $diff->format("%r%a");
			$invDetails = $invDisb->invoice;
			$anchor_name = $invDetails->anchor->comp_name;
			$offerDetails = $invDetails->program_offer->toArray();
			$offerDetails['user_id'] = $invDetails->supplier_id;
			$prgmDetails = $invDetails->program;
			$payment_frequency='none';
			$disbursement_method = 'Gross';
			if($invDisb->invoice->program_offer->payment_frequency == '1'){
				$payment_frequency = 'Up Front';
				if($prgmDetails->interest_borne_by == 2){
					$disbursement_method = 'Net';
				}
			}else if($invDisb->invoice->program_offer->payment_frequency == '2'){
				$payment_frequency = 'Monthly';
			}else if($invDisb->invoice->program_offer->payment_frequency == '3'){
				$payment_frequency = 'Rear Ended';
			}
			// $limitUsed[$offerDetails['prgm_offer_id']] = $limitUsed[$offerDetails['prgm_offer_id']] ?? round(Helper::invoiceAnchorLimitApprove($offerDetails),2);
			$limitSanctioned[$invDisb->invoice->supplier_id] = $limitSanctioned[$invDisb->invoice->supplier_id] ?? Helper::getCustomerSanctionedAmt($invDisb->invoice->supplier_id);
			$limitUsed[$invDisb->invoice->supplier_id] = $limitUsed[$invDisb->invoice->supplier_id] ?? Helper::getCustomerUtilizedAmt($invDisb->invoice->supplier_id);
			$limitAvl[$invDisb->invoice->supplier_id] = $limitAvl[$invDisb->invoice->supplier_id] ?? $limitSanctioned[$invDisb->invoice->supplier_id] - $limitUsed[$invDisb->invoice->supplier_id];
			$limitAvl[$invDisb->invoice->supplier_id] = ($limitAvl[$invDisb->invoice->supplier_id] > 0) ? $limitAvl[$invDisb->invoice->supplier_id] : 0; 
			$result[$invDisb->invoice_disbursed_id] = [
				'loan_ac'=>config('common.idprefix.APP').$invDisb->invoice->app_id,
				'cust_name' => $invDisb->invoice->business->biz_entity_name,
				'customer_id' => $invDisb->invoice->lms_user->customer_id??null,
				'anchor_name'=> $anchor_name,
				'invoice_no' => $invDisb->invoice->invoice_no,
				'disbursement_date'=>isset($invDisb->disbursal->funded_date) ? Carbon::parse($invDisb->disbursal->funded_date)->format('Y-m-d'):'',
				'payment_due_date' => $invDisb->payment_due_date,
				'virtual_ac' => $invDisb->invoice->lms_user->virtual_acc_id,
				'payment_frequency'=>$payment_frequency,
				'disburse_amount'=>$invDisb->disburse_amt,
				'interest_amount'=>number_format($invDisb->total_interest,2),
				'disbursement_method' => $disbursement_method,
				'client_sanction_limit' => $limitSanctioned[$invDisb->invoice->supplier_id],
				'limit_available' => $limitAvl[$invDisb->invoice->supplier_id],
				'utilized_amt'=> $limitUsed[$invDisb->invoice->supplier_id],
				'tenure' =>	$invDisb->tenor_days,
				'roi' => $invDisb->interest_rate,
				'roodi' => $invDisb->overdue_interest_rate,
				'principalOut' => $principalOut,
				'interestOut' => $interestOut,
				'overdueDays' => $curOddays,
				'overdueOut' => $overdueInterestOut,
				'soa_balance' => $principalOut + $interestOut + $overdueInterestOut,
				'grace_period' => $invDisb->grace_period,
				'odDaysWithoutGrace' => $odDaysWithoutGrace,
				'maturityDays' => $maturityDays
				//'sales_person_name'=> ($invDisb->invoice->anchor->salesUser->f_name.' '. $invDisb->invoice->anchor->salesUser->m_name.' '. $invDisb->invoice->anchor->salesUser->l_name)
			];
			$result[$invDisb->invoice_disbursed_id]['maxBucOdDaysWithoutGrace'] = $result[$invDisb->invoice_disbursed_id]['maxBucOdDaysWithoutGrace'] ?? 0;
			if($principalOut > 100 && ($result[$invDisb->invoice_disbursed_id]['maxBucOdDaysWithoutGrace'] ?? 0) < $odDaysWithoutGrace){
				$result[$invDisb->invoice_disbursed_id]['maxBucOdDaysWithoutGrace'] = $odDaysWithoutGrace;
			}
		}
		return $result;
	}

	public function getOverdueReport($whereCondition=[], &$sendMail){
		$curdate = Helper::getSysStartDate();
		$curdate = Carbon::parse($curdate)->format('Y-m-d');

		$invDisbList = InvoiceDisbursed::with(['transactions' => function($query2){
			$query2->whereNull('payment_id')
			->whereNull('link_trans_id')
			->whereNull('parent_trans_id')
			->where('trans_type',config('lms.TRANS_TYPE.PAYMENT_DISBURSED'))
			->where('entry_type','0');
		},
		'invoice'=>function($query2) use($whereCondition){
			if(isset($whereCondition['anchor_id'])){
				$query2->where('anchor_id',$whereCondition['anchor_id']);
			}
			if(isset($whereCondition['user_id'])){
				$query2->where('supplier_id',$whereCondition['user_id']);
			}
		},
		'invoice.lms_user', 'invoice.business', 'disbursal','invoice.app.appLimit'])
		->whereIn('status_id', [12,13,15,47])
		->whereHas('invoice', function($query3) use($whereCondition){
			if(isset($whereCondition['anchor_id'])){
				$query3->where('anchor_id',$whereCondition['anchor_id']);
			}
			if(isset($whereCondition['user_id'])){
				$query3->where('supplier_id',$whereCondition['user_id']);
			}
		})
		->whereHas('accruedInterest', function($query3) use($whereCondition){
			if(isset($whereCondition['to_date'])){
				$query3->whereDate('interest_date','<=',$whereCondition['to_date']);
			}
		})
		->where('payment_due_date','<=',$curdate)
		->get();

		$sendMail = ($invDisbList->count() > 0)?true:false;

		$result = [];
		foreach($invDisbList as $invDisb){

			$overdue = $invDisb->accruedInterest()->whereNotNull('overdue_interest_rate');
			if(isset($whereCondition['to_date'])){
				$overdue  = $overdue->whereDate('interest_date','<=',$whereCondition['to_date']);
			}
			$overdue2  =   clone $overdue;
			$overdueDays = $overdue2->count();
			//$overdueAmt = $overdue->sum('accrued_interest');
			$overdueAmt = $invDisb->transactions()->where('trans_type',config('lms.TRANS_TYPE.INTEREST_OVERDUE'))->where('entry_type','0')->sum('outstanding');
			$runnTrans  = $invDisb->runningTransactions()->where('trans_type', config('lms.TRANS_TYPE.INTEREST_OVERDUE'))->where('entry_type', '0')->get();
			foreach($runnTrans as $runnTran){
				$overdueAmt += $runnTran->outstanding;
			}
			$outstandingAmt = $invDisb->transactions->sum('outstanding');
			$invDetails = $invDisb->invoice;
			$offerDetails = $invDetails->program_offer->toArray();
			$offerDetails['user_id'] = $invDetails->supplier_id;
			$prgmDetails = $invDetails->program;
			
			// $limitUsed[$offerDetails['prgm_offer_id']] = $limitUsed[$offerDetails['prgm_offer_id']] ?? round(Helper::invoiceAnchorLimitApprove($offerDetails),2);
			$limitUsed[$offerDetails['prgm_offer_id']] = $limitUsed[$offerDetails['prgm_offer_id']] ?? round(Helper::anchorSupplierPrgmUtilizedLimitByInvoice($offerDetails),2);
			$limitAvl[$offerDetails['prgm_offer_id']] = $limitAvl[$offerDetails['prgm_offer_id']] ?? $offerDetails['prgm_limit_amt'] - $limitUsed[$offerDetails['prgm_offer_id']];
			$limitAvl[$offerDetails['prgm_offer_id']] = ($limitAvl[$offerDetails['prgm_offer_id']] > 0) ? $limitAvl[$offerDetails['prgm_offer_id']] : 0; 
			if($overdueAmt > 0 || $outstandingAmt > 0){
				$result[] = [
					'cust_name'=>$invDisb->invoice->business->biz_entity_name,
					'loan_ac'=>config('common.idprefix.APP').$invDisb->invoice->app_id,
					'invoice_no' => $invDetails->invoice_no,
					'payment_due_date' => $invDisb->payment_due_date,
					'customer_id'=>$invDetails->lms_user->customer_id,
					'prgm_name' => $prgmDetails->parentProgram->prgm_name,
					'sub_prgm_name' => $prgmDetails->prgm_name,
					'virtual_ac'=>$invDisb->invoice->lms_user->virtual_acc_id,
					'client_sanction_limit'=>$offerDetails['prgm_limit_amt'],
					'limit_available'=> $limitAvl[$offerDetails['prgm_offer_id']],
					'out_amt'=>$outstandingAmt,
					'od_days'=>$overdueDays,
					'od_amt'=>$overdueAmt,
					'sales_person_name'=> ($invDisb->invoice->anchor->salesUser->f_name.' '. $invDisb->invoice->anchor->salesUser->m_name.' '. $invDisb->invoice->anchor->salesUser->l_name)
				];
			}
		}
		return $result;
	}

	public function getAccountDisbursalReport($whereCondition=[], &$sendMail){
		$curdate = Helper::getSysStartDate();
		$curdate = Carbon::parse($curdate)->format('Y-m-d');

		$invDisbList = InvoiceDisbursed::with(['transactions' => function($query2){
			$query2->whereNull('payment_id')
			->whereNull('link_trans_id')
			->whereNull('parent_trans_id')
			->where('trans_type',config('lms.TRANS_TYPE.PAYMENT_DISBURSED'))
			->where('entry_type','0');
		},
		'invoice'=>function($query2) use($whereCondition){
			if(isset($whereCondition['anchor_id'])){
				$query2->where('anchor_id',$whereCondition['anchor_id']);
			}
		},
		'invoice.lms_user', 'invoice.business', 'disbursal'])
		->whereIn('status_id', [12,13,15,47])
		->whereHas('invoice', function($query3) use($whereCondition){
			if(isset($whereCondition['anchor_id'])){
				$query3->where('anchor_id',$whereCondition['anchor_id']);
			}
			/*$query2->whereHas('invoiceStatusLog', function($query3) use($curdate){
				$query3->whereDate('disburse_date',$curdate)
				->where('status_id',12);
			});*/
		})
		->get();

		$sendMail = ($invDisbList->count() > 0)?true:false;

		$result = [];
		foreach($invDisbList as $invDisb){
			$result[] = [
			'cust_name'=>$invDisb->invoice->business->biz_entity_name,
			'loan_ac'=>config('common.idprefix.APP').$invDisb->invoice->app_id,
			'trans_date'=>$invDisb->disbursal->disburse_date,
			'trans_no'=>$invDisb->disbursal->tran_id,
			'invoice_no'=>$invDisb->invoice->invoice_no,
			'invoice_date'=>$invDisb->invoice->invoice_date,
			'invoice_amt'=>$invDisb->invoice->invoice_amount,
			'margin_amt'=>$invDisb->invoice->invoice_approve_amount*$invDisb->margin/100,
			'disb_amt'=>$invDisb->invoice->invoice_amount,
			'trans_utr'=>$invDisb->disbursal->tran_id,
			'remark'=>$invDisb->invoice->remark,
			'bank_ac'=>$invDisb->disbursal->acc_no,
			'ifsc'=>$invDisb->disbursal->ifsc_code,
			'status'=>'',
			'status_des'=>''
			];
		}
		return $result;
	}
    
	public function getMarginReport($whereCondition=[], &$sendMail){
        $invDisbList = DB::select("SELECT * FROM margin_report");
        $sendMail = (count($invDisbList) > 0)?true:false;
 		$result = [];
		foreach($invDisbList as $invDisb){
			$result[] = [
				'anchor'=>$invDisb->anchor,
				'client'=>$invDisb->client,
				'client_id' => $invDisb->client_id,
				'invoice_no' => $invDisb->invoice_no,
				'invoice_date'=>$invDisb->invoice_date,
				'invoice_amount' => $invDisb->invoice_amount,
				'disbursed_amt' => $invDisb->disbursed_amt,
                'disbursal_date'=>$invDisb->disbursal_date,
				'margin_per'=>$invDisb->margin_per,
				'margin_allocated'=>$invDisb->margin_allocated,
				'margin_outstanding'=> $invDisb->margin_outstanding
			 ];
		}
		return $result;
	}

	public function getReceiptReport($whereCondition=[], &$sendMail){
        $transactions = DB::select("SELECT * FROM receipt_report");
        $sendMail = (count($transactions) > 0)?true:false;
 		$result = [];
		foreach($transactions as $trans){
			$result[] = [
				'receipt_date' => $trans->receipt_date,
				'receipt_account' => $trans->receipt_account,
				'client_name' => $trans->client_name,
				'client_id' => $trans->client_id,
				'trans_type_name' => $trans->trans_type_name,
				'invoice_no' => $trans->invoice_no,
				'receipt_utr' => $trans->receipt_utr,
				'invoice_date' => $trans->invoice_date,
				'capsave_invoice_no' => $trans->capsave_invoice_no,
				'capsave_inv_date' => $trans->capsave_inv_date,
				'disburse_date' => $trans->disburse_date,
				'amount' => $trans->amount,
				'total_amount' => $trans->total_amount
			 ];
		}
		return $result;
	}

	public function etlReportSync(){
		$report_1_clear = 'TRUNCATE `etl_margin_report`';
		$report_1_data = 'INSERT INTO etl_margin_report (`anchor`,`client`,`client_id`,`invoice_no`,`invoice_date`,`invoice_amount`,`disbursed_amount`,`disbursal_date`,`margin_percentage`,`margin_allocated`,`margin_outstanding`) SELECT anchor, CLIENT, client_id, invoice_no, invoice_date, invoice_amount, disbursed_amt, disbursal_date, margin_per, margin_allocated, margin_outstanding  FROM margin_report';
		DB::statement(\DB::raw($report_1_clear));
		
		$report_1_res = DB::statement(\DB::raw($report_1_data));
		
		$report_2_clear = 'TRUNCATE `etl_settlement_report`';
		$report_2_data = 'INSERT INTO etl_settlement_report (`receipt_date`,`receipt_account_no`,`client_borrower_name`,`client_id`,`head_against_ipc`,`invoice_no`,`utr_no`,`invoice_date`,`capsave_invoice_no`,`capsave_invoice_date`,`disbursement_date`,`amount_applied`,`amount_received`,`transaction_date`,`anchor_name`,`program_name`,`due_date`) SELECT `receipt_date`,`receipt_account`,`client_name`,`client_id`,`trans_type_name`,`invoice_no`,`receipt_utr`,`invoice_date`,`capsave_invoice_no`,`capsave_inv_date`,`disburse_date`,`amount`,`total_amount` FROM receipt_report';
		DB::statement(\DB::raw($report_2_clear));
		
		$report_2_res = DB::statement(\DB::raw($report_2_data));
				
		$report_3_clear = 'TRUNCATE `etl_perfios_log`';
		$report_3_data = 'INSERT INTO etl_perfios_log (`id`,`perfios_log_id`,`status`,`created_by`,`created_at`,`updated_at`) SELECT `id`,`perfios_log_id`,`status`,`created_by`,`created_at`,`updated_at` FROM rta_biz_perfios_log';
		DB::statement(\DB::raw($report_3_clear));
		
		$report_3_res = DB::statement(\DB::raw($report_3_data));

		return [
			'report_1' => $report_1_res,
			'report_2' => $report_2_res,
			'report_3' => $report_3_res,
		];
	}
 
	public function getOutstandingReportManual($whereCondition=[], &$sendMail){
		$curdate = Helper::getSysStartDate();
		$curdate = Carbon::parse($curdate)->format('Y-m-d');
		$curdate = $whereCondition['to_date']??$curdate;
		$invDisbList = InvoiceDisbursed::
		with([
			'invoice'=>function($query2) use($whereCondition){
					if(isset($whereCondition['anchor_id'])){
						$query2->where('anchor_id',$whereCondition['anchor_id']);
					}
					if(isset($whereCondition['user_id'])){
						$query2->where('supplier_id',$whereCondition['user_id']);
					}
					$query2->select('anchor_id','invoice_no','invoice_id','supplier_id','prgm_offer_id','biz_id','program_id');
				},
			'invoice.lms_user:user_id,customer_id,virtual_acc_id', 
			'invoice.anchor:anchor_id,comp_name,sales_user_id', 
			'invoice.anchor.salesUser:user_id,f_name,m_name,l_name', 
			'invoice.program:prgm_id,prgm_type,prgm_name,interest_borne_by',
			'invoice.program_offer:prgm_offer_id,payment_frequency',
			'invoice.business:biz_id,biz_entity_name', 
			'transactions:invoice_disbursed_id,trans_id,payment_id,link_trans_id,parent_trans_id,trans_type,entry_type,soa_flag,settled_outstanding,outstanding,due_date,amount',
			'disburseDetails:invoice_disbursed_id,grace_period,funded_date,approve_amount,margin_amount,interest_capitalized,tenor,overdue_capitalized,request_amount'
		])
		->whereIn('status_id', [12,13,15,47])
		->whereHas('invoice', function($query3) use($whereCondition){
			if(isset($whereCondition['anchor_id'])){
				$query3->where('anchor_id',$whereCondition['anchor_id']);
			}
			if(isset($whereCondition['user_id'])){
				$query3->where('supplier_id',$whereCondition['user_id']);
			}
		})
		->whereDate('int_accrual_start_dt','<=',$curdate)
		->get();
		$sendMail = ($invDisbList->count() > 0)?true:false;
		$result = [];
		foreach($invDisbList as $key => $invDisb){
			$invDetails = $invDisb->invoice;
			$disbDetails = $invDisb->disburseDetails;
			if(!$disbDetails){
				continue;
			}

			$principalOutstanding = $invDisb->transactions->where('trans_type','16')->where('entry_type',0)->whereNull('parent_trans_id')->sum('outstanding');
			$principalOutstanding = $principalOutstanding > 0 ? $principalOutstanding : 0;
			
			$marginOutstanding = $invDisb->transactions->where('trans_type','10')->where('entry_type',0)->whereNull('parent_trans_id')->sum('outstanding');
			$marginOutstanding = $marginOutstanding > 0 ? $marginOutstanding : 0;

			$interestOutstanding = $invDisb->transactions->where('trans_type','9')->where('entry_type',0)->whereNull('parent_trans_id')->sum('outstanding');
			$interestOutstanding = $interestOutstanding > 0 ? $interestOutstanding : 0;
			
			$overdueOutstanding = $invDisb->transactions->where('trans_type','33')->where('entry_type',0)->whereNull('parent_trans_id')->where('soa_flag','1')->sum('outstanding');
			$overdueOutstanding = $overdueOutstanding > 0 ? $overdueOutstanding : 0;

			$chargesOutstanding = $invDisb->transactions->where('trans_type','>','50')->where('entry_type',0)->whereNull('parent_trans_id')->where('soa_flag','1')->sum('outstanding');
			
			$charges = $invDisb->transactions->where('trans_type','>','50')->where('entry_type',0)->whereNull('parent_trans_id')->where('soa_flag','1')->sum('amount');
							  
			$totalOutstanding = ($principalOutstanding + $interestOutstanding + $overdueOutstanding + $chargesOutstanding);
	
			$interestIds = $invDisb->transactions->where('trans_type','9')->where('entry_type',0)->whereNull('parent_trans_id')->where('soa_flag','1')->pluck('trans_id')->toArray();
			$interest_to_refunded = $invDisb->transactions->where('trans_type','32')->where('entry_type',1)->whereIn('parent_trans_id',$interestIds)->sum('settled_outstanding');
			$interest_to_refunded = $interest_to_refunded > 0 ? $interest_to_refunded : 0;

			$overdueIds = $invDisb->transactions->where('trans_type','33')->where('entry_type',0)->whereNull('parent_trans_id')->where('soa_flag','1')->pluck('trans_id')->toArray();
			$overdueinterest_to_refunded = $invDisb->transactions->where('trans_type','32')->where('entry_type',1)->whereIn('parent_trans_id',$overdueIds)->sum('settled_outstanding');
			$overdueinterest_to_refunded = $overdueinterest_to_refunded > 0 ? $overdueinterest_to_refunded : 0;

			$marginIds = $invDisb->transactions->where('trans_type','10')->where('entry_type',1)->where('soa_flag','1')->pluck('trans_id')->toArray();
			$margin_to_refunded = $invDisb->transactions->where('trans_type','32')->where('entry_type',1)->whereIn('link_trans_id',$marginIds)->sum('settled_outstanding');
			$margin_to_refunded = $margin_to_refunded > 0 ? $margin_to_refunded : 0;

			// if($totalOutstanding <= 0 && $interest_to_refunded <= 0 && $margin_to_refunded <= 0 && $overdueinterest_to_refunded <= 0 && $marginOutstanding <= 0){
			// 	continue;
			// }
	
			$anchor_name = $invDetails->anchor->comp_name ?? '';
			$offer = $invDetails->program_offer;
	
			$payment_frequency='none';
			if($offer->payment_frequency == '1'){
				$payment_frequency = 'Up Front';
			}else if($offer->payment_frequency == '2'){
				$payment_frequency = 'Monthly';
			}else if($offer->payment_frequency == '3'){
				$payment_frequency = 'Rear Ended';
			}
	
			$prgmDetails = $invDetails->program;

			$prgmType = $prgmDetails->prgm_type;
			if($prgmType == 1){
				$product = 'Vendor Finance';
			}elseif($prgmType == 2){
				$product = 'Channel Finance';
			}
			
			$disbursement_method = 'Gross';
	
			/**
			 * For VFS, Gross if int borne by anchor and net if int borne by supplier.
			 * For Channel Finance, Gross if int borne by buyer and net if int borne by anchor. 
			**/

			if($prgmDetails->interest_borne_by == 2 && $offer->payment_frequency == 1 && $invDisb->total_interest > 0){
				$disbursement_method = 'Net';
			}
			$odiInterest = round((round($invDisb->overdue_interest_rate,2) - round($invDisb->interest_rate,2)),2);

			$principalDPD = $invDisb->transactions->where('trans_type','16')->where('entry_type',0)->where('outstanding', '>', 0)->whereNull('payment_id')->whereNull('parent_trans_id')->max('dpd');

			$interestDPD = $invDisb->transactions->where('trans_type','9')->where('entry_type',0)->where('outstanding', '>', 0)->whereNull('payment_id')->whereNull('parent_trans_id')->max('dpd');
			
			$overdueDPD = $invDisb->transactions->where('trans_type','33')->where('entry_type',0)->where('outstanding', '>', 0)->whereNull('payment_id')->whereNull('parent_trans_id')->max('dpd');

			$principalDPD = (round($principalOutstanding,2) > 0) ? ($principalDPD > 0 ? $principalDPD : 0) : 0;

			$interestDPD = (round($interestOutstanding,2) > 0) ? ($interestDPD > 0 ? $interestDPD : 0) : 0;

			$overdueDPD = (round($overdueOutstanding,2) > 0) ? ($overdueDPD > 0 ? $overdueDPD : 0) : 0;

			$principalOverdue = '';
			$principalOverdueCategory = '';
			if($principalOutstanding > 0 && isset($invDisb->payment_due_date) && strtotime($invDisb->payment_due_date) <= strtotime($curdate) ){
				$dateoverdueFormat = Carbon::createFromFormat('Y-m-d', $invDisb->payment_due_date);
				$daysToAdd = (int)$disbDetails->grace_period;
				$dateoverdueFormat = $dateoverdueFormat->addDays($daysToAdd);
				if(strtotime($dateoverdueFormat) > strtotime($curdate) && strtotime($invDisb->payment_due_date) <= strtotime($curdate) && $daysToAdd > 0){
					$principalOverdueCategory='with in grace';
				}
				elseif($principalDPD > 0){
					$principalOverdueCategory='Overdue';
				    $principalOverdue = $principalOutstanding;
				}
			}

			$maxDPD = $principalDPD > $interestDPD ? $principalDPD : $interestDPD;
			$outstanding_max_bucket = "Not Outstanding";
			if($maxDPD > 0){
				if($maxDPD <= 7)
				  $outstanding_max_bucket = "01 - 07 Days";
				else if($maxDPD <= 15)
				  $outstanding_max_bucket = "08 - 15 Days";
				else if($maxDPD <= 30)  
				  $outstanding_max_bucket = "16 - 30 Days";
				else if($maxDPD <= 60) 
				  $outstanding_max_bucket = "31 - 60 Days"; 
				else if($maxDPD <= 90)
				  $outstanding_max_bucket = "61 - 90 Days";  
				else
				  $outstanding_max_bucket = "90 + Days"; 
			}

			$diff=date_diff(date_create($curdate),date_create($invDisb->payment_due_date));
			$maturityDays = $diff->format("%r%a");
			$maturityDays = ($maturityDays > 0) ? $maturityDays : 0;
			
			$maturityMaxbucket = "Not Outstanding";
			if($principalOutstanding > 0 && $maturityDays > 0){
				if($maturityDays <= 7)
				  $maturityMaxbucket = "01 - 07 Days";
				else if($maturityDays <= 15)
				  $maturityMaxbucket = "08 - 15 Days";
				else if($maturityDays <= 30)  
				  $maturityMaxbucket = "16 - 30 Days";
				else if($maturityDays <= 60) 
				  $maturityMaxbucket = "31 - 60 Days"; 
				else if($maturityDays <= 90)
				  $maturityMaxbucket = "61 - 90 Days";  
				else
				  $maturityMaxbucket = "90 + Days"; 
			}
			$graceDate = 0;

			if($invDisb->payment_due_date){
				$dateOutsFormat = Carbon::createFromFormat('Y-m-d', $invDisb->payment_due_date);
				$daysToAdd = (int)$disbDetails->grace_period;
				$graceDate = $dateOutsFormat->addDays($daysToAdd);
				$graceDate = Carbon::parse($graceDate)->format('d-m-Y');
			}
            $salesUserDetails = $invDetails->anchor->salesUser;
			$date = $invDisb->inv_due_date;

			$result[$invDisb->invoice_disbursed_id] = [
				'custName' => $invDetails->business->biz_entity_name ?? '',
				'customerId' => $invDetails->lms_user->customer_id ?? '',
				'anchorName'=> $anchor_name,
				'SubPrgmName'=> $invDetails->program->prgm_name ?? '',
				'invoiceNo' => $invDetails->invoice_no,
				'disbursementDate' => isset($disbDetails->funded_date) ? Carbon::parse($disbDetails->funded_date)->format('d-m-Y'):'',
				'invoiceAmt' => round($disbDetails->request_amount,2),
				'invoiceApproveAmount' => round($disbDetails->approve_amount,2),
				'marginPosted' => round($disbDetails->margin_amount,2),
				'upfrontInterest' => ($prgmDetails->interest_borne_by == 2 && $offer->payment_frequency == 1) ? round($invDisb->total_interest,2) : 0,
				'chargeDeduction' => round(0,2),
				'invoiceLevelChrg'=> round($charges,2),
				'disburseAmount' => round(($invDisb->disburse_amt - ($invDisb->total_interest + $invDisb->processing_fee + $invDisb->processing_fee_gst)),2),
				'product' => $product,
				'paymentFrequency' => $payment_frequency,
				'interestPosted' => round($disbDetails->interest_capitalized,2), 
				'disbursementMethod' => $disbursement_method,
				'paymentDueDate' => isset($invDisb->payment_due_date) ? Carbon::parse($invDisb->payment_due_date)->format('d-m-Y'):'',
				'virtualAc' => $invDetails->lms_user->virtual_acc_id ?? '',
				'tenure' =>	$disbDetails->tenor,
				'roi' => $invDisb->interest_rate,
				'odi' => $odiInterest,
				'principalOut' => round($principalOutstanding,2),
				'marginOut' => round($marginOutstanding,2),
				'interestOut' => round($interestOutstanding,2),
				'overduePosted' => round($disbDetails->overdue_capitalized,2),
				'overdueOut' => round($overdueOutstanding,2),
				'invoiceLevelChrgOut'=> round($chargesOutstanding,2),
				'totalOutStanding' => round($totalOutstanding,2),
				'intGraceDays' => '',
				'principalGraceDays' => $disbDetails->grace_period,
				'principalOverdue' => $principalOverdue,
				'principalOverdueCategory'=> $principalOverdueCategory,
				'principalDPD' => ($principalDPD > 0) ? $principalDPD : 0,
				'interestDPD' => ($interestDPD > 0) ? $interestDPD : 0,
				'overdueDPD' => ($overdueDPD > 0) ? $overdueDPD : 0,
				'finalDPD' => $maxDPD,
				'outstandingMaxBucket' => $outstanding_max_bucket,
				'maturityDays' => $maturityDays,
				'maturityBucket' => $maturityMaxbucket,
				'marginToRefunded' => round($margin_to_refunded,2),
				'interestToRefunded' => round($interest_to_refunded,2),
				'overdueToRefunded' => round($overdueinterest_to_refunded,2),
				'salesManager' => $salesUserDetails->f_name.' '. $salesUserDetails->m_name.' '. $salesUserDetails->l_name,
				'gracePeriodEndDate' => $graceDate,
			];
			$invDisbList->forget($key);
		}
		return $result;
	}

	public function getBackDateOutstandingReportManual($whereCondition=[], &$sendMail){
		$curdate = Helper::getSysStartDate();
		$curdate = Carbon::parse($curdate)->format('Y-m-d');
		$curdate = $whereCondition['to_date']??$curdate;
		$invDisbList = InvoiceDisbursed::
		with([
			'invoice'=>function($query2) use($whereCondition){
					if(isset($whereCondition['anchor_id'])){
						$query2->where('anchor_id',$whereCondition['anchor_id']);
					}
					if(isset($whereCondition['user_id'])){
						$query2->where('supplier_id',$whereCondition['user_id']);
					}
				},
			'invoice.lms_user', 
			'invoice.anchor', 
			'invoice.program_offer',
			'invoice.business', 
			'disbursal',
			'transaction',
			'disburseDetails'
		])
		->whereIn('status_id', [12,13,15,47])
		->whereHas('invoice', function($query3) use($whereCondition){
			if(isset($whereCondition['anchor_id'])){
				$query3->where('anchor_id',$whereCondition['anchor_id']);
			}
			if(isset($whereCondition['user_id'])){
				$query3->where('supplier_id',$whereCondition['user_id']);
			}
		})
		->whereDate('int_accrual_start_dt','<=',$curdate)
		->get();
	
		$outstandingData = self::getOutstandingData($curdate);
		$sendMail = ($invDisbList->count() > 0)?true:false;
		$result = [];
		foreach($invDisbList as $invDisb){
			
			$transactions = Transactions::select('invoice_disbursed_id',
			DB::raw('sum(IF (trans_type = 9 ,(amount-getTransCancelDateAmt(trans_id, amount, \''.$curdate.'\')),0)) as interest_posted'),
			DB::raw('sum(IF (trans_type = 33 ,(amount-getTransCancelDateAmt(trans_id, amount, \''.$curdate.'\')),0)) as overdue_interest_posted'),
			DB::raw('sum(IF (trans_type = 9 ,(getTransOutstandingDateAmt(trans_id, amount, \''.$curdate.'\')),0)) as interest_os_amount'),
			DB::raw('sum(IF (trans_type = 33 ,(getTransOutstandingDateAmt(trans_id, amount, \''.$curdate.'\')),0)) as overdue_os_amount'),
			DB::raw('sum(IF (trans_type = 16 ,(getTransOutstandingDateAmt(trans_id, amount, \''.$curdate.'\')),0)) as principal_os_amount'),
			DB::raw('sum(IF (trans_type > 50 ,(amount - getTransCancelDateAmt(trans_id, amount, \''.$curdate.'\')),0)) as charge_amount'),
			DB::raw('sum(IF (trans_type > 50 ,(getTransOutstandingDateAmt(trans_id, amount, \''.$curdate.'\')),0)) as charge_os_amount'))
					->where('invoice_disbursed_id',$invDisb->invoice_disbursed_id)
					->whereNULL('parent_trans_id')
					->whereNULL('link_trans_id')
					->where('entry_type','=',0)
					->whereDate('created_at','<=',$curdate)
					->first();
			$invDetails = $invDisb->invoice;
			$disbDetails = $invDisb->disburseDetails;
			if(!$disbDetails || !$transactions){
				continue;
			}
			
			$principalOutstanding = $transactions->principal_os_amount;			
			$principalOutstanding = $principalOutstanding > 0 ? $principalOutstanding : 0;
	
			$interestOutstanding = $transactions->interest_os_amount;
			$interestOutstanding = $interestOutstanding > 0 ? $interestOutstanding : 0;
	
			$overdueAmount = $transactions->overdue_os_amount;
			$overdueAmount = $overdueAmount > 0 ? $overdueAmount : 0;
		   
			$totalOutstanding = ($principalOutstanding + $interestOutstanding + $overdueAmount + $transactions->charge_os_amount);
	
			if($totalOutstanding <= 0){
				continue;
			}
	
			$anchor_name = $invDetails->anchor->comp_name ?? '';
			$offer = $invDetails->program_offer;
	
			$payment_frequency='none';
			if($offer->payment_frequency == '1'){
				$payment_frequency = 'Up Front';
			}else if($offer->payment_frequency == '2'){
				$payment_frequency = 'Monthly';
			}else if($offer->payment_frequency == '3'){
				$payment_frequency = 'Rear Ended';
			}
	
			$prgmDetails = $invDetails->program;
	
			$prgmType = $prgmDetails->prgm_type;
			
			$disbursement_method = 'Net';
	
			/**
			 * For VFS, Gross if int borne by anchor and net if int borne by supplier.
			 * For Channel Finance, Gross if int borne by buyer and net if int borne by anchor. 
			**/
			if($prgmType == '1'){ 
				if($prgmDetails->interest_borne_by == '1'){
					$disbursement_method = 'Gross';
				}
			} 
			elseif($prgmType == '2'){
				if($prgmDetails->interest_borne_by == '2'){
					$disbursement_method = 'Gross';
				}
			}
		   
			$odiInterest = round((round($invDisb->overdue_interest_rate,2) - round($invDisb->interest_rate,2)),2);
		   
			$principalOverdueCategory='';
			if(!is_null($disbDetails->payment_due_date) && strtotime($disbDetails->payment_due_date) <= strtotime($curdate) ){
				$dateoverdueFormat = Carbon::createFromFormat('Y-m-d', $disbDetails->payment_due_date);
				$daysToAdd = (int)$disbDetails->grace_period;
				$dateoverdueFormat = $dateoverdueFormat->addDays($daysToAdd);
				if(strtotime($dateoverdueFormat) > strtotime($curdate) && strtotime($disbDetails->payment_due_date) <= strtotime($curdate)){
					$principalOverdueCategory='with in grace';
				}
				else{
					$principalOverdueCategory='Overdue';
				}
			}
			$principalDPD = 0;
			$now = Carbon::parse($curdate);
            $paymentDueDate = Carbon::parse($invDisb->payment_due_date);
            $principalDPD = $paymentDueDate->diffInDays($now);

			$maxDPD = $principalDPD;

			$outstanding_max_bucket = "Not Outstanding";
			if($principalOutstanding > 100 && $maxDPD > 0){
				if($maxDPD <= 7)
				  $outstanding_max_bucket = "01 - 07 Days";
				else if($maxDPD <= 15)
				  $outstanding_max_bucket = "08 - 15 Days";
				else if($maxDPD <= 30)  
				  $outstanding_max_bucket = "16 - 30 Days";
				else if($maxDPD <= 60) 
				  $outstanding_max_bucket = "31 - 60 Days"; 
				else if($maxDPD <= 90)
				  $outstanding_max_bucket = "61 - 90 Days";  
				else
				  $outstanding_max_bucket = "90 + Days"; 
			}

			$diff=date_diff(date_create($curdate),date_create($disbDetails->payment_due_date));
			$maturityDays = $diff->format("%r%a");
			
			$maturityMaxbucket = "Not Outstanding";
			if($principalOutstanding > 100 && $maturityDays > 0){
				if($maturityDays <= 7)
				  $maturityMaxbucket = "01 - 07 Days";
				else if($maturityDays <= 15)
				  $maturityMaxbucket = "08 - 15 Days";
				else if($maturityDays <= 30)  
				  $maturityMaxbucket = "16 - 30 Days";
				else if($maturityDays <= 60) 
				  $maturityMaxbucket = "31 - 60 Days"; 
				else if($maturityDays <= 90)
				  $maturityMaxbucket = "61 - 90 Days";  
				else
				  $maturityMaxbucket = "90 + Days"; 
			}

			$interest_to_refunded = round((round($invDisb->disburseDetails->interest_refundable,2) - round($invDisb->disburseDetails->interest_refunded,2)),2);
			$margin_to_refunded = round((round($invDisb->disburseDetails->margin_repayment,2) - round($invDisb->disburseDetails->margin_refunded,2)),2);
			$overdueinterest_to_refunded = round((round($invDisb->disburseDetails->overdue_refundable,2) - round($invDisb->disburseDetails->overdue_refunded,2)),2);
			

			$result[$invDisb->invoice_disbursed_id] = [
				'custName' => $invDetails->business->biz_entity_name ?? '',
				'customerId' => $invDetails->lms_user->customer_id ?? '',
				'anchorName'=> $anchor_name,
				'invoiceNo' => $invDetails->invoice_no,
				'disbursementDate' => isset($disbDetails->funded_date) ? Carbon::parse($disbDetails->funded_date)->format('d-m-Y'):'',
				'invoiceAmt' => round($disbDetails->request_amount,2),
				'invoiceApproveAmount' => round($disbDetails->approve_amount,2),
				'marginPosted' => round($disbDetails->margin_amount,2),
				'upfrontInterest' => ($prgmDetails->interest_borne_by == 2 && $offer->payment_frequency == 1) ? round($invDisb->total_interest,2) : 0,
				'chargeDeduction' => round(0,2),
				'invoiceLevelChrg'=> round($transactions->charge_amount,2),
				'disburseAmount' => round(($invDisb->disburse_amt - ($invDisb->total_interest + $invDisb->processing_fee + $invDisb->processing_fee_gst)),2),
				'paymentFrequency' => $payment_frequency,
				'interestPosted' => round($disbDetails->interest_capitalized,2), 
				'disbursementMethod' => $disbursement_method,
				'paymentDueDate' => isset($invDisb->payment_due_date) ? Carbon::parse($invDisb->payment_due_date)->format('d-m-Y'):'',
				'virtualAc' => $invDetails->lms_user->virtual_acc_id ?? '',
				'tenure' =>	$disbDetails->tenor,
				'roi' => $invDisb->interest_rate,
				'odi' => $odiInterest,
				'principalOut' => round($principalOutstanding,2),
				'interestOut' => round($interestOutstanding,2),
				'overduePosted' => round($disbDetails->overdue_capitalized,2),
				'overdueOut' => round($overdueAmount,2),
				'invoiceLevelChrgOut'=> round($transactions->charge_os_amount,2),
				'totalOutStanding' => round($totalOutstanding,2),
				'intGraceDays' => '',
				'principalGraceDays' => $disbDetails->grace_period,
				'principalOverdue' => '',
				'principalOverdueCategory'=> $principalOverdueCategory,
				'principalDPD' => $principalDPD,
				'interestDPD' => 0,
				'finalDPD' => $maxDPD,
				'outstandingMaxBucket' => $outstanding_max_bucket,
				'maturityDays' => $maturityDays,
				'maturityBucket' => $maturityMaxbucket,
				'marginToRefunded' => round($margin_to_refunded,2),
				'interestToRefunded' => round($interest_to_refunded,2),
				'overdueToRefunded' => round($overdueinterest_to_refunded,2)
			];
		}
		return $result;
	}

	public function getReconReportData($userId){
		
        $resultValue = [];
		$soaBalance = DB::select('SELECT customer_id, soa_outstanding as SOA_Outstanding FROM (SELECT user_id, customer_id FROM rta_lms_users GROUP BY user_id ) AS a LEFT JOIN(SELECT b.user_id, (SUM(b.debit_amount) - SUM(b.credit_amount)) AS soa_outstanding FROM rta_customer_transaction_soa AS b LEFT JOIN rta_transactions AS c ON c.trans_id = b.trans_id WHERE c.soa_flag = 1 GROUP BY c.user_id) AS d ON a.user_id = d.user_id'); 
		foreach($soaBalance as $key => $soaBal) {
			$resultValue[$soaBal->customer_id]['SOA_Outstanding'] = number_format($soaBal->SOA_Outstanding,2);
		}
		
		$chargeOutstanding = DB::select('SELECT b.customer_id, SUM(a.outstanding) as Outstanding_Amount FROM rta_transactions AS a JOIN (SELECT * FROM rta_lms_users GROUP BY user_id) AS b ON a.user_id = b.user_id WHERE a.trans_type > 50 AND a.entry_type = 0 GROUP BY a.user_id');

		foreach($chargeOutstanding as $key => $chrgOut) {
			$resultValue[$chrgOut->customer_id]['Outstanding_Amount'] = isset($chrgOut->Outstanding_Amount) ? number_format($chrgOut->Outstanding_Amount,2) : 0;
		}

		$nonFactorOutstanding = DB::select('SELECT c.customer_id AS customer_id, SUM(a.settled_outstanding) AS outstandingAmount FROM rta_transactions AS a JOIN rta_transactions AS b ON a.link_trans_id = b.trans_id AND b.trans_type = 35 AND b.entry_type = 1 JOIN (SELECT * FROM rta_lms_users GROUP BY user_id) AS c ON a.user_id = c.user_id WHERE a.trans_type = 32 AND a.entry_type = 1 AND a.settled_outstanding > 0 GROUP BY a.user_id');
		foreach($nonFactorOutstanding as $key => $nonfactorOut) {
			$resultValue[$nonfactorOut->customer_id]['outstandingAmount'] = number_format($nonfactorOut->outstandingAmount,2);
		}
		
		$txnChrgRefund = DB::select('SELECT c.customer_id AS customer_id, SUM(a.settled_outstanding) AS Refundable FROM rta_transactions AS a JOIN rta_transactions AS b ON a.parent_trans_id = b.trans_id AND b.trans_type > 50 AND b.entry_type = 0 JOIN (SELECT * FROM rta_lms_users GROUP BY user_id) AS c ON a.user_id = c.user_id WHERE a.trans_type = 32 AND a.entry_type = 1 AND a.settled_outstanding > 0 GROUP BY a.user_id');
		foreach($txnChrgRefund as $key => $chrgRefund) {
			$resultValue[$chrgRefund->customer_id]['Refundable'] = number_format($chrgRefund->Refundable,2);
		}

		$controller = app()->make('App\Http\Controllers\Lms\SoaController');
		$controller2 = app()->make('App\Http\Controllers\Lms\ApportionmentController');
		$users = LmsUser::groupBy('customer_id')->get();        
		$cusOutData = [];
		foreach($users as $key => $user) {
			$cusOutData[$key]['customer_id'] = 0;
			$cusOutData[$key]['customer_outstanding_amount'] = 0; 
			$result = $controller->getUserLimitDetais($user->user_id);
			if(isset($result['userInfo'])){
				$resultValue[$user->customer_id]['customer_outstanding_amount'] = number_format($controller2->lmsRepo->getUnsettledTrans($user->user_id, ['trans_type_not_in' => [config('lms.TRANS_TYPE.MARGIN'),config('lms.TRANS_TYPE.NON_FACTORED_AMT')] ])->sum('outstanding'),2);
			}
		}
        return $resultValue;
    }
}
