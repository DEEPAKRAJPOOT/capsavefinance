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

}
