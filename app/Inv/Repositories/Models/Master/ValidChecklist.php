<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class ValidChecklist extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_valid_checklist';

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
        'title',
        'status'
    ];

    /**
     * Scopes for active list
     *
     * @param string $query
     * @param string $type
     * @return type
     */
    public function scopeActive($query, $type)
    {
        return $query->where('is_active', $type);
    }

    /**
     * Get Drop down list
     *
     * @return type
     */
    public static function getDropDown()
    {
        return self::active(config('inv_common.ACTIVE'))->pluck('title', 'id');
    }

    /*
    /**
     * Get all Right type
     *
     * @return type
     */
    public static function getValidNovltyCheckList($column_name ,$type)
    {
        $right_type = self::active(config('inv_common.ACTIVE'))
                ->where($column_name, 1)
                ->where('type', $type)->get();

        return $right_type ?: false;
    }
    /**
     * Get Skill name   
     *
     * @return type
     */
    public static function getChecklistName($id)
    {
        $right_type = self::where('id',$id)->first();
        
        return $right_type->title ?: false;
    }
    
    /**
     * Get type wise List   
     *
     * @return type
     */
    
    public static function getTypeWiseList($type)
    {
        return self::active(config('inv_common.ACTIVE'))->where('type',$type)->pluck('id');
    }
}