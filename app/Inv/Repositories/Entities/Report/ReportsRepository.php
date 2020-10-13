<?php

namespace App\Inv\Repositories\Entities\Report;

use DB;
use Session;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Http\Requests\Request;
use App\Inv\Repositories\Models\Payment;
use App\Inv\Repositories\Models\Master\State;
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

	public function getMaturityReport($whereCondition=[]){
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

		$result = [];
		foreach($invDisbList as $invDisb){
			$result[] = [
			'cust_name'=>$invDisb->invoice->business->biz_entity_name,
			'loan_ac'=>$invDisb->disbursal->acc_no,
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

	public function getDisbursalReport($whereCondition=[]){
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
		->whereHas('disbursal', function($query3) use($curdate){
			$query3->whereDate('disburse_date',$curdate);
		})
		->get();

		$result = [];
		foreach($invDisbList as $invDisb){
			$result[] = [
			'cust_name'=>$invDisb->invoice->business->biz_entity_name,
			'loan_ac'=>$invDisb->disbursal->acc_no,
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
			'trans_utr'=>$invDisb->disbursal->tran_id,
			'remark'=>$invDisb->invoice->remark
			];
		}
		return $result;
	}
}
