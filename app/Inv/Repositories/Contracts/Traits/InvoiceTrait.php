<?php
namespace App\Inv\Repositories\Contracts\Traits;

use Auth;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Lms\InterestAccrual;
use App\Inv\Repositories\Models\Lms\InvoiceRepaymentTrail;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Models\BizPanGst;
use App\Helpers\ApportionmentHelper;
use App\Inv\Repositories\Models\Lms\RefundTransactions;

trait InvoiceTrait
{
    /**
     * Calculate Interest
     * 
     * @param float $principalAmt
     * @param float $interestRate
     * @param integer $tenorDays
     * 
     * @return mixed
     */
   public static function checkCsvFile($data)
   {
        $cusomer_id  =   $data['cusomer_id']; 
        $inv_no  =   $data['inv_no']; 
        $inv_date  =   $data['inv_date']; 
        $inv_due_date  =   $data['inv_due_date']; 
        $amount  =   $data['amount']; 
        $file_name  =   $data['file_name'];
        $user_id  =   $data['user_id'];
        $attr[]="";
        $invoice_due_date_validate  = self::validateDate($inv_due_date, $format = 'd-m-Y');
        $invoice_date_validate  = self::validateDate($inv_date, $format = 'd-m-Y');
        $res =  self::checkDuplicateInvoice($inv_no,$user_id);
        if($res)
        {
            $attr['status'] = 0;
            $attr['message']=  'Invoice No '.$inv_no.' already exits with '.$cusomer_id;
            return  $attr;    
        }
        if( $invoice_due_date_validate==false)
        {
            $attr['status'] = 0;
            $attr['message']=  'Please check the invoice date, It should be "dd-mm-yy" format';
            return  $attr;  

        }
        else if( $invoice_date_validate==false)
        {
             $attr['status'] = 0;
             $attr['message']=  'Please check the due invoice date, It Should be "dd-mm-yy" format';
             return  $attr;    
        } 
        else
        {
             $attr['status'] = 1;
             $attr['message']= true;
             return  $attr;    
    
        }
       
   }
   
      
    public static function checkDuplicateInvoice($invNo,$user_id)
    {
        
        return BizInvoice::where(['invoice_no' => $invNo,'supplier_id' => $user_id])->first();
    }  
        
       public static  function validateDate($date, $format = 'd-m-Y')
    { 
       
       return  $d = DateTime::createFromFormat($format, $date);
    
     }
     protected  function applicationSave($app_id,$biz_id)
    {
       try
       {   
            $date = Carbon::now();
            $id = Auth::user()->user_id;
            $app = Application::find($app_id);
            $obj =   $app->replicate();
            $obj->app_id = "";
            $obj->biz_id = $biz_id;
            $obj->created_by = $id;
            $obj->created_at = $date;
            $obj->save(); 
        return $obj->app_id;
       } catch (Exception $ex) {

       }
    }
   
}
