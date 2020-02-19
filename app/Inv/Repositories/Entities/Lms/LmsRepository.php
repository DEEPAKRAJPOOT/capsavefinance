<?php

namespace App\Inv\Repositories\Entities\Lms;

use DB;
use Session;
use App\Inv\Repositories\Contracts\LmsInterface;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;
use App\Inv\Repositories\Models\LmsUser;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Models\ProgramCharges;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Lms\Charges;
use App\Inv\Repositories\Models\Lms\DisburseApiLog;
use App\Inv\Repositories\Models\Lms\TransType;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Lms\ChargesTransactions;
use App\Inv\Repositories\Models\Lms\InterestAccrual;
use App\Inv\Repositories\Models\Master\GstTax;
use App\Inv\Repositories\Models\Lms\InvoiceRepaymentTrail;

/**
 * Lms Repository class
 */
class LmsRepository extends BaseRepositories implements LmsInterface {

    use CommonRepositoryTraits;

    /**
     * Class constructor
     *
     * @return void
     */    
    public function __construct() {
    }

    /**
     * Create method
     *
     * @param array $attributes
     */
    protected function create(array $attributes) {        
    }

    /**
     * Update method
     *
     * @param array $attributes
     */
    protected function update(array $attributes, $id) {        
    }

    /**
     * Get all records method
     *
     * @param array $columns
     */
    public function all($columns = array('*')) {        
    }

    /**
     * Find method
     *
     * @param mixed $id
     * @param array $columns     
     */
    public function find($id, $columns = array('*')) {        
    }

    /**
     * Save or Update Disbursal Request
     * 
     * @param array $data
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function saveDisbursalRequest($data, $whereCondition=[])
    {
        return Disbursal::saveDisbursalRequest($data, $whereCondition);
    }
    
    /**
     * Save Transactions
     * 
     * @param array $transactions
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function saveTransaction($transactions)
    {
        return Transactions::saveTransaction($transactions);
    }

    /**
     * Save or Update Invoice Repayment
     * 
     * @param array $data
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function saveRepayment($data, $whereCondition=[])
    {
        return InvoiceRepaymentTrail::saveRepayment($data, $whereCondition);
    }

    /**
     * Save or Update Interest Accrual
     * 
     * @param array $data
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function saveInterestAccrual($data, $whereCondition=[])
    {
        return InterestAccrual::saveInterestAccrual($data, $whereCondition);
    }
    
    /**
     * Get Disbursal Requests
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function getDisbursalRequests($whereCondition=[])
    {
        return Disbursal::getDisbursalRequests($whereCondition);
    }
    
    /**
     * Get Transactions
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function getTransactions($whereCondition=[])
    {
        return Transactions::getTransactions($whereCondition);
    }
    
    /**
     * Get Repayments
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function getRepayments($whereCondition=[])
    {
        return InvoiceRepaymentTrail::getRepayments($whereCondition);
    }   
    

    public function getAllUserInvoice($userId)
    {
        return BizInvoice::getAllUserInvoice($userId);
    }
    
    public static function getAllUserInvoiceIds($userId)
    {
        return BizInvoice::getAllUserInvoiceIds($userId);
    }

    /**
     * Get Accrued Interest Data
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */    
    public function getAccruedInterestData($whereCondition=[])
    {
        return InterestAccrual::getAccruedInterestData($whereCondition);
    }
    
    /**
     * Get Program Offer Data
     * 
     * @param array $whereCondition
     * @return mixed
     */
    public function getProgramOffer($whereCondition=[])
    {
        return Disbursal::getProgramOffer($whereCondition);
    }

    
    public function getInvoices($invoiceIds)
    {
        return BizInvoice::whereIn('invoice_id', $invoiceIds)
               ->with(['program_offer','lms_user' , 'supplier.anchor_bank_details.bank', 'supplier_bank_detail.bank'])
               ->get();
    }  

    /**
     * Get Repayments
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function getInvoiceSupplier($invoiceIds)
    {
        return BizInvoice::groupBy('supplier_id')
                ->whereIn('invoice_id', $invoiceIds)
                ->pluck('supplier_id');
    }    

    /**
     * Get Repayments
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function updateInvoiceStatus($invoiceId, $status)
    {
        return BizInvoice::where('invoice_id', $invoiceId)
                ->update(['status_id' => $status]);
    }    

    /**
     * Get Sum of Accrued Interest
     *      
     * @param array $whereCond
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function sumAccruedInterest($whereCond) 
    {        
        return InterestAccrual::sumAccruedInterest($whereCond);
    }    


    /**
     * create Disburse Api Log
     */
    public static function createDisburseApi($data)
    {
        return DisburseApiLog::create($data);
    }

     /**
     * Get Repayments
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function updateDisburse($data, $disbursalId)
    {
        return Disbursal::where('disbursal_id', $disbursalId)
                ->update($data);
    }          
     
     /**
     * Get Repayments
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getVirtualAccIdByUserId($userId)
    {
        return LmsUser::where('user_id', $userId)
                ->pluck('virtual_acc_id')->first();
    } 
    
    /****
     * get trans  type
     */
      public static function getTrnasType($whr)
    {
        try{
            return Charges::getTransData($whr);
        } catch (Exception $ex) {
           return $ex;
        }
        
               
    }  
    /****
     * get trans  type
     */
      public static function getProgramUser($userId)
    {
       try
       {
          return User::getProgramUser($userId); 
       } catch (Exception $ex) {
          return $ex;
       }
       
               
    }  
    
    public static function getUserDetails($uid)
    {
       try
       {
          return User::getUserDetails($uid); 
       } catch (Exception $ex) {
          return $ex;
       }
       
               
    }    
      public static function getSingleChargeAmount($attr)
    {
       try
       {
          return ProgramCharges::getSingleChargeAmount($attr); 
       } catch (Exception $ex) {
          return $ex;
       }
       
               
    }   
    
      public static function saveCharge($attr)
    {
       try
       {
          return Transactions::saveCharge($attr); 
       } catch (Exception $ex) {
          return $ex;
       }
       
               
    }   
    
    public static function getAllTransCharges()
    {
        try
       {
          return ChargesTransactions::getAllTransCharges(); 
       } catch (Exception $ex) {
          return $ex;
       }
        
    }
      public static function saveChargeTrans($attr)
    {
       try
       {
          return ChargesTransactions::saveChargeTrans($attr); 
       } catch (Exception $ex) {
          return $ex;
       }
       
               
    }    
     
      public static function getAllUserChargeTransaction()
    {
       try
       {
          return Transactions::getAllUserChargeTransaction(); 
       } catch (Exception $ex) {
          return $ex;
       }
       
               
    }    
      public static function getTransName($attr)
    {
       try
       {
          return ProgramCharges::getTransName($attr); 
       } catch (Exception $ex) {
          return $ex;
       }
       
               
    }    
    /**
     * Get program offer limit amount  //
     *      
     * @param array $whereCondition
     * @param array $data
     * 
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
      public static function getLimitAmount($attr)
    {
       try
       {
          return AppProgramOffer::getLimitAmount($attr); 
       } catch (Exception $ex) {
          return $ex;
       }
       
               
    }     
    
    /**
     * Update Transactions
     *      
     * @param array $whereCondition
     * @param array $data
     * 
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function updateTransaction($whereCondition, $data)
    {
        return Transactions::updateTransaction($whereCondition, $data);
    }
    
    /** 
     * @Author: Rent Aplha
     * @Date: 2020-02-17 14:53:10 
     * @Desc:  
     */    
    public static function getManualTranType(){        
     $result=TransType::getManualTranType();
     return  $result? $result:false;
    }
      /** 
     * @Author: Rent Aplha
     * @Date: 2020-02-17 14:53:10 
     * @Desc:  
     */    
    public static function getActiveGST(){        
        $result=GstTax::getActiveGST();
        return  $result? $result:false;
       }

       /** 
        * @Author: Rent Alpha
        * @Date: 2020-02-18 13:04:19 
        * @Desc:  
        */       
       public function getAllLmsUser(){
        $result=LmsUser::getLmsUser();
        return  $result? $result:false;
       }            
}
