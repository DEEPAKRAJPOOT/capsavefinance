<?php
namespace App\Inv\Repositories\Models\Lms;

use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class Transactions extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    private static $balacnce = 0;
    protected $table = 'transactions';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'trans_id';

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
        'payment_id',
        'parent_trans_id',
        'invoice_disbursed_id',
        'user_id',
        'trans_date',
        'trans_type',
        'amount',
        'entry_type',
        'gst',
        'cgst',
        'sgst',
        'igst',
        'tds_per',
        'gl_flag',
        'soa_flag',
        'trans_by',
        'pay_from',
        'is_settled',
        'is_posted_in_tally',
        'comment',
        'created_at',
        'created_by'
    ];

    public function payment(){
        return $this->belongsTo('App\Inv\Repositories\Models\Payment','payment_id','payment_id');
    } 

    public function invoiceDisbursed(){
        return $this->belongsTo('App\Inv\Repositories\Models\Lms\InvoiceDisbursed','invoice_disbursed_id','invoice_disbursed_id');
    }
        
    public function user(){
        return $this->belongsTo('App\Inv\Repositories\Models\Users','user_id','user_id');
    }
    
    public function lmsUser(){
        return $this->belongsTo('App\Inv\Repositories\Models\LmUsersUser','user_id','user_id');
    }

    public function transType(){
       return $this->hasOne('App\Inv\Repositories\Models\Lms\TransType', 'id', 'trans_type');
    }   
  
    public function refundTransaction(){
        return $this->hasOne('App\Inv\Repositories\Models\Lms\RefundTransactions', 'new_trans_id', 'trans_id');
    }

    public function getsettled_amtAttribute(){
        return self::where('parent_trans_id','=',$this->trans_id)->sum('amount');
    }

    public function getOutstandingAttribute(){
        return $this->amount-$this->getSettledAmt($this->trans_id);
    }

    public function getTransNameAttribute(){
        if($this->entry_type == 0){
            return $this->transType->debit_desc;
        }elseif($this->entry_type == 1){
            return $this->transType->credit_desc;
        }
    }

    private function getSettledAmt($trans_id){
        return self::where('parent_trans_id','=',$this->trans_id)->sum('amount');
    }

    public static function getUnsettledTrans($userId){
        return self::whereIn('is_settled',[0,1])
                ->whereNull('parent_trans_id')
                ->where('user_id','=',$userId)
                ->orderBy('invoice_disbursed_id','ASC')
                ->with(array('invoiceDisbursed' => function($query) {
                    $query->orderBy('int_accrual_start_dt','ASC');
                }))
                ->orderByRaw("FIELD(trans_type, '9', '16', '33', '10')")
                ->get()
                ->filter(function($item) {
                    return $item->outstanding > 0;
                });
    }

    public static function getSettledTrans($userId){
        return self::whereIn('is_settled',[2])
                ->whereNotNull('parent_trans_id')
                ->whereNotIn('trans_type',[config('lms.TRANS_TYPE.INTEREST_REFUND'),config('lms.TRANS_TYPE.MARGIN')])
                ->where('user_id','=',$userId);
    }

    public static function getRefundTrans($userId){
        return self::whereIn('is_settled',[2])
                ->whereNotNull('parent_trans_id')
                ->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST_REFUND'),config('lms.TRANS_TYPE.MARGIN')])
                ->where('user_id','=',$userId);
    }

    /**
     * Save Transactions
     * 
     * @param array $transactions
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function saveTransaction($transactions,$whereCondition=[])
    {
        if (!is_array($transactions)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        if (!isset($transactions['created_at'])) {
            $transactions['created_at'] = \Carbon\Carbon::now()->setTimezone(config('common.timezone'))->format('Y-m-d h:i:s');
        }
        if (!isset($transactions['created_by'])) {
            $transactions['created_by'] = \Auth::user()->user_id;
        }        
        
        if (!empty($whereCondition)) {
            return self::where($whereCondition)->update($transactions);
        } else if (!isset($transactions[0])) {
            return self::create($transactions);
        } else {            
            return self::insert($transactions);
        }
    }

    /**
     * Get Transactions
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getTransactions($whereCondition=[])
    {
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


    /**
     * Get Unsettled Inovoices
     * 
     * @param array 
     * @return mixed
     */
    public static function getUnsettledInvoices(){
        return self::whereIn('trans_type',[16])
        ->get()
        ->filter(function($item) {
            return $item->outstanding > 0;
        })
        ;
    }









    

    /*** save repayment transaction details for invoice  **/
    public static function saveRepaymentTrans($attr)
    {
          return self::create($attr);
    }
    
    
    /*** save repayment transaction details for invoice  **/
    public static function saveCharge($attr)
    {
        return self::insert($attr);
          
    } 
    
    /*** get all transaction  **/
    public static function getAllManualTransaction()
    {
          return self::with(['biz','disburse','trans_detail','user'])->where('trans_by','!=',NULL)->orderBy('trans_id','DESC');
    }
    

    public static function get_balance($trans_code,$user_id){

        $dr =  self::whereRaw('concat_ws("",user_id, DATE_FORMAT(created_at, "%y%m%d"), (1000000000+trans_id)) <= ?',[$trans_code])
                    ->where('user_id','=',$user_id)
                    ->where('soa_flag','=',1)
                    ->whereNull('repay_trans_id')
                    ->where('entry_type','=','0')
                    ->sum('amount');
                    
        $dr +=  self::whereRaw('concat_ws("",user_id, DATE_FORMAT(created_at, "%y%m%d"), (1000000000+trans_id)) <= ?',[$trans_code])
                    ->where('user_id','=',$user_id)
                    ->where('soa_flag','=',1)
                    ->whereNotNull('repay_trans_id')
                    ->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST_OVERDUE'),config('lms.TRANS_TYPE.INTEREST_REFUND')])
                    ->where('entry_type','=','0')
                    ->sum('amount');
                    
        $cr =   self::whereRaw('concat_ws("",user_id, DATE_FORMAT(created_at, "%y%m%d"), (1000000000+trans_id)) <= ?',[$trans_code])
                    ->where('user_id','=',$user_id)
                    ->where('soa_flag','=',1)
                    ->whereNull('repay_trans_id')
                    ->where('entry_type','=','1')
                    ->sum('amount');

        $cr +=  self::whereRaw('concat_ws("",user_id, DATE_FORMAT(created_at, "%y%m%d"), (1000000000+trans_id)) <= ?',[$trans_code])
                    ->where('user_id','=',$user_id)
                    ->where('soa_flag','=',1)
                    ->whereNotNull('repay_trans_id')
                    ->whereIn('trans_type',[config('lms.TRANS_TYPE.INTEREST_OVERDUE'),config('lms.TRANS_TYPE.INTEREST_REFUND')])
                    ->where('entry_type','=','1')
                    ->sum('amount');
        

        return $dr - $cr;
    }
    
    public  function getBalanceAttribute()
    {
        return self::get_balance($this->user_id.Carbon::parse($this->created_at)->format('ymd').(1000000000+$this->trans_id), $this->user_id);
    }

    public static function getUserBalance($user_id){

        $trans = self::select(DB::raw('max(concat_ws("",user_id, DATE_FORMAT(created_at, "%y%m%d"), (1000000000+trans_id)))as trans_code'))->where('user_id','=',$user_id)->get();
        return self::get_balance($trans[0]->trans_code, $user_id);
    }

     /*** get all transaction  **/
    public static function getAllUserChargeTransaction()
    {
          return self::with('user')->where('chrg_trans_id','!=',NULL)->groupBy('user_id')->get();
    }   
     
    /**
     * Update Transaction
     * 
     * @param array $whereCondition
     * @param array $data
     * @return mixed
     */
    public static function updateTransaction($whereCondition, $data)
    {
        return self::where($whereCondition)->update($data);
    }



    public function getOppTransNameAttribute(){
        if($this->trans_detail->chrg_master_id!='0'){
            if($this->is_waveoff == 1){
                return $this->trans_detail->charge->chrg_name.' Waved Off';
            }if($this->is_tds == 1){
                return $this->trans_detail->charge->chrg_name.' TDS';
            }elseif($this->entry_type == 0){
                return $this->trans_detail->charge->credit_desc;
            }elseif($this->entry_type == 1){
                return $this->trans_detail->charge->debit_desc;
            }
        }else{
            if($this->is_waveoff == 1){
                return $this->trans_detail->trans_name.' Waved Off';
            }if($this->is_tds == 1){
                return $this->trans_detail->trans_name.' TDS';
            }elseif($this->entry_type == 0){
                return $this->trans_detail->credit_desc;
            }elseif($this->entry_type == 1){
                return $this->trans_detail->debit_desc;
            }
        }
    }

    public function getModeOfPaymentNameAttribute(){
        $modeName = '';
        switch ($this->mode_of_pay) {
            case '1':
                $modeName = 'UTR No';
                break;
            case '2':
                $modeName = 'Cheque No';
                break;
            case '3':
                $modeName = 'URN No';
                break;  
        }
        return $modeName;
    }

    public function getModeOfPaymentNoAttribute(){
        $modeNo = '';
        switch ($this->mode_of_pay) {
            case '1':
                $modeNo = $this->utr_no;
                break;
            case '2':
                $modeNo = $this->cheque_no;
                break;
            case '3':
                $modeNo = $this->unr_no;
                break;   
        }
        return $modeNo;
    }

    public function getBatchNoAttribute(){
        if(in_array($this->trans_type ,[config('lms.TRANS_TYPE.REPAYMENT'),config('lms.TRANS_TYPE.PAYMENT_DISBURSED')])){
            return $this->txn_id;
        }
        if(in_array($this->trans_type ,[config('lms.TRANS_TYPE.INTEREST_REFUND'), config('lms.TRANS_TYPE.NON_FACTORED_AMT'), config('lms.TRANS_TYPE.MARGIN') ]) && !$this->repay_trans_id && $this->refundTransaction != null){
            return $this->refundTransaction->request->batch->batch_id;
        }
    }

    public function getNarrationAttribute(){
        $data = '';
        if($this->trans_type == config('lms.TRANS_TYPE.REPAYMENT'))
        $data .= $this->BatchNo.' ';

        if($this->modeOfPaymentName && $this->modeOfPaymentNo)
        $data .= $this->modeOfPaymentName.': '.$this->modeOfPaymentNo.' ';

        if($this->trans_type == config('lms.TRANS_TYPE.REPAYMENT'))
        $data .= ' Repayment Allocated as Normal: '.$this->amount . ' TDS:0.00'.' ';

        if($this->is_tds && $this->tds_cert)
        $data .= ' TDS CERT No: '.$this->tds_cert ;

        return $data;
    }

    public static function getSoaList(){

        return self::select('transactions.*')
                    ->join('users', 'transactions.user_id', '=', 'users.user_id')
                    ->join('lms_users','users.user_id','lms_users.user_id')
                    ->where('soa_flag','=',1)
                    ->orderBy('user_id', 'asc')
                    ->orderBy(DB::raw("DATE_FORMAT(rta_transactions.created_at, '%Y-%m-%d')"), 'asc')
                    ->orderBy('trans_id', 'asc');
    }
    
    /**
     * Get Repayment Amount
     * 
     * @param integer $userId
     * @param integer $transType
     * @return mixed
     */
    public static function getRepaymentAmount($userId, $transType)
    {
        //Calculate Debit Amount
        $result = self::select(DB::raw('SUM(amount) AS amount, gst, SUM(cgst) AS cgst, SUM(sgst) AS sgst, SUM(igst) AS igst'))
                ->whereIn('is_settled', [0,1])
                ->where('user_id', $userId)
                ->where('entry_type', '0')    //Debit
                ->where('trans_type', $transType)
                ->groupBy('user_id')
                ->first();
        $debitAmtData = $result ? $result->toArray() : [];
        
        //Calculate Credit Amount
        $result = self::select(DB::raw('SUM(amount) AS amount, gst, SUM(cgst) AS cgst, SUM(sgst) AS sgst, SUM(igst) AS igst'))
                ->whereIn('is_settled', [0,1])
                ->where('user_id', $userId)
                ->where('entry_type', '1')
                ->where('trans_type', $transType)
                ->groupBy('user_id')
                ->first();
        $creditAmtData = $result ? $result->toArray() : [];
        
        $repaymentAmount = ['debitAmtData' => $debitAmtData, 'creditAmtData' => $creditAmtData];
        
        return $repaymentAmount;
    }

    
    public static function getAllChargesApplied(array $where = array()) {
        $cond = '';
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                $wh[] = "t1.$key = '$value'";
            }
           $cond = ' AND ' .implode(' AND ', $wh);
        }
        $query = "SELECT DATE_FORMAT(t1.trans_date, '%d/%m/%Y') as trans_date, t1.trans_id, t1.parent_trans_id, t1.trans_name, t1.trans_desc, t1.user_id, t1.entry_type, t1.amount AS debit_amount, IFNULL(SUM(t2.amount), 0) as credit_amount, (t1.amount - IFNULL(SUM(t2.amount), 0)) as remaining 
        FROM `get_all_charges` t1 
        LEFT JOIN rta_transactions as t2 ON t1.trans_id = t2.parent_trans_id 
        WHERE t1.entry_type = 0  ". $cond ." GROUP BY t1.trans_id HAVING remaining > 0";
        $result = \DB::SELECT(\DB::raw($query));
        return $result;
    }
    
    public static function getTallyTxns(array $where = array()) {
        $result = self::select('transactions.trans_id', 'transactions.repay_trans_id', 'transactions.parent_trans_id', 'transactions.user_id', 'users.f_name', 'users.m_name', 'users.l_name', 'transactions.biz_id', 'transactions.virtual_acc_id', 'transactions.disbursal_id', 'transactions.trans_date', 'transactions.trans_type', 'transactions.chrg_trans_id', 'transactions.amount', 'transactions.settled_amount', 'transactions.entry_type', 'user_bank_account.acc_name', 'user_bank_account.acc_no', 'mst_bank.bank_name', 'user_bank_account.ifsc_code' , 'transactions.is_settled', 'transactions.mode_of_pay', 'transactions.utr_no', 'transactions.unr_no', 'transactions.cheque_no', 'transactions.trans_by', 'transactions.pay_from', 'transactions.txn_id', 'transactions.is_posted_in_tally', 'transactions.comment', 'tally_voucher.trans_type_id', 'mst_trans_type.trans_name', 'mst_trans_type.credit_desc', 'mst_trans_type.debit_desc', 'mst_trans_type.tally_trans_type', 'tally_voucher.tally_voucher_id', 'tally_voucher.voucher_name', 'tally_voucher.created_at as voucher_date')
            ->join('users', 'users.user_id', '=', 'transactions.user_id')
            ->join('mst_trans_type', 'mst_trans_type.id', '=', 'transactions.trans_type')
            ->join('tally_voucher', 'tally_voucher.trans_type_id', '=', 'mst_trans_type.id')
            ->join('user_bank_account', 'user_bank_account.user_id', '=', 'transactions.user_id')
            ->join('mst_bank', 'mst_bank.id', '=', 'user_bank_account.bank_id')
            ->where($where)
            ->groupBy('transactions.trans_id')
            ->get();
        return $result;
    }
}
