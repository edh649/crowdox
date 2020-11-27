<?php

namespace edh649\CrowdOx\Resources;

use edh649\CrowdOx\Exceptions\CrowdOxException;
use edh649\CrowdOx\Connector\ApiClient;

abstract class Resource {

    /** @var string The base resource URL */
    protected $baseResourceUrl = "";

    /**
     * Set base resource URL. blank for root level
     *
     * @param string $baseResourceUrl - the base resource url
     * @return Resource the resource object
     */
    public function baseResourceUrl(string $baseResourceUrl = null): Resource {
        $this->baseResourceUrl = $baseResourceUrl;
        return $this;
    }

    /** @var ApiClient An instance of the API client */
    protected $client;

    /**
     * Create the resource
     *
     * @param ApiClient $client the client to user for connections
     * @return Resource the resource object
     */
    
    public function client(ApiClient $client): Resource {
        $client->parameter("page[number]", $this->page_number);
        $client->parameter("page[size]", $this->page_size);
        $this->client = $client;
        return $this;
    }


    /** @var int The page number to fetch */
    protected $page_number = 1;

    /**
     * Set the page number to return
     *
     * @param inusers?page[number]=200teger $page_number
     * @return Resource
     */
    public function pageNumber(int $page_number) : Resource {
        $this->page_number = $page_number;
        if ($this->client) { $this->client->parameter("page[number]", $page_number); }
        return $this;
    }

    /** @var int The number of items to fetch from the page */
    protected $page_size = 30;

    /**
     * Set the page size to return
     *
     * @param integer $page_size number of items per page
     * @return Resource
     */
    public function pageSize(int $page_size): Resource {
        $this->page_size = $page_size;
        if ($this->client) { $this->client->parameter("page[size]", $page_size); }
        return $this;
    }

    /**
     * Include a relationship with this resouce
     *
     * @param array|string $items
     * @return Resource
     */
    public function include($items): Resource {
        if (!is_array($items)) { $items = [$items]; }

        //check items are includable
        foreach ($items as $item) {
            //the naming convention is related-object.related-related-object
            //we'll only check the first one for now.
            $itemSplit = explode(".", $item);
            if (!in_array($itemSplit[0], $this->includable)) {
                $class = get_class($this);
                throw new CrowdOxException("Resource [{$itemSplit[0]}] is not present on list of includables for resource [{$class}]");
            }
        }

        //include
        $this->client->parameter("include", implode(",", $items));
        return $this;
    }

    /**
     * Manually set a parameter
     *
     * @param [type] $key
     * @param [type] $value
     * @return Resource
     */
    public function parameter(string $key, string $value): Resource {
        $this->client->parameter($key, $value);
        return $this;
    }


    protected $resource_id;

    /**
     * Set resource id
     *
     * @param string|int ?$resource_id
     */
    public function __construct($resource_id = null) {
        if (is_array($resource_id) && count($resource_id) == 1) { $resource_id = $resource_id[0]; }
        if (!$resource_id || is_string($resource_id) || is_int($resource_id)) {
            $this->resource_id = $resource_id;
        }
        else {
            $type = gettype($resource_id);
            throw new CrowdOxException("Invalid resource ID type [{$type}]. Should be int or string");
        }
    }

    /**
     * Returns a list of the valid resources
     *
     * @return array
     */
    protected function getValidResources(): array {
        return [];
    }

    /**
     * Access sub resources
     *
     * @param $name
     * @param $arguments
     *
     * @return ChainedParametersTrait
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if ((array_key_exists($name, $validSubResources = $this->getValidResources()))) {
            $className = $validSubResources[$name];
            $client    = $this->client;
            $class     = new $className($arguments);
            $class     = $class->client($client);
            $class     = $class->baseResourceUrl($this->resourceUrl."/".$this->resource_id."/");
        } else {
            throw new \Exception("No method called $name available in " . __CLASS__);
        }

        return $class;
    }
}