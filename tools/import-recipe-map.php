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

require dirname(__FILE__) . '/../autoload.php';

Propel::disableInstancePooling();

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
        $r->setName($row['Name']);
        $r->setRating($row['Rating']);
        $r->setCount($row['Count']);
        $r->setDisciplineId($row['Type']);
        $r->setRequiresUnlock(isset($row['RequiresRecipeItem']) && $row['RequiresRecipeItem'] !== false);

        if (!($result = ItemQuery::create()->findOneByDataId($row['CreatedItemId']))) {
            throw new NoResultItemException("no result [[ {$row['CreatedItemId']} ]]");
        } else {
            $r->setResultItem($result);
        }
            

        // grab old ingredients
        $oldRIs = $r->getIngredients();

        // loop over new ingredients
        foreach ($row['Ingredients'] as $ingrow) {
            // check if we know the item
            if (!($item = ItemQuery::create()->findOneByDataId($ingrow['ItemID']))) {
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

if (count($failed) > 0)
    var_dump($failed);

Propel::enableInstancePooling();