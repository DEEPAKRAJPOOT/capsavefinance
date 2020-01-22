<?php

namespace App\Inv\Repositories\Models;
use App\Inv\Repositories\Factory\Models\BaseModel;

class UserDoc extends BaseModel
{
 

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_doc';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'user_doc_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'file_id',
        'created_by',
        'updated_by'
     ];
  
}
  

