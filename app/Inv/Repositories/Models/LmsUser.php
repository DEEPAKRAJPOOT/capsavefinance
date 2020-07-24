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
use App\Inv\Repositories\Models\UserDetail;

use Auth;



class LmsUser extends Authenticatable
{
     use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lms_users';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'lms_user_id';
    /**
     * Maintain created_at and updated_at automatically
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Maintain created_by and updated_by automatically
     *
     * @var boolean
     */
    public $userstamps = false;
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
        'customer_id',
        'app_id',
        'virtual_acc_id',
        'created_at',
        'created_by'
    ];
  
    public static  function checkLmsUser()
    {
        $id = Auth::user()->user_id;
        return self::where(['user_id' => $id])->first();
    }
    public static function getCustomers($search){
        return $data = self::select('customer_id','virtual_acc_id','users.user_id', DB::raw("CONCAT_WS(' ', rta_users.f_name, rta_users.m_name, rta_users.l_name) AS customer"),'biz.biz_entity_name','biz.biz_id' )
            ->join('users', 'lms_users.user_id', '=', 'users.user_id')
            ->join('biz','lms_users.user_id', '=', 'biz.user_id')
            ->where('biz_entity_name', 'like', '%'.$search.'%')
            ->orwhere("customer_id","LIKE","%{$search}%")->get();
    }
      /////////////* get customer id   */////////////////
      public static function  getLmsUser()
      {
           $result= self::with('user')->groupBy('user_id')->get();
           return $result?$result:false;
      }

    public function user()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\User', 'user_id');
    }
    
    public static function getCustomerUser($customer_id)
    {
        return self::with(['bizApp'])->where(['customer_id' => $customer_id ])->first();
    }

     public function bizApp()
    {
         return $this->hasOne('App\Inv\Repositories\Models\Application', 'user_id', 'user_id');
    }
    
    public static function updateVirtualId($lmsUserId, $virtualId)
    {
        return self::where('lms_user_id', $lmsUserId)
                    ->update(['virtual_acc_id' => $virtualId]);
    }

    public static function lmsGetDisbursalCustomer($userIds = [])
    {
        return self::with(['bank_details.bank', 'app.invoices.program_offer', 'user.anchor_bank_details.bank'])
                ->whereHas('app')
                ->whereHas('app.invoices', function($query) use ($userIds) {
                    if (!empty($userIds)) {
                        $query->whereIn('supplier_id', $userIds);
                    }
                })
                ->get();
    }

    public static function lmsGetSentToBankInvCustomer($userIds = [])
    {
        return self::with(['bank_details.bank', 'senttb_app.senttb_invoices.program_offer', 'user.anchor_bank_details.bank'])
                ->whereHas('senttb_app')
                ->whereHas('senttb_app.senttb_invoices', function($query) use ($userIds) {
                    if (!empty($userIds)) {
                        $query->whereIn('supplier_id', $userIds);
                    }
                })
                ->whereHas('senttb_app.senttb_invoices.invoice_disbursed.disbursal', function($query) {
                        $query->where('disburse_type', 2);
                });
    }

    public function bank_details()
    {
        return $this->hasOne('App\Inv\Repositories\Models\UserBankAccount', 'user_id', 'user_id')->where(['is_active' => 1, 'is_default' => 1]);
    }

    public function app()
    {
        return $this->hasMany('App\Inv\Repositories\Models\Application', 'user_id', 'user_id')->whereHas('invoices');
    }

    public function senttb_app()
    {
        return $this->hasMany('App\Inv\Repositories\Models\Application', 'user_id', 'user_id');
    }

    public function transaction()
    {
        return $this->hasMany('App\Inv\Repositories\Models\Lms\Transactions', 'user_id', 'user_id');
    }

    public function disbursal()
    {
        return $this->hasMany('App\Inv\Repositories\Models\Lms\Disbursal', 'user_id', 'user_id');
    }
    
    public static function getLmsUsers($whereCond=[])
    {
        $query = self::select('*');
        if (count($whereCond) > 0) {
            $query->where($whereCond);
        }
        $result = $query->get();
        return $result;
    }
    
    public static function getAccountClosure()
    {
         $id = Auth::user()->user_id;
        return UserDetail::where(['user_id' => $id,'is_active' =>1])->count();
    }
    
     public static function getAccountActiveClosure($uid)
    {
       
        return self::where(['user_id' => $uid,'is_active' =>1])->count();
    }
    
}