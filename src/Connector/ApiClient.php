<?php

namespace edh649\CrowdOx\Requests;

use BadMethodCallException;
use GuzzleHttp\Client;


class ApiClient {
    
    /** @var GuzzleHttp\Client An instance of the guzzle http client */
    protected $client;

    /**
     * Intialise requests client
     *
     * @param string|null $authToken The authorization token for default headers. If null no header will be added
     * @param string|null $baseAddress The base address for all requests (e.g. api/v2/)
     * @param string|null $subdomain The subdomain to setup with. Default: api
     */
    public function __construct(string $authToken = null, string $baseAddress = null, string $subdomain = null) {
        if ($subdomain === null) { $subdomain = "api"; }
        if ($baseAddress === null) { $baseAddress = ""; }

        $headers = [
            'Referer' => 'https://manage.crowdox.com/',
        ];
        if ($authToken !== null) {
            $headers['Authorization'] = 'Bearer '.$authToken;
        }

        $parameters = [
            // Base URI is used with relative requests
            'base_uri' => 'https://'.$subdomain.'.crowdox.com/'.$baseAddress,
            // You can set any number of default request options.
            'timeout'  => 30.0,
            //set default headers
            'headers' => $headers,
        ];

        $this->client = new Client($parameters);
    }


    /**
     * Pass any method calls onto $this->client
     *
     * @return mixed
     */
    public function __call($method, $args) {
        if(is_callable([$this->client,$method])) {
            return call_user_func_array([$this->client,$method],$args);
        } else {
            throw new BadMethodCallException("Method $method does not exist");
        }
    }


}