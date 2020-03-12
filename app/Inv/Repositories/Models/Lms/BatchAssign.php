<?php

namespace App\Inv\Repositories\Models\Lms;

use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class BatchAssign extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'lms_batch_assign';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'batch_assign_id';

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
        'batch_assign_id',
        'from_id',
        'to_id',
        'role_id',        
        'assigned_user_id',
        'batch_id',
        'assign_status',
        'assign_type',
        'sharing_comment',
        'is_owner',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public function batch()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\Lms\Batch', 'batch_id');
    }

    public function fromUser(){
        return $this->belongsTo('App\Inv\Repositories\Models\User', 'from_id', 'user_id');
    }

    public function toUser(){
        return $this->belongsTo('App\Inv\Repositories\Models\User', 'to_id', 'user_id');
    }

    public function assignedUser(){
        return $this->belongsTo('App\Inv\Repositories\Models\User', 'assigned_user_id', 'user_id');
    }

    public function role(){
        return $this->belongsTo('App\Inv\Repositories\Models\Role', 'role_id', 'id');
    }
}

