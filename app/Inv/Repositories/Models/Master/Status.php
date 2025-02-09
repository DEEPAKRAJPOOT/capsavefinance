<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class Status extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_status';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'id';

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
        'status_type',
        'status_name',
        'is_active'
    ]; 
    
    
    
    /**
     * get status list
     * 
     * @return mixed
     */
    
    public static function getStatusList($status_type)
    {
        if (empty($status_type)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        if (!is_int($status_type)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $res = self::where('is_active',1)->where('status_type', $status_type)->pluck('status_name','id');
        return $res ?: false;
    }
}