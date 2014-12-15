<?php
namespace Quartet\BaseApi\Api;

use Phake;

class SavingsTest extends \PHPUnit_Framework_TestCase
{
    private $client;
    private $em;

    public function setUp()
    {
        $this->client = Phake::mock('\Quartet\BaseApi\Client');
        $this->em = Phake::mock('\Quartet\BaseApi\EntityManager');
    }

    /**
     * @dataProvider limitProvider
     */
    public function test_get($limit, $isValid)
    {
        Phake::when($this->client)->request('get', '/1/savings', array_filter([
            'start_created' => '2000-01-01',
            'end_created' => '2100-12-31',
            'limit' => $limit,
            'offset' => 0,
        ]))->thenReturn([
            'savings' => [
                ['test1' => 'test'],
                ['test2' => 'test'],
                ['test3' => 'test'],
            ],
        ]);
        Phake::when($this->em)->getEntity('Saving', ['test1' => 'test'])->thenReturn('entity1');
        Phake::when($this->em)->getEntity('Saving', ['test2' => 'test'])->thenReturn('entity2');
        Phake::when($this->em)->getEntity('Saving', ['test3' => 'test'])->thenReturn('entity3');

        $savingsApi = new Savings($this->client, $this->em);

        $expected = ['entity1', 'entity2', 'entity3'];

        if (!$isValid) {
            $this->setExpectedException('\Quartet\BaseApi\Exception\InvalidParameterException');
        }
        $this->assertEquals($expected, $savingsApi->get('2000-01-01', '2100-12-31', $limit));
    }

    public function limitProvider()
    {
        return [
            [0, true],
            [20, true],
            [100, true],
            [101, false],
        ];
    }
}
