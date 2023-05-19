<?php

namespace App\Inv\Repositories\Models;

use DB;
use Carbon\Carbon;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class UserCkycDoc extends BaseModel {

    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'user_ckyc_doc';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'user_ckyc_doc_id';

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
        'user_id',
        'ckyc_api_log_id',
        'file_id',
        'file_type',
        'doc_id',
        'user_ckyc_id',
    ];

    public function file()
    {
        return $this->hasOne('App\Inv\Repositories\Models\UserFile', 'file_id', 'file_id');
    }
}