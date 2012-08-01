<?php

namespace GW2Spidy\Queue;

use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\ItemTypeQuery;
use GW2Spidy\WorkerQueue\ItemTypeDBWorker;
use GW2Spidy\WorkerQueue\ItemListingsDBWorker;
use GW2Spidy\WorkerQueue\ItemDBWorker;

class QueueManager {
    public function buildItemTypeDB() {
        ItemTypeDBWorker::enqueueWorker();
    }

    public function buildItemDB($full = true) {
        foreach (ItemTypeQuery::create()->find() as $type) {
            if ($type->getSubTypes()->count()) {
                foreach ($type->getSubTypes() as $subtype) {
                    ItemDBWorker::enqueueWorker($type, $subtype, $full);
                }
            } else {
                ItemDBWorker::enqueueWorker($type, null, $full);
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