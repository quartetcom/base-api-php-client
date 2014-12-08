<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

use Quartet\BaseApi\Api\Users;
use Quartet\BaseApi\Client;

$client = new Client(CLIENT_ID, CLIENT_SECRET, REDIRECT_URI);

if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
} else {
    $client->authorize();
}

// Users api.
$users = new Users($client);
$user = $users->me();
var_dump($user->shop_id);
