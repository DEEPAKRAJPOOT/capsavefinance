<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;

class ResetPasswordToken extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'resetpassword_token';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    //protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'token',
        'is_reset',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];
    
     /**
     * Save User Detail
     *
     * @param  array $arrUsers
     *
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions
     */
    public static function saveUserTokenDetails($arrUsers = [])
    {

        //Check data is Array
        if (!is_array($arrUsers)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        //Check data is not blank
        if (empty($arrUsers)) {
            throw new BlankDataExceptions(trans('error_message.data_not_found'));
        }
        /**
         * Create User Detail
         */
        $objUsers = self::create($arrUsers);

        return ($objUsers->id ?: false);
    }
    
    /**
     * Get Skills
     *
     * @param  integer $userId
     *
     * @return mixed Array | Boolean false
     * @throws InvalidDataTypeExceptions
     */
    public static function getUserTokenDetails($token)
    {
        /**
         * Check user id is not an integer
         */
        //die($token);
        if (empty($token)) {
            throw new InvalidDataTypeExceptions(trans('error_message.data_not_found'));
        }

        $arrSkill = self::where('token',$token)
            ->get();

        return ($arrSkill ?: false);
    }
}