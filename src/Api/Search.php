<?php
namespace Quartet\BaseApi\Api;

use Quartet\BaseApi\Client;
use Quartet\BaseApi\EntityManager;
use Quartet\BaseApi\Exception\InvalidParameterException;

class Search extends Api
{
    private $sortableFields;
    private $searchableFields;

    public function __construct(Client $client, EntityManager $entityManager = null)
    {
        parent::__construct($client, $entityManager);

        $this->sortableFields = [
            'item_id',
            'price',
            'stock',
            'order_count',
            'modified',
        ];

        $this->searchableFields = [
            'item_id',
            'title',
            'detail',
            'price',
            'stock',
            'img1_origin',
            'img1_76',
            'img1_146',
            'img1_300',
            'img1_500',
            'img1_640',
            'img1_sp_480',
            'img1_sp_640',
            'img2_origin',
            'img2_76',
            'img2_146',
            'img2_300',
            'img2_500',
            'img2_640',
            'img2_sp_480',
            'img2_sp_640',
            'img3_origin',
            'img3_76',
            'img3_146',
            'img3_300',
            'img3_500',
            'img3_640',
            'img3_sp_480',
            'img3_sp_640',
            'img4_origin',
            'img4_76',
            'img4_146',
            'img4_300',
            'img4_500',
            'img4_640',
            'img4_sp_480',
            'img4_sp_640',
            'img5_origin',
            'img5_76',
            'img5_146',
            'img5_300',
            'img5_500',
            'img5_640',
            'img5_sp_480',
            'img5_sp_640',
            'modified',
            'shop_id',
            'shop_name',
            'shop_url',
            'categories',
        ];
    }

    /**
     * @param string $client_id
     * @param string $client_secret
     * @param string $q
     * @param string|null $sort
     * @param int $size
     * @param string $fields
     * @return \Quartet\BaseApi\Entity\SearchResult
     * @throws InvalidParameterException
     */
    public function get($client_id, $client_secret, $q, $sort = null, $start = 0, $size = 10, $fields = 'shop_name,title,detail,categories')
    {
        $criteria = explode(',', $sort);
        if ($criteria[0]) {
            foreach ($criteria as $criterion) {
                $criterion = trim($criterion);
                $criterion = preg_split('/\s+/', $criterion);

                if (!in_array($criterion[0], $this->sortableFields)) {
                    throw new InvalidParameterException;
                }
                if (!in_array($criterion[1], ['asc', 'desc'])) {
                    throw new InvalidParameterException;
                }
            }
        }

        if (intval($size) > 50) {
            throw new InvalidParameterException;
        }

        $fieldList = explode(',', $fields);
        if ($fieldList[0]) {
            foreach ($fieldList as $field) {
                $field = trim($field);

                if (!in_array($field, $this->searchableFields)) {
                    throw new InvalidParameterException;
                }
            }
        }

        $params = array_filter(compact('client_id', 'client_secret', 'q', 'start', 'sort', 'size', 'fields'));

        $data = $this->client->request('get', '/1/search', $params);

        $searchResult = $this->entityManager->getEntity('SearchResult', $data);

        return $searchResult;
    }

    /**
     * @param string $client_id
     * @param string $client_secret
     * @param int|string $item_id
     * @param int|string $shop_id
     * @return \Quartet\BaseApi\Entity\Item[]
     */
    public function refresh($client_id, $client_secret, $item_id, $shop_id)
    {
        if (count(explode(',', $item_id)) > 50) {
            throw new InvalidParameterException;
        }

        $params = compact('client_id', 'client_secret', 'item_id', 'shop_id');

        $data = $this->client->request('get', '/1/search/refresh', $params);

        $items = [];
        foreach ($data['items'] as $itemArray) {
            $items[] = $this->entityManager->getEntity('Item', $itemArray);
        }

        return $items;
    }
}
