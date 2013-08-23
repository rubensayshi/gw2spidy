<?php
use GW2Spidy\Util\CurlRequest;
use GW2Spidy\TradingPostSpider;
use GW2Spidy\DB\ItemQuery;

ini_set('memory_limit', '1G');

require dirname(__FILE__) . '/../autoload.php';

$max = null; //Set the maximum number of items to retrieve
$item_count = 0; //Start the counter at zero.

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

$number_of_items = count($data['items']) - 1;
$error_values = array();

$render_url = getAppConfig('gw2spidy.gw2render_url')."/file";

foreach($data['items'] as $item_id) {
    try {
        echo "[{$item_count} / {$number_of_items}]: ";
        
        $curl_item = CurlRequest::newInstance(getAppConfig('gw2spidy.gw2api_url')."/v1/item_details.json?item_id={$item_id}")->exec();
        $item = json_decode($curl_item->getResponseBody(), true);
        
        echo $item['name'] . "\n";
        
        $itemData = array(  'type_id'           => getIDFromMarketData($market_data['types'], $item['type']),
                            'data_id'           => $item_id,
                            'name'              => $item['name'],
                            //'gem_store_description'       => $item['description'],
                            'restriction_level' => $item['level'],
                            'rarity'            => getIDFromMarketData($market_data['rarities'], $item['rarity']),
                            'vendor_sell_price' => $item['vendor_value'],
                            'img'               => "{$render_url}/{$item['icon_file_signature']}/{$item['icon_file_id']}.png",
                            'rarity_word'       => $item['rarity']);
        
        $itemQuery = ItemQuery::create()->findPK($item_id);       
        $itemQuery->fromArray($itemData, \BasePeer::TYPE_FIELDNAME);
        $itemQuery->save();
    } catch (Exception $e) {
        $error_values[] = $item_id;
        
        echo "failed [[ {$e->getMessage()} ]] .. \n";
    }
    
    $item_count++;
    
    if ($max && $item_count >= $max) 
        break;
}

var_dump($error_values);