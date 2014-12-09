<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

use Quartet\BaseApi\Api\Items;
use Quartet\BaseApi\Api\Users;
use Quartet\BaseApi\Client;

$scopes = [
    'read_users',
    'read_items',
];

$client = new Client(CLIENT_ID, CLIENT_SECRET, REDIRECT_URI, $scopes);

if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
} else {
    $client->authorize();
}

// Users api.
$usersApi = new Users($client);
$user = $usersApi->me();
var_dump($user->shop_name);

// Items api.
$itemsApi = new Items($client);
$items = $itemsApi->get();
foreach ($items as $item) {
    $detail = $itemsApi->detail($item->item_id);
    var_dump($detail);
}
