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
            $items[] = $this->entityFactory->get('Item', $item);
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

        return $this->entityFactory->get('Item', $data['item']);
    }

    /**
     * @param Item $item
     * @return \Quartet\BaseApi\Entity\EntityInterface
     * @throws \Quartet\BaseApi\Exception\MissingRequiredParameterException
     */
    public function add(Item $item)
    {
        if (is_null($item->title) || is_null($item->price) || is_null($item->stock)) {
            throw new MissingRequiredParameterException;
        }

        $params = [
            'title' => $item->title,
            'detail' => $item->detail,
            'price' => $item->price,
            'stock' => $item->stock,
            'visible' => $item->visible,
            'identifier' => $item->identifier,
            'list_order' => $item->list_order,
        ];

        $i = 0;
        foreach ($item->variations as $variation) {
            /** @var \Quartet\BaseApi\Entity\Variation $variation */
            $params['variation'][$i] = $variation->variation;
            $params['variation_stock'][$i] = $variation->variation_stock;
            $params['variation_identifier'][$i] = $variation->variation_identifier;
            $i++;
        }

        $response = $this->client->request('post', '/1/items/add', $params);

        $data = json_decode($response->getBody(), true);

        return $this->entityFactory->get('Item', $data['item']);
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
