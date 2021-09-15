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
			'out_days'=>(strtotime($invDisb->payment_due_date) - strtotime($curdate))/86400,
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
		$curdate = Helper::getSysStartDate();
		$curdate = Carbon::parse($curdate)->addDays(1)->format('Y-m-d');
                $fromdate = Carbon::parse($curdate)->subDays(30)->format('Y-m-d');

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
                ->whereHas('invoice', function($query3) use($whereCondition, $fromdate, $curdate){
			if(isset($whereCondition['anchor_id'])){
				$query3->where('anchor_id',$whereCondition['anchor_id']);
			}
		$query3->whereBetween('invoice_date', [$fromdate, $curdate]);
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
			if(($invDisb->invoice->program_offer->payment_frequency == 1) && (strtotime($invDisb->payment_due_date) <= strtotime($curdate)) ) {
				$interestAmount = $invDisb->total_interest;
				$fromDate = $invDisb->int_accrual_start_dt;
				$toDate = $invDisb->payment_due_date;
			} else {
				$interestAmount = $invDisb->sumInterestAccured($invDisb->invoice_disbursed_id);
				$fromDate = $invDisb->interests->min('interest_date');
				$toDate = $invDisb->interests->max('interest_date');
			}
			
			$result[] = [
			'cust_name'=>$invDisb->invoice->business->biz_entity_name,
			'rm_sales'=>isset($salesDetails) ? $salesDetails->f_name . ' ' . $salesDetails->l_name : '',
			'anchor_name'=>isset($invDisb->invoice->anchor) ? $invDisb->invoice->anchor->comp_name : '',
			'anchor_prgm_name'=>isset($invDisb->invoice->program) ? $invDisb->invoice->program->prgm_name : '',
			'vendor_ben_name'=>$invDisb->invoice->supplier_bank_detail->acc_name ?? '', // Amit sir
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
			'intrst_rec_date'=>'', // blank
			'proce_fee'=>$invDisb->transactions->where('trans_type',62)->where('entry_type',0)->sum('base_amt'),
			'proce_amt'=>$invDisb->transactions->where('trans_type',62)->where('entry_type',0)->sum('base_amt'),
			'proce_fee_gst'=>$invDisb->transactions->where('trans_type',62)->where('entry_type',0)->sum('amount'),
			'tds_proce_fee'=>$invDisb->transactions[0]->tdsProcessingFee(),
			'net_proc_fee_rec'=>($invDisb->transactions->where('trans_type',62)->where('entry_type',0)->sum('amount')) - ($invDisb->transactions[0]->tdsProcessingFee()),
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
			$result[$invDetails->program_id]['client_sanction'] =  count(array_unique($sanctionCase[$invDetails->program_id]));
			$result[$invDetails->program_id]['ttl_od_customer'] =  count($odCustCnt[$invDetails->program_id]??[]);
			$result[$invDetails->program_id]['ttl_od_amt'] = ($result[$invDetails->program_id]['ttl_od_amt']??0) + $overdueAmt;
			$result[$invDetails->program_id]['disbursement'][$invDetails->supplier_id.'-'.$invDetails->app_id]['client_name'] = $invDetails->business->biz_entity_name;
			$result[$invDetails->program_id]['disbursement'][$invDetails->supplier_id.'-'.$invDetails->app_id]['loan_ac'] = config('common.idprefix.APP').$invDetails->app_id;
			$result[$invDetails->program_id]['disbursement'][$invDetails->supplier_id.'-'.$invDetails->app_id]['user_id'] = $invDetails->lms_user->customer_id;
			$result[$invDetails->program_id]['disbursement'][$invDetails->supplier_id.'-'.$invDetails->app_id]['virtual_ac'] = $invDetails->lms_user->virtual_acc_id;
			$result[$invDetails->program_id]['disbursement'][$invDetails->supplier_id.'-'.$invDetails->app_id]['client_sanction_limit'] = $offerDetails['prgm_limit_amt'];
			$result[$invDetails->program_id]['disbursement'][$invDetails->supplier_id.'-'.$invDetails->app_id]['limit_utilize'] = Helper::invoiceAnchorLimitApprove($offerDetails);
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
		'invoice'=>function($query2) use($whereCondition, $curdate){
			if(isset($whereCondition['anchor_id'])){
				$query2->where('anchor_id',$whereCondition['anchor_id']);
			}
			/*$query2->whereHas('invoiceStatusLog', function($query3) use($curdate){
				$query3->whereDate('disburse_date',$curdate)
				->where('status_id',12);
			});*/
		},
		'invoice.lms_user', 'invoice.business', 'disbursal','invoice.app.appLimit'])
		->whereIn('status_id', [12,13,15,47])
		->whereHas('invoice', function($query3) use($whereCondition){
			if(isset($whereCondition['anchor_id'])){
				$query3->where('anchor_id',$whereCondition['anchor_id']);
			}
		})
		->whereHas('accruedInterest', function($query3){
			$query3->whereNotNull('overdue_interest_rate');
		})
		->get();

		$sendMail = ($invDisbList->count() > 0)?true:false;

		$result = [];
		foreach($invDisbList as $invDisb){
			$invDetails = $invDisb->invoice;
			$offerDetails = $invDetails->program_offer->toArray();
			$offerDetails['user_id'] = $invDetails->supplier_id;
			$prgmDetails = $invDetails->program;
			
			$limitUsed[$offerDetails['prgm_offer_id']] = $limitUsed[$offerDetails['prgm_offer_id']] ?? round(Helper::invoiceAnchorLimitApprove($offerDetails),2);
			$limitAvl[$offerDetails['prgm_offer_id']] = $limitAvl[$offerDetails['prgm_offer_id']] ?? $offerDetails['prgm_limit_amt'] - $limitUsed[$offerDetails['prgm_offer_id']];
			$limitAvl[$offerDetails['prgm_offer_id']] = ($limitAvl[$offerDetails['prgm_offer_id']] > 0) ? $limitUsed[$offerDetails['prgm_offer_id']] : 0; 
			$result[] = [
				'cust_name'=>$invDisb->invoice->business->biz_entity_name,
				'loan_ac'=>config('common.idprefix.APP').$invDisb->invoice->app_id,
				'customer_id'=>$invDetails->lms_user->customer_id,
				'prgm_name' => $prgmDetails->parentProgram->prgm_name,
				'sub_prgm_name' => $prgmDetails->prgm_name,
				'virtual_ac'=>$invDisb->invoice->lms_user->virtual_acc_id,
				'client_sanction_limit'=>$offerDetails['prgm_limit_amt'],
				'limit_available'=> $limitAvl[$offerDetails['prgm_offer_id']],
				'out_amt'=>$invDisb->transactions->sum('outstanding'),
				'od_days'=>$invDisb->accruedInterest()->whereNotNull('overdue_interest_rate')->get()->count(),
				'od_amt'=>$invDisb->accruedInterest()->whereNotNull('overdue_interest_rate')->sum('accrued_interest'),
				'sales_person_name'=> ($invDisb->invoice->anchor->salesUser->f_name.' '. $invDisb->invoice->anchor->salesUser->m_name.' '. $invDisb->invoice->anchor->salesUser->l_name)
			 ];
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
}
