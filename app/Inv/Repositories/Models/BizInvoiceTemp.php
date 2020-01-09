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
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;

class BizInvoiceTemp extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'invoice_temp';

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
    
    
public static function saveBulkTempInvoice($arrInvoice)
    {
        $arrInvoiceVal = self::insert($arrInvoice);
        return ($arrInvoiceVal ?: false);
    } 
    
   public static function getTempInvoiceData($whr)
    {
       return self::where($whr)->get();
    }  
    
    public static function DeleteTempInvoice($whr)
    {
       return self::where($whr)->delete();
    }  
    
  public static function saveBulk($attributes)
    {
    //dd($attributes);
     $count = count($attributes['id']); 
        for ($i=0;$i< $count;$i++)  
     {   
          
            $updateTemp =  self::where('invoice_id',$attributes['id'][$i])
                    ->update(['invoice_no' => $attributes['invoice_no'][$i],
                'invoice_due_date' => ($attributes['invoice_due_date'][$i]) ? Carbon::createFromFormat('d/m/Y', $attributes['invoice_due_date'][$i])->format('Y-m-d') : '',
                'invoice_date' => ( $attributes['invoice_date'][$i]) ? Carbon::createFromFormat('d/m/Y',  $attributes['invoice_date'][$i])->format('Y-m-d') : '',
                'invoice_approve_amount' => $attributes['invoice_approve_amount'][$i]]
            );
            
            if($updateTemp)
            {
                
               $result =  self::where('invoice_id',$attributes['id'][$i])->first();
               $insert = BizInvoice::insert(['anchor_id' => $result->anchor_id,
                        'supplier_id' => $result->supplier_id,
                        'program_id' => $result->program_id,
                        'app_id'    => $result->app_id,
                        'biz_id'  => $result->biz_id,
                        'invoice_no' => $result->invoice_no,
                        'invoice_due_date' => $result->invoice_due_date,
                        'invoice_date' => $result->invoice_date,
                        'invoice_approve_amount' => $result->invoice_approve_amount,
                        'is_bulk_upload'    =>1,
                        'bulk_invoice_file'  => $result->bulk_invoice_file,
                        'remark' => $result->remark,
                        'file_id'  => $result->file_id,
                        'created_by' => $result->created_by,
                        'created_at' =>$result->created_at]);
            }
           
       }  
       return  $insert;
    }
     
     
}