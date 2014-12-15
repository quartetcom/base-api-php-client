<?php
namespace Quartet\BaseApi\Api;

use Phake;
use Quartet\BaseApi\Entity\Category;

class CategoriesTest extends \PHPUnit_Framework_TestCase
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
        Phake::when($this->client)->request('get', '/1/categories')->thenReturn([
            'categories' => [
                ['test1' => 'test'],
                ['test2' => 'test'],
                ['test3' => 'test'],
            ],
        ]);
        Phake::when($this->em)->getEntity('Category', ['test1' => 'test'])->thenReturn('entity1');
        Phake::when($this->em)->getEntity('Category', ['test2' => 'test'])->thenReturn('entity2');
        Phake::when($this->em)->getEntity('Category', ['test3' => 'test'])->thenReturn('entity3');

        $categoriesApi = new Categories($this->client, $this->em);

        $expected = ['entity1', 'entity2', 'entity3'];

        $this->assertEquals($expected, $categoriesApi->get());
    }

    public function test_add()
    {
        $category = new Category;
        $category->name = 'test';

        Phake::when($this->em)->getFlatArray($category)->thenReturn(['flat' => 'array']);
        Phake::when($this->client)->request('post', '/1/categories/add', ['flat' => 'array'])->thenReturn([
            'categories' => [
                ['test1' => 'test'],
                ['test2' => 'test'],
                ['test3' => 'test'],
            ],
        ]);
        Phake::when($this->em)->getEntity('Category', ['test1' => 'test'])->thenReturn('entity1');
        Phake::when($this->em)->getEntity('Category', ['test2' => 'test'])->thenReturn('entity2');
        Phake::when($this->em)->getEntity('Category', ['test3' => 'test'])->thenReturn('entity3');

        $categoriesApi = new Categories($this->client, $this->em);

        $expected = ['entity1', 'entity2', 'entity3'];

        $this->assertEquals($expected, $categoriesApi->add($category));
    }

    public function test_add_with_missing_parameters()
    {
        $category = new Category;   // no name.

        $categoriesApi = new Categories($this->client, $this->em);

        $this->setExpectedException('\Quartet\BaseApi\Exception\MissingRequiredParameterException');
        $categoriesApi->add($category);
    }

    public function test_edit()
    {
        $category = new Category;
        $category->category_id = 'test';

        Phake::when($this->em)->getFlatArray($category)->thenReturn(['flat' => 'array']);
        Phake::when($this->client)->request('post', '/1/categories/edit', ['flat' => 'array'])->thenReturn([
            'categories' => [
                ['test1' => 'test'],
                ['test2' => 'test'],
                ['test3' => 'test'],
            ],
        ]);
        Phake::when($this->em)->getEntity('Category', ['test1' => 'test'])->thenReturn('entity1');
        Phake::when($this->em)->getEntity('Category', ['test2' => 'test'])->thenReturn('entity2');
        Phake::when($this->em)->getEntity('Category', ['test3' => 'test'])->thenReturn('entity3');

        $categoriesApi = new Categories($this->client, $this->em);

        $expected = ['entity1', 'entity2', 'entity3'];

        $this->assertEquals($expected, $categoriesApi->edit($category));
    }

    public function test_edit_with_missing_parameters()
    {
        $category = new Category;   // no category_id.

        $categoriesApi = new Categories($this->client, $this->em);

        $this->setExpectedException('\Quartet\BaseApi\Exception\MissingRequiredParameterException');
        $categoriesApi->edit($category);
    }

    public function test_delete()
    {
        Phake::when($this->client)->request('post', '/1/categories/delete', ['category_id' => 100])->thenReturn([
            'categories' => [
                ['test1' => 'test'],
                ['test2' => 'test'],
                ['test3' => 'test'],
            ],
        ]);
        Phake::when($this->em)->getEntity('Category', ['test1' => 'test'])->thenReturn('entity1');
        Phake::when($this->em)->getEntity('Category', ['test2' => 'test'])->thenReturn('entity2');
        Phake::when($this->em)->getEntity('Category', ['test3' => 'test'])->thenReturn('entity3');

        $categoriesApi = new Categories($this->client, $this->em);

        $expected = ['entity1', 'entity2', 'entity3'];

        $this->assertEquals($expected, $categoriesApi->delete(100));
    }
}
