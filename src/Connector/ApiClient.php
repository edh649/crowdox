<?php

namespace edh649\CrowdOx\Connector;

use BadMethodCallException;
use edh649\CrowdOx\Auth\Auth;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
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
    public function __construct(Auth $auth = null, string $baseAddress = null, string $subdomain = null) {
        $this->subdomain = $subdomain ?? "api";
        $this->baseAddress = $baseAddress ?? "";

        $token = null;
        if ($auth) { $this->auth = $auth; $token = $auth->token(); }

        $this->createClient($token);
    }

    protected $subdomain = null;
    protected $baseAddress = null;
    protected $auth = null; //authentication object

    protected function createClient(string $authToken = null) {
        $headers = [
            'Referer' => 'https://manage.crowdox.com/',
        ];
        if ($authToken !== null) {
            $headers['Authorization'] = 'Bearer '.$authToken;
        }

        $parameters = [
            // Base URI is used with relative requests
            'base_uri' => 'https://'.$this->subdomain.'.crowdox.com/'.$this->baseAddress,
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
        try {
            $query = http_build_query($this->parameters);
            return $this->client->get($this->resource."?".$query);
        }
        catch (ClientException $e) {
            if ($e->getCode() == 401) {
                //attempt new login
                $this->auth = $this->auth->login();
                $this->createClient($this->auth->token());

                //retry query
                $query = http_build_query($this->parameters);
                return $this->client->get($this->resource."?".$query);
            }
        }
    }

    /**
     * Send a post request to the API
     *
     * @return Response
     */
    public function post(): Response {
        try {
            return $this->client->post($this->resource, $this->parameters);
        }
        catch (ClientException $e) {
            if ($e->getCode() == 401) {
                //attempt new login
                $this->auth = $this->auth->login();
                $this->createClient($this->auth->token());

                //retry query
            return $this->client->post($this->resource, $this->parameters);
            }
        }
    }
}