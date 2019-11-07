<?php

namespace App\Inv\Repositories\Models\Master;

use App\Inv\Repositories\Factory\Models\BaseModel;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class Documents extends BaseModel
{
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
        'is_active'
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
        return self::active(config('inv_common.ACTIVE'))->orderBy('title', 'ASC')->pluck('title', 'id');
    }

    /**
     * Get all Document type
     *
     * @return type
     */
    public static function getSkillsList()
    {
        $right_type = self::active(config('inv_common.ACTIVE'))->get();

        return $right_type ?: false;
    }
    /**
     * Get Document name
     *
     * @return type
     */
    public static function getDocumentName($id)
    {
        $right_type = self::where('id',$id)->first();
        
        return $right_type->title ?: false;
    }
}