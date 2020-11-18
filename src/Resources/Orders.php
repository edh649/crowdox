<?php

namespace edh649\CrowdOx\Resources;

use edh649\CrowdOx\Resources\Traits\All;

class Orders extends Resource {

    use All;

    protected $resourceUrl = "orders";

    protected $includable = [
        'project',
        'customer',
        'original-configuration',
        'current-configuration',
        'shipping-address',
        'local-pickup-address',
        'source',
        'transactions',
        'emails',
        'digital-downloads',
        'digital-keys',
        'tags',
        'syncs',
        'lines',
        'selections',
        'tracking-parameter-value-orders',
    ];

}