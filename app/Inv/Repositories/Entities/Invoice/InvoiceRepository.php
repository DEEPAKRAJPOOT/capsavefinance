<?php

namespace App\Inv\Repositories\Entities\Invoice;
use DB;
use Session;
use App\Inv\Repositories\Contracts\InvoiceInterface;
use App\Inv\Repositories\Factory\Repositories\BaseRepositories;
use App\Inv\Repositories\Models\User as UserModel;
use App\Inv\Repositories\Models\BizInvoice as InvoiceModel;
use App\Inv\Repositories\Contracts\Traits\CommonRepositoryTraits;
use App\Inv\Repositories\Models\AppProgramLimit;
use App\Inv\Repositories\Models\Anchor;
use App\Inv\Repositories\Models\BizInvoice;

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
    
    public function getInvoice()
    {
        try
        {
           return InvoiceModel::getInvoice();  
        } catch (Exception $ex) {
           return $ex;
        }
         
    }
    
    public function getAllInvoice($request)
    {
        try
        {
           return InvoiceModel::getAllInvoice($request);  
        } catch (Exception $ex) {
           return $ex;
        }
         
    } 
    
    public function getBusinessName()
    {
        try
        {
           return InvoiceModel::getBusinessName();  
        } catch (Exception $ex) {
           return $ex;
        }
         
    }  
    
    
      public function getAllAnchor()
    {
        try
        {
           return InvoiceModel::getAllAnchor();  
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
          return InvoiceModel::getUserBehalfAnchor($uid);
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

 public function getLimitProgram($aid)
    {
     
        try
        {
          return AppProgramLimit::getLimitProgram($aid);
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
          return AppProgramLimit::getSingleLimit($aid);
        } catch (Exception $ex) {
           return $ex;
        } 
    }    
    
    
    
}
