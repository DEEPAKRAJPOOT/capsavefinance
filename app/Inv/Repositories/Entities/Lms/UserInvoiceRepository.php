<?php

namespace App\Inv\Repositories\Entities\Lms;

use App\Http\Requests\Request;
use Carbon\Carbon;
use DB;
use Session;
use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Contracts\UserInvoiceInterface;
use App\Inv\Repositories\Models\Master\State;
use App\Inv\Repositories\Models\Lms\UserInvoice;
use App\Inv\Repositories\Models\Lms\UserInvoiceTrans;
use App\Inv\Repositories\Models\BizPanGst;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\LmsUser;
use App\Inv\Repositories\Models\Lms\Transactions;

/**
 * User Invoice Repository class
 */
class UserInvoiceRepository extends BaseRepositories implements UserInvoiceInterface{
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

	

	public function getBizId($appId = null) {
		$records =  Application::where('app_id', $appId)->first();
		return $records->biz_id ?? NULL;
	}


	public function getGSTs($appId = null) {
		$biz_id = $this->getBizId($appId);
		$gsts = [];
		if (!empty($biz_id)) {
			$gsts = BizPanGst::where(['biz_id' => $biz_id, 'type' => 2])->where('parent_pan_gst_id', '!=', 0)->get();
		}
		return $gsts->isEmpty() ? [] : $gsts;
	}

	public function getPAN($appId = null) {
		$biz_id = $this->getBizId($appId);
		$pan = [];
		if (!empty($biz_id)) {
			$pan = BizPanGst::where(['biz_id' => $biz_id, 'type' => 1])->where('parent_pan_gst_id', '=', 0)->get();
		}
		return $pan->isEmpty() ? [] : $pan;
	}

	public function getAppsByUserId($userId = null) {
		$apps = Application::getAllAppsNbizByUserId($userId);
		return $apps->isEmpty() ? [] : $apps;
	}

	public function saveUserInvoice($invoices,$whereCondition=[]) {
		return UserInvoice::saveUserInvoice($invoices,$whereCondition);
	}

	public function saveUserInvoiceTxns($invoices_txns,$whereCondition=[]) {
		return UserInvoiceTrans::saveUserInvoiceTxns($invoices_txns,$whereCondition);
	}

	public function getInvoices($whereCondition=[]) {
			return UserInvoice::getInvoices($whereCondition);
	}	

	public function getStateListCode() {
		return State::getStateListCode();
	}

	public function getUserCustomerID($user_id) {
		return LmsUser::getCustomers($user_id);
	}

	/**
     * Get state code by ajax
     */
	public function getUserStateCodeList($state_code) {
		return State::getUserStateCodeList($state_code);
	}

	/**
     * Get User invoice id
     */
	public function findUserInvoiceById($userInvoice_id) {
		return UserInvoice::findUserInvoiceById($userInvoice_id);
	}

	public function getUserInvoiceTxns($userId, $invoiceType = 'I', $transIds = []) {
		return Transactions::getUserInvoiceTxns($userId, $invoiceType, $transIds);
	}
}
