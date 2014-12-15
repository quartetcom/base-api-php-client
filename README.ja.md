# base-api-php-client

[![Build Status](https://travis-ci.org/quartetcom/base-api-php-client.svg?branch=master)](https://travis-ci.org/quartetcom/base-api-php-client)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/quartetcom/base-api-php-client/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/quartetcom/base-api-php-client/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/quartetcom/base-api-php-client/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/quartetcom/base-api-php-client/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/quartetcom/base-api-php-client/v/stable.svg)](https://packagist.org/packages/quartetcom/base-api-php-client)
[![Total Downloads](https://poser.pugx.org/quartetcom/base-api-php-client/downloads.svg)](https://packagist.org/packages/quartetcom/base-api-php-client)

[BASE API](https://developers.thebase.in/) を PHP で利用するためのクライアントライブラリです。

## 要件

* PHP 5.4+

## 導入

`composer.json` に以下のいずれかの方法で依存を追加してください。

```json
{
    "require": {
        "quartetcom/base-api-php-client": "1.0.*@dev",
        "cakephp/utility": "3.0.*@beta"
    }
}
```

または

```json
{
    "require": {
        "quartetcom/base-api-php-client": "1.0.*@dev"
    },
    "minimum-stability": "beta"
}
```

## 使用方法

```php
$clientId     = '2aacd57f14ffe6edafd402934593a0ce';
$clientSecret = '2e3389dc5fe7c9607115541e409dd2c3';
$callbackUrl  = 'http://hogehoge.com/callback';

$scopes = [
    'read_users',
    'read_items',
];

// BASE API クライアントオブジェクトを初期化.
$client = new Quartet\BaseApi\Client($clientId, $clientSecret, $callbackUrl, $scopes);

// OAuth 認証.
if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);   // クエリパラメータに認可コードがセットされていたらそのコードで認証.
} else {
    $client->authorize();                   // そうでなければ BASE の認証画面へリダイレクト.
}

// 各種 API は、API クラス群経由で使用します.
$usersApi = new Quartet\BaseApi\Api\Users($client);
$user = $usersApi->me();

// API からのレスポンスはオブジェクトの形で取得されます.
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

[こちら](demo/index.php) にデモコードもありますので、ご参照ください。

## 関連リンク

* [BASE Developers](https://developers.thebase.in/)
* [BASE API v1 ドキュメント (β版)](https://gist.github.com/baseinc/9634675)
