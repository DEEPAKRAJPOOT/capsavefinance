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
        'pan_no',
        'biz_id',
        'email',
        'phone',
        'user_type',
        'token',
        'is_registered',
        'registered_type',
        'is_term_accept',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by',
        // 'supplier_code'
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
    public static function getAllAnchorUsers($datatable=false) {
        $roleData = User::getBackendUser(\Auth::user()->user_id);
        
        $result = self::select('anchor_user.*', 'anchor.comp_name')
             ->join('anchor', 'anchor_user.anchor_id', '=', 'anchor.anchor_id');
        
        if ($roleData[0]->is_superadmin != 1) {        
             $result->where('anchor_user.anchor_id', \Auth::user()->anchor_id);
             //$result->where('anchor_user.created_by', \Auth::user()->user_id);
        }
        if (!$datatable) {
            $result =  $result->orderByRaw('anchor_user_id DESC');
        }
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
     * function for get particular user detail
     * @param type $email
     * @return type
     */
    public static function getAnchorUsersByanchorId($anchor_user_id){
        $arrUser = self::select('anchor_user.*')
             ->where('anchor_user_id', '=', $anchor_user_id)
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
    

    public function anchors(){
        return $this->hasMany('App\Inv\Repositories\Models\Anchor', 'anchor_id', 'anchor_id');               
    }
    
    public static function getAnchorsByUserId($userId) {
        $roleData = User::getBackendUser(\Auth::user()->user_id);
                
        $query = self::select('anchor.*')
            //self::select('anchor.*', 'users.f_name', 'users.l_name', \DB::raw("CONCAT(rta_users.f_name,' ', IFNULL(rta_users.l_name, '')) AS comp_name"))
            ->join('anchor', 'anchor_user.anchor_id', '=', 'anchor.anchor_id');
            /*
            ->join('users', function ($join) {               
                $join->on('users.anchor_id', '=', 'anchor_user.anchor_id');
                $join->on('users.user_type', '=', \DB::raw("2"));
            })
             * 
             */
        if (isset($roleData[0]) && $roleData[0]->id == 11) {
            $query->where('anchor_user.anchor_id', \Auth::user()->anchor_id);
        }
        
        $query->where('anchor_user.user_id', $userId);
        $anchors = $query->get();            
        return ($anchors ? $anchors : []);
    }
    
    /**
     * function for get particular anchor detail using pan.
     * @param type $pan
     * @return type
     */
    public static function getAnchorByPan($pan){
        $arrAnchorData = self::where('pan_no', '=', $pan)->first();
        
        return ($arrAnchorData ? $arrAnchorData : FALSE);
    }
    
    public static function updateAnchorUserData($arrUserData, $whereCond){
        $rowUpdate = self::where($whereCond)->update($arrUserData);
        return ($rowUpdate ? true : false); 
    }
    
    public static function getAnchorUserData($whereCond) {
        $anchors = self::select('anchor_user.*')            
            ->where($whereCond)            
            ->get();            
           return ($anchors ? $anchors : []);
    }
    
    public static function getUsersByPan($userId, $anchorId=null) {
        $query = self::join(\DB::raw('(SELECT rta_anchor_user.pan_no FROM rta_anchor_user WHERE user_id = ?) AS rta_a'), function( $join ) {
                    $join->on( 'anchor_user.pan_no', '=', 'a.pan_no' );
                })
                ->setBindings([$userId]);
        if (!is_null($anchorId)) {
            $query->where('anchor_user.anchor_id', $anchorId); 
        }
        $anchorsUsers = $query->pluck('anchor_user.user_id');
                
        return $anchorsUsers;
    }
    
    /**
    * 
    * @param type $emailId
    * @param type $arrUserData
    * @return type
    * @throws BlankDataExceptions
    * @throws InvalidDataTypeExceptions
    */ 
    public static function updateAnchorUserByEmailId($emailId, $arrUserData = [])
    {
        /**
         * Check Email Id is not blank
         */
        if (empty($emailId)) {
            throw new BlankDataExceptions(trans('error_messages.no_data_found'));
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

        $rowUpdate = self::where('email', $emailId)->update($arrUserData);
        return ($rowUpdate ? true : false);
    }

    public static function getAnchorUserDataDetail($anchorId = null) {
        $roleData = User::getBackendUser(\Auth::user()->user_id);
        
        $result = self::with('user');
        
        if ($roleData[0]->id == 11) {        
             $result->where('anchor_id', $anchorId);
        }
        $data = $result->get();

        return $data;
    }

    public function user(){
        return $this->belongsTo('App\Inv\Repositories\Models\User', 'user_id', 'user_id');               
    }

    public static function getAnchorInactiveUserDataDetail($anchorId = null) {
        $roleData = User::getBackendUser(\Auth::user()->user_id);
        
        $result = self::with('user')
            ->whereHas('user', function ($query) {
                $query->where('is_active','!=' , 1);
            });
        
        if ($roleData[0]->id == 11) {        
             $result->where('anchor_id', $anchorId);
        }
        $data = $result->get();

        return $data;
    }
    
    public static function getAnchorUserDataByDate($whereCond, $date) {
        $anchors = self::select('anchor_user.*')            
            ->where($whereCond)
            ->whereDate('created_at', $date)
            ->get();            
           return ($anchors ? $anchors : []);
    }
}