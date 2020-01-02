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
        'invoice_date',
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
/* get invoice */    
  public static function getInvoice()
    {
       return Anchor::get();
     }   
     
     public static function getAllInvoice($request)
     {
         
         if($request->get('supplier_id')!='' && $request->get('biz_id')=='')
         {
             $whr = ['anchor_id' => $request->get('anchor_id'),'supplier_id' => $request->get('supplier_id')];
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
       
                    return self::where($whr)->with('anchor')->where(['created_by' => Auth::user()->user_id])->with('supplier')->with('program')->get();
     } 
     
     function anchor()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\Anchor', 'anchor_id','anchor_id');  
    
     }
     
     function supplier()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\User', 'supplier_id','user_id')->whereIn('is_buyer',[1,2]); 
     
     }
     
     function program()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\Program', 'program_id','prgm_id')->where(['status' => 1]);  
     
     }
        function anchorOne()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\Anchor', 'anchor_id','anchor_id');  
    
     }
    public static function getUser($uid)
    {
       return User::whereIn('is_buyer',[1,2])->where('user_id',$uid)->first();
    }   
    
     public static function getUserBehalfAnchor($uid)
    {
   
       return User::whereIn('is_buyer',[1,2])->where('anchor_id',$uid)->get();
    }   
    
    public static function getAnchor($aid)
    {
       return Anchor::where('anchor_id',$aid)->first();
    }    
     
     public static function getProgram($aid)
    {
       return Program::where(['status' => 1,'anchor_id' =>$aid])->get();
     }    
      
     public static function getAllAnchor()
    {
         
       return self::with('anchorOne')->get();
    }  
     
     public static function getBusinessName()
     {
        return self::with('business')->where(['created_by' => Auth::user()->user_id])->get();
     }   
     
     function Business()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\Business', 'biz_id','biz_id');  
     
     } 
     
     
}