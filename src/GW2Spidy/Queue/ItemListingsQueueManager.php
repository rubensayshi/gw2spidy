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

    public function exists($id) {
        // start transaction
        $tx = $this->client->multi();

        // removed the item from the $queueKey
        $this->client->zrank($this->getQueueName(), $id);

        // execute the transaction
        $results = $this->client->exec();

        // check if the zrank command returned a rank
        return is_numeric($results[0]);
    }

    public function multi_exists(array $ids, $mResult = false) {
        // ensure sequentual keys
        $ids = array_values($ids);

        // start transaction
        $tx = $this->client->multi();

        // removed the item from the $queueKey

        foreach ($ids as $k => $id) {
            $this->client->zrank($this->getQueueName(), $id);
        }

        // execute the transaction
        $results = $this->client->exec();

        if ($mResult) {
            $exists  = array();
            $nexists = array();
            foreach ($ids as $k => $id) {
                if (is_numeric($results[$k])) {
                    $exists[] = $id;
                } else {
                    $nexists[] = $id;
                }
            }

            return array($exists, $nexists);
        } else {
            $exists  = array();
            foreach ($ids as $k => $id) {
                $exists[$id] = is_numeric($results[$k]);
            }

            return $exists;

        }
    }
}

?>