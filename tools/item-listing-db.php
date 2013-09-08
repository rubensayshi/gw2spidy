<?php

use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\SellListingQuery;
use GW2Spidy\NewQueue\ItemListingDBQueueItem;
use GW2Spidy\NewQueue\ItemListingDBQueueManager;
use GW2Spidy\NewQueue\ItemListingDBQueueWorker;


require dirname(__FILE__) . '/../autoload.php';

$queueManager = new ItemListingDBQueueManager();
$queueWorker  = new ItemListingDBQueueWorker($queueManager);

$item = ItemQuery::create()->findPk($argv[1]);

if (!$item) die("failed to find item");

var_dump($item->getName(), $item->getItemTypeId());

$queueItem = new ItemListingDBQueueItem($item);
var_dump($queueItem->getItem()->getQueuePriority());

if (isset($argv[2]) && strstr("search", $argv[2])) {
    $queueItem = array($queueItem);
}

var_dump(is_array($queueItem));

$queueWorker->work($queueItem);

$l = SellListingQuery::create()
        ->filterByItemId($item->getDataId())
        ->orderByListingDatetime(\Criteria::DESC)
        ->limit(1)
        ->findOne();

var_dump($l->toArray());