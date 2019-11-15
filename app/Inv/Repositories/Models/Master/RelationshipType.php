<?php

namespace App\B2c\Repositories\Models\Master;

use App\B2c\Repositories\Factory\Models\BaseModel;

class RelationshipType extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_relationship_type';

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
    public $userstamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'is_active'];

    /**
     * Scopes for active System relationship list
     *
     * @param string $query
     * @param string $type
     *
     * @return type
     */
    public function scopeActive($query, $type)
    {
        return $query->where('is_active', $type);
    }

    /**
     * Get relationship List
     *
     * @param void
     *
     * @return array|boolean
     *
     * @since 0.1
     */
    public static function getRelationshipTypeList()
    {
        $arrRelationType = self::active(config('Inv_common.ACTIVE'))->orderBy('id', 'asc')->lists("name", "id");

        return ($arrRelationType ? : false);
    }
}