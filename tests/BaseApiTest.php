<?php

namespace Quartet\BaseApi;

class BaseApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BaseApi
     */
    protected $skeleton;

    protected function setUp()
    {
        $this->skeleton = new BaseApi;
    }

    public function testNew()
    {
        $actual = $this->skeleton;
        $this->assertInstanceOf('\Quartet\BaseApi\BaseApi', $actual);
    }

    /**
     * @expectedException \Quartet\BaseApi\Exception\LogicException
     */
    public function testException()
    {
        throw new Exception\LogicException;
    }
}
