<?php

namespace GW2Spidy\Queue;

use GW2Spidy\Util\RedisQueue\RedisQueueItem;

class WorkerQueueItem extends RedisQueueItem {
    protected $worker;
    protected $data  = null;

    public function setWorker($worker) {
        $this->worker = $worker;

        return $this;
    }

    public function getWorker() {
        return $this->worker;
    }

    public function setData($data) {
        $this->data = $data;

        return $this;
    }

    public function getData() {
        return $this->data;
    }

    public function work() {
        throw new Exception("This worker queue item ain't new age yet, should have it's work() method called ... ;-)");
    }
}

?>