<?php
namespace Quartet\BaseApi\Api;

use Quartet\BaseApi\EntityFactory;

class AbstractApi
{
    protected $entityFactory;

    public function __construct()
    {
        $this->entityFactory = new EntityFactory;
    }
}
