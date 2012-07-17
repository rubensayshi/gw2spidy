<?php

namespace GW2Spidy\WorkerQueue;


use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\WorkerQueueItem;
use GW2Spidy\TradeMarket\TradeMarket;

use GW2Spidy\DB\ItemType;
use GW2Spidy\DB\ItemSubType;

class ItemListingsDBWorker implements Worker {
    public function work(WorkerQueueItem $item) {
        $item = $item->getData();

        $this->buildListingsDB($item);
        $this->enqeueNextHour($item);
    }

    protected function buildListingsDB(Item $item) {
        $market   = TradeMarket::getInstance();
        $listings = $market->getListingsById($item->getDataId());

        if ($listings) {
            var_dump($listings);
        }
    }

    protected function enqeueNextHour(Item $item) {
        return self::enqueueWorker($item, strtotime("+1 hour"));
    }

    public static function enqueueWorker($item, $time = null) {
        $queueItem = new WorkerQueueItem();
        $queueItem->setWorker("\\GW2Spidy\\WorkerQueue\\ItemListingsDBWorker");
        $queueItem->setPriority(WorkerQueueItem::PRIORITY_LISTINGSDB);
        $queueItem->setData($item);

        if ($time) {
            $queueItem->setTouched($time);
        }

        $queueItem->save();

        return $queueItem;
    }
}

?>