<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DateTime;
use App\Inv\Repositories\Factory\Models\BaseModel;

class AppProduct extends BaseModel
{
    /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'app_product';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'id';
    public $userstamps = false;
    public $timestamps = false;

    
    protected $fillable = [
        'app_id',
        'product_id',
        'loan_amount',
        'tenor_days',
    ];
   
    /**
     * Get Application Product Data
     * 
     * @param array $whereCond
     * @return mixed
     */
    public static function getAppProductData($whereCond=[])
    {
        $query = self::select('*');
        if (count($whereCond) > 0) {
            $query->where($whereCond);
        }
        $result = $query->get();
        return $result;
    }   
    
    /**
     * Save Application Product Data
     * 
     * @param array $appProductData
     * @return mixed
     */
    public static function saveAppProductData($appProductData)
    {
        return self::create($appProductData);
    }    
}




