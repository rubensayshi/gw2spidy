<?php

use GW2Spidy\TradingPostSpider;

use GW2Spidy\DB\ItemQuery;

use GW2Spidy\WorkerQueue\ItemDBWorker;


require dirname(__FILE__) . '/../autoload.php';

$args = $argv;
unset($args[0]);

$worker = new ItemDBWorker();

$items = TradingPostSpider::getInstance()->getItemsByIds($args);

foreach ($items as $itemData) {
    $worker->storeItemData($itemData);
}