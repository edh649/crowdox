<?php

namespace edh649\CrowdOx\Auth;

use edh649\CrowdOx\Exceptions\CrowdOxException;
use edh649\CrowdOx\Requests\ApiClient;

class Auth {
    
    /** @var string The username for access */
    private $username = null;
    /** @var string The password for access */
    private $password = null;

    /** @var bool Login status */
    private $loggedIn = false;

    /** @var string The password for access */
    private $token = null;

    /** @var ApiClient the client used for accessing the API */
    private $client;

    public function __construct() {
        $this->client = new ApiClient(null, null, "auth");
    }

    /**
     * Set the username for authentication
     *
     * @param string $username
     * @return Auth
     */
    public function username(string $username): Auth {
        $this->username = $username;
        return $this;
    }

    /**
     * Set the password for authentication
     *
     * @param string $password
     * @return Auth
     */
    public function password(string $password): Auth {
        $this->password = $password;
        return $this;
    }

    /**
     * Login
     *
     * @return Auth
     */
    public function login(): Auth {
        $response = $this->client->resource('sessions')->parameter('form_params', [
            "username" => $this->username,
            "password" => $this->password,
            ])->post();
        $body = $response->getBody()->getContents();
        $reponse_object = json_decode($body)->result;
        $this->token = $reponse_object->id_token;
        $this->loggedIn = true;
        return $this;
    }

    /**
     * Login status
     *
     * @return boolean
     */
    public function loggedIn(): bool {
        return $this->loggedIn;
    }

    /**
     * Get token
     *
     * @throws CrowdOxException If not logged in
     * @return string
     */
    public function token(): string {
        if (!$this->loggedIn) { throw new CrowdOxException("Can't fetch API token, not logged in!"); }
        return $this->token;
    }
}