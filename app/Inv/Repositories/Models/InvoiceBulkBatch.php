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

class InvoiceBulkBatch extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'invoice_bulk_batch';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'invoice_bulk_batch_id';

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
        'batch_no',
        'file_id',
        'parent_bulk_batch_id',
        'type_id',
        'invoice_zip_file_name',
        'created_by',
        'created_at'
    ];

    
    /**
     * update user details
     *
     * @param integer $user_id     user id
     * @param array   $arrUserData user data
     *
     * @return boolean
     */
    public static function saveInvoiceBatch($res)
    {   
        $date = Carbon::now();
        $id = Auth::user()->user_id;
        return  self::create(['batch_no' =>$res->batch_no,'file_id' =>$res->file_id,'type_id' => 1,'invoice_zip_file_name' =>$res->file_name,'created_by' =>  $id,
               'created_at' =>  $date]);
    }
    
   public static function saveInvoiceZipBatch($res)
    {   
        $date = Carbon::now();
        $id = Auth::user()->user_id;
        return  self::create(['batch_no' =>$res['batch_no'],'type_id' => 2,'parent_bulk_batch_id' => $res['parent_bulk_batch_id'],'invoice_zip_file_name' =>$res['file_name'],'created_by' =>  $id,
               'created_at' =>  $date]);
    }
      
 
}