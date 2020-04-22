<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class InvoiceDisbursed extends BaseModel {
	/* The database table used by the model.
	 *
	 * @var string
	 */

	protected $table = 'invoice_disbursed';

	/**
	 * Custom primary key is set for the table
	 *
	 * @var integer
	 */
	protected $primaryKey = 'invoice_disbursed_id';

	/**
	 * Maintain created_at and updated_at automatically
	 *
	 * @var boolean
	 */
	public $timestamps = true;

	/**
	 * Maintain created_by and updated_by automatically
	 *
	 * @var boolean
	 */
	public $userstamps = true;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'disbursal_id',
		'invoice_id',
		'disbursal_api_log_id',
		'disburse_amt',
		'interest_rate',
		'tenor_days',
		'margin',
		'inv_due_date',
		'payment_due_date',
		'customer_id',
		'total_interest',
		'status_id',
		'grace_period',
		'overdue_interest_rate',
		'int_accrual_start_dt',
		'created_at',
		'created_by',
		'updated_at',
		'updated_by',
	];

	/**
	 * Save or Update
	 * 
	 * @param array $data
	 * @param array $whereCondition | optional
	 * @return mixed
	 * @throws InvalidDataTypeExceptions
	 */
	public static function saveUpdateInvoiceDisbursed($data, $whereCondition=[])
	{
		if (!is_array($data)) {
			throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
		}
		
		if (!empty($whereCondition)) {
			return self::where($whereCondition)->update($data);
		} else if (isset($data[0])) {
			return self::insert($data);
		} else {
			return self::create($data);
		}
	}

	/**
	 * Get disbursal 
	 * 
	 * @return type
	 */
	public function disbursal() { 
		return $this->belongsTo('App\Inv\Repositories\Models\Lms\Disbursal', 'disbursal_id', 'disbursal_id'); 
	}

	public function transactions(){
		return $this->hasMany('App\Inv\Repositories\Models\Lms\Transactions','invoice_disbursed_id','invoice_disbursed_id');
	}

	public function interests() { 
		return $this->hasMany('App\Inv\Repositories\Models\Lms\InterestAccrual', 'invoice_disbursed_id', 'invoice_disbursed_id'); 
	}

	public function invoice(){
		return $this->belongsTo('App\Inv\Repositories\Models\BizInvoice','invoice_id','invoice_id');
	}

	public function accruedInterest(){
        return $this->hasMany('App\Inv\Repositories\Models\Lms\InterestAccrual','invoice_disbursed_id','invoice_disbursed_id');
    }
	
	public function appProgramOffer(){
		return $this->belongsTo('App\Inv\Repositories\Models\AppProgramOffer');
	}

}
