<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use Session;
use Auth;
use DB;
 

class FactPaymentEntry extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fact_payment_entry';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'fact_payment_entry_id';

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
        'voucher',
        'sr',
        'date',
        'description',
        'chq_/_ref_number',
        'dt_value',
        'fc_amount',
        'amount',
        'bank_code',
        'bank_name',
        'account_no',
        'payment_vendor_name',
        'paid_to_client',
        'code',
        'remarks',
        'type',
        'gL_code',
        'remark',
        'upload_status',
    ];

    /**
     * get Tally Data
     * 
     * @param type $where array
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    
}
