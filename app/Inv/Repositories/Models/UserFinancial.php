<?php

namespace App\Inv\Repositories\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class UserFinancial extends Model
{
    
      /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users_kyc_finance';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'user_financial_info_id';

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
    public $userstamps = false;

    
    protected $fillable=[
        'user_kyc_id',
        'user_id',
        'source_funds',
        'other_source',
        'jurisdiction_funds',
        'annual_income',
        'estimated_wealth',
        'wealth_source',
        'tin_code',
        'is_abandoned',
        'date_of_abandonment',
        'abandonment_reason',
        'justification',
        'tin_country_name',
        'tin_number',	
        'created_by',	
        'created_at',	
        'updated_by',	
        'updated_at',
    ];
    
 
    
    /**
     * storeData
     * @param
     * @return array
     * @since 0.1
     * @author Arvind soni
     */
    public static function storeData($inputData,$id='') {
        try {
            $id  = (int)$id;
            $data = self::updateOrCreate(['user_financial_info_id' => $id],$inputData);
            return ($data ?: false);
        } catch (\Exception $e) {
            $data['errors'] = $e->getMessage();
        }
    }
    
    /*
     * Get Financial infomation
     * @param int user_id
     * @return array
     *@author  arvind soni
    */
    public static function getData($user_kyc_id=''){
        $result=self::where('user_kyc_id','=',$user_kyc_id)->first();
        return ($result?:false);
    }
    
  
    
}
