<?php

namespace Quartet\BaseApi;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    private $client;

    public function setUp()
    {
        $this->client = new Client('clientId', 'clientSecret', 'redirectUri');
    }

    // todo.
    public function test()
    {
        $this->assertTrue(true);
    }
}
