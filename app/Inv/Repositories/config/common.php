<?php
/**
 * All common config items those we are not keeping in
 * the database level.
 */
return [
    'COMPLETE_APP' => 1,
    'INCOMPLETE_APP' => 0,
    'COMPLETE_ADD_INFO' => 1,
    'INCOMPLETE_ADD_INFO' => 0,
    'ENDED_APP' => 3,
    'ACTIVE_APP' => 20,
    'YES' => 1,
    'NO' => 0,
    'ACTIVE' => 1,
    'DEACTIVE' => 0,
    'MALE' => 1,
    'FEMALE' => 2,
    'COUNTRY_ID' => 253,
    'CURRENCY_CODE' => '$',
    'LENDER_EMAIL' => 'info@dev.rentaplha.com',
    'LENDER_NAME' => 'RentAlpha',
    'FILE_EXT' => [".xls", ".xlsx", ".txt", ".doc", ".docx", ".ppt", ".pptx", ".pdf",
        ".jpg", ".jpeg", ".gif", ".png", ".tif"],
    /*
     * User type
     *
     * 1 => Front end; 2 => Back end
     */
    'USER_FRONTEND' => 0,
    'USER_BACKEND' => 1,
    /**
     * Secret key for captcha
     */
    'RE_CAP_SECRET' => '6LdIYBUUAAAAAMhsWdpbqL8WIzYDpWy08e5JQZsp',
    'RE_CAP_SITE' => '6LdIYBUUAAAAAFE8r_dDDe2qHHwgFtqEOJDI1g_I',
    'LEAD_SHEET_PREFIX' => 'LS',
    //Email Template type
    'SYSTEM_GENERATED' => 1,
    'USER_DEFINED' => 2,
    /**
     * Roles Id
     */
    'ROLE_CUSTOMER' => 3,
    'SBDSM_PARENT_ROLE_ID' => 1,
    'SC_PARENT_ROLE_ID' => 8,
    'SBB_PARENT_ROLE_ID' => 4,
    /**
     * Flag for registration save and share
     */
    //Defaul sharing Admin Id
    'ADMIN_ID' => 5,
    /**
     * Application and Additional info steps
     */
    'MAX_PASSWORD_ALLOWED' => 6,
    'MAX_DAYS_ALLOWED' => 90,
    'MAX_INACTIVITY_ALLOWED' => 100,
    /**
     * Database Action
     */
    'DB_ACTION_INSERT' => 1,
    'DB_ACTION_UPDATE' => 2,
    'DB_ACTION_SYNC' => 3,
    'FRONTEND_FROM_EMAIL' => 'huntington@b2cdev.com',
    'FRONTEND_FROM_EMAIL_NAME' => 'Huntington',
    'BACKEND_FROM_EMAIL' => 'huntington@b2cdev.com',
    'BACKEND_FROM_EMAIL_NAME' => 'Huntington',
    //This flag show the user document status
    'IS_USER_DOCUMENT_UPLOAD' => 'NO',
    /**
     * Huntington unit array
     */
    'units' => [
        'doller' => '$',
        'rupees' => 'Rs',
    ],
    'YES_NO_ARRAY' => [
        '' => 'Please Select',
        '1' => 'Yes',
        '0' => 'No'
    ],
    'INDUSTRY_OTHER' => 'Other',
    'ARRAY_STATUS_ADD' => ['10', '11', '12'],
    'APP_STAGE' => [
        'business_information' => '2',
        'individual_information' => '3',
        'loan_product' => '4',
        'collateral_information' => '5',
        'upload_documents' => '6',
        'review_submit' => '7',
    ],
    'LOAN_PURPOSE_BIZ' => 1,
    'LOAN_PURPOSE_SCREENING' => 2,
    'TECHNICAL_HELP_NUMBER'=> 'x-xxx-xxx-xxxx',
    'INQUIRY_NUMBER'=> 'x-xxx-xxx-xxxx'
];
