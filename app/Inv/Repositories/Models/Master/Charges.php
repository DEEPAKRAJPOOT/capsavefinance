<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\User;

class Charges extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_chrg';

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
        'chrg_name',
        'chrg_desc',
        'chrg_type',
        'chrg_calculation_type',
        'chrg_calculation_amt',
        'chrg_applicable_id',
        'is_gst_applicable',
        'gst_percentage',
        'chrg_tiger_id',
        'is_active',
        'created_at',
        'created_by'
    ];

    public function userDetail(){
        return $this->belongsTo(User::class, 'created_by');
    }
}