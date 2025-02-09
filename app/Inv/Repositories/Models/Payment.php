<?php

namespace App\Inv\Repositories\Models;

use DB;
use Helpers;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Inv\Repositories\Models\Lms\Transactions;

class Payment extends BaseModel {
    
    use SoftDeletes;
    
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'payments';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'payment_id';

    protected $softDelete = true;
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
        'biz_id',
        'virtual_acc',
        'action_type',
        'trans_type',
        'parent_trans_id',
        'amount',
        'date_of_payment',
        'bank_account_id',
        'gst',
        'sgst_amt',
        'cgst_amt',
        'igst_amt',
        'payment_type',
        'utr_no',
        'unr_no',
        'cheque_no',
        'tds_certificate_no',
        'file_id',
        'description',
        'is_settled',
        'is_manual',
        'is_refundable',
        'generated_by',
        'sys_created_at',
        'sys_updated_at',
        'payment_excel_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
    ];

    CONST PAYMENT_SETTLED_PENDING    = 0;
    CONST PAYMENT_SETTLED_PROCESSING = 2;
    CONST PAYMENT_SETTLED_PROCESSED  = 3;
    CONST PAYMENT_SETTLED            = 1;
    
    public function biz() {
       return $this->belongsTo('App\Inv\Repositories\Models\Business', 'biz_id');
    }

    public function user(){
        return $this->belongsTo('App\Inv\Repositories\Models\User','user_id','user_id');
    }
    
    public function lmsUser(){
        return $this->belongsTo('App\Inv\Repositories\Models\LmsUser','user_id','user_id');
    }

    public function apportionment(){
        return $this->hasMany('App\Inv\Repositories\Models\Lms\Apportionment','payment_id','payment_id');
    }

    public function transaction(){
        return $this->hasMany('App\Inv\Repositories\Models\Lms\Transactions','payment_id','payment_id');
    }
    
    public function creator(){
        return $this->belongsTo('App\Inv\Repositories\Models\User','created_by','user_id');
    }
    
    public function userFile(){
        return $this->hasOne('App\Inv\Repositories\Models\UserFile','file_id','file_id');
    }    

    public function getBusinessName() {
        return $this->belongsTo(Business::class, 'biz_id');
    }

    public function getUserName() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getCreatedByName() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transType(){
        return $this->belongsTo('App\Inv\Repositories\Models\Lms\TransType', 'trans_type', 'id');
    } 

    public function refundReq(){
        return $this->hasOne('App\Inv\Repositories\Models\Lms\Refund\RefundReq','payment_id','payment_id');
    }
     
    public function getPaymentNameAttribute(){
        $tdsType = ($this->action_type == 3) ? '/TDS' : '';   
        return $this->transType->trans_name . $tdsType;
    }

    public static function getTallyTxns(array $where = array()) {
        return self::with('user', 'lmsUser', 'transType')->where(['is_settled' => 1, 'generated_by' => 0, 'is_refundable' => 1, 'trans_type' => 17, 'action_type' => 1])->where($where)->get();
    }

    /**
     * get Payment data list
     * 
     * @return type mixed
     */
    public static function getPayments(array $where = [], $orderBy = []) {
        $res = self::where($where);
        if(!empty($orderBy)){
            foreach($orderBy as $key => $val){
                $res = $res->orderBy($key, $val);
            }
        }
        return $res;
    }

    public function userRelation() {
        return $this->hasOne('App\Inv\Repositories\Models\Lms\UserInvoiceRelation', 'user_id', 'user_id')->where('is_active', 1);
    }

    /**
     * insert Payment data
     * 
     * @return type int payment_id
     */
    public static function insertPayments(array $arr = [])
    {
        $resp = [
            'status' => 'success',
            'code' => '000',
            'message' => 'success',
        ];
        try {
            $arr['sys_updated_at'] = Helpers::getSysStartDate();
            $arr['sys_created_at'] = $arr['sys_updated_at'];
            // dd($arr);
            $insertId = self::create($arr)->payment_id;
            $resp['code'] = $insertId;  
            $resp['message'] = 'Payment inserted successfuly';  
        } catch (\Exception $e) {
            $errorInfo  = $e->errorInfo;
            $resp['status'] = 'error';  
            $resp['code'] = $errorInfo[1] ?? '444';  
            $resp['message'] = preg_replace('#[^A-Za-z./\s\_]+#', '', $errorInfo[2]) ?? 'Some DB Error occured. Try again.';  
        }
        return $resp['status'] == 'success' ? $resp['code'] : $resp['message'];
    }   

    public function getPaymentModeAttribute() {
        if($this->action_type == 1){
            $payment_type = $this->payment_type;
            $payModes = config('payment.type') ?? [];
            $mode_of_pay = $payModes[$payment_type] ?? NULL;
        }else{
            $mode_of_pay = $this->paymentname;
        }
        return $mode_of_pay;
    }

    public function getTransactionNoAttribute() {
        $payment_type = $this->payment_type;
        switch ($payment_type) {
            case '1':
                $attr = $this->utr_no;
                break;
            case '2':
                $attr = $this->cheque_no;
                break;
            case '3':
                $attr = $this->unr_no;
                break;
            case '4':
                $attr = $this->unr_no;
                break;    
            default:
               $attr = '';
                break;
        }
        return $attr;
    }

    public static function getPaymentReceipt(array $where = []) {
        return self::with('userRelation')->where(['is_settled' => 1, 'generated_by' => 0, 'is_refundable' => 1, 'trans_type' => config('lms.TRANS_TYPE.REPAYMENT'), 'action_type' => 1])
        ->where($where)
        ->get();
    }

    public function companyBank()
    {
        return $this->hasOne('App\Inv\Repositories\Models\Master\Bank', 'id','bank_id');
    }

    public function companyUserAccount(){
        return $this->hasOne('App\Inv\Repositories\Models\UserBankAccount', 'bank_account_id','bank_account_id');
    }

    public function getSettledTxns() {
       return $this->hasMany('App\Inv\Repositories\Models\Lms\Transactions','payment_id','payment_id')->whereNotIn('trans_type',[config('lms.TRANS_TYPE.REPAYMENT'), config('lms.TRANS_TYPE.REFUND')]);
    }

    public function paymentRefrenceTxns(){
        return $this->hasMany('App\Inv\Repositories\Models\Lms\Transactions','payment_id','payment_id')->whereIn('trans_type',[config('lms.TRANS_TYPE.REPAYMENT')]);
    }
    
    /*** get all transaction  **/
    public static function getAllManualTransaction()
    {
          return self::with(['biz','user', 'transType', 'transaction'])->where('trans_type','!=',NULL)->orderBy('payment_id','DESC');
    }

    public function getisApportPayValidAttribute (){
        $isValid = false;
        $error = '';
        $lastSettledPaymentDate = self::where('user_id',$this->user_id)
        ->where('is_settled','1')->max('date_of_payment');
        
        $validPayment = self::where('user_id',$this->user_id)
        ->whereIn('is_settled',[self::PAYMENT_SETTLED_PENDING, self::PAYMENT_SETTLED_PROCESSING, self::PAYMENT_SETTLED_PROCESSED]);
        //->whereIn('action_type',['1','5']);

        if($lastSettledPaymentDate){
            $date_of_payment = date('Y-m-d', strtotime($this->date_of_payment));
            $lastSettledPaymentDate = date('Y-m-d', strtotime($lastSettledPaymentDate));
            $validPayment = $validPayment->where('date_of_payment','>=',$lastSettledPaymentDate);
            if(strtotime($lastSettledPaymentDate) > strtotime($date_of_payment)){
                $error = '<a class="btn btn-action-btn btn-sm" title = "Invalid Payment: The backdated payment from the last settled payment!"><i class="fa fa-info" aria-hidden="true"></i></a>';
                // Invalid Payment: The backdated payment from the last settled payment!
            }
        }

        $validPaymentId = $validPayment->orderBy('date_of_payment','asc')
        ->orderBy('payment_id','asc')
        ->first();

        if($validPaymentId && $validPaymentId->payment_id == $this->payment_id ){
            $isValid = true;
        }
        return ['isValid' => $isValid, 'error' => $error];
    }
    
    /**
     * Get all TDS transaction
     * 
     * @param type $whereCondition
     * @param type $whereRawCondition
     * @return type
     * @throws InvalidDataTypeExceptions
     */
    public static function getAllTdsTransaction($whereCondition=[], $whereRawCondition = NULL) {
        if (!is_array($whereCondition)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        $query = self::select('payments.user_id', 'trans_type', 'action_type', 'amount', 'date_of_payment', 'tds_certificate_no', 'file_id', 'payments.created_at as trans_date', 'payments.created_by', 'biz.biz_entity_name', 'users.f_name', 'users.l_name', 'mst_trans_type.trans_name')
                        ->join('biz', 'biz.biz_id', '=', 'payments.biz_id')
                        ->join('users', 'payments.created_by', '=', 'users.user_id')
                        ->join('mst_trans_type', 'mst_trans_type.id', '=', 'payments.trans_type')
                        ->where('action_type', 3)
                        ->where('file_id', 0);
                
        if (!empty($whereCondition)) {
            $query->where('payments.user_id', $whereCondition['user_id']);
        }
        if (!empty($whereRawCondition)) {
            $query->whereRaw($whereRawCondition);
        }
        return $query;
    }

    /**
     * get Payment data list
     * 
     * @return type mixed
     */
    public static function checkTdsCertificate($tdsCertiName, $id=null)
    {
        $query = self::select('payment_id')
                ->where('tds_certificate_no', $tdsCertiName);
                // ->where(function($q) use($tdsCertiName) {
                //     $search_keyword = trim($tdsCertiName);
                //     $q->where('tds_certificate_no', 'like', "%$search_keyword%");
                // });
        if (!is_null($id)) {
            $query->where('payment_id', '!=', $id);
        }
        $res = $query->get();        
        return $res ?: [];
    }

    public function scopeSettledProcessing($query)
    {
        return $query->orWhere('is_settled', self::PAYMENT_SETTLED_PROCESSING);
    }

    public function scopeSettledProcessed($query)
    {
        return $query->orWhere('is_settled', self::PAYMENT_SETTLED_PROCESSED);
    }

    public function getApportionmentIdAttribute(){
        return self::apportionment()->latest('apportionment_id')->value('apportionment_id');
    }

    public function getValidRevertPaymentAttribute() {
        $result = false;
        $curDate = Helper::getSysStartDate();
        $curYearMonth = Carbon::parse($curDate)->format('Ym');
        $apportDetails = self::apportionment()->latest('apportionment_id')->select('apportionment_id','apportionment_type','created_at')->first();

        $payYearMonth = $apportDetails ? Carbon::parse($apportDetails->created_at)->timezone(config('common.timezone'))->format('Ym') : Carbon::parse($this->date_of_payment)->format('Ym');
        $lastSettledPaymentId = Payment::where('user_id',$this->user_id)
        ->where('is_settled',self::PAYMENT_SETTLED)
        ->orderBy('date_of_payment','DESC')
        ->orderBy('payment_id','DESC')
        ->limit(1)
        ->value('payment_id');

        if($this->is_settled == self::PAYMENT_SETTLED && $lastSettledPaymentId == $this->payment_id){
            if($curYearMonth == $payYearMonth && $apportDetails->apportionment_type == 1 && !$this->refundReq){
                $refAdjTransId = Transactions::where('apportionment_id',$apportDetails->apportionment_id)->whereIn('trans_type',[config('lms.TRANS_TYPE.REFUND'),config('lms.TRANS_TYPE.MARGIN'),config('lms.TRANS_TYPE.NON_FACTORED_AMT')])->where('entry_type',1)->whereColumn('amount','<>','settled_outstanding')->pluck('trans_id')->toArray();
                $refAdjTransCnt = Transactions::whereIn('link_trans_id',$refAdjTransId)->whereIn('trans_type',[config('lms.TRANS_TYPE.REFUND'),config('lms.TRANS_TYPE.ADJUSTMENT')])->where('entry_type',0)->where('outstanding','>',0)->count();
                if(!$refAdjTransCnt){
                    $result = true;
                }
            }
        }
        return $result;
    }
}