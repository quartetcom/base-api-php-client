<?php
namespace Quartet\BaseApi\Api;

use Quartet\BaseApi\Client;
use Quartet\BaseApi\EntityFactory;

class AbstractApi
{
    /**
     * @var \Quartet\BaseApi\EntityFactory
     */
    protected $entityFactory;

    /**
     * @var \Quartet\BaseApi\Client
     */
    protected $client;

    public function __construct(Client $client)
    {
        $this->entityFactory = new EntityFactory;
        $this->client = $client;
    }
}
