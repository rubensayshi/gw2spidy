<?php

ini_set('memory_limit', '1G');

use GW2Spidy\TradeMarket;

use GW2Spidy\DB\ItemQuery;

use GW2Spidy\WorkerQueue\ItemDBWorker;

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

$con = Propel::getConnection();
$stmt1 = $con->prepare("REPLACE INTO gw2spidy.sell_listing SELECT * FROM gw2spidy_old.sell_listing WHERE item_id = :item_id");
$stmt2 = $con->prepare("REPLACE INTO gw2spidy.buy_listing SELECT * FROM gw2spidy_old.buy_listing WHERE item_id = :item_id");

$items = ItemQuery::create()->find();
$cnt   = count($items);
$i     = 0;
foreach ($items as $item) {
    echo "[{$item->getDataId()}] [{$i} / {$cnt}] ... \n";

    $stmt1->bindValue('item_id', $item->getDataId());
    $stmt1->execute();

    $stmt2->bindValue('item_id', $item->getDataId());
    $stmt2->execute();

    $i++;
}
