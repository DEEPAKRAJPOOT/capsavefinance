<?php

namespace App\Inv\Repositories\Models;

use DB;
use Helpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Inv\Repositories\Entities\User\Exceptions\BlankDataExceptions;
use App\Inv\Repositories\Entities\User\Exceptions\InvalidDataTypeExceptions;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Factory\Models\BaseModel;

class AppStatusLog extends BaseModel
{
   
   /**
    * The database table used by the model.
    *
    * @var string
    */
   protected $table = 'app_status_log';

   /**
    * Custom primary key is set for the table
    *
    * @var integer
    */
   protected $primaryKey = 'app_status_log_id';

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
   public $userstamps = true;
   
   /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
   //protected $dates = ['deleted_at'];

   /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
   protected $fillable = [
       'user_id',
       'app_id',
       'note_id',
       'status_id',
       'note_id',
       'created_by',
       'created_at'
   ];





   /**
    * function for save app status log
    *
    * @param integer $user_id     user id
    * @param array   $arrAppStatusLog user data
    *
    * @return boolean
    */
public static function saveAppStatusLog($arrAppStatusLog = [])
{
   //Check data is Array
   if (!is_array($arrAppStatusLog)) {
       throw new InvalidDataTypeExceptions(trans('error_messages.send_array'));
   }

   //Check data is not blank
   if (empty($arrAppStatusLog)) {
       throw new BlankDataExceptions(trans('error_messages.data_not_found'));
   }
   
   /**
    * Create anchor
    */
   $arrAppLog = self::create($arrAppStatusLog);

   return ($arrAppLog->app_status_log_id ?: false);
}


   /** 
    * @Author: Dynamic Discounting 
    * @Date: 2020-01-31 10:18:40 
    * @Desc:  
    */    
   public static function getAppStatusLog($user_id)
   {
       /**
        * Check id is not blank
        */
       if (empty($user_id)) {
           throw new BlankDataExceptions(trans('error_message.no_data_found'));
       }

       /**
        * Check id is not an integer
        */
       if (!is_int($user_id)) {
           throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
       }
              
       
       $appData = self::select('app_status_log.*')
               ->where('app_status_log.user_id', $user_id)->get();
                      
       return ($appData?$appData:null);
       
   }
   /**
    * update Details details
    *
    * @param integer $user_id     user id
    * @param array   $arrUserData user data
    *
    * @return boolean
    */

   
    public static function updateAppStatusLog($app_id, $arrUserData = [])
   {
        $app_id = (int)$app_id;
       /**
        * Check id is not blank
        */
       if (empty($app_id)) {
           throw new BlankDataExceptions(trans('error_message.no_data_found'));
       }

       /**
        * Check id is not an integer
        */
       if (!is_int($app_id)) {
           throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
       }

       /**
        * Check Data is Array
        */
       if (!is_array($arrUserData)) {
           throw new InvalidDataTypeExceptions(trans('error_message.send_array'));
       }

       /**
        * Check Data is not blank
        */
       if (empty($arrUserData)) {
           throw new BlankDataExceptions(trans('error_message.no_data_found'));
       }

       $rowUpdate = self::find((int) $app_id)->update($arrUserData);

       return ($rowUpdate ? $rowUpdate : false);
   }
   
    public static function getAllCommentsByAppId($app_id){
        $appNote = self::select('app_status_log.*','note.note_data', 'users.f_name', 'users.m_name', 'users.l_name', 'mst_status.status_name')                
                ->join('mst_status', 'app_status_log.status_id', '=', 'mst_status.id')
                ->leftJoin('users', 'users.user_id', '=', 'app_status_log.created_by')
                ->leftJoin('note', 'app_status_log.note_id', '=', 'note.note_id')                
                ->where('app_status_log.app_id', $app_id)
                ->orderBy('app_status_log.app_status_log_id', 'DESC')
                ->get();      
        return $appNote;
    }   
 
    public static function getAppOfferLimitApproved($userId, $appId){
        return self::where([
            'user_id'    => $userId,
            'app_id'     => $appId,
            'status_id'  => config('common.mst_status_id.OFFER_LIMIT_APPROVED'),
        ])->first() ? true : false;
    }    
}   