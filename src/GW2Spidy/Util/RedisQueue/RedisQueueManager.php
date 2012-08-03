<?php

namespace GW2Spidy\Util\RedisQueue;

use Predis\Client;

abstract class RedisQueueManager {
    protected $client;

    protected static $instance;

    protected function __construct() {
        $this->client = new Client();
    }

    /**
     * @return RedisQueueManager
     */
    public static function getInstance() {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
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

    public function getLength() {
        return $this->client->llen($this->getQueueName());
    }
}

?>