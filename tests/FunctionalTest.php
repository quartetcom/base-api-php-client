<?php
namespace Quartet\BaseApi;

use Phake;
use Quartet\BaseApi\Api\Orders;
use Quartet\BaseApi\Api\Search;

class FunctionalTest extends \PHPUnit_Framework_TestCase
{
    private $client;

    public function setUp()
    {
        $this->client = Phake::mock('\Quartet\BaseApi\Client');
    }

    /**
     * @group functional
     * @group orders
     */
    public function test_orders_get()
    {
        $this->setFixture(__FUNCTION__);
        $ordersApi = new Orders($this->client);
        $orders = $ordersApi->get();

        $this->assertInstanceOf('\Quartet\BaseApi\Entity\Order', $orders[0]);
        $this->assertInstanceOf('\Quartet\BaseApi\Entity\Order', $orders[1]);
        $this->assertEquals('154D88A39E454289', $orders[0]->unique_key);
        $this->assertFalse($orders[1]->terminated);
    }

    /**
     * @group functional
     * @group orders
     */
    public function test_orders_detail()
    {
        $this->setFixture(__FUNCTION__);
        $ordersApi = new Orders($this->client);
        $order = $ordersApi->detail('unique_key');

        $this->assertInstanceOf('\Quartet\BaseApi\Entity\Order', $order);
        $this->assertEquals('154D88A39E454289', $order->unique_key);
        $this->assertEquals('106-0032', $order->zip_code);

        $this->assertInstanceOf('\Quartet\BaseApi\Entity\Subset\OrderReceiver', $order->order_receiver);
        $this->assertInstanceOf('\Quartet\BaseApi\Entity\Subset\OrderDiscount', $order->order_discount);
        $this->assertInstanceOf('\Quartet\BaseApi\Entity\Subset\CCPaymentTransaction', $order->c_c_payment_transaction);
        $this->assertInstanceOf('\Quartet\BaseApi\Entity\Subset\CvsPaymentTransaction', $order->cvs_payment_transaction);
        $this->assertInstanceOf('\Quartet\BaseApi\Entity\Subset\OrderItem', $order->order_items[0]);

        $this->assertEquals('150-0043', $order->order_receiver->zip_code);
        $this->assertEquals(1000, $order->order_discount->discount);
        $this->assertEquals(200, $order->c_c_payment_transaction->collected_fee);
        $this->assertEquals(null, $order->cvs_payment_transaction->collected_fee);
        $this->assertEquals(124, $order->order_items[1]->order_item_id);
    }

    /**
     * @group functional
     * @group search
     */
    public function test_search_get()
    {
        $this->setFixture(__FUNCTION__);
        $searchApi = new Search($this->client);
        $result = $searchApi->get('', '', '');

        $this->assertInstanceOf('\Quartet\BaseApi\Entity\SearchResult', $result);
        $this->assertEquals(2, $result->found);
        $this->assertEquals(0, $result->start);

        $this->assertInstanceOf('\Quartet\BaseApi\Entity\Item', $result->items[0]);
        $this->assertEquals(2234, $result->items[1]->item_id);
        $this->assertEquals('shop', $result->items[1]->shop_id);
        $this->assertEquals('BASEショップ', $result->items[1]->shop_name);
        $this->assertEquals('http://shop.thebase.in', $result->items[1]->shop_url);
        $this->assertEquals(['Tシャツ', '奇抜'], $result->items[1]->categories);
    }

    private function setFixture($testFunctionName)
    {
        preg_match('/^test_(.+)$/', $testFunctionName, $matches);
        $jsonFileName = __DIR__ . '/Fixture/' . $matches[1] . '.json';
        $data = json_decode(file_get_contents($jsonFileName), true);

        Phake::when($this->client)->request(Phake::anyParameters())->thenReturn($data);
    }
}
