<?php

namespace GW2Spidy\Util\RedisQueue;

abstract class RedisPriorityIdentifierQueueManager extends RedisPriorityQueueManager {
    abstract protected function queueItemFromIdentifier($identifier);

    protected function prepareItem(RedisPriorityIdentifierQueueItem $queueItem) {
        return $queueItem->getIdentifier();
    }

    protected function returnItem($queueItem) {
        $queueItem = $this->queueItemFromIdentifier($queueItem);

        $queueItem->setManager($this);

        return $queueItem;
    }
}

?>