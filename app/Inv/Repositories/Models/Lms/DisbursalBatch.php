<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use Carbon\Carbon;

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
		'disbursal_api_log_id',
		'batch_status',
		'created_at',
		'created_by',
		'updated_at',
		'updated_by',
	];


	public function disbursal() { 
		return $this->hasMany('App\Inv\Repositories\Models\Lms\Disbursal', 'disbursal_batch_id', 'disbursal_batch_id'); 
	}


	public function disbursalOne() { 
		return $this->hasOne('App\Inv\Repositories\Models\Lms\Disbursal', 'disbursal_batch_id', 'disbursal_batch_id'); 
	}

	public function disbursal_api_log() { 
		return $this->belongsTo('App\Inv\Repositories\Models\Lms\DisburseApiLog', 'disbursal_batch_id', 'disbursal_batch_id')->orderBy('disbursal_batch_id', 'DESC'); 
	}

	public static function getAllBatches($from_date, $to_date){
		$from_date = Carbon::createFromFormat('d/m/Y', $from_date)->format('Y-m-d');
		$to_date = Carbon::createFromFormat('d/m/Y', $to_date)->format('Y-m-d');
        $result = \DB::select("SELECT rta_disbursal_batch.disbursal_batch_id, rta_disbursal_batch.batch_id, rta_disbursal_batch.created_by, rta_disbursal_batch.created_at, A.total_users, A.total_amt, rta_users.f_name as created_by_user
		FROM rta_disbursal_batch
		JOIN rta_users ON (rta_users.user_id=rta_disbursal_batch.created_by)
		JOIN (SELECT rta_disbursal.disbursal_batch_id, COUNT(DISTINCT(user_id)) as total_users, SUM(disburse_amount) as total_amt FROM rta_disbursal
		WHERE rta_disbursal.disbursal_batch_id IS NOT null GROUP BY rta_disbursal.disbursal_batch_id) A ON (A.disbursal_batch_id=rta_disbursal_batch.disbursal_batch_id)
		WHERE rta_disbursal_batch.created_at>=? AND rta_disbursal_batch.created_at<=?
		ORDER BY rta_disbursal_batch.disbursal_batch_id DESC", [$from_date, $to_date]);
        return $result;    
	}
	
	public static function lmsGetDisbursalBatchRequest()
    {
        return self::with('disbursal')
        		->where('batch_status', 1)
        		->whereHas('disbursal', function($query) {
						$query->where('disburse_type', 1);
				})
        		->orderBy('disbursal_batch_id', 'DESC');
    }

	public static function lmsGetDisbursalBatchRequestCron()
	{
		$today = Carbon::now()->timezone(config('common.timezone'));
		$to_date = $today->toDateString();
                //$disbursal_id = 5376;
		$query = self::with('disbursal')
					->where('batch_status', 1)
					->whereHas('disbursal', function($query) {
						$query->where('disburse_type', 1);
					})
					->orderBy('disbursal_batch_id', 'DESC')
                                        ->where('disbursal_id', '=', $disbursal_id)
					//->whereDate('created_at', '=', $to_date)
					->get();
		return $query;
	}

}
