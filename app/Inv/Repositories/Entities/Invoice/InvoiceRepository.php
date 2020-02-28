<?php

namespace App\Inv\Repositories\Entities\Invoice;
use DB;
use Session;
use App\Inv\Repositories\Contracts\InvoiceInterface;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Models\User as UserModel;
use App\Inv\Repositories\Models\BizInvoice as InvoiceModel;
use App\Inv\Repositories\Models\BizInvoiceTemp as TempInvoiceModel;
use App\Inv\Repositories\Models\BizBatchInvoice as BizBatchInvoice;
use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;
use App\Inv\Repositories\Models\Lms\InvoiceRepaymentTrail;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\ExcelPaymentTemp;
use App\Inv\Repositories\Models\AppProgramLimit;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\Anchor;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Models\InvoiceActivityLog;
//


class InvoiceRepository extends BaseRepositories implements InvoiceInterface
{
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
    *create business information details
    * @param mixed $userId
    * @param array $attributes     
    */
    public function save($attributes = [])
    {
       
        /**
         * Check Data is Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions('Please send an array');
        }

        /**
         * Check Data is not blank
         */
        if (empty($attributes)) {
            throw new BlankDataExceptions('No Data Found');
        }

        return InvoiceModel::saveInvoice($attributes);
    }
    
     public function saveBulkTempInvoice($attributes = [])
    {
       
        /**
         * Check Data is Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions('Please send an array');
        }

        /**
         * Check Data is not blank
         */
        if (empty($attributes)) {
            throw new BlankDataExceptions('No Data Found');
        }

        return TempInvoiceModel::saveBulkTempInvoice($attributes);
    }
    
       public function saveBulkInvoice($attributes = [])
    {
       
        /**
         * Check Data is Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions('Please send an array');
        }

        /**
         * Check Data is not blank
         */
        if (empty($attributes)) {
            throw new BlankDataExceptions('No Data Found');
        }

        return InvoiceModel::saveBulkInvoice($attributes);
    }
    
        public function getTempInvoiceData($attributes = [])
    {
       
        /**
         * Check Data is Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions('Please send an array');
        }

        /**
         * Check Data is not blank
         */
        if (empty($attributes)) {
            throw new BlankDataExceptions('No Data Found');
        }

        return TempInvoiceModel::getTempInvoiceData($attributes);
    }
    
    
      public function DeleteTempInvoice($attributes = [])
    {
       
        /**
         * Check Data is Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions('Please send an array');
        }

        /**
         * Check Data is not blank
         */
        if (empty($attributes)) {
            throw new BlankDataExceptions('No Data Found');
        }

       return TempInvoiceModel::DeleteTempInvoice($attributes);  
    }
    
     /* save invoice activity log  */
      public function saveInvoiceActivityLog($invoice_id,$status_id=0,$activity_name,$created_by,$updatedBy)
    {
       
         try
        {
          return InvoiceActivityLog::saveInvoiceActivityLog($invoice_id,$status_id,$activity_name,$created_by,$updatedBy);  
        } catch (Exception $ex) {
           return $ex;
        }

       
    }
    
      public function DeleteSingleTempInvoice($attributes = [])
    {
       
        /**
         * Check Data is Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions('Please send an array');
        }

        /**
         * Check Data is not blank
         */
        if (empty($attributes)) {
            throw new BlankDataExceptions('No Data Found');
        }

       return TempInvoiceModel::DeleteSingleTempInvoice($attributes);  
    }
    
        public function saveBulk($attributes = [])
    {
       
        /**
         * Check Data is Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions('Please send an array');
        }

        /**
         * Check Data is not blank
         */
        if (empty($attributes)) {
            throw new BlankDataExceptions('No Data Found');
        }

       return TempInvoiceModel::saveBulk($attributes);  
    }
    
       public function saveRepayment($attributes = [])
    {
       
        /**
         * Check Data is Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions('Please send an array');
        }

        /**
         * Check Data is not blank
         */
        if (empty($attributes)) {
            throw new BlankDataExceptions('No Data Found');
        }

       return InvoiceRepaymentTrail::saveRepay($attributes);  
    } 
    
    //////////////////update invoice repayment/////////////
    
      public function updateRepayment($attributes = [])
    {
       
        /**
         * Check Data is Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions('Please send an array');
        }

        /**
         * Check Data is not blank
         */
        if (empty($attributes)) {
            throw new BlankDataExceptions('No Data Found');
        }

       return Disbursal::updateRepayment($attributes);  
    }  
    /********* save excel bulk format //////////////
     */
     public function insertExcelTrans($attributes = [])
    {
       
        /**
         * Check Data is Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions('Please send an array');
        }

        /**
         * Check Data is not blank
         */
        if (empty($attributes)) {
            throw new BlankDataExceptions('No Data Found');
        }

       return ExcelPaymentTemp::insertExcelTrans($attributes);  
    }  
    ////////////* get excel temp data *********/
      public function getExcelTrans($attributes = [])
    {
       
        /**
         * Check Data is Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions('Please send an array');
        }

        /**
         * Check Data is not blank
         */
        if (empty($attributes)) {
            throw new BlankDataExceptions('No Data Found');
        }

       return ExcelPaymentTemp::getExcelTrans($attributes);  
    }  
    ///////////////* delete excel trans  ************/
   
     public function deleteExcelTrans($attributes = [])
    {
       
        /**
         * Check Data is Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions('Please send an array');
        }

        /**
         * Check Data is not blank
         */
        if (empty($attributes)) {
            throw new BlankDataExceptions('No Data Found');
        }

       return ExcelPaymentTemp::deleteExcelTrans($attributes);  
    }  
    /* get all bulk transaction      */
    /* created by gajendra chauhan  */
    function  getAllManualTransaction()
    {
       try
       {
           return Transactions::getAllManualTransaction();  
       } catch (Exception $ex) {
          return $ex;
       }
    }
    
     /* get all save bulk transaction      */
    /* created by gajendra chauhan  */
        public function saveRepaymentTrans($attributes = [])
    {
       
        /**
         * Check Data is Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions('Please send an array');
        }

        /**
         * Check Data is not blank
         */
        if (empty($attributes)) {
            throw new BlankDataExceptions('No Data Found');
        }

       return Transactions::saveRepaymentTrans($attributes);  
    }
    
     public function getCustomerId($uid)
    {
        try
        {
           return Disbursal::getCustomerId($uid);  
        } catch (Exception $ex) {
           return $ex;
        }
         
    }
    
    public function getDisburseCustomerId()
    {
        try
        {
           return Disbursal::getDisburseCustomerId();  
        } catch (Exception $ex) {
           return $ex;
        }
         
    } 
    
     public function singleRepayment($id,$repayment)
    {
        try
        {
           return Disbursal::singleRepayment($id,$repayment);  
        } catch (Exception $ex) {
           return $ex;
        }
    }
    
     public function getDisbursedAmount($invid)
    {
        try
        {
           return InvoiceModel::getDisbursedAmount($invid);  
        } catch (Exception $ex) {
           return $ex;
        }
         
    }
    
     public function getRepaymentAmount($uid)
     {
          try
        {
           return Disbursal::getRepaymentAmount($uid);  
        } catch (Exception $ex) {
           return $ex;
        }
     }
     
    public function saveBatchNo($path)
    {
        try
        {
           return BizBatchInvoice::saveBatchInvoice($path);  
        } catch (Exception $ex) {
           return $ex;
        }
         
    }
    
    public function getInvoice()
    {
        try
        {
           return InvoiceModel::getInvoice();  
        } catch (Exception $ex) {
           return $ex;
        }
         
    }
    
    public function getAllInvoice($request,$status)
    {
        try
        {
           return InvoiceModel::getAllInvoice($request,$status);  
        } catch (Exception $ex) {
           return $ex;
        }
         
    } 
    
    public function getAllActivityInvoiceLog($invId)
    {
        try
        {
           return InvoiceActivityLog::getAllActivityInvoiceLog($invId);  
        } catch (Exception $ex) {
           return $ex;
        }
         
    }   
    
     public function updateInvoiceAmount($attributes)
    {
        try
        {
           return InvoiceModel::updateInvoiceAmount($attributes);  
        } catch (Exception $ex) {
           return $ex;
        }
         
    }  
    
    
    
     public function updateFileId($arr,$invoiceId)
    {
        try
        {
           return InvoiceModel::updateFileId($arr,$invoiceId);  
        } catch (Exception $ex) {
           return $ex;
        }
         
    } 
     
    
    public function getBusinessName()
    {
        try
        {
           return AppProgramLimit::getBusinessName();  
        } catch (Exception $ex) {
           return $ex;
        }
         
    }  
    
    
      public function getAllAnchor()
    {
        try
        {
           return AppProgramLimit::getAllAnchor();  
        } catch (Exception $ex) {
           return $ex;
        }
         
    } 
    
    public function getUser($uid)
    {
       
        try
        {
          return InvoiceModel::getUser($uid);
        } catch (Exception $ex) {
           return $ex;
        } 
    }
    
     public function getUserBehalfAnchor($uid)
    {
       
        try
        {
          return AppProgramLimit::getUserBehalfAnchor($uid);
        } catch (Exception $ex) {
           return $ex;
        } 
    }
    
     public function getAnchor($aid)
    {
       
        try
        {
          return InvoiceModel::getAnchor($aid);
        } catch (Exception $ex) {
           return $ex;
        } 
    }
    
     public function getLimitAnchor($aid)
    {
       
        try
        {
          return AppProgramLimit::getLimitAnchor($aid);
        } catch (Exception $ex) {
           return $ex;
        } 
    }
    
    public function getProgram($aid)
    {
     
        try
        {
          return BizInvoice::getProgram($aid);
        } catch (Exception $ex) {
           return $ex;
        } 
    }
    
     public function getProgramForLimit($aid)
    {
     
        try
        {
          return BizInvoice::getProgramForLimit($aid);
        } catch (Exception $ex) {
           return $ex;
        } 
    }
    
      public function getOfferForLimit($oid)
    {
     
        try
        {
          return AppProgramOffer::getOfferForLimit($oid);
        } catch (Exception $ex) {
           return $ex;
        } 
    }
    

 public function getLimitProgram($aid)
    {
     
        try
        {
          return AppProgramLimit::getLimitProgram($aid);
        } catch (Exception $ex) {
           return $ex;
        } 
    }
    
    public function updateInvoice($invId,$status)
    {
       try
        {
          return BizInvoice::updateInvoice($invId,$status);
        } catch (Exception $ex) {
           return $ex;
        } 
          
    }
    
   public function getSingleInvoice($invId)
    {
       try
        {
          return BizInvoice::getSingleInvoice($invId);
        } catch (Exception $ex) {
           return $ex;
        } 
          
    }
     public function geAnchortLimitProgram($aid)
    {
     
        try
        {
          return AppProgramLimit::geAnchortLimitProgram($aid);
        } catch (Exception $ex) {
           return $ex;
        } 
    }
    
   public function getLimitAllAnchor()
    {
     
        try
        {
          return AppProgramLimit::getLimitAllAnchor();
        } catch (Exception $ex) {
           return $ex;
        } 
    }  
    
   public function getLimitSupplier($pid)
    {
     
        try
        {
          return AppProgramLimit::getLimitSupplier($pid);
        } catch (Exception $ex) {
           return $ex;
        } 
    }    
     public function getSingleLimit($aid)
    {
     
        try
        {
          return AppProgramOffer::getSingleLimit($aid);
        } catch (Exception $ex) {
           return $ex;
        } 
    }


 public function getSingleApp($uid)
    {
     
        try
        {
          return AppProgramLimit::getSingleApp($uid);
        } catch (Exception $ex) {
           return $ex;
        } 
    }

    /**
     * get invoice Data
     * 
     * @param type $where Array 
     * @param type $select Array
     * @return type mixed
     */
    public function getInvoiceData($where, $select)
    {
        return InvoiceModel::getInvoiceData($where, $select);
    }
    
    public function checkDuplicateInvoice($invoiceNo)
    {
        
          return BizInvoice::checkDuplicateInvoice($invoiceNo);
    }

}
