<?php
namespace Quartet\BaseApi\Api;

use Phake;

class OrdersTest extends \PHPUnit_Framework_TestCase
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
        Phake::when($this->client)->request('get', '/1/orders', array_filter([
            'start_ordered' => '2000-01-01',
            'end_ordered' => '2100-12-31',
            'limit' => $limit,
            'offset' => 0,
        ]))->thenReturn([
            'orders' => [
                ['test1' => 'test'],
                ['test2' => 'test'],
                ['test3' => 'test'],
            ],
        ]);
        Phake::when($this->em)->getEntity('Order', ['test1' => 'test'])->thenReturn('entity1');
        Phake::when($this->em)->getEntity('Order', ['test2' => 'test'])->thenReturn('entity2');
        Phake::when($this->em)->getEntity('Order', ['test3' => 'test'])->thenReturn('entity3');

        $ordersApi = new Orders($this->client, $this->em);

        $expected = ['entity1', 'entity2', 'entity3'];

        if (!$isValid) {
            $this->setExpectedException('\Quartet\BaseApi\Exception\InvalidParameterException');
        }
        $this->assertEquals($expected, $ordersApi->get('2000-01-01', '2100-12-31', $limit));
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

    public function test_detail()
    {
        Phake::when($this->client)->request('get', '/1/orders/detail/unique_key')->thenReturn(['order' => ['test' => 'test']]);
        Phake::when($this->em)->getEntity('Order', ['test' => 'test'])->thenReturn('entity');

        $ordersApi = new Orders($this->client, $this->em);

        $this->assertEquals('entity', $ordersApi->detail('unique_key'));
    }

    /**
     * @dataProvider statusProvider
     */
    public function test_edit_status($status, $isValid)
    {
        Phake::when($this->client)->request('post', '/1/orders/edit_status', [
            'order_item_id' => 100,
            'status' => $status,
            'add_comment' => 'test',
        ])->thenReturn(['order' => ['test' => 'test']]);
        Phake::when($this->em)->getEntity('Order', ['test' => 'test'])->thenReturn('entity');

        $ordersApi = new Orders($this->client, $this->em);

        if (!$isValid) {
            $this->setExpectedException('\Quartet\BaseApi\Exception\InvalidParameterException');
        }
        $this->assertEquals('entity', $ordersApi->edit_status(100, $status, 'test'));
    }

    public function statusProvider()
    {
        return [
            ['dispatched', true],
            ['cancelled', true],
            ['ordered', false],
            ['other invalid status', false],
        ];
    }
}
