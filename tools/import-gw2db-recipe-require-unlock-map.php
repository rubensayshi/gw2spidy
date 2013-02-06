<?php
/**
 * This script can update the recipe table with the requires_unlock data.
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

$stmt = Propel::getConnection()->prepare("UPDATE recipe SET
                                                 requires_unlock = 1
                                           WHERE data_id = :data_id");

foreach ($data as $i => $row) {
	echo "[{$i} / {$cnt}] \n";
   
    if (isset($row['RequiresRecipeItem']) && $row['RequiresRecipeItem'] !== false && isset($row['DataID'])) {
		$stmt->bindValue('data_id', $row['DataID']);
		$stmt->execute();
    }
}


// ensure purged cache, otherwise everything goes to hell
CacheHandler::getInstance("purge")->purge();

