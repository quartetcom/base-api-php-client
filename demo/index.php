<?php
/**
 * Place this file on the path which is accessible with your REDIRECT URI.
 */

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

// Items api.
$itemsApi = new Items($client);
$items = $itemsApi->get();
var_dump($items[0]);

// object(Quartet\BaseApi\Entity\Item)[49]
//   public 'item_id' => int 842194
//   public 'title' => string 'Tシャツ' (length=10)
//   public 'detail' => string '' (length=0)
//   public 'price' => int 2000
//   public 'stock' => int 8
//   public 'visible' => int 1
//   public 'list_order' => int 1
//   public 'identifier' => null
//   public 'img1_origin' => null
//   public 'img1_76' => null
//   public 'img1_146' => null
//   public 'img1_300' => null
//   public 'img1_500' => null
//   public 'img1_640' => null
//   public 'img1_sp_480' => null
//   public 'img1_sp_640' => null
//   public 'img2_origin' => null
//   public 'img2_76' => null
//   public 'img2_146' => null
//   public 'img2_300' => null
//   public 'img2_500' => null
//   public 'img2_640' => null
//   public 'img2_sp_480' => null
//   public 'img2_sp_640' => null
//   public 'img3_origin' => null
//   public 'img3_76' => null
//   public 'img3_146' => null
//   public 'img3_300' => null
//   public 'img3_500' => null
//   public 'img3_640' => null
//   public 'img3_sp_480' => null
//   public 'img3_sp_640' => null
//   public 'img4_origin' => null
//   public 'img4_76' => null
//   public 'img4_146' => null
//   public 'img4_300' => null
//   public 'img4_500' => null
//   public 'img4_640' => null
//   public 'img4_sp_480' => null
//   public 'img4_sp_640' => null
//   public 'img5_origin' => null
//   public 'img5_76' => null
//   public 'img5_146' => null
//   public 'img5_300' => null
//   public 'img5_500' => null
//   public 'img5_640' => null
//   public 'img5_sp_480' => null
//   public 'img5_sp_640' => null
//   public 'modified' => int 1418616522
//   public 'variations' =>
//     array (size=2)
//       0 =>
//         object(Quartet\BaseApi\Entity\Subset\Variation)[47]
//           public 'variation_id' => int 1748328
//           public 'variation' => string 'サイズS' (length=10)
//           public 'variation_stock' => int 5
//           public 'variation_identifier' => null
//       1 =>
//         object(Quartet\BaseApi\Entity\Subset\Variation)[45]
//           public 'variation_id' => int 1748329
//           public 'variation' => string 'サイズM' (length=10)
//           public 'variation_stock' => int 3
//           public 'variation_identifier' => null
//   public 'shop_id' => null
//   public 'shop_name' => null
//   public 'shop_url' => null
//   public 'categories' => null
