<?php

namespace GW2Spidy\NewQueue;

use Predis\Client;

class ItemListingDBQueueManager {
    protected $client;

    public function __construct() {
        $this->client = new Client();
    }

    protected function getQueueName() {
        return 'item-listing-db-queue';
    }

    protected function returnItem($queueItem) {
        $queueItem = $this->queueItemFromIdentifier($queueItem);

        if (!($queueItem instanceof ItemListingDBQueueItem)) {
            return null;
        }

        $this->requeue($queueItem);

        return $queueItem;
    }

    protected function queueItemFromIdentifier($identifier) {
        return new ItemListingDBQueueItem($identifier);
    }

    public function next() {
        $queueKey  = $this->getQueueName();
        $triesLeft = 2;

        do {
            // set a watch on the $queueKey
            $this->client->watch($queueKey);

            // pop the hotest item off $queueKey which is between -inf and +inf with limit 0,1
            $items = $this->client->zRangeByScore($queueKey, '-inf', '+inf', array('limit' => array(0, 1)));
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
                return $this->returnItem($queueItem);
            }

            // if we didn't get a usable slot we retry
        } while ($triesLeft-- > 0);

        return null;
    }

    public function enqueue(ItemListingDBQueueItem $queueItem) {
        return $this->client->zadd($this->getQueueName(), $queueItem->getPriority(), $queueItem->getIdentifier());
    }

    public function requeue(ItemListingDBQueueItem $queueItem) {
        return $this->enqueue(clone $queueItem);
    }

    public function purge() {
        $this->client->del($this->getQueueName());
    }

    public function getLength() {
        return $this->client->zcard($this->getQueueName());
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