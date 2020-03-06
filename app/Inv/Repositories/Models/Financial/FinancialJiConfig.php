<?php

namespace App\Inv\Repositories\Models\Financial;

use App\Inv\Repositories\Factory\Models\BaseModel;

class FinancialJiConfig extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'financial_ji_config';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'ji_config_id';

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
        'account_id',
        'is_partner', 
        'label',
        'value_type',    
        'config_value',
        'je_config_id', 
        'created_at',   
        'created_by',        
        'updated_at',
        'updated_by'
    ];

    public static function saveJiData($data, $jiConfigId = null){
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }       
        if(isset($jiConfigId) && !empty($jiConfigId)) {
            $updObj = self::where('ji_config_id', $jiConfigId);
            return $updObj->update($data);
        } else {
            return self::create($data); 
        }      
    }

    public static function getAllJiConfig($jeConfigId){
        $result = \DB::select("SELECT rta_financial_ji_config.ji_config_id, rta_financial_ji_config.je_config_id, rta_financial_ji_config.label,rta_financial_ji_config.config_value, rta_financial_ji_config.is_partner as is_partner_val, IF(rta_financial_ji_config.is_partner='1','Yes','No') as is_partner, rta_financial_ji_config.value_type as value_type_val, IF(rta_financial_ji_config.value_type='1','Credit','Debit') as value_type, CONCAT(rta_financial_accounts.account_name, '-', rta_financial_accounts.account_code) as account_name, rta_financial_accounts.id as account_id FROM rta_financial_ji_config
        JOIN rta_financial_accounts ON (rta_financial_accounts.id=rta_financial_ji_config.account_id)
        WHERE je_config_id = ?",[$jeConfigId]);
        return $result;    
    }

    public static function getJiConfigByjiConfigId($jiConfigId){
        $result = \DB::select("select * from rta_financial_ji_config where ji_config_id=?",[$jiConfigId]);
        return $result ? $result[0] : false; 
    }
}
