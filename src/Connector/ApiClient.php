<?php

namespace edh649\CrowdOx\Requests;

use BadMethodCallException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

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

    
    /** @var string the resource to fetch */
    protected $resource = "";

    /**
     * Set the resource (URL path) to access
     *
     * @param string $resource
     * @return ApiClient
     */
    public function resource(string $resource): ApiClient {
        $this->resource = $resource;
        return $this;
    }

    /** @var array the parameters in the URL */
    protected $parameters = [];

    /**
     * Set the parameters
     *
     * @param string $resource
     * @return ApiClient
     */
    public function parameter($key, $value): ApiClient {
        $this->parameters[$key] = $value;
        return $this;
    }

    /**
     * Send a get request to the API
     *
     * @return Response
     */
    public function get(): Response {
        $query = http_build_query($this->parameters);
        return $this->client->get($this->resource."?".$query);
    }

    /**
     * Send a post request to the API
     *
     * @return Response
     */
    public function post(): Response {
        return $this->client->post($this->resource, $this->parameters);
    }
}