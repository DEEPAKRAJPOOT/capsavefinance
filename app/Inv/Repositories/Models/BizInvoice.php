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
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;

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
        'invoice_due_date',
        'invoice_date',
        'invoice_amount',
        'invoice_approve_amount',
        'file_id',
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
        return self::where(['invoice_id' => $invoiceId])->update(['status_id' => $status]);
       
    } 
    
    public static function updateInvoiceAmount($invoiceId,$amount)
    {
        return self::where(['invoice_id' => $invoiceId])->update(['invoice_approve_amount' => $amount]);
       
    } 
    
/* get invoice */    
  public static function getInvoice()
    {
       return Anchor::get();
     }   
     
     public static function getAllInvoice($request,$status)
     {
         
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
        else {
             $whr = [];
        }
       
                    return self::where('status_id',$status)->where($whr)->where(['created_by' => Auth::user()->user_id])->with(['anchor','supplier','userFile','program'])->get();
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
     
     function program()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\Program', 'program_id','prgm_id')->where(['status' => 1]);  
     
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

}