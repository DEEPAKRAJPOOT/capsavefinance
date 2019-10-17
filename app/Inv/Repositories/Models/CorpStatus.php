<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DB;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\Application\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\Application\Exceptions\InvalidDataTypeExceptions;

class CorpStatus extends BaseModel
{
    /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'corp_status';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'sid';

    /**
     * Maintain created_at and updated_at automatically
     *
     * @var boolean
     */
    public $timestamps = false;

  

}