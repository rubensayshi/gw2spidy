<?php

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

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

if (!isset($argv[1]) || !($mapfilename = $argv[1])) {
    die('map file required.');
}

if (!file_exists($mapfilename)) {
    die('map file does not exist.');
}

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
foreach ($data as $i => $row) {
    try {
        echo "[{$i} / {$cnt}] \n";

        if (RecipeQuery::create()->findByGw2dbId($row['ID'])->count() == 0) {
            $r = new Recipe();
            $r->setDataId($row['DataID']);
            $r->setGw2dbId($row['ID']);
            $r->setGw2dbExternalId($row['ExternalID']);
            $r->setName($row['Name']);
            $r->setRating($row['Rating']);
            $r->setCount($row['Count']);
            $r->setDisciplineId($row['Type']);

            $result = ItemQuery::create()->findByGw2dbId($row['CreatedItemId']);
            if (!$result->count()) {
                // throw new NoResultItemException();
            } else {
                $result = $result[0];
                $r->setResultItem($result);
            }

            foreach ($row['Ingredients'] as $ingrow) {
                $ri = new RecipeIngredient();

                $ri->setRecipe($r);

                $item = ItemQuery::create()->findByGw2dbId($ingrow['ItemID']);
                if (!$item->count()) {
                    throw new NoIngredientItemException();
                } else {
                    $item = $item[0];
                    $ri->setItem($item);
                    $ri->setCount($ingrow['Count']);

                    $ri->save();
                }
            }

            $r->save();
        }
    } catch (FailedImportException $e) {
        $failed[] = $row;
        echo "failed .. \n";
    }

    if ($max && $i >= $max) {
        break;
    }
}

var_dump($failed);


