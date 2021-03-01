<?php

namespace App\Inv\Repositories\Models;
use DB;
use Auth;
use Carbon\Carbon;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\Master\Status;
class InvoiceStatusLog extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'invoice_status_log';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'invoice_log_id';

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
                'status_id',
                'invoice_amt',
                'comm_txt',
                'created_by',
                'updated_by',
                'created_at'
          
    ];
    
  
      /* invoice  status log  */
       public static function  saveInvoiceStatusLog($invoice_id,$status_id)
       {
           //$invlog = self::where(['invoice_id' => $invoice_id])->orderBy('invoice_log_id', 'desc')->first();
           //if ($invlog && $invlog->status_id != $status_id) {
            $created_at  = Carbon::now()->toDateTimeString();
            $id = Auth::user()->user_id;
            $arr  =  ['invoice_id' => $invoice_id,'status_id' =>$status_id,'created_at' =>$created_at,'created_by' => $id]; 
            return  self::insert($arr);  
           //}
       }
      public static function getAllActivityInvoiceLog($inv)
       {
           return self::with('status','user', 'invoice')->where(['invoice_id' => $inv])->orderBy('invoice_log_id', 'desc')->get();
       }
         /* invoice  status log  */
      public static function  saveInvoiceLog($invoice_id,$status_id,$amount,$comment)
      {
         
           $id = Auth::user()->user_id;
           $created_at  = Carbon::now(); 
           $arr  =  ['invoice_id' => $invoice_id,'status_id' => $status_id,'invoice_amt' => $amount,'comm_txt' =>$comment,'created_at' =>$created_at,'created_by' => $id]; 
           return  self::insert($arr);  
       }
      
       public function status()
       {
           return $this->belongsTo('App\Inv\Repositories\Models\Master\Status', 'status_id','id')->where('status_type',4);  
       }
       
       public function invoiceLog()
       {
           return $this->belongsTo('App\Inv\Repositories\Models\InvoiceLog','invoice_id','invoice_id');  
       }
       
      function user()
     {
       return $this->belongsTo('App\Inv\Repositories\Models\User','created_by','user_id');
     }
      function invoice()
     {
       return $this->belongsTo('App\Inv\Repositories\Models\BizInvoice','invoice_id','invoice_id');
     }

  /* update invoice amount with statusid  */
  public static function  saveInvoiceLogWithStatusId($invoice_id,$status_id,$amount,$comment)
  {

    $id = Auth::user()->user_id;
    $created_at  = Carbon::now(); 
    $arr  =  ['invoice_id' => $invoice_id,'status_id' => $status_id,'invoice_amt' => $amount,'comm_txt' =>$comment,'created_at' =>$created_at,'created_by' => $id]; 
    return  self::insert($arr);  
  }     
}
