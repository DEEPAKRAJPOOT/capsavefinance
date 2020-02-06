<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;

class FiFileLog extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'fi_file_log';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'fi_file_log_id';

    public $userstamps = false;
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'fi_addr_id',
        'file_id',
        'created_at',
        'created_by'
        ];
}
