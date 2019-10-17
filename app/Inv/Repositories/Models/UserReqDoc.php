<?php

namespace App\Inv\Repositories\Models;

use DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;

class UserReqDoc extends Authenticatable
{

    use Notifiable;
 

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_req_doc';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'user_req_doc_id';

    protected $fillable=[
       'doc_id',
       'doc_type',
       'user_id',
       'upload_doc_name',
       'user_kyc_id',
       'is_upload',
       'created_by',

    ];

   public static function createCorpDocRequired($doc,$userKycid, $userID)
   {
       
        if(count($doc)>0)
        {
            
            foreach($doc as $val)
            {
               $res=UserReqDoc::create([
                       'doc_type'=>$val->doc_for,
                       'user_kyc_id'=>$userKycid,
                       'user_id'=>$userID,
                       'doc_id'=>$val->id,
                       'upload_doc_name'=>$val->doc_name,
                       'is_upload'=>0,
                       'created_by'=>$userID,

                ]);


            }
        }
        return $res;
   }
    

   public static function getUserDocuments()
   {
        $userkyc=Userkyc::where('user_id',Auth()->user()->user_id)->first();

        $corpdata=UserReqDoc::where('user_kyc_id', $userkyc->kyc_id)->get();
        if(!empty($corpdata)){
             
             return   $corpdata;
        }

   } 

   
  

   
   

}

