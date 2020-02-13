<?php

namespace App\Inv\Repositories\Entities\Lms;

use DB;
use Session;
use App\Inv\Repositories\Contracts\LmsInterface;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;
use App\Inv\Repositories\Models\LmsUser;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Models\Lms\Disbursal;
use App\Inv\Repositories\Models\Lms\DisburseApiLog;
use App\Inv\Repositories\Models\Lms\TransType;
use App\Inv\Repositories\Models\Lms\Transactions;
use App\Inv\Repositories\Models\Lms\InterestAccrual;
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
    public static function saveDisbursalRequest($data, $whereCondition=[])
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
    public static function saveTransaction($transactions)
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
    public static function saveRepayment($data, $whereCondition=[])
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
    public static function saveInterestAccrual($data, $whereCondition=[])
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
    public static function getDisbursalRequests($whereCondition=[])
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
    public static function getTransactions($whereCondition=[])
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
    public static function getRepayments($whereCondition=[])
    {
        return InvoiceRepaymentTrail::getRepayments($whereCondition);
    }   
    

    public static function getAllUserInvoice($userId)
    {
        return BizInvoice::getAllUserInvoice($userId);
    }

    /**
     * Get Accrued Interest Data
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */    
    public static function getAccruedInterestData($whereCondition=[])
    {
        return InterestAccrual::getAccruedInterestData($whereCondition);
    }
    
    /**
     * Get Program Offer Data
     * 
     * @param array $whereCondition
     * @return mixed
     */
    public static function getProgramOffer($whereCondition=[])
    {
        return Disbursal::getProgramOffer($whereCondition);
    }

    
    public static function getInvoices($invoiceIds)
    {
        return BizInvoice::whereIn('invoice_id', $invoiceIds)
               ->with(['program_offer','lms_user' , 'supplier', 'supplier_bank_detail.bank'])
               ->get();
    }  

    /**
     * Get Repayments
     *      
     * @param array $whereCondition | optional
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getInvoiceSupplier($invoiceIds)
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
    public static function updateInvoiceStatus($invoiceId, $status)
    {
        return BizInvoice::where('invoice_id', $invoiceId)
                ->update(['status_id' => $status]);
    }    

    /**
     * Get Sum of Accrued Interest
     *      
     * @param integer $disbursal_id
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function sumAccruedInterest($disbursal_id)
    { 
        return InterestAccrual::sumAccruedInterest($disbursal_id);
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
}
