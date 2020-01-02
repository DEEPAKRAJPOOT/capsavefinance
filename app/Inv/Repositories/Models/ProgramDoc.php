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
     * Get Program Documents
     * 
     * @param array $whereCondition
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public static function getProgramDocs($whereCondition=[])
    {
        //Check $whereCondition is not an array
        if (!is_array($whereCondition)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }
        
        //$whereCondition['prgm.prgm_id'] = isset($whereCondition['prgm_id']) ? $whereCondition['prgm_id'] : 1;
        $whereCondition['prgm_doc.is_active'] = isset($whereCondition['is_active']) ? $whereCondition['is_active'] : 1;
        $whereCondition['prgm.status'] = isset($whereCondition['status']) ? $whereCondition['status'] : 1;
        
        $prgmDocs = self::select('prgm_doc.*')
                ->join('prgm', 'prgm.prgm_id', '=', 'prgm_doc.prgm_id')     
                //->join('user', 'user.user_id', '=', 'prgm_doc.user_id')                                
                //->join('anchor_user', 'anchor_user.anchor_id', '=', 'user.anchor_id')
                //->join('app', 'anchor_user.user_id', '=', 'app.user_id')                       
                ->join('wf_stage', 'prgm_doc.wf_stage_id', '=', 'wf_stage.wf_stage_id')
                
                ->where($whereCondition)
                ->orderBy('prgm.prgm_id', 'DESC')
                ->groupBy('prgm_doc.doc_id')
                ->get();
        return $prgmDocs;
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

}
