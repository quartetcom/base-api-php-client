<?php
namespace Quartet\BaseApi\Api;

use Quartet\BaseApi\Exception\InvalidParameterException;

class Orders extends Api
{
    /**
     * @param string|null $start_ordered
     * @param string|null $end_ordered
     * @param int $limit
     * @param int $offset
     * @return \Quartet\BaseApi\Entity\Order[]
     * @throws InvalidParameterException
     */
    public function get($start_ordered = null, $end_ordered = null, $limit = 20, $offset = 0)
    {
        if (intval($limit) > 100) {
            throw new InvalidParameterException;
        }

        $params = array_filter(compact('start_ordered', 'end_ordered', 'limit', 'offset'));

        $data = $this->client->request('get', '/1/orders', $params);

        $orders = [];
        foreach ($data['orders'] as $orderArray) {
            $orders[] = $this->entityManager->getEntity('Order', $orderArray);
        }

        return $orders;
    }

    /**
     * @param string $unique_key
     * @return \Quartet\BaseApi\Entity\Order
     */
    public function detail($unique_key)
    {
        $data = $this->client->request('get', "/1/orders/detail/{$unique_key}");

        $order = $this->entityManager->getEntity('Order', $data['order']);

        return $order;
    }

    /**
     * @param int|string $order_item_id
     * @param string $status
     * @param string $add_comment
     * @return \Quartet\BaseApi\Entity\Order
     * @throws InvalidParameterException
     */
    public function edit_status($order_item_id, $status, $add_comment = '')
    {
        if ($status !== 'dispatched' && $status !== 'cancelled') {
            throw new InvalidParameterException;
        }

        $params = compact('order_item_id', 'status', 'add_comment');

        $data = $this->client->request('post', '/1/orders/edit_status', $params);

        $order = $this->entityManager->getEntity('Order', $data['order']);

        return $order;
    }
}
