<?php
namespace Quartet\BaseApi\Api;

use Phake;

class UsersTest extends \PHPUnit_Framework_TestCase
{
    public function test_me()
    {
        $client = Phake::mock('\Quartet\BaseApi\Client');
        Phake::when($client)->request('get', '/1/users/me')->thenReturn(['user' => ['test' => 'test']]);

        $em = Phake::mock('\Quartet\BaseApi\EntityManager');
        Phake::when($em)->getEntity('User', ['test' => 'test'])->thenReturn('entity');

        $usersApi = new Users($client, $em);

        $this->assertEquals('entity', $usersApi->me());
    }
}
