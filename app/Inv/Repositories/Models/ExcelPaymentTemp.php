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
use App\Inv\Repositories\Models\InvoiceActivityLog;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;

class ExcelPaymentTemp extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'excel_trans_temp';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'payment_temp_id';

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
        'user_id',
        'payment_date',
        'amount',
        'virtual_account_no',
        'payment_ref_no',
        'remark',
        'status',
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
    public static function insertExcelTrans($arr)
    {
        return  self::insert($arr);
       
    } 
     public static function deleteExcelTrans($arr)
    {
        return self::where($arr)->delete();
    } 
    
   
     
}