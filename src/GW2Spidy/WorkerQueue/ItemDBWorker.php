<?php

namespace GW2Spidy\WorkerQueue;

use GW2Spidy\DB\SellListing;

use GW2Spidy\Util\Functions;

use GW2Spidy\Queue\WorkerQueueManager;
use GW2Spidy\Queue\WorkerQueueItem;

use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\TradeMarket;

use GW2Spidy\DB\ItemType;
use GW2Spidy\DB\ItemSubType;

class ItemDBWorker implements Worker {
    public function work(WorkerQueueItem $item) {
        $data = $item->getData();

        $res = $this->buildItemDB($data['type'], $data['subtype'], $data['offset']);

        // we stop enqueueing the next slice when we stop getting results
        if ($data['full'] && $res) {
            $this->enqeueNextOffset($data['type'], $data['subtype'], $data['offset']);
        }
    }

    protected function buildItemDB($type, $subtype, $offset) {
        $now    = new \DateTime();
        $items  = TradeMarket::getInstance()->getItemList($type, $subtype, $offset);

        if ($items) {
            foreach ($items as $itemData) {
                $item = ItemQuery::create()->findPK($itemData['data_id']);

                if ($item) {
                    if (($p = Functions::almostEqualCompare($itemData['name'], $item->getName())) > 50) {
                        $item->fromArray($itemData);
                        $item->save();

                    } else {
                        throw new \Exception("Title for ID no longer matches! item [{$p}] [json::{$itemData['data_id']}::{$itemData['name']}] vs [db::{$item->getDataId()}::{$item->getName()}]");
                    }
                } else {
                    $item = new Item();
                    $item->fromArray($itemData, \BasePeer::TYPE_FIELDNAME);
                    $item->setItemType($type);
                    $item->setItemSubType($subtype);

                    $item->save();
                }

                if ($itemData['min_sale_unit_price'] > 0) {
                    $sellListing = new SellListing();
                    $sellListing->setItem($item);
                    $sellListing->setListingDate($now);
                    $sellListing->setListingDate($now);
                    $sellListing->setQuantity(1);
                    $sellListing->setUnitPrice($itemData['min_sale_unit_price']);
                    $sellListing->setListings(1);

                    $sellListing->save();
                }
            }
        }

        return (boolean)$items;
    }

    protected function enqeueNextOffset($type, $subtype, $offset) {
        return self::enqueueWorker($type, $subtype, $offset + 10, true);
    }

    public static function enqueueWorker($type, $subtype, $offset = 0, $full = true) {
        $queueItem = new WorkerQueueItem();
        $queueItem->setWorker("\\GW2Spidy\\WorkerQueue\\ItemDBWorker");
        // $queueItem->setPriority(WorkerQueueItem::PRIORITY_ITEMDB);
        $queueItem->setData(array(
            'type'    => $type,
            'subtype' => $subtype,
            'offset'  => $offset,
            'full'    => $full,
        ));

        WorkerQueueManager::getInstance()->enqueue($queueItem);

        return $queueItem;
    }
}

?>