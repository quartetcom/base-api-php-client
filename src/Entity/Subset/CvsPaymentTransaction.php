<?php
namespace Quartet\BaseApi\Entity\Subset;

use Quartet\BaseApi\Entity\EntityInterface;

class CvsPaymentTransaction implements EntityInterface
{
    public $collected_fee;
    public $status;
}
