<?php

namespace App\Inv\Repositories\Entities\Document;

use Carbon\Carbon;

use App\Inv\Repositories\Contracts\DocumentInterface;
use App\Inv\Repositories\Models\AppDocumentFile;
 use App\Inv\Repositories\Contracts\Traits\AuthTrait;

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
     * save document method
     *
     * @param mixed $ids
     */
    
    public function saveDocument($attributes = [], $mstDocId){
        /**
         * Check Valid Array
         */
        if (!is_array($attributes)) {
            throw new InvalidDataTypeExceptions('Please send an array');
        }
        if (empty($attributes)) {
            throw new BlankDataExceptions('No Data Found');
        }
       
        return AppDocumentFile::creates($attributes, $mstDocId,);
    }
}