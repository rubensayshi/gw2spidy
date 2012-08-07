<?php

namespace GW2Spidy\Util\RedisQueue;

abstract class RedisPriorityQueueManager extends RedisQueueManager {
    public function enqueue(RedisPriorityQueueItem $queueItem) {
        return $this->client->zadd($this->getQueueName(), $queueItem->getPriority(), serialize($queueItem));
    }

    public function next() {
        $queueKey  = $this->getQueueName();
        $triesLeft = 2;

        do {
            // set a watch on the $queueKey
            $this->client->watch($queueKey);

            // pop the hotest item off $queueKey which is between 0 and time() with limit 0,1
            $items = $this->client->zrevRangeByScore($queueKey, '+inf', '-inf', array('limit' => array(0, 1)));
            // grab the item we popped off
            $queueItem = $items ? $items[0] : null;

            // no item :(
            if (is_null($queueItem)) {
                return null;
            }

            // start transaction
            $tx = $this->client->multi();

            // removed the item from the $queueKey
            $this->client->zrem($queueKey, $queueItem);

            // execute the transaction
            $results = $this->client->exec();

            // check if the zrem command removed 1 (or more)
            // if it did we can use this item
            if ($results[0] >= 1) {
                $queueItem = unserialize($queueItem);

                var_dump($queueItem);

                return ($queueItem instanceof RedisPriorityQueueItem) ? $queueItem : null;
            }

            // if we didn't get a usable slot we retry
        } while ($triesLeft-- > 0);

        return null;
    }

    public function getLength() {
        return $this->client->zcard($this->getQueueName());
    }
}

?>