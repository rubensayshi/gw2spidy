<?php

namespace GW2Spidy\WorkerQueue;

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
        $items  = TradeMarket::getInstance()->getItemList($type, $subtype, $offset);

        if ($items) {
            foreach ($items as $itemData) {
                $item = ItemQuery::create()->findPK($itemData['data_id']);

                if ($item) {
                    if ($item->getName() != $itemData['name']) {
                        throw new \Exception("Title for ID no longer matches! [json::{$itemData['id']}::{$itemData['name']}] vs [db::{$item->getDataId()}::{$item->getName()}]");
                    }
                } else {
                    $item = new Item();
                    $item->fromArray($itemData, \BasePeer::TYPE_FIELDNAME);
                    $item->setItemType($type);
                    $item->setItemSubType($subtype);

                    $item->save();
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