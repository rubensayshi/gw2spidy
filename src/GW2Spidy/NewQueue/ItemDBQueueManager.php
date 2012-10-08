<?php

namespace GW2Spidy\NewQueue;

use Predis\Client;

class ItemDBQueueManager {
    protected $client;

    public function __construct() {
        $this->client = new Client();
    }

    protected function getQueueName() {
        return "item-db-queue";
    }

    public function enqueue(ItemDBQueueItem $queueItem) {
        return $this->client->lpush($this->getQueueName(), serialize($queueItem));
    }

    public function next() {
        $result    = $this->client->brpop($this->getQueueName(), 2);
        $queueItem = unserialize($result);

        if (!($queueItem instanceof RedisPriorityQueueItem)) {
            return null;
        }

        return $queueItem;
    }

    public function purge() {
        $this->client->del($this->getQueueName());
    }

    public function getLength() {
        return $this->client->llen($this->getQueueName());
    }
}

?>