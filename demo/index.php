<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

use Quartet\BaseApi\Api\Users;
use Quartet\BaseApi\Client;

$client = new Client(CLIENT_ID, CLIENT_SECRET, REDIRECT_URI, ['read_users']);

if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
} elseif (isset($_GET['error']) && isset($_GET['error_description'])) {
    echo "[{$_GET['error']}] {$_GET['error_description']}";
    exit;
} else {
    $client->authorize();
}

$usersApi = new Users($client);
$user = $usersApi->me();
var_dump($user->shop_name);
