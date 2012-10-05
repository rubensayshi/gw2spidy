<?php

namespace GW2Spidy\Util\RedisQueue;

class RedisPriorityIdentifierQueueManager extends RedisPriorityQueueManager {
    protected $itemClass;

    public function __construct($queueName, $itemClass) {
        $this->itemClass = $itemClass;

        parent::__construct($queueName);
    }

    protected function prepareItem(RedisPriorityIdentifierQueueItem $queueItem) {
        return $queueItem->getIdentifier();
    }

    protected function returnItem($queueItem) {
        $queueItem = new $this->itemClass($queueItem);

        $queueItem->setManager($this);

        return $queueItem;
    }
}

?>