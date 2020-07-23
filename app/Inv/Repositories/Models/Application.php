<?php

namespace App\Inv\Repositories\Models;

use DB;
use Helpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\BusinessAddress;
use App\Inv\Repositories\Models\Master\State;
use App\Inv\Repositories\Models\Master\RoleUser;
use App\Inv\Repositories\Models\Master\Role;
use App\Inv\Repositories\Models\Master\Company;
use App\Inv\Repositories\Factory\Models\BaseModel;
use Auth;

class Application extends BaseModel
{
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'app';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'app_id';

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
        'parent_app_id',
        'user_id',
        'biz_id',
        'loan_amt',
        'status',
        'is_assigned',
        'curr_status_id',
        'app_type',
        'renewal_status',
        'app_code',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    public function business()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\Business', 'biz_id');
    }
    
    
     /**
     * join with app limit table to get limit amount for application
     */
    public function appLimit()
    {
        return $this->hasOne('App\Inv\Repositories\Models\AppLimit', 'app_id')->where(['status' =>1]);
    }

     /**
     * join with app limit table to get limit amount for application
     */
    public function appPrgmOffer()
    {
        return $this->hasOne('App\Inv\Repositories\Models\AppProgramOffer', 'app_id')->where('is_active', 1);
    }

    public function acceptedOffer()
    {
        return $this->hasOne('App\Inv\Repositories\Models\AppProgramOffer', 'app_id')->where(['is_active' => 1, 'status' => 1])->whereHas('invPL');
    }

    public function prgmLimit()
    {
        return $this->hasOne('App\Inv\Repositories\Models\AppProgramLimit', 'app_id')->where(['product_id' => 1]);
    }
    
    function disbursal()
    {
        return $this->hasMany('App\Inv\Repositories\Models\Lms\Disbursal', 'user_id','user_id');
    }
    
    function transactions()
    {
        return $this->hasMany('App\Inv\Repositories\Models\Lms\Transactions', 'user_id','user_id')->where('trans_type', 17);
    }
    public function invoices()
    {
        return $this->hasMany('App\Inv\Repositories\Models\BizInvoice', 'app_id', 'app_id')->where('status_id', 9);
    }

    public function senttb_invoices()
    {
        return $this->hasMany('App\Inv\Repositories\Models\BizInvoice', 'app_id', 'app_id')->where('status_id', 10);
    }
    /**
     * Get Applications for Application list data tables
     */
    protected static function getApplications() 
    {
        $roleData = User::getBackendUser(\Auth::user()->user_id);
        $curUserId = \Auth::user()->user_id;
        $userArr = Helpers::getChildUsersWithParent($curUserId);
        $query = self::select('app.user_id','app.app_id', 'app.app_code' ,'app.curr_status_id', 'biz.biz_entity_name', 'biz.biz_id', 
                'app.status','app_assign.to_id', 'users.anchor_id', 'users.is_buyer as user_type', 'app.renewal_status',
                DB::raw("CONCAT_WS(' ', rta_users.f_name, rta_users.l_name) AS assoc_anchor"),
                DB::raw("CONCAT_WS(' ', rta_assignee_u.f_name, rta_assignee_u.l_name) AS assignee"), 
                DB::raw("CONCAT_WS(' ', rta_from_u.f_name, rta_from_u.l_name) AS assigned_by"),                
                DB::raw("CONCAT_WS(' ', rta_users.f_name, rta_users.l_name) AS name"),
                'users.email',
                'users.mobile_no',                
                'app_assign.sharing_comment', 'assignee_r.name as assignee_role', 'from_r.name as from_role',
                'app_assign.app_assign_id', 'app.parent_app_id', 'note.note_data as reason', 'note.note_id','anchor_user.pan_no')
                ->join('users', 'users.user_id', '=', 'app.user_id')  
                ->join('biz', 'app.biz_id', '=', 'biz.biz_id')
                ->leftjoin('note','app.app_id', '=', 'note.app_id');
        if ($roleData[0]->id == 11) {            
        $query  = $query->leftJoin('app_assign', function ($join) use($roleData, $curUserId, $userArr) {
                    $join->on('app.app_id', '=', 'app_assign.app_id');                    
                })
                ->leftJoin('users as assignee_u', 'app_assign.to_id', '=', 'assignee_u.user_id')           
                ->leftJoin('users as from_u', 'app_assign.from_id', '=', 'from_u.user_id')
                ->leftJoin('role_user as assignee_ru', 'app_assign.to_id', '=', 'assignee_ru.user_id')
                ->leftJoin('roles as assignee_r', 'assignee_ru.role_id', '=', 'assignee_r.id')
                ->leftJoin('role_user as from_ru', 'app_assign.from_id', '=', 'from_ru.user_id')
                ->leftJoin('roles as from_r', 'from_ru.role_id', '=', 'from_r.id')
                ->leftJoin('anchor_user', 'anchor_user.user_id', '=', 'app.user_id');
                
                $query->where('anchor_user.anchor_id', \Auth::user()->anchor_id);            
        } else {
        $query  = $query->join('app_assign', function ($join) use($roleData, $curUserId, $userArr) {
                    $join->on('app.app_id', '=', 'app_assign.app_id');
                    if ($roleData[0]->is_superadmin != 1) {
                        //$join->on('app_assign.to_id', '=', DB::raw($curUserId));
                        $join->whereIn('app_assign.to_id', $userArr);
                        
                    } else {
                        $join->on('app_assign.is_owner', '=', DB::raw("1"));
                        $join->whereNotNull('app_assign.to_id');
                    }
                })
                ->join('users as assignee_u', 'app_assign.to_id', '=', 'assignee_u.user_id')           
                ->join('users as from_u', 'app_assign.from_id', '=', 'from_u.user_id')
                ->join('role_user as assignee_ru', 'app_assign.to_id', '=', 'assignee_ru.user_id')
                ->join('roles as assignee_r', 'assignee_ru.role_id', '=', 'assignee_r.id')
                ->leftJoin('role_user as from_ru', 'app_assign.from_id', '=', 'from_ru.user_id')
                ->leftJoin('roles as from_r', 'from_ru.role_id', '=', 'from_r.id')    
                ->leftJoin('anchor_user', 'anchor_user.user_id', '=', 'app.user_id');
        }
                //$query->where('users.anchor_user_id', \Auth::user()->user_id);            
                //$query->where('users.anchor_id', \Auth::user()->anchor_id);              
        $query->groupBy('app.app_id');
        $appData = $query->orderBy('app.app_id', 'DESC');
        return $appData;
    }
   
    
    public static function getApplicationsDetail($user_id)
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
               
        
        $appData = self::select('app.*','biz.biz_entity_name')
                ->leftjoin('biz', 'app.biz_id', '=', 'biz.biz_id')
                ->where('app.user_id', $user_id)->get();
                       
        return ($appData?$appData:null);
        
    }
    
    public static function getSingleAnchorDataByAppId($appId)
    {
      return  self::where('app_id',$appId)->first();
        
    } 
    /**
     * Get Applications for Application list data tables
     */
    public static function getApplicationPoolData() 
    {
        
        $roleData = User::getBackendUser(\Auth::user()->user_id);
        $appData = self::distinct()->select('app.app_id','app.biz_id','app.user_id','biz.biz_entity_name', 'app.status',
                'users.is_buyer as user_type', DB::raw("CONCAT_WS(' ', rta_users.f_name, rta_users.l_name) AS assoc_anchor"),
                'assignee_r.name AS assignee', 
                DB::raw("CONCAT_WS(' ', rta_from_u.f_name, rta_from_u.l_name) AS assigned_by"),
                DB::raw("CONCAT_WS(' ', rta_users.f_name, rta_users.l_name) AS name"),
                'users.email',
                'users.mobile_no',                   
                'app_assign.sharing_comment')                 
                ->join('users', 'users.user_id', '=', 'app.user_id')  
                ->join('biz', 'app.biz_id', '=', 'biz.biz_id')
                //->leftJoin('anchor_user', 'app.user_id', '=', 'anchor_user.user_id')
                
                ->leftJoin('app_assign', function ($join) {
                    $join->on('app.app_id', '=', 'app_assign.app_id');
                    $join->on('app_assign.is_owner', '=', DB::raw("1"));                    
                })                
                ->leftJoin('roles as assignee_r', 'app_assign.role_id', '=', 'assignee_r.id')
                ->leftJoin('users as from_u', 'app_assign.from_id', '=', 'from_u.user_id');
        if ($roleData[0]->is_superadmin != 1) {
            $appData->where('app_assign.role_id', $roleData[0]->id);
        }
        $appData->whereNull('app_assign.to_id');
        //$appData->groupBy('app.app_id');
        $appData = $appData->orderBy('app.app_id', 'DESC');
        
        return $appData;
    } 
    /**
     * update application details
     *
     * @param integer $user_id     user id
     * @param array   $arrUserData user data
     *
     * @return boolean
     */

    
     public static function updateAppDetails($app_id, $arrUserData = [])
    {
         $app_id = (int)$app_id;
        /**
         * Check id is not blank
         */
        if (empty($app_id)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($app_id)) {
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

        $rowUpdate = self::find((int) $app_id)->update($arrUserData);

        return ($rowUpdate ? $rowUpdate : false);
    }
    
    /**
     * Get Application Data By Biz Id
     * 
     * @param integer $biz_id
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public static function getAppDataByBizId($biz_id)
    {
        /**
         * Check id is not blank
         */
        if (empty($biz_id)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($biz_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
               
        
        $appData = self::select('app.app_id')
                ->where('app.biz_id', $biz_id)->first();
                       
        return ($appData ? $appData : null);        
    }

    
    /**
     * Update Application Data By application Id
     * 
     * @param integer $app_id
     * @param array $arrData
     *
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public static function updateAppData($app_id, $arrData=[])
    {
        /**
         * Check id is not blank
         */
        if (empty($app_id)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($app_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
               
        
        $appData = self::where('app_id', $app_id)->update($arrData);                
                       
        return ($appData ? $appData : false);
    }    
    
    /**
     * Get Application Data By App Id
     * 
     * @param integer $app_id
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public static function getAppData($app_id)
    {
        /**
         * Check id is not blank
         */
        if (empty($app_id)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($app_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
               
        
        $appData = self::select('app.*')
                ->where('app.app_id', $app_id)->first();
                       
        return ($appData ? $appData : null);        
    }    
    
    /**
     * Get Latest application
     * 
     * @param integer $user_id
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public static function getLatestApp($user_id)
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
               
        
        $appData = self::select('app.*')
                ->where('app.user_id', $user_id)
                ->where('app.status', '0')
                ->orderBy('app.app_id', 'DESC')
                ->first();
                       
        return ($appData ? $appData : null);        
    }

    /**
     * Get User Applications for Application list data tables
     */
    protected static function getUserApplications() 
    {  
        $usersIds = AnchorUser::getUsersByPan(\Auth::user()->user_id);        
        $appData = self::distinct()->select('app.user_id','app.app_id','app_product.loan_amount', 'users.f_name', 'users.m_name', 'users.l_name', 'users.email', 'users.mobile_no', 'biz.biz_entity_name', 'biz.biz_id', 'app.status', 'users.anchor_id', 'users.is_buyer as user_type', 'app.created_at', 'anchor_user.pan_no')
                ->join('biz', 'app.biz_id', '=', 'biz.biz_id')
                ->join('users', 'app.user_id', '=', 'users.user_id')
                ->join('app_product', 'app_product.app_id', '=', 'app.app_id')
                ->leftJoin('anchor_user', 'anchor_user.user_id', '=', 'app.user_id');
                //->join(DB::raw('(SELECT rta_anchor_user.pan_no FROM rta_anchor_user WHERE user_id = ?) AS rta_a'), function( $join ) {
                //    $join->on( 'anchor_user.pan_no', '=', 'a.pan_no' );
                //})
                //->setBindings([\Auth::user()->user_id]);
        if (count($usersIds) > 0) {
            $appData->whereIn('app.user_id', $usersIds);
        } else {
            $appData->where('app.user_id', \Auth::user()->user_id);
        }
        //$appData->groupBy('app.app_id');
        $appData = $appData->orderBy('app.app_id', 'DESC');
        return $appData;
    }

    protected static function getAgencyApplications() 
    {  
        $roleData = User::getBackendUser(\Auth::user()->user_id);
        $appData = self::distinct()
                
                // ->whereHas('address.activeFiAddress')->orWhereHas('rcuDocument')

                ->select('app.user_id','app.app_id', 'app.app_code', 'app.loan_amt', 'users.agency_id', 'users.f_name', 'users.m_name', 'users.l_name', 'users.email', 'users.mobile_no', 'biz.biz_entity_name', 'biz.biz_id', 'app.status', 'users.anchor_id', 'users.is_buyer as user_type', 'app.created_at')
                ->join('biz', 'app.biz_id', '=', 'biz.biz_id')
                ->join('users', 'app.user_id', '=', 'users.user_id')
                ->join('biz_addr', 'biz.biz_id', '=', 'biz_addr.biz_id')
                ->join('fi_addr', 'fi_addr.biz_addr_id', '=', 'biz_addr.biz_addr_id')
                // ->join('rcu_doc', 'app.app_id', '=', 'rcu_doc.app_id')
                ->where(['biz_addr.is_active' => 1, 'fi_addr.agency_id' => \Auth::user()->agency_id]);
                // ->orWhere(['rcu_doc.is_active' => 1, 'rcu_doc.agency_id' => \Auth::user()->agency_id])
        //$appData->groupBy('app.app_id');
        $appDataAdmin = self::distinct()->select('app.user_id','app.app_id', 'app.app_code', 'app.loan_amt', 'users.agency_id', 'users.f_name', 'users.m_name', 'users.l_name', 'users.email', 'users.mobile_no', 'biz.biz_entity_name', 'biz.biz_id', 'app.status', 'users.anchor_id', 'users.is_buyer as user_type', 'app.created_at')
                ->join('biz', 'app.biz_id', '=', 'biz.biz_id')
                ->join('users', 'app.user_id', '=', 'users.user_id');
                //->where('users.agency_id', \Auth::user()->agency_id);
        //$appData->groupBy('app.app_id');

        if ($roleData[0]->is_superadmin != 1) {
            return $appData->orderBy('app.app_id', 'DESC');
        } else {
            return $appDataAdmin->orderBy('app.app_id', 'DESC');;
        }
        // $appDa = $appData->orderBy('app.app_id', 'DESC');
        // return $appDa;
    }

    public function address(){
        return $this->hasMany('App\Inv\Repositories\Models\BusinessAddress','biz_id','biz_id');
    }

    public function rcuDocument(){
        return $this->hasMany('App\Inv\Repositories\Models\RcuDocument','app_id','app_id')->where(['is_active' => 1, 'agency_id' => \Auth::user()->agency_id]);
    }

    /**
     * Count total numbers of applications
     * 
     * @param integer $user_id
     * @return integer
     */
    public static function getAllAppsByUserId($user_id)
    {
        $appData = self::select('app.*')
                ->where('app.user_id', $user_id)
                ->get();
        return $appData ? $appData : [];
    }

    public function bizPanGst(){
      return $this->hasMany('App\Inv\Repositories\Models\BizPanGst', 'biz_id','biz_id');    
   }
    
    public  function user(){
        return $this->hasOne('App\Inv\Repositories\Models\User','user_id','user_id');  
    }
     
     
    /**
     * Get Anchor Data By Application Id
     * 
     * @param integer $app_id
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public static function getAnchorDataByAppId($app_id)
    {
        /**
         * Check id is not blank
         */
        if (empty($app_id)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_int($app_id)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        $appData = self::select('app.*','users.*')  //,'anchor_user.*', 'anchor.*'
                //->join('anchor_user', 'anchor_user.user_id', '=', 'app.user_id')
                //->join('anchor', 'anchor.anchor_id', '=', 'anchor_user.anchor_id')
                ->join('users', 'users.user_id', '=', 'app.user_id')
                ->where('app.app_id', $app_id)                
                ->first();
                       
        return ($appData ? $appData : null);             
    }

    /**
     * Get DoA Users By $appId
     * 
     * @param type $appId
     */
    public static function getDoAUsersByAppId($appId)
    {
        
        $doaUsers = self::select('users.user_id','users.f_name','users.l_name','mst_product.product_name','app_prgm_offer.prgm_limit_amt')
                ->join('app_prgm_offer', 'app_prgm_offer.app_id', '=', 'app.app_id')
                ->join('app_prgm_limit', 'app_prgm_limit.app_prgm_limit_id', '=', 'app_prgm_offer.app_prgm_limit_id')
                //->join('prgm_doa_level', 'prgm_doa_level.prgm_id', '=', 'app_prgm_limit.prgm_id')                
               
                ->join('doa_level', function ($join) {
                    //$join->on('doa_level.doa_level_id', '=', 'prgm_doa_level.doa_level_id');
                    $join->on('app_prgm_offer.prgm_limit_amt', '>=', 'doa_level.min_amount');
                    $join->on('app_prgm_offer.prgm_limit_amt', '<=', 'doa_level.max_amount');
                })
                  ->join('mst_product', 'mst_product.id', '=', 'doa_level.product_id')
                ->join('app_product', function ($join) {
                    $join->on('app_product.product_id', '=', 'doa_level.product_id');
                    $join->on('app_product.app_id', '=', 'app.app_id');
                 }) 
                 
                ->join('doa_level_role', 'doa_level_role.doa_level_id', '=', 'doa_level.doa_level_id')
                //->join('role_user', 'role_user.role_id', '=', 'doa_level_role.role_id')
                        
                ->join('doa_level_states', 'doa_level_states.doa_level_id', '=', 'doa_level.doa_level_id')
                ->join('users', function ($join) {
                    //$join->on('role_user.user_id', '=', 'users.user_id');
                    //$join->on('doa_level.city_id', '=', 'users.city_id');
                    $join->on('doa_level_role.user_id', '=', 'users.user_id');
                    $join->on('doa_level_states.city_id', '=', 'users.city_id');
                })
                ->where('app.app_id', $appId)
                ->where('app_prgm_offer.is_active', 1)
                ->where('doa_level.is_active', 1)
                ->where('users.is_active', 1)        
                ->groupBy('users.user_id')
                ->get();
                       
        return ($doaUsers ? $doaUsers : []);
    }
    
    /**
     * Get Program Documents
     * 
     * @param array $whereCondition
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getProgramDocs($whereCondition = [])
    {
        //Check $whereCondition is not an array
        if (!is_array($whereCondition)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        $whereCondition['app.app_id']  = $whereCondition['app_id'];
        $whereCondition['wf_stage.stage_code']  = $whereCondition['stage_code'];
        $whereCondition['prgm_doc.is_active'] = isset($whereCondition['is_active']) ? $whereCondition['is_active'] : 1;
        $whereCondition['app_prgm_offer.is_active'] = 1;
        $whereCondition['app_prgm_offer.is_approve'] = 1;
        $whereCondition['mst_doc.is_active'] = 1;
        
        unset($whereCondition['app_id']);
        unset($whereCondition['stage_code']);
        unset($whereCondition['is_active']);
        
        $prgmDocs = self::select('prgm_doc.doc_id')
                ->join('app_prgm_offer', 'app_prgm_offer.app_id', '=', 'app.app_id')
                //->join('app_prgm_limit', 'app_prgm_limit.app_prgm_limit_id', '=', 'app_prgm_offer.app_prgm_limit_id')
                ->join('prgm_doc', 'prgm_doc.prgm_id', '=', 'app_prgm_offer.prgm_id')
                ->join('mst_doc', 'mst_doc.id', '=', 'prgm_doc.doc_id')
                ->join('wf_stage', 'prgm_doc.wf_stage_id', '=', 'wf_stage.wf_stage_id')
                ->where($whereCondition)
                ->orderBy('prgm_doc.doc_id')
                ->groupBy('prgm_doc.doc_id')
                ->get();
        return $prgmDocs;
    }

    public function products(){
        return $this->belongsToMany('App\Inv\Repositories\Models\Master\Product', 'app_product', 'app_id')->withPivot('loan_amount', 'tenor_days');;
    }

    /**
     * Get Updated application BusinessAddress
     * 
     * @param integer $user_id
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public static function getUpdatedApp($user_id)
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
               
        
        $appData = self::select('app.*')
                ->where('app.user_id', $user_id)                
                ->orderBy('app.app_id', 'DESC')
                ->first();
        return ($appData ? $appData : null);        
    } 

    public static function getAppDataByOrder($where , $orderBy = 'DESC')
    {
        /**
         * Check id is not blank
         */
        if (empty($where)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        /**
         * Check id is not an integer
         */
        if (!is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $result = self::where($where)->orderBy('app_id', $orderBy)->get();
        return $result ?: false;
    }

    /* get address  */
    public static function getUserAddress($app_id)
    {
        
        $biz_id =  self::where(['app_id' => $app_id])->pluck('biz_id');
        return  BusinessAddress::whereIn('biz_id',$biz_id)->where(['address_type' => 0])->pluck('state_id')->first(); 
        
    }
    
    public static  function companyAdress()
    {
        return  Company::where(['company_id' => 1,'is_active' =>1])->pluck('state')->first(); 
    }

    public function prgmLimits()
    {
        return $this->hasMany('App\Inv\Repositories\Models\AppProgramLimit', 'app_id');
    }

    public function acceptedOffers()
    {
        return $this->hasMany('App\Inv\Repositories\Models\AppProgramOffer', 'app_id')->where(['is_active' => 1, 'status' => 1]);
    }   

    protected static function getColenderApplications() 
    {  
        $appData = self::distinct()->whereHas('colender')->select('app.user_id','app.app_id','app.loan_amt', 'users.co_lender_id', 'users.f_name', 'users.m_name', 'users.l_name', 'users.email', 'users.mobile_no', 'biz.biz_entity_name', 'biz.biz_id', 'app.status', 'users.anchor_id', 'users.is_buyer as user_type', 'app.created_at')
                ->join('biz', 'app.biz_id', '=', 'biz.biz_id')
                ->join('users', 'app.user_id', '=', 'users.user_id');
                //->where('users.agency_id', \Auth::user()->agency_id);
        //$appData->groupBy('app.app_id');
        $appData = $appData->orderBy('app.app_id', 'DESC');
        return $appData;
    }

    public function colender(){
        return $this->hasOne('App\Inv\Repositories\Models\ColenderShare','app_id','app_id')->where(['is_active' => 1, 'co_lender_id' => \Auth::user()->co_lender_id]);
    }        
    
    public static function getUserBehalfApplication($attr)
    { 
       
        return  User::where(['anchor_id' => $attr['anchor_id']])->get();
    }  
    public static function getLmsUserBehalfApplication($attr)
    { 
        $id = Auth::user()->user_id;
        return  User::where(['user_id' =>$id,'anchor_id' => $attr['anchor_id']])->get();
    } 
    public static function chkUser() 
    {
       $id = Auth::user()->user_id;
       $role_id = RoleUser::where(['user_id' => $id])->pluck('role_id');
       return Role::whereIn('id',$role_id)->first();
    }
    
    /**
     * Get Renewal applications
     * 
     * @return mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public static function getRenewalApp()
    {
        $today = \Carbon\Carbon::now();
        $curDate = $today->toDateString();
        $startDate = $today->addDays(7)->toDateString();
        $endDate = $today->addDays(10)->toDateString();
        $appData = self::select('app.*','biz.biz_entity_name', 'app_limit.start_date', 'app_limit.end_date')
                ->join('biz', 'app.biz_id', '=', 'biz.biz_id')                
                ->join('app_product', 'app_product.app_id', '=', 'app.app_id')
                ->join('app_limit', 'app_limit.app_id', '=', 'app.app_id')
                ->whereIn('app.status', [2])
                ->where('app_limit.status', '1')
                ->where('app_product.product_id', '1')
                //->where('app_limit.start_date', '>=', $currDate)
                ->where('app_limit.end_date', '=', $startDate)
                //->whereBetween('app_limit.end_date', [$startDate, $endDate])
                ->groupBy('app.app_id')
                ->orderBy('app.app_id', 'DESC')
                ->get();
        
        return ($appData ? $appData : []);        
    }

    /**
     * Get Applications Data
     * 
     * @param array $where
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getApplicationsData($where=[])
    {
        /**
         * $where is not an array
         */
        if (!is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        $query = self::select('*');        
       
        if (isset($where['user_id'])) {
            $query->where('user_id', $where['user_id']);            
        }
        
        if (isset($where['status']) && is_array($where['status'])) {
            $query->whereIn('status', $where['status']);            
        }
        
        $result = $query->get();       
        return $result ? $result: [];
    }

    public static function getAllRenewalApps()
    {
        $today = \Carbon\Carbon::now();
        $currDate = $today->toDateString();
        
        $roleData = User::getBackendUser(\Auth::user()->user_id);
        $curUserId = \Auth::user()->user_id;
        $userArr = Helpers::getChildUsersWithParent($curUserId);
        $appData = self::select('app.user_id','app.app_id','app.curr_status_id', 'biz.biz_entity_name', 'biz.biz_id', 
                'app.status','app_assign.to_id', 'users.anchor_id', 'users.is_buyer as user_type', 'app.renewal_status',
                DB::raw("CONCAT_WS(' ', rta_users.f_name, rta_users.l_name) AS assoc_anchor"),
                DB::raw("CONCAT_WS(' ', rta_assignee_u.f_name, rta_assignee_u.l_name) AS assignee"), 
                DB::raw("CONCAT_WS(' ', rta_from_u.f_name, rta_from_u.l_name) AS assigned_by"),                
                DB::raw("CONCAT_WS(' ', rta_users.f_name, rta_users.l_name) AS name"),
                'users.email',
                'users.mobile_no',                
                'app_assign.sharing_comment', 'assignee_r.name as assignee_role', 'from_r.name as from_role',
                'app_assign.app_assign_id')
                ->join('users', 'users.user_id', '=', 'app.user_id')  
                ->join('biz', 'app.biz_id', '=', 'biz.biz_id')                                               
                ->join('app_assign', function ($join) use($roleData, $curUserId, $userArr) {
                    $join->on('app.app_id', '=', 'app_assign.app_id');
                    if ($roleData[0]->is_superadmin != 1) {
                        //$join->on('app_assign.to_id', '=', DB::raw($curUserId));
                        $join->whereIn('app_assign.to_id', $userArr);
                        
                    } else {
                        $join->on('app_assign.is_owner', '=', DB::raw("1"));
                        $join->whereNotNull('app_assign.to_id');
                    }
                })
                ->join('users as assignee_u', 'app_assign.to_id', '=', 'assignee_u.user_id')             
                ->join('users as from_u', 'app_assign.from_id', '=', 'from_u.user_id')
                ->join('role_user as assignee_ru', 'app_assign.to_id', '=', 'assignee_ru.user_id')
                ->join('roles as assignee_r', 'assignee_ru.role_id', '=', 'assignee_r.id')
                ->join('app_limit', 'app_limit.app_id', '=', 'app.app_id')                     
                ->leftJoin('role_user as from_ru', 'app_assign.from_id', '=', 'from_ru.user_id')
                ->leftJoin('roles as from_r', 'from_ru.role_id', '=', 'from_r.id')
                ->whereIn('app.renewal_status', [1])
                ->where('app_limit.status', '1');
                        
                //->where('app_limit.start_date', '>=', $currDate)
                //->where('app_limit.end_date', '<=', $currDate)
                        
        if ($roleData[0]->id == 11) {            
                //$appData->where('users.anchor_user_id', \Auth::user()->user_id);            
                $appData->where('users.anchor_id', \Auth::user()->anchor_id);            
        }

        $appData->groupBy('app.app_id');
        $appData = $appData->orderBy('app.app_id', 'DESC');  
                       
        return ($appData ? $appData : []);        
    }
    
     public static function getSentionUserDetails($uid)
     {
       
         return self::where(['user_id' =>$uid,'status' => 2])->first();
         
     }
     
     public static function checkAppByPan($userId)
     {
        $appData = self::select('app.*')
                ->leftJoin('anchor_user', 'anchor_user.user_id', '=', 'app.user_id') 
                ->join(DB::raw('(SELECT rta_anchor_user.pan_no FROM rta_anchor_user WHERE user_id = ?) AS rta_a'), function( $join ) {
                    $join->on( 'anchor_user.pan_no', '=', 'a.pan_no' );
                })                                
                ->setBindings([$userId])
                ->whereIn('app.status', [0,1])
                ->first();
                       
        return ($appData && $appData->app_id ? true : false);    
     }

     public static function getSanctionApp() 
     {
        $roleData = User::getBackendUser(\Auth::user()->user_id);
        if (isset($roleData[0]) && $roleData[0]->id == 11) {
            $getAppId  = self::select('app.*')
                    ->join('anchor_user', 'anchor_user.user_id', '=', 'app.user_id')
                    ->where(['app.status' => 2])
                    ->where(['anchor_user.anchor_id' => \Auth::user()->anchor_id])
                    ->pluck('app.app_id');
        } else {
            $getAppId  = self::select('app.*')
                    ->where(['status' => 2])
                    ->pluck('app_id');            
        }
        return $getAppId;
     }
     
    /**
     * Get User Applications for Application list data tables
     */
    protected static function getApplicationsByPan($userId) 
    {  
        $roleData = User::getBackendUser(\Auth::user()->user_id);
        if (isset($roleData[0]) && $roleData[0]->id == 11) {            
            $usersIds = AnchorUser::getUsersByPan($userId, \Auth::user()->anchor_id);
        } else {
            $usersIds = AnchorUser::getUsersByPan($userId);
        }
        
        $appData = self::distinct()->select('app.user_id','app.app_id','app_product.loan_amount', 'users.f_name', 'users.m_name', 'users.l_name', 'users.email', 'users.mobile_no', 'biz.biz_entity_name', 'biz.biz_id', 'app.status', 'users.anchor_id', 'users.is_buyer as user_type', 'app.created_at','anchor_user.pan_no')
                ->join('biz', 'app.biz_id', '=', 'biz.biz_id')
                ->join('users', 'app.user_id', '=', 'users.user_id')
                ->join('app_product', 'app_product.app_id', '=', 'app.app_id')
                ->leftJoin('anchor_user', 'anchor_user.user_id', '=', 'app.user_id');
                //->join(DB::raw('(SELECT rta_anchor_user.pan_no FROM rta_anchor_user WHERE user_id = ?) AS rta_a'), function( $join ) {
                //    $join->on( 'anchor_user.pan_no', '=', 'a.pan_no' );
                //})
                //->setBindings([$userId]);
        if (count($usersIds) > 0) {
            $appData->whereIn('app.user_id', $usersIds);
        } else {
            $appData->where('app.user_id', $userId);
        }
        //$appData->groupBy('app.app_id');
        $appData = $appData->orderBy('app.app_id', 'DESC')->get();
        return $appData ? $appData : [];
    }     
}