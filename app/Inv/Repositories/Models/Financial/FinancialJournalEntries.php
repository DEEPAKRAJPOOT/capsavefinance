<?php

namespace App\Inv\Repositories\Models\Financial;

use App\Inv\Repositories\Factory\Models\BaseModel;

class FinancialJournalEntries extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'financial_journal_entries';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'journal_entry_id';

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
        'journal_id',
        'entry_type',
        'reference', 
        'date',            
        'created_at',   
        'created_by',        
        'updated_at',
        'updated_by'
    ];

}
