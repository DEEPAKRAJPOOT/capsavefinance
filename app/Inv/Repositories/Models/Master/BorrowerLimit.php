<?php

namespace App\Inv\Repositories\Models\Master;

use Carbon\Carbon;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class BorrowerLimit extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_borrower_limit';


    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'limit_id';


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
        'limit_id',
        'single_limit',
        'multiple_limit',
        'start_date',
        'end_date',
        'is_active',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];


    /**
     * Get Limit for Data Render
     *      
     * @return type mixed
     */
    
    public static function getAllLimit() 
    {
        $result = self::select('mst_borrower_limit.limit_id', 'mst_borrower_limit.single_limit', 'mst_borrower_limit.multiple_limit', 'mst_borrower_limit.start_date', 'mst_borrower_limit.end_date', 'mst_borrower_limit.is_active')->orderBy('mst_borrower_limit.limit_id', 'DESC');

        return $result;
    }

    public static function findLastLimit(){

        $result = self::select('mst_borrower_limit.limit_id', 'mst_borrower_limit.single_limit', 'mst_borrower_limit.multiple_limit', 'mst_borrower_limit.start_date', 'mst_borrower_limit.end_date', 'mst_borrower_limit.is_active')->where('mst_borrower_limit.is_active', 1)->orderBy('mst_borrower_limit.limit_id', 'DESC')->first();

        return $result?$result:false;
    }

    public static function getavailFutureDate(){

        $result = self::select('mst_borrower_limit.limit_id', 'mst_borrower_limit.single_limit', 'mst_borrower_limit.multiple_limit', 'mst_borrower_limit.start_date', 'mst_borrower_limit.end_date', 'mst_borrower_limit.is_active')->where('mst_borrower_limit.is_active', 1)->where('start_date','>=',Carbon::now())->first();

        return $result?$result:false;
    }

    public static function getCurrentBorrowerLimitData(){
        //dd(DB::enableQueryLog());
        $result = self::select('mst_borrower_limit.limit_id', 'mst_borrower_limit.single_limit', 'mst_borrower_limit.multiple_limit', 'mst_borrower_limit.start_date', 'mst_borrower_limit.end_date', 'mst_borrower_limit.is_active')->where('mst_borrower_limit.is_active', 1)->where(function($q) {
            $q->where('start_date','<=',Carbon::now())->where('end_date','>=',Carbon::now());
            $q->orWhere('start_date','<=',Carbon::now())->orWhereNull('end_date');
        })->first();
        //dd(DB::getQueryLog());
        return $result?$result:false;
    }

    // update tax_to means end date in gst table
    public static function updatePrevLimitStatus(){

        return self::where('end_date','<',Carbon::now())->update(['is_active'=>0]);
        
    }


    // update tax_to means end date in gst table
    public static function updateLimitEndDate($id , $date){

        $query = self::where('limit_id','<>',$id)->where('end_date','=',NULL)->orderBy('limit_id', 'DESC')->first();
        if($query){
            return $query->update(['end_date'=>$date]);
        }else{
            return true;
        }

    }

    public static function saveLimit($attributes, $limit_id)
    {
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        return self::updateOrCreate(['limit_id' => $limit_id], $attributes);
    }

    public static function updateLimit($attributes, $limit_id)
    {
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        return self::where('limit_id', $limit_id)->first()->update($attributes);
    }



}
