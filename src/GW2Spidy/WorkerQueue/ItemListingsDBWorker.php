<?php

namespace GW2Spidy\WorkerQueue;

use GW2Spidy\Queue\WorkerQueueManager;
use GW2Spidy\Queue\WorkerQueueItem;

use GW2Spidy\DB\SellListing;

use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\TradeMarket;

use GW2Spidy\DB\ItemType;
use GW2Spidy\DB\ItemSubType;

class ItemListingsDBWorker implements Worker {
    public function work(WorkerQueueItem $item) {
        $item = $item->getData();

        $this->buildListingsDB($item);
    }

    public function buildListingsDB(Item $item) {
        $now      = new \DateTime();
        $market   = TradeMarket::getInstance();
        $listings = $market->getListingsById($item->getDataId());

        if ($listings) {
            foreach ($listings as $listingData) {
                $listing = new SellListing();
                $listing->fromArray($listingData, \BasePeer::TYPE_FIELDNAME);
                $listing->setItem($item);
                $listing->setListingDate($now);
                $listing->setListingTime($now);

                $listing->save();
            }
        }

        return $listings;
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