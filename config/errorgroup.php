<?php

/**
 * Error group for all error emails
 */

return [

    /*
     * Email address from where the emails should be triggered
     */
    'error_notification_email' => env('ERROR_FROM_EMAIL', 'dev'),

    /*
     * From email name
     */
    'error_notification_from' => env('ERROR_FROM_NAME', 'RentAlpha'),

    /*
     * Group of people to whom the error should be reported
     */
    'error_notification_group' => [
        'pankaj.sharma@prolitus.com',
        'anuj.chauhan@prolitus.com',
        'gaurav.agarwal@prolitus.com',
        'ravi.awasthi@prolitus.com',
        'varun.dudani@zuron.in'
    ]
];
