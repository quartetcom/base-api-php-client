<?php
namespace Quartet\BaseApi\Api;

use Quartet\BaseApi\Client;
use Quartet\BaseApi\EntityManager;

abstract class AbstractApi
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(Client $client, EntityManager $entityManager = null)
    {
        if (is_null($entityManager)) {
            $this->entityManager = new EntityManager;
        } else {
            $this->entityManager = $entityManager;
        }

        $this->client = $client;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
}
