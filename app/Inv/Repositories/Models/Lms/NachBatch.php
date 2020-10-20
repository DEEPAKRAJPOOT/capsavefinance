<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use Carbon\Carbon;

class NachBatch extends BaseModel {
	/* The database table used by the model.
	 *
	 * @var string
	 */

	protected $table = 'nach_batch';

	/**
	 * Custom primary key is set for the table
	 *
	 * @var integer
	 */
	protected $primaryKey = 'nach_batch_id';

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
		'req_file_id',
                'res_file_id',
		'status',
		'created_at',
		'created_by',
		'updated_at',
		'updated_by',
	];

    /**
     * Save Nach Batch
     * 
     * @param type $attributes array
     * @param type $id int
     * @return type mixed
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions 
     */
    public static function saveNachBatch($attributes, $nach_batch_id = null)
    {
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        if (empty($attributes)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        $query = self::updateOrCreate(['nach_batch_id' => $nach_batch_id], $attributes);
        return $query ? $query->nach_batch_id : $id;
    }
}
