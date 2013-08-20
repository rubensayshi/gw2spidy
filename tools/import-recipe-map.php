<?php

use GW2Spidy\Util\CacheHandler;
use GW2Spidy\Util\CurlRequest;

use GW2Spidy\DB\RecipeQuery;
use GW2Spidy\DB\RecipeIngredient;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\Recipe;
use GW2Spidy\DB\Discipline;
use GW2Spidy\DB\DisciplineQuery;
use GW2Spidy\DB\GW2DBItemArchiveQuery;
use GW2Spidy\NewQueue\RequestSlotManager;
use GW2Spidy\NewQueue\ItemDBQueueWorker;
use GW2Spidy\TradingPostSpider;

ini_set('memory_limit', '1G');

class FailedImportException extends Exception {}
class NoResultItemException extends FailedImportException {}
class NoIngredientItemException extends FailedImportException {}

require dirname(__FILE__) . '/../autoload.php';

if (!isset($argv[1]) || !($mapfilename = $argv[1])) {
    die('map file required.');
}

if (!file_exists($mapfilename)) {
    die('map file does not exist.');
}

// ensure purged cache, otherwise everything goes to hell
CacheHandler::getInstance("purge")->purge();

if (DisciplineQuery::create()->count() == 0) {
    $disciplines = array(
        1 => 'Huntsman',
        2 => 'Artificer',
        3 => 'Weaponsmith',
        4 => 'Armorsmith',
        5 => 'Leatherworker',
        6 => 'Tailor',
        7 => 'Jeweler',
        8 => 'Cook'
    );

    foreach ($disciplines as $id => $name) {
        $d = new Discipline();
        $d->setId($id);
        $d->setName($name);
        $d->save();
    }
}

$data = json_decode(file_get_contents($mapfilename), true);
$cnt  = count($data) - 1;

$failed = array();
$max    = null;

$tp = TradingPostSpider::getInstance();
$slots = RequestSlotManager::getInstance();
$worker = new ItemDBQueueWorker(null);
$market_data = $tp->getMarketData();

$getItemByDataID = function($DataID) use ($tp, $slots, $worker, $market_data) {
    $result = ItemQuery::create()->findOneByDataId($DataID);
    
    //If the item wasn't found in the database, try to create it from the trading post.
    if (!$result) {
        // claim a slot if possible, if not just continue, this has priority over the slots
        if (($slot = $slots->getAvailableSlot())) {
            $slot->hold();
        }
        
        try {
            $itemData = $tp->getItemById($DataID);
        } catch (Exception $e) {
            echo "Trading Post failed. [[ {$e->getMessage()} ]] .. \n";
            echo "Trying GW2 API... \n";
            
            try {
                $curl_item = CurlRequest::newInstance(getAppConfig('gw2spidy.gw2api_url')."/v1/item_details.json?item_id={$DataID}")->exec();
                $item = json_decode($curl_item->getResponseBody(), true);
                
                $getIDFromMarketData = function ($marketData, $searchValue) {
                    $marketID = 0;

                    foreach ($marketData as $marketValues) {
                        if ($marketValues['name'] == $searchValue) {
                            $marketID = $marketValues['id'];
                            break;
                        }
                    }

                    return $marketID;
                };

                $itemData = array(  'type_id'       => $getIDFromMarketData($market_data['types'], $item['type']),
                                    'data_id'       => $item['item_id'],
                                    'name'          => $item['name'],
                                    'description'   => $item['description'],
                                    'level'         => $item['level'],
                                    'rarity'        => $getIDFromMarketData($market_data['rarities'], $item['rarity']),
                                    'vendor'        => $item['vendor_value'],
                                    'img'           => "https://render.guildwars2.com/file/{$item['icon_file_signature']}/{$item['icon_file_id']}.png",
                                    'rarity_word'   => $item['rarity']);
            } catch (Exception $e) {
                echo "Complete failure to create item with API [[ {$e->getMessage()} ]] .. \n";
                return false;
            }
        }
        
        if (($gw2dbItem = GW2DBItemArchiveQuery::create()->findOneByDataid($DataID))) {
            $itemData['gw2db_id']          = $gw2dbItem->getId();
            $itemData['gw2db_external_id'] = $gw2dbItem->getExternalid();
        }
        
        $result = $worker->storeItemData($itemData);
    }
    
    return $result;
};

foreach ($data as $i => $row) {
    try {
        echo "[{$i} / {$cnt}]: {$row['Name']}\n";

        $q = RecipeQuery::create()->findByDataId($row['DataID']);

        if ($q->count() == 0) {
            $r = new Recipe();
        } else {
            $r = $q[0];
        }

        $r->setDataId($row['DataID']);
        $r->setGw2dbId($row['ID']);
        $r->setGw2dbExternalId($row['ExternalID']);
        $r->setName($row['Name']);
        $r->setRating($row['Rating']);
        $r->setCount($row['Count']);
        $r->setDisciplineId($row['Type']);
        $r->setRequiresUnlock(isset($row['RequiresRecipeItem']) && $row['RequiresRecipeItem'] !== false);

        if (!($result = $getItemByDataID($row['CreatedItemId']))) {
            throw new NoResultItemException("no result [[ {$row['CreatedItemId']} ]]");
        } else {
            $r->setResultItem($result);
        }
            

        // grab old ingredients
        $oldRIs = $r->getIngredients();

        // loop over new ingredients
        foreach ($row['Ingredients'] as $ingrow) {
            // check if we know the item
            if (!($item = $getItemByDataID($ingrow['ItemID']))) {
                throw new NoIngredientItemException("no ingredient [[ {$ingrow['ItemID']} ]]");
            } else {
                // see if we can match a previously imported ingredient for this recipe
                $foundOld = false;
                foreach ($oldRIs as $oldRI) {
                    if ($oldRI->getItemId() == $item->getDataId()) {
                        // mark the recipe
                        $oldRI->setOkOnImport();
                        $foundOld = true;

                        // update the count if it changed
                        if ($oldRI->getCount() != $ingrow['Count']) {
                            $oldRI->setCount($ingrow['Count']);
                            $oldRI->save();
                        }
                    }
                }

                // only create a new recipe if we haven't found it
                if (!$foundOld) {
                    $ri = new RecipeIngredient();

                    $ri->setItem($item);
                    $ri->setCount($ingrow['Count']);

                    $ri->setRecipe($r);
                    $ri->save();

                    // mark the recipe
                    $ri->setOkOnImport();
                }
            }
        }

        // remove old ingredients that aren't in the import any more
        foreach ($oldRIs as $oldRI) {
            if (!$oldRI->getOkOnImport()) {
                $oldRI->delete();
            }
        }

        $r->save();
    } catch (Exception $e) {
        $failed[] = $row;
        echo "failed [[ {$e->getMessage()} ]] .. \n";
        print_r($e->getTrace());
    }
    
    if ($max && $i >= $max) {
        break;
    }
}

var_dump($failed);