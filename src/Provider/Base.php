<?php
namespace Quartet\BaseApi\Provider;

use League\OAuth2\Client\Provider\GenericProvider;

class Base extends GenericProvider
{
    const BASE_URL = 'https://api.thebase.in';

    public function __construct(array $options = [])
    {
        parent::__construct(array_merge($options, [
            'scopeSeparator' => ' ',
            'urlAuthorize' => self::BASE_URL . '/1/oauth/authorize',
            'urlAccessToken' => self::BASE_URL . '/1/oauth/token',
            'urlResourceOwnerDetails' => self::BASE_URL . '/1/users/me',
        ]));
    }
}
