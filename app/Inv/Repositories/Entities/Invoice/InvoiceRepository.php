<?php

namespace App\Inv\Repositories\Entities\Invoice;
use DB;
use Session;
use App\Inv\Repositories\Contracts\InvoiceInterface;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Models\User as UserModel;
use App\Inv\Repositories\Models\BizInvoice as InvoiceModel;
use App\Inv\Repositories\Models\BizInvoiceTemp as TempInvoiceModel;
use App\Inv\Repositories\Models\InvoiceBulkBatch;
use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;
use App\Inv\Repositories\Models\Lms\InvoiceRepaymentTrail;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\ExcelPaymentTemp;
use App\Inv\Repositories\Models\AppProgramLimit;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\Anchor;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Models\LmsUser;
use App\Inv\Repositories\Models\Payment;
use App\Inv\Repositories\Models\AppLimit;
use App\Inv\Repositories\Models\AppOfferAdhocLimit;
use App\Inv\Repositories\Models\InvoiceBulkUpload;
use App\Inv\Repositories\Models\InvoiceStatusLog;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\Lms\DisbursalBatch;

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
    
    public function saveInvoice($attributes = [])
    {
       
        try
        {
            return InvoiceBulkUpload::saveInvoice($attributes); 
        } catch (Exception $ex) {
            return $ex;
        }
       
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
      public function saveInvoiceStatusLog($invoice_id,$status_id)
    {
       
         try
        {
          return InvoiceStatusLog::saveInvoiceStatusLog($invoice_id,$status_id);  
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

       return InvoiceBulkUpload::DeleteTempInvoice($attributes);  
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
           return Payment::getAllManualTransaction();  
       } catch (Exception $ex) {
          return $ex;
       }
    }
    
    function  getPaymentById($paymentId)
    {
       try
       {
           return Payment::find($paymentId);  
       } catch (Exception $ex) {
          return $ex;
       }
    }

    public function updatePayment($data = [], $paymentId){
        if (!is_array($paymentId)) {
            return Payment::where('payment_id', $paymentId)
                ->update($data);
        } else {
            return Payment::whereIn('payment_id', $paymentId)
                    ->update($data);
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
    
       public function getBusinessNameApp($status_id)
    {
        try
        {
           return InvoiceModel::getBusinessNameApp($status_id);  
        } catch (Exception $ex) {
           return $ex;
        }
         
    }
      public function getUserBusinessNameApp($status_id)
    {
        try
        {
           return InvoiceModel::getUserBusinessNameApp($status_id);  
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
     public function getUserAllInvoice($request)
    {
        try
        {
           return InvoiceModel::getUserAllInvoice($request);  
        } catch (Exception $ex) {
           return $ex;
        }
         
    } 
    public function getAllActivityInvoiceLog($invId)
    {
        try
        {
           return InvoiceStatusLog::getAllActivityInvoiceLog($invId);  
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
    
   public function getAllInvoiceAnchor($status_id)
    {
       
        try
        {
          return InvoiceModel::getAllInvoiceAnchor($status_id);
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
      public function getUserBehalfApplication($attr)
    {
       
        try
        {
          return Application::getUserBehalfApplication($attr);
        } catch (Exception $ex) {
           return $ex;
        } 
    }
      public function getLmsUserBehalfApplication($attr)
    {
       
        try
        {
          return Application::getLmsUserBehalfApplication($attr);
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
    
      public function getProgramForAppLimit($pid,$appId)
    {
     
        try
        {
          return BizInvoice::getProgramForAppLimit($pid,$appId);
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
    

 public function getProgramLmsSingleList($aid)
    {
     
        try
        {
          return AppProgramLimit::getProgramLmsSingleList($aid);
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
    public function getLimitProgram($aid)
    {
     
        try
        {
          return AppProgramLimit::getLimitProgram($aid);
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
    
   public function getLmsLimitAllAnchor()
    {
     
        try
        {
          return AppProgramLimit::getLmsLimitAllAnchor();
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
    
    public function getSingleAnchorDataByAppId($appId)
    {
     
        try
        {
          return Application::getSingleAnchorDataByAppId($appId);
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
    public function getInvoiceData($where, $select = ['*'])
    {
        return InvoiceModel::getInvoiceData($where, $select);
    }
    
    public function checkDuplicateInvoice($invoiceNo,$user_id)
    {
        
          return BizInvoice::checkDuplicateInvoice($invoiceNo,$user_id);
    }
    
    public function getUserWiseInvoiceData($user_id)
    {
        try
        {
             return BizInvoice::getUserWiseInvoiceData($user_id);
        } catch (Exception $ex) 
        {
             return $ex;
        }
         
    }  

    public function  getPaymentAdvice()
    {
       try
       {
           return Transactions::select('transactions.*', 'users.f_name','users.m_name','users.l_name','req.req_id')
                            ->join('users', 'transactions.user_id', '=', 'users.user_id')
                            ->leftJoin('lms_approval_request as req', 'req.trans_id', '=', 'transactions.trans_id')
                            ->where('trans_type','=', 17)
                            ->orderBy('trans_id', 'asc');  
       } catch (Exception $ex) {
          return $ex;
       }
    }

    public function findTransById($trans_id)
    {
        if (empty($trans_id) || !ctype_digit($trans_id)) {
        throw new BlankDataExceptions('No Data Found');
        }
        $result = Transactions::find($trans_id);
        return $result ?: false;
    }
    
    
    /**
     * Get a customer model by id
     *
     * @param integer $userId
     *
     * @return boolean
     *
     * @since 0.1
     */
    public function getCustomerDetail($userId)
    {
        $result = UserModel::getCustomerDetail((int) $userId);

        return $result ?: false;
    }

    public function getProgramOfferByPrgmId($prgmId)
    {
        return AppProgramOffer::getProgramOfferByPrgmId($prgmId);
    } 
    
    public function getBulkProgramOfferByPrgmId($attr)
    {
       try
       {
         return AppProgramOffer::getBulkProgramOfferByPrgmId($attr);  
       } catch (Exception $ex) {
         return $ex;
       }
    } 
    
    public function getUserProgramOfferByPrgmId($prgmId,$user_id)
    {
        return AppProgramOffer::getUserProgramOfferByPrgmId($prgmId,$user_id);
    }  
    
     public function getBizAnchor($attributes = [])
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

       return BizInvoice::getBizAnchor($attributes);  
    }  
    
      public function getUserBizAnchor($attributes = [])
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

       return BizInvoice::getUserBizAnchor($attributes);  
    }  
    public function getTenor($attributes = [])
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

       return AppProgramOffer::getTenor($attributes);  
    }  
  public function getAmountOfferLimit($attributes = [])
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

       return AppProgramOffer::getAmountOfferLimit($attributes);  
    } 
   public function getRemainAmount($attributes = [])
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

       return BizInvoice::getRemainAmount($attributes);  
    }  
    public function saveFinalInvoice($attributes)
    {
         try
        {
             return BizInvoice::saveFinalInvoice($attributes);  
        } catch (Exception $ex) {
             return $ex;
        }

       
    }   
     public function saveInvoiceBatch($attributes)
    {
       
        try
        {
             return InvoiceBulkBatch::saveInvoiceBatch($attributes);  
        } catch (Exception $ex) {
             return $ex;
        }
      
    }  
   
     public function saveInvoiceZipBatch($attributes)
    {
       
        try
        {
             return InvoiceBulkBatch::saveInvoiceZipBatch($attributes);  
        } catch (Exception $ex) {
             return $ex;
        }
      
    } 
    
   
    public function getAllBankInvoice($from_date, $to_date)
    {
        $this->result = DisbursalBatch::getAllBatches($from_date, $to_date);
        return $this->result;
    }

    public function checkSingleInvoice($invNo)
    {
        try
        {
            $this->result = BizInvoice::checkSingleInvoice($invNo);
            return $this->result;
        } catch (Exception $ex) {
            return $ex;
        }
        
    } 
    
    public function updateInvoiceUser($uid)
    {
        try
        {
            return BizInvoice::updateInvoiceUser($uid);
           
        } catch (Exception $ex) {
            return $ex;
        }
        
    } 
     public function getCustomerUser($custid)
    {
        try
        {
            return LmsUser::getCustomerUser($custid);
           
        } catch (Exception $ex) {
            return $ex;
        }
        
    } 
     public function getAllBulkInvoice()
    {
        try
        {
            return InvoiceBulkUpload::getAllBulkInvoice();
           
        } catch (Exception $ex) {
            return $ex;
        }
        
    }  
    
    public function getUserAllBulkInvoice()
    {
      
        try
        {
            return InvoiceBulkUpload::getUserAllBulkInvoice();
           
        } catch (Exception $ex) {
            return $ex;
        }
        
    } 
   public function checkLmsUser()
    {
      
        try
        {
            return LmsUser::checkLmsUser();
           
        } catch (Exception $ex) {
            return $ex;
        }
        
    }  
     public function getSingleBulkInvoice($id)
    {
       try
        {
          return InvoiceBulkUpload::getSingleBulkInvoice($id);
        } catch (Exception $ex) {
           return $ex;
        } 
          
    }
    
     public function updateBulkUpload($attr)
    {
       try
        {
          return InvoiceBulkUpload::updateBulkUpload($attr);
        } catch (Exception $ex) {
           return $ex;
        } 
          
    }
    
   public function getAllBankInvoiceCustomers($batch_id)
    {
        $this->result = Disbursal::getAllBankInvoiceCustomers($batch_id);
        return $this->result;
    }

    public function  getAllDisburseInvoice($batch_id, $disbursed_user_id)
    {
        $this->result = Disbursal::getAllDisburseInvoice($batch_id, $disbursed_user_id);
        return $this->result;
    }   
    
     public function checkUserAdhoc($attributes)
    {
       
        try
        {
             return AppOfferAdhocLimit::checkUserAdhoc($attributes);  
        } catch (Exception $ex) {
             return $ex;
        }
      
    }  
   
     public function getAccountClosure()
    {
       
        try
        {
             return LmsUser::getAccountClosure();  
        } catch (Exception $ex) {
             return $ex;
        }
      
    }  
    

    /**
     * Get Total Invoice Approval Amount
     * 
     * @param array $invoices
     * @return decimal
     * @throws InvalidDataTypeExceptions
     */    
    public function getTotalInvApprAmt($invoices)
    {
        return InvoiceModel::getTotalInvApprAmt($invoices);
    }
    public function getReportAllInvoice()
    {
        return InvoiceModel::getReportAllInvoice();
    }
    
    
}
