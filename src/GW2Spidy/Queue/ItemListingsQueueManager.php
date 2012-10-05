<?php

namespace GW2Spidy\Queue;

use GW2Spidy\Util\RedisQueue\RedisPriorityIdentifierQueueItem;
use GW2Spidy\Util\RedisQueue\RedisPriorityIdentifierQueueManager;

class ItemListingsQueueManager extends RedisPriorityIdentifierQueueManager {
    protected function queueItemFromIdentifier($identifier) {
        return new ItemListingsQueueItem($identifier);
    }

    protected function returnItem($queueItem) {
        $queueItem = parent::returnItem($queueItem);
        $queueItem->requeue();

        return $queueItem;
    }
}

?>