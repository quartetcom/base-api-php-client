<?php
namespace Quartet\BaseApi\Api;

class Users extends Api
{
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
