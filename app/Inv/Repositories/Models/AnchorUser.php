<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DateTime;
use App\Inv\Repositories\Factory\Models\BaseModel;

class AnchorUser extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'anchor_user';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'anchor_user_id';

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
        'name',
        'email',
        'phone',
        'token'
    ];

    /**
     * update user details
     *
     * @param integer $user_id     user id
     * @param array   $arrUserData user data
     *
     * @return boolean
     */
    public static function saveAnchorUser($arrAnchorUser) {
        //Check data is Array
        if (!is_array($arrAnchorUser)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        //Check data is not blank
        if (empty($arrAnchorUser)) {
            throw new BlankDataExceptions(trans('error_message.data_not_found'));
        }

        /**
         * Create anchor
         */
        $arrAnchorUser = self::create($arrAnchorUser);

        return ($arrAnchorUser->anchor_user_id ?: false);
    }

    /**
     * 
     * @return type
     */
    public static function getAllAnchorUsers() {
        $result = self::select('anchor_user.*')
            ->orderByRaw('anchor_user_id DESC');
                //->where('user_type', 1);
        return ($result ? $result : '');
    }
    
    /**
     * function for get particular user detail
     * @param type $email
     * @return type
     */
    public static function getAnchorUsersByEmail($token){
        $arrUser = self::select('anchor_user.*')
             ->where('token', '=', $token)
            ->first();
           return ($arrUser ? $arrUser : FALSE);
    }

}
