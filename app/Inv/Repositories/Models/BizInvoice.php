<?php
namespace App\Inv\Repositories\Models;
use Carbon\Carbon;
use DateTime;
use Auth;
use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\Anchor;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\Program;
use App\Inv\Repositories\Models\InvoiceBulkUpload;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\Lms\InvoiceRepaymentTrail;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Models\InvoiceStatusLog;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\Lms\InvoiceDisbursed;
use App\Inv\Repositories\Contracts\Traits\InvoiceTrait;
use App\Helpers\Helper;

class BizInvoice extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'invoice';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'invoice_id';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    //protected $dates = ['deleted_at'];

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
        'anchor_id',
        'supplier_id',
        'program_id',
        'app_id',
        'biz_id',
        'invoice_no',
        'tenor',
        'is_tenor_mannual',
        'invoice_due_date',
        'invoice_date',
        'pay_calculation_on',
        'invoice_amount',
        'invoice_approve_amount',
        'invoice_margin_amount',
        'is_margin_deduct',
        'prgm_offer_id',
        'app_offer_adhoc_limit_id',
        'file_id',
        'status_id',
        'is_bulk_upload',
        'status_update_time',
        'remark',
        'is_adhoc',
        'limit_exceed',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by',
        'is_repayment',  
        'repayment_amt',
        'principal_repayment_amt'
    ];

    
    /**
     * update user details
     *
     * @param integer $user_id     user id
     * @param array   $arrUserData user data
     *
     * @return boolean
     */
    public static function saveInvoice($arrInvoice)
    {
        return  self::create($arrInvoice);
        
    } 
    
    
    public static function saveBulkInvoice($arrInvoice)
    {
        $arrInvoiceVal = self::insert($arrInvoice);
        return ($arrInvoiceVal ?: false);
    } 
    
        
    public static function updateInvoice($invoiceId,$status)
    {
        $updated_at  = Carbon::now()->toDateTimeString();
        $id = Auth::user()->user_id;
        InvoiceStatusLog::saveInvoiceStatusLog($invoiceId,$status);
        $marginStatus =  [7,11,14,28];
        if(in_array($status,$marginStatus))
        {
            self::where(['invoice_id' => $invoiceId])->update(['is_margin_deduct' => 0]);
        }
       
          return self::where(['invoice_id' => $invoiceId])->update(['status_id' => $status,'status_update_time' => $updated_at,'updated_by' =>$id]);
        
    } 
    
    public static function updateInvoiceAmount($attributes)
    {
        $invoiceId  =    $attributes['invoice_id'];
        $amount     =  str_replace(',','', $attributes['approve_invoice_amount']);  
        $comment    =    $attributes['comment'];
        $updated_at  = Carbon::now()->toDateTimeString();
        $id = Auth::user()->user_id;    
        $result =  User::getSingleUserDetails($id);
        InvoiceStatusLog::saveInvoiceLog($invoiceId,7,$amount,$comment);
        
        $invoice = self::find($invoiceId);
        $marginAmt = Helper::getOfferMarginAmtOfInvoiceAmt($invoice->prgm_offer_id, $amount);

        return  self::where(['invoice_id' => $invoiceId])->update([
            'invoice_approve_amount' => $amount,
            'status_update_time' => $updated_at,
            'updated_by' => $id,
            'invoice_margin_amount' => $marginAmt
        ]);
        
    } 
    
    public static function getDisbursedAmount($invid)
    {
       return self::with(['disbursal','invoicePayment'])->where('invoice_id',$invid)->first();
    }

    function invoicePayment()
     {
          return $this->hasMany('App\Inv\Repositories\Models\Lms\InvoiceRepaymentTrail', 'invoice_id','invoice_id');  
     
     }
    
    function invoiceStatusLog()
    {
        return $this->hasMany('App\Inv\Repositories\Models\InvoiceStatusLog','invoice_id','invoice_id');
    }
    
    /* get anchor */    
    public static function getInvoice()
    {
       return Anchor::get();
     }   
  
     public static function getAllInvoice($request,$status)
     {
        $id = Auth::user()->user_id;
        $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
        $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
        if( $chkUser->id==11)
        {
            $res  = User::where('user_id',$id)->first();

            return self::where(['status_id' => $status,'anchor_id' => $res->anchor_id])->orderBy('invoice_id', 'DESC');

        }
        else
        {
           return self::where('status_id',$status)->orderBy('invoice_id', 'DESC');
        }
     } 

     public function getBillNoAttribute(){
        $poNo = null;            
        $invNo = $this->invoice_no;

        if(!$poNo && $this->parent_invoice_id){
            $pInv = BizInvoice::find($this->parent_invoice_id);
            $poNo = null;
        }

        $data = '';
        
        if($poNo){
            $data .= $poNo;
        }

        if($poNo && $invNo){
            $data .= ' / ';
        }

        if($invNo){
            $data .= $invNo;
        }

        return $data;
        
    }
     
     public static function getUserAllInvoice($request)
     {
        $id = Auth::user()->user_id;
        return self::where(['supplier_id' =>$id])->with(['mstStatus','business','anchor','supplier','userFile','program','program_offer'])->orderBy('invoice_id', 'DESC');
     }  
     
    public static function  getSingleInvoice($invId)
     {
         return self::with(['anchor','supplier','gst','pan'])->where(['invoice_id' =>$invId])->first();
         
     }
     public static function  getAllInvoiceAnchor($status_id)
     {
         return self::with(['business','anchor'])->where(['status_id' =>$status_id])->groupBy('biz_id')->get();
         
     }  
    
     
     public static function  getBusinessNameApp($status_id)
     {
         return self::with(['business'])->where(['status_id' =>$status_id])->groupBy('biz_id')->get();
         
     }  
      public static function  getUserBusinessNameApp($status_id)
     {
         $id = Auth::user()->user_id;
         return self::with(['business'])->where(['supplier_id' =>$id,'status_id' =>$status_id])->groupBy('biz_id')->get();
         
     }
       function business()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\Business', 'biz_id','biz_id');  
    
     }
     
      function pan()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\BizPanGst', 'supplier_id','user_id')->where(['status' =>1,'type' =>1,]);  
    
     }
      
     function gst()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\BizPanGst', 'supplier_id','user_id')->where(['status' =>1,'type' =>2,]);  
    
     }
     
     function anchor()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\Anchor', 'anchor_id','anchor_id');  
    
     }
     function userFile()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\UserFile', 'file_id','file_id');  
    
     }
     
     function supplier()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\User', 'supplier_id','user_id')->whereIn('is_buyer',[1,2]); 
     
     }
     
     function lms_user()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\LmsUser', 'supplier_id','user_id'); 
     
     }
     
     function program_offer()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\AppProgramOffer', 'prgm_offer_id','prgm_offer_id');  
     
     }

     function app()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\Application', 'app_id','app_id');  
     
     }

     function mstStatus()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\Master\Status', 'status_id');  
     
     }

     function program()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\Program', 'program_id');  
     
     }

     function invoice_disbursed()
     {
          return $this->hasOne('App\Inv\Repositories\Models\Lms\InvoiceDisbursed', 'invoice_id','invoice_id');  
     
     }
    
     function supplier_bank_detail()
     {
          return $this->hasOne('App\Inv\Repositories\Models\UserBankAccount', 'user_id', 'supplier_id')->where(['is_default' => 1, 'is_active' => 1]);  
     
     }
    
      function Invoiceuser()
     {
       return $this->belongsTo('App\Inv\Repositories\Models\User','updated_by','user_id');
     }
     
    function user()
    {
       return $this->hasOne('App\Inv\Repositories\Models\User','user_id');  
    }
     
    function processing_fee()
    {
       return $this->hasOne('App\Inv\Repositories\Models\Lms\InvoiceCharge','invoice_id')->where(['charge_id' => 12, 'is_active' => 1]);  
    }

    function userDetail()
    {
       return $this->belongsTo('App\Inv\Repositories\Models\UserDetail','supplier_id','user_id'); 
    }
    function bulkUpload()
    {
          return $this->belongsTo('App\Inv\Repositories\Models\InvoiceBulkUpload', 'invoice_id', 'invoice_id');
     
     }
    public static function getUser($uid)
    {
       return User::whereIn('is_buyer',[1,2])->where('user_id',$uid)->first();
    }   
    
    public static function updateFileId($arr,$invoiceId)
    {
       return self::where(['invoice_id' => $invoiceId])->update($arr);
    }    
    public static function getAnchor($aid)
    {
       return Anchor::where('anchor_id',$aid)->first();
    }    
     
     public static function getProgram($aid)
    {
       return Program::where(['status' => 1,'anchor_id' =>$aid])->get();
     }   
     
     public static function getProgramForLimit($pid)
    {
       return Program::where(['prgm_id' =>$pid])->first();
    }   
   
         public static function getProgramForAppLimit($pid,$appId)
    {
           return  AppProgramOffer::where(['prgm_id' =>$pid,'app_id' => $appId,'is_approve' =>1,'is_active'=>1,'status' =>1])->sum('prgm_limit_amt');
     }  
     
     
    public static function getAllUserInvoice($userId)
    {
        return self::with('app.acceptedOffer')
            ->whereHas('app.user', function($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
            ->where('status_id', 9)
            ->get();
    }

    public static function getAllUserInvoiceIds($userIds)
    {
        return self::whereHas('app.user', function($query) use ($userIds) {
                    $query->whereIn('user_id', $userIds);
                })
            ->where('status_id', 9)
            ->pluck('invoice_id');
    }
     
     /**
     * get Invoice Data
     * 
     * @param type $where
     * @param type $select
     * @return type mixed
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions 
     */
    public static function getInvoiceData($where, $select = ['*'])
    {
        /**
         * Check Data is Array
         */
        if (!is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        /**
         * Check Data is not blank
         */
        if (empty($where)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }
        $res = self::select($select)->where($where)->get();
        return $res ?: false;
    }
    
    public static function checkDuplicateInvoice($invNo,$user_id)
    {
        
        return self::where(['invoice_no' => $invNo,'supplier_id' => $user_id])->first();
    }
   public static function getUserWiseInvoiceData($user_id)
    {
        
        return self::with('mstStatus','invoice_disbursed')->where(['supplier_id' => $user_id]);
    }
    
    public static function getUserInvoiceIds($userId)
    {
        return self::where('supplier_id', $userId)
            ->where('status_id', 9)
            ->pluck('invoice_id');
    }
     public static function getBizAnchor($attr)
    {
        return self::with('anchor')->where(['status_id' => $attr['status_id'],'biz_id' => $attr['biz_id']])->groupBy('anchor_id')->get();
          
    }
    
     public static function getUserBizAnchor($attr)
    {
         $id = Auth::user()->user_id;
         return self::with('anchor')->where(['supplier_id' =>$id,'status_id' => $attr['status_id'],'biz_id' => $attr['biz_id']])->groupBy('anchor_id')->get();
          
    }
     public static function checkSingleInvoice($invNo)
    {
        
        return self::where(['invoice_id' => $invNo])->first();
    }

    public static function getAllUserBatchInvoice($data)
    {
        return self::with('app.acceptedOffer')->with('invoice_disbursed')->with('program')
            ->whereHas('invoice_disbursed.disbursal', function($query) use ($data) {
                    $query->where($data);
                })
            ->where('status_id', 10)
            ->get();
    } 
    
     public static function  updateInvoiceUser($uid)
     {
       return self::create(['updated_by' =>$uid ]);  
     }
    public static function getRemainAmount($res) 
    {
        
        return  self::where(['anchor_id'=>$res['anchor_id'],'program_id'=>$res['prgm_id'],'app_id'=>$res['app_id'],'supplier_id' => $res['user_id'],'status_id' =>12 ])->sum('invoice_approve_amount');      
    }
    
       protected  function saveFinalInvoice($res)
    {
           $mytime = Carbon::now();
           $arr =          ['anchor_id' => $res->anchor_id,
                            'supplier_id' => $res->supplier_id,
                            'program_id' => $res->program_id,
                            'prgm_offer_id' => $res->prgm_offer_id,
                            'app_id' => $res->app_id,
                            'biz_id' => $res->biz_id,
                            'invoice_no' => $res->invoice_no,
                            'tenor' => $res->tenor,
                            'invoice_due_date' => $res->invoice_due_date,
                            'invoice_date' => $res->invoice_date,
                            'pay_calculation_on' => $res->pay_calculation_on,
                            'invoice_amount' => $res->invoice_approve_amount, 	
                            'invoice_approve_amount' => $res->invoice_approve_amount,
                            'remark' => $res->comm_txt,
                            'limit_exceed' => $res->limit_exceed,
                            'is_bulk_upload' => 1,
                            'status_id' => $res->status_id,
                            'file_id' => $res->file_id,
                            'created_by' => $res->created_by,
                            'created_at' =>  $mytime,
                            'invoice_margin_amount' => $res['invoice_margin_amount']
                        ];
     return self::create($arr);   
   
    }
    
    /**
     * Get Total Invoice Approval Amount
     * 
     * @param array $invoices
     * @return decimal
     * @throws InvalidDataTypeExceptions
     */
    public static function getTotalInvApprAmt($invoices)
    {
        /**
         * Check Data is Array
         */
        if (!is_array($invoices)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }
        
        return self::whereIn('invoice_id', $invoices)->sum('invoice_approve_amount');
    }
    
   
      function detail()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\UserDetail', 'supplier_id','user_id')->where('is_overdue',1); 
     
     }
     
   
    public static function getInvoiceUtilizedAmount($attr)
    {
        return  BizInvoice::whereIn('status_id',[8,9,10,12,13,15])->where(['is_adhoc' =>0,'supplier_id' =>$attr['user_id'],'anchor_id' =>$attr['anchor_id'],'program_id' =>$attr['prgm_id'],'app_id' =>$attr['app_id']])->sum('invoice_approve_amount');       
    }

    public static function getSettledInvoiceAmount($attr)
    {
        return  BizInvoice::whereIn('status_id',[13,15])->where(['is_adhoc' => 0,'supplier_id' => $attr['user_id'],'anchor_id' => $attr['anchor_id'],'program_id' => $attr['prgm_id'],'app_id' => $attr['app_id']])->sum('invoice_approve_amount');       
    }
    
    public static function getAnchorInvoiceDataDetail($anchorId = null) 
    {  
        if (!is_null($anchorId)) {
            $data['PENDING'] = self::whereHas('supplier', function($query) use($anchorId) { $query->where('anchor_id', $anchorId);})->where('status_id', config('lms.mst_status_invoice')['PENDING'])->count();
            $data['APPROVED'] = self::whereHas('supplier', function($query) use($anchorId) { $query->where('anchor_id', $anchorId);})->where('status_id', config('lms.mst_status_invoice')['APPROVED'])->count();
            $data['DISBURSMENT_QUE'] = self::whereHas('supplier', function($query) use($anchorId) { $query->where('anchor_id', $anchorId);})->where('status_id', config('lms.mst_status_invoice')['DISBURSMENT_QUE'])->count();
            $data['SENT_TO_BANK'] = self::whereHas('supplier', function($query) use($anchorId) { $query->where('anchor_id', $anchorId);})->where('status_id', config('lms.mst_status_invoice')['SENT_TO_BANK'])->count();
            $data['FAILED_DISBURSMENT'] = self::whereHas('supplier', function($query) use($anchorId) { $query->where('anchor_id', $anchorId);})->where('status_id', config('lms.mst_status_invoice')['FAILED_DISBURSMENT'])->count();
            $data['DISBURSED'] = self::whereHas('supplier', function($query) use($anchorId) { $query->where('anchor_id', $anchorId);})->where('status_id', config('lms.mst_status_invoice')['DISBURSED'])->count();
            $data['PAYMENT_SETTLED'] = self::whereHas('supplier', function($query) use($anchorId) { $query->where('anchor_id', $anchorId);})->where('status_id', config('lms.mst_status_invoice')['PAYMENT_SETTLED'])->count();
            $data['REJECT'] = self::whereHas('supplier', function($query) use($anchorId) { $query->where('anchor_id', $anchorId);})->where('status_id', config('lms.mst_status_invoice')['REJECT'])->count();
            $data['EXCEPTION_CASE'] = self::whereHas('supplier', function($query) use($anchorId) { $query->where('anchor_id', $anchorId);})->where('status_id', config('lms.mst_status_invoice')['EXCEPTION_CASE'])->count();
        }else{
            $data['PENDING'] = self::where('status_id', config('lms.mst_status_invoice')['PENDING'])->count();
            $data['APPROVED'] = self::where('status_id', config('lms.mst_status_invoice')['APPROVED'])->count();
            $data['DISBURSMENT_QUE'] = self::where('status_id', config('lms.mst_status_invoice')['DISBURSMENT_QUE'])->count();
            $data['SENT_TO_BANK'] = self::where('status_id', config('lms.mst_status_invoice')['SENT_TO_BANK'])->count();
            $data['FAILED_DISBURSMENT'] = self::where('status_id', config('lms.mst_status_invoice')['FAILED_DISBURSMENT'])->count();
            $data['DISBURSED'] = self::where('status_id', config('lms.mst_status_invoice')['DISBURSED'])->count();
            $data['PAYMENT_SETTLED'] = self::where('status_id', config('lms.mst_status_invoice')['PAYMENT_SETTLED'])->count();
            $data['REJECT'] = self::where('status_id', config('lms.mst_status_invoice')['REJECT'])->count();
            $data['EXCEPTION_CASE'] = self::where('status_id', config('lms.mst_status_invoice')['EXCEPTION_CASE'])->count();
        }
        return $data;
    }
    
    /* update invoice amount with statusid  */
    public static function updateInvoiceAmountWithStausId($attributes)
    {
        $invoiceId  =    $attributes['invoice_id'];
        $amount     =  str_replace(',','', $attributes['approve_invoice_amount']);  
        $comment    =    $attributes['comment'];
        $statusId    =    $attributes['status_id'];
        $updated_at  = Carbon::now()->toDateTimeString();
        $id = Auth::user()->user_id;    
        $result =  User::getSingleUserDetails($id);
        InvoiceStatusLog::saveInvoiceLogWithStatusId($invoiceId,$statusId,$amount,$comment);
        $invoice = self::find($invoiceId);
        $marginAmt = Helper::getOfferMarginAmtOfInvoiceAmt($invoice->prgm_offer_id, $amount);

        return  self::where(['invoice_id' => $invoiceId])->update([
            'invoice_approve_amount' => $amount,
            'status_update_time' => $updated_at,
            'updated_by' => $id,
            'invoice_margin_amount' => $marginAmt
        ]);
        
    }
    public static function getAllManageInvoice($request,$status)
    {
        $id = Auth::user()->user_id;
        $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
        $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
        if( $chkUser->id==11) {
            $res  = User::where('user_id',$id)->first();
            return self::with('program_offer')->where(['status_id' => $status,'anchor_id' => $res->anchor_id])->orderBy('updated_at', 'DESC');
        } else {
           return self::with('program_offer')->where('status_id',$status)->orderBy('updated_at', 'DESC');
        }
    }

    public static function updateInvoiceTenor($data, $invoiceId)
    {

        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        return  self::where(['invoice_id' => $invoiceId])->update($data);
        
    } 
    
    
    function appStatusLog()
    {
        return $this->hasMany('App\Inv\Repositories\Models\AppStatusLog','app_id','app_id')->where('status_id',50)->latest();
    }    
    
    function ucicUserUcic()
    {
    return $this->belongsTo('App\Inv\Repositories\Models\UcicUserUcic', 'app_id','app_id');  
    }
}