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
    'FRONTEND_FROM_EMAIL' => 'admin@dexter.com',
    'FRONTEND_FROM_EMAIL_NAME' => 'Rent Alpha',
    'BACKEND_FROM_EMAIL' => 'admin@dexter.com',
    'BACKEND_FROM_EMAIL_NAME' =>'Rent Alpha',
    'APISecret'   => env('APISecret', ''),
    'apiKey'      => env('apiKey', ''),
    'groupId'     => env('groupId', ''),
    'gatwayurl'   => env('gatwayurl', ''),
    'contentType' => env('contentType', ''),
    'gatwayhost'  => env('gatwayhost', ''),
    'google_recaptcha_key'  => env('GOOGLE_RECAPTCHA_KEY', ''), 
    'google_recaptcha_secret'  => env('GOOGLE_RECAPTCHA_SECRET', ''),    
    'app_status' => [
        0 => 'In complete',
        1 => 'Completed'
    ],
    
    //Roles Ids
    'anchor_role' => 11,
    'YES'=>1,
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
    ]    
];
