<?php
namespace Quartet\BaseApi\Exception;

class MissingRequiredParameterException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Some required parameters are missing.');
    }
}
