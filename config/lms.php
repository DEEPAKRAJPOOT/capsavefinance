<?php

/* 
  array define for mantain LMS Configuration
  created by
  @Sudesh Kumar
 
*/

return [
    'DAILY_REPORT_MAIL' => (env('DAILY_REPORT_MAIL'))?explode(',', env('DAILY_REPORT_MAIL')):[],
    'EOD_FAILURE_MAIL' => explode(',',env('EOD_FAILURE_MAIL','')),
    'LMS_STATUS' => env('LMS_STATUS', 0),
    'NPA_DAYS' => env('NPA_DAYS', 90),
    'TRANS_TYPE' => [    
       'REVERSE' => '2',
       'TDS'=>'7',
       'CANCEL'=>'8',
       'INTEREST'=>'9',
       'MARGIN'=>'10',
       'PAYMENT_DISBURSED' =>'16',
       'REPAYMENT'=> '17',
       'FAILED'=>'18',
       'INTEREST_OVERDUE'=>'33',
       'ADJUSTMENT'=>'31',
       'REFUND'=>'32',
       'NON_FACTORED_AMT' => '35',
       'WAVED_OFF'=>'36',
       'WRITE_OFF'=>'37',
        /*
        'PAYMENT_RECEIVED' => '1',
        'DISCOUNT_ON_PAYMENT' => '3',
        'PROCESSING_FEE' => '4',
        'CHEQUE_BOUNCE'=>'5',
        'LOAN_DISBURSED'=>'6',
        'INCOME'=>'8',
        'PENALTY'=>'11',
        'GST'=>'12',
        'CGST'=>'13',
        'SGST'=>'14',
        'IGST'=>'15',
        'DOCUMENT_FEE'=>'20',
        'NACH_BOUNCE_CHARGE'=>'24',
        'SOA_PULL'=>'25',
        'PREPAYMENT_CHARGE'=>'28',
        'OTHER_CHARGE'=>'29',
        'INVOICE_KNOCKED_OFF'=>'30',
        'INTEREST_REFUND'=>'31',
        'INTEREST_PAID'=>'32',
        'INVOICE_PARTIALLY_KNOCKED_OFF'=>'34',
        */
    ],
    'STATUS_ID' => [
        'DISBURSED'=>'12',
        'PARTIALLY_PAYMENT_SETTLED' => '13',
        'PAYMENT_SETTLED' => '15'
    ],
    'DISBURSE_TYPE' => [
        'ONLINE' => '1',
        'OFFLINE'=>'2'
    ],
    'REQUEST_TYPE' => [
        'REFUND' => '1',
        'ADJUSTMENT' => '2',
        'WAVE_OFF' => '3',
    ],
    'REQUEST_STATUS' => [
        'NEW_REQUEST' => '1',
        'DELETED' => '2',
        'IN_PROCESS' => '3',
        'REJECTED' => '4',
        'APPROVED' => '5',
        'REFUND_QUEUE' => '6',
        'SEND_TO_BANK' => '7',
        'PROCESSED' => '8'
    ],
    'WF_STAGE_STATUS' => [
        'PENDING' => '0',
        'COMPLETED' => '1',
        'IN_PROGRESS' => '2',
    ],
    'REQUEST_TYPE_DISP' => [
        '1' => 'Refund',
        '2' => 'Adjustment',
        '3' => 'Waveoff',
    ],
    'REQUEST_STATUS_DISP' => [
        '1' => ['SYSTEM' => 'New Request', 'USER' => 'New Request'],
        '2' => ['SYSTEM' => 'Deleted', 'USER' => 'Delete'],
        '3' => ['SYSTEM' => 'Pending', 'USER' => 'Pending'],
        '4' => ['SYSTEM' => 'Rejected', 'USER' => 'Reject'],
        '5' => ['SYSTEM' => 'Approved', 'USER' => 'Approve'],
        '6' => ['SYSTEM' => 'Refund Queue', 'USER' => 'Refund Queue'],
        '7' => ['SYSTEM' => 'Sent to Bank', 'USER' => 'Sent to Bank'],
        '8' => ['SYSTEM' => 'Processed', 'USER' => 'Process'],
    ],
    'EOD_PROCESS_STATUS' => [
        'RUNNING' => 0,
        'COMPLETED' => 1,
        'STOPPED' => 2,
        'FAILED' => 3,
        'WATING' => 4,
    ],
    'EOD_PROCESS_STATUS_LIST' => [
        0 => 'Running',
        1 => 'Completed',
        2 => 'Stopped',
        3 => 'Failed',
        4 => 'WATING',
    ],
    'EOD_PROCESS_ROUTES' => [
        'update_bulk_invoice',
        'update_invoice_approve',
        'lms_refund_new',
        'lms_refund_pending',
        'lms_refund_approved',
        'request_list',
        'lms_refund_sentbank',
        'lms_refund_refunded',
        'backend_upload_all_invoice',
        'backend_get_invoice',
        'backend_get_approve_invoice',
        'backend_get_disbursed_invoice',
        'backend_get_sent_to_bank',
        'backend_get_failed_disbursment',
        'backend_get_disbursed',
        'backend_get_repaid_invoice',
        'backend_get_reject_invoice',
        'backend_get_exception_cases',
        'lms_disbursal_request_list',
        'lms_disbursed_list',
        'payment_list',
        'settled_payments',
        'unsettled_payments',
        'payment_advice',
        'add_payment',
        'edit_payment',
        'lms_refund_payment_advise',
    ],
    'EOD_PASS_STATUS' => 1,
    'EOD_FAIL_STATUS' => 2,
    'EOD_PASS_FAIL_STATUS' => [
        0 => '',
        1 => 'Pass',
        2 => 'Fail'
    ],
    'EOD_PROCESS_CHECK_TYPE' => [
        'TALLY_POSTING' => 'tally_status',
        'INT_ACCRUAL' => 'int_accrual_status',
        'REPAYMENT' => 'repayment_status',
        'DISBURSAL' => 'disbursal_status',
        'CHARGE_POST' => 'charge_post_status',
        'OVERDUE_INT_ACCRUAL' => 'overdue_int_accrual_status',
        'DISBURSAL_BLOCK' => 'disbursal_block_status',
        'is_running_trans_settled' => 'is_running_trans_settled',
    ],    
    'DECIMAL_TYPE' => [
        'PERCENTAGE' => '2',
        'AMOUNT'=>'5',
        'AMOUNT_TWO_DECIMAL'=>'2'
    ],
    'STATUS' => [
        'PENDING' => '0',
        'APPROVED' => '1',
    ],  
    'LIMIT_TYPE' => [
        'NORMAL' => '0',
        'ADHOC' => '1',
        'TEMPORARY' => '2',
    ],
    'WRITE_OFF_STATUS' => [
        'NEW' => '36',
        'IN_PROCESS' => '37',
        'APPROVED' => '38',
        'TRANSACTION_SETTLED' => '39',
        'COMPLETED' => '40',
        'REVERT_BACK'=> '42'
    ],
    'CHARGE_TYPE'=>[
        'CHEQUE_BOUNCE' => '3',
        'NACH_BOUNCE' => '4',
    ],
    'CHARGE_PAYMENT_TYPE_MAP'=>[
        '3'=>'2',  // CHEQUE BOUNCE 3
        '4'=>'3'  // NACH 4
    ],
    'BANK_TYPE' => [
        'IDFC' => '1'
    ],
    'DISBURSAL_TIME_VALIDATE' => '17',
    'DISBURSAL_STATUS' => [    
       'PENDING' => '7',
       'APPROVED'=>'8',
       'DISBURSMENT_QUE'=>'9',
       'SENT_TO_BANK'=>'10',
       'FAILED_DISBURSMENT'=>'11',
       'DISBURSED' =>'12',
       'PARTIALLY_PAYMENT_SETTLED '=> '13',
       'REJECT'=>'14',
       'PAYMENT_SETTLED'=>'15',
    ],
    'BATCH_STATUS' => [    
       'SENT_TO_BANK' => '1',
       'SUCCESS'=>'2',
       'FAILED'=>'3',
    ],
    'REFUND_STATUS' => [    
       'NEW' => '1',
       'DELETED' => '2',
       'PENDING' => '3',
       'REJECTED'=>'4',
       'APPROVED'=>'5',
       'REFUND_QUE'=>'6',
       'SENT_TO_BANK'=>'7',
       'DISBURSED' =>'8',
       'FAILED_REFUND'=>'9',
    ],
    'IDFC_DEBIT_BANK' => [    
       'DEBIT_ACC_NO' => '10062193074',
       'DEBIT_ACC_NAME' => 'Capsave Finance Pvt Ltd',
       'DEBIT_MOBILE' => '9930840248'
    ],
    'IDFC_API_URL' => 'https://api.idfcbank.com:443/',
    'IDFC_CRYPTO_KEY' => 'MXgMPdydQGTiNyWXoEnyCySHLiWRYMFo',
    'IDFC_CORP_ID' => 'CAPSAVEAPI',
];