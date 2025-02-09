<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use Carbon\Carbon;

class RefundBatch extends BaseModel {
	/* The database table used by the model.
	 *
	 * @var string
	 */

	protected $table = 'lms_refund_batch';

	/**
	 * Custom primary key is set for the table
	 *
	 * @var integer
	 */
	protected $primaryKey = 'refund_batch_id';

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
		'batch_type',
		'batch_id',
		'file_id',
		'created_at',
		'created_by',
		'updated_at',
		'updated_by',
	];

}
