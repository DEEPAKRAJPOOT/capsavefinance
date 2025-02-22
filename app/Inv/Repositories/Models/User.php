<?php

namespace App\Inv\Repositories\Models;

use DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\Master\Role as Role;
use App\Inv\Repositories\Models\Master\Permission;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\AppProgramLimit;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\LmsUser;
use App\Inv\Repositories\Models\LmsUsersLog;

class User extends Authenticatable
{
     use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'user_id';

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
        'anchor_id',
        'agency_id',
        'co_lender_id',
        'anchor_user_id',
        'f_name',
        'm_name',
        'l_name',
        'biz_name',
//        'pan_no',
        'email',
        'password',
        'mobile_no',
        'user_type',
        'is_buyer',
        'is_email_verified',
        'email_verified_updatetime',
        'is_otp_verified',
        'otp_verified_updatetime',
        'is_pwd_changed',
        'pwd_updatetime',
        'is_active',
        'block_status_id',
        'block_status_updatetime',
        'parent_id',
        'is_appr_required',
        'state_id',
        'city_id',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by',
        // 'supplier_code',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * update user details
     *
     * @param integer $user_id     user id
     * @param array   $arrUserData user data
     *
     * @return boolean
     */

    
    public static function updateUser($user_id, $arrUserData = [])
    {
        /**
         * Check id is not blank
         */
        if (empty($user_id)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($user_id)) {
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

        $rowUpdate = self::find((int) $user_id)->update($arrUserData);

        return ($rowUpdate ? $user_id : false);
    }

    /**
     * Get User Details base of user Id
     *
     * @param  integer $user_id
     * @return array
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     * Since 0.1
     */
    public static function getUserDetail($user_id)
    {
        
        //Check id is not blank

        if (empty($user_id)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        //Check id is not an integer

        if (!is_int($user_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
         $arrUser = self::from('users as u')
            ->select('u.*')
            ->where('u.user_id', (int) $user_id)
            ->first();
         
            
        return ($arrUser ?: false);
    }

    /**
     * Get User Details base of user Id
     *
     * @param  integer $user_id
     * @return array
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     * Since 0.1
     */
    public static function getCustomerDetail($user_id)
    {
         $arrUser = self::where('user_id', (int) $user_id)
            ->with(['anchor.salesUser','biz'])
            ->first();
         

        return ($arrUser ?: false);
    }
/**
     * Get User Details base of user Id
     *
     * @param  integer $user_id
     * @return array
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     * Since 0.1
     */
    public static function getUserID($user_id)
    {
        //dd($user_id);
        //Check id is not blank

        if (empty($user_id)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        //Check id is not an integer

        if (!is_int($user_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $arrUser = self::select('users.*')
            ->where('users.user_id', (int) $user_id)
            ->first();

        return ($arrUser->user_id ?: false);
    }

  
   /**
     * Get User Details base of user Id
     *
     * @param  integer $user_id
     * @return array
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     * Since 0.1
     */
    public static function getfullUserDetail($user_id)
    {
        //dd($user_id);
        //Check id is not blank

        if (empty($user_id)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        //Check id is not an integer

        if (!is_int($user_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $arrUser = self::select('users.*')
            ->where('users.user_id', (int) $user_id)
            ->first();

        return ($arrUser ?: false);
    }

    /**
     * Get all users
     *
     * @return array
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     * Since 0.1
     */
    public static function getAllUsers()
    {
        $userArr = \Helpers::getChildUsersWithParent(\Auth::user()->user_id);
        $roleData = User::getBackendUser(\Auth::user()->user_id);
        $result = self::select('users.user_id','users.f_name','users.l_name','users.email',
                'users.mobile_no','users.created_at', 'users.anchor_id as UserAnchorId',
                'users.is_buyer as AnchUserType','lead_assign.to_id','anchor_user.pan_no','users.is_active','non_anchor_leads.pan_no as nonAnchorPanNo')                
                ->join('lead_assign', function ($join) {
                    $join->on('lead_assign.assigned_user_id', '=', 'users.user_id');
                    $join->on('lead_assign.is_owner', '=', DB::raw("1"));
                    $join->on('lead_assign.is_deleted', '=', DB::raw("0"));                    
                })
                ->leftJoin('anchor_user', 'anchor_user.user_id', '=', 'users.user_id')
                ->leftJoin('non_anchor_leads', 'non_anchor_leads.user_id', '=', 'users.user_id')
                 ->where('users.user_type', 1);
        if ($roleData[0]->id == 11) {
            //$result->where('users.anchor_id', \Auth::user()->anchor_id);                        
            $result->where('anchor_user.anchor_id', \Auth::user()->anchor_id);                        
        } else if ($roleData[0]->is_superadmin != 1) {
            //$result->where('lead_assign.to_id', \Auth::user()->user_id);
            $result->whereIn('lead_assign.to_id', $userArr);            
        }
        $result->groupBy('users.user_id');
        $result = $result->orderBy('users.user_id', 'desc');
              
        return ($result ? $result : '');
    }

    /**
     * Get all users by role's user id
     *
     * @return array
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     * Since 0.1
     */
    public static function getAssignedUsers($role_id,$to_id)
    {
        $userArr = array($to_id);
        $result = self::select('users.user_id','users.f_name','users.l_name','users.email',
                'users.mobile_no','users.created_at', 'users.anchor_id as UserAnchorId',
                'users.is_buyer as AnchUserType','lead_assign.to_id','anchor_user.pan_no')                
                ->join('lead_assign', function ($join) {
                    $join->on('lead_assign.assigned_user_id', '=', 'users.user_id');
                    $join->on('lead_assign.is_owner', '=', DB::raw("1"));
                    $join->on('lead_assign.is_deleted', '=', DB::raw("0"));                    
                })
                ->leftJoin('anchor_user', 'anchor_user.user_id', '=', 'users.user_id')
                 ->where('users.user_type', 1);
       
            $result->whereIn('lead_assign.to_id', $userArr);            
        
        $result->groupBy('users.user_id');
        $result = $result->orderBy('users.user_id', 'desc');
              
        return ($result ? $result : '');
    }
    
    /**
     * Get all users
     *
     * @return array
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     * Since 0.1
     */
    public static function getAllUsersPaginate()
    {
        $result = self::select('users.*')
            ->where('user_type', 1)->paginate(5);
        return ($result ? $result : '');
    }
    
    /**
     * getUserByemail
     * @param $email
     * @return array $arrUser
     * @since 0.1
     * @author Minee Soni
     */
    public static function getUserByemail($email)
    {
        $arrUser = SELF::select('*')
            ->where('email', '=', $email)
            ->first();
        return ($arrUser ? $arrUser : []);
    }


    /**
     * update backend user details
     *
     * @param integer $user_id     user id
     * @param array   $arrUserData user data
     *
     * @return boolean
     */
    public static function updateBackendUser($user_id, $arrUserData = [])
    {
        /**
         * Check id is not blank
         */
        if (empty($user_id)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($user_id)) {
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

        $rowUpdate = self::find((int) $user_id)->update($arrUserData);
        return ($rowUpdate ? true : false);
    }




    /**
     * Get User Details base of user Id
     *
     * @param  integer $user_id
     * @return array
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     * Since 0.1
     */
    public static function getUserPersonalData($user_id)
    {
       // return User::find($user_id);
        //Check id is not blank

        if (empty($user_id)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        //Check id is not an integer

        if (!is_int($user_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        /*$arrUser = self::select('users.*')
            ->where('users.user_id', (int) $user_id)
            ->first();
        */

         $arrUser = self::select('users.*', 'ud.country_id',
             'ud.date_of_birth'
              )

            ->join('user_detail as ud', 'users.user_id', '=', 'ud.user_id')
            ->where('users.user_id', (int) $user_id)
            ->first();




        return ($arrUser ?: false);
    }

   /**
     * Get User Details base of user Id
     *
     * @param  integer $user_id
     * @return array
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     * Since 0.1
     */
    public static function getUserCorpData($user_id)
    {
       // return User::find($user_id);
        //Check id is not blank

        if (empty($user_id)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        //Check id is not an integer

        if (!is_int($user_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        /*$arrUser = self::select('users.*')
            ->where('users.user_id', (int) $user_id)
            ->first();
*/

         $arrUser = self::select('users.*', 'ud.country_id',
             'ud.corp_name', 'ud.corp_date_of_formation',
             'ud.corp_date_of_formation', 'ud.corp_license_number')

            ->join('corp_detail as ud', 'users.user_id', '=', 'ud.user_id')
            ->where('users.user_id', (int) $user_id)
            ->first();




        return ($arrUser ?: false);
    }
    
   /**
     * A user may have multiple roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, "role_user", 'user_id')->where('is_logged_in_role', 1);
    }

    public function allRoles()
    {
        return $this->belongsToMany(Role::class, "role_user", 'user_id');
    }

    /**
     * Assign the given role to the user.
     *
     * @param string $role
     *
     * @return mixed
     */
    public function assignRole($role_id)
    {
        return $this->roles()->sync(array($role_id));
    }

    /**
     * Determine if the user has the given role.
     *
     * @param  mixed $role
     * @return boolean
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }
        return !!$role->intersect($this->roles)->count();
    }

    /**
     * Determine if the user may perform the given permission.
     *
     * @param Permission $permission
     *
     * @return boolean
     */
    public function hasPermission(Permission $permission)
    {
        return $this->hasRole($permission->roles);
    }

    /**
     * Get Roles by user id
     *
     * @param $user_id user id
     *
     * @return object roles
     */
    public static function getUserRoles($user_id)
    {
        // dd($user_id);
        /**
         * Check id is not blank
         */
        if (empty($user_id)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($user_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        $arrRoles = self::find($user_id)->roles;
        // dd($arrRoles);
        return ($arrRoles ? : false);
    }
    
    
    /**
     * Backend user scope
     *
     * @param type $query
     *
     * @return type
     */
    public  function scopeBackendUser($query)
    {
        return $query->where('user_type', '=', 2);
    }

   /**
     * Get backend user data w.r.t id
     *
     * @param integer $user_id
     *
     * @return array User List
     */
    public static function getBackendUser($user_id)
    {
         $users = self::getUserRoles($user_id);
         return $users;
    }

    /**
     * Get  user email exist
     *
     * @param integer $user_id
     *
     * @return array User List
     */
    public static function checkUserEmailExist($email,$anchId,$userType)
    {
        $userEmailExist = self::where('email','=',$email)
                   ->where('user_type','=',$userType)
                   ->where('user_id','!=',$anchId)->count();
         
        return $userEmailExist;
    }

    /**
     * Get Anchor Details using anchor id
     *
     * @param  integer $anchId
     * @return array
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     * Since 0.1
     */
    public static function getAnchorByAnchorId($anchId)
    {
        
        //Check anchId is not blank
        if (empty($anchId)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        //Check anchId is not an integer

        if (!is_int($anchId)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $arrAnchUser = self::select('users.*')
            ->where('users.anchor_id', (int) $anchId)
            ->first();

        return ($arrAnchUser ?: false);
    }

    
    /**
     * Get User Details using anchor id
     *
     * @param  integer $anchId
     * @return array
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     * Since 0.1
     */
    public static function getUserByAnchorId($anchId)
    {
        //Check anchId is not blank
        if (empty($anchId)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        
        //Check anchId is not an integer

        // if (!is_int($anchId)) {
        //     throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        // }
        // dd($anchId);

        $arrAnchUser = self::select('users.*', 'user_app_doc.file_id')
            ->leftJoin('user_app_doc', function($join)
            {
                $join->on('users.user_id', '=', 'user_app_doc.user_id')
                ->where('user_app_doc.is_active', '=', 1)
                ->where('user_app_doc.file_type', '=', 1);
            })
            ->where('users.anchor_id', (int) $anchId)
            ->first();
        return ($arrAnchUser ?: false);
    }
    
    /**
     * Get Lead Sales Manager
     * 
     * @param integer $userId
     * @return mixed
     */
    public static function getLeadSalesManager($userId) {        
        
        $result = self::select('anchor.sales_user_id')
              ->join('anchor', 'anchor.anchor_id', '=', 'users.anchor_id')                       
              ->where('users.user_id', '=', $userId)
              ->first();
        return ($result ? $result->sales_user_id : null);        
    }
    
    /**
     * Get User Details using application id
     *
     * @param  integer $anchId
     * @return array
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     * Since 0.1
     */
    public static function getUserByAppId($appId){
        //Check anchId is not blank
        if (empty($appId)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        $arrUser = self::select('users.*')
              ->join('app', 'app.user_id', '=', 'users.user_id')
              ->where('app.app_id', '=', $appId)
              ->first();

        return ($arrUser ?: false);
    }

    public static function getBankData(){
        $result = self::select('*')
                ->from('mst_bank')
                ->where('is_active', '1')
                ->get();
        return ($result ?? null);
    }

    public function agency(){
        return $this->hasOne('App\Inv\Repositories\Models\Agency', 'agency_id', 'agency_id');
    }

    public function anchor(){
        return $this->hasOne('App\Inv\Repositories\Models\Anchor', 'anchor_id', 'anchor_id');
    }
   
     public function biz(){
        return $this->hasOne('App\Inv\Repositories\Models\Business', 'user_id', 'user_id');
    }
     public static function getProgramUser($user_id)
    {
         $appIds =    LmsUser::where('user_id',$user_id)->pluck('app_id');
         $proId =  AppProgramOffer::whereHas('productHas')->whereIn('app_id', $appIds)->where(['is_active' =>1,'is_approve' =>1,'status' =>1])->where('prgm_id','<>', null)->pluck('prgm_id');
         return Program::whereIn('prgm_id', $proId)->get();
      }
    
    
      public  function userApps()
    {
         return $this->hasMany('App\Inv\Repositories\Models\Application', 'user_id', 'user_id')->pluck('app_id');
    }
    
     public static function getUserDetails($uid)
     {
       
         return self::with('app')->where(['user_id' =>$uid])->first();
         
     }
     
         public  function app()
    {
         return $this->belongsTo('App\Inv\Repositories\Models\Application', 'user_id', 'user_id');

    }

    public function anchors(){
        return $this->hasMany('App\Inv\Repositories\Models\Anchor', 'anchor_id', 'anchor_id');
    }
    
    public function anchorUsers(){
        return $this->hasMany('App\Inv\Repositories\Models\AnchorUser', 'user_id', 'user_id');               
    }    

    /**
     * Get Backend Users
     * 
     * @return type
     */
    public static function getBackendUsers(){
        $result = self::select('*')
                ->from('users')
                ->where('user_type', '2')
                ->where('is_active', '1')
                ->get();
        return ($result ? $result : []);
    }

    /**
     * Get child users
     * 
     * @return type
     */
    public function children() { 
        return $this->hasMany('App\Inv\Repositories\Models\User', 'parent_id', 'user_id'); 
    }

    /**
     * 
     * @param type $parentUserId
     * @return type
     */
    public static function getChildUsers($parentUserId)
    {
        $result = self::where('parent_id', $parentUserId)
                ->where('is_active', 1)
                ->get();        
        return $result ? $result : [];
    }
    
    /**
     * Get Approval Authority Users
     *
     * @return mixed
     */
    public static function getApprAuthorityUsers()
    {
        $result = self::select('user_id')
                ->where('is_appr_required', 1)
                ->where('is_active', 1)
                ->get();        
        return $result ? $result : [];
    }
    //////// get single user behalf of user id    */
    public static function getSingleUserDetails($uid)
    {
        
        return self::where(['user_id' => $uid])->first();
    }

    public static function updateUserRolePassword($arrData, $userId)
    {

        if (!is_array($arrData)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        return self::where('user_id', $userId)->first()->update($arrData);
    }
    
    public function bank_details()
    {
        return $this->hasOne('App\Inv\Repositories\Models\UserBankAccount', 'user_id', 'user_id')->where(['is_active' => 1, 'is_default' => 1]);
    }

    public function anchor_bank_details()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\UserBankAccount', 'anchor_id', 'anchor_id')->where(['is_active' => 1, 'is_default' => 1]);
    }

    public function supplier_bank_detail()
    {
        return $this->hasOne('App\Inv\Repositories\Models\UserBankAccount', 'user_id', 'user_id')->where(['is_default' => 1, 'is_active' => 1]);  
     
    }
    public function lms_user()
    {
        return $this->hasOne('App\Inv\Repositories\Models\LmsUser', 'user_id','user_id'); 
    }
     
    public function userDetail()
    {
        return $this->hasOne('App\Inv\Repositories\Models\UserDetail', 'user_id', 'user_id');
    }

    /*
    * Get User Details base of user Id
    *
    * @param  integer $user_id
    * @return array
    * @throws BlankDataExceptions
    * @throws InvalidDataTypeExceptions
    * Since 0.1
    */
   public static function getfullSalesUserDetail($user_id)
   {
       //dd($user_id);
       //Check id is not blank

       if (empty($user_id)) {
           throw new BlankDataExceptions(trans('error_message.no_data_found'));
       }
       //Check id is not an integer

       if (!is_int($user_id)) {
           throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
       }

       $arrUser = self::select('users.*')
           ->where('users.user_id', (int) $user_id)
           ->where('users.user_type', 2)
           ->first();

       return ($arrUser ?: false);
   }

       /**
     * Update User status in agency user list 
     * 
     * @param type $attributes
     * @param type $conditions
     * @return type
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions 
     */
    public static function updateUserStatus($attributes = [], $conditions = [])
    {
        /**
         * Check Data is Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

       
        /**
         * Check Data is Array
         */
        if (!is_array($conditions)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        /**
         * Check Data is not blank
         */
        if (empty($conditions)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        $res = self::where($conditions)->update($attributes);

        return ($res ?: false);
    }

    public  function apps()
    {
        return $this->hasMany('App\Inv\Repositories\Models\Application', 'user_id', 'user_id');
    }

    // Frontend Dashboard
    public static function getSupplierDataById($supplierId) {
        $res = DB::select("SELECT supplier_id,COUNT(a.invoice_id) AS Total_Invoices,SUM(invoice_amount) AS Total_Invoice_Amount,
                      (SELECT COUNT(invoice_id) FROM rta_invoice c WHERE status_id IN (8,9,10) AND c.supplier_id=a.supplier_id ) AS Total_Approved_Invoice,
                      (SELECT SUM(invoice_approve_amount) FROM rta_invoice c WHERE status_id IN (8,9,10)    AND c.supplier_id=a.supplier_id) AS Total_Approved_Value,
                      (SELECT COUNT(invoice_id) FROM rta_invoice c WHERE status_id IN (9)    AND c.supplier_id=a.supplier_id ) AS Total_Invoice_InDisbursedProcess,
                      (SELECT SUM(invoice_approve_amount) - SUM(invoice_margin_amount) FROM rta_invoice c WHERE status_id IN (9)  AND c.supplier_id=a.supplier_id)
                      AS Total_Invoice_IDisbursedProcess_Value,
                      (SELECT COUNT(invoice_id) FROM rta_invoice c WHERE status_id  IN (12)    AND c.supplier_id=a.supplier_id ) AS Total_Disbursed_Invoice,
                      (SELECT SUM(invoice_approve_amount) FROM rta_invoice c WHERE status_id  IN (12)   AND c.supplier_id=a.supplier_id) AS Total_Disbursed_Value,
                      (SELECT COUNT(invoice_id) FROM rta_invoice c WHERE status_id  IN (15)    AND c.supplier_id=a.supplier_id ) AS Total_Invoice_Repaid,
                      (SELECT COUNT(invoice_id) FROM rta_invoice c WHERE status_id IN (7,11,14,28)    AND c.supplier_id=a.supplier_id ) AS Total_Invoice_Pending
                      FROM rta_invoice a
                      WHERE a.supplier_id = $supplierId;");
        return ($res ? $res : []);
    }   
    public function getFullNameAttribute(){ 
        return $this->f_name.' '.$this->m_name.' '.$this->l_name;
    }

    public static function getBackendUserByEmail($email)
    {
        return self::backendUser()
                    ->where('email', $email)
                    ->first();
    }

    public function getIsAccountCloseAttribute(){
        $cnt = LmsUsersLog::where('user_id',$this->user_id)->where('status_id',config('common.USER_STATUS.ACCOUNT_CLOSURE'))->count();
        return ($cnt > 0) ? TRUE : FALSE;
    }
    
    public static function getCustomerData($userId) {
        $query = self::select('biz.biz_entity_name','lms_users.customer_id')
            ->join('lms_users', 'users.user_id', '=', 'lms_users.user_id')
            ->join('biz', 'users.user_id', '=', 'biz.user_id');
        $query->where('users.user_id', $userId);
        $customers = $query->first();            
        return ($customers ? $customers : []);
    }
}