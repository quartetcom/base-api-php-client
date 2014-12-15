<?php
namespace Quartet\BaseApi\Entity;

class Saving implements EntityInterface
{
    public $saving_id;
    public $bank_name;
    public $branch_name;
    public $account_type;
    public $account_name;
    public $account_number;
    public $drawings;
    public $due_date;
    public $status;
    public $created;
}
