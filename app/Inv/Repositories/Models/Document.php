<?php

namespace App\Inv\Repositories\Models;

use DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\UserReqDoc;

class Document extends Authenticatable
{

    use Notifiable;
 

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_upload_doc';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'upload_doc_id';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_req_doc_id',
        'doc_type',
        'doc_id',
        'doc_name',
        'user_kyc_id',
        'user_id',
        'doc_ext',
        'enc_id',
        'doc_status',
        'created_by',
     ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    

    /**
     * update user details
     *
     * @param integer $user_id     user id
     * @param array   $arrUserData user data
     *
     * @return boolean
     */



    public static function getTotalDoc($userId,$docid)
    {
             return $data=DB::table('user_upload_doc')
                ->where('user_id',$userId)
                ->where('doc_id',$docid)
                ->get();
    }

public static function getDocumentList($kycid,$user_req_doc_id)
    {
             $data=DB::table('user_upload_doc')
                ->where('user_kyc_id',$kycid)
                ->where('user_req_doc_id',$user_req_doc_id)
                ->get();
               return $data ?: false;
    }


    public static function getSingleDocument($documentHash)
    {
             $data=DB::table('user_upload_doc')
                ->where('enc_id',$documentHash)
                ->first();
               return $data ?: false;
    }
}
  

