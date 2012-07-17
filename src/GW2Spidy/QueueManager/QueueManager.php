<?php

namespace GW2Spidy\QueueManager;

use GW2Spidy\WorkerQueue\ItemTypeDBWorker;

use GW2Spidy\WorkerQueue\ItemListingsDBWorker;

use GW2Spidy\WorkerQueue\ItemDBWorker;

use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\ItemSubType;
use GW2Spidy\DB\ItemSubTypeQuery;
use GW2Spidy\DB\ItemType;
use GW2Spidy\DB\ItemTypeQuery;
use GW2Spidy\DB\WorkerQueueItem;
use GW2Spidy\TradeMarket\TradeMarket;

class QueueManager {
    public function fillQueue() {
        $this->buildItemTypeDB();
        $this->buildItemDB();
        $this->buildListingsDB();
    }

    public function buildItemTypeDB() {
        ItemTypeDBWorker::enqueueWorker();
    }

    public function buildItemDB() {
        foreach (ItemTypeQuery::create()->find() as $type) {
            foreach ($type->getSubTypes() as $subtype) {
                ItemDBWorker::enqueueWorker($type, $subtype);

            }
        }
    }

    public function buildListingsDB() {
        foreach (ItemQuery::create()->find() as $item) {
            ItemListingsDBWorker::enqueueWorker($item);
        }
    }
}

?>