<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\User;

class GstTax extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_gst_tax_slab';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'tax_id';

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
        'tax_name',
        'tax_value',
        'cgst',
        'sgst',
        'igst',
        'created_at',
        'is_active'
    ];

    /**
     * Get GST TAX for Data Render
     *      
     * @return type mixed
     */
    
    public static function getAllGST() 
    {
        $result = self::select('mst_gst_tax_slab.tax_id', 'mst_gst_tax_slab.tax_name', 'mst_gst_tax_slab.tax_value', 'mst_gst_tax_slab.cgst', 'mst_gst_tax_slab.sgst', 'mst_gst_tax_slab.igst', 'mst_gst_tax_slab.tax_from', 'mst_gst_tax_slab.tax_to', 'mst_gst_tax_slab.is_active')
        ->orderBy('mst_gst_tax_slab.tax_id', 'DESC');
    return $result;
    }

     
    public static function saveGst($attributes, $tax_id)
    {
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        // dd($attributes);
        return self::updateOrCreate(['tax_id' => $tax_id], $attributes);
    }

    public static function updateGST($attributes, $tax_id)
    {
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        return self::where('tax_id', $tax_id)->first()->update($attributes);
    }

    /** 
     * @Author: Rent Aplha
     * @Date: 2020-02-17 17:12:17 
     * @Desc:  
     */    
    public static function getActiveGST(){
        $result=self::select("*")
        ->where("is_active","=",1)
        ->get();
        return $result?$result:false;
    }

     
}