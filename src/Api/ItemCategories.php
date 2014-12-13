<?php
namespace Quartet\BaseApi\Api;

use Quartet\BaseApi\Entity\ItemCategory;

class ItemCategories extends Api
{
    /**
     * @param int|string $item_id
     * @return ItemCategory[]
     */
    public function detail($item_id)
    {
        $data = $this->client->request('post', "/1/item_categories/detail/{$item_id}");

        $itemCategories = [];
        foreach ($data['item_categories'] as $itemCategoryArray) {
            $itemCategories[] = $this->entityManager->getEntity('ItemCategory', $itemCategoryArray);
        }

        return $itemCategories;
    }

    /**
     * @param ItemCategory $itemCategory
     * @return ItemCategory[]
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
        foreach ($data['item_categories'] as $itemCategoryArray) {
            $itemCategories[] = $this->entityManager->getEntity('Category', $itemCategoryArray);
        }

        return $itemCategories;

    }

    /**
     * @param int|string $item_category_id
     * @return ItemCategory[]
     */
    public function delete($item_category_id)
    {
        $params = compact('item_category_id');

        $data = $this->client->request('post', '/1/item_categories/delete', $params);

        $itemCategories = [];
        foreach ($data['item_categories'] as $itemCategoryArray) {
            $itemCategories[] = $this->entityManager->getEntity('Category', $itemCategoryArray);
        }

        return $itemCategories;
    }
}
