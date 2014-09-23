<?php
use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\ItemSubType;
use GW2Spidy\DB\ItemSubTypeQuery;
use GW2Spidy\DB\ItemType;
use GW2Spidy\DB\ItemTypeQuery;
use GW2Spidy\GW2API\APIItem;
use GW2Spidy\Util\CurlRequest;

ini_set('memory_limit', '1G');

require dirname(__FILE__) . '/../autoload.php';

function getOrCreateTypeID($typeName){
    $itemType = ItemTypeQuery::create()
        ->findOneByTitle($typeName);

    if ($itemType==null){

        $maxItemTypes = ItemTypeQuery::create()
            ->withColumn('MAX(id)', 'MAXid')
            ->find();

        $itemType = new ItemType();
        $itemType->setId($maxItemTypes[0]->getMAXid() + 1);
        $itemType->setTitle($typeName);

        $itemType->save();
    }

    return $itemType->getId();
}

function getRarityID($rarityName){

    $rarities = array(
        "Junk"       => 0,
        "Common"     => 1,
        "Fine"       => 2,
        "Masterwork" => 3,
        "Rare"       => 4,
        "Exotic"     => 5,
        "Ascended"   => 6,
        "Legendary"  => 7,
        "Basic"      => 8,
    );
    return $rarities[$rarityName];
}

function getIDFromMarketData ($marketData, $searchValue) {
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

$curl = CurlRequest::newInstance(getAppConfig('gw2spidy.gw2api_url')."/v1/items.json") ->exec();
$data = json_decode($curl->getResponseBody(), true);
$multi_curl = EpiCurl::getInstance();
$item_curls = array();

$error_values = array();

$number_of_items = count($data['items']);

$itemSubTypes = array();

$i = 0;
$ii = 0;

foreach (array_chunk($data['items'], 1000) as $items) {

    Propel::disableInstancePooling();

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

            $API_JSON = $item_curls[$item_id]->data;
            $APIItem = APIItem::getItemByJSON($API_JSON);
            
            if ($APIItem === null) throw new Exception("Item not found: $item_id");

            echo $APIItem->getName() . "\n";

            $itemData = array(  'TypeId'            => getOrCreateTypeID($APIItem->getMarketType()),
                                'DataId'            => $APIItem->getItemId(),
                                'Name'              => $APIItem->getName(),
                                'RestrictionLevel'  => $APIItem->getLevel(),
                                'Rarity'            => getRarityID($APIItem->getRarity()),
                                'VendorSellPrice'   => $APIItem->getVendorValue(),
                                'Img'               => $APIItem->getImageURL(),
                                'RarityWord'        => $APIItem->getRarity(),
                                'UnsellableFlag'    => $APIItem->isUnsellable());
            
            $item = ItemQuery::create()->findPK($APIItem->getItemId());
            
            if ($item === null) {
                $item = new Item();
            }

            $item->fromArray($itemData);

            $itemType = ItemTypeQuery::create()->findPk($itemData['TypeId']);

            if ($itemType !== null) {
                if ($APIItem->getSubType() !== null) {

                    $itemSubType = ItemSubTypeQuery::create()->findOneByTitle($APIItem->getDBSubType());
                    
                    if ($itemSubType === null) {
                        //All of the below types are known to not exist in the market data with an ID (by this name).
                        //Rune/Sigil/Utility/Gem/Booze/Halloween/LargeBundle/RentableContractNpc/ContractNPC/UnlimitedConsumable
                        //TwoHandedToy/AppearanceChange/Immediate/Unknown

                        $itemSubTypes = ItemSubTypeQuery::create()
                                ->filterByMainTypeId($itemData['TypeId'])
                                ->withColumn('MAX(id)', 'MAXid')
                                ->find();

                        $SubTypeID = $itemSubTypes[0]->getMAXid() + 1;

                        $itemSubType = new ItemSubType();
                        $itemSubType->fromArray(array(  'Id'            => $SubTypeID, 
                                                        'MainTypeId'    => $itemData['TypeId'], 
                                                        'Title'         => $APIItem->getDBSubType()));
                        $itemSubType->save();

                        $itemType->addSubType($itemSubType);
                        $item->setItemSubType($itemSubType);
                    }
                    
                    $itemType->addSubType($itemSubType);
                    $item->setItemSubType($itemSubType);
                }

                $item->setItemType($itemType);
            }

            $item->save();
        } catch (Exception $e) {
            $error_values[] = $item_id;

            echo "failed [[ {$e->getMessage()} ]] .. \n";
        }
    }
}

if (count($error_values) > 0)
    var_dump($error_values);