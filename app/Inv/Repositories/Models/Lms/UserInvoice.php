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
use App\Inv\Repositories\Models\LmsUser;

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
    public $timestamps = true;

    /**
     * Maintain created_by and updated_by automatically
     *
     * @var boolean
     */
    public $userstamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'parent_user_invoice_id',
        'user_invoice_rel_id',
        'user_gst_state_id',
        'comp_gst_state_id',
        'pan_no',
        'biz_gst_no',
        'biz_gst_state_code',
        'gst_addr',
        'biz_entity_name',
        'reference_no',
        'invoice_type',
        'invoice_cat',
        'invoice_type_name',
        'invoice_no',
        'inv_serial_no',
        'invoice_date',
        'due_date',
        'invoice_state_code',
        'place_of_supply',
        'comp_addr_id',
        'inv_comp_data',
        'registered_comp_id',
        'comp_addr_register',
        'bank_id',
        'tot_paid_amt',
        'tot_no_of_trans',
        'is_active',
        'file_id',
        'job_id',
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
        /*
        if (!isset($invoices['created_at'])) {
            $invoices['created_at'] = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
        }
        if (!isset($invoices['created_by'])) {
            $invoices['created_by'] = \Auth::user()->user_id;
        }        
        */
        if (!empty($whereCondition)) {
            return self::where($whereCondition)->update($invoices);
        } elseif (!isset($invoices[0])) {
            $invoiceSerialNo = self::where(['invoice_type'=> $invoices['invoice_type'],'invoice_cat' => $invoices['invoice_cat']])->orderBy('user_invoice_id','desc')->limit(1)->value('inv_serial_no');
            $invoiceSerialNo = sprintf('%04d', (($invoiceSerialNo ?? 0) + 1) ?? rand(0, 9999));
            $invoices['invoice_no'] = $invoices['invoice_no']. '/' . $invoiceSerialNo;
            $invoices['inv_serial_no'] = $invoiceSerialNo;
            return self::create($invoices);
        } else {
            $invoiceSerialNo = self::where(['invoice_type'=> $invoices['invoice_type'],'invoice_cat' => $invoices['invoice_cat']])->orderBy('user_invoice_id','desc')->limit(1)->value('inv_serial_no');
            $invoiceSerialNo = sprintf('%04d', (($invoiceSerialNo ?? 0) + 1) ?? rand(0, 9999));
            $invoices['invoice_no'] = $invoices['invoice_no']. '/' . $invoiceSerialNo;
            $invoices['inv_serial_no'] = $invoiceSerialNo;        
            return self::insert($invoices);
        }
    }

    
    public static function getInvoiceById(int $user_invoice_id){
       return self::where('user_invoice_id', '=', $user_invoice_id)->first();
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
    public static function getUserInvoiceList($user_id, $appId = null) {
        $result = self::where('user_id' , $user_id)
        ->where('invoice_cat', '!=', 3)
        ->orderBy('user_invoice_id','desc');
        return $result ? : false;
    }

    public static function getUserLastInvoiceNo(){
        $result =  self::orderBy('user_invoice_id','desc')->first();
        return $result ?? false;
    }

    public static function getLastInvoiceSerialNo($inv_type, $inv_cat = null){
        $invoiceDetails = self::where('invoice_type',$inv_type);

        switch (strtoupper($inv_cat)) {
            case 'CN':
                $invoiceDetails->where('invoice_cat','2');
                break;
            case 'DN':
                $invoiceDetails->where('invoice_cat','3');
                break;
            default:
                $invoiceDetails->where('invoice_cat','1');
                break;
        }
        return $invoiceDetails->orderBy('user_invoice_id','desc')->first();
    }

    public function lmsUser(){
        return $this->belongsTo(LmsUser::class, 'user_id', 'user_id'); //don't change to hasMany
    }
}
