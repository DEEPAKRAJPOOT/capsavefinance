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
        'description',
        'settle_payment_desc',
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
        $query = self::select('mst_state.name','user_invoice.inv_comp_data','user_invoice.biz_gst_no','user_invoice.biz_entity_name','user_invoice.gst_addr','user_invoice_trans.sac_code','user_invoice.invoice_type','user_invoice.invoice_no as capinvoice','user_invoice.invoice_date', 'user_invoice.due_date', 'user_invoice_trans.base_amount','user_invoice_trans.sgst_rate','user_invoice_trans.sgst_amount','user_invoice_trans.cgst_rate','user_invoice_trans.cgst_amount','user_invoice_trans.igst_rate','user_invoice_trans.igst_amount','user_invoice.user_id', 'invoice.invoice_no as invoice','transactions.trans_id as transId', 'transactions.trans_type as transTypeId', 'invoice_disbursed.interest_rate as interestRate','invoice_disbursed.overdue_interest_rate as odi', 'user_invoice.invoice_cat','user_invoice.parent_user_invoice_id','user_invoice_trans.description', 'parent_user_invoice.invoice_no as parentCapinvoice','transactions.from_date','transactions.to_date', 'customer_transaction_soa.trans_name','linkUserInv.invoice_no as link_invoice_no', 'parentUserInv.invoice_no as parent_invoice_no','user_invoice.invoice_type_name')
        ->Join('user_invoice', 'user_invoice.user_invoice_id', 'user_invoice_trans.user_invoice_id')
        ->Join('transactions', 'transactions.trans_id','user_invoice_trans.trans_id')
        ->Join('customer_transaction_soa','customer_transaction_soa.trans_id','transactions.trans_id')
        ->Join('mst_state', 'mst_state.id','user_invoice.comp_gst_state_id')
        ->leftJoin('invoice_disbursed', 'invoice_disbursed.invoice_disbursed_id','transactions.invoice_disbursed_id')
        ->leftJoin('invoice', 'invoice.invoice_id','invoice_disbursed.invoice_id')
        ->leftJoin('user_invoice as parent_user_invoice','parent_user_invoice.user_invoice_id', 'user_invoice.parent_user_invoice_id')
        ->leftJoin('transactions as linkTrans', 'linkTrans.trans_id','transactions.link_trans_id')
        ->leftJoin('user_invoice_trans as linkUserInvTrans','linkUserInvTrans.trans_id','linkTrans.trans_id')
        ->leftJoin('user_invoice as linkUserInv', 'linkUserInv.user_invoice_id','linkUserInvTrans.user_invoice_id')
        ->leftJoin('transactions as parentTrans', 'parentTrans.trans_id','transactions.parent_trans_id')
        ->leftJoin('user_invoice_trans as parentUserInvTrans','parentUserInvTrans.trans_id','parentTrans.trans_id')
        ->leftJoin('user_invoice as parentUserInv', 'parentUserInv.user_invoice_id','parentUserInvTrans.user_invoice_id');
            
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
