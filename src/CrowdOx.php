<?php

namespace edh649\CrowdOx;

use edh649\CrowdOx\Auth\Auth;
use edh649\CrowdOx\Exceptions\CrowdOxException;
use edh649\CrowdOx\Connector\ApiClient;

class CrowdOx
{

    /** @var string The base address for API calls */
    protected $baseAddress = "api/v2/";

    /** @var ApiClient The api client to use */
    private $client;

    /**
     * @param string $username The username for access
     * @param string $password The password for access
     * @throws CrowdOxException When no username or password is provided
     */
    public function __construct($username, $password)
    {
        if ($username === null) {
            $msg = 'No username provided';
            throw new CrowdOxException($msg);
        }
        if ($password === null) {
            $msg = 'No password provided';
            throw new CrowdOxException($msg);
        }
        $auth = new Auth();
        $auth = $auth->username($username)->password($password)->login();
        $this->client = new ApiClient($auth, $this->baseAddress);
    }


    /**
     * Returns a list of the valid resources
     *
     * @return array
     */
    protected function getValidResources(): array {
        return [
            'countries' => \edh649\CrowdOx\Resources\Countries::class,
            'customers' => \edh649\CrowdOx\Resources\Customers::class,
            'order_lines' => \edh649\CrowdOx\Resources\OrderLines::class,
            'orders' => \edh649\CrowdOx\Resources\Orders::class,
            'order_selections' => \edh649\CrowdOx\Resources\OrderSelections::class,
            'order_addresses' => \edh649\CrowdOx\Resources\OrderAddresses::class,
            'order_tags' => \edh649\CrowdOx\Resources\OrderTags::class,
            'products' => \edh649\CrowdOx\Resources\Products::class,
            'product_variations' => \edh649\CrowdOx\Resources\ProductVariations::class,
            'projects' => \edh649\CrowdOx\Resources\Projects::class,
            'orders' => \edh649\CrowdOx\Resources\Orders::class,
            'states' => \edh649\CrowdOx\Resources\States::class,
        ];
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
            $class    =  $class->client($client);
        } else {
            throw new \Exception("No method called $name available in " . __CLASS__);
        }

        return $class;
    }
}
