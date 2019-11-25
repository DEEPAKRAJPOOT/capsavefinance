<?php

namespace App\Libraries\Ui;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;


class CibilApi {

    protected $client;
    protected $key;
    protected $headers;
    protected $url;
    protected $path = '';
    protected $consent = 'Y';
    protected $body;
    

    public function __construct(Client $client) {
        $this->client = $client;
 //         $this->headers = [
//            'cache-control' => "no-cache",
//            'Content-Type' => "application/json",
//            'x-karza-key' => env('KARZA_AUTHENTICATION_API_KEY'),
//        ];
    }


    public function getPromoterCibilRequest() {
        try {
            $api_url = 'Inquiry/doGet.service/requestResponseSync';
//dd($arrData['pan']);
            $options = [];
            $response = $this->client->post($api_url, $options);
             $response = $response->getBody()->getContents();
             //dd($response);
             return $response;
            
        } catch (\Exception $e) {
            return [];
        }
    }
    

   

}
