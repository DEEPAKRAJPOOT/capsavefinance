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

class UserInvoice extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'user_invoice';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'user_invoice_id';

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
        'invoice_user_id',
        'app_id',
        'pan_no',
        'biz_gst_no',
        'gst_addr',
        'reference_no',
        'invoice_type',
        'invoice_no',
        'invoice_date',
        'invoice_state_code',
        'place_of_supply',
        'comp_id',
        'bank_id',
        'tot_paid_amt',
        'tot_no_of_trans',
        'is_active',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by',
    ];

    /**
     * Save Invoices
     * 
     * @param array $invoices
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function saveUserInvoice($invoices,$whereCondition=[])
    {
        if (!is_array($invoices)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        if (!isset($invoices['created_at'])) {
            $invoices['created_at'] = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
        }
        if (!isset($invoices['created_by'])) {
            $invoices['created_at'] = \Auth::user()->user_id;
        }        
        
        if (!empty($whereCondition)) {
            return self::where($whereCondition)->update($invoices);
        } else if (!isset($invoices[0])) {
            return self::create($invoices);
        } else {            
            return self::insert($invoices);
        }
    }

    /**
     * Get Invoices
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getInvoices($whereCondition=[]) {
        if (!is_array($whereCondition)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        $query = self::with('userInvoiceTxns');  
        if (!empty($whereCondition)) {
            $query->where($whereCondition);
        }
        $result = $query->get();
        return $result;
    }

    public function userInvoiceTxns(){
       return $this->hasMany('App\Inv\Repositories\Models\Lms\UserInvoiceTrans', 'user_invoice_id', 'user_invoice_id');
    }

    /**
     * Get Customer Name
     *      
     */

    public function getUserName() {
       return $this->belongsTo(User::class, 'user_id');
    }

    
     /**
     * Get Created By Name
     *      
     **/
    public function getCreatedByName() {
       return $this->belongsTo(User::class, 'created_by');
    }
    
     /**
     * Get State Name
     *      
     **/
    public function getStateNameByStateCode() {
       return $this->belongsTo(State::class, 'invoice_state_code', 'state_code');
    }

    /**
     * GET AJAX result list
     */
    public static function getUserInvoiceList($invoice_user_id) {
        $result = self::where('invoice_user_id' , $invoice_user_id)->get();
        return $result ? : false;
    }
}
