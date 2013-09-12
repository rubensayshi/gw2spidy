<?php
use GW2Spidy\Util\CurlRequest;
use GW2Spidy\TradingPostSpider;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemTypeQuery;
use GW2Spidy\DB\ItemSubTypeQuery;
use GW2Spidy\DB\ItemSubType;

ini_set('memory_limit', '1G');

require dirname(__FILE__) . '/../autoload.php';

function getIDFromMarketData ($marketData, $searchValue) {
    //Replace left value with right value
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

function getSubIDFromMarketData ($marketData, $searchValue, $itemSubTypeName) {
    $marketID = null; //Default to null if it doesn't exist.

    foreach ($marketData as $marketValues) {
        if ($marketValues['name'] == $searchValue) {
            foreach ($marketValues as $subs) {
                if (isset($subs['name']) && $subs['name'] == $itemSubTypeName) {
                    $marketID = $subs['id'];
                    break;
                }
            }
        }
    }

    return $marketID;
}

$market_data = TradingPostSpider::getInstance()->getMarketData();

$curl = CurlRequest::newInstance(getAppConfig('gw2spidy.gw2api_url')."/v1/items.json") ->exec();
$data = json_decode($curl->getResponseBody(), true);
$multi_curl = EpiCurl::getInstance();
$item_curls = array();

$error_values = array();

$render_url = getAppConfig('gw2spidy.gw2render_url')."/file";

$number_of_items = count($data['items']);

$itemSubTypes = array();

$i = 0;
$ii = 0;

foreach (array_chunk($data['items'], 1000) as $items) {

    //Add all curl requests to the EpiCurl instance.
    foreach ($items as $item_id) {
        $i++;

        $ch = curl_init(getAppConfig('gw2spidy.gw2api_url')."/v1/item_details.json?item_id={$item_id}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $item_curls[$item_id] = $multi_curl->addCurl($ch);

        echo "[{$i} / {$number_of_items}]: {$item_id}\n";
    }

    foreach ($items as $item_id) {
        $ii++;

        try {
            echo "[{$ii} / {$number_of_items}]: ";

            $ch = $item_curls[$item_id];

            $API_item = json_decode($ch->data, true);
            if (!isset($API_item['name'])) throw new Exception("Item not found: $i");

            echo $API_item['name'] . "\n";

            $itemData = array(  'type_id'           => getIDFromMarketData($market_data['types'], $API_item['type']),
                                'data_id'           => $data['items'][$i],
                                'name'              => $API_item['name'],
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

            $itemType = ItemTypeQuery::create()->findPk($itemData['type_id']);

            if ($itemType !== null) {
                //Known item types with no subtypes.
                $noSubTypes = array("CraftingMaterial", "Trophy", "MiniPet", "Bag", "Back");

                if (!in_array($API_item['type'], $noSubTypes)) {
                    $itemTypeName = strtolower($API_item['type']);

                    //Workaround for upgradecomponents
                    if ($itemTypeName == 'upgradecomponent') {
                        $itemTypeName = 'upgrade_component';
                    }

                    $itemSubTypeName = $API_item[$itemTypeName]['type'];

                    //Replace left value with right value
                    $keys = array(
                        'Harpoon Gun'   => 'Harpoon',
                        'Aquatic Helm'  => 'HelmAquatic',
                        'Spear'         => 'Speargun',
                        'Short Bow'     => 'ShortBow',
                        'Gift Box'      => 'GiftBox'
                    );

                    if (in_array($itemSubTypeName, $keys)) {
                        $a = array_keys($keys, $itemSubTypeName);
                        $itemSubTypeName = $a[0];
                    }

                    $itemSubType = ItemSubTypeQuery::create()->findOneByTitle($itemSubTypeName);

                    if ($itemSubType !== null) {
                        $itemType->addSubType($itemSubType);
                        $item->setItemSubType($itemSubType);
                    }
                    else {
                        //All of the below types are known to not exist in the market data with an ID (by this name).
                        //Rune/Sigil/Utility/Gem/Booze/Halloween/LargeBundle/RentableContractNpc/ContractNPC/UnlimitedConsumable
                        //TwoHandedToy/AppearanceChange/Immediate/Unknown

                        $SubTypeID = getSubIDFromMarketData($market_data['types'], $API_item['type'], $itemSubTypeName);

                        //If the SubTypeID cannot be found in the market data, then just create it by adding one to the highest
                        //Subtype id within the current ItemType.
                        if ($SubTypeID === null) {
                            $itemSubTypes = ItemSubTypeQuery::create()
                                    ->filterByMainTypeId($itemData['type_id'])
                                    ->withColumn('MAX(id)', 'MAXid')
                                    ->find();

                            $SubTypeID = $itemSubTypes[0]->getMAXid() + 1;
                        }

                        $itemSubType = new ItemSubType();
                        $itemSubType->fromArray(array('Id' => $SubTypeID, 'MainTypeId' => $itemData['type_id'], 'Title' => $itemSubTypeName));
                        $itemSubType->save();

                        $itemType->addSubType($itemSubType);
                        $item->setItemSubType($itemSubType);
                    }
                }

                $item->setItemType($itemType);
            }

            $item->save();
        } catch (Exception $e) {
            $error_values[] = $data['items'][$i];

            echo "failed [[ {$e->getMessage()} ]] .. \n";
        }
    }
}

if (count($error_values) > 0)
    var_dump($error_values);

