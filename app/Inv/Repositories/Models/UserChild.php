<?php

namespace App\Inv\Repositories\Models;

use Illuminate\Database\Eloquent\Model;

class UserChild extends Model
{
     /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users_child';

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
        'user_id',
        'child_name',
        'child_dob',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];
    
    /**
     * storeData
     * @param
     * @return array
     * @since 0.1
     * @author Arvind soni
     */
    public static function storeData($inputData,$id='') {
        try {
            $data = self::updateOrCreate(['id' => (int) $id],$inputData);
            return ($data ?: false);
        } catch (\Exception $e) {
            $data['errors'] = $e->getMessage();
        }
    }
    
}
