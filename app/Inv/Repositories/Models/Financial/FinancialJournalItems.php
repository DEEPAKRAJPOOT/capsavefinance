<?php

namespace App\Inv\Repositories\Models\Financial;

use App\Inv\Repositories\Factory\Models\BaseModel;

class FinancialJournalItems extends BaseModel {
    /* The database table used by the model.
     *
     * @var string
     */

    protected $table = 'financial_journal_items';

    /**
     * Custom primary key is set for the table
     *
     * @var integer
     */
    protected $primaryKey = 'journal_item_id';

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
        'ji_config_id',
        'date',
        'account_id', 
        'biz_id',
        'label',    
        'debit_amount',
        'credit_amount', 
        'journal_id', 
        'journal_entry_id',        
        'created_at',   
        'created_by',        
        'updated_at',
        'updated_by'
    ];

    public static function saveJournalItems($data) {
        if (!is_array($data)) {
            throw new InvalidDataTypeExceptions(trans('error_message.invalid_data_type'));
        }     
        return self::create($data);            
    }

    public static function getTransactions() {
        $result = self::select('financial_journal_items.*', 'financial_accounts.account_code', 'financial_accounts.account_name','financial_journal_entries.reference','financial_journal_entries.entry_type','financial_journal_entries.invoice_id','financial_journal_entries.trans_id','financial_journals.name as journals_name','financial_journals.journal_type as journal_type','users.f_name','users.m_name','users.l_name')
            ->join('financial_accounts','financial_accounts.id','=','financial_journal_items.account_id')
            ->join('financial_journal_entries','financial_journal_entries.journal_entry_id','=','financial_journal_items.journal_entry_id')
            ->join('financial_journals','financial_journals.id','=','financial_journal_items.journal_id')
            ->join('users','users.user_id','=','financial_journal_entries.user_id')
            ->orderBy('journal_item_id', 'DESC');
        return $result;
    }

    public static function getTally(array $where = array())
    {
        $query = "SELECT rta_tally.transaction_id, rta_tally.batch_id, rta_tally.amount, rta_tally.tally_at,  CONCAT(rta_users.f_name,' ', rta_users.l_name) fullname, rta_transactions.biz_id, rta_transactions.virtual_acc_id, rta_user_bank_account.acc_name, rta_user_bank_account.acc_no, rta_mst_bank.bank_name, rta_user_bank_account.ifsc_code ,rta_transactions.disbursal_id, rta_transactions.trans_date, rta_trans_type.trans_name, rta_transactions.chrg_trans_id, rta_transactions.amount, rta_transactions.settled_amount, IF(rta_transactions.entry_type = 0 , 'Debit', 'Credit') entry_type, IF(rta_transactions.gst = 1, 'Yes', 'No') gst_applicable, rta_transactions.cgst, rta_transactions.sgst, rta_transactions.igst, rta_transactions.tds_per,(CASE WHEN rta_transactions.is_settled = 1 THEN 'Partally Settled' WHEN rta_transactions.is_settled = 2 THEN 'Fully Settled' ELSE 'New' END) as is_settled, (CASE WHEN rta_transactions.mode_of_pay = 1 THEN 'Online' WHEN rta_transactions.mode_of_pay = 2 THEN 'Cheque' WHEN rta_transactions.mode_of_pay = 3 THEN 'Cash' ELSE 'NA' END) as mode_of_pay, rta_transactions.utr_no, rta_transactions.unr_no, rta_transactions.cheque_no, (CASE WHEN rta_transactions.trans_by = 1 THEN 'Bulk' WHEN rta_transactions.trans_by = 2 THEN 'Excel' ELSE 'NA' END) as trans_by, (CASE WHEN rta_transactions.pay_from = 1 THEN 'Supplier' WHEN rta_transactions.pay_from = 2 THEN 'Buyer' ELSE 'Lender' END) as pay_from, rta_transactions.txn_id, rta_transactions.comment, rta_transactions.created_at, CONCAT(rta_users1.f_name, ' ', rta_users1.m_name, ' ', rta_users1.l_name) created_by  FROM `rta_tally` JOIN rta_transactions ON rta_tally.transaction_id = rta_transactions.trans_id JOIN rta_mst_trans_type rta_trans_type ON rta_transactions.trans_type = rta_trans_type.id JOIN rta_users ON rta_transactions.user_id = rta_users.user_id JOIN rta_users rta_users1 ON rta_transactions.created_by = rta_users1.user_id JOIN rta_user_bank_account ON rta_user_bank_account.user_id = rta_transactions.user_id JOIN rta_mst_bank ON rta_mst_bank.id = rta_user_bank_account.bank_id";
        $cond = '';
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                $wh[] = "$key = '$value'";
            }
           $cond = ' WHERE ' .implode(' AND ', $wh);
        }
        $sql = $query .$cond;
       $result = \DB::SELECT(\DB::raw($query));
       return $result;
    }

    public static function getTallyTxns(array $where = array()) {
      $query = "SELECT batch_no, IF(is_debit_credit = 0 , 'Debit', 'Credit') entry_type, trans_type, (CASE WHEN tally_trans_type_id = 1 THEN 'Payment' WHEN tally_trans_type_id = 2 THEN 'Payment Receipt' ELSE 'Journal' END) voucher_type, tally_voucher_code voucher_code, tally_voucher_date voucher_date, invoice_no, invoice_date, ledger_name, amount, ref_no, ref_amount, acc_no, ifsc_code, bank_name, cheque_amount, cross_using, (CASE WHEN mode_of_pay = 1 THEN 'Online' WHEN mode_of_pay = 2 THEN 'Cheque' WHEN mode_of_pay = 3 THEN 'Cash' ELSE 'NA' END) as mode_of_pay, inst_no, inst_date, favoring_name, remarks, narration, is_updated is_posted FROM rta_tally_entry ORDER BY voucher_date, trans_type ASC";
        $cond = '';
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                $wh[] = "$key = '$value'";
            }
           $cond = ' WHERE ' .implode(' AND ', $wh);
        }
        $sql = $query .$cond;
       $result = \DB::SELECT(\DB::raw($query));
       return $result;
    }
}
