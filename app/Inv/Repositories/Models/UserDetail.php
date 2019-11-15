<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DateTime;
use App\Inv\Repositories\Factory\Models\BaseModel;

class UserDetail extends BaseModel
{
    /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_detail';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'user_detail_id';

    
    protected $fillable = [
        'user_detail_id',
        'user_id',
        'access_token',
        'created_by'
    ];

     /**
     * Save User Detail
     *
     * @param  array $arrUsers
     *
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions
     */
    public static function saveUserDetails($arrUsers = [])
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

        return ($objUsers ?: false);
    }
   
    
}


