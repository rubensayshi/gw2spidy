<?php

use GW2Spidy\TradeMarket;

use GW2Spidy\DB\ItemQuery;

use GW2Spidy\WorkerQueue\ItemDBWorker;

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

$sql = "REPLACE INTO gw2spidy.sell_listing SELECT * FROM gw2spidy_old.sell_listing WHERE item_id = :item_id";
$con = Propel::getConnection();
$stmt = $con->prepare($sql);

$items = ItemQuery::create()->find();
$cnt   = count($items);
$i     = 0;
foreach ($items as $item) {
    echo "[{$item->getDataId()}] [{$i} / {$cnt}] ... \n";

    $stmt->bindValue('item_id', $item->getDataId());
    $stmt->execute();

    $i++;
}
