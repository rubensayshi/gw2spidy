<?php

namespace GW2Spidy\WorkerQueue;

use GW2Spidy\Queue\WorkerQueueManager;
use GW2Spidy\Queue\WorkerQueueItem;

use GW2Spidy\DB\BuyListing;
use GW2Spidy\DB\SellListing;

use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\TradingPostSpider;

use GW2Spidy\DB\ItemType;
use GW2Spidy\DB\ItemSubType;

class ItemListingsDBWorker extends ItemDBWorker implements Worker {
    public function getRetries() {
        return 0;
    }

    public function work(WorkerQueueItem $item) {
        $item = $item->getData();

        $this->buildListingsDB($item);
    }

    public function buildListingsDB(Item $item) {
       if ($itemData = TradingPostSpider::getInstance()->getItemById($item->getDataId())) {
            var_dump($item->getName(), (boolean)$itemData);

            $this->storeItemData($itemData, null, null, $item);
        }

    }

    public static function enqueueWorker($item) {
        $queueItem = new WorkerQueueItem();
        $queueItem->setWorker("\\GW2Spidy\\WorkerQueue\\ItemListingsDBWorker");
        // $queueItem->setPriority(WorkerQueueItem::PRIORITY_LISTINGSDB);
        $queueItem->setData($item);

        WorkerQueueManager::getInstance()->enqueue($queueItem);

        return $queueItem;
    }
}

?>