<?php

namespace edh649\CrowdOx\Resources;


class Projects extends Resource {

    /**
     * Retrieves a list of all projects
     *
     * @return array
     */
    public function all(): array {
        $response = json_decode($this->client->get("projects")->getBody()->getContents());

        return $this->squishAttributes($response->data);
    }

    protected function squishAttributes($data) {
        return array_map(function ($item) {
            $attributes = $item->attributes;
            $attributes->id = $item->id;

            return $attributes;
        }, $data);
    }

}