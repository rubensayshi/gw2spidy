<?php

use GW2Spidy\DB\GW2DBItemArchive;

use GW2Spidy\DB\Item;

use GW2Spidy\DB\ItemQuery;

ini_set('memory_limit', '1G');

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

if (!isset($argv[1]) || !($mapfilename = $argv[1])) {
    die('map file required.');
}

if (!file_exists($mapfilename)) {
    die('map file does not exist.');
}

$data = json_decode(file_get_contents($mapfilename), true);
$cnt  = count($data);

$stmt = Propel::getConnection()->prepare("UPDATE item SET gw2db_id = :gw2db_id, gw2db_external_id = :gw2db_external_id WHERE data_id = :data_id");

/*
 {
 "ID":499635,
 "ExternalID":7587,
 "DataID":130,
 "Name":"Traveler's Duelist's Mask of Vampirism"
 }
 */
foreach ($data as $i => $row) {
    echo "[{$i} / {$cnt}] \n";

    $stmt->bindValue('gw2db_id', $row['ID']);
    $stmt->bindValue('gw2db_external_id', $row['ExternalID']);
    $stmt->bindValue('data_id', $row['DataID']);

    $stmt->execute();
    if ($stmt->rowCount() <= 0 && strpos($row['Name'], "Recipe: ") === false) {
        if (ItemQuery::create()->filterByDataId($row['DataID'])->count() == 0) {

            $i = new GW2DBItemArchive();
            $i->fromArray($row, BasePeer::TYPE_FIELDNAME);
            $i->save();
        }
    }
}


