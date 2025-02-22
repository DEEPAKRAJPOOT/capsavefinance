<?php

namespace App\Inv\Repositories\Models;

use DB;
use File;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Inv\Repositories\Factory\Models\BaseModel;

class AppDocument extends BaseModel
{
 

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'app_doc';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'app_doc_id';
    
    /**
     * Maintain created_at and updated_at automatically
     *
     * @var boolean
     */
    public $timestamps = true;

    /**
     * Maintain created_by and updated_by automatically
     *
     * @var boolean
     */
    public $userstamps = true;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'app_id',
        'doc_id',
        'is_upload',
        'rcu_status',
        'is_required',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
     ];
    
    public function document()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\Master\Documents', 'doc_id')->whereIn('doc_type_id', [1]);;
    }

    public function app_doc_product()
    {
        return $this->hasOne('App\Inv\Repositories\Models\AppDocProduct', 'app_doc_id');
    }
    public function ppDocument()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\Master\Documents', 'doc_id')
            ->whereIn('doc_type_id', [2,3,4]);
    }
    
    /**
     * Managing inputs as required Array
     *
     * @param Array $attributes
     *
     * @return Array
     */
    
    public static function getRcuLists($appId)
    {  
        return AppDocument::with('rcuDoc')
                ->whereHas('rcuDoc')
                ->where('app_id', $appId)
                ->where('is_upload', 1)
                ->get();
        
    }
    
    public function rcuDoc()
    {
        return $this->belongsTo('App\Inv\Repositories\Models\Master\Documents', 'doc_id')->where('is_rcu', 1);
    }
    
    /**
     * Save Required Documents for Application
     * 
     * @param array $attributes
     * @return mixed
     */
    public static function saveAppRequiredDocs($attributes) 
    {        
        return self::create($attributes);
    }
 
    /**
     * Check the required document for application
     * 
     * @param integer $app_id
     * @param integer $doc_id
     * 
     * @return boolean
     */
    public static function isAppDocFound($app_id, $doc_id) 
    {
        $appDocCheck = self::where('app_id', $app_id)
                ->where('doc_id', $doc_id)
                ->count();
        
        return $appDocCheck > 0 ? true : false;
    } 
    
    /**
     * Find application required documents
     *
     * @param mixed $ids
     */  
    public static function findRequiredDocsByStage($userId, $appId, $wfStageCode='doc_upload')
    {
        
        $result = AppDocument::join('prgm_doc', 'prgm_doc.doc_id', '=', 'app_doc.doc_id')
                ->join('wf_stage', 'prgm_doc.wf_stage_id', '=', 'wf_stage.wf_stage_id') 
                ->where('app_doc.user_id', $userId)
                ->where('app_doc.app_id', $appId)        
                ->where('wf_stage.stage_code', $wfStageCode)        
                ->with('document')
                ->get();
        
        return $result ?: false;
    }
    
    /**
     * Check any one post sanction document is uploaded or not
     * 
     * @param integer $appId
     * @return boolean
     */
    public static function isDocsUploaded($appId, $docIds=[])
    {
        $appDocCheck = self::where('app_id', $appId)
                ->where('is_upload', 1)
                ->whereIn('doc_id', $docIds)
                ->count();
        
        return $appDocCheck > 0 ? true : false;        
    }
    
    /**
     * Get Application Documents
     * 
     * @param array $whereCond
     * @return mixed
     */
    public static function getAppDocuments($whereCond=[])
    {
        $query = self::select('*');
        if (count($whereCond) > 0) {
            $query->where($whereCond);
        }
        $result = $query->get();
        return $result;
    }     
}
  

