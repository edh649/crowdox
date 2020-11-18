<?php

namespace edh649\CrowdOx\Resources;

use edh649\CrowdOx\Resources\Traits\All;

class Projects extends Resource {

    use All;

    protected $resourceUrl = "projects";

    protected $includable = [
        'payment-gateways',
        'addresses',
        'sources',
        'configurations',
        'stats',
        'configuration-stats',
        'products',
        'product-variants',
        'product-variant-values',
        'product-variations',
        'translations',
        'apps',
        'digital-fulfillments',
        'custom-fields',
    ];


    /**
     * Returns a list of the valid resources
     *
     * @return array
     */
    protected static function getValidResources(): array {
        return [
            'custom_fields' => \edh649\CrowdOx\Resources\Project\CustomFields::class,
        ];
    }


}