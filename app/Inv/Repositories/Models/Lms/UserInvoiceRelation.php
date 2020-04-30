<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\Master\State;

class UserInvoiceRelation extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'user_invoice_rel';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'user_invoice_rel_id';

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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_invoice_rel_id',
        'user_id',
        'company_id',
        'biz_addr_id',
        'company_state_id',
        'biz_addr_state_id',
        'is_active',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    /**
     * Save Invoices
     * 
     * @param array $invoices
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */

    public static function saveUserInvoiceLocation($userInvoiceData) {
        $data = self::create($userInvoiceData);
        return $data ? : false;
    }

    public static function unPublishAddr(int $userId) {
        $data = self::where('user_id', $userId)
            ->update(['is_active' => 2, 'updated_at' => \carbon\Carbon::now(), 'updated_by' => Auth::user()->user_id]);
        return $data;
    }

    public static function checkUserInvoiceLocation($userInvData) {
        $data = self::where($userInvData)->first();
        return $data ? : false;
    }


    public static function getUserCurrCompany(int $user_id) {
        return self:: where(['user_id' => $user_id, 'is_active' => 1])->first();
    }


    public static function getCustAndCapsLoc($user_id, $appId = null) {
       $result = self::where(['user_id' => $user_id])->orderBy('user_invoice_rel_id' , 'DESC');
        return $result ? : false;
    }

    public function userBizAddr() {
       return $this->belongsTo('App\Inv\Repositories\Models\BusinessAddress', 'biz_addr_id', 'biz_addr_id');
   }
   
   public function capsavBizAddr() {
       return $this->belongsTo('App\Inv\Repositories\Models\Master\Company', 'company_id', 'comp_addr_id');
   }


}
