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

class UserInvoiceTrans extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'user_invoice_trans';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'invoice_trans_id';

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
        'user_invoice_id',
        'trans_id',
    ];

    public function trans(){
        return $this->hasOne('App\Inv\Repositories\Models\Lms\Transactions','trans_id','trans_id');
    } 

    /**
     * Save Invoices
     * 
     * @param array $user invoices txn
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function saveUserInvoiceTxns($invoices_txns,$whereCondition=[])
    {
        if (!is_array($invoices_txns)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }    
        if (!empty($whereCondition)) {
            return self::where($whereCondition)->update($invoices_txns);
        } else if (!isset($invoices_txns[0])) {
            return self::create($invoices_txns);
        } else {            
            return self::insert($invoices_txns);
        }
    }

    /**
     * Get Invoices Transactions
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getInvoicesTxns($whereCondition=[]) {
        if (!is_array($whereCondition)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        $query = self::select('*');
                
        if (!empty($whereCondition)) {
            $query->where($whereCondition);
        }
        
        $result = $query->get();
        return $result;
    }

    public static function leaseRegisters($whereCondition=[], $whereRawCondition = NULL) {
        if (!is_array($whereCondition)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        $query = self::select('name','inv_comp_data','biz_gst_no','biz_entity_name','gst_addr','sac_code','invoice_type','invoice_no','invoice_date', 'due_date', 'base_amount','sgst_rate','sgst_amount','cgst_rate','cgst_amount','igst_rate','igst_amount','user_id')
        ->join('user_invoice', 'user_invoice.user_invoice_id', 'user_invoice_trans.user_invoice_id')
        ->join('mst_state', 'mst_state.id','user_invoice.comp_gst_state_id');
                
        if (!empty($whereCondition)) {
            $query->where($whereCondition);
        }        
        if (!empty($whereRawCondition)) {
            $query->whereRaw($whereRawCondition);
        }
        return $query;
    }

    public function getUserInvoice() {
        return $this->belongsTo('App\Inv\Repositories\Models\Lms\UserInvoice', 'user_invoice_id', 'user_invoice_id');
    }
}
