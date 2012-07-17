<?php

namespace GW2Spidy\Spider;

use GW2Spidy\WorkerQueue\ItemDBWorker;

use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemSubTypeQuery;
use GW2Spidy\DB\ItemSubType;
use GW2Spidy\DB\ItemType;
use GW2Spidy\DB\ItemTypeQuery;
use GW2Spidy\DB\WorkerQueueItem;
use GW2Spidy\TradeMarket\TradeMarket;

class ItemDBSpider {
    public function fillQueue() {
        $this->buildItemTypeDB();
        $this->buildItemDB();
    }

    public function buildItemDB() {
        foreach (ItemTypeQuery::create()->find() as $type) {
            foreach ($type->getSubTypes() as $subtype) {
                ItemDBWorker::enqueueWorker($type, $subtype);

            }
        }
    }

    public function buildItemTypeDB() {
        $queueItem = new WorkerQueueItem();
        $queueItem->setWorker("\\GW2Spidy\\WorkerQueue\\ItemTypeDBWorker");
        $queueItem->save();
    }
}

?>