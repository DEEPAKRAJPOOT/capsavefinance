<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class NachStatusLog extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'nach_status_log';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'nach_status_log_id';

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
        'users_nach_id',
        'status',
        'created_by',
        'created_at'
    ];

    public function  user_nach()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\UserNach','users_nach_id','users_nach_id');
    }

}

