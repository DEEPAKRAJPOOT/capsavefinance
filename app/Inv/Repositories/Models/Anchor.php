<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DateTime;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Models\AppProgramLimit;


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

    
   
    public function salesUser(){
        return $this->hasOne('App\Inv\Repositories\Models\User', 'user_id', 'sales_user_id');
    }

     
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
            throw new InvalidDataTypeExceptions(trans('error_messages.send_array'));
        }

        //Check data is not blank
        if (empty($arrAnchor)) {
            throw new BlankDataExceptions(trans('error_messages.data_not_found'));
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
    public static function getAllAnchor($orderBy='anchor_id') {
        $result = self::select('anchor.*', 'u.user_id', 'u.f_name','f.file_path')
                ->join('users as u', 'anchor.anchor_id', '=', 'u.anchor_id')
                ->leftjoin('user_doc as u_doc', function($join)
                {
                    $join->on( 'u.user_id', '=', 'u_doc.user_id')
                    ->where('u_doc.is_active', 1);
                });
        $result->leftjoin('file as f','f.file_id','=','u_doc.file_id')
                ->where('u.user_type', 2);
    
        if ($orderBy == 'anchor_id') {
            $result->orderBy('anchor.anchor_id', 'DESC');
        } else {
            $result->orderBy('anchor.comp_name');
        }
            
        $result = $result->get();
        return ($result ? $result : false);
    }
    
//    public static function getAllAnchorUsers() {
//        $result = self::select('anchor_user.*', 'ud.country_id',
//             'ud.date_of_birth')
//                ->join('users as u', 'anchor_user.anchor_id', '=', 'u.anchor_id')
//            ->orderByRaw('anchor_user_id DESC');
//                //->where('user_type', 1);
//        return ($result ? $result : '');
//    }
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
            throw new BlankDataExceptions(trans('error_messages.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($anchId)) {
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

        $rowUpdate = self::find((int) $anchId)->update($arrUserData);
        return ($rowUpdate ? true : false);
    }
    
    
    /**
     * get all anchor list 
     * 
     * @param type $id int
     * @return type mixed
     */
    public static function getAnchorDataById($id)
    {
       
         if (empty($id)) {
            throw new BlankDataExceptions(trans('error_messages.no_data_found'));
        }

        if (!is_int($id)) {
            throw new InvalidDataTypeExceptions(trans('error_messages.invalid_data_type'));
        }
        $result = self::
                join('users as u', 'anchor.anchor_id', '=', 'u.anchor_id')
                ->where(['u.user_type' => 2, 'anchor.anchor_id' => $id])->get();
               
        return ($result ? : false);
    }

    public function prgmData()
    {
        return $this->hasOne('App\Inv\Repositories\Models\Program', 'anchor_id', 'anchor_id')->where(['status'=>1, 'parent_prgm_id'=> 0]);
    }
}