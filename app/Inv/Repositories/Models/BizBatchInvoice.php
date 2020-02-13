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

class BizBatchInvoice extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'batch_invoice';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'batch_invoice_id';

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
        'batch_id',
        'file_no',
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
    public static function saveBatchInvoice($path)
    {
         $date = Carbon::now();
         $id = Auth::user()->user_id;
         $batch_id =  self::createBatchNumber($date);
         $arrInvoiceVal = self::create(['batch_id' =>$batch_id,'file_no' => $path,'created_by' =>  $id,
                'created_at' =>  $date]);
        return ($arrInvoiceVal->batch_id ?: false);
    } 
    
   public static function createBatchNumber($date)
    {
          return $unique_code = $date->format('YmdHisu');
    }
    

}