<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;

class Awards extends BaseModel
{
    /* The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_awards_honors';

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
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at'
    ];

    /**
     * Save Awards
     *
     * @param  array $arrAwards
     *
     * @throws InvalidDataTypeExceptions
     * @throws BlankDataExceptions
     */
    public static function saveAwards($arrAwards = [])
    {

        //Check Data is Array
        if (!is_array($arrAwards)) {
            throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
        }

        //Check Data is not blank
        if (empty($arrAwards)) {
            throw new BlankDataExceptions(trans('error_message.data_not_found'));
        }
        /**
         * Create Education
         */
        $objAwards = self::create($arrAwards);

        return ($objAwards->id ?: false);
    }
    
    /**
     * Get researches
     *
     * @param  integer $userId
     *
     * @return mixed Array | Boolean false
     * @throws InvalidDataTypeExceptions
     * 
     * @author Anand dwivedi
     */
    public static function getAwards($userId)
    {
        /**
         * Check user id is not an integer
         */
        if (!is_int((int) $userId) && $userId != 0) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $arrAwards = self::where('user_id', (int) $userId)
            ->get();

        return ($arrAwards ?: false);
    }
    
    
    /**
     * Delete awards
     *
     * @param  integer $userId
     * @return mixed Array | Boolean false
     * @throws InvalidDataTypeExceptions
     */
    public static function deleteAwards($userId)
    {
        /**
         * Check user id is not an integer
         */
        if (!is_int((int) $userId) && $userId != 0) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }

        $awDelete = self::where('user_id', '=', $userId)->delete();

        return ($awDelete ?: false);
    }
}