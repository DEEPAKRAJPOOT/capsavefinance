<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;

class RcuStatusLog extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'rcu_status_log';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'rcu_status_log_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'rcu_addr_id',
        'rcu_status_id',
        'rcu_comment',
        'created_at',
        'created_by'
        ];
}
