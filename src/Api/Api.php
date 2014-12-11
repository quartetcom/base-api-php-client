<?php
namespace Quartet\BaseApi\Api;

use Quartet\BaseApi\Client;
use Quartet\BaseApi\EntityManager;

class Api
{
    /**
     * @var \Quartet\BaseApi\EntityManager
     */
    protected $entityManager;

    /**
     * @var \Quartet\BaseApi\Client
     */
    protected $client;

    public function __construct(Client $client, EntityManager $entityManager = null)
    {
        if (is_null($entityManager)) {
            $this->entityManager = new EntityManager;
        } else {
            $this->entityManager = $entityManager;
        }

        $this->client = $client;
    }
}
