<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Notifications\Notifiable;
use App\Inv\Repositories\Factory\Models\BaseModel;

class BankAnalysis extends BaseModel
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'app_bank_analysis';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'bank_analysis_id';

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
                        'bank_name',
                        'act_type',
                        'uti_max',  
                        'uti_min', 
                        'uti_avg', 
                        'chk_inward', 
                        'chk_presented_per',
                        'chk_outward',
                        'chk_deposited_per', 
                        'submission_credit',
                        'submission_debbit',
                        'overdrwaing_in_six_month',
                        'is_active',
                        'created_at',
                        'created_by',
                        'updated_at',
                        'updated_by'
    ];
    
    public static function saveBankAnalysis($data) {
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }     
        return self::insert($data);
    }
    
    public static function updateBankAnalysis($bankDetailId, $dataArr) {
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
    
    public static function getBankAnalysis($bankDetailId) {
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