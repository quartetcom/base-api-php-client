<?php
namespace Quartet\BaseApi\Api;

use Quartet\BaseApi\Client;
use Quartet\BaseApi\Entity\User;

class Users
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
        $this->client = $client;
    }

    /**
     * @return User
     */
    public function me()
    {
        $response = $this->client->request('get', '/1/users/me', ['scopes' => ['read_users', 'read_users_mail']]);

        $data = json_decode($response->getBody(), true);

        $user = new User();

        $data = isset($data['user']) ? $data['user'] : [];
        foreach ($data as $key => $value) {
            if (property_exists($user, $key)) {
                $user->$key = $data[$key];
            }
        }

        return $user;
    }
}
