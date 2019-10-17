<?php
return [
    /**
     * URL for front-end
     */
    'frontend_uri' => env('FRONTEND_URI', 'localhost'),
    /**
     * URL for api
     */
    'api_uri' => env('API_URI', 'localhost'),
    /**
     * URL for back-end
     */
    'backend_uri' => env('BACKEND_URI', 'localhost'),
    /**
     * CDN / static file serving URL
     */
    'cdn_uri' => env('CDN_URI', 'localhost'),
    /**
     * Session cookie name for front-end
     */
    'frontend_cookie_name' => env('FRONTEND_COOKIE_NAME', 'localhost'),
    /**
     * Session cookie name for front-end
     */
    'backend_cookie_name' => env('BACKEND_COOKIE_NAME', 'localhost'),
    /**
     * Free Holder Per IP(Intellectual Property) add fee
     */
    'free_holder_fee' => env('FREE_HOLDER_FEE',1),
    
    /**
     * PremiumRetailer Per month subscription fee
     */
    'premium_retailer_fee' => env('PREMIUM_RETAILER_FEE', 96),
];
