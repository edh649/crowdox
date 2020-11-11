<?php

namespace edh649\CrowdOx\Resources;


class Projects extends Resource {

    /**
     * Retrieves a list of all projects
     *
     * @return array
     */
    public function all(): array {
        $response = $this->client->get("projects")->getBody();

        return $response->data;
    }

}