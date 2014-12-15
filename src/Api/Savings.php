<?php
namespace Quartet\BaseApi\Api;

use Quartet\BaseApi\Exception\InvalidParameterException;

class Savings extends Api
{
    /**
     * @param string|null $start_created
     * @param string|null $end_created
     * @param int $limit
     * @param int $offset
     * @return \Quartet\BaseApi\Entity\Saving[]
     * @throws InvalidParameterException
     */
    public function get($start_created = null, $end_created = null, $limit = 20, $offset = 0)
    {
        if (intval($limit) > 100) {
            throw new InvalidParameterException;
        }

        $params = array_filter(compact('start_created', 'end_created', 'limit', 'offset'));

        $data = $this->client->request('get', '/1/savings', $params);

        $savings = [];
        foreach ($data['savings'] as $savingsArray) {
            $savings[] = $this->entityManager->getEntity('Saving', $savingsArray);
        }

        return $savings;
    }
}
