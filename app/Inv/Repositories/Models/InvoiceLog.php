<?php

namespace App\Inv\Repositories\Models;
use DB;
use Auth;
use Carbon\Carbon;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Factory\Models\BaseModel;

class InvoiceLog extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'invoice_log';

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
                'invoice_amt',
                'comm_txt',
                'created_at',
                'created_by'
          
    ];
    
  
      /* invoice  status log  */
      public static function  saveInvoiceLog($invoice_id,$amount,$comment)
      {
         
           $id = Auth::user()->user_id;
           $created_at  = Carbon::now(); 
           $arr  =  ['invoice_id' => $invoice_id,'invoice_amt' => $amount,'comm_txt' =>$comment,'created_at' =>$created_at,'created_by' => $id]; 
           return  self::insert($arr);  
       }
    

}
