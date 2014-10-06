<?php
use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\ItemSubType;
use GW2Spidy\DB\ItemSubTypeQuery;
use GW2Spidy\DB\ItemType;
use GW2Spidy\DB\ItemTypeQuery;
use GW2Spidy\GW2API\APIItemV2;
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

/**
 * @param $API_JSON
 * @param $offset
 */
function processApiData($API_JSON, $offset)
{

    $APIItems = APIItemV2::getMultipleItemsByJSON($API_JSON);

    try {
        $itemCount = $offset;

        foreach ($APIItems as $APIItem) {

            $itemCount++;

            if ($APIItem == null) {
                print "Skipped item {$offset}.\n";
                continue;
            }

            echo "{$itemCount}: {$APIItem->getName()} (ID: {$APIItem->getItemId()})\n";

            $itemData = array('TypeId' => getOrCreateTypeID($APIItem->getMarketType()),
                'DataId' => $APIItem->getItemId(),
                'Name' => $APIItem->getName(),
                'RestrictionLevel' => $APIItem->getLevel(),
                'Rarity' => getRarityID($APIItem->getRarity()),
                'VendorSellPrice' => $APIItem->getVendorValue(),
                'Img' => $APIItem->getImageURL(),
                'RarityWord' => $APIItem->getRarity(),
                'UnsellableFlag' => $APIItem->isUnsellable());

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
                        $itemSubType->fromArray(array('Id' => $SubTypeID,
                            'MainTypeId' => $itemData['TypeId'],
                            'Title' => $APIItem->getDBSubType()));
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
        }
    } catch (Exception $e) {
        echo "failed [[ {$e->getMessage()} ]] .. \n";
    }
}

Propel::disableInstancePooling();

$pageSize = 200;

$curl = CurlRequest::newInstance(getAppConfig('gw2spidy.gw2api_url')."/v2/items?page=0&page_size={$pageSize}") ->exec();
if($curl->getInfo("http_code") != 200) {
    print "Failed curl request. Returned Status was {$curl->getInfo("http_code")}.\n";
    print "Returned body: {$curl->getResponseBody()}\n";
    exit(1);
}
processApiData($curl->getResponseBody(), 0);
$numberOfPages = intval($curl->getResponseHeaders("X-Page-Total"));

$error_values = array();
$multi_curl = EpiCurl::getInstance();
$item_curls = array();
$itemSubTypes = array();

//Add all curl requests to the EpiCurl instance.
for($page=1; $page < $numberOfPages; $page++) {
    echo "REQUESTING [{$page} / {$numberOfPages}]\n";
    $ch = curl_init(getAppConfig('gw2spidy.gw2api_url')."/v2/items?page={$page}&page_size={$pageSize}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $item_curls[$page] = $multi_curl->addCurl($ch);
}

for ($page = 1; $page < $numberOfPages; $page++){
    echo "PROCESSING [{$page} / {$numberOfPages}]:\n";
    $API_JSON = $item_curls[$page]->data;
    if($item_curls[$page]->code != 200) {
        print "Failed curl request. Returned Status was $item_curls[$page]->code}.\n";
        print "Returned body: {$API_JSON}\n";
        exit(1);
    }
    processApiData($API_JSON, $page * $pageSize);
}

if (count($error_values) > 0)
    var_dump($error_values);

Propel::enableInstancePooling();

exit(0);