<?php

namespace App\Inv\Repositories\Models;
use DB;
use Carbon\Carbon;
use App\Inv\Repositories\Factory\Models\BaseModel;

class InvoiceActivityLog extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'invoice_activity_log';

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
                'activity_name',
                'created_by',
                'created_at'
          
    ];
    
  
      /* invoice activity log  */
      public static function  saveInvoiceActivityLog($invoice_id,$status_id,$activity_name,$created_by)
      {
          if($status_id!=0)
          {
              $res = DB::table('mst_status')->where(['status_type' => 4,'id' => $status_id])->first();
              $activity_name = $res->status_name;
          }
           $created_at  = Carbon::now(); 
           $arr  =  ['invoice_id' => $invoice_id,'status_id' =>$status_id,'activity_name' =>$activity_name,'created_by' => $created_by,'created_at' => $created_at ]; 
           return self::insert($arr);  
     
       }

}
