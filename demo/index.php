<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

use Quartet\BaseApi\Client;

$client = new Client(CLIENT_ID, CLIENT_SECRET, REDIRECT_URI);

if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
} else {
    $client->authorize();
}

// just a sample.
$response = $client->request('get', '/1/users/me', ['scopes' => ['read_users', 'read_users_mail']]);
$me = json_decode($response->getBody(), true);
var_dump($me);
