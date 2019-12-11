<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Models\FiRcuLog;
use Auth;

class RcuDocument extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'rcu_doc';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'rcu_doc_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'agency_id',
        'from_id',
        'to_id',
        'app_doc_file_id',
        'rcu_status',
        'rcu_status_updated_by',
        'rcu_status_updatetime',
        'rcu_comment',
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
