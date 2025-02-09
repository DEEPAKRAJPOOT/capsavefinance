<?php

namespace App\Inv\Repositories\Entities\Document;

use Carbon\Carbon;

use App\Inv\Repositories\Contracts\DocumentInterface;
use App\Inv\Repositories\Models\AppDocumentFile;
use App\Inv\Repositories\Models\AppDocument;
use App\Inv\Repositories\Models\AppDocProduct;
use App\Inv\Repositories\Models\BizOwner;
use App\Inv\Repositories\Models\UserFile;
use App\Inv\Repositories\Models\OfferAdhocDocument;
use App\Inv\Repositories\Contracts\Traits\AuthTrait;
use App\Inv\Repositories\Models\ProgramDoc;

class DocumentRepository implements DocumentInterface
{

    public function __construct()
    {
        // parent::__construct();
    }

    /**
     * Create method
     *
     * @param array $attributes
     */
    public function all()
    {
        return AppDocumentFile::with('user')->get();
    }

    /**
     * Create method
     *
     * @param array $attributes
     */
    protected function create(array $attributes)
    {
        
        return AppDocumentFile::create($attributes);
    }

    /**
     * Find method
     *
     * @param mixed $id
     * @param array $columns
     */
    public function find($id, $columns = array('*'))
    {
       
        return (AppDocumentFile::find($id)) ?: false;
    }

    /**
     * Update method
     *
     * @param array $attributes
     */
    protected function update(array $attributes, $id)
    {
        $result = AppDocumentFile::update((int) $id, $attributes);

        return $result ?: false;
    }

    /**
     * Delete method
     *
     * @param mixed $ids
     */
    protected function delete($ids)
    {
        //
    }
    
    /**
     * find application required documents
     *
     * @param mixed $ids
     */
    
    public function findRequiredDocs($userId, $appId){
        
        $result = AppDocument::where('user_id', $userId)
                ->where('app_id', $appId)
                ->with('document')
                ->whereHas('document')
                ->get();
        
        return $result ?: false;
    }

    /**
     * application pre and post document
     *
     * @param mixed $ids
     */
    
    public function appPPDocuments($requiredDocs, $appId){
        foreach ($requiredDocs as $key => $value) {
            $result[$value->ppDocument->doc_name] = AppDocumentFile::where('doc_id', $value->doc_id)
                    ->where('app_id', $appId)
                    ->where('is_active', 1)
                    ->with('userFile')
                    ->get();
        }
        
        return (!empty($result)) ? $result : false;
    }
    /**
     * find application pre and post santioned required documents
     *
     * @param mixed $ids
     */
    
    public function findPPRequiredDocs($userId, $appId, $productId){
        $result = AppDocument::where('user_id', $userId)
                ->join('product_doc', 'product_doc.doc_id', '=', 'app_doc.doc_id') 
                ->where('app_id', $appId)
                ->where('product_doc.is_active', 1)
                ->where('product_doc.product_id', $productId)
                //->whereHas('app_doc_product', function ($query) use ($productId) {
                //    $query->where(function ($q) use ($productId) {
                //        $q->where('product_id', $productId);
                //    });
                //})
                ->with('ppDocument')
                ->whereHas('ppDocument')
                ->get();
        
        return $result ?: false;
    }
    
    
    /**
     * find application required documents
     *
     * @param mixed $ids
     */
    
    public function isUploadedCheck($userId, $appId){
        $result = AppDocument::where('user_id', $userId)
                ->where('app_id', $appId)
                ->where('is_upload', 0)
                ->get();
        
        return $result ?: false;
    }
    
    
    /**
     * find application required documents
     *
     * @param mixed $ids
     */
    
    public function appOwnerDocCheck($appId, $docId, $ownerId){
        $result = AppDocumentFile::where('app_id', $appId)
                ->where('doc_id', $docId)
                ->where('biz_owner_id', $ownerId)
                ->first();
        
        return $result ?: false;
    }
    
    public function getAppDocFileById($appDocFileId){
        $result = AppDocumentFile::find($appDocFileId);
        
        return $result ?: false;
    }
    /**
     * save document method
     *
     * @param mixed $ids
     */
    
    public function saveDocument($attributes = [], $docId, $userId){
        /**
         * Check Valid Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions('Please send an array');
        }
        if (empty($attributes)) {
            throw new BlankDataExceptions('No Data Found');
        }
        return UserFile::creates($attributes, $docId, $userId);
    }

    public function updateDocument($data = [], $appDocFileId){
        if (!is_array($appDocFileId)) {
            return AppDocumentFile::where('app_doc_file_id', $appDocFileId)
                ->update($data);
        } else {
            return AppDocumentFile::whereIn('app_doc_file_id', $appDocFileId)
                    ->update($data);
        }
    }
    
    
    /**
     * save file method
     *
     * @param mixed $requests
     */
    
    public function saveFile($attributes = []){
        /**
         * Check Valid Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions('Please send an array');
        }
        if (empty($attributes)) {
            throw new BlankDataExceptions('No Data Found');
        }
        
        return UserFile::create($attributes);
    }

     /**
     * save file method
     *
     * @param mixed $requests
     */
    
    public function saveAdhocFile($attributes = []){
        /**
         * Check Valid Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions('Please send an array');
        }
        if (empty($attributes)) {
            throw new BlankDataExceptions('No Data Found');
        }
        
        return OfferAdhocDocument::create($attributes);
    }
    
    
    /**
     * save app document method
     *
     * @param mixed $requests
     */
    
    public function saveAppDoc($attributes = []){
        /**
         * Check Valid Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions('Please send an array');
        }
        if (empty($attributes)) {
            throw new BlankDataExceptions('No Data Found');
        }
        return AppDocumentFile::create($attributes);
    }
    
    
    /**
     * save app document method
     *
     * @param mixed $requests
     */
    
    public function updateAppDocFile($ownerDocFile = [], $fileId){
        $doc = AppDocumentFile::find($ownerDocFile->app_doc_file_id);
        if($doc)
        {
            $doc->file_id = $fileId;
            $doc->is_ovd_enabled = 1;
            $doc->save();
        }
        
        return $doc;
    }


    /**
     * save app document method
     *
     * @param mixed $requests
     */

    public function updateAppDocNumberFile($ownerDocFile = [], $doc_id_no){
        $doc = AppDocumentFile::find($ownerDocFile->app_doc_file_id);
        if($doc)
        {
            $doc->doc_id_no = $doc_id_no;
            $doc->save();
        }

        return $doc;
    }

    public function updateAppDocNumberFilewithArray($ownerDocFile = [], $doc_id_no){
        $doc = AppDocumentFile::find($ownerDocFile['app_doc_file_id']);
        if($doc)
        {
            $doc->doc_id_no = $doc_id_no;
            $doc->save();
        }

        return $doc;
    }
    
    /**
     * application document
     *
     * @param mixed $ids
     */
    
    public function appDocuments($requiredDocs, $appId){
        foreach ($requiredDocs as $key => $value) {
            $result[$value->document->doc_name] = AppDocumentFile::where('doc_id', $value->doc_id)
                    ->where('app_id', $appId)
                    ->where('is_active', 1)
                    ->with('userFile')
                    ->get();
            
        }
        
        return (!empty($result)) ? $result : false;
    }
    
    /**
     * application document
     *
     * @param mixed $ids
     */
    
    public function deleteDocument($appDocFileId){
        
        $appDocFile = AppDocumentFile::find($appDocFileId);
        $response = AppDocumentFile::deletes($appDocFileId);
        
        if($response){
            $result = UserFile::deletes($appDocFile->file_id);
        }
        
        return (!empty($result)) ? $result : false;
    }
    
    /**
     * application document
     *
     * @param mixed $ids
     */
    
    public function UpdateAppDocument($arrayToUpdate, $appDocFileId){
        $doc = AppDocumentFile::find($appDocFileId);
        if($doc){
            foreach ($arrayToUpdate as $column => $value) {
               $doc->$column = $value;
            }
        }
        $doc->save();
        return $doc;
    }
    
    /**
     * application document
     *
     * @param mixed $ids
     */
    
    public function getFileByFileId($FileId){
        
        /**
         * Check Valid Array
         */
        if (!isset($FileId)) {
            throw new BlankDataExceptions('No Data Found');
        }
        
        return UserFile::find($FileId);
    }
    
    
    /**
     * Save Required Documents for Application
     * 
     * @param array $attributes
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function saveAppRequiredDocs($attributes=[])
    {
        return AppDocument::saveAppRequiredDocs($attributes);
    }
     
    
    /**
     * Save Required Documents for Application
     * 
     * @param array $attributes
     * @return mixed
     * @throws InvalidDataTypeExceptions
     */
    public function saveAppDocProduct($attributes=[])
    {
        return AppDocProduct::create($attributes);
    }
    
    /**
     * Check the required document for application
     * 
     * @param integer $app_id
     * @param integer $doc_id
     * 
     * @return boolean
     */
    public function isAppDocFound($app_id, $doc_id) 
    {
        return AppDocument::isAppDocFound($app_id, $doc_id);
    }  


    public function getMultipleFileByFileId(array $arrFileId){
        
        /**
         * Check Valid Array
         */
        if (!is_array($arrFileId)) {
            throw new BlankDataExceptions('No Data Found');
        }
        
        return UserFile::whereIn('file_id', $arrFileId)->get();
    }  

    /**
     * find application required documents
     *
     * @param mixed $ids
     */  
    public function findRequiredDocsByStage($userId, $appId, $wfStageCode='doc_upload')
    {
        $result = AppDocument::findRequiredDocsByStage($userId, $appId, $wfStageCode);        
        return $result ?: [];
    }    


        /**
     * save file method
     *
     * @param mixed $requests
     */
    
    public function deleteFile($attributes){
        /**
         * Check Valid Array
         */
        $fileId = $attributes->file_id;
        
        return UserFile::deleteFile($fileId);
    }
    
    /**
     * Save Nach document method
     *
     * @param mixed $ids
     */
    public function saveNachDocument($attributes = [], $userId){
        /**
         * Check Valid Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions('Please send an array');
        }
        if (empty($attributes)) {
            throw new BlankDataExceptions('No Data Found');
        }
        return UserFile::saveNachFile($attributes, $userId);
    }

    /**
     * Update method diabled is_ovd check
     *
     * @param array $attributes
     */
    public function disableIsOVD($where)
    {
        $result = AppDocumentFile::where($where)->orderBy('app_doc_file_id', 'DESC')->first()->toArray();

        if($result) {
            $updateData = [
                'is_ovd_enabled' => 0
            ];
            $where = [
                'app_doc_file_id' => $result['app_doc_file_id']
            ];
            AppDocumentFile::where($where)->update($updateData); 
        }
        return $result ?: false;
    }    
}