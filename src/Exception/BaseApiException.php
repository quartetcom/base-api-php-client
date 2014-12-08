<?php
namespace Quartet\BaseApi\Exception;

class BaseApiException extends RuntimeException
{
    public function __construct(array $body, $code = 400)
    {
        parent::__construct();

        $this->message = "[{$body['error']}] {$body['error_description']}";
        $this->code = $code;
    }
}
