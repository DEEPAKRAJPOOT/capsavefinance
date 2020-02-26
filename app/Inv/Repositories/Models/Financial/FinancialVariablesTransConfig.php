<?php

namespace App\Inv\Repositories\Models\Financial;

use App\Inv\Repositories\Factory\Models\BaseModel;

class FinancialVariablesTransConfig extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'financial_variables_trans_config';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    //protected $primaryKey = 'id';

    /**
     * Maintain created_at and updated_at automatically
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Maintain created_by and updated_by automatically
     *
     * @var boolean
     */
    public $userstamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trans_config_id',
        'variable_id'        
    ];

    public static function saveTransVarData($data){
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }        
        return self::insert($data);       
    }
}
