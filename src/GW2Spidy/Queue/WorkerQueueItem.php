<?php

namespace GW2Spidy\Queue;

use GW2Spidy\Util\RedisQueue\RedisQueueItem;

class WorkerQueueItem extends RedisQueueItem {
    protected $worker;
    protected $priority;

    public function setWorker($worker) {
        $this->worker = $worker;

        return $this;
    }

    public function getWorker() {
        return $this->worker;
    }

    public function setPriority($priority) {
        $this->priority = $priority;

        return $this;
    }

    public function getPriority() {
        return $this->priority;
    }

}

?>