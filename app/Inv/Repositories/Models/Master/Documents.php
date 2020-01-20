<?php

namespace App\Inv\Repositories\Models\Master;

use DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\User;

class Documents extends Authenticatable
{

    use Notifiable;
 

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_doc';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'doc_type_id',
        'doc_name',
        'is_rcu',
        'is_active',
        'created_by',
        'updated_by'
     ];


     public function userDetail(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function product_document()
    {
        return $this->hasMany('App\Inv\Repositories\Models\ProductDoc', 'doc_id')->where('is_active', 1);
    }
}

