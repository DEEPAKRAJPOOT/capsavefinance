<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DateTime;
use DB;
use Auth;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\Anchor;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\Program;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\InvoiceStatusLog;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Models\Master\RoleUser;
use App\Inv\Repositories\Models\Master\Role;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;

class InvoiceBulkUpload extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'invoice_bulk_upload';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'invoice_bulk_upload_id';

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
        'invoice_id',
        'anchor_id',
        'supplier_id',
        'program_id',
        'prgm_offer_id',
        'app_id',
        'biz_id',
        'invoice_no',
        'tenor',
        'invoice_due_date',
        'invoice_date',
        'pay_calculation_on',
        'invoice_approve_amount',
        'limit_exceed',
        'comm_txt',
        'status',
        'status_id',
        'file_id',
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
       return  self::create($arrInvoice);
 
    } 
     public static function updateBulkUpload($attr)
    {    if($attr['invoice_id']!=null)
         {
            InvoiceStatusLog::saveInvoiceStatusLog($attr['invoice_id'],$attr['status_id']);
         }
       return  self::where('invoice_bulk_upload_id',$attr['invoice_bulk_upload_id'])->update(['invoice_id' => $attr['invoice_id'],'status' => $attr['status']]);
 
    } 
    
     public static function getSingleBulkInvoice($invoice_bulk_upload_id)
    {
       return  self::where(['invoice_bulk_upload_id' => $invoice_bulk_upload_id])->first();
 
    } 
      public static function getUserAllBulkInvoice()
    {
       $id = Auth::user()->user_id; 
       return  self::with(['user','anchor','supplier','program','lms_user','business'])->whereIn('status',[0,2])->where(['created_by' => $id,'supplier_id' => $id])->get();
 
    } 
    
    public static function getAllBulkInvoice()
    {
        $id = Auth::user()->user_id; 
        $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
        $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
        if( $chkUser->id==11)
        {
            $res  = User::where('user_id',$id)->first();
            return  self::with(['user','anchor','supplier','program','lms_user','business'])->whereIn('status',[0,2])->where(['created_by' => $id,'anchor_id' => $res->anchor_id])->get();
         }
        else 
       {
            return  self::with(['user','anchor','supplier','program','lms_user','business'])->whereIn('status',[0,2])->where(['created_by' => $id])->get();
        }
    
    } 
      function business()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\Business', 'biz_id','biz_id');  
    
     }
    function anchor()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\Anchor', 'anchor_id','anchor_id');  
    
     }
    
     function supplier()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\User', 'supplier_id','user_id')->whereIn('is_buyer',[1,2]); 
     
     }
     function user()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\User', 'created_by','user_id'); 
     
     }
     function program()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\Program', 'program_id','prgm_id');  
     
     }
     
       function lms_user()
     {
          return $this->belongsTo('App\Inv\Repositories\Models\LmsUser', 'supplier_id','user_id'); 
     
     }
    
    public static function DeleteTempInvoice($attr)
    {
       return self::where(['invoice_bulk_upload_id' => $attr['invoice_bulk_upload_id']])->delete();
    } 
     
    public static function saveBulk($attributes)
    {
     $id = Auth::user()->user_id;
     $count = count($attributes['id']); 
        for ($i=0;$i< $count;$i++)  
     {   
        $mytime = Carbon::now();
        $cDate = Carbon::parse($mytime)->format('Y/m/d');
        $invDate =  Carbon::createFromFormat('d/m/Y',  $attributes['invoice_date'][$i])->format('Y/m/d');
        $now = strtotime($cDate); // or your date as well
        $your_date = strtotime($invDate);
        $datediff = abs($now - $your_date);
        $tenor = $datediff / (60 * 60 * 24);
        
            $updateTemp =  self::where('invoice_id',$attributes['id'][$i])
                    ->update(['invoice_no' => $attributes['invoice_no'][$i],
                        'status' => 1,
                         'tenor' => $attributes['tenor'],
                'invoice_due_date' => ($attributes['invoice_due_date'][$i]) ? Carbon::createFromFormat('d/m/Y', $attributes['invoice_due_date'][$i])->format('Y-m-d') : '',
                'invoice_date' => ($attributes['invoice_date'][$i]) ? Carbon::createFromFormat('d/m/Y',  $attributes['invoice_date'][$i])->format('Y-m-d') : '',
                'invoice_approve_amount' => str_replace(',', '',$attributes['invoice_approve_amount'][$i])]
            );
            
            if($updateTemp)
            {
                $id = Auth::user()->user_id;
                $result =  self::where('invoice_id',$attributes['id'][$i])->first();
                $getPrgm  = Program::where(['prgm_id' => $result->program_id])->first(); 
                $customer  = 4;
                $expl  =  explode(",",$getPrgm->invoice_approval); 
                if($tenor > $attributes['tenor_old_invoice'])
                {
                    $status_id =  28;
                }
                else
                {
                    if(in_array($customer, $expl))  
                    {
                       $status_id =  8;
                    }
                     else if($getPrgm->invoice_approval==4)
                    {
                        $status_id = 8;   
                    }
                    else
                    {
                      $status_id = 7;
                    }
                }
          
               $data = new BizInvoice;
                       $data->anchor_id =  $result->anchor_id;
                        $data->supplier_id =  $result->supplier_id;
                        $data->program_id =  $result->program_id;
                        $data->app_id    =  $result->app_id;
                        $data->biz_id  =  $result->biz_id;
                        $data->invoice_no =  $result->invoice_no;
                        $data->tenor =  $result->tenor;
                        $data->invoice_due_date =  $result['invoice_due_date'];
                        $data->invoice_date =   $result['invoice_date'];
                        $data->pay_calculation_on =   $result['pay_calculation_on'];
                        $data->invoice_amount =  $result->invoice_approve_amount;
                        $data->invoice_approve_amount = $result->invoice_approve_amount;
                        $data->is_bulk_upload    =  1;
                        $data->batch_id  =  $result->batch_id;
                        $data->prgm_offer_id =   $attributes['prgm_offer_id'];
                        $data->remark =  $result->remark;
                        $data->status_id =  $status_id;	
                        $data->created_by =  $result->created_by;
                        $data->created_at =  $result->created_at;
               $insert = $data->save();
               InvoiceStatusLog::saveInvoiceStatusLog($data->invoice_id,$status_id);
            }
            
       }  
       return  $insert;
    }
                        
    /* two date diffrence  *///////
    function twoDateDiff($fdate,$tdate)
    {
            $curdate=strtotime($fdate);
            $mydate=strtotime($tdate);

            if($curdate > $mydate)
            {
               return 1;
            }
            else
            {
                return 0;
            }
    } 
     
   
}