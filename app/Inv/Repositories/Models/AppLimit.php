<?php

namespace App\Inv\Repositories\Models;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class AppLimit extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'app_limit';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'app_limit_id';

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
        'app_id',
        'biz_id',
        'tot_limit_amt',
        'created_at',
        'created_by',
        'updated_at',        
        'updated_by',
    ];

    public static function saveAppLimit($data, $limit_id){
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        if (!is_null($limit_id)) {
            return self::where('app_limit_id', $limit_id)->update($data);
        } else {
            return self::create($data);
        }
    }    
}
