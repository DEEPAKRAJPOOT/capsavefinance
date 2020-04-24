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
use App\Inv\Repositories\Models\BizPanGst;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\LmsUser;

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



	public function getInvoices($whereCondition=[]) {
			return UserInvoice::getInvoices($whereCondition);
	}	

	public function getStateListCode() {
		return State::getStateListCode();
	}

	/**
     * Get bussiness address for user invoice
     */ 
    public function getBizUserInvoiceAddr($user_id) {
		$addr = 'Ador Powertron Limited Plot No-51, D-2 Block,Ram Nagar Complex,MIDC, Chinchwad, Pune, Maharashtra, 411019';
        return response()->json($addr);
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

	/**
     * save user invoice
     */
	public function saveUserInvoiceData($arrUserData) {
		return UserInvoice::saveUserInvoiceData($arrUserData);
	}

	/**
     * update user invoice
     */
	public function updateUserInvoice($arrUserData, $userInvoice_id) {
		return UserInvoice::updateUserInvoice($arrUserData, $userInvoice_id);
	}

	/**
	 * Get AJAX list of user invoice result
	 */
	public function getUserInvoiceList($user_id, $appId) {
		return UserInvoice::getUserInvoiceList($user_id, $appId);
	}
}
