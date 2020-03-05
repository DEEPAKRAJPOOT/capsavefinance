<?php

namespace App\Inv\Repositories\Models\Financial;

use App\Inv\Repositories\Factory\Models\BaseModel;

class FinancialAccount extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'financial_accounts';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'id';

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
        'account_code',
        'account_name',  
        'is_active',    
        'created_at',   
        'created_by',        
        'updated_at',
        'updated_by'
    ];

    public static function getAllAccount() 
    {
        $result = self::select('id','account_code','account_name','is_active')->orderBy('id', 'DESC');
        return $result;
    }

    public static function saveAccountData($data, $accountId = null){
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }       
        if(isset($accountId) && !empty($accountId)) {
            $updObj = self::where('id', $accountId);
            return $updObj->update($data);
        } else {
            return self::create($data); 
        }      
    }
}
