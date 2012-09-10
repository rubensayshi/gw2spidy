<?php

namespace GW2Spidy\Util\RedisQueue;

use GW2Spidy\Util\Singleton;

use Predis\Client;

abstract class RedisQueueManager extends Singleton {
    protected $client;

    protected function __construct() {
        $this->client = new Client();
    }

    abstract protected function getQueueName();

    public function enqueue(RedisQueueItem $queueItem) {
        return $this->client->lpush($this->getQueueName(), serialize($queueItem));
    }

    public function next() {
        $result    = $this->client->brpop($this->getQueueName(), 2);
        $queueItem = unserialize($result[1]);

        return ($queueItem instanceof RedisQueueItem) ? $queueItem : null;
    }

    public function purge() {
        $this->client->del($this->getQueueName());
    }

    public function getLength() {
        return $this->client->llen($this->getQueueName());
    }
}

?>