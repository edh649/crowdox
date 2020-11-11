<?php

namespace edh649\CrowdOx\Resources;

use edh649\CrowdOx\Requests\ApiClient;

abstract class Resource {

    /** @var ApiClient An instance of the API client */
    protected $client;

    /**
     * Create the resource
     *
     * @param ApiClient $client the client to user for connections
     */
    public function __construct(ApiClient $client) {
        $this->client = $client;
    }
}