<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use Carbon\Carbon;

class NachRepaymentReqBatch extends BaseModel {
	/* The database table used by the model.
	 *
	 * @var string
	 */

	protected $table = 'lms_nach_repayment_req_batch';

	/**
	 * Custom primary key is set for the table
	 *
	 * @var integer
	 */
	protected $primaryKey = 'req_batch_id';

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
		'batch_no',
		'file_id',
		'batch_status',
		'created_at',
		'created_by',
		'updated_at',
		'updated_by',
	];


	public function disbursal() { 
		return $this->hasMany('App\Inv\Repositories\Models\Lms\Disbursal', 'disbursal_batch_id', 'disbursal_batch_id'); 
	}

	public function disbursal_api_log() { 
		return $this->belongsTo('App\Inv\Repositories\Models\Lms\DisburseApiLog', 'disbursal_api_log_id', 'disbursal_api_log_id'); 
	}

}
