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
        'is_upload',
        'file_type',
        'file_name',
        'file_size',
        'file_encp_key',
        'file_path',
        'created_by'
     ];
    
    
    /**
    * Create a new record in document file
    *
    * @param Array $attributes
    *
    * @return Array
    */
    
    public static function creates($attributes, $docId)
    {
        $inputArr =  AppDocumentFile::arrayInputData($attributes, $docId);
        $owner = AppDocumentFile::insert($inputArr);
        return $owner;
    }
    
    
    /**
     * Managing inputs as required Array
     *
     * @param Array $attributes
     *
     * @return Array
     */
    
    public static function arrayInputData($attributes, $mstDocId)
    {
       
        $inputArr = [];
        $count = count($attributes['bank_docs']);
        for ( $i=0; $i < $count; $i++) 
        {
            if($attributes['bank_docs'][$i]) {
                if(!Storage::exists('/public/uploads/1/bank_document')) {
                    Storage::makeDirectory('/public/uploads/1/bank_document', 0775, true);
                }
                $path = Storage::disk('public')->put('/uploads/1/bank_document', $attributes['bank_docs'][$i],null);
                $inputArr[$i]['file_path'] = $path;
            }
            $inputArr[$i]['app_id']  = 1;   
            $inputArr[$i]['doc_id']  = $mstDocId; 
            $inputArr[$i]['is_upload'] = $attributes['bank_docs'][$i];
            $inputArr[$i]['file_type'] = $attributes['bank_docs'][$i]->getClientMimeType();
            $inputArr[$i]['file_name'] = $attributes['bank_docs'][$i]->getClientOriginalName();
            $inputArr[$i]['file_size'] = $attributes['bank_docs'][$i]->getClientSize();
            $inputArr[$i]['file_encp_key'] = 1;
            $inputArr[$i]['created_by'] = 1;
        }
        dd($inputArr);
        return $inputArr;
  }
  
}
  

