<?php
namespace Quartet\BaseApi\Provider;

use League\OAuth2\Client\Entity\User;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;

class Base extends AbstractProvider
{
    const BASE_URL = 'https://api.thebase.in';

    public function __construct(array $options = [])
    {
        parent::__construct(array_merge($options, [
            'scopeSeparator' => ' ',
        ]));
    }

    public function urlAuthorize()
    {
        return self::BASE_URL . '/1/oauth/authorize';
    }

    public function urlAccessToken()
    {
        return self::BASE_URL . '/1/oauth/token';
    }

    public function urlUserDetails(AccessToken $token)
    {
        return self::BASE_URL . '/1/users/me?access_token=' . $token;
    }

    public function userDetails($response, AccessToken $token)
    {
        $user = new User();
        $user->exchangeArray([
            'uid' => $response->user->shop_id,
            'name' => $response->user->shop_name,
            'email' => $response->user->mail_address,
            'description' => $response->user->shop_introduction,
            'urls' => [
                'thebase' => $response->user->shop_url,
            ],
        ]);

        return $user;
    }

    public function userUid($response, AccessToken $token)
    {
        return $response->user->shop_id;
    }

    public function userEmail($response, AccessToken $token)
    {
        return $response->user->email;
    }

    public function userScreenName($response, AccessToken $token)
    {
        return $response->user->name;
    }
}
