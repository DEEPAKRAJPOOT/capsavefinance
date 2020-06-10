<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DateTime;
use App\Inv\Repositories\Factory\Models\BaseModel;

class BizEntityCin extends BaseModel
{
    /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'biz_entity_cin';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'biz_entity_cin_id';
    public $userstamps = false;
    public $timestamps = false;

    
    protected $fillable = [
        'biz_id',
        'cin',
        'is_active',
        'created_by',
        'created_at'
    ];
    
    /**
     * Get Biz Entity Cin Data
     * 
     * @param array $whereCond
     * @return mixed
     */
    public static function getBizEntityCinData($whereCond=[])
    {
        /**
         * $where is not an array
         */
        if (!is_array($whereCond)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        $query = self::select('*');        
       
        if (count($whereCond) > 0) {
            $query->where($whereCond);            
        }        
        
        $result = $query->get();
        return $result ? $result: [];
    }    
  
}
