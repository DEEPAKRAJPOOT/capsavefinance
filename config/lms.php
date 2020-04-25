<?php

/* 
  array define for mantain LMS Configuration
  created by
  @Sudesh Kumar
 
*/

return [
    'TRANS_TYPE' => [
        
        
        'PAYMENT_RECEIVED' => '1',
        'REVERSE' => '2',
        'DISCOUNT_ON_PAYMENT' => '3',
        'PROCESSING_FEE' => '4',
        'CHEQUE_BOUNCE'=>'5',
        'LOAN_DISBURSED'=>'6',
        'TDS'=>'7',
        'INCOME'=>'8',
        'INTEREST'=>'9',
        'MARGIN'=>'10',
        'PENALTY'=>'11',
        'GST'=>'12',
        'CGST'=>'13',
        'SGST'=>'14',
        'IGST'=>'15',
        'PAYMENT_DISBURSED' =>'16',
        'REPAYMENT'=> '17',
        'DOCUMENT_FEE'=>'20',
        'NACH_BOUNCE_CHARGE'=>'24',
        'SOA_PULL'=>'25',
        'PREPAYMENT_CHARGE'=>'28',
        'OTHER_CHARGE'=>'29',
        'INVOICE_KNOCKED_OFF'=>'30',
        'INTEREST_REFUND'=>'31',
        'REFUND'=>'32',
        'INTEREST_PAID'=>'32',
        'INTEREST_OVERDUE'=>'33',
        'INVOICE_PARTIALLY_KNOCKED_OFF'=>'34',
        'NON_FACTORED_AMT' => '35',
        'WAVED_OFF'=>'36',
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
    'DECIMAL_TYPE' => [
        'PERCENTAGE' => '2',
        'AMOUNT'=>'5'
    ],    
];