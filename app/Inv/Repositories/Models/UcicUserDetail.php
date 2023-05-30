<?php

namespace App\Inv\Repositories\Models;

use DB;
use Helpers;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class UcicUserDetail extends BaseModel
{
    
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'user_ucic_detail';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'user_ucic_detail_id';

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
        'user_ucic_id',
        'invoice_level_mail',
        'updated_at',
        'created_at'
    ];

    public static function saveUserUcicDetail($attributes){

        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.send_array'));
        }

        if (empty($attributes)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }

        return self::create($attributes);
    }

    public static function deleteUcicUserDetail($userUcicId){

        if (empty($userUcicId)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
        }
        return self::where('user_ucic_id',$userUcicId)->delete();
    }
}
