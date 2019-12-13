<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;

class AppPdNote extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'pd_note';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'pd_note_id';

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
        'app_id',
        'type',
        'comments',
        'created_at',
        'created_by'
    ];

    /**
     * Show data 
     * 
     * @param type $app_id Int
     * @return type mixed
     */
    public static function showData($app_id)
    {
        $appNote = self::select('pd_note.*', 'users.f_name', 'users.m_name', 'users.l_name')
                ->join('users', 'users.user_id', '=', 'note.created_by')
                ->where('pd_note.app_id', $app_id)
                ->get();
        return $appNote ?: false;
    }

    /**
     * Create or update pd notes 
     * 
     * @param type $attr Array
     * @param type $id Int
     * @return type mixed
     * @throws BlankDataExceptions 
     */
    public static function savePdNotes($attr, $id = null)
    {
        if (empty($attr)) {
            throw new BlankDataExceptions(trans('error_message.no_data_found'));
        }

        $result = self::updateOrCreate($attr, ['pd_note_id' => $id]);
        return $result ?: false;
    }

}
