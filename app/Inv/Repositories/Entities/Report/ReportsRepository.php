<?php

namespace App\Inv\Repositories\Entities\Report;

use App\Http\Requests\Request;
use Carbon\Carbon;
use DB;
use Session;
use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Contracts\ReportInterface;
use App\Inv\Repositories\Models\Master\State;
use App\Inv\Repositories\Models\Lms\UserInvoice;
use App\Inv\Repositories\Models\Lms\UserInvoiceTrans;
use App\Inv\Repositories\Models\User as UserModel;


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

	public function getCustomerDetail($userId) {
        $result = UserModel::getCustomerDetail((int) $userId);
        return $result ?: false;
    }

}
