<?php
/**
 * This script can update the karma value of the gw2db_item_archive table.
 * Only necessary when upgrading an old table.
 */

use GW2Spidy\Util\CacheHandler;

ini_set('memory_limit', '1G');

require dirname(__FILE__) . '/../autoload.php';

if (!isset($argv[1]) || !($mapfilename = $argv[1])) {
    die('map file required.');
}

if (!file_exists($mapfilename)) {
    die('map file does not exist.');
}

// ensure purged cache, otherwise everything goes to hell
CacheHandler::getInstance("purge")->purge();

$data = json_decode(file_get_contents($mapfilename), true);
$cnt  = count($data);

$stmt = Propel::getConnection()->prepare("UPDATE gw2db_item_archive 
                                             SET karma_price = :karma_price
                                           WHERE DataId = :data_id");
                                           
$bulk = Propel::getConnection()->prepare(
"UPDATE gw2db_item_archive LEFT JOIN item
     ON gw2db_item_archive.DataID = item.data_id
    SET gw2db_item_archive.karma_price = :karma_price, 
        item.karma_price = :karma_price
  WHERE gw2db_item_archive.Name IN (:name,:names);");
  
// fix for some words where the plural is irregular
$singular = array(
    "Tomatoe"   => "Tomato",
    "Cherrie"   => "Cherry",
    "Peache"    => "Peach",
    "Buttermilk"=> "Glass of Buttermilk"
);

foreach ($data as $i => $row) {
      
    if (strpos($row['Name'], "Recipe: ") !== false) {
        continue;
    }
    if($i % 100 == 0)
        echo "[{$i} / {$cnt}]: {$row['Name']} \n";
	
    if (isset($row['SoldBy']) && isset($row['DataID'])) {
        $lowestKarma = 0;
        foreach($row['SoldBy'] as $r) {    
            if (isset($r['KarmaCost']) && ($r['KarmaCost'] < $lowestKarma || $lowestKarma == 0)) {
                $lowestKarma = $r['KarmaCost'];
            }
        }
        
        if($lowestKarma > 0) { 
            $stmt->bindValue('karma_price', $lowestKarma);
            $stmt->bindValue('data_id', $row['DataID']);
            $stmt->execute();
            
            if(preg_match("/^(.+?)s? in Bulk$/", $row['Name'], $match)) {
                $name = $match[1];
                if(array_key_exists($name, $singular)) $name = $singular[$name];
                echo "[{$i} / {$cnt}]: {$name} with {$lowestKarma} Karma: ";
                $bulk->bindValue('name', $name);
                $bulk->bindValue('names', $name.'[s]');
                $bulk->bindValue('karma_price', ceil($lowestKarma / 25));
                $bulk->execute();
                echo "{$bulk->rowCount()} updated.\n";
            }
        }
    }
}


// ensure purged cache, otherwise everything goes to hell
CacheHandler::getInstance("purge")->purge();

