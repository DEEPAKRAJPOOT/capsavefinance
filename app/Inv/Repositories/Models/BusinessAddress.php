<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Inv\Repositories\Factory\Models\BaseModel;

class BusinessAddress extends BaseModel
{
    use Notifiable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'biz_addr';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'biz_addr_id';

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
        'biz_id',
        'biz_owner_id',
        'addr_1',
        'addr_2',
        'city_name',
        'state_name',
        'pin_code',
        'address_type',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];
}