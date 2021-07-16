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
			/*'out_amt'=>$invDisb->transactions->sum('outstanding'),
			'out_days'=>(strtotime($invDisb->payment_due_date) - strtotime($curdate))/86400,
			'tenor'=>$invDisb->tenor_days,
			'due_date'=>$invDisb->payment_due_date,
			'due_amt'=>$invDisb->invoice->invoice_amount,*/
			'trans_utr'=>$invDisb->disbursal->tran_id,
			'remark'=>$invDisb->invoice->remark
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
            if($inv->invoice_disbursed){
				$overdueAmt = $inv->invoice_disbursed->accruedInterest()->whereNotNull('overdue_interest_rate')->sum('accrued_interest');
                $overdueDays = $inv->invoice_disbursed->accruedInterest()->whereNotNull('overdue_interest_rate')->get()->count();
                $marginRate = $inv->invoice_disbursed->margin;
				$principalOdAmount = round(($inv->invoice_disbursed->disburse_amt + $inv->invoice_disbursed->total_interest - $inv->principal_repayment_amt),2);
				$principalOdAmount = $principalOdAmount>0?$principalOdAmount:0;
				if(strtotime($inv->invoice_disbursed->payment_due_date) <= strtotime($curdate) && $principalOdAmount > 0){
					$date = Carbon::parse($inv->invoice_disbursed->payment_due_date);
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
				'disb_amt' => in_array($invDetails->status_id, [12,13,15])?$invDetails->invoice_approve_amount:0,
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
			$limit = AppProgramLimit::getProductLimit($invDisb->invoice->lms_user->app_id, 1)->sum('product_limit');
			$limit_utilize = AppProgramLimit::getUtilizeLimit($invDisb->invoice->lms_user->app_id, 1)->sum('utilize_limit');
			$result[] = [
				'cust_name'=>$invDisb->invoice->business->biz_entity_name,
				'loan_ac'=>config('common.idprefix.APP').$invDisb->invoice->app_id,
				'virtual_ac'=>$invDisb->invoice->lms_user->virtual_acc_id,
				'client_sanction_limit'=>$limit,
				'limit_available'=>($limit-$limit_utilize),
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
