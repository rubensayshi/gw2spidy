<?php

namespace GW2Spidy\Util\RedisQueue;

use Predis\Client;

class RedisQueueManager {
    protected $client;
    protected $queue;

    protected static $instance;

    private function __construct($queue) {
        $this->queue  = $queue;
        $this->client = new Client();
    }

    public static function getInstance() {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function enqueue(RedisQueueItem $queueItem) {
        return $this->client->lpush($this->queue, $queueItem);
    }

    public function next() {
        $queueItem = $this->client->brpop($this->queue);

        return ($queueItem instanceof RedisQueueItem) ? $queueItem : null;
    }
}

?>