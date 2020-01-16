<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DateTime;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class CoLenderUsers extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'co_lenders_user';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'co_lender_user_id';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    //protected $dates = ['deleted_at'];

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
        'user_id',
        'anchor_id',
        'name',
        'l_name',
        'biz_name',
        'email',
        'phone',
        'user_type',
        'token',
        'is_registered',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    
    /**
     * save co lender Users
     * 
     * @param Array $attributes
     * @return mixed
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions
     */
    public static function saveColenderUsers($attributes)
    {
        //Check data is Array
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.send_array'));
        }

        //Check data is not blank
        if (empty($attributes)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }

        /**
         * Create anchor
         */
        $res = self::create($attributes);

        return ($res->co_lender_user_id ?: false);
    }

}
