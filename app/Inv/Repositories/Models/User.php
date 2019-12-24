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
        'anchor_user_id',
        'f_name',
        'm_name',
        'l_name',
        'biz_name',
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
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
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
        //dd($user_id);
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
                'users.is_buyer as AnchUserType','lead_assign.to_id')                
                ->join('lead_assign', function ($join) {
                    $join->on('lead_assign.assigned_user_id', '=', 'users.user_id');
                    $join->on('lead_assign.is_owner', '=', DB::raw("1"));                    
                })                                  
                 ->where('users.user_type', 1);
        if ($roleData[0]->id == 11) {
            $result->where('users.anchor_id', \Auth::user()->anchor_id);                        
        } else if ($roleData[0]->is_superadmin != 1) {
            //$result->where('lead_assign.to_id', \Auth::user()->user_id);
            $result->whereIn('lead_assign.to_id', $userArr);            
        }
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

        if (!is_int($anchId)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $arrAnchUser = self::select('users.*')
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
    
}