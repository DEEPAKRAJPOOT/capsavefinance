<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;

class MakerChecker extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_maker_checker';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'maker_checker_id';

    /**
     * Maintain created_at and updated_at automatically
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Maintain created_by and updated_by automatically
     *
     * @var boolean
     */
    public $userstamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'table_name',
        'group_id',
        'type',
        'route_name',
        'status',
        'created_by',
        'created_at',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
            $model->created_by = auth()->user() ? auth()->user()->user_id : 0;
        });
    }

    public static function checkGroupIsApproved($groupId)
    {
        $result = self::where('group_id', $groupId)
                    ->where('status', 1)
                    ->where('type', 2)
                    ->first();
        return $result ? true : false;            
    }
}
