<?php

namespace App\Inv\Repositories\Models\Financial;

use App\Inv\Repositories\Factory\Models\BaseModel;

class FinancialJeConfig extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'financial_je_config';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'je_config_id';

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
        'trans_config_id',
        'journal_id',
        'created_at',   
        'created_by',        
        'updated_at',
        'updated_by'        
    ];

    public static function saveJeData($data){
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }        
        return self::create($data);       
    }

    public static function getAllJeConfig(){
        $result = \DB::select("SELECT rta_financial_je_config.je_config_id,rta_financial_je_config.trans_config_id, rta_financial_je_config.journal_id, rta_financial_trans_config.trans_type, rta_financial_journals.name as journal_name, rta_financial_journals.journal_type,A.variable_name FROM rta_financial_je_config
        JOIN rta_financial_trans_config ON (rta_financial_trans_config.trans_config_id=rta_financial_je_config.trans_config_id)
        JOIN rta_financial_journals ON (rta_financial_journals.id=rta_financial_je_config.journal_id)
        JOIN 
        (SELECT rfvtc.trans_config_id,GROUP_CONCAT(distinct(rfv.name)) AS variable_name FROM rta_financial_variables_trans_config
         as rfvtc JOIN rta_financial_variables as rfv ON (rfv.id=rfvtc.variable_id)
        GROUP BY rfvtc.trans_config_id) AS A ON (A.trans_config_id=rta_financial_je_config.trans_config_id)
        ORDER BY rta_financial_je_config.je_config_id DESC");
        return $result;    
    }

    public static function getJeConfigByjeConfigId($jeConfigId){
        $result = \DB::select("SELECT rta_financial_je_config.je_config_id,rta_financial_je_config.trans_config_id, rta_financial_je_config.journal_id, rta_financial_trans_config.trans_type, rta_financial_journals.name as journal_name, rta_financial_journals.journal_type,A.variable_name FROM rta_financial_je_config
        JOIN rta_financial_trans_config ON (rta_financial_trans_config.trans_config_id=rta_financial_je_config.trans_config_id)
        JOIN rta_financial_journals ON (rta_financial_journals.id=rta_financial_je_config.journal_id)
        JOIN 
        (SELECT rfvtc.trans_config_id,GROUP_CONCAT(distinct(rfv.name)) AS variable_name FROM rta_financial_variables_trans_config
         as rfvtc JOIN rta_financial_variables as rfv ON (rfv.id=rfvtc.variable_id)
        GROUP BY rfvtc.trans_config_id) AS A ON (A.trans_config_id=rta_financial_je_config.trans_config_id)
        Where rta_financial_je_config.je_config_id=?", [$jeConfigId]);
        return $result ? $result[0] : false;    
    }

    public static function getAllJeConfigByTransConfigId($transConfigId){
        $result = \DB::select("SELECT rta_financial_je_config.je_config_id,rta_financial_je_config.trans_config_id, rta_financial_je_config.journal_id, rta_financial_trans_config.trans_type, rta_financial_journals.name as journal_name, rta_financial_journals.journal_type,A.variable_name,A.sys_func_name FROM rta_financial_je_config
        JOIN rta_financial_trans_config ON (rta_financial_trans_config.trans_config_id=rta_financial_je_config.trans_config_id)
        JOIN rta_financial_journals ON (rta_financial_journals.id=rta_financial_je_config.journal_id)
        JOIN 
        (SELECT rfvtc.trans_config_id,GROUP_CONCAT(distinct(rfv.name)) AS variable_name,GROUP_CONCAT(distinct(rfv.sys_func_name)) AS sys_func_name FROM rta_financial_variables_trans_config
         as rfvtc JOIN rta_financial_variables as rfv ON (rfv.id=rfvtc.variable_id)
        GROUP BY rfvtc.trans_config_id) AS A ON (A.trans_config_id=rta_financial_je_config.trans_config_id)
        WHERE rta_financial_je_config.trans_config_id = ?", [$transConfigId]);
        return empty($result) ? false : $result;    
    }

    public static function getAllTxns(){
        /*$result = \DB::select("SELECT 
            journal_entries.invoice_id, journals.name as journal_name, trxn.`date`, 
            users.f_name, users.m_name, users.l_name, trxn.label as naration, 
            trxn.`debit_amount`, journal_entries.reference as dr_ref_no, trxn.`debit_amount` as dr_ref_amount,
            trxn.`credit_amount`, journal_entries.reference as cr_ref_no,  trxn.`credit_amount` as cr_ref_amount,
            IF(jiconf.value_type = 0, 'Debit', 'Credit') as transtype, if(jiconf.is_partner = '0' , 'No', 'Yes') as is_partner,
            journal_entries.entry_type
            FROM 
            rta_financial_journal_items as trxn, 
            rta_financial_journals as journals, 
            rta_financial_ji_config as jiconf, 
            rta_financial_journal_entries as journal_entries,
            rta_disbursal as disbursal, 
            rta_users as users
            WHERE 
            trxn. journal_id = journals.id 
            AND trxn.ji_config_id = jiconf.ji_config_id 
            AND trxn.journal_entry_id = journal_entries.journal_entry_id 
            AND journal_entries.invoice_id = disbursal.invoice_id 
            AND disbursal.user_id = users.user_id 
            ");*/
         $result = self::select('journal_entries.invoice_id', 'journals.name as journal_name', 'trxn.date', 
            'users.f_name', 'users.m_name', 'users.l_name', 
             // \DB::raw("CONCAT(users.f_name,' ',users.m_name,' ',users.l_name) as ledger_Name"),
            'trxn.debit_amount', 'journal_entries.reference as dr_ref_no', 'trxn.debit_amount as dr_ref_amount',
            'trxn.credit_amount', 'journal_entries.reference as cr_ref_no',  'trxn.credit_amount as cr_ref_amount',
            \DB::raw("IF(value_type = 0, 'Debit', 'Credit') as transtype"), \DB::raw("IF(is_partner= 0, 'No', 'Yes') as is_partner"), 
            'journal_entries.entry_type', 'trxn.label as naration')
           ->from('financial_journal_items as trxn')
           ->join('financial_journals as journals', 'trxn.journal_id' ,'=' ,'journals.id')
           ->join('financial_ji_config as jiconf', 'trxn.ji_config_id', '=' , 'jiconf.ji_config_id')
           ->join('financial_journal_entries as journal_entries', 'trxn.journal_entry_id', '=', 'journal_entries.journal_entry_id')
           ->join('disbursal', 'journal_entries.invoice_id', '=', 'disbursal.invoice_id')
           ->join('users', 'disbursal.user_id', '=', 'users.user_id')
           ->get();
        return empty($result) ? false : $result;    
    }
}
