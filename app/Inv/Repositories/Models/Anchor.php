<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DateTime;
use App\Inv\Repositories\Factory\Models\BaseModel;


class Anchor extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'anchor';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'anchor_id';

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
        'sales_user_id',
        'comp_name',
        'comp_email',
        'comp_addr',
        'comp_state',
        'comp_city',
        'comp_zip',
        'comp_phone',
        'doc_name',
        'is_active',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    
    /**
     * update user details
     *
     * @param integer $user_id     user id
     * @param array   $arrUserData user data
     *
     * @return boolean
     */
public static function saveAnchor($arrAnchor = [])
    {
        //Check data is Array
        if (!is_array($arrAnchor)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        //Check data is not blank
        if (empty($arrAnchor)) {
            throw new BlankDataExceptions(trans('error_message.data_not_found'));
        }
        
        /**
         * Create anchor
         */
        $arrAnchorVal = self::create($arrAnchor);

        return ($arrAnchorVal->anchor_id ?: false);
    }
    
      /**
     * 
     * @return type
     */
    public static function getAllAnchor() {
        $result = self::select('anchor.*')
            ->orderByRaw('anchor_id DESC');
                //->where('user_type', 1);
        return ($result ? $result : false);
    }
     /**
     * function for get particular user detail
     * @param type $email
     * @return type
     */
    public static function getAnchorById($anch_id){
        $arrUser = self::select('anchor.*')
             ->where('anchor_id', '=', $anch_id)
            ->first();
           return ($arrUser ? $arrUser : FALSE);
    }
    
    
    /**
     * function for get particular user detail using email.
     * @param type $email
     * @return type
     */
    public static function getAnchorsByEmail($email){
        $arrEmailUser = self::select('anchor.*')
             ->where('email', '=', $email)
            ->first();
           return ($arrEmailUser ? $arrEmailUser : FALSE);
    }
   /**
    * 
    * @param type $anchId
    * @param type $arrUserData
    * @return type
    * @throws BlankDataExceptions
    * @throws InvalidDataTypeExceptions
    */ 
    public static function updateAnchor($anchId, $arrUserData = [])
    {
        /**
         * Check id is not blank
         */
        if (empty($anchId)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($anchId)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        /**
         * Check Data is Array
         */
        if (!is_array($arrUserData)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        /**
         * Check Data is not blank
         */
        if (empty($arrUserData)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        $rowUpdate = self::find((int) $anchId)->update($arrUserData);
        return ($rowUpdate ? true : false);
    }
}