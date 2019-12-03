<?php

namespace App\Inv\Repositories\Models;

use DB;
use File;
use Auth;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Rcu extends Authenticatable
{

    use Notifiable;
 

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rcu';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'rcu_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'agency_id',
        'from_id',
        'to_id',
        'doc_id',
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
        'created_by'
     ];
    
    
}
  


