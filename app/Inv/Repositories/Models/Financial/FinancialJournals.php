<?php

namespace App\Inv\Repositories\Models\Financial;

use App\Inv\Repositories\Factory\Models\BaseModel;

class FinancialJournals extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'financial_journals';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'id';

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
        'name',
        'journal_type', 
        'is_active',
        'created_at',   
        'created_by',        
        'updated_at',
        'updated_by'
    ];

    public static function getAllJournal() 
    {
        $result = self::select('id','name','journal_type', 'is_active')->orderBy('id', 'DESC');
        return $result;
    }

    public static function saveJournalData($data, $journalId = null){
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }       
        if(isset($journalId) && !empty($journalId)) {
            $updObj = self::where('id', $journalId);
            return $updObj->update($data);
        } else {
            return self::create($data); 
        }      
    }
}
