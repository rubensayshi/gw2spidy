<?php

namespace GW2Spidy\NewQueue;

use GW2Spidy\DB\ItemTypeQuery;

use \Propel;
use GW2Spidy\Util\Singleton;
use GW2Spidy\DB\ItemType;
use GW2Spidy\DB\ItemQuery;

class QueueHelper extends Singleton {
    public function getItemListingDBQueueManager() {
        return new ItemListingDBQueueManager();
    }

    public function enqueueItemListingDB($type = null) {
        Propel::disableInstancePooling();

        $q = ItemQuery::create();
        $queueManager = $this->getItemListingDBQueueManager();

        if ($type instanceof ItemType) {
            $q->filterByType($type);
        } else if (is_numeric($type)) {
            $q->filterByTypeId($type);
        }

        $q->filterByUnsellableFlag(false);

        $i = 0;
        foreach ($q->find() as $item) {
            $queueItem = new ItemListingDBQueueItem($item);
            $queueManager->enqueue($queueItem);

            unset($item, $queueItem);

            var_dump($i++);
        }

        Propel::enableInstancePooling();
    }

    public function superviseItemListingDB($type = null) {
        Propel::disableInstancePooling();

        $q = ItemQuery::create();
        $q->select('DataId');
        $queueManager = $this->getItemListingDBQueueManager();

        if ($type instanceof ItemType) {
            $q->filterByType($type);
        } else if (is_numeric($type)) {
            $q->filterByTypeId($type);
        }

        $q->filterByUnsellableFlag(false);

        list($exists, $nexists) = $queueManager->multi_exists($q->find()->toArray(), true);

        if ($nexists) {
            $lowestprio = $queueManager->getLowestPrio();

            foreach ($nexists as $id) {
                $queueItem = new ItemListingDBQueueItem($id);
                $queueManager->enqueue($queueItem, $lowestprio);
            }
        }

        Propel::enableInstancePooling();
    }
}