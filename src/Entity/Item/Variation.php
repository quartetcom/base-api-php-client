<?php
namespace Quartet\BaseApi\Entity\Item;

use Quartet\BaseApi\Entity\EntityInterface;

class Variation implements EntityInterface
{
    public $variation_id;
    public $variation;
    public $variation_stock;
    public $variation_identifier;
}
