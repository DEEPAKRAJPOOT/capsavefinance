<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class Segment extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_biz_segment';

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
        'id',
        'name',
        'is_active',
        'created_at',
        'updated_at'
    ];


    
    /**
     * Get all State
     *
     * @return type array
     */
    
    /**
     * Get all Constitution
     *
     * @return type array
    */
    public static function getSegmentLists() 
    {
        $result = self::select('mst_biz_segment.id', 'mst_biz_segment.name', 'mst_biz_segment.created_at', 'mst_biz_segment.is_active')
        ->orderBy('mst_biz_segment.id', 'DESC');
    return $result;
    }

    public static function saveSegment($arrSegmentData)
    {
        if (!is_array($arrSegmentData)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        $res = self::create($arrSegmentData);
        return $res ?: false;
    }

    public static function updateSegment($arrSegmentData, $id)
    {
        if (!is_array($arrSegmentData)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        return self::where('id', $id)->first()->update($arrSegmentData);
    }

    public static function getSegmentDropDown()
    {
        $res = self::where('is_active', 1)->pluck('name', 'id');
        return $res ?: [];
    }

    
}