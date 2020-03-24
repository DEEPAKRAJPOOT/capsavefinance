<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class DisbursalBatch extends BaseModel {
	/* The database table used by the model.
	 *
	 * @var string
	 */

	protected $table = 'disbursal_batch';

	/**
	 * Custom primary key is set for the table
	 *
	 * @var integer
	 */
	protected $primaryKey = 'disbursal_batch_id';

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
		'batch_id',
		'file_id',
		'created_at',
		'created_by',
		'updated_at',
		'updated_by',
	];

	public static function getAllBatches(){
        $result = \DB::select("SELECT rta_disbursal_batch.disbursal_batch_id, rta_disbursal_batch.batch_id, rta_disbursal_batch.created_by, rta_disbursal_batch.created_at, A.total_users, A.total_amt, rta_users.f_name as created_by_user
		FROM rta_disbursal_batch
		JOIN rta_users ON (rta_users.user_id=rta_disbursal_batch.created_by)
		JOIN (SELECT rta_disbursal.disbursal_batch_id, COUNT(DISTINCT(user_id)) as total_users, SUM(disburse_amount) as total_amt FROM rta_disbursal
		WHERE rta_disbursal.disbursal_batch_id IS NOT null GROUP BY rta_disbursal.disbursal_batch_id) A ON (A.disbursal_batch_id=rta_disbursal_batch.disbursal_batch_id)
		ORDER BY rta_disbursal_batch.disbursal_batch_id DESC");
        return $result;    
	}
}
