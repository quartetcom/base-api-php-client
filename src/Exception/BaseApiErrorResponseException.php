<?php
namespace Quartet\BaseApi\Exception;

class BaseApiErrorResponseException extends RuntimeException
{
    public function __construct(array $body, $code = 400)
    {
        parent::__construct("[{$body['error']}] {$body['error_description']}", $code);
    }
}
