<?php
namespace Quartet\BaseApi\Api;

use Quartet\BaseApi\Entity\Category;
use Quartet\BaseApi\Exception\MissingRequiredParameterException;

class Categories extends AbstractApi
{
    /**
     * @return Category[]
     */
    public function get()
    {
        $data = $this->client->request('get', '/1/categories');

        $categories = [];
        foreach ($data['categories'] as $categoryArray) {
            $categories[] = $this->entityManager->getEntity('Category', $categoryArray);
        }

        return $categories;
    }

    /**
     * @param Category $category
     * @return Category[]
     * @throws MissingRequiredParameterException
     */
    public function add(Category $category)
    {
        if (is_null($category->name)) {
            throw new MissingRequiredParameterException;
        }

        $params = $this->entityManager->getFlatArray($category);

        $data = $this->client->request('post', '/1/categories/add', $params);

        $categories = [];
        foreach ($data['categories'] as $categoryArray) {
            $categories[] = $this->entityManager->getEntity('Category', $categoryArray);
        }

        return $categories;
    }

    /**
     * @param Category $category
     * @return Category[]
     * @throws MissingRequiredParameterException
     */
    public function edit(Category $category)
    {
        if (is_null($category->category_id)) {
            throw new MissingRequiredParameterException;
        }

        $params = $this->entityManager->getFlatArray($category);

        $data = $this->client->request('post', '/1/categories/edit', $params);

        $categories = [];
        foreach ($data['categories'] as $categoryArray) {
            $categories[] = $this->entityManager->getEntity('Category', $categoryArray);
        }

        return $categories;
    }

    /**
     * @param int|string $category_id
     * @return Category[]
     */
    public function delete($category_id)
    {
        $params = compact('category_id');

        $data = $this->client->request('post', '/1/categories/delete', $params);

        $categories = [];
        foreach ($data['categories'] as $categoryArray) {
            $categories[] = $this->entityManager->getEntity('Category', $categoryArray);
        }

        return $categories;
    }
}
