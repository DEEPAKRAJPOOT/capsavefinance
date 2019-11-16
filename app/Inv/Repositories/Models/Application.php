<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Application extends Model
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'app';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'app_id';

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
        'user_id',
        'biz_id',
        'loan_amt',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    /**
     * Get Applications for Application list data tables
     */
    protected static function getApplications() 
    {
        $appData = self::select('app.app_id', 'biz.biz_entity_name', 
                'biz.biz_entity_name as assoc_anchor', 'biz.biz_entity_name as user_type', 
                'biz.biz_entity_name as assignee', 'biz.biz_entity_name as shared_detail', 
                'biz.biz_entity_name as status', 'biz.biz_entity_name as action')
                ->join('app', 'app.biz_id', '=', 'biz.biz_id')
                ->where('status', 1)
                ->orderBy('app_id');        
        return $appData;
    }    
}