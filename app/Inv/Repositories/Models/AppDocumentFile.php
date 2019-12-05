<?php

namespace App\Inv\Repositories\Models;

use DB;
use File;
use Auth;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AppDocumentFile extends Authenticatable
{

    use Notifiable;
 

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'app_doc_file';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'app_doc_file_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rcu_status',
        'app_id',
        'biz_owner_id',
        'doc_id',
        'doc_name',
        'finc_year',
        'gst_month',
        'gst_year',
        'file_bank_id',
        'is_pwd_protected',
        'is_scanned',
        'pwd_txt',
        'doc_id_no',
        'file_id',
        'is_upload',
        'created_by'
     ];
    
    
    /**
    * Create a new record in document file
    *
    * @param Array $attributes
    *
    * @return Array
    */
    
    public static function creates($attributes, $fileId, $userId)
    {
        $inputArr =  AppDocumentFile::arrayInputData($attributes, $fileId, $userId);
        $appDocFile = AppDocumentFile::create($inputArr);
        if($appDocFile){
            $result = AppDocument::where('user_id', $userId)
                    ->where('app_id', $appDocFile->app_id)
                    ->where('doc_id', $appDocFile->doc_id)
                    ->update(['is_upload' => 1]);
        }
        
        return $result;
    }
    
    /**
    * Create a new record in document file
    *
    * @param Array $attributes
    *
    * @return Array
    */
    
    public static function deletes($appDocFileId)
    {
        $deleteRes =  AppDocumentFile::where('app_doc_file_id', $appDocFileId)
                ->update(['is_active' => 0]);
        
        return $deleteRes;
    }
    
    
    /**
     * Managing inputs as required Array
     *
     * @param Array $attributes
     *
     * @return Array
     */
    
    public static function arrayInputData($attributes, $fileId, $userId)
    {
        $inputArr = [];
//        dd($attributes);
        $inputArr['app_id']  = (isset($attributes['appId'])) ? $attributes['appId'] : $attributes['app_id']; 
        $inputArr['doc_id']  = (isset($attributes['docId'])) ? $attributes['docId'] : $attributes['doc_id']; 
        $inputArr['doc_name']  = (isset($attributes['doc_name'])) ? $attributes['doc_name'] : ''; 
        $inputArr['finc_year']  = (isset($attributes['finc_year'])) ? $attributes['finc_year'] : ''; 
        $inputArr['gst_month']  = (isset($attributes['gst_month'])) ? $attributes['gst_month'] : ''; 
        $inputArr['gst_year']  = (isset($attributes['gst_year'])) ? $attributes['gst_year'] : ''; 
        $inputArr['doc_id_no']  = (isset($attributes['doc_id_no'])) ? $attributes['doc_id_no'] : '';

        $inputArr['file_bank_id']  = $attributes['file_bank_id'] ?? NULL;
        $inputArr['is_pwd_protected']  = $attributes['is_pwd_protected'] ?? NULL;
        $inputArr['is_scanned']  = $attributes['is_scanned'] ?? NULL;
        $inputArr['pwd_txt']  = $attributes['pwd_txt'] ?? NULL;
        $inputArr['file_id']  = $fileId; 
        $inputArr['is_upload'] = 1;
        $inputArr['created_by'] = 1;
        
        return $inputArr;
    }
    
    public function userFile()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\UserFile', 'file_id');
    }
    
    
    /**
     * Managing inputs as required Array
     *
     * @param Array $attributes
     *
     * @return Array
     */
    
//    public static function getRcuLists($appId)
//    {
//         DB::enableQueryLog(); // Enable query log
//         $results = DB::select('SELECT a.*,b.* FROM `rta_app_doc_file` a INNER JOIN rta_mst_doc b ON a.doc_id=b.id WHERE a.app_id=10 AND b.is_rcu=1 ORDER BY b.id');
//            dd(DB::getQueryLog());
//           
//        dd(DB::getQueryLog()); // Show results of log
//        return $results;
//             
//    }
    
     /**
     * fetching Rcu documents and files
     *
     * @return Array
     */
    public static function getRcuLists($appId)
    {  
       return AppDocumentFile::select(['doc_id'])
                ->with('rcuDoc')
                ->whereHas('rcuDoc')
                ->where('app_id', $appId)
                ->groupBy('doc_id')
                ->get();
    }
    
    public function rcuDoc()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\Master\Documents', 'doc_id')->where('is_rcu', 1);
    }
    
    public static function getRcuDocuments($appId, $docId)
    {
        return AppDocumentFile::with('userFile')
                ->with('rcu')
                ->whereHas('userFile')
                ->where('app_id', $appId)
                ->where('doc_id', $docId)
                ->where('is_active', 1)
                ->get();
        return $results;
             
    }
    
    public function rcu()
    {
        return $this->hasOne('App\Inv\Repositories\Models\Rcu', 'doc_id', 'app_doc_file_id');
    }
}
  

