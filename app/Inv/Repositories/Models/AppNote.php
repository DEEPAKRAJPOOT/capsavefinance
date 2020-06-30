<?php

namespace App\Inv\Repositories\Models;

use App\Inv\Repositories\Factory\Models\BaseModel;
use DB;

class AppNote extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'note';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'note_id';

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
        'note_data',
        'app_id',
        'created_by',
        'created_at'
    ];


    public static function showData($app_id){
        $appNote = self::select('note.*', 'users.f_name', 'users.m_name', 'users.l_name')
                ->join('users', 'users.user_id', '=', 'note.created_by')
                ->where('note.app_id', $app_id)
                ->orderBy('note_id', 'DESC')
                ->get();      
        return $appNote;
    }

    public static function getNoteDataById($noteId, $appId){
        $result = self::where('app_id', $appId)
                    ->latest()
                    ->first();
        return $result ?: false;
    }

    public static function findNoteDatabyNoteId($noteId){
        $result = self::find((int)$noteId);
        return $result ?: false;
    }

    public static function getAllCommentsByAppId($app_id){
        $appNote = self::select('note.*', 'users.f_name', 'users.m_name', 'users.l_name', 'app_status_log.app_status_log_id', 'app_status_log.status_id')
                ->join('users', 'users.user_id', '=', 'note.created_by')
                ->join('app_status_log', 'app_status_log.note_id', '=', 'note.note_id')
                ->where('note.app_id', $app_id)
                ->orderBy('note_id', 'DESC')
                ->get();      
        return $appNote;
    }
}
