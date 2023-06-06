<?php

namespace App\Inv\Repositories\Models;

use DB;
use File;

use App\Inv\Repositories\Models\AppDocumentFile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Helpers;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Inv\Repositories\Factory\Models\BaseModel;

class UserFile extends BaseModel
{
 

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'file';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'file_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file_type',
        'file_name',
        'file_size',
        'file_encp_key',
        'file_path',
        'created_by',
        'updated_by',
        'is_active'
     ];
    
    
    /**
    * Create a new record in document file
    *
    * @param Array $attributes
    *
    * @return Array
    */
    
    public static function creates($attributes, $docId, $userId)
    {

        $inputArr = UserFile::arrayInputData($attributes, $docId, $userId);
        foreach ($inputArr as $value) {
            $file = UserFile::create($value);
            $file = AppDocumentFile::creates($attributes, $file->file_id, $userId);
        }
        
        return $file;
    }
    
    
    /**
    * Create a new record in document file
    *
    * @param Array $attributes
    *
    * @return Array
    */
    
    public static function deletes($fileId)
    {
        $deleteFile = UserFile::where('file_id', $fileId)
                ->update(['is_active' => 0, 'deleted_at' => date("Y-m-d h:m:s",time()) ]);
        
        return $deleteFile;
    }
    
    
    /**
     * Managing inputs as required Array
     *
     * @param Array $attributes
     *
     * @return Array
     */
    
    public static function arrayInputData($attributes, $mstDocId, $userId)
    {
        $inputArr = [];
        $count = count($attributes['doc_file']);
        $appId = (isset($attributes['appId'])) ? $attributes['appId'] : $attributes['app_id'];
        for ( $i=0; $i < $count; $i++) 
        {   
            $s3path = env('S3_BUCKET_DIRECTORY_PATH').'/user/' .$userId. '/' .$appId;
            if($attributes['doc_file'][$i]) {
                $data['doc_file'] = $attributes['doc_file'][$i];
                $path = Helpers::uploadAwsS3Bucket($s3path,$data);
                $inputArr[$i]['file_path'] = $path['file_path'];
            }
             
            $inputArr[$i]['file_type'] = $attributes['doc_file'][$i]->getMimeType();
            $inputArr[$i]['file_name'] = $attributes['doc_file'][$i]->getClientOriginalName();
            $inputArr[$i]['file_size'] = $attributes['doc_file'][$i]->getSize();
            $inputArr[$i]['file_encp_key'] =  !empty($path['file_path']) ? md5(basename($path['file_path'])) : md5('2');
            $inputArr[$i]['created_by'] = 1;
            $inputArr[$i]['updated_by'] = 1;
        }
        
        return $inputArr;
    }


        /**
     * Managing information "Documents" 
     *
     * @param Array $attributes
     *
     * @return Array
     */
    public static function deleteFile($fileId)
    {
        $deleteFile = UserFile::where('file_id', $fileId)
                ->update(['is_active' => 0, 'deleted_at' => date("Y-m-d h:m:s",time()) ]);
        
        return $deleteFile;
    }

    /**
    * Create a new record in for Nach document file
    *
    * @param Array $attributes
    *
    * @return Array
    */
    public static function saveNachFile($attributes, $userId)
    {
        $docId = '';
        $inputArr = UserFile::arrayInputNachData($attributes, $docId, $userId);
        foreach ($inputArr as $value) {
            $file = UserFile::create($value);
        }
        return $file;
    }
    
    /**
     * Managing nach file inputs as required Array
     *
     * @param Array $attributes
     *
     * @return Array
     */
    
    public static function arrayInputNachData($attributes, $mstDocId, $userId)
    {
        $inputArr = [];
        $count = count($attributes['doc_file']);
        for ( $i=0; $i < $count; $i++) 
        {   
            if($attributes['doc_file'][$i]) {
                if(!Storage::exists('/public/user/' .$userId)) {
                    Storage::makeDirectory('/public/user/' .$userId, 0775, true);
                }
                $path = Storage::disk('public')->put('/user/' .$userId, $attributes['doc_file'][$i], null);
                $inputArr[$i]['file_path'] = $path;
            }
             
            $inputArr[$i]['file_type'] = $attributes['doc_file'][$i]->getMimeType();
            $inputArr[$i]['file_name'] = $attributes['doc_file'][$i]->getClientOriginalName();
            $inputArr[$i]['file_size'] = $attributes['doc_file'][$i]->getSize();
            $inputArr[$i]['file_encp_key'] =  !empty($path) ? md5(basename($path)) : md5('2');
            $inputArr[$i]['created_by'] = 1;
            $inputArr[$i]['updated_by'] = 1;
        }
        
        return $inputArr;
    }
  
}
  

