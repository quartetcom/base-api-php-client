<?php
namespace Quartet\BaseApi\Api;

use Quartet\BaseApi\Entity\Item;
use Quartet\BaseApi\Exception\MissingRequiredParameterException;

class Items extends Api
{
    /**
     * @param array $params
     * @return \Quartet\BaseApi\Entity\Item[]
     */
    public function get(array $params = [])
    {
        $response = $this->client->request('get', '/1/items', $params);

        $data = json_decode($response->getBody(), true);

        $items = [];
        foreach ($data['items'] as $item) {
            $items[] = $this->entityManager->getEntity('Item', $item);
        }

        return $items;
    }

    /**
     * @param string $id
     * @return \Quartet\BaseApi\Entity\EntityInterface
     */
    public function detail($id)
    {
        $response = $this->client->request('get', "/1/items/detail/{$id}");

        $data = json_decode($response->getBody(), true);

        return $this->entityManager->getEntity('Item', $data['item']);
    }

    /**
     * @param Item $item
     * @return \Quartet\BaseApi\Entity\Item
     * @throws \Quartet\BaseApi\Exception\MissingRequiredParameterException
     */
    public function add(Item $item)
    {
        if (is_null($item->title) || is_null($item->price) || is_null($item->stock)) {
            throw new MissingRequiredParameterException;
        }

        $params = $this->entityManager->getFlatArray($item);

        $response = $this->client->request('post', '/1/items/add', $params);

        $data = json_decode($response->getBody(), true);

        return $this->entityManager->getEntity('Item', $data['item']);
    }

    public function edit()
    {
    }

    public function delete()
    {
    }

    public function add_image()
    {
    }

    public function delete_image()
    {
    }

    public function edit_stock()
    {
    }

    public function delete_variation()
    {
    }
}
