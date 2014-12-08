<?php
namespace Quartet\BaseApi\Api;

use Quartet\BaseApi\Client;
use Quartet\BaseApi\Entity\User;

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
     * @return User
     */
    public function me()
    {
        $response = $this->client->request('get', '/1/users/me', ['scopes' => ['read_users', 'read_users_mail']]);

        $data = json_decode($response->getBody(), true);

        return $this->entityFactory->get('User', $data['user']);
    }
}
