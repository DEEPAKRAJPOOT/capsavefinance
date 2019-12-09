<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DateTime;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Models\User;

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
        'anchor_id',
        'name',
        'l_name',
        'biz_name',
        'email',
        'phone',
        'user_type',
        'token',
        'is_registered',
        'registered_type',
        'created_by'
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
            throw new InvalidDataTypeExceptions(trans('error_messages.send_array'));
        }

        //Check data is not blank
        if (empty($arrAnchorUser)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
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
        $roleData = User::getBackendUser(\Auth::user()->user_id);
        
        $result = self::select('anchor_user.*');
             //->join('users', 'users.user_id', '=', 'anchor_user.user_id')
        if ($roleData[0]->is_superadmin != 1) {        
             $result->where('anchor_user.anchor_id', \Auth::user()->anchor_id);
        }
        $result =  $result->orderByRaw('anchor_user_id DESC');
                //->where('user_type', 1);
        return ($result ? $result : '');
    }
    
    /**
     * function for get particular user detail
     * @param type $email
     * @return type
     */
    public static function getAnchorUsersByToken($token){
        $arrUser = self::select('anchor_user.*')
             ->where('token', '=', $token)
            ->first();
           return ($arrUser ? $arrUser : FALSE);
    }
    
    /**
    * 
    * @param type $anchId
    * @param type $arrUserData
    * @return type
    * @throws BlankDataExceptions
    * @throws InvalidDataTypeExceptions
    */ 
    public static function updateAnchorUser($anchUId, $arrUserData = [])
    {
        /**
         * Check id is not blank
         */
        if (empty($anchUId)) {
            throw new BlankDataExceptions(trans('error_messages.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($anchUId)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }

        /**
         * Check Data is Array
         */
        if (!is_array($arrUserData)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.send_array'));
        }

        /**
         * Check Data is not blank
         */
        if (empty($arrUserData)) {
            throw new BlankDataExceptions(trans('error_messages.no_data_found'));
        }

        $rowUpdate = self::find((int) $anchUId)->update($arrUserData);
        return ($rowUpdate ? true : false);
    }
    
    /**
     * function for get particular user detail using email.
     * @param type $email
     * @return type
     */
    public static function getAnchorUsersByEmail($email){
        $arrEmailUser = self::select('anchor_user.*')
             ->where('email', '=', $email)
            ->first();
           return ($arrEmailUser ? $arrEmailUser : FALSE);
    }
}