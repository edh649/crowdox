<?php

namespace edh649\CrowdOx\Resources\Traits;

Trait All {

    public function all() {
        $url = $this->baseResourceUrl.$this->resourceUrl;
        if ($this->resource_id) {
            $url = $url."/".$this->resource_id;
        }
        $response = $this->client->resource($url)->get();
        $contents = json_decode($response->getBody()->getContents());
        
        return $contents;
    }
}