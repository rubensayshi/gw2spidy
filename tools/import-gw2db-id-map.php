<?php

use GW2Spidy\DB\GW2DBItemArchiveQuery;

use GW2Spidy\Util\CacheHandler;

use GW2Spidy\DB\GW2DBItemArchive;

use GW2Spidy\DB\Item;

use GW2Spidy\DB\ItemQuery;

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

$stmt = Propel::getConnection()->prepare("UPDATE item SET gw2db_id = :gw2db_id,
                                                          gw2db_external_id = :gw2db_external_id,
                                                          name = :name,
                                                          vendor_price = :vendor_price,
                                                          karma_price = :karma_price
                                                      WHERE data_id = :data_id");

foreach ($data as $i => $row) {
    echo "[{$i} / {$cnt}] \n";

    if (strpos($row['Name'], "Recipe: ") !== false) {
        continue;
    }

    $lowestPrice = null;
    $lowestKarma = null;
    if (isset($row['SoldBy']) && $row['SoldBy']) {
        foreach($row['SoldBy'] as $r) {
            if (isset($r['GoldCost']) && $r['GoldCost']) {
                $lowestPrice = $r['GoldCost'];
            } else if (isset($r['KarmaCost']) && $r['KarmaCost']) {
                $lowestKarma = $r['KarmaCost'];
            }
        }
    }

    $stmt->bindValue('name', $row['Name']);
    $stmt->bindValue('gw2db_id', $row['ID']);
    $stmt->bindValue('gw2db_external_id', $row['ExternalID']);
    $stmt->bindValue('data_id', $row['DataID']);
    $stmt->bindValue('vendor_price', $lowestPrice);
    $stmt->bindValue('karma_price', $lowestKarma);

    $stmt->execute();
    if ($stmt->rowCount() <= 0) {
        if (ItemQuery::create()->filterByDataId($row['DataID'])->count() == 0) {

            if (!($i = GW2DBItemArchiveQuery::create()->findPk($row['ID']))) {
                $i = new GW2DBItemArchive();
            }

            $i->fromArray($row, BasePeer::TYPE_FIELDNAME);
            $i->save();
        }
    } else {

    }
}


