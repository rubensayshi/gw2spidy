<?php

use GW2Spidy\TradingPostSpider;

use GW2Spidy\DB\ItemQuery;

use GW2Spidy\WorkerQueue\ItemDBWorker;

require dirname(__FILE__) . '/../config/config.inc.php';
require dirname(__FILE__) . '/../autoload.php';

$item = ItemQuery::create()->findPK($argv[1]);
$itemData = TradingPostSpider::getInstance()->getItemById($item->getDataId());

$worker = new ItemDBWorker();
$worker->storeItemData($itemData, $item->getItemType(), $item->getItemSubType());