<?php
namespace Quartet\BaseApi\Entity\Subset;

use Quartet\BaseApi\Entity\EntityInterface;

class ContactInformation implements EntityInterface
{
    public $first_name;
    public $last_name;
    public $zip_code;
    public $prefecture;
    public $address;
    public $address2;
    public $mail_address;
    public $tel;
}
