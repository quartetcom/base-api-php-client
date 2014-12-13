<?php
namespace Quartet\BaseApi\Api;

class Users extends Api
{
    /**
     * @return \Quartet\BaseApi\Entity\User
     */
    public function me()
    {
        $data = $this->client->request('get', '/1/users/me');

        return $this->entityManager->getEntity('User', $data['user']);
    }
}
