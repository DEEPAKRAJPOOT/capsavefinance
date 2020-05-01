<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Notifications\Notifiable;
use App\Inv\Repositories\Factory\Models\BaseModel;

class BankTermBusiLoan extends BaseModel
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bank_term_busi_loan';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'bank_term_busi_loan_id';

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
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    //protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
                        'bank_detail_id',
                        'bank_name_tlbl',
                        'loan_name',
                        'facility_amt',  
                        'facility_os_amt', 
                        'relationship_len_tlbl',
                        'is_active',             
                        'created_by',
                        'created_at',
                        'updated_at',
                        'updated_by'
    ];
    
    public static function saveBankTermBusiLoan($data) {
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }     
        return self::insert($data);
    }
    
    public static function updateBankTermBusiLoan($bankDetailId, $dataArr) {
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
    
    public static function getBankTermBusiLoan($bankDetailId) {
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