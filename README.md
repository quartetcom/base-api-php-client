# base-api-php-client

[![Build Status](https://travis-ci.org/quartetcom/base-api-php-client.svg?branch=master)](https://travis-ci.org/quartetcom/base-api-php-client)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/quartetcom/base-api-php-client/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/quartetcom/base-api-php-client/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/quartetcom/base-api-php-client/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/quartetcom/base-api-php-client/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/quartetcom/base-api-php-client/v/stable.svg)](https://packagist.org/packages/quartetcom/base-api-php-client)
[![Total Downloads](https://poser.pugx.org/quartetcom/base-api-php-client/downloads.svg)](https://packagist.org/packages/quartetcom/base-api-php-client)

[日本語はこちら](README.ja.md)

PHP client library for accessing [BASE API](https://developers.thebase.in/).

## Requirements

* PHP 5.5+

## Getting started

Just add this dependency into your `composer.json` as below:

```json
{
    "require": {
        "quartetcom/base-api-php-client": "1.0.*@dev",
        "cakephp/utility": "3.0.*@beta"
    }
}
```

or:

```json
{
    "require": {
        "quartetcom/base-api-php-client": "1.0.*@dev"
    },
    "minimum-stability": "beta"
}
```

## Usage

```php
$clientId     = '2aacd57f14ffe6edafd402934593a0ce';
$clientSecret = '2e3389dc5fe7c9607115541e409dd2c3';
$callbackUrl  = 'http://hogehoge.com/callback';

$scopes = [
    'read_users',
    'read_items',
];

// Initialize BASE API client object.
$client = new Quartet\BaseApi\Client($clientId, $clientSecret, $callbackUrl, $scopes);

// OAuth.
if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);   // authenticate with query string of 'code'.
} else {
    $client->authorize();                   // redirect to BASE authorization page and get code.
}

// Call APIs via API Classes.
$usersApi = new Quartet\BaseApi\Api\Users($client);
$user = $usersApi->me();

// You got response from API as an object.
var_dump($user);

// object(Quartet\BaseApi\Entity\User)[30]
//   public 'shop_id' => string 'sample_shop' (length=11)
//   public 'shop_name' => string 'sample shop name' (length=16)
//   public 'shop_introduction' => string '' (length=0)
//   public 'shop_url' => string 'http://sample_shop.thebase.in' (length=29)
//   public 'twitter_id' => string '' (length=0)
//   public 'facebook_id' => string '' (length=0)
//   public 'ameba_id' => string '' (length=0)
//   public 'instagram_id' => string '' (length=0)
//   public 'background' => null
//   public 'logo' => null
//   public 'mail_address' => null
```

You can see demo code [here](demo/index.php).

## See also

* [BASE Developers](https://developers.thebase.in/)
* [BASE API v1 ドキュメント (β版)](https://gist.github.com/baseinc/9634675)
