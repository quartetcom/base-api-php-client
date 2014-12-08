<?php
namespace Quartet\BaseApi\Api;

use Quartet\BaseApi\Client;

class Users extends AbstractApi
{
    /**
     * @var \Quartet\BaseApi\Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        parent::__construct();

        $this->client = $client;
    }

    /**
     * @return \Quartet\BaseApi\Entity\User
     */
    public function me()
    {
        $response = $this->client->request('get', '/1/users/me');

        $data = json_decode($response->getBody(), true);

        return $this->entityFactory->get('User', $data['user']);
    }
}
