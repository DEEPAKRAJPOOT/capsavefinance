<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;

class FiAddress extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'fi_addr';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'fi_addr_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'agency_id',
        'from_id',
        'to_id',
        'biz_addr_id',
        'fi_status',
        'fi_status_updated_by',
        'fi_status_updatetime',
        'fi_comment',
        'cm_status_updatetime',
        'cm_status_updated_by',
        'is_active',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function agency(){
        return $this->belongsTo('App\Inv\Repositories\Models\Agency','agency_id','agency_id');
    }

    public function user(){
        return $this->belongsTo('App\Inv\Repositories\Models\User','to_id','user_id');
    }

}
