<?php

namespace App\Inv\Repositories\Models;

use DB;
use File;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AppDocument extends Authenticatable
{

    use Notifiable;
 

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'app_doc';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'app_doc_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'app_id',
        'doc_id',
        'is_upload',
        'created_by',
        'updated_by'
     ];
    
    public function document()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\DocumentMaster', 'doc_id');
    }
  
}
  

