<?php
namespace Quartet\BaseApi\Api;

use Quartet\BaseApi\Entity\Item;
use Quartet\BaseApi\Exception\InvalidParameterException;
use Quartet\BaseApi\Exception\MissingRequiredParameterException;

class Items extends Api
{
    /**
     * @param string $order
     * @param string $sort
     * @param int $limit
     * @param int $offset
     * @return Item[]
     */
    public function get($order = 'list_order', $sort = 'asc', $limit = 20, $offset = 0)
    {
        if ($order !== 'list_order' && $order !== 'created') {
            throw new InvalidParameterException;
        }

        if ($sort !== 'asc' && $sort !== 'desc') {
            throw new InvalidParameterException;
        }

        if (intval($limit) > 100) {
            throw new InvalidParameterException;
        }

        $params = compact('order', 'sort', 'limit', 'offset');

        $data = $this->client->request('get', '/1/items', $params);

        $items = [];
        foreach ($data['items'] as $item) {
            $items[] = $this->entityManager->getEntity('Item', $item);
        }

        return $items;
    }

    /**
     * @param int|string $item_id
     * @return Item
     */
    public function detail($item_id)
    {
        $data = $this->client->request('get', "/1/items/detail/{$item_id}");

        return $this->entityManager->getEntity('Item', $data['item']);
    }

    /**
     * @param Item $item
     * @return Item
     * @throws MissingRequiredParameterException
     */
    public function add(Item $item)
    {
        if (is_null($item->title) || is_null($item->price) || is_null($item->stock)) {
            throw new MissingRequiredParameterException;
        }

        $params = $this->entityManager->getFlatArray($item);

        $data = $this->client->request('post', '/1/items/add', $params);

        return $this->entityManager->getEntity('Item', $data['item']);
    }

    /**
     * @param Item $item
     * @return Item
     * @throws MissingRequiredParameterException
     */
    public function edit(Item $item)
    {
        if (is_null($item->item_id)) {
            throw new MissingRequiredParameterException;
        }

        $params = $this->entityManager->getFlatArray($item);

        $data = $this->client->request('post', '/1/items/edit', $params);

        return $this->entityManager->getEntity('Item', $data['item']);
    }

    /**
     * @param int|string $item_id
     * @return bool
     */
    public function delete($item_id)
    {
        $data = $this->client->request('post', '/1/items/delete', [
            'item_id' => $item_id,
        ]);

        return $data['result'] === 'true';
    }

    /**
     * @param int|string $item_id
     * @param int|string $image_no
     * @param string $image_url
     * @return Item
     * @throws InvalidParameterException
     */
    public function add_image($item_id, $image_no, $image_url)
    {
        if (intval($image_no) < 1 || 5 < intval($image_no)) {
            throw new InvalidParameterException;
        }

        $params = compact('item_id', 'image_no', 'image_url');

        $data = $this->client->request('post', '/1/items/add_image', $params);

        return $this->entityManager->getEntity('Item', $data['item']);
    }

    /**
     * @param int|string $item_id
     * @param int|string $image_no
     * @return Item
     * @throws InvalidParameterException
     */
    public function delete_image($item_id, $image_no)
    {
        if (intval($image_no) < 1 || 5 < intval($image_no)) {
            throw new InvalidParameterException;
        }

        $params = compact('item_id', 'image_no');

        $data = $this->client->request('post', '/1/items/delete_image', $params);

        return $this->entityManager->getEntity('Item', $data['item']);
    }

    /**
     * @param int|string $item_id
     * @param int|string|null $stock
     * @param int|string|null $variation_id
     * @param int|string|null $variation_stock
     * @return Item
     * @throws InvalidParameterException
     */
    public function edit_stock($item_id, $stock = null, $variation_id = null, $variation_stock = null)
    {
        if (is_null($stock) && (is_null($variation_id) || is_null($variation_stock))) {
            throw new InvalidParameterException;
        }

        $params = array_filter(compact('item_id', 'stock', 'variation_id', 'variation_stock'));

        $data = $this->client->request('post', '/1/items/edit_stock', $params);

        return $this->entityManager->getEntity('Item', $data['item']);
    }

    /**
     * @param int|string $item_id
     * @param int|string $variation_id
     * @return Item
     */
    public function delete_variation($item_id, $variation_id)
    {
        $params = compact('item_id', 'variation_id');

        $data = $this->client->request('post', '/1/items/delete_variation', $params);

        return $this->entityManager->getEntity('Item', $data['item']);
    }
}
