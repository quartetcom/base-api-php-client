<?php
namespace Quartet\BaseApi\Entity\Subset;

use Quartet\BaseApi\Entity\EntityInterface;

class BtPaymentTransaction implements EntityInterface
{
    public $collected_fee;
    public $status;
}
