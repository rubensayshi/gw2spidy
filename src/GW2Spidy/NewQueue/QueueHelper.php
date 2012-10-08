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

    public function getItemDBQueueManager() {
        return new ItemDBQueueManager();
    }

    public function enqueueItemDB($type = null) {
        Propel::disableInstancePooling();

        $q = ItemQuery::create();
        $queueManager = $this->getItemDBQueueManager();

        foreach (ItemTypeQuery::getAllTypes() as $type) {
            foreach ($type->getSubTypes() as $subtype) {
                $queueManager->enqueue(new ItemDBQueueItem($type, $subtype));
            }

            $queueManager->enqueue(new ItemDBQueueItem($type, null));
        }
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

        $i = 0;
        foreach ($q->find() as $item) {
            $queueItem = new ItemListingDBQueueItem($item);
            $queueManager->enqueue($queueItem);

            unset($item, $queueItem);

            var_dump($i++);
        }
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

        list($exists, $nexists) = $queueManager->multi_exists($q->find()->toArray(), true);

        foreach ($nexists as $id) {
            var_dump($id);

            $queueItem = new ItemListingDBQueueItem($id);
            $queueManager->enqueue($queueItem);
        }
    }
}

?>
