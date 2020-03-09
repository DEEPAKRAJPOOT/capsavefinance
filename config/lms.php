<?php

/* 
  array define for mantain LMS Configuration
  created by
  @Sudesh Kumar
 
*/

return [
    'TRANS_TYPE' => [
        'PAYMENT_RECEIVED' => '1',
        'PAYMENT_REVERSE' => '2',
        'DISCOUNT_ON_PAYMENT' => '3',
        'PROCESSING_FEE' => '4',
        'CHEQUE_BOUNCE'=>'5',
        'LOAN_DISBURSED'=>'6',
        'TDS'=>'7',
        'INCOME'=>'8',
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
        'INTEREST'=>'9',
        'INTEREST_REFUND'=>'31',
        'INTEREST_PAID'=>'32',
        'INTEREST_OVERDUE'=>'33',
        'INVOICE_PARTIALLY_KNOCKED_OFF'=>'34',
        'NON_FACTORED_AMT' => '35'
    ],
    'STATUS_ID' => [
        'DISBURSED'=>'12',
        'PARTIALLY_PAYMENT_SETTLED' => '13',
        'PAYMENT_SETTLED' => '15'
    ]
];