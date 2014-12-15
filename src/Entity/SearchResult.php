<?php
namespace Quartet\BaseApi\Entity;

class SearchResult implements EntityInterface
{
    public $found;
    public $start;

    /**
     * @var Item[]
     */
    public $items;
}
