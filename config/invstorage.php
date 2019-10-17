<?php

return [
    'remote' => env('REMOTE_STORAGE', false),

    /**
     * This is the path starting after the S3 bucket.
     * If this is "false", base path will be the bucket.
     */
    's3subfolder' => env('AMAZON_S3_BASEFOLDER', false),
];
