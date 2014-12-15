<?php
namespace Quartet\BaseApi\Api;

use Phake;
use Quartet\BaseApi\Entity\ItemCategory;

class ItemCategoriesTest extends \PHPUnit_Framework_TestCase
{
    private $client;
    private $em;

    public function setUp()
    {
        $this->client = Phake::mock('\Quartet\BaseApi\Client');
        $this->em = Phake::mock('\Quartet\BaseApi\EntityManager');
    }

    public function test_detail()
    {
        Phake::when($this->client)->request('get', '/1/item_categories/detail/100')->thenReturn([
            'item_categories' => [
                ['test1' => 'test'],
                ['test2' => 'test'],
                ['test3' => 'test'],
            ],
        ]);
        Phake::when($this->em)->getEntity('ItemCategory', ['test1' => 'test'])->thenReturn('entity1');
        Phake::when($this->em)->getEntity('ItemCategory', ['test2' => 'test'])->thenReturn('entity2');
        Phake::when($this->em)->getEntity('ItemCategory', ['test3' => 'test'])->thenReturn('entity3');

        $itemCategoriesApi = new ItemCategories($this->client, $this->em);

        $expected = ['entity1', 'entity2', 'entity3'];

        $this->assertEquals($expected, $itemCategoriesApi->detail(100));
    }

    public function test_add()
    {
        $itemCategory = new ItemCategory;
        $itemCategory->item_id = 'test';

        Phake::when($this->em)->getFlatArray($itemCategory)->thenReturn(['flat' => 'array']);
        Phake::when($this->client)->request('post', '/1/item_categories/add', ['flat' => 'array'])->thenReturn([
            'item_categories' => [
                ['test1' => 'test'],
                ['test2' => 'test'],
                ['test3' => 'test'],
            ],
        ]);
        Phake::when($this->em)->getEntity('ItemCategory', ['test1' => 'test'])->thenReturn('entity1');
        Phake::when($this->em)->getEntity('ItemCategory', ['test2' => 'test'])->thenReturn('entity2');
        Phake::when($this->em)->getEntity('ItemCategory', ['test3' => 'test'])->thenReturn('entity3');

        $itemCategoriesApi = new ItemCategories($this->client, $this->em);

        $expected = ['entity1', 'entity2', 'entity3'];

        $this->assertEquals($expected, $itemCategoriesApi->add($itemCategory));
    }

    public function test_add_with_missing_parameters()
    {
        $itemCategory = new ItemCategory;   // no item_id.

        $itemCategoriesApi = new ItemCategories($this->client, $this->em);

        $this->setExpectedException('\Quartet\BaseApi\Exception\MissingRequiredParameterException');
        $itemCategoriesApi->add($itemCategory);
    }

    public function test_delete()
    {
        Phake::when($this->client)->request('post', '/1/item_categories/delete', ['item_category_id' => 100])->thenReturn([
            'item_categories' => [
                ['test1' => 'test'],
                ['test2' => 'test'],
                ['test3' => 'test'],
            ],
        ]);
        Phake::when($this->em)->getEntity('ItemCategory', ['test1' => 'test'])->thenReturn('entity1');
        Phake::when($this->em)->getEntity('ItemCategory', ['test2' => 'test'])->thenReturn('entity2');
        Phake::when($this->em)->getEntity('ItemCategory', ['test3' => 'test'])->thenReturn('entity3');

        $itemCategoriesApi = new ItemCategories($this->client, $this->em);

        $expected = ['entity1', 'entity2', 'entity3'];

        $this->assertEquals($expected, $itemCategoriesApi->delete(100));
    }
}
