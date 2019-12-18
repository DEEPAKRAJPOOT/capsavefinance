<?php

namespace App\Inv\Repositories\Models;

use Carbon\Carbon;
use DateTime;
use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;

class Agency extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'agency';
    public $timestamps = false;
    public $userstamps = false;

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'agency_id';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    //protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'comp_name',
        'comp_email',
        'comp_addr',
        'comp_state',
        'comp_city',
        'comp_zip',
        'comp_phone',
        'is_active',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    public function getFullnameAttribute(){
        return ucwords($this->f_name.' '.$this->m_name.' '.$this->l_name);
    }


}