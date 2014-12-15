<?php
namespace Quartet\BaseApi\Api;

use Phake;
use Quartet\BaseApi\Entity\Item;

class ItemsTest extends \PHPUnit_Framework_TestCase
{
    private $client;
    private $em;

    public function setUp()
    {
        $this->client = Phake::mock('\Quartet\BaseApi\Client');
        $this->em = Phake::mock('\Quartet\BaseApi\EntityManager');
    }

    public function test_get()
    {
        Phake::when($this->client)->request('get', '/1/items', [
            'order' => 'list_order',
            'sort' => 'asc',
            'limit' => 20,
            'offset' => 0,
        ])->thenReturn([
            'items' => [
                ['test1' => 'test'],
                ['test2' => 'test'],
                ['test3' => 'test'],
            ],
        ]);
        Phake::when($this->em)->getEntity('Item', ['test1' => 'test'])->thenReturn('entity1');
        Phake::when($this->em)->getEntity('Item', ['test2' => 'test'])->thenReturn('entity2');
        Phake::when($this->em)->getEntity('Item', ['test3' => 'test'])->thenReturn('entity3');

        $itemsApi = new Items($this->client, $this->em);

        $expected = ['entity1', 'entity2', 'entity3'];

        $this->assertEquals($expected, $itemsApi->get());
    }

    /**
     * @dataProvider invalidParametersProvider
     */
    public function test_get_with_invalid_parameters($order, $sort, $limit)
    {
        $itemsApi = new Items($this->client, $this->em);

        $this->setExpectedException('\Quartet\BaseApi\Exception\InvalidParameterException');
        $itemsApi->get($order, $sort, $limit);
    }

    public function invalidParametersProvider()
    {
        return [
            ['invalid order', 'asc', 20],       // invalid order.
            ['list_order', 'invalid sort',20],  // invalid sort.
            ['list_order', 'asc', 101],         // invalid limit.
        ];
    }

    public function test_detail()
    {
        Phake::when($this->client)->request('get', '/1/items/detail/100')->thenReturn(['item' => ['test' => 'test']]);
        Phake::when($this->em)->getEntity('Item', ['test' => 'test'])->thenReturn('entity');

        $itemsApi = new Items($this->client, $this->em);

        $this->assertEquals('entity', $itemsApi->detail(100));
    }

    public function test_add()
    {
        $item = new Item;
        $item->title = $item->price = $item->stock = 'test';

        Phake::when($this->em)->getFlatArray($item)->thenReturn(['flat' => 'array']);
        Phake::when($this->client)->request('post', '/1/items/add', ['flat' => 'array'])->thenReturn(['item' => ['test' => 'test']]);
        Phake::when($this->em)->getEntity('Item', ['test' => 'test'])->thenReturn('entity');

        $itemsApi = new Items($this->client, $this->em);

        $this->assertEquals('entity', $itemsApi->add($item));
    }

    /**
     * @dataProvider invalidItemProvider
     */
    public function test_add_with_missing_parameters($item)
    {
        $itemsApi = new Items($this->client, $this->em);

        $this->setExpectedException('\Quartet\BaseApi\Exception\MissingRequiredParameterException');
        $itemsApi->add($item);
    }

    public function invalidItemProvider()
    {
        $item1 = new Item;
        $item2 = clone $item1;
        $item3 = clone $item2;

        $item1->price = $item1->stock = 'test'; // no title.
        $item2->title = $item2->stock = 'test'; // no price.
        $item3->title = $item3->price = 'test'; // no stock.

        return [
            [$item1],
            [$item2],
            [$item3],
        ];
    }

    public function test_edit()
    {
        $item = new Item();
        $item->item_id = 'test';

        Phake::when($this->em)->getFlatArray($item)->thenReturn(['flat' => 'array']);
        Phake::when($this->client)->request('post', '/1/items/edit', ['flat' => 'array'])->thenReturn(['item' => ['test' => 'test']]);
        Phake::when($this->em)->getEntity('Item', ['test' => 'test'])->thenReturn('entity');

        $itemsApi = new Items($this->client, $this->em);

        $this->assertEquals('entity', $itemsApi->edit($item));
    }

    public function test_edit_with_missing_parameters()
    {
        $item = new Item;   // no item_id.

        $itemsApi = new Items($this->client, $this->em);

        $this->setExpectedException('\Quartet\BaseApi\Exception\MissingRequiredParameterException');
        $itemsApi->edit($item);
    }

    public function test_delete()
    {
        Phake::when($this->client)->request('post', '/1/items/delete', ['item_id' => 100])->thenReturn(['result' => true]);
        Phake::when($this->client)->request('post', '/1/items/delete', ['item_id' => -1])->thenReturn(['result' => false]);

        $itemsApi = new Items($this->client, $this->em);

        $this->assertTrue($itemsApi->delete(100));
        $this->assertFalse($itemsApi->delete(-1));
    }

    /**
     * @dataProvider imageNoProvider
     */
    public function test_add_image($imageNo, $isValid)
    {
        Phake::when($this->client)->request('post', '/1/items/add_image', [
            'item_id' => 100,
            'image_no' => $imageNo,
            'image_url' => 'test url',
        ])->thenReturn(['item' => ['test' => 'test']]);
        Phake::when($this->em)->getEntity('Item', ['test' => 'test'])->thenReturn('entity');

        $itemsApi = new Items($this->client, $this->em);

        if (!$isValid) {
            $this->setExpectedException('\Quartet\BaseApi\Exception\InvalidParameterException');
        }
        $this->assertEquals('entity', $itemsApi->add_image(100, $imageNo, 'test url'));
    }

    /**
     * @dataProvider imageNoProvider
     */
    public function test_delete_image($imageNo, $isValid)
    {
        Phake::when($this->client)->request('post', '/1/items/delete_image', [
            'item_id' => 100,
            'image_no' => $imageNo,
        ])->thenReturn(['item' => ['test' => 'test']]);
        Phake::when($this->em)->getEntity('Item', ['test' => 'test'])->thenReturn('entity');

        $itemsApi = new Items($this->client, $this->em);

        if (!$isValid) {
            $this->setExpectedException('\Quartet\BaseApi\Exception\InvalidParameterException');
        }
        $this->assertEquals('entity', $itemsApi->delete_image(100, $imageNo));
    }

    public function imageNoProvider()
    {
        return [
            [0, false],
            [1, true],
            [5, true],
            [6, false],
        ];
    }

    /**
     * @dataProvider stockParametersProvider
     */
    public function test_edit_stock($stock, $variationId, $variationStock, $isValid)
    {
        Phake::when($this->client)->request('post', '/1/items/edit_stock', array_filter([
            'item_id' => 100,
            'stock' => $stock,
            'variation_id' => $variationId,
            'variation_stock' => $variationStock,
        ]))->thenReturn(['item' => ['test' => 'test']]);
        Phake::when($this->em)->getEntity('Item', ['test' => 'test'])->thenReturn('entity');

        $itemsApi = new Items($this->client, $this->em);

        if (!$isValid) {
            $this->setExpectedException('\Quartet\BaseApi\Exception\InvalidParameterException');
        }
        $this->assertEquals('entity', $itemsApi->edit_stock(100, $stock, $variationId, $variationStock));
    }

    public function stockParametersProvider()
    {
        return [
            // change item and variation's stock.
            [10, 1, 5, true],

            // change only item's stock.
            [10, null, 5, true],
            [10, 1, null, true],
            [10, null, null, true],

            // change only variation's stock.
            [null, 1, 5, true],
            [null, null, 5, false],
            [null, 1, null, false],
            [null, null, null, false],
        ];
    }

    public function test_delete_variation()
    {
        Phake::when($this->client)->request('post', '/1/items/delete_variation', [
            'item_id' => 100,
            'variation_id' => 1,
        ])->thenReturn(['item' => ['test' => 'test']]);
        Phake::when($this->em)->getEntity('Item', ['test' => 'test'])->thenReturn('entity');

        $itemsApi = new Items($this->client, $this->em);

        $this->assertEquals('entity', $itemsApi->delete_variation(100, 1));
    }
}
