<?php

namespace GW2Spidy\Util\RedisQueue;

use GW2Spidy\Util\Singleton;

use Predis\Client;

class RedisQueueManager {
    protected $queueName;
    protected $client;

    public function __construct($queueName) {
        $this->client = new Client();
        $this->queueName = $queueName;
    }

    protected function getQueueName() {
        return $this->queueName;
    }

    public function enqueue(RedisQueueItem $queueItem) {
        return $this->client->lpush($this->getQueueName(), serialize($queueItem));
    }

    public function next() {
        $result    = $this->client->brpop($this->getQueueName(), 2);
        $queueItem = unserialize($result[1]);

        if (!($queueItem instanceof RedisQueueItem)) {
            return null;
        }

        $queueItem->setManager($this);

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