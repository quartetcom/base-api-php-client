<?php
namespace Quartet\BaseApi\Api;

use Quartet\BaseApi\Client;

class Items extends AbstractApi
{
    /**
     * @var \Quartet\BaseApi\Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        parent::__construct();

        $this->client = $client;
    }

    /**
     * @param array $params
     * @return \Quartet\BaseApi\Entity\Item[]
     */
    public function get(array $params = [])
    {
        $response = $this->client->request('get', '/1/items', $params);

        $data = json_decode($response->getBody(), true);

        $items = [];
        foreach ($data['items'] as $itemArray) {
            $variations = [];
            foreach ($itemArray['variations'] as $variationArray) {
                $variations[] = $this->entityFactory->get('Item\\Variation', $variationArray);
            }
            $item = $this->entityFactory->get('Item', $itemArray);
            $item->variations = $variations;
            $items[] = $item;
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
