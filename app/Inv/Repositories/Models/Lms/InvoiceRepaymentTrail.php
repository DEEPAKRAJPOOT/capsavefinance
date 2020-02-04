<?php

namespace App\Inv\Repositories\Models\Lms;
use Auth;
use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class InvoiceRepaymentTrail extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'invoice_repayment_trail';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'inv_repayment_id';

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
        'user_id',
        'invoice_id',
        'repaid_amount',
        'repaid_date',
        'trans_type',  
        'file_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

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
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        if (!empty($whereCondition)) {
            return self::where($whereCondition)->update($data);
        } else {
            return self::create($data);
        }
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
        if (!is_array($whereCondition)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        $query = self::select('*');
                
        if (!empty($whereCondition)) {
            $query->where($whereCondition);
        }
        
        $result = $query->get();
        return $result;
    }
    
    /* save repayment amount//////////////////
     * saveRepayment  */
    
    public static function saveRepay($attr)
    {
        return self::create($attr);
    }
    
    
}
