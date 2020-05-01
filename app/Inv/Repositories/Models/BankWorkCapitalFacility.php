<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;

class BankWorkCapitalFacility extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'app_bank_wc';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'app_bank_wc_id';

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
                        'bank_detail_id',
                        'bank_name',
                        'fund_facility',
                        'fund_amt',  
                        'fund_os_amt', 
                        'nonfund_facility', 
                        'nonfund_amt', 
                        'nonfund_os_amt', 
                        'relationship_len', 
                        'is_active',             
                        'created_by',
                        'created_at',
                        'updated_at',
                        'updated_by'
    ];
    
    public static function saveBankWcFacility($data) {
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }     
        return self::insert($data);
    }
    
    public static function updateBankWcFacility($bankDetailId, $dataArr) {
        /**
         * Check id is not an integer
         */
        if (!is_int($bankDetailId)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        /**
         * Check Data is Array
         */
        if (!is_array($dataArr)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        } 
        $rowUpdate = self::where('bank_detail_id',(int) $bankDetailId)->update($dataArr);
        return ($rowUpdate ? $rowUpdate : false);
    }
    
    public static function getBankWcFacility($bankDetailId) {
        /**
         * Check id is not an integer
         */
        if (!is_int($bankDetailId)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        $result = self::where('bank_detail_id',(int) $bankDetailId)
                ->where('is_active', 1)->get();
        return ($result ? $result : []);
    }
}