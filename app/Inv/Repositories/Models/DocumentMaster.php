<?php

namespace App\Inv\Repositories\Models;

use DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Factory\Models\BaseModel;

class DocumentMaster extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_doc';

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
        'doc_name',
        'is_active',
        'created_by',
        'updated_by'
    ];

    
    /**
     * get Document list
     * 
     * @param type $where array
     * @return type Mixed
     * @throws BlankDataExceptions
     * @throws InvalidDataTypeExceptions
     */
    public static function getDocumentList($where)
    {
        if (empty($where)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }


        /**
         * Check Data is Array
         */
        if (!is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        $res = self::where($where)->pluck('doc_name', 'id');
        return $res ?: false;
    }

    /**
     * Get required documents
     * 
     * @param array $where
     * @return mixed
     */
    public static function getRequiredDocs($where)
    {
        if (empty($where)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }


        /**
         * Check Data is Array
         */
        if (!is_array($where)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        $res = self::where($where)->get();
        return $res ?: [];        
    }    
}
