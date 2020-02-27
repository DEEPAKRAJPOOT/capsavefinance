<?php

namespace App\Inv\Repositories\Models\Financial;

use App\Inv\Repositories\Factory\Models\BaseModel;

class FinancialJeConfig extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'financial_je_config';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'je_config_id';

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
        'trans_config_id',
        'journal_id',
        'created_at',   
        'created_by',        
        'updated_at',
        'updated_by'        
    ];

    public static function saveJeData($data){
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }        
        return self::create($data);       
    }

    public static function getAllJeConfig(){
        $result = \DB::select("SELECT rta_financial_je_config.je_config_id,rta_financial_je_config.trans_config_id, rta_financial_je_config.journal_id, rta_financial_trans_config.trans_type, rta_financial_journals.name as journal_name, rta_financial_journals.journal_type,A.variable_name FROM rta_financial_je_config
        JOIN rta_financial_trans_config ON (rta_financial_trans_config.trans_config_id=rta_financial_je_config.trans_config_id)
        JOIN rta_financial_journals ON (rta_financial_journals.id=rta_financial_je_config.journal_id)
        JOIN 
        (SELECT rfvtc.trans_config_id,GROUP_CONCAT(rfv.name) AS variable_name FROM rta_financial_variables_trans_config
         as rfvtc JOIN rta_financial_variables as rfv ON (rfv.id=rfvtc.variable_id)
        GROUP BY rfvtc.trans_config_id) AS A ON (A.trans_config_id=rta_financial_je_config.trans_config_id)
        ORDER BY rta_financial_je_config.je_config_id DESC");
        return $result;    
    }

}
