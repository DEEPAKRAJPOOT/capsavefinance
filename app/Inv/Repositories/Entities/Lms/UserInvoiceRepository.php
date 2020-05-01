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
use App\Inv\Repositories\Models\Master\Company;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\UserBankAccount;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\Lms\UserInvoiceRelation;
use App\Inv\Repositories\Models\Lms\InvoiceNo;
use App\Inv\Repositories\Models\BusinessAddress;


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

	public function getInvoiceById(int $user_invoice_id) {
			return UserInvoice::getInvoiceById($user_invoice_id);
	}	

	public function getStateById(int $state_id) {
		return State::getStateById($state_id);
	}

	public function getNextInv($data) {
		return InvoiceNo::create($data);
	}

	public function getUserCompanyRelation(int $user_id) {
		return UserInvoiceRelation::getUserCurrCompany($user_id);
	}

	public function getCompanyRegAddr() {
		return Company::getCompanyRegAddr();
	}

	public function getCompanyDetail($company_id) {
		return Company::findCompanyById($company_id);
	}

	public function getCompanyBankAcc($company_id) {
		return UserBankAccount::getAllCompanyBankAcc($company_id);
	}

	public function getUserInvoiceTxns($userId, $invoiceType = 'I', $transIds = [], $is_force = false) {
		$UserInvoiceTxns = Transactions::getUserInvoiceTxns($userId, $invoiceType, $transIds, $is_force);
		if (empty($transIds)) {
			foreach ($UserInvoiceTxns as $key => $txn) {
				$waiveOffAmount = $txn->getWaiveOffAmount();
				$txn->amount = $txn->amount -$waiveOffAmount;
				if ($txn->amount > 0) {
					unset($UserInvoiceTxns[$key]);
				}
			}
		}
		return $UserInvoiceTxns;
	}
	public function getTxnByTransId(int $trans_id) {
		return Transactions::find($trans_id);
	}

	public function updateIsInvoiceGenerated($transDataArray){
		$data = ['is_invoice_generated' => 1];
		return Transactions::updateIsInvoiceGenerated($transDataArray, $data);
	}

	/**
	 * Get AJAX list of user invoice result
	 */
	public function getUserInvoiceList($user_id) {
		return UserInvoice::getUserInvoiceList($user_id);
	}

	/**
	 * Get capsave address from mst_company
	 */
	public function getCapsavAddr() {
		return Company::getCapsavAddr();
	}
	/**
	 * POST save capsave address from mst_company
	 */
	public function saveUserInvoiceLocation($userInvoiceData) {
		return UserInvoiceRelation::saveUserInvoiceLocation($userInvoiceData);
	}

	public function getBusinessAddressByaddrId(int $biz_addr_id) {
		return BusinessAddress::getAddressByAddrId($biz_addr_id);
	}

	public function unPublishAddr($user_id) {
		return UserInvoiceRelation::unPublishAddr($user_id);
	}

	public function checkUserInvoiceLocation($userInvData) {
		return UserInvoiceRelation::checkUserInvoiceLocation($userInvData);
	}

	public function getCustAndCapsLoc($user_id) {
		return UserInvoiceRelation::getCustAndCapsLoc($user_id);
	}

	public function getUserAllApplicationsDetail(int $user_id) {
		return Application::getApplicationsDetail($user_id);
	}

}
