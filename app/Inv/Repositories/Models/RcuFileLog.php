<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;

class RcuFileLog extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'rcu_file_log';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'rcu_file_log_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'rcu_doc_id',
        'file_id',
        'created_by',
        'created_at'
        ];
}
