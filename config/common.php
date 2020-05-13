<?php

return [
    /**
     * All common config items those we are not keeping in
     * the file level.
     */
    'EDUCATION_FORM_LIMIT' => '5',
    'SOCIAL_MEDIA_LINK' => '5',
    'DOCUMENT_LIMIT' => '5',
    'RESEARCH_FORM_LIMIT' => '5',
    'AWARD_FORM_LIMIT' => '5',
    'FRONTEND_FROM_EMAIL' => 'scfit@capsavefinance.com',
    'FRONTEND_FROM_EMAIL_NAME' => 'Capsave',
    'BACKEND_FROM_EMAIL' => 'scfit@capsavefinance.com',
    'BACKEND_FROM_EMAIL_NAME' => 'Capsave',
    'APISecret' => env('APISecret', ''),
    'apiKey' => env('apiKey', ''),
    'groupId' => env('groupId', ''),
    'gatwayurl' => env('gatwayurl', ''),
    'contentType' => env('contentType', ''),
    'gatwayhost' => env('gatwayhost', ''),
    'google_recaptcha_key' => env('GOOGLE_RECAPTCHA_KEY', ''),
    'google_recaptcha_secret' => env('GOOGLE_RECAPTCHA_SECRET', ''),
    'app_status' => [
        0 => 'Incomplete',
        1 => 'Completed',
        2 => 'Sanctioned',
        3 => 'Closed',        
    ],
    //Roles Ids
    'anchor_role' => 11,
    'YES' => 1,
    'NO' => 0,
    'yes_no' => [
        '0' => 'No',
        '1' => 'Yes'
    ],
    'interest_borne_by' => [
        '1' => 'Anchor',
        '2' => 'Customer'
    ],
    'repayment_method' => [
        '1' => 'By Nach',
        '2' => 'By Cheque',
        '3' => 'By NEFT/RTGS'
    ],
    'disburse_method' => [
        '1' => 'To Anchor',
        '2' => 'To Customer'
    ],
    'prgm_status' => [
        '0' => 'In Active',
        '1' => 'Active',
        '2' => 'Ended'
    ],
    'USER_TYPE' => [
        'FRONTEND' => 1,
        'BACKEND' => 2,
    ],
    'YES' => 1,
    'MAX_UPLOAD_SIZE'=>4*1024*1024,
    'PRODUCT' => [
        'SUPPLY_CHAIN' => 1,
        'TERM_LOAN' => 2,
        'LEASE_LOAN' => 3,
    ],
    'active' => [
        'yes' => '1',
        'no' => '2'
    ], 
    'rental_frequency' => [
        '1' => 'Yearly',
        '2' => 'Bi-Yearly',
        '3' => 'Quaterly',
        '4' => 'Monthly'
    ],
    'addl_security' => [
        '1' => 'BG',
        '2' => 'FD',
        '3' => 'MF',
        '4' => 'Others'
    ],
    'review_summ_mails' => ['gaurav.agarwal@zuron.in','varun.dudani@zuron.in','updesh.sharma@prolitus.com'],
    'review_summ_mail_docs_id' => [3,9],
    'user_role' => [
        'SALES'=>4,
        'CPA'=>5,
        'APPROVER'=>8,
        'OPPS_CHECKER'=>10,
        'ANCHOR'=>11,
    ],
    'mst_status_id' => [
        'NEW'=>19,
        'COMPLETED'=>20,
        'OFFER_LIMIT_APPROVED'=>21,
        'OFFER_ACCEPTED'=>22,
        'OFFER_REJECTED'=>23,
        'PRE_SANCTION_DOC_UPLOADED'=>24,
        'SANCTION_LETTER_GENERATED'=>25,
        'POST_SANCTION_DOC_UPLOADED'=>26,
        'OFFER_GENERATED'=>28,
        'DISBURSED'=>27,
    ],
    'facility_type' => [
        '1' => 'Rental Facility',
        '2' => 'Sale and Lease Back',
        '3' => 'Rental Discounting'
    ],
    'deposit_type' => [
        '1' => 'Loan Amount',
        '2' => 'Asset Value',
        '3' => 'Asset Base Value',
        '4' => 'Sanction'
    ],

    /*'SEND_MAIL_ACTIVE'=>1,
    'SEND_MAIL'=>["gaurav.agarwal@prolitus.com", "varun.dudani@zuron.in", "binay.kumar@prolitus.com", "dhriti.barman@capsavefinance.com", "vinay.agarwal@capsavefinance.com", "vilesh.modi@rentalpha.com"],*/
    'OPERATORS' => [
        ' ( ' => ' ( ',
        ' ) ' => ' ) ',
        ' / ' => ' / ',
        ' * ' => ' * ',
        ' + ' => ' + ',
        ' - ' => ' - '
    ], 
    'doc_type' => [
        '1' => 'On Boarding',
        '2' => 'Pre Sanction',
        '3' => 'Post Sanction',
        '4' => 'Pre Offer'
    ],     
    'ps_security_id' => [
        '1'=>'Current assets',
        '2'=>'Plant and Machinery',
        '3'=>'Land and Building',
        '4'=>'Commercial Property',
        '5'=>'Land',
        '6'=>'Industrial Premises',
        '7'=>'Residential Property',
        '8'=>'Farm House and Land',
        '9'=>'Listed Share',
        '10'=>'Unlisted Share',
        '11'=>'Mutual Funds',
        '12'=>'Intercorporate Deposits',
        '13'=>'Bank Guarantee',
        '14'=>'SBLC'
    ],
    'ps_type_of_security_id'=>[
        '1'=>'Registered Mortgage',
        '2'=>'Equitable Mortgage',
        '3'=>'Hypothecation',
        '4'=>'Pledge',
        '5'=>'Lien',
        '6'=>'Negative Lien',
        '7'=>'Deposit of Title deeds'
    ],
    'ps_status_of_security_id'=>[
        '1'=>'First Pari-pasu',
        '2'=>'Exclusive',
        '3'=>'Third Pari-pasu',
        '4'=>'Second Pari-pasu',
        '5'=>'Sub-Servient'
    ],
    'ps_time_for_perfecting_security_id'=>[
        '1'=>'Before Disbusrement',
        '2'=>'With in 30 days from date of first disbusrement',
        '3'=>'With in 60 days from date of first disbsurement',
        '4'=>'With in 90 days from date of first disbursement ',
        '5'=>'With in 120 days from date of first disbursement',
        '6'=>'with in 180 days from date of first disbursement',
        '7'=>'with in 360 days from date of first disbsurement'
    ],
    'cs_desc_security_id'=>[
        '1'=>'Current assets',
        '2'=>'Plant and Machinery',
        '3'=>'Land &amp; Building',
        '4'=>'Commercial Property',
        '5'=>'Land',
        '6'=>'Industrial Premises',
        '7'=>'Residential Property',
        '8'=>'Farm House &amp; Land',
        '9'=>'Listed Share',
        '10'=>'Unlisted Share',
        '11'=>'Mutual Funds',
        '12'=>'Intercorporate Deposits',
        '13'=>'Bank Guarantee',
        '14'=>'SBLC'
    ],
    'cs_type_of_security_id'=>[
        '1'=>'Registered Mortgage',
        '2'=>'Equitable Mortgage',
        '3'=>'Hypothecation',
        '4'=>'Pledge',
        '5'=>'Lien',
        '6'=>'Negative Lien',
        '7'=>'Deposit of Title deeds'
    ],
    'cs_status_of_security_id'=>[
        '1'=>'First Pari-pasu',
        '2'=>'Exclusive',
        '3'=>'Third Pari-pasu',
        '4'=>'Second Pari-pasu',
        '5'=>'Sub-Servient'
    ],
    'cs_time_for_perfecting_security_id'=>[
        '1'=>'Before Disbusrement',
        '2'=>'With in 30 days from date of first disbusrement',
        '3'=>'With in 60 days from date of first disbsurement',
        '4'=>'With in 90 days from date of first disbursement ',
        '5'=>'With in 120 days from date of first disbursement',
        '6'=>'with in 180 days from date of first disbursement',
        '7'=>'with in 360 days from date of first disbsurement'
    ],
    'pg_time_for_perfecting_security_id'=>[
        '1'=>'Before Disbusrement',
        '2'=>'With in 30 days from date of first disbusrement',
        '3'=>'With in 60 days from date of first disbsurement',
        '4'=>'With in 90 days from date of first disbursement ',
        '5'=>'With in 120 days from date of first disbursement',
        '6'=>'with in 180 days from date of first disbursement',
        '7'=>'with in 360 days from date of first disbsurement'
    ],
    'cg_type_id'=>[
        '1'=>'Corporate Guarante with BR',
        '2'=>'Letter of Comfort with BR',
        '3'=>'Corporate Guarantee w/o BR',
        '4'=>'Letter of Comfort w/o BR',
        '5'=>'Put option with BR',
        '6'=>'Put option w/o BR'
    ],
    'cg_time_for_perfecting_security_id'=>[
        '1'=>'Before Disbusrement',
        '2'=>'With in 30 days from date of first disbusrement',
        '3'=>'With in 60 days from date of first disbsurement',
        '4'=>'With in 90 days from date of first disbursement ',
        '5'=>'With in 120 days from date of first disbursement',
        '6'=>'with in 180 days from date of first disbursement',
        '7'=>'with in 360 days from date of first disbsurement'
    ],
    'em_time_for_perfecting_security_id'=>[
        '1'=>'Before Disbusrement',
        '2'=>'With in 30 days from date of first disbusrement',
        '3'=>'With in 60 days from date of first disbsurement',
        '4'=>'With in 90 days from date of first disbursement ',
        '5'=>'With in 120 days from date of first disbursement',
        '6'=>'with in 180 days from date of first disbursement',
        '7'=>'with in 360 days from date of first disbsurement'
    ],
    'em_mechanism_id'=>[
        '1'=>'With direct Payment confirmation',
        '2'=>'W/o direct payment confirmation',
        '3'=>'With payment confirmation with Escrow a/c',
        '4'=>'W/o payment confirmation w/o Escrow a/c'
    ],
    'payment_frequency' => [
        1 => 'Up Front',
        2 => 'Monthly',
        3 => 'Rear Ended',
        4 => 'Pre Offer'        
    ],
    'JOURNAL_TYPE' => [
        'Bank' => 'Bank',
        'Cash' => 'Cash',
        'Purchase' => 'Purchase',
        'Sales' => 'Sales',        
        'Miscellaneous' => 'Miscellaneous'
    ], 
    'TRANS_CONFIG_TYPE' => [
        'DISBURSAL' => 1,
        'PAYMENT' => 2,
        'REPAYMENT' => 3,
        'CHARGES' => 4
    ],
    'timezone' => 'Asia/Kolkata',
    'idprefix' => [
        'APP' => 'CAPAI',
        'VA' => 'CAPVA',
        'CUSTID' => 'CAPCI',
        'REFUND' => 'REF',
        'LEADID' => 'CAP'
    ],
    'chrg_trigger_list' => [
        '3' => 'None',
        '1' => 'Limit Assignment',
        '2' => 'First Invoice Disbursement',
        '4' => 'Limit Enhancement',
        '5' => 'Limit Renewal',
        '6' => 'Limit Closure'
    ],
    // Mapping for app_type to chrg_trigger_id (rta_app => rta_mst_chrg)
    'app_type' => [
        0 => 1,
        1 => 5,
        2 => 4,
        3 => 1
    ]
];
 