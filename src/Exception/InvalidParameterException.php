<?php
namespace Quartet\BaseApi\Exception;

class InvalidParameterException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Some parameters have invalid values.');
    }
}
