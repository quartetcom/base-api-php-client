<?php
namespace Quartet\BaseApi\Api;

class Items extends AbstractApi
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

    public function detail()
    {
    }

    public function add()
    {
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
