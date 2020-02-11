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
        'created_at',
        'created_by'
    ];

    public function user()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\User', 'user_id');
    }

    public static function updateVirtualId($lmsUserId, $virtualId)
    {
        return self::where('lms_user_id', $lmsUserId)
                    ->update(['virtual_acc_id' => $virtualId]);
    }

    public static function lmsGetDisbursalCustomer()
    {
        return self::with(['bank_details.bank', 'app.invoices'])
                ->whereHas('app.invoices');
    }

    public function bank_details()
    {
        return $this->hasOne('App\Inv\Repositories\Models\UserBankAccount', 'user_id', 'user_id')->where(['is_active' => 1]);
    }

    public function app()
    {
        return $this->hasMany('App\Inv\Repositories\Models\Application', 'user_id', 'user_id');
    }
}