<?php

namespace GW2Spidy\WorkerQueue;

use GW2Spidy\DB\ItemSubType;
use GW2Spidy\DB\ItemSubTypeQuery;
use GW2Spidy\DB\ItemType;
use GW2Spidy\DB\ItemTypeQuery;
use GW2Spidy\DB\WorkerQueueItem;
use GW2Spidy\TradeMarket;

class ItemTypeDBWorker implements Worker {
    public function work(WorkerQueueItem $item) {
        $market = TradeMarket::getInstance();

        $marketData = $market->getMarketData();

        foreach ((array)$marketData['types'] as $mainTypeData) {
            if (!isset($mainTypeData['id']) || !isset($mainTypeData['name'])) {
                continue;
            }

            $type = ItemTypeQuery::create()->findPK($mainTypeData['id']);

            if ($type) {
                if ($type->getTitle() != $mainTypeData['name']) {
                    throw new \Exception("Title for ID no longer matches! [{$mainTypeData['name']}] [{$mainTypeData['id']}]");
                }
            } else {
                $type = new ItemType();
                $type->setId($mainTypeData['id']);
                $type->setTitle($mainTypeData['name']);
                $type->save();
            }

            foreach ((array)$mainTypeData['subs'] as $subTypeData) {
                if (!isset($subTypeData['id']) || !isset($subTypeData['name'])) {
                    continue;
                }

                $subtype = ItemSubTypeQuery::create()->findPK(array($subTypeData['id'], $type->getId()));

                if ($subtype) {
                    if ($subtype->getTitle() != $subTypeData['name']) {
                        throw new \Exception("Title for ID no longer matches! [{$subTypeData['name']}] [{$subTypeData['id']}]");
                    }
                    if (!$subtype->getMainType()->equals($type)) {
                        throw new \Exception("Maintype no longer matches! [{$subTypeData['name']}] [{$subTypeData['id']}]");
                    }
                } else {
                    $subtype = new ItemSubType();
                    $subtype->setMainType($type);
                    $subtype->setId($subTypeData['id']);
                    $subtype->setTitle($subTypeData['name']);
                    $subtype->save();
                }
            }
        }
    }

    public static function enqueueWorker() {
        $queueItem = new WorkerQueueItem();
        $queueItem->setWorker("\\GW2Spidy\\WorkerQueue\\ItemTypeDBWorker");

        $queueItem->save();

        return $queueItem;
    }
}

?>