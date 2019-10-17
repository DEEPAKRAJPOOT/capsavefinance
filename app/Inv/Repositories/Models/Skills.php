<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;

class Skills extends BaseModel
{
    /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_skills';

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
        'skill_id',
        'level',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at'
    ];

    /**
     * Save Skills
     *
     * @param  array $arrSkills
     *
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions
     */
    public static function saveSkills($arrSkills = [])
    {

        //Check data is Array
        if (!is_array($arrSkills)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        //Check data is not blank
        if (empty($arrSkills)) {
            throw new BlankDataExceptions(trans('error_message.data_not_found'));
        }
        /**
         * Create skills
         */
        $objSkill = self::create($arrSkills);

        return ($objSkill->id ?: false);
    }

    /**
     * Get Skills
     *
     * @param  integer $userId
     *
     * @return mixed Array | Boolean false
     * @throws InvalidDataTypeExceptions
     */
    public static function getSkills($userId)
    {
        /**
         * Check user id is not an integer
         */
        if (!is_int((int) $userId) && $userId != 0) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $arrSkill = self::where('user_id', (int) $userId)
            ->get();

        return ($arrSkill ?: false);
    }

    /**
     * Delete skills
     *
     * @param  integer $userId
     * @return mixed Array | Boolean false
     * @throws InvalidDataTypeExceptions
     */
    public static function deleteSkills($userId)
    {
        /**
         * Check user id is not an integer
         */
        if (!is_int((int) $userId) && $userId != 0) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $sklDelete = self::where('user_id', '=', $userId)->delete();

        return ($sklDelete ?: false);
    }
}