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



class Disbursal extends Authenticatable
{
     use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'disbursal';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'disbursal_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'app_id',
        'invoice_id',
        'prgm_offer_id',
        'disburse_date',
        'bank_id',
        'bank_name',
        'ifsc_code',
        'acc_no',
        'virtual_acc_id',
        'customer_id',
        'principal_amount',
        'inv_due_date',
        'tenor_days',
        'interest_rate',
        'total_interest',
        'margin',
        'disburse_amount',
        'total_repaid_amt',
        'status',
        'settlement_date',
        'accured_interest',
        'interest_refund',
        'funded_date',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\User', 'user_id');
    }
}