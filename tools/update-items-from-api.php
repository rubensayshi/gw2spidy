<?php
use GW2Spidy\Util\CurlRequest;
use GW2Spidy\TradingPostSpider;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemTypeQuery;

ini_set('memory_limit', '1G');

require dirname(__FILE__) . '/../autoload.php';

function getIDFromMarketData ($marketData, $searchValue) {
    $keys = array(
        'Crafting Material'     => 'CraftingMaterial',
        'Crafting Component'    => 'CraftingComponent',
        'Upgrade Component'     => 'UpgradeComponent',
        'Mini'                  => 'MiniPet'
    );
    
    if (in_array($searchValue, $keys)) {
        $a = array_keys($keys, $searchValue);
        $searchValue = $a[0];
    }
    
    $marketID = 0;
    
    foreach ($marketData as $marketValues) {
        if ($marketValues['name'] == $searchValue) {
            $marketID = $marketValues['id'];
            break;
        }
    }
    
    return $marketID;
}

$market_data = TradingPostSpider::getInstance()->getMarketData();

$curl = CurlRequest::newInstance(getAppConfig('gw2spidy.gw2api_url')."/v1/items.json") ->exec();
$data = json_decode($curl->getResponseBody(), true);

$error_values = array();

$render_url = getAppConfig('gw2spidy.gw2render_url')."/file";

$number_of_items = count($data['items']) - 1;
$item_start = (isset($argv[1]) && $argv[1] >= 1)                ? $argv[1] - 1 : 0;    //Default the start at zero.
$item_end   = (isset($argv[2]) && $argv[2] <= $number_of_items) ? $argv[2] - 1 : $number_of_items - 1; //Max items default

for ($i = $item_start; $i <= $item_end; $i++) {
    try {
        echo "[{$i} / {$item_end}]: ";
        
        $curl_item = CurlRequest::newInstance(getAppConfig('gw2spidy.gw2api_url')."/v1/item_details.json?item_id={$data['items'][$i]}")->exec();
        $API_item = json_decode($curl_item->getResponseBody(), true);
        
        echo $API_item['name'] . "\n";
        
        $itemData = array(  'type_id'           => getIDFromMarketData($market_data['types'], $API_item['type']),
                            'data_id'           => $data['items'][$i],
                            'name'              => $API_item['name'],
                            //'gem_store_description'       => $item['description'],
                            'restriction_level' => $API_item['level'],
                            'rarity'            => getIDFromMarketData($market_data['rarities'], $API_item['rarity']),
                            'vendor_sell_price' => $API_item['vendor_value'],
                            'img'               => "{$render_url}/{$API_item['icon_file_signature']}/{$API_item['icon_file_id']}.png",
                            'rarity_word'       => $API_item['rarity']);
        
        $item = ItemQuery::create()->findPK($data['items'][$i]);
        
        if ($item === null) {
            $item = new Item();
        }        
        
        $item->fromArray($itemData, \BasePeer::TYPE_FIELDNAME);
        /*
        $itemType = ItemTypeQuery::create()->findPk($itemData['type_id']);
        
        if ($itemType !== null) {
            if (isset($API_item[strtolower($API_item['type'])])) {
                print_r($API_item[strtolower($API_item['type'])]) . "\n";
            }
            else {
                print_r($API_item);
            }
        }
        */
        $item->save();
    } catch (Exception $e) {
        $error_values[] = $data['items'][$i];
        
        echo "failed [[ {$e->getMessage()} ]] .. \n";
    }
}

if (count($error_values) > 0) 
    var_dump($error_values);