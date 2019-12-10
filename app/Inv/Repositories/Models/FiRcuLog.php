<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;

class FiRcuLog extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'fi_rcu_log';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'fi_rcu_log_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'whom_id',
        'fi_rcu_type',
        'fi_rcu_status',
        'fi_rcu_comment',
        'created_at',
        'created_by'
        ];
}
