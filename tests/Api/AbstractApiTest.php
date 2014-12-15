<?php
namespace Quartet\BaseApi\Api;

use Quartet\BaseApi\Client;
use Quartet\BaseApi\EntityManager;

class AbstractApiTest extends \PHPUnit_Framework_TestCase
{
    public function test_constructor()
    {
        $client = new Client('', '', '');
        $em = new EntityManager;

        // inject client and entityManager.
        $api = new DummyApi($client, $em);
        $this->assertEquals($client, $api->getClient());
        $this->assertEquals($em, $api->getEntityManager());

        // inject only client.
        $api = new DummyApi($client);
        $this->assertEquals($client, $api->getClient());
        $this->assertInstanceOf('\Quartet\BaseApi\EntityManager', $api->getEntityManager());
    }
}

class DummyApi extends AbstractApi
{
}
