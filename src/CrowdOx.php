<?php

namespace edh649\CrowdOx;

use edh649\CrowdOx\Auth\Auth;
use edh649\CrowdOx\Exceptions\CrowdOxException;
use edh649\CrowdOx\Requests\ApiClient;
use edh649\CrowdOx\Resources\Projects;

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
        $token = $auth->username($username)->password($password)->login()->token();
        $this->client = new ApiClient($token, $this->baseAddress);
    }


    /**
     * Returns a list of the valid resources
     *
     * @return array
     */
    protected static function getValidResources(): array {
        return [
            'projects' => Projects::class,
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
        if ((array_key_exists($name, $validSubResources = $this::getValidResources()))) {
            $className = $validSubResources[$name];
            $client    = $this->client;
            $class     = new $className($client);
        } else {
            throw new \Exception("No method called $name available in " . __CLASS__);
        }

        return $class;
    }
}
