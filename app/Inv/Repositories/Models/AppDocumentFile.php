<?php

namespace App\Inv\Repositories\Models;

use DB;
use File;

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
        'app_id',
        'doc_id',
        'doc_name',
        'doc_id_no',
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
    
    public static function creates($attributes, $fileId)
    {
        $inputArr =  AppDocumentFile::arrayInputData($attributes, $fileId);
        $appDocFile = AppDocumentFile::insert($inputArr);
        return $appDocFile;
    }
    
    
    /**
     * Managing inputs as required Array
     *
     * @param Array $attributes
     *
     * @return Array
     */
    
    public static function arrayInputData($attributes, $fileId)
    {
       
        $inputArr = [];
        
            
        $inputArr['app_id']  = 1;   
        $inputArr['doc_id']  = $mstDocId; 
        $inputArr['doc_name']  = $attributes['doc_name']; 
        $inputArr['doc_id_no']  = $attributes['doc_id_no']; 
        $inputArr['file_id']  = $fileId; 
        $inputArr['is_upload'] = 1;
        $inputArr['created_by'] = 1;

        
        return $inputArr;
  }
  
}
  

