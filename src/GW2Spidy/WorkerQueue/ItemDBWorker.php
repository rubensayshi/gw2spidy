<?php

namespace GW2Spidy\WorkerQueue;


use GW2Spidy\DB\Item;
use GW2Spidy\DB\ItemQuery;
use GW2Spidy\DB\WorkerQueueItem;
use GW2Spidy\TradeMarket\TradeMarket;

use GW2Spidy\DB\ItemType;
use GW2Spidy\DB\ItemSubType;

class ItemDBWorker implements Worker {
    public function work(WorkerQueueItem $item) {
        $data = $item->getData();

        $this->buildItemDB($data['type'], $data['subtype'], $data['offset']);
        $this->enqeueNextOffset($data['type'], $data['subtype'], $data['offset']);
    }

    protected function buildItemDB($type, $subtype, $offset) {
        $items  = TradeMarket::getInstance()->getItemList($type, $subtype, $offset);

        if ($items) {
            foreach ($items as $itemData) {
                $item = ItemQuery::create()->findPK($itemData['data_id']);

                if ($item) {
                    if ($item->getName() != $itemData['name']) {
                        throw new \Exception("Title for ID no longer matches! [{$itemData['name']}] [{$itemData['id']}]");
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
    }

    protected function enqeueNextOffset($type, $subtype, $offset) {
        return self::enqueueWorker($type, $subtype, $offset + 10);
    }

    public static function enqueueWorker($type, $subtype, $offset = 0) {
        $queueItem = new WorkerQueueItem();
        $queueItem->setWorker("\\GW2Spidy\\WorkerQueue\\ItemDBWorker");
        $queueItem->setPriority(WorkerQueueItem::PRIORITY_ITEMDB);
        $queueItem->setData(array(
            'type'    => $type,
            'subtype' => $subtype,
            'offset'  => $offset,
        ));

        $queueItem->save();

        return $queueItem;
    }
}

?>