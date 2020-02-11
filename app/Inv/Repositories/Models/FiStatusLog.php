<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;

class FiStatusLog extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'fi_status_log';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'fi_status_log_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'fi_addr_id',
        'fi_status_id',
        'fi_comment',
        'created_at',
        'created_by'
        ];
}
