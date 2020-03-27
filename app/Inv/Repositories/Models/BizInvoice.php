<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DateTime;
use Auth;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\Anchor;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\Program;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\Lms\InvoiceRepaymentTrail;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Models\InvoiceActivityLog;

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
        'invoice_due_date',
        'invoice_date',
        'pay_calculation_on',
        'invoice_amount',
        'invoice_approve_amount',
        'prgm_offer_id',
        'file_id',
        'status_id',
        'remark',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
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
        $arrInvoiceVal = self::create($arrInvoice);
        return ($arrInvoiceVal->invoice_id ?: false);
    } 
    
    
public static function saveBulkInvoice($arrInvoice)
    {
        $arrInvoiceVal = self::insert($arrInvoice);
        return ($arrInvoiceVal ?: false);
    } 
    
        
public static function updateInvoice($invoiceId,$status)
    {
        $id = Auth::user()->user_id;
        InvoiceActivityLog::saveInvoiceActivityLog($invoiceId,$status,null,$id,null);
        return self::where(['invoice_id' => $invoiceId])->update(['status_id' => $status]);
       
    } 
    
    public static function updateInvoiceAmount($attributes)
    {
        $invoiceId  =    $attributes['invoice_id'];
        $amount     =  str_replace(',','', $attributes['approve_invoice_amount']);  
        $comment    =    $attributes['comment'];
        $id = Auth::user()->user_id;
        $result =  User::getSingleUserDetails($id);
        $name =  "Update by ".$result->f_name." ".$result->l_name;
        InvoiceActivityLog::saveInvoiceActivityLog($invoiceId,0,$comment,$id,null);
        InvoiceActivityLog::saveInvoiceActivityLog($invoiceId,0,$name,null,$id);
        return self::where(['invoice_id' => $invoiceId])->update(['invoice_approve_amount' => $amount]);
       
    } 
    
    public static function getDisbursedAmount($invid)
    {
       return self::with(['disbursal','invoicePayment'])->where('invoice_id',$invid)->first();
    }

    function invoicePayment()
     {
          return $this->hasMany('App\Inv\Repositories\Models\Lms\InvoiceRepaymentTrail', 'invoice_id','invoice_id');  
     
     }
    
    /* get anchor */    
    public static function getInvoice()
    {
       return Anchor::get();
     }   
  
     public static function getAllInvoice($request,$status)
     {
      
      /*  $whr = [];
         
         if($request->get('supplier_id')!='' && $request->get('biz_id')=='')
         {
             $whr= ['anchor_id' => $request->get('anchor_id'),'supplier_id' => $request->get('supplier_id')];
         }
         else if($request->get('supplier_id')!='' && $request->get('biz_id')!='' && $request->get('anchor_id')!='')
         {
                 $whr = ['biz_id' => $request->get('biz_id'),'anchor_id' => $request->get('anchor_id'),'supplier_id' => $request->get('supplier_id')];
          
         }
          else if($request->get('biz_id')!='' && $request->get('anchor_id')!='' && $request->get('supplier_id')=='')
         {
                 $whr = ['biz_id' => $request->get('biz_id'),'anchor_id' => $request->get('anchor_id')];
          
         }
           else if($request->get('supplier_id')=='' && $request->get('biz_id')!='' && $request->get('anchor_id')=='')
         {
                 $whr = ['biz_id' => $request->get('biz_id')];
          
         }
           else if($request->get('biz_id')=='' && $request->get('anchor_id')!='' && $request->get('supplier_id')=='')
         {
                 $whr = ['anchor_id' => $request->get('anchor_id')];
          
         }
          
       
         if($request->get('app_id')!=''){
             $whr['app_id']= $request->get('app_id');
        }  */ 
        //backend_get_invoice

        return self::where('status_id',$status)->with(['business','anchor','supplier','userFile','program','program_offer','user','disbursal'])->orderBy('invoice_id', 'DESC');
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

     function disbursal()
     {
          return $this->hasOne('App\Inv\Repositories\Models\Lms\Disbursal', 'invoice_id','invoice_id');  
     
     }
    
     function supplier_bank_detail()
     {
          return $this->hasOne('App\Inv\Repositories\Models\UserBankAccount', 'user_id', 'supplier_id')->where(['is_default' => 1, 'is_active' => 1]);  
     
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
        
        return self::with('mstStatus')->where(['supplier_id' => $user_id]);
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
        return self::with('app.acceptedOffer')
            ->whereHas('disbursal', function($query) use ($data) {
                    $query->where($data);
                })
            ->where('status_id', 10)
            ->get();
    } 
    
      function user()
     {
          return $this->hasOne('App\Inv\Repositories\Models\User','user_id');  
     
     }
}