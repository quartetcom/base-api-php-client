<?php
namespace Quartet\BaseApi\Api;

use Phake;

class SearchTest extends \PHPUnit_Framework_TestCase
{
    private $client;
    private $em;

    public function setUp()
    {
        $this->client = Phake::mock('\Quartet\BaseApi\Client');
        $this->em = Phake::mock('\Quartet\BaseApi\EntityManager');
    }

    /**
     * @dataProvider parametersProvider
     */
    public function test_get($sort, $size, $fields, $isValid)
    {
        Phake::when($this->client)->request('get', '/1/search', array_filter([
            'client_id' => 'test client id',
            'client_secret' => 'test client secret',
            'q' => 'test search words',
            'sort' => $sort,
            'start' => 0,
            'size' => $size,
            'fields' => $fields,
        ]))->thenReturn(['test' => 'test']);
        Phake::when($this->em)->getEntity('SearchResult', ['test' => 'test'])->thenReturn('entity');

        $searchApi = new Search($this->client, $this->em);

        if (!$isValid) {
            $this->setExpectedException('\Quartet\BaseApi\Exception\InvalidParameterException');
        }
        $this->assertEquals('entity', $searchApi->get('test client id', 'test client secret', 'test search words', $sort, 0, $size, $fields));
    }

    public function parametersProvider()
    {
        return [
            // valid parameters.
            ['order_count desc,item_id asc', 10, 'shop_name,title,detail,categories', true],
            ['   stock   desc   ,   modified   asc   ', 50, '   shop_name   ,   title   ,   detail   ,   categories   ', true],

            // invalid sort.
            ['item_id dsc', 10, '', false],
            ['invalid_field asc', 10, '', false],

            // invalid size.
            ['item_id asc', 51, '', false],

            // invalid fields.
            ['item_id asc', 10, 'invalid_field', false],
        ];
    }

    /**
     * @dataProvider itemIdProvider
     */
    public function test_refresh($itemId, $isValid)
    {
        Phake::when($this->client)->request('get', '/1/search/refresh', [
            'client_id' => 'test client id',
            'client_secret' => 'test client secret',
            'item_id' => $itemId,
            'shop_id' => 100,
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

        $searchApi = new Search($this->client, $this->em);

        $expected = ['entity1', 'entity2', 'entity3'];

        if (!$isValid) {
            $this->setExpectedException('\Quartet\BaseApi\Exception\InvalidParameterException');
        }
        $this->assertEquals($expected, $searchApi->refresh('test client id', 'test client secret', $itemId, 100));
    }

    public function itemIdProvider()
    {
        return [
            ['1,2,3,4,5,6,7,8,9,10', true],
            ['1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20', true],
            ['1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30', true],
            ['1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50', true],
            ['1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51', false],
        ];
    }
}
