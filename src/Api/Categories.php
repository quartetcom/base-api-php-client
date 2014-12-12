<?php
namespace Quartet\BaseApi\Api;

use Quartet\BaseApi\Entity\Category;

class Categories extends Api
{
    /**
     * @return \Quartet\BaseApi\Entity\Category[]
     */
    public function get()
    {
        $response = $this->client->request('get', '/1/categories');

        $data = json_decode($response->getBody(), true);

        $categories = [];
        foreach ($data['categories'] as $category) {
            $categories[] = $this->entityManager->getEntity('Category', $category);
        }

        return $categories;
    }

    /**
     * @param \Quartet\BaseApi\Entity\Category $category
     * @return \Quartet\BaseApi\Entity\Category[]
     * @throws MissingRequiredParameterException
     */
    public function add(Category $category)
    {
        if (is_null($category->name)) {
            throw new MissingRequiredParameterException;
        }

        $params = $this->entityManager->getFlatArray($category);

        $response = $this->client->request('post', '/1/categories/add', $params);

        $data = json_decode($response->getBody(), true);

        $categories = [];
        foreach ($data['categories'] as $category) {
            $categories[] = $this->entityManager->getEntity('Category', $category);
        }

        return $categories;
    }

    /**
     * @param \Quartet\BaseApi\Entity\Category $category
     * @return \Quartet\BaseApi\Entity\Category[]
     * @throws MissingRequiredParameterException
     */
    public function edit(Category $category)
    {
        if (is_null($category->category_id)) {
            throw new MissingRequiredParameterException;
        }

        $params = $this->entityManager->getFlatArray($category);

        $response = $this->client->request('post', '/1/categories/edit', $params);

        $data = json_decode($response->getBody(), true);

        $categories = [];
        foreach ($data['categories'] as $category) {
            $categories[] = $this->entityManager->getEntity('Category', $category);
        }

        return $categories;
    }

    /**
     * @param int|string $category_id
     * @return \Quartet\BaseApi\Entity\Category[]
     */
    public function delete($category_id)
    {
        $response = $this->client->request('post', '/1/categories/delete', [
            'category_id' => $category_id,
        ]);

        $data = json_decode($response->getBody(), true);

        $categories = [];
        foreach ($data['categories'] as $category) {
            $categories[] = $this->entityManager->getEntity('Category', $category);
        }

        return $categories;
    }
}
