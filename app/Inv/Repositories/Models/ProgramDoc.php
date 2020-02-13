<?php

namespace App\Inv\Repositories\Models;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class ProgramDoc extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'prgm_doc';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'prgm_doc_id';

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
        'user_id',
        'prgm_id',
        'doc_id',
        'wf_stage_id',
        'is_active',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];

    /**
     * program relation 
     * 
     * @return type mixed
     */
    public function program()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\Program', 'prgm_id', 'prgm_id');
    }

    /**
     * save Doc
     * 
     * @param type $attributes
     * @return type mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    public static function saveDoc($attributes)
    {

        if (empty($attributes)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $result = self::insert($attributes);
        return $result ?: false;
    }
    
    
    
    /**
     * delete program doc 
     * 
     * @param type $conditions
     * @return type mixed 
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions 
     */
    
    public static function deleteDoc($conditions)
    {
        if (empty($conditions)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        if (!is_array($conditions)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        return self::where($conditions)->delete();
    }

}
