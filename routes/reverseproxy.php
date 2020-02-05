<?php

if(config('app.env') == "production"){
    $proxy_url    = 'https://'.request()->server('HTTP_HOST');
    $proxy_schema = 'https';

    // dd(config('app.env'));
    // $proxy_url    = getenv('PROXY_URL');
    // $proxy_schema = getenv('PROXY_SCHEMA');

    if (!empty($proxy_url)) {
        \URL::forceRootUrl($proxy_url);
    }

    if (!empty($proxy_schema)) {
        \URL::forceScheme($proxy_schema);
    }
}

