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
        0 => 'In complete',
        1 => 'Completed'
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
    ]
];
 