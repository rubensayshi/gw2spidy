<?php

use GW2Spidy\TradingPostSpider;

use GW2Spidy\DB\ItemQuery;

use GW2Spidy\WorkerQueue\ItemDBWorker;


require dirname(__FILE__) . '/../autoload.php';

$args = $argv;
unset($args[0]);

$worker = new ItemDBWorker();

foreach ($args as $id) {
    $item = ItemQuery::create()->findPK($id);
    $itemData = TradingPostSpider::getInstance()->getItemById($item->getDataId());

    $worker->storeItemData($itemData, $item->getItemType(), $item->getItemSubType());
}