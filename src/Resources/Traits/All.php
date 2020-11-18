<?php

namespace edh649\CrowdOx\Resources\Traits;

Trait All {

    public function all() {
        $response = $this->client->resource($this->resourceUrl)->get();
        $contents = json_decode($response->getBody()->getContents());
        
        return $contents;
    }
}