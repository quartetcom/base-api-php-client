<?php
namespace Quartet\BaseApi\Entity\Subset;

use Quartet\BaseApi\Entity\EntityInterface;

class OrderItem implements EntityInterface
{
    public $order_item_id;
    public $item_id;
    public $title;
    public $variation;
    public $price;
    public $amount;
    public $total;
    public $status;
    public $modified;
}
