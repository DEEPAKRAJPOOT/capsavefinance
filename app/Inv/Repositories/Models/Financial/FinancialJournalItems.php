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

    public static function getTallyTxns(array $where = array()) {
      $query = "SELECT tally_entry_id, batch_no, is_debit_credit  , tally_entry.trans_type, voucher_type, voucher_no, voucher_date,transaction_date, invoice_no,fact_voucher_number, invoice_date, ledger_name, tally_entry.amount, ref_no, ref_amount, acc_no, ifsc_code, bank_name, cheque_amount, cross_using, mode_of_pay,utr_no, inst_no, inst_date, favoring_name, remarks, narration, is_updated is_posted,company_bank_name,company_bank_acc,trans.trans_type AS trans_type_id,trans.entry_type AS entry_type,trans.parent_trans_id FROM rta_tally_entry AS tally_entry LEFT JOIN rta_transactions AS trans ON trans.trans_id = tally_entry.transactions_id ";
        $cond = 'WHERE voucher_no is not null ';
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                $wh[] = "$key = '$value'";
            }
           $cond .= ' AND ' .implode(' AND ', $wh);
        }
        $sql = $query .$cond;
        $sql .= " ORDER BY voucher_no ASC ";
        $result = \DB::SELECT(\DB::raw($sql));
        return $result;
    }

    public static function getAllBatches(array $where = array()) {
      $query = "SELECT * FROM rta_tally ";
        $cond = 'WHERE is_active = 1 and';
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                $wh[] = "$key = '$value'";
            }
           $cond .= implode(' AND ', $wh);
        }else{
          $cond .= ' 1';  
        }
        $sql = $query .$cond;
        $sql .= " ORDER BY id DESC ";
        $result = \DB::SELECT(\DB::raw($sql));
        return $result;
    }

    public static function getLatestBatch(array $where = array()) {
      $query = "SELECT * FROM rta_tally ";
        $cond = 'WHERE ';
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                $wh[] = "$key = '$value'";
            }
           $cond .=  implode(' AND ', $wh);
        }else{
          $cond .= ' 1';  
        }
        $sql = $query .$cond;
        $sql .= " ORDER BY created_at DESC LIMIT 1";
        $result = \DB::SELECT(\DB::raw($sql));
        return !empty($result[0]) ? $result[0] : [] ;
    }

    public static function getPaymentFactTxns(array $where = array()) {
        $query = "SELECT tally_entry_id, batch_no, is_debit_credit entry_type, trans_type, voucher_type, voucher_no, voucher_date,transaction_date, invoice_no,a.fact_voucher_number, invoice_date, ledger_name, amount, ref_no, ref_amount, a.acc_no, ifsc_code, a.bank_name, cheque_amount, cross_using, mode_of_pay,utr_no, inst_no, inst_date, favoring_name, remarks, narration, is_updated is_posted,company_bank_name,company_bank_acc, cc.bank_name as bank, dd.acc_no as bank_acc_no FROM rta_tally_entry AS a left join ( SELECT b.acc_no,b.fact_voucher_number FROM rta_tally_entry AS b WHERE  b.batch_no = '".$where['batch_no']."' AND b.transactions_id IS NULL and b.acc_no <> '' GROUP BY b.fact_voucher_number ) as dd on dd.fact_voucher_number = a.fact_voucher_number left join ( SELECT b.bank_name,b.fact_voucher_number FROM rta_tally_entry AS b WHERE  b.batch_no = '".$where['batch_no']."' and b.bank_name <> '' GROUP BY b.fact_voucher_number ) as cc on cc.fact_voucher_number = a.fact_voucher_number WHERE transactions_id is not null AND voucher_type IN ('Payment','Receipt') AND batch_no = '".$where['batch_no']."' ORDER BY voucher_no ASC";
        return \DB::SELECT(\DB::raw($query));
    }

      public static function getJournalReportData(array $where = array()) {
        $query = "SELECT * FROM rta_fact_journal_entry ";
          $cond = 'WHERE voucher_no is not null ';
          if (!empty($where)) {
              foreach ($where as $key => $value) {
                  $wh[] = "$key = '$value'";
              }
             $cond .= ' AND ' .implode(' AND ', $wh);
          }
          $sql = $query .$cond;
          $sql .= " ORDER BY fact_journal_entry_id ASC ";
          $result = \DB::SELECT(\DB::raw($sql));
          return $result;
      }

      public static function getPaymentReportData(array $where = array()) {
        $query = "SELECT * FROM rta_fact_payment_entry ";
          $cond = 'WHERE voucher is not null ';
          if (!empty($where)) {
              foreach ($where as $key => $value) {
                  $wh[] = "$key = '$value'";
              }
             $cond .= ' AND ' .implode(' AND ', $wh);
          }
          $sql = $query .$cond;
          $sql .= " ORDER BY fact_payment_entry_id ASC ";
          $result = \DB::SELECT(\DB::raw($sql));
          return $result;
        }
      
}
