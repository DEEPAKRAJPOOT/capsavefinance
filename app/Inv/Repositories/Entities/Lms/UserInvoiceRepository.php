<?php

namespace App\Inv\Repositories\Entities\Lms;

use App\Http\Requests\Request;
use Carbon\Carbon;
use DB;
use Session;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Contracts\UserInvoiceInterface;
use App\Inv\Repositories\Models\Lms\UserInvoice;
use App\Inv\Repositories\Models\BizPanGst;
use App\Inv\Repositories\Models\Application;

/**
 * User Invoice Repository class
 */
class UserInvoiceRepository extends BaseRepositories implements UserInvoiceInterface{

	/**
	 * Class constructor
	 *
	 * @return void
	 */    
	public function __construct() {
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

	/**
	 * Get all records method
	 *
	 * @param array $columns
	 */
	public function all($columns = array('*')) {        
	}

	/**
	 * Find method
	 *
	 * @param mixed $id
	 * @param array $columns     
	 */
	public function find($id, $columns = array('*')) {        
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

	public function getAppsByUserId($userId = null) {
		$apps = Application::getAllAppsByUserId($userId);
		return $apps->isEmpty() ? [] : $apps;
	}

        
	public function saveUserInvoice($invoices,$whereCondition=[]) {
		return UserInvoice::saveUserInvoice($invoices,$whereCondition);
	}



	public function getInvoices($whereCondition=[]) {
			return UserInvoice::getInvoices($whereCondition);
	}	
}
