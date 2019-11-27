<?php

namespace App\Libraries\Ui;

use Helpers;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class KarzaApi {

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

    public function panVerificationRequest($pancard = null) {

        try {
            $api_url = '/v2/pan';
            $baseUrl = config('proin.karza_auth_api_url');
            $apiKey = config('proin.karza_auth_api_key');

            $options = [
                'base_uri' => $baseUrl,
                'json' => [
                    'consent' => 'Y',
                    'pan' => $pancard],
                'headers' => [
                    'cache-control' => "no-cache",
                    'Content-Type' => "application/json",
                    'x-karza-key' => $apiKey  //env('KARZA_AUTHENTICATION_API_KEY')
                ]
            ];
            $response = $this->client->post($api_url, $options);
            $response = $response->getBody()->getContents();
            return $response;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Pan Card  status verification API
     *
     * @return \Illuminate\Http\Response
     */
    public function checkPanStatusVerification($pancard) {

        try {
            $api_url = '/v2/pan-authentication';
             $baseUrl = config('proin.karza_auth_api_url');
             $apiKey = config('proin.karza_auth_api_key');
            $options = [
                'base_uri' => $baseUrl,
                'json' => [
                    'consent' => 'Y',
                    'pan' => $pancard['pan'],
                    'name' => $pancard['name'],
                    'dob' => date("d/m/Y", strtotime($pancard['dob']))],
                'headers' => [
                    'cache-control' => "no-cache",
                    'Content-Type' => "application/json",
                    'x-karza-key' => $apiKey  //env('KARZA_AUTHENTICATION_API_KEY')
                ]
            ];
            $response = $this->client->post($api_url, $options);
            $response = $response->getBody()->getContents();
            return $response;
        } catch (\Exception $e) {
            return $e;
            return [];
        }
    }

    /**
     * Voter ID Card Verification status verification API
     *
     * @return \Illuminate\Http\Response
     */
    public function checkVoterIdVerification($voterid) {
        try {
            $api_url = '/v2/voter';
            $baseUrl = config('proin.karza_auth_api_url');
            $apiKey = config('proin.karza_auth_api_key');
            $options = [
                'base_uri' => $baseUrl,
                'json' => [
                    'consent' => 'Y',
                    'epic_no' => $voterid['epic_no']],
                'headers' => [
                    'cache-control' => "no-cache",
                    'Content-Type' => "application/json",
                    'x-karza-key' => $apiKey  //env('KARZA_AUTHENTICATION_API_KEY')
                ]
            ];
            $response = $this->client->post($api_url, $options);
            $response = $response->getBody()->getContents();
            $response = json_decode($response);

            return ['response' => $response, 'request' => $options];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Dl Verification status verification API
     *
     * @return \Illuminate\Http\Response
     */
    public function checkDlVerification($dlArr) {

        try {
            $api_url = '/v2/dl';
            $baseUrl = config('proin.karza_auth_api_url');
            $apiKey = config('proin.karza_auth_api_key');
            $options = [
                'base_uri' => $baseUrl,
                'json' => [
                    'consent' => 'Y',
                    'dl_no' => $dlArr['dl_no'],
                    'dob' => date("d-m-Y", strtotime($dlArr['dob']))
                ],
                'headers' => [
                    'cache-control' => "no-cache",
                    'Content-Type' => "application/json",
                    'x-karza-key' => $apiKey  //env('KARZA_AUTHENTICATION_API_KEY')
                ]
            ];
            $response = $this->client->post($api_url, $options);
            $response = $response->getBody()->getContents();
            return $response;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Passport ID Card Verification status verification API
     *
     * @return \Illuminate\Http\Response
     */
    public function checkPassportVerification($passportArr) {
        try {
            $api_url = '/v2/passport-verification';
            $baseUrl = config('proin.karza_auth_api_url');
            $apiKey = config('proin.karza_auth_api_key');
            $options = [
                'base_uri' => $baseUrl,
                'json' => [
                    'consent' => 'Y',
                    'fileNo' => $passportArr['fileNo'],
                    'dob' => date("d/m/Y", strtotime($passportArr['dob']))
                ],
                'headers' => [
                    'cache-control' => "no-cache",
                    'Content-Type' => "application/json",
                    'x-karza-key' => $apiKey  //env('KARZA_AUTHENTICATION_API_KEY')
                ]
            ];
            $response = $this->client->post($api_url, $options);
            $response = $response->getBody()->getContents();
            return $response;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function all() {
        return $this->endpointRequest('/dummy/posts');
    }

    public function findById($id) {
        return $this->endpointRequest('/dummy/post/' . $id);
    }

    public function endpointRequest($url) {
        try {
            $response = $this->client->request('POST', $url);
        } catch (\Exception $e) {
            return [];
        }

        return $this->response_handler($response->getBody()->getContents());
    }

    public function response_handler($response) {
        if ($response) {
            return json_decode($response);
        }

        return [];
    }

    public function getPromoterCibilRequest($arrData) {
        echo "sdfsd";
        dd($arrData);

        try {
            $api_url = '/v2/pan';
            $baseUrl = config('proin.karza_auth_api_url');
            $apiKey = config('proin.karza_auth_api_key');
            $options = [
                'base_uri' => $baseUrl,
                'json' => [
                    'consent' => 'Y',
                    'pan' => $pancard],
                'headers' => [
                    'cache-control' => "no-cache",
                    'Content-Type' => "application/json",
                    'x-karza-key' => $apiKey  //env('KARZA_AUTHENTICATION_API_KEY')
                ]
            ];
            $response = $this->client->post($api_url, $options);
            $response = $response->getBody()->getContents();
            return $response;
        } catch (\Exception $e) {
            return [];
        }
    }

}
