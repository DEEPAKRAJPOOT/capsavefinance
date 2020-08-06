<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class Tds extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_tds';

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
        'id',
        'tds_per',
        'is_active',
        'created_by',
        'start_date',
        'end_date',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    public static function saveTds($tdsData)
    {
        if (!is_array($tdsData)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        $res = self::create($tdsData);
        return $res ?: false;
    }

    public static function getTDSLists() {
        $data = self::select('mst_tds.id', 'mst_tds.tds_per', 'mst_tds.is_active', 'mst_tds.created_at', 'mst_tds.start_date', 'mst_tds.end_date')->orderBy('mst_tds.id', 'DESC');
        return $data ? $data : "";
    }

    public static function updateTds($tdsData, $tds_id)
    {
        if (!is_array($tdsData)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        return self::where('id', $tds_id)->first()->update($tdsData);
    }


}