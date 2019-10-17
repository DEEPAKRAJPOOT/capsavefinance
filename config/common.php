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
    'FRONTEND_FROM_EMAIL_NAME' => 'Dexter',
    'BACKEND_FROM_EMAIL' => 'admin@dexter.com',
    'BACKEND_FROM_EMAIL_NAME' => 'Dexter',
    'APISecret'   => env('APISecret', ''),
    'apiKey'      => env('apiKey', ''),
    'groupId'     => env('groupId', ''),
    'gatwayurl'   => env('gatwayurl', ''),
    'contentType' => env('contentType', ''),
    'gatwayhost'  => env('gatwayhost', ''),
    
];
