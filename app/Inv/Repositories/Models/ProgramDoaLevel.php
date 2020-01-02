<?php

namespace App\Inv\Repositories\Models;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class ProgramDoaLevel extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'prgm_doa_level';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    //  protected $primaryKey = 'prgm_doc_id';

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
        'doa_level_id',
        'prgm_id',
        'is_required',
        'min_amount',
        'max_amount'
    ];

    /**
     * insert DOA level
     * 
     * @param type $data array
     * @return type mixed
     */
    public static function insertDoaLevel($data)
    {

        if (empty($data)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        $res = self::insert($data);
        return $res ?: false;
    }

    /**
     * Delete DOA level 
     * 
     * @param type $where Array
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function deleteDoaLevelBywhere($where)
    {
        if (empty($where)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        if (!is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        return self::where($where)->delete();
    }

}
