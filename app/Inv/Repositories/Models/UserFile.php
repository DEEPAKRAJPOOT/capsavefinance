<?php

namespace App\Inv\Repositories\Models;

use DB;
use File;

use App\Inv\Repositories\Models\AppDocumentFile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserFile extends Authenticatable
{

    use Notifiable;
 

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
        'updated_by'
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
        $inputArr = UserFile::arrayInputData($attributes, $docId);
        foreach ($inputArr as $value) {
            $file = UserFile::create($value);
            $file = AppDocumentFile::creates($attributes, $file->file_id);
        }
        
        return $file;
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
       $userId = 1;
        $inputArr = [];
        $count = count($attributes['bank_docs']);
        for ( $i=0; $i < $count; $i++) 
        {   
            if($attributes['bank_docs'][$i]) {
                if(!Storage::exists('/public/user/' .$userId. '/' .$attributes['appId'])) {
                    Storage::makeDirectory('/public/user/' .$userId. '/' .$attributes['appId'], 0775, true);
                }
                $path = Storage::disk('public')->put('/user/' .$userId. '/' .$attributes['appId'], $attributes['bank_docs'][$i], null);
                $inputArr[$i]['file_path'] = $path;
            }
             
            $inputArr[$i]['file_type'] = $attributes['bank_docs'][$i]->getClientMimeType();
            $inputArr[$i]['file_name'] = $attributes['bank_docs'][$i]->getClientOriginalName();
            $inputArr[$i]['file_size'] = $attributes['bank_docs'][$i]->getClientSize();
            $inputArr[$i]['file_encp_key'] =  md5('2');
            $inputArr[$i]['created_by'] = 1;
            $inputArr[$i]['updated_by'] = 1;
        }
//        dd($inputArr);
        return $inputArr;
  }
  
}
  

