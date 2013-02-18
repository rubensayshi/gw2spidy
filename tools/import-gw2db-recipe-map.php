<?php

use GW2Spidy\NewQueue\ItemDBQueueWorker;

use GW2Spidy\NewQueue\ItemDBQueueItem;

use GW2Spidy\NewQueue\RequestSlotManager;

use GW2Spidy\Util\CacheHandler;

use GW2Spidy\DB\GW2DBItemArchive;

use GW2Spidy\DB\ItemTypeQuery;

use GW2Spidy\DB\ItemType;

use GW2Spidy\TradingPostSpider;

use GW2Spidy\DB\GW2DBItemArchiveQuery;

use GW2Spidy\DB\RecipeQuery;

use GW2Spidy\DB\RecipeIngredient;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\Recipe;
use GW2Spidy\DB\Discipline;
use GW2Spidy\DB\DisciplineQuery;

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
        8 => 'Cook',
    );

    foreach ($disciplines as $id => $name) {
        $d = new Discipline();
        $d->setId($id);
        $d->setName($name);
        $d->save();
    }
}

$data = json_decode(file_get_contents($mapfilename), true);
$cnt  = count($data);

/*
 {
 "ID":73902,
 "ExternalID":9137,
 "DataID":3083,
 "Name":"Pile[s] of Pumpkin Pie Spice",
 "Rating":225,
 "Type":8,
 "Count":1,
 "CreatedItemId":504736,
 "Ingredients":[{"ItemID":504475,"Count":1},{"ItemID":504751,"Count":1},{"ItemID":504466,"Count":1},{"ItemID":504545,"Count":1}]
 }
*/
$failed = array();
$max    = null;

$tp = TradingPostSpider::getInstance();
$slots = RequestSlotManager::getInstance();
$worker = new ItemDBQueueWorker(null);

$getItemByGW2DBID = function($gw2dbID) use ($tp, $slots, $worker) {
    $result = ItemQuery::create()->findOneByGw2dbId($gw2dbID);
    if (!$result) {
        if ($gw2dbItem = GW2DBItemArchiveQuery::create()->findOneById($gw2dbID)) {

            // claim a slot if posible, if not just continue, this has prio over the slots xD
            if ($slot = $slots->getAvailableSlot()) {
                $slot->hold();
            }

            $itemData = $tp->getItemById($gw2dbItem->getDataid());
            $itemData['name']              = $gw2dbItem->getName();
            $itemData['gw2db_id']          = $gw2dbItem->getId();
            $itemData['gw2db_external_id'] = $gw2dbItem->getExternalid();

            $result = $worker->storeItemData($itemData, null, null);
        }
    }

    return $result;
};

foreach ($data as $i => $row) {
    try {
        echo "[{$i} / {$cnt}]: {$row['Name']}\n";

        $q = RecipeQuery::create()->findByGw2dbExternalId($row['ExternalID']);

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

        if (!($result = $getItemByGW2DBID($row['CreatedItemId']))) {
            throw new NoResultItemException("no result [[ {$row['CreatedItemId']} ]]");
        } else {
            $r->setResultItem($result);
        }

        // grab old ingredients
        $oldRIs = $r->getIngredients();

        // loop over new ingredients
        foreach ($row['Ingredients'] as $ingrow) {
            // check if we know the item
            if (!($item = $getItemByGW2DBID($ingrow['ItemID']))) {
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

        // remove old ingredients that aren't in the import anymore
        foreach ($oldRIs as $oldRI) {
            if (!$oldRI->getOkOnImport()) {
                $oldRI->delete();
            }
        }

        $r->save();
    } catch (Exception $e) {
        $failed[] = $row;
        echo "failed [[ {$e->getMessage()} ]] .. \n";
    }

    if ($max && $i >= $max) {
        break;
    }
}

var_dump($failed);

// fix wintersday recipes
\Propel::getConnection()->exec("UPDATE recipe_ingredient SET count = 5 WHERE recipe_id IN(6761, 6762, 6737) AND item_id = 38143");
\Propel::getConnection()->exec("UPDATE recipe_ingredient SET count = 3 WHERE recipe_id IN(6736, 6759, 6760) AND item_id = 38142");

