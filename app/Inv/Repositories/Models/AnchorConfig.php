<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnchorConfig extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'anchor_config';

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
    public $timestamps = true;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'anchor_id',
        'css_file',
        'redirect_url',
        'created_at',
        'updated_at'
    ];
}
