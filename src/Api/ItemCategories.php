<?php
namespace Quartet\BaseApi\Api;

use Quartet\BaseApi\Entity\ItemCategory;

class ItemCategories extends Api
{
    /**
     * @param int|string $item_id
     * @return \Quartet\BaseApi\Entity\ItemCategory[]
     */
    public function detail($item_id)
    {
        $data = $this->client->request('post', "/1/item_categories/detail/{$item_id}");

        $itemCategories = [];
        foreach ($data['item_categories'] as $itemCategory) {
            $itemCategories[] = $this->entityManager->getEntity('ItemCategory', $itemCategory);
        }

        return $itemCategories;
    }

    /**
     * @param \Quartet\BaseApi\Entity\ItemCategory $itemCategory
     * @return \Quartet\BaseApi\Entity\ItemCategory[]
     * @throws MissingRequiredParameterException
     */
    public function add(ItemCategory $itemCategory)
    {
        if (is_null($itemCategory->item_id)) {
            throw new MissingRequiredParameterException;
        }

        $params = $this->entityManager->getFlatArray($itemCategory);

        $data = $this->client->request('post', '/1/item_categories/add', $params);

        $itemCategories = [];
        foreach ($data['item_categories'] as $itemCategory) {
            $itemCategories[] = $this->entityManager->getEntity('Category', $itemCategory);
        }

        return $itemCategories;

    }

    /**
     * @param int|string $item_category_id
     * @return \Quartet\BaseApi\Entity\ItemCategory[]
     */
    public function delete($item_category_id)
    {
        $data = $this->client->request('post', '/1/item_categories/delete', [
            'item_category_id' => $item_category_id,
        ]);

        $itemCategories = [];
        foreach ($data['item_categories'] as $itemCategory) {
            $itemCategories[] = $this->entityManager->getEntity('Category', $itemCategory);
        }

        return $itemCategories;
    }
}
